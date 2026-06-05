@extends('layouts.app')

@section('title', __('accountant.sidebar.transactions'))
@section('page-title', __('accountant.sidebar.transactions'))

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between"><h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sidebar.transactions') }}</h2><a href="{{ route('accountant.journal.index') }}" class="text-sm font-semibold text-indigo-600">{{ __('accountant.actions.open_journal') }}</a></div>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.table.reference') }}</th><th class="pb-3">{{ __('general.date') }}</th><th class="pb-3">{{ __('general.description') }}</th><th class="pb-3">{{ __('general.status') }}</th><th class="pb-3 text-right">{{ __('general.amount') }}</th></tr></thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold text-secondary">{{ $transaction->entry_no }}</td><td class="py-3 text-gray-600">{{ $transaction->entry_date?->format('M d, Y') }}</td><td class="py-3 text-gray-600">{{ $transaction->description }}</td><td class="py-3 text-gray-600">{{ ucfirst($transaction->status) }}</td><td class="py-3 text-right font-bold text-secondary"><x-money :amount="$transaction->total_debit" /></td></tr>
                @empty
                    <x-empty-state table colspan="5" title="No transactions found" message="Transactions will appear here once accounting entries are posted." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
