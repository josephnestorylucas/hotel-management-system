<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BuffetSale extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'sale_number',
        'buffet_package_id',
        'booking_id',
        'sale_type',
        'adults_count',
        'children_count',
        'adult_price_snapshot',
        'child_price_snapshot',
        'package_name_snapshot',
        'total_amount',
        'status',
        'payment_method',
        'payment_reference',
        'notes',
        'served_by',
        'settled_by',
        'settled_at',
    ];

    protected $casts = [
        'adult_price_snapshot' => 'decimal:2',
        'child_price_snapshot' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'settled_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BuffetSale $sale) {
            if (empty($sale->sale_number)) {
                $count = self::whereDate('created_at', today())->count() + 1;
                $sale->sale_number = 'BUF-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(BuffetPackage::class, 'buffet_package_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    public function toReceiptData(): array
    {
        return [
            'receipt_no' => $this->sale_number,
            'issued_at' => $this->settled_at ?? $this->created_at,
            'module' => 'restaurant',
            'customer_name' => $this->sale_type === 'booking' ? ($this->booking?->guest_display_name ?? null) : null,
            'customer_phone' => $this->sale_type === 'booking' ? ($this->booking?->guest_display_phone ?? null) : null,
            'items' => [[
                'name' => $this->package_name_snapshot,
                'details' => "Adults: {$this->adults_count}, Children: {$this->children_count}",
                'quantity' => 1,
                'unit_price' => (float) $this->total_amount,
                'amount' => (float) $this->total_amount,
            ]],
            'subtotal' => (float) $this->total_amount,
            'discount' => 0.0,
            'tax' => 0.0,
            'total' => (float) $this->total_amount,
            'amount_paid' => $this->isPaid() ? (float) $this->total_amount : 0.0,
            'balance' => $this->isPaid() ? 0.0 : (float) $this->total_amount,
            'currency' => 'TZS',
            'payment_method' => $this->payment_method,
            'payment_status' => $this->isPaid() ? 'paid' : 'unpaid',
            'transaction_reference' => $this->payment_reference,
            'cashier' => $this->settler?->name,
            'notes' => $this->notes,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'restaurant';
    }

    public function isPaid(): bool
    {
        return $this->status === 'settled';
    }
}

