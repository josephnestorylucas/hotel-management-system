<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\StoreNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalEntryController extends Controller
{
    public function index(Request $request): View
    {
        $entriesQuery = JournalEntry::query()
            ->with(['creator', 'poster', 'supplier', 'lines.account']);

        $this->applyFilters($entriesQuery, $request);

        $entries = (clone $entriesQuery)
            ->orderBy('entry_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = (clone $entriesQuery)
            ->selectRaw('COUNT(*) as total_entries')
            ->selectRaw("SUM(CASE WHEN status = 'posted' THEN 1 ELSE 0 END) as posted_entries")
            ->selectRaw("SUM(CASE WHEN source = 'manual' THEN 1 ELSE 0 END) as manual_entries")
            ->selectRaw('COALESCE(SUM(total_debit), 0) as total_value')
            ->first();

        $filters = $request->only(['status', 'source', 'date_from', 'date_to']);

        return view('accounting.journal.index', compact('entries', 'stats', 'filters'));
    }

    public function create(): View
    {
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        return view('accounting.journal.create', [
            'accounts' => $accounts,
            'journalEntry' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureCanCreateDraft();

        $validated = $this->validateEntryPayload($request);

        DB::transaction(function () use ($validated) {
            $entry = JournalEntry::create([
                'entry_date'   => $validated['entry_date'],
                'description'  => $validated['description'],
                'reference'    => $validated['reference'] ?? null,
                'source'       => 'manual',
                'total_debit'  => $validated['totals']['debit'],
                'total_credit' => $validated['totals']['credit'],
                'status'       => 'draft',
                'created_by'   => auth()->id(),
            ]);

            $this->syncLines($entry, $validated['lines']);
        });

        return redirect()->route('accounting.journal.index')
            ->with('success', __('accountant.journal.messages.draft_saved'));
    }

    public function edit(JournalEntry $journalEntry): View
    {
        $this->ensureCanEdit($journalEntry);

        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        $journalEntry->load('lines');

        return view('accounting.journal.create', compact('accounts', 'journalEntry'));
    }

    public function update(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        $this->ensureCanEdit($journalEntry);

        $validated = $this->validateEntryPayload($request);

        DB::transaction(function () use ($journalEntry, $validated) {
            $journalEntry->update([
                'entry_date'   => $validated['entry_date'],
                'description'  => $validated['description'],
                'reference'    => $validated['reference'] ?? null,
                'total_debit'  => $validated['totals']['debit'],
                'total_credit' => $validated['totals']['credit'],
            ]);

            $this->syncLines($journalEntry, $validated['lines']);
        });

        return redirect()->route('accounting.journal.show', $journalEntry)
            ->with('success', __('accountant.journal.messages.draft_updated'));
    }

    public function post(JournalEntry $journalEntry): RedirectResponse
    {
        $this->ensureCanPost();

        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['entry' => __('accountant.journal.messages.only_draft_postable')]);
        }

        $journalEntry->load('lines');

        $totalDebit = (float) $journalEntry->lines->where('type', 'debit')->sum('amount');
        $totalCredit = (float) $journalEntry->lines->where('type', 'credit')->sum('amount');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['entry' => __('accountant.journal.messages.unbalanced_posting')]);
        }

        DB::transaction(function () use ($journalEntry, $totalDebit, $totalCredit) {
            $journalEntry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $this->auditAction(
                type: 'journal_entry_posted',
                title: __('accountant.journal.audit.posted_title', ['entry' => $journalEntry->entry_no]),
                body: __('accountant.journal.audit.posted_body', ['entry' => $journalEntry->entry_no])
            );
        });

        return redirect()->route($this->showRouteForCurrentUser(), $journalEntry)
            ->with('success', __('accountant.journal.messages.posted'));
    }

    public function reverse(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        $this->ensureCanPost();

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($journalEntry->status !== 'posted') {
            return back()->withErrors(['entry' => __('accountant.journal.messages.only_posted_reversible')]);
        }

        $journalEntry->load('lines');

        DB::transaction(function () use ($journalEntry, $data) {
            $reverseEntry = JournalEntry::create([
                'entry_date'   => now()->toDateString(),
                'description'  => __('accountant.journal.messages.reversal_description', [
                    'entry' => $journalEntry->entry_no,
                    'reason' => $data['reason'],
                ]),
                'reference'    => 'REV-' . $journalEntry->entry_no,
                'source'       => $journalEntry->source,
                'source_id'    => $journalEntry->source_id,
                'supplier_id'  => $journalEntry->supplier_id,
                'total_debit'  => $journalEntry->total_credit,
                'total_credit' => $journalEntry->total_debit,
                'status'       => 'posted',
                'created_by'   => auth()->id(),
                'posted_by'    => auth()->id(),
                'posted_at'    => now(),
            ]);

            foreach ($journalEntry->lines as $line) {
                $reverseEntry->lines()->create([
                    'account_id' => $line->account_id,
                    'type'       => $line->type === 'debit' ? 'credit' : 'debit',
                    'amount'     => $line->amount,
                    'notes'      => __('accountant.journal.messages.reversal_line_note', ['entry' => $journalEntry->entry_no]),
                ]);
            }

            $journalEntry->update(['status' => 'reversed']);

            $this->auditAction(
                type: 'journal_entry_reversed',
                title: __('accountant.journal.audit.reversed_title', ['entry' => $journalEntry->entry_no]),
                body: __('accountant.journal.audit.reversed_body', [
                    'entry' => $journalEntry->entry_no,
                    'reversal' => $reverseEntry->entry_no,
                    'reason' => $data['reason'],
                ])
            );
        });

        return redirect()->route($this->showRouteForCurrentUser(), $journalEntry)
            ->with('success', __('accountant.journal.messages.reversed'));
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['lines.account', 'creator', 'poster', 'supplier']);
        return view('accounting.journal.show', compact('journalEntry'));
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('status'), fn (Builder $builder) => $builder->where('status', (string) $request->input('status')))
            ->when($request->filled('source'), fn (Builder $builder) => $builder->where('source', (string) $request->input('source')))
            ->when($request->filled('date_from'), fn (Builder $builder) => $builder->whereDate('entry_date', '>=', (string) $request->input('date_from')))
            ->when($request->filled('date_to'), fn (Builder $builder) => $builder->whereDate('entry_date', '<=', (string) $request->input('date_to')));
    }

    private function validateEntryPayload(Request $request): array
    {
        $normalizedLines = collect($request->input('lines', []))
            ->filter(fn ($line) => is_array($line) && !empty($line['account_id']) && !empty($line['amount']))
            ->values()
            ->all();

        $payload = $request->all();
        $payload['lines'] = $normalizedLines;

        $validated = validator($payload, [
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'reference' => 'nullable|string|max:200',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|uuid|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.notes' => 'nullable|string',
        ])->validate();

        $totalDebit = (float) collect($validated['lines'])->where('type', 'debit')->sum('amount');
        $totalCredit = (float) collect($validated['lines'])->where('type', 'credit')->sum('amount');

        if ($totalDebit <= 0 || $totalCredit <= 0) {
            throw ValidationException::withMessages([
                'lines' => __('accountant.journal.messages.debit_credit_required'),
            ]);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw ValidationException::withMessages([
                'lines' => __('accountant.journal.messages.unbalanced_posting'),
            ]);
        }

        $validated['totals'] = [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
        ];

        return $validated;
    }

    private function syncLines(JournalEntry $journalEntry, array $lines): void
    {
        $journalEntry->lines->each(fn($line) => $this->softDelete($line));

        foreach ($lines as $line) {
            $journalEntry->lines()->create([
                'account_id' => $line['account_id'],
                'type' => $line['type'],
                'amount' => $line['amount'],
                'notes' => $line['notes'] ?? null,
            ]);
        }
    }

    private function ensureCanCreateDraft(): void
    {
        if (!auth()->user()?->hasRole('ACCOUNTANT')) {
            abort(403);
        }
    }

    private function ensureCanEdit(JournalEntry $journalEntry): void
    {
        $this->ensureCanCreateDraft();

        if ($journalEntry->source !== 'manual' || $journalEntry->status !== 'draft') {
            abort(403, __('accountant.journal.messages.posting_lock'));
        }
    }

    private function ensureCanPost(): void
    {
        if (!auth()->user()?->hasAnyRole(['ACCOUNTANT', 'manager'])) {
            abort(403);
        }
    }

    private function auditAction(string $type, string $title, string $body): void
    {
        StoreNotification::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'reference_type' => 'journal_entry',
            'reference_id' => null,
            'action_url' => null,
            'created_at' => now(),
        ]);
    }

    private function showRouteForCurrentUser(): string
    {
        return auth()->user()?->hasRole('manager')
            ? 'manager.accounting.journal.show'
            : 'accounting.journal.show';
    }
}
