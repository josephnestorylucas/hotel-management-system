@extends('layouts.app')

@section('title', 'Add Kitchen Stock Item')
@section('page-title', 'Add Stock Item')

@section('content')
<div class="max-w-lg mx-auto">
    <form method="POST" action="{{ route('manager.kitchen-stock.store') }}" class="bg-white rounded-xl border border-gray-200 p-6">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" required class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                <input type="text" name="unit" placeholder="e.g. bags, kg, liters, pieces" required class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Quantity</label>
                <input type="number" name="current_quantity" value="0" step="0.01" min="0" class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Quantity (low stock alert)</label>
                <input type="number" name="minimum_quantity" value="0" step="0.01" min="0" class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700">Create Item</button>
        </div>
    </form>
</div>
@endsection
