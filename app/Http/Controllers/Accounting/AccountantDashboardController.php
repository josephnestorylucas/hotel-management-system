<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BankReconciliation;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\PayrollRun;
use App\Models\StoreNotification;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AccountantDashboardController extends Controller
{
    public function dashboard(): View
    {
        $snapshot = $this->snapshot();

        $recentTransactions = JournalEntry::with(['creator', 'supplier'])
            ->latest('entry_date')
            ->limit(8)
            ->get();

        $pendingPayables = $this->supplierPayables()->take(5);
        $pendingReceivables = Invoice::with('issuer')
            ->whereIn('status', ['draft', 'issued'])
            ->latest('invoice_date')
            ->limit(5)
            ->get();

        $insights = [
            [
                'label' => __('accountant.insights.cash_position'),
                'value' => $snapshot['cashPosition'],
                'tone' => $snapshot['cashPosition'] >= 0 ? 'positive' : 'negative',
            ],
            [
                'label' => __('accountant.insights.payables_exposure'),
                'value' => $snapshot['accountsPayable'],
                'tone' => $snapshot['accountsPayable'] > $snapshot['accountsReceivable'] ? 'warning' : 'neutral',
            ],
            [
                'label' => __('accountant.insights.outstanding_receivables'),
                'value' => $snapshot['accountsReceivable'],
                'tone' => $snapshot['accountsReceivable'] > 0 ? 'warning' : 'positive',
            ],
        ];

        return view('accountant.dashboard', compact(
            'snapshot',
            'recentTransactions',
            'pendingPayables',
            'pendingReceivables',
            'insights'
        ));
    }

    public function overview(): View
    {
        $snapshot = $this->snapshot();
        $monthlyTrend = [
            'revenue' => Account::where('type', 'revenue')->where('is_active', true)->get()->sum(fn (Account $account) => $account->getBalance(now()->startOfMonth()->toDateString(), now()->toDateString())),
            'expenses' => Account::whereIn('type', ['expense', 'cogs'])->where('is_active', true)->get()->sum(fn (Account $account) => $account->getBalance(now()->startOfMonth()->toDateString(), now()->toDateString())),
            'bank_reconciliations' => BankReconciliation::latest('statement_date')->limit(5)->get(),
            'payrolls' => PayrollRun::latest('pay_date')->limit(5)->get(),
        ];

        return view('accountant.overview', compact('snapshot', 'monthlyTrend'));
    }

    public function transactions(): View
    {
        $transactions = JournalEntry::with(['creator', 'poster', 'supplier'])
            ->latest('entry_date')
            ->limit(30)
            ->get();

        return view('accountant.transactions', compact('transactions'));
    }

    public function accountsPayable(): View
    {
        $payables = $this->supplierPayables();
        $totalOutstanding = (float) $payables->sum('balance');
        $recentReconciliations = BankReconciliation::with('account')
            ->latest('statement_date')
            ->limit(5)
            ->get();

        return view('accountant.accounts-payable', compact('payables', 'totalOutstanding', 'recentReconciliations'));
    }

    public function accountsReceivable(): View
    {
        $receivables = Invoice::with('issuer')
            ->whereIn('status', ['draft', 'issued'])
            ->latest('invoice_date')
            ->limit(30)
            ->get();

        $totalReceivables = (float) $receivables->sum('total');

        return view('accountant.accounts-receivable', compact('receivables', 'totalReceivables'));
    }

    public function expenses(): View
    {
        $expenseAccounts = Account::whereIn('type', ['expense', 'cogs'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn (Account $account) => [
                'account' => $account,
                'balance' => $account->getBalance(now()->startOfMonth()->toDateString(), now()->toDateString()),
            ]);

        $recentExpenseLines = JournalLine::with(['entry', 'account'])
            ->whereHas('account', fn ($query) => $query->whereIn('type', ['expense', 'cogs']))
            ->where('type', 'debit')
            ->latest()
            ->limit(20)
            ->get();

        return view('accountant.expenses', compact('expenseAccounts', 'recentExpenseLines'));
    }

    public function reports(): View
    {
        $snapshot = $this->snapshot();
        $dateFrom = request('date_from', now()->startOfMonth()->toDateString());
        $dateTo = request('date_to', now()->toDateString());
        $reportMetrics = $this->reportMetrics($dateFrom, $dateTo);
        $reportQuery = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
        $asOfQuery = [
            'as_of' => $dateTo,
        ];

        $reportLinks = [
            ['label' => __('accountant.reports.profit_loss'), 'route' => 'accounting.reports.profit-loss', 'query' => $reportQuery],
            ['label' => __('accountant.reports.balance_sheet'), 'route' => 'accounting.reports.balance-sheet', 'query' => $asOfQuery],
            ['label' => __('accountant.reports.cashflow_summary'), 'route' => 'accounting.reports.cashflow-summary', 'query' => $reportQuery],
            ['label' => __('accountant.reports.ap_aging'), 'route' => 'accounting.reports.ap-aging', 'query' => $asOfQuery],
            ['label' => __('accountant.reports.receipts_summary'), 'route' => 'accounting.reports.receipts-summary', 'query' => $reportQuery],
        ];

        return view('accountant.reports', compact('snapshot', 'reportMetrics', 'reportLinks', 'dateFrom', 'dateTo'));
    }

    public function auditLogs(): View
    {
        $logs = StoreNotification::query()
            ->latest('created_at')
            ->limit(20)
            ->get();

        $journalTrail = JournalEntry::with(['creator', 'poster'])
            ->latest('updated_at')
            ->limit(12)
            ->get();

        return view('accountant.audit-logs', compact('logs', 'journalTrail'));
    }

    private function snapshot(): array
    {
        $revenue = Account::where('type', 'revenue')->where('is_active', true)->get()->sum(fn (Account $account) => $account->getBalance());
        $expenses = Account::whereIn('type', ['expense', 'cogs'])->where('is_active', true)->get()->sum(fn (Account $account) => $account->getBalance());

        return [
            'totalRevenue' => (float) $revenue,
            'totalExpenses' => (float) $expenses,
            'netProfit' => (float) ($revenue - $expenses),
            'accountsPayable' => (float) Account::findByCode('2100')->getBalance(),
            'accountsReceivable' => (float) Account::findByCode('1300')->getBalance(),
            'cashPosition' => (float) Account::findByCode('1100')->getBalance(),
            'pendingInvoices' => Invoice::whereIn('status', ['draft', 'issued'])->count(),
            'openPayrollRuns' => PayrollRun::where('status', 'draft')->count(),
            'draftSupplierPayments' => SupplierPayment::where('status', 'draft')->count(),
        ];
    }

    private function reportMetrics(string $dateFrom, string $dateTo): array
    {
        $revenue = Account::where('type', 'revenue')
            ->where('is_active', true)
            ->get()
            ->sum(fn (Account $account) => $account->getBalance($dateFrom, $dateTo));

        $expenses = Account::whereIn('type', ['expense', 'cogs'])
            ->where('is_active', true)
            ->get()
            ->sum(fn (Account $account) => $account->getBalance($dateFrom, $dateTo));

        return [
            'totalRevenue' => (float) $revenue,
            'totalExpenses' => (float) $expenses,
            'netProfit' => (float) ($revenue - $expenses),
        ];
    }

    private function supplierPayables(): Collection
    {
        return SupplierPayable::with('supplier')
            ->orderByDesc('payable_date')
            ->get()
            ->groupBy('supplier_id')
            ->map(function (Collection $payables) {
                return [
                    'supplier' => $payables->first()->supplier,
                    'invoiced' => (float) $payables->sum('amount_total'),
                    'paid' => (float) $payables->sum('amount_paid'),
                    'balance' => (float) $payables->sum('balance'),
                ];
            })
            ->sortByDesc('balance')
            ->values();
    }
}
