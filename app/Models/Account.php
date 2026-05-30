<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\JournalLine;
use App\Traits\HasSoftDelete;

class Account extends Model
{
    use HasUuids, HasSoftDelete;

    protected $fillable = [
        'code', 'name', 'type', 'normal_balance',
        'parent_id', 'description', 'is_active', 'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'deleted_at' => 'datetime',
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
        $account = self::where('code', $code)->first();
        
        if (!$account) {
            // Try to seed the accounts table first
            self::seedDefaultAccounts();
            
            // Try again after seeding
            $account = self::where('code', $code)->first();
            
            if (!$account) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                    "Account with code '{$code}' not found. Please run: php artisan db:seed --class=AccountSeeder"
                );
            }
        }
        
        return $account;
    }
    
    /**
     * Seed default accounts if they don't exist.
     */
    public static function seedDefaultAccounts(): void
    {
        $accounts = [
            // Assets (1xxx)
            ['code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Parent account for all assets', 'is_system' => true],
            ['code' => '1100', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Cash on hand', 'is_system' => true],
            ['code' => '1200', 'name' => 'Bank', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Bank accounts', 'is_system' => true],
            ['code' => '1300', 'name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Money owed by customers', 'is_system' => true],
            ['code' => '1400', 'name' => 'Inventory', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Stock and supplies', 'is_system' => true],
            
            // Liabilities (2xxx)
            ['code' => '2100', 'name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Money owed to suppliers', 'is_system' => true],
            ['code' => '2200', 'name' => 'VAT Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'VAT collected on sales', 'is_system' => true],
            ['code' => '2300', 'name' => 'Input VAT', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'VAT paid on purchases', 'is_system' => true],
            ['code' => '2400', 'name' => 'NSSF Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'NSSF contributions payable', 'is_system' => true],
            ['code' => '2500', 'name' => 'PAYE Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'PAYE tax payable', 'is_system' => true],
            
            // Revenue (4xxx)
            ['code' => '4100', 'name' => 'Room Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Income from room bookings', 'is_system' => true],
            ['code' => '4200', 'name' => 'F&B Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Food and beverage income', 'is_system' => true],
            ['code' => '4300', 'name' => 'Laundry Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Laundry service income', 'is_system' => true],
            
            // Expenses (6xxx)
            ['code' => '6100', 'name' => 'Salary Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Employee salaries', 'is_system' => true],
            ['code' => '6200', 'name' => 'NSSF Employer Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Employer NSSF contribution', 'is_system' => true],
        ];

        foreach ($accounts as $account) {
            self::firstOrCreate(
                ['code' => $account['code']],
                array_merge($account, ['is_active' => true])
            );
        }
    }

    public function children()    { return $this->hasMany(Account::class, 'parent_id'); }
    public function parent()      { return $this->belongsTo(Account::class, 'parent_id'); }
    public function journalLines(){ return $this->hasMany(JournalLine::class); }
}
