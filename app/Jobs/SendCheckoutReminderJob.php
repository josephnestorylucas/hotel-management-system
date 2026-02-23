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

    public int $tries = 3;

    public function __construct(public array $booking) {}

    public function handle(SmsService $smsService): void
    {
        if (!empty($this->booking['email'])) {
            Mail::to($this->booking['email'])->send(new CheckoutReminderMail($this->booking));
        }

        if (!empty($this->booking['phone'])) {
            $balance = number_format($this->booking['balance'] ?? 0, 2);
            $msg = "Grand Hotel: Your check-out is TOMORROW {$this->booking['check_out']}. " .
                   "Outstanding balance: TZS {$balance}. Front desk: +255xxx.";
            $smsService->send($this->booking['phone'], $msg);
        }
    }
}
