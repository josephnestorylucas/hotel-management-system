{{-- resources/views/restaurant/tables/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tables')
@section('page-title', 'Tables')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-extrabold text-gray-800">Tables</h1>
</div>

{{-- Location tabs --}}
<div class="flex gap-3 mb-6">
    <a href="{{ route('restaurant.tables.index') }}"
       class="px-4 py-2 rounded text-sm border {{ !request('location_id') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        All Sections
    </a>
    @foreach($locations as $loc)
    <a href="{{ route('restaurant.tables.index', ['location_id' => $loc->id]) }}"
       class="px-4 py-2 rounded text-sm border {{ request('location_id') === $loc->id ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $loc->name }}
    </a>
    @endforeach
</div>

{{-- Table grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
    @forelse($tables as $table)
    @php
        $statusColors = [
            'available'  => 'border-green-400 bg-green-50',
            'occupied'   => 'border-red-400 bg-red-50',
            'reserved'   => 'border-yellow-400 bg-yellow-50',
            'cleaning'   => 'border-gray-400 bg-gray-50',
        ];
        $statusText = [
            'available'  => 'text-green-700',
            'occupied'   => 'text-red-700',
            'reserved'   => 'text-yellow-700',
            'cleaning'   => 'text-gray-600',
        ];
    @endphp
    <div class="border-2 rounded-2xl shadow-lg p-4 text-center {{ $statusColors[$table->status] ?? 'border-gray-200 bg-white' }}">
        <p class="text-2xl font-bold text-gray-800">{{ $table->table_number }}</p>
        <p class="text-xs text-gray-400 mb-1">{{ $table->location->name }} &middot; {{ $table->capacity }} seats</p>
        <p class="text-xs font-semibold mb-3 {{ $statusText[$table->status] ?? 'text-gray-600' }}">
            {{ ucfirst($table->status) }}
        </p>

        @if($table->activeOrder)
        <a href="{{ route('restaurant.orders.show', $table->activeOrder) }}"
           class="block text-xs text-primary hover:underline mb-2">
            {{ $table->activeOrder->order_number }}
        </a>
        @endif
        @if($table->status === 'available' || $table->status === 'occupied')
        <a href="{{ route('restaurant.orders.create', ['location_id' => $table->location_id, 'table_id' => $table->id]) }}"
           class="block text-xs bg-primary text-white rounded py-1 hover:opacity-90 mb-2">
            New Order
        </a>
        @endif

        {{-- Quick status change --}}
        <form method="POST" action="{{ route('restaurant.tables.updateStatus', $table) }}" class="mt-2">
            @csrf
            <select name="status" onchange="this.form.submit()" class="w-full text-xs border-gray-300 rounded py-1">
                @foreach(['available','occupied','reserved','cleaning'] as $s)
                <option value="{{ $s }}" {{ $table->status === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-400">
        No tables found. Add tables below.
    </div>
    @endforelse
</div>

{{-- Add new table --}}
@if(auth()->user()->hasAnyRole(['RESTAURANT_MANAGER', 'ADMIN']))
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 max-w-md">
    <h2 class="font-semibold text-gray-700 mb-3">Add New Table</h2>
    <form method="POST" action="{{ route('restaurant.tables.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-xs text-gray-500 mb-1">Section *</label>
            <select name="location_id" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">— Select —</option>
                @foreach($locations as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
            @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Table Number *</label>
                <input type="text" name="table_number" required maxlength="20" placeholder="e.g. B1, K5"
                       class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                @error('table_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="w-24">
                <label class="block text-xs text-gray-500 mb-1">Capacity *</label>
                <input type="number" name="capacity" required min="1" max="20" value="4"
                       class="w-full border-gray-300 rounded px-3 py-2 text-sm">
            </div>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded text-sm hover:opacity-90">
            Add Table
        </button>
    </form>
</div>
@endif
@endsection
