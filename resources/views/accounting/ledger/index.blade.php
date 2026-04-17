@extends('layouts.app')

@section('title', 'General Ledger')
@section('page-title', 'General Ledger')

@section('content')
<div class="space-y-6">
    <form method="GET" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-4">
        <select name="account_id" class="rounded-xl border-gray-200 text-sm">
            <option value="">Select account</option>
            @foreach($accounts as $entryAccount)
                <option value="{{ $entryAccount->id }}" @selected(request('account_id') === $entryAccount->id)>{{ $entryAccount->code }} - {{ $entryAccount->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-gray-200 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-gray-200 text-sm">
        <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Filter ledger</button>
    </form>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-extrabold text-secondary">{{ $account?->name ?: 'Ledger entries' }}</h2>
            @if($account)
                <span class="text-sm text-gray-500">{{ $account->code }}</span>
            @endif
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Entry</th>
                        <th class="px-4 py-3 font-semibold">Type</th>
                        <th class="px-4 py-3 font-semibold text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lines as $line)
                        <tr>
                            <td class="px-4 py-3">{{ $line->entry?->entry_date?->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $line->entry?->entry_no }}</td>
                            <td class="px-4 py-3 capitalize">{{ $line->type }}</td>
                            <td class="px-4 py-3 text-right font-semibold"><x-money :amount="$line->amount" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No ledger lines found for the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
