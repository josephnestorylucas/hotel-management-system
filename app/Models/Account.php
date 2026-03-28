<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\JournalLine;

class Account extends Model
{
    use HasUuids;

    protected $fillable = [
        'code', 'name', 'type', 'normal_balance',
        'parent_id', 'description', 'is_active', 'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // Get running balance for this account
    public function getBalance(?string $dateFrom = null, ?string $dateTo = null): float
    {
        $query = JournalLine::where('account_id', $this->id)
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted');

        if ($dateFrom) $query->whereDate('journal_entries.entry_date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('journal_entries.entry_date', '<=', $dateTo);

        $debits  = (clone $query)->where('journal_lines.type', 'debit')->sum('journal_lines.amount');
        $credits = (clone $query)->where('journal_lines.type', 'credit')->sum('journal_lines.amount');

        // Normal balance direction determines sign
        if ($this->normal_balance === 'debit') {
            return $debits - $credits;
        }
        return $credits - $debits;
    }

    // Static helpers for common accounts — avoids hardcoding IDs
    public static function findByCode(string $code): self
    {
        return self::where('code', $code)->firstOrFail();
    }

    public function children()    { return $this->hasMany(Account::class, 'parent_id'); }
    public function parent()      { return $this->belongsTo(Account::class, 'parent_id'); }
    public function journalLines(){ return $this->hasMany(JournalLine::class); }
}
