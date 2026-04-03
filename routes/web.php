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
use App\Http\Controllers\Laundry\LaundryServiceController;
use App\Http\Controllers\Laundry\LaundryOrderController as NewLaundryOrderController;
use App\Http\Controllers\Laundry\LaundryReportController;
use App\Http\Controllers\BookingChargeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SnippePaymentController;
use App\Http\Controllers\ConferenceHallController;
use App\Http\Controllers\ConferenceBookingController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ConferenceParticipantController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StockController;
use App\Http\Controllers\Store\AdjustmentController;
use App\Http\Controllers\Store\InternalRequestController;
use App\Http\Controllers\Store\StockTransferController;
use App\Http\Controllers\Store\ReportController;
use App\Http\Controllers\Restaurant\MenuItemController;
use App\Http\Controllers\Restaurant\TableController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\Restaurant\ReportController as RestaurantReportController;
use App\Http\Controllers\Finance\CheckoutController as FinanceCheckoutController;
use App\Http\Controllers\Finance\FinancePaymentController;
use App\Http\Controllers\Finance\ReceiptController as FinanceReceiptController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Finance\FinancialDashboardController;
use App\Http\Controllers\Procurement\DashboardController as ProcurementDashboardController;
use App\Http\Controllers\Procurement\SupplierController;
use App\Http\Controllers\Procurement\LocalPurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceivedNoteController;
use App\Http\Controllers\Accounting\AccountingReportController;
use App\Http\Controllers\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\InvoiceController;
use App\Http\Controllers\Accounting\PayrollController;
use App\Http\Controllers\Accounting\BankReconciliationController;
use App\Http\Controllers\Finance\PettyCashController;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\LanguageController;

// Language Switch Route (accessible to everyone)
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

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

// Guest Booking Routes (public - no authentication required, rate-limited)
Route::get('/booking', [BookingController::class, 'showBookingPage'])->name('booking');
Route::get('/booking/search', [BookingController::class, 'searchAvailability'])->name('booking.search')->middleware('throttle:30,1');
Route::get('/booking/room/{room}', [BookingController::class, 'showRoom'])->name('booking.room');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:10,60');
Route::get('/booking/confirmation/{reservation}', [BookingController::class, 'showConfirmation'])->name('booking.confirmation');

// ═══ PAYMENT WEBHOOKS (no auth — called by payment provider servers) ═══
Route::post('/payments/webhook/snippe', [SnippePaymentController::class, 'webhook'])
    ->name('payments.webhook.snippe')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Payment callback (redirect back from card/QR payments)
Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');

// Guest Routes — rate-limited to prevent brute-force attacks
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:3,60');
    
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:3,60');
    
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');
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
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Reservations, Bookings, Guests — restricted to authorized roles (NO admin - admin is system only)
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
        Route::resource('reservations', ReservationController::class);
        Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
        Route::post('reservations/{reservation}/no-show', [ReservationController::class, 'noShow'])->name('reservations.no-show');
        Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Booking Management Routes
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
    });

    // ═══ LAUNDRY MODULE ═══ (NO admin - admin is system only; manager has oversight)
    Route::prefix('laundry')->name('laundry.')->group(function () {

        // ── Price List ────────────────────────────────────────────────────────
        Route::get('services', [LaundryServiceController::class, 'index'])
             ->name('services.index')
             ->middleware('role:laundry_manager,supervisor,manager');
        Route::post('services/{service}/items', [LaundryServiceController::class, 'addItem'])
             ->name('services.add-item')
             ->middleware('role:laundry_manager,manager');
        Route::put('services/{service}/items/{item}', [LaundryServiceController::class, 'updateItem'])
             ->name('services.update-item')
             ->middleware('role:laundry_manager,manager');
        Route::delete('services/{service}/items/{item}', [LaundryServiceController::class, 'removeItem'])
             ->name('services.remove-item')
             ->middleware('role:laundry_manager,manager');

        // ── Orders ────────────────────────────────────────────────────────────
        Route::get('orders', [NewLaundryOrderController::class, 'index'])
             ->name('orders.index')
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager,cashier');
        Route::get('orders/create', [NewLaundryOrderController::class, 'create'])
             ->name('orders.create')
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager');
        Route::post('orders', [NewLaundryOrderController::class, 'store'])
             ->name('orders.store')
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager');
        Route::get('orders/{laundryOrder}', [NewLaundryOrderController::class, 'show'])
             ->name('orders.show')
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager,cashier');
        Route::post('orders/{laundryOrder}/process', [NewLaundryOrderController::class, 'process'])
             ->name('orders.process')
             ->middleware('role:house_help,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/ready', [NewLaundryOrderController::class, 'markReady'])
             ->name('orders.ready')
             ->middleware('role:house_help,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/deliver', [NewLaundryOrderController::class, 'deliver'])
             ->name('orders.deliver')
             ->middleware('role:house_help,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/collected', [NewLaundryOrderController::class, 'collected'])
             ->name('orders.collected')
             ->middleware('role:house_help,cashier,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/settle', [NewLaundryOrderController::class, 'settle'])
             ->name('orders.settle')
             ->middleware('role:cashier,front_desk,laundry_manager,supervisor,manager');
        Route::post('orders/{laundryOrder}/cancel', [NewLaundryOrderController::class, 'cancel'])
             ->name('orders.cancel')
             ->middleware('role:supervisor,laundry_manager,manager');

        // ── Reports ───────────────────────────────────────────────────────────
        Route::get('reports/daily', [LaundryReportController::class, 'daily'])
             ->name('reports.daily')
             ->middleware('role:laundry_manager,supervisor,manager');
    });

    // Booking Charges (NO admin - admin is system only)
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
        Route::get('bookings/{booking}/charges', [BookingChargeController::class, 'index'])->name('booking-charges.index');
        Route::post('booking-charges/{bookingCharge}/mark-paid', [BookingChargeController::class, 'markPaid'])->name('booking-charges.mark-paid');
        Route::post('bookings/{booking}/charges/mark-all-paid', [BookingChargeController::class, 'markAllPaid'])->name('booking-charges.mark-all-paid');
    });

    // ═══ PAYMENTS ═══ (NO admin - admin is system only)
    Route::middleware(['role:supervisor,front_desk,manager,cashier'])->group(function () {
        Route::get('bookings/{booking}/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('bookings/{booking}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('bookings/{booking}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('payments/{payment}/status', [PaymentController::class, 'status'])->name('payments.status');
        Route::get('payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
        Route::post('payments/{payment}/trigger-push', [PaymentController::class, 'triggerPush'])->name('payments.trigger-push');
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    });

    // Conference Management Routes (Manager has hall booking/conference access but NOT hall management)
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
        // Conference Bookings (Hall Bookings) - Manager has full access
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
        
        // Conferences - Manager has full access
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

    // Conference Halls CRUD (Admin only - infrastructure management)
    // NOTE: Create route must come BEFORE the {conferenceHall} wildcard route
    Route::middleware(['role:admin'])->group(function () {
        Route::get('conference-halls/create', [ConferenceHallController::class, 'create'])->name('conference-halls.create');
        Route::post('conference-halls', [ConferenceHallController::class, 'store'])->name('conference-halls.store');
        Route::get('conference-halls/{conferenceHall}/edit', [ConferenceHallController::class, 'edit'])->name('conference-halls.edit');
        Route::put('conference-halls/{conferenceHall}', [ConferenceHallController::class, 'update'])->name('conference-halls.update');
        Route::delete('conference-halls/{conferenceHall}', [ConferenceHallController::class, 'destroy'])->name('conference-halls.destroy');
    });

    // Conference Halls - View access for manager/supervisor/front_desk/admin
    Route::middleware(['role:admin,supervisor,front_desk,manager'])->group(function () {
        Route::get('conference-halls', [ConferenceHallController::class, 'index'])->name('conference-halls.index');
        Route::get('conference-halls/{conferenceHall}', [ConferenceHallController::class, 'show'])->name('conference-halls.show');
    });

    // ═══ STORE MODULE ═══
    Route::prefix('store')->name('store.')->group(function () {

        // ── Products ──────────────────────────────────────────────────────
        Route::get('products',                  [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create',           [ProductController::class, 'create'])->name('products.create')
             ->middleware('role:store_manager');
        Route::post('products',                 [ProductController::class, 'store'])->name('products.store')
             ->middleware('role:store_manager');
        Route::get('products/{product}',        [ProductController::class, 'show'])->name('products.show');
        Route::get('products/{product}/edit',   [ProductController::class, 'edit'])->name('products.edit')
             ->middleware('role:store_manager');
        Route::put('products/{product}',        [ProductController::class, 'update'])->name('products.update')
             ->middleware('role:store_manager');
        Route::delete('products/{product}',     [ProductController::class, 'destroy'])->name('products.destroy')
             ->middleware('role:store_manager');

        // ── Stock ─────────────────────────────────────────────────────────
        Route::get('stock/levels',              [StockController::class, 'levels'])->name('stock.levels')
             ->middleware('role:store_manager,store_keeper');
        Route::get('stock/restock',             [StockController::class, 'restockForm'])->name('stock.restock-form')
             ->middleware('role:store_keeper,store_manager');
        Route::post('stock/restock',            [StockController::class, 'restock'])->name('stock.restock')
             ->middleware('role:store_keeper,store_manager');
        Route::get('stock/damage',              [StockController::class, 'damageForm'])->name('stock.damage-form')
             ->middleware('role:store_keeper,store_manager,restaurant_manager');
        Route::post('stock/damage',             [StockController::class, 'damage'])->name('stock.damage')
             ->middleware('role:store_keeper,store_manager,restaurant_manager');

        // ── Adjustments ───────────────────────────────────────────────────
        Route::get('adjustments',              [AdjustmentController::class, 'index'])->name('adjustments.index')
             ->middleware('role:store_manager,supervisor');
        Route::get('adjustments/create',       [AdjustmentController::class, 'create'])->name('adjustments.create')
             ->middleware('role:store_manager,supervisor,restaurant_manager');
        Route::post('adjustments',             [AdjustmentController::class, 'store'])->name('adjustments.store')
             ->middleware('role:store_manager,supervisor,restaurant_manager');
        Route::post('adjustments/{adjustment}/approve', [AdjustmentController::class, 'approve'])->name('adjustments.approve')
             ->middleware('role:store_manager');
        Route::post('adjustments/{adjustment}/reject',  [AdjustmentController::class, 'reject'])->name('adjustments.reject')
             ->middleware('role:store_manager');

        // ── Internal Requests ─────────────────────────────────────────────
        Route::get('internal-requests',                [InternalRequestController::class, 'index'])->name('internal-requests.index');
        Route::get('internal-requests/create',         [InternalRequestController::class, 'create'])->name('internal-requests.create')
             ->middleware('role:house_help');
        Route::post('internal-requests',               [InternalRequestController::class, 'store'])->name('internal-requests.store')
             ->middleware('role:house_help');
        Route::post('internal-requests/{internalUsageRequest}/approve', [InternalRequestController::class, 'approve'])->name('internal-requests.approve')
             ->middleware('role:supervisor');
        Route::post('internal-requests/{internalUsageRequest}/reject',  [InternalRequestController::class, 'reject'])->name('internal-requests.reject')
             ->middleware('role:supervisor');
        Route::post('internal-requests/{internalUsageRequest}/fulfill', [InternalRequestController::class, 'fulfill'])->name('internal-requests.fulfill')
             ->middleware('role:store_keeper');
        Route::post('internal-requests/{internalUsageRequest}/cancel',  [InternalRequestController::class, 'cancel'])->name('internal-requests.cancel');

        // ── Stock Transfers ───────────────────────────────────────────────
        Route::get('transfers',                [StockTransferController::class, 'index'])->name('transfers.index')
             ->middleware('role:store_manager,store_keeper,restaurant_manager');
        Route::get('transfers/create',         [StockTransferController::class, 'create'])->name('transfers.create')
             ->middleware('role:restaurant_manager');
        Route::post('transfers',               [StockTransferController::class, 'store'])->name('transfers.store')
             ->middleware('role:restaurant_manager');
        Route::post('transfers/{stockTransfer}/fulfill', [StockTransferController::class, 'fulfill'])->name('transfers.fulfill')
             ->middleware('role:store_keeper,store_manager');
        Route::post('transfers/{stockTransfer}/reject',  [StockTransferController::class, 'reject'])->name('transfers.reject')
             ->middleware('role:store_manager');

        // ── Reports ───────────────────────────────────────────────────────
        Route::get('reports/stock-snapshot',   [ReportController::class, 'stockSnapshot'])->name('reports.stock-snapshot')
             ->middleware('role:store_manager,store_keeper');
        Route::get('reports/movements',        [ReportController::class, 'movements'])->name('reports.movements')
             ->middleware('role:store_manager,store_keeper');
        Route::get('reports/damage',           [ReportController::class, 'damage'])->name('reports.damage')
             ->middleware('role:store_manager,supervisor');
    });

    // ═══ BAR & RESTAURANT MODULE ═══
    Route::prefix('restaurant')->name('restaurant.')->group(function () {

        // ── Menu (CRUD: restaurant_manager only; index: any authenticated) ──
        Route::get('menu',                   [MenuItemController::class, 'index'])->name('menu.index');
        Route::get('menu/create',            [MenuItemController::class, 'create'])->name('menu.create')
             ->middleware('role:restaurant_manager,admin');
        Route::post('menu',                  [MenuItemController::class, 'store'])->name('menu.store')
             ->middleware('role:restaurant_manager,admin');
        Route::get('menu/{menuItem}/edit',   [MenuItemController::class, 'edit'])->name('menu.edit')
             ->middleware('role:restaurant_manager,admin');
        Route::put('menu/{menuItem}',        [MenuItemController::class, 'update'])->name('menu.update')
             ->middleware('role:restaurant_manager,admin');
        Route::delete('menu/{menuItem}',     [MenuItemController::class, 'destroy'])->name('menu.destroy')
             ->middleware('role:restaurant_manager,admin');

        // ── Tables ────────────────────────────────────────────────────────
        Route::get('tables',                 [TableController::class, 'index'])->name('tables.index');
        Route::post('tables',                [TableController::class, 'store'])->name('tables.store')
             ->middleware('role:restaurant_manager,admin');
        Route::post('tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus');

        // ── Orders ────────────────────────────────────────────────────────
        Route::get('orders',                       [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create',                [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders',                      [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}',               [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/send',         [OrderController::class, 'send'])->name('orders.send');
        Route::post('orders/{order}/ready',        [OrderController::class, 'ready'])->name('orders.ready');
        Route::post('orders/{order}/serve',        [OrderController::class, 'serve'])->name('orders.serve');
        Route::post('orders/{order}/settle',       [OrderController::class, 'settle'])->name('orders.settle')
             ->middleware('role:restaurant_manager,cashier,admin');
        Route::post('orders/{order}/cancel',       [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/items',        [OrderController::class, 'addItem'])->name('orders.addItem');
        Route::delete('orders/{order}/items/{orderItem}', [OrderController::class, 'removeItem'])->name('orders.removeItem');

        // ── Reports (restaurant_manager / admin only) ─────────────────────
        Route::get('reports/daily-sales',    [RestaurantReportController::class, 'dailySales'])->name('reports.dailySales')
             ->middleware('role:restaurant_manager,admin');
        Route::get('reports/popular-items',  [RestaurantReportController::class, 'popularItems'])->name('reports.popularItems')
             ->middleware('role:restaurant_manager,admin');
    });

    // ═══ FINANCE MODULE ═══
    Route::prefix('finance')->name('finance.')->group(function () {

        // ── Dashboard ─────────────────────────────────────────────────────────────
        Route::get('dashboard', [FinancialDashboardController::class, 'index'])->name('dashboard')
             ->middleware('role:store_manager,cashier,front_desk,manager');

        // ── Checkout ──────────────────────────────────────────────────────────────
        Route::get('checkout/{booking}',              [FinanceCheckoutController::class, 'show'])->name('checkout.show')
             ->middleware('role:front_desk,cashier,manager');
        Route::post('checkout/{checkout}/process',    [FinanceCheckoutController::class, 'process'])->name('checkout.process')
             ->middleware('role:cashier,front_desk,manager');
        Route::post('checkout/{checkout}/add-charge', [FinanceCheckoutController::class, 'addCharge'])->name('checkout.add-charge')
             ->middleware('role:front_desk,cashier,manager');

        // ── Walk-in Payments ──────────────────────────────────────────────────────
        Route::get('payments',         [FinancePaymentController::class, 'index'])->name('payments.index')
             ->middleware('role:cashier,store_manager');
        Route::post('payments/walkin', [FinancePaymentController::class, 'storeWalkin'])->name('payments.walkin')
             ->middleware('role:cashier,bar_tender,restaurant_manager');
        
        // ── Walk-in Payment Processing (unified for laundry/restaurant/bar) ──────
        Route::post('walkin-payment/process', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'process'])
             ->name('walkin-payment.process')
             ->middleware('role:cashier,front_desk,laundry_manager,supervisor,bar_tender,restaurant_manager,manager,admin');
        Route::get('walkin-payment/status/{reference}', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'status'])
             ->name('walkin-payment.status')
             ->middleware('role:cashier,front_desk,laundry_manager,supervisor,bar_tender,restaurant_manager,manager,admin');
        Route::get('walkin-payment/callback/{transaction}', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'callback'])
             ->name('walkin-payment.callback');

        // ── Receipts ──────────────────────────────────────────────────────────────
        Route::get('receipts/guest/{checkout}', [FinanceReceiptController::class, 'guest'])->name('receipt.guest');
        Route::get('receipts/walkin',           [FinanceReceiptController::class, 'walkin'])->name('receipt.walkin');
        // ── Petty Cash ──────────────────────────────────────────────────────
        Route::post('petty-cash/{pettyCash}/approve', [PettyCashController::class, 'approve'])->name('petty-cash.approve')
             ->middleware('role:store_manager');
        Route::post('petty-cash/{pettyCash}/reject',  [PettyCashController::class, 'reject'])->name('petty-cash.reject')
             ->middleware('role:store_manager');
    });

    // ═══ PROCUREMENT MODULE ═══ (Store Manager exclusive - per Task 8 requirements)
    Route::prefix('procurement')->name('procurement.')->group(function () {

        // Dashboard
        Route::get('/', [ProcurementDashboardController::class, 'index'])->name('dashboard')
             ->middleware('role:store_manager,store_keeper');

        // ── Suppliers ─────────────────────────────────────────────────────
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')
             ->middleware('role:store_manager,store_keeper');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show')
             ->middleware('role:store_manager,store_keeper');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')
             ->middleware('role:store_manager,store_keeper');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')
             ->middleware('role:store_manager,store_keeper');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')
             ->middleware('role:store_manager');

        // ── Local Purchase Orders ─────────────────────────────────────────
        Route::get('lpo', [LocalPurchaseOrderController::class, 'index'])->name('lpo.index')
             ->middleware('role:store_manager,store_keeper');
        Route::get('lpo/create', [LocalPurchaseOrderController::class, 'create'])->name('lpo.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('lpo', [LocalPurchaseOrderController::class, 'store'])->name('lpo.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'show'])->name('lpo.show')
             ->middleware('role:store_manager,store_keeper');
        Route::get('lpo/{localPurchaseOrder}/edit', [LocalPurchaseOrderController::class, 'edit'])->name('lpo.edit')
             ->middleware('role:store_manager,store_keeper');
        Route::put('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'update'])->name('lpo.update')
             ->middleware('role:store_manager,store_keeper');
        Route::delete('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'destroy'])->name('lpo.destroy')
             ->middleware('role:store_manager');
        Route::post('lpo/{localPurchaseOrder}/submit', [LocalPurchaseOrderController::class, 'submitForApproval'])->name('lpo.submit')
             ->middleware('role:store_manager,store_keeper');
        Route::post('lpo/{localPurchaseOrder}/approve', [LocalPurchaseOrderController::class, 'approve'])->name('lpo.approve')
             ->middleware('role:store_manager');
        Route::post('lpo/{localPurchaseOrder}/reject', [LocalPurchaseOrderController::class, 'reject'])->name('lpo.reject')
             ->middleware('role:store_manager');
        Route::post('lpo/{localPurchaseOrder}/sent', [LocalPurchaseOrderController::class, 'markSent'])->name('lpo.sent')
             ->middleware('role:store_manager,store_keeper');

        // ── Goods Received Notes ──────────────────────────────────────────
        Route::get('grn', [GoodsReceivedNoteController::class, 'index'])->name('grn.index')
             ->middleware('role:store_manager,store_keeper');
        Route::get('grn/create', [GoodsReceivedNoteController::class, 'create'])->name('grn.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn', [GoodsReceivedNoteController::class, 'store'])->name('grn.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('grn/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'show'])->name('grn.show')
             ->middleware('role:store_manager,store_keeper');
        Route::delete('grn/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'destroy'])->name('grn.destroy')
             ->middleware('role:store_manager');
        Route::post('grn/{goodsReceivedNote}/receipt', [GoodsReceivedNoteController::class, 'uploadReceipt'])->name('grn.upload-receipt')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn/{goodsReceivedNote}/submit', [GoodsReceivedNoteController::class, 'submitForConfirmation'])->name('grn.submit')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn/{goodsReceivedNote}/confirm', [GoodsReceivedNoteController::class, 'confirm'])->name('grn.confirm')
             ->middleware('role:store_manager');
        Route::post('grn/{goodsReceivedNote}/reject', [GoodsReceivedNoteController::class, 'reject'])->name('grn.reject')
             ->middleware('role:store_manager');
    });

    // ═══ NOTIFICATIONS ═══
    Route::get('notifications',                          [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count',             [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::post('notifications/{notification}/read',     [NotificationController::class, 'markRead'])->name('notifications.read');

    // ═══ UNIFIED RECEIPTS ═══
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('search',                    [ReceiptController::class, 'search'])->name('search');
        Route::get('laundry/{laundryOrder}',    [ReceiptController::class, 'laundry'])->name('laundry');
        Route::get('order/{order}',             [ReceiptController::class, 'order'])->name('order');
        Route::get('checkout/{checkout}',       [ReceiptController::class, 'checkout'])->name('checkout');
        Route::get('walkin/{walkinTransaction}',[ReceiptController::class, 'walkin'])->name('walkin');
        Route::get('reprint/{receiptNumber}',   [ReceiptController::class, 'reprint'])->name('reprint');
        Route::post('{uuid}/refresh',           [ReceiptController::class, 'refresh'])->name('refresh');
        Route::post('{uuid}/printed',           [ReceiptController::class, 'markPrinted'])->name('printed');
        Route::get('{uuid}',                    [ReceiptController::class, 'show'])->name('show');
    });

    // ═══ ADMIN — Broadcasts & Offers ═══
    Route::middleware(['role:admin,manager,supervisor,laundry_manager'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('broadcasts',                       [BroadcastController::class, 'index'])->name('broadcasts.index');
        Route::get('broadcasts/create',                [BroadcastController::class, 'create'])->name('broadcasts.create');
        Route::post('broadcasts',                      [BroadcastController::class, 'store'])->name('broadcasts.store');
        Route::post('broadcasts/{broadcast}/send',     [BroadcastController::class, 'send'])->name('broadcasts.send');
    });

    // ═══ ADMIN — Discount Audit ═══
    Route::middleware(['role:admin,manager,supervisor'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('audit/discounts',                  [AuditController::class, 'discounts'])->name('audit.discounts');
        Route::post('bookings/{booking}/discount',     [AuditController::class, 'applyDiscount'])->name('audit.apply-discount');
    });

    // ═══ ADMIN — System Settings ═══
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('settings',                         [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings',                        [SettingsController::class, 'updateSettings'])->name('settings.update');
        Route::post('settings/password',               [SettingsController::class, 'updatePassword'])->name('settings.password');
    });

    // ═══ ACCOUNTING MODULE ═══
    Route::middleware(['role:ACCOUNTANT,STORE_MANAGER'])->prefix('accounting')->name('accounting.')->group(function () {

        // Dashboard
        Route::get('/', fn() => view('accounting.dashboard.index'))->name('dashboard');

        // Chart of Accounts
        Route::get('accounts',                    [ChartOfAccountsController::class, 'index'])->name('accounts.index');
        Route::get('accounts/create',             [ChartOfAccountsController::class, 'create'])->name('accounts.create');
        Route::post('accounts',                   [ChartOfAccountsController::class, 'store'])->name('accounts.store');
        Route::get('accounts/{account}/edit',     [ChartOfAccountsController::class, 'edit'])->name('accounts.edit');
        Route::put('accounts/{account}',          [ChartOfAccountsController::class, 'update'])->name('accounts.update');

        // Journal
        Route::get('journal',                     [JournalEntryController::class, 'index'])->name('journal.index');
        Route::get('journal/create',              [JournalEntryController::class, 'create'])->name('journal.create')
             ->middleware('role:ACCOUNTANT');
        Route::post('journal',                    [JournalEntryController::class, 'store'])->name('journal.store')
             ->middleware('role:ACCOUNTANT');
        Route::get('journal/{journalEntry}',      [JournalEntryController::class, 'show'])->name('journal.show');

        // General Ledger
        Route::get('ledger',                      [AccountingReportController::class, 'ledger'])->name('ledger');

        // Invoices
        Route::get('invoices',                    [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}',          [InvoiceController::class, 'show'])->name('invoices.show');

        // Payroll
        Route::get('payroll',                     [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/create',              [PayrollController::class, 'create'])->name('payroll.create')
             ->middleware('role:ACCOUNTANT');
        Route::post('payroll',                    [PayrollController::class, 'store'])->name('payroll.store')
             ->middleware('role:ACCOUNTANT');
        Route::get('payroll/{payrollRun}',        [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('payroll/{payrollRun}/approve',[PayrollController::class, 'approve'])->name('payroll.approve')
             ->middleware('role:ACCOUNTANT,STORE_MANAGER');

        // Bank Reconciliation
        Route::get('reconciliation',              [BankReconciliationController::class, 'index'])->name('reconciliation.index');
        Route::get('reconciliation/create',       [BankReconciliationController::class, 'create'])->name('reconciliation.create');
        Route::post('reconciliation',             [BankReconciliationController::class, 'store'])->name('reconciliation.store');
        Route::get('reconciliation/{rec}',        [BankReconciliationController::class, 'show'])->name('reconciliation.show');

        // Reports
        Route::get('reports/profit-loss',         [AccountingReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('reports/balance-sheet',       [AccountingReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('reports/trial-balance',       [AccountingReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('reports/vat',                 [AccountingReportController::class, 'vatReport'])->name('reports.vat');
    });
});