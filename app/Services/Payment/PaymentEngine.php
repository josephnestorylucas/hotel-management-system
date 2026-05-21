<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProvider;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\BookingCharge;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PaymentEngine — orchestrates payment operations.
 *
 * Uses the PaymentProvider contract to interact with the configured
 * payment provider (AzamPesa). Creates Payment records, calls the provider,
 * and updates statuses accordingly.
 */
class PaymentEngine
{
    protected PaymentProvider $provider;

    public function __construct(?PaymentProvider $provider = null)
    {
        $this->provider = $provider ?? $this->resolveProvider();
    }

    /**
     * Resolve the default payment provider.
     */
    protected function resolveProvider(): PaymentProvider
    {
        $default = config('payment.default', 'azampesa');

        return match ($default) {
            'azampesa' => new AzamPesaProvider(),
            default    => throw new \InvalidArgumentException("Unsupported payment provider: {$default}"),
        };
    }

    /**
     * Get the active provider instance.
     */
    public function getProvider(): PaymentProvider
    {
        return $this->provider;
    }

    /**
     * Get supported payment methods for the active provider.
     */
    public function supportedMethods(): array
    {
        return $this->provider->supportedMethods();
    }

    /**
     * Initiate a payment for a booking (full or specific charge).
     *
     * Creates a Payment record, calls the provider to start the transaction,
     * then updates the Payment with provider details.
     */
    public function pay(
        Booking $booking,
        float $amount,
        string $method,
        array $options = [],
        ?BookingCharge $charge = null,
        ?string $userId = null
    ): Payment {
        $currency = config('payment.currency', 'TZS');
        $idempotencyKey = $options['idempotency_key'] ?? Str::uuid()->toString();

        // Create payment record
        $payment = Payment::create([
            'booking_id'       => $booking->id,
            'charge_type'      => $charge ? $charge->charge_type : 'booking',
            'reference_id'     => $charge?->id,
            'provider_name'    => $this->provider->name(),
            'payment_method'   => $method,
            'amount'           => $amount,
            'currency'         => $currency,
            'status'           => Payment::STATUS_PENDING,
            'created_by'       => $userId,
            'idempotency_key'  => $idempotencyKey,
        ]);

        // Build metadata for the provider
        $metadata = array_merge([
            'booking_id'       => $booking->id,
            'charge_id'        => $charge?->id,
            'payment_id'       => $payment->id,
            'booking_number'   => $booking->booking_number,
            'description'      => $charge
                ? "Payment for {$charge->charge_type_label} — {$booking->booking_number}"
                : "Room payment — {$booking->booking_number}",
            'guest_first_name' => $booking->guest?->first_name ?? explode(' ', $booking->guest_name ?? 'Guest')[0],
            'guest_last_name'  => $booking->guest?->last_name ?? '',
            'guest_email'      => $booking->guest_display_email ?? '',
            'idempotency_key'  => $idempotencyKey,
        ], $options);

        // Call the provider
        $result = $this->provider->initiatePayment($amount, $currency, $method, $metadata);

        // Update payment with provider response
        if ($result['success']) {
            $payment->update([
                'provider_reference' => $result['reference'],
                'payment_url'        => $result['payment_url'] ?? null,
                'payment_qr_code'    => $result['payment_qr_code'] ?? null,
                'payment_token'      => $result['payment_token'] ?? null,
                'metadata'           => $result['raw'] ?? [],
            ]);

            Log::info('Payment initiated', [
                'payment_id' => $payment->id,
                'reference'  => $result['reference'],
                'method'     => $method,
            ]);
        } else {
            $payment->markFailed(['error' => $result['error'] ?? 'Unknown error', 'raw' => $result['raw'] ?? []]);

            Log::error('Payment initiation failed', [
                'payment_id' => $payment->id,
                'error'      => $result['error'] ?? 'Unknown error',
            ]);
        }

        return $payment->fresh();
    }

    /**
     * Verify a payment's current status with the provider.
     */
    public function verify(Payment $payment): Payment
    {
        if (!$payment->provider_reference) {
            return $payment;
        }

        $result = $this->provider->verifyPayment($payment->provider_reference);

        if ($result['success']) {
            $status = $result['status'];

            if ($status === 'completed' && $payment->isPending()) {
                $payment->markSuccessful($result['raw'] ?? []);

                // Mark linked charge as paid
                if ($payment->reference_id) {
                    $payment->bookingCharge?->markAsPaid();
                }

                Log::info('Payment verified as successful', ['payment_id' => $payment->id]);
            } elseif ($status === 'failed' && $payment->isPending()) {
                $payment->markFailed($result['raw'] ?? []);
                Log::info('Payment verified as failed', ['payment_id' => $payment->id]);
            }
        }

        return $payment->fresh();
    }

    /**
     * Refund a payment.
     */
    public function refund(Payment $payment, ?float $amount = null): Payment
    {
        if (!$payment->canBeRefunded()) {
            throw new \RuntimeException('Payment cannot be refunded — status: ' . $payment->status);
        }

        if (!$payment->provider_reference) {
            throw new \RuntimeException('No provider reference to refund.');
        }

        $result = $this->provider->refundPayment($payment->provider_reference, $amount);

        if ($result['success']) {
            $payment->markRefunded([
                'refund_reference' => $result['refund_reference'] ?? null,
                'refund_amount'    => $amount ?? $payment->amount,
                'raw'              => $result['raw'] ?? [],
            ]);

            // If linked to a charge, revert to unpaid
            if ($payment->reference_id) {
                $payment->bookingCharge?->update(['status' => 'unpaid']);
            }

            Log::info('Payment refunded', ['payment_id' => $payment->id]);
        } else {
            Log::error('Refund failed', [
                'payment_id' => $payment->id,
                'error'      => $result['error'] ?? 'Unknown error',
            ]);

            throw new \RuntimeException($result['error'] ?? 'Refund failed');
        }

        return $payment->fresh();
    }

    /**
     * Handle an incoming webhook from the provider.
     *
     * Validates the signature, parses the event, finds the Payment record,
     * and updates its status.
     */
    public function handleWebhook(array $payload, array $headers): ?Payment
    {
        // Validate signature
        if (!$this->provider->validateWebhook($payload, $headers)) {
            Log::warning('Webhook signature validation failed', ['provider' => $this->provider->name()]);
            return null;
        }

        // Parse the webhook
        $parsed = $this->provider->parseWebhook($payload);

        if (empty($parsed['reference'])) {
            Log::warning('Webhook has no reference', ['event' => $parsed['event'] ?? 'unknown']);
            return null;
        }

        // Find the payment by provider reference
        $payment = Payment::where('provider_reference', $parsed['reference'])
            ->where('provider_name', $this->provider->name())
            ->first();

        if (!$payment) {
            // Try to find by metadata payment_id
            $metaPaymentId = $parsed['metadata']['payment_id'] ?? null;
            if ($metaPaymentId) {
                $payment = Payment::find($metaPaymentId);
            }
        }

        if (!$payment) {
            Log::warning('No payment found for webhook', [
                'reference' => $parsed['reference'],
                'provider'  => $this->provider->name(),
            ]);
            return null;
        }

        // Only update if payment is still pending
        if (!$payment->isPending()) {
            Log::info('Webhook received for non-pending payment', [
                'payment_id' => $payment->id,
                'status'     => $payment->status,
            ]);
            return $payment;
        }

        // Update status based on webhook event
        if ($parsed['status'] === 'successful') {
            $payment->markSuccessful(['webhook' => $parsed['raw']]);

            // Mark linked charge as paid
            if ($payment->reference_id) {
                $payment->bookingCharge?->markAsPaid();
            }

            Log::info('Payment marked successful via webhook', ['payment_id' => $payment->id]);
        } elseif ($parsed['status'] === 'failed') {
            $payment->markFailed(['webhook' => $parsed['raw']]);
            Log::info('Payment marked failed via webhook', ['payment_id' => $payment->id]);
        }

        return $payment->fresh();
    }

    /**
     * Trigger a mobile money push notification.
     * Not supported by AzamPesa — returns error.
     */
    public function triggerPush(Payment $payment, ?string $phone = null): array
    {
        return ['success' => false, 'error' => 'Push notifications are not supported by AzamPesa'];
    }
}
