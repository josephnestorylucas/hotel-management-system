<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCharge extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'booking_id',
        'checkout_id',
        'order_id',
        'charge_type',
        'source',
        'reference_id',
        'description',
        'amount',
        'currency',
        'amount_tzs',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'amount_tzs' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'reference_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('charge_type', $type);
    }

    public function scopeForBooking($query, string $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // ── Methods ──────────────────────────────────────────────────────────────

    public function markAsPaid(): bool
    {
        return $this->update(['status' => 'paid']);
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    // Apply TZS amount using current exchange rate from system settings
    public function applyTzsAmount(float $exchangeRate): void
    {
        $this->update(['amount_tzs' => round($this->amount * $exchangeRate, 2)]);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getChargeTypeLabelAttribute(): string
    {
        return match ($this->charge_type) {
            'laundry'      => 'Laundry Service',
            'restaurant'   => 'Restaurant / Bar',
            'room_service' => 'Room Service',
            'damage'       => 'Damage',
            'minibar'      => 'Mini Bar',
            'extra_bed'    => 'Extra Bed',
            'conference'   => 'Conference',
            'store'        => 'Store Items',
            default        => ucfirst(str_replace('_', ' ', $this->charge_type)),
        };
    }

    public function getAmountInTzsAttribute(): float
    {
        return (float) $this->amount_tzs;
    }
}
