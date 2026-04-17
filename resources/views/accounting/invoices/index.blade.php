@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-secondary">Invoices</h2>
        <p class="mt-1 text-sm text-gray-500">Browse issued guest and service invoices from the accounting module.</p>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Invoice</th>
                    <th class="px-6 py-4 font-semibold">Guest</th>
                    <th class="px-6 py-4 font-semibold">Date</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('accounting.invoices.show', $invoice) }}" class="font-semibold text-indigo-600">{{ $invoice->invoice_no }}</a></td>
                        <td class="px-6 py-4">{{ $invoice->guest_name }}</td>
                        <td class="px-6 py-4">{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($invoice->status) }}</span></td>
                        <td class="px-6 py-4 text-right font-semibold"><x-money :amount="$invoice->total" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No invoices available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
@endsection
