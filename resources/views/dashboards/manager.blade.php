{{-- resources/views/dashboards/manager.blade.php --}}
{{-- Manager Dashboard - Full business operations control --}}
@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Manager Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-indigo-100">Here's your business operations overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-indigo-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Bookings</p>
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['active_bookings'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Currently checked-in</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Reservations</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['pending_reservations'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Awaiting confirmation</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Today's Check-ins</p>
                <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['today_checkins'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Expected arrivals</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Today's Check-outs</p>
                <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['today_checkouts'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Expected departures</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Laundry & Conference Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Laundry Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-extrabold text-secondary flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Laundry Orders
            </h3>
            <a href="{{ route('laundry.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">View All</a>
        </div>
        @php
            $laundryStats = [
                'pending' => \App\Models\LaundryOrder::where('status', 'pending')->count(),
                'in_progress' => \App\Models\LaundryOrder::where('status', 'in_progress')->count(),
                'completed' => \App\Models\LaundryOrder::where('status', 'completed')->count(),
                'delivered' => \App\Models\LaundryOrder::where('status', 'delivered')->count(),
            ];
        @endphp
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                <p class="text-2xl font-extrabold text-yellow-600">{{ $laundryStats['pending'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Pending</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-xl border border-blue-200">
                <p class="text-2xl font-extrabold text-blue-600">{{ $laundryStats['in_progress'] }}</p>
                <p class="text-xs text-gray-500 font-medium">In Progress</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-xl border border-green-200">
                <p class="text-2xl font-extrabold text-green-600">{{ $laundryStats['completed'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Completed</p>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                <p class="text-2xl font-extrabold text-indigo-600">{{ $laundryStats['delivered'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Delivered</p>
            </div>
        </div>
    </div>

    <!-- Conference Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-extrabold text-secondary flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Conference Bookings
            </h3>
            <a href="{{ route('conference-bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">View All</a>
        </div>
        @php
            $conferenceStats = [
                'pending' => \App\Models\ConferenceBooking::where('status', 'pending')->count(),
                'confirmed' => \App\Models\ConferenceBooking::where('status', 'confirmed')->count(),
                'today' => \App\Models\ConferenceBooking::whereDate('booking_date', today())->count(),
                'upcoming' => \App\Models\ConferenceBooking::where('booking_date', '>', today())->where('status', 'confirmed')->count(),
            ];
        @endphp
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                <p class="text-2xl font-extrabold text-yellow-600">{{ $conferenceStats['pending'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Pending</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-xl border border-green-200">
                <p class="text-2xl font-extrabold text-green-600">{{ $conferenceStats['confirmed'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Confirmed</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-xl border border-blue-200">
                <p class="text-2xl font-extrabold text-blue-600">{{ $conferenceStats['today'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Today</p>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                <p class="text-2xl font-extrabold text-indigo-600">{{ $conferenceStats['upcoming'] }}</p>
                <p class="text-xs text-gray-500 font-medium">Upcoming</p>
            </div>
        </div>
    </div>
</div>

<!-- Room Status & Revenue -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Occupancy Rate -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Occupancy Rate</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-40 h-40">
                <svg class="transform -rotate-90 w-40 h-40">
                    <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="10" fill="none" />
                    <circle cx="80" cy="80" r="70" stroke="#4f46e5" stroke-width="10" fill="none"
                            stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - ($stats['occupancy_rate'] ?? 0) / 100) }}"
                            stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-extrabold text-secondary">{{ $stats['occupancy_rate'] ?? 0 }}%</span>
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-2 text-sm">
            <div class="text-center p-3 bg-green-50 rounded-xl">
                <p class="font-bold text-green-700">{{ $stats['available_rooms'] ?? 0 }}</p>
                <p class="text-green-600 text-xs">Available</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-xl">
                <p class="font-bold text-red-700">{{ $stats['occupied_rooms'] ?? 0 }}</p>
                <p class="text-red-600 text-xs">Occupied</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <p class="font-bold text-blue-700">{{ $stats['reserved_rooms'] ?? 0 }}</p>
                <p class="text-blue-600 text-xs">Reserved</p>
            </div>
            <div class="text-center p-3 bg-yellow-50 rounded-xl">
                <p class="font-bold text-yellow-700">{{ $stats['dirty_rooms'] ?? 0 }}</p>
                <p class="text-yellow-600 text-xs">Dirty</p>
            </div>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:col-span-2">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Revenue Overview</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Today</p>
                <p class="text-2xl font-extrabold text-green-700 mt-2">{{ number_format($stats['today_revenue'] ?? 0, 0) }}</p>
                <p class="text-xs text-green-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">This Week</p>
                <p class="text-2xl font-extrabold text-blue-700 mt-2">{{ number_format($stats['week_revenue'] ?? 0, 0) }}</p>
                <p class="text-xs text-blue-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">This Month</p>
                <p class="text-2xl font-extrabold text-purple-700 mt-2">{{ number_format($stats['month_revenue'] ?? 0, 0) }}</p>
                <p class="text-xs text-purple-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-200">
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-extrabold text-indigo-700 mt-2">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                <p class="text-xs text-indigo-600 mt-1">All Time</p>
            </div>
        </div>
    </div>
</div>

<!-- Building Stats & Staff Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Staff by Role -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Staff Overview</h3>
        <div class="space-y-3">
            @foreach($staffByRole as $roleName => $count)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-secondary">{{ $roleName }}</span>
                </div>
                <span class="bg-indigo-100 text-indigo-700 text-sm font-bold px-3 py-1 rounded-full">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Building Stats -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Building Overview</h3>
        <div class="space-y-3">
            @foreach($buildingStats as $building)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-secondary">{{ $building->name }}</span>
                        <p class="text-xs text-gray-500">{{ $building->floors_count }} floors</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-700 text-sm font-bold px-3 py-1 rounded-full">{{ $building->rooms_count }} rooms</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Room Status Distribution -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Room Status Distribution</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $statusColors = [
                'available' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'label' => 'Available'],
                'occupied' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'label' => 'Occupied'],
                'reserved' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'label' => 'Reserved'],
                'dirty' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'label' => 'Dirty'],
                'out_of_order' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'label' => 'Out of Order'],
            ];
        @endphp
        @foreach($statusColors as $status => $colors)
        <div class="{{ $colors['bg'] }} {{ $colors['border'] }} border rounded-xl p-4 text-center">
            <p class="text-3xl font-extrabold {{ $colors['text'] }}">{{ $roomStatusCounts[$status] ?? 0 }}</p>
            <p class="text-xs font-medium {{ $colors['text'] }} mt-1">{{ $colors['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Recent Reservations -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary">Recent Reservations</h3>
        <a href="{{ route('reservations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">Guest</th>
                    <th class="pb-3 pr-4">Room</th>
                    <th class="pb-3 pr-4">Check-in</th>
                    <th class="pb-3 pr-4">Check-out</th>
                    <th class="pb-3 pr-4">Amount</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentReservations as $reservation)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-4">
                        <span class="font-medium text-secondary">{{ $reservation->guest_name ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->room->room_number ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->check_in_date ? \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->check_out_date ? \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-semibold text-secondary">{{ number_format($reservation->estimated_amount ?? 0) }}</span>
                    </td>
                    <td class="py-3">
                        @php
                            $statusBadge = match($reservation->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-green-100 text-green-800',
                                'converted' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'no_show' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">No recent reservations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('reservations.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl hover:shadow-lg transition border border-indigo-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">New Reservation</span>
        </a>
        <a href="{{ route('bookings.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-lg transition border border-green-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">New Booking</span>
        </a>
        <a href="{{ route('laundry.orders.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:shadow-lg transition border border-purple-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">New Laundry Order</span>
        </a>
        <a href="{{ route('conference-bookings.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:shadow-lg transition border border-blue-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">New Conference</span>
        </a>
    </div>
</div>
@endsection
