@extends('layouts.app')

@section('title', $journalEntry->entry_no)
@section('page-title', __('accountant.journal.titles.details'))

@section('content')
@php($showRoute = auth()->user()->hasRole('manager') ? 'manager.accounting.journal.show' : 'accounting.journal.show')
@php($postRoute = auth()->user()->hasRole('manager') ? 'manager.accounting.journal.post' : 'accounting.journal.post')
@php($reverseRoute = auth()->user()->hasRole('manager') ? 'manager.accounting.journal.reverse' : 'accounting.journal.reverse')
@php($backRoute = auth()->user()->hasRole('manager') ? 'manager.accounting.reports.supplier-payables' : 'accounting.journal.index')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $journalEntry->entry_no }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('accountant.journal.labels.detail_subtitle') }}</p>
        </div>
        <a href="{{ route($backRoute) }}" class="text-primary hover:text-blue-700 font-semibold">← {{ __('accountant.journal.actions.back_to_journal') }}</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.source') }}</div>
            <div class="mt-2 text-xl font-extrabold text-secondary">{{ __('accountant.journal.sources.' . $journalEntry->source) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.supplier') }}</div>
            <div class="mt-2 text-xl font-extrabold text-secondary">{{ $journalEntry->supplier?->name ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.debit') }}</div>
            <div class="mt-2 text-xl font-extrabold text-green-600"><x-money :amount="$journalEntry->total_debit" /></div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.credit') }}</div>
            <div class="mt-2 text-xl font-extrabold text-primary"><x-money :amount="$journalEntry->total_credit" /></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">{{ __('accountant.journal.labels.entry_information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">{{ __('accountant.journal.labels.reference') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->reference ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('accountant.journal.labels.entry_date') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->entry_date->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('accountant.journal.labels.created_by') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->creator?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('accountant.journal.labels.posted_by') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->poster?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('accountant.journal.labels.posted_at') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->posted_at?->format('M d, Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-gray-500">{{ __('accountant.journal.labels.description') }}</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->description }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-secondary">{{ __('accountant.journal.labels.lines') }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gradient-to-r from-blue-50 to-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.account') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.type') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.notes') }}</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($journalEntry->lines as $line)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-secondary">{{ $line->account->code }} - {{ $line->account->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($line->account->type) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $line->type === 'debit' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ __('accountant.journal.labels.' . $line->type) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $line->notes ?? '—' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-secondary"><x-money :amount="$line->amount" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">{{ __('accountant.journal.labels.context') }}</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('accountant.journal.labels.supplier') }}</span>
                        <span class="font-semibold text-secondary">{{ $journalEntry->supplier?->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('accountant.journal.labels.source_record') }}</span>
                        <span class="font-semibold text-secondary">{{ $journalEntry->source_id ?? '—' }}</span>
                    </div>
                    @if($journalEntry->source === 'procurement' && $journalEntry->reference && str_starts_with($journalEntry->reference, 'GRN-'))
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">GRN</span>
                        <a href="{{ route('procurement.grn.show', $journalEntry->source_id) }}" class="font-semibold text-primary hover:text-blue-700">{{ $journalEntry->reference }}</a>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('accountant.journal.labels.status') }}</span>
                        <span class="font-semibold {{ $journalEntry->status === 'posted' ? 'text-green-600' : 'text-amber-600' }}">{{ __('accountant.journal.statuses.' . $journalEntry->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">{{ __('accountant.journal.labels.balance_check') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('accountant.journal.labels.total_debit') }}</span>
                        <span class="font-semibold text-secondary"><x-money :amount="$journalEntry->total_debit" /></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('accountant.journal.labels.total_credit') }}</span>
                        <span class="font-semibold text-secondary"><x-money :amount="$journalEntry->total_credit" /></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        <span>{{ __('accountant.journal.labels.balanced') }}</span>
                        <span>{{ $journalEntry->isBalanced() ? __('general.yes') : __('general.no') }}</span>
                    </div>
                </div>
            </div>

            @if($journalEntry->source === 'manual')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-secondary">{{ __('accountant.journal.labels.actions') }}</h3>

                @if($journalEntry->status === 'draft' && auth()->user()->hasRole('ACCOUNTANT'))
                    <a href="{{ route('accounting.journal.edit', $journalEntry) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                        {{ __('accountant.journal.actions.edit_draft') }}
                    </a>
                @endif

                @if($journalEntry->status === 'draft' && auth()->user()->hasAnyRole(['ACCOUNTANT', 'manager']))
                    <form method="POST" action="{{ route($postRoute, $journalEntry) }}">
                        @csrf
                        <button class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg">
                            {{ __('accountant.journal.actions.post_entry') }}
                        </button>
                    </form>
                @endif

                @if($journalEntry->status === 'posted' && auth()->user()->hasAnyRole(['ACCOUNTANT', 'manager']))
                    <form method="POST" action="{{ route($reverseRoute, $journalEntry) }}" class="space-y-2">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700">{{ __('accountant.journal.labels.reversal_reason') }}</label>
                        <textarea name="reason" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"></textarea>
                        <button class="w-full px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-lg hover:bg-rose-700">
                            {{ __('accountant.journal.actions.reverse_entry') }}
                        </button>
                    </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
