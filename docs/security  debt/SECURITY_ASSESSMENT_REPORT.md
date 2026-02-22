# Security Assessment Report

**Project:** Hotel Management System (Laravel 12)
**Assessment Date:** February 19, 2026
**Auditor:** Independent Security Audit
**Scope:** Full codebase static analysis — authentication, authorization, input handling, business logic, payment integration, configuration, dependencies

---

## Executive Summary

| Metric | Value |
|---|---|
| **Overall Risk Posture** | **HIGH** |
| **Critical Findings** | 4 |
| **High Findings** | 7 |
| **Medium Findings** | 9 |
| **Low Findings** | 6 |
| **Total Vulnerabilities** | 26 |

### Immediate Remediation Priorities
1. **Open registration grants staff-level access** — any anonymous user can register as `front_desk` staff and access all booking/guest/payment data.
2. **APP_KEY committed to `.env` in repository** — full session/cookie forgery risk.
3. **No authorization on booking/reservation CRUD** — any authenticated user can view, edit, or delete any booking/reservation regardless of role.
4. **Webhook signature bypass when secret is empty** — payment status can be forged by any attacker.

---

## Methodology

This assessment was performed via manual static code review of the entire Laravel application source, including:

- Route definitions (`routes/web.php`)
- All 24 controllers in `app/Http/Controllers/`
- All 20 Eloquent models in `app/Models/`
- Middleware, observers, service classes, and traits
- Configuration files (`config/`, `.env`, `.env.example`)
- `composer.json` / `composer.lock` dependency analysis
- Session, auth, and payment configuration
- Blade views were not directly reviewed but controller output escaping patterns were assessed

Analysis techniques: threat modeling (STRIDE), OWASP Top 10 2021 mapping, attacker-mindset scenario chaining, and dependency CVE cross-reference.

---

## Vulnerability Findings

---

### VULN-001 — Open Registration Grants Staff Access (Privilege Escalation)

- **Severity:** Critical
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/Auth/RegisterController.php](app/Http/Controllers/Auth/RegisterController.php#L24-L38) — `register()` method
- **Description:** The public registration endpoint (`POST /register`) creates a new user with the `front_desk` role by default. There is no administrator approval, email verification, or invite-only flow. Any anonymous internet user can create an account and immediately access the authenticated dashboard, bookings, reservations, guest PII, payment data, and laundry operations.
- **Impact:** Complete unauthorized access to the hotel management system. An attacker gains front-desk level access to all guest personal data (names, emails, phones, ID numbers, nationality, passports), financial records, booking details, and can perform check-ins/check-outs, create reservations, and process payments.
- **Proof of Concept:**
```bash
curl -X POST https://target.com/register \
  -d "name=Evil+Attacker" \
  -d "email=evil@attacker.com" \
  -d "password=Password123!" \
  -d "password_confirmation=Password123!" \
  -H "Content-Type: application/x-www-form-urlencoded"
# User is now logged in with front_desk role — full dashboard access
```
- **Exploitability:** Easy
- **Recommended Fix:** Either remove public registration entirely (admin-only user creation), or implement a registration approval workflow:
```php
// Option A: Remove the register route entirely
// In routes/web.php, delete:
// Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
// Route::post('/register', [RegisterController::class, 'register']);

// Option B: Require admin approval
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role_id' => $defaultRole->id,
    'is_active' => false, // Require admin activation
]);
// Do NOT auto-login — redirect to "pending approval" page
return redirect()->route('login')->with('info', 'Account pending admin approval.');
```

---

### VULN-002 — APP_KEY and Secrets Committed in `.env`

- **Severity:** Critical
- **OWASP Category:** A02:2021 — Cryptographic Failures
- **Location:** [.env](.env#L3) — `APP_KEY=base64:k8xEI5RPYF/02wQ69X3qpF2o2D/IYJY9W2bHrrFBncA=`
- **Description:** The `.env` file containing the application encryption key is present in the workspace. While `.gitignore` lists `.env`, the file exists in the working tree and the key value `base64:k8xEI5RPYF/02wQ69X3qpF2o2D/IYJY9W2bHrrFBncA=` is exposed. If this repository was ever committed with the `.env` file (even once), the key is in git history permanently. The `APP_KEY` is used to encrypt all session data, cookies, CSRF tokens, and any `encrypt()` calls.
- **Impact:** If the key is leaked: full session forgery (impersonate any user including admin), CSRF token generation, decryption of any encrypted data (e.g., session contents), and potential RCE via Laravel's deserialization pipeline.
- **Proof of Concept:** An attacker with the APP_KEY can forge a valid Laravel session cookie to impersonate the administrator.
- **Exploitability:** Easy (if key is in git history) / Moderate (if never committed)
- **Recommended Fix:**
  1. Rotate the APP_KEY immediately in production: `php artisan key:generate`
  2. Verify `.env` was never committed: `git log --all -- .env`
  3. If it was, use `git filter-branch` or `BFG Repo-Cleaner` to purge it from history
  4. Add `.env` verification to CI: fail if `.env` is tracked
  5. Invalidate all existing sessions after key rotation

---

### VULN-003 — Missing Authorization on Booking/Reservation/Guest CRUD

- **Severity:** Critical
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [routes/web.php](routes/web.php#L114-L141) — `reservations`, `bookings`, `guests` resource routes
- **Description:** The `reservations`, `bookings`, and `guests` routes are protected only by `auth` middleware — any authenticated user (including the self-registered `front_desk` from VULN-001) can access **all** reservations, bookings, and guest records regardless of role. There is no `role:` middleware applied to these route groups. Specifically:
  - `Route::resource('reservations', ...)` — no role check
  - `Route::resource('guests', ...)` — no role check
  - `bookings/*` routes — no role check  
  - Reservation status actions (`confirm`, `check-in`, `cancel`, `no-show`) — no role check
  
  Additionally, none of the controllers implement per-record ownership checks (e.g., "did this user create this reservation?").
- **Impact:** Any authenticated user can view all guest PII (ID numbers, passports, photos), modify or delete any booking/reservation, perform check-ins/check-outs, and cancel reservations they did not create.
- **Proof of Concept:** After self-registering (VULN-001), navigate to `/reservations`, `/guests`, or `/bookings` to see all records. Call `POST /reservations/{any-id}/confirm` to confirm any reservation.
- **Exploitability:** Easy
- **Recommended Fix:** Add role middleware and implement ownership-based authorization:
```php
// routes/web.php — restrict by role
Route::middleware(['role:admin,supervisor,front_desk,manager'])->group(function () {
    Route::resource('reservations', ReservationController::class);
    Route::resource('guests', GuestController::class);
    // ... booking routes
});

// Additionally, use Laravel Policies for per-record authorization:
// php artisan make:policy BookingPolicy --model=Booking
```

---

### VULN-004 — Webhook Signature Bypass When Secret Is Empty

- **Severity:** Critical
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [app/Services/Payment/SnippeProvider.php](app/Services/Payment/SnippeProvider.php) — `validateWebhook()` method
- **Description:** The `validateWebhook()` method returns `true` unconditionally when `SNIPPE_WEBHOOK_SECRET` is empty or not configured:
  ```php
  if (empty($this->webhookSecret)) {
      Log::warning('Snippe webhook secret not configured — skipping signature verification');
      return true; // BYPASS
  }
  ```
  The `.env` file shows `SNIPPE_WEBHOOK_SECRET` is not set. This means any attacker can send a forged webhook to `POST /payments/webhook/snippe` to mark any pending payment as successful without actually paying.
- **Impact:** Complete payment bypass. An attacker can submit a forged webhook payload to mark reservations as paid without any actual money transfer, resulting in direct financial loss.
- **Proof of Concept:**
```bash
curl -X POST https://target.com/payments/webhook/snippe \
  -H "Content-Type: application/json" \
  -d '{
    "type": "payment.completed",
    "data": {
      "reference": "KNOWN_PROVIDER_REFERENCE",
      "amount": {"value": 500000, "currency": "TZS"},
      "metadata": {"payment_id": "target-payment-uuid"}
    }
  }'
# Payment is marked as successful — guest "paid" nothing
```
- **Exploitability:** Easy
- **Recommended Fix:**
```php
public function validateWebhook(array $payload, array $headers): bool
{
    if (empty($this->webhookSecret)) {
        Log::critical('Snippe webhook secret not configured — rejecting webhook');
        return false; // REJECT, never bypass
    }

    $signature = $headers['x-webhook-signature'] ?? $headers['X-Webhook-Signature'] ?? null;
    if (!$signature) {
        return false;
    }

    $computed = hash_hmac('sha256', json_encode($payload), $this->webhookSecret);
    return hash_equals($computed, $signature);
}
```

---

### VULN-005 — No Rate Limiting on Login Endpoint

- **Severity:** High
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [app/Http/Controllers/Auth/LoginController.php](app/Http/Controllers/Auth/LoginController.php) — `login()` method
- **Description:** The custom `LoginController::login()` method does not use the `LoginRequest` class (which exists at `app/Http/Requests/Auth/LoginRequest.php` with proper `RateLimiter` integration). Instead, it performs manual `$request->validate()` with no throttling. There is no limit on login attempts, enabling brute-force attacks on any user account.
- **Impact:** Unlimited password guessing attacks. An attacker can attempt thousands of passwords per minute against any email address, including the admin account.
- **Proof of Concept:**
```bash
# Bruteforce loop — no lockout occurs
for i in $(seq 1 10000); do
  curl -s -X POST https://target.com/login \
    -d "email=admin@hotel.com&password=attempt$i"
done
```
- **Exploitability:** Easy
- **Recommended Fix:** Use the existing `LoginRequest` form request or add throttling:
```php
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;

public function login(Request $request) {
    $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();

    if (RateLimiterFacade::tooManyAttempts($key, 5)) {
        $seconds = RateLimiterFacade::availableIn($key);
        throw ValidationException::withMessages([
            'email' => "Too many attempts. Try again in {$seconds} seconds.",
        ]);
    }

    // ... existing auth logic

    if (!Auth::attempt($credentials, $request->filled('remember'))) {
        RateLimiterFacade::hit($key, 300); // 5-minute window
        throw ValidationException::withMessages([...]);
    }

    RateLimiterFacade::clear($key);
    // ... rest of method
}
```

---

### VULN-006 — No Rate Limiting on Public Booking/Payment Endpoints

- **Severity:** High
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [routes/web.php](routes/web.php#L52-L67) — Public booking routes & payment callback
- **Description:** The public-facing endpoints have no rate limiting:
  - `POST /booking` (booking.store) — create unlimited reservations
  - `GET /booking/search` — enumerate available rooms
  - `GET /payments/callback` — payment callback accessible without auth
  - `POST /payments/webhook/snippe` — webhook endpoint
  
  These endpoints are accessible to anonymous users without any throttling.
- **Impact:** An attacker can flood the system with fake reservations to block all rooms (denial of service), enumerate all room availability/pricing, or abuse payment callbacks.
- **Proof of Concept:**
```bash
# Flood fake reservations to block all rooms
for room_id in $(get_all_room_ids); do
  curl -X POST https://target.com/booking \
    -d "room_id=$room_id&check_in=2026-03-01&check_out=2026-12-31&guests=1&guest_name=Fake&guest_email=fake$RANDOM@test.com&guest_phone=000"
done
```
- **Exploitability:** Easy
- **Recommended Fix:** Add rate limiting via middleware:
```php
// In bootstrap/app.php or a custom middleware
->withMiddleware(function (Middleware $middleware) {
    $middleware->throttleApi('60,1'); // 60 requests per minute
});

// Or per-route:
Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('booking.store');
```

---

### VULN-007 — IDOR on Public Reservation Confirmation Page

- **Severity:** High
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php) — `showConfirmation()` method; [routes/web.php](routes/web.php#L57)
- **Description:** The reservation confirmation page at `GET /booking/confirmation/{reservation}` is publicly accessible (no auth middleware). It uses Laravel's implicit route model binding with the UUID primary key. While UUIDs provide some obscurity, the endpoint loads full reservation details including room type, floor, guest name, email, phone, check-in/check-out dates, and amounts. There is no secondary verification (e.g., email token, booking reference match).
- **Impact:** If an attacker guesses or obtains a reservation UUID (e.g., from browser history, shared links, or sequential probing), they can view the guest's personal information and booking details.
- **Proof of Concept:** If a reservation ID is leaked (URL shared, referrer header, etc.), anyone can access: `GET /booking/confirmation/<uuid>`
- **Exploitability:** Moderate (requires UUID knowledge)
- **Recommended Fix:** Add a signed URL or token verification:
```php
// In store():
return redirect()->route('booking.confirmation', [
    'reservation' => $reservation->id,
    'token' => hash_hmac('sha256', $reservation->id, config('app.key')),
]);

// In showConfirmation():
public function showConfirmation(Reservation $reservation, Request $request) {
    $expected = hash_hmac('sha256', $reservation->id, config('app.key'));
    if (!hash_equals($expected, $request->query('token', ''))) {
        abort(403);
    }
    // ... load and show
}
```

---

### VULN-008 — Mass Assignment on User Model

- **Severity:** High
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Models/User.php](app/Models/User.php) — `$fillable`; [app/Http/Controllers/UserController.php](app/Http/Controllers/UserController.php) — `store()` and `update()` methods
- **Description:** The `User` model's `$fillable` includes `role_id` and `is_active`:
  ```php
  protected $fillable = ['name', 'email', 'password', 'role_id', 'is_active'];
  ```
  The `UserController::store()` passes all validated data directly to `User::create($validated)`. While the route is admin-protected, the `RegisterController` also creates users and could potentially be exploited if the request includes a `role_id` field — although currently the role is hard-coded. More critically, the `ProfileController::update()` only validates `name` and `email`, but if an attacker adds `role_id` or `is_active` to the request body, Laravel's `$request->user()->fill($validated)` would not accept them (since they're not in `$validated`). However, the `$fillable` array makes the model susceptible if any controller passes unfiltered input.
- **Impact:** If any code path passes unfiltered request data to `User::create()` or `->update()`, an attacker could escalate to admin or reactivate a deactivated account.
- **Exploitability:** Moderate (requires a code path that passes raw input)
- **Recommended Fix:** Consider using `$guarded` or removing sensitive fields from `$fillable`:
```php
protected $fillable = ['name', 'email', 'password'];
// Set role_id and is_active explicitly in controllers, never from mass assignment
```

---

### VULN-009 — Payment Amount Controlled by Client

- **Severity:** High
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Http/Controllers/PaymentController.php](app/Http/Controllers/PaymentController.php) — `store()` method
- **Description:** The payment initiation accepts the `amount` from the client request with only a `min:1` validation:
  ```php
  'amount' => 'required|numeric|min:1',
  ```
  There is no server-side validation that the payment amount matches the booking `total_amount` or the `charge.amount`. An attacker with front-desk access could initiate a payment for `1 TZS` instead of the actual booking amount.
- **Impact:** Staff or compromised accounts can process payments for arbitrary (lower) amounts, resulting in revenue loss.
- **Proof of Concept:**
```http
POST /bookings/{booking-uuid}/payments
amount=1&payment_method=mobile&phone_number=255700000000
# Payment of 1 TZS initiated for a 500,000 TZS booking
```
- **Exploitability:** Easy (for authenticated staff)
- **Recommended Fix:**
```php
$request->validate([
    'amount' => 'required|numeric|min:1',
    // ...
]);

// Server-side amount validation
$expectedAmount = $charge ? $charge->amount : $booking->total_amount;
if ((float) $request->amount > $expectedAmount) {
    return back()->with('error', 'Amount exceeds the outstanding balance.');
}
// Optionally enforce exact match:
if (abs((float) $request->amount - $expectedAmount) > 0.01) {
    return back()->with('error', 'Amount does not match the expected payment.');
}
```

---

### VULN-010 — Booking Total Amount Controlled by Client (Price Manipulation)

- **Severity:** High
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php) — `storeFrontdesk()` and `update()` methods
- **Description:** The `total_amount` for walk-in bookings is submitted by the client:
  ```php
  'total_amount' => 'required|numeric|min:0',
  ```
  The server does not recalculate the amount based on room type, nights, and rates. An attacker with front-desk access can set `total_amount=0` for any booking, creating stays with zero charges.
- **Impact:** Direct financial loss — bookings created with manipulated (zero or reduced) amounts.
- **Proof of Concept:**
```http
POST /bookings
room_id=xxx&check_in_date=2026-03-01&check_out_date=2026-03-05&number_of_guests=2&total_amount=0&guest_id=xxx
# 4-night stay created with $0 total
```
- **Exploitability:** Easy (for authenticated staff)
- **Recommended Fix:**
```php
// Calculate server-side:
$nights = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
$calculatedAmount = $nights * $room->roomType->base_rate;
// Use $calculatedAmount, ignore user input for total_amount
```

---

### VULN-011 — Charge Belongs-To-Booking Not Verified (IDOR)

- **Severity:** High
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/PaymentController.php](app/Http/Controllers/PaymentController.php) — `create()` and `store()` methods
- **Description:** When paying for a specific charge via `?charge_id=xxx`, the controller loads the charge globally:
  ```php
  $charge = BookingCharge::findOrFail($request->charge_id);
  ```
  It does not verify that the charge belongs to the current booking (`$booking`). An attacker can pass a `charge_id` from a different booking, potentially paying a charge on the wrong booking or viewing another booking's charge details.
- **Impact:** Cross-booking data leakage and misattributed payments.
- **Exploitability:** Moderate
- **Recommended Fix:**
```php
if ($request->filled('charge_id')) {
    $charge = $booking->bookingCharges()->findOrFail($request->charge_id);
    $amount = $charge->amount;
}
```

---

### VULN-012 — Session Not Encrypted / Secure Cookie Flag Not Set

- **Severity:** Medium
- **OWASP Category:** A02:2021 — Cryptographic Failures
- **Location:** [config/session.php](config/session.php) and [.env](.env#L33)
- **Description:** Multiple session security settings are weakened:
  - `SESSION_ENCRYPT=false` — session data stored unencrypted in the database
  - `SESSION_SECURE_COOKIE` is not set in `.env` (defaults to `null`) — cookies sent over HTTP
  - `SESSION_DOMAIN=null` — cookie scoped broadly
  - `SESSION_SAME_SITE=lax` (acceptable but `strict` is more secure for admin panels)
- **Impact:** Session data readable in plaintext in the database. Session cookies can be intercepted over HTTP connections (no `Secure` flag).
- **Exploitability:** Moderate (requires network interception or DB access)
- **Recommended Fix:**
```env
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

---

### VULN-013 — APP_DEBUG=true in Production Configuration

- **Severity:** Medium
- **OWASP Category:** A05:2021 — Security Misconfiguration
- **Location:** [.env](.env#L4) — `APP_DEBUG=true`
- **Description:** Debug mode is enabled. If deployed with this configuration, detailed stack traces, environment variables, database queries, and internal paths will be exposed to users on any unhandled exception.
- **Impact:** Information disclosure — database credentials, file paths, internal architecture, and potentially the APP_KEY leaked in error pages.
- **Exploitability:** Easy
- **Recommended Fix:**
```env
APP_DEBUG=false
APP_ENV=production
```

---

### VULN-014 — LIKE Query Wildcard Injection (Search DoS)

- **Severity:** Medium
- **OWASP Category:** A03:2021 — Injection
- **Location:** [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php#L180-L183), [app/Http/Controllers/guestcontroller.php](app/Http/Controllers/guestcontroller.php#L22-L26), [app/Http/Controllers/LaundryOrderController.php](app/Http/Controllers/LaundryOrderController.php)
- **Description:** Search queries use unescaped user input directly in `LIKE` patterns:
  ```php
  $q->where('guest_name', 'like', "%{$search}%")
  ```
  An attacker can inject `%` and `_` wildcards to force expensive full-table scans.
- **Impact:** Denial of service via expensive database queries. For example, searching for `%%%%%` forces the DB to scan every row multiple times.
- **Proof of Concept:**
```http
GET /bookings?search=%25%25%25%25%25%25%25%25%25%25%25%25
```
- **Exploitability:** Easy
- **Recommended Fix:**
```php
$search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
$q->where('guest_name', 'like', "%{$search}%");
```

---

### VULN-015 — Missing CSRF Protection Understanding for AJAX Endpoints

- **Severity:** Medium
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php) — `checkAvailability()`, `getAvailableRooms()`; [app/Http/Controllers/ConferenceParticipantController.php](app/Http/Controllers/ConferenceParticipantController.php) — `checkInByScan()`, `checkInByCode()`
- **Description:** Several endpoints return JSON but are registered as regular web routes (not API routes). While Laravel's CSRF middleware should protect POST routes, the GET endpoints that return JSON data (`checkAvailability`, `getAvailableRooms`) are accessible without authentication. The conference check-in POST endpoints (`checkInByScan`, `checkInByCode`) are within authenticated routes and properly protected by CSRF, but return JSON — they should be explicitly confirmed as CSRF-protected.
- **Impact:** Room availability data accessible to unauthenticated users via the authenticated API endpoints.
- **Exploitability:** Low
- **Recommended Fix:** Move JSON-returning endpoints to dedicated API routes with explicit authentication, or add `auth` middleware to the availability check endpoints that are inside the auth group.

---

### VULN-016 — No Password Complexity Enforcement on Admin User Creation

- **Severity:** Medium
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [app/Http/Controllers/UserController.php](app/Http/Controllers/UserController.php) — `store()` method
- **Description:** Admin user creation validates passwords with only `min:8`:
  ```php
  'password' => 'required|min:8|confirmed',
  ```
  Unlike the registration endpoint which uses `Rules\Password::defaults()`, the admin user creation has no complexity requirements, no breach checking, and no mixed case/number/symbol requirements.
- **Impact:** Admin can create user accounts with weak passwords (e.g., `password`, `12345678`).
- **Exploitability:** Moderate
- **Recommended Fix:**
```php
'password' => ['required', 'confirmed', Rules\Password::defaults()],
```

---

### VULN-017 — No Email Verification on Registration or Public Booking

- **Severity:** Medium
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [app/Http/Controllers/Auth/RegisterController.php](app/Http/Controllers/Auth/RegisterController.php), [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php) — `store()` (public)
- **Description:** Neither user registration nor public booking requires email verification. The `User` model does not implement `MustVerifyEmail`. Public bookings accept any email address without validation of ownership.
- **Impact:** Fake accounts, spam reservations with fabricated emails, no way to contact guests for legitimate reservations.
- **Exploitability:** Easy
- **Recommended Fix:**
```php
// User model:
class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}

// In bootstrap/app.php, add 'verified' middleware to authenticated routes
```

---

### VULN-018 — Missing Security Headers

- **Severity:** Medium
- **OWASP Category:** A05:2021 — Security Misconfiguration
- **Location:** [bootstrap/app.php](bootstrap/app.php) — No security header middleware configured
- **Description:** The application does not set any of the following HTTP security headers:
  - `Content-Security-Policy`
  - `X-Frame-Options`
  - `X-Content-Type-Options`
  - `Strict-Transport-Security`
  - `Referrer-Policy`
  - `Permissions-Policy`
  
  No custom middleware is registered for security headers.
- **Impact:** Vulnerable to clickjacking (no `X-Frame-Options`), MIME-type sniffing attacks, and missing HSTS allows SSL stripping.
- **Exploitability:** Moderate
- **Recommended Fix:** Create a `SecurityHeaders` middleware:
```php
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';");
        return $response;
    }
}
```

---

### VULN-019 — Race Condition on Room Availability Check

- **Severity:** Medium
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Http/Controllers/BookingController.php](app/Http/Controllers/BookingController.php) — `store()`, `storeFrontdesk()`; [app/Models/Booking.php](app/Models/Booking.php) — `isRoomAvailable()`
- **Description:** Room availability check and booking creation are not atomic. The sequence is:
  1. Check `isRoomAvailable()` — SELECT query
  2. Create booking — INSERT query
  
  Between steps 1 and 2, another concurrent request can also pass the availability check and create a booking for the same room, resulting in double-booking.
- **Impact:** Two guests booked into the same room for overlapping dates.
- **Proof of Concept:** Send two simultaneous `POST /booking` requests for the same room and dates — both will pass the availability check.
- **Exploitability:** Moderate
- **Recommended Fix:** Use a database-level unique constraint or pessimistic locking:
```php
DB::transaction(function () use ($validated, $room) {
    // Lock the room row
    $room = Room::lockForUpdate()->findOrFail($validated['room_id']);

    if (!Booking::isRoomAvailable($room->id, $validated['check_in'], $validated['check_out'])) {
        throw new \Exception('Room no longer available');
    }

    $reservation = Reservation::create([...]);
});
```

---

### VULN-020 — Admin Can Delete Own Account

- **Severity:** Medium
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Http/Controllers/UserController.php](app/Http/Controllers/UserController.php) — `destroy()` method
- **Description:** The `UserController::destroy()` method has no check preventing an admin from deleting their own account or the last admin account:
  ```php
  public function destroy(User $user) {
      $user->delete();
      // No check: is this the current user? Is this the last admin?
  }
  ```
- **Impact:** System lockout — if the only admin deletes their own account, no one can manage the system.
- **Exploitability:** Easy (requires admin access)
- **Recommended Fix:**
```php
public function destroy(User $user) {
    if ($user->id === auth()->id()) {
        return back()->with('error', 'You cannot delete your own account.');
    }
    if ($user->isAdmin() && User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count() <= 1) {
        return back()->with('error', 'Cannot delete the last admin account.');
    }
    $user->delete();
}
```

---

### VULN-021 — Unrestricted File Upload Size and Types on Guest Documents

- **Severity:** Medium
- **OWASP Category:** A04:2021 — Insecure Design
- **Location:** [app/Http/Controllers/guestcontroller.php](app/Http/Controllers/guestcontroller.php) — `store()` and `update()` methods
- **Description:** While file type validation exists (`mimes:jpeg,png,jpg,pdf|max:5120`), the validation can be bypassed by crafting files with correct MIME headers but malicious content. The uploads are processed by Spatie Media Library which stores them on disk. There is no check for:
  - Double extensions (e.g., `malware.pdf.php`)
  - SVG files (which can contain JavaScript)  
  - File content validation beyond extension/MIME
  - Maximum number of files per guest (no `max` on the `id_documents` array)
- **Impact:** Potential storage exhaustion (unlimited documents), and if the web server misconfigures file serving, uploaded files could be executed.
- **Exploitability:** Moderate
- **Recommended Fix:**
```php
'id_documents' => 'nullable|array|max:10', // Limit number of files
'id_documents.*' => 'file|mimes:jpeg,png,jpg,pdf|max:5120',
// In Spatie config, ensure files are served with proper Content-Disposition: attachment headers
// Ensure storage is not within web root or use signed URLs
```

---

### VULN-022 — Reservation Deletion Without Authorization Check

- **Severity:** Low
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/ReservationController.php](app/Http/Controllers/ReservationController.php) — `destroy()` method
- **Description:** Any authenticated user can delete any reservation without status or ownership checks:
  ```php
  public function destroy(Reservation $reservation) {
      $reservation->delete();
      // No status check, no ownership check
  }
  ```
  This allows deletion of confirmed or even converted reservations.
- **Impact:** Data loss — confirmed reservations deleted, audit trail destroyed.
- **Exploitability:** Easy (for any authenticated user)
- **Recommended Fix:**
```php
public function destroy(Reservation $reservation) {
    if (!in_array($reservation->status, ['pending', 'cancelled'])) {
        return back()->with('error', 'Only pending or cancelled reservations can be deleted.');
    }
    $reservation->delete();
}
```

---

### VULN-023 — Conference Check-in Endpoints Lack Conference Scoping

- **Severity:** Low
- **OWASP Category:** A01:2021 — Broken Access Control
- **Location:** [app/Http/Controllers/ConferenceParticipantController.php](app/Http/Controllers/ConferenceParticipantController.php) — `checkInByScan()`, `checkInByCode()`
- **Description:** The `checkInByScan` and `checkInByCode` endpoints search participants globally by `access_token` or `access_code` without scoping to a specific conference:
  ```php
  $participant = ConferenceParticipant::where('access_token', $validated['access_token'])->first();
  ```
  If two conferences share participant data or tokens collide, the wrong participant could be checked in.
- **Impact:** Incorrect check-in records across conferences.
- **Exploitability:** Low
- **Recommended Fix:** Include `conference_id` in the check-in requests and scope the query.

---

### VULN-024 — Sensitive Data Logged in Payment Webhook

- **Severity:** Low
- **OWASP Category:** A09:2021 — Security Logging and Monitoring Failures
- **Location:** [app/Http/Controllers/SnippePaymentController.php](app/Http/Controllers/SnippePaymentController.php) — `webhook()` method
- **Description:** The webhook controller logs the payload type and reference, which is acceptable. However, the `PaymentEngine::pay()` method logs `payment_id` and `reference` after initiation, and the full provider response is stored in the `metadata` JSON column. If the provider sends sensitive data (card numbers, tokens), it is persisted in the database.
- **Impact:** Potential PCI compliance issues if card data is stored in metadata. Log files may contain sensitive financial data.
- **Exploitability:** Low (requires DB/log access)
- **Recommended Fix:** Sanitize provider response before storing in metadata:
```php
$sanitized = collect($result['raw'])->except(['card_number', 'cvv', 'pan', 'token'])->toArray();
$payment->update(['metadata' => $sanitized]);
```

---

### VULN-025 — Password Confirmation Timeout Too Long

- **Severity:** Low
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [config/auth.php](config/auth.php) — `password_timeout`
- **Description:** The password confirmation timeout is set to 10,800 seconds (3 hours). After a user confirms their password (e.g., for profile deletion), they won't be asked again for 3 hours. This is excessive for sensitive operations.
- **Impact:** If a session is hijacked within the 3-hour window after password confirmation, the attacker can perform destructive actions without re-authentication.
- **Exploitability:** Low
- **Recommended Fix:**
```php
'password_timeout' => 900, // 15 minutes
```

---

### VULN-026 — `is_active` Check After Authentication (TOCTOU)

- **Severity:** Low
- **OWASP Category:** A07:2021 — Identification and Authentication Failures
- **Location:** [app/Http/Controllers/Auth/LoginController.php](app/Http/Controllers/Auth/LoginController.php#L29-L33) — `login()` method
- **Description:** The `is_active` check happens after `Auth::attempt()` succeeds. Between the `Auth::attempt()` and the `is_active` check, the user has a valid authenticated session (even if only briefly):
  ```php
  if (!Auth::attempt($credentials, $request->filled('remember'))) { ... }
  // User is authenticated HERE — session exists
  if (!Auth::user()->is_active) {
      Auth::logout(); // Race window
  }
  ```
  Additionally, there is no middleware that continuously checks `is_active` on subsequent requests. Once logged in, if an admin deactivates the user, the existing session remains valid until it expires.
- **Impact:** Deactivated users retain access until session expiration (2 hours). Brief authenticated window for deactivated users during login.
- **Exploitability:** Low
- **Recommended Fix:** Add a middleware that checks `is_active` on every request:
```php
class EnsureUserIsActive
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }
        return $next($request);
    }
}
```

---

## Risk Matrix Table

| ID | Vulnerability | Severity | Impact | Likelihood | Status |
|---|---|---|---|---|---|
| VULN-001 | Open Registration Grants Staff Access | Critical | Critical | High | Open |
| VULN-002 | APP_KEY Committed in .env | Critical | Critical | Medium | Open |
| VULN-003 | Missing Authorization on CRUD Routes | Critical | Critical | High | Open |
| VULN-004 | Webhook Signature Bypass | Critical | Critical | High | Open |
| VULN-005 | No Rate Limiting on Login | High | High | High | Open |
| VULN-006 | No Rate Limiting on Public Endpoints | High | Medium | High | Open |
| VULN-007 | IDOR on Reservation Confirmation | High | High | Medium | Open |
| VULN-008 | Mass Assignment on User Model | High | Critical | Low | Open |
| VULN-009 | Payment Amount Client-Controlled | High | High | Medium | Open |
| VULN-010 | Booking Total Amount Client-Controlled | High | High | Medium | Open |
| VULN-011 | Charge IDOR Across Bookings | High | Medium | Medium | Open |
| VULN-012 | Session Not Encrypted / No Secure Cookie | Medium | Medium | Medium | Open |
| VULN-013 | APP_DEBUG=true | Medium | Medium | High | Open |
| VULN-014 | LIKE Wildcard Injection | Medium | Low | High | Open |
| VULN-015 | AJAX Endpoints Auth Gaps | Medium | Low | Medium | Open |
| VULN-016 | No Password Complexity on User Create | Medium | Medium | Medium | Open |
| VULN-017 | No Email Verification | Medium | Medium | High | Open |
| VULN-018 | Missing Security Headers | Medium | Medium | Medium | Open |
| VULN-019 | Race Condition on Room Booking | Medium | High | Low | Open |
| VULN-020 | Admin Can Delete Own Account | Medium | High | Low | Open |
| VULN-021 | Unrestricted File Upload Count | Medium | Low | Medium | Open |
| VULN-022 | Reservation Deletion Without Auth | Low | Medium | Medium | Open |
| VULN-023 | Conference Check-in Not Scoped | Low | Low | Low | Open |
| VULN-024 | Sensitive Data in Logs/Metadata | Low | Low | Low | Open |
| VULN-025 | Password Confirmation Timeout 3h | Low | Low | Low | Open |
| VULN-026 | is_active Check TOCTOU | Low | Medium | Low | Open |

---

## Attack Scenarios

### Scenario 1: Anonymous to Full System Compromise

**Chain:** VULN-001 → VULN-003 → VULN-009 → VULN-004

1. **Attacker registers** at `/register` — gets `front_desk` role (VULN-001)
2. **Accesses all guest data** — browses `/guests` to extract PII including passport/ID numbers (VULN-003)
3. **Views all active bookings** — browses `/bookings` to see room occupancy and financial data (VULN-003)
4. **Creates walk-in bookings with amount=0** — steals room nights (VULN-010)
5. **Forges payment webhooks** — marks any pending payment as paid without actual money (VULN-004)
6. **Result:** Complete financial fraud + PII data breach, zero cost to attacker

### Scenario 2: Reservation Flooding (Denial of Service)

**Chain:** VULN-006 → VULN-019

1. **Attacker scripts automated reservation creation** via `POST /booking` (no auth required, no rate limit)
2. **Creates reservations for all rooms** across all available dates at 100+ requests/second
3. **Race condition** ensures overlapping reservations slip through (VULN-019)
4. **All rooms appear reserved** — legitimate guests cannot book
5. **Result:** Complete service disruption, hotel cannot accept reservations

### Scenario 3: Payment Fraud by Compromised Staff

**Chain:** VULN-009 → VULN-010 → VULN-011

1. **Compromised front-desk account** (via VULN-005 brute-force or VULN-001 registration)
2. **Creates booking with total_amount=0** for an accomplice guest (VULN-010)
3. **If challenged,** initiates payment of `1 TZS` and forges webhook to mark as paid (VULN-004)
4. **Cross-references charges** using IDOR to pay charges on wrong bookings (VULN-011)
5. **Result:** Free hotel stays, misattributed payments, difficult to detect in accounting

### Scenario 4: Session Hijacking via Debug Exposure

**Chain:** VULN-013 → VULN-002 → VULN-012

1. **Trigger an error** on the production site (e.g., visit an invalid route with malformed input)
2. **Debug page reveals** environment variables including `APP_KEY` (VULN-013)
3. **With APP_KEY,** forge a session cookie for the admin user (VULN-002)
4. **Sessions are unencrypted** in DB — if attacker gets DB read access, they see all sessions (VULN-012)
5. **Result:** Full admin access without knowing any password

---

## Hardening Recommendations

### Priority 1 — Immediate (Deploy today)
- [ ] **Remove or disable public registration** — VULN-001
- [ ] **Rotate APP_KEY** and verify `.env` not in git history — VULN-002
- [ ] **Add role middleware** to reservation, booking, and guest routes — VULN-003
- [ ] **Reject webhooks when secret is not configured** — VULN-004
- [ ] **Set APP_DEBUG=false** in production — VULN-013

### Priority 2 — High (This week)
- [ ] **Add login rate limiting** — use existing `LoginRequest` or add throttle — VULN-005
- [ ] **Add rate limiting** to all public routes — VULN-006
- [ ] **Validate payment amounts server-side** against booking/charge amounts — VULN-009, VULN-010
- [ ] **Scope charges to parent booking** in payment controller — VULN-011
- [ ] **Add signed URL** to reservation confirmation page — VULN-007

### Priority 3 — Medium (This sprint)
- [ ] **Enable session encryption** and secure cookie flags — VULN-012
- [ ] **Add security headers middleware** — VULN-018
- [ ] **Implement email verification** (`MustVerifyEmail`) — VULN-017
- [ ] **Escape LIKE wildcards** in all search queries — VULN-014
- [ ] **Use `Password::defaults()`** everywhere — VULN-016
- [ ] **Add `is_active` check middleware** for ongoing sessions — VULN-026
- [ ] **Use database transactions** with row locking for bookings — VULN-019
- [ ] **Prevent self-deletion** and last-admin deletion — VULN-020
- [ ] **Limit file upload counts** — VULN-021

### Priority 4 — Low (Next sprint)
- [ ] **Add reservation deletion safeguards** — VULN-022
- [ ] **Scope conference check-in** to conference ID — VULN-023
- [ ] **Sanitize payment metadata** — VULN-024
- [ ] **Reduce password confirmation timeout** — VULN-025
- [ ] **Add CORS configuration** (`config/cors.php`) with restrictive defaults
- [ ] **Implement audit logging** for all state-changing operations (who did what, when)
- [ ] **Add Laravel Policies** for fine-grained per-model authorization
- [ ] **Implement Content-Security-Policy** with nonce-based script sources

---

## Dependency & CVE Summary

| Package | Version | Status | Notes |
|---|---|---|---|
| `laravel/framework` | `^12.0` | Current | Latest major version — no known CVEs |
| `spatie/laravel-medialibrary` | `^11.18` | Current | No known CVEs for v11 |
| `php` | `^8.2` | Current | Ensure latest patch (8.2.x or 8.3.x) |
| `fruitcake/php-cors` | In `composer.lock` | Review | Included via framework, ensure up-to-date |

**No critical CVEs found** in the declared dependencies. However:
- Run `composer audit` regularly to check for newly disclosed vulnerabilities
- Pin exact versions in `composer.lock` (already done) and review before deployment
- Consider adding `roave/security-advisories` as a dev dependency:
```bash
composer require --dev roave/security-advisories:dev-latest
```
This will prevent `composer update` from installing packages with known security issues.

**Frontend dependencies** (`package.json`): Not deeply audited. Run `npm audit` and address any reported issues.

---

## Final Risk Score

| Dimension | Score (1-10) | Justification |
|---|---|---|
| Authentication | 3/10 | Login works but no rate limiting, no MFA, no email verification, open registration |
| Authorization | 2/10 | Role middleware exists but not applied to primary routes, no per-record policies |
| Input Validation | 6/10 | Laravel validation present but LIKE injection, client-controlled amounts |
| Payment Security | 3/10 | Webhook bypass, client-controlled amounts, cross-booking charge IDOR |
| Session Security | 5/10 | Session regeneration done correctly, but no encryption, no secure flag |
| Infrastructure Config | 4/10 | Debug mode on, no security headers, APP_KEY potentially leaked |
| Data Protection | 5/10 | Passwords hashed (bcrypt 12 rounds), but PII accessible without proper roles |
| **Overall** | **3.5/10** | **HIGH RISK — Not suitable for production deployment** |

**Justification:** The combination of open registration granting staff access (VULN-001), missing authorization on all primary CRUD routes (VULN-003), and payment webhook bypass (VULN-004) creates a trivially exploitable attack chain that grants any anonymous internet user full access to guest PII, financial data, and payment manipulation. These must be resolved before any production deployment.

---

*Report generated via static code analysis. Dynamic testing (penetration testing against a running instance) is recommended to validate findings and discover runtime-only vulnerabilities.*
