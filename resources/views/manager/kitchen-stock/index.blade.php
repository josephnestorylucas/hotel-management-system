@extends('layouts.app')

@section('title', 'Kitchen Stock')
@section('page-title', 'Kitchen Stock Management')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Simple stock monitoring — no recipe tracking</p>
        <a href="{{ route('manager.kitchen-stock.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
            + Add Item
        </a>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
            No kitchen stock items configured yet.
        </div>
    @else
        <div class="space-y-3">
        @foreach($items as $item)
            <a href="{{ route('manager.kitchen-stock.show', $item) }}" class="block bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-sm transition p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                        <p class="text-sm text-gray-500">{{ number_format($item->current_quantity, 2) }} {{ $item->unit }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($item->isLow())
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">LOW STOCK</span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">OK</span>
                        @endif
                        <span class="text-xs text-gray-400">Min: {{ number_format($item->minimum_quantity, 2) }}</span>
                    </div>
                </div>
            </a>
        @endforeach
        </div>
    @endif
</div>
@endsection
