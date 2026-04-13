@extends('layouts.app')

@section('title', 'Movement Log')
@section('page-title', 'Movement Log')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Movement Log</h1>
    <div class="flex gap-2">
        <a href="{{ route('store.reports.stock-snapshot') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Stock Snapshot</a>
        <a href="{{ route('store.reports.damage') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm hover:bg-gray-50">Damage Report</a>
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
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select name="type" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <option value="">All Types</option>
            @foreach(['restock','damage','adjustment','transfer_in','transfer_out','internal_use'] as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
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
    <a href="{{ route('store.reports.movements') }}" class="text-gray-400 text-sm hover:text-gray-600 px-2 py-2">Reset</a>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date/Time</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Location</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Type</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">Before</th>
                <th class="px-4 py-3 text-right text-gray-600 font-semibold">After</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Reference</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">By</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($movements as $m)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $m->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-3 font-medium">{{ $m->product->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $m->location->name }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if(in_array($m->type, ['restock','transfer_in'])) bg-green-100 text-green-700
                        @elseif(in_array($m->type, ['damage','transfer_out','internal_use'])) bg-red-100 text-red-600
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ ucwords(str_replace('_',' ', $m->type)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-mono {{ in_array($m->type, ['restock', 'transfer_in']) ? 'text-green-600' : 'text-red-600' }}">
                    {{ in_array($m->type, ['restock', 'transfer_in']) ? '+' : '-' }}{{ $m->quantity }}
                </td>
                <td class="px-4 py-3 text-right text-gray-400">{{ $m->quantity_before }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $m->quantity_after }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $m->reference ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $m->actor->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">No movements found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $movements->withQueryString()->links() }}</div>
</div>
@endsection
