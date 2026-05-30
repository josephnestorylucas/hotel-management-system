<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    use HasUuid, HasSoftDelete;

    const STATUS_AVAILABLE = 'available';
    const STATUS_NEEDS_CLEANING = 'dirty';
    const STATUS_OUT_OF_ORDER = 'out_of_order';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';

    protected $fillable = [
        'floor_id', 'room_type_id', 'room_number', 'status', 'is_active',
        'cleaning_assigned_to', 'cleaning_assigned_at',
        'cleaning_completed_at', 'cleaning_confirmed_by', 'cleaning_confirmed_at',
        'out_of_order_reason', 'out_of_order_set_by', 'out_of_order_set_at',
    ];

    protected $casts = [
        'is_active'              => 'boolean',
        'cleaning_assigned_at'   => 'datetime',
        'cleaning_completed_at'  => 'datetime',
        'cleaning_confirmed_at'  => 'datetime',
        'out_of_order_set_at'    => 'datetime',
        'deleted_at'             => 'datetime',
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function cleaningAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleaning_assigned_to');
    }

    public function cleaningConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleaning_confirmed_by');
    }

    public function outOfOrderBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'out_of_order_set_by');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active === true;
    }

    /**
     * Get the room image from its room type with fallback.
     */
    public function getImageAttribute(): string
    {
        if ($this->roomType && $this->roomType->hasImage()) {
            return $this->roomType->getFirstMediaUrl('room_type_image', 'medium') 
                ?: $this->roomType->getFirstMediaUrl('room_type_image');
        }
        return asset('images/room-placeholder.svg');
    }

    /**
     * Scope to get rooms available for a specific date range.
     * Checks both bookings and reservations for conflicts.
     */
    public function scopeAvailableForDates($query, string $checkIn, string $checkOut)
    {
        return $query->where('status', 'available')
            ->where('is_active', true)
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn)
                  ->whereNotIn('status', ['cancelled', 'no_show', 'checked_out']);
            })
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn)
                  ->whereNotIn('status', ['cancelled', 'no_show', 'converted'])
                  ->whereNull('booking_id');
            });
    }
}