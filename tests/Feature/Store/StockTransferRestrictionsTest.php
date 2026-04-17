<?php

namespace Tests\Feature\Store;

use App\Models\Product;
use App\Models\Role;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StockTransferRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_manager_cannot_access_direct_stock_edit_routes(): void
    {
        $this->bootstrapStockContext();

        $storeManager = $this->makeUser('store_manager');
        $product = Product::query()->firstOrFail();
        $barLocation = StockLocation::bar();

        $this->actingAs($storeManager)
            ->get(route('store.stock.restock-form'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->post(route('store.stock.restock'), [
                'product_id' => $product->id,
                'location_id' => $barLocation->id,
                'quantity' => 2,
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->get(route('store.stock.damage-form'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->post(route('store.stock.damage'), [
                'product_id' => $product->id,
                'location_id' => $barLocation->id,
                'quantity' => 1,
                'reason' => 'Test damage',
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->get(route('store.adjustments.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_store_manager_can_create_and_complete_transfer_with_audit_records(): void
    {
        $this->bootstrapStockContext();

        $storeManager = $this->makeUser('store_manager');
        $product = Product::query()->firstOrFail();
        $mainStore = StockLocation::mainStore();
        $bar = StockLocation::bar();

        StockLevel::where('product_id', $product->id)
            ->where('location_id', $mainStore->id)
            ->update(['quantity' => 20, 'reserved_qty' => 0]);

        $this->actingAs($storeManager)
            ->post(route('store.transfers.store'), [
                'product_id' => $product->id,
                'to_location_code' => 'bar',
                'quantity' => 5,
                'reason' => 'Daily bar replenishment',
            ])
            ->assertRedirect(route('store.transfers.index'));

        $transfer = StockTransfer::query()->latest()->firstOrFail();

        $this->assertSame($storeManager->id, $transfer->requested_by);

        $this->actingAs($storeManager)
            ->post(route('store.transfers.fulfill', $transfer))
            ->assertRedirect(route('store.transfers.index'));

        $transfer->refresh();
        $this->assertSame('completed', $transfer->status);
        $this->assertSame($storeManager->id, $transfer->approved_by);
        $this->assertNotNull($transfer->approved_at);
        $this->assertSame($storeManager->id, $transfer->fulfilled_by);
        $this->assertNotNull($transfer->completed_at);

        $this->assertDatabaseHas('stock_levels', [
            'product_id' => $product->id,
            'location_id' => $mainStore->id,
            'quantity' => '15.000',
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'product_id' => $product->id,
            'location_id' => $bar->id,
            'quantity' => '5.000',
        ]);

        $this->assertSame(
            1,
            StockMovement::where('reference_type', 'transfer')
                ->where('reference_id', $transfer->id)
                ->where('type', 'transfer_out')
                ->count()
        );

        $this->assertSame(
            1,
            StockMovement::where('reference_type', 'transfer')
                ->where('reference_id', $transfer->id)
                ->where('type', 'transfer_in')
                ->count()
        );
    }

    public function test_transfer_rejection_requires_reason_and_records_rejection_audit(): void
    {
        $this->bootstrapStockContext();

        $storeManager = $this->makeUser('store_manager');
        $manager = $this->makeUser('manager');
        $product = Product::query()->firstOrFail();
        $mainStore = StockLocation::mainStore();

        StockLevel::where('product_id', $product->id)
            ->where('location_id', $mainStore->id)
            ->update(['quantity' => 8, 'reserved_qty' => 0]);

        $this->actingAs($storeManager)
            ->post(route('store.transfers.store'), [
                'product_id' => $product->id,
                'to_location_code' => 'kitchen',
                'quantity' => 3,
            ])
            ->assertRedirect(route('store.transfers.index'));

        $transfer = StockTransfer::query()->latest()->firstOrFail();

        $this->actingAs($manager)
            ->post(route('store.transfers.reject', $transfer), [])
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAs($manager)
            ->post(route('store.transfers.reject', $transfer), [
                'rejection_reason' => 'Insufficient documentation for request',
            ])
            ->assertRedirect(route('store.transfers.index'));

        $transfer->refresh();
        $this->assertSame('rejected', $transfer->status);
        $this->assertSame($manager->id, $transfer->rejected_by);
        $this->assertNotNull($transfer->rejected_at);
        $this->assertSame('Insufficient documentation for request', $transfer->rejection_reason);
    }

    private function bootstrapStockContext(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'StockLocationSeeder']);

        $storeManager = $this->makeUser('store_manager');

        Product::create([
            'name' => 'Cooking Oil',
            'sku' => 'OIL-001',
            'category' => 'Kitchen',
            'unit' => 'ltr',
            'cost_price' => 10000,
            'selling_price' => 12000,
            'reorder_level' => 2,
            'is_active' => true,
            'created_by' => $storeManager->id,
        ]);
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
