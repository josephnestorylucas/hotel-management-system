<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendCheckoutReminderJob;
use App\Models\Booking;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══ SCHEDULED TASKS ═══

// Send checkout reminders every day at 10:00 AM for guests checking out tomorrow
Schedule::call(function () {
    $tomorrow = now()->addDay()->toDateString();

    Booking::where('check_out_date', $tomorrow)
        ->where('status', 'checked_in')
        ->with(['guest', 'room'])
        ->get()
        ->each(function ($booking) {
            $data = [
                'guest_name'  => $booking->guest?->full_name ?? $booking->guest_name,
                'email'       => $booking->guest?->email ?? $booking->guest_email,
                'phone'       => $booking->guest?->phone_number ?? $booking->guest_phone,
                'room_number' => $booking->room?->room_number ?? '',
                'check_in'    => $booking->check_in_date,
                'check_out'   => $booking->check_out_date,
                'balance'     => $booking->total_amount ?? 0,
                'reference'   => $booking->booking_number,
            ];

            SendCheckoutReminderJob::dispatch($data)->onQueue('notifications');
        });
})->dailyAt('10:00')->name('send-checkout-reminders')->withoutOverlapping();
