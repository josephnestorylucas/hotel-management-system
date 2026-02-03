{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Sign in to your MRK Hotel account to manage bookings and access guest services.">
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
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Side - Hotel Image Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&h=1600&fit=crop" 
                     alt="MRK Hotel" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-dark/50 to-dark/30"></div>
            </div>
            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto brightness-0 invert" onerror="this.style.display='none'">
                    <div>
                        <span class="text-2xl font-serif font-bold text-white block leading-tight">MRK Hotel</span>
                        <span class="text-xs text-gray-300 tracking-wider uppercase">& Resort</span>
                    </div>
                </a>
                
                <!-- Content -->
                <div class="max-w-md">
                    <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Welcome Back</p>
                    <h1 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">
                        Access Your Guest Portal
                    </h1>
                    <p class="text-lg text-gray-300 leading-relaxed mb-8">
                        Sign in to manage your reservations, view booking history, and access exclusive guest services at MRK Hotel & Resort.
                    </p>
                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="flex items-center gap-2 text-gray-300">
                            <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm">Manage Bookings</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-300">
                            <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm">View History</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-300">
                            <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm">Special Offers</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} MRK Hotel & Resort. All rights reserved.
                </p>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/header.png') }}" alt="MRK Hotel" class="h-12 w-auto" onerror="this.style.display='none'">
                        <div>
                            <span class="text-xl font-serif font-bold text-primary block leading-tight">MRK Hotel</span>
                            <span class="text-xs text-gray-500 tracking-wider uppercase">& Resort</span>
                        </div>
                    </a>
                </div>
                
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-serif font-bold text-dark mb-2">Guest Sign In</h2>
                    <p class="text-gray-600">Enter your credentials to access your account</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input id="password" name="password" type="password" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-light transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3 text-base font-semibold rounded-lg bg-primary hover:bg-primary-light text-white transition-colors">
                        Sign In
                    </button>
                </form>

                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">New Guest?</span>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-semibold rounded-lg border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary transition-all">
                            Create Guest Account
                        </a>
                    </div>
                </div>

                <!-- Staff Login Notice -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 text-center">
                        <span class="font-medium">Hotel Staff?</span> Use your staff credentials to access the management portal.
                    </p>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    By signing in, you agree to our 
                    <a href="{{ url('/terms') }}" class="text-primary hover:underline">Terms of Service</a> 
                    and 
                    <a href="{{ url('/privacy') }}" class="text-primary hover:underline">Privacy Policy</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>