{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MRK Hotel & Resort')</title>
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
            @if(auth()->user()->isAdmin())
                @include('shared.sidebar.admin')
            @elseif(auth()->user()->isGeneralManager())
                @include('shared.sidebar.manager')
            @elseif(auth()->user()->isStoreManager())
                @include('shared.sidebar.store-manager')
            @elseif(auth()->user()->isSupervisor())
                @include('shared.sidebar.supervisor')
            @elseif(auth()->user()->isHouseHelp())
                @include('shared.sidebar.house-help')
            @elseif(auth()->user()->isStoreKeeper())
                @include('shared.sidebar.store-keeper')
            @elseif(auth()->user()->isRestaurantManager())
                @include('shared.sidebar.restaurant-manager')
            @elseif(auth()->user()->isBarTender())
                @include('shared.sidebar.bar-tender')
            @elseif(auth()->user()->isCashier())
                @include('shared.sidebar.cashier')
            @else
                @include('shared.sidebar.front-desk')
            @endif

            <!-- Logout -->
            <div class="p-3 border-t border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl w-full text-red-600 hover:bg-red-50 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-50">
            <!-- Top Navigation -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-extrabold text-secondary">@yield('page-title', 'Dashboard')</h1>
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
                    
                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
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

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>