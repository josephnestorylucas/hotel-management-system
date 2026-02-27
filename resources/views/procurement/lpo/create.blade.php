{{-- resources/views/procurement/lpo/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Purchase Order')
@section('page-title', 'Local Purchase Orders')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-secondary">Create Purchase Order</h2>
            <p class="text-sm text-gray-500 mt-1">Create a new local purchase order</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('procurement.lpo.store') }}" class="p-6" id="lpo-form">
            @csrf

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
                        <!-- Supplier -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Supplier
                            </label>
                            <select 
                                name="supplier_id" 
                                id="supplier_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('supplier_id') border-red-500 @enderror">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Manual Supplier Name -->
                        <div>
                            <label for="supplier_name_manual" class="block text-sm font-medium text-gray-700 mb-2">
                                Or Enter Manually
                            </label>
                            <input 
                                type="text" 
                                name="supplier_name_manual" 
                                id="supplier_name_manual"
                                value="{{ old('supplier_name_manual') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('supplier_name_manual') border-red-500 @enderror"
                                placeholder="For one-time suppliers">
                            @error('supplier_name_manual')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                        <!-- Order Date -->
                        <div>
                            <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Order Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="order_date" 
                                id="order_date"
                                value="{{ old('order_date', now()->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('order_date') border-red-500 @enderror">
                            @error('order_date')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expected Delivery Date -->
                        <div>
                            <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Expected Delivery
                            </label>
                            <input 
                                type="date" 
                                name="expected_delivery_date" 
                                id="expected_delivery_date"
                                value="{{ old('expected_delivery_date') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('expected_delivery_date') border-red-500 @enderror">
                            @error('expected_delivery_date')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea 
                                name="notes" 
                                id="notes"
                                rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Terms -->
                        <div class="md:col-span-2">
                            <label for="terms" class="block text-sm font-medium text-gray-700 mb-2">
                                Terms & Conditions
                            </label>
                            <textarea 
                                name="terms" 
                                id="terms"
                                rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Payment terms, delivery conditions...">{{ old('terms') }}</textarea>
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
                        <button 
                            type="button" 
                            onclick="addItem()"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            + Add Item
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        <!-- Items will be added here dynamically -->
                    </div>

                    @error('items')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Summary -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Subtotal</div>
                            <div class="text-2xl font-bold text-secondary" id="subtotal-display">$0.00</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Tax (18%)</div>
                            <div class="text-2xl font-bold text-secondary" id="tax-display">$0.00</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Grand Total</div>
                            <div class="text-3xl font-extrabold text-primary" id="total-display">$0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('procurement.lpo.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    Create LPO
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 0;
const products = @json($products);

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 item-row" data-index="${itemIndex}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                    <select 
                        name="items[${itemIndex}][product_id]" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary product-select"
                        onchange="fillProductDetails(${itemIndex})">
                        <option value="">-- Select Product --</option>
                        ${products.map(p => `<option value="${p.id}" data-name="${p.name}" data-unit="${p.unit}" data-cost="${p.cost_price}">${p.name}</option>`).join('')}
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Item Name *</label>
                    <input 
                        type="text" 
                        name="items[${itemIndex}][item_name]" 
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-name"
                        placeholder="Item name">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                    <input 
                        type="text" 
                        name="items[${itemIndex}][unit]" 
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-unit"
                        placeholder="pcs">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                    <input 
                        type="number" 
                        name="items[${itemIndex}][quantity]" 
                        step="0.001"
                        required
                        min="0.001"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-qty"
                        placeholder="0"
                        onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price *</label>
                    <input 
                        type="number" 
                        name="items[${itemIndex}][unit_price]" 
                        step="0.01"
                        required
                        min="0.01"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-price"
                        placeholder="0.00"
                        onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button 
                        type="button" 
                        onclick="removeItem(${itemIndex})"
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
    const row = document.querySelector(`[data-index="${index}"]`);
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    
    calculateGrandTotal();
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
    
    document.getElementById('subtotal-display').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-display').textContent = '$' + tax.toFixed(2);
    document.getElementById('total-display').textContent = '$' + total.toFixed(2);
}

function removeItem(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    row.remove();
    calculateGrandTotal();
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endsection