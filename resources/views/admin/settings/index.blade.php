{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', __('settings.system_settings'))
@section('page-title', __('settings.system_settings'))

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

    @if(session('sms_success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('sms_success') }}</span>
        </div>
    </div>
    @endif

    @if(session('email_success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('email_success') }}</span>
        </div>
    </div>
    @endif

    @if(session('azampesa_success'))

            <span class="font-medium">{{ session('azampesa_success') }}</span>
        </div>
    </div>
    @endif

    <!-- Currency Settings Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.currency') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.currency') }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.default_currency') }}</label>
                <select name="default_currency" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    @foreach($currencies as $currency)
                    <option value="{{ $currency['code'] }}" {{ $settings['default_currency'] === $currency['code'] ? 'selected' : '' }}>
                        {{ $currency['label'] }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.currency_usage') }}</p>
                @error('default_currency')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.tzs_exchange_rate') }}</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">{{ __('settings.labels.usd_equals') }}</span>
                    <input type="number" name="tzs_exchange_rate" value="{{ old('tzs_exchange_rate', $settings['tzs_exchange_rate']) }}" 
                           class="w-full pl-20 pr-16 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                           min="1" step="1" required>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">{{ __('settings.labels.tzs') }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.exchange_rate') }}</p>
                @error('tzs_exchange_rate')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                {{ __('settings.save_currency') }}
            </button>
        </form>
    </div>

    <!-- SMS Settings Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 16c0 1.105-1.343 2-3 2H8l-4 4V6c0-1.105 1.343-2 3-2h11c1.657 0 3 .895 3 2v10z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.sms') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.sms') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.sms') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.sms_provider_key') }}</label>
                <input type="text" name="sms_provider_key" value="{{ old('sms_provider_key', $settings['sms_provider_key']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('sms_provider_key')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.sms_sender_id') }}</label>
                <input type="text" name="sms_sender_id" value="{{ old('sms_sender_id', $settings['sms_sender_id']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('sms_sender_id')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.sms_api_key') }}</label>
                <input type="password" name="sms_api_key" placeholder="{{ $settings['sms_api_key_set'] ? __('settings.hints.secret_saved') : __('settings.hints.secret_required') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.secret_masked') }}</p>
                @error('sms_api_key')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.sms_base_url') }}</label>
                <input type="url" name="sms_base_url" value="{{ old('sms_base_url', $settings['sms_base_url']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('sms_base_url')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" name="sms_is_enabled" value="1" id="sms_is_enabled"
                       {{ old('sms_is_enabled', $settings['sms_is_enabled']) ? 'checked' : '' }}
                       class="w-5 h-5 text-primary border-gray-200 rounded focus:ring-2 focus:ring-primary">
                <label for="sms_is_enabled" class="text-sm font-semibold text-secondary">{{ __('settings.fields.sms_is_enabled') }}</label>
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                {{ __('settings.save_sms') }}
            </button>
        </form>
    </div>

    <!-- Email Settings Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-sky-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 8H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v6a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.email') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.email') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.email') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_driver') }}</label>
                <select name="mail_driver" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    @foreach(['smtp', 'sendmail', 'ses', 'postmark', 'resend', 'log', 'array'] as $driver)
                    <option value="{{ $driver }}" {{ old('mail_driver', $settings['mail_driver']) === $driver ? 'selected' : '' }}>
                        {{ strtoupper($driver) }}
                    </option>
                    @endforeach
                </select>
                @error('mail_driver')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_host') }}</label>
                <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('mail_host')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_port') }}</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" min="1" max="65535">
                    @error('mail_port')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_encryption') }}</label>
                    <select name="mail_encryption" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        @foreach(['tls', 'ssl', 'none'] as $enc)
                        <option value="{{ $enc }}" {{ old('mail_encryption', $settings['mail_encryption']) === $enc ? 'selected' : '' }}>
                            {{ strtoupper($enc) }}
                        </option>
                        @endforeach
                    </select>
                    @error('mail_encryption')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_username') }}</label>
                <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('mail_username')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_password') }}</label>
                <input type="password" name="mail_password" placeholder="{{ $settings['mail_password_set'] ? __('settings.hints.secret_saved') : __('settings.hints.secret_required') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.secret_masked') }}</p>
                @error('mail_password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_from_address') }}</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    @error('mail_from_address')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.mail_from_name') }}</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    @error('mail_from_name')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" name="mail_is_enabled" value="1" id="mail_is_enabled"
                       {{ old('mail_is_enabled', $settings['mail_is_enabled']) ? 'checked' : '' }}
                       class="w-5 h-5 text-primary border-gray-200 rounded focus:ring-2 focus:ring-primary">
                <label for="mail_is_enabled" class="text-sm font-semibold text-secondary">{{ __('settings.fields.mail_is_enabled') }}</label>
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-sky-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                {{ __('settings.save_email') }}
            </button>
        </form>
    </div>

    <!-- AzamPesa Payment Settings Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-fuchsia-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.azampesa') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.azampesa') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.azampesa') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.azampesa_base_url') }}</label>
                <input type="url" name="azampesa_base_url" value="{{ old('azampesa_base_url', $settings['azampesa_base_url']) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('azampesa_base_url')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.azampesa_app_name') }}</label>
                <input type="text" name="azampesa_app_name" placeholder="{{ $settings['azampesa_app_name_set'] ? __('settings.hints.secret_saved') : __('settings.hints.secret_required') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                @error('azampesa_app_name')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.azampesa_client_id') }}</label>
                <input type="password" name="azampesa_client_id" placeholder="{{ $settings['azampesa_client_id_set'] ? __('settings.hints.secret_saved') : __('settings.hints.secret_required') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.secret_masked') }}</p>
                @error('azampesa_client_id')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.azampesa_client_secret') }}</label>
                <input type="password" name="azampesa_client_secret" placeholder="{{ $settings['azampesa_client_secret_set'] ? __('settings.hints.secret_saved') : __('settings.hints.secret_required') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.secret_masked') }}</p>
                @error('azampesa_client_secret')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" name="azampesa_is_enabled" value="1" id="azampesa_is_enabled"
                       {{ old('azampesa_is_enabled', $settings['azampesa_is_enabled']) ? 'checked' : '' }}
                       class="w-5 h-5 text-primary border-gray-200 rounded focus:ring-2 focus:ring-primary">
                <label for="azampesa_is_enabled" class="text-sm font-semibold text-secondary">{{ __('settings.fields.azampesa_is_enabled') }}</label>
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-purple-500 to-fuchsia-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all">
                {{ __('settings.save_azampesa') }}
            </button>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-100 to-orange-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.change_password') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.password') }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.settings.password') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.current_password') }}</label>
                <input type="password" name="current_password" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="current-password">
                @error('current_password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.new_password') }}</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="new-password">
                <p class="text-xs text-gray-500 mt-1.5">{{ __('settings.hints.password_requirements') }}</p>
                @error('password')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.confirm_password') }}</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" 
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all">
                {{ __('settings.change_password') }}
            </button>
        </form>
    </div>

    <!-- Profile Information Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-secondary">{{ __('settings.sections.profile_info') }}</h3>
                <p class="text-sm text-gray-500">{{ __('settings.subtitles.profile') }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-5">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.name') }}</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('name')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('settings.fields.email') }}</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all" required>
                @error('email')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                {{ __('settings.save_profile') }}
            </button>
        </form>
    </div>
</div>
@endsection
