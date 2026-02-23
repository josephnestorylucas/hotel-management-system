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

    public int $timeout = 300;

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
                    'guest_name' => $guest->full_name,
                    'subject'    => $this->broadcast->title,
                    'title'      => $this->broadcast->title,
                    'body'       => $this->broadcast->body,
                    'type'       => $this->broadcast->type,
                ]));
                $emailCount++;
            }

            // SMS
            if (in_array($this->broadcast->channels, ['sms', 'both']) && $guest->phone_number) {
                $message = $this->broadcast->sms_message ?? substr($this->broadcast->body, 0, 155);
                SendSmsJob::dispatch($guest->phone_number, "Grand Hotel: {$message}")
                    ->onQueue('notifications');
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
        $query = Guest::query();

        return match ($this->broadcast->target) {
            'Silver'   => $query->whereIn('loyalty_tier', ['Silver', 'Gold', 'Platinum'])->get(),
            'Gold'     => $query->whereIn('loyalty_tier', ['Gold', 'Platinum'])->get(),
            'Platinum' => $query->where('loyalty_tier', 'Platinum')->get(),
            default    => $query->get(),
        };
    }
}
