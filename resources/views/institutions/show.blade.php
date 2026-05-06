{{-- resources/views/institutions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Institution Details')
@section('page-title', 'Institutions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $institution->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Institution Profile</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('institutions.edit', $institution) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('conference-bookings.create') }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                New Booking
            </a>
        </div>
    </div>

    <!-- Institution Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Profile</h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Institution Name</span>
                    <span class="text-sm font-semibold text-secondary">{{ $institution->name }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Contact Person</span>
                    <span class="text-sm font-semibold text-secondary">{{ $institution->contact_person }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Phone</span>
                    <span class="text-sm text-secondary">{{ $institution->phone }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm text-secondary">{{ $institution->email ?? 'N/A' }}</span>
                </div>
                @if($institution->address)
                <div class="py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Address</span>
                    <p class="text-sm text-secondary mt-1">{{ $institution->address }}</p>
                </div>
                @endif
                @if($institution->notes)
                <div class="py-2">
                    <span class="text-sm text-gray-500">Notes</span>
                    <p class="text-sm text-secondary mt-1">{{ $institution->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Booking Summary</h3>

            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Total Bookings</span>
                    <span class="text-sm font-bold text-secondary">{{ $institution->conferenceBookings->count() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Pending</span>
                    <span class="text-sm font-semibold text-yellow-600">{{ $institution->conferenceBookings->where('status', 'pending')->count() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Confirmed</span>
                    <span class="text-sm font-semibold text-green-600">{{ $institution->conferenceBookings->where('status', 'confirmed')->count() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Completed</span>
                    <span class="text-sm font-semibold text-purple-600">{{ $institution->conferenceBookings->where('status', 'completed')->count() }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-500">Total Spent</span>
                    <span class="text-sm font-bold text-secondary">@currency($institution->conferenceBookings->sum('total_cost'))</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    @if($institution->conferenceBookings->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-secondary">Conference Hall Bookings</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Booking #</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Hall</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($institution->conferenceBookings as $booking)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-3 text-sm font-medium text-secondary">{{ $booking->booking_number }}</td>
                    <td class="px-6 py-3 text-sm text-secondary">{{ $booking->conferenceHall->name }}</td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="text-sm text-secondary">{{ $booking->booking_date->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm font-bold text-secondary">@currency($booking->total_cost)</td>
                    <td class="px-6 py-3">@include('components.conference-booking-status-badge', ['status' => $booking->status])</td>
                    <td class="px-6 py-3 text-sm">
                        <a href="{{ route('conference-bookings.show', $booking) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('institutions.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to Institutions
        </a>
    </div>
</div>
@endsection
