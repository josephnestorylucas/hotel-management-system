@extends('layouts.app')

@section('title', __('bartender.titles.order_show', ['order' => $order->order_number]))
@section('page-title', __('bartender.titles.order_show', ['order' => $order->order_number]))

@section('content')
@php($sourceLabel = __('bartender.sources.' . ($order->order_source ?? 'unknown')))
@php($statusLabel = __('bartender.statuses.' . ($order->bartender_status ?? 'pending')))
@php($order->loadMissing('receipt'))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('bartender.messages.order_details') }}</h2>
                    <p class="text-xs text-gray-500">{{ __('bartender.messages.source_requested', ['source' => $sourceLabel, 'time' => $order->created_at->format('Y-m-d H:i')]) }}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ $statusLabel }}</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-left">{{ __('bartender.fields.item') }}</th>
                        <th class="px-3 py-2 text-right">{{ __('bartender.fields.qty') }}</th>
                        <th class="px-3 py-2 text-right">{{ __('bartender.fields.price') }}</th>
                        <th class="px-3 py-2 text-right">{{ __('bartender.fields.subtotal') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-3 py-2">{{ $item->menuItem?->name ?? $item->item_name_snapshot ?? '—' }}</td>
                            <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">@currency($item->unit_price, 'TZS')</td>
                            <td class="px-3 py-2 text-right font-semibold">@currency($item->subtotal, 'TZS')</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-right mt-3 text-lg font-bold text-gray-800">@currency($order->total, 'TZS')</div>
        </div>

        @if(!$availability['ok'])
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="text-sm font-semibold text-red-700 mb-2">{{ __('bartender.messages.availability_failed') }}</h3>
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach($availability['errors'] as $error)
                        <li>{{ $error['message'] }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                {{ __('bartender.messages.availability_ok') }}
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('general.actions') }}</h3>

            @if(($order->bartender_status ?? 'pending') === 'pending')
                <form method="POST" action="{{ route('bartender.orders.accept', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.accept_order') }}</button>
                </form>
                <form method="POST" action="{{ route('bartender.orders.reject', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">{{ __('bartender.actions.reject_order') }}</button>
                </form>
            @endif

            @if($order->bartender_status === 'accepted')
                <form method="POST" action="{{ route('bartender.orders.prepare', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">{{ __('bartender.actions.mark_prepared') }}</button>
                </form>
            @endif

            @if($order->bartender_status === 'prepared')
                <form method="POST" action="{{ route('bartender.orders.serve', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">{{ __('bartender.actions.mark_served') }}</button>
                </form>
            @endif

            @if(!in_array($order->bartender_status, ['served', 'cancelled', 'rejected']))
                <form method="POST" action="{{ route('bartender.orders.cancel', $order) }}">@csrf
                    <button class="w-full py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('bartender.actions.cancel_order') }}</button>
                </form>
            @endif
        </div>

        @if($order->order_source === 'walkin' && $order->status === 'charged' && $order->booking_id)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <h3 class="font-semibold text-green-800 mb-2">{{ __('bartender.messages.charged_to_folio') }}</h3>
                <p class="text-sm text-green-700 mb-3">
                    This order has been added to the guest's folio. Payment will be collected at checkout.
                </p>
                <a href="{{ route('finance.checkout.show', $order->booking_id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    {{ __('bartender.actions.view_checkout') }}
                </a>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('bartender.actions.receipt') }}</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('receipts.order', $order) }}" target="_blank" class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm">
                        {{ __('bartender.actions.print_receipt') }}
                    </a>
                    @if($order->receipt)
                        <a href="{{ route('receipts.reprint', $order->receipt->receipt_number) }}" target="_blank" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            {{ __('bartender.actions.reprint_receipt') }} ({{ $order->receipt->receipt_number }})
                        </a>
                    @endif
                </div>
            </div>
        @elseif($order->order_source === 'walkin' && !in_array($order->status, ['settled', 'cancelled']) && $order->bartender_status === 'prepared')
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('bartender.messages.walkin_payment') }}</h3>
                <x-walkin-payment-modal
                    :amount="$order->total"
                    :order-id="$order->id"
                    :order-number="$order->order_number"
                    module="bar"
                    :customer-name="$order->customer_name ?? ''"
                    :customer-phone="$order->customer_phone ?? ''"
                />
            </div>
        @elseif($order->order_source === 'walkin' && !in_array($order->status, ['settled', 'cancelled']))
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                {{ __('bartender.messages.prepare_before_payment') }}
            </div>
        @elseif($order->order_source === 'walkin' && $order->status === 'settled')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                {{ __('bartender.messages.walkin_payment_completed') }}
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('bartender.actions.receipt') }}</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('receipts.order', $order) }}" target="_blank" class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm">
                        {{ __('bartender.actions.print_receipt') }}
                    </a>
                    @if($order->receipt)
                        <a href="{{ route('receipts.reprint', $order->receipt->receipt_number) }}" target="_blank" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            {{ __('bartender.actions.reprint_receipt') }} ({{ $order->receipt->receipt_number }})
                        </a>
                    @endif
                </div>
            </div>
        @elseif($order->order_source === 'restaurant')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
                {{ __('bartender.messages.restaurant_front_desk') }}
            </div>
        @elseif($order->order_source === 'room_service')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                {{ __('bartender.messages.room_service_checkout') }}
            </div>
        @endif
    </div>
</div>
@endsection
