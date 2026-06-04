@extends('layouts.app')

@section('title', 'Restaurant POS')
@section('page-title', 'Restaurant POS')

@section('content')
<div x-data="restaurantPos()" class="h-[calc(100vh-8rem)] flex gap-4">
    <!-- LEFT PANEL: Customer + Products -->
    <div class="flex-1 flex flex-col gap-4 min-w-0">
        <!-- Customer / Walk-in Info -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-semibold text-gray-700">Customer:</span>
                    <span class="text-sm text-gray-600 ml-2">Walk-in Customer</span>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" x-model="customerName" placeholder="Guest name (optional)"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <input type="text" x-model="customerPhone" placeholder="Phone number (optional)"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
        </div>

        <!-- Product Catalog -->
        <div class="flex flex-1 gap-3 min-h-0">
            <!-- Categories -->
            <div class="w-48 bg-white rounded-xl border border-gray-100 shadow-sm p-2 overflow-y-auto flex-shrink-0">
                <button @click="activeCategory = null"
                    :class="!activeCategory ? 'bg-blue-50 text-primary' : 'text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    All Items
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = '{{ $cat->id }}'"
                    :class="activeCategory === '{{ $cat->id }}' ? 'bg-blue-50 text-primary' : 'text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    {{ $cat->name }}
                    <span class="text-xs text-gray-400">({{ $cat->menuItems->count() }})</span>
                </button>
                @endforeach
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="showFinalisePanel = !showFinalisePanel"
                    :class="showFinalisePanel ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-emerald-600 hover:bg-emerald-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    💳 Finalise
                </button>
            </div>

            <!-- Products / Recent Orders Grid -->
            <div class="flex-1 bg-white rounded-xl border border-gray-100 shadow-sm p-3 overflow-y-auto">
                <!-- Orders to Finalise Panel -->
                <div x-show="showFinalisePanel" x-transition class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Orders to Finalise</h3>
                    @if($recentOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-gray-200 text-gray-500">
                                    <th class="text-left py-2 px-2 font-medium">Order #</th>
                                    <th class="text-right py-2 px-2 font-medium">Total</th>
                                    <th class="text-center py-2 px-2 font-medium">When</th>
                                    <th class="text-center py-2 px-2 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-2 px-2 font-mono font-medium text-primary">{{ $order->order_number }}</td>
                                    <td class="py-2 px-2 text-right font-semibold">@currency($order->total, 'TZS')</td>
                                    <td class="py-2 px-2 text-center text-gray-500">{{ $order->created_at->format('H:i') }}</td>
                                    <td class="py-2 px-2 text-center">
                                        <button @click="loadOrderForFinalise(servedOrders.find(o => o.id === '{{ $order->id }}'))"
                                            class="inline-block bg-emerald-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-emerald-700 cursor-pointer">
                                            Finalise
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 text-center py-4">No orders to finalise.</p>
                    @endif
                </div>

                <!-- Menu Items Grid -->
                <div x-show="!showFinalisePanel || selectedOrderId" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    <template x-if="selectedOrderId">
                        <div class="col-span-full text-center py-12 text-gray-400">
                            <p class="text-sm font-medium">Finalising order: <span class="text-primary" x-text="selectedOrderNumber"></span></p>
                            <p class="text-xs mt-1">Select payment method and click Finalise Payment.</p>
                        </div>
                    </template>
                    {{-- Menu Items --}}
                    <template x-if="!selectedOrderId">
                    @foreach($categories as $cat)
                        @foreach($cat->menuItems as $item)
                        @php
                            $itemStock = $stockMap[mb_strtolower(trim($item->name))] ?? 999;
                        @endphp
                        <div @click="selectProduct('{{ $item->id }}', '{{ addslashes($item->name) }}', {{ $item->selling_price }})"
                            x-show="!activeCategory || activeCategory === '{{ $cat->id }}'"
                            class="cursor-pointer hover:border-blue-300 hover:bg-blue-50/50 active:scale-95 p-3 rounded-lg border border-gray-100 transition-all relative"
                            title="{{ $item->name }} - @currency($item->selling_price, 'TZS')">
                            <div class="w-full h-16 mb-2 overflow-hidden rounded-md bg-gray-100 flex items-center justify-center">
                                @if($item->hasMedia('menu_item_image'))
                                <img src="{{ $item->getFirstMediaUrl('menu_item_image', 'thumb') }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                @endif
                            </div>
                            <div class="text-xs font-semibold text-gray-800 truncate">{{ $item->name }}</div>
                            @if(!empty($item->varieties))
                            <div class="text-xs text-amber-600 mt-0.5">{{ count($item->varieties) }} sizes</div>
                            @endif
                            <div class="text-xs font-bold text-primary mt-1">@currency($item->selling_price, 'TZS')</div>
                        </div>
                        @endforeach
                    @endforeach
                    </template>
                </div>
                <div x-show="displayedCount() === 0" class="text-center py-8 text-gray-400 text-sm">
                    No menu items found.
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Cart -->
    <div class="w-96 bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800" x-text="selectedOrderId ? 'Finalising: ' + selectedOrderNumber : 'Current Order'"></h3>
                    <span class="text-xs text-gray-500" x-text="cart.length + ' items'"></span>
                </div>
                <button x-show="selectedOrderId" @click="clearCart()" class="text-xs text-red-500 hover:text-red-700 font-medium">Cancel</button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></div>
                        <div class="text-xs text-gray-500">
                            <span x-text="formatCurrency(item.unit_price) + ' / unit'"></span>
                        </div>
                    </div>
                    <template x-if="!selectedOrderId">
                        <div class="flex items-center gap-1">
                            <button @click="decrementQty(idx)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition-colors">−</button>
                            <input type="number" x-model.number="item.quantity" @input="updateQty(idx)" min="1" max="999"
                                class="w-14 text-center border border-gray-200 rounded-lg text-sm py-1">
                            <button @click="incrementQty(idx)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition-colors">+</button>
                        </div>
                    </template>
                    <template x-if="selectedOrderId">
                        <span class="text-sm font-bold text-gray-700 w-8 text-center" x-text="item.quantity"></span>
                    </template>
                    <div class="text-sm font-bold text-gray-800 w-20 text-right" x-text="formatCurrency(lineSubtotal(item))"></div>
                    <template x-if="!selectedOrderId">
                        <button @click="removeFromCart(idx)" class="text-red-400 hover:text-red-600 px-1">✕</button>
                    </template>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center py-8 text-gray-400 text-sm">
                <span x-text="selectedOrderId ? 'No items in this order.' : 'Tap items from the menu to build the order.'"></span>
            </div>
        </div>

        <!-- Notes -->
        <div class="px-3 pb-2">
            <input type="text" x-model="orderNotes" placeholder="Order notes"
                class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
        </div>

        <!-- Totals & Actions -->
        <div class="border-t border-gray-100 p-4 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-medium" x-text="formatCurrency(grandTotal())"></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Tax (18% VAT)</span>
                <span class="font-medium" x-text="formatCurrency(taxAmount())"></span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                <span>Grand Total</span>
                <span x-text="formatCurrency(totalWithTax())"></span>
            </div>

            <!-- Payment Method -->
            <div class="border-t border-gray-100 pt-3">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Payment Method *</label>
                <div class="flex gap-2">
                    <template x-for="method in ['Cash', 'Mobile', 'Card']" :key="method">
                        <button type="button" @click="paymentMethod = method; chargeToBooking = false"
                            :class="paymentMethod === method ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400'"
                            class="flex-1 px-2 py-2 rounded-lg border text-xs font-semibold transition-all" x-text="method">
                        </button>
                    </template>
                    <button type="button" @click="paymentMethod = 'Charge to Booking'; chargeToBooking = true"
                        :class="paymentMethod === 'Charge to Booking' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
                        class="flex-1 px-2 py-2 rounded-lg border text-xs font-semibold transition-all">
                        Folio
                    </button>
                </div>
                <input type="hidden" name="payment_method" x-model="paymentMethod">
                <!-- Booking Selection for Charge to Folio -->
                <div x-show="chargeToBooking" class="mt-3 relative" x-transition>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Guest Booking *</label>
                    <input type="text" x-model="bookingSearch" @focus="showBookingDropdown = true" @input="filterBookings()"
                        placeholder="Search by guest name or booking #"
                        class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <div x-show="showBookingDropdown && filteredBookings.length > 0"
                        class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="booking in filteredBookings" :key="booking.id">
                            <div @click="selectBooking(booking)"
                                class="cursor-pointer px-3 py-2 hover:bg-blue-50 text-sm border-b border-gray-100 last:border-0">
                                <div class="font-medium text-gray-800" x-text="booking.guest_name"></div>
                                <div class="text-xs text-gray-500">
                                    <span x-text="booking.booking_number"></span>
                                    <span x-show="booking.room"> · Room <span x-text="booking.room"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="selectedBooking" class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg text-sm">
                        <span class="font-medium text-green-800" x-text="selectedBooking?.guest_name"></span>
                        <span class="text-green-600 text-xs ml-2" x-text="'(' + selectedBooking?.booking_number + ')'"></span>
                        <button @click="clearBookingSelection()" class="float-right text-green-600 hover:text-green-800 text-xs">✕</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Charges will be added to the guest folio and paid at checkout.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button x-show="!selectedOrderId" @click="clearCart()"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    Clear
                </button>
                <button x-show="selectedOrderId" @click="clearCart()"
                    class="px-4 py-2.5 rounded-lg border border-red-300 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                    Cancel
                </button>
                <button @click="saveOrder()" :disabled="cart.length === 0"
                    class="px-4 py-2.5 rounded-lg text-white text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    :class="selectedOrderId ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700'"
                    x-text="selectedOrderId ? 'Finalise Payment' : 'Create Order'">
                </button>
            </div>
        </div>
    </div>

    <div x-show="showVarietyModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-black bg-opacity-40" @click="showVarietyModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800" x-text="selectingProduct?.name + ' — Select Size'"></h3>
                <button @click="showVarietyModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="p-4 space-y-2">
                <template x-for="(v, idx) in selectingVarieties" :key="idx">
                    <div @click="addVarietyToCart(v)"
                        class="cursor-pointer p-3 rounded-lg border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition-all flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-800" x-text="v.label"></span>
                        <span class="text-sm font-bold text-primary" x-text="formatCurrency(v.price || selectingProduct?.basePrice)"></span>
                    </div>
                </template>
                <div @click="addToCart(selectingProduct?.id, selectingProduct?.name, selectingProduct?.basePrice); showVarietyModal = false"
                    class="cursor-pointer p-3 rounded-lg border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition-all text-sm text-gray-500 text-center">
                    No preference — use standard
                </div>
            </div>
        </div>
    </div>

    <!-- Options Popup Modal (for items with option groups) -->
    <div x-show="showOptionsModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-black bg-opacity-40" @click="showOptionsModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[80vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-800" x-text="optionsItem?.name || 'Select Options'"></h3>
                <button @click="showOptionsModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <template x-for="group in optionsGroups" :key="group.id">
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-gray-700" x-text="group.name"></h4>
                            <span x-show="group.is_required" class="text-xs text-red-500 font-medium">Required</span>
                        </div>
                        <div class="space-y-1">
                            <template x-for="value in group.values" :key="value.id">
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input :type="group.selection_type === 'single' ? 'radio' : 'checkbox'"
                                        :name="'option_group_' + group.id"
                                        :value="value.id"
                                        x-model="selectedOptions[group.id]"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800" x-text="value.label"></span>
                                    </div>
                                    <span x-show="value.price_delta !== 0" class="text-sm font-semibold text-primary" x-text="'+' + formatCurrency(value.price_delta)"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
                <div class="pt-2 border-t border-gray-200">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-600">Base Price</span>
                        <span class="font-medium" x-text="formatCurrency(optionsItem?.base_price || 0)"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-600">Options</span>
                        <span class="font-medium" x-text="formatCurrency(optionsExtraCost())"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span x-text="formatCurrency((optionsItem?.base_price || 0) + optionsExtraCost())"></span>
                    </div>
                </div>
                <button @click="addWithOptions()"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    Add to Order
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
$menuPricesJson = $categories
    ->flatMap(fn($cat) => $cat->menuItems->map(fn($item) => ['id' => $item->id, 'price' => (float) $item->selling_price]))
    ->pluck('price', 'id')
    ->toJson();

$menuVarietiesJson = $categories
    ->flatMap(fn($cat) => $cat->menuItems->filter(fn($item) => !empty($item->varieties)))
    ->mapWithKeys(fn($item) => [$item->id => $item->varieties])
    ->toJson();

$menuStockJson = collect($categories)
    ->flatMap(fn($cat) => $cat->menuItems)
    ->mapWithKeys(fn($item) => [
        $item->id => $stockMap[mb_strtolower(trim($item->name))] ?? 999
    ])
    ->toJson();

$menuOptionsJson = collect($categories)
    ->flatMap(fn($cat) => $cat->menuItems)
    ->filter(fn($item) => $item->optionGroups->isNotEmpty())
    ->mapWithKeys(fn($item) => [$item->id => $item->optionGroups->map(fn($g) => [
        'id' => $g->id,
        'name' => $g->name,
        'selection_type' => $g->selection_type,
        'is_required' => $g->is_required,
        'values' => $g->values->map(fn($v) => [
            'id' => $v->id,
            'label' => $v->label,
            'price_delta' => (float) $v->price_delta,
        ])->values()->all(),
    ])->values()->all()])
    ->toJson();

$servedOrdersJson = $recentOrders->map(fn($order) => [
    'id' => $order->id,
    'order_number' => $order->order_number,
    'order_type' => $order->order_type,
    'customer_name' => $order->customer_name,
    'customer_phone' => $order->customer_phone,
    'total' => (float) $order->total,
    'notes' => $order->notes,
    'booking_id' => $order->booking_id,
    'items' => $order->items->map(fn($item) => [
        'menu_item_id' => $item->menu_item_id,
        'name' => $item->item_name_snapshot ?? $item->menuItem?->name ?? 'Item',
        'quantity' => $item->quantity,
        'unit_price' => (float) $item->unit_price,
        'subtotal' => (float) $item->subtotal,
        'selected_options_snapshot' => $item->selected_options_snapshot,
    ])->all(),
])->toJson();
@endphp
<script>
function restaurantPos() {
    const menuPrices = JSON.parse({!! json_encode($menuPricesJson) !!});
    const menuVarieties = JSON.parse({!! json_encode($menuVarietiesJson) !!});
    const menuStock = JSON.parse({!! json_encode($menuStockJson) !!});
    const menuOptions = JSON.parse({!! json_encode($menuOptionsJson) !!});
    const servedOrders = {!! $servedOrdersJson !!};

    return {
        init() {
            this.filteredBookings = this.bookings;
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showBookingDropdown = false;
                }
            });
        },

        // Customer
        customerName: '',
        customerPhone: '',

        // Catalog
        activeCategory: null,
        showVarietyModal: false,
        selectingProduct: null,
        selectingVarieties: [],

        // Options
        showOptionsModal: false,
        optionsItem: null,
        optionsGroups: [],
        selectedOptions: {},

        // Cart
        cart: [],
        orderNotes: '',

        // Finalise existing order
        showFinalisePanel: true,
        selectedOrderId: null,
        selectedOrderNumber: '',
        selectedOrderType: '',
        servedOrders: servedOrders,

        // Payment
        paymentMethod: '',
        chargeToBooking: false,
        bookingId: '',
        selectedBooking: null,
        bookings: @js($activeBookings->map(fn($b) => [
            'id' => $b->id,
            'booking_number' => $b->booking_number,
            'guest_name' => $b->guest_name,
            'room' => $b->room?->number,
        ])),
        bookingSearch: '',
        showBookingDropdown: false,
        filteredBookings: [],

        selectProduct(menuItemId, name, basePrice) {
            // Check if item has options
            const options = menuOptions[menuItemId];
            if (options && options.length > 0) {
                this.openOptionsModal(menuItemId, name, basePrice, options);
                return;
            }

            const varieties = menuVarieties[menuItemId];
            if (varieties && varieties.length > 0) {
                this.selectingProduct = { id: menuItemId, name, basePrice };
                this.selectingVarieties = varieties;
                this.showVarietyModal = true;
                return;
            }
            this.addToCart(menuItemId, name, basePrice);
        },

        openOptionsModal(menuItemId, name, basePrice, options) {
            this.optionsItem = { id: menuItemId, name, base_price: basePrice };
            this.optionsGroups = options;
            this.selectedOptions = {};
            // Pre-select single required options with first value
            options.forEach(group => {
                if (group.selection_type === 'single' && group.is_required && group.values.length > 0) {
                    this.selectedOptions[group.id] = group.values[0].id;
                }
            });
            this.showOptionsModal = true;
        },

        optionsExtraCost() {
            let extra = 0;
            for (const groupId in this.selectedOptions) {
                const selectedVal = this.selectedOptions[groupId];
                if (Array.isArray(selectedVal)) {
                    // Multiple selection
                    selectedVal.forEach(valId => {
                        const group = this.optionsGroups.find(g => g.id === groupId);
                        if (group) {
                            const val = group.values.find(v => v.id === valId);
                            if (val) extra += Number(val.price_delta || 0);
                        }
                    });
                } else if (selectedVal) {
                    // Single selection
                    const group = this.optionsGroups.find(g => g.id === groupId);
                    if (group) {
                        const val = group.values.find(v => v.id === selectedVal);
                        if (val) extra += Number(val.price_delta || 0);
                    }
                }
            }
            return extra;
        },

        addWithOptions() {
            if (!this.optionsItem) return;

            // Validate required groups
            for (const group of this.optionsGroups) {
                if (group.is_required) {
                    const selected = this.selectedOptions[group.id];
                    if (!selected || (Array.isArray(selected) && selected.length === 0)) {
                        alert('Please select an option for: ' + group.name);
                        return;
                    }
                }
            }

            const basePrice = Number(this.optionsItem.base_price || 0);
            const extraCost = this.optionsExtraCost();
            const unitPrice = basePrice + extraCost;

            // Build option label
            let optionLabels = [];
            let selectedOptionsSnapshot = [];
            for (const group of this.optionsGroups) {
                const selected = this.selectedOptions[group.id];
                if (!selected) continue;

                const selectedIds = Array.isArray(selected) ? selected : [selected];
                const selectedValues = selectedIds.map(id => group.values.find(v => v.id === id)).filter(Boolean);

                if (selectedValues.length > 0) {
                    optionLabels.push(group.name + ': ' + selectedValues.map(v => v.label).join(', '));
                    selectedOptionsSnapshot.push({
                        group_id: group.id,
                        group_name: group.name,
                        selection_type: group.selection_type,
                        required: group.is_required,
                        values: selectedValues.map(v => ({
                            id: v.id,
                            label: v.label,
                            price_delta: v.price_delta,
                        })),
                    });
                }
            }

            const label = optionLabels.length > 0
                ? this.optionsItem.name + ' (' + optionLabels.join(' | ') + ')'
                : this.optionsItem.name;

            // Create a unique key for this option combination
            const optionKey = this.optionsItem.id + '|' + JSON.stringify(selectedOptionsSnapshot);

            const existing = this.cart.find(i => i._optionKey === optionKey);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    menu_item_id: this.optionsItem.id,
                    name: label,
                    base_price: basePrice,
                    unit_price: unitPrice,
                    quantity: 1,
                    _optionKey: optionKey,
                    selected_options: selectedOptionsSnapshot,
                });
            }

            this.showOptionsModal = false;
            this.optionsItem = null;
            this.optionsGroups = [];
            this.selectedOptions = {};
        },

        addVarietyToCart(variety) {
            const p = this.selectingProduct;
            const price = variety.price || p.basePrice;
            const label = p.name + ' (' + variety.label + ')';
            const key = p.id + '|' + variety.label;
            const existing = this.cart.find(i => i._varietyKey === key);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    menu_item_id: p.id,
                    name: label,
                    unit_price: price,
                    quantity: 1,
                    _varietyKey: key,
                });
            }
            this.showVarietyModal = false;
            this.selectingProduct = null;
        },

        addToCart(menuItemId, name, unitPrice) {
            const existing = this.cart.find(i => i.menu_item_id === menuItemId && !i._optionKey && !i._varietyKey);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ menu_item_id: menuItemId, name, unit_price: unitPrice, quantity: 1 });
            }
        },

        removeFromCart(idx) { this.cart.splice(idx, 1); },
        incrementQty(idx) { this.cart[idx].quantity++; },
        decrementQty(idx) {
            if (this.cart[idx].quantity > 1) { this.cart[idx].quantity--; }
            else { this.removeFromCart(idx); }
        },
        updateQty(idx) {
            const qty = parseInt(this.cart[idx].quantity, 10);
            if (!qty || qty < 1) { this.cart[idx].quantity = 1; }
        },
        clearCart() {
            if (this.selectedOrderId) {
                this.selectedOrderId = null;
                this.selectedOrderNumber = '';
                this.selectedOrderType = '';
                this.cart = [];
                this.orderNotes = '';
                this.customerName = '';
                this.customerPhone = '';
                this.paymentMethod = '';
                this.chargeToBooking = false;
                this.bookingId = '';
                this.selectedBooking = null;
                this.bookingSearch = '';
                this.showFinalisePanel = true;
            } else if (this.cart.length > 0 && confirm('Clear current order? All items will be removed.')) {
                this.cart = [];
                this.orderNotes = '';
            }
        },

        filterBookings() {
            const query = this.bookingSearch.toLowerCase();
            this.filteredBookings = this.bookings.filter(b =>
                b.guest_name.toLowerCase().includes(query) ||
                b.booking_number.toLowerCase().includes(query) ||
                (b.room && b.room.toLowerCase().includes(query))
            );
        },

        selectBooking(booking) {
            this.selectedBooking = booking;
            this.bookingId = booking.id;
            this.bookingSearch = '';
            this.showBookingDropdown = false;
        },

        clearBookingSelection() {
            this.selectedBooking = null;
            this.bookingId = '';
        },

        loadOrderForFinalise(order) {
            if (!order) return;
            this.selectedOrderId = order.id;
            this.selectedOrderNumber = order.order_number;
            this.selectedOrderType = order.order_type;
            this.customerName = order.customer_name || '';
            this.customerPhone = order.customer_phone || '';
            this.orderNotes = order.notes || '';
            this.showFinalisePanel = false;
            this.cart = order.items.map(item => ({
                menu_item_id: item.menu_item_id,
                name: item.name,
                unit_price: item.unit_price,
                quantity: item.quantity,
                selected_options: item.selected_options_snapshot || [],
            }));
            this.activeCategory = null;
        },

        lineSubtotal(item) {
            return Number(item.unit_price || 0) * Number(item.quantity || 0);
        },
        grandTotal() { return this.cart.reduce((sum, i) => sum + this.lineSubtotal(i), 0); },
        taxAmount() { return Math.round(this.grandTotal() * 0.18); },
        totalWithTax() { return this.grandTotal() + this.taxAmount(); },
        displayedCount() {
            return 1;
        },

        saveOrder() {
            if (this.cart.length === 0) return;
            if (!this.paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            if (this.chargeToBooking && !this.bookingId) {
                alert('Please select a guest booking to charge to.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('restaurant.pos.store') }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            if (this.selectedOrderId) {
                const existingInput = document.createElement('input');
                existingInput.type = 'hidden';
                existingInput.name = 'existing_order_id';
                existingInput.value = this.selectedOrderId;
                form.appendChild(existingInput);
            }

            const customerNameInput = document.createElement('input');
            customerNameInput.type = 'hidden';
            customerNameInput.name = 'customer_name';
            customerNameInput.value = this.customerName || (this.chargeToBooking ? 'Guest' : 'Walk-in Guest');
            form.appendChild(customerNameInput);

            const customerPhoneInput = document.createElement('input');
            customerPhoneInput.type = 'hidden';
            customerPhoneInput.name = 'customer_phone';
            customerPhoneInput.value = this.customerPhone;
            form.appendChild(customerPhoneInput);

            const paymentInput = document.createElement('input');
            paymentInput.type = 'hidden';
            paymentInput.name = 'payment_method';
            paymentInput.value = this.chargeToBooking ? 'charge_to_booking' : this.paymentMethod.toLowerCase();
            form.appendChild(paymentInput);

            if (this.chargeToBooking) {
                const bookingInput = document.createElement('input');
                bookingInput.type = 'hidden';
                bookingInput.name = 'booking_id';
                bookingInput.value = this.bookingId;
                form.appendChild(bookingInput);
            }

            if (this.orderNotes) {
                const notesInput = document.createElement('input');
                notesInput.type = 'hidden';
                notesInput.name = 'notes';
                notesInput.value = this.orderNotes;
                form.appendChild(notesInput);
            }

            this.cart.forEach((item, idx) => {
                const menuId = document.createElement('input');
                menuId.type = 'hidden';
                menuId.name = `items[${idx}][menu_item_id]`;
                menuId.value = item.menu_item_id;
                form.appendChild(menuId);

                const qty = document.createElement('input');
                qty.type = 'hidden';
                qty.name = `items[${idx}][quantity]`;
                qty.value = item.quantity;
                form.appendChild(qty);

                // Add selected option value IDs if present
                if (item.selected_options && item.selected_options.length > 0) {
                    item.selected_options.forEach((optGroup, gi) => {
                        optGroup.values.forEach((val, vi) => {
                            const optInput = document.createElement('input');
                            optInput.type = 'hidden';
                            optInput.name = `items[${idx}][selected_option_value_ids][]`;
                            optInput.value = val.id;
                            form.appendChild(optInput);
                        });
                    });
                }
            });

            document.body.appendChild(form);
            form.submit();
        },

        formatCurrency(value) {
            const symbol = '{{ \App\Helpers\CurrencyHelper::getCurrencySymbol('TZS') }}';
            return new Intl.NumberFormat('en-TZ', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(Number(value || 0)) + ' ' + symbol;
        },
    }
}
</script>
@endpush
@endsection
