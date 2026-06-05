{{-- resources/views/laundry-items/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laundry Items - Hotel Management System')
@section('page-title', 'Laundry Items')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laundry Pricing</h2>
            <p class="text-sm text-gray-500 mt-1">Manage clothing types and their prices</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <a href="{{ route('laundry-items.archived') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                View Deleted
            </a>
            @endif
            <a href="{{ route('laundry-items.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Item
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900">{{ $item->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-green-600">{{ number_format($item->price) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $item->description ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->is_active)
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('laundry-items.edit', $item) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                                <form action="{{ route('laundry-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this item?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <x-empty-state title="No laundry items found" message="Add one to begin managing laundry inventory.">
                                <a href="{{ route('laundry-items.create') }}" class="text-blue-600 hover:underline">Add one</a>
                            </x-empty-state>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
