@extends('layouts.app')

@section('title', __('bartender.titles.walkin_sales'))
@section('page-title', __('bartender.titles.walkin_sales'))

@php
    $statusOptions = ['open', 'pending', 'accepted', 'prepared', 'served', 'charged', 'settled', 'cancelled', 'rejected'];
    $paymentOptions = ['cash', 'card', 'mobile_money', 'bank_transfer', 'charge_to_booking'];
@endphp

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('bartender.fields.date_from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('bartender.fields.date_to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('bartender.fields.status') }}</label>
                <select name="status" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="">{{ __('bartender.statuses.all') }}</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('bartender.statuses.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('bartender.fields.payment_method') }}</label>
                <select name="payment_method" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="">{{ __('bartender.payment_methods.all') }}</option>
                    @foreach($paymentOptions as $method)
                        <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ __('bartender.payment_methods.' . $method) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">{{ __('bartender.actions.apply') }}</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="text-xs text-gray-500">{{ __('bartender.messages.summary_count') }}</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($summary['count']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="text-xs text-gray-500">{{ __('bartender.messages.summary_total') }}</div>
            <div class="text-2xl font-bold text-gray-800">@currency($summary['total'], 'TZS')</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="text-xs text-gray-500">{{ __('bartender.messages.summary_paid') }}</div>
            <div class="text-2xl font-bold text-green-700">@currency($summary['paid'], 'TZS')</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.reference') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.customer') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.status') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.payment_method') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.total') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.settled_at') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->customer_name ?? __('bartender.placeholders.walkin_guest') }}</td>
                        @php($statusKey = in_array($order->status, $statusOptions, true) ? $order->status : 'open')
                        <td class="px-4 py-3">{{ __('bartender.statuses.' . $statusKey) }}</td>
                        @php($paymentKey = in_array($order->payment_method, $paymentOptions, true) ? $order->payment_method : 'unknown')
                        <td class="px-4 py-3">{{ __('bartender.payment_methods.' . $paymentKey) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">@currency($order->total, 'TZS')</td>
                        <td class="px-4 py-3">{{ $order->settled_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bartender.orders.show', $order) }}" class="text-blue-600 hover:underline">{{ __('bartender.actions.open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ __('bartender.messages.no_walkin_sales') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
