@extends('laundry.layout')

@section('title', 'Order ' . $laundryOrder->order_number)

@section('content')

{{-- Header --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $laundryOrder->order_number }}</h1>
        <p class="text-sm text-gray-400 mt-1">
            @if($laundryOrder->customer_type === 'guest')
                Hotel Guest · Room {{ $laundryOrder->room_number }}
            @else
                Walk-in · {{ $laundryOrder->customer_name }}
                @if($laundryOrder->customer_phone) · {{ $laundryOrder->customer_phone }} @endif
            @endif
        </p>
    </div>
    <span class="px-3 py-1.5 rounded-full text-sm font-medium
        @if($laundryOrder->status === 'received')      bg-yellow-100 text-yellow-700
        @elseif($laundryOrder->status === 'processing') bg-blue-100 text-blue-700
        @elseif($laundryOrder->status === 'ready')     bg-orange-100 text-orange-700
        @elseif($laundryOrder->status === 'delivered') bg-indigo-100 text-indigo-700
        @elseif($laundryOrder->status === 'collected') bg-teal-100 text-teal-700
        @elseif($laundryOrder->status === 'settled')   bg-green-100 text-green-700
        @else bg-gray-100 text-gray-500 @endif">
        {{ ucfirst($laundryOrder->status) }}
    </span>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Items --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-700">Items</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500">Service</th>
                        <th class="px-4 py-2 text-left text-gray-500">Item</th>
                        <th class="px-4 py-2 text-right text-gray-500">Qty</th>
                        <th class="px-4 py-2 text-right text-gray-500">Unit Price</th>
                        <th class="px-4 py-2 text-right text-gray-500">Subtotal</th>
                        <th class="px-4 py-2 text-left text-gray-500">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($laundryOrder->items as $item)
                    <tr>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $item->serviceItem->service->name }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item->serviceItem->item_name }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($item->subtotal, 0) }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $item->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t bg-gray-50">
                    @if($laundryOrder->discount > 0)
                    <tr>
                        <td colspan="3"></td>
                        <td class="px-4 py-2 text-right text-gray-500 text-sm">Discount</td>
                        <td class="px-4 py-2 text-right text-red-500">
                            - {{ number_format($laundryOrder->discount, 0) }}
                        </td>
                        <td></td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3"></td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">Total (TZS)</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">
                            {{ number_format($laundryOrder->total, 0) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Action buttons --}}
        @if(!in_array($laundryOrder->status, ['settled', 'cancelled']))
        <div class="flex gap-3 flex-wrap">

            {{-- HOUSE_HELP: Start processing --}}
            @if($laundryOrder->status === 'received' && auth()->user()->hasAnyRole(['house_help','supervisor','laundry_manager','admin']))
            <form method="POST" action="{{ route('laundry.orders.process', $laundryOrder) }}">
                @csrf
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Start Processing
                </button>
            </form>
            @endif

            {{-- HOUSE_HELP: Mark ready --}}
            @if($laundryOrder->status === 'processing' && auth()->user()->hasAnyRole(['house_help','supervisor','laundry_manager','admin']))
            <form method="POST" action="{{ route('laundry.orders.ready', $laundryOrder) }}">
                @csrf
                <button class="bg-orange-500 text-white px-4 py-2 rounded text-sm hover:bg-orange-600">
                    Mark Ready
                </button>
            </form>
            @endif

            {{-- Guest: deliver to room --}}
            @if($laundryOrder->status === 'ready' && $laundryOrder->customer_type === 'guest'
                && auth()->user()->hasAnyRole(['house_help','supervisor','laundry_manager','admin']))
            <form method="POST" action="{{ route('laundry.orders.deliver', $laundryOrder) }}">
                @csrf
                <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    Deliver to Room {{ $laundryOrder->room_number }}
                </button>
            </form>
            @endif

            {{-- Walk-in: mark collected --}}
            @if($laundryOrder->status === 'ready' && $laundryOrder->customer_type === 'walkin'
                && auth()->user()->hasAnyRole(['house_help','cashier','supervisor','laundry_manager','admin']))
            <form method="POST" action="{{ route('laundry.orders.collected', $laundryOrder) }}">
                @csrf
                <button class="bg-teal-600 text-white px-4 py-2 rounded text-sm hover:bg-teal-700">
                    Mark Collected
                </button>
            </form>
            @endif

            {{-- Settle payment --}}
            @if(auth()->user()->hasAnyRole(['cashier','front_desk','laundry_manager','supervisor','admin']))
            <button onclick="document.getElementById('settle-panel').classList.toggle('hidden')"
                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                Settle Payment
            </button>
            @endif

            {{-- Cancel --}}
            @if(auth()->user()->hasAnyRole(['supervisor','laundry_manager','admin']))
            <form method="POST" action="{{ route('laundry.orders.cancel', $laundryOrder) }}"
                  onsubmit="return confirm('Cancel this order?')">
                @csrf
                <button class="bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">
                    Cancel Order
                </button>
            </form>
            @endif

        </div>

        {{-- Settle panel --}}
        <div id="settle-panel" class="hidden bg-white rounded shadow p-5 mt-2">
            <h3 class="font-semibold text-gray-700 mb-4">Settle Payment</h3>
            <form method="POST" action="{{ route('laundry.orders.settle', $laundryOrder) }}">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method *
                        </label>
                        <select name="payment_method"
                                class="w-full border rounded px-3 py-2 text-sm"
                                onchange="document.getElementById('booking-field').classList.toggle(
                                    'hidden', this.value !== 'charge_to_booking')">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            @if($laundryOrder->customer_type === 'guest')
                            <option value="charge_to_booking" selected>Charge to Booking</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Discount (optional)
                        </label>
                        <input type="number" name="discount" value="0" min="0" step="100"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div id="booking-field"
                         class="{{ $laundryOrder->customer_type === 'guest' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID</label>
                        <input type="text" name="booking_id"
                               value="{{ $laundryOrder->booking_id }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="mt-4 bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 text-sm">
                    Confirm — {{ number_format($laundryOrder->total, 0) }} TZS
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Order Info</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Received by</dt>
                    <dd>{{ $laundryOrder->receiver->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Received at</dt>
                    <dd>{{ $laundryOrder->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Expected ready</dt>
                    <dd class="{{ $laundryOrder->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                        {{ $laundryOrder->expected_ready_at?->format('d M Y H:i') }}
                        @if($laundryOrder->isOverdue()) ⚠ @endif
                    </dd>
                </div>
                @if($laundryOrder->processed_by)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Processed by</dt>
                    <dd>{{ $laundryOrder->processor->name ?? '—' }}</dd>
                </div>
                @endif
                @if($laundryOrder->ready_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ready at</dt>
                    <dd>{{ $laundryOrder->ready_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->delivered_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Delivered at</dt>
                    <dd>{{ $laundryOrder->delivered_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->collected_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Collected at</dt>
                    <dd>{{ $laundryOrder->collected_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->status === 'settled')
                <div class="flex justify-between border-t pt-2">
                    <dt class="text-gray-500">Payment</dt>
                    <dd class="text-green-700 font-medium">
                        {{ ucwords(str_replace('_', ' ', $laundryOrder->payment_method)) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Settled by</dt>
                    <dd>{{ $laundryOrder->settler->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Settled at</dt>
                    <dd>{{ $laundryOrder->settled_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->special_instructions)
                <div class="border-t pt-2">
                    <dt class="text-gray-500 text-xs mb-1">Special Instructions</dt>
                    <dd class="text-gray-700 text-xs">{{ $laundryOrder->special_instructions }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

</div>
@endsection
