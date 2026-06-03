{{-- resources/views/procurement/grn/create.blade.php --}}
@php use App\Helpers\CurrencyHelper; @endphp
@extends('layouts.app')

@section('title', 'Create GRN')
@section('page-title', 'Goods Received Notes')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-secondary">Create Goods Received Note</h2>
            <p class="text-sm text-gray-500 mt-1">Record goods received from supplier</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('procurement.grn.store') }}" class="p-6" id="grn-form">
            @csrf

            <div class="space-y-6">
                <!-- Select LPO -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Select Purchase Order
                    </h3>

                    <div>
                        <label for="lpo_id" class="block text-sm font-medium text-gray-700 mb-2">
                            LPO Number <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="lpo_id" 
                            id="lpo_id"
                            required
                            onchange="loadLpoItems()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('lpo_id') border-red-500 @enderror">
                            <option value="">-- Select LPO --</option>
                            @foreach($lpos as $lpo)
                            <option value="{{ $lpo->id }}" 
                                    {{ (request('lpo_id') == $lpo->id || old('lpo_id') == $lpo->id) ? 'selected' : '' }}
                                    data-lpo='@json($lpo)'>
                                {{ $lpo->lpo_number }} - {{ $lpo->supplierName }} ({{ $lpo->order_date->format('M d, Y') }})
                            </option>
                            @endforeach
                        </select>
                        @error('lpo_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Delivery Details -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Delivery Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Received Date -->
                        <div>
                            <label for="received_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Received Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="received_date" 
                                id="received_date"
                                value="{{ old('received_date', now()->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </div>

                        <!-- Vehicle -->
                        <div>
                            <label for="delivery_vehicle" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Plate
                            </label>
                            <input 
                                type="text" 
                                name="delivery_vehicle" 
                                id="delivery_vehicle"
                                value="{{ old('delivery_vehicle') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="e.g., T123ABC">
                        </div>

                        <!-- Driver -->
                        <div>
                            <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver Name
                            </label>
                            <input 
                                type="text" 
                                name="driver_name" 
                                id="driver_name"
                                value="{{ old('driver_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Driver's name">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-3">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea 
                                name="notes" 
                                id="notes"
                                rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Delivery condition, damages, etc...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Items -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Received Items
                    </h3>

                    <div id="items-container" class="space-y-3">
                        <div class="text-center text-gray-500 py-8">
                            Please select an LPO to load items
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        <span class="font-semibold text-amber-600">Short delivery?</span>
                        Enter <code class="px-1 py-0.5 bg-gray-100 rounded">0</code> in the Received field for any items the supplier did not deliver. They will be recorded in the GRN as a shortage but will not add to stock.
                    </p>
                </div>

                <!-- Total Summary -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Subtotal</div>
                            <div class="text-2xl font-bold text-secondary" id="subtotal-display">{{ CurrencyHelper::formatCurrency(0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Tax (18%)</div>
                            <div class="text-2xl font-bold text-secondary" id="tax-display">{{ CurrencyHelper::formatCurrency(0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Grand Total</div>
                            <div class="text-3xl font-extrabold text-primary" id="total-display">{{ CurrencyHelper::formatCurrency(0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('procurement.grn.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    Create GRN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 0;
const currencySymbol = '{{ CurrencyHelper::getCurrencySymbol() }}';
const currencyPosition = '{{ CurrencyHelper::CURRENCIES[CurrencyHelper::getDefaultCurrency()]["position"] ?? "before" }}';

function formatMoney(amount) {
    const formatted = amount.toFixed(2);
    if (currencyPosition === 'before') {
        return currencySymbol + formatted;
    }
    return formatted + ' ' + currencySymbol;
}

function loadLpoItems() {
    const select = document.getElementById('lpo_id');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        document.getElementById('items-container').innerHTML = '<div class="text-center text-gray-500 py-8">Please select an LPO to load items</div>';
        return;
    }
    
    const lpo = JSON.parse(option.dataset.lpo);
    const container = document.getElementById('items-container');
    container.innerHTML = '';
    
    itemIndex = 0;
    lpo.items.forEach(item => {
        addItemRow(item);
    });
    
    calculateGrandTotal();
}

function addItemRow(lpoItem) {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200" data-index="${itemIndex}">
            <input type="hidden" name="items[${itemIndex}][lpo_item_id]" value="${lpoItem.id}">
            <input type="hidden" name="items[${itemIndex}][product_id]" value="${lpoItem.product_id || ''}">
            <input type="hidden" name="items[${itemIndex}][item_name]" value="${lpoItem.item_name}">
            <input type="hidden" name="items[${itemIndex}][unit]" value="${lpoItem.unit}">
            <input type="hidden" name="items[${itemIndex}][quantity_ordered]" value="${lpoItem.quantity}">
            <input type="hidden" name="items[${itemIndex}][unit_price]" value="${lpoItem.unit_price}">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-4">
                    <div class="text-sm font-semibold text-secondary">${lpoItem.item_name}</div>
                    <div class="text-xs text-gray-500">${lpoItem.product?.sku || 'Manual entry'}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-gray-500 mb-1">Unit</div>
                    <div class="text-sm text-secondary">${lpoItem.unit}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-gray-500 mb-1">Ordered</div>
                    <div class="text-sm font-medium text-secondary">${parseFloat(lpoItem.quantity).toFixed(2)}</div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Received *</label>
                    <input
                        type="number"
                        name="items[${itemIndex}][quantity_received]"
                        step="0.001"
                        required
                        min="0"
                        value="${lpoItem.quantity}"
                        placeholder="0 for short"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-received"
                        onchange="calculateGrandTotal(); markShortDelivery(this);">
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-gray-500 mb-1">Unit Price</div>
                    <div class="text-sm font-bold text-secondary">${formatMoney(parseFloat(lpoItem.unit_price))}</div>
                </div>
            </div>
            <div class="mt-2">
                <input 
                    type="text" 
                    name="items[${itemIndex}][notes]" 
                    placeholder="Item notes (damages, shortages, etc.)"
                    class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function calculateGrandTotal() {
    let subtotal = 0;

    document.querySelectorAll('[data-index]').forEach((row, idx) => {
        const qtyReceived = parseFloat(row.querySelector('.item-received').value) || 0;
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
        subtotal += qtyReceived * unitPrice;
    });

    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
    document.getElementById('tax-display').textContent = formatMoney(tax);
    document.getElementById('total-display').textContent = formatMoney(total);
}

function markShortDelivery(input) {
    const row = input.closest('[data-index]');
    const isShort = parseFloat(input.value) === 0;
    if (isShort) {
        row.classList.add('border-amber-400', 'bg-amber-50');
        row.classList.remove('border-gray-200', 'bg-gray-50');
    } else {
        row.classList.remove('border-amber-400', 'bg-amber-50');
        row.classList.add('border-gray-200', 'bg-gray-50');
    }
}

// Auto-load if LPO is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const lpoSelect = document.getElementById('lpo_id');
    if (lpoSelect.value) {
        loadLpoItems();
    }
});
</script>
@endsection
