{{-- resources/views/laundry/services/index.blade.php --}}
@extends('layouts.app')

@section('title', __('laundry.laundry_price_list'))
@section('page-title', __('laundry.title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('laundry.laundry_price_list') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('laundry.price_list_subtitle') }}</p>
        </div>
    </div>

    @foreach($services as $service)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Service Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-secondary">{{ $service->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $service->description }} · {{ $service->turnaround_hours }}{{ __('laundry.turnaround') }}</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                    🧺
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.item') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.price') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">{{ __('laundry.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($service->serviceItems as $item)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-secondary">{{ $item->item_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST"
                                  action="{{ route('laundry.services.update-item', [$service, $item]) }}"
                                  class="flex items-center justify-end gap-3">
                                @csrf @method('PUT')
                                <div class="relative w-36">
                                    <input type="number" name="price" value="{{ $item->price }}"
                                           min="1" step="100"
                                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm text-right transition-all">
                                </div>
                                <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                    {{ __('laundry.actions.save') }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST"
                                  action="{{ route('laundry.services.remove-item', [$service, $item]) }}"
                                  onsubmit="return confirm('{{ __('laundry.confirm_remove', ['item' => $item->item_name]) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-2 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-xl transition-all">
                                    {{ __('laundry.actions.remove') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Add item form --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('laundry.services.add-item', $service) }}"
                  class="flex gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.new_item.name') }}</label>
                    <input type="text" name="item_name" required placeholder="e.g. Blazer"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.new_item.price') }}</label>
                    <input type="number" name="price" required min="1" step="100" placeholder="5000"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
                </div>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('laundry.actions.add_item') }}
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
