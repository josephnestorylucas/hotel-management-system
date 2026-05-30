<?php
// app/Models/Reservation.php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * Reservation = future room hold / intent.
 *
 * Statuses: pending → confirmed → cancelled | no_show | converted
 *
 * A reservation is converted into a Booking at check-in.
 * No billing or service charges attach here — only an estimated_amount quote.
 */
class Reservation extends Model {
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'reservation_number', 'room_id', 'guest_id', 'booking_id', 'guest_name', 'guest_phone', 'guest_email',
        'check_in_date', 'check_out_date', 'number_of_guests', 'status', 'estimated_amount', 'created_by'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'estimated_amount' => 'decimal:2',
        'deleted_at'    => 'datetime',
    ];

    // ─── Valid Statuses ────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW   = 'no_show';
    const STATUS_CONVERTED = 'converted';

    protected static function boot() {
        parent::boot();
        static::creating(function ($reservation) {
            $reservation->reservation_number = 'RES-' . strtoupper(Str::random(10));
        });
    }

    // ─── Relationships ─────────────────────────────────────────────

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo {
        return $this->belongsTo(Guest::class);
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The Booking created when this reservation was converted (checked in).
     */
    public function booking(): HasOne {
        return $this->hasOne(Booking::class, 'reservation_id');
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getGuestDisplayNameAttribute(): string
    {
        if ($this->guest) {
            return $this->guest->full_name;
        }
        return $this->guest_name ?? 'Unknown Guest';
    }

    public function getGuestDisplayPhoneAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->phone_number;
        }
        return $this->guest_phone;
    }

    public function getGuestDisplayEmailAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->email;
        }
        return $this->guest_email;
    }

    public function getNightsAttribute(): int
    {
        if ($this->check_in_date && $this->check_out_date) {
            return $this->check_in_date->diffInDays($this->check_out_date);
        }
        return 0;
    }

    // ─── Status Helpers ────────────────────────────────────────────

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isConfirmed(): bool  { return $this->status === self::STATUS_CONFIRMED; }
    public function isCancelled(): bool  { return $this->status === self::STATUS_CANCELLED; }
    public function isNoShow(): bool     { return $this->status === self::STATUS_NO_SHOW; }
    public function isConverted(): bool  { return $this->status === self::STATUS_CONVERTED; }

    public function canBeConfirmed(): bool  { return $this->status === self::STATUS_PENDING; }
    public function canBeCheckedIn(): bool  { return $this->status === self::STATUS_CONFIRMED && $this->room_id !== null; }
    public function canBeCancelled(): bool  { return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]); }
    public function canBeEdited(): bool     { return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]); }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)    { return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]); }
    public function scopePending($query)   { return $query->where('status', self::STATUS_PENDING); }
    public function scopeConfirmed($query) { return $query->where('status', self::STATUS_CONFIRMED); }
}