@extends('layouts.app')

@section('title', $item->name)
@section('page-title', $item->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="text-3xl font-bold {{ $item->isLow() ? 'text-red-600' : 'text-gray-800' }}">
                    {{ number_format($item->current_quantity, 2) }}
                </p>
                <p class="text-gray-500">{{ $item->unit }}</p>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $item->isLow() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $item->isLow() ? 'LOW STOCK' : 'OK' }}
                </span>
                <p class="text-xs text-gray-400 mt-1">Min: {{ number_format($item->minimum_quantity, 2) }} {{ $item->unit }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Record Movement</h3>
        <form method="POST" action="{{ route('manager.kitchen-stock.record-movement', $item) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="movement_type" required class="w-full border-gray-300 rounded-lg px-3 py-2">
                        <option value="purchase">+ Purchase</option>
                        <option value="damage">- Damage</option>
                        <option value="transfer">- Transfer</option>
                        <option value="adjustment">= Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" value="1" step="0.01" min="0.01" required class="w-full border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" class="w-full border-gray-300 rounded-lg px-3 py-2" placeholder="Reason">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">Record</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Movement History</h3>
        </div>
        @if($movements->isEmpty())
            <p class="p-6 text-center text-gray-500 text-sm">No movements recorded yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($movements as $m)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                    {{ $m->movement_type === 'purchase' ? 'bg-green-100 text-green-700' :
                                       ($m->movement_type === 'damage' ? 'bg-red-100 text-red-700' :
                                       ($m->movement_type === 'transfer' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
                                    {{ ucfirst($m->movement_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap {{ $m->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $m->quantity >= 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }}
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $m->notes }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $m->recordedBy?->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection
