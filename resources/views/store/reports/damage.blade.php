@extends('layouts.app')

@section('title', 'Damage Report')
@section('page-title', 'Damage Report')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Damage Report</h1>
    <div class="flex gap-2">
        <a href="{{ route('store.reports.stock-snapshot') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Stock Snapshot</a>
        <a href="{{ route('store.reports.movements') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Movement Log</a>
    </div>
</div>

<form method="GET" class="flex flex-wrap gap-3 mb-6 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" name="from" value="{{ request('from') }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" name="to" value="{{ request('to') }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Location</label>
        <select name="location_id" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
            <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700">Filter</button>
    <a href="{{ route('store.reports.damage') }}" class="text-gray-400 text-sm hover:text-gray-600 px-2 py-2">Reset</a>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Location</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty Lost</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Est. Cost</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Reason</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Notes</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Recorded By</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($damages as $d)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $d->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $d->product->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $d->location->name }}</td>
                <td class="px-4 py-3 text-right text-red-600 font-medium">{{ $d->quantity }}</td>
                <td class="px-4 py-3 text-right text-gray-500">
                    {{ number_format($d->quantity * ($d->product->cost_price ?? 0), 0) }} TZS
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $d->reason ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ \Illuminate\Support\Str::limit($d->notes, 40) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $d->actor->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No damage records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $damages->withQueryString()->links() }}</div>
</div>

{{-- Summary --}}
@if($damages->count())
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Total Damage Incidents</p>
        <p class="text-2xl font-bold text-red-600">{{ $totalDamageCount }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Estimated Total Loss</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($totalDamageCost, 0) }} TZS</p>
    </div>
</div>
@endif
@endsection
