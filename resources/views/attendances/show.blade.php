@extends('layouts.app')

@section('title', $attendance->full_name)
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $attendance->full_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }} &middot; {{ $attendance->ticket_number }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($attendance->registration_status === 'confirmed')
            <form method="POST" action="{{ route('organizations.events.attendances.mark-no-show', [$organization, $event, $attendance]) }}" class="inline">@csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">Mark No-Show</button>
            </form>
            @endif
            <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Attendees</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Attendee Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $attendance->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Company</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->company ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->job_title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Type</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->eventPass?->tier_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                        <p class="mt-1">
                            @if($attendance->registration_status === 'confirmed') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Confirmed</span>
                            @elseif($attendance->registration_status === 'no_show') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">No Show</span>
                            @else <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($attendance->registration_status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Check-ins</label>
                        <p class="text-2xl font-extrabold text-secondary mt-1">{{ $attendance->total_check_ins }}</p>
                    </div>
                </div>
            </div>

            <!-- Check-in History -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Check-in History</h3>
                @if($attendance->checkIns->count() > 0)
                <div class="space-y-3">
                    @foreach($attendance->checkIns as $checkIn)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-secondary">{{ ucfirst($checkIn->check_in_type) }} &middot; {{ ucfirst(str_replace('_', ' ', $checkIn->check_in_method)) }}</div>
                            @if($checkIn->eventSchedule)
                            <div class="text-xs text-gray-500">{{ $checkIn->eventSchedule->name }}</div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">{{ $checkIn->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">No check-ins yet.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Pass Details</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Number</label>
                        <p class="text-sm font-mono font-semibold text-secondary mt-1">{{ $attendance->ticket_number }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Manual Code</label>
                        <p class="text-lg font-mono font-bold text-primary mt-1">{{ $attendance->manual_code }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">QR Token</label>
                        <p class="text-xs font-mono text-gray-500 mt-1 break-all">{{ Str::limit($attendance->qr_token, 32) }}...</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->registration_date->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($attendance->first_check_in_at)
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">First Check-in</label>
                        <p class="text-sm text-secondary mt-1">{{ $attendance->first_check_in_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($attendance->guest)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Linked Guest</h3>
                <p class="text-sm font-semibold text-secondary">{{ $attendance->guest->full_name }}</p>
                <p class="text-xs text-gray-500">{{ $attendance->guest->email }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
