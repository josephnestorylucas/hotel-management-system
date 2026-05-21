@extends('layouts.app')

@section('title', $organization->name . ' - Events')
@section('page-title', 'Organizations')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $organization->name }} - Events</h2>
            <p class="text-sm text-gray-500 mt-1">All events organized by {{ $organization->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.create', $organization) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Event
            </a>
            <a href="{{ route('organizations.show', $organization) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Org</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Event</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Attendees</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($events as $event)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $event->title }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($event->description, 60) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $event->conferenceType?->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-secondary">{{ $event->start_date->format('M d, Y') }}</div>
                        @if($event->end_date)
                        <div class="text-xs text-gray-500">to {{ $event->end_date->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-primary">{{ $event->attendances_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($event->status === 'draft') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Draft</span>
                        @elseif($event->status === 'scheduled') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Scheduled</span>
                        @elseif($event->status === 'ongoing') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Ongoing</span>
                        @elseif($event->status === 'completed') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Completed</span>
                        @else <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <h3 class="text-lg font-bold text-secondary">No events yet</h3>
                        <p class="mt-2 text-sm text-gray-500">Create the first event for this organization.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
    <div class="mt-6">{{ $events->links() }}</div>
    @endif
</div>
@endsection
