@extends('layouts.app')

@section('title', 'Refunds Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Refunds Management</h1>
            <p class="text-gray-500 text-sm mt-1">Process full or partial refunds for online and walk-in payments</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.payments.index') }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Back to Payments
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Refundable Payments</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_refundable_payments'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Refundable Walk-ins</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_refundable_walkin'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Refunded Today</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_refunded_today'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <form method="GET" action="{{ route('finance.refunds.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="online" {{ $type === 'online' ? 'selected' : '' }}>Online Payments</option>
                    <option value="walkin" {{ $type === 'walkin' ? 'selected' : '' }}>Walk-in Payments</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="refundable" {{ $status === 'refundable' ? 'selected' : '' }}>Refundable</option>
                    <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Reference, customer..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Online Payments Table --}}
    @if($type === 'all' || $type === 'online')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Online Payments</h2>
            <p class="text-sm text-gray-500">Booking payments processed via AzamPesa</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Payment #</th>
                        <th class="px-4 py-3 text-left font-medium">Booking</th>
                        <th class="px-4 py-3 text-left font-medium">Guest</th>
                        <th class="px-4 py-3 text-left font-medium">Method</th>
                        <th class="px-4 py-3 text-right font-medium">Amount</th>
                        <th class="px-4 py-3 text-right font-medium">Refunded</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-center font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600">{{ $payment->payment_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($payment->booking)
                            <a href="{{ route('bookings.show', $payment->booking_id) }}" class="text-indigo-600 hover:underline">
                                {{ $payment->booking->booking_number }}
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($payment->booking?->guest)
                            <div class="font-medium text-gray-900">{{ $payment->booking->guest->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->booking->guest->phone_number }}</div>
                            @else
                            <span class="text-gray-400">Guest</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $payment->payment_method === 'mobile' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($payment->payment_method) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @php $refunded = $payment->refund_metadata['total_refunded'] ?? 0; @endphp
                            @if($refunded > 0)
                            <span class="text-red-600 font-medium">{{ number_format($refunded, 2) }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $payment->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($payment->status === 'successful' || $payment->status === 'partially_refunded')
                            <a href="{{ route('finance.refunds.payment', $payment) }}" 
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refund
                            </a>
                            @elseif($payment->status === 'refunded')
                            <span class="text-gray-400 text-xs">Fully Refunded</span>
                            @else
                            <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            No online payments found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $payments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Walk-in Transactions Table --}}
    @if($type === 'all' || $type === 'walkin')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Walk-in Transactions</h2>
            <p class="text-sm text-gray-500">Laundry, Restaurant, and Bar payments</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Transaction #</th>
                        <th class="px-4 py-3 text-left font-medium">Module</th>
                        <th class="px-4 py-3 text-left font-medium">Order #</th>
                        <th class="px-4 py-3 text-left font-medium">Customer</th>
                        <th class="px-4 py-3 text-left font-medium">Method</th>
                        <th class="px-4 py-3 text-right font-medium">Amount</th>
                        <th class="px-4 py-3 text-right font-medium">Refunded</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-center font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($walkinTransactions as $txn)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600">{{ $txn->transaction_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $txn->module === 'laundry' ? 'bg-purple-100 text-purple-700' : ($txn->module === 'restaurant' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($txn->module) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $txn->order_number }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $txn->customer_name }}</div>
                            @if($txn->customer_phone)
                            <div class="text-xs text-gray-500">{{ $txn->customer_phone }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $txn->payment_method === 'mobile' ? 'bg-green-100 text-green-700' : ($txn->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($txn->payment_method) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format($txn->amount, 2) }} {{ $txn->currency }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @php $refunded = $txn->metadata['total_refunded'] ?? 0; @endphp
                            @if($refunded > 0)
                            <span class="text-red-600 font-medium">{{ number_format($refunded, 2) }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusClass = match($txn->status) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $txn->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($txn->status === 'completed' && $txn->provider_reference)
                                @php $maxRefund = (float)$txn->amount - ($txn->metadata['total_refunded'] ?? 0); @endphp
                                @if($maxRefund > 0)
                                <a href="{{ route('finance.refunds.walkin', $txn) }}" 
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Refund
                                </a>
                                @else
                                <span class="text-gray-400 text-xs">Fully Refunded</span>
                                @endif
                            @elseif($txn->status === 'refunded')
                            <span class="text-gray-400 text-xs">Refunded</span>
                            @elseif($txn->payment_method === 'cash')
                            <span class="text-gray-400 text-xs">Cash (Manual)</span>
                            @else
                            <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            No walk-in transactions found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($walkinTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $walkinTransactions->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $walkinTransactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
