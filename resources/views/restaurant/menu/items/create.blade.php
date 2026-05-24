{{-- resources/views/restaurant/menu/items/create.blade.php --}}
@extends('layouts.app')

@section('title', __('general.restaurant.menu.new_item'))
@section('page-title', __('general.restaurant.menu.new_item'))

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-6">{{ __('general.restaurant.menu.new_item') }}</h1>

    <form method="POST" action="{{ route('restaurant.menu.store') }}" x-data="menuItemForm(@js($products))" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.category') }} *</label>
            <select name="category_id" required
                    class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">{{ __('general.restaurant.placeholders.select_category') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ $cat->location->name }})
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Quick link to store product (beverage) --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
            <label class="block text-sm font-semibold text-blue-800 mb-2">Quick Link to Store Product (Optional)</label>
            <p class="text-xs text-blue-600 mb-3">Select a store product to auto-fill details and track stock automatically.</p>
            <select x-model="linkedProductId" @change="onProductLinked"
                    class="w-full border-blue-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-300">
                <option value="">— None (manual entry) —</option>
                @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-name="{{ $prod->name }}" data-unit="{{ $prod->unit }}" data-type="{{ $prod->product_type }}">
                    {{ $prod->name }} ({{ $prod->unit }}) {{ $prod->product_type === 'bar' ? '🍸' : '📦' }}
                </option>
                @endforeach
            </select>
            <input type="hidden" name="linked_product_id" x-model="linkedProductId">
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.name') }} *</label>
            <input type="text" name="name" x-model="itemName" required
                   class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                   placeholder="{{ __('general.restaurant.placeholders.item_name') }}">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Selling price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.base_price_tzs') }} *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price') }}" required min="1" step="1"
                   class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                   placeholder="15000">
            @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.description') }}</label>
            <textarea name="description" rows="2" x-model="itemDescription"
                      class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                      placeholder="{{ __('general.restaurant.placeholders.optional_description') }}">{{ old('description') }}</textarea>
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                   class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <p class="text-xs text-gray-400 mt-1">JPEG/PNG/WebP, max 2MB</p>
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Destination & Buffet --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination *</label>
                <select name="destination" required
                        class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="kitchen" {{ old('destination') === 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                    <option value="bar" {{ old('destination') === 'bar' ? 'selected' : '' }}>Bar</option>
                </select>
                @error('destination') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Is Buffet Item?</label>
                <select name="is_buffet"
                        class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="0" {{ old('is_buffet') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_buffet') === '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Location Tag</label>
                <input type="text" name="service_location_tag" value="{{ old('service_location_tag') }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                       placeholder="e.g. pool, terrace">
            </div>
        </div>

        {{-- Time Availability --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Available From (optional)</label>
                <input type="time" name="available_from" value="{{ old('available_from') }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-xs text-gray-400 mt-1">Leave empty for all-day availability</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Available Until (optional)</label>
                <input type="time" name="available_until" value="{{ old('available_until') }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-xs text-gray-400 mt-1">Leave empty for all-day availability</p>
            </div>
        </div>

        {{-- Option Groups --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.options.attach_groups') }}</label>
            <select name="option_group_ids[]" multiple class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm h-28 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @foreach($optionGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }} ({{ ucfirst($group->selection_type) }})</option>
                @endforeach
            </select>
        </div>

        {{-- Ingredients (collapsible) --}}
        <div x-data="{ showIngredients: false }" class="border border-gray-200 rounded-2xl overflow-hidden">
            <button type="button" @click="showIngredients = !showIngredients"
                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-700">
                <span>{{ __('general.restaurant.menu.ingredients') }} (Advanced)</span>
                <svg class="w-4 h-4 transition-transform" :class="showIngredients ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="showIngredients" x-cloak class="px-4 py-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-3">{{ __('general.restaurant.menu.ingredients_help') }}</p>
                <div class="flex justify-end mb-3">
                    <button type="button" @click="addIngredient()"
                            class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-lg hover:bg-gray-200">
                        + {{ __('general.add') }}
                    </button>
                </div>

                <template x-for="(ing, idx) in ingredients" :key="idx">
                    <div class="flex gap-2 mb-2 items-start">
                        <select :name="'ingredients['+idx+'][product_id]'" x-model="ing.product_id" required
                                class="flex-1 border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">{{ __('general.restaurant.placeholders.product') }}</option>
                            @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->unit }})</option>
                            @endforeach
                        </select>
                        <input type="number" :name="'ingredients['+idx+'][quantity]'" x-model="ing.quantity"
                               step="0.0001" min="0.0001" required :placeholder="'{{ __('general.quantity') }}'"
                               class="w-20 border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <input type="text" :name="'ingredients['+idx+'][unit]'" x-model="ing.unit"
                               required :placeholder="'{{ __('general.unit') }}'" maxlength="30"
                               class="w-20 border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <button type="button" @click="ingredients.splice(idx, 1)"
                                class="text-red-500 hover:text-red-700 text-sm px-2 py-2">✕</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl text-sm hover:opacity-90 font-medium">
                {{ __('general.restaurant.menu.create_item') }}
            </button>
            <a href="{{ route('restaurant.menu.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('general.cancel') }}</a>
        </div>
    </form>
</div>

<script>
function menuItemForm(products) {
    return {
        linkedProductId: '{{ old('linked_product_id') }}',
        itemName: '{{ old('name') }}',
        itemDescription: '{{ old('description') }}',
        ingredients: [],
        addIngredient() {
            this.ingredients.push({ product_id: '', quantity: '', unit: '' });
        },
        onProductLinked() {
            if (!this.linkedProductId) return;
            const prod = products.find(p => p.id === this.linkedProductId);
            if (prod) {
                if (!this.itemName) this.itemName = prod.name;
                if (!this.itemDescription) this.itemDescription = 'Store beverage: ' + prod.name;
                this.ingredients = [{ product_id: prod.id, quantity: '1', unit: prod.unit }];
            }
        }
    }
}
</script>
@endsection
