<?php
// app/Models/GoodsReceivedNoteItem.php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedNoteItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'grn_id',
        'lpo_item_id',
        'product_id',
        'item_name',
        'unit',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'subtotal',
        'stock_movement_id',
        'notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:3',
        'quantity_received' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function lpoItem(): BelongsTo
    {
        return $this->belongsTo(LocalPurchaseOrderItem::class, 'lpo_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }
}
