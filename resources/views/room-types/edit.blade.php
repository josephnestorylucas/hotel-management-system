{{-- resources/views/room-types/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Room Type')
@section('page-title', 'Room Types')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">Edit Room Type</h2>
            <p class="text-sm text-gray-500 mt-1">Update room type information</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('room-types.update', $roomType) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-secondary mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name', $roomType->name) }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
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

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-secondary mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code"
                        value="{{ old('code', $roomType->code) }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('code') border-red-500 @enderror"
                        required>
                    @error('code')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Base Rate -->
                    <div>
                        <label for="base_rate" class="block text-sm font-semibold text-secondary mb-2">
                            Base Rate ($) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="base_rate" 
                            id="base_rate"
                            value="{{ old('base_rate', $roomType->base_rate) }}" 
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('base_rate') border-red-500 @enderror"
                            required>
                        @error('base_rate')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Max Occupancy -->
                    <div>
                        <label for="max_occupancy" class="block text-sm font-semibold text-secondary mb-2">
                            Max Occupancy <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="max_occupancy" 
                            id="max_occupancy"
                            value="{{ old('max_occupancy', $roomType->max_occupancy) }}" 
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('max_occupancy') border-red-500 @enderror"
                            required>
                        @error('max_occupancy')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-secondary mb-2">
                        Description
                    </label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none @error('description') border-red-500 @enderror">{{ old('description', $roomType->description) }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Images Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Room Type Images
                    </h3>

                    <!-- Current Main Image -->
                    @if($roomType->hasImage())
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-secondary mb-2">Current Main Image</label>
                        <div class="flex items-start gap-4">
                            <div class="relative">
                                <img src="{{ $roomType->medium_image_with_fallback }}" 
                                     alt="{{ $roomType->name }}" 
                                     class="w-32 h-24 object-cover rounded-xl border border-gray-200 shadow-sm">
                            </div>
                            <div class="flex-1">
                                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-red-600 transition-colors">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span>Remove main image</span>
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Uploading a new image will replace this one</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Main Image Upload -->
                    <div class="mb-6">
                        <label for="image" class="block text-sm font-semibold text-secondary mb-2">
                            {{ $roomType->hasImage() ? 'Replace Main Image' : 'Main Image' }} <span class="text-gray-400 text-xs font-normal">(Optional, max 2MB, JPG/PNG/WebP)</span>
                        </label>
                        <input 
                            type="file" 
                            name="image" 
                            id="image"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('image') border-red-500 @enderror">
                        @error('image')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Current Gallery Images -->
                    @if($roomType->hasGalleryImages())
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-secondary mb-2">Current Gallery Images ({{ $roomType->gallery_images_count }})</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($roomType->gallery_images as $image)
                            <div class="relative group">
                                <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}" 
                                     alt="{{ $image->file_name }}" 
                                     class="w-full h-24 object-cover rounded-xl border border-gray-200 shadow-sm">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                                    <a href="{{ $image->getUrl() }}" target="_blank" class="p-1.5 bg-white rounded-lg text-gray-700 hover:text-primary mr-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                                <label class="absolute bottom-2 left-2 flex items-center gap-1 bg-white/90 px-2 py-1 rounded-lg text-xs cursor-pointer hover:bg-red-50 transition-colors">
                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-3 h-3">
                                    <span class="text-gray-600">Remove</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Gallery Images Upload -->
                    <div>
                        <label for="gallery" class="block text-sm font-semibold text-secondary mb-2">
                            Add Gallery Images <span class="text-gray-400 text-xs font-normal">(Optional, max 2MB each, JPG/PNG/WebP, multiple allowed)</span>
                        </label>
                        <input 
                            type="file" 
                            name="gallery[]" 
                            id="gallery"
                            accept=".jpg,.jpeg,.png,.webp"
                            multiple
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('gallery.*') border-red-500 @enderror">
                        @error('gallery.*')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Stats Info -->
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <h4 class="text-sm font-bold text-secondary mb-3">Room Type Statistics</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Total Rooms:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $roomType->rooms->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $roomType->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <button 
                    type="button"
                    onclick="if(confirm('Are you sure you want to delete this room type?')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    Delete Room Type
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('room-types.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        Update Room Type
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('room-types.destroy', $roomType) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection