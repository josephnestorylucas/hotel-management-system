@extends('layouts.app')
@section('title', 'Guest Folio — ' . ($booking->guest_name ?? $booking->id))
@section('page-title', 'Guest Folio')

@php
    use App\Helpers\CurrencyHelper;
@endphp

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <p class="text-sm text-gray-400 mt-1">
            {{ $booking->guest_name ?? 'Guest' }}
            · Room {{ $booking->room->room_number ?? '—' }}
            · Receipt: {{ $checkout->receipt_number }}
        </p>
    </div>
    <span class="px-3 py-1 rounded-full text-sm font-medium
        {{ $checkout->status === 'completed' ? 'bg-green-100 text-green-700' : ($checkout->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
        {{ ucfirst($checkout->status) }}
    </span>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Charges by type --}}
    <div class="col-span-2 space-y-4">
        @foreach($chargesByType as $type => $items)
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b">
                <h2 class="font-semibold text-gray-700">
                    {{ $items->first()->charge_type_label }}
                    <span class="text-gray-400 font-normal text-sm ml-2">({{ $items->count() }} item(s))</span>
                </h2>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="border-b">
                    <th class="px-4 py-2 text-left text-gray-500">Date</th>
                    <th class="px-4 py-2 text-left text-gray-500">Description</th>
                    <th class="px-4 py-2 text-right text-gray-500">{{ CurrencyHelper::getCurrencySymbol('USD') }}</th>
                    <th class="px-4 py-2 text-right text-gray-500">{{ CurrencyHelper::getCurrencySymbol('TZS') }}</th>
                    <th class="px-4 py-2 text-center text-gray-500">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($items as $charge)
                    <tr>
                        <td class="px-4 py-2 text-gray-400 text-xs">{{ $charge->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">
                            {{ $charge->description }}
                            @if($charge->charge_type === 'laundry' && $charge->laundryOrder)
                                <a href="{{ route('laundry.orders.show', $charge->laundryOrder) }}"
                                   class="text-xs text-blue-500 hover:underline ml-1" target="_blank">
                                    (view order)
                                </a>
                            @elseif($charge->charge_type === 'restaurant' && $charge->order)
                                <a href="{{ route('restaurant.orders.show', $charge->order) }}"
                                   class="text-xs text-blue-500 hover:underline ml-1" target="_blank">
                                    (view order)
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-medium">{{ CurrencyHelper::formatCurrency($charge->amount, 'USD', false) }}</td>
                        <td class="px-4 py-2 text-right text-gray-500">
                            @if($charge->amount_tzs)
                                {{ CurrencyHelper::formatCurrency($charge->amount_tzs, 'TZS', false) }}
                            @else
                                {{ CurrencyHelper::formatCurrency($charge->amount * $exchangeRate, 'TZS', false) }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $charge->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ ucfirst($charge->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-medium">
                        <td colspan="2" class="px-4 py-2 text-right text-gray-600">Subtotal</td>
                        <td class="px-4 py-2 text-right">{{ CurrencyHelper::formatCurrency($items->sum('amount'), 'USD', false) }}</td>
                        <td class="px-4 py-2 text-right text-gray-500">
                            @php
                                $totalTzs = $items->sum(function($c) {
                                    return $c->amount_tzs ?? ($c->amount * $exchangeRate);
                                });
                            @endphp
                            {{ CurrencyHelper::formatCurrency($totalTzs, 'TZS', false) }}
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- Manually add charge --}}
        @if(in_array($checkout->status, ['pending', 'draft']))
        <div class="bg-white rounded shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Add Charge to Folio</h3>
            <form method="POST" action="{{ route('finance.checkout.add-charge', $checkout) }}">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <select name="charge_type" required class="border rounded px-3 py-2 text-sm">
                        @foreach(['laundry','restaurant','room_service','damage','minibar','extra_bed','conference','store'] as $ct)
                        <option value="{{ $ct }}">{{ ucwords(str_replace('_', ' ', $ct)) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="description" placeholder="Description" required
                           class="border rounded px-3 py-2 text-sm">
                    <div class="flex gap-2">
                        <input type="number" name="amount" placeholder="Amount ({{ CurrencyHelper::getCurrencySymbol('USD') }})" step="0.01" min="0.01" required
                               class="border rounded px-3 py-2 text-sm flex-1">
                        <button class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">Add</button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Totals + payment --}}
    <div class="space-y-4">
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Bill Summary</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total Charges</dt>
                    <dd>{{ CurrencyHelper::formatUSD($checkout->total_charges_usd) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Discount</dt>
                    <dd class="text-red-500">- {{ CurrencyHelper::formatUSD($checkout->discount_usd) }}</dd>
                </div>
                <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                    <dt>Grand Total</dt>
                    <dd>{{ CurrencyHelper::formatUSD($checkout->grand_total_usd) }}</dd>
                </div>
                <div class="flex justify-between text-gray-500">
                    <dt>In TZS (rate: {{ number_format($exchangeRate, 0) }})</dt>
                    <dd>{{ CurrencyHelper::formatTZS($checkout->grand_total_tzs) }}</dd>
                </div>
            </dl>
        </div>

        @if(in_array($checkout->status, ['pending', 'draft']))
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Process Payment</h2>
            <form method="POST" action="{{ route('finance.checkout.process', $checkout) }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" id="pmMethod" required
                                class="w-full border rounded px-3 py-2 text-sm"
                                onchange="toggleSplitFields(this.value)">
                            <option value="cash_usd">Cash — {{ CurrencyHelper::getCurrencySymbol('USD') }}</option>
                            <option value="cash_tzs">Cash — {{ CurrencyHelper::getCurrencySymbol('TZS') }}</option>
                            <option value="card_usd">Card — {{ CurrencyHelper::getCurrencySymbol('USD') }}</option>
                            <option value="card_tzs">Card — {{ CurrencyHelper::getCurrencySymbol('TZS') }}</option>
                            <option value="split">Split Payment</option>
                        </select>
                    </div>

                    <div id="split-fields" class="hidden space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Cash {{ CurrencyHelper::getCurrencySymbol('USD') }}</label>
                                <input type="number" name="cash_usd_amount" step="0.01" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Card {{ CurrencyHelper::getCurrencySymbol('USD') }}</label>
                                <input type="number" name="card_usd_amount" step="0.01" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Cash {{ CurrencyHelper::getCurrencySymbol('TZS') }}</label>
                                <input type="number" name="cash_tzs_amount" step="1" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Card {{ CurrencyHelper::getCurrencySymbol('TZS') }}</label>
                                <input type="number" name="card_tzs_amount" step="1" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount ({{ CurrencyHelper::getCurrencySymbol('USD') }})</label>
                        <input type="number" name="discount_usd" value="0" step="0.01" min="0"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2.5 rounded hover:bg-green-700 font-medium">
                        Complete Checkout
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('finance.checkout.draft', $checkout) }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full bg-gray-200 text-gray-700 py-2.5 rounded hover:bg-gray-300 font-medium">
                    Save as Draft
                </button>
            </form>
                </div>
            </form>
        </div>
        @endif

        @if($checkout->status === 'completed')
        <div class="space-y-2">
            <a href="{{ route('receipts.checkout', $checkout) }}" target="_blank"
               class="block text-center bg-blue-600 text-white py-2.5 rounded hover:bg-blue-700 font-medium">
                Print Receipt
            </a>
            <a href="{{ route('finance.receipt.guest', $checkout) }}" target="_blank"
               class="block text-center border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-50 text-sm">
                Print Detailed Folio
            </a>
        </div>
        @endif
    </div>
</div>

<script>
function toggleSplitFields(val) {
    document.getElementById('split-fields').classList.toggle('hidden', val !== 'split');
}
</script>
@endsection
