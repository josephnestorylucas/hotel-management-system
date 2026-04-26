@extends('layouts.app')

@section('title', __('accountant.ap.create_payment_title'))
@section('page-title', __('accountant.ap.create_payment_title'))

@section('content')
<form method="POST" action="{{ route('accountant.payments.store') }}" class="mx-auto max-w-3xl space-y-6 rounded-2xl bg-white p-6 shadow-sm sm:p-7">
    @csrf
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <div class="font-semibold">{{ __('accountant.ap.form_error_title') }}</div>
            <ul class="mt-2 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
        {{ __('accountant.ap.create_payment_help') }}
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.name') }}</label>
            <select id="supplier_id" name="supplier_id" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                <option value="">{{ __('accountant.ap.select_supplier') }}</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') === $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.select_grn_payable') }}</label>
            <select id="supplier_payable_id" name="supplier_payable_id" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">{{ __('accountant.ap.select_grn_payable_optional') }}</option>
                @foreach($grnPayables as $payable)
                    <option
                        value="{{ $payable->id }}"
                        data-supplier-id="{{ $payable->supplier_id }}"
                        @selected(old('supplier_payable_id') === $payable->id)
                    >
                        {{ $payable->reference }} - {{ $payable->supplier?->name }} - {{ __('accountant.labels.balance') }}: {{ number_format((float) $payable->balance, 2) }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">{{ __('accountant.ap.select_grn_payable_help') }}</p>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.date') }}</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.amount') }}</label>
            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.payment_method') }}</label>
            <select name="method" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                @foreach(['cash', 'bank', 'mobile', 'card'] as $method)
                    <option value="{{ $method }}" @selected(old('method') === $method)>{{ ucfirst($method) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.currency') }}</label>
            <select name="currency" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                @foreach(['USD', 'TZS'] as $currency)
                    <option value="{{ $currency }}" @selected(old('currency', \App\Helpers\CurrencyHelper::getDefaultCurrency()) === $currency)>{{ $currency }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.payment_reference') }}</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.notes') }}</label>
        <textarea name="notes" rows="4" class="block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-secondary shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('notes') }}</textarea>
    </div>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('accountant.payables.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">{{ __('general.cancel') }}</a>
        <button class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">{{ __('accountant.ap.save_draft') }}</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const supplierSelect = document.getElementById('supplier_id');
    const payableSelect = document.getElementById('supplier_payable_id');

    if (!supplierSelect || !payableSelect) {
        return;
    }

    const allOptions = Array.from(payableSelect.options).slice(1);

    const filterPayablesBySupplier = function () {
        const supplierId = supplierSelect.value;
        const current = payableSelect.value;

        allOptions.forEach((option) => {
            option.hidden = supplierId !== '' && option.dataset.supplierId !== supplierId;
        });

        const selectedOption = payableSelect.selectedOptions[0];
        if (selectedOption && selectedOption.hidden) {
            payableSelect.value = '';
        }

        if (current && payableSelect.value === '') {
            const candidate = allOptions.find((option) => option.value === current && !option.hidden);
            if (candidate) {
                payableSelect.value = candidate.value;
            }
        }
    };

    supplierSelect.addEventListener('change', filterPayablesBySupplier);
    payableSelect.addEventListener('change', function () {
        const selected = payableSelect.selectedOptions[0];
        if (selected && selected.dataset.supplierId) {
            supplierSelect.value = selected.dataset.supplierId;
            filterPayablesBySupplier();
        }
    });

    filterPayablesBySupplier();
});
</script>
@endsection
