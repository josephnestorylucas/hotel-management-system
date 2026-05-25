<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPass extends Model
{
    use HasUuid;

    protected $table = 'event_passes';

    protected $fillable = [
        'event_id',
        'tier_name',
        'tier_type',
        'description',
        'quantity_available',
        'quantity_sold',
        'benefits',
        'access_type',
        'status',
        'color',
    ];

    protected $casts = [
        'quantity_sold' => 'integer',
        'benefits' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'event_pass_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['archived']);
    }

    public function getQuantityRemainingAttribute(): ?int
    {
        if ($this->quantity_available === null) return null;
        return max(0, $this->quantity_available - $this->quantity_sold);
    }

    public function getRemainingQuantity(): ?int
    {
        return $this->quantity_remaining;
    }

    public function canRegister(int $quantity = 1): bool
    {
        if ($this->quantity_available !== null && ($this->quantity_sold + $quantity) > $this->quantity_available) return false;
        return true;
    }

    public function recordRegistration(int $quantity = 1): void
    {
        $this->increment('quantity_sold', $quantity);
    }
}
