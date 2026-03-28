<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BankReconciliation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankReconciliationController extends Controller
{
    public function index(): View
    {
        $reconciliations = BankReconciliation::with(['account', 'preparer'])
            ->orderBy('period_month', 'desc')
            ->paginate(20);
        return view('accounting.reconciliation.index', compact('reconciliations'));
    }

    public function create(): View
    {
        $bankAccounts = Account::where('type', 'asset')
            ->whereIn('code', ['1200', '1210']) // bank accounts
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        return view('accounting.reconciliation.create', compact('bankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id'               => 'required|uuid|exists:accounts,id',
            'period_month'             => 'required|string|regex:/^\d{4}-\d{2}$/',
            'statement_date'           => 'required|date',
            'statement_opening_balance'=> 'required|numeric',
            'statement_closing_balance'=> 'required|numeric',
            'notes'                    => 'nullable|string',
        ]);

        // Calculate system balances from journal entries up to statement_date
        $account = Account::findOrFail($validated['account_id']);
        $systemClosingBalance = $account->getBalance(null, $validated['statement_date']);
        // For simplicity, assume opening balance is previous month's closing.
        // In real system, you'd fetch from previous reconciliation.
        $systemOpeningBalance = $systemClosingBalance; // placeholder

        $difference = $validated['statement_closing_balance'] - $systemClosingBalance;

        $reconciliation = BankReconciliation::create([
            'account_id'                => $validated['account_id'],
            'period_month'              => $validated['period_month'],
            'statement_date'            => $validated['statement_date'],
            'statement_opening_balance' => $validated['statement_opening_balance'],
            'statement_closing_balance' => $validated['statement_closing_balance'],
            'system_opening_balance'    => $systemOpeningBalance,
            'system_closing_balance'    => $systemClosingBalance,
            'difference'                => $difference,
            'status'                    => abs($difference) < 0.01 ? 'reconciled' : 'open',
            'notes'                     => $validated['notes'] ?? null,
            'prepared_by'               => auth()->id(),
        ]);

        return redirect()
            ->route('accounting.reconciliation.show', $reconciliation)
            ->with('success', 'Bank reconciliation created.');
    }

    public function show(BankReconciliation $rec): View
    {
        $rec->load(['account', 'preparer']);
        return view('accounting.reconciliation.show', compact('rec'));
    }
}
