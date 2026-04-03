<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets (1xxx)
            ['code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Parent account for all assets', 'is_system' => true],
            ['code' => '1100', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Cash on hand', 'is_system' => true],
            ['code' => '1200', 'name' => 'Bank', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Bank accounts', 'is_system' => true],
            ['code' => '1300', 'name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Money owed by customers', 'is_system' => true],
            ['code' => '1400', 'name' => 'Inventory', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Stock and supplies', 'is_system' => true],
            ['code' => '1500', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Expenses paid in advance', 'is_system' => true],

            // Liabilities (2xxx)
            ['code' => '2000', 'name' => 'Liabilities', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Parent account for all liabilities', 'is_system' => true],
            ['code' => '2100', 'name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Money owed to suppliers', 'is_system' => true],
            ['code' => '2200', 'name' => 'VAT Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'VAT collected on sales', 'is_system' => true],
            ['code' => '2300', 'name' => 'Input VAT', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'VAT paid on purchases', 'is_system' => true],
            ['code' => '2400', 'name' => 'NSSF Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'NSSF contributions payable', 'is_system' => true],
            ['code' => '2500', 'name' => 'PAYE Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'PAYE tax payable', 'is_system' => true],
            ['code' => '2600', 'name' => 'Guest Deposits', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Advance deposits from guests', 'is_system' => true],

            // Equity (3xxx)
            ['code' => '3000', 'name' => 'Equity', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Parent account for equity', 'is_system' => true],
            ['code' => '3100', 'name' => 'Owner Capital', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Owner investment', 'is_system' => true],
            ['code' => '3200', 'name' => 'Retained Earnings', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Accumulated profits', 'is_system' => true],

            // Revenue (4xxx)
            ['code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Parent account for all revenue', 'is_system' => true],
            ['code' => '4100', 'name' => 'Room Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Income from room bookings', 'is_system' => true],
            ['code' => '4200', 'name' => 'F&B Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Food and beverage income', 'is_system' => true],
            ['code' => '4300', 'name' => 'Laundry Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Laundry service income', 'is_system' => true],
            ['code' => '4400', 'name' => 'Conference Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Conference and events income', 'is_system' => true],
            ['code' => '4500', 'name' => 'Other Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Miscellaneous income', 'is_system' => true],

            // Expenses (5xxx & 6xxx)
            ['code' => '5000', 'name' => 'Operating Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Parent account for operating expenses', 'is_system' => true],
            ['code' => '5100', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Direct costs of goods sold', 'is_system' => true],
            ['code' => '5200', 'name' => 'Utilities Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Electricity, water, etc.', 'is_system' => true],
            ['code' => '5300', 'name' => 'Maintenance Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Repairs and maintenance', 'is_system' => true],
            ['code' => '5400', 'name' => 'Supplies Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Office and operating supplies', 'is_system' => true],
            ['code' => '5500', 'name' => 'Marketing Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Advertising and promotions', 'is_system' => true],

            ['code' => '6000', 'name' => 'Payroll Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Parent account for payroll', 'is_system' => true],
            ['code' => '6100', 'name' => 'Salary Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Employee salaries', 'is_system' => true],
            ['code' => '6200', 'name' => 'NSSF Employer Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Employer NSSF contribution', 'is_system' => true],
            ['code' => '6300', 'name' => 'Staff Benefits', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Employee benefits and allowances', 'is_system' => true],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                array_merge($account, ['is_active' => true])
            );
        }
    }
}
