{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hotel Management System')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col shadow-sm">


            <!-- User Profile -->
            <div class="p-4 border-b border-gray-100 bg-gradient-to-br from-blue-50 via-white to-blue-50">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-secondary truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-primary font-medium">{{ auth()->user()->role->description }}</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            @include(auth()->user()->sidebarView())

            <!-- Logout -->
            <div class="p-3 border-t border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl w-full text-red-600 hover:bg-red-50 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>{{ __('general.sign_out') }}</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-50">
            <!-- Top Navigation -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-extrabold text-secondary">@yield('page-title', __('general.dashboard'))</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Current Date -->
                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-xl">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">{{ now()->format('D, M d, Y') }}</span>
                    </div>

                    <!-- Notification Bell -->
                    @include('partials.notification-bell')

                    <!-- Language Switcher -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            <span class="hidden sm:inline">{{ app()->getLocale() === 'sw' ? 'SW' : 'EN' }}</span>
                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                             x-cloak>
                            <a href="{{ route('language.switch', 'en') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors {{ app()->getLocale() === 'en' ? 'text-primary font-semibold bg-blue-50' : 'text-gray-700' }}">
                                <span class="text-lg">🇬🇧</span>
                                <span>English</span>
                                @if(app()->getLocale() === 'en')
                                    <svg class="w-4 h-4 ml-auto text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                            <a href="{{ route('language.switch', 'sw') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors {{ app()->getLocale() === 'sw' ? 'text-primary font-semibold bg-blue-50' : 'text-gray-700' }}">
                                <span class="text-lg">🇹🇿</span>
                                <span>Kiswahili</span>
                                @if(app()->getLocale() === 'sw')
                                    <svg class="w-4 h-4 ml-auto text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                    
                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ __('general.profile') }}
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                        <div class="bg-red-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                        <div class="font-medium">{{ $errors->first() }}</div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Unauthorized Access Modal -->
    @if(session('unauthorized'))
    <div id="unauthorizedModal" class="fixed inset-0 z-50 flex items-center justify-center" x-data="{ open: true }" x-show="open" x-cloak>
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Red Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-white">{{ __('general.messages.unauthorized') }}</h2>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-6 text-center">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Unauthorized
                    </span>
                </div>
                <p class="text-gray-600 text-lg mb-2">{{ session('unauthorized') }}</p>
                <p class="text-gray-500 text-sm">{{ __('general.messages.no_permission') }}</p>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                <a href="{{ route('dashboard') }}" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold text-center transition-colors">
                    {{ __('general.nav.dashboard') }}
                </a>
                <button @click="open = false" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-semibold transition-colors">
                    Got it!
                </button>
            </div>
        </div>
    </div>
    @endif

    @stack('scripts')

    @include('components.bug-reporter')
    
    <!-- Live Time Script - Updates every second without page refresh -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent duplicate intervals
            if (window.liveTimeInterval) {
                clearInterval(window.liveTimeInterval);
            }
            
            function updateLiveTime() {
                const now = new Date();
                
                // Format time: 12-hour format with AM/PM (e.g., 12:07:45 PM)
                const timeElement = document.getElementById('liveTime');
                if (timeElement) {
                    let hours = now.getHours();
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // 0 should be 12
                    const formattedTime = hours.toString().padStart(2, '0') + ':' + minutes + ':' + seconds + ' ' + ampm;
                    timeElement.textContent = formattedTime;
                }
                
                // Format date: Full format (e.g., Wednesday, April 01, 2026)
                const dateElement = document.getElementById('liveDate');
                if (dateElement) {
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: '2-digit' };
                    const formattedDate = now.toLocaleDateString('en-US', options);
                    dateElement.textContent = formattedDate;
                }
            }
            
            // Run immediately on page load
            updateLiveTime();
            
            // Update every second
            window.liveTimeInterval = setInterval(updateLiveTime, 1000);
        });
    </script>
</body>
</html>
