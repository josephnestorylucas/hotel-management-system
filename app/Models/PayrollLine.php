<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PayrollLine extends Model
{
    use HasUuids, HasSoftDelete;

    protected $fillable = [
        'payroll_run_id', 'user_id', 'staff_name', 'role',
        'basic_salary', 'allowances', 'gross_salary',
        'nssf_employee', 'nssf_employer', 'paye',
        'other_deductions', 'net_salary', 'notes',
    ];

    protected $casts = [
        'basic_salary'     => 'decimal:2',
        'allowances'       => 'decimal:2',
        'gross_salary'     => 'decimal:2',
        'nssf_employee'    => 'decimal:2',
        'nssf_employer'    => 'decimal:2',
        'paye'             => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary'       => 'decimal:2',
        'deleted_at'       => 'datetime',
    ];

    public function payrollRun() { return $this->belongsTo(PayrollRun::class); }
    public function user()       { return $this->belongsTo(User::class); }
}
