{{-- resources/views/laundry/reports/daily.blade.php --}}
@extends('layouts.app')

@section('title', __('laundry.daily_laundry_report'))
@section('page-title', __('laundry.title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('laundry.daily_laundry_report') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('laundry.report_subtitle') }}</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <div class="relative">
                <input type="date" name="date" value="{{ $date }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                {{ __('laundry.actions.view_report') }}
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $summary['total_orders'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('laundry.reports.total_orders') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ number_format($summary['total_revenue'], 0) }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('laundry.reports.total_revenue_tzs') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ number_format($summary['guest_revenue'], 0) }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('laundry.reports.guest_revenue') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ number_format($summary['walkin_revenue'], 0) }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('laundry.reports.walkin_revenue') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-xl font-extrabold text-secondary">{{ number_format($summary['cash'], 0) }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">{{ __('laundry.payment.cash_payments') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="text-xl font-extrabold text-secondary">{{ number_format($summary['card'], 0) }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">{{ __('laundry.payment.card_payments') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="text-xl font-extrabold text-secondary">{{ number_format($summary['charged'], 0) }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">{{ __('laundry.payment.charged_to_booking') }}</div>
        </div>
    </div>

    {{-- Overdue orders --}}
    @if($overdueOrders->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-red-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
            <h3 class="text-lg font-bold text-red-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ __('laundry.sections.overdue_orders') }} ({{ $overdueOrders->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('laundry.table.order') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('laundry.table.customer') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('laundry.table.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('laundry.table.expected_ready') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('laundry.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($overdueOrders as $order)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-secondary">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-secondary">
                                {{ $order->customer_type === 'guest' ? __('laundry.info.room') . ' ' . $order->room_number : $order->customer_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full font-medium bg-{{ $order->status_badge_color }}-100 text-{{ $order->status_badge_color }}-700">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-red-500">{{ $order->expected_ready_at?->format('M d, H:i') }} ⚠</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('laundry.orders.show', $order) }}" 
                               class="text-primary hover:text-blue-700 font-semibold text-sm">{{ __('laundry.actions.view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Settled orders --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider">{{ __('laundry.sections.settled_orders') }} — {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.order') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.customer') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.type') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.items') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.total') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.payment') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.settled_by') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.time') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('laundry.orders.show', $order) }}" class="text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-secondary">
                                {{ $order->customer_type === 'guest' ? __('laundry.info.room') . ' ' . $order->room_number : $order->customer_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $order->customer_type === 'guest' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $order->customer_type === 'guest' ? __('laundry.customer_type.guest') : __('laundry.customer_type.walkin') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm text-secondary">{{ $order->items->count() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold text-secondary">{{ number_format($order->total, 0) }} <span class="text-xs text-gray-400">TZS</span></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-xs text-gray-500">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-secondary">{{ $order->settler->name ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs text-gray-500">{{ $order->settled_at->format('H:i') }}</span>
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
                            <h3 class="text-lg font-bold text-secondary">{{ __('laundry.messages.no_settled_orders') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ __('laundry.messages.no_settled_orders_date') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
