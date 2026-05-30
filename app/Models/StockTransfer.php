<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StockTransfer extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'from_location_id', 'to_location_id', 'product_id', 'quantity',
        'status', 'reason', 'requested_by', 'approved_by', 'approved_at',
        'rejected_by', 'rejected_at', 'rejection_reason', 'fulfilled_by', 'completed_at',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'completed_at' => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(StockLocation::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(StockLocation::class, 'to_location_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function fulfiller()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    public function toReceiptData(): array
    {
        $this->loadMissing(['product', 'fromLocation', 'toLocation', 'requester']);

        $items = [[
            'name'       => $this->product?->name ?? 'Product',
            'details'    => 'Transfer: ' . ($this->fromLocation?->name ?? '—') . ' → ' . ($this->toLocation?->name ?? '—'),
            'quantity'   => $this->quantity ?? 1,
            'unit_price' => 0,
            'amount'     => 0,
        ]];

        return [
            'receipt_no'            => $this->uuid,
            'issued_at'             => $this->completed_at ?? $this->created_at,
            'module'                => 'store',
            'customer_name'         => $this->fromLocation?->name ?? 'Stock Location',
            'customer_phone'        => null,
            'items'                 => $items,
            'subtotal'              => 0.0,
            'discount'              => 0.0,
            'tax'                   => 0.0,
            'total'                 => 0.0,
            'amount_paid'           => 0.0,
            'balance'               => 0.0,
            'currency'              => 'TZS',
            'payment_method'        => null,
            'payment_status'        => $this->status === 'completed' ? 'paid' : 'unpaid',
            'transaction_reference' => null,
            'cashier'               => $this->requester?->name,
            'notes'                 => $this->reason,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'store';
    }

    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }
}
