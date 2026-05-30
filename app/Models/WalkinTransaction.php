<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * WalkinTransaction — records all walk-in payment transactions.
 * 
 * Tracks payments made by walk-in customers across different modules:
 * - Laundry
 * - Restaurant
 * - Bar
 * 
 * This provides a unified view of all walk-in transactions with
 * customer identity information captured at the time of payment.
 */
class WalkinTransaction extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    protected $table = 'walkin_transactions';

    protected $fillable = [
        'transaction_number',
        'module',
        'order_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'amount',
        'currency',
        'payment_method',
        'provider_reference',
        'status',
        'metadata',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'metadata'     => 'array',
        'completed_at' => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /**
     * Auto-generate transaction number on create.
     */
    protected static function booted(): void
    {
        static::creating(function (WalkinTransaction $txn) {
            if (empty($txn->transaction_number)) {
                $prefix = strtoupper(substr($txn->module ?? 'WLK', 0, 3));
                $count = self::whereDate('created_at', today())->count() + 1;
                $txn->transaction_number = "WLK-{$prefix}-" . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related order based on module.
     */
    public function getOrderAttribute()
    {
        return match ($this->module) {
            'laundry' => LaundryOrder::find($this->order_id),
            'restaurant', 'bar' => Order::find($this->order_id),
            default => null,
        };
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ── Status Helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markCompleted(array $metadata = []): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'metadata'     => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    public function markFailed(array $metadata = []): void
    {
        $this->update([
            'status'   => 'failed',
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    // ── Receipt Relationship ─────────────────────────────────────────────────

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    // ── ReceiptPrintable Implementation ──────────────────────────────────────

    public function toReceiptData(): array
    {
        $this->loadMissing(['creator', 'order']);

        $items = [];
        if ($this->order) {
            $items = collect($this->order->items ?? [])
                ->map(function ($item) {
                    return [
                        'name'       => $item->name ?? $item->description ?? 'Item',
                        'details'    => null,
                        'quantity'   => $item->quantity ?? 1,
                        'unit_price' => $item->unit_price ?? 0,
                        'amount'     => $item->subtotal ?? ($item->quantity ?? 1) * ($item->unit_price ?? 0),
                    ];
                })->toArray();
        }

        if (empty($items)) {
            $items[] = [
                'name'       => 'Walk-in Payment',
                'details'    => $this->order_number,
                'quantity'   => 1,
                'unit_price' => (float) $this->amount,
                'amount'     => (float) $this->amount,
            ];
        }

        return [
            'receipt_no'            => $this->transaction_number,
            'issued_at'             => $this->completed_at ?? $this->created_at,
            'module'                => $this->module ?? 'walkin',
            'customer_name'         => $this->customer_name,
            'customer_phone'        => $this->customer_phone,
            'items'                 => $items,
            'subtotal'              => (float) $this->amount,
            'discount'              => 0.0,
            'tax'                   => 0.0,
            'total'                 => (float) $this->amount,
            'amount_paid'           => $this->isCompleted() ? (float) $this->amount : 0.0,
            'balance'               => $this->isCompleted() ? 0.0 : (float) $this->amount,
            'currency'              => $this->currency ?? 'TZS',
            'payment_method'        => $this->payment_method,
            'payment_status'        => $this->getPaymentStatus(),
            'transaction_reference' => $this->provider_reference,
            'cashier'               => $this->creator?->name,
            'notes'                 => $this->metadata['notes'] ?? null,
        ];
    }

    public function getReceiptModule(): string
    {
        return $this->module ?? 'walkin';
    }

    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }

    protected function getPaymentStatus(): string
    {
        return match ($this->status) {
            'completed' => 'paid',
            'failed'    => 'unpaid',
            default     => 'unpaid',
        };
    }
}
