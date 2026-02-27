<?php
// app/Models/LocalPurchaseOrderItem.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalPurchaseOrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'lpo_id',
        'product_id',
        'item_name',
        'unit',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function lpo(): BelongsTo
    {
        return $this->belongsTo(LocalPurchaseOrder::class, 'lpo_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}