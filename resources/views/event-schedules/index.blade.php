@extends('layouts.app')

@section('title', 'Sessions - ' . $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Sessions</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.schedules.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Session
            </a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    @forelse($schedules as $date => $sessions)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">{{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }}</h3>
        <div class="space-y-3">
            @foreach($sessions as $schedule)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <div class="text-sm font-bold text-secondary">{{ $schedule->start_datetime->format('h:i A') }}</div>
                        <div class="text-xs text-gray-500">{{ $schedule->end_datetime->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-secondary">{{ $schedule->name }}</div>
                        <div class="text-xs text-gray-500">
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ ucfirst($schedule->session_type) }}</span>
                            @if($schedule->speaker_name) &middot; {{ $schedule->speaker_name }} @endif
                            @if($schedule->location) &middot; {{ $schedule->location }} @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('organizations.events.schedules.show', [$organization, $event, $schedule]) }}" class="text-primary hover:text-blue-700 font-semibold text-sm">View</a>
                    <a href="{{ route('organizations.events.schedules.edit', [$organization, $event, $schedule]) }}" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">Edit</a>
                    <form method="POST" action="{{ route('organizations.events.schedules.destroy', [$organization, $event, $schedule]) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm" onclick="return confirm('Delete this session?')">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
        <h3 class="text-lg font-bold text-secondary">No sessions yet</h3>
        <p class="mt-2 text-sm text-gray-500">Add sessions to schedule your event agenda.</p>
        <div class="mt-6">
            <a href="{{ route('organizations.events.schedules.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Session
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection
