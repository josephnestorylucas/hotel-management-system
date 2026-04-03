{{-- resources/views/restaurant/orders/show.blade.php --}}
@extends('restaurant.layout')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-400">
                {{ $order->location->name ?? '—' }}
                @if($order->table) &middot; Table {{ $order->table->table_number }} @endif
                &middot; {{ $order->created_at->format('d M Y H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($order->status === 'settled')
                <a href="{{ route('receipts.order', $order) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Receipt
                </a>
            @else
                <a href="{{ route('receipts.order', $order) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 border-2 border-yellow-400 text-yellow-700 text-sm font-semibold rounded-lg hover:bg-yellow-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    View Receipt (Unpaid)
                </a>
            @endif
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
            <span class="text-sm px-4 py-1.5 rounded-full font-semibold {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order items --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b font-semibold text-gray-700 text-sm flex justify-between items-center">
                    <span>Items ({{ $order->items->where('status', '!=', 'cancelled')->count() }})</span>
                    @if($order->status === 'open')
                    <span class="text-xs text-gray-400">You can add/remove items</span>
                    @endif
                </div>
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 text-xs">
                        <tr>
                            <th class="px-4 py-2">Item</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Price</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                            <th class="px-4 py-2">Status</th>
                            @if($order->status === 'open')
                            <th class="px-4 py-2"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                        <tr class="{{ $item->status === 'cancelled' ? 'opacity-40 line-through' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $item->menuItem->name }}</span>
                                @if($item->notes)
                                <span class="block text-xs text-gray-400">{{ $item->notes }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 0) }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->subtotal, 0) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs {{ $item->status === 'cancelled' ? 'text-red-500' : 'text-gray-500' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            @if($order->status === 'open')
                            <td class="px-4 py-3 text-right">
                                @if($item->status !== 'cancelled')
                                <form method="POST" action="{{ route('restaurant.orders.removeItem', [$order, $item]) }}"
                                      onsubmit="return confirm('Remove this item?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 text-xs hover:underline">Remove</button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="bg-gray-50 px-4 py-3 border-t text-right space-y-1">
                    @if($order->discount > 0)
                    <p class="text-sm text-gray-500">Subtotal: {{ number_format($order->subtotal, 0) }} TZS</p>
                    <p class="text-sm text-red-500">Discount: -{{ number_format($order->discount, 0) }} TZS</p>
                    @endif
                    @if($order->tax > 0)
                    <p class="text-sm text-gray-500">Tax: {{ number_format($order->tax, 0) }} TZS</p>
                    @endif
                    <p class="text-lg font-bold text-gray-800">Total: {{ number_format($order->total, 0) }} TZS</p>
                </div>
            </div>

            {{-- Add item form (open orders only) --}}
            @if($order->status === 'open')
            <div class="bg-white rounded-lg shadow p-5" x-data="{ showAdd: false }">
                <button @click="showAdd = !showAdd" class="text-sm text-primary hover:underline font-medium">
                    + Add Item to Order
                </button>
                <form method="POST" action="{{ route('restaurant.orders.addItem', $order) }}"
                      x-show="showAdd" x-cloak class="mt-4 space-y-3">
                    @csrf
                    <select name="menu_item_id" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— Select menu item —</option>
                        @php
                            $menuCategories = \App\Models\MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->where('is_available', true)])
                                ->where('is_active', true)
                                ->where('location_id', $order->location_id)
                                ->get();
                        @endphp
                        @foreach($menuCategories as $cat)
                            @if($cat->menuItems->count())
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->menuItems as $mi)
                                <option value="{{ $mi->id }}">{{ $mi->name }} — {{ number_format($mi->selling_price, 0) }} TZS</option>
                                @endforeach
                            </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <input type="number" name="quantity" value="1" min="1" required class="w-20 border-gray-300 rounded px-3 py-2 text-sm">
                        <input type="text" name="notes" placeholder="Notes (optional)" class="flex-1 border-gray-300 rounded px-3 py-2 text-sm">
                        <button type="submit" class="bg-primary text-white px-4 py-2 text-sm rounded hover:opacity-90">Add</button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Order info --}}
            <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
                <h3 class="font-semibold text-gray-700">Order Info</h3>
                <div class="flex justify-between">
                    <span class="text-gray-500">Type</span>
                    <span class="font-medium">{{ ucfirst($order->order_type) }}</span>
                </div>
                @if($order->customer_name)
                <div class="flex justify-between">
                    <span class="text-gray-500">Customer</span>
                    <span class="font-medium">{{ $order->customer_name }}</span>
                </div>
                @endif
                @if($order->booking_id)
                <div class="flex justify-between">
                    <span class="text-gray-500">Booking</span>
                    <span class="font-mono text-xs">{{ Str::limit($order->booking_id, 8) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Created by</span>
                    <span>{{ $order->creator->name ?? '—' }}</span>
                </div>
                @if($order->payment_method)
                <div class="flex justify-between">
                    <span class="text-gray-500">Payment</span>
                    <span class="font-medium">{{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</span>
                </div>
                @endif
                @if($order->settler)
                <div class="flex justify-between">
                    <span class="text-gray-500">Settled by</span>
                    <span>{{ $order->settler->name }}</span>
                </div>
                @endif
                @if($order->settled_at)
                <div class="flex justify-between">
                    <span class="text-gray-500">Settled at</span>
                    <span class="text-xs">{{ $order->settled_at }}</span>
                </div>
                @endif
                @if($order->notes)
                <div class="pt-2 border-t">
                    <span class="text-gray-500 block mb-1">Notes</span>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Workflow actions --}}
            @if(!in_array($order->status, ['settled', 'charged', 'cancelled']))
            <div class="bg-white rounded-lg shadow p-5 space-y-3">
                <h3 class="font-semibold text-gray-700 text-sm">Actions</h3>

                @if($order->status === 'open')
                <form method="POST" action="{{ route('restaurant.orders.send', $order) }}">
                    @csrf
                    <button class="w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700">
                        Send to Kitchen/Bar
                    </button>
                </form>
                @endif

                @if($order->status === 'sent')
                <form method="POST" action="{{ route('restaurant.orders.ready', $order) }}">
                    @csrf
                    <button class="w-full bg-indigo-600 text-white py-2 rounded text-sm hover:bg-indigo-700">
                        Mark Ready
                    </button>
                </form>
                @endif

                @if($order->status === 'ready')
                <form method="POST" action="{{ route('restaurant.orders.serve', $order) }}">
                    @csrf
                    <button class="w-full bg-green-600 text-white py-2 rounded text-sm hover:bg-green-700">
                        Mark Served
                    </button>
                </form>
                @endif

                {{-- Settlement/Checkout --}}
                @if($order->order_type === 'walkin')
                    {{-- Walk-in: Use unified payment modal (direct payment allowed) --}}
                    <x-walkin-payment-modal 
                        :amount="$order->total" 
                        :order-id="$order->id"
                        :order-number="$order->order_number"
                        module="restaurant"
                        :customer-name="$order->customer_name ?? ''"
                        :customer-phone="$order->customer_phone ?? ''"
                    />
                @else
                    {{-- Guest: Charge to Booking and Proceed to Checkout --}}
                    <div x-data="{ showCharge: false }">
                        <button @click="showCharge = !showCharge"
                                class="w-full bg-primary text-white py-2 rounded text-sm hover:opacity-90">
                            Charge to Guest Folio
                        </button>
                        <form method="POST" action="{{ route('restaurant.orders.settle', $order) }}"
                              x-show="showCharge" x-cloak class="mt-3 space-y-3 p-3 bg-gray-50 rounded">
                            @csrf
                            <p class="text-xs text-gray-600">
                                This will add the order to the guest's folio. Payment will be collected at checkout.
                            </p>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Booking ID *</label>
                                <input type="text" name="booking_id" value="{{ $order->booking_id }}" required
                                       class="w-full border-gray-300 rounded px-3 py-2 text-sm" placeholder="Booking UUID">
                            </div>
                            <button type="submit" class="w-full bg-green-700 text-white py-2 rounded text-sm hover:bg-green-800">
                                Charge to Booking & Proceed to Checkout
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Cancel --}}
                <form method="POST" action="{{ route('restaurant.orders.cancel', $order) }}"
                      onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button class="w-full border border-red-300 text-red-600 py-2 rounded text-sm hover:bg-red-50">
                        Cancel Order
                    </button>
                </form>
            </div>
            @elseif($order->status === 'charged')
            {{-- Order is charged but not yet settled at checkout --}}
            <div class="bg-white rounded-lg shadow p-5 space-y-3">
                <h3 class="font-semibold text-gray-700 text-sm">Order Charged to Folio</h3>
                <p class="text-sm text-gray-600">
                    This order has been added to the guest's folio. Payment pending at checkout.
                </p>
                <a href="{{ route('finance.checkout.show', $order->booking_id) }}"
                   class="block w-full bg-primary text-white py-2 rounded text-sm text-center hover:opacity-90">
                    View Guest Checkout
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
