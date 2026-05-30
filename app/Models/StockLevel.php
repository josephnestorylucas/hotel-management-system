<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasUuid, HasSoftDelete;

    public $timestamps = false;

    protected $fillable = ['product_id', 'location_id', 'quantity', 'reserved_qty', 'last_counted_at'];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'reserved_qty'  => 'decimal:3',
        'deleted_at'    => 'datetime',
    ];

    /**
     * Available = total minus anything reserved for pending orders.
     */
    public function getAvailableQtyAttribute(): float
    {
        return (float) $this->quantity - (float) $this->reserved_qty;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }
}
