<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasUuids;

    protected $table = 'petty_cash_expenses';

    protected $fillable = [
        'reference_no', 'category', 'amount', 'description',
        'status', 'requested_by', 'approved_by', 'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PettyCash $pc) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $pc->reference_no = 'PC-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver()  { return $this->belongsTo(User::class, 'approved_by'); }
}
