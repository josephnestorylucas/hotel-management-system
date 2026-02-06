{{-- resources/views/shared/sidebar/supervisor.blade.php --}}
<nav class="flex-1 overflow-y-auto py-4 px-3">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-medium">Dashboard</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Room Management</div>
    </div>
    
    <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 {{ request()->routeIs('rooms.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-medium">All Rooms</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-gray-700 hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">Housekeeping</span>
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Soon</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-gray-700 hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="font-medium">Maintenance</span>
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Soon</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operations</div>
    </div>

    <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 {{ request()->routeIs('reservations.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="font-medium">Reservations</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-gray-700 hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="font-medium">Staff Schedule</span>
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Soon</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
    </div>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-gray-700 hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span class="font-medium">Occupancy Report</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-gray-700 hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">Revenue Report</span>
    </a>
</nav>
