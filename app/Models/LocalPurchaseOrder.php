<?php
// app/Models/LocalPurchaseOrder.php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LocalPurchaseOrder extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'lpo_number',
        'supplier_id',
        'supplier_name_manual',
        'order_date',
        'expected_delivery_date',
        'subtotal',
        'tax_amount',
        'grand_total',
        'notes',
        'terms',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
        'rejected_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LocalPurchaseOrder $lpo) {
            if (empty($lpo->lpo_number)) {
                $count = self::whereDate('created_at', today())->count() + 1;
                $lpo->lpo_number = 'LPO-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = round($subtotal * 0.18, 2); // 18% VAT
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'grand_total' => $subtotal + $tax,
        ]);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LocalPurchaseOrderItem::class, 'lpo_id');
    }

    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class, 'lpo_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getSupplierNameAttribute(): string
    {
        return $this->supplier?->name ?? $this->supplier_name_manual ?? 'N/A';
    }

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    public function toReceiptData(): array
    {
        $this->loadMissing(['items.product', 'supplier', 'creator']);

        $items = $this->items->map(function ($item) {
            return [
                'name'       => $item->product?->name ?? $item->item_name ?? 'Item',
                'details'    => $item->description ?? '',
                'quantity'   => $item->quantity ?? 1,
                'unit_price' => (float) ($item->unit_price ?? 0),
                'amount'     => (float) ($item->subtotal ?? (($item->quantity ?? 1) * ($item->unit_price ?? 0))),
            ];
        })->toArray();

        return [
            'receipt_no'            => $this->lpo_number,
            'issued_at'             => $this->approved_at ?? $this->created_at,
            'module'                => 'procurement',
            'customer_name'         => $this->supplier_name,
            'customer_phone'        => $this->supplier?->phone ?? null,
            'items'                 => $items,
            'subtotal'              => (float) $this->subtotal,
            'discount'              => 0.0,
            'tax'                   => (float) $this->tax_amount,
            'total'                 => (float) $this->grand_total,
            'amount_paid'           => 0.0,
            'balance'               => (float) $this->grand_total,
            'currency'              => 'TZS',
            'payment_method'        => null,
            'payment_status'        => $this->isPaid() ? 'paid' : 'unpaid',
            'transaction_reference' => $this->lpo_number,
            'cashier'               => $this->creator?->name,
            'notes'                 => $this->notes,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'procurement';
    }

    public function isPaid(): bool
    {
        return $this->status === 'fully_received' || $this->status === 'completed';
    }
}
