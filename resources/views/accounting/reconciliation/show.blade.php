@extends('layouts.app')

@section('title', $rec->reference_no)
@section('page-title', 'Reconciliation Details')

@section('content')
<div class="grid gap-4 md:grid-cols-4">
    <div class="rounded-2xl bg-white p-5 shadow-sm md:col-span-2">
        <div class="text-sm text-gray-500">Reference</div>
        <div class="mt-2 text-2xl font-extrabold text-secondary">{{ $rec->reference_no }}</div>
        <div class="mt-2 text-sm text-gray-500">{{ $rec->account?->name }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Closing balance</div><div class="mt-2 text-lg font-bold text-secondary"><x-money :amount="$rec->statement_closing_balance" /></div></div>
    <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Difference</div><div class="mt-2 text-lg font-bold text-secondary"><x-money :amount="$rec->difference" /></div></div>
</div>
@endsection
