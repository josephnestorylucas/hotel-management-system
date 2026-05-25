<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMetrics extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'metric_date',
        'total_registrations',
        'confirmed_registrations',
        'checked_in_count',
        'no_show_count',
        'cancellations',
        'avg_check_in_minutes',
        'peak_check_in_hour',
        'total_revenue_collected',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'total_revenue_collected' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function computeForEvent(Event $event): self
    {
        $attendances = $event->attendances();
        $today = today();

        $totalRegistrations = $attendances->count();
        $confirmedRegistrations = $attendances->where('registration_status', 'confirmed')->count();
        $checkedInCount = $attendances->where('total_check_ins', '>', 0)->count();
        $noShowCount = $attendances->where('registration_status', 'no_show')->count();
        $cancellations = $attendances->where('registration_status', 'cancelled')->count();

        $checkInTimes = $attendances->whereNotNull('first_check_in_at')
            ->pluck('first_check_in_at')
            ->map(fn($t) => \Carbon\Carbon::parse($t)->hour);

        $peakHour = $checkInTimes->isNotEmpty()
            ? $checkInTimes->countBy()->sortDesc()->keys()->first()
            : null;

        // Calculate average check-in time in minutes from midnight
        $checkInMinutes = $attendances->whereNotNull('first_check_in_at')
            ->pluck('first_check_in_at')
            ->map(fn($t) => $c = \Carbon\Carbon::parse($t); return $c->hour * 60 + $c->minute);

        $avgCheckInMinutes = $checkInMinutes->isNotEmpty()
            ? (int) $checkInMinutes->avg()
            : null;

        $billing = $event->calculateBilling();
        $revenue = $billing['grand_total'];

        return self::updateOrCreate(
            ['event_id' => $event->id, 'metric_date' => $today],
            [
                'total_registrations' => $totalRegistrations,
                'confirmed_registrations' => $confirmedRegistrations,
                'checked_in_count' => $checkedInCount,
                'no_show_count' => $noShowCount,
                'cancellations' => $cancellations,
                'avg_check_in_minutes' => $avgCheckInMinutes,
                'peak_check_in_hour' => $peakHour,
                'total_revenue_collected' => $revenue,
            ]
        );
    }
}
