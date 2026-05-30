<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'payment_id', 'order_item_id', 'description',
        'quantity', 'unit_price', 'subtotal', 'currency',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function payment() { return $this->belongsTo(FinancePayment::class, 'payment_id'); }
}
