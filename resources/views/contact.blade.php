<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Get in touch with MRK Hotel & Resort. Our concierge team is ready to assist with reservations, inquiries, and special requests.">
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
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&h=900&fit=crop" 
                 alt="MRK Hotel Reception" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-dark/80 via-dark/60 to-dark/40"></div>
        </div>
        <div class="container mx-auto px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Contact Us</p>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-white leading-tight mb-6">
                    We're Here to<br/>Assist You
                </h1>
                <p class="text-lg md:text-xl text-gray-200 leading-relaxed">
                    Whether you have a question about reservations, special requests, or our services, 
                    our dedicated concierge team is ready to help make your stay exceptional.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Information & Form -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Contact Info Cards -->
                <div class="space-y-6">
                    <!-- Phone -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <div class="bg-primary h-14 w-14 rounded-xl flex items-center justify-center mb-5">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-dark mb-2">Reservations</h3>
                        <p class="text-gray-600 mb-4">Available 24/7 for bookings</p>
                        <a href="tel:+255123456789" class="text-primary font-semibold hover:text-primary-light transition-colors">+255 123 456 789</a>
                        <br>
                        <span class="text-gray-500 text-sm">Toll-free: 0800 123 456</span>
                    </div>

                    <!-- Email -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <div class="bg-primary h-14 w-14 rounded-xl flex items-center justify-center mb-5">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-dark mb-2">Email Us</h3>
                        <p class="text-gray-600 mb-4">We respond within 2 hours</p>
                        <a href="mailto:reservations@mrkhotel.com" class="text-primary font-semibold hover:text-primary-light transition-colors">reservations@mrkhotel.com</a>
                        <br>
                        <a href="mailto:concierge@mrkhotel.com" class="text-primary font-semibold hover:text-primary-light transition-colors">concierge@mrkhotel.com</a>
                    </div>

                    <!-- Location -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <div class="bg-primary h-14 w-14 rounded-xl flex items-center justify-center mb-5">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-dark mb-2">Our Location</h3>
                        <p class="text-gray-600 mb-4">Beachfront property</p>
                        <p class="text-gray-700">123 Ocean Drive<br>
                        Masaki Peninsula<br>
                        Dar es Salaam, Tanzania</p>
                    </div>

                    <!-- Business Hours -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <div class="bg-primary h-14 w-14 rounded-xl flex items-center justify-center mb-5">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-dark mb-2">Front Desk Hours</h3>
                        <p class="text-gray-600 mb-4">We're always here for you</p>
                        <p class="text-gray-700">Check-in: 2:00 PM<br>
                        Check-out: 11:00 AM<br>
                        <span class="text-accent font-medium">Front Desk: 24 Hours</span></p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-10 shadow-xl border border-gray-100">
                        <h2 class="text-2xl font-serif font-bold text-dark mb-2">Send Us a Message</h2>
                        <p class="text-gray-600 mb-8">Have a special request or inquiry? Fill out the form below and our team will respond promptly.</p>
                        
                        <form action="#" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" id="first_name" name="first_name" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="John">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="Doe">
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="john@example.com">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" id="phone" name="phone"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="+255 123 456 789">
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Inquiry Type</label>
                                <select id="subject" name="subject" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    <option value="">Select an inquiry type</option>
                                    <option value="reservation">Room Reservation</option>
                                    <option value="group">Group Booking</option>
                                    <option value="event">Event & Conference</option>
                                    <option value="dining">Restaurant Reservation</option>
                                    <option value="spa">Spa Booking</option>
                                    <option value="special">Special Request</option>
                                    <option value="feedback">Guest Feedback</option>
                                    <option value="other">General Inquiry</option>
                                </select>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="arrival" class="block text-sm font-medium text-gray-700 mb-2">Expected Arrival (Optional)</label>
                                    <input type="date" id="arrival" name="arrival"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                </div>
                                <div>
                                    <label for="guests" class="block text-sm font-medium text-gray-700 mb-2">Number of Guests (Optional)</label>
                                    <select id="guests" name="guests"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="">Select</option>
                                        <option value="1">1 Guest</option>
                                        <option value="2">2 Guests</option>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests</option>
                                        <option value="5+">5+ Guests</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                          placeholder="Please share details about your inquiry or any special requests..."></textarea>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="newsletter" name="newsletter" 
                                       class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                                <label for="newsletter" class="text-sm text-gray-600">
                                    I'd like to receive updates about special offers, events, and promotions from MRK Hotel & Resort.
                                </label>
                            </div>

                            <button type="submit" 
                                    class="w-full px-8 py-4 text-base font-semibold rounded-lg bg-primary hover:bg-primary-light text-white transition-colors">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Find Us</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                    Our Prime Location
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Situated on the pristine Masaki Peninsula, our beachfront property offers easy access to the city center and major attractions.
                </p>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                <div class="aspect-video bg-gray-200 flex items-center justify-center">
                    <div class="text-center p-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Interactive Map</p>
                        <p class="text-gray-400 text-sm">Map integration would be displayed here</p>
                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-8 mt-8">
                <div class="text-center">
                    <div class="text-accent font-bold text-2xl mb-2">15 min</div>
                    <p class="text-gray-600">From Julius Nyerere Airport</p>
                </div>
                <div class="text-center">
                    <div class="text-accent font-bold text-2xl mb-2">5 min</div>
                    <p class="text-gray-600">From City Center</p>
                </div>
                <div class="text-center">
                    <div class="text-accent font-bold text-2xl mb-2">Walking Distance</div>
                    <p class="text-gray-600">To Beach & Shopping</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">FAQ</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-dark mb-6">
                    Frequently Asked Questions
                </h2>
            </div>
            <div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-dark">What are the check-in and check-out times?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 1" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Check-in time is 2:00 PM and check-out time is 11:00 AM. Early check-in and late check-out can be arranged based on availability. Please contact our front desk for arrangements.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-dark">Do you offer airport transfers?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 2" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Yes, we provide complimentary airport transfers for guests staying in our Suites and Executive rooms. Standard room guests can arrange transfers at an additional fee. Please provide your flight details at least 24 hours in advance.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-dark">Is breakfast included in the room rate?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 3" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Our room rates include a complimentary breakfast buffet at The Garden Restaurant, featuring local and international cuisine. Breakfast is served daily from 6:30 AM to 10:30 AM.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-dark">What amenities are available at the hotel?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 4" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Our hotel features an infinity pool, full-service spa, fitness center, multiple restaurants, business center, concierge services, and complimentary Wi-Fi throughout the property. We also offer room service 24 hours a day.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-dark">What is your cancellation policy?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 5 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 5" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Reservations can be cancelled free of charge up to 48 hours before the scheduled arrival date. Cancellations made within 48 hours will be charged for one night's stay. Special rates and promotional bookings may have different cancellation policies.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white mb-6">
                    Ready to Experience MRK Hospitality?
                </h2>
                <p class="text-xl text-gray-300 mb-10 leading-relaxed">
                    Book your stay today and discover why our guests return again and again.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/rooms') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg bg-accent hover:bg-accent/90 text-white transition-colors">
                        Book a Room
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="tel:+255123456789" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg border-2 border-white text-white hover:bg-white hover:text-primary transition-colors">
                        Call Us Now
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
