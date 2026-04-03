{{-- resources/views/dashboards/front-desk.blade.php --}}
@extends('layouts.app')

@section('title', __('dashboard.front_desk_title') . ' - MRK Hotel')
@section('page-title', __('dashboard.front_desk_title'))

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h2>
            <p class="text-blue-100">{{ __('dashboard.front_desk_welcome') }}</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-blue-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.available_rooms') }}</p>
                <p class="text-4xl font-extrabold text-green-600 mt-1">{{ $stats['available_rooms'] }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">{{ __('dashboard.stats.ready_for_booking') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.today_checkins') }}</p>
                <p class="text-4xl font-extrabold text-primary mt-1">{{ $stats['today_checkins'] }}</p>
                <p class="text-xs text-primary font-medium mt-1">{{ __('dashboard.stats.expected_arrivals') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.today_checkouts') }}</p>
                <p class="text-4xl font-extrabold text-red-600 mt-1">{{ $stats['today_checkouts'] }}</p>
                <p class="text-xs text-red-600 font-medium mt-1">{{ __('dashboard.stats.expected_departures') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.quick_actions') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('reservations.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-primary/5 to-blue-100 rounded-xl hover:shadow-lg transition border border-primary/20 group">
            <div class="w-14 h-14 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.new_reservation') }}</span>
        </a>
        <a href="{{ route('reservations.index') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-lg transition border border-green-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.view_reservations') }}</span>
        </a>
        <div class="flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ $stats['pending_reservations'] }} {{ __('dashboard.pending') }}</span>
        </div>
        <div class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ $stats['occupied_rooms'] }} {{ __('dashboard.stats.occupied_rooms') }}</span>
        </div>
    </div>
</div>

<!-- Available Rooms by Type -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.available_by_type') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @forelse($availableRoomsByType as $item)
        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
            <div class="text-3xl font-extrabold text-green-600">{{ $item->count }}</div>
            <div class="text-sm text-gray-600 font-medium mt-1">{{ $item->roomType->name }}</div>
            <div class="text-xs text-primary font-medium mt-1"><x-money :amount="$item->roomType->base_rate" />/{{ __('general.per_night') }}</div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-6">{{ __('dashboard.no_rooms') }}</div>
        @endforelse
    </div>
</div>

<!-- Today's Arrivals -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.arrivals_today') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-green-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.reservation_number') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.guest_name') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.phone') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.room') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-green-600 uppercase tracking-wider">{{ __('dashboard.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($todayActivity as $reservation)
                <tr class="hover:bg-green-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->room?->room_number ?? __('dashboard.not_assigned') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('reservations.edit', $reservation) }}" class="text-primary hover:text-blue-700 font-semibold mr-3">{{ __('dashboard.actions.view') }}</a>
                        @if($reservation->status === 'confirmed')
                            <form method="POST" action="{{ route('reservations.check-in', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-700 font-semibold">{{ __('dashboard.actions.check_in') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">{{ __('dashboard.no_arrivals') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Today's Departures (Bookings) -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.departures_today') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-red-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('dashboard.table.booking_number') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('dashboard.table.guest_name') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('dashboard.table.room') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('dashboard.table.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('dashboard.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($todayDepartures as $booking)
                <tr class="hover:bg-red-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $booking->booking_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $booking->guest->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $booking->room?->room_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.booking-status-badge', ['status' => $booking->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($booking->status === 'checked_in')
                            <form method="POST" action="{{ route('bookings.check-out', $booking) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">{{ __('dashboard.actions.check_out') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('dashboard.no_departures') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upcoming Arrivals -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.upcoming_arrivals') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.date') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.reservation_number') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.guest') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.phone') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.room') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.guests') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('dashboard.table.status') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($upcomingArrivals as $reservation)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-secondary">{{ $reservation->check_in_date->format('M d') }}</div>
                        <div class="text-xs text-primary">{{ $reservation->check_in_date->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($reservation->room)
                            <span class="font-semibold text-secondary">{{ $reservation->room->room_number }}</span>
                        @else
                            <span class="text-red-600 font-medium">{{ __('dashboard.not_assigned') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->number_of_guests }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __('dashboard.no_upcoming') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- My Recent Reservations -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.my_recent_reservations') }}</h3>
    <div class="space-y-3">
        @forelse($myRecentReservations as $reservation)
        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl hover:shadow-md transition border border-gray-100">
            <div class="flex-1">
                <p class="font-semibold text-secondary">{{ $reservation->guest_name }}</p>
                <p class="text-sm text-primary font-medium">{{ $reservation->reservation_number }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $reservation->check_in_date->format('M d') }} - {{ $reservation->check_out_date->format('M d, Y') }}</p>
            </div>
            <div class="text-right ml-4">
                @include('components.reservation-status-badge', ['status' => $reservation->status])
                <p class="text-xs text-gray-500 mt-1">{{ $reservation->created_at->diffForHumans() }}</p>
            </div>
            <div class="ml-4">
                <a href="{{ route('reservations.edit', $reservation) }}" class="text-primary hover:text-blue-700 font-semibold text-sm flex items-center gap-1">
                    {{ __('dashboard.actions.view') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">{{ __('dashboard.no_recent_reservations') }}</p>
        @endforelse
    </div>
</div>
@endsection
