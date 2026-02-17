<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCharge extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_id',
        'charge_type',
        'reference_id',
        'description',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Get the source record (polymorphic-like via charge_type + reference_id)
    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'reference_id');
    }

    // Scopes
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

    // Methods
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

    // Accessors
    public function getChargeTypeLabelAttribute(): string
    {
        return match ($this->charge_type) {
            'laundry' => 'Laundry Service',
            'room_service' => 'Room Service',
            'damage' => 'Damage',
            'minibar' => 'Mini Bar',
            'extra_bed' => 'Extra Bed',
            'conference' => 'Conference',
            default => ucfirst(str_replace('_', ' ', $this->charge_type)),
        };
    }
}
