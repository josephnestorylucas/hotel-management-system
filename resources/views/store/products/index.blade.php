@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Products</h1>
    @if(auth()->user()->hasRole('STORE_MANAGER'))
    <div class="flex items-center gap-2">
        <a href="{{ route('store.products.create') }}"
           class="bg-primary text-white px-4 py-2 rounded-xl hover:bg-blue-700 text-sm font-medium">
            + New Product
        </a>
        <a href="{{ route('store.products.archived') }}"
           class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            View Deleted
        </a>
    </div>
    @endif
</div>

{{-- Search & filter --}}
<form method="GET" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search products..."
           class="border border-gray-200 rounded-xl px-3 py-2 text-sm flex-1 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
    <select name="category" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
        <option value="">All Categories</option>
        @foreach(['beverages','food','toiletries','cleaning','stationery','other'] as $cat)
        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
        @endforeach
    </select>
    <select name="product_type" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
        <option value="">All Types</option>
        <option value="normal" {{ request('product_type') === 'normal' ? 'selected' : '' }}>Normal</option>
        <option value="bar" {{ request('product_type') === 'bar' ? 'selected' : '' }}>Bar</option>
    </select>
    <button class="bg-gray-200 px-4 py-2 rounded-xl text-sm hover:bg-gray-300 font-medium">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold"></th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Name</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">SKU</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Category</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Unit</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Type</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Cost Price</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Selling Price</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Bar Stock</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    @if($product->hasImage())
                        <img src="{{ $product->image_thumb_url ?? $product->image_url }}"
                             alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                    @else
                        <img src="{{ asset('images/product-placeholder.svg') }}"
                             alt="No image" class="w-10 h-10 rounded-lg border border-gray-200">
                    @endif
                </td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $product->sku }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $product->category ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $product->unit }}</td>
                <td class="px-4 py-3 text-center">
                    @if($product->product_type === 'bar')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Bar</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right text-gray-700">@currency($product->cost_price, 'TZS')</td>
                <td class="px-4 py-3 text-right text-gray-700">@currency($product->selling_price, 'TZS')</td>
                <td class="px-4 py-3 text-right">
                    @php
                        $barStock = $product->stockLevels->firstWhere('location.code', 'bar');
                        $barQty = $barStock ? (float) $barStock->available_qty : 0;
                    @endphp
                    @if($barQty <= 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">0</span>
                    @elseif($barQty <= $product->reorder_level)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ number_format($barQty, 0) }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ number_format($barQty, 0) }}</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('store.products.show', $product) }}"
                       class="text-primary hover:underline text-xs mr-2">View</a>
                    @if(auth()->user()->hasRole('STORE_MANAGER'))
                    <a href="{{ route('store.products.edit', $product) }}"
                       class="text-yellow-600 hover:underline text-xs">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <x-empty-state table colspan="10" title="No products found" message="Add a product or adjust the filters to see results here." />
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
