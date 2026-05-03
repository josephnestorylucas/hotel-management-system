@extends('finance.layout')
@section('title', __('finance.dashboard.title'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ __('finance.dashboard.title') }}</h1>
    <form method="GET" class="flex gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded px-3 py-1.5 text-sm">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="border rounded px-3 py-1.5 text-sm">
        <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">{{ __('finance.dashboard.apply') }}</button>
    </form>
</div>

{{-- Today's summary cards --}}
<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('finance.dashboard.today_revenue') }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">
            @currency($todaySummary['total_revenue'], 'USD')
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('finance.dashboard.checkout') }}</p>
        <p class="text-2xl font-bold text-green-600 mt-1">
            @currency($todaySummary['checkout_revenue'], 'USD')
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('finance.dashboard.walkin_sales') }}</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">
            @currency($todaySummary['walkin_revenue'], 'USD')
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('finance.dashboard.cash') }}</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            @currency($todaySummary['cash_total'], 'USD')
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('finance.dashboard.card') }}</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            @currency($todaySummary['card_total'], 'USD')
        </p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    {{-- Revenue by module --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('finance.dashboard.revenue_by_module') }}</h2>
        @forelse($revenueByModule as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $row->source_module) }}</span>
            <span class="font-medium">@currency($row->total_usd, 'USD')</span>
        </div>
        @empty
        <p class="text-sm text-gray-400">{{ __('finance.dashboard.no_data') }}</p>
        @endforelse
    </div>

    {{-- Revenue by payment method --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('finance.dashboard.by_payment_method') }}</h2>
        @forelse($revenueByMethod as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ ucfirst($row->payment_method) }}</span>
            <span class="font-medium">@currency($row->total_usd, 'USD')</span>
        </div>
        @empty
        <p class="text-sm text-gray-400">{{ __('finance.dashboard.no_data') }}</p>
        @endforelse
    </div>

    {{-- Outstanding --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('finance.dashboard.outstanding_balance') }}</h2>
        <p class="text-3xl font-bold text-red-500">@currency($outstandingTotal, 'USD')</p>
        <p class="text-sm text-gray-400 mt-2">{{ __('finance.dashboard.outstanding_help') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('finance.dashboard.completed_missing_charges') }}</h2>
        <div class="space-y-2 text-sm">
            @php($missingChargeCount = $ordersMissingCharges->count() + $laundryMissingCharges->count())

            @forelse($ordersMissingCharges as $order)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-gray-500">{{ $order->booking?->booking_number }} · {{ $order->location?->name }} · {{ ucfirst($order->status) }}</p>
                    </div>
                    <span class="text-red-600 font-medium">{{ __('finance.dashboard.missing_charge') }}</span>
                </div>
            @empty @endforelse

            @forelse($laundryMissingCharges as $order)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-gray-500">{{ $order->booking?->booking_number }} · {{ __('general.nav.laundry') }} · {{ ucfirst($order->status) }}</p>
                    </div>
                    <span class="text-red-600 font-medium">{{ __('finance.dashboard.missing_charge') }}</span>
                </div>
            @empty @endforelse

            @if($missingChargeCount === 0)
                <p class="text-sm text-gray-400">{{ __('finance.dashboard.no_missing_charges') }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('finance.dashboard.unpaid_charges_by_booking') }}</h2>
        <div class="space-y-2 text-sm">
            @forelse($unpaidChargesByBooking as $row)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $row->booking?->booking_number ?? __('finance.dashboard.unknown_booking') }}</p>
                        <p class="text-gray-500">{{ __('finance.dashboard.unpaid_charge_count', ['count' => $row->charge_count]) }}</p>
                    </div>
                    <span class="font-medium text-amber-700">@currency($row->total_tzs, 'TZS')</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">{{ __('finance.dashboard.no_unpaid_charges') }}</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Draft Checkouts --}}
@if($draftCheckouts->isNotEmpty())
<div class="bg-white rounded shadow overflow-hidden mb-6">
    <div class="px-5 py-4 border-b bg-yellow-50">
        <h2 class="font-semibold text-gray-700">Draft Checkouts — Pending Completion</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-500">Receipt</th>
                <th class="px-4 py-3 text-left text-gray-500">Booking</th>
                <th class="px-4 py-3 text-left text-gray-500">Guest</th>
                <th class="px-4 py-3 text-left text-gray-500">Room</th>
                <th class="px-4 py-3 text-left text-gray-500">Total (TZS)</th>
                <th class="px-4 py-3 text-left text-gray-500">Updated</th>
                <th class="px-4 py-3 text-left text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($draftCheckouts as $checkout)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $checkout->receipt_number }}</td>
                <td class="px-4 py-3 text-xs text-gray-600">{{ $checkout->booking?->booking_number }}</td>
                <td class="px-4 py-3 text-sm">{{ $checkout->booking?->guest_name }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $checkout->booking?->room?->room_number }}</td>
                <td class="px-4 py-3 text-sm font-medium">@currency($checkout->grand_total_tzs, 'TZS')</td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $checkout->updated_at->format('d M H:i') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('finance.checkout.show', $checkout->booking) }}"
                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                        Complete Checkout
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Recent transactions --}}
<div class="bg-white rounded shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">{{ __('finance.dashboard.recent_transactions') }}</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.txn') }}</th>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.type') }}</th>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.module') }}</th>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.method') }}</th>
                <th class="px-4 py-3 text-right text-gray-500">USD</th>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.by') }}</th>
                <th class="px-4 py-3 text-left text-gray-500">{{ __('finance.dashboard.time') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($recentTransactions as $txn)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $txn->transaction_number }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $txn->type === 'checkout_payment' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucwords(str_replace('_', ' ', $txn->type)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->source_module }}</td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->payment_method }}</td>
                <td class="px-4 py-3 text-right font-medium">@currency($txn->amount_usd, 'USD')</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $txn->actor?->name }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $txn->created_at->format('H:i d M') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ __('finance.dashboard.no_transactions') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
