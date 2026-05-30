<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'order_id', 'menu_item_id', 'item_name_snapshot',
        'quantity', 'base_unit_price', 'options_unit_price', 'unit_price', 'subtotal',
        'selected_options_snapshot', 'options_signature',
        'notes', 'status',
    ];

    protected $casts = [
        'base_unit_price' => 'decimal:2',
        'options_unit_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
        'selected_options_snapshot' => 'array',
        'deleted_at'                => 'datetime',
    ];

    public function order()    { return $this->belongsTo(Order::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}
