<?php
// app/Models/ConferenceHall.php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasSoftDelete;

class ConferenceHall extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'name',
        'building_id',
        'capacity',
        'hourly_rate',
        'currency',
        'status',
        'amenities',
        'description',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'amenities' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function conferenceBookings(): HasMany
    {
        return $this->hasMany(ConferenceBooking::class);
    }

    public function getFormattedRateAttribute(): string
    {
        $storedCurrency = $this->currency ?? 'USD';
        $systemCurrency = CurrencyHelper::getDefaultCurrency();
        $amount = (float) $this->hourly_rate;

        if ($storedCurrency === $systemCurrency) {
            return CurrencyHelper::formatCurrency($amount, $systemCurrency);
        }

        $converted = CurrencyHelper::convert($amount, $storedCurrency, $systemCurrency);

        return CurrencyHelper::formatCurrency($converted, $systemCurrency);
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