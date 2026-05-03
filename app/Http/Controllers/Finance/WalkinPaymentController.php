<?php

namespace App\Http\Controllers\Finance;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Models\WalkinTransaction;
use App\Services\Bartender\BarOrderStockService;
use App\Services\AccountingService;
use App\Services\Payment\StandardizedPaymentService;
use App\Services\Payment\SnippeProvider;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * WalkinPaymentController — handles walk-in payment processing.
 * 
 * This controller provides a unified payment flow for walk-in customers
 * across Laundry, Restaurant, and Bar modules.
 * 
 * Uses StandardizedPaymentService to ensure correct customer identity
 * is passed to Snippe for all payment types:
 * - Cash: Records payment directly (physical cash handled at POS)
 * - Card: Redirects to Snippe payment page for card entry
 * - Mobile: Sends USSD push to customer's phone via Snippe
 */
class WalkinPaymentController extends Controller
{
    protected StandardizedPaymentService $paymentService;
    protected SnippeProvider $snippeProvider;

    public function __construct(
        StandardizedPaymentService $paymentService,
        protected ReceiptService $receiptService
    )
    {
        $this->paymentService = $paymentService;
        $this->snippeProvider = $paymentService->getProvider();
    }

    /**
     * Process a walk-in payment.
     * 
     * Handles cash, card, and mobile money payments for walk-in orders.
     */
    public function process(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id'       => 'required|uuid',
            'module'         => 'required|in:laundry,restaurant,bar',
            'amount'         => 'required|numeric|min:1',
            'customer_name'  => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'payment_method' => 'required|in:cash,card,mobile',
            'mobile_phone'   => 'required_if:payment_method,mobile|nullable|string|max:30',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        try {
            // Find the order based on module
            $order = $this->findOrder($data['module'], $data['order_id']);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            if (!$this->canProcessModulePayment($data['module'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to process this walk-in payment.',
                ], 403);
            }

            // Check order status
            if ($this->isOrderAlreadySettled($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has already been settled.',
                ], 422);
            }

            if (!$this->canUseWalkinPaymentFlow($order, $data['module'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order must use its module billing flow and cannot be settled as a walk-in payment.',
                ], 422);
            }

            $data['amount'] = (float) $order->total;

            // Update customer info on order first
            $this->updateOrderCustomerInfo($order, $data);

            // Process based on payment method
            return match ($data['payment_method']) {
                'cash'   => $this->processCashPayment($order, $data),
                'card'   => $this->processCardPayment($order, $data),
                'mobile' => $this->processMobilePayment($order, $data),
            };

        } catch (\Exception $e) {
            Log::error('Walk-in payment error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'module'  => $data['module'] ?? 'unknown',
                'order_id' => $data['order_id'] ?? 'unknown',
            ]);

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Unable to finalize payment.',
                ], $e->getStatusCode());
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Check payment status (for polling from frontend).
     */
    public function status(string $reference): JsonResponse
    {
        $walkinTxn = WalkinTransaction::where('provider_reference', $reference)->first();

        if (!$walkinTxn) {
            return response()->json([
                'success' => false,
                'status'  => 'not_found',
                'message' => 'Transaction not found.',
            ], 404);
        }

        // If still pending, check with Snippe
        if ($walkinTxn->status === 'pending' && $walkinTxn->provider_reference) {
            $result = $this->snippeProvider->verifyPayment($walkinTxn->provider_reference);
            
            if ($result['success']) {
                $providerStatus = $result['status'] ?? 'unknown';
                
                if ($providerStatus === 'completed') {
                    // Payment confirmed - settle the order
                    $order = $this->findOrder($walkinTxn->module, $walkinTxn->order_id);
                    
                    if ($order && !$this->isOrderAlreadySettled($order)) {
                        DB::transaction(function () use ($order, $walkinTxn) {
                            $data = [
                                'module'         => $walkinTxn->module,
                                'amount'         => $walkinTxn->amount,
                                'payment_method' => $walkinTxn->payment_method,
                                'customer_name'  => $walkinTxn->customer_name,
                                'payment_reference' => $walkinTxn->provider_reference,
                            ];
                            
                            $this->settleOrder($order, $data);
                            $this->recordFinancePayment($order, $data, $walkinTxn);
                            $this->createOrderReceiptIfNeeded($order);
                            $walkinTxn->markCompleted(['verified_at' => now()]);
                        });
                    }
                    
                    return response()->json([
                        'success'      => true,
                        'status'       => 'completed',
                        'message'      => 'Payment confirmed!',
                        'redirect_url' => $this->getRedirectUrl($walkinTxn->module, $order),
                    ]);
                } elseif ($providerStatus === 'failed') {
                    $walkinTxn->markFailed(['reason' => 'Payment failed at provider']);
                    
                    return response()->json([
                        'success' => false,
                        'status'  => 'failed',
                        'message' => 'Payment failed or was cancelled.',
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'status'  => $walkinTxn->status,
            'message' => $walkinTxn->status === 'completed' 
                ? 'Payment completed!' 
                : 'Payment is still pending.',
        ]);
    }

    /**
     * Find the order based on module type.
     */
    protected function findOrder(string $module, string $orderId)
    {
        return match ($module) {
            'laundry' => LaundryOrder::find($orderId),
            'restaurant', 'bar' => Order::find($orderId),
            default => null,
        };
    }

    /**
     * Check if order is already settled.
     */
    protected function isOrderAlreadySettled($order): bool
    {
        return in_array($order->status, ['settled', 'cancelled']);
    }

    protected function canUseWalkinPaymentFlow($order, string $module): bool
    {
        if ($order instanceof LaundryOrder) {
            return $order->customer_type === 'walkin';
        }

        if ($order instanceof Order) {
            if ($order->booking_id || $order->order_type !== 'walkin') {
                return false;
            }

            $locationCode = strtolower($order->location?->code ?? '');

            if (str_contains($locationCode, 'bar') && $order->bartender_status !== 'prepared') {
                return false;
            }

            return match ($module) {
                'bar' => str_contains($locationCode, 'bar'),
                'restaurant' => !str_contains($locationCode, 'bar'),
                default => false,
            };
        }

        return false;
    }

    protected function canProcessModulePayment(string $module): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return match ($module) {
            'bar' => $user->hasAnyRole(['bar_tender']),
            default => true,
        };
    }

    /**
     * Process cash payment - settles immediately.
     * Cash is handled physically at POS, we just record it.
     */
    protected function processCashPayment($order, array $data): JsonResponse
    {
        DB::transaction(function () use ($order, $data) {
            // Create walk-in transaction record
            $walkinTxn = WalkinTransaction::create([
                'module'         => $data['module'],
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'amount'         => $data['amount'],
                'currency'       => 'TZS',
                'payment_method' => 'cash',
                'status'         => 'completed',
                'created_by'     => (string) Auth::id(),
                'completed_at'   => now(),
                'provider_reference' => $data['payment_reference'] ?? null,
            ]);

            // Settle the order
            $this->settleOrder($order, $data);

            // Record in finance system
            $this->recordFinancePayment($order, $data, $walkinTxn);
            $this->createOrderReceiptIfNeeded($order);
        });

        return response()->json([
            'success'      => true,
            'message'      => 'Cash payment recorded. Order has been settled.',
            'redirect_url' => $this->getRedirectUrl($data['module'], $order),
        ]);
    }

    /**
     * Process card payment via Snippe - redirects to payment page.
     * Uses StandardizedPaymentService to ensure correct customer identity.
     */
    protected function processCardPayment($order, array $data): JsonResponse
    {
        $idempotencyKey = Str::uuid()->toString();
        
        // Create pending transaction record
        $walkinTxn = WalkinTransaction::create([
            'module'         => $data['module'],
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'amount'         => $data['amount'],
            'currency'       => 'TZS',
            'payment_method' => 'card',
            'status'         => 'pending',
            'created_by'     => (string) Auth::id(),
            'metadata'       => ['idempotency_key' => $idempotencyKey],
        ]);

        // Get customer identity using standardized service
        $identity = $this->paymentService->resolveIdentity(null, [
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
        ]);

        // Call Snippe via standardized service
        $result = $this->paymentService->initiateCardPayment(
            amount: (float) $data['amount'],
            currency: 'TZS',
            identity: $identity,
            metadata: [
                'payment_id'     => $walkinTxn->id,
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'module'         => $data['module'],
                'idempotency_key' => $idempotencyKey,
                'redirect_url'   => route('finance.walkin-payment.callback', [
                    'transaction' => $walkinTxn->id,
                    'status'      => 'success',
                ]),
                'cancel_url'     => route('finance.walkin-payment.callback', [
                    'transaction' => $walkinTxn->id,
                    'status'      => 'cancel',
                ]),
            ]
        );

        if ($result['success'] && !empty($result['payment_url'])) {
            // Update transaction with Snippe reference
            $walkinTxn->update([
                'provider_reference' => $result['reference'],
                'metadata'           => array_merge($walkinTxn->metadata ?? [], [
                    'snippe_response' => $result['raw'] ?? [],
                    'customer_identity' => $identity,
                ]),
            ]);

            return response()->json([
                'success'     => true,
                'message'     => 'Redirecting to secure payment page...',
                'payment_url' => $result['payment_url'],
                'reference'   => $result['reference'],
            ]);
        }

        // Payment initiation failed
        $walkinTxn->markFailed(['error' => $result['error'] ?? 'Unknown error']);

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to initiate card payment. Please try again.',
        ], 422);
    }

    /**
     * Process mobile money payment via Snippe - sends USSD push.
     * Uses StandardizedPaymentService to ensure correct customer identity and phone validation.
     */
    protected function processMobilePayment($order, array $data): JsonResponse
    {
        $idempotencyKey = Str::uuid()->toString();
        $mobilePhone = $data['mobile_phone'] ?? $data['customer_phone'];

        // Validate phone number first using standardized service
        $phoneValidation = $this->paymentService->validatePhone($mobilePhone);
        if (!$phoneValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $phoneValidation['error'],
            ], 422);
        }

        // Create pending transaction record
        $walkinTxn = WalkinTransaction::create([
            'module'         => $data['module'],
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $mobilePhone,
            'amount'         => $data['amount'],
            'currency'       => 'TZS',
            'payment_method' => 'mobile',
            'status'         => 'pending',
            'created_by'     => (string) Auth::id(),
            'metadata'       => ['idempotency_key' => $idempotencyKey],
        ]);

        // Get customer identity using standardized service
        $identity = $this->paymentService->resolveIdentity(null, [
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $mobilePhone,
        ]);

        // Call Snippe via standardized service (phone already validated)
        $result = $this->paymentService->initiateUssdPayment(
            amount: (float) $data['amount'],
            currency: 'TZS',
            identity: $identity,
            metadata: [
                'payment_id'       => $walkinTxn->id,
                'order_id'         => $order->id,
                'order_number'     => $order->order_number,
                'module'           => $data['module'],
                'idempotency_key'  => $idempotencyKey,
            ]
        );

        if ($result['success']) {
            // Update transaction with Snippe reference
            $walkinTxn->update([
                'provider_reference' => $result['reference'],
                'metadata'           => array_merge($walkinTxn->metadata ?? [], [
                    'snippe_response' => $result['raw'] ?? [],
                    'customer_identity' => $identity,
                ]),
            ]);

            return response()->json([
                'success'   => true,
                'pending'   => true,
                'message'   => 'Payment request sent! Please check your phone and enter PIN to confirm.',
                'reference' => $result['reference'],
            ]);
        }

        // Payment initiation failed
        $walkinTxn->markFailed(['error' => $result['error'] ?? 'Unknown error']);

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to send payment request. Please check the phone number and try again.',
        ], 422);
    }

    /**
     * Handle callback from Snippe after card payment.
     */
    public function callback(Request $request, string $transaction): \Illuminate\Http\RedirectResponse
    {
        $walkinTxn = WalkinTransaction::findOrFail($transaction);
        $status = $request->query('status', 'cancel');

        if ($status === 'success' && $walkinTxn->provider_reference) {
            // Verify payment with Snippe
            $result = $this->snippeProvider->verifyPayment($walkinTxn->provider_reference);
            
            if ($result['success'] && ($result['status'] ?? '') === 'completed') {
                // Payment confirmed - settle the order
                $order = $this->findOrder($walkinTxn->module, $walkinTxn->order_id);
                
                if ($order && !$this->isOrderAlreadySettled($order)) {
                    DB::transaction(function () use ($order, $walkinTxn) {
                        $data = [
                            'module'         => $walkinTxn->module,
                            'amount'         => $walkinTxn->amount,
                            'payment_method' => $walkinTxn->payment_method,
                            'customer_name'  => $walkinTxn->customer_name,
                            'payment_reference' => $walkinTxn->provider_reference,
                        ];
                        
                        $this->settleOrder($order, $data);
                        $this->recordFinancePayment($order, $data, $walkinTxn);
                        $this->createOrderReceiptIfNeeded($order);
                        $walkinTxn->markCompleted(['callback_verified' => true]);
                    });
                    
                    return redirect($this->getRedirectUrl($walkinTxn->module, $order))
                        ->with('success', 'Payment successful! Order has been settled.');
                }
            }
        }

        // Payment failed or cancelled
        if ($walkinTxn->isPending()) {
            $walkinTxn->markFailed(['reason' => 'Payment cancelled or failed']);
        }

        $order = $this->findOrder($walkinTxn->module, $walkinTxn->order_id);
        
        return redirect($this->getRedirectUrl($walkinTxn->module, $order))
            ->with('error', 'Payment was cancelled or failed. Please try again.');
    }

    /**
     * Update customer information on the order.
     */
    protected function updateOrderCustomerInfo($order, array $data): void
    {
        if ($order instanceof LaundryOrder) {
            $order->update([
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? $data['mobile_phone'] ?? $order->customer_phone,
            ]);
        } else {
            $order->update([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? $data['mobile_phone'] ?? $order->customer_phone,
            ]);
        }
    }

    /**
     * Settle the order and mark it as paid.
     */
    protected function settleOrder($order, array $data): void
    {
        $paymentMethod = $data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method'];

        if ($order instanceof LaundryOrder) {
            // For laundry, mark as collected if not already collected
            $updateData = [
                'status'         => 'settled',
                'payment_method' => $paymentMethod,
                'settled_by'     => (string) Auth::id(),
                'settled_at'     => now(),
            ];

            if (in_array($order->status, ['ready', 'collected']) && !$order->collected_at) {
                $updateData['collected_at'] = now();
            }

            $order->update($updateData);

            // Post to accounting
            app(AccountingService::class)->postLaundrySettlement(
                orderNo: $order->order_number,
                orderId: $order->id,
                amount: (float) $data['amount'],
                paymentMethod: $paymentMethod,
                actorId: (string) Auth::id()
            );
        } else {
            // Restaurant/Bar order
            
            app(BarOrderStockService::class)->deductForOrder($order, (string) Auth::id());

            $order->update([
                'status'         => 'settled',
                'bartender_status' => 'served',
                'bartender_status_updated_at' => now(),
                'payment_method' => $paymentMethod,
                'payment_reference' => $data['payment_reference'] ?? null,
                'settled_by'     => (string) Auth::id(),
                'settled_at'     => now(),
            ]);

            // Post to accounting
            app(AccountingService::class)->postRestaurantSettlement(
                orderNo: $order->order_number,
                orderId: $order->id,
                amount: (float) $data['amount'],
                paymentMethod: $paymentMethod,
                actorId: (string) Auth::id()
            );

            // Free up the table if applicable
            if ($order->table_id) {
                \App\Models\Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }
    }

    /**
     * Record payment in the finance system.
     */
    protected function recordFinancePayment($order, array $data, ?WalkinTransaction $walkinTxn = null): void
    {
        $exchangeRate = CurrencyHelper::getExchangeRate();
        $amountUsd = FinancePayment::toUsd((float) $data['amount'], 'TZS', $exchangeRate);

        $payment = FinancePayment::create([
            'payment_type'  => 'walkin',
            'checkout_id'   => null,
            'order_id'      => $order instanceof Order ? $order->id : null,
            'method'        => $data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method'],
            'currency'      => 'TZS',
            'amount'        => $data['amount'],
            'amount_usd'    => $amountUsd,
            'exchange_rate' => $exchangeRate,
            'status'        => 'completed',
            'reference'     => $walkinTxn?->provider_reference ?? $data['payment_reference'] ?? null,
            'notes'         => "{$data['module']} walk-in — {$order->order_number} — {$data['customer_name']}",
            'created_by'    => (string) Auth::id(),
            'paid_at'       => now(),
        ]);

        // Determine source module
        $sourceModule = $data['module'];
        if ($sourceModule === 'restaurant' && $order instanceof Order && $order->location) {
            $code = strtolower($order->location->code ?? '');
            if (str_contains($code, 'bar')) {
                $sourceModule = 'bar';
            }
        }

        FinancialTransaction::record([
            'type'           => 'walkin_sale',
            'source_module'  => $sourceModule,
            'payment_id'     => $payment->id,
            'order_id'       => $order instanceof Order ? $order->id : null,
            'currency'       => 'TZS',
            'amount'         => $data['amount'],
            'amount_usd'     => $amountUsd,
            'exchange_rate'  => $exchangeRate,
            'payment_method' => $data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method'],
            'description'    => "Walk-in {$data['module']} payment — {$order->order_number} — {$data['customer_name']}" .
                (($walkinTxn?->provider_reference ?? $data['payment_reference'] ?? null) ? " (Ref: " . ($walkinTxn?->provider_reference ?? $data['payment_reference']) . ")" : ''),
        ], (string) Auth::id());
    }

    protected function createOrderReceiptIfNeeded($order): void
    {
        if ($order instanceof Order && $order->status === 'settled') {
            $this->receiptService->getOrCreateReceipt($order->fresh());
        }
    }

    /**
     * Get the redirect URL after successful payment.
     */
    protected function getRedirectUrl(string $module, $order): string
    {
        return match ($module) {
            'laundry' => route('laundry.orders.show', $order->id),
            'bar' => route('bartender.orders.show', $order->id),
            'restaurant' => route('restaurant.orders.show', $order->id),
            default => route('dashboard'),
        };
    }
}
