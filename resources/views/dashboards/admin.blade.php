{{-- resources/views/dashboards/admin.blade.php --}}
{{-- System Administrator Dashboard - Restricted to system management --}}
{{-- Operations modules (Reservations, Bookings, Guests, Laundry, Conferences, Procurement) removed per RBAC requirements --}}
@extends('layouts.app')

@section('title', __('dashboard.admin_title') . ' - MRK Hotel')
@section('page-title', __('dashboard.admin_title'))

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('dashboard.welcome_back') }}, {{ auth()->user()->name }}!</h2>
            <p class="text-blue-100">{{ __('dashboard.admin_welcome') }}</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-blue-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<!-- Top Stats Grid - System Administration Focus -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.total_buildings') }}</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_buildings'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.total_rooms') }}</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_rooms'] }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">{{ $stats['active_rooms'] }} {{ __('dashboard.stats.active_rooms') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.total_users') }}</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">{{ __('dashboard.stats.active_staff') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.room_types') }}</p>
                @php
                    $roomTypesCount = \App\Models\RoomType::count();
                @endphp
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $roomTypesCount }}</p>
                <p class="text-xs text-orange-600 font-medium mt-1">{{ __('dashboard.stats.categories_configured') }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Room Status Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Room Status Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.room_status_overview') }}</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                <div class="text-3xl font-extrabold text-green-600">{{ $stats['available_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">{{ __('dashboard.room_status.available') }}</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border border-red-200">
                <div class="text-3xl font-extrabold text-red-600">{{ $stats['occupied_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">{{ __('dashboard.room_status.occupied') }}</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
                <div class="text-3xl font-extrabold text-yellow-600">{{ $stats['reserved_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">{{ __('dashboard.room_status.reserved') }}</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                <div class="text-3xl font-extrabold text-primary">{{ $roomStatusCounts['dirty'] ?? 0 }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">{{ __('dashboard.stats.needs_cleaning') }}</div>
            </div>
        </div>
        <div class="mt-6 pt-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">{{ __('dashboard.stats.occupancy_rate') }}</span>
                <span class="text-xl font-extrabold text-primary">{{ $stats['total_rooms'] > 0 ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Users by Role -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.staff_by_role') }}</h3>
        @php
            $staffByRole = \App\Models\User::with('role')
                ->where('is_active', true)
                ->get()
                ->groupBy(fn($user) => $user->role->description ?? 'Unknown')
                ->map->count();
        @endphp
        <div class="space-y-3">
            @foreach($staffByRole as $roleName => $count)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-secondary">{{ $roleName }}</span>
                </div>
                <span class="bg-blue-100 text-blue-700 text-sm font-bold px-3 py-1 rounded-full">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Building Stats -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.building_overview') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($buildingStats as $building)
        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg hover:border-primary/30 transition-all bg-gradient-to-br from-blue-50 via-white to-white">
            <h4 class="font-bold text-secondary">{{ $building->name }}</h4>
            <p class="text-sm text-primary font-medium">{{ __('dashboard.table.code') }}: {{ $building->code }}</p>
            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-500">{{ __('dashboard.table.floors') }}:</span>
                    <span class="font-bold ml-1 text-secondary">{{ $building->floors_count }}</span>
                </div>
                <div>
                    <span class="text-gray-500">{{ __('dashboard.table.rooms') }}:</span>
                    <span class="font-bold ml-1 text-secondary">{{ $building->rooms_count ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Recent Users -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('dashboard.sections.recent_users') }}</h3>
        <a href="{{ route('users.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold flex items-center gap-1">
            {{ __('dashboard.actions.view_all') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="space-y-3">
        @forelse($recentUsers as $user)
        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl hover:shadow-md transition border border-gray-100">
            <div class="flex-1">
                <p class="font-semibold text-secondary">{{ $user->name }}</p>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-xs rounded-full bg-primary/10 text-primary font-semibold">
                    {{ $user->role->description ?? 'Unknown' }}
                </span>
                <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-6">{{ __('dashboard.no_recent_users') }}</p>
        @endforelse
    </div>
</div>

<!-- Quick Actions - System Administration -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.quick_actions') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('buildings.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-primary/5 to-blue-100 rounded-xl hover:shadow-lg transition border border-primary/20 group">
            <div class="w-14 h-14 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.add_building') }}</span>
        </a>
        <a href="{{ route('floors.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-lg transition border border-green-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.add_floor') }}</span>
        </a>
        <a href="{{ route('rooms.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl hover:shadow-lg transition border border-orange-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.add_room') }}</span>
        </a>
        <a href="{{ route('users.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:shadow-lg transition border border-purple-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">{{ __('dashboard.actions.add_user') }}</span>
        </a>
    </div>
</div>
@endsection
