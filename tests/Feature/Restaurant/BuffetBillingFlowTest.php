<?php

namespace Tests\Feature\Restaurant;

use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\BuffetPackage;
use App\Models\BuffetSale;
use App\Models\Building;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BuffetBillingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_restaurant_manager_can_create_buffet_package(): void
    {
        $manager = $this->makeUser('restaurant_manager');

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.packages.store'), [
                'name' => 'Breakfast Buffet',
                'adult_price' => 25000,
                'child_price' => 15000,
                'available_days' => ['monday', 'tuesday'],
                'start_time' => '06:00',
                'end_time' => '10:00',
                'is_active' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('buffet_packages', [
            'name' => 'Breakfast Buffet',
            'adult_price' => 25000,
            'child_price' => 15000,
            'is_active' => 1,
            'created_by' => $manager->id,
        ]);
    }

    public function test_restaurant_manager_can_update_buffet_package_schedule_and_prices(): void
    {
        $manager = $this->makeUser('restaurant_manager');
        $package = BuffetPackage::create([
            'name' => 'Breakfast Buffet',
            'adult_price' => 25000,
            'child_price' => 15000,
            'available_days' => ['monday'],
            'start_time' => '06:00:00',
            'end_time' => '10:00:00',
            'is_active' => true,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->put(route('restaurant.buffet.packages.update', $package), [
                'name' => 'Late Breakfast Buffet',
                'adult_price' => 28000,
                'child_price' => 18000,
                'available_days' => ['monday', 'wednesday'],
                'start_time' => '07:00',
                'end_time' => '11:00',
                'is_active' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $package->refresh();
        $this->assertSame('Late Breakfast Buffet', $package->name);
        $this->assertEquals(28000, (float) $package->adult_price);
        $this->assertEquals(18000, (float) $package->child_price);
        $this->assertSame(['monday', 'wednesday'], $package->available_days);
    }

    public function test_walkin_buffet_sale_can_be_settled_with_payment_and_receipt(): void
    {
        $manager = $this->makeUser('restaurant_manager');
        $package = BuffetPackage::create([
            'name' => 'Lunch Buffet',
            'adult_price' => 30000,
            'child_price' => 15000,
            'available_days' => [],
            'is_active' => true,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.store'), [
                'buffet_package_id' => $package->id,
                'sale_type' => 'walkin',
                'adults_count' => 2,
                'children_count' => 1,
                'notes' => 'Family table',
            ])
            ->assertRedirect();

        $sale = BuffetSale::query()->latest()->firstOrFail();
        $this->assertSame('pending', $sale->status);
        $this->assertEquals(75000, (float) $sale->total_amount);

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.settle-walkin', $sale), [
                'payment_method' => 'cash',
                'payment_reference' => 'BUF-CASH-001',
            ])
            ->assertRedirect(route('restaurant.buffet.show', $sale))
            ->assertSessionHas('success');

        $sale->refresh();
        $this->assertSame('settled', $sale->status);
        $this->assertNotNull($sale->settled_at);
        $this->assertSame($manager->id, $sale->settled_by);

        $this->assertDatabaseHas('finance_payments', [
            'payment_type' => 'walkin',
            'method' => 'cash',
            'status' => 'completed',
            'reference' => 'BUF-CASH-001',
            'amount' => 75000,
            'created_by' => $manager->id,
        ]);

        $payment = FinancePayment::query()->latest()->firstOrFail();
        $this->assertDatabaseHas('financial_transactions', [
            'payment_id' => $payment->id,
            'type' => 'walkin_sale',
            'source_module' => 'restaurant',
            'amount' => 75000,
        ]);
        $this->assertSame(1, FinancialTransaction::where('payment_id', $payment->id)->count());

        $receipt = Receipt::where('receiptable_type', BuffetSale::class)
            ->where('receiptable_id', $sale->id)
            ->first();
        $this->assertNotNull($receipt);
        $this->assertEquals(75000, (float) $receipt->total);
        $this->assertSame('cash', $receipt->payment_method);
        $this->assertSame('BUF-CASH-001', $receipt->transaction_reference);

        $sale->refresh();
        $this->assertSame('cash', $sale->payment_method);
        $this->assertSame('BUF-CASH-001', $sale->payment_reference);
    }

    public function test_booking_linked_buffet_sale_charges_guest_folio(): void
    {
        $manager = $this->makeUser('restaurant_manager');
        $booking = $this->createCheckedInBooking($manager);

        $package = BuffetPackage::create([
            'name' => 'Dinner Buffet',
            'adult_price' => 35000,
            'child_price' => 10000,
            'available_days' => [],
            'is_active' => true,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.store'), [
                'buffet_package_id' => $package->id,
                'sale_type' => 'booking',
                'booking_id' => $booking->id,
                'adults_count' => 2,
                'children_count' => 1,
            ])
            ->assertRedirect();

        $sale = BuffetSale::query()->latest()->firstOrFail();

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.charge-booking', $sale))
            ->assertRedirect(route('finance.checkout.show', $booking->id))
            ->assertSessionHas('success');

        $sale->refresh();
        $this->assertSame('charged', $sale->status);

        $this->assertDatabaseHas('booking_charges', [
            'booking_id' => $booking->id,
            'source' => 'restaurant',
            'reference_id' => $sale->id,
            'charge_type' => 'restaurant',
            'status' => 'unpaid',
            'created_by' => $manager->id,
        ]);

        $charge = BookingCharge::where('reference_id', $sale->id)->firstOrFail();
        $this->assertEquals((float) $sale->total_amount, (float) $charge->amount_tzs);
    }

    public function test_sale_creation_is_blocked_for_inactive_or_unavailable_package(): void
    {
        $manager = $this->makeUser('restaurant_manager');

        Carbon::setTestNow(Carbon::create(2026, 4, 20, 10, 0, 0)); // Monday

        $inactive = BuffetPackage::create([
            'name' => 'Inactive Package',
            'adult_price' => 20000,
            'child_price' => 5000,
            'available_days' => [],
            'is_active' => false,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->from(route('restaurant.buffet.create'))
            ->post(route('restaurant.buffet.store'), [
                'buffet_package_id' => $inactive->id,
                'sale_type' => 'walkin',
                'adults_count' => 1,
                'children_count' => 0,
            ])
            ->assertRedirect(route('restaurant.buffet.create'))
            ->assertSessionHasErrors('buffet_package_id');

        $unavailable = BuffetPackage::create([
            'name' => 'Tuesday Only',
            'adult_price' => 22000,
            'child_price' => 7000,
            'available_days' => ['tuesday'],
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->from(route('restaurant.buffet.create'))
            ->post(route('restaurant.buffet.store'), [
                'buffet_package_id' => $unavailable->id,
                'sale_type' => 'walkin',
                'adults_count' => 1,
                'children_count' => 0,
            ])
            ->assertRedirect(route('restaurant.buffet.create'))
            ->assertSessionHasErrors('buffet_package_id');

        $this->assertSame(0, BuffetSale::count());
        Carbon::setTestNow();
    }

    public function test_historical_sale_snapshot_is_preserved_after_package_price_change(): void
    {
        $manager = $this->makeUser('restaurant_manager');
        $package = BuffetPackage::create([
            'name' => 'Brunch Buffet',
            'adult_price' => 25000,
            'child_price' => 10000,
            'available_days' => [],
            'is_active' => true,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.store'), [
                'buffet_package_id' => $package->id,
                'sale_type' => 'walkin',
                'adults_count' => 2,
                'children_count' => 0,
            ])
            ->assertRedirect();

        $sale = BuffetSale::query()->latest()->firstOrFail();
        $this->assertEquals(50000, (float) $sale->total_amount);
        $this->assertEquals(25000, (float) $sale->adult_price_snapshot);
        $this->assertSame('Brunch Buffet', $sale->package_name_snapshot);

        $package->update([
            'name' => 'Brunch Buffet New',
            'adult_price' => 45000,
            'child_price' => 20000,
        ]);

        $this->actingAs($manager)
            ->post(route('restaurant.buffet.settle-walkin', $sale), [
                'payment_method' => 'card',
                'payment_reference' => 'BUF-CARD-001',
            ])
            ->assertRedirect();

        $sale->refresh();
        $this->assertSame('Brunch Buffet', $sale->package_name_snapshot);
        $this->assertEquals(25000, (float) $sale->adult_price_snapshot);
        $this->assertEquals(10000, (float) $sale->child_price_snapshot);
        $this->assertEquals(50000, (float) $sale->total_amount);

        $receipt = Receipt::where('receiptable_type', BuffetSale::class)
            ->where('receiptable_id', $sale->id)
            ->firstOrFail();
        $this->assertEquals(50000, (float) $receipt->total);
    }

    private function makeUser(string $roleName): User
    {
        $role = Role::updateOrCreate(
            ['name' => $roleName],
            ['description' => $roleName]
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function createCheckedInBooking(User $actor): Booking
    {
        $building = Building::create([
            'name' => 'Main Building',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $floor = Floor::create([
            'building_id' => $building->id,
            'name' => 'Ground',
            'floor_number' => 1,
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'name' => 'Standard',
            'code' => 'STD',
            'base_rate' => 100,
            'max_occupancy' => 2,
        ]);

        $room = Room::create([
            'floor_id' => $floor->id,
            'room_type_id' => $roomType->id,
            'room_number' => '101',
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $guest = Guest::create([
            'first_name' => 'John',
            'last_name' => 'Guest',
            'phone_number' => '+255700111222',
            'email' => 'guest@example.test',
        ]);

        return Booking::create([
            'booking_number' => 'BK-TST-' . now()->format('YmdHisv'),
            'guest_id' => $guest->id,
            'guest_name' => 'John Guest',
            'guest_email' => 'guest@example.test',
            'guest_phone' => '+255700111222',
            'room_id' => $room->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'number_of_guests' => 2,
            'total_amount' => 100,
            'status' => 'checked_in',
            'source' => 'frontdesk',
            'created_by' => $actor->id,
        ]);
    }
}

