# 💰 Accounting & Financial Records Module — Laravel + Blade
### Full Double-Entry · All Revenue Streams · P&L · Balance Sheet · VAT/TRA · Payroll · Bank Reconciliation

> Built on your existing hotel system.
> Pulls data automatically from: Bookings, Restaurant, Laundry, Procurement, Petty Cash.
> New role: ACCOUNTANT.
> Same Laravel folder structure. No surprises.

---

## 📋 Table of Contents

1. [Overview & Architecture](#1-overview--architecture)
2. [New Role — ACCOUNTANT](#2-new-role--accountant)
3. [Chart of Accounts](#3-chart-of-accounts)
4. [File Map](#4-file-map)
5. [Migrations](#5-migrations)
6. [Models](#6-models)
7. [How Revenue Auto-Posts to Ledger](#7-how-revenue-auto-posts-to-ledger)
8. [Controllers](#8-controllers)
9. [Blade Views](#9-blade-views)
10. [Routes](#10-routes)
11. [Reports — P&L, Balance Sheet, VAT](#11-reports)
12. [Payroll](#12-payroll)
13. [Bank & Cash Reconciliation](#13-bank--cash-reconciliation)
14. [Business Rules](#14-business-rules)
15. [Build Order & Checklist](#15-build-order--checklist)

---

## 1. Overview & Architecture

### How It All Connects

```
REVENUE SOURCES (auto-post to journal)
    ├── Room Bookings         → DR Cash/AR   CR Room Revenue
    ├── Restaurant & Bar      → DR Cash/AR   CR F&B Revenue
    ├── Laundry               → DR Cash/AR   CR Laundry Revenue
    └── Store Sales           → DR Cash/AR   CR Store Revenue

EXPENSE SOURCES (auto-post to journal)
    ├── Procurement LPOs      → DR Purchases     CR Accounts Payable
    ├── GRN Confirmations     → DR Inventory     CR Accounts Payable
    ├── Petty Cash (approved) → DR Expense acct  CR Petty Cash
    └── Payroll               → DR Salary Expense CR Cash/Bank

ACCOUNTING MODULE
    ├── Chart of Accounts     ← defines all GL accounts
    ├── Journal Entries       ← every financial event (double-entry)
    ├── General Ledger        ← all transactions per account
    ├── Trial Balance         ← sum of all debit/credit balances
    ├── Profit & Loss         ← Revenue minus Expenses by period
    ├── Balance Sheet         ← Assets = Liabilities + Equity
    ├── VAT/TRA Report        ← taxable transactions for TRA filing
    ├── Payroll               ← staff salaries, deductions, NSSF, PAYE
    └── Bank Reconciliation   ← match bank statement to system records
```

### Double-Entry Rule — Always Balanced

```
Every transaction:    DEBITS = CREDITS
Example — Room sale settled by cash:
    DR  Cash Account           500,000
    CR  Room Revenue Account   500,000

Example — LPO goods received on credit:
    DR  Inventory / Purchases  200,000
    CR  Accounts Payable       200,000

Example — Pay supplier:
    DR  Accounts Payable       200,000
    CR  Bank Account           200,000
```

---

## 2. New Role — ACCOUNTANT

Add to `RoleSeeder.php`:

```php
['name' => 'ACCOUNTANT', 'description' => 'Full financial records, reports, payroll, reconciliation'],
```

Add migration:

```php
// database/migrations/xxxx_add_accountant_role.php
public function up(): void
{
    \App\Models\Role::updateOrCreate(
        ['name' => 'ACCOUNTANT'],
        ['description' => 'Full accounting, financial reports, payroll, bank reconciliation']
    );
}
```

### Role Permission Matrix

| Feature | ACCOUNTANT | STORE_MANAGER | SUPERVISOR | CASHIER |
|---|---|---|---|---|
| Chart of Accounts | Full CRUD | View | View | — |
| Journal Entries | Create + View | View | — | — |
| General Ledger | View | View | — | — |
| P&L Report | View | View | View | — |
| Balance Sheet | View | View | — | — |
| VAT/TRA Report | View + Export | View | — | — |
| Payroll | Full CRUD | View | Approve | — |
| Bank Reconciliation | Full CRUD | View | — | — |
| Invoice Management | View + Print | View + Print | — | Print |

---

## 3. Chart of Accounts

### Standard Account Structure for Hotel

```
ASSETS (1xxx)
    1100  Cash on Hand
    1200  Bank Account — Main
    1210  Bank Account — Secondary
    1300  Accounts Receivable (Guests)
    1400  Inventory / Stock
    1500  Prepaid Expenses

LIABILITIES (2xxx)
    2100  Accounts Payable (Suppliers)
    2200  VAT Payable (Output VAT)
    2300  VAT Receivable (Input VAT)
    2400  NSSF Payable
    2500  PAYE Payable
    2600  Deposits — Guest Advance Payments

EQUITY (3xxx)
    3100  Owner's Capital
    3200  Retained Earnings

REVENUE (4xxx)
    4100  Room Revenue
    4200  Food & Beverage Revenue
    4300  Laundry Revenue
    4400  Store / Miscellaneous Revenue

COST OF GOODS SOLD (5xxx)
    5100  Cost of Food & Beverages
    5200  Cost of Laundry Supplies
    5300  Cost of Store Items

EXPENSES (6xxx)
    6100  Salaries & Wages
    6200  NSSF — Employer Contribution
    6300  Utilities (Electricity, Water)
    6400  Repairs & Maintenance
    6500  Office Supplies
    6600  Transport & Fuel
    6700  Marketing & Advertising
    6800  Petty Cash Expenses
    6900  Depreciation
```

---

## 4. File Map

```
app/
├── Http/
│   └── Controllers/
│       └── Accounting/                              ← new subfolder
│           ├── ChartOfAccountsController.php
│           ├── JournalEntryController.php
│           ├── GeneralLedgerController.php
│           ├── InvoiceController.php
│           ├── PayrollController.php
│           ├── BankReconciliationController.php
│           └── AccountingReportController.php
│
├── Services/
│   └── AccountingService.php                        ← core posting engine
│
└── Models/
    ├── Account.php                                  ← chart of accounts
    ├── JournalEntry.php                             ← journal header
    ├── JournalLine.php                              ← debit/credit lines
    ├── Invoice.php                                  ← guest invoices
    ├── InvoiceLine.php
    ├── PayrollRun.php                               ← monthly payroll batch
    ├── PayrollLine.php                              ← per-employee line
    └── BankReconciliation.php

database/
└── migrations/
    ├── xxxx_add_accountant_role.php
    ├── xxxx_create_accounts_table.php
    ├── xxxx_create_journal_entries_table.php
    ├── xxxx_create_journal_lines_table.php
    ├── xxxx_create_invoices_table.php
    ├── xxxx_create_invoice_lines_table.php
    ├── xxxx_create_payroll_runs_table.php
    ├── xxxx_create_payroll_lines_table.php
    └── xxxx_create_bank_reconciliations_table.php

resources/
└── views/
    └── accounting/
        ├── layout.blade.php
        ├── dashboard/
        │   └── index.blade.php
        ├── accounts/
        │   ├── index.blade.php
        │   └── create.blade.php
        ├── journal/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── show.blade.php
        ├── ledger/
        │   └── index.blade.php
        ├── invoices/
        │   ├── index.blade.php
        │   └── show.blade.php                       ← printable A4
        ├── payroll/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── show.blade.php
        ├── reconciliation/
        │   ├── index.blade.php
        │   └── create.blade.php
        └── reports/
            ├── profit-loss.blade.php
            ├── balance-sheet.blade.php
            ├── trial-balance.blade.php
            └── vat.blade.php
```

---

## 5. Migrations

---

**File:** `database/migrations/xxxx_create_accounts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();           // 4100, 6100, etc.
            $table->string('name', 150);                    // Room Revenue, Cash on Hand
            $table->enum('type', [
                'asset',
                'liability',
                'equity',
                'revenue',
                'expense',
                'cogs',                                     // cost of goods sold
            ]);
            $table->enum('normal_balance', ['debit', 'credit']); // asset/expense = debit, others = credit
            $table->uuid('parent_id')->nullable();          // for account grouping
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);   // system accounts cannot be deleted
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('accounts'); }
};
```

---

**File:** `database/migrations/xxxx_create_journal_entries_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_no', 30)->unique();       // JE-20240222-0001
            $table->date('entry_date');
            $table->string('reference', 200)->nullable();   // "Booking BK-001", "GRN-0042"
            $table->string('source', 50);                   // booking, restaurant, laundry, procurement, payroll, manual
            $table->uuid('source_id')->nullable();          // FK to source record (order_id, lpo_id, etc.)
            $table->text('description');
            $table->decimal('total_debit', 14, 2);
            $table->decimal('total_credit', 14, 2);
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('posted');
            $table->uuid('created_by');
            $table->uuid('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['entry_date', 'source']);
        });
    }

    public function down(): void { Schema::dropIfExists('journal_entries'); }
};
```

---

**File:** `database/migrations/xxxx_create_journal_lines_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_entry_id');
            $table->uuid('account_id');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('journal_entry_id')
                  ->references('id')->on('journal_entries')->cascadeOnDelete();
            $table->foreign('account_id')
                  ->references('id')->on('accounts');
        });
    }

    public function down(): void { Schema::dropIfExists('journal_lines'); }
};
```

---

**File:** `database/migrations/xxxx_create_invoices_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_no', 30)->unique();     // INV-20240222-0001
            $table->enum('invoice_type', [
                'guest_checkout',     // final hotel bill
                'restaurant',         // restaurant only
                'laundry',            // laundry only
            ]);
            $table->uuid('guest_id')->nullable();
            $table->string('guest_name', 150);
            $table->uuid('booking_id')->nullable();
            $table->date('invoice_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0); // 18% VAT
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'charge_to_booking'])->nullable();
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->uuid('issued_by');
            $table->timestamps();

            $table->foreign('issued_by')->references('id')->on('users');
            $table->index(['invoice_date', 'invoice_type']);
        });
    }

    public function down(): void { Schema::dropIfExists('invoices'); }
};
```

---

**File:** `database/migrations/xxxx_create_invoice_lines_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->string('description', 255);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->boolean('is_taxable')->default(true);
            $table->timestamps();

            $table->foreign('invoice_id')
                  ->references('id')->on('invoices')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('invoice_lines'); }
};
```

---

**File:** `database/migrations/xxxx_create_payroll_runs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no', 30)->unique();   // PAY-2024-03
            $table->string('period_month', 7);              // 2024-03 (YYYY-MM)
            $table->date('pay_date');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_nssf_employee', 12, 2)->default(0);
            $table->decimal('total_nssf_employer', 12, 2)->default(0);
            $table->decimal('total_paye', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->uuid('prepared_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('prepared_by')->references('id')->on('users');
            $table->unique('period_month');                 // one payroll run per month
        });
    }

    public function down(): void { Schema::dropIfExists('payroll_runs'); }
};
```

---

**File:** `database/migrations/xxxx_create_payroll_lines_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_run_id');
            $table->uuid('user_id');                        // the staff member
            $table->string('staff_name', 150);              // snapshot
            $table->string('role', 100);                    // snapshot
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('nssf_employee', 10, 2)->default(0);  // 5% of gross
            $table->decimal('nssf_employer', 10, 2)->default(0);  // 15% of gross
            $table->decimal('paye', 10, 2)->default(0);           // per TRA PAYE bands
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('payroll_run_id')
                  ->references('id')->on('payroll_runs')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void { Schema::dropIfExists('payroll_lines'); }
};
```

---

**File:** `database/migrations/xxxx_create_bank_reconciliations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no', 30)->unique();    // BNK-REC-2024-03
            $table->uuid('account_id');                      // the bank account being reconciled
            $table->string('period_month', 7);               // 2024-03
            $table->date('statement_date');
            $table->decimal('statement_opening_balance', 14, 2);
            $table->decimal('statement_closing_balance', 14, 2);
            $table->decimal('system_opening_balance', 14, 2);
            $table->decimal('system_closing_balance', 14, 2);
            $table->decimal('difference', 14, 2)->default(0); // should be 0 when reconciled
            $table->enum('status', ['open', 'reconciled'])->default('open');
            $table->text('notes')->nullable();
            $table->uuid('prepared_by');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('prepared_by')->references('id')->on('users');
        });
    }

    public function down(): void { Schema::dropIfExists('bank_reconciliations'); }
};
```

---

## 6. Models

**File:** `app/Models/Account.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
```

---

**File:** `app/Models/JournalEntry.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
```

---

**File:** `app/Models/JournalLine.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    use HasUuids;

    protected $fillable = [
        'journal_entry_id', 'account_id', 'type', 'amount', 'notes',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function entry()   { return $this->belongsTo(JournalEntry::class, 'journal_entry_id'); }
    public function account() { return $this->belongsTo(Account::class); }
}
```

---

**File:** `app/Models/Invoice.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_no', 'invoice_type', 'guest_id', 'guest_name',
        'booking_id', 'invoice_date', 'subtotal', 'discount',
        'tax_amount', 'total', 'payment_method', 'status',
        'notes', 'issued_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $inv) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $inv->invoice_no = 'INV-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    public function lines()   { return $this->hasMany(InvoiceLine::class); }
    public function issuer()  { return $this->belongsTo(User::class, 'issued_by'); }
}
```

---

**File:** `app/Models/PayrollRun.php`

```php
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
```

---

## 7. How Revenue Auto-Posts to Ledger

### `app/Services/AccountingService.php`

This is the core engine. Every module calls this when money moves.

```php
<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create a balanced journal entry.
     *
     * @param array $data [
     *   'date'        => '2024-03-01',
     *   'description' => 'Room booking settled — BK-0042',
     *   'source'      => 'booking',
     *   'source_id'   => $bookingId,
     *   'reference'   => 'BK-0042',
     *   'lines' => [
     *     ['account_code' => '1100', 'type' => 'debit',  'amount' => 500000],
     *     ['account_code' => '4100', 'type' => 'credit', 'amount' => 500000],
     *   ]
     * ]
     */
    public function post(array $data, string $actorId): JournalEntry
    {
        return DB::transaction(function () use ($data, $actorId) {

            $totalDebit  = collect($data['lines'])->where('type', 'debit')->sum('amount');
            $totalCredit = collect($data['lines'])->where('type', 'credit')->sum('amount');

            // Hard stop — never post an unbalanced entry
            abort_if(
                abs($totalDebit - $totalCredit) > 0.01,
                422,
                "Journal entry is not balanced. Debits: {$totalDebit} Credits: {$totalCredit}"
            );

            $entry = JournalEntry::create([
                'entry_date'   => $data['date'],
                'description'  => $data['description'],
                'source'       => $data['source'],
                'source_id'    => $data['source_id'] ?? null,
                'reference'    => $data['reference'] ?? null,
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'status'       => 'posted',
                'created_by'   => $actorId,
                'posted_by'    => $actorId,
                'posted_at'    => now(),
            ]);

            foreach ($data['lines'] as $line) {
                $account = Account::findByCode($line['account_code']);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $account->id,
                    'type'             => $line['type'],
                    'amount'           => $line['amount'],
                    'notes'            => $line['notes'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    // ─── Pre-built posting methods for each module ───────────────────────────

    /**
     * POST: Room booking settled
     * DR Cash/Bank  CR Room Revenue  CR VAT Payable
     */
    public function postBookingSettlement(
        string $bookingRef,
        string $bookingId,
        float $amount,
        string $paymentMethod,
        string $actorId
    ): JournalEntry {
        $cashAccountCode = $paymentMethod === 'bank_transfer' ? '1200' : '1100';
        $netAmount = round($amount / 1.18, 2);   // extract VAT from inclusive amount
        $vatAmount = $amount - $netAmount;

        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Room revenue — {$bookingRef}",
            'source'      => 'booking',
            'source_id'   => $bookingId,
            'reference'   => $bookingRef,
            'lines' => [
                ['account_code' => $cashAccountCode, 'type' => 'debit',  'amount' => $amount],
                ['account_code' => '4100',           'type' => 'credit', 'amount' => $netAmount],
                ['account_code' => '2200',           'type' => 'credit', 'amount' => $vatAmount],
            ],
        ], $actorId);
    }

    /**
     * POST: Restaurant order settled
     * DR Cash/Bank  CR F&B Revenue  CR VAT Payable
     */
    public function postRestaurantSettlement(
        string $orderNo,
        string $orderId,
        float $amount,
        string $paymentMethod,
        string $actorId
    ): JournalEntry {
        $cashAccountCode = $paymentMethod === 'card' ? '1200' : '1100';
        $netAmount = round($amount / 1.18, 2);
        $vatAmount = $amount - $netAmount;

        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "F&B revenue — {$orderNo}",
            'source'      => 'restaurant',
            'source_id'   => $orderId,
            'reference'   => $orderNo,
            'lines' => [
                ['account_code' => $cashAccountCode, 'type' => 'debit',  'amount' => $amount],
                ['account_code' => '4200',           'type' => 'credit', 'amount' => $netAmount],
                ['account_code' => '2200',           'type' => 'credit', 'amount' => $vatAmount],
            ],
        ], $actorId);
    }

    /**
     * POST: Laundry order settled
     * DR Cash/Bank  CR Laundry Revenue  CR VAT Payable
     */
    public function postLaundrySettlement(
        string $orderNo,
        string $orderId,
        float $amount,
        string $paymentMethod,
        string $actorId
    ): JournalEntry {
        $cashAccountCode = in_array($paymentMethod, ['card', 'bank_transfer']) ? '1200' : '1100';
        $netAmount = round($amount / 1.18, 2);
        $vatAmount = $amount - $netAmount;

        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Laundry revenue — {$orderNo}",
            'source'      => 'laundry',
            'source_id'   => $orderId,
            'reference'   => $orderNo,
            'lines' => [
                ['account_code' => $cashAccountCode, 'type' => 'debit',  'amount' => $amount],
                ['account_code' => '4300',           'type' => 'credit', 'amount' => $netAmount],
                ['account_code' => '2200',           'type' => 'credit', 'amount' => $vatAmount],
            ],
        ], $actorId);
    }

    /**
     * POST: GRN confirmed — goods received on credit
     * DR Inventory  DR Input VAT  CR Accounts Payable
     */
    public function postGrnConfirmation(
        string $grnNo,
        string $grnId,
        float $netAmount,
        float $vatAmount,
        string $actorId
    ): JournalEntry {
        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Goods received — {$grnNo}",
            'source'      => 'procurement',
            'source_id'   => $grnId,
            'reference'   => $grnNo,
            'lines' => [
                ['account_code' => '1400', 'type' => 'debit',  'amount' => $netAmount],
                ['account_code' => '2300', 'type' => 'debit',  'amount' => $vatAmount],
                ['account_code' => '2100', 'type' => 'credit', 'amount' => $netAmount + $vatAmount],
            ],
        ], $actorId);
    }

    /**
     * POST: Supplier payment made
     * DR Accounts Payable  CR Bank
     */
    public function postSupplierPayment(
        string $reference,
        string $sourceId,
        float $amount,
        string $actorId
    ): JournalEntry {
        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Supplier payment — {$reference}",
            'source'      => 'procurement',
            'source_id'   => $sourceId,
            'reference'   => $reference,
            'lines' => [
                ['account_code' => '2100', 'type' => 'debit',  'amount' => $amount],
                ['account_code' => '1200', 'type' => 'credit', 'amount' => $amount],
            ],
        ], $actorId);
    }

    /**
     * POST: Petty cash expense approved
     * DR Expense Account  CR Petty Cash (1100)
     */
    public function postPettyCash(
        string $reference,
        string $sourceId,
        float $amount,
        string $expenseAccountCode,
        string $actorId
    ): JournalEntry {
        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Petty cash expense — {$reference}",
            'source'      => 'petty_cash',
            'source_id'   => $sourceId,
            'reference'   => $reference,
            'lines' => [
                ['account_code' => $expenseAccountCode, 'type' => 'debit',  'amount' => $amount],
                ['account_code' => '1100',              'type' => 'credit', 'amount' => $amount],
            ],
        ], $actorId);
    }

    /**
     * POST: Payroll approved
     * DR Salary Expense  DR NSSF Employer  CR Cash  CR NSSF Payable  CR PAYE Payable
     */
    public function postPayroll(
        string $reference,
        string $payrollId,
        float $grossSalary,
        float $nssf_employer,
        float $netSalary,
        float $nssf_payable,
        float $paye_payable,
        string $actorId
    ): JournalEntry {
        return $this->post([
            'date'        => now()->toDateString(),
            'description' => "Payroll — {$reference}",
            'source'      => 'payroll',
            'source_id'   => $payrollId,
            'reference'   => $reference,
            'lines' => [
                ['account_code' => '6100', 'type' => 'debit',  'amount' => $grossSalary],
                ['account_code' => '6200', 'type' => 'debit',  'amount' => $nssf_employer],
                ['account_code' => '1100', 'type' => 'credit', 'amount' => $netSalary],
                ['account_code' => '2400', 'type' => 'credit', 'amount' => $nssf_payable],
                ['account_code' => '2500', 'type' => 'credit', 'amount' => $paye_payable],
            ],
        ], $actorId);
    }
}
```

---

### Wire Into Existing Controllers

**In `OrderController::settle()`** (restaurant):

```php
// After existing settlement logic, add:
app(\App\Services\AccountingService::class)->postRestaurantSettlement(
    orderNo: $order->order_number,
    orderId: $order->id,
    amount:  (float) $order->total,
    paymentMethod: $request->payment_method,
    actorId: auth()->id()
);
```

**In `LaundryOrderController::settle()`**:

```php
app(\App\Services\AccountingService::class)->postLaundrySettlement(
    orderNo: $laundryOrder->order_number,
    orderId: $laundryOrder->id,
    amount:  (float) $laundryOrder->total,
    paymentMethod: $request->payment_method,
    actorId: auth()->id()
);
```

**In `GoodsReceivedNoteController::confirm()`**:

```php
app(\App\Services\AccountingService::class)->postGrnConfirmation(
    grnNo:      $goodsReceivedNote->grn_no,
    grnId:      $goodsReceivedNote->id,
    netAmount:  (float) $goodsReceivedNote->subtotal,
    vatAmount:  (float) $goodsReceivedNote->tax,
    actorId:    auth()->id()
);
```

**In `PettyCashController::approve()`**:

```php
// Map category to expense account code
$expenseCode = match($pettyCash->category) {
    'transport' => '6600',
    'repairs'   => '6400',
    'office'    => '6500',
    default     => '6800',
};
app(\App\Services\AccountingService::class)->postPettyCash(
    reference:           $pettyCash->reference_no,
    sourceId:            $pettyCash->id,
    amount:              (float) $pettyCash->amount,
    expenseAccountCode:  $expenseCode,
    actorId:             auth()->id()
);
```

---

## 8. Controllers

**File:** `app/Http/Controllers/Accounting/AccountingReportController.php`

```php
<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingReportController extends Controller
{
    // GET /accounting/reports/profit-loss
    public function profitLoss(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Revenue accounts (4xxx)
        $revenueAccounts = Account::where('type', 'revenue')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account'  => $acc,
                'balance'  => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // COGS accounts (5xxx)
        $cogsAccounts = Account::where('type', 'cogs')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // Expense accounts (6xxx)
        $expenseAccounts = Account::where('type', 'expense')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        $totalRevenue  = $revenueAccounts->sum('balance');
        $totalCogs     = $cogsAccounts->sum('balance');
        $grossProfit   = $totalRevenue - $totalCogs;
        $totalExpenses = $expenseAccounts->sum('balance');
        $netProfit     = $grossProfit - $totalExpenses;

        return view('accounting.reports.profit-loss', compact(
            'revenueAccounts', 'cogsAccounts', 'expenseAccounts',
            'totalRevenue', 'totalCogs', 'grossProfit',
            'totalExpenses', 'netProfit',
            'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/reports/balance-sheet
    public function balanceSheet(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $assets      = Account::where('type', 'asset')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $liabilities = Account::where('type', 'liability')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $equity      = Account::where('type', 'equity')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);

        $totalAssets      = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity      = $equity->sum('balance');

        return view('accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'asOf'
        ));
    }

    // GET /accounting/reports/trial-balance
    public function trialBalance(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOf) {
                $rawBalance = $acc->getBalance(null, $asOf);
                return [
                    'account' => $acc,
                    'debit'   => $acc->normal_balance === 'debit'  && $rawBalance > 0 ? $rawBalance : 0,
                    'credit'  => $acc->normal_balance === 'credit' && $rawBalance > 0 ? $rawBalance : 0,
                ];
            })
            ->filter(fn($row) => $row['debit'] + $row['credit'] > 0);

        $totalDebits  = $accounts->sum('debit');
        $totalCredits = $accounts->sum('credit');

        return view('accounting.reports.trial-balance', compact(
            'accounts', 'totalDebits', 'totalCredits', 'asOf'
        ));
    }

    // GET /accounting/reports/vat
    public function vatReport(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Output VAT collected (account 2200)
        $outputVatAccount = Account::findByCode('2200');
        $outputVat = $outputVatAccount->getBalance($dateFrom, $dateTo);

        // Input VAT paid on purchases (account 2300)
        $inputVatAccount = Account::findByCode('2300');
        $inputVat = $inputVatAccount->getBalance($dateFrom, $dateTo);

        $vatPayable = $outputVat - $inputVat;

        // Detailed VAT lines for TRA filing
        $vatLines = JournalLine::with(['entry', 'account'])
            ->whereHas('account', fn($q) => $q->whereIn('code', ['2200', '2300']))
            ->whereHas('entry', fn($q) => $q
                ->where('status', 'posted')
                ->whereDate('entry_date', '>=', $dateFrom)
                ->whereDate('entry_date', '<=', $dateTo)
            )
            ->get();

        return view('accounting.reports.vat', compact(
            'outputVat', 'inputVat', 'vatPayable', 'vatLines', 'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/ledger
    public function ledger(Request $request): View
    {
        $accounts  = Account::where('is_active', true)->orderBy('code')->get();
        $account   = null;
        $lines     = collect();

        if ($request->account_id) {
            $account  = Account::findOrFail($request->account_id);
            $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
            $dateTo   = $request->date_to   ?? now()->toDateString();

            $lines = JournalLine::with('entry')
                ->where('account_id', $account->id)
                ->whereHas('entry', fn($q) => $q
                    ->where('status', 'posted')
                    ->whereDate('entry_date', '>=', $dateFrom)
                    ->whereDate('entry_date', '<=', $dateTo)
                )
                ->orderBy('created_at')
                ->get();
        }

        return view('accounting.ledger.index', compact('accounts', 'account', 'lines'));
    }
}
```

---

**File:** `app/Http/Controllers/Accounting/PayrollController.php`

```php
<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PayrollLine;
use App\Models\PayrollRun;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(): View
    {
        $payrolls = PayrollRun::with(['preparer', 'approver'])
            ->latest()
            ->paginate(20);

        return view('accounting.payroll.index', compact('payrolls'));
    }

    public function create(): View
    {
        $staff = User::whereHas('role')->where('is_active', true)->with('role')->get();
        return view('accounting.payroll.create', compact('staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'period_month'           => 'required|string|regex:/^\d{4}-\d{2}$/',
            'pay_date'               => 'required|date',
            'notes'                  => 'nullable|string',
            'lines'                  => 'required|array|min:1',
            'lines.*.user_id'        => 'required|uuid|exists:users,id',
            'lines.*.basic_salary'   => 'required|numeric|min:0',
            'lines.*.allowances'     => 'nullable|numeric|min:0',
        ]);

        abort_if(
            PayrollRun::where('period_month', $data['period_month'])->exists(),
            422,
            "Payroll for {$data['period_month']} already exists."
        );

        $run = DB::transaction(function () use ($data) {
            $run = PayrollRun::create([
                'period_month' => $data['period_month'],
                'pay_date'     => $data['pay_date'],
                'notes'        => $data['notes'] ?? null,
                'status'       => 'draft',
                'prepared_by'  => auth()->id(),
            ]);

            foreach ($data['lines'] as $line) {
                $user        = User::with('role')->findOrFail($line['user_id']);
                $basic       = (float) $line['basic_salary'];
                $allowances  = (float) ($line['allowances'] ?? 0);
                $gross       = $basic + $allowances;

                // Tanzania NSSF: Employee 5%, Employer 15%
                $nssf_employee = round($gross * 0.05, 2);
                $nssf_employer = round($gross * 0.15, 2);

                // Tanzania PAYE bands (2024)
                $paye = $this->calculatePaye($gross);

                $net = $gross - $nssf_employee - $paye;

                PayrollLine::create([
                    'payroll_run_id'   => $run->id,
                    'user_id'          => $user->id,
                    'staff_name'       => $user->name,
                    'role'             => $user->role->name,
                    'basic_salary'     => $basic,
                    'allowances'       => $allowances,
                    'gross_salary'     => $gross,
                    'nssf_employee'    => $nssf_employee,
                    'nssf_employer'    => $nssf_employer,
                    'paye'             => $paye,
                    'net_salary'       => $net,
                ]);
            }

            $run->recalculate();
            return $run;
        });

        return redirect()
            ->route('accounting.payroll.show', $run)
            ->with('success', "Payroll {$run->reference_no} created.");
    }

    public function show(PayrollRun $payrollRun): View
    {
        $payrollRun->load(['lines.user', 'preparer', 'approver']);
        return view('accounting.payroll.show', compact('payrollRun'));
    }

    // POST — approve payroll → post to journal
    public function approve(PayrollRun $payrollRun, AccountingService $accounting): RedirectResponse
    {
        abort_if($payrollRun->status !== 'draft', 422, 'Only draft payrolls can be approved.');

        DB::transaction(function () use ($payrollRun, $accounting) {
            $payrollRun->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Post to accounting journal
            $accounting->postPayroll(
                reference:     $payrollRun->reference_no,
                payrollId:     $payrollRun->id,
                grossSalary:   (float) $payrollRun->total_gross,
                nssf_employer: (float) $payrollRun->total_nssf_employer,
                netSalary:     (float) $payrollRun->total_net,
                nssf_payable:  (float) ($payrollRun->total_nssf_employee + $payrollRun->total_nssf_employer),
                paye_payable:  (float) $payrollRun->total_paye,
                actorId:       auth()->id()
            );
        });

        return redirect()
            ->route('accounting.payroll.show', $payrollRun)
            ->with('success', "Payroll {$payrollRun->reference_no} approved and posted to ledger.");
    }

    // Tanzania PAYE calculation (TRA bands 2024)
    private function calculatePaye(float $monthlyGross): float
    {
        $annual = $monthlyGross * 12;
        $paye   = 0;

        if ($annual <= 2_040_000)      $paye = 0;
        elseif ($annual <= 4_320_000)  $paye = ($annual - 2_040_000) * 0.08;
        elseif ($annual <= 6_480_000)  $paye = 182_400 + ($annual - 4_320_000) * 0.20;
        elseif ($annual <= 8_640_000)  $paye = 614_400 + ($annual - 6_480_000) * 0.25;
        else                           $paye = 1_154_400 + ($annual - 8_640_000) * 0.30;

        return round($paye / 12, 2); // monthly PAYE
    }
}
```

---

## 9. Blade Views (Key Views)

---

**File:** `resources/views/accounting/layout.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Accounting') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-60 bg-slate-900 text-slate-200 flex-shrink-0">
        <div class="px-4 py-5 border-b border-slate-700">
            <div class="font-bold text-white">💰 Accounting</div>
            <div class="text-xs text-slate-400 mt-0.5">Financial Records</div>
        </div>
        <nav class="py-4 px-2 space-y-0.5 text-sm">
            <a href="{{ route('accounting.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('accounting.accounts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.accounts*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                📒 Chart of Accounts
            </a>
            <a href="{{ route('accounting.journal.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.journal*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                📝 Journal Entries
            </a>
            <a href="{{ route('accounting.ledger') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.ledger*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                📖 General Ledger
            </a>
            <a href="{{ route('accounting.invoices.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.invoices*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                🧾 Invoices
            </a>
            <a href="{{ route('accounting.payroll.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.payroll*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                💵 Payroll
            </a>
            <a href="{{ route('accounting.reconciliation.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700
                      {{ request()->routeIs('accounting.reconciliation*') ? 'bg-slate-700 text-white' : 'text-slate-300' }}">
                🏦 Bank Reconciliation
            </a>
            <div class="pt-3 pb-1 px-3 text-xs text-slate-500 uppercase tracking-wider">Reports</div>
            <a href="{{ route('accounting.reports.profit-loss') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700 text-slate-300">
                📈 Profit & Loss
            </a>
            <a href="{{ route('accounting.reports.balance-sheet') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700 text-slate-300">
                ⚖️ Balance Sheet
            </a>
            <a href="{{ route('accounting.reports.trial-balance') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700 text-slate-300">
                🔢 Trial Balance
            </a>
            <a href="{{ route('accounting.reports.vat') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700 text-slate-300">
                🇹🇿 VAT / TRA Report
            </a>
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-3 flex justify-between items-center">
            <h1 class="text-sm font-semibold text-gray-700">@yield('page-title', 'Accounting')</h1>
            <span class="text-xs text-gray-400">
                {{ auth()->user()->name }} — {{ auth()->user()->role->name }}
            </span>
        </header>

        <div class="px-6 mt-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <main class="flex-1 px-6 py-4">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
```

---

**File:** `resources/views/accounting/reports/profit-loss.blade.php`

```blade
@extends('accounting.layout')
@section('title', 'Profit & Loss')
@section('page-title', 'Profit & Loss Statement')

@section('content')

{{-- Date filter --}}
<form method="GET" class="flex gap-3 mb-6 items-end no-print">
    <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}"
               class="border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" name="date_to" value="{{ $dateTo }}"
               class="border rounded px-3 py-2 text-sm">
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Apply</button>
    <button type="button" onclick="window.print()"
            class="bg-gray-600 text-white px-4 py-2 rounded text-sm">🖨️ Print</button>
</form>

<div class="print-area max-w-3xl">

    {{-- Header --}}
    <div class="text-center mb-6 print-header">
        <h1 class="text-xl font-bold">Grand Hotel</h1>
        <h2 class="text-lg font-semibold text-gray-700">Profit & Loss Statement</h2>
        <p class="text-sm text-gray-400">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
            to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
        </p>
    </div>

    {{-- Revenue --}}
    <div class="bg-white rounded shadow overflow-hidden mb-4">
        <div class="px-5 py-3 bg-blue-50 border-b">
            <h3 class="font-semibold text-blue-800">Revenue</h3>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @foreach($revenueAccounts as $row)
                @if($row['balance'] > 0)
                <tr class="border-b">
                    <td class="px-5 py-2 text-gray-600">{{ $row['account']->code }}</td>
                    <td class="px-5 py-2">{{ $row['account']->name }}</td>
                    <td class="px-5 py-2 text-right">{{ number_format($row['balance'], 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-blue-50 font-semibold">
                    <td colspan="2" class="px-5 py-3">Total Revenue</td>
                    <td class="px-5 py-3 text-right text-blue-700">
                        {{ number_format($totalRevenue, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- COGS --}}
    <div class="bg-white rounded shadow overflow-hidden mb-4">
        <div class="px-5 py-3 bg-orange-50 border-b">
            <h3 class="font-semibold text-orange-800">Cost of Goods Sold</h3>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @foreach($cogsAccounts as $row)
                @if($row['balance'] > 0)
                <tr class="border-b">
                    <td class="px-5 py-2 text-gray-600">{{ $row['account']->code }}</td>
                    <td class="px-5 py-2">{{ $row['account']->name }}</td>
                    <td class="px-5 py-2 text-right">{{ number_format($row['balance'], 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-orange-50 font-semibold">
                    <td colspan="2" class="px-5 py-3">Gross Profit</td>
                    <td class="px-5 py-3 text-right
                        {{ $grossProfit >= 0 ? 'text-green-700' : 'text-red-600' }}">
                        {{ number_format($grossProfit, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Expenses --}}
    <div class="bg-white rounded shadow overflow-hidden mb-4">
        <div class="px-5 py-3 bg-red-50 border-b">
            <h3 class="font-semibold text-red-800">Operating Expenses</h3>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @foreach($expenseAccounts as $row)
                @if($row['balance'] > 0)
                <tr class="border-b">
                    <td class="px-5 py-2 text-gray-600">{{ $row['account']->code }}</td>
                    <td class="px-5 py-2">{{ $row['account']->name }}</td>
                    <td class="px-5 py-2 text-right">{{ number_format($row['balance'], 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-red-50 font-semibold">
                    <td colspan="2" class="px-5 py-3">Total Expenses</td>
                    <td class="px-5 py-3 text-right text-red-700">{{ number_format($totalExpenses, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Net Profit --}}
    <div class="bg-white rounded shadow p-5 text-right">
        <span class="text-lg font-bold
            {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-600' }}">
            Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}: {{ number_format(abs($netProfit), 2) }}
        </span>
    </div>
</div>

@push('styles')
<style>
    @media print {
        aside, nav, header, .no-print { display: none !important; }
        body { background: white; }
        .print-area { max-width: 100%; }
        @page { size: A4; margin: 15mm; }
    }
</style>
@endpush
@endsection
```

---

## 10. Routes

```php
use App\Http\Controllers\Accounting\AccountingReportController;
use App\Http\Controllers\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\InvoiceController;
use App\Http\Controllers\Accounting\PayrollController;
use App\Http\Controllers\Accounting\BankReconciliationController;

Route::middleware(['auth', 'role:ACCOUNTANT,STORE_MANAGER'])->prefix('accounting')->name('accounting.')->group(function () {

    // Dashboard
    Route::get('/', fn() => view('accounting.dashboard.index'))->name('dashboard');

    // Chart of Accounts
    Route::get('accounts',                    [ChartOfAccountsController::class, 'index'])->name('accounts.index');
    Route::get('accounts/create',             [ChartOfAccountsController::class, 'create'])->name('accounts.create');
    Route::post('accounts',                   [ChartOfAccountsController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}/edit',     [ChartOfAccountsController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}',          [ChartOfAccountsController::class, 'update'])->name('accounts.update');

    // Journal
    Route::get('journal',                     [JournalEntryController::class, 'index'])->name('journal.index');
    Route::get('journal/create',              [JournalEntryController::class, 'create'])->name('journal.create')
         ->middleware('role:ACCOUNTANT');
    Route::post('journal',                    [JournalEntryController::class, 'store'])->name('journal.store')
         ->middleware('role:ACCOUNTANT');
    Route::get('journal/{journalEntry}',      [JournalEntryController::class, 'show'])->name('journal.show');

    // General Ledger
    Route::get('ledger',                      [AccountingReportController::class, 'ledger'])->name('ledger');

    // Invoices
    Route::get('invoices',                    [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}',          [InvoiceController::class, 'show'])->name('invoices.show');

    // Payroll
    Route::get('payroll',                     [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/create',              [PayrollController::class, 'create'])->name('payroll.create')
         ->middleware('role:ACCOUNTANT');
    Route::post('payroll',                    [PayrollController::class, 'store'])->name('payroll.store')
         ->middleware('role:ACCOUNTANT');
    Route::get('payroll/{payrollRun}',        [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('payroll/{payrollRun}/approve',[PayrollController::class, 'approve'])->name('payroll.approve')
         ->middleware('role:ACCOUNTANT,STORE_MANAGER');

    // Bank Reconciliation
    Route::get('reconciliation',              [BankReconciliationController::class, 'index'])->name('reconciliation.index');
    Route::get('reconciliation/create',       [BankReconciliationController::class, 'create'])->name('reconciliation.create');
    Route::post('reconciliation',             [BankReconciliationController::class, 'store'])->name('reconciliation.store');
    Route::get('reconciliation/{rec}',        [BankReconciliationController::class, 'show'])->name('reconciliation.show');

    // Reports
    Route::get('reports/profit-loss',         [AccountingReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/balance-sheet',       [AccountingReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('reports/trial-balance',       [AccountingReportController::class, 'trialBalance'])->name('reports.trial-balance');
    Route::get('reports/vat',                 [AccountingReportController::class, 'vatReport'])->name('reports.vat');
});
```

---

## 11. Reports

| Report | What It Shows | Who Sees It |
|---|---|---|
| Profit & Loss | Revenue vs Expenses, Net Profit/Loss by date range | ACCOUNTANT, STORE_MANAGER |
| Balance Sheet | Assets = Liabilities + Equity as of a date | ACCOUNTANT, STORE_MANAGER |
| Trial Balance | All accounts with debit/credit balances | ACCOUNTANT |
| VAT / TRA Report | Output VAT, Input VAT, Net VAT payable for TRA filing | ACCOUNTANT |
| General Ledger | All transactions per account with running balance | ACCOUNTANT, STORE_MANAGER |
| Payroll Summary | Staff salaries, NSSF, PAYE per month | ACCOUNTANT, STORE_MANAGER |

All reports are printable A4 via `window.print()` and export-ready.

---

## 12. Payroll

Tanzania-specific calculations built in:

```
NSSF Employee:   5% of gross salary
NSSF Employer:  15% of gross salary
PAYE Bands (2024):
    0 – 2,040,000/yr    →  0%
    2,040,001 – 4,320,000  → 8%
    4,320,001 – 6,480,000  → 20%
    6,480,001 – 8,640,000  → 25%
    8,640,001+             → 30%
Net Salary = Gross − NSSF Employee − PAYE
```

Payroll approval auto-posts to the journal (DR Salary Expense, CR Bank, CR NSSF Payable, CR PAYE Payable).

---

## 13. Bank & Cash Reconciliation

```
ACCOUNTANT opens a reconciliation for a given month and bank account.
Enters:
  - Bank statement opening balance
  - Bank statement closing balance
System calculates:
  - System closing balance from journal entries
  - Difference (should be zero when reconciled)
ACCOUNTANT investigates any difference.
Marks as Reconciled when difference = 0.
```

---

## 14. Business Rules

| # | Rule | Where Enforced |
|---|---|---|
| 001 | Every journal entry must balance (debits = credits) | `AccountingService::post()` aborts if unbalanced |
| 002 | Journal entries posted automatically on module events | `AccountingService` called in each module's settle/confirm/approve |
| 003 | Manual journal entries require ACCOUNTANT role | Route middleware |
| 004 | Posted journal entries cannot be deleted — only reversed | Status enum: `posted` → `reversed` only |
| 005 | Only one payroll run per calendar month | `unique('period_month')` in migration |
| 006 | Payroll approval posts to journal automatically | `PayrollController::approve()` calls `AccountingService::postPayroll()` |
| 007 | VAT rate 18% applied to all revenue | Hardcoded in `AccountingService` — update `config/accounting.php` to make configurable |
| 008 | Tanzania PAYE calculated per TRA 2024 bands | `calculatePaye()` in `PayrollController` |
| 009 | System accounts (Cash, Bank, VAT) cannot be deleted | `is_system = true` flag checked before delete |
| 010 | Balance sheet must always balance (Assets = L + E) | Validated in Balance Sheet report view |

---

## 15. Build Order & Checklist

```
STEP 1 — Add ACCOUNTANT role (1 hour)
  ✓ Migration: add_accountant_role
  ✓ Add to RoleSeeder
  ✓ Test: ACCOUNTANT can access /accounting, others cannot

STEP 2 — Chart of Accounts (1 day)
  ✓ Migration + Model: accounts table
  ✓ Seeder: ChartOfAccountsSeeder with all standard hotel accounts
  ✓ ChartOfAccountsController (index, create, store, edit, update)
  ✓ Blade views

STEP 3 — Journal + AccountingService (2 days) ← MOST CRITICAL
  ✓ Migrations: journal_entries + journal_lines
  ✓ Models: JournalEntry + JournalLine
  ✓ AccountingService::post() with balance validation
  ✓ All pre-built posting methods
  ✓ JournalEntryController
  ✓ Test: post a manual entry → debits = credits → appears in ledger

STEP 4 — Wire into existing modules (1 day)
  ✓ OrderController::settle() → postRestaurantSettlement()
  ✓ LaundryOrderController::settle() → postLaundrySettlement()
  ✓ GoodsReceivedNoteController::confirm() → postGrnConfirmation()
  ✓ PettyCashController::approve() → postPettyCash()
  ✓ BookingController::checkout() → postBookingSettlement()
  ✓ Test: settle a restaurant order → journal entry auto-created

STEP 5 — Invoices (1 day)
  ✓ Migrations + Models
  ✓ InvoiceController
  ✓ Printable A4 invoice view

STEP 6 — Reports (2 days)
  ✓ P&L Report
  ✓ Balance Sheet
  ✓ Trial Balance
  ✓ VAT/TRA Report
  ✓ General Ledger view
  ✓ All printable A4

STEP 7 — Payroll (2 days)
  ✓ Migrations + Models
  ✓ PayrollController with Tanzania PAYE/NSSF calculations
  ✓ Payroll approval → posts to journal
  ✓ Printable payslip view

STEP 8 — Bank Reconciliation (1 day)
  ✓ Migration + Model
  ✓ BankReconciliationController
  ✓ Reconciliation view

TOTAL ESTIMATED: ~11 development days

CRITICAL TEST CASES
  ✓ Settle restaurant order → journal entry created → P&L shows F&B revenue
  ✓ Approve GRN → journal entry → Balance Sheet shows inventory increase
  ✓ Run payroll → approve → journal entry → NSSF/PAYE in liabilities
  ✓ Check Trial Balance → total debits = total credits (always)
  ✓ Check Balance Sheet → total assets = liabilities + equity (always)
  ✓ VAT report → output VAT matches sum of all settled orders VAT
  ✓ Manual journal entry with unequal debits/credits → BLOCKED
```


Done

Good — the accounting module has the structure but is missing several full implementations: ChartOfAccountsController, JournalEntryController, InvoiceController, BankReconciliationController, the ChartOfAccountsSeeder, remaining Blade views (journal, ledger, balance sheet, trial balance, VAT, payroll, reconciliation), and the accounting dashboard. Let me build all of that now.
Read the full existing accounting module to avoid duplication
