{{-- resources/views/booking-confirmation.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - MRK Hotel & Resort</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Your reservation at MRK Hotel & Resort has been confirmed.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005eb8',
                        secondary: '#000000',
                        dark: '#1a202c',
                    },
                    fontFamily: {
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

    <!-- Confirmation Content -->
    <section class="pt-32 pb-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-secondary mb-4">Reservation Confirmed!</h1>
                <p class="text-xl text-gray-500">Thank you for choosing MRK Hotel & Resort</p>
            </div>

            <!-- Confirmation Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <!-- Header -->
                <div class="bg-gradient-to-r from-primary to-blue-600 p-6 text-center">
                    <p class="text-white/80 font-semibold tracking-widest uppercase text-sm mb-1">Booking Number</p>
                    <h2 class="text-3xl font-bold text-white tracking-wider">{{ $booking->booking_number }}</h2>
                </div>

                <!-- Reservation Details -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Guest Information -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Guest Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-500 text-sm">Name</span>
                                    <p class="font-semibold text-secondary">{{ $booking->guest_name }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Email</span>
                                    <p class="font-semibold text-secondary">{{ $booking->guest_email }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Phone</span>
                                    <p class="font-semibold text-secondary">{{ $booking->guest_phone }}</p>
                                </div>
                                @if($booking->guest_country)
                                <div>
                                    <span class="text-gray-500 text-sm">Country</span>
                                    <p class="font-semibold text-secondary">{{ $booking->guest_country }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stay Details -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Stay Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <div>
                                        <span class="text-gray-500 text-sm">Check-in</span>
                                        <p class="font-semibold text-secondary">{{ $booking->check_in_date->format('D, M d, Y') }}</p>
                                        <p class="text-sm text-gray-500">After 3:00 PM</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-500 text-sm">Check-out</span>
                                        <p class="font-semibold text-secondary">{{ $booking->check_out_date->format('D, M d, Y') }}</p>
                                        <p class="text-sm text-gray-500">Before 11:00 AM</p>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Guests</span>
                                    <p class="font-semibold text-secondary">{{ $booking->number_of_guests }} {{ $booking->number_of_guests > 1 ? 'Guests' : 'Guest' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Information -->
                    <div class="border-t border-gray-100 pt-6 mb-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Room Details</h3>
                        <div class="flex items-start gap-6">
                            <div class="w-32 h-24 bg-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=300&h=200&fit=crop" 
                                     alt="{{ $booking->room->roomType->name ?? 'Room' }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-secondary">{{ $booking->room->roomType->name ?? 'Room' }}</h4>
                                <p class="text-gray-500">Room {{ $booking->room->room_number }} &bull; Floor {{ $booking->room->floor->number ?? 'N/A' }}</p>
                                @if($booking->special_requests)
                                    <p class="text-sm text-gray-500 mt-2">
                                        <span class="font-medium">Special Requests:</span> {{ $booking->special_requests }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Payment Summary</h3>
                        @php
                            $nights = $booking->nights;
                            $pricePerNight = $booking->room->roomType->price_per_night ?? 150;
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-500">
                                <span>${{ number_format($pricePerNight, 2) }} x {{ $nights }} {{ $nights > 1 ? 'nights' : 'night' }}</span>
                                <span>${{ number_format($pricePerNight * $nights, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Taxes & Fees</span>
                                <span>Included</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-secondary pt-2 border-t border-gray-100 mt-2">
                                <span>Total</span>
                                <span class="text-primary">${{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Payment will be collected at check-in.</p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="bg-gradient-to-br from-primary/5 to-blue-50 px-8 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-sm font-medium text-gray-700">Status: {{ ucfirst($booking->status) }}</span>
                        </div>
                        <span class="text-sm text-gray-500">A confirmation email has been sent to {{ $booking->guest_email }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/booking') }}" class="px-6 py-3 bg-gradient-to-r from-primary to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-shadow text-center">
                    Make Another Reservation
                </a>
                <a href="{{ url('/') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary font-semibold rounded-xl transition-colors text-center">
                    Return to Home
                </a>
            </div>

            <!-- Important Information -->
            <div class="mt-12 bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-extrabold text-secondary mb-6">Important Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-secondary mb-2">Check-in Policy</h4>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Check-in time: 3:00 PM</li>
                            <li>• Valid ID required at check-in</li>
                            <li>• Credit card for incidentals</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary mb-2">Check-out Policy</h4>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Check-out time: 11:00 AM</li>
                            <li>• Late check-out available upon request</li>
                            <li>• Express check-out available</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary mb-2">Cancellation Policy</h4>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Free cancellation up to 24 hours before check-in</li>
                            <li>• No-show will be charged one night</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary mb-2">Need Help?</h4>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Call: +1 (555) 123-4567</li>
                            <li>• Email: reservations@mrkhotel.com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.public-footer')
</body>
</html>
