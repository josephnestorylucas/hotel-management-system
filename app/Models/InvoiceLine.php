<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSoftDelete;

class InvoiceLine extends Model
{
    use HasUuids, HasSoftDelete;

    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price',
        'subtotal', 'is_taxable',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'is_taxable' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}
