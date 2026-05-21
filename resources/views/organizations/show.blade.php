@extends('layouts.app')

@section('title', $organization->name)
@section('page-title', 'Organizations')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $organization->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ ucfirst($organization->type) }} Organization</p>
        </div>
        <div class="flex items-center gap-3">
            @if(!$organization->isVerified())
            <form method="POST" action="{{ route('organizations.verify', $organization) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">Verify</button>
            </form>
            @endif
            <a href="{{ route('organizations.events-list', $organization) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">View Events</a>
            <a href="{{ route('organizations.edit', $organization) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-secondary">Organization Details</h3>
                @if($organization->isVerified())
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Verified</span>
                @else
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Unverified</span>
                @endif
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $organization->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Type</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst($organization->type) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                        <p class="text-sm text-secondary mt-1">{{ $organization->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                        <p class="text-sm text-secondary mt-1">{{ $organization->phone ?? 'N/A' }}</p>
                    </div>
                    @if($organization->registration_number)
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Number</label>
                        <p class="text-sm text-secondary mt-1">{{ $organization->registration_number }}</p>
                    </div>
                    @endif
                    @if($organization->website)
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Website</label>
                        <a href="{{ $organization->website }}" target="_blank" class="text-sm text-primary hover:text-blue-700 mt-1 block">{{ $organization->website }}</a>
                    </div>
                    @endif
                </div>
                @if($organization->full_address)
                <div class="pt-4 border-t border-gray-100">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</label>
                    <p class="text-sm text-secondary mt-1">{{ $organization->full_address }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Contact Person</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</label>
                    <p class="text-sm font-semibold text-secondary mt-1">{{ $organization->contact_person_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                    <p class="text-sm text-secondary mt-1">{{ $organization->contact_person_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                    <p class="text-sm text-secondary mt-1">{{ $organization->contact_person_phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-secondary">Recent Events ({{ $organization->events_count }})</h3>
            <a href="{{ route('organizations.events.create', $organization) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Event
            </a>
        </div>
        @if($organization->events->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($organization->events as $event)
                    <tr class="hover:bg-blue-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-secondary">{{ $event->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $event->start_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            @if($event->status === 'draft') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Draft</span>
                            @elseif($event->status === 'scheduled') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Scheduled</span>
                            @elseif($event->status === 'ongoing') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Ongoing</span>
                            @elseif($event->status === 'completed') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Completed</span>
                            @else <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-8">No events yet. Create your first event for this organization.</p>
        @endif
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('organizations.index') }}" class="text-primary hover:text-blue-700 font-semibold">Back to Organizations</a>
        <form method="POST" action="{{ route('organizations.destroy', $organization) }}" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm('Delete this organization and all its events?')">Delete Organization</button>
        </form>
    </div>
</div>
@endsection
