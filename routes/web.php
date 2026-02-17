<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LaundryTaskController;
use App\Http\Controllers\LaundryItemController;
use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\BookingChargeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SnippePaymentController;
use App\Http\Controllers\ConferenceHallController;
use App\Http\Controllers\ConferenceBookingController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ConferenceParticipantController;
// Public welcome page (accessible to everyone)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public pages
Route::get('/about', function () {
    return view('welcome.about');
})->name('about');

Route::get('/contact', function () {
    return view('welcome.contact');
})->name('contact');

Route::get('/pricing', function () {
    return view('welcome.pricing');
})->name('pricing');

Route::get('/features', function () {
    return view('welcome.features');
})->name('features');

// Guest Booking Routes (public - no authentication required)
Route::get('/booking', [BookingController::class, 'showBookingPage'])->name('booking');
Route::get('/booking/search', [BookingController::class, 'searchAvailability'])->name('booking.search');
Route::get('/booking/room/{room}', [BookingController::class, 'showRoom'])->name('booking.room');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{reservation}', [BookingController::class, 'showConfirmation'])->name('booking.confirmation');

// ═══ PAYMENT WEBHOOKS (no auth — called by payment provider servers) ═══
Route::post('/payments/webhook/snippe', [SnippePaymentController::class, 'webhook'])
    ->name('payments.webhook.snippe')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Payment callback (redirect back from card/QR payments)
Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('buildings', BuildingController::class);
        Route::resource('floors', FloorController::class);
        Route::resource('room-types', RoomTypeController::class);
        Route::delete('room-types/{room_type}/media/{media}', [RoomTypeController::class, 'removeMedia'])->name('room-types.media.destroy');
        Route::resource('users', UserController::class);
    });

    // Admin & Supervisor Routes
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::resource('rooms', RoomController::class);
        Route::post('rooms/{room}/toggle-status', [RoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // All authenticated users
    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('reservations/{reservation}/no-show', [ReservationController::class, 'noShow'])->name('reservations.no-show');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Booking Management Routes (frontdesk)
    Route::get('bookings/api/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
    Route::get('bookings/api/available-rooms', [BookingController::class, 'getAvailableRooms'])->name('bookings.available-rooms');
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [BookingController::class, 'storeFrontdesk'])->name('bookings.store');
    Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Guest Management Routes
    Route::resource('guests', GuestController::class);
    Route::get('guests-search', [GuestController::class, 'search'])->name('guests.search');
    Route::delete('guests/{guest}/media/{media}', [GuestController::class, 'removeMedia'])->name('guests.media.destroy');

    // Laundry Management Routes (Legacy - keeping for backward compatibility)
    // Combined: Admin, Supervisor, and House Help
    Route::middleware(['role:admin,supervisor,house_help'])->group(function () {
        Route::get('laundry-tasks', [LaundryTaskController::class, 'index'])->name('laundry-tasks.index');
        Route::post('laundry-tasks/{laundryTask}/mark-returned', [LaundryTaskController::class, 'markAsReturned'])->name('laundry-tasks.mark-returned');
    });

    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::get('laundry-tasks/create', [LaundryTaskController::class, 'create'])->name('laundry-tasks.create');
        Route::post('laundry-tasks', [LaundryTaskController::class, 'store'])->name('laundry-tasks.store');
        Route::get('laundry-tasks/{laundryTask}/edit', [LaundryTaskController::class, 'edit'])->name('laundry-tasks.edit');
        Route::put('laundry-tasks/{laundryTask}', [LaundryTaskController::class, 'update'])->name('laundry-tasks.update');
        Route::delete('laundry-tasks/{laundryTask}', [LaundryTaskController::class, 'destroy'])->name('laundry-tasks.destroy');
        Route::post('laundry-tasks/{laundryTask}/mark-in-progress', [LaundryTaskController::class, 'markAsInProgress'])->name('laundry-tasks.mark-in-progress');
        Route::post('laundry-tasks/{laundryTask}/mark-completed', [LaundryTaskController::class, 'markAsCompleted'])->name('laundry-tasks.mark-completed');
    });

    // ═══ NEW LAUNDRY SYSTEM ═══

    // Laundry Items (Admin & Supervisor - pricing management)
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::resource('laundry-items', LaundryItemController::class);
    });

    // Laundry Orders - View (all staff roles)
    Route::middleware(['role:admin,supervisor,front_desk,house_help,manager,store_keeper'])->group(function () {
        Route::get('laundry', [LaundryOrderController::class, 'index'])->name('laundry.index');
        Route::get('laundry/{laundryOrder}', [LaundryOrderController::class, 'show'])->name('laundry.show');
        Route::get('laundry-orders/items', [LaundryOrderController::class, 'getItems'])->name('laundry.items.json');
    });

    // Laundry Orders - Create/Edit (Front Desk, Admin, Supervisor)
    Route::middleware(['role:admin,supervisor,front_desk'])->group(function () {
        Route::get('laundry-orders/create', [LaundryOrderController::class, 'create'])->name('laundry.create');
        Route::post('laundry-orders', [LaundryOrderController::class, 'store'])->name('laundry.store');
        Route::get('laundry-orders/{laundryOrder}/edit', [LaundryOrderController::class, 'edit'])->name('laundry.edit');
        Route::put('laundry-orders/{laundryOrder}', [LaundryOrderController::class, 'update'])->name('laundry.update');
        Route::delete('laundry-orders/{laundryOrder}', [LaundryOrderController::class, 'destroy'])->name('laundry.destroy');
        Route::post('laundry-orders/{laundryOrder}/mark-delivered', [LaundryOrderController::class, 'markDelivered'])->name('laundry.mark-delivered');
    });

    // Laundry Orders - Status updates (House Help can mark in-progress & completed)
    Route::middleware(['role:admin,supervisor,house_help'])->group(function () {
        Route::post('laundry-orders/{laundryOrder}/mark-in-progress', [LaundryOrderController::class, 'markInProgress'])->name('laundry.mark-in-progress');
        Route::post('laundry-orders/{laundryOrder}/mark-completed', [LaundryOrderController::class, 'markCompleted'])->name('laundry.mark-completed');
    });

    // Booking Charges
    Route::middleware(['role:admin,supervisor,front_desk,manager'])->group(function () {
        Route::get('bookings/{booking}/charges', [BookingChargeController::class, 'index'])->name('booking-charges.index');
        Route::post('booking-charges/{bookingCharge}/mark-paid', [BookingChargeController::class, 'markPaid'])->name('booking-charges.mark-paid');
        Route::post('bookings/{booking}/charges/mark-all-paid', [BookingChargeController::class, 'markAllPaid'])->name('booking-charges.mark-all-paid');
    });

    // ═══ PAYMENTS ═══
    Route::middleware(['role:admin,supervisor,front_desk,manager'])->group(function () {
        Route::get('bookings/{booking}/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('bookings/{booking}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('bookings/{booking}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('payments/{payment}/status', [PaymentController::class, 'status'])->name('payments.status');
        Route::get('payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
        Route::post('payments/{payment}/trigger-push', [PaymentController::class, 'triggerPush'])->name('payments.trigger-push');
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    });

    // Conference Management Routes (Front Desk, Admin, Supervisor)
    Route::middleware(['role:admin,supervisor,front_desk'])->group(function () {
        // Conference Halls
        Route::resource('conference-halls', ConferenceHallController::class);
        
        // Conference Bookings
        Route::get('conference-bookings', [ConferenceBookingController::class, 'index'])->name('conference-bookings.index');
        Route::get('conference-bookings/create', [ConferenceBookingController::class, 'create'])->name('conference-bookings.create');
        Route::post('conference-bookings', [ConferenceBookingController::class, 'store'])->name('conference-bookings.store');
        Route::get('conference-bookings/{conferenceBooking}', [ConferenceBookingController::class, 'show'])->name('conference-bookings.show');
        Route::get('conference-bookings/{conferenceBooking}/edit', [ConferenceBookingController::class, 'edit'])->name('conference-bookings.edit');
        Route::put('conference-bookings/{conferenceBooking}', [ConferenceBookingController::class, 'update'])->name('conference-bookings.update');
        Route::delete('conference-bookings/{conferenceBooking}', [ConferenceBookingController::class, 'destroy'])->name('conference-bookings.destroy');
        Route::post('conference-bookings/{conferenceBooking}/confirm', [ConferenceBookingController::class, 'confirm'])->name('conference-bookings.confirm');
        Route::post('conference-bookings/{conferenceBooking}/cancel', [ConferenceBookingController::class, 'cancel'])->name('conference-bookings.cancel');
        Route::get('conference-bookings/check-availability', [ConferenceBookingController::class, 'checkAvailability'])->name('conference-bookings.check-availability');
        
        // Conferences
        Route::get('conferences', [ConferenceController::class, 'index'])->name('conferences.index');
        Route::get('conferences/create', [ConferenceController::class, 'create'])->name('conferences.create');
        Route::post('conferences', [ConferenceController::class, 'store'])->name('conferences.store');
        Route::get('conferences/{conference}', [ConferenceController::class, 'show'])->name('conferences.show');
        Route::get('conferences/{conference}/edit', [ConferenceController::class, 'edit'])->name('conferences.edit');
        Route::put('conferences/{conference}', [ConferenceController::class, 'update'])->name('conferences.update');
        Route::delete('conferences/{conference}', [ConferenceController::class, 'destroy'])->name('conferences.destroy');
        
        // Conference Participants
        Route::post('conferences/{conference}/participants', [ConferenceParticipantController::class, 'store'])->name('conference-participants.store');
        Route::put('conference-participants/{participant}', [ConferenceParticipantController::class, 'update'])->name('conference-participants.update');
        Route::delete('conference-participants/{participant}', [ConferenceParticipantController::class, 'destroy'])->name('conference-participants.destroy');
        Route::get('conference-participants/{participant}/badge', [ConferenceParticipantController::class, 'printBadge'])->name('conference-participants.badge');
        Route::get('conferences/{conference}/badges', [ConferenceParticipantController::class, 'printAllBadges'])->name('conferences.badges');
        Route::post('conference-participants/{participant}/convert-to-guest', [ConferenceParticipantController::class, 'convertToGuest'])->name('conference-participants.convert-to-guest');
        
        // Check-in
        Route::get('conferences/{conference}/check-in', [ConferenceParticipantController::class, 'checkInDashboard'])->name('conferences.check-in');
        Route::post('conference-check-in/scan', [ConferenceParticipantController::class, 'checkInByScan'])->name('conference-check-in.scan');
        Route::post('conference-check-in/manual', [ConferenceParticipantController::class, 'checkInByCode'])->name('conference-check-in.manual');
    });
});