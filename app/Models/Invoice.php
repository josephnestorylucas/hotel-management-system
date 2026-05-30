<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSoftDelete;

class Invoice extends Model
{
    use HasUuids, HasSoftDelete;

    protected $fillable = [
        'invoice_no', 'invoice_type', 'guest_id', 'guest_name',
        'booking_id', 'invoice_date', 'subtotal', 'discount',
        'tax_amount', 'total', 'payment_method', 'status',
        'notes', 'issued_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total'        => 'decimal:2',
        'deleted_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $inv) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $inv->invoice_no = 'INV-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    public function lines()   { return $this->hasMany(InvoiceLine::class); }
    public function issuer()  { return $this->belongsTo(User::class, 'issued_by'); }
}
