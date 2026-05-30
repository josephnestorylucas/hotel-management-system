<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryOrderItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'laundry_order_id', 'laundry_service_item_id',
        'quantity', 'unit_price', 'subtotal', 'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'laundry_order_id');
    }

    public function serviceItem(): BelongsTo
    {
        return $this->belongsTo(LaundryServiceItem::class, 'laundry_service_item_id');
    }
}
