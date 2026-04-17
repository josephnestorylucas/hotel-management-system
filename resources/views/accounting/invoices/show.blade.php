@extends('layouts.app')

@section('title', $invoice->invoice_no)
@section('page-title', 'Invoice Details')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm md:col-span-2">
            <div class="text-sm text-gray-500">Invoice</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary">{{ $invoice->invoice_no }}</div>
            <div class="mt-2 text-sm text-gray-500">{{ $invoice->guest_name }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Date</div><div class="mt-2 text-lg font-bold text-secondary">{{ $invoice->invoice_date?->format('M d, Y') }}</div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Total</div><div class="mt-2 text-lg font-bold text-secondary"><x-money :amount="$invoice->total" /></div></div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">Line items</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Description</th>
                        <th class="px-4 py-3 font-semibold text-right">Qty</th>
                        <th class="px-4 py-3 font-semibold text-right">Unit price</th>
                        <th class="px-4 py-3 font-semibold text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoice->lines as $line)
                        <tr>
                            <td class="px-4 py-3">{{ $line->description }}</td>
                            <td class="px-4 py-3 text-right">{{ $line->quantity }}</td>
                            <td class="px-4 py-3 text-right"><x-money :amount="$line->unit_price" /></td>
                            <td class="px-4 py-3 text-right font-semibold"><x-money :amount="$line->subtotal" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No line items recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
