@extends('layouts.app')

@section('title', __('accountant.ap.apply_payment_title'))
@section('page-title', __('accountant.ap.apply_payment_title'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm lg:col-span-2">
            <div class="text-sm text-gray-500">{{ __('general.name') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary">{{ $supplierPayment->supplier?->name }}</div>
            <div class="mt-3 text-sm text-gray-500">{{ __('accountant.ap.payment_reference') }}: {{ $supplierPayment->reference ?: '-' }}</div>
            <div class="mt-1 text-sm text-gray-500">{{ __('general.date') }}: {{ $supplierPayment->payment_date?->format('M d, Y') }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('general.amount') }}</div><div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$supplierPayment->amount" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('general.status') }}</div><div class="mt-2 text-2xl font-extrabold {{ $supplierPayment->status === 'posted' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst(str_replace('_', ' ', $supplierPayment->status)) }}</div></div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.allocated_amount') }}</div><div class="mt-2 text-2xl font-extrabold text-indigo-600"><x-money :amount="$allocatedAmount" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.remaining_to_allocate') }}</div><div class="mt-2 text-2xl font-extrabold {{ $remainingAmount == 0.0 ? 'text-emerald-600' : 'text-rose-600' }}"><x-money :amount="$remainingAmount" /></div></div>
    </div>

    <form method="POST" action="{{ route('accountant.payments.allocate', $supplierPayment) }}" class="rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.ap.allocate_payment') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('accountant.ap.allocate_help') }}</p>
            </div>
            @if($supplierPayment->status !== 'posted')
                <button class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">{{ __('accountant.ap.save_allocations') }}</button>
            @endif
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[780px] text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.table.reference') }}</th><th class="pb-3">{{ __('general.date') }}</th><th class="pb-3 text-right">{{ __('general.total') }}</th><th class="pb-3 text-right">{{ __('accountant.labels.balance') }}</th><th class="pb-3 text-right">{{ __('accountant.ap.allocate_amount') }}</th></tr></thead>
                <tbody>
                    @forelse($payables as $payable)
                        @php($existing = optional($supplierPayment->allocations->firstWhere('supplier_payable_id', $payable->id))->allocated_amount)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-4 align-top"><a href="{{ route('accountant.payables.show', $payable) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">{{ $payable->reference }}</a></td>
                            <td class="py-4 align-top text-gray-600">{{ $payable->payable_date?->format('M d, Y') }}</td>
                            <td class="py-4 align-top text-right"><x-money :amount="$payable->amount_total" /></td>
                            <td class="py-4 align-top text-right font-bold text-amber-700"><x-money :amount="$payable->balance + (float) $existing" /></td>
                            <td class="py-4 text-right">
                                <div class="ml-auto w-full max-w-[220px]">
                                    <input type="number" step="0.01" min="0" name="allocations[{{ $payable->id }}]" value="{{ old('allocations.' . $payable->id, $existing) }}" class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-base font-semibold text-secondary focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" placeholder="0.00" {{ $supplierPayment->status === 'posted' ? 'disabled' : '' }}>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supplierPayment->status !== 'posted')
            <div class="mt-6 flex justify-end border-t border-gray-100 pt-5">
                <button class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">{{ __('accountant.ap.save_allocations') }}</button>
            </div>
        @endif
    </form>

    @if($supplierPayment->status !== 'posted')
        <div class="flex justify-end">
            <form method="POST" action="{{ route('accountant.payments.post', $supplierPayment) }}">
                @csrf
                <button class="rounded-xl px-5 py-3 text-sm font-semibold text-white {{ $remainingAmount == 0.0 ? 'bg-emerald-600' : 'bg-gray-400 cursor-not-allowed' }}" {{ $remainingAmount == 0.0 ? '' : 'disabled' }}>{{ __('accountant.ap.post_payment') }}</button>
            </form>
        </div>
        @if($remainingAmount != 0.0)
            <p class="text-right text-sm text-rose-600">{{ __('accountant.ap.finalize_hint') }}</p>
        @endif
    @endif
</div>
@endsection
