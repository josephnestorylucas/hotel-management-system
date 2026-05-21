{{-- resources/views/shared/sidebar/manager.blade.php --}}
{{-- Manager Sidebar - Full business operations control --}}
{{-- Includes: Operations, Reservations, Bookings, Guests, Laundry Orders, Hall Bookings, Conference --}}
{{-- NOTE: Manager does NOT have access to infrastructure (Buildings, Floors, Rooms, Room Types, Users) --}}
<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    @php
        $pendingApprovals = \App\Models\LocalPurchaseOrder::where('status', 'pending_approval')->count();
        $pendingGrnApprovals = \App\Models\GoodsReceivedNote::whereIn('status', [
            \App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
            \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
        ])->count();
        $pendingLaundry = \App\Models\LaundryOrder::whereIn('status', ['pending', 'in_progress'])->count();
    @endphp

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>{{ __('general.nav.dashboard') }}</span>
    </a>

    <!-- Operations Section - Core business operations for Manager -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('dashboard.sections.quick_actions') }}</p>
        
        <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('reservations.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ __('general.nav.reservations') }}</span>
            @php
                $pendingReservations = \App\Models\Reservation::where('status', 'pending')->count();
            @endphp
            @if($pendingReservations > 0)
                <span class="ml-auto bg-yellow-100 text-yellow-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingReservations }}</span>
            @endif
        </a>

        <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('bookings.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>{{ __('general.nav.bookings') }}</span>
            @php
                $activeBookings = \App\Models\Booking::where('status', 'checked_in')->count();
            @endphp
            @if($activeBookings > 0)
                <span class="ml-auto bg-green-100 text-green-600 text-xs font-bold px-2 py-1 rounded-full">{{ $activeBookings }}</span>
            @endif
        </a>

    </div>

    <!-- Guests Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.guests') }}</p>

        <a href="{{ route('guests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('guests.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>{{ __('guests.all_guests') }}</span>
        </a>

        <a href="{{ route('guests.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('guests.create') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>{{ __('guests.add_guest') }}</span>
        </a>

        <a href="{{ route('guests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('guests.index') && request('search') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>{{ __('guests.search_guests') }}</span>
        </a>

        <a href="{{ route('bookings.current-guests') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('bookings.current-guests') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>{{ __('bookings.current_guests') }}</span>
        </a>
    </div>

    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.procurement') }}</p>

        <a href="{{ route('manager.procurement.approvals') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('manager.procurement.approvals') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>{{ __('general.nav.approval_dashboard') }}</span>
            @if($pendingApprovals > 0)
                <span class="ml-auto bg-yellow-100 text-yellow-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingApprovals }}</span>
            @endif
        </a>

        <a href="{{ route('manager.procurement.grn-approvals') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('manager.procurement.grn-approvals') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l-2 2-1-1"/>
            </svg>
            <span>GRN Approvals</span>
            @if($pendingGrnApprovals > 0)
                <span class="ml-auto bg-orange-100 text-orange-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingGrnApprovals }}</span>
            @endif
        </a>

        <a href="{{ route('manager.stock.overview') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('manager.stock.overview') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>{{ __('general.nav.stock_overview') }}</span>
        </a>

        <a href="{{ route('manager.stock.movements') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('manager.stock.movements') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <span>{{ __('general.nav.movements') }}</span>
        </a>

        <a href="{{ route('manager.accounting.reports.supplier-payables') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('manager.accounting.reports.supplier-payables') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ __('general.nav.supplier_payables') }}</span>
        </a>
    </div>

    <!-- Housekeeping Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.housekeeping') }}</p>

        <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ __('general.nav.rooms') }}</span>
        </a>

        <a href="{{ route('cleaning.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('cleaning.index') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <span>Cleaning</span>
        </a>

        <a href="{{ route('cleaning.maintenance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('cleaning.maintenance') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Maintenance</span>
            @php $oooCount = \App\Models\Room::where('status', 'out_of_order')->count(); @endphp
            @if($oooCount > 0)
                <span class="ml-auto bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full">{{ $oooCount }}</span>
            @endif
        </a>
    </div>

    <!-- Laundry Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.laundry') }}</p>

        <a href="{{ route('laundry.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laundry.orders.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>{{ __('laundry.laundry_orders') }}</span>
            @if($pendingLaundry > 0)
                <span class="ml-auto bg-purple-100 text-purple-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingLaundry }}</span>
            @endif
        </a>

        <a href="{{ route('laundry.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laundry.services.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ __('laundry.price_list') }}</span>
        </a>
    </div>

    <!-- Conference Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.conference') }}</p>

        <a href="{{ route('organizations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('organizations.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Organizations & Events</span>
        </a>

        <a href="{{ route('conference-bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('conference-bookings.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ __('bookings.hall_bookings') }}</span>
        </a>

        <a href="{{ route('conferences.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('conferences.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>{{ __('bookings.conferences') }}</span>
        </a>

        <a href="{{ route('conference-halls.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('conference-halls.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>{{ __('general.nav.conference') }}</span>
            <span class="ml-auto text-xs text-gray-400">({{ __('general.view') }})</span>
        </a>
    </div>

    <!-- Reports Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.reports') }}</p>
        
        <a href="{{ route('laundry.reports.daily') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laundry.reports.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>{{ __('laundry.daily_report') }}</span>
        </a>
    </div>

    <!-- Finance Section -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Finance</p>
        
        <a href="{{ route('finance.refunds.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('finance.refunds.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Refunds</span>
        </a>

        <a href="{{ route('finance.payments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('finance.payments.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Payments</span>
        </a>
    </div>

    <!-- Settings -->
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('general.nav.settings') }}</p>
        
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ __('general.profile') }}</span>
        </a>
    </div>
</nav>
