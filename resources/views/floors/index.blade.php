{{-- resources/views/floors/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Floors')
@section('page-title', 'Floors')

@section('content')
<div x-data="floorManager()" class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Floors</h2>
            <p class="text-sm text-gray-500 mt-1">Manage building floors</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <a href="{{ route('floors.archived') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                View Deleted
            </a>
            @endif
            <a href="{{ route('floors.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Floor
            </a>
        </div>
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
                        <span class="inline-flex items-center px-3 py-1 bg-primary/10 rounded-lg text-sm font-bold text-primary">{{ $floor->rooms_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $floor->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $floor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('floors.edit', $floor) }}" class="text-primary hover:text-blue-700 font-semibold">Edit</a>
                            <button type="button" class="text-red-600 hover:text-red-700 font-semibold"
                                    @click="confirmDelete('{{ route('floors.destroy', $floor) }}', '{{ addslashes($floor->name) }}')">Delete</button>
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
                <h2 class="text-2xl font-extrabold text-white">Delete Floor</h2>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-gray-600 mb-1">Are you sure you want to delete this floor?</p>
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
function floorManager() {
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