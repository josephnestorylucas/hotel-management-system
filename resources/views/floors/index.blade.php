{{-- resources/views/floors/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Floors')
@section('page-title', 'Floors')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Floors</h2>
            <p class="text-sm text-gray-500 mt-1">Manage building floors</p>
        </div>
        <a href="{{ route('floors.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Floor
        </a>
    </div>

    <!-- Floors Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Floor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Building</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Floor Number</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Rooms</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($floors as $floor)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-secondary">{{ $floor->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-secondary">{{ $floor->building->name }}</div>
                        <div class="text-xs text-primary">{{ $floor->building->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-secondary">{{ $floor->floor_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 bg-primary/10 rounded-lg text-sm font-bold text-primary">{{ $floor->rooms->count() }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $floor->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $floor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('floors.edit', $floor) }}" class="text-primary hover:text-blue-700 font-semibold">Edit</a>
                            <form method="POST" action="{{ route('floors.destroy', $floor) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-secondary">No floors yet</h3>
                        <p class="mt-2 text-sm text-gray-500">Get started by creating your first floor.</p>
                        <div class="mt-6">
                            <a href="{{ route('floors.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Floor
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($floors->hasPages())
    <div class="mt-6">
        {{ $floors->links() }}
    </div>
    @endif
</div>
@endsection