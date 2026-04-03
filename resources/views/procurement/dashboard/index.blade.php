{{-- resources/views/procurement/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Procurement Dashboard')
@section('page-title', 'Procurement Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-extrabold text-secondary">Procurement Overview</h2>
        <p class="text-sm text-gray-500 mt-1">Manage suppliers, purchase orders, and goods received</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $summary['active_suppliers'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Active Suppliers</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $summary['pending_lpos'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Pending Approval</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $summary['active_lpos'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Active LPOs</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $summary['pending_grns'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Pending GRNs</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('procurement.suppliers.create') }}" class="block bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-secondary group-hover:text-primary transition-colors">New Supplier</div>
                    <div class="text-xs text-gray-500">Add a new supplier</div>
                </div>
            </div>
        </a>

        <a href="{{ route('procurement.lpo.create') }}" class="block bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-secondary group-hover:text-primary transition-colors">New Purchase Order</div>
                    <div class="text-xs text-gray-500">Create new LPO</div>
                </div>
            </div>
        </a>

        <a href="{{ route('procurement.grn.create') }}" class="block bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-secondary group-hover:text-primary transition-colors">Receive Goods</div>
                    <div class="text-xs text-gray-500">Create GRN</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent LPOs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-secondary">Recent Purchase Orders</h3>
            <a href="{{ route('procurement.lpo.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold">
                View All →
            </a>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">LPO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentLpos as $lpo)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ route('procurement.lpo.show', $lpo) }}" class="text-sm font-semibold text-primary hover:text-blue-700">
                            {{ $lpo->lpo_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $lpo->supplierName }}</span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $lpo->order_date->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm font-bold text-secondary"><x-money :amount="$lpo->grand_total" /></span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        @include('components.lpo-status-badge', ['status' => $lpo->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                        No recent purchase orders
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Recent GRNs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-secondary">Recent Goods Received</h3>
            <a href="{{ route('procurement.grn.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold">
                View All →
            </a>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">GRN Number</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">LPO</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Received Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentGrns as $grn)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ route('procurement.grn.show', $grn) }}" class="text-sm font-semibold text-primary hover:text-blue-700">
                            {{ $grn->grn_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $grn->lpo->lpo_number }}</span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $grn->received_date->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="text-sm font-bold text-secondary"><x-money :amount="$grn->grand_total" /></span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        @include('components.grn-status-badge', ['status' => $grn->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                        No recent goods received
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection