<?php

namespace Tests\Feature\Accounting;

use App\Models\Account;
use App\Models\BankReconciliation;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\PayrollRun;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccountingUiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_accounting_pages_render_without_server_errors(): void
    {
        [$accountant, $resources] = $this->bootstrapAccountingUiContext();

        $routeAssertions = [
            ['name' => 'accountant.dashboard'],
            ['name' => 'accountant.overview'],
            ['name' => 'accountant.transactions'],
            ['name' => 'accountant.payables.dashboard'],
            ['name' => 'accountant.payables.index'],
            ['name' => 'accountant.payables.show', 'parameters' => $resources['payable']],
            ['name' => 'accountant.payments.create'],
            ['name' => 'accountant.payments.apply', 'parameters' => $resources['payment']],
            ['name' => 'accountant.accounts-receivable'],
            ['name' => 'accountant.receipts.index'],
            ['name' => 'accountant.receipts.show', 'parameters' => $resources['receipt']],
            ['name' => 'accountant.expenses'],
            ['name' => 'accountant.reports'],
            ['name' => 'accountant.audit-logs'],
            ['name' => 'accounting.dashboard'],
            ['name' => 'accounting.accounts.index'],
            ['name' => 'accounting.accounts.create'],
            ['name' => 'accounting.accounts.edit', 'parameters' => $resources['account']],
            ['name' => 'accounting.journal.index'],
            ['name' => 'accounting.journal.create'],
            ['name' => 'accounting.journal.show', 'parameters' => $resources['journalEntry']],
            ['name' => 'accounting.journal.edit', 'parameters' => $resources['journalEntry']],
            ['name' => 'accounting.ledger', 'query' => ['account_id' => $resources['account']->id]],
            ['name' => 'accounting.invoices.index'],
            ['name' => 'accounting.invoices.show', 'parameters' => $resources['invoice']],
            ['name' => 'accounting.payroll.index'],
            ['name' => 'accounting.payroll.create'],
            ['name' => 'accounting.payroll.show', 'parameters' => $resources['payrollRun']],
            ['name' => 'accounting.reconciliation.index'],
            ['name' => 'accounting.reconciliation.create'],
            ['name' => 'accounting.reconciliation.show', 'parameters' => $resources['reconciliation']],
            ['name' => 'accounting.reports.profit-loss'],
            ['name' => 'accounting.reports.balance-sheet'],
            ['name' => 'accounting.reports.cashflow-summary'],
            ['name' => 'accounting.reports.ap-aging'],
            ['name' => 'accounting.reports.receipts-summary'],
            ['name' => 'accounting.reports.trial-balance'],
            ['name' => 'accounting.reports.vat'],
            ['name' => 'accounting.reports.supplier-payables'],
        ];

        foreach ($routeAssertions as $routeAssertion) {
            $url = route(
                $routeAssertion['name'],
                $routeAssertion['parameters'] ?? []
            );

            if (! empty($routeAssertion['query'])) {
                $url .= '?' . http_build_query($routeAssertion['query']);
            }

            $this->actingAs($accountant)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_reports_center_propagates_selected_period_to_report_links(): void
    {
        [$accountant] = $this->bootstrapAccountingUiContext();

        $dateFrom = now()->subDays(7)->toDateString();
        $dateTo = now()->toDateString();

        $response = $this->actingAs($accountant)
            ->get(route('accountant.reports', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]));

        $response->assertOk();
        $response->assertSee('accounting/reports/profit-loss?date_from=' . $dateFrom . '&amp;date_to=' . $dateTo, false);
        $response->assertSee('accounting/reports/ap-aging?as_of=' . $dateTo, false);
    }

    public function test_reports_center_summary_cards_use_selected_period_metrics(): void
    {
        [$accountant] = $this->bootstrapAccountingUiContext();

        $cashAccountId = Account::where('code', '1100')->value('id');
        $revenueAccountId = Account::where('type', 'revenue')->orderBy('code')->value('id');
        $expenseAccountId = Account::where('type', 'expense')->orderBy('code')->value('id');

        $outsideRangeEntry = JournalEntry::create([
            'entry_date' => now()->subMonths(2)->toDateString(),
            'description' => 'Outside range sale',
            'reference' => 'OUTSIDE-RANGE-001',
            'source' => 'manual',
            'total_debit' => 500,
            'total_credit' => 500,
            'status' => 'posted',
            'created_by' => $accountant->id,
            'posted_by' => $accountant->id,
            'posted_at' => now()->subMonths(2),
        ]);

        $outsideRangeEntry->lines()->createMany([
            ['account_id' => $cashAccountId, 'type' => 'debit', 'amount' => 500],
            ['account_id' => $revenueAccountId, 'type' => 'credit', 'amount' => 500],
        ]);

        $inRangeSale = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'In range sale',
            'reference' => 'IN-RANGE-SALE-001',
            'source' => 'manual',
            'total_debit' => 200,
            'total_credit' => 200,
            'status' => 'posted',
            'created_by' => $accountant->id,
            'posted_by' => $accountant->id,
            'posted_at' => now(),
        ]);

        $inRangeSale->lines()->createMany([
            ['account_id' => $cashAccountId, 'type' => 'debit', 'amount' => 200],
            ['account_id' => $revenueAccountId, 'type' => 'credit', 'amount' => 200],
        ]);

        $inRangeExpense = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'In range expense',
            'reference' => 'IN-RANGE-EXP-001',
            'source' => 'manual',
            'total_debit' => 80,
            'total_credit' => 80,
            'status' => 'posted',
            'created_by' => $accountant->id,
            'posted_by' => $accountant->id,
            'posted_at' => now(),
        ]);

        $inRangeExpense->lines()->createMany([
            ['account_id' => $expenseAccountId, 'type' => 'debit', 'amount' => 80],
            ['account_id' => $cashAccountId, 'type' => 'credit', 'amount' => 80],
        ]);

        $response = $this->actingAs($accountant)
            ->get(route('accountant.reports', [
                'date_from' => now()->startOfMonth()->toDateString(),
                'date_to' => now()->toDateString(),
            ]));

        $response->assertOk()
            ->assertViewHas('reportMetrics', function (array $reportMetrics) {
                return $reportMetrics['totalRevenue'] === 200.0
                    && $reportMetrics['totalExpenses'] === 80.0
                    && $reportMetrics['netProfit'] === 120.0;
            });
    }

    private function bootstrapAccountingUiContext(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);

        $accountantRole = Role::where('name', 'ACCOUNTANT')->firstOrFail();
        $accountant = User::factory()->create([
            'role_id' => $accountantRole->id,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Smoke Supplier',
            'is_active' => true,
            'created_by' => $accountant->id,
        ]);

        $payable = SupplierPayable::create([
            'supplier_id' => $supplier->id,
            'reference' => 'GRN-SMOKE-001',
            'payable_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount_total' => 750,
            'amount_paid' => 250,
            'balance' => 500,
            'status' => 'partial',
            'source_module' => 'procurement',
            'source_reference_type' => 'grn',
            'source_reference_id' => fake()->uuid(),
            'created_by' => $accountant->id,
            'notes' => 'Smoke test payable',
        ]);

        $payment = SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'payment_date' => now()->toDateString(),
            'currency' => 'USD',
            'amount' => 250,
            'method' => 'bank',
            'reference' => 'SUPPAY-SMOKE-001',
            'status' => 'draft',
            'created_by' => $accountant->id,
            'notes' => 'Smoke test payment',
        ]);

        $payment->allocations()->create([
            'supplier_payable_id' => $payable->id,
            'allocated_amount' => 250,
            'created_by' => $accountant->id,
        ]);

        $account = Account::where('code', '1100')->firstOrFail();

        $journalEntry = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'Smoke journal entry',
            'reference' => 'SMOKE-JRN-001',
            'source' => 'manual',
            'total_debit' => 250,
            'total_credit' => 250,
            'status' => 'draft',
            'created_by' => $accountant->id,
            'supplier_id' => $supplier->id,
        ]);

        $journalEntry->lines()->createMany([
            [
                'account_id' => Account::where('code', '1100')->value('id'),
                'type' => 'debit',
                'amount' => 250,
                'notes' => 'Smoke debit',
            ],
            [
                'account_id' => Account::where('code', '2100')->value('id'),
                'type' => 'credit',
                'amount' => 250,
                'notes' => 'Smoke credit',
            ],
        ]);

        $invoice = Invoice::create([
            'invoice_type' => 'restaurant',
            'guest_name' => 'Smoke Guest',
            'invoice_date' => now()->toDateString(),
            'subtotal' => 100,
            'discount' => 0,
            'tax_amount' => 18,
            'total' => 118,
            'payment_method' => 'cash',
            'status' => 'issued',
            'notes' => 'Smoke invoice',
            'issued_by' => $accountant->id,
        ]);

        $invoice->lines()->create([
            'description' => 'Room service',
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
            'is_taxable' => true,
        ]);

        $payrollRun = PayrollRun::create([
            'period_month' => now()->format('Y-m'),
            'pay_date' => now()->toDateString(),
            'status' => 'draft',
            'notes' => 'Smoke payroll',
            'prepared_by' => $accountant->id,
        ]);

        $payrollRun->lines()->create([
            'user_id' => $accountant->id,
            'staff_name' => $accountant->name,
            'role' => 'ACCOUNTANT',
            'basic_salary' => 1000,
            'allowances' => 100,
            'gross_salary' => 1100,
            'nssf_employee' => 55,
            'nssf_employer' => 165,
            'paye' => 40,
            'net_salary' => 1005,
        ]);

        $payrollRun->recalculate();

        $reconciliation = BankReconciliation::create([
            'account_id' => Account::where('code', '1200')->value('id') ?? $account->id,
            'period_month' => now()->format('Y-m'),
            'statement_date' => now()->toDateString(),
            'statement_opening_balance' => 1000,
            'statement_closing_balance' => 1000,
            'system_opening_balance' => 1000,
            'system_closing_balance' => 1000,
            'difference' => 0,
            'status' => 'reconciled',
            'notes' => 'Smoke reconciliation',
            'prepared_by' => $accountant->id,
        ]);

        $receipt = Receipt::create([
            'module' => 'restaurant',
            'receipt_number' => 'HMS-SMOKE-0001',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total' => 118,
            'subtotal' => 100,
            'amount_paid' => 118,
            'balance' => 0,
            'currency' => 'USD',
            'issued_at' => now(),
            'customer_name' => 'Smoke Guest',
        ]);

        return [$accountant, [
            'account' => $account,
            'invoice' => $invoice,
            'journalEntry' => $journalEntry,
            'payable' => $payable,
            'payment' => $payment,
            'payrollRun' => $payrollRun,
            'receipt' => $receipt,
            'reconciliation' => $reconciliation,
        ]];
    }
}
