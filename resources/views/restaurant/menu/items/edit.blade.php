{{-- resources/views/restaurant/menu/items/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('general.edit') . ': ' . $menuItem->name)
@section('page-title', __('general.edit') . ': ' . $menuItem->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-6">{{ __('general.restaurant.menu.edit_item') }}</h1>

    <form method="POST" action="{{ route('restaurant.menu.update', $menuItem) }}" x-data="menuItemForm()" class="space-y-6" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Category (read-only display) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.category') }}</label>
            <p class="text-sm text-gray-600 bg-gray-50 rounded-xl px-3 py-2">
                {{ $menuItem->category->name ?? '—' }}
                ({{ $menuItem->category->location->name ?? '' }})
            </p>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" required
                   class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Selling price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.base_price_tzs') }} *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price', $menuItem->selling_price) }}"
                   required min="1" step="1"
                   class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.description') }}</label>
            <textarea name="description" rows="2"
                      class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('description', $menuItem->description) }}</textarea>
        </div>

        {{-- Availability toggle --}}
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700">{{ __('general.restaurant.fields.available') }}?</label>
            <input type="hidden" name="is_available" value="0">
            <input type="checkbox" name="is_available" value="1"
                   {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}
                   class="rounded text-primary focus:ring-primary">
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
            @if($menuItem->hasMedia('menu_item_image'))
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $menuItem->getFirstMediaUrl('menu_item_image', 'thumb') }}" alt="{{ $menuItem->name }}" class="w-16 h-16 rounded-xl object-cover border">
                    <label class="flex items-center gap-1 text-xs text-red-500 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1"> Remove current image
                    </label>
                </div>
            @endif
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
                    <option value="kitchen" {{ old('destination', $menuItem->destination) === 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                    <option value="bar" {{ old('destination', $menuItem->destination) === 'bar' ? 'selected' : '' }}>Bar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Is Buffet Item?</label>
                <select name="is_buffet"
                        class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="0" {{ old('is_buffet', $menuItem->is_buffet ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_buffet', $menuItem->is_buffet ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Location Tag</label>
                <input type="text" name="service_location_tag" value="{{ old('service_location_tag', $menuItem->service_location_tag) }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                       placeholder="e.g. pool, terrace">
            </div>
        </div>

        {{-- Time Availability --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Available From (optional)</label>
                <input type="time" name="available_from" value="{{ old('available_from', $menuItem->available_from ? substr($menuItem->available_from, 0, 5) : '') }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-xs text-gray-400 mt-1">Leave empty for all-day availability</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Available Until (optional)</label>
                <input type="time" name="available_until" value="{{ old('available_until', $menuItem->available_until ? substr($menuItem->available_until, 0, 5) : '') }}"
                       class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-xs text-gray-400 mt-1">Leave empty for all-day availability</p>
            </div>
        </div>

        {{-- Option Groups --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.options.attach_groups') }}</label>
            <select name="option_group_ids[]" multiple class="w-full border-gray-300 rounded-xl px-3 py-2 text-sm h-28 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @foreach($optionGroups as $group)
                    <option value="{{ $group->id }}" @selected($menuItem->optionGroups->contains('id', $group->id))>
                        {{ $group->name }} ({{ ucfirst($group->selection_type) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Ingredients (collapsible) --}}
        <div x-data="{ showIngredients: {{ $menuItem->ingredients->count() > 0 ? 'true' : 'false' }} }" class="border border-gray-200 rounded-2xl overflow-hidden">
            <button type="button" @click="showIngredients = !showIngredients"
                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-700">
                <span>{{ __('general.restaurant.menu.ingredients') }} ({{ $menuItem->ingredients->count() }} {{ $menuItem->ingredients->count() == 1 ? 'item' : 'items' }})</span>
                <svg class="w-4 h-4 transition-transform" :class="showIngredients ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="showIngredients" x-cloak class="px-4 py-3 border-t border-gray-100">
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
                            <option value="{{ $prod->id }}" :selected="ing.product_id === '{{ $prod->id }}'">{{ $prod->name }} ({{ $prod->unit }})</option>
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
                {{ __('general.update') }}
            </button>
            <a href="{{ route('restaurant.menu.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('general.cancel') }}</a>
        </div>
    </form>
</div>

<script>
function menuItemForm() {
    return {
        ingredients: @json($menuItem->ingredients->map(fn($i) => [
            'product_id' => $i->product_id,
            'quantity'    => $i->quantity,
            'unit'        => $i->unit,
        ])->values()),
        addIngredient() {
            this.ingredients.push({ product_id: '', quantity: '', unit: '' });
        }
    }
}
</script>
@endsection
