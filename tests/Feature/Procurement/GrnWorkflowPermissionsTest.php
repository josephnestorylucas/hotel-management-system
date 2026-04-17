<?php

namespace Tests\Feature\Procurement;

use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GrnWorkflowPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_storekeeper_can_confirm_but_store_manager_cannot_confirm(): void
    {
        [$storeKeeper, $storeManager, $manager, $grn] = $this->buildWorkflowContext();

        $this->actingAs($storeManager)
            ->post(route('procurement.grn.confirm', $grn))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeKeeper)
            ->post(route('procurement.grn.confirm', $grn))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('goods_received_notes', [
            'id' => $grn->id,
            'status' => GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
            'confirmed_by' => $storeKeeper->id,
        ]);
    }

    public function test_manager_can_approve_and_storekeeper_cannot(): void
    {
        [$storeKeeper, $storeManager, $manager, $grn] = $this->buildWorkflowContext();

        $this->actingAs($storeKeeper)->post(route('procurement.grn.confirm', $grn))->assertRedirect();

        $this->actingAs($storeKeeper)
            ->post(route('procurement.grn.approve', $grn))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($manager)
            ->post(route('procurement.grn.approve', $grn))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('goods_received_notes', [
            'id' => $grn->id,
            'status' => GoodsReceivedNote::STATUS_APPROVED,
            'approved_by' => $manager->id,
        ]);
    }

    public function test_rejection_requires_reason_and_is_recorded(): void
    {
        [$storeKeeper, $storeManager, $manager, $grn] = $this->buildWorkflowContext();

        $this->actingAs($storeKeeper)->post(route('procurement.grn.confirm', $grn))->assertRedirect();

        $this->actingAs($manager)
            ->post(route('procurement.grn.reject', $grn), ['rejection_reason' => ''])
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAs($manager)
            ->post(route('procurement.grn.reject', $grn), ['rejection_reason' => 'Damaged delivery items'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('goods_received_notes', [
            'id' => $grn->id,
            'status' => GoodsReceivedNote::STATUS_REJECTED,
            'rejected_by' => $manager->id,
            'rejection_reason' => 'Damaged delivery items',
        ]);
    }

    public function test_store_manager_has_read_only_grn_access(): void
    {
        [$storeKeeper, $storeManager, $manager, $grn] = $this->buildWorkflowContext();

        $this->actingAs($storeManager)
            ->get(route('procurement.grn.create'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->post(route('procurement.grn.submit', $grn))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->post(route('procurement.grn.approve', $grn))
            ->assertRedirect(route('dashboard'));
    }

    private function buildWorkflowContext(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);
        Artisan::call('db:seed', ['class' => 'StockLocationSeeder']);

        $storeKeeper = $this->makeUser('store_keeper');
        $storeManager = $this->makeUser('store_manager');
        $manager = $this->makeUser('manager');

        $supplier = Supplier::create([
            'name' => 'Workflow Supplier',
            'is_active' => true,
            'created_by' => $storeKeeper->id,
        ]);

        $product = Product::create([
            'name' => 'Workflow Product',
            'sku' => 'WF-001',
            'category' => 'General',
            'unit' => 'pcs',
            'cost_price' => 100,
            'selling_price' => 150,
            'reorder_level' => 1,
            'is_active' => true,
            'created_by' => $storeKeeper->id,
        ]);

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
            'quantity' => 10,
            'unit_price' => 100,
            'subtotal' => 1000,
        ]);

        $grn = GoodsReceivedNote::create([
            'lpo_id' => $lpo->id,
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => GoodsReceivedNote::STATUS_SUBMITTED,
            'received_by' => $storeManager->id,
        ]);

        GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'lpo_item_id' => $lpoItem->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'unit' => $product->unit,
            'quantity_ordered' => 10,
            'quantity_received' => 10,
            'unit_price' => 100,
            'subtotal' => 1000,
        ]);

        $grn->load('items');
        $grn->recalculate();

        return [$storeKeeper, $storeManager, $manager, $grn];
    }

    private function makeUser(string $roleName): User
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}

