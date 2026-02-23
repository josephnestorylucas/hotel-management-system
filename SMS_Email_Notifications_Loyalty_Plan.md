# 📱📧 SMS · Email · Notifications · Loyalty Program
### Implementation Plan — Laravel + Blade · Your Hotel System

> Covers: SMS notifications, email receipts, customer alerts, offers & events,
> loyalty program ranking (Silver, Gold, Platinum), and the booking discount audit trail.
> All built on your existing roles, Blade views, and standard Laravel folder structure.

---

## 📋 Table of Contents

1. [Overview — What We Are Building](#1-overview)
2. [Technology Stack Decisions](#2-technology-stack)
3. [File Map](#3-file-map)
4. [Phase 1 — Notifications Foundation](#4-phase-1--notifications-foundation)
5. [Phase 2 — Email Receipts](#5-phase-2--email-receipts)
6. [Phase 3 — SMS Notifications](#6-phase-3--sms-notifications)
7. [Phase 4 — Loyalty Program](#7-phase-4--loyalty-program)
8. [Phase 5 — Offers & Events Broadcasts](#8-phase-5--offers--events-broadcasts)
9. [Phase 6 — Discount Audit Trail](#9-phase-6--discount-audit-trail)
10. [Migrations](#10-migrations)
11. [Build Order & Checklist](#11-build-order--checklist)

---

## 1. Overview

### What Gets Sent, When, and To Whom

| Trigger | Channel | Recipient | Template |
|---|---|---|---|
| Booking confirmed | Email + SMS | Guest | Booking confirmation with details |
| Booking cancelled | Email + SMS | Guest | Cancellation notice |
| Check-in today | SMS | Guest | Welcome + room number |
| Check-out tomorrow | Email + SMS | Guest | Check-out reminder + total balance |
| Laundry order ready | SMS | Guest (room) / Walk-in (phone) | Ready for collection/delivery |
| Restaurant order settled | Email | Guest | Receipt |
| Invoice / Final bill | Email | Guest | PDF receipt at checkout |
| Loyalty rank upgrade | Email + SMS | Guest | Congratulations + new tier benefits |
| Offer or event | Email + SMS | All guests / filtered tier | Promotional broadcast |
| Discount applied | Email | STORE_MANAGER + SUPERVISOR | Audit notification |
| Low stock alert | In-app | STORE_MANAGER | Already built — extend to email |

---

## 2. Technology Stack

### Email — Laravel Mail + Mailtrap (dev) / SMTP (prod)

```
Laravel built-in Mail facade
Mailable classes → resources/views/emails/
Queue-based sending via Laravel Jobs
```

### SMS — Africa's Talking (Tanzania/East Africa) or Twilio

```
Africa's Talking recommended for TZ — cheaper, local support
Package: africastalking/africastalking
Fallback: nexmo/vonage or Twilio
Send via Laravel Jobs (queued — never block the request)
```

### Push / In-App Notifications

```
Already partially built via store_notifications table
Extend to a proper notification bell in all module layouts
```

### Queues

```
Laravel Queue with database driver (start simple)
Upgrade to Redis when volume grows
php artisan queue:work
```

### PDF Receipts

```
barryvdh/laravel-dompdf
Renders Blade view → PDF → attached to email or downloadable
```

---

## 3. File Map

```
app/
├── Http/
│   └── Controllers/
│       ├── NotificationController.php          ← in-app notification bell
│       └── Admin/
│           ├── BroadcastController.php         ← send offers/events to guests
│           └── AuditController.php             ← discount audit log
│
├── Mail/                                       ← Laravel Mailable classes
│   ├── BookingConfirmedMail.php
│   ├── BookingCancelledMail.php
│   ├── CheckoutReminderMail.php
│   ├── InvoiceMail.php
│   ├── LaundryReadyMail.php
│   ├── OrderReceiptMail.php
│   ├── LoyaltyUpgradeMail.php
│   └── BroadcastMail.php
│
├── Jobs/                                       ← queued workers
│   ├── SendSmsJob.php
│   ├── SendBookingConfirmationJob.php
│   ├── SendCheckoutReminderJob.php
│   └── SendBroadcastJob.php
│
├── Services/                                   ← one new directory allowed for services
│   └── SmsService.php                          ← wraps Africa's Talking SDK
│
└── Models/
    ├── Guest.php                               ← guest profile with loyalty fields
    ├── LoyaltyTransaction.php                  ← points ledger
    ├── Broadcast.php                           ← stored offers/events
    └── DiscountAudit.php                       ← discount audit log

database/
└── migrations/
    ├── xxxx_create_guests_table.php
    ├── xxxx_create_loyalty_transactions_table.php
    ├── xxxx_create_broadcasts_table.php
    └── xxxx_create_discount_audits_table.php

resources/
└── views/
    ├── emails/                                 ← all email Blade templates
    │   ├── layout.blade.php                    ← base email layout
    │   ├── booking-confirmed.blade.php
    │   ├── booking-cancelled.blade.php
    │   ├── checkout-reminder.blade.php
    │   ├── invoice.blade.php
    │   ├── laundry-ready.blade.php
    │   ├── order-receipt.blade.php
    │   ├── loyalty-upgrade.blade.php
    │   └── broadcast.blade.php
    │
    └── admin/
        ├── broadcasts/
        │   ├── index.blade.php
        │   └── create.blade.php
        └── audit/
            └── index.blade.php

routes/
└── web.php                                     ← add notification + admin routes
```

---

## 4. Phase 1 — Notifications Foundation

### What to build first:
Extend the existing `store_notifications` table and wire up the notification bell across all module layouts (store, restaurant, laundry).

---

### Update `store_notifications` table — add `is_emailed` and `is_sms_sent` columns

```php
// database/migrations/xxxx_add_channels_to_store_notifications_table.php

Schema::table('store_notifications', function (Blueprint $table) {
    $table->boolean('is_emailed')->default(false)->after('is_read');
    $table->boolean('is_sms_sent')->default(false)->after('is_emailed');
});
```

---

### `app/Http/Controllers/NotificationController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\StoreNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    // GET /notifications — list all for current user
    public function index(): \Illuminate\View\View
    {
        $notifications = StoreNotification::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(30);

        // Mark all as read
        StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    // GET /notifications/unread-count  (called by navbar JS)
    public function unreadCount(): JsonResponse
    {
        $count = StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // POST /notifications/{notification}/read
    public function markRead(StoreNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->update(['is_read' => true]);

        return redirect($notification->action_url ?? route('dashboard'));
    }
}
```

---

### Notification bell — add to every layout navbar

Add this snippet inside the `<nav>` of `store/layout.blade.php`, `restaurant/layout.blade.php`, and `laundry/layout.blade.php`:

```blade
{{-- Notification Bell --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative text-gray-600 hover:text-blue-600">
        🔔
        <span id="notif-badge"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full
                     w-4 h-4 flex items-center justify-center hidden">
            0
        </span>
    </button>

    <div x-show="open" @click.away="open = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded shadow-lg border z-50 max-h-96 overflow-y-auto">
        @foreach(auth()->user()->latestNotifications as $notif)
        <a href="{{ route('notifications.read', $notif) }}"
           class="block px-4 py-3 border-b hover:bg-gray-50 {{ $notif->is_read ? 'opacity-60' : 'font-medium' }}">
            <div class="text-sm text-gray-800">{{ $notif->title }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $notif->body }}</div>
            <div class="text-xs text-gray-300 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
        </a>
        @endforeach
        <a href="{{ route('notifications.index') }}"
           class="block px-4 py-3 text-center text-xs text-blue-600 hover:bg-blue-50">
            View all notifications
        </a>
    </div>
</div>

<script>
    // Poll for unread count every 30 seconds
    function fetchNotifCount() {
        fetch('/notifications/unread-count')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notif-badge');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
    }
    fetchNotifCount();
    setInterval(fetchNotifCount, 30000);
</script>
```

Add `latestNotifications` to `User` model:

```php
// app/Models/User.php
public function latestNotifications()
{
    return $this->hasMany(\App\Models\StoreNotification::class)
                ->latest('created_at')
                ->limit(10);
}
```

---

## 5. Phase 2 — Email Receipts

### Install dependencies

```bash
composer require barryvdh/laravel-dompdf
```

Configure `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io      # dev
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourhotel.com
MAIL_FROM_NAME="Grand Hotel"
```

---

### Base email layout — `resources/views/emails/layout.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1e3a5f; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.8; }
        .body { padding: 32px; }
        .footer { background: #f5f5f5; padding: 16px 32px; font-size: 12px; color: #999; text-align: center; }
        .btn { display: inline-block; background: #1e3a5f; color: #fff; padding: 10px 20px;
               border-radius: 4px; text-decoration: none; font-size: 14px; margin-top: 16px; }
        table.details { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.details th { background: #f5f5f5; text-align: left; padding: 8px 12px; font-size: 13px; }
        table.details td { padding: 8px 12px; font-size: 13px; border-bottom: 1px solid #eee; }
        .highlight { background: #f0f7ff; border-left: 4px solid #1e3a5f; padding: 12px 16px; margin: 16px 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🏨 Grand Hotel</h1>
        <p>Your comfort is our priority</p>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        Grand Hotel · P.O. Box 000, Dar es Salaam, Tanzania · +255 xxx xxx xxx<br>
        This email was sent to {{ $email ?? 'you' }}. If you have any questions, contact our front desk.
    </div>
</div>
</body>
</html>
```

---

### `app/Mail/BookingConfirmedMail.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $booking) {}

    public function build(): self
    {
        return $this
            ->subject("Booking Confirmed — {$this->booking['reference']}")
            ->view('emails.booking-confirmed');
    }
}
```

### `resources/views/emails/booking-confirmed.blade.php`

```blade
@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $booking['guest_name'] }}</strong>,</p>
<p>Your reservation at <strong>Grand Hotel</strong> has been confirmed. Here are your booking details:</p>

<div class="highlight">
    <strong>Booking Reference: {{ $booking['reference'] }}</strong>
</div>

<table class="details">
    <tr><th>Room</th><td>{{ $booking['room_number'] }} — {{ $booking['room_type'] }}</td></tr>
    <tr><th>Check-in</th><td>{{ $booking['check_in'] }}</td></tr>
    <tr><th>Check-out</th><td>{{ $booking['check_out'] }}</td></tr>
    <tr><th>Nights</th><td>{{ $booking['nights'] }}</td></tr>
    <tr><th>Rate per Night</th><td>{{ number_format($booking['rate'], 2) }}</td></tr>
    <tr><th>Total</th><td><strong>{{ number_format($booking['total'], 2) }}</strong></td></tr>
    @if(!empty($booking['discount']))
    <tr><th>Discount Applied</th><td class="text-red-600">- {{ number_format($booking['discount'], 2) }}</td></tr>
    @endif
</table>

<p>We look forward to welcoming you. If you need to make any changes, please contact our front desk.</p>
@endsection
```

---

### `app/Mail/InvoiceMail.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $invoice) {}

    public function build(): self
    {
        return $this
            ->subject("Invoice — {$this->invoice['reference']} — Grand Hotel")
            ->view('emails.invoice')
            ->attachData(
                $this->generatePdf(),
                "invoice-{$this->invoice['reference']}.pdf",
                ['mime' => 'application/pdf']
            );
    }

    private function generatePdf(): string
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.invoice-pdf', ['invoice' => $this->invoice]);
        return $pdf->output();
    }
}
```

---

### `app/Mail/LoyaltyUpgradeMail.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyUpgradeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build(): self
    {
        return $this
            ->subject("Congratulations! You've reached {$this->data['tier']} status 🎉")
            ->view('emails.loyalty-upgrade');
    }
}
```

### `resources/views/emails/loyalty-upgrade.blade.php`

```blade
@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $data['guest_name'] }}</strong>,</p>

<div class="highlight">
    🎉 Congratulations! You have been upgraded to
    <strong>
        @if($data['tier'] === 'Silver') 🥈 Silver
        @elseif($data['tier'] === 'Gold') 🥇 Gold
        @else 💎 Platinum
        @endif
        Member
    </strong>
</div>

<p>
    You have earned <strong>{{ number_format($data['points']) }} points</strong>
    across your stays and purchases at Grand Hotel.
</p>

<table class="details">
    <tr><th>Your Tier</th><td><strong>{{ $data['tier'] }}</strong></td></tr>
    <tr><th>Total Points</th><td>{{ number_format($data['points']) }}</td></tr>
    <tr><th>Member Since</th><td>{{ $data['member_since'] }}</td></tr>
</table>

<p>As a <strong>{{ $data['tier'] }}</strong> member you now enjoy:</p>
<ul>
    @if($data['tier'] === 'Silver')
        <li>Priority check-in</li>
        <li>5% discount on room rate</li>
        <li>Free laundry — 3 items per stay</li>
    @elseif($data['tier'] === 'Gold')
        <li>Priority check-in and late checkout</li>
        <li>10% discount on room rate</li>
        <li>Free laundry — 5 items per stay</li>
        <li>Complimentary breakfast on arrival</li>
    @else
        <li>VIP check-in and guaranteed late checkout</li>
        <li>15% discount on room rate</li>
        <li>Unlimited complimentary laundry</li>
        <li>Daily complimentary breakfast</li>
        <li>Access to exclusive Platinum lounge</li>
    @endif
</ul>

<p>Thank you for choosing Grand Hotel. We look forward to your next visit.</p>
@endsection
```

---

### `app/Mail/BroadcastMail.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build(): self
    {
        return $this
            ->subject($this->data['subject'])
            ->view('emails.broadcast');
    }
}
```

---

## 6. Phase 3 — SMS Notifications

### Install Africa's Talking

```bash
composer require africastalking/africastalking
```

Configure `.env`:

```env
AT_USERNAME=your_username
AT_API_KEY=your_api_key
AT_SENDER_ID=GRANDHOTEL       # your registered sender ID
AT_ENV=sandbox                # change to production when live
```

---

### `app/Services/SmsService.php`

```php
<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $sms;

    public function __construct()
    {
        $at        = new AfricasTalking(config('services.africastalking.username'),
                                        config('services.africastalking.api_key'));
        $this->sms = $at->sms();
    }

    /**
     * Send a single SMS.
     *
     * @param string $phone  e.g. +255712345678
     * @param string $message
     */
    public function send(string $phone, string $message): bool
    {
        try {
            $phone = $this->normalizePhone($phone);

            $this->sms->send([
                'to'      => $phone,
                'message' => $message,
                'from'    => config('services.africastalking.sender_id'),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("SMS send failed to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send same message to multiple numbers.
     *
     * @param array  $phones  ['+255712345678', '+255723456789']
     * @param string $message
     */
    public function sendBulk(array $phones, string $message): void
    {
        $normalized = array_map([$this, 'normalizePhone'], $phones);

        $this->sms->send([
            'to'      => implode(',', $normalized),
            'message' => $message,
            'from'    => config('services.africastalking.sender_id'),
        ]);
    }

    // Normalize TZ numbers to international format
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '0')) {
            return '+255' . substr($phone, 1);
        }
        if (str_starts_with($phone, '255')) {
            return '+' . $phone;
        }
        return $phone;
    }
}
```

Add to `config/services.php`:

```php
'africastalking' => [
    'username'  => env('AT_USERNAME'),
    'api_key'   => env('AT_API_KEY'),
    'sender_id' => env('AT_SENDER_ID', 'GRANDHOTEL'),
],
```

---

### `app/Jobs/SendSmsJob.php`

```php
<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60; // retry after 60 seconds

    public function __construct(
        public string $phone,
        public string $message
    ) {}

    public function handle(SmsService $smsService): void
    {
        $smsService->send($this->phone, $this->message);
    }
}
```

---

### SMS Templates — use these strings throughout the system

```php
// Booking confirmation
$msg = "Grand Hotel: Booking CONFIRMED. Ref: {$ref}. Room {$room}. Check-in: {$checkIn}. Total: {$total}. Welcome!";

// Check-in day welcome
$msg = "Grand Hotel: Welcome {$name}! Your room {$room} is ready. Check-out: {$checkOut}. Enjoy your stay!";

// Check-out reminder (sent day before)
$msg = "Grand Hotel: Reminder — your check-out is TOMORROW {$date}. Balance due: {$balance}. Front desk: +255xxx.";

// Laundry ready — guest
$msg = "Grand Hotel Laundry: Your order {$orderNo} is ready and will be delivered to Room {$room} shortly.";

// Laundry ready — walk-in
$msg = "Grand Hotel Laundry: Your order {$orderNo} is ready for collection. Ref: {$orderNo}. See you soon!";

// Loyalty upgrade
$msg = "Grand Hotel: Congratulations {$name}! You've reached {$tier} Member status. Thank you for your loyalty!";

// Offer/event broadcast
$msg = "Grand Hotel: {$title} — {$shortMessage}. Valid: {$dates}. Book now or call +255xxx.";
```

---

### `app/Jobs/SendBookingConfirmationJob.php`

```php
<?php

namespace App\Jobs;

use App\Mail\BookingConfirmedMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $booking) {}

    public function handle(SmsService $smsService): void
    {
        // Send email
        if (!empty($this->booking['email'])) {
            Mail::to($this->booking['email'])->send(new BookingConfirmedMail($this->booking));
        }

        // Send SMS
        if (!empty($this->booking['phone'])) {
            $msg = "Grand Hotel: Booking CONFIRMED. Ref: {$this->booking['reference']}. " .
                   "Room {$this->booking['room_number']}. Check-in: {$this->booking['check_in']}. " .
                   "Welcome!";
            $smsService->send($this->booking['phone'], $msg);
        }
    }
}
```

**Dispatch after booking is created:**

```php
// In your BookingController::store()
SendBookingConfirmationJob::dispatch($booking)->onQueue('notifications');
```

---

### `app/Jobs/SendCheckoutReminderJob.php`

```php
<?php

namespace App\Jobs;

use App\Mail\CheckoutReminderMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCheckoutReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $booking) {}

    public function handle(SmsService $smsService): void
    {
        if (!empty($this->booking['email'])) {
            Mail::to($this->booking['email'])->send(new CheckoutReminderMail($this->booking));
        }

        if (!empty($this->booking['phone'])) {
            $msg = "Grand Hotel: Your check-out is TOMORROW {$this->booking['check_out']}. " .
                   "Outstanding balance: {$this->booking['balance']}. Front desk: +255xxx.";
            $smsService->send($this->booking['phone'], $msg);
        }
    }
}
```

**Schedule this in `app/Console/Kernel.php`:**

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Run daily at 10am — send checkout reminders for tomorrow's checkouts
    $schedule->call(function () {
        $tomorrow = now()->addDay()->toDateString();

        // Query your bookings table for checkouts tomorrow
        \DB::table('bookings')
            ->where('check_out_date', $tomorrow)
            ->where('status', 'occupied')
            ->get()
            ->each(function ($booking) {
                SendCheckoutReminderJob::dispatch((array) $booking)
                    ->onQueue('notifications');
            });
    })->dailyAt('10:00');
}
```

---

## 7. Phase 4 — Loyalty Program

### Loyalty Tiers

| Tier | Points Required | Benefits |
|---|---|---|
| None | 0 – 499 | Standard rate |
| Silver 🥈 | 500 – 1,999 | 5% room discount, priority check-in |
| Gold 🥇 | 2,000 – 4,999 | 10% room discount, late checkout, breakfast |
| Platinum 💎 | 5,000+ | 15% room discount, VIP treatment, lounge access |

### How Points Are Earned

| Action | Points Earned |
|---|---|
| 1 night stay | 100 points |
| Every 10,000 TZS spent at restaurant | 50 points |
| Every 10,000 TZS spent on laundry | 30 points |
| Loyalty referral | 200 points |

---

### Migration — `xxxx_create_guests_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('id_type', 50)->nullable();     // passport, national ID
            $table->string('id_number', 100)->nullable();

            // Loyalty
            $table->integer('loyalty_points')->default(0);
            $table->enum('loyalty_tier', ['none', 'Silver', 'Gold', 'Platinum'])->default('none');
            $table->timestamp('tier_upgraded_at')->nullable();

            // Totals (updated on each checkout)
            $table->integer('total_stays')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
```

---

### Migration — `xxxx_create_loyalty_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Immutable points ledger — never update, only add rows
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guest_id');
            $table->enum('type', ['earn', 'redeem', 'adjust']);
            $table->integer('points');                       // positive = earn, negative = redeem
            $table->integer('balance_after');
            $table->string('source', 100);                   // booking, restaurant, laundry, manual
            $table->uuid('reference_id')->nullable();        // booking_id, order_id, etc.
            $table->text('notes')->nullable();
            $table->uuid('created_by');
            $table->timestamp('created_at');

            $table->foreign('guest_id')->references('id')->on('guests');
            $table->index(['guest_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
```

---

### `app/Models/Guest.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Guest extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'email', 'phone', 'nationality', 'gender',
        'date_of_birth', 'id_type', 'id_number',
        'loyalty_points', 'loyalty_tier', 'tier_upgraded_at',
        'total_stays', 'total_spent', 'is_active', 'created_by',
    ];

    protected $casts = [
        'total_spent'      => 'decimal:2',
        'tier_upgraded_at' => 'datetime',
    ];

    /**
     * Add loyalty points, update tier, fire notifications if tier changes.
     */
    public function addPoints(int $points, string $source, ?string $referenceId = null, ?string $actorId = null): void
    {
        $oldTier = $this->loyalty_tier;

        $this->increment('loyalty_points', $points);
        $this->refresh();

        $newTier = $this->calculateTier($this->loyalty_points);

        // Update tier if changed
        if ($newTier !== $oldTier) {
            $this->update([
                'loyalty_tier'      => $newTier,
                'tier_upgraded_at'  => now(),
            ]);

            // Send upgrade notifications
            $this->sendTierUpgradeNotifications($newTier);
        }

        // Log the transaction
        LoyaltyTransaction::create([
            'guest_id'     => $this->id,
            'type'         => 'earn',
            'points'       => $points,
            'balance_after'=> $this->loyalty_points,
            'source'       => $source,
            'reference_id' => $referenceId,
            'created_by'   => $actorId ?? auth()->id(),
            'created_at'   => now(),
        ]);
    }

    public function calculateTier(int $points): string
    {
        if ($points >= 5000) return 'Platinum';
        if ($points >= 2000) return 'Gold';
        if ($points >= 500)  return 'Silver';
        return 'none';
    }

    private function sendTierUpgradeNotifications(string $tier): void
    {
        $data = [
            'guest_name'   => $this->name,
            'tier'         => $tier,
            'points'       => $this->loyalty_points,
            'member_since' => $this->created_at->format('d M Y'),
        ];

        // Email
        if ($this->email) {
            Mail::to($this->email)->queue(new \App\Mail\LoyaltyUpgradeMail($data));
        }

        // SMS
        if ($this->phone) {
            \App\Jobs\SendSmsJob::dispatch(
                $this->phone,
                "Grand Hotel: Congratulations {$this->name}! You've reached {$tier} Member status. Thank you for your loyalty! 🎉"
            )->onQueue('notifications');
        }
    }

    public function loyaltyTransactions() { return $this->hasMany(LoyaltyTransaction::class); }
    public function bookings()            { return $this->hasMany(\App\Models\Booking::class); }
}
```

---

### `app/Models/LoyaltyTransaction.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'guest_id', 'type', 'points', 'balance_after',
        'source', 'reference_id', 'notes', 'created_by', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function guest() { return $this->belongsTo(Guest::class); }
}
```

---

### Award points at key moments

```php
// After booking checkout — in BookingController::checkout()
$guest->addPoints(
    points: $booking->nights * 100,
    source: 'booking',
    referenceId: $booking->id
);

// After restaurant order settled — in OrderController::settle()
$pointsEarned = (int) floor($order->total / 10000) * 50;
if ($pointsEarned > 0) {
    $guest->addPoints($pointsEarned, 'restaurant', $order->id);
}

// After laundry settled — in LaundryOrderController::settle()
$pointsEarned = (int) floor($laundryOrder->total / 10000) * 30;
if ($pointsEarned > 0) {
    $guest->addPoints($pointsEarned, 'laundry', $laundryOrder->id);
}
```

---

## 8. Phase 5 — Offers & Events Broadcasts

### Migration — `xxxx_create_broadcasts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 200);
            $table->text('body');                            // full message for email
            $table->string('sms_message', 160)->nullable(); // 160 char SMS version
            $table->enum('type', ['offer', 'event', 'announcement']);
            $table->enum('target', [
                'all',        // all guests with contact info
                'Silver',     // loyalty tier Silver and above
                'Gold',       // loyalty tier Gold and above
                'Platinum',   // Platinum only
                'walkin',     // walk-in customers only
                'guests',     // hotel guests only
            ])->default('all');
            $table->enum('channels', ['email', 'sms', 'both'])->default('both');
            $table->timestamp('scheduled_at')->nullable();   // null = send immediately
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
```

---

### `app/Http/Controllers/Admin/BroadcastController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBroadcastJob;
use App\Models\Broadcast;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BroadcastController extends Controller
{
    // GET /admin/broadcasts
    public function index(): View
    {
        $broadcasts = Broadcast::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    // GET /admin/broadcasts/create
    public function create(): View
    {
        return view('admin.broadcasts.create');
    }

    // POST /admin/broadcasts
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string',
            'sms_message'  => 'nullable|string|max:160',
            'type'         => 'required|in:offer,event,announcement',
            'target'       => 'required|in:all,Silver,Gold,Platinum,walkin,guests',
            'channels'     => 'required|in:email,sms,both',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $broadcast = Broadcast::create([
            ...$data,
            'status'     => $data['scheduled_at'] ? 'scheduled' : 'draft',
            'created_by' => auth()->id(),
        ]);

        if ($request->action === 'send') {
            $this->dispatch($broadcast);
        }

        return redirect()
            ->route('admin.broadcasts.index')
            ->with('success', $request->action === 'send'
                ? "Broadcast is being sent."
                : "Broadcast saved as draft.");
    }

    // POST /admin/broadcasts/{broadcast}/send
    public function send(Broadcast $broadcast): RedirectResponse
    {
        abort_if($broadcast->status === 'sent', 422, 'Already sent.');
        $this->dispatch($broadcast);

        return redirect()
            ->route('admin.broadcasts.index')
            ->with('success', "Broadcast '{$broadcast->title}' queued for sending.");
    }

    private function dispatch(Broadcast $broadcast): void
    {
        $broadcast->update(['status' => 'sending']);
        SendBroadcastJob::dispatch($broadcast)->onQueue('broadcasts');
    }
}
```

---

### `app/Jobs/SendBroadcastJob.php`

```php
<?php

namespace App\Jobs;

use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\Guest;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes for bulk sends

    public function __construct(public Broadcast $broadcast) {}

    public function handle(SmsService $smsService): void
    {
        $guests = $this->getTargetGuests();

        $emailCount = 0;
        $smsCount   = 0;

        foreach ($guests as $guest) {

            // Email
            if (in_array($this->broadcast->channels, ['email', 'both']) && $guest->email) {
                Mail::to($guest->email)->queue(new BroadcastMail([
                    'guest_name' => $guest->name,
                    'subject'    => $this->broadcast->title,
                    'title'      => $this->broadcast->title,
                    'body'       => $this->broadcast->body,
                    'type'       => $this->broadcast->type,
                ]));
                $emailCount++;
            }

            // SMS
            if (in_array($this->broadcast->channels, ['sms', 'both']) && $guest->phone) {
                $message = $this->broadcast->sms_message ?? substr($this->broadcast->body, 0, 155);
                SendSmsJob::dispatch($guest->phone, "Grand Hotel: {$message}")->onQueue('notifications');
                $smsCount++;
            }
        }

        $this->broadcast->update([
            'status'           => 'sent',
            'sent_at'          => now(),
            'recipients_count' => $guests->count(),
        ]);
    }

    private function getTargetGuests()
    {
        $query = Guest::where('is_active', true);

        return match($this->broadcast->target) {
            'Silver'   => $query->whereIn('loyalty_tier', ['Silver', 'Gold', 'Platinum'])->get(),
            'Gold'     => $query->whereIn('loyalty_tier', ['Gold', 'Platinum'])->get(),
            'Platinum' => $query->where('loyalty_tier', 'Platinum')->get(),
            default    => $query->get(),
        };
    }
}
```

---

## 9. Phase 6 — Discount Audit Trail

### Migration — `xxxx_create_discount_audits_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->uuid('authorized_by');              // must be STORE_MANAGER, SUPERVISOR
            $table->decimal('discount_amount', 10, 2); // manual amount — not percentage
            $table->integer('valid_days');              // number of days discount applies
            $table->date('valid_from');
            $table->date('valid_until');                // valid_from + valid_days
            $table->text('reason')->nullable();
            $table->timestamp('authorized_at');         // exact timestamp for audit
            $table->timestamps();

            $table->foreign('authorized_by')->references('id')->on('users');
            $table->index(['booking_id', 'authorized_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_audits');
    }
};
```

---

### `app/Models/DiscountAudit.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DiscountAudit extends Model
{
    use HasUuids;

    protected $fillable = [
        'booking_id', 'authorized_by', 'discount_amount',
        'valid_days', 'valid_from', 'valid_until', 'reason', 'authorized_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'valid_from'      => 'date',
        'valid_until'     => 'date',
        'authorized_at'   => 'datetime',
    ];

    public function authorizer() { return $this->belongsTo(User::class, 'authorized_by'); }
}
```

---

### Where to create the audit record

```php
// In your BookingController::applyDiscount() — only SUPERVISOR or STORE_MANAGER can reach this
// Middleware: ->middleware('role:STORE_MANAGER,SUPERVISOR')

public function applyDiscount(Request $request, $bookingId): RedirectResponse
{
    $data = $request->validate([
        'discount_amount' => 'required|numeric|min:1',
        'valid_days'      => 'required|integer|min:1',
        'valid_from'      => 'required|date',
        'reason'          => 'required|string|min:10',
    ]);

    $validUntil = \Carbon\Carbon::parse($data['valid_from'])->addDays($data['valid_days']);

    DiscountAudit::create([
        'booking_id'      => $bookingId,
        'authorized_by'   => auth()->id(),
        'discount_amount' => $data['discount_amount'],
        'valid_days'      => $data['valid_days'],
        'valid_from'      => $data['valid_from'],
        'valid_until'     => $validUntil,
        'reason'          => $data['reason'],
        'authorized_at'   => now(),  // exact timestamp
    ]);

    // Notify STORE_MANAGER by email
    User::whereHas('role', fn($q) => $q->whereIn('name', ['STORE_MANAGER', 'SUPERVISOR']))
        ->where('id', '!=', auth()->id())  // don't notify the one who applied it
        ->get()
        ->each(fn($m) => Mail::to($m->email)->queue(new \App\Mail\DiscountAuditMail([
            'authorized_by'   => auth()->user()->name,
            'booking_id'      => $bookingId,
            'discount_amount' => $data['discount_amount'],
            'valid_days'      => $data['valid_days'],
            'reason'          => $data['reason'],
            'authorized_at'   => now()->format('d M Y H:i:s'),
        ])));

    return redirect()->back()->with('success', 'Discount applied and logged.');
}
```

---

### Audit log Blade view — `resources/views/admin/audit/index.blade.php`

```blade
@extends('store.layout')

@section('title', 'Discount Audit Log')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Discount Audit Log</h1>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Date & Time</th>
                <th class="px-4 py-3 text-left text-gray-600">Booking</th>
                <th class="px-4 py-3 text-left text-gray-600">Authorized By</th>
                <th class="px-4 py-3 text-right text-gray-600">Discount</th>
                <th class="px-4 py-3 text-center text-gray-600">Valid Days</th>
                <th class="px-4 py-3 text-left text-gray-600">Valid Period</th>
                <th class="px-4 py-3 text-left text-gray-600">Reason</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($audits as $audit)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                    {{ $audit->authorized_at->format('d M Y H:i:s') }}
                </td>
                <td class="px-4 py-3 font-mono text-xs">{{ $audit->booking_id }}</td>
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $audit->authorizer->name }}</div>
                    <div class="text-xs text-gray-400">{{ $audit->authorizer->role->name }}</div>
                </td>
                <td class="px-4 py-3 text-right font-bold text-red-600">
                    - {{ number_format($audit->discount_amount, 2) }}
                </td>
                <td class="px-4 py-3 text-center">{{ $audit->valid_days }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">
                    {{ $audit->valid_from->format('d M') }} — {{ $audit->valid_until->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">{{ $audit->reason }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No discount records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $audits->links() }}</div>
</div>
@endsection
```

---

## 10. Migrations

Run in this exact order:

```bash
php artisan make:migration add_channels_to_store_notifications_table
php artisan make:migration create_guests_table
php artisan make:migration create_loyalty_transactions_table
php artisan make:migration create_broadcasts_table
php artisan make:migration create_discount_audits_table
php artisan migrate
```

---

## 11. Build Order & Checklist

```
PHASE 1 — Notifications Bell (2 days)
  ✓ Add is_emailed, is_sms_sent to store_notifications
  ✓ NotificationController (index, unreadCount, markRead)
  ✓ Notification bell snippet added to store, restaurant, laundry layouts
  ✓ latestNotifications relationship on User model
  ✓ Test: unread count updates every 30 seconds

PHASE 2 — Email (3 days)
  ✓ Install barryvdh/laravel-dompdf
  ✓ Configure MAIL_* in .env
  ✓ Base email layout (resources/views/emails/layout.blade.php)
  ✓ BookingConfirmedMail + view
  ✓ CheckoutReminderMail + view
  ✓ InvoiceMail + PDF attachment
  ✓ LoyaltyUpgradeMail + view
  ✓ BroadcastMail + view
  ✓ Queue setup: php artisan queue:table → migrate → queue:work
  ✓ Test all templates in Mailtrap

PHASE 3 — SMS (2 days)
  ✓ Install africastalking/africastalking
  ✓ Configure AT_* in .env
  ✓ SmsService (send, sendBulk, normalizePhone)
  ✓ Add to config/services.php
  ✓ SendSmsJob
  ✓ SendBookingConfirmationJob (email + SMS together)
  ✓ SendCheckoutReminderJob + scheduler in Kernel.php
  ✓ Test with Africa's Talking sandbox

PHASE 4 — Loyalty Program (3 days)
  ✓ guests migration
  ✓ loyalty_transactions migration
  ✓ Guest model with addPoints(), calculateTier(), sendTierUpgradeNotifications()
  ✓ LoyaltyTransaction model
  ✓ Wire addPoints() into BookingController::checkout()
  ✓ Wire addPoints() into OrderController::settle()
  ✓ Wire addPoints() into LaundryOrderController::settle()
  ✓ Test: earn points → tier upgrade → email + SMS fires

PHASE 5 — Offers & Events (2 days)
  ✓ broadcasts migration
  ✓ Broadcast model
  ✓ BroadcastController (index, create, store, send)
  ✓ SendBroadcastJob with target filtering
  ✓ Blade views: admin/broadcasts/index, create
  ✓ Routes with role:STORE_MANAGER,LAUNDRY_MANAGER
  ✓ Test: send to Platinum only → only Platinum guests receive

PHASE 6 — Discount Audit (1 day)
  ✓ discount_audits migration
  ✓ DiscountAudit model
  ✓ applyDiscount() in BookingController
  ✓ Admin audit Blade view
  ✓ Route with role:STORE_MANAGER,SUPERVISOR only
  ✓ Test: apply discount → audit row created → email sent to other managers

TOTAL ESTIMATED: ~13 development days
```

---

### Routes to add in `routes/web.php`

```php
// Notifications
Route::middleware(['auth'])->group(function () {
    Route::get('notifications',                    [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count',       [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::post('notifications/{notification}/read',[NotificationController::class, 'markRead'])->name('notifications.read');
});

// Admin — Broadcasts & Audit (STORE_MANAGER only)
Route::middleware(['auth', 'role:STORE_MANAGER,LAUNDRY_MANAGER'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('broadcasts',                       [BroadcastController::class, 'index'])->name('broadcasts.index');
    Route::get('broadcasts/create',                [BroadcastController::class, 'create'])->name('broadcasts.create');
    Route::post('broadcasts',                      [BroadcastController::class, 'store'])->name('broadcasts.store');
    Route::post('broadcasts/{broadcast}/send',     [BroadcastController::class, 'send'])->name('broadcasts.send');
});

Route::middleware(['auth', 'role:STORE_MANAGER,SUPERVISOR'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('audit/discounts', [AuditController::class, 'discounts'])->name('audit.discounts');
});
```
