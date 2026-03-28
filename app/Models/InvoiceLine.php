<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price',
        'subtotal', 'is_taxable',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}
