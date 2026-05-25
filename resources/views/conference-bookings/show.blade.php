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



    <!-- Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('conference-bookings.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to Bookings
        </a>
    </div>
</div>
@endsection