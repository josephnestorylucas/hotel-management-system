{{-- resources/views/buildings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Buildings')
@section('page-title', 'Buildings')

@section('content')
<div x-data="buildingManager()" class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Buildings</h2>
            <p class="text-sm text-gray-500 mt-1">Manage your hotel buildings and properties</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <a href="{{ route('buildings.archived') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                View Archived
            </a>
            @endif
            <a href="{{ route('buildings.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Building
            </a>
        </div>
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
                    <button type="button"
                            @click="confirmDelete('{{ route('buildings.destroy', $building) }}', '{{ addslashes($building->name) }}')"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                        Delete
                    </button>
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

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
             x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Delete Building</h2>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-gray-600 mb-1">Are you sure you want to delete this building?</p>
                <p class="text-lg font-bold text-gray-800" x-text="deleteName"></p>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                    Cancel
                </button>
                <form :action="deleteAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-semibold transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    @if(session('error'))
    <div class="fixed inset-0 z-50 flex items-center justify-center" x-data="{ showError: true }" x-show="showError" x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showError = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
             x-show="showError"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Cannot Delete</h2>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-gray-600 mb-2">{{ session('error') }}</p>
                <p class="text-sm text-gray-500">Remove the associated records first, then try again.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button @click="showError = false" class="w-full px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl font-semibold transition-colors">
                    Understood
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function buildingManager() {
    return {
        showDeleteModal: false,
        deleteAction: '',
        deleteName: '',
        confirmDelete(url, name) {
            this.deleteAction = url;
            this.deleteName = name;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endsection