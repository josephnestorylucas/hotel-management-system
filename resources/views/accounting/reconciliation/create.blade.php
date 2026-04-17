@extends('layouts.app')

@section('title', 'Create Reconciliation')
@section('page-title', 'Create Reconciliation')

@section('content')
<form method="POST" action="{{ route('accounting.reconciliation.store') }}" class="mx-auto max-w-4xl space-y-6 rounded-2xl bg-white p-6 shadow-sm">
    @csrf
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Bank account</label>
            <select name="account_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                <option value="">Select bank account</option>
                @foreach($bankAccounts as $bankAccount)
                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->code }} - {{ $bankAccount->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Period month</label>
            <input type="month" name="period_month" value="{{ old('period_month') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Statement date</label>
            <input type="date" name="statement_date" value="{{ old('statement_date', now()->toDateString()) }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Opening balance</label>
            <input type="number" step="0.01" name="statement_opening_balance" value="{{ old('statement_opening_balance') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Closing balance</label>
            <input type="number" step="0.01" name="statement_closing_balance" value="{{ old('statement_closing_balance') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">Notes</label>
        <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-200 text-sm">{{ old('notes') }}</textarea>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('accounting.reconciliation.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white">Create reconciliation</button>
    </div>
</form>
@endsection
