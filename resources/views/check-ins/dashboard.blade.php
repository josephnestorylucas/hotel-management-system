@extends('layouts.app')

@section('title', 'Check-in Dashboard')
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Check-in Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.check-in.scanner', [$organization, $event]) }}" class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700">Open Scanner</a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    <!-- Live Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-secondary">{{ $stats['total_registered'] }}</div>
            <div class="text-sm text-gray-500 font-medium">Registered</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-green-600">{{ $stats['checked_in'] }}</div>
            <div class="text-sm text-gray-500 font-medium">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-yellow-600">{{ $stats['not_checked_in'] }}</div>
            <div class="text-sm text-gray-500 font-medium">Not Arrived</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-red-600">{{ $stats['no_shows'] }}</div>
            <div class="text-sm text-gray-500 font-medium">No Shows</div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($stats['total_registered'] > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-secondary">Check-in Progress</span>
            <span class="text-sm font-bold text-primary">{{ round(($stats['checked_in'] / $stats['total_registered']) * 100, 1) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-4 rounded-full transition-all" style="width: {{ ($stats['checked_in'] / $stats['total_registered']) * 100 }}%"></div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Check-ins -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Recent Check-ins</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentCheckIns as $ci)
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-secondary">{{ $ci->attendance->full_name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $ci->check_in_method)) }}</div>
                    </div>
                    <div class="text-sm text-gray-600">{{ $ci->created_at->format('h:i A') }}</div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No check-ins yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Session Breakdown -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Session Attendance</h3>
            <div class="space-y-3">
                @forelse($scheduleCheckIns as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-secondary">{{ $item['schedule']->name }}</div>
                        <div class="text-xs text-gray-500">{{ $item['schedule']->start_datetime->format('M d, h:i A') }}</div>
                    </div>
                    <div class="text-lg font-bold text-secondary">{{ $item['count'] }}</div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No sessions defined.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh every 30 seconds
setTimeout(function() { location.reload(); }, 30000);
</script>
@endsection
