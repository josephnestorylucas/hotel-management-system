<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPaymentAllocation extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'supplier_payment_id',
        'supplier_payable_id',
        'allocated_amount',
        'created_by',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'deleted_at'       => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id');
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(SupplierPayable::class, 'supplier_payable_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
