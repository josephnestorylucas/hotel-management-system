<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendCheckoutReminderJob;
use App\Models\Booking;
use App\Models\GoodsReceivedNote;
use App\Models\LocalPurchaseOrderItem;
use App\Services\ProcurementIntegrationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('translations:verify', function () {
    $basePath = resource_path('lang/en');
    $targetPath = resource_path('lang/sw');

    $flatten = function (array $data, string $prefix = '') use (&$flatten): array {
        $keys = [];

        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $keys = array_merge($keys, $flatten($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        return $keys;
    };

    $readLocaleKeys = function (string $path) use ($flatten): array {
        $files = collect(File::allFiles($path))
            ->filter(fn ($file) => $file->getExtension() === 'php');

        $keys = [];

        foreach ($files as $file) {
            $group = $file->getFilenameWithoutExtension();
            $data = require $file->getRealPath();

            if (!is_array($data)) {
                continue;
            }

            foreach ($flatten($data, $group) as $key) {
                $keys[] = $key;
            }
        }

        sort($keys);

        return $keys;
    };

    $enKeys = $readLocaleKeys($basePath);
    $swKeys = $readLocaleKeys($targetPath);

    $missingInSw = array_values(array_diff($enKeys, $swKeys));
    $missingInEn = array_values(array_diff($swKeys, $enKeys));

    if (empty($missingInSw) && empty($missingInEn)) {
        $this->info('Translation key parity verified for en/sw.');
        return 0;
    }

    if (!empty($missingInSw)) {
        $this->error('Missing Swahili keys:');
        foreach ($missingInSw as $key) {
            $this->line(' - ' . $key);
        }
    }

    if (!empty($missingInEn)) {
        $this->error('Missing English keys:');
        foreach ($missingInEn as $key) {
            $this->line(' - ' . $key);
        }
    }

    return 1;
})->purpose('Verify en/sw translation key parity');

Artisan::command('procurement:audit-links {--fix-statuses}', function () {
    $confirmedGrns = GoodsReceivedNote::with(['items.lpoItem', 'lpo.items'])
        ->where('status', 'confirmed')
        ->get();

    $missingAccounting = $confirmedGrns->filter(fn ($grn) => ! $grn->accounting_journal_entry_id);
    $missingStockLinks = $confirmedGrns->filter(function ($grn) {
        return $grn->items->contains(fn ($item) => $item->product_id && ! $item->stock_movement_id);
    });

    $mismatchedReceivedQty = LocalPurchaseOrderItem::query()
        ->get()
        ->filter(function ($item) {
            $confirmedQty = $item->grnItems()
                ->whereHas('grn', fn ($q) => $q->where('status', 'confirmed'))
                ->sum('quantity_received');

            return abs((float) $item->received_quantity - (float) $confirmedQty) > 0.0001;
        });

    $this->info('Procurement integration audit complete.');
    $this->line('Confirmed GRNs: ' . $confirmedGrns->count());
    $this->line('Missing accounting links: ' . $missingAccounting->count());
    $this->line('Missing stock links: ' . $missingStockLinks->count());
    $this->line('Mismatched received quantities: ' . $mismatchedReceivedQty->count());

    if ($this->option('fix-statuses')) {
        $service = app(ProcurementIntegrationService::class);

        LocalPurchaseOrderItem::query()->each(function ($item) {
            $confirmedQty = $item->grnItems()
                ->whereHas('grn', fn ($q) => $q->where('status', 'confirmed'))
                ->sum('quantity_received');

            $item->update(['received_quantity' => round((float) $confirmedQty, 3)]);
        });

        $confirmedGrns->pluck('lpo')->filter()->unique('id')->each(function ($lpo) use ($service) {
            $service->syncLpoReceivingStatus($lpo->fresh('items'));
        });

        $this->info('Received quantities and LPO statuses repaired from confirmed GRNs.');
    }

    return 0;
})->purpose('Audit procurement, stock, and accounting integration links');

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
