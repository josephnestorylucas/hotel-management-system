@extends('finance.layout')

@section('title', 'Refund Payment')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Process Refund</h1>
            <p class="text-gray-500 text-sm mt-1">Payment {{ $payment->payment_number }}</p>
        </div>
        <a href="{{ route('finance.refunds.index') }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            Back to Refunds
        </a>
    </div>

    {{-- Payment Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Payment Details</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Payment Number</p>
                    <p class="font-mono text-gray-900">{{ $payment->payment_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Provider Reference</p>
                    <p class="font-mono text-gray-900">{{ $payment->provider_reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Booking</p>
                    @if($payment->booking)
                    <a href="{{ route('bookings.show', $payment->booking_id) }}" class="text-indigo-600 hover:underline">
                        {{ $payment->booking->booking_number }}
                    </a>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-500">Guest</p>
                    @if($payment->booking?->guest)
                    <p class="text-gray-900">{{ $payment->booking->guest->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $payment->booking->guest->phone_number }}</p>
                    @else
                    <span class="text-gray-400">Guest</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Method</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ $payment->payment_method === 'mobile' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($payment->payment_method) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Date</p>
                    <p class="text-gray-900">{{ $payment->payment_date?->format('M d, Y H:i') ?? $payment->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            {{-- Amount Summary --}}
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-500">Original Amount</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Already Refunded</p>
                        <p class="text-xl font-bold {{ $totalRefunded > 0 ? 'text-red-600' : 'text-gray-400' }}">
                            {{ number_format($totalRefunded, 2) }} {{ $payment->currency }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Max Refundable</p>
                        <p class="text-xl font-bold text-green-600">{{ number_format($maxRefundable, 2) }} {{ $payment->currency }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Refund Form --}}
    @if($validation['valid'])
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Process Refund</h2>
        </div>
        <form action="{{ route('finance.refunds.payment.process', $payment) }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Refund Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Refund Type</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="refund_type" value="full" class="text-indigo-600 focus:ring-indigo-500" checked onchange="togglePartialAmount(false)">
                        <span class="text-sm text-gray-900">Full Refund ({{ number_format($maxRefundable, 2) }} {{ $payment->currency }})</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="refund_type" value="partial" class="text-indigo-600 focus:ring-indigo-500" onchange="togglePartialAmount(true)">
                        <span class="text-sm text-gray-900">Partial Refund</span>
                    </label>
                </div>
            </div>

            {{-- Partial Amount --}}
            <div id="partial-amount-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount</label>
                <div class="relative max-w-xs">
                    <input type="number" name="refund_amount" id="refund_amount" step="0.01" min="0.01" max="{{ $maxRefundable }}"
                        class="w-full rounded-lg border-gray-300 pr-16 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="0.00">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500">{{ $payment->currency }}</span>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Maximum: {{ number_format($maxRefundable, 2) }} {{ $payment->currency }}</p>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Refund (Optional)</label>
                <textarea name="reason" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Enter the reason for this refund..."></textarea>
            </div>

            {{-- Warning --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Important Notice</p>
                        <p class="text-sm text-yellow-700 mt-1">
                            Refunds are processed through Snippe and may take 1-5 business days to reflect in the customer's account.
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('finance.refunds.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                    Process Refund
                </button>
            </div>
        </form>
    </div>
    @else
    {{-- Cannot Refund --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex gap-3">
            <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-red-800">Cannot Process Refund</h3>
                <p class="text-red-700 mt-1">{{ $validation['error'] }}</p>
                <a href="{{ route('finance.refunds.index') }}" class="inline-block mt-4 text-sm text-red-700 hover:text-red-800 underline">
                    Return to Refunds List
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Refund History --}}
    @if($totalRefunded > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Refund History</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @if($payment->refund_metadata)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Last Refund</p>
                        <p class="text-xs text-gray-500">{{ $payment->refund_metadata['last_refund_at'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-600">-{{ number_format($payment->refund_metadata['last_refund_amount'] ?? 0, 2) }} {{ $payment->currency }}</p>
                        @if($payment->refund_metadata['refund_reason'] ?? null)
                        <p class="text-xs text-gray-500">{{ $payment->refund_metadata['refund_reason'] }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function togglePartialAmount(show) {
    const field = document.getElementById('partial-amount-field');
    const input = document.getElementById('refund_amount');
    if (show) {
        field.classList.remove('hidden');
        input.required = true;
    } else {
        field.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}
</script>
@endpush
@endsection
