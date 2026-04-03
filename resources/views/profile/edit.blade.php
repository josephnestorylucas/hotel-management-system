{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('profile.title'))
@section('page-title', __('profile.page_title'))

@section('content')
<div class="max-w-2xl">
    {{-- Success Messages --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('password_success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('password_success') }}</span>
        </div>
    </div>
    @endif

    <!-- Profile Information Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-secondary">{{ __('profile.sections.profile_information') }}</h3>
        </div>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('profile.fields.name') }}</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('name')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('profile.fields.email') }}</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('email')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                {{ __('profile.actions.save_changes') }}
            </button>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-100 to-orange-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('profile.sections.change_password') }}</h3>
                <p class="text-sm text-gray-500">{{ __('profile.subtitles.password_security') }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('profile.fields.current_password') }}</label>
                <input type="password" name="current_password" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="current-password">
                @error('current_password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('profile.fields.new_password') }}</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="new-password">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('profile.subtitles.password_requirements') }}</p>
                @error('password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('profile.fields.confirm_new_password') }}</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all">
                {{ __('profile.actions.change_password') }}
            </button>
        </form>
    </div>
</div>
@endsection