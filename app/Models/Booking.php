<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['cancelled', 'no_show', 'checked_out']);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeBySource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }

    public function scopeForDateRange(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->where('check_in_date', '<', $checkOut)
              ->where('check_out_date', '>', $checkIn);
        })->whereNotIn('status', ['cancelled', 'no_show', 'checked_out']);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('check_in_date', '>=', now()->toDateString())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('check_in_date', now()->toDateString());
    }

    // ─── Computed Attributes ───────────────────────────────────────

    /**
     * Get the number of nights for the stay.
     */
    public function getNightsAttribute(): int
    {
        if ($this->check_in_date && $this->check_out_date) {
            return $this->check_in_date->diffInDays($this->check_out_date);
        }
        return 0;
    }

    /**
     * Get guest display name - from guest relationship or legacy field.
     */
    public function getGuestDisplayNameAttribute(): string
    {
        if ($this->guest) {
            return $this->guest->full_name;
        }
        return $this->guest_name ?? 'Unknown Guest';
    }

    /**
     * Get guest display email - from guest relationship or legacy field.
     */
    public function getGuestDisplayEmailAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->email;
        }
        return $this->guest_email;
    }

    /**
     * Get guest display phone - from guest relationship or legacy field.
     */
    public function getGuestDisplayPhoneAttribute(): ?string
    {
        if ($this->guest) {
            return $this->guest->phone_number;
        }
        return $this->guest_phone;
    }

    // ─── Status Helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCheckedIn(): bool
    {
        return $this->status === 'checked_in';
    }

    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isNoShow(): bool
    {
        return $this->status === 'no_show';
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCheckedIn(): bool
    {
        return $this->status === 'confirmed';
    }

    public function canBeCheckedOut(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
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
            ->whereNotIn('status', ['cancelled', 'no_show', 'checked_out'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();

        if ($bookingConflict) {
            return false;
        }

        // Also check against existing reservations (created without bookings)
        $reservationConflict = Reservation::where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled', 'no_show', 'checked_out'])
            ->whereNull('booking_id')
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();

        return !$reservationConflict;
    }

    /**
     * Create a linked reservation from this booking.
     */
    public function createReservation(): Reservation
    {
        $reservation = Reservation::create([
            'room_id' => $this->room_id,
            'guest_id' => $this->guest_id,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'guest_phone' => $this->guest_phone,
            'check_in_date' => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'number_of_guests' => $this->number_of_guests,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'booking_id' => $this->id,
            'created_by' => $this->created_by,
        ]);

        // Link reservation back to this booking
        $this->update(['reservation_id' => $reservation->id]);

        return $reservation;
    }

    /**
     * Sync this booking's status to the linked reservation.
     */
    public function syncReservationStatus(): void
    {
        if ($this->reservation) {
            $this->reservation->update(['status' => $this->status]);
        }
    }

    /**
     * Get the price per night based on the room type.
     */
    public function getPricePerNightAttribute(): float
    {
        return $this->room?->roomType?->base_rate ?? 0;
    }
}
