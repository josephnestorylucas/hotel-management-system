@php use App\Helpers\CurrencyHelper; @endphp

@extends('layouts.app')

@section('title', 'Create Product')
@section('page-title', 'Create Product')

@section('content')
<div class="max-w-2xl" x-data="{ productType: '{{ old('product_type', 'normal') }}' }">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Product</h1>

    {{-- Barcode Scanner Section --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6" id="barcode-scanner-section">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Barcode Scanner</h2>
                <p class="text-xs text-gray-500">Scan a barcode to auto-fill product details or enter manually below</p>
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

        {{-- Local DB Result --}}
        <div id="scan-result-local" class="hidden mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold text-green-800">Product Found Locally</span>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                <div><span class="text-gray-500">Name:</span> <span id="local-name" class="font-medium"></span></div>
                <div><span class="text-gray-500">Cost:</span> <span id="local-cost" class="font-medium"></span></div>
                <div><span class="text-gray-500">Selling:</span> <span id="local-selling" class="font-medium"></span></div>
                <div><span class="text-gray-500">Stock:</span> <span id="local-stock" class="font-medium"></span></div>
            </div>
            <button type="button" id="local-use-btn" onclick="useLocalProduct()"
                    class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-700 font-medium">
                Use This Product
            </button>
            <input type="hidden" id="local-product-id" name="product_id" value="">
        </div>

        {{-- Open Food Facts Result --}}
        <div id="scan-result-online" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <span class="font-semibold text-blue-800">Product Found Online</span>
            </div>
            <form id="online-product-form" onsubmit="return false;">
                <input type="hidden" id="online-barcode" name="barcode" value="">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Product Name</label>
                        <input type="text" id="online-name" name="name"
                               class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Brand</label>
                        <input type="text" id="online-brand" readonly
                               class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Cost Price *</label>
                        <input type="number" id="online-cost" name="cost_price" step="0.01" min="0.01"
                               class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Selling Price *</label>
                        <input type="number" id="online-selling" name="selling_price" step="0.01" min="0.01"
                               class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm" required>
                    </div>
                </div>
                <button type="button" onclick="saveOnlineProduct()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700 font-medium">
                    Save &amp; Use
                </button>
            </form>
        </div>

        {{-- Unknown Barcode --}}
        <div id="scan-result-unknown" class="hidden mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span class="font-semibold text-yellow-800">Barcode Not Found</span>
            </div>
            <p class="text-sm text-yellow-700 mb-3">Barcode <span id="unknown-barcode-display" class="font-mono font-bold"></span> was not found in the database or online. Fill details manually below.</p>
            <button type="button" onclick="showManualForm()"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-yellow-700 font-medium">
                Enter Manually
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('store.products.store') }}" x-data="productVarieties()" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <!-- Product Type -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Type *</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-colors"
                               :class="productType === 'normal' ? 'border-primary bg-blue-50 text-primary' : 'border-gray-200 text-gray-600'">
                            <input type="radio" name="product_type" value="normal" x-model="productType" class="sr-only">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Normal Product
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-colors"
                               :class="productType === 'bar' ? 'border-primary bg-blue-50 text-primary' : 'border-gray-200 text-gray-600'">
                            <input type="radio" name="product_type" value="bar" x-model="productType" class="sr-only">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Bar Product
                        </label>
                    </div>
                </div>

                <!-- POS Category (Bar only) -->
                <div class="col-span-2" x-show="productType === 'bar'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">POS Category *</label>
                    <select name="menu_category_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select bar category for POS...</option>
                        @foreach($menuCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('menu_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('menu_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">This product will appear in the Bar POS under the selected category.</p>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" id="form-product-name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" id="form-barcode" value="{{ old('barcode') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('barcode') border-red-400 @enderror">
                    @error('barcode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU (auto-generated if empty)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select unit...</option>
                        @foreach(['bottle','kg','piece','litre','pack','box','can','sachet','glass','ml'] as $unit)
                        <option value="{{ $unit }}" {{ old('unit') === $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select category...</option>
                        @foreach(['beverages','food','toiletries','cleaning','stationery','other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}"
                           step="0.01" min="0.01" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('cost_price') border-red-400 @enderror">
                    @error('cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price') }}"
                           step="0.01" min="0.01" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('selling_price') border-red-400 @enderror">
                    @error('selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}"
                           min="0" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <p class="text-xs text-gray-400 mt-1">Alert when stock drops to or below this number.</p>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Varieties -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Varieties (optional)</h3>
                    <button type="button" @click="addVariety()"
                            class="text-xs text-primary hover:text-blue-700 font-medium">+ Add Variety</button>
                </div>
                <p class="text-xs text-gray-400 mb-3">Add size/brand variations. Price override is optional — leave empty to use base price.</p>

                <template x-for="(v, idx) in varieties" :key="idx">
                    <div class="grid grid-cols-3 gap-2 mb-2 items-end">
                        <div>
                            <label class="text-xs text-gray-500">Label (e.g. 330ml)</label>
                            <input type="text" :name="'varieties['+idx+'][label]'" x-model="v.label"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Price Override ({{ CurrencyHelper::getCurrencySymbol('TZS') }})</label>
                            <input type="number" :name="'varieties['+idx+'][price]'" x-model.number="v.price"
                                   step="1" min="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                        </div>
                        <button type="button" @click="varieties.splice(idx, 1)"
                                class="text-red-500 hover:text-red-700 text-xs pb-1.5">Remove</button>
                    </div>
                </template>

                <input type="hidden" name="varieties" x-model="varietiesJson">
            </div>

            {{-- Product Image --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Product Image</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        @error('image_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Max 2MB. JPEG, PNG, or WebP.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Or Image URL (CDN)</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}"
                               placeholder="https://cdn.example.com/image.jpg"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        @error('image_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">External URL takes priority over uploaded file.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-primary text-white px-5 py-2 rounded-xl hover:bg-blue-700 text-sm font-medium">
                    Create Product
                </button>
                <a href="{{ route('store.products.index') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function productVarieties() {
    return {
        varieties: [],
        init() {
            this.syncJson();
            this.$watch('varieties', () => this.syncJson(), { deep: true });
        },
        addVariety() {
            this.varieties.push({ label: '', price: null });
        },
        syncJson() {
            const valid = this.varieties.filter(v => v.label && v.label.trim() !== '');
            this.varietiesJson = JSON.stringify(valid.map(v => ({
                label: v.label.trim(),
                price: v.price ? Number(v.price) : null,
            })));
        },
        get varietiesJson() {
            return this._json || '[]';
        },
        set varietiesJson(val) {
            this._json = val;
        },
    }
}

// Barcode Scanner
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
    const resultOnline = document.getElementById('scan-result-online');
    const resultUnknown = document.getElementById('scan-result-unknown');

    spinner.classList.remove('hidden');
    resultLocal.classList.add('hidden');
    resultOnline.classList.add('hidden');
    resultUnknown.classList.add('hidden');

    try {
        const response = await fetch(`/store/products/lookup?barcode=${encodeURIComponent(barcode)}`);
        const data = await response.json();
        spinner.classList.add('hidden');

        if (data.found && data.source === 'local') {
            document.getElementById('local-name').textContent = data.product.name;
            document.getElementById('local-cost').textContent = data.product.cost_price;
            document.getElementById('local-selling').textContent = data.product.selling_price;
            document.getElementById('local-stock').textContent = data.product.stock;
            document.getElementById('local-product-id').value = data.product.id;
            resultLocal.classList.remove('hidden');
        } else if (data.found && data.source === 'openfoodfacts') {
            document.getElementById('online-name').value = data.product.name;
            document.getElementById('online-brand').value = data.product.brand;
            document.getElementById('online-barcode').value = data.product.barcode;
            resultOnline.classList.remove('hidden');
        } else {
            document.getElementById('unknown-barcode-display').textContent = barcode;
            document.getElementById('form-barcode').value = barcode;
            resultUnknown.classList.remove('hidden');
        }
    } catch (err) {
        spinner.classList.add('hidden');
        document.getElementById('unknown-barcode-display').textContent = barcode;
        document.getElementById('form-barcode').value = barcode;
        resultUnknown.classList.remove('hidden');
    }
}

function useLocalProduct() {
    const productId = document.getElementById('local-product-id').value;
    if (productId) {
        window.location.href = `/store/products/${productId}`;
    }
}

async function saveOnlineProduct() {
    const form = document.getElementById('online-product-form');
    const name = document.getElementById('online-name').value.trim();
    const barcode = document.getElementById('online-barcode').value.trim();
    const costPrice = document.getElementById('online-cost').value;
    const sellingPrice = document.getElementById('online-selling').value;

    if (!name || !barcode || !costPrice || !sellingPrice) {
        alert('Please fill in all required fields.');
        return;
    }

    try {
        const response = await fetch('/store/products/store-scanned', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name, barcode, cost_price: costPrice, selling_price: sellingPrice }),
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = `/store/products/${data.product.id}`;
        } else {
            alert('Error saving product. Please try again.');
        }
    } catch (err) {
        alert('Error saving product. Please try again.');
    }
}

function showManualForm() {
    const barcode = document.getElementById('unknown-barcode-display').textContent;
    document.getElementById('form-barcode').value = barcode;
    document.getElementById('form-product-name').focus();
    document.getElementById('barcode-scanner-section').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush
@endsection
