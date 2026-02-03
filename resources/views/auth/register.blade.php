{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Registration - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Create your MRK Hotel guest account to book rooms and access exclusive services.">
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
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&h=1600&fit=crop" 
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
                    <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Join Us</p>
                    <h1 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">
                        Create Your Guest Account
                    </h1>
                    <p class="text-lg text-gray-300 leading-relaxed mb-8">
                        Register to enjoy seamless booking, exclusive member rates, and personalized services at MRK Hotel & Resort.
                    </p>
                    
                    <!-- Benefits List -->
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-300">
                            <div class="bg-accent/20 rounded-full p-1.5">
                                <svg class="h-4 w-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Faster checkout process</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <div class="bg-accent/20 rounded-full p-1.5">
                                <svg class="h-4 w-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Exclusive member discounts</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <div class="bg-accent/20 rounded-full p-1.5">
                                <svg class="h-4 w-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Manage bookings online</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <div class="bg-accent/20 rounded-full p-1.5">
                                <svg class="h-4 w-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Earn loyalty rewards</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Footer -->
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} MRK Hotel & Resort. All rights reserved.
                </p>
            </div>
        </div>
        
        <!-- Right Side - Register Form -->
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
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-serif font-bold text-dark mb-2">Guest Registration</h2>
                    <p class="text-gray-600">Create your account to start booking</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="John Doe">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

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
                               placeholder="Create a password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400" 
                               placeholder="Confirm your password">
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="terms" name="terms" type="checkbox" required
                               class="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="terms" class="text-sm text-gray-600">
                            I agree to the 
                            <a href="{{ url('/terms') }}" class="text-primary hover:underline font-medium">Terms of Service</a> 
                            and 
                            <a href="{{ url('/privacy') }}" class="text-primary hover:underline font-medium">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3 text-base font-semibold rounded-lg bg-primary hover:bg-primary-light text-white transition-colors">
                        Create Guest Account
                    </button>
                </form>

                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Already a guest?</span>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-semibold rounded-lg border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary transition-all">
                            Sign In to Your Account
                        </a>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    By creating an account, you agree to receive emails about booking confirmations, special offers, and hotel updates.
                </p>
            </div>
        </div>
    </div>
</body>
</html>