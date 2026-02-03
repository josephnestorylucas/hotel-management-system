<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Discover the story of MRK Hotel & Resort - a premier destination offering luxury accommodations, exceptional service, and unforgettable experiences.">
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
                },
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white font-sans antialiased scroll-smooth">
@include('partials.public-header')

<!-- Main Content -->
<main class="overflow-hidden">
    <!-- Hero Section -->
    <section class="relative py-24 md:py-32">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1600&h=900&fit=crop" 
                 alt="MRK Hotel Exterior" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-dark/80 via-dark/60 to-dark/40"></div>
        </div>
        <div class="container mx-auto px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Our Story</p>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-white leading-tight mb-6">
                    A Legacy of<br/>Exceptional Hospitality
                </h1>
                <p class="text-lg md:text-xl text-gray-200 leading-relaxed">
                    For over two decades, MRK Hotel & Resort has been a beacon of luxury and comfort, 
                    offering guests an unforgettable experience rooted in tradition and refined elegance.
                </p>
            </div>
        </div>
    </section>

    <!-- Our Heritage Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Our Heritage</p>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                        A Tradition of Excellence Since 2001
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Founded with a vision to create a sanctuary of comfort and sophistication, MRK Hotel & Resort 
                        has grown from a boutique establishment to one of the region's most distinguished hospitality destinations.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Our commitment to personalized service, attention to detail, and genuine warmth has earned us 
                        the trust of discerning travelers from around the world. Every guest who walks through our doors 
                        becomes part of the MRK family.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Whether you're here for business or leisure, celebrating a milestone or seeking respite, 
                        we dedicate ourselves to making every moment memorable.
                    </p>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-accent/20 via-primary/10 to-transparent rounded-3xl blur-2xl"></div>
                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&h=600&fit=crop" 
                         alt="MRK Hotel Lobby" 
                         class="relative rounded-2xl shadow-2xl w-full h-auto object-cover ring-1 ring-gray-200">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-10 shadow-lg border border-gray-100">
                    <div class="bg-primary h-16 w-16 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-dark mb-4">Our Mission</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To provide an extraordinary hospitality experience that exceeds expectations, 
                        where every guest feels valued, every detail is perfected, and every stay 
                        creates lasting memories of comfort, elegance, and genuine care.
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-10 shadow-lg border border-gray-100">
                    <div class="bg-primary h-16 w-16 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-dark mb-4">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To be recognized as the premier destination for travelers seeking authentic 
                        luxury and heartfelt hospitality, setting the standard for excellence in 
                        service, sustainability, and creating meaningful connections with our guests.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Hotel Highlights -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">At a Glance</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                    The MRK Experience
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Discover what makes our hotel a preferred choice for travelers worldwide
                </p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-8 bg-gray-50 rounded-2xl">
                    <div class="text-4xl md:text-5xl font-serif font-bold text-primary mb-2">150+</div>
                    <div class="text-gray-600 font-medium">Elegant Rooms</div>
                </div>
                <div class="text-center p-8 bg-gray-50 rounded-2xl">
                    <div class="text-4xl md:text-5xl font-serif font-bold text-primary mb-2">22</div>
                    <div class="text-gray-600 font-medium">Years of Service</div>
                </div>
                <div class="text-center p-8 bg-gray-50 rounded-2xl">
                    <div class="text-4xl md:text-5xl font-serif font-bold text-primary mb-2">4.8</div>
                    <div class="text-gray-600 font-medium">Guest Rating</div>
                </div>
                <div class="text-center p-8 bg-gray-50 rounded-2xl">
                    <div class="text-4xl md:text-5xl font-serif font-bold text-primary mb-2">50K+</div>
                    <div class="text-gray-600 font-medium">Happy Guests</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Our Leadership</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                    The Team Behind Your Experience
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Dedicated professionals committed to making your stay exceptional
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop" 
                         alt="General Manager" 
                         class="w-full h-64 object-cover">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-serif font-bold text-dark">Michael Karanja</h3>
                        <p class="text-accent font-medium mb-3">General Manager</p>
                        <p class="text-gray-600 text-sm">25+ years in luxury hospitality management across international destinations.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop" 
                         alt="Director of Operations" 
                         class="w-full h-64 object-cover">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-serif font-bold text-dark">Rose Mwangi</h3>
                        <p class="text-accent font-medium mb-3">Director of Operations</p>
                        <p class="text-gray-600 text-sm">Expert in guest services with a passion for creating memorable experiences.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1583394293214-28ez24e6cdca?w=400&h=400&fit=crop" 
                         alt="Executive Chef" 
                         class="w-full h-64 object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=400&h=400&fit=crop'">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-serif font-bold text-dark">Chef David Odhiambo</h3>
                        <p class="text-accent font-medium mb-3">Executive Chef</p>
                        <p class="text-gray-600 text-sm">Award-winning culinary artist blending local flavors with international cuisines.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Our Values</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                    The Principles That Guide Us
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mb-5 mx-auto">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Warmth</h3>
                    <p class="text-gray-600 text-sm">Every guest is welcomed as family with genuine care and attention.</p>
                </div>
                <div class="text-center">
                    <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mb-5 mx-auto">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Excellence</h3>
                    <p class="text-gray-600 text-sm">We strive for perfection in every detail of your stay.</p>
                </div>
                <div class="text-center">
                    <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mb-5 mx-auto">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Sustainability</h3>
                    <p class="text-gray-600 text-sm">Committed to eco-friendly practices and community support.</p>
                </div>
                <div class="text-center">
                    <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mb-5 mx-auto">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Integrity</h3>
                    <p class="text-gray-600 text-sm">Honest, transparent service that builds lasting trust.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white mb-6">
                    Experience MRK Hospitality
                </h2>
                <p class="text-xl text-gray-300 mb-10 leading-relaxed">
                    We invite you to be our guest and discover why MRK Hotel & Resort 
                    has been the choice of discerning travelers for over two decades.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/rooms') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg bg-accent hover:bg-accent/90 text-white transition-colors">
                        View Our Rooms
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg border-2 border-white text-white hover:bg-white hover:text-primary transition-colors">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
