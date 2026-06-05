@extends('layouts.app')

@section('title', 'Attendees - ' . $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Attendees</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.attendances.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Register Attendee
            </a>
            <a href="{{ route('organizations.events.attendances.bulk-upload', [$organization, $event]) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Bulk Upload</a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-secondary">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Total</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-green-600">{{ $stats['confirmed'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Confirmed</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Pending</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-blue-600">{{ $stats['checked_in'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-red-600">{{ $stats['no_shows'] }}</div>
            <div class="text-xs text-gray-500 font-medium">No Shows</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
        <form method="GET" class="flex items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or pass #" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Company</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Pass</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Check-ins</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $a)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-secondary">{{ $a->full_name }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $a->ticket_number }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $a->email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $a->company ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $a->eventPass?->tier_name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($a->registration_status === 'confirmed') <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Confirmed</span>
                        @elseif($a->registration_status === 'pending') <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                        @elseif($a->registration_status === 'no_show') <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">No Show</span>
                        @else <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($a->registration_status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-secondary">{{ $a->total_check_ins }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('organizations.events.attendances.show', [$organization, $event, $a]) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                            <a href="{{ route('organizations.events.attendances.edit', [$organization, $event, $a]) }}" class="text-gray-600 hover:text-gray-800 font-semibold">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <x-empty-state table colspan="7" title="No attendees found" message="No attendance records match the current view." />
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attendances->hasPages())<div class="mt-6">{{ $attendances->links() }}</div>@endif
</div>
@endsection
