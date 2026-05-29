@php use App\Helpers\CurrencyHelper; @endphp
@extends('layouts.app')

@section('title', 'Edit ' . $localPurchaseOrder->lpo_number)
@section('page-title', 'Local Purchase Orders')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-secondary">Edit {{ $localPurchaseOrder->lpo_number }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Modify this local purchase order</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($localPurchaseOrder->status === 'draft') bg-gray-100 text-gray-700
                    @elseif($localPurchaseOrder->status === 'rejected') bg-red-100 text-red-700
                    @endif">
                    {{ ucfirst($localPurchaseOrder->status) }}
                </span>
            </div>
        </div>

        @if($localPurchaseOrder->status === 'rejected' && $localPurchaseOrder->rejection_reason)
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-800">Rejection Reason</p>
                    <p class="text-sm text-red-700 mt-1">{{ $localPurchaseOrder->rejection_reason }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('procurement.lpo.update', $localPurchaseOrder) }}" class="p-6" id="lpo-form">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Supplier Information -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Supplier Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Select Supplier</label>
                            <select name="supplier_id" id="supplier_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('supplier_id') border-red-500 @enderror">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $localPurchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="supplier_name_manual" class="block text-sm font-medium text-gray-700 mb-2">Or Enter Manually</label>
                            <input type="text" name="supplier_name_manual" id="supplier_name_manual"
                                value="{{ old('supplier_name_manual', $localPurchaseOrder->supplier_name_manual) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('supplier_name_manual') border-red-500 @enderror"
                                placeholder="For one-time suppliers">
                            @error('supplier_name_manual')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Order Details -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Order Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" id="order_date"
                                value="{{ old('order_date', $localPurchaseOrder->order_date->format('Y-m-d')) }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('order_date') border-red-500 @enderror">
                            @error('order_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Delivery</label>
                            <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                                value="{{ old('expected_delivery_date', $localPurchaseOrder->expected_delivery_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('expected_delivery_date') border-red-500 @enderror">
                            @error('expected_delivery_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Additional notes...">{{ old('notes', $localPurchaseOrder->notes) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="terms" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                            <textarea name="terms" id="terms" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Payment terms, delivery conditions...">{{ old('terms', $localPurchaseOrder->terms) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Items -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-secondary flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Order Items
                        </h3>
                        <button type="button" onclick="addItem()"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            + Add Item
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        @foreach($localPurchaseOrder->items as $idx => $item)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 item-row" data-index="{{ $idx }}">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                                    <select name="items[{{ $idx }}][product_id]"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary product-select"
                                        onchange="fillProductDetails({{ $idx }})">
                                        <option value="">-- Select Product --</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-unit="{{ $product->unit }}"
                                            data-cost="{{ $product->cost_price }}"
                                            {{ old('items.'.$idx.'.product_id', $item->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Item Name *</label>
                                    <input type="text" name="items[{{ $idx }}][item_name]" required
                                        value="{{ old('items.'.$idx.'.item_name', $item->item_name) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-name"
                                        placeholder="Item name">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                                    <input type="text" name="items[{{ $idx }}][unit]" required
                                        value="{{ old('items.'.$idx.'.unit', $item->unit) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-unit"
                                        placeholder="pcs">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                                    <input type="number" name="items[{{ $idx }}][quantity]" step="0.001" required min="0.001"
                                        value="{{ old('items.'.$idx.'.quantity', $item->quantity) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-qty"
                                        placeholder="0"
                                        onchange="calculateItemTotal({{ $idx }})">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price *</label>
                                    <input type="number" name="items[{{ $idx }}][unit_price]" step="0.01" required min="0.01"
                                        value="{{ old('items.'.$idx.'.unit_price', $item->unit_price) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-price"
                                        placeholder="0.00"
                                        onchange="calculateItemTotal({{ $idx }})">
                                </div>
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button" onclick="removeItem({{ $idx }})"
                                        class="w-full px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @error('items')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Total Summary -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Subtotal</div>
                            <div class="text-2xl font-bold text-secondary" id="subtotal-display">{{ CurrencyHelper::formatCurrency($localPurchaseOrder->subtotal ?? 0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Tax (18%)</div>
                            <div class="text-2xl font-bold text-secondary" id="tax-display">{{ CurrencyHelper::formatCurrency($localPurchaseOrder->tax_amount ?? 0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Grand Total</div>
                            <div class="text-3xl font-extrabold text-primary" id="total-display">{{ CurrencyHelper::formatCurrency($localPurchaseOrder->grand_total ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('procurement.lpo.show', $localPurchaseOrder) }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                @if($localPurchaseOrder->status === 'rejected')
                <button type="submit" name="resubmit" value="1"
                    onclick="document.getElementById('resubmit-flag').value='1'"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all">
                    Save &amp; Resubmit for Approval
                </button>
                @endif
                <button type="submit"
                    onclick="document.getElementById('resubmit-flag').value='0'"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    Update LPO
                </button>
                <input type="hidden" name="resubmit" id="resubmit-flag" value="0">
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = {{ $localPurchaseOrder->items->count() }};
const products = @json($products);
const currencySymbol = '{{ CurrencyHelper::getCurrencySymbol() }}';
const currencyPosition = '{{ CurrencyHelper::CURRENCIES[CurrencyHelper::getDefaultCurrency()]["position"] ?? "before" }}';

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 item-row" data-index="${itemIndex}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                    <select name="items[${itemIndex}][product_id]"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary product-select"
                        onchange="fillProductDetails(${itemIndex})">
                        <option value="">-- Select Product --</option>
                        ${products.map(p => `<option value="${p.id}" data-name="${p.name}" data-unit="${p.unit}" data-cost="${p.cost_price}">${p.name}</option>`).join('')}
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Item Name *</label>
                    <input type="text" name="items[${itemIndex}][item_name]" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-name"
                        placeholder="Item name">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                    <input type="text" name="items[${itemIndex}][unit]" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-unit"
                        placeholder="pcs">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="items[${itemIndex}][quantity]" step="0.001" required min="0.001"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-qty"
                        placeholder="0"
                        onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price *</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" required min="0.01"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-price"
                        placeholder="0.00"
                        onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeItem(${itemIndex})"
                        class="w-full px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        ✕
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function fillProductDetails(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    const select = row.querySelector('.product-select');
    const option = select.options[select.selectedIndex];

    if (option.value) {
        row.querySelector('.item-name').value = option.dataset.name;
        row.querySelector('.item-unit').value = option.dataset.unit;
        row.querySelector('.item-price').value = option.dataset.cost;
        calculateItemTotal(index);
    }
}

function calculateItemTotal(index) {
    calculateGrandTotal();
}

function formatMoney(amount) {
    const formatted = amount.toFixed(2);
    if (currencyPosition === 'before') {
        return currencySymbol + formatted;
    }
    return formatted + ' ' + currencySymbol;
}

function calculateGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        subtotal += qty * price;
    });

    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
    document.getElementById('tax-display').textContent = formatMoney(tax);
    document.getElementById('total-display').textContent = formatMoney(total);
}

function removeItem(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    row.remove();
    calculateGrandTotal();
}

document.addEventListener('DOMContentLoaded', function() {
    calculateGrandTotal();
});
</script>
@endsection
