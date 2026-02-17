{{-- resources/views/dashboards/store-keeper.blade.php --}}
@extends('layouts.app')

@section('title', 'Store Keeper Dashboard - MRK Hotel')
@section('page-title', 'Store Keeper Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-teal-500 to-teal-700 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-teal-100">Here's your store & inventory overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-teal-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Laundry</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['pending_laundry'] }}</p>
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
                <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['inprogress_laundry'] }}</p>
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
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['completed_laundry'] }}</p>
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
                <p class="text-3xl font-extrabold text-teal-600 mt-1">{{ $stats['delivered_laundry'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Laundry Overview & Room Status -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Laundry Status Summary -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Laundry Summary</h3>
        <div class="space-y-4">
            @php
                $laundryColors = [
                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'bar' => 'bg-yellow-500', 'label' => 'Pending'],
                    'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'bar' => 'bg-blue-500', 'label' => 'In Progress'],
                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'bar' => 'bg-green-500', 'label' => 'Completed'],
                    'delivered' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'bar' => 'bg-teal-500', 'label' => 'Delivered'],
                ];
                $totalLaundry = array_sum($laundryStatusCounts);
            @endphp
            @foreach($laundryColors as $status => $colors)
            @php $count = $laundryStatusCounts[$status] ?? 0; @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-600">{{ $colors['label'] }}</span>
                    <span class="text-sm font-bold {{ $colors['text'] }}">{{ $count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="{{ $colors['bar'] }} h-2.5 rounded-full" style="width: {{ $totalLaundry > 0 ? ($count / $totalLaundry * 100) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6 p-3 bg-teal-50 rounded-xl text-center">
            <p class="text-sm text-teal-600 font-medium">Today's Tasks</p>
            <p class="text-2xl font-extrabold text-teal-700">{{ $stats['today_laundry'] }}</p>
        </div>
    </div>

    <!-- Room Status -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:col-span-2">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Room Status Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-extrabold text-green-700">{{ $stats['available_rooms'] }}</p>
                <p class="text-xs font-medium text-green-600 mt-1">Available</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-extrabold text-red-700">{{ $stats['occupied_rooms'] }}</p>
                <p class="text-xs font-medium text-red-600 mt-1">Occupied</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-extrabold text-yellow-700">{{ $stats['dirty_rooms'] }}</p>
                <p class="text-xs font-medium text-yellow-600 mt-1">Needs Cleaning</p>
            </div>
        </div>

        @if($roomsNeedingAttention->count() > 0)
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Rooms Needing Attention</h4>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @foreach($roomsNeedingAttention as $room)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $room->status === 'dirty' ? 'bg-yellow-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $room->status === 'dirty' ? 'text-yellow-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-secondary">Room {{ $room->room_number }}</span>
                        <p class="text-xs text-gray-500">{{ $room->floor->building->name ?? '' }} - {{ $room->roomType->name ?? '' }}</p>
                    </div>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $room->status === 'dirty' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-500 font-medium">All rooms are in good condition!</p>
        </div>
        @endif
    </div>
</div>

<!-- Recent Laundry Orders -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Laundry Orders</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">Order #</th>
                    <th class="pb-3 pr-4">Guest</th>
                    <th class="pb-3 pr-4">Room</th>
                    <th class="pb-3 pr-4">Total</th>
                    <th class="pb-3 pr-4">Date</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentLaundryOrders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-4">
                        <a href="{{ route('laundry.show', $order) }}" class="font-semibold text-blue-600 hover:text-blue-800">{{ $order->order_number }}</a>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $order->guest->first_name ?? '' }} {{ $order->guest->last_name ?? '' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $order->booking->room->room_number ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-bold text-green-600">{{ number_format($order->total_amount) }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="py-3">
                        @php
                            $orderBadge = match($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'delivered' => 'bg-teal-100 text-teal-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $orderBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">No laundry orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
