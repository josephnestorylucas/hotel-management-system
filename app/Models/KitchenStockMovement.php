<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class KitchenStockMovement extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'kitchen_stock_item_id', 'movement_type', 'quantity', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(KitchenStockItem::class, 'kitchen_stock_item_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
