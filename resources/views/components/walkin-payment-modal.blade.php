{{--
    Walk-in Payment Modal Component
    
    Usage:
    <x-walkin-payment-modal 
        :amount="$order->total" 
        :order-id="$order->id"
        :order-number="$order->order_number"
        module="laundry|restaurant|bar"
        :customer-name="$order->customer_name ?? ''"
        :customer-phone="$order->customer_phone ?? ''"
    />
--}}
@props([
    'amount' => 0,
    'orderId' => null,
    'orderNumber' => '',
    'module' => 'restaurant',
    'customerName' => '',
    'customerPhone' => '',
])

<div x-data="walkinPayment(@js([
    'amount' => $amount,
    'orderId' => $orderId,
    'orderNumber' => $orderNumber,
    'module' => $module,
    'customerName' => $customerName,
    'customerPhone' => $customerPhone,
]))" x-cloak>
    
    {{-- Trigger Button --}}
    <button type="button" 
            @click="openModal()"
            class="w-full bg-green-600 text-white py-2.5 px-4 rounded-lg text-sm font-semibold hover:bg-green-700 transition-all flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        {{ __('Pay & Settle') }}
    </button>

    {{-- Modal Overlay --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>
        
        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ __('Walk-in Payment') }}</h3>
                                <p class="text-xs text-gray-500" x-text="'Order: ' + orderNumber"></p>
                            </div>
                        </div>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5">
                    {{-- Amount Display --}}
                    <div class="text-center py-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-500 mb-1">{{ __('Amount to Pay') }}</p>
                        <p class="text-3xl font-extrabold text-gray-800" x-text="formatCurrency(amount)"></p>
                    </div>

                    {{-- Customer Information --}}
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('Customer Information') }}
                        </h4>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('Customer Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   x-model="form.customer_name" 
                                   :class="{'border-red-300 ring-red-100': errors.customer_name}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                   placeholder="{{ __('Enter customer name') }}">
                            <p x-show="errors.customer_name" x-text="errors.customer_name" class="mt-1 text-xs text-red-500"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('Phone Number') }}</label>
                            <input type="tel" 
                                   x-model="form.customer_phone"
                                   :class="{'border-red-300 ring-red-100': errors.customer_phone}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                   placeholder="+255 7XX XXX XXX">
                            <p x-show="errors.customer_phone" x-text="errors.customer_phone" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            {{ __('Payment Method') }}
                        </h4>
                        
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Cash --}}
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="form.payment_method" value="cash" class="sr-only peer">
                                <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="text-xs font-medium text-gray-600">{{ __('Cash') }}</span>
                                </div>
                            </label>
                            
                            {{-- Card --}}
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="form.payment_method" value="card" class="sr-only peer">
                                <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span class="text-xs font-medium text-gray-600">{{ __('Card') }}</span>
                                </div>
                            </label>
                            
                            {{-- Mobile Money --}}
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="form.payment_method" value="mobile" class="sr-only peer">
                                <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs font-medium text-gray-600">{{ __('Mobile') }}</span>
                                </div>
                            </label>
                        </div>
                        <p x-show="errors.payment_method" x-text="errors.payment_method" class="text-xs text-red-500"></p>
                        
                        {{-- Mobile Money Phone (shown only for mobile payments) --}}
                        <div x-show="form.payment_method === 'mobile'" x-transition class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('Mobile Money Number') }} <span class="text-red-500">*</span></label>
                            <input type="tel" 
                                   x-model="form.mobile_phone"
                                   :class="{'border-red-300 ring-red-100': errors.mobile_phone}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                   placeholder="+255 7XX XXX XXX">
                            <p class="mt-1 text-xs text-gray-400">{{ __('A payment prompt will be sent to this number') }}</p>
                            <p x-show="errors.mobile_phone" x-text="errors.mobile_phone" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="globalError" x-transition class="p-3 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-sm text-red-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="globalError"></span>
                        </p>
                    </div>

                    {{-- Success Message --}}
                    <div x-show="successMessage" x-transition class="p-3 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="successMessage"></span>
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-between gap-3">
                    <button type="button" 
                            @click="closeModal()"
                            :disabled="processing"
                            class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-100 transition-all disabled:opacity-50">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" 
                            @click="submitPayment()"
                            :disabled="processing"
                            class="px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="processing ? '{{ __('Processing...') }}' : '{{ __('Confirm Payment') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function walkinPayment(config) {
    return {
        showModal: false,
        processing: false,
        globalError: '',
        successMessage: '',
        errors: {},
        
        // Config from props
        amount: config.amount,
        orderId: config.orderId,
        orderNumber: config.orderNumber,
        module: config.module,
        
        // Form data
        form: {
            customer_name: config.customerName || '',
            customer_phone: config.customerPhone || '',
            payment_method: 'cash',
            mobile_phone: config.customerPhone || '',
        },
        
        openModal() {
            this.showModal = true;
            this.globalError = '';
            this.successMessage = '';
            this.errors = {};
        },
        
        closeModal() {
            if (!this.processing) {
                this.showModal = false;
            }
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-TZ', {
                style: 'decimal',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount) + ' TZS';
        },
        
        validate() {
            this.errors = {};
            
            if (!this.form.customer_name.trim()) {
                this.errors.customer_name = '{{ __('Customer name is required') }}';
            }
            
            if (!this.form.payment_method) {
                this.errors.payment_method = '{{ __('Please select a payment method') }}';
            }
            
            if (this.form.payment_method === 'mobile' && !this.form.mobile_phone.trim()) {
                this.errors.mobile_phone = '{{ __('Mobile money number is required') }}';
            }
            
            return Object.keys(this.errors).length === 0;
        },
        
        async submitPayment() {
            if (!this.validate()) return;
            
            this.processing = true;
            this.globalError = '';
            this.successMessage = '';
            
            try {
                const response = await fetch('{{ route('finance.walkin-payment.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: this.orderId,
                        module: this.module,
                        amount: this.amount,
                        customer_name: this.form.customer_name,
                        customer_phone: this.form.customer_phone || this.form.mobile_phone,
                        payment_method: this.form.payment_method,
                        mobile_phone: this.form.payment_method === 'mobile' ? this.form.mobile_phone : null,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.successMessage = data.message || '{{ __('Payment successful!') }}';
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    this.globalError = data.message || '{{ __('Payment failed. Please try again.') }}';
                    
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                }
            } catch (error) {
                console.error('Payment error:', error);
                this.globalError = '{{ __('An error occurred. Please try again.') }}';
            } finally {
                this.processing = false;
            }
        }
    };
}
</script>
