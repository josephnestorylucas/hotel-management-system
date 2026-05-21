<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * FinancePayment — tracks walk-in sales payments and checkout payments.
 *
 * This is separate from the existing Payment model (AzamPesa online payments).
 * Used by the Finance module for cash/card/mobile transactions.
 */
class FinancePayment extends Model
{
    use HasUuid;

    protected $table = 'finance_payments';

    protected $fillable = [
        'payment_number', 'payment_type', 'checkout_id', 'order_id', 'booking_id',
        'currency', 'amount', 'amount_usd', 'exchange_rate',
        'method', 'status', 'reference', 'notes', 'created_by', 'paid_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'amount_usd'    => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'paid_at'       => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (FinancePayment $payment) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function checkout()  { return $this->belongsTo(Checkout::class); }
    public function order()     { return $this->belongsTo(Order::class); }
    public function items()     { return $this->hasMany(PaymentItem::class, 'payment_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function transaction() { return $this->hasOne(FinancialTransaction::class, 'payment_id'); }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Convert amount to USD regardless of currency received.
     */
    public static function toUsd(float $amount, string $currency, float $exchangeRate): float
    {
        if ($currency === 'USD') return $amount;
        return round($amount / $exchangeRate, 2); // TZS → USD
    }
}
