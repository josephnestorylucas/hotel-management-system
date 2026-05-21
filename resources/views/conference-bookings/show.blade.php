{{-- resources/views/conference-bookings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Details')
@section('page-title', 'Conference Hall Bookings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Booking Details</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $conferenceBooking->booking_number }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($conferenceBooking->status === 'pending')
            <form method="POST" action="{{ route('conference-bookings.confirm', $conferenceBooking) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    Confirm Booking
                </button>
            </form>
            @endif
            @if(in_array($conferenceBooking->status, ['pending', 'confirmed']))
            <form method="POST" action="{{ route('conference-bookings.cancel', $conferenceBooking) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors" onclick="return confirm('Are you sure?')">
                    Cancel Booking
                </button>
            </form>
            @endif
            <a href="{{ route('conference-bookings.edit', $conferenceBooking) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Edit
            </a>
        </div>
    </div>

    <!-- Booking Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Booking Information</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Booking Number</span>
                    <span class="text-sm font-semibold text-secondary">{{ $conferenceBooking->booking_number }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Status</span>
                    <div>@include('components.conference-booking-status-badge', ['status' => $conferenceBooking->status])</div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Created By</span>
                    <span class="text-sm font-semibold text-secondary">{{ $conferenceBooking->creator->name }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Created At</span>
                    <span class="text-sm text-secondary">{{ $conferenceBooking->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Institution Information</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Institution</span>
                    <span class="text-sm font-semibold text-secondary">{{ $conferenceBooking->institution->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Contact Person</span>
                    <span class="text-sm text-secondary">{{ $conferenceBooking->institution->contact_person ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Phone</span>
                    <span class="text-sm text-secondary">{{ $conferenceBooking->institution->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm text-secondary">{{ $conferenceBooking->institution->email ?? 'N/A' }}</span>
                </div>
                @if($conferenceBooking->institution)
                <div class="pt-1">
                    <a href="{{ route('institutions.show', $conferenceBooking->institution) }}" class="text-xs text-primary hover:text-blue-700 font-semibold">
                        View Institution Profile →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hall & Schedule Details -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Hall & Schedule Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                <div class="text-sm text-gray-600 mb-1">Conference Hall</div>
                <div class="text-lg font-bold text-secondary">{{ $conferenceBooking->conferenceHall->name }}</div>
                @if($conferenceBooking->conferenceHall->building)
                <div class="text-xs text-primary mt-1">{{ $conferenceBooking->conferenceHall->building->name }}</div>
                @endif
                <div class="text-xs text-gray-500 mt-1">Capacity: {{ $conferenceBooking->conferenceHall->capacity }} people</div>
            </div>

            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                <div class="text-sm text-gray-600 mb-1">Date & Time</div>
                <div class="text-lg font-bold text-secondary">{{ $conferenceBooking->booking_date->format('M d, Y') }}</div>
                <div class="text-sm text-primary mt-1">
                    {{ \Carbon\Carbon::parse($conferenceBooking->start_time)->format('h:i A') }} - 
                    {{ \Carbon\Carbon::parse($conferenceBooking->end_time)->format('h:i A') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Duration: {{ $conferenceBooking->duration_in_hours }} hours</div>
            </div>

            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                <div class="text-sm text-gray-600 mb-1">Total Cost</div>
                <div class="text-2xl font-bold text-secondary">@currency($conferenceBooking->total_cost)</div>
                <div class="text-xs text-gray-500 mt-1">
                    Rate: {{ $conferenceBooking->conferenceHall->formatted_rate }}/hr
                </div>
            </div>
        </div>

        @if($conferenceBooking->conferenceHall->amenities)
        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Available Amenities</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($conferenceBooking->conferenceHall->amenities as $amenity)
                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">{{ $amenity }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Conference Details -->
    @if($conferenceBooking->conference)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-secondary">Conference Details</h3>
            <a href="{{ route('conferences.show', $conferenceBooking->conference) }}" class="text-primary hover:text-blue-700 font-semibold text-sm">
                View Conference →
            </a>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Conference Title</span>
                <span class="text-sm font-semibold text-secondary">{{ $conferenceBooking->conference->title }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Status</span>
                <div>@include('components.conference-status-badge', ['status' => $conferenceBooking->conference->status])</div>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Participants</span>
                <span class="text-sm font-semibold text-secondary">{{ $conferenceBooking->conference->participants_count }} registered</span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-yellow-800 mb-1">No Conference Created</h4>
                <p class="text-sm text-yellow-700">This booking doesn't have a conference yet. Create one to add event details and participants.</p>
                <a href="{{ route('conferences.create') }}?booking_id={{ $conferenceBooking->id }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-yellow-600 text-white text-sm font-semibold rounded-lg hover:bg-yellow-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Conference
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('conference-bookings.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to Bookings
        </a>
    </div>
</div>
@endsection