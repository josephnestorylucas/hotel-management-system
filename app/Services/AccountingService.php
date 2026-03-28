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
