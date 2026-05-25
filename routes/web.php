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
use App\Http\Controllers\AzamPesaPaymentController;
use App\Http\Controllers\ConferenceHallController;
use App\Http\Controllers\ConferenceBookingController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventScheduleController;
use App\Http\Controllers\EventTicketController;
use App\Http\Controllers\EventVenueController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\EventReportController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StockController;
use App\Http\Controllers\Store\AdjustmentController;
use App\Http\Controllers\Store\InternalRequestController;
use App\Http\Controllers\Store\StockTransferController;
use App\Http\Controllers\Store\ReportController;
use App\Http\Controllers\Restaurant\MenuItemController;
use App\Http\Controllers\Restaurant\MenuCategoryController;
use App\Http\Controllers\Restaurant\MenuOptionGroupController;
use App\Http\Controllers\Restaurant\BuffetController;
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
use App\Http\Controllers\Accounting\AccountantDashboardController;
use App\Http\Controllers\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\InvoiceController;
use App\Http\Controllers\Accounting\PayrollController;
use App\Http\Controllers\Accounting\BankReconciliationController;
use App\Http\Controllers\Accounting\SupplierPayableController;
use App\Http\Controllers\Accounting\ReceiptManagementController;
use App\Http\Controllers\Manager\OversightController;
use App\Http\Controllers\Finance\PettyCashController;
use App\Http\Controllers\Bartender\BartenderController;
use App\Http\Controllers\BuffetPosController;
use App\Http\Controllers\Reception\DrinkRequestController;
use App\Http\Controllers\CleaningController;

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

// ═══ PAYMENT CALLBACKS (no auth — called by payment provider servers) ═══
Route::post('/payments/callback/azampesa', [AzamPesaPaymentController::class, 'callback'])
    ->name('payments.callback.azampesa')
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

    // Rooms — supervisor & manager: view only, admin: full CRUD
    Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index')
        ->middleware('role:admin,supervisor,manager');
    Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show')
        ->middleware('role:admin,supervisor,manager');
    Route::middleware(['role:admin'])->group(function () {
        Route::get('rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::post('rooms/{room}/toggle-status', [RoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Reservations, Bookings — restricted to authorized roles (NO admin - admin is system only)
    Route::middleware(['role:front_desk,manager'])->group(function () {
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
        Route::get('bookings/current-guests', [BookingController::class, 'currentGuests'])->name('bookings.current-guests');
        Route::post('bookings', [BookingController::class, 'storeFrontdesk'])->name('bookings.store');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
        Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });

    // Guest Management — supervisor: view only, front_desk/manager: full CRUD
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
        Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('guests/create', [GuestController::class, 'create'])->name('guests.create');
        Route::get('guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
        Route::get('guests-search', [GuestController::class, 'search'])->name('guests.search');
    });
    Route::middleware(['role:front_desk,manager'])->group(function () {
        Route::post('guests', [GuestController::class, 'store'])->name('guests.store');
        Route::get('guests/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
        Route::put('guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
        Route::delete('guests/{guest}/media/{media}', [GuestController::class, 'removeMedia'])->name('guests.media.destroy');
    });

    // Reception Drink Requests
    Route::middleware(['role:supervisor,front_desk,manager'])->prefix('drinks')->name('reception.drinks.')->group(function () {
        Route::get('request', [DrinkRequestController::class, 'create'])->name('create');
        Route::post('request', [DrinkRequestController::class, 'store'])->name('store');
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
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager');
        Route::get('orders/create', [NewLaundryOrderController::class, 'create'])
             ->name('orders.create')
             ->middleware('role:house_help,front_desk,laundry_manager,manager');
        Route::post('orders', [NewLaundryOrderController::class, 'store'])
             ->name('orders.store')
             ->middleware('role:house_help,front_desk,laundry_manager,manager');
        Route::get('orders/{laundryOrder}', [NewLaundryOrderController::class, 'show'])
             ->name('orders.show')
             ->middleware('role:house_help,front_desk,supervisor,laundry_manager,manager,ACCOUNTANT');
        Route::post('orders/{laundryOrder}/process', [NewLaundryOrderController::class, 'process'])
             ->name('orders.process')
             ->middleware('role:house_help,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/ready', [NewLaundryOrderController::class, 'markReady'])
             ->name('orders.ready')
             ->middleware('role:house_help,supervisor,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/confirm', [NewLaundryOrderController::class, 'confirm'])
             ->name('orders.confirm')
             ->middleware('role:supervisor');
        Route::post('orders/{laundryOrder}/deliver', [NewLaundryOrderController::class, 'deliver'])
             ->name('orders.deliver')
             ->middleware('role:house_help,front_desk,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/collected', [NewLaundryOrderController::class, 'collected'])
             ->name('orders.collected')
             ->middleware('role:house_help,front_desk,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/settle', [NewLaundryOrderController::class, 'settle'])
             ->name('orders.settle')
             ->middleware('role:front_desk,laundry_manager,manager');
        Route::post('orders/{laundryOrder}/cancel', [NewLaundryOrderController::class, 'cancel'])
             ->name('orders.cancel')
             ->middleware('role:laundry_manager,manager');

        // ── Reports ───────────────────────────────────────────────────────────
        Route::get('reports/daily', [LaundryReportController::class, 'daily'])
             ->name('reports.daily')
             ->middleware('role:laundry_manager,supervisor,manager');
    });

    // ═══ CLEANING WORKFLOW ═══
    // Supervisor & Manager: manage room cleaning queue
    Route::middleware(['role:supervisor,manager'])->prefix('cleaning')->name('cleaning.')->group(function () {
        Route::get('/', [CleaningController::class, 'index'])->name('index');
        Route::post('assign/{room}', [CleaningController::class, 'assign'])->name('assign');
        Route::post('confirm/{room}', [CleaningController::class, 'confirm'])->name('confirm');
    });

    // House Help: view assigned rooms and mark done
    Route::middleware(['role:house_help'])->prefix('cleaning')->name('cleaning.')->group(function () {
        Route::get('my-rooms', [CleaningController::class, 'myRooms'])->name('my-rooms');
        Route::post('mark-done/{room}', [CleaningController::class, 'markDone'])->name('mark-done');
    });

    // Maintenance tracking: view out_of_order rooms and their progress
    Route::middleware(['role:admin,front_desk,supervisor,manager,house_help'])->group(function () {
        Route::get('cleaning/maintenance', [CleaningController::class, 'maintenanceIndex'])->name('cleaning.maintenance');
        Route::post('rooms/{room}/out-of-order', [CleaningController::class, 'markOutOfOrder'])->name('rooms.out-of-order');
    });

    // Booking Charges (NO admin - admin is system only)
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
        Route::get('bookings/{booking}/charges', [BookingChargeController::class, 'index'])->name('booking-charges.index');
        Route::post('booking-charges/{bookingCharge}/mark-paid', [BookingChargeController::class, 'markPaid'])->name('booking-charges.mark-paid');
        Route::post('bookings/{booking}/charges/mark-all-paid', [BookingChargeController::class, 'markAllPaid'])->name('booking-charges.mark-all-paid');
    });

    // ═══ PAYMENTS ═══ (NO admin - admin is system only)
    Route::middleware(['role:supervisor,front_desk,manager'])->group(function () {
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
        // Institutions (organisations that book conference halls)
        Route::get('institutions', [InstitutionController::class, 'index'])->name('institutions.index');
        Route::get('institutions/create', [InstitutionController::class, 'create'])->name('institutions.create');
        Route::post('institutions', [InstitutionController::class, 'store'])->name('institutions.store');
        Route::get('institutions/{institution}', [InstitutionController::class, 'show'])->name('institutions.show');
        Route::get('institutions/{institution}/edit', [InstitutionController::class, 'edit'])->name('institutions.edit');
        Route::put('institutions/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
        Route::delete('institutions/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');

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
        

    });

    // ═══ REVAMPED CONFERENCE MANAGEMENT SYSTEM ═══
    Route::middleware(['role:supervisor,front_desk,manager'])->prefix('organizations')->name('organizations.')->group(function () {
        // Organization CRUD
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/create', [OrganizationController::class, 'create'])->name('create');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::get('/{organization}', [OrganizationController::class, 'show'])->name('show');
        Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
        Route::put('/{organization}', [OrganizationController::class, 'update'])->name('update');
        Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');
        Route::post('/{organization}/verify', [OrganizationController::class, 'verify'])->name('verify');
        Route::get('/{organization}/events-list', [OrganizationController::class, 'events'])->name('events-list');

        // Events (nested under organization)
        Route::prefix('{organization}/events')->name('events.')->group(function () {
            Route::get('/', [EventController::class, 'index'])->name('index');
            Route::get('/create', [EventController::class, 'create'])->name('create');
            Route::post('/', [EventController::class, 'store'])->name('store');
            Route::get('/{event}', [EventController::class, 'show'])->name('show');
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
            Route::put('/{event}', [EventController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');

            // Event state transitions
            Route::post('/{event}/publish', [EventController::class, 'publish'])->name('publish');
            Route::post('/{event}/start', [EventController::class, 'start'])->name('start');
            Route::post('/{event}/complete', [EventController::class, 'complete'])->name('complete');
            Route::post('/{event}/cancel', [EventController::class, 'cancel'])->name('cancel');
            Route::post('/{event}/duplicate', [EventController::class, 'duplicate'])->name('duplicate');

            // Event Schedules
            Route::prefix('{event}/schedules')->name('schedules.')->group(function () {
                Route::get('/', [EventScheduleController::class, 'index'])->name('index');
                Route::get('/create', [EventScheduleController::class, 'create'])->name('create');
                Route::post('/', [EventScheduleController::class, 'store'])->name('store');
                Route::get('/{schedule}', [EventScheduleController::class, 'show'])->name('show');
                Route::get('/{schedule}/edit', [EventScheduleController::class, 'edit'])->name('edit');
                Route::put('/{schedule}', [EventScheduleController::class, 'update'])->name('update');
                Route::delete('/{schedule}', [EventScheduleController::class, 'destroy'])->name('destroy');
            });

            // Event Tickets
            Route::prefix('{event}/tickets')->name('tickets.')->group(function () {
                Route::get('/', [EventTicketController::class, 'index'])->name('index');
                Route::get('/create', [EventTicketController::class, 'create'])->name('create');
                Route::post('/', [EventTicketController::class, 'store'])->name('store');
                Route::get('/{ticket}', [EventTicketController::class, 'show'])->name('show');
                Route::get('/{ticket}/edit', [EventTicketController::class, 'edit'])->name('edit');
                Route::put('/{ticket}', [EventTicketController::class, 'update'])->name('update');
                Route::delete('/{ticket}', [EventTicketController::class, 'destroy'])->name('destroy');
                Route::post('/{ticket}/put-on-sale', [EventTicketController::class, 'putOnSale'])->name('put-on-sale');
                Route::post('/{ticket}/archive', [EventTicketController::class, 'archive'])->name('archive');
            });

            // Event Venues
            Route::prefix('{event}/venues')->name('venues.')->group(function () {
                Route::get('/', [EventVenueController::class, 'index'])->name('index');
                Route::get('/create', [EventVenueController::class, 'create'])->name('create');
                Route::post('/', [EventVenueController::class, 'store'])->name('store');
                Route::get('/{venue}', [EventVenueController::class, 'show'])->name('show');
                Route::get('/{venue}/edit', [EventVenueController::class, 'edit'])->name('edit');
                Route::put('/{venue}', [EventVenueController::class, 'update'])->name('update');
                Route::delete('/{venue}', [EventVenueController::class, 'destroy'])->name('destroy');
            });

            // Attendances
            Route::prefix('{event}/attendances')->name('attendances.')->group(function () {
                Route::get('/', [AttendanceController::class, 'index'])->name('index');
                Route::get('/create', [AttendanceController::class, 'create'])->name('create');
                Route::post('/', [AttendanceController::class, 'store'])->name('store');
                Route::get('/{attendance}', [AttendanceController::class, 'show'])->name('show');
                Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
                Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
                Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
                Route::get('/{attendance}/ticket-pdf', [AttendanceController::class, 'ticketPdf'])->name('ticket-pdf');
                Route::post('/{attendance}/link-guest', [AttendanceController::class, 'linkGuest'])->name('link-guest');
                Route::post('/{attendance}/mark-no-show', [AttendanceController::class, 'markNoShow'])->name('mark-no-show');
                Route::post('/{attendance}/confirm', [AttendanceController::class, 'confirm'])->name('confirm');
                Route::get('/bulk-upload', [AttendanceController::class, 'bulkUpload'])->name('bulk-upload');
                Route::post('/bulk-upload', [AttendanceController::class, 'processBulkUpload'])->name('process-bulk-upload');
                Route::post('/print-badges', [AttendanceController::class, 'printBadges'])->name('print-badges');
            });

            // Check-in
            Route::prefix('{event}/check-in')->name('check-in.')->group(function () {
                Route::get('/dashboard', [CheckInController::class, 'dashboard'])->name('dashboard');
                Route::get('/scanner', [CheckInController::class, 'scanner'])->name('scanner');
                Route::post('/process', [CheckInController::class, 'process'])->name('process');
                Route::post('/manual', [CheckInController::class, 'manualEntry'])->name('manual');
                Route::post('/staff-override', [CheckInController::class, 'staffOverride'])->name('staff-override');
            });

            // Reports
            Route::prefix('{event}/reports')->name('reports.')->group(function () {
                Route::get('/pre-event', [EventReportController::class, 'preEvent'])->name('pre-event');
                Route::get('/live', [EventReportController::class, 'live'])->name('live');
                Route::get('/post-event', [EventReportController::class, 'postEvent'])->name('post-event');
                Route::get('/export', [EventReportController::class, 'export'])->name('export');
            });
        });
    });

    // Mobile scanner API routes (for check-in app - requires web auth)
    Route::post('/api/check-in', [CheckInController::class, 'apiProcess']);
    Route::post('/api/check-in/manual', [CheckInController::class, 'apiManual']);

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
        Route::get('products',                  [ProductController::class, 'index'])->name('products.index')
             ->middleware('role:store_manager,store_keeper,restaurant_manager,supervisor,manager');
        Route::get('products/create',           [ProductController::class, 'create'])->name('products.create')
             ->middleware('role:store_manager');
        Route::post('products',                 [ProductController::class, 'store'])->name('products.store')
             ->middleware('role:store_manager');
        Route::get('products/{product}',        [ProductController::class, 'show'])->name('products.show')
             ->middleware('role:store_manager,store_keeper,restaurant_manager,supervisor,manager');
        Route::get('products/{product}/edit',   [ProductController::class, 'edit'])->name('products.edit')
             ->middleware('role:store_manager');
        Route::put('products/{product}',        [ProductController::class, 'update'])->name('products.update')
             ->middleware('role:store_manager');
        Route::delete('products/{product}',     [ProductController::class, 'destroy'])->name('products.destroy')
             ->middleware('role:store_manager');

        // ── Stock ─────────────────────────────────────────────────────────
        Route::get('stock/levels',              [StockController::class, 'levels'])->name('stock.levels')
             ->middleware('role:store_manager,store_keeper,restaurant_manager');
        Route::get('stock/restock',             [StockController::class, 'restockForm'])->name('stock.restock-form')
             ->middleware('role:store_keeper');
        Route::post('stock/restock',            [StockController::class, 'restock'])->name('stock.restock')
             ->middleware('role:store_keeper');
        Route::get('stock/damage',              [StockController::class, 'damageForm'])->name('stock.damage-form')
             ->middleware('role:store_keeper,restaurant_manager');
        Route::post('stock/damage',             [StockController::class, 'damage'])->name('stock.damage')
             ->middleware('role:store_keeper,restaurant_manager');

        // ── Adjustments ───────────────────────────────────────────────────
        Route::get('adjustments',              [AdjustmentController::class, 'index'])->name('adjustments.index')
             ->middleware('role:manager,supervisor,restaurant_manager');
        Route::get('adjustments/create',       [AdjustmentController::class, 'create'])->name('adjustments.create')
             ->middleware('role:supervisor,restaurant_manager');
        Route::post('adjustments',             [AdjustmentController::class, 'store'])->name('adjustments.store')
             ->middleware('role:supervisor,restaurant_manager');
        Route::post('adjustments/{adjustment}/approve', [AdjustmentController::class, 'approve'])->name('adjustments.approve')
             ->middleware('role:manager');
        Route::post('adjustments/{adjustment}/reject',  [AdjustmentController::class, 'reject'])->name('adjustments.reject')
             ->middleware('role:manager');

        // ── Internal Requests ─────────────────────────────────────────────
        Route::get('internal-requests',                [InternalRequestController::class, 'index'])->name('internal-requests.index')
             ->middleware('role:house_help,supervisor,store_keeper,store_manager');
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
        Route::post('internal-requests/{internalUsageRequest}/cancel',  [InternalRequestController::class, 'cancel'])->name('internal-requests.cancel')
             ->middleware('role:house_help,supervisor');

        // ── Stock Transfers ───────────────────────────────────────────────
        Route::get('transfers',                [StockTransferController::class, 'index'])->name('transfers.index')
             ->middleware('role:store_manager,store_keeper,manager,restaurant_manager');
        Route::get('transfers/create',         [StockTransferController::class, 'create'])->name('transfers.create')
             ->middleware('role:store_keeper');
        Route::post('transfers',               [StockTransferController::class, 'store'])->name('transfers.store')
             ->middleware('role:store_keeper');
        Route::post('transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('transfers.approve')
             ->middleware('role:store_manager');
        Route::post('transfers/{stockTransfer}/fulfill', [StockTransferController::class, 'fulfill'])->name('transfers.fulfill')
             ->middleware('role:store_keeper');
        Route::post('transfers/{stockTransfer}/reject',  [StockTransferController::class, 'reject'])->name('transfers.reject')
             ->middleware('role:store_manager');

        // ── Reports ───────────────────────────────────────────────────────
        Route::get('reports/stock-snapshot',   [ReportController::class, 'stockSnapshot'])->name('reports.stock-snapshot')
             ->middleware('role:store_manager,store_keeper,restaurant_manager');
        Route::get('reports/movements',        [ReportController::class, 'movements'])->name('reports.movements')
             ->middleware('role:store_manager,store_keeper');
        Route::get('reports/damage',           [ReportController::class, 'damage'])->name('reports.damage')
             ->middleware('role:store_manager,store_keeper,supervisor');
    });

    // ═══ BAR & RESTAURANT MODULE ═══
    Route::prefix('restaurant')->name('restaurant.')->middleware('role:restaurant_manager,manager,admin,waiter')->group(function () {

        // ── Menu (CRUD: restaurant_manager only; index: any authenticated) ──
        Route::get('menu',                   [MenuItemController::class, 'index'])->name('menu.index');
        Route::get('menu/create',            [MenuItemController::class, 'create'])->name('menu.create')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::post('menu',                  [MenuItemController::class, 'store'])->name('menu.store')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::get('menu/{menuItem}/edit',   [MenuItemController::class, 'edit'])->name('menu.edit')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::put('menu/{menuItem}',        [MenuItemController::class, 'update'])->name('menu.update')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::delete('menu/{menuItem}',     [MenuItemController::class, 'destroy'])->name('menu.destroy')
             ->middleware('role:restaurant_manager,manager,admin');

        // Sync store beverages/products as menu items
        Route::post('menu/sync-beverages', [MenuItemController::class, 'syncBeverages'])->name('menu.sync-beverages')
             ->middleware('role:restaurant_manager,manager,admin');

        Route::get('menu/categories', [MenuCategoryController::class, 'index'])->name('menu.categories.index')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::post('menu/categories', [MenuCategoryController::class, 'store'])->name('menu.categories.store')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::put('menu/categories/{menuCategory}', [MenuCategoryController::class, 'update'])->name('menu.categories.update')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::delete('menu/categories/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('menu.categories.destroy')
            ->middleware('role:restaurant_manager,manager,admin');

        Route::get('menu/options', [MenuOptionGroupController::class, 'index'])->name('menu.options.index')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::post('menu/options', [MenuOptionGroupController::class, 'store'])->name('menu.options.store')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::put('menu/options/{menuOptionGroup}', [MenuOptionGroupController::class, 'update'])->name('menu.options.update')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::delete('menu/options/{menuOptionGroup}', [MenuOptionGroupController::class, 'destroy'])->name('menu.options.destroy')
            ->middleware('role:restaurant_manager,manager,admin');

        // ── Buffet ────────────────────────────────────────────────────────
        Route::get('buffet/packages', [BuffetController::class, 'packages'])->name('buffet.packages')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::post('buffet/packages', [BuffetController::class, 'storePackage'])->name('buffet.packages.store')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::put('buffet/packages/{buffetPackage}', [BuffetController::class, 'updatePackage'])->name('buffet.packages.update')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::delete('buffet/packages/{buffetPackage}', [BuffetController::class, 'deactivatePackage'])->name('buffet.packages.deactivate')
            ->middleware('role:restaurant_manager,manager,admin');

        Route::get('buffet/sales', [BuffetController::class, 'index'])->name('buffet.index');
        Route::get('buffet/sales/create', [BuffetController::class, 'create'])->name('buffet.create');
        Route::post('buffet/sales', [BuffetController::class, 'store'])->name('buffet.store');
        Route::get('buffet/sales/{buffetSale}', [BuffetController::class, 'show'])->name('buffet.show');
        Route::post('buffet/sales/{buffetSale}/charge-booking', [BuffetController::class, 'chargeToBooking'])->name('buffet.charge-booking')
            ->middleware('role:restaurant_manager,manager,admin');
        Route::post('buffet/sales/{buffetSale}/settle-walkin', [BuffetController::class, 'settleWalkin'])->name('buffet.settle-walkin')
            ->middleware('role:restaurant_manager,manager,admin');

        // ── Tables ────────────────────────────────────────────────────────
        Route::get('tables',                 [TableController::class, 'index'])->name('tables.index');
        Route::post('tables',                [TableController::class, 'store'])->name('tables.store')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::post('tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus');

        // ── Orders ────────────────────────────────────────────────────────
        Route::get('orders',                       [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create',                [OrderController::class, 'create'])->name('orders.create')
             ->middleware('role:waiter,restaurant_manager,manager');
        Route::post('orders',                      [OrderController::class, 'store'])->name('orders.store')
             ->middleware('role:waiter,restaurant_manager,manager');
        Route::get('orders/{order}',               [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/send',         [OrderController::class, 'send'])->name('orders.send');
        Route::post('orders/{order}/ready',        [OrderController::class, 'ready'])->name('orders.ready');
        Route::post('orders/{order}/serve',        [OrderController::class, 'serve'])->name('orders.serve');
        Route::post('orders/{order}/settle',       [OrderController::class, 'settle'])->name('orders.settle')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::post('orders/{order}/cancel',       [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/items',        [OrderController::class, 'addItem'])->name('orders.addItem');
        Route::delete('orders/{order}/items/{orderItem}', [OrderController::class, 'removeItem'])->name('orders.removeItem');

        // ── POS (Restaurant walk-in and guest folio sales) ─────────────────
        Route::get('pos',                          [OrderController::class, 'pos'])->name('pos')
             ->middleware('role:waiter,restaurant_manager,manager');
        Route::post('pos',                         [OrderController::class, 'storePos'])->name('pos.store')
             ->middleware('role:waiter,restaurant_manager,manager');

        // ── Kitchen Queue (kitchen_staff, restaurant_manager, manager, admin, supervisor) ──
        Route::get('kitchen/queue',                    [\App\Http\Controllers\Restaurant\KitchenController::class, 'queue'])->name('kitchen.queue')
             ->middleware('role:restaurant_manager,manager,admin,supervisor');
        Route::post('kitchen/tickets/{ticket}/preparing', [\App\Http\Controllers\Restaurant\KitchenController::class, 'markPreparing'])->name('kitchen.preparing')
             ->middleware('role:restaurant_manager,manager,admin,supervisor');
        Route::post('kitchen/tickets/{ticket}/ready',     [\App\Http\Controllers\Restaurant\KitchenController::class, 'markReady'])->name('kitchen.ready')
             ->middleware('role:restaurant_manager,manager,admin,supervisor');

        // ── Bar Queue & Tabs (bar_tender, restaurant_manager, manager, admin, supervisor) ──
        Route::get('bar/queue',                        [\App\Http\Controllers\Restaurant\BarController::class, 'queue'])->name('bar.queue')
             ->middleware('role:bar_tender,restaurant_manager,manager,admin,supervisor');
        Route::get('bar/tabs',                         [\App\Http\Controllers\Restaurant\BarController::class, 'tabs'])->name('bar.tabs')
             ->middleware('role:bar_tender,restaurant_manager,manager,admin,supervisor');
        Route::post('bar/tickets/{ticket}/preparing',  [\App\Http\Controllers\Restaurant\BarController::class, 'markPreparing'])->name('bar.preparing')
             ->middleware('role:bar_tender,restaurant_manager,manager,admin,supervisor');
        Route::post('bar/tickets/{ticket}/ready',      [\App\Http\Controllers\Restaurant\BarController::class, 'markReady'])->name('bar.ready')
             ->middleware('role:bar_tender,restaurant_manager,manager,admin,supervisor');

        // ── Menu Item Options API ─────────────────────────────────────────
        Route::get('menu-items/{menuItem}/options',    [\App\Http\Controllers\Restaurant\MenuItemController::class, 'options'])->name('menu.items.options');

        // ── Reports (restaurant_manager / admin only) ─────────────────────
        Route::get('reports/daily-sales',    [RestaurantReportController::class, 'dailySales'])->name('reports.dailySales')
             ->middleware('role:restaurant_manager,manager,admin');
        Route::get('reports/popular-items',  [RestaurantReportController::class, 'popularItems'])->name('reports.popularItems')
             ->middleware('role:restaurant_manager,manager,admin');
    });

    // ═══ BUFFET POS MODULE ═══
    Route::middleware(['role:waiter,restaurant_manager,manager,admin,supervisor'])
        ->prefix('buffet')
        ->name('buffet.pos.')
        ->group(function () {
            Route::get('/', [BuffetPosController::class, 'index'])->name('index');
            Route::post('/', [BuffetPosController::class, 'store'])->name('store');
        });

    // ═══ BARTENDER MODULE ═══
    // Bar stock is also viewable by restaurant_manager (bar products only)
    Route::get('bartender/stock', [BartenderController::class, 'stock'])
        ->middleware('role:bar_tender,manager,admin,restaurant_manager')
        ->name('bartender.stock');

    Route::prefix('bartender')->name('bartender.')->middleware('role:bar_tender,manager,admin')->group(function () {
        Route::get('/', [BartenderController::class, 'dashboard'])->name('dashboard');

        // ═══ BAR POS ═══
        Route::get('pos', [BartenderController::class, 'pos'])->name('pos');
        Route::post('pos', [BartenderController::class, 'storePos'])->name('pos.store');

        Route::get('orders', [BartenderController::class, 'inbox'])->name('inbox');
        Route::get('orders/{order}', [BartenderController::class, 'showOrder'])->name('orders.show');
        Route::post('orders/{order}/accept', [BartenderController::class, 'acceptOrder'])->name('orders.accept');
        Route::post('orders/{order}/prepare', [BartenderController::class, 'prepareOrder'])->name('orders.prepare');
        Route::post('orders/{order}/serve', [BartenderController::class, 'serveOrder'])->name('orders.serve');
        Route::post('orders/{order}/reject', [BartenderController::class, 'rejectOrder'])->name('orders.reject');
        Route::post('orders/{order}/cancel', [BartenderController::class, 'cancelOrder'])->name('orders.cancel');

        Route::get('walkin-sales', [BartenderController::class, 'walkinSalesReport'])->name('walkin-sales');

        Route::get('damage', [BartenderController::class, 'damageIndex'])->name('damage.index');
        Route::get('damage/create', [BartenderController::class, 'damageForm'])->name('damage.create');
        Route::post('damage', [BartenderController::class, 'reportDamage'])->name('damage.store');

        Route::get('drink-inbox', [BartenderController::class, 'drinkInbox'])->name('drink-inbox');
    });

    // ═══ FINANCE MODULE ═══
    Route::prefix('finance')->name('finance.')->group(function () {

        // ── Dashboard ─────────────────────────────────────────────────────────────
        Route::get('dashboard', [FinancialDashboardController::class, 'index'])->name('dashboard')
             ->middleware('role:store_manager,front_desk,manager');

        // ── Checkout ──────────────────────────────────────────────────────────────
        Route::get('checkout/{booking}',              [FinanceCheckoutController::class, 'show'])->name('checkout.show')
             ->middleware('role:front_desk,manager,ACCOUNTANT');
        Route::post('checkout/{checkout}/process',    [FinanceCheckoutController::class, 'process'])->name('checkout.process')
             ->middleware('role:front_desk,manager');
        Route::post('checkout/{checkout}/add-charge', [FinanceCheckoutController::class, 'addCharge'])->name('checkout.add-charge')
             ->middleware('role:front_desk,manager');
        Route::post('checkout/{checkout}/draft',     [FinanceCheckoutController::class, 'saveDraft'])->name('checkout.draft')
             ->middleware('role:front_desk,manager');

        // ── Walk-in Payments ──────────────────────────────────────────────────────
        Route::get('payments',         [FinancePaymentController::class, 'index'])->name('payments.index')
             ->middleware('role:store_manager,ACCOUNTANT,manager');
        Route::post('payments/walkin', [FinancePaymentController::class, 'storeWalkin'])->name('payments.walkin')
             ->middleware('role:bar_tender,restaurant_manager');
        
        // ── Walk-in Payment Processing (unified for laundry/restaurant/bar) ──────
        Route::post('walkin-payment/process', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'process'])
             ->name('walkin-payment.process')
             ->middleware('role:front_desk,laundry_manager,supervisor,bar_tender,restaurant_manager,manager,admin');
        Route::get('walkin-payment/status/{reference}', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'status'])
             ->name('walkin-payment.status')
             ->middleware('role:front_desk,laundry_manager,supervisor,bar_tender,restaurant_manager,manager,admin');
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

        // ── Refunds ─────────────────────────────────────────────────────────
        Route::get('refunds', [\App\Http\Controllers\Finance\RefundController::class, 'index'])->name('refunds.index')
             ->middleware('role:manager,store_manager');
        Route::get('refunds/payment/{payment}', [\App\Http\Controllers\Finance\RefundController::class, 'showPayment'])->name('refunds.payment')
             ->middleware('role:manager,store_manager');
        Route::post('refunds/payment/{payment}', [\App\Http\Controllers\Finance\RefundController::class, 'processPaymentRefund'])->name('refunds.payment.process')
             ->middleware('role:manager');
        Route::get('refunds/walkin/{transaction}', [\App\Http\Controllers\Finance\RefundController::class, 'showWalkin'])->name('refunds.walkin')
             ->middleware('role:manager,store_manager');
        Route::post('refunds/walkin/{transaction}', [\App\Http\Controllers\Finance\RefundController::class, 'processWalkinRefund'])->name('refunds.walkin.process')
             ->middleware('role:manager');
        // API endpoints for validation
        Route::get('refunds/payment/{payment}/validate', [\App\Http\Controllers\Finance\RefundController::class, 'validatePaymentRefund'])->name('refunds.payment.validate')
             ->middleware('role:manager,store_manager');
        Route::get('refunds/walkin/{transaction}/validate', [\App\Http\Controllers\Finance\RefundController::class, 'validateWalkinRefund'])->name('refunds.walkin.validate')
             ->middleware('role:manager,store_manager');
    });

    // ═══ PROCUREMENT MODULE ═══ (Store Manager exclusive - per Task 8 requirements)
    Route::prefix('procurement')->name('procurement.')->group(function () {

        // Dashboard
        Route::get('/', [ProcurementDashboardController::class, 'index'])->name('dashboard')
             ->middleware('role:store_manager,store_keeper,manager');

        // ── Suppliers ─────────────────────────────────────────────────────
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')
             ->middleware('role:store_manager,store_keeper');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')
             ->middleware('role:store_manager,store_keeper');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')
             ->middleware('role:store_manager');

        // ── Local Purchase Orders ─────────────────────────────────────────
        Route::get('lpo', [LocalPurchaseOrderController::class, 'index'])->name('lpo.index')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('lpo/create', [LocalPurchaseOrderController::class, 'create'])->name('lpo.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('lpo', [LocalPurchaseOrderController::class, 'store'])->name('lpo.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'show'])->name('lpo.show')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('lpo/{localPurchaseOrder}/print', [LocalPurchaseOrderController::class, 'print'])->name('lpo.print')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('lpo/{localPurchaseOrder}/edit', [LocalPurchaseOrderController::class, 'edit'])->name('lpo.edit')
             ->middleware('role:store_manager,store_keeper');
        Route::put('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'update'])->name('lpo.update')
             ->middleware('role:store_manager,store_keeper');
        Route::delete('lpo/{localPurchaseOrder}', [LocalPurchaseOrderController::class, 'destroy'])->name('lpo.destroy')
             ->middleware('role:store_manager');
        Route::post('lpo/{localPurchaseOrder}/submit', [LocalPurchaseOrderController::class, 'submitForApproval'])->name('lpo.submit')
             ->middleware('role:store_manager,store_keeper');
        Route::post('lpo/{localPurchaseOrder}/approve', [LocalPurchaseOrderController::class, 'approve'])->name('lpo.approve')
             ->middleware('role:manager');
        Route::post('lpo/{localPurchaseOrder}/reject', [LocalPurchaseOrderController::class, 'reject'])->name('lpo.reject')
             ->middleware('role:manager');
        Route::post('lpo/{localPurchaseOrder}/sent', [LocalPurchaseOrderController::class, 'markSent'])->name('lpo.sent')
             ->middleware('role:store_manager,store_keeper');

        // ── Goods Received Notes ──────────────────────────────────────────
        Route::get('grn', [GoodsReceivedNoteController::class, 'index'])->name('grn.index')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('grn/create', [GoodsReceivedNoteController::class, 'create'])->name('grn.create')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn', [GoodsReceivedNoteController::class, 'store'])->name('grn.store')
             ->middleware('role:store_manager,store_keeper');
        Route::get('grn/{goodsReceivedNote}/edit', [GoodsReceivedNoteController::class, 'edit'])->name('grn.edit')
             ->middleware('role:store_manager,store_keeper');
        Route::put('grn/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'update'])->name('grn.update')
             ->middleware('role:store_manager,store_keeper');
        Route::get('grn/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'show'])->name('grn.show')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::get('grn/{goodsReceivedNote}/print', [GoodsReceivedNoteController::class, 'print'])->name('grn.print')
             ->middleware('role:store_manager,store_keeper,manager');
        Route::delete('grn/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'destroy'])->name('grn.destroy')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn/{goodsReceivedNote}/receipt', [GoodsReceivedNoteController::class, 'uploadReceipt'])->name('grn.upload-receipt')
             ->middleware('role:store_manager');
        Route::post('grn/{goodsReceivedNote}/submit', [GoodsReceivedNoteController::class, 'submitForConfirmation'])->name('grn.submit')
             ->middleware('role:store_manager,store_keeper');
        Route::post('grn/{goodsReceivedNote}/confirm', [GoodsReceivedNoteController::class, 'confirm'])->name('grn.confirm')
             ->middleware('role:manager');
        Route::post('grn/{goodsReceivedNote}/approve', [GoodsReceivedNoteController::class, 'approve'])->name('grn.approve')
             ->middleware('role:manager');
        Route::post('grn/{goodsReceivedNote}/reject', [GoodsReceivedNoteController::class, 'reject'])->name('grn.reject')
             ->middleware('role:manager');
    });

    Route::middleware(['role:manager,restaurant_manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('procurement/approvals', [OversightController::class, 'lpoApprovals'])->name('procurement.approvals');
          Route::get('procurement/grn-approvals', [OversightController::class, 'grnApprovals'])->name('procurement.grn-approvals');
        Route::get('stock/overview', [OversightController::class, 'stockOverview'])->name('stock.overview');
        Route::get('stock/movements', [OversightController::class, 'stockMovements'])->name('stock.movements');
        Route::get('accounting/journal/{journalEntry}', [JournalEntryController::class, 'show'])->name('accounting.journal.show');
        Route::post('accounting/journal/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('accounting.journal.post');
        Route::post('accounting/journal/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('accounting.journal.reverse');
        Route::get('accounting/reports/supplier-payables', [AccountingReportController::class, 'supplierPayables'])->name('accounting.reports.supplier-payables');
        
        // Kitchen Stock Management
        Route::prefix('kitchen-stock')->name('kitchen-stock.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'store'])->name('store');
            Route::get('/{item}', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'edit'])->name('edit');
            Route::put('/{item}', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'update'])->name('update');
            Route::delete('/{item}', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'destroy'])->name('destroy');
            Route::post('/{item}/movement', [\App\Http\Controllers\Restaurant\KitchenStockController::class, 'recordMovement'])->name('record-movement');
        });
    });

    // ═══ NOTIFICATIONS ═══
     Route::get('notifications',                          [NotificationController::class, 'index'])->name('notifications.index');
     Route::get('notifications/unread-count',             [NotificationController::class, 'unreadCount'])->name('notifications.count');
      Route::match(['get', 'post'], 'notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
     Route::post('notifications/mark-all-read',          [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

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
        // Procurement receipts
        Route::get('procurement/lpo/{lpo}',             [ReceiptController::class, 'lpo'])->name('procurement.lpo');
        Route::get('procurement/grn/{grn}',             [ReceiptController::class, 'grn'])->name('procurement.grn');
        Route::get('procurement/payment/{payment}',     [ReceiptController::class, 'supplierPayment'])->name('procurement.payment');
        // Store receipts
        Route::get('store/adjustment/{adjustment}',     [ReceiptController::class, 'stockAdjustment'])->name('store.adjustment');
        Route::get('store/transfer/{transfer}',         [ReceiptController::class, 'stockTransfer'])->name('store.transfer');
        Route::get('store/internal-request/{internalRequest}', [ReceiptController::class, 'internalRequest'])->name('store.internal-request');
        Route::get('{uuid}',                    [ReceiptController::class, 'show'])->name('show');
    });

    // ═══ ADMIN — Broadcasts & Offers ═══
     Route::middleware(['role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
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
                    Route::post('settings',                        [SettingsController::class, 'updateSettings'])->name('settings.update')->middleware('throttle:10,1');
                         Route::post('settings/sms',                    [SettingsController::class, 'updateSmsSettings'])->name('settings.sms')->middleware('throttle:5,1');
                         Route::post('settings/email',                  [SettingsController::class, 'updateEmailSettings'])->name('settings.email')->middleware('throttle:5,1');
                         Route::post('settings/azampesa',               [SettingsController::class, 'updateAzamPesaSettings'])->name('settings.azampesa')->middleware('throttle:5,1');
        Route::post('settings/password',               [SettingsController::class, 'updatePassword'])->name('settings.password');
    });

    Route::middleware(['role:ACCOUNTANT,manager'])->prefix('accountant')->name('accountant.')->group(function () {
        Route::get('dashboard', [AccountantDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('financial-overview', [AccountantDashboardController::class, 'overview'])->name('overview');
        Route::get('transactions', [AccountantDashboardController::class, 'transactions'])->name('transactions');
        Route::get('journal-entries', [JournalEntryController::class, 'index'])->name('journal.index');
        Route::get('accounts-payable', [SupplierPayableController::class, 'dashboard'])->name('payables.dashboard');
        Route::get('accounts-payable/list', [SupplierPayableController::class, 'index'])->name('payables.index');
        Route::get('accounts-payable/{supplierPayable}', [SupplierPayableController::class, 'show'])->name('payables.show');
        Route::get('supplier-payments/create', [SupplierPayableController::class, 'createPayment'])->name('payments.create');
        Route::post('supplier-payments', [SupplierPayableController::class, 'storePayment'])->name('payments.store');
        Route::get('supplier-payments/{supplierPayment}/apply', [SupplierPayableController::class, 'applyPayment'])->name('payments.apply');
        Route::post('supplier-payments/{supplierPayment}/apply', [SupplierPayableController::class, 'allocatePayment'])->name('payments.allocate');
        Route::post('supplier-payments/{supplierPayment}/post', [SupplierPayableController::class, 'postPayment'])->name('payments.post');
        Route::post('supplier-payments/{supplierPayment}/cancel', [SupplierPayableController::class, 'cancelPayment'])->name('payments.cancel');
        Route::delete('supplier-payments/{supplierPayment}', [SupplierPayableController::class, 'destroyPayment'])->name('payments.destroy');
        Route::get('accounts-receivable', [AccountantDashboardController::class, 'accountsReceivable'])->name('accounts-receivable');
        Route::get('expenses', [AccountantDashboardController::class, 'expenses'])->name('expenses');
        Route::get('reports', [AccountantDashboardController::class, 'reports'])->name('reports');
        Route::get('audit-logs', [AccountantDashboardController::class, 'auditLogs'])->name('audit-logs');
    });

    Route::middleware(['role:ACCOUNTANT,manager'])->prefix('accountant')->name('accountant.')->group(function () {
        Route::get('receipts', [ReceiptManagementController::class, 'index'])->name('receipts.index');
        Route::get('receipts/{receipt}', [ReceiptManagementController::class, 'show'])->name('receipts.show');
        Route::get('source/restaurant-order/{order}', [OrderController::class, 'show'])->name('source.restaurant-order');
        Route::get('source/walkin-payment/{reference}', [\App\Http\Controllers\Finance\WalkinPaymentController::class, 'status'])->name('source.walkin-payment.status');
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
        Route::get('journal/{journalEntry}/edit', [JournalEntryController::class, 'edit'])->name('journal.edit')
             ->middleware('role:ACCOUNTANT');
        Route::post('journal',                    [JournalEntryController::class, 'store'])->name('journal.store')
             ->middleware('role:ACCOUNTANT');
        Route::put('journal/{journalEntry}',      [JournalEntryController::class, 'update'])->name('journal.update')
             ->middleware('role:ACCOUNTANT');
        Route::post('journal/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal.post')
             ->middleware('role:ACCOUNTANT');
        Route::post('journal/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal.reverse')
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
        Route::get('reports/cashflow-summary',    [AccountingReportController::class, 'cashflowSummary'])->name('reports.cashflow-summary');
        Route::get('reports/ap-aging',            [AccountingReportController::class, 'apAging'])->name('reports.ap-aging');
        Route::get('reports/receipts-summary',    [AccountingReportController::class, 'receiptsSummary'])->name('reports.receipts-summary');
        Route::get('reports/trial-balance',       [AccountingReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('reports/vat',                 [AccountingReportController::class, 'vatReport'])->name('reports.vat');
        Route::get('reports/supplier-payables',   [AccountingReportController::class, 'supplierPayables'])->name('reports.supplier-payables');
    });
});
