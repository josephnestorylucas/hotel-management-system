@extends('layouts.app')

@section('title', $supplierPayable->reference)
@section('page-title', __('accountant.ap.payable_detail_title'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm lg:col-span-2">
            <div class="text-sm text-gray-500">{{ __('general.name') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary">{{ $supplierPayable->supplier?->name }}</div>
            <div class="mt-3 text-sm text-gray-500">{{ __('accountant.ap.source_reference') }}: {{ $supplierPayable->reference }}</div>
            <div class="mt-1 text-sm text-gray-500">{{ __('general.date') }}: {{ $supplierPayable->payable_date?->format('M d, Y') }}</div>
            <div class="mt-1 text-sm text-gray-500">{{ __('general.status') }}: <span class="font-semibold {{ $supplierPayable->status === 'paid' ? 'text-emerald-600' : ($supplierPayable->status === 'partial' ? 'text-amber-600' : ($supplierPayable->status === 'cancelled' ? 'text-rose-600' : 'text-slate-600')) }}">{{ ucfirst($supplierPayable->status) }}</span></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('general.total') }}</div><div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$supplierPayable->amount_total" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.labels.balance') }}</div><div class="mt-2 text-2xl font-extrabold text-amber-600"><x-money :amount="$supplierPayable->balance" /></div></div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.ap.source_module') }}</div>
            <div class="mt-2 text-sm font-semibold uppercase tracking-wide text-secondary">{{ $supplierPayable->source_module }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.ap.source_type') }}</div>
            <div class="mt-2 text-sm font-semibold uppercase tracking-wide text-secondary">{{ $supplierPayable->source_reference_type ?: '-' }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('general.created_by') }}</div>
            <div class="mt-2 text-sm font-semibold text-secondary">{{ $supplierPayable->creator?->name ?: '-' }}</div>
        </div>
    </div>

    @if($supplierPayable->notes)
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-700">
            <div class="font-semibold text-secondary">{{ __('general.notes') }}</div>
            <div class="mt-2 whitespace-pre-line">{{ $supplierPayable->notes }}</div>
        </div>
    @endif

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.ap.allocation_history') }}</h3>
            @if($canManageAp && $supplierPayable->status !== 'paid')
                <a href="{{ route('accountant.payments.create') }}" class="text-sm font-semibold text-indigo-600">{{ __('accountant.ap.new_payment') }}</a>
            @endif
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.ap.payment_reference') }}</th><th class="pb-3">{{ __('general.date') }}</th><th class="pb-3">{{ __('general.status') }}</th><th class="pb-3">{{ __('general.created_by') }}</th><th class="pb-3 text-right">{{ __('general.amount') }}</th></tr></thead>
                <tbody>
                    @forelse($supplierPayable->allocations as $allocation)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3">
                                @if($canManageAp && $allocation->payment)
                                    <a href="{{ route('accountant.payments.apply', $allocation->payment) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">{{ $allocation->payment->reference ?: $allocation->payment->id }}</a>
                                @else
                                    <span class="font-semibold text-secondary">{{ $allocation->payment?->reference ?: $allocation->payment?->id ?: '-' }}</span>
                                @endif
                            </td>
                            <td class="py-3 text-gray-600">{{ $allocation->payment?->payment_date?->format('M d, Y') }}</td>
                            <td class="py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $allocation->payment?->status ?? 'draft')) }}</td>
                            <td class="py-3 text-gray-600">{{ $allocation->creator?->name ?: '-' }}</td>
                            <td class="py-3 text-right font-bold text-secondary"><x-money :amount="$allocation->allocated_amount" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
