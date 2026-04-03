{{-- resources/views/dashboards/cashier.blade.php --}}
@extends('layouts.app')

@section('title', __('dashboard.titles.cashier'))
@section('page-title', __('dashboard.titles.cashier'))

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('dashboard.welcome.greeting', ['name' => auth()->user()->name]) }}</h2>
            <p class="text-green-100">{{ __('dashboard.welcome.cashier_message') }}</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-green-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.cashier.today_revenue') }}</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">@currency($stats['today_revenue'], 'TZS')</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.cashier.active_bookings') }}</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['active_bookings'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.cashier.today_checkouts') }}</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['today_checkouts'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.cashier.pending_payments') }}</p>
                <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['pending_payments'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Today's Departures -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-4">Today's Departures</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Room</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Guest</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Check-out</th>
                    <th class="px-4 py-3 text-right text-gray-600 font-semibold">Amount</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-semibold">Payment</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($todayDepartures as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $booking->room->room_number ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $booking->guest_name ?? $booking->guest_display_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $booking->check_out_date->format('h:i A') }}</td>
                    <td class="px-4 py-3 text-right font-medium">@currency($booking->total_amount, 'TZS')</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($booking->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('bookings.show', $booking) }}" class="text-primary text-xs hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No departures today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
