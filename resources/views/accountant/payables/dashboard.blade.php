@extends('layouts.app')

@section('title', __('accountant.ap.dashboard_title'))
@section('page-title', __('accountant.ap.dashboard_title'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-slate-900 via-teal-800 to-emerald-700 p-6 text-white shadow-xl lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold">{{ __('accountant.ap.hero_title') }}</h2>
            <p class="mt-2 text-sm text-emerald-100">{{ __('accountant.ap.hero_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('accountant.payables.index') }}" class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 transition hover:bg-white/20">{{ __('accountant.ap.view_payables') }}</a>
            @if($canManageAp)
                <a href="{{ route('accountant.payments.create') }}" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-emerald-50">{{ __('accountant.ap.new_payment') }}</a>
            @endif
        </div>
    </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-2xl bg-white p-5 shadow-sm xl:col-span-2">
                <div class="text-sm text-gray-500">{{ __('accountant.ap.total_outstanding') }}</div>
                <div class="mt-2 text-3xl font-extrabold text-amber-600"><x-money :amount="$totalOutstanding" /></div>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.aging_0_30') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$aging['0_30']" /></div></div>
            <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.aging_31_60') }}</div><div class="mt-2 text-2xl font-extrabold text-yellow-600"><x-money :amount="$aging['31_60']" /></div></div>
            <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.aging_61_90') }}</div><div class="mt-2 text-2xl font-extrabold text-orange-600"><x-money :amount="$aging['61_90']" /></div></div>
            <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.ap.aging_90_plus') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$aging['90_plus']" /></div></div>
        </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.ap.open_payables') }}</h3>
                <a href="{{ route('accountant.payables.index') }}" class="text-sm font-semibold text-indigo-600">{{ __('general.view_all') }}</a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-gray-500">
                            <th class="pb-3">{{ __('general.name') }}</th>
                            <th class="pb-3">{{ __('accountant.table.reference') }}</th>
                            <th class="pb-3">{{ __('general.date') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.labels.balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($openPayables->take(8) as $payable)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-3 font-semibold text-secondary">{{ $payable->supplier?->name }}</td>
                                <td class="py-3"><a href="{{ route('accountant.payables.show', $payable) }}" class="text-indigo-600 hover:text-indigo-700">{{ $payable->reference }}</a></td>
                                <td class="py-3 text-gray-600">{{ $payable->payable_date?->format('M d, Y') }}</td>
                                <td class="py-3 text-right font-bold text-amber-700"><x-money :amount="$payable->balance" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.ap.recent_payments') }}</h3>
                @if($canManageAp)
                    <a href="{{ route('accountant.payments.create') }}" class="text-sm font-semibold text-indigo-600">{{ __('accountant.ap.new_payment') }}</a>
                @endif
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentPayments as $payment)
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-secondary">{{ $payment->supplier?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->payment_date?->format('M d, Y') }} · {{ ucfirst($payment->method) }}</div>
                            </div>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $payment->status === 'posted' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst(str_replace('_', ' ', $payment->status)) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm font-bold text-secondary"><x-money :amount="$payment->amount" /></div>
                            @if($canManageAp && ! in_array($payment->status, ['posted', 'cancelled'], true))
                                <a href="{{ route('accountant.payments.apply', $payment) }}" class="text-sm font-semibold text-indigo-600">{{ __('accountant.ap.allocate_now') }}</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-8 text-center text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
