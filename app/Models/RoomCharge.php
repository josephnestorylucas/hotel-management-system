<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class RoomCharge extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_id', 'order_id', 'description', 'amount', 'charged_at',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'charged_at' => 'datetime',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function order()   { return $this->belongsTo(Order::class); }
}
