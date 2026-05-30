<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * Receipt — stores all generated receipts for printing/reprinting.
 * 
 * Links to any receiptable model (LaundryOrder, Order, Checkout, WalkinTransaction, etc.)
 * Ensures idempotent receipt number generation (reprint uses same number).
 */
class Receipt extends Model
{
    use HasSoftDelete;

    protected $fillable = [
        'receipt_number',
        'module',
        'receiptable_type',
        'receiptable_id',
        'customer_name',
        'customer_phone',
        'items_snapshot',
        'subtotal',
        'discount',
        'tax',
        'total',
        'amount_paid',
        'balance',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'cashier_id',
        'cashier_name',
        'notes',
        'is_refund',
        'refund_receipt_id',
        'issued_at',
        'printed_at',
        'print_count',
    ];

    protected $casts = [
        'items_snapshot' => 'array',
        'subtotal'       => 'decimal:2',
        'discount'       => 'decimal:2',
        'tax'            => 'decimal:2',
        'total'          => 'decimal:2',
        'amount_paid'    => 'decimal:2',
        'balance'        => 'decimal:2',
        'is_refund'      => 'boolean',
        'issued_at'      => 'datetime',
        'printed_at'     => 'datetime',
        'print_count'    => 'integer',
        'deleted_at'     => 'datetime',
    ];

    /**
     * Auto-generate receipt number on create.
     */
    protected static function booted(): void
    {
        static::creating(function (Receipt $receipt) {
            // Generate UUID for the uuid column
            if (empty($receipt->uuid)) {
                $receipt->uuid = (string) Str::uuid();
            }
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
            if (empty($receipt->issued_at)) {
                $receipt->issued_at = now();
            }
            $receipt->print_count = 0;
        });
    }

    /**
     * Generate a unique sequential receipt number.
     * Format: HMS-YYYY-XXXXXX (e.g., HMS-2026-000001)
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "HMS-{$year}-";
        
        $lastReceipt = self::where('receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('receipt_number')
            ->first();

        if ($lastReceipt) {
            $lastNum = (int) substr($lastReceipt->receipt_number, -6);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /**
     * Get the parent receiptable model (LaundryOrder, Order, Checkout, etc.)
     */
    public function receiptable(): MorphTo
    {
        return $this->morphTo();
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function refundReceipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'refund_receipt_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Mark this receipt as printed and increment print count.
     */
    public function markPrinted(): void
    {
        $this->update([
            'printed_at'  => now(),
            'print_count' => $this->print_count + 1,
        ]);
    }

    /**
     * Check if this receipt has been printed before.
     */
    public function hasBeenPrinted(): bool
    {
        return $this->print_count > 0;
    }

    /**
     * Get formatted total with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 0) . ' ' . ($this->currency ?? 'TZS');
    }

    /**
     * Get payment status label.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'     => 'PAID',
            'partial'  => 'PARTIAL PAYMENT',
            'unpaid'   => 'UNPAID',
            'refunded' => 'REFUNDED',
            default    => strtoupper($this->payment_status ?? 'UNKNOWN'),
        };
    }

    /**
     * Get module label.
     */
    public function getModuleLabelAttribute(): string
    {
        return match ($this->module) {
            'laundry'     => 'Laundry Service',
            'restaurant'  => 'Restaurant',
            'bar'         => 'Bar',
            'checkout'    => 'Hotel Checkout',
            'walkin'      => 'Walk-in Sale',
            'conference'  => 'Conference',
            'procurement' => 'Procurement',
            'store'       => 'Store / Inventory',
            'accounting'  => 'Accounting',
            default       => ucfirst($this->module ?? 'Unknown'),
        };
    }
}
