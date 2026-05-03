{{-- resources/views/laundry/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', __('laundry.laundry_orders'))
@section('page-title', __('laundry.title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('laundry.laundry_orders') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('laundry.manage_subtitle') }}</p>
        </div>
        <a href="{{ route('laundry.orders.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('laundry.new_order') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            'received'              => ['label' => __('laundry.status.received'),              'color' => 'yellow',  'icon' => 'M19 14l-7 7m0 0l-7-7m7 7V3'],
            'processing'            => ['label' => __('laundry.status.processing'),            'color' => 'blue',    'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            'pending_confirmation'  => ['label' => 'Pending Confirmation',                     'color' => 'purple',  'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            'ready'                 => ['label' => __('laundry.status.ready'),                 'color' => 'orange',  'icon' => 'M5 13l4 4L19 7'],
            'delivered'             => ['label' => __('laundry.status.delivered'),             'color' => 'indigo',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            'collected'             => ['label' => __('laundry.status.collected'),             'color' => 'teal',    'icon' => 'M5 13l4 4L19 7'],
        ] as $status => $meta)
        <a href="{{ route('laundry.orders.index', ['status' => $status]) }}" 
           class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-{{ $meta['color'] }}-50 to-{{ $meta['color'] }}-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-{{ $meta['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $statusCounts[$status] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ $meta['label'] }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form method="GET" action="{{ route('laundry.orders.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, name, room, phone..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div>
                <select name="customer_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                    <option value="">{{ __('laundry.filters.all_customers') }}</option>
                    <option value="guest"  {{ request('customer_type') === 'guest'  ? 'selected' : '' }}>{{ __('laundry.customer_type.hotel_guests') }}</option>
                    <option value="walkin" {{ request('customer_type') === 'walkin' ? 'selected' : '' }}>{{ __('laundry.customer_type.walkin') }}</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                    <option value="">{{ __('laundry.filters.all_statuses') }}</option>
                    @foreach(['received','processing','pending_confirmation','ready','delivered','collected','settled','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ __('laundry.status.' . $s) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                    {{ __('laundry.actions.filter') }}
                </button>
                <a href="{{ route('laundry.orders.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    {{ __('laundry.actions.reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.order') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.customer') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.type') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.items') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.total') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.expected_ready') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-blue-50/50 transition-colors {{ $order->isOverdue() ? 'bg-red-50/50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ substr($order->order_number, -4) }}
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('laundry.orders.show', $order) }}" class="text-sm font-semibold text-secondary hover:text-primary transition-colors">
                                        {{ $order->order_number }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->customer_type === 'guest')
                                <div class="text-sm font-semibold text-secondary">{{ __('laundry.info.room') }} {{ $order->room_number }}</div>
                                <div class="text-xs text-gray-500">{{ __('laundry.customer_type.guest') }}</div>
                            @else
                                <div class="text-sm font-semibold text-secondary">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $order->customer_type === 'guest'
                                   ? 'bg-purple-100 text-purple-700'
                                   : 'bg-gray-100 text-gray-600' }}">
                                {{ $order->customer_type === 'guest' ? __('laundry.customer_type.guest') : __('laundry.customer_type.walkin') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-secondary">{{ $order->items->count() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold text-secondary">@currency($order->total, 'TZS')</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($order->status === 'received')      bg-yellow-100 text-yellow-700
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                                @elseif($order->status === 'pending_confirmation') bg-purple-100 text-purple-700
                                @elseif($order->status === 'ready')     bg-orange-100 text-orange-700
                                @elseif($order->status === 'delivered') bg-indigo-100 text-indigo-700
                                @elseif($order->status === 'collected') bg-teal-100 text-teal-700
                                @elseif($order->status === 'settled')   bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ __('laundry.status.' . $order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm {{ $order->isOverdue() ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                {{ $order->expected_ready_at?->format('M d, H:i') }}
                                @if($order->isOverdue())
                                    <span class="ml-1 text-red-500">⚠</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('laundry.orders.show', $order) }}" class="text-primary hover:text-blue-700 font-semibold" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-secondary">{{ __('laundry.messages.no_orders') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ __('laundry.messages.no_orders_subtitle') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('laundry.orders.create') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('laundry.new_order') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-6">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
