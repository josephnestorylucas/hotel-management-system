<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference_no', 'account_id', 'period_month', 'statement_date',
        'statement_opening_balance', 'statement_closing_balance',
        'system_opening_balance', 'system_closing_balance',
        'difference', 'status', 'notes', 'prepared_by',
    ];

    protected $casts = [
        'statement_date'           => 'date',
        'statement_opening_balance'=> 'decimal:2',
        'statement_closing_balance'=> 'decimal:2',
        'system_opening_balance'   => 'decimal:2',
        'system_closing_balance'   => 'decimal:2',
        'difference'               => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (BankReconciliation $rec) {
            $rec->reference_no = 'BNK-REC-' . $rec->period_month;
        });
    }

    public function account()  { return $this->belongsTo(Account::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
}
