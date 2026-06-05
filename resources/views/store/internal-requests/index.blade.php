@extends('layouts.app')

@section('title', 'Internal Requests')
@section('page-title', 'Internal Requests')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Internal Usage Requests</h1>
    @if(auth()->user()->hasRole('HOUSE_HELP'))
    <a href="{{ route('store.internal-requests.create') }}"
       class="bg-primary text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700 font-medium">
        + New Request
    </a>
    @endif
</div>

{{-- Status filter --}}
<div class="flex gap-2 mb-6">
    @foreach(['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'fulfilled' => 'Fulfilled', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'] as $value => $label)
    <a href="{{ route('store.internal-requests.index', $value ? ['status' => $value] : []) }}"
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
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Department</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Requested By</th>
                <th class="px-4 py-3 text-center text-gray-600 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $req->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">{{ $req->department }}</td>
                <td class="px-4 py-3 font-medium">{{ $req->product->name }}</td>
                <td class="px-4 py-3 text-right">{{ $req->quantity }} {{ $req->product->unit }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($req->status === 'pending')   bg-yellow-100 text-yellow-700
                        @elseif($req->status === 'approved')  bg-blue-100 text-blue-700
                        @elseif($req->status === 'fulfilled') bg-green-100 text-green-700
                        @elseif($req->status === 'rejected')  bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($req->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->requester->name }}</td>
                <td class="px-4 py-3 text-center space-x-1">
                    @if($req->status === 'pending' && auth()->user()->hasRole('SUPERVISOR'))
                    <form method="POST" action="{{ route('store.internal-requests.approve', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-green-500 text-white px-2 py-1 rounded-lg hover:bg-green-600">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('store.internal-requests.reject', $req) }}" class="inline"
                          onsubmit="var r = prompt('Rejection reason:'); if(!r){return false;} document.getElementById('reject-reason-{{ $req->id }}').value = r; return true;">
                        @csrf
                        <input type="hidden" id="reject-reason-{{ $req->id }}" name="reason">
                        <button class="text-xs bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-600">Reject</button>
                    </form>
                    @endif

                    @if($req->status === 'approved' && auth()->user()->hasRole('STORE_KEEPER'))
                    <form method="POST" action="{{ route('store.internal-requests.fulfill', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-primary text-white px-2 py-1 rounded-lg hover:bg-blue-700">Fulfill</button>
                    </form>
                    @endif

                    @if($req->status === 'pending' && $req->requested_by === auth()->id())
                    <form method="POST" action="{{ route('store.internal-requests.cancel', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-gray-400 text-white px-2 py-1 rounded-lg hover:bg-gray-500">Cancel</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <x-empty-state table colspan="7" title="No requests found" message="Internal requests will appear here once they are submitted." />
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $requests->withQueryString()->links() }}</div>
</div>
@endsection
