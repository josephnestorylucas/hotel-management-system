<?php

namespace Tests\Feature\Accounting;

use App\Models\PettyCash;
use App\Models\User;
use App\Models\Role;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class PettyCashTest extends TestCase
{
    use RefreshDatabase;

    public function test_petty_cash_approval_creates_journal_entry()
    {
        // Seed roles and chart of accounts
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);

        // Get store_manager role (required for petty cash approval route)
        $role = Role::where('name', 'store_manager')->firstOrFail();

        // Create a user with admin role
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        // Create a petty cash expense (draft)
        $pettyCash = PettyCash::create([
            'category'      => 'transport',
            'amount'        => 50000,
            'description'   => 'Taxi to airport',
            'requested_by'  => $user->id,
            'status'        => 'draft',
        ]);

        // Authenticate as the user
        $this->actingAs($user);

        // Send POST request to approve
        $response = $this->post(route('finance.petty-cash.approve', $pettyCash->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert petty cash status updated
        $this->assertDatabaseHas('petty_cash_expenses', [
            'id' => $pettyCash->id,
            'status' => 'approved',
        ]);

        // Assert a journal entry created with source 'petty_cash'
        $journalEntry = JournalEntry::where('source', 'petty_cash')
            ->where('reference', $pettyCash->reference_no)
            ->first();

        $this->assertNotNull($journalEntry);
        $this->assertEquals($pettyCash->amount, $journalEntry->total_debit);
        $this->assertEquals($pettyCash->amount, $journalEntry->total_credit);

        // Assert debit line to expense account (6600) and credit line to cash (1100)
        $debitLine = $journalEntry->lines()->where('type', 'debit')->first();
        $creditLine = $journalEntry->lines()->where('type', 'credit')->first();

        $this->assertNotNull($debitLine);
        $this->assertNotNull($creditLine);

        $expenseAccount = Account::where('code', '6600')->first();
        $cashAccount = Account::where('code', '1100')->first();

        $this->assertEquals($expenseAccount->id, $debitLine->account_id);
        $this->assertEquals($cashAccount->id, $creditLine->account_id);
        $this->assertEquals($pettyCash->amount, $debitLine->amount);
        $this->assertEquals($pettyCash->amount, $creditLine->amount);
    }
}
