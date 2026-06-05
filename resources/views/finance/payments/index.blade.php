@extends('layouts.app')
@section('title', 'Payments')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Payments</h1>
</div>

{{-- Today's summary --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Today Total</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">@currency($summary['total_usd'], 'USD')</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Cash</p>
        <p class="text-2xl font-bold text-green-600 mt-1">@currency($summary['cash_usd'], 'USD')</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Card</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">@currency($summary['card_usd'], 'USD')</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Type</label>
            <select name="type" class="border rounded px-3 py-1.5 text-sm">
                <option value="">All Types</option>
                <option value="checkout" {{ request('type') === 'checkout' ? 'selected' : '' }}>Checkout</option>
                <option value="walkin" {{ request('type') === 'walkin' ? 'selected' : '' }}>Walk-in</option>
                <option value="advance" {{ request('type') === 'advance' ? 'selected' : '' }}>Advance</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Method</label>
            <select name="method" class="border rounded px-3 py-1.5 text-sm">
                <option value="">All Methods</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>Card</option>
                <option value="mobile_money" {{ request('method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Currency</label>
            <select name="currency" class="border rounded px-3 py-1.5 text-sm">
                <option value="">All</option>
                <option value="USD" {{ request('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                <option value="TZS" {{ request('currency') === 'TZS' ? 'selected' : '' }}>TZS</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-3 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-3 py-1.5 text-sm">
        </div>
        <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Filter</button>
        <a href="{{ route('finance.payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-1.5">Reset</a>
    </form>
</div>

{{-- Payments table --}}
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-500">Payment #</th>
                <th class="px-4 py-3 text-left text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-gray-500">Method</th>
                <th class="px-4 py-3 text-left text-gray-500">Currency</th>
                <th class="px-4 py-3 text-right text-gray-500">Amount</th>
                <th class="px-4 py-3 text-right text-gray-500">USD Equiv.</th>
                <th class="px-4 py-3 text-left text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-gray-500">By</th>
                <th class="px-4 py-3 text-left text-gray-500">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($payments as $payment)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $payment->payment_number }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $payment->payment_type === 'checkout' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($payment->payment_type) }}
                    </span>
                </td>
                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $payment->method) }}</td>
                <td class="px-4 py-3">{{ $payment->currency }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ number_format($payment->amount, 2) }}</td>
                <td class="px-4 py-3 text-right">@currency($payment->amount_usd, 'USD')</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $payment->createdBy?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $payment->created_at?->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <x-empty-state table colspan="9" title="No payments found" message="Try a different date range or payment filter." />
            @endforelse
        </tbody>
    </table>

    @if($payments->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $payments->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
