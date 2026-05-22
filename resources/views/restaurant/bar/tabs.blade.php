@extends('layouts.app')

@section('title', 'Bar Tabs')
@section('page-title', 'Bar Tabs')

@section('content')
<div class="h-[calc(100vh-8rem)]">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Open Bar Tabs</h2>
            <p class="text-sm text-gray-500">{{ $tabs->count() }} open tab(s)</p>
        </div>
        <a href="{{ route('restaurant.bar.queue') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
            Back to Queue
        </a>
    </div>

    @if($tabs->isEmpty())
    <div class="flex items-center justify-center h-64">
        <div class="text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No open bar tabs</p>
            <p class="text-gray-400 text-sm mt-1">Open tabs will appear here</p>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tabs as $tab)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-4 bg-purple-50 border-b border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-bold text-gray-800">{{ $tab->order_number }}</span>
                        @if($tab->table)
                        <span class="ml-2 text-sm text-gray-600">Table {{ $tab->table->name }}</span>
                        @endif
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold uppercase bg-purple-200 text-purple-800">
                        {{ $tab->status }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $tab->customer_name ?? 'Bar Tab' }}
                    &middot; {{ $tab->created_at->diffForHumans() }}
                </div>
            </div>

            <div class="p-4">
                <div class="space-y-2">
                    @foreach($tab->items->where('status', '!=', 'cancelled') as $item)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-800">{{ $item->item_name_snapshot ?? $item->menuItem?->name ?? 'Item' }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">x{{ $item->quantity }}</span>
                            <span class="text-sm font-semibold text-gray-700">{{ number_format($item->subtotal) }} TZS</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">{{ number_format($tab->total) }} TZS</span>
                    <a href="{{ route('restaurant.orders.show', $tab) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition-colors">
                        View Tab
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
