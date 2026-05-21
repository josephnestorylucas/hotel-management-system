<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTicket extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'tier_name',
        'tier_type',
        'description',
        'price',
        'quantity_available',
        'quantity_sold',
        'early_bird_until',
        'benefits',
        'includes_guide',
        'access_type',
        'bulk_discount_percent',
        'sale_start_date',
        'sale_end_date',
        'status',
        'color',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity_sold' => 'integer',
        'includes_guide' => 'boolean',
        'bulk_discount_percent' => 'decimal:2',
        'benefits' => 'array',
        'early_bird_until' => 'date',
        'sale_start_date' => 'date',
        'sale_end_date' => 'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeOnSale($query)
    {
        return $query->where('status', 'on_sale');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'on_sale')
            ->where(function ($q) {
                $q->whereNull('quantity_available')
                    ->orWhereColumn('quantity_sold', '<', 'quantity_available');
            })
            ->where(function ($q) {
                $q->whereNull('sale_end_date')
                    ->orWhere('sale_end_date', '>=', now());
            });
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

    public function getIsEarlyBirdAttribute(): bool
    {
        return $this->early_bird_until !== null && $this->early_bird_until->isFuture();
    }

    public function getPriceForQuantity(int $quantity): float
    {
        $price = (float) $this->price;
        if ($this->is_early_bird) {
            return $price * $quantity;
        }
        if ($this->bulk_discount_percent && $quantity >= 10) {
            $discount = $price * ($this->bulk_discount_percent / 100);
            return ($price - $discount) * $quantity;
        }
        return $price * $quantity;
    }

    public function getRemainingQuantity(): ?int
    {
        return $this->quantity_remaining;
    }

    public function canPurchase(int $quantity = 1): bool
    {
        if ($this->status !== 'on_sale') return false;
        if ($this->sale_start_date && $this->sale_start_date->isFuture()) return false;
        if ($this->sale_end_date && $this->sale_end_date->isPast()) return false;
        if ($this->quantity_available !== null && ($this->quantity_sold + $quantity) > $this->quantity_available) return false;
        return true;
    }

    public function markSoldOut(): void
    {
        $this->update(['status' => 'sold_out']);
    }

    public function recordSale(int $quantity = 1): void
    {
        $this->increment('quantity_sold', $quantity);
        if ($this->quantity_available !== null && $this->quantity_sold >= $this->quantity_available) {
            $this->markSoldOut();
        }
    }
}
