{{-- resources/views/manager/reports/accommodation.blade.php --}}
@extends('layouts.app')

@section('title', 'Accommodation Report')
@section('page-title', 'Accommodation Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Accommodation Report</h2>
            <p class="mt-1 text-sm text-gray-500">
                Guest stays, check-ins &amp; check-outs, and room occupancy
                &mdash; {{ $dateFrom->format('M d, Y') }} to {{ $dateTo->format('M d, Y') }}
            </p>
        </div>
        <form method="GET" action="{{ route('manager.reports.accommodation') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom->toDateString() }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $dateTo->toDateString() }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Room Number</label>
                <input type="text" name="room_number" value="{{ $roomNumber }}" placeholder="e.g. 101"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all w-32">
            </div>
            <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                Filter
            </button>
            <a href="{{ route('manager.reports.accommodation') }}"
               class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                Reset
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Guests -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $totalGuests }}</div>
                    <div class="text-xs text-gray-500 font-medium">Total Guests</div>
                </div>
            </div>
        </div>

        <!-- Today's Check-ins -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $todayCheckins }}</div>
                    <div class="text-xs text-gray-500 font-medium">Check-ins Today</div>
                </div>
            </div>
        </div>

        <!-- Today's Check-outs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $todayCheckouts }}</div>
                    <div class="text-xs text-gray-500 font-medium">Check-outs Today</div>
                </div>
            </div>
        </div>

        <!-- Occupancy % -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $occupancyPercent }}%</div>
                    <div class="text-xs text-gray-500 font-medium">Occupancy ({{ $occupiedToday }}/{{ $totalRooms }})</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Check-in / Check-out Breakdown -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider">Daily Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-primary uppercase tracking-wider">Check-ins</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-primary uppercase tracking-wider">Check-outs</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($dailyBreakdown as $day)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-secondary">{{ $day['date']->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $day['date']->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold
                                {{ $day['checkins'] > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $day['checkins'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold
                                {{ $day['checkouts'] > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $day['checkouts'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500">
                            No data for the selected date range.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Room Occupancy Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-800 to-gray-900">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Room Occupancy Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Room Number</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Room Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Guest Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider"># Guests</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ $booking->room?->room_number ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded font-medium bg-purple-100 text-purple-700">
                                {{ $booking->room?->roomType?->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-secondary">{{ $booking->guest_display_name }}</div>
                            @if($booking->guest_phone)
                                <div class="text-xs text-gray-500">{{ $booking->guest_phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_in_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->check_in_date->format('D') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_out_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->check_out_date->format('D') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-700 text-sm font-bold">
                                {{ $booking->number_of_guests }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-secondary">No accommodation data found</h3>
                            <p class="mt-2 text-sm text-gray-500">There are no bookings matching the selected filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                <!-- Summary Footer -->
                @if($bookings->count() > 0)
                <tfoot class="bg-gradient-to-r from-gray-800 to-gray-900">
                    <tr>
                        <td colspan="3" class="px-6 py-4">
                            <span class="text-sm font-bold text-white">
                                Total: {{ $totalBookings }} booking{{ $totalBookings !== 1 ? 's' : '' }} &middot; {{ $totalGuests }} guest{{ $totalGuests !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td colspan="2" class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-white">Occupancy:</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-white/20 text-white text-sm font-bold">
                                {{ $occupancyPercent }}%
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
