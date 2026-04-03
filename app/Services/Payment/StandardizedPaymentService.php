<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Models\SystemSetting;
use App\Models\WalkinTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * StandardizedPaymentService — Centralizes all payment operations.
 *
 * Ensures correct identity is used for payments:
 * - Customer payments → use guest/customer data (name, phone)
 * - Organization/system payments → use business/org data from settings
 *
 * Supports:
 * - USSD payments (phone-based, requires customer phone)
 * - Card payments (redirect flow)
 * - Full and partial refunds with proper tracking
 */
class StandardizedPaymentService
{
    protected SnippeProvider $provider;

    public function __construct(?SnippeProvider $provider = null)
    {
        $this->provider = $provider ?? new SnippeProvider();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // IDENTITY RESOLUTION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Get customer identity from Guest model or booking relation.
     *
     * @param Guest|null $guest The guest model
     * @param array $fallback Fallback data if guest is null
     * @return array{name: string, first_name: string, last_name: string, phone: string|null, email: string|null}
     */
    public function getCustomerIdentity(?Guest $guest, array $fallback = []): array
    {
        if ($guest) {
            return [
                'name'       => $guest->full_name,
                'first_name' => $guest->first_name,
                'last_name'  => $guest->last_name,
                'phone'      => $guest->phone_number,
                'email'      => $guest->email,
            ];
        }

        // Fallback to provided data (e.g., walk-in customer)
        $name = $fallback['customer_name'] ?? $fallback['name'] ?? 'Guest Customer';
        $nameParts = explode(' ', $name, 2);

        return [
            'name'       => $name,
            'first_name' => $nameParts[0] ?? 'Guest',
            'last_name'  => $nameParts[1] ?? '',
            'phone'      => $fallback['customer_phone'] ?? $fallback['phone'] ?? null,
            'email'      => $fallback['customer_email'] ?? $fallback['email'] ?? null,
        ];
    }

    /**
     * Get customer identity from a booking.
     *
     * @param Booking $booking
     * @return array{name: string, first_name: string, last_name: string, phone: string|null, email: string|null}
     */
    public function getCustomerIdentityFromBooking(Booking $booking): array
    {
        // Try to get guest from booking relation
        if ($booking->guest) {
            return $this->getCustomerIdentity($booking->guest);
        }

        // Fallback to booking fields
        return $this->getCustomerIdentity(null, [
            'customer_name'  => $booking->guest_name ?? 'Guest',
            'customer_phone' => $booking->guest_phone ?? null,
            'customer_email' => $booking->guest_display_email ?? null,
        ]);
    }

    /**
     * Get organization identity for system-level payments.
     * Used when no customer is attached (internal/bulk payments).
     *
     * @return array{name: string, first_name: string, last_name: string, phone: string|null, email: string|null}
     */
    public function getOrganizationIdentity(): array
    {
        $businessName = SystemSetting::getValue('business_name', 'Grand Hotel');
        $businessPhone = SystemSetting::getValue('business_phone', null);
        $businessEmail = SystemSetting::getValue('business_email', 'info@grandhotel.com');

        return [
            'name'       => $businessName,
            'first_name' => $businessName,
            'last_name'  => 'Organization',
            'phone'      => $businessPhone,
            'email'      => $businessEmail,
        ];
    }

    /**
     * Determine which identity to use based on context.
     *
     * @param Guest|null $guest
     * @param array $customerData Walk-in customer data
     * @param bool $useOrgFallback If true, use org data when no customer
     * @return array
     */
    public function resolveIdentity(?Guest $guest, array $customerData = [], bool $useOrgFallback = false): array
    {
        // Priority: Guest model > Walk-in customer data > Organization (if allowed)
        if ($guest) {
            return $this->getCustomerIdentity($guest);
        }

        if (!empty($customerData['customer_name']) || !empty($customerData['name'])) {
            return $this->getCustomerIdentity(null, $customerData);
        }

        if ($useOrgFallback) {
            return $this->getOrganizationIdentity();
        }

        // Default minimal identity
        return $this->getCustomerIdentity(null, ['customer_name' => 'Customer']);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PHONE NUMBER VALIDATION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Validate and format phone number for Snippe.
     *
     * @param string|null $phone
     * @return array{valid: bool, phone: string|null, error: string|null}
     */
    public function validatePhone(?string $phone): array
    {
        if (empty($phone)) {
            return [
                'valid' => false,
                'phone' => null,
                'error' => 'Phone number is required for USSD payments',
            ];
        }

        // Remove all non-digits
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Check minimum length (Tanzania numbers are 12 digits with country code)
        if (strlen($cleaned) < 9) {
            return [
                'valid' => false,
                'phone' => null,
                'error' => 'Phone number is too short',
            ];
        }

        // Normalize to Tanzania format (255XXXXXXXXX)
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '255' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '255')) {
            $cleaned = '255' . $cleaned;
        }

        // Validate final length (should be 12 digits for Tanzania)
        if (strlen($cleaned) !== 12) {
            return [
                'valid' => false,
                'phone' => null,
                'error' => 'Invalid phone number format. Expected 12 digits (255XXXXXXXXX)',
            ];
        }

        return [
            'valid' => true,
            'phone' => $cleaned,
            'error' => null,
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PAYMENT INITIATION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Initiate a USSD (mobile money) payment.
     *
     * @param float $amount
     * @param string $currency
     * @param array $identity Customer identity from resolveIdentity()
     * @param array $metadata Additional metadata (booking_id, order_id, etc.)
     * @return array
     */
    public function initiateUssdPayment(float $amount, string $currency, array $identity, array $metadata = []): array
    {
        // Validate phone number
        $phoneValidation = $this->validatePhone($identity['phone'] ?? null);
        if (!$phoneValidation['valid']) {
            Log::warning('USSD payment failed: invalid phone', [
                'phone' => $identity['phone'] ?? null,
                'error' => $phoneValidation['error'],
            ]);
            return [
                'success' => false,
                'error'   => $phoneValidation['error'],
                'status'  => 'failed',
            ];
        }

        $payload = array_merge([
            'guest_first_name' => $identity['first_name'],
            'guest_last_name'  => $identity['last_name'],
            'guest_email'      => $identity['email'] ?? 'customer@example.com',
            'phone_number'     => $phoneValidation['phone'],
            'idempotency_key'  => $metadata['idempotency_key'] ?? Str::uuid()->toString(),
        ], $metadata);

        Log::info('Initiating USSD payment', [
            'amount'   => $amount,
            'currency' => $currency,
            'customer' => $identity['name'],
            'phone'    => $phoneValidation['phone'],
        ]);

        return $this->provider->initiatePayment($amount, $currency, 'mobile', $payload);
    }

    /**
     * Initiate a card payment (redirect flow).
     *
     * @param float $amount
     * @param string $currency
     * @param array $identity Customer identity from resolveIdentity()
     * @param array $metadata Additional metadata including redirect URLs
     * @return array
     */
    public function initiateCardPayment(float $amount, string $currency, array $identity, array $metadata = []): array
    {
        $payload = array_merge([
            'guest_first_name' => $identity['first_name'],
            'guest_last_name'  => $identity['last_name'],
            'guest_email'      => $identity['email'] ?? 'customer@example.com',
            'phone_number'     => $identity['phone'], // Optional for card
            'idempotency_key'  => $metadata['idempotency_key'] ?? Str::uuid()->toString(),
        ], $metadata);

        Log::info('Initiating card payment', [
            'amount'   => $amount,
            'currency' => $currency,
            'customer' => $identity['name'],
        ]);

        return $this->provider->initiatePayment($amount, $currency, 'card', $payload);
    }

    /**
     * Unified payment initiation - routes to correct method based on payment method.
     *
     * @param float $amount
     * @param string $currency
     * @param string $method 'mobile', 'card', or 'dynamic-qr'
     * @param array $identity Customer identity
     * @param array $metadata
     * @return array
     */
    public function initiatePayment(
        float $amount,
        string $currency,
        string $method,
        array $identity,
        array $metadata = []
    ): array {
        return match ($method) {
            'mobile' => $this->initiateUssdPayment($amount, $currency, $identity, $metadata),
            'card', 'dynamic-qr' => $this->initiateCardPayment($amount, $currency, $identity, $metadata),
            default => [
                'success' => false,
                'error'   => "Unsupported payment method: {$method}",
                'status'  => 'failed',
            ],
        };
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PAYMENT VERIFICATION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Verify payment status with provider.
     *
     * @param string $reference Provider reference
     * @return array
     */
    public function verifyPayment(string $reference): array
    {
        return $this->provider->verifyPayment($reference);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // REFUND OPERATIONS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Process a full refund for a payment.
     *
     * @param Payment $payment The payment to refund
     * @param string|null $reason Reason for refund
     * @param string|null $actorId User who initiated refund
     * @return array{success: bool, message: string, refund_reference?: string}
     */
    public function processFullRefund(Payment $payment, ?string $reason = null, ?string $actorId = null): array
    {
        return $this->processRefund($payment, null, $reason, $actorId);
    }

    /**
     * Process a partial refund for a payment.
     *
     * @param Payment $payment The payment to refund
     * @param float $amount Amount to refund
     * @param string|null $reason Reason for refund
     * @param string|null $actorId User who initiated refund
     * @return array{success: bool, message: string, refund_reference?: string}
     */
    public function processPartialRefund(Payment $payment, float $amount, ?string $reason = null, ?string $actorId = null): array
    {
        return $this->processRefund($payment, $amount, $reason, $actorId);
    }

    /**
     * Core refund processing logic with validation.
     *
     * @param Payment $payment
     * @param float|null $amount Null for full refund
     * @param string|null $reason
     * @param string|null $actorId
     * @return array
     */
    public function processRefund(Payment $payment, ?float $amount = null, ?string $reason = null, ?string $actorId = null): array
    {
        // Validation: Check if payment can be refunded
        $validation = $this->validateRefund($payment, $amount);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['error'],
            ];
        }

        $refundAmount = $amount ?? (float) $payment->amount;
        $isFullRefund = $amount === null || $amount >= (float) $payment->amount;

        try {
            // Call Snippe refund API
            $result = $this->provider->refundPayment($payment->provider_reference, $isFullRefund ? null : $refundAmount);

            if (!$result['success']) {
                Log::error('Refund API failed', [
                    'payment_id' => $payment->id,
                    'error'      => $result['error'] ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Refund failed at payment provider',
                ];
            }

            // Update payment record
            $existingRefunds = (float) ($payment->refund_metadata['total_refunded'] ?? 0);
            $newTotalRefunded = $existingRefunds + $refundAmount;
            $isFullyRefunded = $newTotalRefunded >= (float) $payment->amount;

            $payment->update([
                'status'          => $isFullyRefunded ? Payment::STATUS_REFUNDED : Payment::STATUS_PARTIALLY_REFUNDED,
                'refunded_at'     => $isFullyRefunded ? now() : $payment->refunded_at,
                'refund_metadata' => [
                    'total_refunded'     => $newTotalRefunded,
                    'last_refund_amount' => $refundAmount,
                    'last_refund_ref'    => $result['refund_reference'] ?? null,
                    'last_refund_at'     => now()->toISOString(),
                    'refund_reason'      => $reason,
                    'refunded_by'        => $actorId,
                    'is_partial'         => !$isFullyRefunded,
                    'raw'                => $result['raw'] ?? [],
                ],
            ]);

            // If fully refunded, revert linked booking charge
            if ($isFullyRefunded && $payment->reference_id && $payment->bookingCharge) {
                $payment->bookingCharge->update(['status' => 'unpaid']);
            }

            // Record financial transaction
            $this->recordRefundTransaction($payment, $refundAmount, $reason, $actorId);

            Log::info('Refund processed successfully', [
                'payment_id'      => $payment->id,
                'refund_amount'   => $refundAmount,
                'total_refunded'  => $newTotalRefunded,
                'status'          => $isFullyRefunded ? 'refunded' : 'partially_refunded',
            ]);

            return [
                'success'          => true,
                'message'          => $isFullyRefunded ? 'Full refund processed successfully' : 'Partial refund processed successfully',
                'refund_reference' => $result['refund_reference'] ?? null,
                'refund_amount'    => $refundAmount,
                'total_refunded'   => $newTotalRefunded,
                'status'           => $isFullyRefunded ? 'refunded' : 'partially_refunded',
            ];

        } catch (\Exception $e) {
            Log::error('Refund exception', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while processing the refund: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate if a refund can be processed.
     *
     * @param Payment $payment
     * @param float|null $amount
     * @return array{valid: bool, error?: string}
     */
    public function validateRefund(Payment $payment, ?float $amount = null): array
    {
        // Must have provider reference
        if (empty($payment->provider_reference)) {
            return [
                'valid' => false,
                'error' => 'Payment has no provider reference - cannot process refund',
            ];
        }

        // Must be successful or partially refunded to allow further refund
        if (!$payment->isSuccessful() && !$payment->isPartiallyRefunded()) {
            return [
                'valid' => false,
                'error' => 'Only successful or partially refunded payments can be refunded. Current status: ' . $payment->status,
            ];
        }

        // Calculate remaining refundable amount
        $totalRefunded = (float) ($payment->refund_metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $payment->amount - $totalRefunded;

        if ($maxRefundable <= 0) {
            return [
                'valid' => false,
                'error' => 'This payment has already been fully refunded',
            ];
        }

        // Validate partial refund amount
        if ($amount !== null) {
            if ($amount <= 0) {
                return [
                    'valid' => false,
                    'error' => 'Refund amount must be greater than 0',
                ];
            }

            if ($amount > $maxRefundable) {
                return [
                    'valid' => false,
                    'error' => "Cannot refund more than the remaining amount. Max refundable: {$maxRefundable}",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Record a refund in the financial transaction ledger.
     *
     * @param Payment $payment
     * @param float $amount
     * @param string|null $reason
     * @param string|null $actorId
     */
    protected function recordRefundTransaction(Payment $payment, float $amount, ?string $reason = null, ?string $actorId = null): void
    {
        $exchangeRate = (float) SystemSetting::getValue('tzs_exchange_rate', 2500);

        // Determine currency - refund in same currency as original payment
        $currency = $payment->currency ?? 'TZS';
        $amountUsd = $currency === 'USD' ? $amount : round($amount / $exchangeRate, 2);

        FinancialTransaction::record([
            'type'           => 'refund',
            'source_module'  => 'payment',
            'payment_id'     => null, // No FinancePayment for refunds
            'booking_id'     => $payment->booking_id,
            'currency'       => $currency,
            'amount'         => -$amount, // Negative for refund
            'amount_usd'     => -$amountUsd,
            'exchange_rate'  => $exchangeRate,
            'payment_method' => $payment->payment_method ?? 'unknown',
            'description'    => "Refund for {$payment->payment_number}" . ($reason ? " — {$reason}" : ''),
        ], $actorId ?? auth()->id() ?? 'system');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // WALKIN REFUND SUPPORT
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Process refund for a walk-in transaction.
     *
     * @param WalkinTransaction $transaction
     * @param float|null $amount Null for full refund
     * @param string|null $reason
     * @param string|null $actorId
     * @return array
     */
    public function processWalkinRefund(WalkinTransaction $transaction, ?float $amount = null, ?string $reason = null, ?string $actorId = null): array
    {
        // Validate
        if (!$transaction->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Only completed transactions can be refunded',
            ];
        }

        if (empty($transaction->provider_reference)) {
            return [
                'success' => false,
                'message' => 'Transaction has no provider reference - was likely paid in cash',
            ];
        }

        $existingRefund = (float) ($transaction->metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $transaction->amount - $existingRefund;

        if ($maxRefundable <= 0) {
            return [
                'success' => false,
                'message' => 'This transaction has already been fully refunded',
            ];
        }

        $refundAmount = $amount ?? $maxRefundable;
        if ($refundAmount > $maxRefundable) {
            return [
                'success' => false,
                'message' => "Cannot refund more than {$maxRefundable}",
            ];
        }

        $isFullRefund = $refundAmount >= $maxRefundable;

        try {
            // Call Snippe
            $result = $this->provider->refundPayment($transaction->provider_reference, $isFullRefund ? null : $refundAmount);

            if (!$result['success']) {
                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Refund failed at provider',
                ];
            }

            // Update transaction
            $newTotalRefunded = $existingRefund + $refundAmount;
            $transaction->update([
                'status'   => $isFullRefund ? 'refunded' : $transaction->status,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'total_refunded'     => $newTotalRefunded,
                    'last_refund_amount' => $refundAmount,
                    'last_refund_ref'    => $result['refund_reference'] ?? null,
                    'last_refund_at'     => now()->toISOString(),
                    'refund_reason'      => $reason,
                    'refunded_by'        => $actorId,
                ]),
            ]);

            // Record financial transaction
            $this->recordWalkinRefundTransaction($transaction, $refundAmount, $reason, $actorId);

            return [
                'success'        => true,
                'message'        => $isFullRefund ? 'Full refund processed' : 'Partial refund processed',
                'refund_amount'  => $refundAmount,
                'total_refunded' => $newTotalRefunded,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Refund error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Record walk-in refund in financial transaction ledger.
     */
    protected function recordWalkinRefundTransaction(WalkinTransaction $transaction, float $amount, ?string $reason, ?string $actorId): void
    {
        $exchangeRate = (float) SystemSetting::getValue('tzs_exchange_rate', 2500);
        $currency = $transaction->currency ?? 'TZS';
        $amountUsd = $currency === 'USD' ? $amount : round($amount / $exchangeRate, 2);

        FinancialTransaction::record([
            'type'           => 'refund',
            'source_module'  => $transaction->module ?? 'walkin',
            'payment_id'     => null,
            'booking_id'     => null,
            'order_id'       => $transaction->order_id,
            'currency'       => $currency,
            'amount'         => -$amount,
            'amount_usd'     => -$amountUsd,
            'exchange_rate'  => $exchangeRate,
            'payment_method' => $transaction->payment_method ?? 'unknown',
            'description'    => "Refund for {$transaction->transaction_number}" . ($reason ? " — {$reason}" : ''),
        ], $actorId ?? auth()->id() ?? 'system');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // UTILITY METHODS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Get the underlying Snippe provider instance.
     *
     * @return SnippeProvider
     */
    public function getProvider(): SnippeProvider
    {
        return $this->provider;
    }
}
