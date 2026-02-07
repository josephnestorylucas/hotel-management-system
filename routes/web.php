<?php
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
Route::get('/booking', function () {
    $roomTypes = \App\Models\RoomType::all();
    return view('public.booking', compact('roomTypes'));
})->name('booking');

Route::get('/booking/search', function () {
    $roomTypes = \App\Models\RoomType::all();
    $checkIn = request('check_in');
    $checkOut = request('check_out');
    $guests = request('guests');
    $roomTypeId = request('room_type');
    
    // Get available rooms for the selected dates
    $availableRooms = \App\Models\Room::with(['roomType', 'floor'])
        ->where('status', 'available')
        ->when($roomTypeId, function($query) use ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        })
        ->whereDoesntHave('reservations', function($query) use ($checkIn, $checkOut) {
            $query->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in_date', '<=', $checkIn)
                         ->where('check_out_date', '>=', $checkOut);
                  });
            })->whereNotIn('status', ['cancelled', 'checked_out']);
        })
        ->get();
    
    return view('public.booking', compact('roomTypes', 'availableRooms', 'checkIn', 'checkOut', 'guests'));
})->name('booking.search');

Route::get('/booking/room/{room}', function (\App\Models\Room $room) {
    $roomTypes = \App\Models\RoomType::all();
    $selectedRoom = $room->load(['roomType', 'floor']);
    $checkIn = request('check_in');
    $checkOut = request('check_out');
    $guests = request('guests');
    
    // Calculate total price
    $nights = (strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24);
    $totalPrice = $nights * ($room->roomType->price_per_night ?? 150);
    
    return view('public.booking', compact('roomTypes', 'selectedRoom', 'checkIn', 'checkOut', 'guests', 'totalPrice'));
})->name('booking.room');

Route::post('/booking', function () {
    $validated = request()->validate([
        'room_id' => 'required|uuid|exists:rooms,id',
        'check_in' => 'required|date',
        'check_out' => 'required|date|after:check_in',
        'guests' => 'required|integer|min:1',
        'guest_name' => 'required|string|max:255',
        'guest_email' => 'required|email|max:255',
        'guest_phone' => 'required|string|max:50',
        'guest_country' => 'nullable|string|max:100',
        'special_requests' => 'nullable|string|max:1000',
    ]);
    
    $room = \App\Models\Room::with('roomType')->findOrFail($validated['room_id']);
    $nights = (strtotime($validated['check_out']) - strtotime($validated['check_in'])) / (60 * 60 * 24);
    $totalPrice = $nights * ($room->roomType->price_per_night ?? 150);
    
    // Create the reservation
    $reservation = \App\Models\Reservation::create([
        'room_id' => $validated['room_id'],
        'guest_name' => $validated['guest_name'],
        'guest_email' => $validated['guest_email'],
        'guest_phone' => $validated['guest_phone'],
        'check_in_date' => $validated['check_in'],
        'check_out_date' => $validated['check_out'],
        'guests_count' => $validated['guests'],
        'total_price' => $totalPrice,
        'special_requests' => $validated['special_requests'] ?? null,
        'status' => 'pending',
    ]);
    
    return redirect()->route('booking.confirmation', $reservation->id);
})->name('booking.store');

Route::get('/booking/confirmation/{reservation}', function (\App\Models\Reservation $reservation) {
    $reservation->load(['room.roomType', 'room.floor']);
    return view('public.booking-confirmation', compact('reservation'));
})->name('booking.confirmation');

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
        Route::resource('users', UserController::class);
    });

    // Admin & Supervisor Routes
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::resource('rooms', RoomController::class);
        Route::post('rooms/{room}/toggle-status', [RoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
    });

    Route::middleware(['auth'])->group(function () {
    // ... existing routes ...
    
    // Add profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // All authenticated users
    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Guest Management Routes
    Route::resource('guests', GuestController::class);
    Route::get('guests-search', [GuestController::class, 'search'])->name('guests.search');
    Route::delete('guests/{guest}/media/{media}', [GuestController::class, 'removeMedia'])->name('guests.media.destroy');
});