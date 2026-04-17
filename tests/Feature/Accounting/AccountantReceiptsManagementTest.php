<?php

namespace Tests\Feature\Accounting;

use App\Contracts\ReceiptPrintable;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\User;
use App\Services\ReceiptService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccountantReceiptsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_receipts_index_loads_and_filters_by_module_and_receipt_number(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $accountant = $this->makeUser('ACCOUNTANT');

        $restaurant = Receipt::create([
            'module' => 'restaurant',
            'receipt_number' => 'HMS-2026-111111',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total' => 50000,
            'subtotal' => 50000,
            'amount_paid' => 50000,
            'balance' => 0,
            'currency' => 'TZS',
            'issued_at' => now(),
        ]);

        Receipt::create([
            'module' => 'laundry',
            'receipt_number' => 'HMS-2026-222222',
            'payment_method' => 'card',
            'payment_status' => 'unpaid',
            'total' => 25000,
            'subtotal' => 25000,
            'amount_paid' => 0,
            'balance' => 25000,
            'currency' => 'TZS',
            'issued_at' => now()->subDay(),
        ]);

        $this->actingAs($accountant)
            ->get(route('accountant.receipts.index', [
                'module' => 'restaurant',
                'receipt_number' => '111111',
            ]))
            ->assertOk()
            ->assertSee($restaurant->receipt_number)
            ->assertDontSee('HMS-2026-222222');
    }

    public function test_non_accountant_cannot_access_receipts_center(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $frontDesk = $this->makeUser('front_desk');

        $this->actingAs($frontDesk)
            ->get(route('accountant.receipts.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_reprint_keeps_same_receipt_number_for_accountant(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $accountant = $this->makeUser('ACCOUNTANT');

        $receipt = Receipt::create([
            'module' => 'checkout',
            'receipt_number' => 'HMS-2026-333333',
            'payment_status' => 'paid',
            'subtotal' => 10000,
            'total' => 10000,
            'amount_paid' => 10000,
            'balance' => 0,
            'currency' => 'TZS',
            'issued_at' => now(),
        ]);

        $this->actingAs($accountant)
            ->get(route('receipts.reprint', $receipt->receipt_number))
            ->assertOk()
            ->assertSee($receipt->receipt_number);

        $this->assertDatabaseHas('receipts', [
            'id' => $receipt->id,
            'receipt_number' => 'HMS-2026-333333',
        ]);
    }

    public function test_receipt_service_preserves_source_receipt_number_when_creating_receipt(): void
    {
        $service = app(ReceiptService::class);

        $source = new class extends Model implements ReceiptPrintable {
            protected $table = 'users';
            public $timestamps = false;

            public function toReceiptData(): array
            {
                return [
                    'receipt_no' => 'SRC-778899',
                    'issued_at' => now(),
                    'module' => 'restaurant',
                    'customer_name' => 'Walk-in Guest',
                    'customer_phone' => '255700000000',
                    'items' => [
                        [
                            'name' => 'Lunch Buffet',
                            'quantity' => 1,
                            'unit_price' => 25000,
                            'amount' => 25000,
                        ],
                    ],
                    'subtotal' => 25000,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 25000,
                    'amount_paid' => 25000,
                    'balance' => 0,
                    'currency' => 'TZS',
                    'payment_method' => 'cash',
                    'payment_status' => 'paid',
                    'transaction_reference' => 'TXN-4455',
                    'cashier' => 'Cashier One',
                    'notes' => 'Captured from source record',
                ];
            }

            public function getReceiptModule(): string
            {
                return 'restaurant';
            }

            public function isPaid(): bool
            {
                return true;
            }
        };

        $source->setAttribute($source->getKeyName(), 123);

        $receipt = $service->createReceipt($source);

        $this->assertSame('SRC-778899', $receipt->receipt_number);
        $this->assertDatabaseHas('receipts', [
            'id' => $receipt->id,
            'receipt_number' => 'SRC-778899',
            'transaction_reference' => 'TXN-4455',
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

