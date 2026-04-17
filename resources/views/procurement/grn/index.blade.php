{{-- resources/views/procurement/grn/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Goods Received Notes')
@section('page-title', 'Goods Received Notes')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Goods Received Notes</h2>
            <p class="text-sm text-gray-500 mt-1">Track goods received from suppliers</p>
        </div>
        @if(auth()->user()->hasRole('store_keeper'))
        <a href="{{ route('procurement.grn.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New GRN
        </a>
        @endif
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 overflow-x-auto pb-2">
        <a href="{{ route('procurement.grn.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ !request('status') ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            All
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'draft']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'draft' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Draft
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'submitted']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'submitted' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Submitted
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'confirmed_by_storekeeper']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'confirmed_by_storekeeper' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Storekeeper Confirmed
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'pending_manager_approval']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'pending_manager_approval' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Pending Manager Approval
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'rejected']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'rejected' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Rejected
        </a>
        <a href="{{ route('procurement.grn.index', ['status' => 'approved']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
            Approved
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
                    <div class="text-2xl font-extrabold text-secondary">{{ $grns->where('status', 'draft')->count() }}</div>
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
                    <div class="text-2xl font-extrabold text-secondary">{{ $grns->where('status', 'pending_manager_approval')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Pending</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $grns->where('status', 'approved')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">Approved</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary"><x-money :amount="$grns->sum('grand_total')" /></div>
                    <div class="text-xs text-gray-500 font-medium">Total Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRNs Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">GRN Number</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">LPO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Received Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Integration</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($grns as $grn)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                {{ substr($grn->grn_number, -4) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-secondary">{{ $grn->grn_number }}</div>
                                <div class="text-xs text-gray-500">{{ $grn->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('procurement.lpo.show', $grn->lpo) }}" class="text-sm text-primary hover:text-blue-700 font-semibold">
                            {{ $grn->lpo->lpo_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $grn->supplierName }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $grn->received_date->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-secondary"><x-money :amount="$grn->grand_total" /></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-600">Stock: {{ $grn->items->whereNotNull('stock_movement_id')->count() }}/{{ $grn->items->whereNotNull('product_id')->count() }}</div>
                        <div class="text-xs {{ $grn->accounting_journal_entry_id ? 'text-green-600' : 'text-amber-600' }}">Accounting: {{ $grn->accounting_journal_entry_id ? 'posted' : 'pending' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.grn-status-badge', ['status' => $grn->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('procurement.grn.show', $grn) }}" class="text-gray-600 hover:text-gray-800 font-semibold">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-secondary">No goods received yet</h3>
                        <p class="mt-2 text-sm text-gray-500">Create a GRN when goods arrive from suppliers.</p>
                        @if(auth()->user()->hasRole('store_keeper'))
                        <div class="mt-6">
                            <a href="{{ route('procurement.grn.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New GRN
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
    @if($grns->hasPages())
    <div class="mt-6">
        {{ $grns->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
