{{-- resources/views/reservations/index.blade.php --}}
@extends('layouts.app')

@section('title', __('reservations.title'))
@section('page-title', __('reservations.title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('reservations.reservations') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('reservations.manage_subtitle') }}</p>
        </div>
        <a href="{{ route('reservations.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('reservations.new_reservation') }}
        </a>
    </div>

    <!-- Filter Stats -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $reservations->where('status', 'pending')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('reservations.status.pending') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $reservations->where('status', 'confirmed')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('reservations.status.confirmed') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $reservations->where('status', 'converted')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('reservations.status.converted') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $reservations->where('status', 'cancelled')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('reservations.status.cancelled') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $reservations->where('status', 'no_show')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('reservations.status.no_show') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.reservation') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.guest') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.room') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.check_in') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.check_out') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.amount') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('reservations.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ substr($reservation->reservation_number, -4) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-semibold text-secondary">{{ $reservation->reservation_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $reservation->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reservation->guest)
                                <a href="{{ route('guests.show', $reservation->guest) }}" class="group">
                                    <div class="text-sm font-semibold text-secondary group-hover:text-primary transition-colors">{{ $reservation->guest_display_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $reservation->guest_display_phone }}</div>
                                </a>
                            @else
                                <div class="text-sm font-semibold text-secondary">{{ $reservation->guest_display_name }}</div>
                                <div class="text-xs text-gray-500">{{ $reservation->guest_display_phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reservation->room)
                                <div class="text-sm font-medium text-secondary">{{ $reservation->room->room_number }}</div>
                                <div class="text-xs text-primary">{{ $reservation->room->roomType->name }}</div>
                            @else
                                <span class="text-xs text-red-600 font-semibold">{{ __('reservations.labels.not_assigned') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $reservation->check_in_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $reservation->check_in_date->format('D') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $reservation->check_out_date->format('M d, Y') }}</div>
                            <div class="text-xs text-primary">{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }} {{ __('reservations.labels.nights') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-secondary">${{ number_format($reservation->estimated_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @include('components.reservation-status-badge', ['status' => $reservation->status])
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('reservations.edit', $reservation) }}" 
                                   class="text-primary hover:text-blue-700 font-semibold">
                                    {{ __('reservations.actions.edit') }}
                                </a>
                                
                                @if($reservation->status === 'pending')
                                    <form method="POST" action="{{ route('reservations.confirm', $reservation) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700 font-semibold">
                                            {{ __('reservations.actions.confirm') }}
                                        </button>
                                    </form>
                                @endif

                                @if($reservation->status === 'confirmed')
                                    <form method="POST" action="{{ route('reservations.check-in', $reservation) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-700 font-semibold">
                                            {{ __('reservations.actions.check_in') }}
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($reservation->status, ['pending', 'confirmed']))
                                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm('{{ __('reservations.messages.confirm_cancel') }}')">
                                            {{ __('reservations.actions.cancel') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-secondary">{{ __('reservations.messages.no_reservations') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ __('reservations.messages.no_reservations_subtitle') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('reservations.create') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('reservations.new_reservation') }}
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
    @if($reservations->hasPages())
    <div class="mt-6">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
@endsection