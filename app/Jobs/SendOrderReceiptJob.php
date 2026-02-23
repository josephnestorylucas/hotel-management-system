<?php

namespace App\Jobs;

use App\Mail\OrderReceiptMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public array $order) {}

    public function handle(): void
    {
        if (!empty($this->order['email'])) {
            Mail::to($this->order['email'])->send(new OrderReceiptMail($this->order));
        }
    }
}
