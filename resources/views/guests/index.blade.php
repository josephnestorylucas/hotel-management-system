{{-- resources/views/guests/index.blade.php --}}
@extends('layouts.app')

@section('title', __('guests.title'))
@section('page-title', __('guests.title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('guests.title') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('guests.subtitle') }}</p>
        </div>
        @if(auth()->user()->isFrontDesk())
        <a href="{{ route('guests.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('guests.new_guest') }}
        </a>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
        <form method="GET" action="{{ route('guests.index') }}" class="flex gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="{{ __('guests.filters.search_placeholder') }}"
                    class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
            </div>
            <button type="submit" 
                    class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                {{ __('guests.actions.search') }}
            </button>
            @if(request('search'))
            <a href="{{ route('guests.index') }}" 
               class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                {{ __('guests.actions.clear') }}
            </a>
            @endif
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $guests->total() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('guests.stats.total_guests') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guests Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.guest') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.contact') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.id_number') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.nationality') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.reservations') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('guests.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($guests as $guest)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg overflow-hidden bg-gradient-to-br from-primary to-blue-600">
                                    {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-semibold text-secondary">{{ $guest->full_name }}</div>
                                    @if($guest->date_of_birth)
                                    <div class="text-xs text-gray-500">{{ __('guests.fields.dob_short') }}: {{ $guest->date_of_birth->format('M d, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $guest->phone_number }}</div>
                            <div class="text-xs text-gray-500">{{ $guest->email ?? __('guests.messages.no_email') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-secondary">{{ $guest->id_number ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-secondary">{{ $guest->nationality ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gradient-to-br from-blue-50 to-blue-100 text-primary">
                                {{ $guest->reservations_count }} {{ __('guests.table.reservations') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('guests.show', $guest) }}" 
                                   class="text-gray-600 hover:text-gray-800" 
                                   aria-label="{{ __('guests.actions.view') }}" 
                                   title="{{ __('guests.actions.view') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>
                                <a href="{{ route('guests.edit', $guest) }}" 
                                   class="text-primary hover:text-blue-700 font-semibold">
                                    {{ __('guests.actions.edit') }}
                                </a>
                                <a href="{{ route('reservations.create', ['guest_id' => $guest->id]) }}" 
                                   class="text-green-600 hover:text-green-700 font-semibold">
                                    {{ __('guests.actions.new_reservation') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">{{ __('guests.messages.no_guests') }}</p>
                                <p class="text-gray-400 text-sm mt-1">{{ __('guests.messages.no_guests_subtitle') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($guests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $guests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
