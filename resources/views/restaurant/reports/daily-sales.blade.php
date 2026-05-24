{{-- resources/views/restaurant/reports/daily-sales.blade.php --}}
@extends('layouts.app')

@section('title', 'Daily Sales Report')
@section('page-title', 'Daily Sales Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-6">Daily Sales Report</h1>

    {{-- Date picker --}}
    <form method="GET" action="{{ route('restaurant.reports.dailySales') }}" class="flex gap-3 items-end mb-6">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}"
                   class="border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">View</button>
    </form>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Orders</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_orders'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Items Sold</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_items_qty'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Subtotal</p>
            <p class="text-lg font-bold text-gray-700">@currency($summary['total_subtotal'], 'TZS')</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Tax (VAT)</p>
            <p class="text-lg font-bold text-gray-700">@currency($summary['total_tax'], 'TZS')</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
            <p class="text-xl font-bold text-primary">@currency($summary['total_revenue'], 'TZS')</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Cash</p>
            <p class="text-lg font-bold text-green-600">{{ number_format($summary['cash_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Card / Mobile</p>
            <p class="text-lg font-bold text-blue-600">{{ number_format($summary['card_revenue'] + $summary['mobile_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Guest Folio</p>
            <p class="text-lg font-bold text-purple-600">{{ number_format($summary['guest_charges'], 0) }}</p>
        </div>
    </div>

    {{-- Revenue by Payment Method Detail --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Cash</span>
                <span class="text-sm font-bold text-green-700">@currency($summary['cash_revenue'], 'TZS')</span>
            </div>
            <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $summary['total_revenue'] > 0 ? round(($summary['cash_revenue'] / $summary['total_revenue']) * 100) : 0 }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Card</span>
                <span class="text-sm font-bold text-blue-700">@currency($summary['card_revenue'], 'TZS')</span>
            </div>
            <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $summary['total_revenue'] > 0 ? round(($summary['card_revenue'] / $summary['total_revenue']) * 100) : 0 }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Mobile Money</span>
                <span class="text-sm font-bold text-cyan-700">@currency($summary['mobile_revenue'], 'TZS')</span>
            </div>
            <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-cyan-500 h-1.5 rounded-full" style="width: {{ $summary['total_revenue'] > 0 ? round(($summary['mobile_revenue'] / $summary['total_revenue']) * 100) : 0 }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Guest Folio</span>
                <span class="text-sm font-bold text-purple-700">@currency($summary['guest_charges'], 'TZS')</span>
            </div>
            <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $summary['total_revenue'] > 0 ? round(($summary['guest_charges'] / $summary['total_revenue']) * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Sales by Category & Buffet Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Sales by Category --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Sales by Category</h3>
            </div>
            <div class="p-4">
                @if($salesByCategory->isNotEmpty())
                <div class="space-y-3">
                    @foreach($salesByCategory as $category => $data)
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-gray-800">{{ $category }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ $data['quantity'] }} items)</span>
                        </div>
                        <span class="text-sm font-bold text-gray-700">@currency($data['revenue'], 'TZS')</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $summary['total_revenue'] > 0 ? round(($data['revenue'] / $summary['total_revenue']) * 100) : 0 }}%"></div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 text-center py-4">No sales data for this date.</p>
                @endif
            </div>
        </div>

        {{-- Buffet Summary --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Buffet Sales</h3>
            </div>
            <div class="p-4">
                @if($buffetSummary['count'] > 0)
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-amber-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Sales</p>
                        <p class="text-2xl font-bold text-amber-700">{{ $buffetSummary['count'] }}</p>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Revenue</p>
                        <p class="text-2xl font-bold text-amber-700">@currency($buffetSummary['revenue'], 'TZS')</p>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Adults</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $buffetSummary['adults'] }}</p>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Children</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $buffetSummary['children'] }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-400 text-center py-4">No buffet sales for this date.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Orders table --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">Order #</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Section</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Table</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Type</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Server</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Customer</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Items</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Subtotal</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Tax</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Total</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Payment</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">
                        <a href="{{ route('restaurant.orders.show', $order) }}" class="text-primary hover:underline">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $order->location->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $order->table->table_number ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $order->order_type === 'guest' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($order->order_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs">{{ $order->creator->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs max-w-[120px] truncate" title="{{ $order->customer_name }}">
                        {{ $order->customer_name ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <button type="button" onclick="document.getElementById('items-{{ $order->id }}').classList.toggle('hidden')"
                            class="text-primary text-xs hover:underline">
                            {{ $order->items->count() }} items ▼
                        </button>
                    </td>
                    <td class="px-4 py-3 text-right text-xs">@currency($order->subtotal, 'TZS')</td>
                    <td class="px-4 py-3 text-right text-xs">@currency($order->tax, 'TZS')</td>
                    <td class="px-4 py-3 text-right font-semibold">@currency($order->total, 'TZS')</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $order->payment_method === 'cash' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ in_array($order->payment_method, ['mobile', 'mobile_money']) ? 'bg-cyan-100 text-cyan-700' : '' }}
                            {{ $order->payment_method === 'charge_to_booking' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ !in_array($order->payment_method, ['cash','card','mobile','mobile_money','charge_to_booking']) && $order->payment_method ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $order->payment_method ? str_replace('_', ' ', ucfirst($order->payment_method)) : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $order->status === 'settled' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $order->settled_at ? \Carbon\Carbon::parse($order->settled_at)->format('H:i') : '—' }}
                    </td>
                </tr>
                {{-- Expandable items detail row --}}
                <tr id="items-{{ $order->id }}" class="hidden bg-gray-50">
                    <td colspan="13" class="px-4 py-3">
                        <div class="text-xs space-y-1 max-w-2xl">
                            <div class="grid grid-cols-5 gap-2 font-medium text-gray-500 border-b pb-1 mb-1">
                                <span class="col-span-2">Item</span>
                                <span class="text-center">Qty</span>
                                <span class="text-right">Unit Price</span>
                                <span class="text-right">Subtotal</span>
                            </div>
                            @foreach($order->items as $item)
                            <div class="grid grid-cols-5 gap-2 {{ $item->status === 'cancelled' ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                <span class="col-span-2">{{ $item->menuItem->name ?? $item->item_name_snapshot ?? 'Unknown' }}</span>
                                <span class="text-center">{{ $item->quantity }}</span>
                                <span class="text-right">@currency($item->unit_price, 'TZS')</span>
                                <span class="text-right">@currency($item->subtotal, 'TZS')</span>
                            </div>
                            @endforeach
                            @if($order->notes)
                            <div class="text-gray-400 italic mt-1">Notes: {{ $order->notes }}</div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="px-4 py-8 text-center text-gray-400">No settled or charged orders for this date.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
