@extends('layouts.app')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">All Events</h2>
            <p class="text-sm text-gray-500 mt-1">Manage conferences, seminars, and workshops</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $statusCounts = ['draft' => 0, 'scheduled' => 0, 'ongoing' => 0, 'completed' => 0, 'cancelled' => 0];
            foreach($events as $e) { if(isset($statusCounts[$e->status])) $statusCounts[$e->status]++; }
        @endphp
        @foreach(['draft' => ['color' => 'gray', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'], 'scheduled' => ['color' => 'blue', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'], 'ongoing' => ['color' => 'green', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'], 'completed' => ['color' => 'purple', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'], 'cancelled' => ['color' => 'red', 'icon' => 'M6 18L18 6M6 6l12 12']] as $status => $config)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-{{ $config['color'] }}-50 to-{{ $config['color'] }}-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $statusCounts[$status] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ ucfirst($status) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Event</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Organization</th>
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
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">{{ substr($event->title, 0, 1) }}</div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-secondary">{{ Str::limit($event->title, 40) }}</div>
                                <div class="text-xs text-gray-500">{{ $event->conferenceType?->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-secondary">{{ $event->organization->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-secondary">{{ $event->start_date->format('M d, Y') }}</div>
                        @if($event->end_date)<div class="text-xs text-gray-500">to {{ $event->end_date->format('M d, Y') }}</div>@endif
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
                        <a href="{{ route('organizations.events.show', [$event->organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-16 text-center text-gray-500">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())<div class="mt-6">{{ $events->links() }}</div>@endif
</div>
@endsection
