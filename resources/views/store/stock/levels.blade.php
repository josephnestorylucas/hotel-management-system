@extends('layouts.app')

@section('title', 'Stock Levels')
@section('page-title', 'Stock Levels')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Levels</h1>
    <div class="flex gap-2">
        @if(auth()->user()->hasAnyRole(['STORE_KEEPER', 'STORE_MANAGER']))
        <a href="{{ route('store.stock.restock-form') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-700 font-medium">+ Restock</a>
        @endif
        @if(auth()->user()->hasAnyRole(['STORE_KEEPER', 'STORE_MANAGER', 'RESTAURANT_MANAGER']))
        <a href="{{ route('store.stock.damage-form') }}"
           class="bg-red-500 text-white px-4 py-2 rounded-xl text-sm hover:bg-red-600 font-medium">Record Damage</a>
        @endif
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by product name..."
           class="border border-gray-200 rounded-xl px-3 py-2 text-sm flex-1 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
    <select name="location_id" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
        <option value="">All Locations</option>
        @foreach($locations as $loc)
        <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
        @endforeach
    </select>
    <button class="bg-gray-200 px-4 py-2 rounded-xl text-sm hover:bg-gray-300 font-medium">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Location</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Quantity</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Reserved</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Available</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($levels as $level)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <a href="{{ route('store.products.show', $level->product) }}" class="font-medium text-primary hover:underline">{{ $level->product->name }}</a>
                    <div class="text-xs text-gray-400">{{ $level->product->unit }}</div>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $level->location->name }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $level->quantity }}</td>
                <td class="px-4 py-3 text-right text-gray-400">{{ $level->reserved_qty }}</td>
                <td class="px-4 py-3 text-right font-semibold">{{ $level->available_qty }}</td>
                <td class="px-4 py-3 text-center">
                    @if($level->quantity <= $level->product->reorder_level)
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Low</span>
                    @else
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">OK</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No stock levels found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $levels->withQueryString()->links() }}</div>
</div>
@endsection
