{{-- resources/views/dashboards/restaurant-manager.blade.php --}}
@extends('layouts.app')

@section('title', __('dashboard.titles.restaurant_manager'))
@section('page-title', __('dashboard.titles.restaurant_manager'))

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('dashboard.welcome.greeting', ['name' => auth()->user()->name]) }}</h2>
            <p class="text-violet-100">{{ __('dashboard.welcome.restaurant_manager_message') }}</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-violet-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.restaurant.bar_products') }}</p>
                <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['total_bar_products'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.restaurant.kitchen_products') }}</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['total_kitchen_products'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stock.low_stock_items') }}</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['low_stock_items'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stock.pending_transfers') }}</p>
                <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['pending_transfers'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stock.today_movements') }}</p>
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['today_movements'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Movements -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('dashboard.sections.recent_bar_kitchen_movements') }}</h3>
            <a href="{{ route('store.reports.movements') }}" class="text-primary text-sm hover:underline">{{ __('dashboard.actions.view_all') }}</a>
        </div>
        <div class="space-y-3">
            @forelse($recentMovements as $m)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-medium text-secondary">{{ $m->product->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">
                        {{ ucwords(str_replace('_',' ',$m->type)) }}
                        &bull; {{ $m->location->name ?? '' }}
                        &bull; {{ $m->created_at->diffForHumans() }}
                    </p>
                </div>
                <span class="text-sm font-bold {{ $m->direction === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $m->direction === 'increase' ? '+' : '-' }}{{ $m->quantity }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">{{ __('dashboard.messages.no_recent_movements') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Notifications -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-4">{{ __('dashboard.sections.notifications') }}</h3>
        <div class="space-y-3">
            @forelse($notifications as $note)
            <a href="{{ $note->action_url ?? '#' }}" class="block p-3 bg-violet-50 rounded-xl hover:bg-violet-100 transition">
                <p class="text-sm font-medium text-secondary">{{ $note->title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($note->message, 60) }}</p>
            </a>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">{{ __('dashboard.messages.no_new_notifications') }}</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('store.transfers.create') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-secondary">{{ __('dashboard.actions.request_transfer') }}</p>
                <p class="text-xs text-gray-500">{{ __('dashboard.actions.request_stock_from_store') }}</p>
            </div>
        </div>
    </a>
    <a href="{{ route('store.stock.levels') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-secondary">{{ __('dashboard.actions.view_stock') }}</p>
                <p class="text-xs text-gray-500">{{ __('dashboard.actions.check_bar_kitchen_inventory') }}</p>
            </div>
        </div>
    </a>
    <a href="{{ route('store.stock.damage-form') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-secondary">{{ __('dashboard.actions.report_damage') }}</p>
                <p class="text-xs text-gray-500">{{ __('dashboard.actions.record_damaged_items') }}</p>
            </div>
        </div>
    </a>
</div>
@endsection
