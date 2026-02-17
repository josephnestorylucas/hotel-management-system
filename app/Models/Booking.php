<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Booking = active guest stay + billing.
 *
 * Created at check-in (either from a converted Reservation, or as a walk-in).
 * All service charges (laundry, minibar, damages, etc.) attach here.
 * Ends at checkout. Responsible for final billing.
 *
 * Statuses: checked_in → checked_out | cancelled
 */
class Booking extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_number',
        'guest_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_country',
        'room_id',
        'reservation_id',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'total_amount',
        'special_requests',
        'status',
        'source',
        'created_by',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Auto-generate booking number on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BK-' . strtoupper(uniqid());
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────────

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * The reservation this booking was created from (nullable — walk-ins have none).
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }

    public function bookingCharges(): HasMany
    {
        return $this->hasMany(BookingCharge::class);
    }

    public function unpaidCharges(): HasMany
    {
        return $this->hasMany(BookingCharge::class)->where('status', 'unpaid');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeCheckedOut(Builder $query): Builder
    {
        return $query->where('status', 'checked_out');
    }

    public function scopeForDateRange(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->where('check_in_date', '<', $checkOut)
              ->where('check_out_date', '>', $checkIn);
        })->whereNotIn('status', ['cancelled', 'checked_out']);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('check_in_date', now()->toDateString());
    }

    // ─── Computed Attributes ───────────────────────────────────────

    public function getNightsAttribute(): int
    {
        if ($this->check_in_date && $this->check_out_date) {
            return $this->check_in_date->diffInDays($this->check_out_date);
        }
        return 0;
    }

    public function getGuestDisplayNameAttribute(): string
    {
        if ($this->guest) {
            return $this->guest->full_name;
        }
        return $this->guest_name ?? 'Unknown Guest';
    }

    public function getGuestDisplayEmailAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->email;
        }
        return $this->guest_email;
    }

    public function getGuestDisplayPhoneAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->phone_number;
        }
        return $this->guest_phone;
    }

    public function getTotalUnpaidChargesAttribute(): float
    {
        return $this->bookingCharges()->unpaid()->sum('amount');
    }

    public function getTotalChargesAttribute(): float
    {
        return $this->bookingCharges()->sum('amount');
    }

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_amount + $this->total_unpaid_charges;
    }

    public function getPricePerNightAttribute(): float
    {
        return $this->room?->roomType?->base_rate ?? 0;
    }

    // ─── Status Helpers ────────────────────────────────────────────

    public function isCheckedIn(): bool  { return $this->status === 'checked_in'; }
    public function isCheckedOut(): bool { return $this->status === 'checked_out'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    public function canBeCheckedOut(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'checked_in';
    }

    // ─── Business Logic ────────────────────────────────────────────

    /**
     * Check if a room is available for the given date range.
     * Checks both bookings and reservations tables.
     */
    public static function isRoomAvailable(string $roomId, string $checkIn, string $checkOut, ?string $excludeBookingId = null): bool
    {
        // Check against existing bookings
        $bookingConflict = static::where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();

        if ($bookingConflict) {
            return false;
        }

        // Also check against active reservations (not yet converted to bookings)
        $reservationConflict = Reservation::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();

        return !$reservationConflict;
    }

    /**
     * Create a Booking from a confirmed Reservation (check-in).
     */
    public static function createFromReservation(Reservation $reservation, ?int $createdBy = null): self
    {
        $booking = static::create([
            'guest_id' => $reservation->guest_id,
            'guest_name' => $reservation->guest_display_name,
            'guest_email' => $reservation->guest_display_email ?? '',
            'guest_phone' => $reservation->guest_display_phone ?? '',
            'guest_country' => $reservation->guest?->nationality ?? null,
            'room_id' => $reservation->room_id,
            'reservation_id' => $reservation->id,
            'check_in_date' => $reservation->check_in_date,
            'check_out_date' => $reservation->check_out_date,
            'number_of_guests' => $reservation->number_of_guests,
            'total_amount' => $reservation->estimated_amount,
            'status' => 'checked_in',
            'source' => 'frontdesk',
            'created_by' => $createdBy,
        ]);

        // Mark the reservation as converted
        $reservation->update([
            'status' => 'converted',
            'booking_id' => $booking->id,
        ]);

        return $booking;
    }
}
