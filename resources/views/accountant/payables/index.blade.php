@extends('layouts.app')

@section('title', __('accountant.ap.payables_list_title'))
@section('page-title', __('accountant.ap.payables_list_title'))

@section('content')
<div class="space-y-6">
    <form method="GET" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-5">
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('general.name') }}</label>
            <select name="supplier_id" class="w-full rounded-xl border-gray-200 text-sm">
                <option value="">{{ __('accountant.ap.all_suppliers') }}</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') === $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('general.status') }}</label>
            <select name="status" class="w-full rounded-xl border-gray-200 text-sm">
                <option value="">{{ __('accountant.ap.all_statuses') }}</option>
                @foreach(['unpaid', 'partial', 'paid', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('accountant.ap.date_from') }}</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-gray-200 text-sm">
        </div>
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('accountant.ap.date_to') }}</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-gray-200 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('general.filter') }}</button>
            <a href="{{ route('accountant.payables.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">{{ __('general.reset') }}</a>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('general.total') }}</div><div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$totals['amount_total']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.labels.paid') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$totals['amount_paid']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.labels.balance') }}</div><div class="mt-2 text-2xl font-extrabold text-amber-600"><x-money :amount="$totals['balance']" /></div></div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('general.name') }}</th>
                        <th class="pb-3">{{ __('accountant.table.reference') }}</th>
                        <th class="pb-3">{{ __('general.date') }}</th>
                        <th class="pb-3 text-right">{{ __('general.total') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.labels.paid') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.labels.balance') }}</th>
                        <th class="pb-3">{{ __('general.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payables as $payable)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3 font-semibold text-secondary">{{ $payable->supplier?->name }}</td>
                            <td class="py-3"><a href="{{ route('accountant.payables.show', $payable) }}" class="text-indigo-600 hover:text-indigo-700">{{ $payable->reference }}</a></td>
                            <td class="py-3 text-gray-600">{{ $payable->payable_date?->format('M d, Y') }}</td>
                            <td class="py-3 text-right"><x-money :amount="$payable->amount_total" /></td>
                            <td class="py-3 text-right"><x-money :amount="$payable->amount_paid" /></td>
                            <td class="py-3 text-right font-bold"><x-money :amount="$payable->balance" /></td>
                            <td class="py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $payable->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payable->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : ($payable->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-700')) }}">{{ ucfirst($payable->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $payables->links() }}</div>
    </div>
</div>
@endsection
