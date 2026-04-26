<?php

namespace Tests\Feature\Accounting;

use App\Models\FinancialTransaction;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\JournalEntry;
use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Services\SupplierPayablesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SupplierPayablesWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_grn_confirmation_creates_supplier_payable_once_per_grn(): void
    {
        [$storeKeeper, $manager, $supplier, $product] = $this->bootstrapProcurementContext();

        $lpo = LocalPurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'status' => 'approved',
            'created_by' => $storeKeeper->id,
            'approved_by' => $manager->id,
            'approved_at' => now(),
        ]);

        $lpoItem = LocalPurchaseOrderItem::create([
            'lpo_id' => $lpo->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity' => 5,
            'unit_price' => 1000,
            'subtotal' => 5000,
        ]);

        $grn = $this->createGrn($lpo, $supplier, $storeKeeper, $lpoItem, $product, 5, 1000);

        $this->actingAs($storeKeeper)
            ->post(route('procurement.grn.confirm', $grn))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($manager)
            ->post(route('procurement.grn.approve', $grn))
            ->assertRedirect()
            ->assertSessionHas('success');

        $payable = SupplierPayable::where('source_reference_id', $grn->id)->first();
        $this->assertNotNull($payable);
        $this->assertSame($supplier->id, $payable->supplier_id);
        $this->assertSame('procurement', $payable->source_module);
        $this->assertSame('grn', $payable->source_reference_type);
        $this->assertEquals((float) $grn->grand_total, (float) $payable->amount_total);
        $this->assertEquals(0.0, (float) $payable->amount_paid);
        $this->assertSame('unpaid', $payable->status);

        app(\App\Services\SupplierPayablesService::class)->ensurePayableFromGrn($grn->fresh(), $manager->id);
        $this->assertEquals(1, SupplierPayable::where('source_reference_id', $grn->id)->count());
    }

    public function test_full_payment_workflow_supports_partial_then_paid_and_prevents_overallocation(): void
    {
        [$accountant, $supplier, $payable] = $this->bootstrapSinglePayable(amountTotal: 1000);

        $payment = SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'payment_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount' => 1000,
            'method' => 'bank',
            'status' => 'draft',
            'created_by' => $accountant->id,
        ]);

        $this->actingAs($accountant)
            ->post(route('accountant.payments.allocate', $payment), [
                'allocations' => [$payable->id => 1100],
            ])
            ->assertSessionHasErrors('allocations');

        $this->actingAs($accountant)
            ->post(route('accountant.payments.allocate', $payment), [
                'allocations' => [$payable->id => 0],
            ])
            ->assertSessionHasErrors('allocations');

        $this->actingAs($accountant)
            ->post(route('accountant.payments.allocate', $payment), [
                'allocations' => [$payable->id => 400],
            ])
            ->assertRedirect();

        $payable->refresh();
        $payment->refresh();
        $this->assertEquals(400.0, (float) $payable->amount_paid);
        $this->assertEquals(600.0, (float) $payable->balance);
        $this->assertSame('partial', $payable->status);

        $this->actingAs($accountant)
            ->post(route('accountant.payments.allocate', $payment), [
                'allocations' => [$payable->id => 1000],
            ])
            ->assertRedirect();

        $this->actingAs($accountant)
            ->post(route('accountant.payments.post', $payment))
            ->assertRedirect(route('accountant.payables.dashboard'));

        $payable->refresh();
        $payment->refresh();

        $this->assertSame('paid', $payable->status);
        $this->assertEquals(0.0, (float) $payable->balance);
        $this->assertSame('posted', $payment->status);
        $this->assertNotNull($payment->posted_at);
        $this->assertNotNull($payment->journal_entry_id);
        $this->assertNotNull($payment->financial_transaction_id);

        $entry = JournalEntry::find($payment->journal_entry_id);
        $transaction = FinancialTransaction::find($payment->financial_transaction_id);
        $this->assertNotNull($entry);
        $this->assertNotNull($transaction);
        $this->assertSame('posted', $entry->status);
    }

    public function test_posted_payment_is_immutable_and_can_only_be_cancelled_with_reason(): void
    {
        [$accountant, $manager, $supplier, $payable] = $this->bootstrapSinglePayable(true);

        $payment = SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'payment_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount' => 1180,
            'method' => 'cash',
            'status' => 'draft',
            'created_by' => $accountant->id,
        ]);

        $this->actingAs($accountant)->post(route('accountant.payments.allocate', $payment), [
            'allocations' => [$payable->id => 1180],
        ])->assertRedirect();

        $this->actingAs($accountant)->post(route('accountant.payments.post', $payment))
            ->assertRedirect(route('accountant.payables.dashboard'));

        $payment->refresh();
        $this->assertSame('posted', $payment->status);

        $this->actingAs($accountant)->post(route('accountant.payments.allocate', $payment), [
            'allocations' => [$payable->id => 100],
        ])->assertSessionHasErrors('payment');

        $this->actingAs($manager)->post(route('accountant.payments.cancel', $payment), [
            'cancellation_reason' => 'Wrong supplier charged',
        ])->assertRedirect();

        $payment->refresh();
        $payable->refresh();

        $this->assertSame('cancelled', $payment->status);
        $this->assertSame('Wrong supplier charged', $payment->cancellation_reason);
        $this->assertNotNull($payment->cancelled_at);
        $this->assertSame($manager->id, $payment->cancelled_by);
        $this->assertSame('unpaid', $payable->status);
        $this->assertEquals((float) $payable->amount_total, (float) $payable->balance);
        $this->assertEquals(0.0, (float) $payable->amount_paid);
    }

    public function test_manager_sees_ap_views_without_payment_actions_reserved_for_accountants(): void
    {
        [$accountant, $manager, $supplier, $payable] = $this->bootstrapSinglePayable(withManager: true);

        $draftPayment = SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'payment_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount' => 300,
            'method' => 'bank',
            'reference' => 'SUPPAY-UI-001',
            'status' => 'draft',
            'created_by' => $accountant->id,
        ]);

        $this->actingAs($manager)
            ->get(route('accountant.payables.dashboard'))
            ->assertOk()
            ->assertDontSee(route('accountant.payments.create'), false)
            ->assertDontSee(route('accountant.payments.apply', $draftPayment), false);

        $this->actingAs($manager)
            ->get(route('accountant.payables.show', $payable))
            ->assertOk()
            ->assertDontSee(route('accountant.payments.create'), false);
    }

    public function test_cancelled_payment_apply_view_remains_read_only(): void
    {
        [$accountant, $supplier, $payable] = $this->bootstrapSinglePayable(amountTotal: 500);

        $payment = SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'payment_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount' => 500,
            'method' => 'cash',
            'status' => 'draft',
            'created_by' => $accountant->id,
        ]);

        $this->actingAs($accountant)->post(route('accountant.payments.allocate', $payment), [
            'allocations' => [$payable->id => 500],
        ])->assertRedirect();

        $this->actingAs($accountant)->post(route('accountant.payments.post', $payment))
            ->assertRedirect(route('accountant.payables.dashboard'));

        $this->actingAs($accountant)->post(route('accountant.payments.cancel', $payment), [
            'cancellation_reason' => 'Duplicate settlement',
        ])->assertRedirect();

        $this->actingAs($accountant)
            ->get(route('accountant.payments.apply', $payment))
            ->assertOk()
            ->assertDontSee(__('accountant.ap.save_allocations'))
            ->assertDontSee(__('accountant.ap.post_payment'));
    }

    public function test_create_payment_view_lists_supplier_and_open_grn_payables(): void
    {
        [$accountant, $supplier, $payable] = $this->bootstrapSinglePayable(amountTotal: 500);

        $this->actingAs($accountant)
            ->get(route('accountant.payments.create'))
            ->assertOk()
            ->assertSee($supplier->name)
            ->assertSee($payable->reference)
            ->assertSee(__('accountant.ap.select_grn_payable'));
    }

    public function test_store_payment_rejects_mismatched_selected_payable_supplier(): void
    {
        [$accountant, $supplierA, $payableA] = $this->bootstrapSinglePayable(amountTotal: 300);
        [, $supplierB, ] = $this->bootstrapSinglePayable(amountTotal: 200);

        $this->actingAs($accountant)
            ->post(route('accountant.payments.store'), [
                'supplier_id' => $supplierB->id,
                'supplier_payable_id' => $payableA->id,
                'payment_date' => now()->toDateString(),
                'currency' => 'USD',
                'amount' => 100,
                'method' => 'cash',
            ])
            ->assertSessionHasErrors('supplier_payable_id');
    }

    public function test_sync_approved_grn_payables_creates_missing_payable_for_open_grn_supplier(): void
    {
        [$storeKeeper, $manager, $supplier, $product] = $this->bootstrapProcurementContext();

        $lpo = LocalPurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'status' => 'approved',
            'created_by' => $storeKeeper->id,
            'approved_by' => $manager->id,
            'approved_at' => now(),
        ]);

        $lpoItem = LocalPurchaseOrderItem::create([
            'lpo_id' => $lpo->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity' => 2,
            'unit_price' => 250,
            'subtotal' => 500,
        ]);

        $grn = $this->createGrn($lpo, $supplier, $storeKeeper, $lpoItem, $product, 2, 250);

        $this->actingAs($storeKeeper)->post(route('procurement.grn.confirm', $grn))->assertRedirect();
        $this->actingAs($manager)->post(route('procurement.grn.approve', $grn))->assertRedirect();

        SupplierPayable::query()
            ->where('source_module', 'procurement')
            ->where('source_reference_id', $grn->id)
            ->delete();

        $created = app(SupplierPayablesService::class)->syncApprovedGrnPayables($manager->id, $supplier->id);

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('supplier_payables', [
            'source_module' => 'procurement',
            'source_reference_type' => 'grn',
            'source_reference_id' => $grn->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    private function bootstrapProcurementContext(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);
        Artisan::call('db:seed', ['class' => 'StockLocationSeeder']);

        $storeKeeperRole = Role::where('name', 'store_keeper')->firstOrFail();
        $managerRole = Role::where('name', 'manager')->firstOrFail();
        $storeKeeper = User::factory()->create([
            'role_id' => $storeKeeperRole->id,
            'is_active' => true,
        ]);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier AP',
            'is_active' => true,
            'created_by' => $storeKeeper->id,
        ]);

        $product = Product::create([
            'name' => 'AP Product',
            'sku' => 'AP-001',
            'category' => 'General',
            'unit' => 'pcs',
            'cost_price' => 1000,
            'selling_price' => 1400,
            'reorder_level' => 1,
            'is_active' => true,
            'created_by' => $storeKeeper->id,
        ]);

        return [$storeKeeper, $manager, $supplier, $product];
    }

    private function bootstrapSinglePayable(bool $withManager = false, float $amountTotal = 1180): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);

        $accountantRole = Role::where('name', 'ACCOUNTANT')->firstOrFail();
        $managerRole = Role::where('name', 'manager')->firstOrFail();

        $accountant = User::factory()->create([
            'role_id' => $accountantRole->id,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier Finance',
            'is_active' => true,
            'created_by' => $accountant->id,
        ]);

        $payable = SupplierPayable::create([
            'supplier_id' => $supplier->id,
            'reference' => 'GRN-AP-001',
            'payable_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount_total' => $amountTotal,
            'amount_paid' => 0,
            'balance' => $amountTotal,
            'status' => 'unpaid',
            'source_module' => 'procurement',
            'source_reference_type' => 'grn',
            'source_reference_id' => fake()->uuid(),
            'created_by' => $accountant->id,
        ]);

        if (! $withManager) {
            return [$accountant, $supplier, $payable];
        }

        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        return [$accountant, $manager, $supplier, $payable];
    }

    private function createGrn(
        LocalPurchaseOrder $lpo,
        Supplier $supplier,
        User $user,
        LocalPurchaseOrderItem $lpoItem,
        Product $product,
        float $quantity,
        float $unitPrice
    ): GoodsReceivedNote {
        $grn = GoodsReceivedNote::create([
            'lpo_id' => $lpo->id,
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => GoodsReceivedNote::STATUS_SUBMITTED,
            'received_by' => $user->id,
        ]);

        GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'lpo_item_id' => $lpoItem->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity_ordered' => $lpoItem->quantity,
            'quantity_received' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ]);

        $grn->load('items');
        $grn->recalculate();

        return $grn;
    }
}
