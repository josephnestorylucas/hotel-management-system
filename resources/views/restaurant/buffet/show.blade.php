@extends('restaurant.layout')

@section('title', $buffetSale->sale_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-5">
        <h1 class="text-2xl font-bold text-gray-800">{{ $buffetSale->sale_number }}</h1>
        <p class="text-sm text-gray-500">{{ $buffetSale->package_name_snapshot }}</p>
    </div>

        <div class="bg-white rounded-lg shadow p-5 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ __('general.restaurant.buffet.fields.sale_type') }}:</span> {{ __('general.restaurant.buffet.sale_type.' . $buffetSale->sale_type) }}</div>
            <div><span class="text-gray-500">{{ __('general.status') }}:</span> {{ ucfirst($buffetSale->status) }}</div>
            <div><span class="text-gray-500">{{ __('general.restaurant.buffet.fields.pax') }}:</span> {{ $buffetSale->adults_count }}A / {{ $buffetSale->children_count }}C</div>
            <div><span class="text-gray-500">{{ __('general.total') }}:</span> {{ number_format($buffetSale->total_amount, 0) }} TZS</div>
            <div><span class="text-gray-500">{{ __('general.restaurant.buffet.fields.payment_method') }}:</span> {{ $buffetSale->payment_method ? ucfirst(str_replace('_', ' ', $buffetSale->payment_method)) : '—' }}</div>
            <div><span class="text-gray-500">{{ __('general.restaurant.buffet.fields.reference') }}:</span> {{ $buffetSale->payment_reference ?? '—' }}</div>
            @if($buffetSale->booking_id)
                <div class="col-span-2"><span class="text-gray-500">{{ __('general.restaurant.buffet.fields.booking') }}:</span> {{ $buffetSale->booking?->booking_number }} - {{ $buffetSale->booking?->guest_display_name }}</div>
            @endif
        </div>

    @if($buffetSale->status === 'settled' && $buffetSale->receipt)
        <div class="bg-white rounded-lg shadow p-5">
            <a href="{{ route('receipts.show', $buffetSale->receipt->uuid) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded text-sm">
                {{ __('general.restaurant.buffet.actions.print_receipt') }}
            </a>
            <a href="{{ route('receipts.reprint', $buffetSale->receipt->receipt_number) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded text-sm">
                {{ __('accountant.receipts.reprint') }}
            </a>
        </div>
    @endif

    @if($buffetSale->status === 'pending')
        <div class="bg-white rounded-lg shadow p-5 space-y-3">
            @if($buffetSale->sale_type === 'booking')
                <form method="POST" action="{{ route('restaurant.buffet.charge-booking', $buffetSale) }}">
                    @csrf
                    <button class="px-4 py-2 bg-primary text-white rounded text-sm">{{ __('general.restaurant.buffet.actions.charge_to_folio') }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('restaurant.buffet.settle-walkin', $buffetSale) }}" class="flex items-end gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ __('general.restaurant.buffet.fields.payment_method') }}</label>
                        <select name="payment_method" class="border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ __('general.restaurant.buffet.fields.reference') }}</label>
                        <input name="payment_reference" class="border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <button class="px-4 py-2 bg-green-600 text-white rounded text-sm">{{ __('general.restaurant.buffet.actions.settle_walkin') }}</button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection

