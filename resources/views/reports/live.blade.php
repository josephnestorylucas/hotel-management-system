@extends('layouts.app')

@section('title', 'Live Dashboard')
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Live Event Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
    </div>

    <!-- Live Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-secondary">{{ $totalRegistered }}</div>
            <div class="text-sm text-gray-500">Registered</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-green-600">{{ $checkedIn }}</div>
            <div class="text-sm text-gray-500">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-blue-600">{{ $checkInRate }}%</div>
            <div class="text-sm text-gray-500">Attendance Rate</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-4xl font-extrabold text-yellow-600">{{ $notCheckedIn }}</div>
            <div class="text-sm text-gray-500">Not Arrived</div>
        </div>
    </div>

    <!-- Progress -->
    @if($totalRegistered > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-secondary">Overall Check-in Progress</span>
            <span class="text-sm font-bold text-primary">{{ $checkInRate }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-6 rounded-full transition-all flex items-center justify-end pr-2" style="width: {{ $checkInRate }}%">
                @if($checkInRate > 10)<span class="text-xs font-bold text-white">{{ $checkedIn }}/{{ $totalRegistered }}</span>@endif
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Check-in Timeline -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Check-in Timeline (by Hour)</h3>
            @if($checkInTimeline->count() > 0)
            <div class="flex items-end gap-1 h-32">
                @php $max = $checkInTimeline->max('count'); @endphp
                @foreach($checkInTimeline as $point)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: {{ $max > 0 ? ($point->count / $max) * 100 : 0 }}%"></div>
                    <div class="text-xs text-gray-500 mt-1">{{ $point->hour }}:00</div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500 text-center py-4">No check-in data yet.</p>
            @endif
        </div>

        <!-- Session Attendance -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Session Attendance</h3>
            <div class="space-y-3">
                @forelse($sessionAttendance as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-secondary">{{ $item['session']->name }}</div>
                        <div class="text-xs text-gray-500">{{ $item['session']->start_datetime->format('h:i A') }}</div>
                    </div>
                    <div class="text-lg font-bold text-secondary">{{ $item['check_ins'] }}</div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No sessions.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Latest Check-ins</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($recentCheckIns as $ci)
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($ci->attendance->first_name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-medium text-secondary">{{ $ci->attendance->full_name ?? 'Unknown' }}</div>
                    <div class="text-xs text-gray-500">{{ $ci->created_at->format('h:i A') }}</div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4 col-span-3">No check-ins yet.</p>
            @endforelse
        </div>
    </div>
</div>

<script>setTimeout(function() { location.reload(); }, 30000);</script>
@endsection
