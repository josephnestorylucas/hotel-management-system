{{-- resources/views/dashboards/store-manager.blade.php --}}
@extends('layouts.app')

@section('title', 'Store Manager Dashboard - MRK Hotel')
@section('page-title', 'Store Manager Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-indigo-100">Procurement & Store Management Overview</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-indigo-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Suppliers</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['active_suppliers'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">of {{ $stats['total_suppliers'] }} total</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending LPOs</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['pending_lpos'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">awaiting approval</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending GRNs</p>
                <p class="text-3xl font-extrabold text-orange-600 mt-1">{{ $stats['pending_grns'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">awaiting confirmation</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['low_stock_items'] }}</p>
                <p class="text-xs text-red-500 font-medium mt-1">need reordering</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Procurement Overview & Financial Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- LPO Status Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Purchase Order Status</h3>
        <div class="space-y-3">
            @php
                $lpoStatuses = [
                    'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Draft'],
                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pending Approval'],
                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Approved'],
                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rejected'],
                    'sent' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Sent to Supplier'],
                    'fully_received' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'label' => 'Fully Received'],
                ];
            @endphp
            @foreach($lpoStatuses as $status => $colors)
            <div class="flex items-center justify-between p-3 {{ $colors['bg'] }} rounded-xl">
                <span class="font-medium {{ $colors['text'] }}">{{ $colors['label'] }}</span>
                <span class="{{ $colors['bg'] }} {{ $colors['text'] }} text-sm font-bold px-3 py-1 rounded-full">{{ $lpoStatusCounts[$status] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Total LPOs</span>
                <span class="text-lg font-extrabold text-secondary">{{ $stats['total_lpos'] }}</span>
            </div>
        </div>
    </div>

    <!-- GRN Status Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Goods Received Status</h3>
        <div class="space-y-3">
            @php
                $grnStatuses = [
                    'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Draft'],
                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pending Confirmation'],
                    'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Confirmed'],
                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rejected'],
                ];
            @endphp
            @foreach($grnStatuses as $status => $colors)
            <div class="flex items-center justify-between p-3 {{ $colors['bg'] }} rounded-xl">
                <span class="font-medium {{ $colors['text'] }}">{{ $colors['label'] }}</span>
                <span class="{{ $colors['bg'] }} {{ $colors['text'] }} text-sm font-bold px-3 py-1 rounded-full">{{ $grnStatusCounts[$status] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Total GRNs</span>
                <span class="text-lg font-extrabold text-secondary">{{ $stats['total_grns'] }}</span>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Financial Summary</h3>
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">This Month Spending</p>
                <p class="text-2xl font-extrabold text-green-700 mt-2">{{ number_format($stats['month_spending'], 0) }} TZS</p>
                <p class="text-xs text-green-600 mt-1">Confirmed GRNs</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Pending Orders Value</p>
                <p class="text-2xl font-extrabold text-yellow-700 mt-2">{{ number_format($stats['pending_orders_value'], 0) }} TZS</p>
                <p class="text-xs text-yellow-600 mt-1">Awaiting delivery</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center p-3 bg-blue-50 rounded-xl">
                    <p class="text-xl font-extrabold text-blue-700">{{ $stats['today_lpos'] }}</p>
                    <p class="text-xs text-blue-600">LPOs Today</p>
                </div>
                <div class="text-center p-3 bg-indigo-50 rounded-xl">
                    <p class="text-xl font-extrabold text-indigo-700">{{ $stats['today_grns'] }}</p>
                    <p class="text-xs text-indigo-600">GRNs Today</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals Section -->
@if($pendingApprovals->count() > 0 || $pendingGrnConfirmations->count() > 0)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Pending LPO Approvals -->
    @if($pendingApprovals->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-yellow-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-extrabold text-secondary">Pending LPO Approvals</h3>
            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingApprovals->count() }} pending</span>
        </div>
        <div class="space-y-3">
            @foreach($pendingApprovals as $lpo)
            <a href="{{ route('procurement.lpo.show', $lpo) }}" class="block p-3 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-secondary">{{ $lpo->lpo_number }}</p>
                        <p class="text-sm text-gray-500">{{ $lpo->supplierName }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-secondary">{{ number_format($lpo->grand_total, 0) }} TZS</p>
                        <p class="text-xs text-gray-500">{{ $lpo->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('procurement.lpo.index') }}?status=pending" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">View All Pending LPOs &rarr;</a>
        </div>
    </div>
    @endif

    <!-- Pending GRN Confirmations -->
    @if($pendingGrnConfirmations->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-orange-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-extrabold text-secondary">Pending GRN Confirmations</h3>
            <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingGrnConfirmations->count() }} pending</span>
        </div>
        <div class="space-y-3">
            @foreach($pendingGrnConfirmations as $grn)
            <a href="{{ route('procurement.grn.show', $grn) }}" class="block p-3 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-secondary">{{ $grn->grn_number }}</p>
                        <p class="text-sm text-gray-500">{{ $grn->supplierName }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-secondary">{{ number_format($grn->grand_total, 0) }} TZS</p>
                        <p class="text-xs text-gray-500">{{ $grn->received_date?->format('M d, Y') }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('procurement.grn.index') }}?status=pending" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">View All Pending GRNs &rarr;</a>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Recent Activity & Top Suppliers -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Purchase Orders -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Purchase Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 pr-4">LPO #</th>
                        <th class="pb-3 pr-4">Supplier</th>
                        <th class="pb-3 pr-4">Amount</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentLpos as $lpo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 pr-4">
                            <a href="{{ route('procurement.lpo.show', $lpo) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $lpo->lpo_number }}</a>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-sm text-gray-600">{{ Str::limit($lpo->supplierName, 20) }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="font-semibold text-secondary">{{ number_format($lpo->grand_total, 0) }}</span>
                        </td>
                        <td class="py-3">
                            @php
                                $statusBadge = match($lpo->status) {
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'fully_received' => 'bg-indigo-100 text-indigo-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $lpo->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-400">No purchase orders yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('procurement.lpo.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">View All Purchase Orders &rarr;</a>
        </div>
    </div>

    <!-- Top Suppliers -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Top Suppliers This Month</h3>
        <div class="space-y-3">
            @forelse($topSuppliers as $index => $supplier)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg flex items-center justify-center">
                        <span class="font-bold text-indigo-600">{{ $index + 1 }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-secondary">{{ $supplier->name }}</span>
                        <p class="text-xs text-gray-500">{{ $supplier->contact_person ?? 'N/A' }}</p>
                    </div>
                </div>
                <span class="bg-indigo-100 text-indigo-700 text-sm font-bold px-3 py-1 rounded-full">{{ $supplier->purchase_orders_count }} orders</span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">No supplier activity this month.</div>
            @endforelse
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('procurement.suppliers.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Manage Suppliers &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Goods Received Notes -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Goods Received Notes</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">GRN #</th>
                    <th class="pb-3 pr-4">Supplier</th>
                    <th class="pb-3 pr-4">LPO Ref</th>
                    <th class="pb-3 pr-4">Received Date</th>
                    <th class="pb-3 pr-4">Amount</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentGrns as $grn)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-4">
                        <a href="{{ route('procurement.grn.show', $grn) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $grn->grn_number }}</a>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ Str::limit($grn->supplierName, 20) }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        @if($grn->lpo)
                        <a href="{{ route('procurement.lpo.show', $grn->lpo) }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ $grn->lpo->lpo_number }}</a>
                        @else
                        <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $grn->received_date?->format('M d, Y') ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-semibold text-secondary">{{ number_format($grn->grand_total, 0) }}</span>
                    </td>
                    <td class="py-3">
                        @php
                            $statusBadge = match($grn->status) {
                                'draft' => 'bg-gray-100 text-gray-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                            {{ ucfirst($grn->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">No goods received notes yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100">
        <a href="{{ route('procurement.grn.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">View All GRNs &rarr;</a>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('procurement.lpo.create') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-indigo-200 rounded-full flex items-center justify-center mb-3 group-hover:bg-indigo-300 transition-colors">
                <svg class="w-6 h-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-indigo-700">New LPO</span>
        </a>
        <a href="{{ route('procurement.grn.create') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center mb-3 group-hover:bg-green-300 transition-colors">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-green-700">Receive Goods</span>
        </a>
        <a href="{{ route('procurement.suppliers.create') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-300 transition-colors">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-blue-700">Add Supplier</span>
        </a>
        <a href="{{ route('store.reports.stock-snapshot') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-300 transition-colors">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-purple-700">Stock Report</span>
        </a>
    </div>
</div>
@endsection
