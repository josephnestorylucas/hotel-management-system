@extends('layouts.app')

@section('title', 'Buffet POS')
@section('page-title', 'Buffet POS')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Buffet POS</h1>
        <a href="{{ route('restaurant.pos') }}" class="text-sm text-blue-600 hover:underline">
            &#8592; Regular POS
        </a>
    </div>

    @if($sessions->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <p class="text-gray-500 text-lg">No buffet sessions active at this time.</p>
        </div>
    @else
        <form method="POST" action="{{ route('buffet.pos.store') }}" x-data="buffetPos()">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Select Buffet Session</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($sessions as $session)
                        <label class="cursor-pointer" x-data="{ selected: false }">
                            <input type="radio" name="buffet_package_id" value="{{ $session->id }}"
                                class="hidden peer" required
                                x-on:change="updateSession({{ $session->id }}, {{ $session->adult_price }}, {{ $session->child_price }})">
                            <div class="border-2 rounded-xl p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-400 transition">
                                <p class="font-bold text-gray-800">{{ $session->name }}</p>
                                @if($session->start_time)
                                <p class="text-xs text-gray-500">
                                    {{ substr($session->start_time, 0, 5) }} &#8211; {{ substr($session->end_time, 0, 5) }}
                                </p>
                                @endif
                                <p class="text-sm mt-1">
                                    Adult: <strong>TZS {{ number_format($session->adult_price) }}</strong>
                                </p>
                                <p class="text-sm">
                                    Child: <strong>TZS {{ number_format($session->child_price) }}</strong>
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Enter Count</h2>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="font-semibold text-lg text-gray-800">Adults</p>
                            <p class="text-sm text-gray-500">Full price</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" x-on:click="adults = Math.max(0, adults - 1); updateTotals()"
                                class="w-10 h-10 rounded-full bg-gray-200 text-xl font-bold hover:bg-gray-300 transition">&#8722;</button>
                            <input type="number" name="adults" x-model.number="adults" min="0"
                                class="w-16 text-center text-2xl font-bold border-0 outline-none bg-transparent" readonly>
                            <button type="button" x-on:click="adults++; updateTotals()"
                                class="w-10 h-10 rounded-full bg-blue-600 text-white text-xl font-bold hover:bg-blue-700 transition">+</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-semibold text-lg text-gray-800">Children</p>
                            <p class="text-sm text-gray-500">Child price</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" x-on:click="children = Math.max(0, children - 1); updateTotals()"
                                class="w-10 h-10 rounded-full bg-gray-200 text-xl font-bold hover:bg-gray-300 transition">&#8722;</button>
                            <input type="number" name="children" x-model.number="children" min="0"
                                class="w-16 text-center text-2xl font-bold border-0 outline-none bg-transparent" readonly>
                            <button type="button" x-on:click="children++; updateTotals()"
                                class="w-10 h-10 rounded-full bg-blue-600 text-white text-xl font-bold hover:bg-blue-700 transition">+</button>
                        </div>
                    </div>

                    <input type="text" name="customer_name" x-model="customerName"
                        placeholder="Customer name (optional)"
                        class="w-full border-gray-200 rounded-lg text-sm px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl border border-blue-200 shadow-sm mb-6 overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between text-lg mb-1">
                        <span class="text-gray-600">Adults:</span>
                        <span class="font-medium" x-text="adults"></span>
                    </div>
                    <div class="flex justify-between text-lg mb-1">
                        <span class="text-gray-600">Children:</span>
                        <span class="font-medium" x-text="children"></span>
                    </div>
                    <div class="border-t border-blue-200 my-3"></div>
                    <div class="flex justify-between text-2xl font-bold text-blue-700">
                        <span>TOTAL</span>
                        <span x-text="'TZS ' + grandTotal().toLocaleString()"></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Payment Method</h2>
                </div>
                <div class="p-4">
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="hidden peer" required>
                            <div class="text-center px-3 py-2.5 rounded-lg border border-gray-300 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 text-sm font-semibold hover:border-blue-400 transition">
                                Cash
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="mobile" class="hidden peer">
                            <div class="text-center px-3 py-2.5 rounded-lg border border-gray-300 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 text-sm font-semibold hover:border-blue-400 transition">
                                Mobile
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="hidden peer">
                            <div class="text-center px-3 py-2.5 rounded-lg border border-gray-300 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 text-sm font-semibold hover:border-blue-400 transition">
                                Card
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="charge_to_booking" class="hidden peer"
                                x-on:change="showBookingField = true">
                            <div class="text-center px-3 py-2.5 rounded-lg border border-gray-300 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 text-sm font-semibold hover:border-green-400 transition">
                                Folio
                            </div>
                        </label>
                    </div>
                    <div x-show="showBookingField" x-transition class="mt-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guest Booking *</label>
                        <input type="text" name="booking_id" x-model="bookingId"
                            placeholder="Enter Booking #"
                            class="w-full border-gray-200 rounded-lg text-sm px-3 py-2.5">
                        <p class="text-xs text-gray-400 mt-1">Charges will be added to the guest folio.</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl text-lg font-bold hover:bg-blue-700 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!sessionSelected || adults < 1">
                Process Sale &#8594;
            </button>
        </form>
    @endif
</div>

<script>
function buffetPos() {
    return {
        adults: 0,
        children: 0,
        sessionSelected: false,
        adultPrice: 0,
        childPrice: 0,
        customerName: '',
        showBookingField: false,
        bookingId: '',

        updateSession(packageId, adultPrice, childPrice) {
            this.sessionSelected = true;
            this.adultPrice = adultPrice;
            this.childPrice = childPrice;
            this.updateTotals();
        },

        updateTotals() {},

        grandTotal() {
            return (this.adults * this.adultPrice) + (this.children * this.childPrice);
        }
    }
}
</script>
@endsection
