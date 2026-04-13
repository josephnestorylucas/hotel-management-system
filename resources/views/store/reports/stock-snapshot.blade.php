@extends('layouts.app')

@section('title', 'Stock Snapshot Report')
@section('page-title', 'Stock Snapshot Report')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Snapshot</h1>
    <div class="flex gap-2">
        <a href="{{ route('store.reports.movements') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Movement Log</a>
        <a href="{{ route('store.reports.damage') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Damage Report</a>
    </div>
</div>

{{-- Location filter --}}
<form method="GET" class="flex gap-3 mb-6 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Location</label>
        <select name="location_id" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
            <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Category</label>
        <select name="category" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">SKU</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Category</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Unit</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Location</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Reorder Lvl</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($levels as $level)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $level->product->name }}</td>
                <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $level->product->sku }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $level->product->category }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $level->product->unit }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $level->location->name }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $level->quantity }}</td>
                <td class="px-4 py-3 text-right text-gray-400">{{ $level->product->reorder_level }}</td>
                <td class="px-4 py-3 text-center">
                    @if($level->quantity <= 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Out of Stock</span>
                    @elseif($level->quantity <= $level->product->reorder_level)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Low Stock</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">OK</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No stock data.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $levels->withQueryString()->links() }}</div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Total Products</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Low Stock Items</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $lowStockCount }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Out of Stock</p>
        <p class="text-2xl font-bold text-red-600">{{ $outOfStockCount }}</p>
    </div>
</div>
@endsection
