@extends('layouts.app')

@section('title', __('accountant.reports.vat'))
@section('page-title', __('accountant.reports.vat'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Output VAT</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$outputVat" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Input VAT</div><div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$inputVat" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">VAT Payable</div><div class="mt-2 text-2xl font-extrabold text-amber-600"><x-money :amount="$vatPayable" /></div></div>
    </div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">Entry</th><th class="pb-3">Account</th><th class="pb-3">Date</th><th class="pb-3 text-right">Amount</th></tr></thead>
                <tbody>
                    @forelse($vatLines as $line)
                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold">{{ $line->entry?->entry_no }}</td><td class="py-3">{{ $line->account?->name }}</td><td class="py-3">{{ $line->entry?->entry_date?->format('M d, Y') }}</td><td class="py-3 text-right"><x-money :amount="$line->amount" /></td></tr>
                    @empty
                        <x-empty-state table colspan="4" title="No VAT entries found" message="VAT lines will appear here once journal entries are posted." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
