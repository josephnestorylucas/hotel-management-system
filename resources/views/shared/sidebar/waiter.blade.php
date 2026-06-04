{{-- resources/views/shared/sidebar/waiter.blade.php --}}
<nav class="flex-1 overflow-y-auto py-4 px-3">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-semibold">{{ __('general.nav.dashboard') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.restaurant') }}</div>
    </div>

    <a href="{{ route('restaurant.orders.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="font-semibold">{{ __('general.nav.new_order') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.orders') }}</div>
    </div>

    <a href="{{ route('restaurant.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('restaurant.orders.index') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="font-medium">{{ __('general.nav.orders') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">{{ __('general.nav.tables') }}</div>
    </div>

    <a href="{{ route('restaurant.tables.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('restaurant.tables.*') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        <span class="font-medium">{{ __('general.nav.tables') }}</span>
    </a>

    <div class="mt-6 mb-2">
        <div class="px-3 text-xs font-bold text-primary uppercase tracking-wider">POS</div>
    </div>

    <a href="{{ route('restaurant.pos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('restaurant.pos') ? 'bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-50 hover:text-primary' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <span class="font-medium">Regular POS</span>
    </a>

    <a href="{{ route('buffet.pos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 {{ request()->routeIs('buffet.pos.*') ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-600' }} transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">Buffet POS</span>
    </a>
</nav>
