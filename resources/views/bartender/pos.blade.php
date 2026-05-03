@extends('layouts.app')

@section('title', __('bartender.titles.pos'))
@section('page-title', __('bartender.titles.pos'))

@section('content')
<div x-data="barPos()" class="h-[calc(100vh-8rem)] flex gap-4">
    <!-- LEFT PANEL: Customer + Products -->
    <div class="flex-1 flex flex-col gap-4 min-w-0">
        <!-- Customer / Walk-in Info -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-semibold text-gray-700">{{ __('bartender.pos.customer') }}:</span>
                    <span class="text-sm text-gray-600 ml-2">{{ __('bartender.pos.walkin_customer') }}</span>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" x-model="customerName" placeholder="{{ __('bartender.placeholders.walkin_guest') }}"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <input type="text" x-model="customerPhone" placeholder="{{ __('bartender.placeholders.customer_phone') }}"
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
                    {{ __('bartender.pos.all_items') }}
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = '{{ $cat->id }}'"
                    :class="activeCategory === '{{ $cat->id }}' ? 'bg-blue-50 text-primary' : 'text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    {{ $cat->name }}
                    <span class="text-xs text-gray-400">({{ $cat->menuItems->count() }})</span>
                </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="flex-1 bg-white rounded-xl border border-gray-100 shadow-sm p-3 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($categories as $cat)
                        @foreach($cat->menuItems as $item)
                        @php
                            $itemStock = $stockMap[mb_strtolower(trim($item->name))] ?? 0;
                            $itemImage = $imageMap[mb_strtolower(trim($item->name))] ?? null;
                        @endphp
                        <div @click="{{ $itemStock > 0 ? "selectProduct('{$item->id}', '".addslashes($item->name)."', {$item->selling_price})" : '' }}"
                            x-show="!activeCategory || activeCategory === '{{ $cat->id }}'"
                            class="{{ $itemStock > 0 ? 'cursor-pointer hover:border-blue-300 hover:bg-blue-50/50 active:scale-95' : 'cursor-not-allowed opacity-50' }} p-3 rounded-lg border border-gray-100 transition-all relative"
                            title="{{ $item->name }} - @currency($item->selling_price, 'TZS') {{ $itemStock <= 0 ? '(Out of Stock)' : '(' . number_format($itemStock, 0) . ' in stock)' }}">
                            @if($itemImage)
                            <div class="w-full h-20 mb-2 overflow-hidden rounded-md bg-gray-100">
                                <img src="{{ $itemImage }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            @else
                            <div class="w-full h-16 mb-2 overflow-hidden rounded-md">
                                <img src="{{ asset('images/product-placeholder.svg') }}" alt="No image" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            @endif
                            <div class="text-xs font-semibold text-gray-800 truncate">{{ $item->name }}</div>
                            @if(!empty($item->varieties))
                            <div class="text-xs text-amber-600 mt-0.5">{{ count($item->varieties) }} sizes</div>
                            @endif
                            <div class="text-xs font-bold text-primary mt-1">@currency($item->selling_price, 'TZS')</div>
                            @if($itemStock <= 0)
                            <div class="absolute top-1 right-1 bg-red-100 text-red-700 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Out of Stock</div>
                            @elseif($itemStock <= 5)
                            <div class="absolute top-1 right-1 bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded-full font-medium">{{ number_format($itemStock, 0) }} left</div>
                            @endif
                        </div>
                        @endforeach
                    @endforeach
                </div>
                <div x-show="displayedCount() === 0" class="text-center py-8 text-gray-400 text-sm">
                    {{ __('general.no_data') }}
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Cart -->
    <div class="w-96 bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800">{{ __('bartender.pos.current_order') }}</h3>
                <span class="text-xs text-gray-500" x-text="cart.length + ' items'"></span>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></div>
                        <div class="text-xs text-gray-500" x-text="formatCurrency(item.unit_price) + ' / unit'"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="decrementQty(idx)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition-colors">−</button>
                        <input type="number" x-model.number="item.quantity" @input="updateQty(idx)" min="1" max="999"
                            class="w-14 text-center border border-gray-200 rounded-lg text-sm py-1">
                        <button @click="incrementQty(idx)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition-colors">+</button>
                    </div>
                    <div class="text-sm font-bold text-gray-800 w-20 text-right" x-text="formatCurrency(lineSubtotal(item))"></div>
                    <button @click="removeFromCart(idx)" class="text-red-400 hover:text-red-600 px-1">✕</button>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center py-8 text-gray-400 text-sm">
                {{ __('bartender.pos.empty_cart') }}
            </div>
        </div>

        <!-- Notes -->
        <div class="px-3 pb-2">
            <input type="text" x-model="orderNotes" placeholder="{{ __('bartender.fields.notes') }}"
                class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
        </div>

        <!-- Totals & Actions -->
        <div class="border-t border-gray-100 p-4 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">{{ __('general.subtotal') }}</span>
                <span class="font-medium" x-text="formatCurrency(grandTotal())"></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">{{ __('general.tax') }} (18% VAT)</span>
                <span class="font-medium" x-text="formatCurrency(taxAmount())"></span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                <span>{{ __('general.grand_total') }}</span>
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
                <div x-show="chargeToBooking" class="mt-3" x-transition>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Guest Booking *</label>
                    <input type="text" x-model="bookingId" placeholder="Enter Booking # or Room #"
                        class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <p class="text-xs text-gray-400 mt-1">Charges will be added to the guest folio and paid at checkout.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button @click="clearCart()"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    {{ __('bartender.pos.clear') }}
                </button>
                <button @click="saveOrder()" :disabled="cart.length === 0"
                    class="px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    {{ __('bartender.pos.create_order') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Variety Selection Modal -->
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
        $item->id => $stockMap[mb_strtolower(trim($item->name))] ?? 0
    ])
    ->toJson();
@endphp
<script>
function barPos() {
    const menuPrices = JSON.parse({!! json_encode($menuPricesJson) !!});
    const menuVarieties = JSON.parse({!! json_encode($menuVarietiesJson) !!});
    const menuStock = JSON.parse({!! json_encode($menuStockJson) !!});

    return {
        // Customer
        customerName: '',
        customerPhone: '',

        // Catalog
        activeCategory: null,
        showVarietyModal: false,
        selectingProduct: null,
        selectingVarieties: [],

        // Cart
        cart: [],
        orderNotes: '',

        // Payment
        paymentMethod: '',
        chargeToBooking: false,
        bookingId: '',

        // Actions

        // Cart actions
        selectProduct(menuItemId, name, basePrice) {
            const stock = menuStock[menuItemId] || 0;
            if (stock <= 0) {
                alert('{{ __('bartender.pos.out_of_stock') }}' || 'This item is out of stock.');
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
            const existing = this.cart.find(i => i.menu_item_id === menuItemId);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ menu_item_id: menuItemId, name, unit_price: unitPrice, quantity: 1 });
            }
        },
        removeFromCart(idx) {
            this.cart.splice(idx, 1);
        },
        incrementQty(idx) {
            this.cart[idx].quantity++;
        },
        decrementQty(idx) {
            if (this.cart[idx].quantity > 1) {
                this.cart[idx].quantity--;
            } else {
                this.removeFromCart(idx);
            }
        },
        updateQty(idx) {
            const qty = parseInt(this.cart[idx].quantity, 10);
            if (!qty || qty < 1) {
                this.cart[idx].quantity = 1;
            }
        },
        clearCart() {
            if (this.cart.length > 0 && confirm('{{ __('bartender.pos.confirm_clear') }}')) {
                this.cart = [];
                this.orderNotes = '';
            }
        },

        // Totals
        lineSubtotal(item) {
            return Number(item.unit_price || 0) * Number(item.quantity || 0);
        },
        grandTotal() {
            return this.cart.reduce((sum, i) => sum + this.lineSubtotal(i), 0);
        },
        taxAmount() {
            return Math.round(this.grandTotal() * 0.18);
        },
        totalWithTax() {
            return this.grandTotal() + this.taxAmount();
        },
        displayedCount() {
            if (!this.activeCategory) {
                return document.querySelectorAll('[x-show]').length || 1;
            }
            return 1;
        },

        // Save
        saveOrder() {
            if (this.cart.length === 0) return;

            if (!this.paymentMethod) {
                alert('{{ __('bartender.pos.select_payment') }}' || 'Please select a payment method.');
                return;
            }

            if (this.chargeToBooking && !this.bookingId) {
                alert('Please enter a booking number or room number to charge to.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('bartender.pos.store') }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

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
            });

            document.body.appendChild(form);
            form.submit();
        },

        // Formatting
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
