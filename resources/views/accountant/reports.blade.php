@extends('layouts.app')

@section('title', __('accountant.sidebar.reports'))
@section('page-title', __('accountant.sidebar.reports'))

@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('accountant.reports') }}" class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.report_center.filter_period') }}</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-xl border-gray-200 text-sm">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.date_to') }}</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-xl border-gray-200 text-sm">
            </div>
            <div class="flex gap-2">
                <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('accountant.report_center.apply') }}</button>
                <a href="{{ route('accountant.reports') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">{{ __('general.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_revenue') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$reportMetrics['totalRevenue']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_expenses') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$reportMetrics['totalExpenses']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.net_profit') }}</div><div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$reportMetrics['netProfit']" /></div></div>
    </div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.financial_reports') }}</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($reportLinks as $report)
                <a href="{{ route($report['route'], $report['query'] ?? []) }}" class="rounded-2xl border border-gray-100 bg-gray-50 p-5 transition hover:border-indigo-200 hover:bg-indigo-50"><div class="text-lg font-bold text-secondary">{{ $report['label'] }}</div><div class="mt-2 text-sm text-gray-500">{{ __('accountant.labels.open_report') }}</div></a>
            @endforeach
        </div>
    </div>
</div>
@endsection
