{{-- resources/views/procurement/lpo/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('page-title', 'Local Purchase Orders')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Local Purchase Orders</h2>
            <p class="text-sm text-gray-500 mt-1">Manage purchase orders to suppliers</p>
        </div>
        @if(auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
        <a href="{{ route('procurement.lpo.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New LPO
        </a>
        @endif
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 overflow-x-auto pb-2">
        <a href="{{ route('procurement.lpo.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ !request('status') ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            All
        </a>
        <a href="{{ route('procurement.lpo.index', ['status' => 'draft']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'draft' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Draft
        </a>
        <a href="{{ route('procurement.lpo.index', ['status' => 'pending_approval']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'pending_approval' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Pending Approval
        </a>
        <a href="{{ route('procurement.lpo.index', ['status' => 'approved']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Approved
        </a>
        <a href="{{ route('procurement.lpo.index', ['status' => 'sent']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'sent' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Sent
        </a>
        <a href="{{ route('procurement.lpo.index', ['status' => 'fully_received']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'fully_received' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Received
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $lpos->where('status', 'draft')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Draft</div>
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
                    <div class="text-2xl font-extrabold text-secondary">{{ $lpos->where('status', 'pending_approval')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Pending</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $lpos->whereIn('status', ['approved', 'sent'])->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Active</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $lpos->where('status', 'fully_received')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Received</div>
                </div>
            </div>
        </div>
    </div>

    <!-- LPOs Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">LPO Number</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Order Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($lpos as $lpo)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                {{ substr($lpo->lpo_number, -4) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-secondary">{{ $lpo->lpo_number }}</div>
                                <div class="text-xs text-gray-500">{{ $lpo->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $lpo->supplierName }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $lpo->order_date->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-secondary">${{ number_format($lpo->grand_total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.lpo-status-badge', ['status' => $lpo->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('procurement.lpo.show', $lpo) }}" class="text-gray-600 hover:text-gray-800 font-semibold">View</a>
                            @if(in_array($lpo->status, ['draft', 'rejected']) && auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
                            <a href="{{ route('procurement.lpo.edit', $lpo) }}" class="text-primary hover:text-blue-700 font-semibold">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-secondary">No purchase orders yet</h3>
                        <p class="mt-2 text-sm text-gray-500">Create your first purchase order to get started.</p>
                        @if(auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
                        <div class="mt-6">
                            <a href="{{ route('procurement.lpo.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New LPO
                            </a>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lpos->hasPages())
    <div class="mt-6">
        {{ $lpos->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection