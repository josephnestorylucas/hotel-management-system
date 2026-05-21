@extends('layouts.app')

@section('title', $schedule->name)
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $schedule->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }} &middot; Session #{{ $schedule->session_number }}</p>
        </div>
        <a href="{{ route('organizations.events.schedules.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Sessions</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Type</label>
                <p class="text-sm font-semibold text-secondary mt-1"><span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">{{ ucfirst($schedule->session_type) }}</span></p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $schedule->duration_minutes }} minutes</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Start</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $schedule->start_datetime->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">End</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $schedule->end_datetime->format('M d, Y h:i A') }}</p>
            </div>
            @if($schedule->location)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $schedule->location }}</p>
            </div>
            @endif
            @if($schedule->speaker_name)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Speaker</label>
                <p class="text-sm font-semibold text-secondary mt-1">{{ $schedule->speaker_name }}</p>
            </div>
            @endif
        </div>
        @if($schedule->description)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
            <p class="text-sm text-gray-700 mt-1">{{ $schedule->description }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
