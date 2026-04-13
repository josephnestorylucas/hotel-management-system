<?php

namespace Tests\Feature\Procurement;

use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\JournalEntry;
use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProcurementIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_a_grn_updates_stock_and_posts_accounting(): void
    {
        [$user, $supplier, $product] = $this->bootstrapProcurementContext();

        $lpo = LocalPurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'status' => 'pending_approval',
            'created_by' => $user->id,
        ]);

        $lpoItem = LocalPurchaseOrderItem::create([
            'lpo_id' => $lpo->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity' => 10,
            'unit_price' => 1000,
            'subtotal' => 10000,
        ]);

        $lpo->load('items');
        $lpo->recalculate();

        $this->actingAs($user)
            ->post(route('procurement.lpo.approve', $lpo))
            ->assertRedirect()
            ->assertSessionHas('success');

        $grn = $this->createGrn($lpo, $supplier, $user, $lpoItem, $product, 10, 1000);

        $this->actingAs($user)
            ->post(route('procurement.grn.confirm', $grn))
            ->assertRedirect()
            ->assertSessionHas('success');

        $grn->refresh();
        $lpo->refresh();
        $lpoItem->refresh();

        $movement = StockMovement::where('reference_type', 'grn_item')->first();
        $entry = JournalEntry::where('source', 'procurement')->where('source_id', $grn->id)->first();
        $mainStore = StockLocation::mainStore();
        $level = StockLevel::where('product_id', $product->id)->where('location_id', $mainStore->id)->first();

        $this->assertNotNull($movement);
        $this->assertNotNull($entry);
        $this->assertSame('confirmed', $grn->status);
        $this->assertSame($entry->id, $grn->accounting_journal_entry_id);
        $this->assertSame($supplier->id, $entry->supplier_id);
        $this->assertEquals(10.0, (float) $lpoItem->received_quantity);
        $this->assertSame('fully_received', $lpo->status);
        $this->assertEquals(10.0, (float) $level->quantity);
        $this->assertEquals(11800.0, (float) $entry->total_credit);
    }

    public function test_partial_grns_keep_lpo_in_sync_until_fully_received(): void
    {
        [$user, $supplier, $product] = $this->bootstrapProcurementContext();

        $lpo = LocalPurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'status' => 'approved',
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $lpoItem = LocalPurchaseOrderItem::create([
            'lpo_id' => $lpo->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity' => 10,
            'unit_price' => 1000,
            'subtotal' => 10000,
        ]);

        $firstGrn = $this->createGrn($lpo, $supplier, $user, $lpoItem, $product, 4, 1000);
        $secondGrn = $this->createGrn($lpo, $supplier, $user, $lpoItem, $product, 6, 1000);

        $this->actingAs($user)->post(route('procurement.grn.confirm', $firstGrn))->assertRedirect();
        $lpo->refresh();
        $lpoItem->refresh();

        $this->assertSame('partially_received', $lpo->status);
        $this->assertEquals(4.0, (float) $lpoItem->received_quantity);

        $this->actingAs($user)->post(route('procurement.grn.confirm', $secondGrn))->assertRedirect();

        $lpo->refresh();
        $lpoItem->refresh();
        $mainStore = StockLocation::mainStore();
        $level = StockLevel::where('product_id', $product->id)->where('location_id', $mainStore->id)->first();

        $this->assertSame('fully_received', $lpo->status);
        $this->assertEquals(10.0, (float) $lpoItem->received_quantity);
        $this->assertEquals(10.0, (float) $level->quantity);
        $this->assertCount(2, JournalEntry::where('source', 'procurement')->get());
        $this->assertCount(2, StockMovement::where('reference_type', 'grn_item')->get());
    }

    private function bootstrapProcurementContext(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);
        Artisan::call('db:seed', ['class' => 'StockLocationSeeder']);

        $role = Role::where('name', 'store_manager')->firstOrFail();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Procurement Supplier',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $product = Product::create([
            'name' => 'Rice Bag',
            'sku' => 'RICE-001',
            'category' => 'Food',
            'unit' => 'bag',
            'cost_price' => 1000,
            'selling_price' => 1500,
            'reorder_level' => 2,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        return [$user, $supplier, $product];
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
            'status' => 'pending_confirmation',
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
