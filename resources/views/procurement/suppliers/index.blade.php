{{-- resources/views/procurement/suppliers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Suppliers</h2>
            <p class="text-sm text-gray-500 mt-1">Manage procurement suppliers and their activity</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.suppliers.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Supplier
            </a>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <a href="{{ route('procurement.suppliers.archived') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                View Deleted
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <p class="text-sm font-medium text-gray-500">Total Suppliers</p>
            <p class="mt-2 text-3xl font-extrabold text-secondary">{{ $suppliers->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <p class="text-sm font-medium text-gray-500">Active Suppliers</p>
            <p class="mt-2 text-3xl font-extrabold text-green-600">{{ $suppliers->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <p class="text-sm font-medium text-gray-500">Inactive Suppliers</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-500">{{ $suppliers->where('is_active', false)->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Purchase Orders</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">GRNs</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-semibold text-secondary">{{ $supplier->name }}</div>
                            <div class="text-xs text-gray-500">{{ $supplier->email ?: 'No email provided' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $supplier->contact_person ?: 'No contact person' }}</div>
                        <div class="text-xs text-gray-500">{{ $supplier->phone ?: 'No phone number' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-secondary">{{ $supplier->purchase_orders_count }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-secondary">{{ $supplier->goods_received_notes_count }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $supplier->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('procurement.suppliers.show', $supplier) }}" class="text-gray-700 hover:text-secondary font-semibold">View</a>
                            <a href="{{ route('procurement.suppliers.edit', $supplier) }}" class="text-primary hover:text-blue-700 font-semibold">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <x-empty-state title="No suppliers found" message="Add your first supplier to start procurement operations." />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
