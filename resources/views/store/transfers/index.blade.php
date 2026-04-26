@extends('layouts.app')

@section('title', 'Stock Transfers')
@section('page-title', 'Stock Transfers')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Transfers</h1>
    @if(auth()->user()->hasRole('STORE_MANAGER'))
    <a href="{{ route('store.transfers.create') }}"
       class="bg-primary text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700 font-medium">
        + New Transfer
    </a>
    @endif
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">From</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">To</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Requested By</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transfers as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $t->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $t->product->name }}</td>
                <td class="px-4 py-3 text-right">{{ $t->quantity }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->fromLocation->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->toLocation->name }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($t->status === 'pending')    bg-yellow-100 text-yellow-700
                        @elseif($t->status === 'approved') bg-blue-100 text-blue-700
                        @elseif($t->status === 'completed') bg-green-100 text-green-700
                        @elseif($t->status === 'rejected')  bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($t->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $t->requester->name }}</td>
                <td class="px-4 py-3 text-center space-x-1">
                    @if($t->status === 'pending' && auth()->user()->hasAnyRole(['manager', 'admin']))
                    <form method="POST" action="{{ route('store.transfers.approve', $t) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-blue-600 text-white px-2 py-1 rounded-lg hover:bg-blue-700">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('store.transfers.reject', $t) }}" class="inline-flex items-center gap-1">
                        @csrf
                        <input type="text"
                               name="rejection_reason"
                               required
                               minlength="5"
                               maxlength="500"
                               placeholder="Reason"
                               class="text-xs border border-gray-200 rounded-lg px-2 py-1 w-28">
                        <button class="text-xs bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-600">Reject</button>
                    </form>
                    @endif
                    @if(in_array($t->status, ['pending', 'approved'], true) && auth()->user()->hasRole('STORE_MANAGER'))
                    <form method="POST" action="{{ route('store.transfers.fulfill', $t) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-green-600 text-white px-2 py-1 rounded-lg hover:bg-green-700">Complete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @if($t->status === 'rejected' && $t->rejection_reason)
            <tr>
                <td colspan="8" class="px-4 py-2 bg-red-50 text-xs text-red-700">
                    <strong>Rejection reason:</strong> {{ $t->rejection_reason }}
                </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No transfers found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $transfers->withQueryString()->links() }}</div>
</div>
@endsection
