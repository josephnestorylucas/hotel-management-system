@extends('laundry.layout')

@section('title', 'Price List — Laundry')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laundry Price List</h1>
</div>

@foreach($services as $service)
<div class="bg-white rounded shadow mb-6">
    <div class="px-5 py-4 border-b flex justify-between items-center">
        <div>
            <h2 class="font-semibold text-gray-800 text-lg">{{ $service->name }}</h2>
            <p class="text-sm text-gray-400">{{ $service->description }} · {{ $service->turnaround_hours }}h turnaround</p>
        </div>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-gray-500">Item</th>
                <th class="px-4 py-2 text-right text-gray-500">Price (TZS)</th>
                <th class="px-4 py-2 text-right text-gray-500 w-48">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($service->serviceItems as $item)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $item->item_name }}</td>
                <td class="px-4 py-3 text-right">
                    <form method="POST"
                          action="{{ route('laundry.services.update-item', [$service, $item]) }}"
                          class="inline-flex items-center gap-2">
                        @csrf @method('PUT')
                        <input type="number" name="price" value="{{ $item->price }}"
                               min="1" step="100"
                               class="w-28 border rounded px-2 py-1 text-right text-sm">
                        <button class="text-blue-600 hover:text-blue-800 text-xs font-medium">Save</button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right">
                    <form method="POST"
                          action="{{ route('laundry.services.remove-item', [$service, $item]) }}"
                          onsubmit="return confirm('Remove {{ $item->item_name }}?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Add item form --}}
    <div class="px-5 py-3 border-t bg-gray-50">
        <form method="POST" action="{{ route('laundry.services.add-item', $service) }}"
              class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">New Item Name</label>
                <input type="text" name="item_name" required placeholder="e.g. Blazer"
                       class="w-full border rounded px-3 py-1.5 text-sm">
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Price (TZS)</label>
                <input type="number" name="price" required min="1" step="100" placeholder="5000"
                       class="w-full border rounded px-3 py-1.5 text-sm">
            </div>
            <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">
                + Add
            </button>
        </form>
    </div>
</div>
@endforeach
@endsection
