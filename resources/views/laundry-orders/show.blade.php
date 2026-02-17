{{-- resources/views/laundry-orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Order #' . $laundryOrder->order_number . ' - MRK Hotel')
@section('page-title', 'Laundry Order Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('laundry.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Orders
        </a>

        <div class="flex items-center gap-2">
            @if($laundryOrder->status === 'pending')
                @if(in_array(auth()->user()->role->name, ['admin', 'supervisor', 'house_help']))
                <form action="{{ route('laundry.mark-in-progress', $laundryOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Start Processing
                    </button>
                </form>
                @endif
                @if(in_array(auth()->user()->role->name, ['admin', 'supervisor', 'front_desk']))
                <a href="{{ route('laundry.edit', $laundryOrder) }}" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">
                    Edit
                </a>
                <form action="{{ route('laundry.destroy', $laundryOrder) }}" method="POST" class="inline" onsubmit="return confirm('Delete this order?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </form>
                @endif
            @endif

            @if($laundryOrder->status === 'in_progress')
                @if(in_array(auth()->user()->role->name, ['admin', 'supervisor', 'house_help']))
                <form action="{{ route('laundry.mark-completed', $laundryOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Mark Completed
                    </button>
                </form>
                @endif
            @endif

            @if($laundryOrder->status === 'completed')
                @if(in_array(auth()->user()->role->name, ['admin', 'supervisor', 'front_desk']))
                <form action="{{ route('laundry.mark-delivered', $laundryOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        Mark Delivered
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    {{-- Order Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Order #{{ $laundryOrder->order_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">Created {{ $laundryOrder->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <div>
                @switch($laundryOrder->status)
                    @case('pending')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @break
                    @case('in_progress')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">In Progress</span>
                        @break
                    @case('completed')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @break
                    @case('delivered')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Delivered</span>
                        @break
                @endswitch
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Guest</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->guest->first_name ?? '' }} {{ $laundryOrder->guest->last_name ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Booking</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->booking->booking_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Room</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->booking->room->room_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Created By</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->creator->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Received At</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->received_at ? $laundryOrder->received_at->format('M d, H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Completed At</p>
                <p class="text-sm font-semibold text-gray-900">{{ $laundryOrder->completed_at ? $laundryOrder->completed_at->format('M d, H:i') : '-' }}</p>
            </div>
        </div>

        @if($laundryOrder->notes)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Notes</p>
            <p class="text-sm text-gray-700">{{ $laundryOrder->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Status Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Status Timeline</h3>
        <div class="flex items-center gap-0">
            @php
                $statuses = ['pending', 'in_progress', 'completed', 'delivered'];
                $statusLabels = ['Pending', 'In Progress', 'Completed', 'Delivered'];
                $statusColors = ['yellow', 'blue', 'green', 'purple'];
                $currentIndex = array_search($laundryOrder->status, $statuses);
            @endphp
            @foreach($statuses as $i => $status)
                <div class="flex-1 relative">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i <= $currentIndex ? 'bg-' . $statusColors[$i] . '-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            @if($i < $currentIndex)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        @if($i < count($statuses) - 1)
                        <div class="flex-1 h-1 mx-1 {{ $i < $currentIndex ? 'bg-' . $statusColors[$i] . '-400' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                    <p class="text-xs mt-1 {{ $i <= $currentIndex ? 'text-gray-700 font-medium' : 'text-gray-400' }}">{{ $statusLabels[$i] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Itemized List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($laundryOrder->items as $index => $item)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $item->laundryItem->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 text-right">{{ number_format($item->unit_price) }}</td>
                        <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($item->total_price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-bold text-gray-900">Grand Total:</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-green-600">{{ number_format($laundryOrder->total_amount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Charge Status --}}
    @if($laundryOrder->bookingCharge)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Billing</h3>
        <div class="flex items-center justify-between p-4 rounded-lg {{ $laundryOrder->bookingCharge->status === 'paid' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
            <div>
                <p class="text-sm font-semibold {{ $laundryOrder->bookingCharge->status === 'paid' ? 'text-green-800' : 'text-yellow-800' }}">
                    Booking Charge: {{ number_format($laundryOrder->bookingCharge->amount) }}
                </p>
                <p class="text-xs {{ $laundryOrder->bookingCharge->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ $laundryOrder->bookingCharge->description }}
                </p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                {{ $laundryOrder->bookingCharge->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ ucfirst($laundryOrder->bookingCharge->status) }}
            </span>
        </div>
    </div>
    @endif
</div>
@endsection
