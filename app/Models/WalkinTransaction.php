<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
class WalkinTransaction extends Model
{
    use HasUuid;

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
}
