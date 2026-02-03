<!-- Public Header -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto" onerror="this.style.display='none'">
                <div>
                    <span class="text-xl font-serif font-bold text-primary block leading-tight">MRK Hotel</span>
                    <span class="text-xs text-gray-500 tracking-wider uppercase">& Resort</span>
                </div>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="{{ url('/rooms') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Rooms</a>
                <a href="{{ url('/services') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Services</a>
                <a href="{{ url('/about') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">About</a>
                <a href="{{ url('/contact') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Contact</a>
            </nav>
            
            <!-- Auth Buttons -->
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">My Account</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">Sign In</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm">
                        Book Now
                    </a>
                @endauth
                
                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden p-2 text-gray-700 hover:text-primary" x-data x-on:click="$dispatch('toggle-mobile-menu')">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Navigation -->
    <div x-data="{ open: false }" x-on:toggle-mobile-menu.window="open = !open" x-show="open" x-cloak class="md:hidden border-t border-gray-100">
        <nav class="container mx-auto px-6 py-4 space-y-3">
            <a href="{{ url('/') }}" class="block text-sm font-medium text-gray-700 hover:text-primary">Home</a>
            <a href="{{ url('/rooms') }}" class="block text-sm font-medium text-gray-700 hover:text-primary">Rooms</a>
            <a href="{{ url('/services') }}" class="block text-sm font-medium text-gray-700 hover:text-primary">Services</a>
            <a href="{{ url('/about') }}" class="block text-sm font-medium text-gray-700 hover:text-primary">About</a>
            <a href="{{ url('/contact') }}" class="block text-sm font-medium text-gray-700 hover:text-primary">Contact</a>
        </nav>
    </div>
</header>
