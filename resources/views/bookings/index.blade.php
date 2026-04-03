{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', __('bookings.title'))
@section('page-title', __('bookings.title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('bookings.title') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('bookings.subtitle') }}</p>
        </div>
        <a href="{{ route('bookings.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('bookings.new_booking') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.stats.total') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $stats['checked_in'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.stats.checked_in') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $stats['checked_out'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.stats.checked_out') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $stats['today_checkins'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.stats.today_checkins') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $stats['today_checkouts'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.stats.today_checkouts') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form method="GET" action="{{ route('bookings.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('bookings.filters.search_placeholder') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                    <option value="">{{ __('bookings.filters.all_statuses') }}</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>{{ __('bookings.status.checked_in') }}</option>
                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>{{ __('bookings.status.checked_out') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('bookings.status.cancelled') }}</option>
                </select>
            </div>
            <div>
                <select name="source" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                    <option value="">{{ __('bookings.filters.all_sources') }}</option>
                    <option value="online" {{ request('source') === 'online' ? 'selected' : '' }}>{{ __('bookings.sources.online') }}</option>
                    <option value="frontdesk" {{ request('source') === 'frontdesk' ? 'selected' : '' }}>{{ __('bookings.sources.frontdesk') }}</option>
                    <option value="phone" {{ request('source') === 'phone' ? 'selected' : '' }}>{{ __('bookings.sources.phone') }}</option>
                    <option value="walkin" {{ request('source') === 'walkin' ? 'selected' : '' }}>{{ __('bookings.sources.walkin') }}</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('bookings.filters.from_date') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                    {{ __('bookings.filter') }}
                </button>
                <a href="{{ route('bookings.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    {{ __('bookings.reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.booking') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.guest') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.room') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.check_in') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.check_out') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.amount') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.source') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ substr($booking->booking_number, -4) }}
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-sm font-semibold text-secondary hover:text-primary transition-colors">
                                        {{ $booking->booking_number }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->guest)
                                <a href="{{ route('guests.show', $booking->guest) }}" class="group">
                                    <div class="text-sm font-semibold text-secondary group-hover:text-primary transition-colors">{{ $booking->guest_display_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->guest_display_phone }}</div>
                                </a>
                            @else
                                <div class="text-sm font-semibold text-secondary">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->guest_phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->room)
                                <div class="text-sm font-medium text-secondary">{{ $booking->room->room_number }}</div>
                                <div class="text-xs text-primary">{{ $booking->room->roomType->name ?? '' }}</div>
                            @else
                                <span class="text-xs text-red-600 font-semibold">{{ __('bookings.table.not_assigned') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_in_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->check_in_date->format('D') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_out_date->format('M d, Y') }}</div>
                            <div class="text-xs text-primary">{{ $booking->nights }} {{ __('bookings.table.nights') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-secondary">@currency($booking->total_amount)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @include('components.booking-status-badge', ['status' => $booking->status])
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-primary hover:text-blue-700 font-semibold" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if($booking->canBeEdited())
                                    <a href="{{ route('bookings.edit', $booking) }}" class="text-primary hover:text-blue-700 font-semibold" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($booking->canBeCheckedOut())
                                    <form method="POST" action="{{ route('bookings.check-out', $booking) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-700 font-semibold text-xs" title="Check Out">
                                            Check Out
                                        </button>
                                    </form>
                                @endif

                                @if($booking->canBeCancelled())
                                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-xs" title="Cancel">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-secondary">No bookings found</h3>
                            <p class="mt-2 text-sm text-gray-500">Get started by creating your first booking.</p>
                            <div class="mt-6">
                                <a href="{{ route('bookings.create') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Booking
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="mt-6">
        {{ $bookings->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
