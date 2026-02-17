{{-- resources/views/dashboards/supervisor.blade.php --}}
@extends('layouts.app')

@section('title', 'Supervisor Dashboard - MRK Hotel')
@section('page-title', 'Supervisor Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-blue-100">Here's your operations overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-blue-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Available Rooms</p>
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['available_rooms'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Occupied Rooms</p>
                <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['occupied_rooms'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Needs Cleaning</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['dirty_rooms'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Out of Order</p>
                <p class="text-3xl font-extrabold text-gray-600 mt-1">{{ $stats['out_of_order_rooms'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Occupancy & Today's Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Occupancy Rate</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-40 h-40">
                <svg class="transform -rotate-90 w-40 h-40">
                    <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="10" fill="none" />
                    <circle cx="80" cy="80" r="70" stroke="#005eb8" stroke-width="10" fill="none"
                            stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $stats['occupancy_rate'] / 100) }}"
                            stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-extrabold text-secondary">{{ $stats['occupancy_rate'] }}%</span>
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-2 text-sm">
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <div class="font-extrabold text-secondary">{{ $stats['occupied_rooms'] }}</div>
                <div class="text-gray-500 text-xs font-medium">Occupied</div>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <div class="font-extrabold text-secondary">{{ $stats['total_rooms'] }}</div>
                <div class="text-gray-500 text-xs font-medium">Total</div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Today's Activity</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                <div class="text-3xl font-extrabold text-green-600">{{ $stats['today_checkins'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Check-ins</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border border-red-200">
                <div class="text-3xl font-extrabold text-red-600">{{ $stats['today_checkouts'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Check-outs</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
                <div class="text-3xl font-extrabold text-yellow-600">{{ $stats['pending_reservations'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Pending</div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="font-bold text-secondary mb-4">Revenue Today</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="p-3 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl border border-gray-200">
                    <p class="text-gray-500 font-medium">Today</p>
                    <p class="text-xl font-extrabold text-secondary">${{ number_format($stats['today_revenue'], 2) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl border border-gray-200">
                    <p class="text-gray-500 font-medium">This Week</p>
                    <p class="text-xl font-extrabold text-secondary">${{ number_format($stats['week_revenue'], 2) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-50 via-white to-white rounded-xl border border-gray-200">
                    <p class="text-gray-500 font-medium">This Month</p>
                    <p class="text-xl font-extrabold text-secondary">${{ number_format($stats['month_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Status Distribution -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Room Status Distribution</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200">
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-green-600">{{ $roomStatusCounts['available'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 font-medium">Available</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border-2 border-yellow-200">
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-yellow-600">{{ $roomStatusCounts['reserved'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 font-medium">Reserved</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border-2 border-red-200">
            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-red-600">{{ $roomStatusCounts['occupied'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 font-medium">Occupied</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200">
            <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-gray-600">{{ $roomStatusCounts['dirty'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 font-medium">Dirty</div>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl border-2 border-gray-300">
            <div class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-gray-700">{{ $roomStatusCounts['out_of_order'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 font-medium">Out of Order</div>
        </div>
    </div>
</div>

{{-- Add this section in resources/views/dashboards/supervisor.blade.php --}}
{{-- Insert after Room Status Distribution section --}}

<!-- Laundry Management Overview -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary flex items-center gap-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Laundry Services
        </h3>
        <a href="{{ route('laundry.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold flex items-center gap-1">
            Manage All Tasks
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border-2 border-yellow-200">
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-yellow-600">{{ $stats['pending_laundry'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Pending</div>
        </div>

        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-blue-600">{{ $stats['inprogress_laundry'] }}</div>
            <div class="text-sm text-gray-600 font-medium">In Progress</div>
        </div>

        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-purple-600">{{ $stats['completed_laundry'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Completed</div>
        </div>

        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200">
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-green-600">{{ $stats['today_laundry'] }}</div>
            <div class="text-sm text-gray-600 font-medium">Today</div>
        </div>
    </div>

    <!-- Recent Laundry Orders -->
    <div class="pt-6 border-t border-gray-100">
        <h4 class="font-bold text-secondary mb-4">Recent Laundry Orders</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-purple-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Guest</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($recentLaundryOrders as $order)
                    <tr class="hover:bg-purple-50/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-secondary">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $order->guest->first_name ?? '' }} {{ $order->guest->last_name ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $order->booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">{{ number_format($order->total_amount) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $badge = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'delivered' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('laundry.show', $order) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No recent laundry orders</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('laundry.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Laundry Order
        </a>
    </div>
</div>

<!-- Rooms Needing Attention -->
@if($roomsNeedingAttention->count() > 0)
<div class="bg-white rounded-2xl shadow-lg border border-red-200 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-red-600 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        Rooms Requiring Attention
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">Floor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($roomsNeedingAttention as $room)
                <tr class="hover:bg-red-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-secondary">{{ $room->room_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $room->floor->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $room->roomType->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.room-status-badge', ['status' => $room->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('rooms.edit', $room) }}" class="text-primary hover:text-blue-700 font-semibold">Update Status</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Today's Check-ins/Check-outs -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Today's Check-ins & Check-outs</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Reservation #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Activity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($todayActivity as $reservation)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reservation->check_in_date->isToday())
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Check-In</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Check-Out</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->room?->room_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($reservation->status === 'confirmed' && $reservation->check_in_date->isToday())
                            <form method="POST" action="{{ route('reservations.check-in', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-700 font-semibold">Check In</button>
                            </form>
                        @elseif($reservation->status === 'checked_in' && $reservation->check_out_date->isToday())
                            <form method="POST" action="{{ route('reservations.check-out', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">Check Out</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No activity today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upcoming Arrivals -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Upcoming Arrivals (Next 7 Days)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Reservation #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Check-In</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($upcomingArrivals as $reservation)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->check_in_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->room?->room_number ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No upcoming arrivals</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
