<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRK Hotel & Resort - Premium Accommodation in East Africa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Experience luxury hospitality at MRK Hotel & Resort. Book your stay, explore our rooms, and discover exceptional services across our East African properties.">
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
@include('partials.public-header')

<!-- Main Content -->
<main class="overflow-hidden">
    <!-- Hero Section with Full-Width Image -->
    <section class="relative py-20 md:py-28 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="container mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-6 border border-primary/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                        Welcome to MRK Hotel & Resort
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                        Experience<br/>
                        <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">Timeless Elegance</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-8 max-w-2xl mx-auto lg:mx-0">
                        Discover exceptional hospitality across East Africa. From business travel to leisure getaways, we provide comfort, service, and memorable experiences.
                    </p>
                    
                    <!-- Quick Booking Form -->
                    <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-200 max-w-2xl">
                        <form class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 basis-[180px] min-w-[160px]">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Check In</label>
                                <input type="date" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div class="flex-1 basis-[180px] min-w-[160px]">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Check Out</label>
                                <input type="date" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div class="flex-1 basis-[160px] min-w-[140px]">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Guests</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <option value="1">1 Guest</option>
                                    <option value="2" selected>2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4+ Guests</option>
                                </select>
                            </div>
                            <div class="flex-none w-full sm:w-auto">
                                <a href="{{ url('/booking') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                                    Check Availability
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center gap-6 justify-center lg:justify-start text-sm text-gray-600 mt-8">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Best Price Guarantee</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Free Cancellation</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">24/7 Support</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Hero Image -->
                <div class="relative hidden lg:block">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary/20 via-blue-600/10 to-transparent rounded-3xl blur-2xl"></div>
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=900&h=700&fit=crop" 
                             alt="MRK Hotel Exterior" 
                             class="rounded-2xl shadow-2xl w-full h-auto object-cover ring-1 ring-gray-200">
                        
                        <!-- Stats Overlay -->
                        <div class="absolute -bottom-3 left-6 right-6 bg-white/90 backdrop-blur-sm rounded-xl py-4 px-6 shadow-lg border border-gray-100">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-primary">15+</div>
                                    <div class="text-xs text-gray-600 font-medium">Years</div>
                                </div>
                                <div class="text-center border-x border-gray-200">
                                    <div class="text-xl font-bold text-primary">500+</div>
                                    <div class="text-xs text-gray-600 font-medium">Rooms</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-primary">50K+</div>
                                    <div class="text-xs text-gray-600 font-medium">Guests</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Cards -->
    <section class="py-6 bg-gradient-to-r from-primary to-blue-600">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-4">
                <a href="{{ url('/booking') }}" class="flex items-center gap-4 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="font-semibold">Make a Reservation</p>
                        <p class="text-sm text-white/70">Book your stay online</p>
                    </div>
                </a>
                <a href="{{ url('/booking') }}" class="flex items-center gap-4 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="font-semibold">Our Rooms</p>
                        <p class="text-sm text-white/70">View accommodation</p>
                    </div>
                </a>
                <a href="{{ url('/services') }}" class="flex items-center gap-4 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="font-semibold">Hotel Services</p>
                        <p class="text-sm text-white/70">Dining, spa & more</p>
                    </div>
                </a>
                <a href="{{ url('/contact') }}" class="flex items-center gap-4 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="font-semibold">Contact Us</p>
                        <p class="text-sm text-white/70">24/7 reception desk</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20 md:py-28 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                        About Our Hotel
                    </span>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-secondary mb-6">
                        A Legacy of Excellence in Hospitality
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Since our establishment, MRK Hotel & Resort has been committed to providing exceptional hospitality services across East Africa. Our properties combine modern comfort with traditional warmth, ensuring every guest feels at home.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Whether you're traveling for business or leisure, our dedicated staff and world-class facilities are designed to exceed your expectations and create lasting memories.
                    </p>
                    
                    <div class="grid grid-cols-3 gap-8 mb-8">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-primary mb-1">15+</div>
                            <div class="text-sm text-gray-500 font-medium">Years of Service</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-primary mb-1">500+</div>
                            <div class="text-sm text-gray-500 font-medium">Rooms Available</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-primary mb-1">50K+</div>
                            <div class="text-sm text-gray-500 font-medium">Happy Guests</div>
                        </div>
                    </div>
                    
                    <a href="{{ url('/about') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                        Learn more about us
                        <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=500&fit=crop" 
                         alt="Hotel Pool" 
                         class="rounded-2xl shadow-lg w-full h-64 object-cover">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=500&fit=crop" 
                         alt="Hotel Room" 
                         class="rounded-2xl shadow-lg w-full h-64 object-cover mt-8">
                    <img src="https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=400&h=500&fit=crop" 
                         alt="Hotel Restaurant" 
                         class="rounded-2xl shadow-lg w-full h-64 object-cover">
                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400&h=500&fit=crop" 
                         alt="Hotel Lobby" 
                         class="rounded-2xl shadow-lg w-full h-64 object-cover mt-8">
                </div>
            </div>
        </div>
    </section>

    <!-- Room Types -->
    <section class="py-20 md:py-28 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Accommodation
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-secondary mb-6">
                    Our Rooms & Suites
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Choose from our selection of elegantly appointed rooms and suites, each designed with your comfort in mind.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Standard Room -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&h=400&fit=crop" 
                             alt="Standard Room" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-primary shadow">
                            From $120/night
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-secondary mb-3">Standard Room</h3>
                        <p class="text-gray-600 mb-4">Comfortable rooms with essential amenities for a pleasant stay. Ideal for solo travelers or couples.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">25 m²</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">Queen Bed</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">City View</span>
                        </div>
                        <a href="{{ url('/booking') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700">
                            Book Now
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Deluxe Room -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=400&fit=crop" 
                             alt="Deluxe Room" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-gradient-to-r from-primary to-blue-600 px-3 py-1 rounded-full text-sm font-semibold text-white shadow">
                            From $180/night
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-secondary mb-3">Deluxe Room</h3>
                        <p class="text-gray-600 mb-4">Spacious rooms with premium furnishings and enhanced amenities for a luxurious experience.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">35 m²</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">King Bed</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">Pool View</span>
                        </div>
                        <a href="{{ url('/booking') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700">
                            Book Now
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Executive Suite -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&h=400&fit=crop" 
                             alt="Executive Suite" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-secondary px-3 py-1 rounded-full text-sm font-semibold text-white shadow">
                            From $350/night
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-secondary mb-3">Executive Suite</h3>
                        <p class="text-gray-600 mb-4">Premium suites with separate living area, perfect for extended stays or special occasions.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">60 m²</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">King Bed</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">Ocean View</span>
                        </div>
                        <a href="{{ url('/booking') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700">
                            Book Now
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ url('/rooms') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg hover:shadow-xl transition-all">
                    View All Rooms
                    <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Hotel Services -->
    <section class="py-20 md:py-28 bg-gradient-to-br from-primary via-blue-600 to-primary text-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-white/10 text-white text-sm font-semibold rounded-full mb-4">
                    Hotel Amenities
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-6">
                    Services & Facilities
                </h2>
                <p class="text-lg text-white/80 leading-relaxed">
                    Enjoy our comprehensive range of services designed to make your stay comfortable and memorable.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Restaurant & Bar</h3>
                    <p class="text-white/70 text-sm">Fine dining and casual options with local and international cuisine</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Spa & Wellness</h3>
                    <p class="text-white/70 text-sm">Rejuvenate with our massage treatments and wellness facilities</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Swimming Pool</h3>
                    <p class="text-white/70 text-sm">Outdoor pool with sun loungers and poolside service</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Business Center</h3>
                    <p class="text-white/70 text-sm">Conference rooms and business facilities for corporate guests</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Free WiFi</h3>
                    <p class="text-white/70 text-sm">High-speed internet throughout the property</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Fitness Center</h3>
                    <p class="text-white/70 text-sm">Modern gym equipment available 24/7</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Airport Transfer</h3>
                    <p class="text-white/70 text-sm">Complimentary shuttle service to and from the airport</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">24/7 Room Service</h3>
                    <p class="text-white/70 text-sm">Round-the-clock dining delivered to your room</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Guest Reviews -->
    <section class="py-20 md:py-28 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Testimonials
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-secondary mb-6">
                    What Our Guests Say
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Hear from travelers who experienced our hospitality firsthand
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" 
                             alt="Guest" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">James M.</div>
                            <div class="text-sm text-gray-500">Business Traveler, Kenya</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic mb-4">"Exceptional service from check-in to check-out. The staff went above and beyond to make our anniversary special. The room was immaculate and the view was breathtaking."</p>
                    <div class="flex items-center text-yellow-500">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop" 
                             alt="Guest" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">Sarah K.</div>
                            <div class="text-sm text-gray-500">Family Guest, Tanzania</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic mb-4">"Perfect location for our family vacation. The kids loved the pool and the breakfast buffet was incredible. We'll definitely be returning next year!"</p>
                    <div class="flex items-center text-yellow-500">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" 
                             alt="Guest" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">David O.</div>
                            <div class="text-sm text-gray-500">Corporate Event, Uganda</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic mb-4">"The conference facilities were excellent and the staff handled all our event requirements professionally. Highly recommend for corporate functions."</p>
                    <div class="flex items-center text-yellow-500">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Staff & Guest Portal CTA -->
    <section class="py-16 bg-white border-t border-gray-100">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Staff Portal -->
                <div class="bg-gradient-to-br from-primary to-blue-600 rounded-2xl p-8 text-white shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="bg-white/10 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2">Staff Portal</h3>
                            <p class="text-white/80 mb-4">Access the hotel management system to manage reservations, room assignments, and guest services.</p>
                            <a href="{{ route('login') }}" class="inline-flex items-center bg-white text-primary px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                                Staff Login
                                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Guest Booking -->
                <div class="bg-secondary rounded-2xl p-8 text-white shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="bg-white/10 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2">Guest Booking</h3>
                            <p class="text-white/80 mb-4">Book your stay directly online. Check room availability, view rates, and make your reservation in minutes.</p>
                            <a href="{{ url('/booking') }}" class="inline-flex items-center bg-white text-secondary px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                                Book Your Stay
                                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information Bar -->
    <section class="py-12 bg-gray-900 text-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div>
                    <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h4 class="font-semibold mb-1">Location</h4>
                    <p class="text-gray-400 text-sm">Samora Avenue, Dar es Salaam</p>
                </div>
                <div>
                    <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <h4 class="font-semibold mb-1">Reservations</h4>
                    <p class="text-gray-400 text-sm">+255 123 456 789</p>
                </div>
                <div>
                    <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h4 class="font-semibold mb-1">Email</h4>
                    <p class="text-gray-400 text-sm">reservations@mrkhotel.com</p>
                </div>
                <div>
                    <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="font-semibold mb-1">Reception Hours</h4>
                    <p class="text-gray-400 text-sm">24 Hours, 7 Days</p>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
