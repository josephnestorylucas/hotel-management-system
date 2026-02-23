<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryOrder extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_number', 'customer_type',
        'booking_id', 'room_number',
        'customer_name', 'customer_phone',
        'status', 'special_instructions',
        'subtotal', 'discount', 'total', 'payment_method',
        'expected_ready_at', 'ready_at',
        'delivered_at', 'collected_at', 'settled_at',
        'received_by', 'processed_by', 'delivered_by', 'settled_by',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'discount'          => 'decimal:2',
        'total'             => 'decimal:2',
        'expected_ready_at' => 'datetime',
        'ready_at'          => 'datetime',
        'delivered_at'      => 'datetime',
        'collected_at'      => 'datetime',
        'settled_at'        => 'datetime',
    ];

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function (LaundryOrder $order) {
            if (empty($order->order_number)) {
                $count = self::whereDate('created_at', today())->count() + 1;
                $order->order_number = 'LND-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Recalculate totals from items
    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount,
        ]);
    }

    // Is this order past its expected ready time and not yet done?
    public function isOverdue(): bool
    {
        return $this->expected_ready_at
            && now()->isAfter($this->expected_ready_at)
            && !in_array($this->status, ['ready', 'delivered', 'collected', 'settled', 'cancelled']);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['settled', 'cancelled']);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'received'   => 'yellow',
            'processing' => 'blue',
            'ready'      => 'orange',
            'delivered'  => 'indigo',
            'collected'  => 'teal',
            'settled'    => 'green',
            'cancelled'  => 'gray',
            default      => 'gray',
        };
    }
}
