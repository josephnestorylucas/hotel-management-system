<?php
// app/Models/ConferenceHall.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceHall extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'hourly_rate',
        'status',
        'amenities',
        'description',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function conferenceBookings(): HasMany
    {
        return $this->hasMany(ConferenceBooking::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}