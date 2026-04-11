{{-- resources/views/dashboards/store-keeper.blade.php --}}
@extends('layouts.app')

@section('title', __('dashboard.titles.store_keeper'))
@section('page-title', __('dashboard.titles.store_keeper'))

@section('content')
<div class="bg-gradient-to-r from-teal-500 to-teal-700 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('dashboard.welcome.greeting', ['name' => auth()->user()->name]) }}</h2>
            <p class="text-teal-100">{{ __('dashboard.welcome.store_keeper_message') }}</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-teal-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.active_products') }}</p>
        <p class="text-3xl font-extrabold text-teal-700 mt-1">{{ $stats['active_products'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.low_stock_items') }}</p>
        <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['low_stock_items'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.pending_internal_requests') }}</p>
        <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['pending_internal_requests'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <p class="text-sm font-medium text-gray-500">{{ __('dashboard.stats.pending_transfers') }}</p>
        <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['pending_transfers'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.low_stock_watchlist') }}</h3>
        <div class="space-y-3">
            @forelse($lowStockProducts as $stock)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                    <div>
                        <p class="font-semibold text-secondary">{{ $stock->product?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $stock->location?->name }} · {{ $stock->product?->unit }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-red-600">{{ number_format((float) $stock->available_qty, 3) }}</p>
                        <p class="text-xs text-gray-500">{{ __('dashboard.stats.need_reordering') }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">{{ __('dashboard.messages.no_low_stock_items') }}</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.pending_internal_requests') }}</h3>
        <div class="space-y-3">
            @forelse($pendingInternalRequests as $request)
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl border border-amber-100">
                    <div>
                        <p class="font-semibold text-secondary">{{ $request->product?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $request->department }} · {{ $request->requester?->name ?? __('dashboard.messages.not_assigned') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-amber-700">{{ number_format((float) $request->quantity, 3) }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($request->status) }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">{{ __('dashboard.messages.no_pending_internal_requests') }}</div>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('dashboard.sections.recent_store_transfers') }}</h3>
        <a href="{{ route('store.transfers.index') }}" class="text-sm text-primary hover:text-blue-700 font-semibold">{{ __('dashboard.actions.view_all') }}</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">{{ __('dashboard.table.product') }}</th>
                    <th class="pb-3 pr-4">{{ __('dashboard.table.quantity') }}</th>
                    <th class="pb-3 pr-4">{{ __('dashboard.table.status') }}</th>
                    <th class="pb-3 pr-4">{{ __('dashboard.table.date') }}</th>
                    <th class="pb-3">{{ __('dashboard.table.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentTransfers as $transfer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 pr-4">
                            <div class="font-semibold text-secondary">{{ $transfer->product?->name }}</div>
                            <div class="text-xs text-gray-500">{{ $transfer->fromLocation?->name }} -> {{ $transfer->toLocation?->name }}</div>
                        </td>
                        <td class="py-3 pr-4 font-semibold text-secondary">{{ number_format((float) $transfer->quantity, 3) }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transfer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($transfer->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($transfer->status) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-sm text-gray-600">{{ $transfer->created_at?->format('M d, Y') }}</td>
                        <td class="py-3">
                            <a href="{{ route('store.transfers.index') }}" class="text-primary hover:text-blue-700 font-semibold">{{ __('dashboard.actions.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">{{ __('dashboard.messages.no_recent_store_transfers') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">{{ __('dashboard.sections.quick_actions') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('store.products.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl border border-teal-200 hover:shadow-lg transition-all group">
            <span class="text-sm font-semibold text-teal-700">{{ __('general.nav.products') }}</span>
        </a>
        <a href="{{ route('store.stock.levels') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:shadow-lg transition-all group">
            <span class="text-sm font-semibold text-blue-700">{{ __('general.nav.stock_levels') }}</span>
        </a>
        <a href="{{ route('store.internal-requests.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200 hover:shadow-lg transition-all group">
            <span class="text-sm font-semibold text-amber-700">{{ __('general.nav.internal_requests') }}</span>
        </a>
        <a href="{{ route('store.transfers.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 hover:shadow-lg transition-all group">
            <span class="text-sm font-semibold text-indigo-700">{{ __('general.nav.transfers') }}</span>
        </a>
    </div>
</div>
@endsection
