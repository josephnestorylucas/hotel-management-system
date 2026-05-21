@extends('layouts.app')

@section('title', $ticket->tier_name)
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $ticket->tier_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.tickets.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Tickets</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Price</label>
                <p class="text-2xl font-extrabold text-secondary mt-1">@currency($ticket->price)</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</label>
                <p class="text-2xl font-extrabold text-secondary mt-1">{{ $ticket->attendances_count }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</label>
                <p class="text-2xl font-extrabold text-secondary mt-1">{{ $ticket->quantity_remaining ?? 'Unlimited' }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Type</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst($ticket->tier_type) }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Access</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst(str_replace('-', ' ', $ticket->access_type)) }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                <p class="mt-1">
                    @if($ticket->status === 'on_sale') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">On Sale</span>
                    @elseif($ticket->status === 'sold_out') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Sold Out</span>
                    @elseif($ticket->status === 'archived') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Archived</span>
                    @else <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Draft</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
