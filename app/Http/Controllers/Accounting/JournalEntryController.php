<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(): View
    {
        $entries = JournalEntry::with(['creator', 'poster'])
            ->orderBy('entry_date', 'desc')
            ->paginate(20);
        return view('accounting.journal.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        return view('accounting.journal.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'reference' => 'nullable|string|max:200',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|uuid|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $totalDebit = collect($validated['lines'])->where('type', 'debit')->sum('amount');
            $totalCredit = collect($validated['lines'])->where('type', 'credit')->sum('amount');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'lines' => "Debits ($totalDebit) must equal credits ($totalCredit)."
                ]);
            }

            $entry = JournalEntry::create([
                'entry_date'   => $validated['entry_date'],
                'description'  => $validated['description'],
                'reference'    => $validated['reference'] ?? null,
                'source'       => 'manual',
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'status'       => 'posted',
                'created_by'   => auth()->id(),
                'posted_by'    => auth()->id(),
                'posted_at'    => now(),
            ]);

            foreach ($validated['lines'] as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'type'       => $line['type'],
                    'amount'     => $line['amount'],
                    'notes'      => $line['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('accounting.journal.index')
            ->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['lines.account', 'creator', 'poster']);
        return view('accounting.journal.show', compact('journalEntry'));
    }
}
