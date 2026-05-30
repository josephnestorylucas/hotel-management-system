<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class SupplierPayable extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'supplier_id',
        'reference',
        'payable_date',
        'currency',
        'amount_total',
        'amount_paid',
        'balance',
        'status',
        'source_module',
        'source_reference_type',
        'source_reference_id',
        'journal_entry_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'payable_date' => 'date',
        'amount_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $payable): void {
            if ($payable->status === 'paid' || $payable->amount_paid > 0) {
                throw new LogicException('Supplier payables with posted allocations cannot be deleted.');
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SupplierPaymentAllocation::class);
    }

    public function recalculateStatus(): void
    {
        $total = round((float) $this->amount_total, 2);
        $paid = round((float) $this->amount_paid, 2);
        $balance = max(round($total - $paid, 2), 0);

        $status = match (true) {
            $this->status === 'cancelled' => 'cancelled',
            $balance <= 0 => 'paid',
            $paid > 0 => 'partial',
            default => 'unpaid',
        };

        $this->forceFill([
            'balance' => $balance,
            'status' => $status,
        ])->save();
    }
}
