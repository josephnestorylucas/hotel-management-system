<?php

namespace App\Jobs;

use App\Mail\LaundryReadyMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLaundryReadyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public array $data) {}

    public function handle(SmsService $smsService): void
    {
        // Send email if guest has email
        if (!empty($this->data['email'])) {
            Mail::to($this->data['email'])->send(new LaundryReadyMail($this->data));
        }

        // Send SMS
        if (!empty($this->data['phone'])) {
            if (!empty($this->data['room_number'])) {
                // Hotel guest
                $msg = "Grand Hotel Laundry: Your order {$this->data['order_number']} is ready " .
                       "and will be delivered to Room {$this->data['room_number']} shortly.";
            } else {
                // Walk-in
                $msg = "Grand Hotel Laundry: Your order {$this->data['order_number']} is ready " .
                       "for collection. See you soon!";
            }
            $smsService->send($this->data['phone'], $msg);
        }
    }
}
