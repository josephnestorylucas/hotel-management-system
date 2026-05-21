<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AzamPesaProvider implements PaymentProvider
{
    protected string $baseUrl;
    protected string $authUrl;
    protected string $appName;
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $webhookSecret;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl       = config('payment.providers.azampesa.base_url', 'https://sandbox.azampay.co.tz');
        $this->authUrl       = config('payment.providers.azampesa.auth_url', 'https://authenticator-sandbox.azampay.co.tz');
        $this->appName       = config('payment.providers.azampesa.app_name', '');
        $this->clientId      = config('payment.providers.azampesa.client_id', '');
        $this->clientSecret  = config('payment.providers.azampesa.client_secret', '');
        $this->webhookSecret = config('payment.providers.azampesa.webhook_secret');
        $this->timeout       = config('payment.providers.azampesa.timeout', 30);
    }

    public function name(): string
    {
        return 'azampesa';
    }

    public function supportedMethods(): array
    {
        return ['mobile', 'card'];
    }

    /**
     * Get or refresh the access token, cached for reuse.
     */
    protected function getAccessToken(): ?string
    {
        $cacheKey = 'azampesa_access_token';

        return Cache::remember($cacheKey, now()->addMinutes(50), function () {
            try {
                $response = Http::timeout($this->timeout)
                    ->post("{$this->authUrl}/AppRegistration/GenerateToken", [
                        'appName'      => $this->appName,
                        'clientId'     => $this->clientId,
                        'clientSecret' => $this->clientSecret,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $token = $data['data']['accessToken']['token']
                        ?? $data['data']['accessToken']
                        ?? null;
                    $expire = $data['data']['expire'] ?? null;

                    if ($token && $expire) {
                        $ttl = max(1, (int) ((strtotime($expire) - time()) / 60) - 5);
                        Cache::put($cacheKey, $token, now()->addMinutes($ttl));
                    }

                    Log::info('AzamPesa token generated successfully');
                    return $token;
                }

                Log::error('AzamPesa token generation failed', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
                return null;
            } catch (\Exception $e) {
                Log::error('AzamPesa token generation exception', ['message' => $e->getMessage()]);
                return null;
            }
        });
    }

    public function initiatePayment(float $amount, string $currency, string $method, array $metadata = []): array
    {
        return match ($method) {
            'mobile' => $this->mnoCheckout($amount, $currency, $metadata),
            'card'   => $this->bankCheckout($amount, $currency, $metadata),
            default  => [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => "Unsupported payment method: {$method}",
                'raw'       => [],
            ],
        };
    }

    /**
     * MNO Checkout — mobile money payment (Tigo, Airtel, Vodacom, Halotel).
     */
    protected function mnoCheckout(float $amount, string $currency, array $metadata): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => 'Failed to obtain access token from AzamPesa',
                'raw'       => [],
            ];
        }

        $phone = $this->normalizePhone($metadata['phone_number'] ?? $metadata['mobile_phone'] ?? '');
        if (empty($phone)) {
            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => 'Phone number is required for mobile payments',
                'raw'       => [],
            ];
        }

        $externalId = $metadata['idempotency_key'] ?? $metadata['payment_id'] ?? Str::uuid()->toString();
        $provider = $this->detectMobileProvider($phone);

        $payload = [
            'accountNumber'     => $phone,
            'amount'            => (string) round($amount),
            'currency'          => $currency,
            'externalId'        => $externalId,
            'provider'          => $provider,
            'additionalProperties' => [
                'booking_id'  => $metadata['booking_id'] ?? null,
                'charge_id'   => $metadata['charge_id'] ?? null,
                'payment_id'  => $metadata['payment_id'] ?? null,
                'order_id'    => $metadata['order_id'] ?? null,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/azampay/mno/checkout", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reference = $data['transactionId'] ?? $data['referenceId'] ?? $data['externalId'] ?? $externalId;

                Log::info('AzamPesa MNO checkout initiated', [
                    'reference' => $reference,
                    'provider'  => $provider,
                    'amount'    => $amount,
                ]);

                return [
                    'success'         => true,
                    'reference'       => (string) $reference,
                    'status'          => 'pending',
                    'payment_url'     => null,
                    'payment_qr_code' => null,
                    'payment_token'   => null,
                    'raw'             => $data,
                ];
            }

            $error = $response->json();
            Log::error('AzamPesa MNO checkout failed', [
                'status' => $response->status(),
                'error'  => $error,
            ]);

            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => $error['message'] ?? $error['title'] ?? 'MNO checkout failed',
                'raw'       => $error,
            ];
        } catch (\Exception $e) {
            Log::error('AzamPesa MNO checkout exception', ['message' => $e->getMessage()]);
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
     * Bank Checkout — card/bank payment (NMB, CRDB, etc.).
     */
    protected function bankCheckout(float $amount, string $currency, array $metadata): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => 'Failed to obtain access token from AzamPesa',
                'raw'       => [],
            ];
        }

        $externalId = $metadata['idempotency_key'] ?? $metadata['payment_id'] ?? Str::uuid()->toString();

        $payload = [
            'amount'                => (string) round($amount),
            'currencyCode'          => $currency,
            'merchantAccountNumber' => config('payment.providers.azampesa.merchant_account', ''),
            'merchantMobileNumber'  => config('payment.providers.azampesa.merchant_phone', ''),
            'merchantName'          => config('payment.providers.azampesa.merchant_name', null),
            'provider'              => $metadata['bank_provider'] ?? 'NMB',
            'referenceId'           => $externalId,
        ];

        $otp = $metadata['otp'] ?? null;
        if ($otp) {
            $payload['otp'] = $otp;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/azampay/bank/checkout", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reference = $data['transactionId'] ?? $data['referenceId'] ?? $data['externalId'] ?? $externalId;

                Log::info('AzamPesa bank checkout initiated', [
                    'reference' => $reference,
                    'provider'  => $payload['provider'],
                    'amount'    => $amount,
                ]);

                return [
                    'success'         => true,
                    'reference'       => (string) $reference,
                    'status'          => 'pending',
                    'payment_url'     => $data['paymentUrl'] ?? $data['checkoutUrl'] ?? null,
                    'payment_qr_code' => null,
                    'payment_token'   => $data['token'] ?? null,
                    'raw'             => $data,
                ];
            }

            $error = $response->json();
            Log::error('AzamPesa bank checkout failed', [
                'status' => $response->status(),
                'error'  => $error,
            ]);

            return [
                'success'   => false,
                'reference' => null,
                'status'    => 'failed',
                'error'     => $error['message'] ?? $error['title'] ?? 'Bank checkout failed',
                'raw'       => $error,
            ];
        } catch (\Exception $e) {
            Log::error('AzamPesa bank checkout exception', ['message' => $e->getMessage()]);
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
     * Verify payment status by querying the transaction status endpoint.
     */
    public function verifyPayment(string $reference): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'status'  => 'unknown',
                'error'   => 'Failed to obtain access token',
                'raw'     => [],
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/api/v1/azampay/transactionstatus", [
                'pgReferenceId' => $reference,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $status = match (strtolower($data['transactionstatus'] ?? $data['status'] ?? '')) {
                    'success', 'successful', 'completed' => 'completed',
                    'failed', 'cancelled', 'error'       => 'failed',
                    default                               => 'pending',
                };

                return [
                    'success' => true,
                    'status'  => $status,
                    'raw'     => $data,
                ];
            }

            return [
                'success' => false,
                'status'  => 'unknown',
                'raw'     => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('AzamPesa verify payment exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'status'  => 'unknown',
                'error'   => $e->getMessage(),
                'raw'     => [],
            ];
        }
    }

    /**
     * Refund is not directly supported by AzamPesa sandbox API.
     * Returns an error — refunds must be handled manually.
     */
    public function refundPayment(string $reference, ?float $amount = null): array
    {
        Log::warning('AzamPesa refund requested but not supported via API', [
            'reference' => $reference,
            'amount'    => $amount,
        ]);

        return [
            'success' => false,
            'error'   => 'AzamPesa does not support automated refunds. Please process manually.',
            'raw'     => [],
        ];
    }

    /**
     * Validate incoming callback signature using RSA public key.
     *
     * The signed data is: {utilityref}{externalreference}{transactionstatus}{operator}
     * The signature is RSA-signed and Base64-encoded.
     */
    public function validateWebhook(array $payload, array $headers): bool
    {
        $signature = $payload['signature'] ?? null;
        if (empty($signature)) {
            Log::warning('AzamPesa callback missing signature');
            return false;
        }

        $publicKeyPem = $this->fetchPublicKey();
        if (!$publicKeyPem) {
            Log::critical('AzamPesa public key unavailable — cannot verify callback');
            return false;
        }

        $signedData = ($payload['utilityref'] ?? '')
            . ($payload['externalreference'] ?? '')
            . ($payload['transactionstatus'] ?? '')
            . ($payload['operator'] ?? '');

        $publicKey = openssl_pkey_get_public($publicKeyPem);
        if (!$publicKey) {
            Log::error('AzamPesa failed to parse public key');
            return false;
        }

        $signatureBytes = base64_decode($signature, true);
        if ($signatureBytes === false) {
            Log::warning('AzamPesa callback signature is not valid base64');
            return false;
        }

        $valid = openssl_verify($signedData, $signatureBytes, $publicKey, OPENSSL_ALGO_SHA256) === 1;
        if (!$valid) {
            Log::warning('AzamPesa callback signature verification failed');
        }

        return $valid;
    }

    /**
     * Parse incoming AzamPesa callback into normalized structure.
     */
    public function parseWebhook(array $payload): array
    {
        $status = match (strtolower($payload['transactionstatus'] ?? '')) {
            'success', 'successful', 'completed' => 'successful',
            'failed', 'cancelled', 'error'       => 'failed',
            default                               => 'pending',
        };

        return [
            'event'     => 'checkout.callback',
            'reference' => $payload['utilityref'] ?? $payload['externalreference'] ?? null,
            'status'    => $status,
            'amount'    => (float) ($payload['amount'] ?? 0),
            'currency'  => 'TZS',
            'metadata'  => [
                'externalreference' => $payload['externalreference'] ?? null,
                'transid'           => $payload['transid'] ?? null,
                'operator'          => $payload['operator'] ?? null,
                'msisdn'            => $payload['msisdn'] ?? null,
                'mnoreference'      => $payload['mnoreference'] ?? null,
            ],
            'raw'       => $payload,
        ];
    }

    /**
     * Fetch AzamPesa RSA public key for callback signature verification.
     */
    protected function fetchPublicKey(): ?string
    {
        $cacheKey = 'azampesa_public_key';

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/api/v1/Checkout/Callback/public-key");

                if ($response->successful()) {
                    return $response->body();
                }

                Log::error('AzamPesa public key fetch failed', ['status' => $response->status()]);
                return null;
            } catch (\Exception $e) {
                Log::error('AzamPesa public key fetch exception', ['message' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Get available payment partners from AzamPesa.
     */
    public function getPaymentPartners(): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/api/v1/Partner/GetPaymentPartners");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('AzamPesa get payment partners exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Normalize phone number to 255XXXXXXXXX format.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return '';
        }

        if (str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '255')) {
            $phone = '255' . $phone;
        }

        return $phone;
    }

    /**
     * Detect mobile provider from phone number prefix.
     */
    protected function detectMobileProvider(string $phone): string
    {
        $prefix = substr($phone, 3, 2);

        return match ($prefix) {
            '65', '66', '67' => 'Tigo',
            '78', '79', '68', '69' => 'Airtel',
            '74', '75', '76' => 'Vodacom',
            '61', '62'        => 'Halotel',
            default           => 'Tigo',
        };
    }
}
