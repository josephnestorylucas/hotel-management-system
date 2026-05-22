@extends('layouts.app')

@section('title', 'Kitchen Queue')
@section('page-title', 'Kitchen Queue')

@section('content')
<div x-data="kitchenQueue()" x-init="startAutoRefresh()" class="h-[calc(100vh-8rem)]">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kitchen Order Tickets</h2>
            <p class="text-sm text-gray-500">{{ $tickets->count() }} active ticket(s)</p>
        </div>
        <div class="flex gap-2">
            <button @click="window.location.reload()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                Refresh
            </button>
        </div>
    </div>

    @if($tickets->isEmpty())
    <div class="flex items-center justify-center h-64">
        <div class="text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No pending kitchen tickets</p>
            <p class="text-gray-400 text-sm mt-1">Tickets will appear here when orders are sent</p>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($tickets as $ticket)
        <div class="bg-white rounded-xl border-2 {{ $ticket->status === 'pending' ? 'border-red-200 bg-red-50/30' : ($ticket->status === 'preparing' ? 'border-amber-200 bg-amber-50/30' : 'border-green-200 bg-green-50/30') }} shadow-sm overflow-hidden">
            <div class="p-4 {{ $ticket->status === 'pending' ? 'bg-red-100' : ($ticket->status === 'preparing' ? 'bg-amber-100' : 'bg-green-100') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-bold text-gray-800">KOT #{{ $ticket->id.substr(0, 6) }}</span>
                        @if($ticket->table)
                        <span class="ml-2 text-sm text-gray-600">Table {{ $ticket->table->name }}</span>
                        @endif
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold uppercase {{ $ticket->status === 'pending' ? 'bg-red-200 text-red-800' : ($ticket->status === 'preparing' ? 'bg-amber-200 text-amber-800' : 'bg-green-200 text-green-800') }}">
                        {{ $ticket->status }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $ticket->created_at->diffForHumans() }}
                    @if($ticket->order)
                    &middot; {{ $ticket->order->customer_name ?? 'Walk-in' }}
                    @endif
                </div>
            </div>

            <div class="p-4">
                <div class="space-y-2">
                    @foreach($ticket->items as $item)
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-sm font-semibold text-gray-800">{{ $item['name'] }}</span>
                            @if(!empty($item['notes']))
                            <div class="text-xs text-gray-500 italic">{{ $item['notes'] }}</div>
                            @endif
                            @if(!empty($item['options']))
                            <div class="text-xs text-blue-600">
                                @foreach($item['options'] as $opt)
                                {{ $opt['group_name'] ?? 'Option' }}: {{ collect($opt['values'] ?? [])->pluck('label')->implode(', ') }}
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <span class="text-sm font-bold text-gray-600">x{{ $item['quantity'] }}</span>
                    </div>
                    @endforeach
                </div>

                @if($ticket->notes)
                <div class="mt-3 p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-xs text-yellow-800">{{ $ticket->notes }}</p>
                </div>
                @endif

                <div class="mt-4 flex gap-2">
                    @if($ticket->status === 'pending')
                    <form action="{{ route('restaurant.kitchen.preparing', $ticket) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            Start Preparing
                        </button>
                    </form>
                    @elseif($ticket->status === 'preparing')
                    <form action="{{ route('restaurant.kitchen.ready', $ticket) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            Mark Ready
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
function kitchenQueue() {
    return {
        refreshInterval: null,
        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                window.location.reload();
            }, 15000);
        },
        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        },
        destroy() {
            this.stopAutoRefresh();
        }
    }
}
</script>
@endpush
@endsection
