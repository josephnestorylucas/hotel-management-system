{{-- resources/views/shared/sidebar/front-desk.blade.php --}}
<nav class="flex-1 overflow-y-auto py-4 px-3">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-semibold">Dashboard</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Quick Actions</div>
    </div>
    
    <a href="{{ route('reservations.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="font-semibold">New Reservation</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Reservations</div>
    </div>

    <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('reservations.index') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="font-medium">All Reservations</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Bookings</div>
    </div>

    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('bookings.index') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">All Bookings</span>
    </a>

    <a href="{{ route('bookings.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('bookings.create') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="font-medium">New Booking</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        <span class="font-medium">Today's Check-ins</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span class="font-medium">Today's Check-outs</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Laundry</div>
    </div>

    <a href="{{ route('laundry.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('laundry.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">Laundry Orders</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Guests</div>
    </div>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <span class="font-medium">Search Guests</span>
        <span class="ml-auto text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-semibold">Soon</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span class="font-medium">Current Guests</span>
        <span class="ml-auto text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-semibold">Soon</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">Help</div>
    </div>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 text-gray-700 hover:bg-blue-50 hover:text-primary transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">Help & Support</span>
    </a>
</nav>
