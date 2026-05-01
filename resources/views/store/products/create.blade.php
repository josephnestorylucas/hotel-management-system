@extends('layouts.app')

@section('title', 'Create Product')
@section('page-title', 'Create Product')

@section('content')
<div class="max-w-2xl" x-data="{ productType: '{{ old('product_type', 'normal') }}' }">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Product</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('store.products.store') }}" x-data="productVarieties()">
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
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                            <label class="text-xs text-gray-500">Price Override (TZS)</label>
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
</script>
@endpush
@endsection
