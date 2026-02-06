{{-- resources/views/floors/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Floor')
@section('page-title', 'Floors')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">Edit Floor</h2>
            <p class="text-sm text-gray-500 mt-1">Update floor information</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('floors.update', $floor) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Building -->
                <div>
                    <label for="building_id" class="block text-sm font-semibold text-secondary mb-2">
                        Building <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="building_id" 
                        id="building_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('building_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a building</option>
                        @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ old('building_id', $floor->building_id) == $building->id ? 'selected' : '' }}>
                            {{ $building->name }} ({{ $building->code }})
                        </option>
                        @endforeach
                    </select>
                    @error('building_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-secondary mb-2">
                        Floor Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name', $floor->name) }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
                        placeholder="e.g., Ground Floor, First Floor"
                        required>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Floor Number -->
                <div>
                    <label for="floor_number" class="block text-sm font-semibold text-secondary mb-2">
                        Floor Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="floor_number" 
                        id="floor_number"
                        value="{{ old('floor_number', $floor->floor_number) }}" 
                        min="0"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('floor_number') border-red-500 @enderror"
                        required>
                    @error('floor_number')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active"
                        value="1" 
                        {{ old('is_active', $floor->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <label for="is_active" class="text-sm font-semibold text-secondary cursor-pointer">
                        Active
                    </label>
                </div>

                <!-- Floor Info -->
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <h4 class="text-sm font-bold text-secondary mb-3">Floor Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Total Rooms:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $floor->rooms->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $floor->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <button 
                    type="button"
                    onclick="if(confirm('Are you sure you want to delete this floor?')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    Delete Floor
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('floors.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        Update Floor
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('floors.destroy', $floor) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection