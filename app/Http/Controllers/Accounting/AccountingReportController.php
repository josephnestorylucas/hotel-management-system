<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingReportController extends Controller
{
    // GET /accounting/reports/profit-loss
    public function profitLoss(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Revenue accounts (4xxx)
        $revenueAccounts = Account::where('type', 'revenue')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account'  => $acc,
                'balance'  => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // COGS accounts (5xxx)
        $cogsAccounts = Account::where('type', 'cogs')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // Expense accounts (6xxx)
        $expenseAccounts = Account::where('type', 'expense')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        $totalRevenue  = $revenueAccounts->sum('balance');
        $totalCogs     = $cogsAccounts->sum('balance');
        $grossProfit   = $totalRevenue - $totalCogs;
        $totalExpenses = $expenseAccounts->sum('balance');
        $netProfit     = $grossProfit - $totalExpenses;

        return view('accounting.reports.profit-loss', compact(
            'revenueAccounts', 'cogsAccounts', 'expenseAccounts',
            'totalRevenue', 'totalCogs', 'grossProfit',
            'totalExpenses', 'netProfit',
            'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/reports/balance-sheet
    public function balanceSheet(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $assets      = Account::where('type', 'asset')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $liabilities = Account::where('type', 'liability')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $equity      = Account::where('type', 'equity')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);

        $totalAssets      = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity      = $equity->sum('balance');

        return view('accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'asOf'
        ));
    }

    // GET /accounting/reports/trial-balance
    public function trialBalance(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOf) {
                $rawBalance = $acc->getBalance(null, $asOf);
                return [
                    'account' => $acc,
                    'debit'   => $acc->normal_balance === 'debit'  && $rawBalance > 0 ? $rawBalance : 0,
                    'credit'  => $acc->normal_balance === 'credit' && $rawBalance > 0 ? $rawBalance : 0,
                ];
            })
            ->filter(fn($row) => $row['debit'] + $row['credit'] > 0);

        $totalDebits  = $accounts->sum('debit');
        $totalCredits = $accounts->sum('credit');

        return view('accounting.reports.trial-balance', compact(
            'accounts', 'totalDebits', 'totalCredits', 'asOf'
        ));
    }

    // GET /accounting/reports/vat
    public function vatReport(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Output VAT collected (account 2200)
        $outputVatAccount = Account::findByCode('2200');
        $outputVat = $outputVatAccount->getBalance($dateFrom, $dateTo);

        // Input VAT paid on purchases (account 2300)
        $inputVatAccount = Account::findByCode('2300');
        $inputVat = $inputVatAccount->getBalance($dateFrom, $dateTo);

        $vatPayable = $outputVat - $inputVat;

        // Detailed VAT lines for TRA filing
        $vatLines = JournalLine::with(['entry', 'account'])
            ->whereHas('account', fn($q) => $q->whereIn('code', ['2200', '2300']))
            ->whereHas('entry', fn($q) => $q
                ->where('status', 'posted')
                ->whereDate('entry_date', '>=', $dateFrom)
                ->whereDate('entry_date', '<=', $dateTo)
            )
            ->get();

        return view('accounting.reports.vat', compact(
            'outputVat', 'inputVat', 'vatPayable', 'vatLines', 'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/ledger
    public function ledger(Request $request): View
    {
        $accounts  = Account::where('is_active', true)->orderBy('code')->get();
        $account   = null;
        $lines     = collect();

        if ($request->account_id) {
            $account  = Account::findOrFail($request->account_id);
            $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
            $dateTo   = $request->date_to   ?? now()->toDateString();

            $lines = JournalLine::with('entry')
                ->where('account_id', $account->id)
                ->whereHas('entry', fn($q) => $q
                    ->where('status', 'posted')
                    ->whereDate('entry_date', '>=', $dateFrom)
                    ->whereDate('entry_date', '<=', $dateTo)
                )
                ->orderBy('created_at')
                ->get();
        }

        return view('accounting.ledger.index', compact('accounts', 'account', 'lines'));
    }
}
