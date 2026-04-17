@extends('restaurant.layout')

@section('title', __('general.restaurant.buffet.nav'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('general.restaurant.buffet.nav') }}</h1>
        <a href="{{ route('restaurant.buffet.create') }}" class="px-4 py-2 bg-primary text-white rounded text-sm">
            {{ __('general.restaurant.buffet.new_sale') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('general.restaurant.buffet.package_setup') }}</h2>
        <form method="POST" action="{{ route('restaurant.buffet.packages.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input name="name" class="border-gray-300 rounded px-3 py-2 text-sm md:col-span-2" placeholder="{{ __('general.restaurant.buffet.fields.package_name') }}" required>
            <input type="number" step="0.01" min="0.01" name="adult_price" class="border-gray-300 rounded px-3 py-2 text-sm" placeholder="{{ __('general.restaurant.buffet.fields.adult_price') }}" required>
            <input type="number" step="0.01" min="0" name="child_price" class="border-gray-300 rounded px-3 py-2 text-sm" placeholder="{{ __('general.restaurant.buffet.fields.child_price') }}">
            <input type="time" name="start_time" class="border-gray-300 rounded px-3 py-2 text-sm">
            <input type="time" name="end_time" class="border-gray-300 rounded px-3 py-2 text-sm">
            <div class="md:col-span-6 grid grid-cols-2 md:grid-cols-7 gap-2">
                @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                    <label class="text-xs inline-flex items-center gap-1">
                        <input type="checkbox" name="available_days[]" value="{{ $day }}">
                        {{ __('general.days.' . $day) }}
                    </label>
                @endforeach
            </div>
            <div class="md:col-span-6">
                <button class="px-4 py-2 bg-green-600 text-white rounded text-sm">{{ __('general.create') }}</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">{{ __('general.name') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.adult_price') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.child_price') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.schedule') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.status') }}</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($packages as $package)
                    <tr>
                        <td colspan="6" class="px-4 py-3">
                            <form method="POST" action="{{ route('restaurant.buffet.packages.update', $package) }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-start">
                                @csrf
                                @method('PUT')
                                <input name="name" value="{{ $package->name }}" class="border-gray-300 rounded px-3 py-2 text-sm md:col-span-2" required>
                                <input type="number" step="0.01" min="0.01" name="adult_price" value="{{ $package->adult_price }}" class="border-gray-300 rounded px-3 py-2 text-sm" required>
                                <input type="number" step="0.01" min="0" name="child_price" value="{{ $package->child_price }}" class="border-gray-300 rounded px-3 py-2 text-sm">
                                <input type="time" name="start_time" value="{{ $package->start_time ? substr((string) $package->start_time, 0, 5) : '' }}" class="border-gray-300 rounded px-3 py-2 text-sm">
                                <input type="time" name="end_time" value="{{ $package->end_time ? substr((string) $package->end_time, 0, 5) : '' }}" class="border-gray-300 rounded px-3 py-2 text-sm">
                                <div class="md:col-span-4 grid grid-cols-2 md:grid-cols-7 gap-2 text-xs text-gray-600">
                                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                        <label class="inline-flex items-center gap-1">
                                            <input type="checkbox" name="available_days[]" value="{{ $day }}" @checked(collect($package->available_days ?? [])->contains($day))>
                                            {{ __('general.days.' . $day) }}
                                        </label>
                                    @endforeach
                                </div>
                                <div class="md:col-span-1 flex items-center gap-2 text-xs">
                                    <label class="inline-flex items-center gap-1">
                                        <input type="checkbox" name="is_active" value="1" @checked($package->is_active)>
                                        {{ __('general.active') }}
                                    </label>
                                </div>
                                <div class="md:col-span-1 flex justify-end gap-2">
                                    <button class="px-3 py-2 bg-primary text-white rounded text-xs">{{ __('general.save') }}</button>
                                    @if($package->is_active)
                                        <button form="deactivate-{{ $package->id }}" class="px-3 py-2 text-red-600 text-xs">{{ __('general.delete') }}</button>
                                    @endif
                                </div>
                            </form>
                            @if($package->is_active)
                                <form id="deactivate-{{ $package->id }}" method="POST" action="{{ route('restaurant.buffet.packages.deactivate', $package) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

