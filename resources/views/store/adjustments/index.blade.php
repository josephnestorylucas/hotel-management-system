@extends('layouts.app')

@section('title', 'Stock Adjustments')
@section('page-title', 'Stock Adjustments')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Adjustments</h1>
    @if(auth()->user()->hasAnyRole(['STORE_MANAGER', 'SUPERVISOR', 'RESTAURANT_MANAGER']))
    <a href="{{ route('store.adjustments.create') }}"
       class="bg-primary text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700 font-medium">
        + New Adjustment
    </a>
    @endif
</div>

{{-- Status filter --}}
<div class="flex gap-2 mb-6">
    @foreach(['' => 'All', 'pending' => 'Pending', 'applied' => 'Applied', 'rejected' => 'Rejected'] as $value => $label)
    <a href="{{ route('store.adjustments.index', $value ? ['status' => $value] : []) }}"
       class="px-3 py-1.5 rounded-xl text-sm border
              {{ request('status') === $value || (empty($value) && !request('status'))
                 ? 'bg-primary text-white border-primary'
                 : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Location</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Previous</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">New</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Diff</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">By</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($adjustments as $adj)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $adj->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $adj->product->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $adj->location->name }}</td>
                <td class="px-4 py-3 text-right text-gray-400">{{ $adj->previous_qty }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $adj->new_qty }}</td>
                <td class="px-4 py-3 text-right {{ $adj->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $adj->difference >= 0 ? '+' : '' }}{{ $adj->difference }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($adj->status === 'pending')  bg-yellow-100 text-yellow-700
                        @elseif($adj->status === 'applied') bg-green-100 text-green-700
                        @else bg-red-100 text-red-600 @endif">
                        {{ ucfirst($adj->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $adj->creator->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center space-x-1">
                    @if($adj->status === 'pending' && auth()->user()->hasRole('STORE_MANAGER'))
                    <form method="POST" action="{{ route('store.adjustments.approve', $adj) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-green-500 text-white px-2 py-1 rounded-lg hover:bg-green-600">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('store.adjustments.reject', $adj) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-600">Reject</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-400">No adjustments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $adjustments->withQueryString()->links() }}</div>
</div>
@endsection
