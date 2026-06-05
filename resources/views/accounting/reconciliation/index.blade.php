@extends('layouts.app')

@section('title', 'Bank Reconciliation')
@section('page-title', 'Bank Reconciliation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Bank Reconciliation</h2>
            <p class="mt-1 text-sm text-gray-500">Track monthly bank statements against ledger balances.</p>
        </div>
        <a href="{{ route('accounting.reconciliation.create') }}" class="inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">New reconciliation</a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Reference</th>
                    <th class="px-6 py-4 font-semibold">Account</th>
                    <th class="px-6 py-4 font-semibold">Period</th>
                    <th class="px-6 py-4 font-semibold text-right">Difference</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reconciliations as $reconciliation)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('accounting.reconciliation.show', $reconciliation) }}" class="font-semibold text-indigo-600">{{ $reconciliation->reference_no }}</a></td>
                        <td class="px-6 py-4">{{ $reconciliation->account?->name }}</td>
                        <td class="px-6 py-4">{{ $reconciliation->period_month }}</td>
                        <td class="px-6 py-4 text-right"><x-money :amount="$reconciliation->difference" /></td>
                        <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($reconciliation->status) }}</span></td>
                    </tr>
                @empty
                    <x-empty-state table colspan="5" title="No reconciliations available" message="Create the first bank reconciliation to start matching statements." />
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $reconciliations->links() }}</div>
</div>
@endsection
