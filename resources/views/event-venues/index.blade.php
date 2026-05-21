@extends('layouts.app')

@section('title', 'Venues - ' . $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Venues</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.venues.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Assign Venue
            </a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    @forelse($venues as $venue)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ $venue->conferenceHall->name }}</h3>
                <p class="text-sm text-gray-500">{{ $venue->conferenceHall->building->name ?? '-' }} &middot; Capacity: {{ $venue->conferenceHall->capacity }}</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                @if($venue->status === 'confirmed') bg-green-100 text-green-700
                @elseif($venue->status === 'active') bg-blue-100 text-blue-700
                @elseif($venue->status === 'cancelled') bg-red-100 text-red-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ ucfirst(str_replace('_', ' ', $venue->status)) }}
            </span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Setup</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst($venue->setup_type) }}</p>
            </div>
            @if($venue->expected_setup_start)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Setup Start</label>
                <p class="text-sm text-secondary mt-1">{{ \Carbon\Carbon::parse($venue->expected_setup_start)->format('M d, H:i') }}</p>
            </div>
            @endif
            @if($venue->booking)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Booking #</label>
                <p class="text-sm text-secondary mt-1">{{ $venue->booking->booking_number }}</p>
            </div>
            @endif
        </div>
        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('organizations.events.venues.edit', [$organization, $event, $venue]) }}" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">Edit</a>
            <form method="POST" action="{{ route('organizations.events.venues.destroy', [$organization, $event, $venue]) }}" class="inline">@csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm" onclick="return confirm('Remove this venue?')">Remove</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
        <h3 class="text-lg font-bold text-secondary">No venues assigned</h3>
        <p class="mt-2 text-sm text-gray-500">Assign conference halls to your event.</p>
    </div>
    @endforelse
</div>
@endsection
