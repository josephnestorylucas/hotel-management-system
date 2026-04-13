<?php
// app/Models/LocalPurchaseOrder.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalPurchaseOrder extends Model
{
    use HasUuid;

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
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
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

    public function getSupplierNameAttribute(): string
    {
        return $this->supplier?->name ?? $this->supplier_name_manual ?? 'N/A';
    }
}
