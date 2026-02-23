@extends('laundry.layout')

@section('title', 'Daily Laundry Report')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daily Laundry Report</h1>
    <form method="GET" class="flex gap-2">
        <input type="date" name="date" value="{{ $date }}"
               class="border rounded px-3 py-2 text-sm">
        <button class="bg-gray-200 px-4 py-2 rounded text-sm hover:bg-gray-300">Go</button>
    </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded shadow p-5">
        <div class="text-xs text-gray-400 uppercase">Total Orders</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $summary['total_orders'] }}</div>
    </div>
    <div class="bg-white rounded shadow p-5">
        <div class="text-xs text-gray-400 uppercase">Total Revenue</div>
        <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($summary['total_revenue'], 0) }} <span class="text-sm text-gray-400">TZS</span></div>
    </div>
    <div class="bg-white rounded shadow p-5">
        <div class="text-xs text-gray-400 uppercase">Guest Revenue</div>
        <div class="text-xl font-bold text-purple-600 mt-1">{{ number_format($summary['guest_revenue'], 0) }}</div>
    </div>
    <div class="bg-white rounded shadow p-5">
        <div class="text-xs text-gray-400 uppercase">Walk-in Revenue</div>
        <div class="text-xl font-bold text-blue-600 mt-1">{{ number_format($summary['walkin_revenue'], 0) }}</div>
    </div>
</div>

{{-- Payment breakdown --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4 text-center">
        <div class="text-xs text-gray-400">Cash</div>
        <div class="text-lg font-bold text-gray-700 mt-1">{{ number_format($summary['cash'], 0) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4 text-center">
        <div class="text-xs text-gray-400">Card</div>
        <div class="text-lg font-bold text-gray-700 mt-1">{{ number_format($summary['card'], 0) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4 text-center">
        <div class="text-xs text-gray-400">Charged to Booking</div>
        <div class="text-lg font-bold text-gray-700 mt-1">{{ number_format($summary['charged'], 0) }}</div>
    </div>
</div>

{{-- Overdue orders --}}
@if($overdueOrders->count() > 0)
<div class="bg-red-50 border border-red-200 rounded shadow mb-6 overflow-hidden">
    <div class="px-5 py-3 border-b border-red-200">
        <h2 class="font-semibold text-red-700">⚠ Overdue Orders ({{ $overdueOrders->count() }})</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-red-100">
            <tr>
                <th class="px-4 py-2 text-left text-red-600">Order #</th>
                <th class="px-4 py-2 text-left text-red-600">Customer</th>
                <th class="px-4 py-2 text-left text-red-600">Status</th>
                <th class="px-4 py-2 text-left text-red-600">Expected Ready</th>
                <th class="px-4 py-2 text-center text-red-600">View</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-red-100">
            @foreach($overdueOrders as $order)
            <tr>
                <td class="px-4 py-2 font-mono text-xs">{{ $order->order_number }}</td>
                <td class="px-4 py-2">
                    {{ $order->customer_type === 'guest' ? 'Room ' . $order->room_number : $order->customer_name }}
                </td>
                <td class="px-4 py-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_badge_color }}-100 text-{{ $order->status_badge_color }}-700">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-2 text-red-500 text-xs">{{ $order->expected_ready_at?->format('d M H:i') }}</td>
                <td class="px-4 py-2 text-center">
                    <a href="{{ route('laundry.orders.show', $order) }}" class="text-blue-600 hover:underline text-xs">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Settled orders --}}
<div class="bg-white rounded shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Settled Orders — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-gray-500">Order #</th>
                <th class="px-4 py-2 text-left text-gray-500">Customer</th>
                <th class="px-4 py-2 text-left text-gray-500">Type</th>
                <th class="px-4 py-2 text-right text-gray-500">Items</th>
                <th class="px-4 py-2 text-right text-gray-500">Total (TZS)</th>
                <th class="px-4 py-2 text-center text-gray-500">Payment</th>
                <th class="px-4 py-2 text-left text-gray-500">Settled By</th>
                <th class="px-4 py-2 text-left text-gray-500">Settled At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">
                    <a href="{{ route('laundry.orders.show', $order) }}" class="text-blue-600 hover:underline">
                        {{ $order->order_number }}
                    </a>
                </td>
                <td class="px-4 py-3">
                    {{ $order->customer_type === 'guest' ? 'Room ' . $order->room_number : $order->customer_name }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $order->customer_type === 'guest' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $order->customer_type === 'guest' ? 'Guest' : 'Walk-in' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">{{ $order->items->count() }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ number_format($order->total, 0) }}</td>
                <td class="px-4 py-3 text-center text-xs">
                    {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}
                </td>
                <td class="px-4 py-3 text-xs">{{ $order->settler->name ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $order->settled_at->format('H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No settled orders on this date.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
