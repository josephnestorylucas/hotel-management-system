{{-- resources/views/dashboards/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard - MRK Hotel')
@section('page-title', 'Admin Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="text-blue-100">Here's what's happening at MRK Hotel today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-blue-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Buildings</p>
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
                <p class="text-sm font-medium text-gray-500">Total Rooms</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_rooms'] }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">{{ $stats['active_rooms'] }} active</p>
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
                <p class="text-sm font-medium text-gray-500">Total Users</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_users'] }}</p>
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
                <p class="text-sm font-medium text-gray-500">Total Reservations</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_reservations'] }}</p>
                <p class="text-xs text-yellow-600 font-medium mt-1">{{ $stats['pending_reservations'] }} pending</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Room & Revenue Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Room Status Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Room Status Overview</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                <div class="text-3xl font-extrabold text-green-600">{{ $stats['available_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">Available</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border border-red-200">
                <div class="text-3xl font-extrabold text-red-600">{{ $stats['occupied_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">Occupied</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
                <div class="text-3xl font-extrabold text-yellow-600">{{ $stats['reserved_rooms'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">Reserved</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                <div class="text-3xl font-extrabold text-primary">{{ $roomStatusCounts['dirty'] ?? 0 }}</div>
                <div class="text-sm text-gray-600 font-medium mt-1">Needs Cleaning</div>
            </div>
        </div>
        <div class="mt-6 pt-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">Occupancy Rate</span>
                <span class="text-xl font-extrabold text-primary">{{ $stats['total_rooms'] > 0 ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Revenue Overview</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-xl border border-gray-200">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Today</p>
                    <p class="text-2xl font-extrabold text-secondary">${{ number_format($stats['today_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between items-center p-4 bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-xl border border-gray-200">
                <div>
                    <p class="text-sm text-gray-500 font-medium">This Week</p>
                    <p class="text-2xl font-extrabold text-secondary">${{ number_format($stats['week_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between items-center p-4 bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-xl border border-gray-200">
                <div>
                    <p class="text-sm text-gray-500 font-medium">This Month</p>
                    <p class="text-2xl font-extrabold text-secondary">${{ number_format($stats['month_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add to resources/views/dashboards/admin.blade.php after Room & Revenue Stats --}}

<!-- Laundry Overview -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary flex items-center gap-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Laundry Services Overview
        </h3>
        <a href="{{ route('laundry.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold">View All →</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $laundryStats = [
                'total' => \App\Models\LaundryOrder::count(),
                'pending' => \App\Models\LaundryOrder::where('status', 'pending')->count(),
                'in_progress' => \App\Models\LaundryOrder::where('status', 'in_progress')->count(),
                'completed' => \App\Models\LaundryOrder::where('status', 'completed')->count(),
                'delivered' => \App\Models\LaundryOrder::where('status', 'delivered')->count(),
            ];
        @endphp
        
        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
            <div class="text-2xl font-extrabold text-purple-600">{{ $laundryStats['total'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Total Orders</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
            <div class="text-2xl font-extrabold text-yellow-600">{{ $laundryStats['pending'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Pending</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
            <div class="text-2xl font-extrabold text-blue-600">{{ $laundryStats['in_progress'] }}</div>
            <div class="text-sm text-gray-600 font-medium">In Progress</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
            <div class="text-2xl font-extrabold text-green-600">{{ $laundryStats['completed'] + $laundryStats['delivered'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Done / Delivered</div>
        </div>
    </div>
</div>

<!-- Today's Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Today's Check-ins/Check-outs</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200">
                <div class="text-4xl font-extrabold text-green-600">{{ $stats['today_checkins'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-2">Expected Check-ins</div>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border-2 border-red-200">
                <div class="text-4xl font-extrabold text-red-600">{{ $stats['today_checkouts'] }}</div>
                <div class="text-sm text-gray-600 font-medium mt-2">Expected Check-outs</div>
            </div>
        </div>
    </div>

    <!-- Reservation Status -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Reservation Status Distribution</h3>
        <div class="space-y-3">
            @foreach(['pending' => 'yellow', 'confirmed' => 'green', 'checked_in' => 'blue', 'checked_out' => 'gray', 'cancelled' => 'red'] as $status => $color)
                <div class="flex items-center justify-between p-3 bg-{{ $color }}-50 rounded-xl">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-{{ $color }}-500 mr-3"></div>
                        <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    </div>
                    <span class="font-bold text-{{ $color }}-600">{{ $reservationStatusCounts[$status] ?? 0 }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Building Stats -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Building Overview</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($buildingStats as $building)
        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg hover:border-primary/30 transition-all bg-gradient-to-br from-blue-50 via-white to-white">
            <h4 class="font-bold text-secondary">{{ $building->name }}</h4>
            <p class="text-sm text-primary font-medium">Code: {{ $building->code }}</p>
            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-500">Floors:</span>
                    <span class="font-bold ml-1 text-secondary">{{ $building->floors_count }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Rooms:</span>
                    <span class="font-bold ml-1 text-secondary">{{ $building->floors_sum_rooms_count ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Reservations -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Reservations</h3>
        <div class="space-y-3">
            @forelse($recentReservations as $reservation)
            <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl hover:shadow-md transition border border-gray-100">
                <div class="flex-1">
                    <p class="font-semibold text-secondary">{{ $reservation->guest_name }}</p>
                    <p class="text-xs text-primary font-medium">{{ $reservation->reservation_number }}</p>
                </div>
                <div class="text-right">
                    @include('components.reservation-status-badge', ['status' => $reservation->status])
                    <p class="text-xs text-gray-500 mt-1">{{ $reservation->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-6">No recent reservations</p>
            @endforelse
        </div>
        <a href="{{ route('reservations.index') }}" class="mt-6 flex items-center justify-center gap-2 text-primary hover:text-blue-700 font-semibold text-sm transition-colors">
            View All Reservations
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Users</h3>
        <div class="space-y-3">
            @forelse($recentUsers as $user)
            <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl hover:shadow-md transition border border-gray-100">
                <div class="flex-1">
                    <p class="font-semibold text-secondary">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 text-xs rounded-full bg-primary/10 text-primary font-semibold">
                        {{ $user->role->display_name }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-6">No recent users</p>
            @endforelse
        </div>
        <a href="{{ route('users.index') }}" class="mt-6 flex items-center justify-center gap-2 text-primary hover:text-blue-700 font-semibold text-sm transition-colors">
            View All Users
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('buildings.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-primary/5 to-blue-100 rounded-xl hover:shadow-lg transition border border-primary/20 group">
            <div class="w-14 h-14 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">Add Building</span>
        </a>
        <a href="{{ route('rooms.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-lg transition border border-green-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">Add Room</span>
        </a>
        <a href="{{ route('users.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:shadow-lg transition border border-purple-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">Add User</span>
        </a>
        <a href="{{ route('reservations.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl hover:shadow-lg transition border border-orange-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">New Reservation</span>
        </a>
    </div>
</div>
@endsection
