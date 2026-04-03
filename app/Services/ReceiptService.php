<?php

namespace App\Services;

use App\Contracts\ReceiptPrintable;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * ReceiptService — handles receipt generation, retrieval, and printing.
 * 
 * Ensures idempotent receipt generation (one receipt per source record).
 */
class ReceiptService
{
    /**
     * Get or create a receipt for a given receiptable model.
     * 
     * This ensures idempotent receipt number generation - calling this
     * method multiple times for the same source record returns the same receipt.
     */
    public function getOrCreateReceipt(ReceiptPrintable&Model $model): Receipt
    {
        // Check if receipt already exists for this model
        $existing = Receipt::where('receiptable_type', get_class($model))
            ->where('receiptable_id', $model->getKey())
            ->first();

        if ($existing) {
            return $existing;
        }

        // Generate new receipt from the model's data
        return $this->createReceipt($model);
    }

    /**
     * Create a new receipt from a receiptable model.
     */
    public function createReceipt(ReceiptPrintable&Model $model): Receipt
    {
        $data = $model->toReceiptData();
        $user = Auth::user();

        return Receipt::create([
            'module'                => $model->getReceiptModule(),
            'receiptable_type'      => get_class($model),
            'receiptable_id'        => $model->getKey(),
            'customer_name'         => $data['customer_name'] ?? null,
            'customer_phone'        => $data['customer_phone'] ?? null,
            'items_snapshot'        => $data['items'] ?? [],
            'subtotal'              => $data['subtotal'] ?? 0,
            'discount'              => $data['discount'] ?? 0,
            'tax'                   => $data['tax'] ?? 0,
            'total'                 => $data['total'] ?? 0,
            'amount_paid'           => $data['amount_paid'] ?? 0,
            'balance'               => $data['balance'] ?? 0,
            'currency'              => $data['currency'] ?? 'TZS',
            'payment_method'        => $data['payment_method'] ?? null,
            'payment_status'        => $data['payment_status'] ?? 'unpaid',
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'cashier_id'            => $user?->id,
            'cashier_name'          => $data['cashier'] ?? $user?->name,
            'notes'                 => $data['notes'] ?? null,
            'issued_at'             => $data['issued_at'] ?? now(),
        ]);
    }

    /**
     * Create a refund receipt linked to an original receipt.
     */
    public function createRefundReceipt(Receipt $originalReceipt, float $refundAmount, ?string $notes = null): Receipt
    {
        $user = Auth::user();

        return Receipt::create([
            'module'                => $originalReceipt->module,
            'receiptable_type'      => $originalReceipt->receiptable_type,
            'receiptable_id'        => $originalReceipt->receiptable_id,
            'customer_name'         => $originalReceipt->customer_name,
            'customer_phone'        => $originalReceipt->customer_phone,
            'items_snapshot'        => $originalReceipt->items_snapshot,
            'subtotal'              => -$refundAmount,
            'discount'              => 0,
            'tax'                   => 0,
            'total'                 => -$refundAmount,
            'amount_paid'           => -$refundAmount,
            'balance'               => 0,
            'currency'              => $originalReceipt->currency,
            'payment_method'        => $originalReceipt->payment_method,
            'payment_status'        => 'refunded',
            'transaction_reference' => null,
            'cashier_id'            => $user?->id,
            'cashier_name'          => $user?->name,
            'notes'                 => $notes ?? "Refund for receipt #{$originalReceipt->receipt_number}",
            'is_refund'             => true,
            'refund_receipt_id'     => $originalReceipt->id,
            'issued_at'             => now(),
        ]);
    }

    /**
     * Find receipt by receipt number.
     */
    public function findByNumber(string $receiptNumber): ?Receipt
    {
        return Receipt::where('receipt_number', $receiptNumber)->first();
    }

    /**
     * Find receipt by UUID.
     */
    public function findByUuid(string $uuid): ?Receipt
    {
        return Receipt::where('uuid', $uuid)->first();
    }

    /**
     * Get receipt for a specific model if it exists.
     */
    public function getReceiptFor(Model $model): ?Receipt
    {
        return Receipt::where('receiptable_type', get_class($model))
            ->where('receiptable_id', $model->getKey())
            ->first();
    }

    /**
     * Mark receipt as printed and return updated receipt.
     */
    public function markPrinted(Receipt $receipt): Receipt
    {
        $receipt->markPrinted();
        return $receipt->fresh();
    }

    /**
     * Update receipt data from its source model.
     * 
     * Useful when the source model has been updated (e.g., payment received).
     */
    public function refreshReceipt(Receipt $receipt): Receipt
    {
        $model = $receipt->receiptable;

        if (!$model || !($model instanceof ReceiptPrintable)) {
            return $receipt;
        }

        $data = $model->toReceiptData();

        $receipt->update([
            'customer_name'         => $data['customer_name'] ?? $receipt->customer_name,
            'customer_phone'        => $data['customer_phone'] ?? $receipt->customer_phone,
            'items_snapshot'        => $data['items'] ?? $receipt->items_snapshot,
            'subtotal'              => $data['subtotal'] ?? $receipt->subtotal,
            'discount'              => $data['discount'] ?? $receipt->discount,
            'tax'                   => $data['tax'] ?? $receipt->tax,
            'total'                 => $data['total'] ?? $receipt->total,
            'amount_paid'           => $data['amount_paid'] ?? $receipt->amount_paid,
            'balance'               => $data['balance'] ?? $receipt->balance,
            'payment_method'        => $data['payment_method'] ?? $receipt->payment_method,
            'payment_status'        => $data['payment_status'] ?? $receipt->payment_status,
            'transaction_reference' => $data['transaction_reference'] ?? $receipt->transaction_reference,
        ]);

        return $receipt->fresh();
    }

    /**
     * Get receipts for a module.
     */
    public function getReceiptsByModule(string $module, int $limit = 50)
    {
        return Receipt::where('module', $module)
            ->orderByDesc('issued_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search receipts by customer name or receipt number.
     */
    public function search(string $query, ?string $module = null, int $limit = 50)
    {
        $builder = Receipt::where(function ($q) use ($query) {
            $q->where('receipt_number', 'like', "%{$query}%")
              ->orWhere('customer_name', 'like', "%{$query}%")
              ->orWhere('customer_phone', 'like', "%{$query}%");
        });

        if ($module) {
            $builder->where('module', $module);
        }

        return $builder->orderByDesc('issued_at')->limit($limit)->get();
    }
}
