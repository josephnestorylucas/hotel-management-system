<?php

namespace App\Contracts;

/**
 * ReceiptPrintable — contract for models that can generate receipts.
 * 
 * Any model that finalizes a payment should implement this interface
 * to provide standardized receipt data.
 */
interface ReceiptPrintable
{
    /**
     * Get the receipt data in a standardized format.
     * 
     * @return array{
     *     receipt_no: string,
     *     issued_at: \Carbon\Carbon,
     *     module: string,
     *     customer_name: string|null,
     *     customer_phone: string|null,
     *     items: array,
     *     subtotal: float,
     *     discount: float,
     *     tax: float,
     *     total: float,
     *     amount_paid: float,
     *     balance: float,
     *     payment_method: string|null,
     *     payment_status: string,
     *     transaction_reference: string|null,
     *     cashier: string|null,
     *     notes: string|null,
     * }
     */
    public function toReceiptData(): array;

    /**
     * Get the module name for this receipt.
     */
    public function getReceiptModule(): string;

    /**
     * Check if this record has been paid.
     */
    public function isPaid(): bool;
}
