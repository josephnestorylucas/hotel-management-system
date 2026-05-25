<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasUuid;

    protected $fillable = [
        'organization_id',
        'conference_type_id',
        'title',
        'slug',
        'description',
        'logo_path',
        'start_date',
        'end_date',
        'theme_color',
        'status',
        'visibility',
        'capacity',
        'organizer_id',
        'expected_attendance',
        'actual_attendance',
        'total_revenue',
        'discount_percent',
        'discount_amount',
        'discount_reason',
        'hall_rate_total',
        'event_rate_total',
        'subtotal',
        'grand_total',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_attendance' => 'integer',
        'total_revenue' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'hall_rate_total' => 'decimal:2',
        'event_rate_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });

        static::saving(function ($event) {
            if (empty($event->slug) || $event->isDirty('title')) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function conferenceType(): BelongsTo
    {
        return $this->belongsTo(ConferenceType::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'organizer_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EventSchedule::class);
    }

    public function passes(): HasMany
    {
        return $this->hasMany(EventPass::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventPass::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(EventVenue::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function checkIns(): HasManyThrough
    {
        return $this->hasManyThrough(CheckIn::class, Attendance::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(EventStaff::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(AttendanceMetrics::class);
    }

    public function conferenceBookings(): HasMany
    {
        return $this->hasMany(ConferenceBooking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'ongoing']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')->where('start_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByOrganization($query, $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    // Computed Properties
    public function getActualAttendanceCountAttribute(): int
    {
        return $this->attendances()->where('registration_status', 'confirmed')
            ->where('total_check_ins', '>', 0)->count();
    }

    public function getTotalRevenueAmountAttribute(): float
    {
        return (float) $this->grand_total ?: (float) $this->total_revenue;
    }

    public function calculateBilling(): array
    {
        $hallTotal = 0;
        foreach ($this->venues as $venue) {
            if ($venue->booking && $venue->booking->total_cost) {
                $hallTotal += (float) $venue->booking->total_cost;
            } elseif ($venue->conferenceHall) {
                $hours = $this->days_count * 8;
                $hallTotal += $hours * (float) $venue->conferenceHall->hourly_rate;
            }
        }

        $eventRate = (float) ($this->metadata['event_rate'] ?? 0);
        $eventTotal = $eventRate;

        $subtotal = $hallTotal + $eventTotal;

        $discountPercent = (float) ($this->discount_percent ?? 0);
        $discountAmount = $subtotal * ($discountPercent / 100);

        $grandTotal = $subtotal - $discountAmount;

        return [
            'hall_rate_total' => $hallTotal,
            'event_rate_total' => $eventTotal,
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ];
    }

    public function applyDiscount(float $percent, ?string $reason = null): bool
    {
        $billing = $this->calculateBilling();
        $billing['discount_percent'] = $percent;
        $billing['discount_amount'] = $billing['subtotal'] * ($percent / 100);
        $billing['grand_total'] = $billing['subtotal'] - $billing['discount_amount'];

        return $this->update([
            'discount_percent' => $percent,
            'discount_amount' => $billing['discount_amount'],
            'discount_reason' => $reason,
            'hall_rate_total' => $billing['hall_rate_total'],
            'event_rate_total' => $billing['event_rate_total'],
            'subtotal' => $billing['subtotal'],
            'grand_total' => $billing['grand_total'],
        ]);
    }

    public function getSessionCountAttribute(): int
    {
        return $this->schedules()->count();
    }

    public function getDaysCountAttribute(): int
    {
        if (!$this->end_date) return 1;
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // State Transitions
    public function publish(): bool
    {
        if ($this->status !== 'draft') return false;
        return $this->update(['status' => 'scheduled']);
    }

    public function start(): bool
    {
        if ($this->status !== 'scheduled') return false;
        return $this->update(['status' => 'ongoing']);
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['scheduled', 'ongoing'])) return false;
        return $this->update(['status' => 'completed']);
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['completed'])) return false;
        return $this->update(['status' => 'cancelled']);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['scheduled', 'ongoing']);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
