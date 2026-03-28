<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ASSETS (1xxx)
            ['code' => '1100', 'name' => 'Cash on Hand', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1200', 'name' => 'Bank Account — Main', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1210', 'name' => 'Bank Account — Secondary', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1300', 'name' => 'Accounts Receivable (Guests)', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1400', 'name' => 'Inventory / Stock', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1500', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => false],
            
            // LIABILITIES (2xxx)
            ['code' => '2100', 'name' => 'Accounts Payable (Suppliers)', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2200', 'name' => 'VAT Payable (Output VAT)', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2300', 'name' => 'VAT Receivable (Input VAT)', 'type' => 'liability', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '2400', 'name' => 'NSSF Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2500', 'name' => 'PAYE Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2600', 'name' => 'Deposits — Guest Advance Payments', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true],
            
            // EQUITY (3xxx)
            ['code' => '3100', 'name' => "Owner's Capital", 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '3200', 'name' => 'Retained Earnings', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true],
            
            // REVENUE (4xxx)
            ['code' => '4100', 'name' => 'Room Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '4200', 'name' => 'Food & Beverage Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '4300', 'name' => 'Laundry Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '4400', 'name' => 'Store / Miscellaneous Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true],
            
            // COST OF GOODS SOLD (5xxx)
            ['code' => '5100', 'name' => 'Cost of Food & Beverages', 'type' => 'cogs', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5200', 'name' => 'Cost of Laundry Supplies', 'type' => 'cogs', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5300', 'name' => 'Cost of Store Items', 'type' => 'cogs', 'normal_balance' => 'debit', 'is_system' => false],
            
            // EXPENSES (6xxx)
            ['code' => '6100', 'name' => 'Salaries & Wages', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '6200', 'name' => 'NSSF — Employer Contribution', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '6300', 'name' => 'Utilities (Electricity, Water)', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6400', 'name' => 'Repairs & Maintenance', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6500', 'name' => 'Office Supplies', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6600', 'name' => 'Transport & Fuel', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6700', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6800', 'name' => 'Petty Cash Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '6900', 'name' => 'Depreciation', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => false],
        ];

        foreach ($accounts as $data) {
            Account::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
