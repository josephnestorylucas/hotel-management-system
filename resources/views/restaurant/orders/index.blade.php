{{-- resources/views/restaurant/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-extrabold text-gray-800">Orders</h1>
    @if(auth()->user()->isWaiter())
    <a href="{{ route('restaurant.orders.create') }}"
       class="bg-primary text-white px-4 py-2 rounded text-sm hover:opacity-90">
        + New Order
    </a>
    @endif
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('restaurant.orders.index') }}" class="flex flex-wrap gap-3 items-end">
        {{-- Status tabs --}}
        <div>
            <label class="text-xs text-gray-500 block mb-1">Status</label>
            <select name="status" class="border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach(['open','sent','ready','served','charged','settled','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Location --}}
        <div>
            <label class="text-xs text-gray-500 block mb-1">Section</label>
            <select name="location_id" class="border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">All Sections</option>
                @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>
                    {{ $loc->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Date --}}
        <div>
            <label class="text-xs text-gray-500 block mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="border-gray-300 rounded px-3 py-2 text-sm">
        </div>

        <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">Filter</button>
        <a href="{{ route('restaurant.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Clear</a>
    </form>
</div>

{{-- Orders table --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3 font-medium text-gray-600">Order #</th>
                <th class="px-4 py-3 font-medium text-gray-600">Table</th>
                <th class="px-4 py-3 font-medium text-gray-600">Type</th>
                <th class="px-4 py-3 font-medium text-gray-600">Items</th>
                <th class="px-4 py-3 font-medium text-gray-600 text-right">Total</th>
                <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 font-medium text-gray-600">Created</th>
                <th class="px-4 py-3 font-medium text-gray-600"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">
                    <a href="{{ route('restaurant.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                </td>
                <td class="px-4 py-3">{{ $order->table->table_number ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $order->order_type === 'guest' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($order->order_type) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">{{ $order->items->count() }}</td>
                <td class="px-4 py-3 text-right font-semibold">@currency($order->total, 'TZS')</td>
                <td class="px-4 py-3">
                    @php
                        $colors = [
                            'open'      => 'bg-yellow-100 text-yellow-700',
                            'sent'      => 'bg-blue-100 text-blue-700',
                            'ready'     => 'bg-indigo-100 text-indigo-700',
                            'served'    => 'bg-green-100 text-green-700',
                            'charged'   => 'bg-purple-100 text-purple-700',
                            'settled'   => 'bg-gray-200 text-gray-600',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('restaurant.orders.show', $order) }}" class="text-primary hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->withQueryString()->links() }}
</div>
@endsection
