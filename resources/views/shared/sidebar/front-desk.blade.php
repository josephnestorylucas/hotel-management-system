{{-- resources/views/shared/sidebar/front-desk.blade.php --}}
<nav class="flex-1 overflow-y-auto py-4 px-3">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-semibold">{{ __('general.nav.dashboard') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.quick_actions') }}</div>
    </div>
    
    <a href="{{ route('reservations.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="font-semibold">{{ __('bookings.new_reservation') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.reservations') }}</div>
    </div>

    <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('reservations.index') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="font-medium">{{ __('bookings.all_reservations') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.bookings') }}</div>
    </div>

    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('bookings.index') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">{{ __('bookings.all_bookings') }}</span>
    </a>

    <a href="{{ route('bookings.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('bookings.create') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="font-medium">{{ __('bookings.new_booking') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Drinks</div>
    </div>

    <a href="{{ route('reception.drinks.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('reception.drinks.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span class="font-medium">Request Drinks</span>
    </a>

    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        <span class="font-medium">{{ __('bookings.todays_checkins') }}</span>
    </a>

    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span class="font-medium">{{ __('bookings.todays_checkouts') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Maintenance</div>
    </div>

    <a href="{{ route('cleaning.maintenance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('cleaning.maintenance') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="font-medium">Room Maintenance</span>
        @php $oooCount = \App\Models\Room::where('status', 'out_of_order')->count(); @endphp
        @if($oooCount > 0)
            <span class="ml-auto bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full">{{ $oooCount }}</span>
        @endif
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.laundry') }}</div>
    </div>

    <a href="{{ route('laundry.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('laundry.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">{{ __('laundry.laundry_orders') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.conference') }}</div>
    </div>

    <a href="{{ route('organizations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('organizations.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <span class="font-medium">Organizations & Events</span>
    </a>

    <a href="{{ route('conference-halls.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('conference-halls.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <span class="font-medium">{{ __('general.nav.conference') }}</span>
    </a>

    <a href="{{ route('conference-bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('conference-bookings.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="font-medium">{{ __('bookings.hall_bookings') }}</span>
    </a>

    <a href="{{ route('conferences.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('conferences.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="font-medium">{{ __('bookings.conferences') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.guests') }}</div>
    </div>

    <a href="{{ route('guests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <span class="font-medium">{{ __('guests.search_guests') }}</span>
    </a>

    <a href="{{ route('bookings.current-guests') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('bookings.current-guests') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span class="font-medium">{{ __('bookings.current_guests') }}</span>
    </a>
</nav>
