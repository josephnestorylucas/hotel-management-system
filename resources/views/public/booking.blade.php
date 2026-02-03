{{-- resources/views/booking.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Book your stay at MRK Hotel & Resort. Check room availability and make reservations online.">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <!-- Header -->
    @include('partials.public-header')

    <!-- Hero Section -->
    <section class="relative bg-primary pt-32 pb-20">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=1920&h=600&fit=crop" 
                 alt="Hotel Room" class="w-full h-full object-cover opacity-30">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-widest uppercase text-sm mb-4">Reserve Your Room</p>
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6">Book Your Stay</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Experience luxury and comfort at MRK Hotel & Resort. Check availability and reserve your perfect room today.
            </p>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section class="py-16 -mt-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Booking Search Card -->
            <div class="bg-white rounded-xl shadow-2xl p-8 mb-16" x-data="{ step: 1 }">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-serif font-bold text-dark mb-2">Check Availability & Book</h2>
                    <p class="text-gray-600">Fill in your details to find available rooms</p>
                </div>

                <form action="{{ route('booking.search') }}" method="GET" class="space-y-8">
                    <!-- Step Indicators -->
                    <div class="flex items-center justify-center mb-8">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary text-white font-semibold">1</div>
                            <span class="ml-2 text-sm font-medium text-dark">Dates & Guests</span>
                        </div>
                        <div class="w-16 h-0.5 bg-gray-300 mx-4"></div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold">2</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Select Room</span>
                        </div>
                        <div class="w-16 h-0.5 bg-gray-300 mx-4"></div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold">3</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Confirm</span>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700 mb-2">Number of Guests</label>
                            <select id="guests" name="guests" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5+ Guests</option>
                            </select>
                        </div>
                        <div>
                            <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                            <select id="room_type" name="room_type"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                <option value="">All Room Types</option>
                                @if(isset($roomTypes))
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                @else
                                    <option value="standard">Standard Room</option>
                                    <option value="deluxe">Deluxe Room</option>
                                    <option value="suite">Suite</option>
                                    <option value="executive">Executive Suite</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" 
                                class="px-8 py-4 bg-primary hover:bg-primary-light text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Check Availability
                        </button>
                    </div>
                </form>
            </div>

            <!-- Available Rooms Section -->
            @if(isset($availableRooms) && count($availableRooms) > 0)
                <div class="mb-16">
                    <h2 class="text-3xl font-serif font-bold text-dark text-center mb-8">Available Rooms</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($availableRooms as $room)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="aspect-video bg-gray-200">
                                    <img src="{{ $room->image ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=400&fit=crop' }}" 
                                         alt="{{ $room->roomType->name ?? 'Room' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-serif font-bold text-dark mb-2">{{ $room->roomType->name ?? 'Room' }}</h3>
                                    <p class="text-gray-600 text-sm mb-4">Room {{ $room->room_number }} &bull; Floor {{ $room->floor->number ?? 'N/A' }}</p>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-2xl font-bold text-primary">${{ number_format($room->roomType->price_per_night ?? 150, 2) }}</span>
                                        <span class="text-gray-500 text-sm">per night</span>
                                    </div>
                                    <a href="{{ route('booking.room', $room->id) }}?check_in={{ request('check_in') }}&check_out={{ request('check_out') }}&guests={{ request('guests') }}" 
                                       class="block w-full text-center px-4 py-3 bg-accent hover:bg-secondary text-white font-semibold rounded-lg transition-colors">
                                        Select This Room
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(isset($availableRooms))
                <div class="text-center py-12 bg-white rounded-xl shadow-lg mb-16">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Rooms Available</h3>
                    <p class="text-gray-500">Sorry, no rooms are available for your selected dates. Please try different dates.</p>
                </div>
            @endif

            <!-- Room Types Showcase -->
            <div class="mb-16">
                <div class="text-center mb-12">
                    <p class="text-accent font-medium tracking-widest uppercase text-sm mb-2">Our Accommodations</p>
                    <h2 class="text-3xl font-serif font-bold text-dark">Room Types & Rates</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Standard Room -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden group">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=450&fit=crop" 
                                 alt="Standard Room" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-serif font-bold text-dark mb-2">Standard Room</h3>
                            <p class="text-gray-600 text-sm mb-4">Comfortable room with essential amenities for a pleasant stay.</p>
                            <ul class="text-sm text-gray-500 space-y-1 mb-4">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Queen Bed
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Free WiFi
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Up to 2 Guests
                                </li>
                            </ul>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-primary">$99</span>
                                <span class="text-gray-500 text-sm">/night</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deluxe Room -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden group">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&h=450&fit=crop" 
                                 alt="Deluxe Room" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-serif font-bold text-dark mb-2">Deluxe Room</h3>
                            <p class="text-gray-600 text-sm mb-4">Spacious room with premium furnishings and city views.</p>
                            <ul class="text-sm text-gray-500 space-y-1 mb-4">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    King Bed
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    City View
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Up to 3 Guests
                                </li>
                            </ul>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-primary">$149</span>
                                <span class="text-gray-500 text-sm">/night</span>
                            </div>
                        </div>
                    </div>

                    <!-- Suite -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden group">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&h=450&fit=crop" 
                                 alt="Suite" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-serif font-bold text-dark mb-2">Suite</h3>
                            <p class="text-gray-600 text-sm mb-4">Elegant suite with separate living area and luxurious amenities.</p>
                            <ul class="text-sm text-gray-500 space-y-1 mb-4">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    King Bed + Sofa
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Living Area
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Up to 4 Guests
                                </li>
                            </ul>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-primary">$249</span>
                                <span class="text-gray-500 text-sm">/night</span>
                            </div>
                        </div>
                    </div>

                    <!-- Executive Suite -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden group">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&h=450&fit=crop" 
                                 alt="Executive Suite" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-serif font-bold text-dark mb-2">Executive Suite</h3>
                            <p class="text-gray-600 text-sm mb-4">Ultimate luxury with panoramic views and exclusive services.</p>
                            <ul class="text-sm text-gray-500 space-y-1 mb-4">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Premium King Bed
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Private Balcony
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Butler Service
                                </li>
                            </ul>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-primary">$399</span>
                                <span class="text-gray-500 text-sm">/night</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest Information Form (shown when room is selected) -->
            @if(isset($selectedRoom))
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-16">
                    <h2 class="text-2xl font-serif font-bold text-dark text-center mb-8">Complete Your Reservation</h2>
                    
                    <!-- Booking Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="font-semibold text-dark mb-4">Booking Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Room</span>
                                <p class="font-medium text-dark">{{ $selectedRoom->roomType->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-in</span>
                                <p class="font-medium text-dark">{{ request('check_in') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-out</span>
                                <p class="font-medium text-dark">{{ request('check_out') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Total</span>
                                <p class="font-bold text-primary text-lg">${{ number_format($totalPrice ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                        <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                        <input type="hidden" name="check_out" value="{{ request('check_out') }}">
                        <input type="hidden" name="guests" value="{{ request('guests') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" id="guest_name" name="guest_name" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="John Doe">
                            </div>
                            <div>
                                <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="guest_email" name="guest_email" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="john@example.com">
                            </div>
                            <div>
                                <label for="guest_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" id="guest_phone" name="guest_phone" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="+1 (555) 000-0000">
                            </div>
                            <div>
                                <label for="guest_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" id="guest_country" name="guest_country"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="United States">
                            </div>
                        </div>

                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                            <textarea id="special_requests" name="special_requests" rows="3"
                                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                      placeholder="Any special requests or preferences..."></textarea>
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="terms" name="terms" required
                                   class="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="terms" class="text-sm text-gray-600">
                                I agree to the <a href="{{ url('/terms') }}" class="text-primary hover:underline">Terms & Conditions</a> 
                                and <a href="{{ url('/privacy') }}" class="text-primary hover:underline">Cancellation Policy</a>
                            </label>
                        </div>

                        <div class="text-center">
                            <button type="submit" 
                                    class="px-8 py-4 bg-accent hover:bg-secondary text-white font-semibold rounded-lg transition-colors text-lg">
                                Confirm Reservation
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Why Book Direct Section -->
            <div class="bg-primary rounded-xl p-8 md:p-12 text-center">
                <h2 class="text-2xl font-serif font-bold text-white mb-6">Why Book Directly With Us?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Best Rate Guarantee</h3>
                        <p class="text-gray-300 text-sm">Book directly for the lowest available rate.</p>
                    </div>
                    <div>
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Free Cancellation</h3>
                        <p class="text-gray-300 text-sm">Cancel up to 24 hours before check-in.</p>
                    </div>
                    <div>
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Exclusive Perks</h3>
                        <p class="text-gray-300 text-sm">Complimentary upgrades when available.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Bar -->
    <section class="bg-dark py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-300 text-center md:text-left">
                    Need assistance with your booking? Call us at <a href="tel:+15551234567" class="text-accent hover:underline font-medium">+1 (555) 123-4567</a>
                </p>
                <a href="{{ url('/contact') }}" class="px-6 py-2 border border-accent text-accent hover:bg-accent hover:text-white rounded-lg transition-colors">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.public-footer')
</body>
</html>
