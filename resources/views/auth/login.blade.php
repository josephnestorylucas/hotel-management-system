{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login.staff_portal') }} - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Staff login portal for MRK Hotel management system.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005eb8',
                        secondary: '#000000',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        };
    </script>
</head>
<body class="bg-white font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Side - Hotel Image Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&h=1600&fit=crop" 
                     alt="MRK Hotel" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-secondary/90 via-secondary/50 to-primary/30"></div>
            </div>
            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto brightness-0 invert" onerror="this.style.display='none'">
                    <div>
                        <span class="text-2xl font-extrabold text-white block leading-tight">MRK Hotel</span>
                        <span class="text-xs text-blue-200 tracking-wider uppercase font-medium">& Resort</span>
                    </div>
                </a>
                
                <!-- Content -->
                <div class="max-w-md">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-semibold rounded-full mb-6 border border-white/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>
                        {{ __('auth.login.staff_portal') }}
                    </span>
                    <h1 class="text-4xl font-extrabold text-white mb-6 leading-tight">
                        {{ __('auth.login.hotel_management_system') }}
                    </h1>
                    <p class="text-lg text-gray-200 leading-relaxed mb-8">
                        {{ __('auth.login.system_description') }}
                    </p>
                    <div class="flex items-center gap-6 flex-wrap">
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ __('auth.login.reservations') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ __('auth.login.room_management') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ __('auth.login.reports') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <p class="text-gray-300 text-sm">
                    &copy; {{ date('Y') }} MRK Hotel & Resort. {{ __('auth.login.all_rights_reserved') }}
                </p>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-blue-50 via-white to-blue-50">
            <div class="w-full max-w-md">
                <!-- Language Switcher -->
                <div class="flex justify-end mb-4">
                    <div class="flex items-center gap-2 text-sm">
                        <a href="{{ url('language/en') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <span>🇬🇧</span> EN
                        </a>
                        <a href="{{ url('language/sw') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'sw' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <span>🇹🇿</span> SW
                        </a>
                    </div>
                </div>

                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto" onerror="this.style.display='none'">
                        <div>
                            <span class="text-xl font-extrabold text-secondary block leading-tight">MRK Hotel</span>
                            <span class="text-xs text-gray-500 tracking-wider uppercase font-medium">& Resort</span>
                        </div>
                    </a>
                </div>
                
                <div class="text-center mb-10">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4 border border-primary/20">
                        {{ __('auth.login.welcome_back') }}
                    </span>
                    <h2 class="text-3xl font-extrabold text-secondary mb-2">{{ __('auth.login.staff_sign_in') }}</h2>
                    <p class="text-gray-600">{{ __('auth.login.enter_credentials') }}</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('auth.login.email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="{{ __('auth.login.email_placeholder') }}">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('auth.login.password') }}</label>
                        <input id="password" name="password" type="password" required 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="{{ __('auth.login.password_placeholder') }}">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                {{ __('auth.login.remember_me') }}
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                            {{ __('auth.login.forgot_password') }}
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3.5 text-base font-semibold rounded-xl bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-primary text-white shadow-lg hover:shadow-xl transition-all">
                        {{ __('auth.login.sign_in') }}
                    </button>
                </form>

                <!-- Guest Booking Notice -->
                <div class="mt-8 p-5 bg-white rounded-2xl border border-gray-200 shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="bg-primary/10 p-3 rounded-xl">
                            <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-secondary mb-1">{{ __('auth.guest_booking.title') }}</p>
                            <p class="text-sm text-gray-600 mb-2">{{ __('auth.guest_booking.description') }}</p>
                            <a href="{{ url('/booking') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                                {{ __('auth.guest_booking.link') }}
                                <svg class="ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    {{ __('auth.terms.by_signing_in') }} 
                    <a href="{{ url('/terms') }}" class="text-primary hover:underline font-medium">{{ __('auth.terms.terms_of_service') }}</a> 
                    {{ __('auth.terms.and') }} 
                    <a href="{{ url('/privacy') }}" class="text-primary hover:underline font-medium">{{ __('auth.terms.privacy_policy') }}</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
