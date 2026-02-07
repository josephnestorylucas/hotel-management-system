{{-- resources/views/bookings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Details')
@section('page-title', 'Bookings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $booking->booking_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">Created {{ $booking->created_at->format('M d, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($booking->canBeEdited())
                <a href="{{ route('bookings.edit', $booking) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('bookings.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Status & Actions Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                @include('components.reservation-status-badge', ['status' => $booking->status])
                @php
                    $sourceClasses = match($booking->source) {
                        'online' => 'bg-blue-100 text-blue-800',
                        'frontdesk' => 'bg-purple-100 text-purple-800',
                        'phone' => 'bg-green-100 text-green-800',
                        'walkin' => 'bg-orange-100 text-orange-800',
                        default => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <span class="px-2 py-1 text-xs rounded {{ $sourceClasses }}">{{ ucfirst($booking->source) }}</span>
                @if($booking->reservation)
                    <span class="text-xs text-gray-500">Reservation: <strong>{{ $booking->reservation->reservation_number }}</strong></span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($booking->canBeConfirmed())
                    <form method="POST" action="{{ route('bookings.confirm', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all">
                            Confirm Booking
                        </button>
                    </form>
                @endif

                @if($booking->canBeCheckedIn())
                    <form method="POST" action="{{ route('bookings.check-in', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                            Check In
                        </button>
                    </form>
                @endif

                @if($booking->canBeCheckedOut())
                    <form method="POST" action="{{ route('bookings.check-out', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition-all">
                            Check Out
                        </button>
                    </form>
                @endif

                @if($booking->canBeCancelled())
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline" 
                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all">
                            Cancel Booking
                        </button>
                    </form>
                @endif

                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('bookings.no-show', $booking) }}" class="inline"
                          onsubmit="return confirm('Mark this booking as no-show?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                            No Show
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Guest Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Guest Information</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    @if($booking->guest && $booking->guest->hasPhoto())
                        <img src="{{ $booking->guest->photo_thumb_url }}" alt="{{ $booking->guest->full_name }}" class="w-12 h-12 rounded-xl object-cover">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($booking->guest_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-secondary">{{ $booking->guest_display_name }}</p>
                        @if($booking->guest)
                            <a href="{{ route('guests.show', $booking->guest) }}" class="text-xs text-primary hover:underline">View Guest Profile</a>
                        @else
                            <span class="text-xs text-gray-500">Public Booking (No Profile)</span>
                        @endif
                    </div>
                </div>
                <div class="pt-2 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-secondary">{{ $booking->guest_email }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium text-secondary">{{ $booking->guest_phone }}</span>
                    </div>
                    @if($booking->guest_country)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Country</span>
                        <span class="font-medium text-secondary">{{ $booking->guest_country }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stay Details -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Stay Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Check-in</span>
                    <span class="font-medium text-secondary">{{ $booking->check_in_date->format('D, M d, Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Check-out</span>
                    <span class="font-medium text-secondary">{{ $booking->check_out_date->format('D, M d, Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Duration</span>
                    <span class="font-medium text-secondary">{{ $booking->nights }} night(s)</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Guests</span>
                    <span class="font-medium text-secondary">{{ $booking->number_of_guests }}</span>
                </div>
            </div>
        </div>

        <!-- Room Details -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Room Details</h3>
            @if($booking->room)
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Room Number</span>
                        <span class="font-bold text-secondary">{{ $booking->room->room_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Room Type</span>
                        <span class="font-medium text-secondary">{{ $booking->room->roomType->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Floor</span>
                        <span class="font-medium text-secondary">{{ $booking->room->floor->number ?? 'N/A' }}</span>
                    </div>
                    @if($booking->room->floor->building ?? false)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Building</span>
                        <span class="font-medium text-secondary">{{ $booking->room->floor->building->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Rate/Night</span>
                        <span class="font-medium text-primary">${{ number_format($booking->room->roomType->base_rate ?? 0, 2) }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-red-600">No room assigned</p>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Payment Summary</h3>
            <div class="space-y-3">
                @php
                    $pricePerNight = $booking->room?->roomType?->base_rate ?? 0;
                @endphp
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">${{ number_format($pricePerNight, 2) }} × {{ $booking->nights }} nights</span>
                    <span class="font-medium text-secondary">${{ number_format($pricePerNight * $booking->nights, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm pt-2 border-t border-gray-100">
                    <span class="font-bold text-secondary">Total</span>
                    <span class="text-lg font-bold text-primary">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Requests & Additional Info -->
    @if($booking->special_requests || $booking->cancellation_reason || $booking->creator)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Additional Information</h3>
        <div class="space-y-4">
            @if($booking->special_requests)
            <div>
                <span class="text-sm font-semibold text-secondary">Special Requests</span>
                <p class="text-sm text-gray-600 mt-1">{{ $booking->special_requests }}</p>
            </div>
            @endif

            @if($booking->cancellation_reason)
            <div>
                <span class="text-sm font-semibold text-red-600">Cancellation Reason</span>
                <p class="text-sm text-gray-600 mt-1">{{ $booking->cancellation_reason }}</p>
            </div>
            @endif

            @if($booking->creator)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Created By</span>
                <span class="font-medium text-secondary">{{ $booking->creator->name }}</span>
            </div>
            @endif

            @if($booking->confirmed_at)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Confirmed At</span>
                <span class="font-medium text-secondary">{{ $booking->confirmed_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif

            @if($booking->cancelled_at)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Cancelled At</span>
                <span class="font-medium text-red-600">{{ $booking->cancelled_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
