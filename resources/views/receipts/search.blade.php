@extends('layouts.app')

@section('title', __('general.receipt.title') . ' - ' . __('general.search'))
@section('page-title', __('general.receipt.title') . ' ' . __('general.search'))

@section('content')
<div class="space-y-6">
    <form method="GET" class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="flex-1 rounded-xl border-gray-200 text-sm" placeholder="{{ __('accountant.receipts.search_customer') }}">
            <select name="module" class="rounded-xl border-gray-200 text-sm">
                <option value="">{{ __('accountant.receipts.all_modules') }}</option>
                <option value="laundry" @selected(($module ?? '') === 'laundry')>{{ __('accountant.receipts.module_laundry') }}</option>
                <option value="restaurant" @selected(($module ?? '') === 'restaurant')>{{ __('accountant.receipts.module_restaurant') }}</option>
                <option value="bar" @selected(($module ?? '') === 'bar')>{{ __('accountant.receipts.module_bar') }}</option>
                <option value="checkout" @selected(($module ?? '') === 'checkout')>{{ __('accountant.receipts.module_checkout') }}</option>
                <option value="walkin" @selected(($module ?? '') === 'walkin')>{{ __('accountant.receipts.module_walkin') }}</option>
                <option value="procurement" @selected(($module ?? '') === 'procurement')>{{ __('general.nav.procurement') }}</option>
                <option value="store" @selected(($module ?? '') === 'store')>{{ __('general.nav.store') }}</option>
            </select>
            <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('general.search') }}</button>
        </div>
    </form>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        @if(isset($receipts) && count($receipts) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.receipts.receipt_no') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.module') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.issued_at') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.customer') }}</th>
                        <th class="pb-3 text-right">{{ __('general.amount') }}</th>
                        <th class="pb-3">{{ __('general.status') }}</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $receipt)
                    <tr class="border-b border-gray-50">
                        <td class="py-3 font-medium">{{ $receipt->receipt_number }}</td>
                        <td class="py-3"><span class="module-badge" style="display:inline-block;background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:bold;text-transform:uppercase;">{{ $receipt->module_label }}</span></td>
                        <td class="py-3 text-gray-500">{{ $receipt->issued_at?->format('d M Y H:i') }}</td>
                        <td class="py-3">{{ $receipt->customer_name ?? '—' }}</td>
                        <td class="py-3 text-right font-medium">{{ number_format($receipt->total, 0) }} {{ $receipt->currency }}</td>
                        <td class="py-3">
                            <span class="status-badge status-{{ $receipt->payment_status }}" style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:bold;text-transform:uppercase;
                                @if($receipt->payment_status === 'paid') background:#dcfce7;color:#166534;
                                @elseif($receipt->payment_status === 'partial') background:#fef3c7;color:#92400e;
                                @elseif($receipt->payment_status === 'refunded') background:#f3e8ff;color:#6b21a8;
                                @else background:#fee2e2;color:#991b1b;
                                @endif
                            ">{{ $receipt->payment_status_label }}</span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('receipts.show', $receipt->uuid) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">{{ __('accountant.receipts.print') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-8 text-center text-gray-500">
            @if(!empty($query))
                {{ __('general.no_results') }}
            @else
                {{ __('general.no_data') }}
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
