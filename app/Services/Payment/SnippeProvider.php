<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Snippe Payment Provider Adapter.
 *
 * Supports: Mobile Money (USSD push), Card (redirect), Dynamic QR.
 * Base URL: https://api.snippe.sh
 * Docs: https://docs.snippe.sh
 */
class SnippeProvider implements PaymentProvider
{
    protected string $baseUrl;
    protected string $apiKey;
    protected ?string $webhookSecret;
    protected int $timeout;
    protected bool $useWebhooks;

    public function __construct()
    {
        $this->baseUrl       = config('payment.providers.snippe.base_url', 'https://api.snippe.sh');
        $this->apiKey        = config('payment.providers.snippe.api_key', '');
        $this->webhookSecret = config('payment.providers.snippe.webhook_secret');
        $this->timeout       = config('payment.providers.snippe.timeout', 30);
        $this->useWebhooks   = config('payment.providers.snippe.use_webhooks', false);
    }

    public function name(): string
    {
        return 'snippe';
    }

    public function supportedMethods(): array
    {
        return ['mobile', 'card', 'dynamic-qr'];
    }

    /**
     * Initiate a payment via Snippe API.
     *
     * POST /v1/payments
     */
    public function initiatePayment(float $amount, string $currency, string $method, array $metadata = []): array
    {
        $idempotencyKey = $metadata['idempotency_key'] ?? Str::uuid()->toString();

        // Build customer details - all fields required by Snippe API
        // Use empty() check instead of ?? because empty string '' is not null
        $firstName = !empty($metadata['guest_first_name']) 
            ? $metadata['guest_first_name'] 
            : (!empty($metadata['customer']['firstname']) ? $metadata['customer']['firstname'] : 'Guest');
        
        $lastName = !empty($metadata['guest_last_name']) 
            ? $metadata['guest_last_name'] 
            : (!empty($metadata['customer']['lastname']) ? $metadata['customer']['lastname'] : 'Customer');
        
        $email = !empty($metadata['guest_email']) 
            ? $metadata['guest_email'] 
            : (!empty($metadata['customer']['email']) ? $metadata['customer']['email'] : 'guest@example.com');

        $customer = [
            'firstname' => $firstName,
            'lastname'  => $lastName,
            'email'     => $email,
        ];

        // Build request payload
        $payload = [
            'payment_type' => $method, // mobile, card, dynamic-qr
            'details' => [
                'amount'   => (int) $amount, // Snippe expects integer (smallest unit)
                'currency' => $currency,
            ],
            'customer' => $customer,
            'metadata' => [
                'booking_id' => $metadata['booking_id'] ?? null,
                'charge_id'  => $metadata['charge_id'] ?? null,
                'payment_id' => $metadata['payment_id'] ?? null,
            ],
        ];

        // Only include webhook_url if webhooks are enabled (requires HTTPS in production)
        // When disabled, use polling via GET /v1/payments/{reference} to check status
        if ($this->useWebhooks) {
            $payload['webhook_url'] = route('payments.webhook.snippe');
        }

        // Phone number is REQUIRED for mobile payments - must be at root level
        // Format: 255XXXXXXXXX (no + prefix, 12 digits for Tanzania)
        if ($method === 'mobile') {
            $phone = $metadata['phone_number'] ?? $metadata['mobile_phone'] ?? null;
            if (empty($phone)) {
                Log::error('Snippe mobile payment requires phone_number', ['metadata' => $metadata]);
                return [
                    'success'   => false,
                    'reference' => null,
                    'status'    => 'failed',
                    'error'     => 'Phone number is required for mobile payments',
                    'raw'       => [],
                ];
            }
            // Normalize phone number: remove +, spaces, dashes
            $phone = preg_replace('/[^0-9]/', '', $phone);
            // Ensure it starts with 255 for Tanzania
            if (str_starts_with($phone, '0')) {
                $phone = '255' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '255')) {
                $phone = '255' . $phone;
            }
            $payload['phone_number'] = $phone;
        }

        // Card payments require redirect URLs
        if (in_array($method, ['card', 'dynamic-qr'])) {
            $payload['details']['redirect_url'] = $metadata['redirect_url']
                ?? route('payments.callback', ['provider' => 'snippe', 'status' => 'success']);
            $payload['details']['cancel_url'] = $metadata['cancel_url']
                ?? route('payments.callback', ['provider' => 'snippe', 'status' => 'cancel']);
            
            // Phone number is optional for card/QR but useful if available
            $phone = $metadata['phone_number'] ?? $metadata['mobile_phone'] ?? null;
            if (!empty($phone)) {
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (str_starts_with($phone, '0')) {
                    $phone = '255' . substr($phone, 1);
                } elseif (!str_starts_with($phone, '255')) {
                    $phone = '255' . $phone;
                }
                $payload['phone_number'] = $phone;
            }
        }

        // Card payments require billing details
        if ($method === 'card') {
            $payload['customer'] = array_merge($payload['customer'], [
                'address'  => $metadata['billing_address'] ?? '',
                'city'     => $metadata['billing_city'] ?? '',
                'state'    => $metadata['billing_state'] ?? '',
                'postcode' => $metadata['billing_postcode'] ?? '',
                'country'  => $metadata['billing_country'] ?? 'TZ',
            ]);
        }

        // Log the payload for debugging (remove sensitive data in production)
        Log::debug('Snippe payment request payload', [
            'method'  => $method,
            'amount'  => $amount,
            'payload' => array_merge($payload, ['customer' => '[REDACTED]']),
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization'  => 'Bearer ' . $this->apiKey,
                'Content-Type'   => 'application/json',
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->timeout($this->timeout)
            ->retry(3, 1000, function ($exception) {
                // Retry on 429 (rate limit) and 5xx server errors
                return $exception instanceof \Illuminate\Http\Client\RequestException
                    && in_array($exception->response?->status(), [429, 500, 502, 503]);
            })
            ->post("{$this->baseUrl}/v1/payments", $payload);

            if ($response->successful()) {
                $data = $response->json('data', []);

                Log::info('Snippe payment initiated', [
                    'reference' => $data['reference'] ?? null,
                    'method'    => $method,
                    'amount'    => $amount,
                ]);

                return [
                    'success'          => true,
                    'reference'        => $data['reference'] ?? null,
                    'status'           => $data['status'] ?? 'pending',
                    'payment_url'      => $data['payment_url'] ?? null,
                    'payment_qr_code'  => $data['payment_qr_code'] ?? null,
                    'payment_token'    => $data['payment_token'] ?? null,
                    'expires_at'       => $data['expires_at'] ?? null,
                    'idempotency_key'  => $idempotencyKey,
                    'raw'              => $data,
                ];
            }

            $error = $response->json();
            Log::error('Snippe payment initiation failed', [
                'status' => $response->status(),
                'error'  => $error,
            ]);

            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => $error['message'] ?? 'Payment initiation failed',
                'raw'       => $error,
            ];
        } catch (\Exception $e) {
            Log::error('Snippe payment exception', [
                'message' => $e->getMessage(),
                'method'  => $method,
            ]);

            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => $e->getMessage(),
                'raw'       => [],
            ];
        }
    }

    /**
     * Verify a payment status.
     *
     * GET /v1/payments/{reference}
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/v1/payments/{$reference}");

            if ($response->successful()) {
                $data = $response->json('data', []);
                return [
                    'success' => true,
                    'status'  => $data['status'] ?? 'unknown',
                    'raw'     => $data,
                ];
            }

            return [
                'success' => false,
                'status'  => 'unknown',
                'raw'     => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Snippe verify payment exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'status'  => 'unknown',
                'error'   => $e->getMessage(),
                'raw'     => [],
            ];
        }
    }

    /**
     * Refund a payment.
     *
     * POST /v1/payments/{reference}/refund
     */
    public function refundPayment(string $reference, ?float $amount = null): array
    {
        try {
            $payload = [];
            if ($amount !== null) {
                $payload['amount'] = (int) $amount;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/v1/payments/{$reference}/refund", $payload);

            if ($response->successful()) {
                $data = $response->json('data', []);
                return [
                    'success'          => true,
                    'refund_reference' => $data['reference'] ?? $reference,
                    'raw'              => $data,
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json('message', 'Refund failed'),
                'raw'     => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Snippe refund exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'raw'     => [],
            ];
        }
    }

    /**
     * Validate webhook by verifying X-Webhook-Signature HMAC-SHA256.
     */
    public function validateWebhook(array $payload, array $headers): bool
    {
        if (empty($this->webhookSecret)) {
            // SECURITY: Reject all webhooks when secret is not configured
            Log::critical('Snippe webhook secret not configured — rejecting webhook for security');
            return false;
        }

        $signature = $headers['x-webhook-signature'] ?? $headers['X-Webhook-Signature'] ?? null;
        if (!$signature) {
            return false;
        }

        ksort($payload);
        $computed = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES), $this->webhookSecret);
        return hash_equals($computed, $signature);
    }

    /**
     * Parse incoming Snippe webhook into normalized structure.
     */
    public function parseWebhook(array $payload): array
    {
        $data = $payload['data'] ?? [];
        $type = $payload['type'] ?? '';

        // Map Snippe event types to our status
        $status = match ($type) {
            'payment.completed' => 'successful',
            'payment.failed'    => 'failed',
            default             => 'pending',
        };

        return [
            'event'     => $type,
            'reference' => $data['reference'] ?? null,
            'status'    => $status,
            'amount'    => $data['amount']['value'] ?? 0,
            'currency'  => $data['amount']['currency'] ?? 'TZS',
            'metadata'  => $data['metadata'] ?? [],
            'raw'       => $payload,
        ];
    }

    /**
     * Trigger push notification for a pending mobile payment.
     *
     * POST /v1/payments/{reference}/push
     */
    public function triggerPush(string $reference, ?string $phone = null): array
    {
        try {
            $payload = [];
            if ($phone) {
                $payload['phone'] = $phone;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/v1/payments/{$reference}/push", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'raw'     => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json('message', 'Push failed'),
                'raw'     => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'raw'     => [],
            ];
        }
    }
}
