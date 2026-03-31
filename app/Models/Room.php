<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    use HasUuid;

    protected $fillable = ['floor_id', 'room_type_id', 'room_number', 'status', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
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