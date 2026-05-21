@extends('layouts.app')

@section('title', 'Tickets - ' . $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Ticket Tiers</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.tickets.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Ticket Tier
            </a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    @forelse($tickets as $ticket)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($ticket->color)
                <div class="w-4 h-12 rounded-full" style="background-color: {{ $ticket->color }}"></div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-secondary">{{ $ticket->tier_name }}</h3>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($ticket->status === 'on_sale') bg-green-100 text-green-700
                            @elseif($ticket->status === 'sold_out') bg-red-100 text-red-700
                            @elseif($ticket->status === 'archived') bg-gray-100 text-gray-500
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ ucfirst($ticket->tier_type) }}</span>
                    </div>
                    @if($ticket->description)
                    <p class="text-sm text-gray-500 mt-1">{{ $ticket->description }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-extrabold text-secondary">@currency($ticket->price)</div>
                <div class="text-xs text-gray-500">
                    {{ $ticket->attendances_count }} sold
                    @if($ticket->quantity_available !== null) / {{ $ticket->quantity_available }} available @else (unlimited) @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('organizations.events.tickets.show', [$organization, $event, $ticket]) }}" class="text-primary hover:text-blue-700 font-semibold text-sm">View</a>
            <a href="{{ route('organizations.events.tickets.edit', [$organization, $event, $ticket]) }}" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">Edit</a>
            @if($ticket->status === 'draft')
            <form method="POST" action="{{ route('organizations.events.tickets.put-on-sale', [$organization, $event, $ticket]) }}" class="inline">@csrf
                <button type="submit" class="text-green-600 hover:text-green-700 font-semibold text-sm">Put on Sale</button>
            </form>
            @endif
            @if($ticket->attendances_count === 0)
            <form method="POST" action="{{ route('organizations.events.tickets.destroy', [$organization, $event, $ticket]) }}" class="inline">@csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm" onclick="return confirm('Delete this ticket tier?')">Delete</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
        <h3 class="text-lg font-bold text-secondary">No ticket tiers yet</h3>
        <p class="mt-2 text-sm text-gray-500">Create ticket tiers to sell registrations for your event.</p>
        <div class="mt-6">
            <a href="{{ route('organizations.events.tickets.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Ticket Tier
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection
