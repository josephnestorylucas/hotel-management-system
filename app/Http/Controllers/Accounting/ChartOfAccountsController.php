<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChartOfAccountsController extends Controller
{
    public function index(): View
    {
        $accounts = Account::with('parent')->orderBy('code')->get();
        return view('accounting.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $parentAccounts = Account::whereNull('parent_id')->orderBy('code')->get();
        return view('accounting.accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense,cogs',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|uuid|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ]);

        Account::create($validated);

        return redirect()->route('accounting.accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function edit(Account $account): View
    {
        $parentAccounts = Account::whereNull('parent_id')
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();
        return view('accounting.accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense,cogs',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|uuid|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('accounting.accounts.index')
            ->with('success', 'Account updated successfully.');
    }
}
