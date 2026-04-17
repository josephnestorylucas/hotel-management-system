<?php

namespace Tests\Feature\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ManagerAccountingUiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_accounting_routes_render_and_post_actions_work(): void
    {
        [$manager, $journalEntry] = $this->bootstrapManagerAccountingContext();

        $this->actingAs($manager)
            ->get(route('manager.accounting.journal.show', $journalEntry))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('manager.accounting.reports.supplier-payables'))
            ->assertOk();

        $this->actingAs($manager)
            ->post(route('manager.accounting.journal.post', $journalEntry))
            ->assertRedirect(route('manager.accounting.journal.show', $journalEntry));

        $journalEntry->refresh();
        $this->assertSame('posted', $journalEntry->status);

        $this->actingAs($manager)
            ->post(route('manager.accounting.journal.reverse', $journalEntry), ['reason' => 'Smoke reversal'])
            ->assertRedirect(route('manager.accounting.journal.show', $journalEntry));

        $journalEntry->refresh();
        $this->assertSame('reversed', $journalEntry->status);
    }

    private function bootstrapManagerAccountingContext(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);

        $managerRole = Role::where('name', 'manager')->firstOrFail();
        $accountantRole = Role::where('name', 'ACCOUNTANT')->firstOrFail();

        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        $accountant = User::factory()->create([
            'role_id' => $accountantRole->id,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Manager Smoke Supplier',
            'is_active' => true,
            'created_by' => $accountant->id,
        ]);

        $journalEntry = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'Manager smoke journal entry',
            'reference' => 'MGR-SMOKE-001',
            'source' => 'manual',
            'total_debit' => 320,
            'total_credit' => 320,
            'status' => 'draft',
            'created_by' => $accountant->id,
            'supplier_id' => $supplier->id,
        ]);

        $journalEntry->lines()->createMany([
            [
                'account_id' => Account::where('code', '1100')->value('id'),
                'type' => 'debit',
                'amount' => 320,
                'notes' => 'Manager smoke debit',
            ],
            [
                'account_id' => Account::where('code', '2100')->value('id'),
                'type' => 'credit',
                'amount' => 320,
                'notes' => 'Manager smoke credit',
            ],
        ]);

        return [$manager, $journalEntry];
    }
}
