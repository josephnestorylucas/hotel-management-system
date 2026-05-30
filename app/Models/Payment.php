<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Builder;

/**
 * Payment — immutable record of every payment attempt.
 *
 * Linked to a Booking (required) and optionally a BookingCharge.
 * Status updates are the ONLY mutable field (pending → successful | failed | refunded | expired).
 */
class Payment extends Model
{
    use HasUuid, HasSoftDelete;

    // ─── Status constants ──────────────────────────────────────────
    const STATUS_PENDING            = 'pending';
    const STATUS_SUCCESSFUL         = 'successful';
    const STATUS_FAILED             = 'failed';
    const STATUS_REFUNDED           = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const STATUS_EXPIRED            = 'expired';

    // ─── Provider constants ────────────────────────────────────────
    const PROVIDER_AZAMPESA = 'azampesa';

    protected $fillable = [
        'payment_number',
        'booking_id',
        'charge_type',
        'reference_id',
        'provider_name',
        'provider_reference',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_date',
        'refunded_at',
        'metadata',
        'refund_metadata',
        'payment_url',
        'payment_qr_code',
        'payment_token',
        'created_by',
        'idempotency_key',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'metadata'        => 'array',
        'refund_metadata' => 'array',
        'payment_date'    => 'datetime',
        'refunded_at'     => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    // ─── Boot ──────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY-' . strtoupper(Str::random(10));
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingCharge(): BelongsTo
    {
        return $this->belongsTo(BookingCharge::class, 'reference_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeForBooking(Builder $query, string $bookingId): Builder
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeByProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider_name', $provider);
    }

    // ─── Status Helpers ────────────────────────────────────────────

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isSuccessful(): bool { return $this->status === self::STATUS_SUCCESSFUL; }
    public function isFailed(): bool     { return $this->status === self::STATUS_FAILED; }
    public function isRefunded(): bool   { return $this->status === self::STATUS_REFUNDED; }
    public function isPartiallyRefunded(): bool { return $this->status === self::STATUS_PARTIALLY_REFUNDED; }
    public function isExpired(): bool    { return $this->status === self::STATUS_EXPIRED; }

    public function canBeRefunded(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL || $this->status === self::STATUS_PARTIALLY_REFUNDED;
    }

    // ─── Mark Methods ──────────────────────────────────────────────

    public function markSuccessful(array $providerData = []): bool
    {
        return $this->update([
            'status'       => self::STATUS_SUCCESSFUL,
            'payment_date' => now(),
            'metadata'     => array_merge($this->metadata ?? [], $providerData),
        ]);
    }

    public function markFailed(array $providerData = []): bool
    {
        return $this->update([
            'status'   => self::STATUS_FAILED,
            'metadata' => array_merge($this->metadata ?? [], $providerData),
        ]);
    }

    public function markRefunded(array $refundData = []): bool
    {
        return $this->update([
            'status'          => self::STATUS_REFUNDED,
            'refunded_at'     => now(),
            'refund_metadata' => $refundData,
        ]);
    }

    public function markExpired(): bool
    {
        return $this->update(['status' => self::STATUS_EXPIRED]);
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getChargeTypeLabelAttribute(): string
    {
        return match ($this->charge_type) {
            'booking'    => 'Room Booking',
            'laundry'    => 'Laundry Service',
            'conference' => 'Conference',
            'service'    => 'Room Service',
            'damage'     => 'Damage',
            'minibar'    => 'Mini Bar',
            default      => ucfirst(str_replace('_', ' ', $this->charge_type)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'            => 'bg-yellow-100 text-yellow-800',
            'successful'         => 'bg-green-100 text-green-800',
            'failed'             => 'bg-red-100 text-red-800',
            'refunded'           => 'bg-purple-100 text-purple-800',
            'partially_refunded' => 'bg-orange-100 text-orange-800',
            'expired'            => 'bg-gray-100 text-gray-800',
            default              => 'bg-gray-100 text-gray-800',
        };
    }

    public function getProviderLabelAttribute(): string
    {
        return match ($this->provider_name) {
            'azampesa' => 'AzamPesa',
            default    => ucfirst($this->provider_name),
        };
    }
}
