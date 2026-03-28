<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\JournalLine;

class JournalEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'entry_no', 'entry_date', 'reference', 'source', 'source_id',
        'description', 'total_debit', 'total_credit',
        'status', 'created_by', 'posted_by', 'posted_at',
    ];

    protected $casts = [
        'entry_date'  => 'date',
        'posted_at'   => 'datetime',
        'total_debit' => 'decimal:2',
        'total_credit'=> 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (JournalEntry $je) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $je->entry_no = 'JE-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    public function lines()   { return $this->hasMany(JournalLine::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function poster()  { return $this->belongsTo(User::class, 'posted_by'); }

    // Validate that debits equal credits before saving
    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}
