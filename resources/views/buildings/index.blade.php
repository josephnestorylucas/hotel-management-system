{{-- resources/views/buildings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Buildings')
@section('page-title', 'Buildings')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Buildings</h2>
            <p class="text-sm text-gray-500 mt-1">Manage your hotel buildings and properties</p>
        </div>
        <a href="{{ route('buildings.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Building
        </a>
    </div>

    <!-- Buildings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($buildings as $building)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary">{{ $building->name }}</h3>
                            <p class="text-sm text-primary font-medium">{{ $building->code }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $building->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $building->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Address -->
                @if($building->address)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $building->address }}</p>
                @endif

                <!-- Stats -->
                <div class="flex items-center gap-4 py-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/>
                            </svg>
                        </div>
                        <span class="text-gray-600 font-medium">{{ $building->floors_count }} Floors</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 mt-4">
                    <a href="{{ route('buildings.edit', $building) }}" 
                       class="flex-1 text-center px-4 py-2.5 text-sm font-semibold text-primary bg-primary/10 rounded-xl hover:bg-primary/20 transition-colors">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('buildings.destroy', $building) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this building?')"
                                class="w-full px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-secondary">No buildings yet</h3>
                <p class="mt-2 text-sm text-gray-500">Get started by creating your first building.</p>
                <div class="mt-6">
                    <a href="{{ route('buildings.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Building
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($buildings->hasPages())
    <div class="mt-6">
        {{ $buildings->links() }}
    </div>
    @endif
</div>
@endsection