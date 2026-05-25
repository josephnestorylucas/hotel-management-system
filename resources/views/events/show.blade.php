@extends('layouts.app')

@section('title', $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $event->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $organization->name }} &middot; {{ $event->conferenceType?->name ?? 'Event' }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($event->isDraft())
            <form method="POST" action="{{ route('organizations.events.publish', [$organization, $event]) }}" class="inline">@csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Publish</button>
            </form>
            @endif
            @if($event->status === 'scheduled')
            <form method="POST" action="{{ route('organizations.events.start', [$organization, $event]) }}" class="inline">@csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Start Event</button>
            </form>
            @endif
            @if(in_array($event->status, ['scheduled', 'ongoing']))
            <button type="button" onclick="document.getElementById('complete-form').classList.toggle('hidden')" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Complete</button>
            @endif
            <a href="{{ route('organizations.events.check-in.dashboard', [$organization, $event]) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Check-in</a>
            <a href="{{ route('organizations.events.check-in.scanner', [$organization, $event]) }}" class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700">Scanner</a>
            @if($event->isDraft())
            <a href="{{ route('organizations.events.edit', [$organization, $event]) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">Edit</a>
            @endif
        </div>
    </div>

    <!-- Complete Event Form (with discount) -->
    @if(in_array($event->status, ['scheduled', 'ongoing']))
    <div id="complete-form" class="hidden bg-white rounded-2xl shadow-lg border border-purple-200 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Complete Event & Apply Discount</h3>
        <form method="POST" action="{{ route('organizations.events.complete', [$organization, $event]) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount %</label>
                    <input type="number" name="discount_percent" value="0" min="0" max="100" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Reason</label>
                    <input type="text" name="discount_reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="e.g. Early completion, loyalty discount">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Complete Event</button>
                <button type="button" onclick="document.getElementById('complete-form').classList.add('hidden')" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-secondary">{{ $stats['total_attendances'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Total Registered</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-green-600">{{ $stats['confirmed'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Confirmed</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-blue-600">{{ $stats['checked_in'] }}</div>
            <div class="text-xs text-gray-500 font-medium">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-red-600">{{ $stats['no_shows'] }}</div>
            <div class="text-xs text-gray-500 font-medium">No Shows</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-2xl font-extrabold text-purple-600">@currency($billing['grand_total'])</div>
            <div class="text-xs text-gray-500 font-medium">Grand Total</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-secondary">Event Information</h3>
                    @if($event->status === 'draft') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Draft</span>
                    @elseif($event->status === 'scheduled') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Scheduled</span>
                    @elseif($event->status === 'ongoing') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Ongoing</span>
                    @elseif($event->status === 'completed') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Completed</span>
                    @else <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Cancelled</span>
                    @endif
                </div>
                @if($event->description)
                <p class="text-sm text-gray-700 mb-4">{{ $event->description }}</p>
                @endif
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $event->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $event->end_date ? $event->end_date->format('M d, Y') : 'Single day' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Visibility</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ ucfirst($event->visibility) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $event->capacity ?? 'Unlimited' }}</p>
                    </div>
                </div>
            </div>

            <!-- Billing Summary -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Billing Summary</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Hall Rate Total</span>
                        <span class="text-sm font-semibold text-secondary">@currency($billing['hall_rate_total'])</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Event Rate Total</span>
                        <span class="text-sm font-semibold text-secondary">@currency($billing['event_rate_total'])</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm font-semibold text-gray-700">Subtotal</span>
                        <span class="text-sm font-bold text-secondary">@currency($billing['subtotal'])</span>
                    </div>
                    @if($billing['discount_amount'] > 0)
                    <div class="flex items-center justify-between py-2 border-b border-green-100 bg-green-50 -mx-6 px-6">
                        <span class="text-sm text-green-700">Discount ({{ $billing['discount_percent'] }}%)</span>
                        <span class="text-sm font-semibold text-green-700">-@currency($billing['discount_amount'])</span>
                    </div>
                    @if($event->discount_reason)
                    <div class="text-xs text-gray-500 italic">Reason: {{ $event->discount_reason }}</div>
                    @endif
                    @endif
                    <div class="flex items-center justify-between py-2">
                        <span class="text-base font-bold text-secondary">Grand Total</span>
                        <span class="text-base font-extrabold text-purple-600">@currency($billing['grand_total'])</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('organizations.events.schedules.index', [$organization, $event]) }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all text-center">
                    <div class="text-2xl font-extrabold text-blue-600">{{ $event->schedules_count }}</div>
                    <div class="text-xs text-gray-500 font-medium">Sessions</div>
                </a>
                <a href="{{ route('organizations.events.passes.index', [$organization, $event]) }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all text-center">
                    <div class="text-2xl font-extrabold text-purple-600">{{ $event->passes_count }}</div>
                    <div class="text-xs text-gray-500 font-medium">Pass Types</div>
                </a>
                <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all text-center">
                    <div class="text-2xl font-extrabold text-green-600">{{ $event->attendances_count }}</div>
                    <div class="text-xs text-gray-500 font-medium">Attendees</div>
                </a>
                <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all text-center">
                    <div class="text-2xl font-extrabold text-orange-600">{{ $event->venues->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Venues</div>
                </a>
            </div>

            <!-- Recent Attendees -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-secondary">Recent Attendees</h3>
                    <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold text-sm">View All</a>
                </div>
                @if($event->attendances->count() > 0)
                <div class="space-y-3">
                    @foreach($event->attendances as $attendance)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <div class="text-sm font-medium text-secondary">{{ $attendance->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->email }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($attendance->pass_type === 'speaker') bg-purple-100 text-purple-700
                                @elseif($attendance->pass_type === 'moderator') bg-blue-100 text-blue-700
                                @elseif($attendance->pass_type === 'backdoor') bg-orange-100 text-orange-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($attendance->pass_type ?? 'attendee') }}
                            </span>
                            @if($attendance->registration_status === 'confirmed') <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Confirmed</span>
                            @elseif($attendance->registration_status === 'pending') <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            @else <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($attendance->registration_status) }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">No attendees yet.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Links -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('organizations.events.attendances.create', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-100 transition-colors text-center">Register Attendee</a>
                    <a href="{{ route('organizations.events.attendances.bulk-upload', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-purple-50 text-purple-700 text-sm font-semibold rounded-lg hover:bg-purple-100 transition-colors text-center">Bulk Upload</a>
                    <a href="{{ route('organizations.events.schedules.index', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-100 transition-colors text-center">Sessions</a>
                    <a href="{{ route('organizations.events.schedules.create', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-green-50 text-green-700 text-sm font-semibold rounded-lg hover:bg-green-100 transition-colors text-center">Add Session</a>
                    <a href="{{ route('organizations.events.passes.create', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-orange-50 text-orange-700 text-sm font-semibold rounded-lg hover:bg-orange-100 transition-colors text-center">Add Pass Type</a>
                    <a href="{{ route('organizations.events.check-in.dashboard', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-teal-50 text-teal-700 text-sm font-semibold rounded-lg hover:bg-teal-100 transition-colors text-center">Check-in Dashboard</a>
                    <a href="{{ route('organizations.events.check-in.scanner', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-cyan-50 text-cyan-700 text-sm font-semibold rounded-lg hover:bg-cyan-100 transition-colors text-center">Scan Passes</a>
                    <a href="{{ route('organizations.events.reports.pre-event', [$organization, $event]) }}" class="block w-full px-4 py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-100 transition-colors text-center">View Reports</a>
                </div>
            </div>

            <!-- Organization Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Organization</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($organization->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-secondary">{{ $organization->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($organization->type) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="text-primary hover:text-blue-700 font-semibold">Back to Events</a>
        @if($event->isDraft())
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('organizations.events.duplicate', [$organization, $event]) }}" class="inline">@csrf
                <button type="submit" class="text-blue-600 hover:text-blue-700 font-semibold">Duplicate</button>
            </form>
            <form method="POST" action="{{ route('organizations.events.destroy', [$organization, $event]) }}" class="inline">@csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm('Delete this event?')">Delete</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
