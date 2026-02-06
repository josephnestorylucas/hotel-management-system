{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile Settings')

@section('content')
<div class="max-w-2xl">
    <!-- Profile Information Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-secondary">Profile Information</h3>
        </div>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('name')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('email')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                Save Changes
            </button>
        </form>
    </div>

    <!-- Delete Account Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-red-600">Delete Account</h3>
        </div>
        
        <div class="bg-gradient-to-br from-red-50 to-red-100/50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-red-700">
                <strong>Warning:</strong> Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.
            </p>
        </div>
        
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This action cannot be undone.')">
            @csrf
            @method('DELETE')

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">Confirm Password</label>
                <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-500 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                Delete Account
            </button>
        </form>
    </div>
</div>
@endsection