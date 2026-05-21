@extends('layouts.app')

@section('title', $venue->conferenceHall->name)
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $venue->conferenceHall->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Venues</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hall</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $venue->conferenceHall->name }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</label>
                <p class="text-sm text-secondary mt-1">{{ $venue->conferenceHall->location }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $venue->conferenceHall->capacity }} people</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Setup Type</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst(str_replace('_', ' ', $venue->setup_type)) }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst(str_replace('_', ' ', $venue->status)) }}</p>
            </div>
            @if($venue->booking)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</label>
                <p class="text-sm text-secondary mt-1">{{ $venue->booking->booking_number }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
