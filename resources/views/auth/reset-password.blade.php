{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.reset.reset_password') }} - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Set a new password for your MRK Hotel staff account.">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a365d',
                        'primary-light': '#2c5282',
                        secondary: '#744210',
                        accent: '#c69c6d',
                        dark: '#1a202c',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        };
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-6 px-8 flex justify-between items-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto" onerror="this.style.display='none'">
                <div>
                    <span class="text-xl font-serif font-bold text-primary block leading-tight">MRK Hotel</span>
                    <span class="text-xs text-gray-500 tracking-wider uppercase">& Resort</span>
                </div>
            </a>
            <!-- Language Switcher -->
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ url('language/en') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>🇬🇧</span> EN
                </a>
                <a href="{{ url('language/sw') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'sw' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>🇹🇿</span> SW
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Icon -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-xl shadow-lg mb-6">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-serif font-bold text-dark mb-2">{{ __('auth.reset.reset_password') }}</h2>
                    <p class="text-gray-600">{{ __('auth.reset.create_new_password') }}</p>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- Email (Read Only) -->
                        <div>
                            <label for="email_display" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.login.email') }}</label>
                            <input id="email_display" type="email" value="{{ $email }}" readonly 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.reset.new_password') }}</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                                   placeholder="{{ __('auth.login.password_placeholder') }}">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.reset.confirm_password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                                   placeholder="{{ __('auth.reset.confirm_password') }}">
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-3 text-base font-semibold rounded-lg bg-primary hover:bg-primary-light text-white transition-colors">
                            {{ __('auth.reset.reset_password') }}
                        </button>
                    </form>
                </div>

                <!-- Back to Login -->
                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-primary transition-colors">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('auth.reset.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-6 px-8 text-center">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} MRK Hotel & Resort. {{ __('auth.login.all_rights_reserved') }}
            </p>
        </footer>
    </div>
</body>
</html>
