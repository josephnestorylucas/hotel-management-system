<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference_no', 'period_month', 'pay_date',
        'total_gross', 'total_nssf_employee', 'total_nssf_employer',
        'total_paye', 'total_net', 'status', 'notes',
        'prepared_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'pay_date'     => 'date',
        'approved_at'  => 'datetime',
        'total_gross'  => 'decimal:2',
        'total_net'    => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (PayrollRun $p) {
            $p->reference_no = 'PAY-' . $p->period_month;
        });
    }

    public function lines()    { return $this->hasMany(PayrollLine::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public function recalculate(): void
    {
        $this->load('lines');
        $this->update([
            'total_gross'          => $this->lines->sum('gross_salary'),
            'total_nssf_employee'  => $this->lines->sum('nssf_employee'),
            'total_nssf_employer'  => $this->lines->sum('nssf_employer'),
            'total_paye'           => $this->lines->sum('paye'),
            'total_net'            => $this->lines->sum('net_salary'),
        ]);
    }
}
