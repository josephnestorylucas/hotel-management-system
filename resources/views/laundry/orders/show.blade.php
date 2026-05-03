{{-- resources/views/laundry/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Order ' . $laundryOrder->order_number)
@section('page-title', __('laundry.title'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $laundryOrder->order_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if($laundryOrder->customer_type === 'guest')
                    {{ __('laundry.customer_type.guest') }} · {{ __('laundry.info.room') }} {{ $laundryOrder->room_number }}
                @else
                    {{ __('laundry.customer_type.walkin') }} · {{ $laundryOrder->customer_name }}
                    @if($laundryOrder->customer_phone) · {{ $laundryOrder->customer_phone }} @endif
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($laundryOrder->status === 'settled')
                <a href="{{ route('receipts.laundry', $laundryOrder) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Receipt
                </a>
            @else
                <a href="{{ route('receipts.laundry', $laundryOrder) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-yellow-400 text-yellow-700 text-sm font-semibold rounded-xl hover:bg-yellow-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    View Receipt (Unpaid)
                </a>
            @endif
            <a href="{{ route('laundry.orders.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('laundry.actions.back') }}
            </a>
        </div>
    </div>

    <!-- Status & Actions Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <span class="px-3 py-1.5 rounded-full text-sm font-medium
                    @if($laundryOrder->status === 'received')      bg-yellow-100 text-yellow-700
                    @elseif($laundryOrder->status === 'processing') bg-blue-100 text-blue-700
                    @elseif($laundryOrder->status === 'pending_confirmation') bg-purple-100 text-purple-700
                    @elseif($laundryOrder->status === 'ready')     bg-orange-100 text-orange-700
                    @elseif($laundryOrder->status === 'delivered') bg-indigo-100 text-indigo-700
                    @elseif($laundryOrder->status === 'collected') bg-teal-100 text-teal-700
                    @elseif($laundryOrder->status === 'charged')   bg-purple-100 text-purple-700
                    @elseif($laundryOrder->status === 'settled')   bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-500 @endif">
                    {{ ucfirst(str_replace('_', ' ', $laundryOrder->status)) }}
                </span>
                <span class="px-2 py-1 text-xs rounded
                    {{ $laundryOrder->customer_type === 'guest' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $laundryOrder->customer_type === 'guest' ? __('laundry.customer_type.guest') : __('laundry.customer_type.walkin') }}
                </span>
                @if($laundryOrder->isOverdue())
                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">⚠ {{ __('laundry.messages.overdue') }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Start processing (house_help, laundry_manager) --}}
                @if($laundryOrder->status === 'received' && auth()->user()->hasAnyRole(['house_help','laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.process', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                        {{ __('laundry.actions.start_processing') }}
                    </button>
                </form>
                @endif

                {{-- Submit for confirmation (house_help, laundry_manager) --}}
                @if($laundryOrder->status === 'processing' && auth()->user()->hasAnyRole(['house_help','laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.ready', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-500 text-white text-sm font-semibold rounded-xl hover:bg-purple-600 transition-all">
                        Submit for Confirmation
                    </button>
                </form>
                @endif

                {{-- Supervisor: Confirm --}}
                @if($laundryOrder->status === 'pending_confirmation' && auth()->user()->hasAnyRole(['supervisor','admin']))
                <form method="POST" action="{{ route('laundry.orders.confirm', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all">
                        Confirm Completion
                    </button>
                </form>
                @endif

                {{-- Guest: deliver to room (house_help, front_desk, laundry_manager) --}}
                @if($laundryOrder->status === 'ready' && $laundryOrder->customer_type === 'guest'
                    && auth()->user()->hasAnyRole(['house_help','front_desk','laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.deliver', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
                        {{ __('laundry.actions.deliver_to_room') }} {{ $laundryOrder->room_number }}
                    </button>
                </form>
                @endif

                {{-- Walk-in: mark collected (ready, delivered, or settled without collection) --}}
                @if(in_array($laundryOrder->status, ['ready', 'delivered']) && $laundryOrder->customer_type === 'walkin'
                    && auth()->user()->hasAnyRole(['house_help','front_desk','laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.collected', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-xl hover:bg-teal-700 transition-all">
                        {{ __('laundry.actions.mark_collected') }}
                    </button>
                </form>
                @endif
                @if($laundryOrder->status === 'settled' && !$laundryOrder->collected_at && $laundryOrder->customer_type === 'walkin'
                    && auth()->user()->hasAnyRole(['house_help','front_desk','laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.collected', $laundryOrder) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-xl hover:bg-teal-700 transition-all">
                        Mark Collected (Paid)
                    </button>
                </form>
                @endif

                {{-- Settle payment (front_desk, laundry_manager) --}}
                @if(!in_array($laundryOrder->status, ['settled', 'charged', 'cancelled']) && auth()->user()->hasAnyRole(['front_desk','laundry_manager','admin']))
                    @if($laundryOrder->customer_type === 'walkin')
                        {{-- Walk-in: Use unified payment modal (direct payment allowed) --}}
                        <x-walkin-payment-modal 
                            :amount="$laundryOrder->total" 
                            :order-id="$laundryOrder->id"
                            :order-number="$laundryOrder->order_number"
                            module="laundry"
                            :customer-name="$laundryOrder->customer_name ?? ''"
                            :customer-phone="$laundryOrder->customer_phone ?? ''"
                        />
                    @else
                        {{-- Guest: Charge to Booking button --}}
                        <button onclick="document.getElementById('settle-panel').classList.toggle('hidden')"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all">
                            Charge to Guest Folio
                        </button>
                    @endif
                @elseif($laundryOrder->status === 'charged')
                    {{-- Already charged - show link to checkout --}}
                    <a href="{{ route('finance.checkout.show', $laundryOrder->booking_id) }}"
                       class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-all">
                        View Guest Checkout
                    </a>
                @endif

                {{-- Cancel (laundry_manager, manager only) --}}
                @if(in_array($laundryOrder->status, ['received', 'processing', 'pending_confirmation', 'ready']) && auth()->user()->hasAnyRole(['laundry_manager','admin']))
                <form method="POST" action="{{ route('laundry.orders.cancel', $laundryOrder) }}"
                      onsubmit="return confirm('{{ __('laundry.messages.cancel_confirm') }}')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition-all">
                        {{ __('laundry.actions.cancel_order') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Settle panel (for guest orders only - walk-ins use the modal) --}}
    @if(!in_array($laundryOrder->status, ['settled', 'charged', 'cancelled']) && $laundryOrder->customer_type === 'guest')
    <div id="settle-panel" class="hidden bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            Charge to Guest Folio
        </h3>
        <form method="POST" action="{{ route('laundry.orders.settle', $laundryOrder) }}">
            @csrf
            <p class="text-sm text-gray-600 mb-4">
                This will add the laundry charges to the guest's folio. Payment will be collected at checkout.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                {{-- Discount field - Only visible to Laundry Manager and Manager --}}
                @if(auth()->user()->hasAnyRole(['laundry_manager', 'manager']))
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.discount') }}</label>
                    <div class="relative">
                        <input type="number" name="discount" value="0" min="0" step="100"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                </div>
                @else
                <div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-sm text-blue-800">
                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Discounts can only be applied by the Laundry Manager or Manager.
                        </p>
                    </div>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.booking_id') }}</label>
                    <input type="text" name="booking_id"
                           value="{{ $laundryOrder->booking_id }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                    Charge to Booking & Proceed to Checkout
                </button>
            </div>
        </form>
    </div>
    @elseif($laundryOrder->status === 'charged' && $laundryOrder->customer_type === 'guest')
    {{-- Order is charged but not yet settled at checkout --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Order Charged to Folio</h3>
        <p class="text-sm text-gray-600 mb-4">
            This laundry order has been added to the guest's folio. Payment pending at checkout.
        </p>
        <a href="{{ route('finance.checkout.show', $laundryOrder->booking_id) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:opacity-90 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            View Guest Checkout
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Items --}}
        <div class="md:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                <h3 class="text-sm font-bold text-primary uppercase tracking-wider">{{ __('laundry.sections.order_items') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.service') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.item') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.qty') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.fields.unit_price') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.fields.subtotal') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.fields.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($laundryOrder->items as $item)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->serviceItem->service->name }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-secondary">{{ $item->serviceItem->item_name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">@currency($item->unit_price, 'TZS')</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-secondary">@currency($item->subtotal, 'TZS')</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-100">
                        @if($laundryOrder->discount > 0)
                        <tr>
                            <td colspan="4"></td>
                            <td class="px-6 py-3 text-right text-sm text-gray-500">{{ __('laundry.fields.discount') }}</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-red-600">- {{ number_format($laundryOrder->discount, 0) }}</td>
                            <td></td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4"></td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-secondary">{{ __('laundry.info.total_tzs') }}</td>
                            <td class="px-6 py-3 text-right text-lg font-extrabold text-primary">@currency($laundryOrder->total, 'TZS')</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Order Info Sidebar --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">{{ __('laundry.sections.order_information') }}</h3>
            <dl class="space-y-4">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.received_by') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->receiver->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.received_at') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.expected_ready') }}</dt>
                    <dd class="font-medium {{ $laundryOrder->isOverdue() ? 'text-red-500' : 'text-secondary' }}">
                        {{ $laundryOrder->expected_ready_at?->format('M d, Y H:i') }}
                        @if($laundryOrder->isOverdue()) ⚠ @endif
                    </dd>
                </div>
                @if($laundryOrder->processed_by)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.processed_by') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->processor->name ?? '—' }}</dd>
                </div>
                @endif
                @if($laundryOrder->confirmed_by)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Confirmed By</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->confirmer->name ?? '—' }}</dd>
                </div>
                @endif
                @if($laundryOrder->confirmed_at)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Confirmed At</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->confirmed_at->format('M d, Y H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->ready_at)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.ready_at') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->ready_at->format('M d, Y H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->delivered_at)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.delivered_at') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->delivered_at->format('M d, Y H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->collected_at)
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">{{ __('laundry.info.collected_at') }}</dt>
                    <dd class="font-medium text-secondary">{{ $laundryOrder->collected_at->format('M d, Y H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->status === 'settled')
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">{{ __('laundry.info.payment') }}</dt>
                        <dd class="font-medium text-green-700">{{ ucwords(str_replace('_', ' ', $laundryOrder->payment_method)) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <dt class="text-gray-500">{{ __('laundry.info.settled_by') }}</dt>
                        <dd class="font-medium text-secondary">{{ $laundryOrder->settler->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <dt class="text-gray-500">{{ __('laundry.info.settled_at') }}</dt>
                        <dd class="font-medium text-secondary">{{ $laundryOrder->settled_at->format('M d, Y H:i') }}</dd>
                    </div>
                </div>
                @endif
            </dl>
        </div>

        {{-- Special Instructions --}}
        @if($laundryOrder->special_instructions)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">{{ __('laundry.fields.special_instructions') }}</h3>
            <p class="text-sm text-gray-600">{{ $laundryOrder->special_instructions }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
