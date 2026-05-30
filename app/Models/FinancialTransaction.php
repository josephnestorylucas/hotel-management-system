<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasUuid, HasSoftDelete;

    public $timestamps = false;

    protected $fillable = [
        'transaction_number', 'type', 'source_module',
        'payment_id', 'booking_id', 'order_id',
        'currency', 'amount', 'amount_usd', 'exchange_rate',
        'payment_method', 'description', 'created_by', 'created_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'amount_usd'    => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'created_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    /**
     * THE CORE METHOD — every financial event goes through here.
     * Immutable. No updates. No deletes.
     */
    public static function record(array $params, string $actorId): self
    {
        $count  = self::whereDate('created_at', today())->count() + 1;
        $txnNum = 'TXN-' . date('Ymd') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        return self::create([
            'transaction_number' => $txnNum,
            'type'               => $params['type'],
            'source_module'      => $params['source_module'],
            'payment_id'         => $params['payment_id']  ?? null,
            'booking_id'         => $params['booking_id']  ?? null,
            'order_id'           => $params['order_id']    ?? null,
            'currency'           => $params['currency'],
            'amount'             => $params['amount'],
            'amount_usd'         => $params['amount_usd'],
            'exchange_rate'      => $params['exchange_rate'] ?? 1,
            'payment_method'     => $params['payment_method'],
            'description'        => $params['description'],
            'created_by'         => $actorId,
            'created_at'         => now(),
        ]);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function payment() { return $this->belongsTo(FinancePayment::class, 'payment_id'); }
    public function actor()   { return $this->belongsTo(User::class, 'created_by'); }
}
