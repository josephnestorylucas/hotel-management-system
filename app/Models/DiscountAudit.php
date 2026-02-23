<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class DiscountAudit extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_id', 'authorized_by', 'discount_amount',
        'valid_days', 'valid_from', 'valid_until', 'reason', 'authorized_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'valid_from'      => 'date',
        'valid_until'     => 'date',
        'authorized_at'   => 'datetime',
    ];

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
