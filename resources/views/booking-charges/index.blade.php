{{-- resources/views/booking-charges/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Charges - MRK Hotel')
@section('page-title', 'Booking Charges')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Booking Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Charges for Booking #{{ $booking->booking_number }}</h2>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    <span>Guest: <strong class="text-gray-900">{{ $booking->guest->first_name ?? '' }} {{ $booking->guest->last_name ?? '' }}</strong></span>
                    <span>Room: <strong class="text-gray-900">{{ $booking->room->room_number ?? 'N/A' }}</strong></span>
                    <span>Status:
                        @switch($booking->status)
                            @case('checked_in')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Checked In</span>
                                @break
                            @case('checked_out')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Checked Out</span>
                                @break
                            @default
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                        @endswitch
                    </span>
                </div>
            </div>
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                &larr; Back
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Charges</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($unpaidTotal + $paidTotal) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-5">
            <p class="text-xs text-yellow-600 uppercase tracking-wider mb-1">Unpaid</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($unpaidTotal) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-green-200 p-5">
            <p class="text-xs text-green-600 uppercase tracking-wider mb-1">Paid</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($paidTotal) }}</p>
        </div>
    </div>

    {{-- Proceed to Checkout Button --}}
    @if($unpaidTotal > 0)
    <div class="flex justify-end">
        <a href="{{ route('finance.checkout.show', $booking) }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Proceed to Checkout ({{ number_format($unpaidTotal) }} TZS)
        </a>
    </div>
    @endif

    {{-- Charges Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($charges as $charge)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                @switch($charge->charge_type)
                                    @case('laundry')
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        @break
                                    @case('restaurant')
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        @break
                                    @case('bar')
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        @break
                                    @case('room_service')
                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                        @break
                                    @case('damage')
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        @break
                                    @case('minibar')
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        @break
                                    @default
                                        <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                @endswitch
                                <span class="text-sm font-medium text-gray-900">{{ $charge->charge_type_label }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $charge->description }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="font-bold text-gray-900">{{ number_format($charge->amount) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($charge->status === 'paid')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $charge->created_at->format('M d, H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                            No charges recorded for this booking.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Unified Checkout Flow</p>
                <p class="mt-1">All charges are settled through the Finance Checkout. Click "Proceed to Checkout" to process payment for all unpaid charges at once.</p>
            </div>
        </div>
    </div>
</div>
@endsection
