# 🔒 HOTEL MANAGEMENT SYSTEM — SECURITY PATCHING REPORT

> **Generated:** May 20, 2026
> **Audit Type:** Full Deep Security Architecture & Code Review
> **Findings:** 18 vulnerabilities (4 Critical, 5 High, 6 Medium, 3 Low)

---

## 📋 TABLE OF CONTENTS

1. [🔴 CRITICAL — Secrets Leakage (V-001)](#v001)
2. [🔴 CRITICAL — Debug Mode Enabled (V-002)](#v002)
3. [🔴 CRITICAL — Mass Assignment Vulnerability (V-003)](#v003)
4. [🔴 CRITICAL — Webhook Secret Exposed (V-004)](#v004)
5. [🟠 HIGH — SQLite in Production (V-005)](#v005)
6. [🟠 HIGH — Predictable IDs (V-006)](#v006)
7. [🟠 HIGH — Broadcast Abuse (V-007)](#v007)
8. [🟠 HIGH — Weak CSP (V-008)](#v008)
9. [🟠 HIGH — SMS Fail-Open (V-009)](#v009)
10. [🟡 MEDIUM — Account Enumeration (V-010)](#v010)
11. [🟡 MEDIUM — IDOR Guest Search (V-011)](#v011)
12. [🟡 MEDIUM — Missing Model Policies (V-012)](#v012)
13. [🟡 MEDIUM — Session Config (V-013)](#v013)
14. [🟡 MEDIUM — Weak Temp Passwords (V-014)](#v014)
15. [🟡 MEDIUM — Race Condition (V-015)](#v015)
16. [🔵 LOW — Account Lockout (V-016)](#v016)
17. [🔵 LOW — Rate Limiting (V-017)](#v017)
18. [🔵 LOW — PII in Logs (V-018)](#v018)

---

<a name="v001"></a>
## 🔴 V-001: LIVE SECRETS COMMITTED TO GIT

### Severity
**CRITICAL** — CVSS 10.0 | CWE-798, CWE-200, CWE-538

### The Problem
The `.env` file containing **live production credentials** is tracked in git history. Anyone with repo access can extract:
- `APP_KEY` — decrypts all encrypted data (sessions, system settings, API keys)
- `SNIPPE_API_KEY` — full payment gateway access
- `SNIPPE_WEBHOOK_SECRET` — forge fake payment confirmations
- `REVERB_APP_KEY/SECRET` — hijack WebSocket broadcasting

### Affected File
`.env` (entire file — 82 lines of secrets)

### What To Do

**Step 1 — Rotate ALL credentials NOW**

Generate a new APP_KEY:
```bash
php artisan key:generate
```

Get new keys from:
- **Snippe Dashboard** → new API key + webhook secret
- **Reverb** → new app key + secret
- **Africa's Talking** → new API key

**Step 2 — Update .env with fresh values**
```env
APP_KEY=base64:<<NEW_KEY_FROM_ARTISAN>>
APP_DEBUG=false

SESSION_LIFETIME=30
SESSION_ENCRYPT=true

SNIPPE_API_KEY=<<NEW_KEY_FROM_SNIPPE>>
SNIPPE_WEBHOOK_SECRET=<<NEW_SECRET_FROM_SNIPPE>>

REVERB_APP_ID=<<NEW_ID>>
REVERB_APP_KEY=<<NEW_KEY>>
REVERB_APP_SECRET=<<NEW_SECRET>>
```

**Step 3 — Remove .env from git tracking**
```bash
git rm --cached .env
git add .gitignore   # verify .env is listed
git commit -m "chore: remove .env from tracking"
```

**Step 4 — Purge .env from ALL git history**
```bash
# WARNING: Rewrites history! Coordinate with team.
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

git push origin --force --all
git push origin --force --tags
```

**Step 5 — Every team member re-clones**
```bash
git fetch --all
git reset --hard origin/main
```

---

<a name="v002"></a>
## 🔴 V-002: DEBUG MODE ENABLED

### Severity
**CRITICAL** — CVSS 9.0 | CWE-489

### The Problem
`APP_DEBUG=true` shows full stack traces + ALL environment variables (including secrets) on every error.

### Affected File
`.env` (line 4)

### What To Do
```diff
- APP_DEBUG=true
+ APP_DEBUG=false
```

Then:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Verify by visiting `http://yoursite.com/nonexistent` — should show generic 404, NOT stack trace.

---

<a name="v003"></a>
## 🔴 V-003: MASS ASSIGNMENT PROTECTION BROKEN

### Severity
**CRITICAL** — CVSS 9.0 | CWE-915

### The Problem
`$guarded_extra` is NOT a Laravel property. It does nothing. The developer intended to guard `role_id` and `is_active` but used the wrong property name.

### Affected File
`app/Models/User.php` (line 23)

### What To Do
```diff
- protected $guarded_extra = ['role_id', 'is_active'];
+ protected $guarded = [
+     'role_id',
+     'is_active',
+     'must_change_password',
+     'password_reset_requested_at',
+     'password_reset_completed_at',
+     'password_reset_phone',
+     'email_verified_at',
+     'remember_token',
+ ];
```

---

<a name="v004"></a>
## 🔴 V-004: WEBHOOK SIGNING SECRET EXPOSED

### Severity
**CRITICAL** — CVSS 8.5 | CWE-345, CWE-347

### The Problem
Webhook secret is in `.env` which is committed to git. Attacker can forge HMAC-signed webhooks to mark fake payments as successful.

### Affected Files
- `app/Http/Controllers/SnippePaymentController.php` (all)
- `app/Services/Payment/SnippeProvider.php` (validateWebhook method)

### What To Do

**Fix 1 — Rotate webhook secret** (same as V-001 Step 1)

**Fix 2 — Harden webhook validation** in `app/Services/Payment/SnippeProvider.php`:

```php
public function validateWebhook(array $payload, array $headers): bool
{
    if (empty($this->webhookSecret)) {
        Log::critical('Webhook secret not configured — ALL webhooks rejected');
        return false;
    }

    $signature = $headers['x-webhook-signature'] ?? $headers['X-Webhook-Signature'] ?? '';
    if (!$signature) {
        Log::warning('Webhook rejected: missing signature');
        return false;
    }

    ksort($payload);
    $computed = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES), $this->webhookSecret);

    return hash_equals($computed, $signature);
}
```

**Fix 3 — Add IP whitelist check** (add after signature check):
```php
$whitelistedIps = ['3.xx.xx.xx', '4.xx.xx.xx']; // Get from Snippe docs
if (!in_array(request()->ip(), $whitelistedIps)) {
    Log::warning('Webhook from untrusted IP', ['ip' => request()->ip()]);
    return false;
}
```

---

<a name="v005"></a>
## 🟠 V-005: SQLITE IN PRODUCTION

### Severity
**HIGH** — CVSS 8.0 | CWE-662, CWE-821

### The Problem
SQLite cannot handle concurrent hotel operations (check-in, payments, orders). Causes: race conditions, `SQLITE_BUSY` errors, data corruption.

### Affected Files
- `.env` (line 23)
- `config/database.php` (lines 34-44)

### What To Do

**Immediate — Switch to PostgreSQL:**

`.env`:
```diff
- DB_CONNECTION=sqlite
+ DB_CONNECTION=pgsql
+ DB_HOST=127.0.0.1
+ DB_PORT=5432
+ DB_DATABASE=hotel_management
+ DB_USERNAME=hotel_user
+ DB_PASSWORD=<<STRONG_PASSWORD>>
- # DB_HOST=127.0.0.1
- # DB_PORT=3306
- # DB_DATABASE=hotel_management_system
- # DB_USERNAME=root
- # DB_PASSWORD=
```

`config/database.php`:
```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'sslmode' => 'require',
],
```

**Migration Steps:**
```bash
# 1. Create Postgres database
psql -U postgres -c "CREATE DATABASE hotel_management;"
psql -U postgres -c "CREATE USER hotel_user WITH PASSWORD '<<password>>';"
psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE hotel_management TO hotel_user;"

# 2. Update .env with Postgres credentials

# 3. Run migrations
php artisan migrate --force

# 4. Seed data
php artisan db:seed --force

# 5. Config cache
php artisan config:cache
```

---

<a name="v006"></a>
## 🟠 V-006: PREDICTABLE BOOKING/PAYMENT NUMBERS

### Severity
**HIGH** — CVSS 7.5 | CWE-338

### The Problem
`uniqid()` is based on microsecond timestamp — predictable and not cryptographically secure.

### Affected Files
- `app/Models/Booking.php` (line 47)
- `app/Models/Payment.php` (line 47)
- `app/Models/Reservation.php` (line 34)

### What To Do

**Booking.php:**
```diff
+ use Illuminate\Support\Str;

static::creating(function ($booking) {
    if (empty($booking->booking_number)) {
-       $booking->booking_number = 'BK-' . strtoupper(uniqid());
+       $booking->booking_number = 'BK-' . strtoupper(Str::random(10));
    }
});
```

**Payment.php:**
```diff
+ use Illuminate\Support\Str;

static::creating(function ($payment) {
    if (empty($payment->payment_number)) {
-       $payment->payment_number = 'PAY-' . strtoupper(uniqid());
+       $payment->payment_number = 'PAY-' . strtoupper(Str::random(10));
    }
});
```

**Reservation.php:**
```diff
+ use Illuminate\Support\Str;

static::creating(function ($reservation) {
-   $reservation->reservation_number = 'RES-' . strtoupper(uniqid());
+   $reservation->reservation_number = 'RES-' . strtoupper(Str::random(10));
});
```

---

<a name="v007"></a>
## 🟠 V-007: BROADCAST MODULE CAN BE ABUSED

### Severity
**HIGH** — CVSS 7.5 | CWE-862

### The Problem
Supervisors and laundry managers (low-privilege roles) can send mass emails/SMS to ALL guests.

### Affected Files
- `routes/web.php` (lines 726-731)
- `app/Jobs/SendBroadcastJob.php` (lines 53-69)

### What To Do

**Fix 1 — Restrict access in routes/web.php:**
```diff
- Route::middleware(['role:admin,manager,supervisor,laundry_manager'])
+ Route::middleware(['role:admin,manager'])
    ->prefix('admin')->name('admin.')->group(function () {
```

**Fix 2 — Add target validation in SendBroadcastJob.php:**
```diff
private function getTargetGuests()
{
    $query = Guest::query();

+   $validTargets = ['all', 'Silver', 'Gold', 'Platinum', 'walkin', 'guests'];
+   if (!in_array($this->broadcast->target, $validTargets)) {
+       Log::error('Invalid broadcast target', ['target' => $this->broadcast->target]);
+       return collect();
+   }

    return match ($this->broadcast->target) {
        ...
    };
}
```

---

<a name="v008"></a>
## 🟠 V-008: WEAK CONTENT SECURITY POLICY

### Severity
**HIGH** — CVSS 5.0 | CWE-79, CWE-1021

### The Problem
CSP allows `unsafe-inline`, `unsafe-eval`, and CDN script sources — making XSS fully exploitable.

### Affected File
`app/Http/Middleware/SecurityHeaders.php` (lines 51-61)

### What To Do

**If using Vite (bundled assets):**
```php
$response->headers->set('Content-Security-Policy', implode('; ', [
    "default-src 'self'",
    "script-src 'self'",
    "style-src 'self'",
    "img-src 'self' data: blob:",
    "font-src 'self'",
    "connect-src 'self'",
    "frame-ancestors 'none'",
    "base-uri 'self'",
    "form-action 'self'",
]));
```

**If using Tailwind CDN (dev only):**
```php
// In production, build Tailwind locally:
// npm install -D tailwindcss
// npx tailwindcss init
// Import in resources/css/app.css instead of CDN
```

---

<a name="v009"></a>
## 🟠 V-009: SMS SERVICE FAILS OPEN

### Severity
**HIGH** — CVSS 5.0 | CWE-754

### The Problem
When SMS provider is not configured, `send()` returns `true` — the system believes SMS was sent when it wasn't.

### Affected File
`app/Services/SmsService.php` (lines 42-44)

### What To Do
```diff
if (!$this->enabled) {
-   Log::info("SMS (disabled/sandbox): To={$phone} | Message={$message}");
-   return true;
+   Log::error("SMS NOT SENT — provider not configured. Phone hash: " . hash('sha256', $phone));
+   return false;
}
```

---

<a name="v010"></a>
## 🟡 V-010: ACCOUNT ENUMERATION VIA LOGS

### Severity
**MEDIUM** — CVSS 6.5 | CWE-204

### The Problem
Raw phone numbers and email addresses logged at INFO level in password reset flow.

### Affected File
`app/Http/Controllers/Auth/ForgotPasswordController.php`

### What To Do

Replace ALL instances of:
```diff
- Log::info('Password reset link requested via email.', [
-     'email' => $validated['email'],
+ Log::info('Password reset link requested.', [
+     'email_hash' => hash('sha256', $validated['email'] ?? ''),
});

- Log::info('Phone password reset completed ...', [
-     'phone_hash' => $phoneHash,
+ Log::info('Password reset completed ...', [
+     // Remove phone_hash — user_id is sufficient
]);

- Log::info('Temporary password SMS sent.', [
-     'phone_hash' => $phoneHash,
+ Log::info('Temporary password SMS processed.', [
+     'user_id' => $user->id,
]);
```

---

<a name="v011"></a>
## 🟡 V-011: GUEST SEARCH LEAKS PII

### Severity
**MEDIUM** — CVSS 6.0 | CWE-639, CWE-200

### The Problem
Any supervisor/front_desk/manager can search ALL guests' phone numbers and emails without restriction.

### Affected File
`app/Http/Controllers/GuestController.php` (lines 225-237)

### What To Do
```diff
public function search(Request $request)
{
    $search = str_replace(['%', '_'], ['\%', '\_'], $request->get('q', ''));

    $guests = Guest::where(function ($query) use ($search) {
        $query->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
-             ->orWhere('email', 'like', "%{$search}%")
-             ->orWhere('phone_number', 'like', "%{$search}%");
    })
+   // Only show guests the user has interacted with
+   ->whereHas('bookings', function ($q) {
+       if (!auth()->user()->isAdmin() && !auth()->user()->isGeneralManager()) {
+           $q->where('created_by', auth()->id());
+       }
+   })
    ->limit(10)
-   ->get(['id', 'first_name', 'last_name', 'email', 'phone_number']);
+   ->get(['id', 'first_name', 'last_name']);

    return response()->json($guests->map(function ($guest) {
        return [
            'id' => $guest->id,
            'name' => $guest->full_name,
-           'email' => $guest->email,
-           'phone' => $guest->phone_number,
        ];
    }));
}
```

---

<a name="v012"></a>
## 🟡 V-012: MISSING MODEL-LEVEL AUTHORIZATION

### Severity
**MEDIUM** — CVSS 5.5 | CWE-862

### The Problem
All Booking controllers rely on role middleware only — no per-booking ownership check. Any front_desk user can edit anyone's booking.

### What To Do

**Create `app/Policies/BookingPolicy.php`:**
```php
<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() || $user->isGeneralManager()) {
            return true;
        }
        return (string) $user->id === (string) $booking->created_by;
    }

    public function create(User $user): bool
    {
        return true; // role middleware handles this
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->isGeneralManager();
    }
}
```

**Register in `app/Providers/AuthServiceProvider.php`:**
```php
use App\Models\Booking;
use App\Policies\BookingPolicy;

protected $policies = [
    Booking::class => BookingPolicy::class,
];
```

**Use in controllers:**
```php
public function show(Booking $booking)
{
    $this->authorize('view', $booking);
    // ... rest of method
}

public function update(Request $request, Booking $booking)
{
    $this->authorize('update', $booking);
    // ... rest of method
}
```

---

<a name="v013"></a>
## 🟡 V-013: INSECURE SESSION CONFIGURATION

### Severity
**MEDIUM** — CVSS 4.5 | CWE-613

### The Problem
Session data stored unencrypted in database. 2-hour idle timeout.

### Affected File
`.env` (lines 31-34)

### What To Do
```diff
- SESSION_LIFETIME=120
+ SESSION_LIFETIME=30
- SESSION_ENCRYPT=false
+ SESSION_ENCRYPT=true
+ SESSION_SECURE_COOKIE=true
+ SESSION_SAME_SITE=strict
```

Then config cache:
```bash
php artisan config:cache
```

---

<a name="v014"></a>
## 🟡 V-014: WEAK TEMPORARY PASSWORDS

### Severity
**MEDIUM** — CVSS 4.0 | CWE-330, CWE-521

### The Problem
Temp password has fixed pattern: `8_random_letters + AB + 12 + !` — predictable structure.

### Affected File
`app/Http/Controllers/Auth/ForgotPasswordController.php` (lines 146-149)

### What To Do
```diff
private function generateTemporaryPassword(): string
{
-   return Str::random(8) . Str::upper(Str::random(2)) . random_int(10, 99) . '!';
+   // 16-char fully random with all character types
+   $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
+   $password = '';
+   for ($i = 0; $i < 16; $i++) {
+       $password .= $chars[random_int(0, strlen($chars) - 1)];
+   }
+   return $password;
}
```

or simply use Laravel's built-in:
```php
return Str::password(16);
```

---

<a name="v015"></a>
## 🟡 V-015: PAYMENT CALLBACK RACE CONDITION

### Severity
**MEDIUM** — CVSS 5.5 | CWE-362

### The Problem
Payment callback can process duplicate requests simultaneously without database transaction lock.

### Affected File
`app/Http/Controllers/PaymentController.php` (lines 126-148)

### What To Do
```diff
+ use Illuminate\Support\Facades\DB;

public function callback(Request $request)
{
    $reference = $request->query('reference')
        ?? $request->query('session_id')
        ?? $request->query('token');

    if ($reference) {
+       $payment = DB::transaction(function () use ($reference) {
            $payment = Payment::where('provider_reference', $reference)
+               ->lockForUpdate()
                ->first();
            if ($payment && $payment->isPending()) {
                $payment = $this->engine->verify($payment);
            }
            return $payment;
+       });
    }
    ...
}
```

---

<a name="v016"></a>
## 🔵 V-016: MISSING ACCOUNT LOCKOUT

### Severity
**LOW** — CVSS 3.0 | CWE-307

### The Problem
No account lockout after failed login attempts. Brute-force across multiple IPs bypasses rate limiting.

### What To Do

**Step 1 — Create migration:**
```bash
php artisan make:migration add_login_attempts_to_users
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->integer('login_attempts')->default(0)->after('is_active');
    $table->timestamp('locked_until')->nullable()->after('login_attempts');
});
```

**Step 2 — Update LoginController:**

```php
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    // Check lockout
    if ($user && $user->locked_until && $user->locked_until->isFuture()) {
        $minutes = now()->diffInMinutes($user->locked_until);
        return back()->withErrors([
            'email' => "Account locked for {$minutes} minutes.",
        ]);
    }

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        if ($user) {
            $user->forceFill(['login_attempts' => 0, 'locked_until' => null])->save();
        }
        return redirect()->intended();
    }

    // Increment failed attempts
    if ($user) {
        $attempts = $user->login_attempts + 1;
        $user->forceFill([
            'login_attempts' => $attempts,
            'locked_until' => $attempts >= 10 ? now()->addMinutes(30) : null,
        ])->save();
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
}
```

---

<a name="v017"></a>
## 🔵 V-017: MISSING RATE LIMITING ON SETTINGS

### Severity
**LOW** — CVSS 2.5 | CWE-770

### The Problem
Admin settings endpoints have no rate limiting — brute-forceable.

### Affected File
`routes/web.php` (lines 739-747)

### What To Do
```diff
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
-   Route::post('settings', [SettingsController::class, 'updateSettings'])->name('settings.update');
+   Route::post('settings', [SettingsController::class, 'updateSettings'])->name('settings.update')
+       ->middleware('throttle:10,1');
-   Route::post('settings/sms', [SettingsController::class, 'updateSmsSettings'])->name('settings.sms');
+   Route::post('settings/sms', [SettingsController::class, 'updateSmsSettings'])->name('settings.sms')
+       ->middleware('throttle:5,1');
-   Route::post('settings/email', [SettingsController::class, 'updateEmailSettings'])->name('settings.email');
+   Route::post('settings/email', [SettingsController::class, 'updateEmailSettings'])->name('settings.email')
+       ->middleware('throttle:5,1');
-   Route::post('settings/snipe', [SettingsController::class, 'updateSnipeSettings'])->name('settings.snipe');
+   Route::post('settings/snipe', [SettingsController::class, 'updateSnipeSettings'])->name('settings.snipe')
+       ->middleware('throttle:5,1');
    Route::post('settings/password', ...);
});
```

---

<a name="v018"></a>
## 🔵 V-018: PII EXPOSED IN CONFIRMATION JOB LOGS

### Severity
**LOW** — CVSS 3.0 | CWE-532

### The Problem
Raw email addresses logged in booking confirmation job.

### Affected File
`app/Jobs/SendBookingConfirmationJob.php`

### What To Do
```diff
Log::info("Booking confirmation email sent", [
    'reference' => $this->booking['reference'],
-   'email' => $this->booking['email'],
+   'email_hash' => hash('sha256', $this->booking['email'] ?? ''),
});

Log::info("Booking confirmation SMS sent", [
    'reference' => $this->booking['reference'],
-   'phone' => $this->booking['phone'],
+   'phone_hash' => hash('sha256', $this->booking['phone'] ?? ''),
]);
```

---

## ✅ DEPLOYMENT VERIFICATION CHECKLIST

Run after every patch round:

```bash
#!/bin/bash
echo "=== SECURITY VERIFICATION ==="

# 1. Config cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Verify debug
grep -q "APP_DEBUG=false" .env && echo "✅ Debug OFF" || echo "❌ Debug ON!"

# 3. Verify HTTPS
grep -q "APP_URL=https://" .env && echo "✅ HTTPS configured" || echo "⚠️ HTTPS check"

# 4. Verify session encryption
grep -q "SESSION_ENCRYPT=true" .env && echo "✅ Sessions encrypted" || echo "❌ Sessions unencrypted!"

# 5. Check for secrets in tracked files
git grep -l "snp_\|SECRET_KEY\|PRIVATE_KEY" -- ':!.gitignore' ':!*.md' || echo "✅ No secrets tracked"

# 6. Run tests
php artisan test --quiet && echo "✅ Tests pass" || echo "❌ Tests failing!"

# 7. Check logs for errors
grep -c "CRITICAL\|EMERGENCY" storage/logs/laravel.log && echo "⚠️ Critical errors found" || echo "✅ No critical errors"
```

---

## 📊 PATCH PROGRESS TRACKER

| # | Vulnerability | Severity | Est. Time | Done? | Date |
|---|---|---|---|---|---|
| V-001 | Secrets in Git | CRITICAL | 1-2 hrs | ☐ | |
| V-002 | Debug Mode | CRITICAL | 5 min | ☐ | |
| V-003 | Mass Assignment | CRITICAL | 5 min | ☐ | |
| V-004 | Webhook Secret | CRITICAL | 30 min | ☐ | |
| V-005 | SQLite Production | HIGH | 2-4 hrs | ☐ | |
| V-006 | Predictable IDs | HIGH | 15 min | ☐ | |
| V-007 | Broadcast Abuse | HIGH | 30 min | ☐ | |
| V-008 | Weak CSP | HIGH | 1-2 hrs | ☐ | |
| V-009 | SMS Fail Open | HIGH | 10 min | ☐ | |
| V-010 | Account Enumeration | MEDIUM | 30 min | ☐ | |
| V-011 | Guest Search IDOR | MEDIUM | 30 min | ☐ | |
| V-012 | Missing Policies | MEDIUM | 1-2 hrs | ☐ | |
| V-013 | Session Config | MEDIUM | 5 min | ☐ | |
| V-014 | Weak Temp Passwords | MEDIUM | 10 min | ☐ | |
| V-015 | Race Condition | MEDIUM | 30 min | ☐ | |
| V-016 | Account Lockout | LOW | 30 min | ☐ | |
| V-017 | Rate Limiting | LOW | 15 min | ☐ | |
| V-018 | PII in Logs | LOW | 15 min | ☐ | |

**Total estimated time:** 12-21 hours

---

## 🔄 GIT CLEANUP — FULL COMMAND SEQUENCE

```bash
# ===== DO THIS FIRST, BEFORE ANY CODE CHANGES =====

# 1. Remove .env from tracking
git rm --cached .env
git add .gitignore
git commit -m "chore: remove .env from git tracking"

# 2. Purge .env from entire history
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

# 3. Clean up
git for-each-ref --format="%(refname)" refs/original/ | xargs -n 1 git update-ref -d
git reflog expire --expire=now --all
git gc --aggressive --prune=now

# 4. Force push (ONLY after coordinating with team!)
git push origin --force --all
git push origin --force --tags

# 5. Tell everyone to re-clone:
echo "ALL DEVELOPERS: run 'git fetch --all && git reset --hard origin/main'"
```

---

**Patching order:** V-001 → V-002 → V-003 → V-004 (critical first), then V-005 → V-006 → V-007 → V-008 → V-009 (high), then the rest (medium → low).

After each patch run: `php artisan config:cache && php artisan test`
