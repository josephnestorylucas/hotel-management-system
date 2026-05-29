@extends('layouts.app')

@section('title', 'Restock')
@section('page-title', 'Restock')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Restock Product</h1>

    {{-- Barcode Scanner --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6" id="barcode-scanner-section">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Scan Product Barcode</h2>
                <p class="text-xs text-gray-500">Scan a barcode to quickly select the product for restocking</p>
            </div>
        </div>

        <div class="relative">
            <input type="text" id="barcode-scanner-input"
                   placeholder="Scan barcode or type and press Enter..."
                   autocomplete="off" autofocus
                   class="w-full border-2 border-dashed border-gray-300 rounded-xl px-4 py-3 text-lg font-mono text-center focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
            <div id="scan-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        <div id="scan-result-local" class="hidden mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold text-green-800">Product Found</span>
            </div>
            <p class="text-sm text-green-700"><span id="local-product-name" class="font-medium"></span> — auto-selected below</p>
        </div>

        <div id="scan-result-unknown" class="hidden mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span class="font-semibold text-yellow-800">Product Not Found</span>
            </div>
            <p class="text-sm text-yellow-700">No product with this barcode. Please select manually below.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('store.stock.restock') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="product_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select product...</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') === $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->unit }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <select name="location_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select location...</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.001" min="0.001" required
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('quantity') border-red-400 @enderror">
                        @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost</label>
                        <input type="number" name="unit_cost" value="{{ old('unit_cost') }}" step="0.01" min="0"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-green-600 text-white px-5 py-2 rounded-xl hover:bg-green-700 text-sm font-medium">
                    Confirm Restock
                </button>
                <a href="{{ route('store.stock.levels') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let scanBuffer = '';
let scanTimeout;

document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
        if (e.target.id === 'barcode-scanner-input' && e.key === 'Enter') {
            e.preventDefault();
            processBarcode(e.target.value.trim());
        }
        return;
    }
    if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
        scanBuffer += e.key;
        clearTimeout(scanTimeout);
        scanTimeout = setTimeout(() => {
            if (scanBuffer.length >= 4) {
                processBarcode(scanBuffer);
            }
            scanBuffer = '';
        }, 100);
    }
});

async function processBarcode(barcode) {
    if (!barcode || barcode.length < 3) return;

    const spinner = document.getElementById('scan-spinner');
    const resultLocal = document.getElementById('scan-result-local');
    const resultUnknown = document.getElementById('scan-result-unknown');

    spinner.classList.remove('hidden');
    resultLocal.classList.add('hidden');
    resultUnknown.classList.add('hidden');

    try {
        const response = await fetch(`/store/products/lookup?barcode=${encodeURIComponent(barcode)}`);
        const data = await response.json();
        spinner.classList.add('hidden');

        if (data.found && data.source === 'local') {
            document.getElementById('local-product-name').textContent = data.product.name;
            resultLocal.classList.remove('hidden');

            const productSelect = document.querySelector('select[name="product_id"]');
            if (productSelect) {
                let found = false;
                for (let i = 0; i < productSelect.options.length; i++) {
                    if (productSelect.options[i].value === data.product.id) {
                        productSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    const opt = document.createElement('option');
                    opt.value = data.product.id;
                    opt.textContent = `${data.product.name} (${data.product.unit || 'piece'})`;
                    opt.selected = true;
                    productSelect.appendChild(opt);
                }
            }
        } else {
            resultUnknown.classList.remove('hidden');
        }
    } catch (err) {
        spinner.classList.add('hidden');
        resultUnknown.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
