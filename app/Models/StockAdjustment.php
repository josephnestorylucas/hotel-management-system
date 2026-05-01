<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StockAdjustment extends Model implements ReceiptPrintable
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'location_id', 'previous_qty', 'new_qty',
        'difference', 'reason', 'requires_approval', 'status', 'approved_by', 'created_by',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'previous_qty'      => 'decimal:3',
        'new_qty'           => 'decimal:3',
        'difference'        => 'decimal:3',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    public function toReceiptData(): array
    {
        $this->loadMissing(['product', 'location', 'creator']);

        $items = [[
            'name'       => $this->product?->name ?? 'Product',
            'details'    => 'Adjustment: ' . ($this->previous_qty ?? 0) . ' → ' . ($this->new_qty ?? 0),
            'quantity'   => abs($this->difference ?? 0),
            'unit_price' => 0,
            'amount'     => 0,
        ]];

        return [
            'receipt_no'            => $this->uuid,
            'issued_at'             => $this->created_at,
            'module'                => 'store',
            'customer_name'         => $this->location?->name ?? 'Stock Location',
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
            'payment_status'        => $this->status === 'approved' ? 'paid' : 'unpaid',
            'transaction_reference' => null,
            'cashier'               => $this->creator?->name,
            'notes'                 => $this->reason,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'store';
    }

    public function isPaid(): bool
    {
        return $this->status === 'approved';
    }
}
