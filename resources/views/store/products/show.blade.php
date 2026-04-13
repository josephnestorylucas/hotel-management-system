@extends('layouts.app')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
        <p class="text-sm text-gray-400 font-mono mt-1">{{ $product->sku }}</p>
    </div>
    <div class="flex gap-2">
        @if(auth()->user()->hasRole('STORE_MANAGER'))
        <a href="{{ route('store.products.edit', $product) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded-xl text-sm hover:bg-yellow-600 font-medium">Edit</a>
        @endif
        @if(auth()->user()->hasAnyRole(['STORE_KEEPER', 'STORE_MANAGER']))
        <a href="{{ route('store.stock.restock-form') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-700 font-medium">Restock</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    {{-- Product details --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Details</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Category</dt><dd>{{ $product->category ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Unit</dt><dd>{{ $product->unit }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Cost Price</dt><dd>{{ number_format($product->cost_price, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Selling Price</dt><dd class="font-medium">{{ number_format($product->selling_price, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Reorder Level</dt><dd>{{ $product->reorder_level }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
                <dd>
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            @if($product->description)
            <div class="pt-2 border-t">
                <dt class="text-gray-500 mb-1">Description</dt>
                <dd class="text-gray-700">{{ $product->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Stock by location --}}
    <div class="col-span-2 bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Stock by Location</h2>
        <table class="w-full text-sm">
            <thead><tr class="border-b">
                <th class="pb-2 text-left text-gray-500">Location</th>
                <th class="pb-2 text-right text-gray-500">Quantity</th>
                <th class="pb-2 text-right text-gray-500">Reserved</th>
                <th class="pb-2 text-right text-gray-500">Available</th>
                <th class="pb-2 text-center text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($product->stockLevels as $level)
                <tr>
                    <td class="py-2">{{ $level->location->name }}</td>
                    <td class="py-2 text-right">{{ $level->quantity }}</td>
                    <td class="py-2 text-right text-gray-400">{{ $level->reserved_qty }}</td>
                    <td class="py-2 text-right font-medium">{{ $level->available_qty }}</td>
                    <td class="py-2 text-center">
                        @if($level->quantity <= $product->reorder_level)
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Low</span>
                        @else
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Recent movements --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <h2 class="font-semibold text-gray-700 mb-3">Recent Movements</h2>
    <table class="w-full text-sm">
        <thead><tr class="border-b">
            <th class="pb-2 text-left text-gray-500">Date</th>
            <th class="pb-2 text-left text-gray-500">Type</th>
            <th class="pb-2 text-left text-gray-500">Location</th>
            <th class="pb-2 text-right text-gray-500">Quantity</th>
            <th class="pb-2 text-right text-gray-500">Before</th>
            <th class="pb-2 text-right text-gray-500">After</th>
            <th class="pb-2 text-left text-gray-500">By</th>
            <th class="pb-2 text-left text-gray-500">Notes</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($recentMovements as $m)
            <tr>
                <td class="py-2 text-gray-400 text-xs">{{ $m->created_at->format('d M Y H:i') }}</td>
                <td class="py-2">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        @if(in_array($m->type, ['restock','transfer_in'])) bg-green-100 text-green-700
                        @elseif(in_array($m->type, ['damage','transfer_out'])) bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ str_replace('_', ' ', $m->type) }}
                    </span>
                </td>
                <td class="py-2 text-gray-500">{{ $m->location->name }}</td>
                <td class="py-2 text-right">{{ $m->quantity }}</td>
                <td class="py-2 text-right text-gray-400">{{ $m->quantity_before }}</td>
                <td class="py-2 text-right font-medium">{{ $m->quantity_after }}</td>
                <td class="py-2 text-gray-500 text-xs">{{ $m->actor->name ?? '—' }}</td>
                <td class="py-2 text-gray-400 text-xs">{{ Str::limit($m->notes, 40) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-6 text-center text-gray-400">No movements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
