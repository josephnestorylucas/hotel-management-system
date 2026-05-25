<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attendance extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'event_pass_id',
        'pass_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'job_title',
        'guest_id',
        'registration_type',
        'ticket_number',
        'qr_token',
        'manual_code',
        'registration_date',
        'registration_status',
        'dietary_requirements',
        'special_accommodations',
        'notes',
        'guest_metadata',
        'first_check_in_at',
        'last_check_in_at',
        'total_check_ins',
        'badge_printed_at',
        'data_shared_consent',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'first_check_in_at' => 'datetime',
        'last_check_in_at' => 'datetime',
        'badge_printed_at' => 'datetime',
        'guest_metadata' => 'array',
        'data_shared_consent' => 'boolean',
        'total_check_ins' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attendance) {
            if (empty($attendance->ticket_number)) {
                $attendance->ticket_number = self::generateTicketNumber($attendance->event_id);
            }
            if (empty($attendance->qr_token)) {
                $attendance->qr_token = Str::random(64);
            }
            if (empty($attendance->manual_code)) {
                $attendance->manual_code = strtoupper(Str::random(8));
            }
            if (empty($attendance->registration_date)) {
                $attendance->registration_date = now();
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function eventPass(): BelongsTo
    {
        return $this->belongsTo(EventPass::class, 'event_pass_id');
    }

    public function eventTicket(): BelongsTo
    {
        return $this->belongsTo(EventPass::class, 'event_pass_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('registration_status', 'confirmed');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('total_check_ins', '>', 0);
    }

    public function scopeNoShow($query)
    {
        return $query->where('registration_status', 'no_show');
    }

    public function scopeByRegistrationType($query, string $type)
    {
        return $query->where('registration_type', $type);
    }

    public function scopeNoGuest($query)
    {
        return $query->whereNull('guest_id');
    }

    public function scopeHasGuest($query)
    {
        return $query->whereNotNull('guest_id');
    }

    // Methods
    public function checkIn(?string $scheduleId = null, string $method = 'qr_scan', ?string $staffId = null): CheckIn
    {
        $checkIn = $this->checkIns()->create([
            'event_schedule_id' => $scheduleId,
            'check_in_type' => 'entry',
            'check_in_method' => $method,
            'staff_id' => $staffId,
            'ip_address' => request()->ip(),
            'device_info' => request()->userAgent(),
        ]);

        $this->increment('total_check_ins');
        $this->update([
            'last_check_in_at' => now(),
            'first_check_in_at' => $this->first_check_in_at ?? now(),
        ]);

        return $checkIn;
    }

    public function markAsNoShow(): bool
    {
        if ($this->registration_status !== 'confirmed') return false;
        return (bool) $this->update(['registration_status' => 'no_show']);
    }

    public function linkToGuest(string $guestId): bool
    {
        return (bool) $this->update(['guest_id' => $guestId]);
    }

    public function hasCheckedIn(): bool
    {
        return $this->total_check_ins > 0;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getQrCodeDataAttribute(): string
    {
        return $this->qr_token;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['full_name'] = $this->full_name;
        $array['event_title'] = $this->event?->title;
        $array['pass_tier_name'] = $this->eventPass?->tier_name;
        $array['pass_type'] = $this->pass_type;
        return $array;
    }

    public static function generateTicketNumber(?string $eventId = null): string
    {
        $prefix = 'EVT';
        if ($eventId) {
            $event = Event::find($eventId);
            if ($event) {
                $prefix = strtoupper(substr($event->slug, 0, 6));
            }
        }
        $year = date('Y');
        $sequence = str_pad(self::where('event_id', $eventId)->count() + 1, 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$sequence}";
    }
}
