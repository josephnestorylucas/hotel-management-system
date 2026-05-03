{{-- resources/views/shared/sidebar/supervisor.blade.php --}}
<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>{{ __('general.nav.dashboard') }}</span>
        @php
            $pendingTasks = \App\Models\Room::whereIn('status', ['dirty', 'out_of_order'])->count();
        @endphp
        @if($pendingTasks > 0)
            <span class="ml-auto bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingTasks }}</span>
        @endif
    </a>

    <!-- Task Supervision Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.housekeeping') }}</p>
        
        <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ __('general.nav.rooms') }}</span>
            @php
                $dirtyRooms = \App\Models\Room::where('status', 'dirty')->count();
            @endphp
            @if($dirtyRooms > 0)
                <span class="ml-auto bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">{{ $dirtyRooms }}</span>
            @endif
        </a>

        <a href="{{ route('laundry.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laundry.orders.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>{{ __('laundry.laundry_orders') }}</span>
            @php
                $pendingLaundry = \App\Models\LaundryOrder::whereIn('status', ['received', 'processing', 'pending_confirmation'])->count();
            @endphp
            @if($pendingLaundry > 0)
                <span class="ml-auto bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingLaundry }}</span>
            @endif
        </a>

        <a href="{{ route('laundry.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laundry.services.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span>{{ __('laundry.price_list') }}</span>
        </a>
    </div>

    <!-- Settings -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.settings') }}</p>
        
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>{{ __('general.nav.reports') }}</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ __('general.profile') }}</span>
        </a>
    </div>
</nav>
