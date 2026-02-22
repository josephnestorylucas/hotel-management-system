{{-- resources/views/shared/sidebar/restaurant-manager.blade.php --}}
<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Dashboard</span>
    </a>

    <!-- Bar & Kitchen Store Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Bar & Kitchen</p>

        <a href="{{ route('store.stock.levels') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('store.stock.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Stock Levels</span>
        </a>

        <a href="{{ route('store.transfers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('store.transfers.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span>Transfers</span>
        </a>

        <a href="{{ route('store.adjustments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('store.adjustments.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            <span>Adjustments</span>
        </a>

        <a href="{{ route('store.stock.damage-form') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('store.stock.damage*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span>Report Damage</span>
        </a>

        <a href="{{ route('store.reports.stock-snapshot') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('store.reports.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Reports</span>
        </a>
    </div>
</nav>
