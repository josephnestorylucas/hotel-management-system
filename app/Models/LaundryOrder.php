<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LaundryOrder extends Model implements ReceiptPrintable
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

    /**
     * Get the guest associated with this laundry order through the booking.
     * Only applicable for guest orders (customer_type = 'guest').
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'booking_id', 'id')
            ->withDefault(function ($guest) {
                if ($this->booking && $this->booking->guest) {
                    return $this->booking->guest;
                }
                return null;
            });
    }

    /**
     * Get the guest through the booking relationship.
     */
    public function getGuestThroughBookingAttribute(): ?Guest
    {
        return $this->booking?->guest;
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function creator(): BelongsTo
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

    // ── Receipt Relationship ─────────────────────────────────────────────────

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    // ── ReceiptPrintable Implementation ──────────────────────────────────────

    public function toReceiptData(): array
    {
        $this->loadMissing(['items.serviceItem', 'settler', 'booking']);

        $items = $this->items->map(function ($item) {
            $serviceItem = $item->serviceItem;
            return [
                'name'       => $serviceItem?->item_name ?? 'Laundry Item',
                'details'    => $serviceItem?->service?->name ?? 'Service',
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount'     => $item->subtotal,
            ];
        })->toArray();

        return [
            'receipt_no'            => $this->order_number,
            'issued_at'             => $this->settled_at ?? $this->created_at,
            'module'                => 'laundry',
            'customer_name'         => $this->customer_name ?? $this->booking?->guest_name ?? null,
            'customer_phone'        => $this->customer_phone ?? $this->booking?->guest?->phone ?? null,
            'items'                 => $items,
            'subtotal'              => (float) $this->subtotal,
            'discount'              => (float) $this->discount,
            'tax'                   => 0.0,
            'total'                 => (float) $this->total,
            'amount_paid'           => $this->isPaid() ? (float) $this->total : 0.0,
            'balance'               => $this->isPaid() ? 0.0 : (float) $this->total,
            'currency'              => 'TZS',
            'payment_method'        => $this->payment_method,
            'payment_status'        => $this->getPaymentStatus(),
            'transaction_reference' => null,
            'cashier'               => $this->settler?->name,
            'notes'                 => $this->special_instructions,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'laundry';
    }

    public function isPaid(): bool
    {
        return $this->status === 'settled' && $this->settled_at !== null;
    }

    protected function getPaymentStatus(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        return $this->isPaid() ? 'paid' : 'unpaid';
    }
}
