@extends('laundry.layout')

@section('title', 'Laundry Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laundry Orders</h1>
    <a href="{{ route('laundry.orders.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        + New Order
    </a>
</div>

{{-- Status summary tiles --}}
<div class="grid grid-cols-5 gap-3 mb-6">
    @foreach([
        'received'   => ['label' => 'Received',   'color' => 'yellow'],
        'processing' => ['label' => 'Processing', 'color' => 'blue'],
        'ready'      => ['label' => 'Ready',      'color' => 'orange'],
        'delivered'  => ['label' => 'Delivered',   'color' => 'indigo'],
        'collected'  => ['label' => 'Collected',   'color' => 'teal'],
    ] as $status => $meta)
    <a href="{{ route('laundry.orders.index', ['status' => $status]) }}"
       class="bg-white rounded shadow p-4 text-center hover:shadow-md transition">
        <div class="text-2xl font-bold text-{{ $meta['color'] }}-600">
            {{ $statusCounts[$status] ?? 0 }}
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ $meta['label'] }}</div>
    </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Order #, name, room, phone..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-48">
    <select name="customer_type" class="border rounded px-3 py-2 text-sm">
        <option value="">All Customers</option>
        <option value="guest"  {{ request('customer_type') === 'guest'  ? 'selected' : '' }}>Hotel Guests</option>
        <option value="walkin" {{ request('customer_type') === 'walkin' ? 'selected' : '' }}>Walk-in</option>
    </select>
    <select name="status" class="border rounded px-3 py-2 text-sm">
        <option value="">All Statuses</option>
        @foreach(['received','processing','ready','delivered','collected','settled','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
            {{ ucfirst($s) }}
        </option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ request('date') }}"
           class="border rounded px-3 py-2 text-sm">
    <button class="bg-gray-200 px-4 py-2 rounded text-sm hover:bg-gray-300">Filter</button>
    <a href="{{ route('laundry.orders.index') }}"
       class="text-gray-400 px-3 py-2 text-sm hover:text-gray-600">Clear</a>
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Order #</th>
                <th class="px-4 py-3 text-left text-gray-600">Customer</th>
                <th class="px-4 py-3 text-left text-gray-600">Type</th>
                <th class="px-4 py-3 text-right text-gray-600">Items</th>
                <th class="px-4 py-3 text-right text-gray-600">Total (TZS)</th>
                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                <th class="px-4 py-3 text-left text-gray-600">Expected Ready</th>
                <th class="px-4 py-3 text-center text-gray-600">View</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 {{ $order->isOverdue() ? 'bg-red-50' : '' }}">
                <td class="px-4 py-3 font-mono text-xs font-medium">
                    {{ $order->order_number }}
                    @if($order->isOverdue())
                        <span class="ml-1 text-red-500 text-xs">⚠ Overdue</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($order->customer_type === 'guest')
                        <div class="font-medium">Room {{ $order->room_number }}</div>
                        <div class="text-xs text-gray-400">Hotel Guest</div>
                    @else
                        <div class="font-medium">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->customer_phone }}</div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $order->customer_type === 'guest'
                           ? 'bg-purple-100 text-purple-700'
                           : 'bg-gray-100 text-gray-600' }}">
                        {{ $order->customer_type === 'guest' ? 'Guest' : 'Walk-in' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">{{ $order->items->count() }}</td>
                <td class="px-4 py-3 text-right font-medium">
                    {{ number_format($order->total, 0) }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($order->status === 'received')      bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                        @elseif($order->status === 'ready')     bg-orange-100 text-orange-700
                        @elseif($order->status === 'delivered') bg-indigo-100 text-indigo-700
                        @elseif($order->status === 'collected') bg-teal-100 text-teal-700
                        @elseif($order->status === 'settled')   bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs
                    {{ $order->isOverdue() ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                    {{ $order->expected_ready_at?->format('d M H:i') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('laundry.orders.show', $order) }}"
                       class="text-blue-600 hover:underline text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
