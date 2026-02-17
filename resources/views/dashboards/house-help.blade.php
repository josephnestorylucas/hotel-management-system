{{-- resources/views/dashboards/house-help.blade.php --}}
@extends('layouts.app')

@section('title', 'House Help Dashboard - MRK Hotel')
@section('page-title', 'House Help Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-purple-100">Here's the laundry overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-purple-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">In Progress</p>
                <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['inprogress_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['completed_orders'] }}</p>
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
                <p class="text-sm font-medium text-gray-500">Delivered</p>
                <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ $stats['delivered_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Overview Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Order Distribution</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <span class="text-sm font-medium text-gray-700">Pending</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $ordersByStatus['pending'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-sm font-medium text-gray-700">In Progress</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $ordersByStatus['in_progress'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="text-sm font-medium text-gray-700">Completed</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $ordersByStatus['completed'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                    <span class="text-sm font-medium text-gray-700">Delivered</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $ordersByStatus['delivered'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Today's Overview</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                <div class="text-3xl font-extrabold text-blue-600">{{ $stats['today_orders'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Orders Today</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                <div class="text-3xl font-extrabold text-purple-600">{{ $stats['total_orders'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Total Orders</div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="font-bold text-secondary mb-4">Room Status</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="p-3 bg-gradient-to-br from-yellow-50 via-white to-white rounded-xl border border-gray-200">
                    <p class="text-gray-500 font-medium">Dirty Rooms</p>
                    <p class="text-xl font-extrabold text-yellow-600">{{ $stats['dirty_rooms'] }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-red-50 via-white to-white rounded-xl border border-gray-200">
                    <p class="text-gray-500 font-medium">Out of Order</p>
                    <p class="text-xl font-extrabold text-red-600">{{ $stats['out_of_order_rooms'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary">Recent Laundry Orders</h3>
        <a href="{{ route('laundry.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold">View All &rarr;</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-purple-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-purple-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('laundry.show', $order) }}" class="font-semibold text-blue-600 hover:text-blue-800">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $order->guest->first_name ?? '' }} {{ $order->guest->last_name ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $order->booking->room->room_number ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-green-600">{{ number_format($order->total_amount) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($order->status)
                            @case('pending')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @break
                            @case('in_progress')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Progress</span>
                                @break
                            @case('completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @break
                            @case('delivered')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Delivered</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($order->status === 'pending')
                            <form method="POST" action="{{ route('laundry.mark-in-progress', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">Start</button>
                            </form>
                        @elseif($order->status === 'in_progress')
                            <form method="POST" action="{{ route('laundry.mark-completed', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium">Complete</button>
                            </form>
                        @else
                            <a href="{{ route('laundry.show', $order) }}" class="text-gray-500 hover:text-gray-700 font-medium">View</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                        <p class="mt-1 text-sm text-gray-500">No laundry orders have been created.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <a href="{{ route('laundry.index') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:shadow-lg transition border border-purple-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">All Orders</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:shadow-lg transition border border-blue-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">My Profile</span>
        </a>

        <a href="{{ route('laundry.index') }}?status=pending" class="flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl hover:shadow-lg transition border border-yellow-200 group">
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-secondary">Pending Orders</span>
        </a>
    </div>
</div>
@endsection
