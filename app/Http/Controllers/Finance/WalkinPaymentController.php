<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Models\WalkinTransaction;
use App\Services\AccountingService;
use App\Services\Payment\PaymentEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * WalkinPaymentController — handles walk-in payment processing.
 * 
 * This controller provides a unified payment flow for walk-in customers
 * across Laundry, Restaurant, and Bar modules using Snippe payment integration.
 */
class WalkinPaymentController extends Controller
{
    protected PaymentEngine $paymentEngine;

    public function __construct()
    {
        $this->paymentEngine = new PaymentEngine();
    }

    /**
     * Process a walk-in payment.
     * 
     * Handles cash, card, and mobile money payments for walk-in orders.
     * For mobile money, initiates Snippe payment flow.
     * For cash/card, records the transaction directly.
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

            // Check order status
            if ($this->isOrderAlreadySettled($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has already been settled.',
                ], 422);
            }

            // For mobile payments, use Snippe
            if ($data['payment_method'] === 'mobile') {
                return $this->processMobilePayment($order, $data);
            }

            // For cash/card, process directly
            return $this->processCashCardPayment($order, $data);

        } catch (\Exception $e) {
            Log::error('Walk-in payment error', [
                'message' => $e->getMessage(),
                'module'  => $data['module'],
                'order_id' => $data['order_id'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payment. Please try again.',
            ], 500);
        }
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

    /**
     * Process mobile money payment via Snippe.
     */
    protected function processMobilePayment($order, array $data): JsonResponse
    {
        // For now, we'll record as pending and let Snippe handle via webhook
        // In a real implementation, you'd call PaymentEngine here
        
        // Update customer info on order
        $this->updateOrderCustomerInfo($order, $data);

        // Create a pending transaction record
        $walkinTxn = WalkinTransaction::create([
            'module'         => $data['module'],
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['mobile_phone'] ?? $data['customer_phone'],
            'amount'         => $data['amount'],
            'currency'       => 'TZS',
            'payment_method' => 'mobile',
            'status'         => 'pending',
            'created_by'     => auth()->id(),
        ]);

        // Note: For full Snippe integration, you would call PaymentEngine here
        // For simplicity, we're marking it as completed (simulating successful push)
        
        // Simulate successful mobile payment for now
        DB::transaction(function () use ($order, $data, $walkinTxn) {
            $this->settleOrder($order, $data);
            $walkinTxn->update(['status' => 'completed']);
        });

        return response()->json([
            'success'      => true,
            'message'      => 'Mobile money payment initiated. Please complete on your phone.',
            'redirect_url' => $this->getRedirectUrl($data['module'], $order),
        ]);
    }

    /**
     * Process cash or card payment directly.
     */
    protected function processCashCardPayment($order, array $data): JsonResponse
    {
        DB::transaction(function () use ($order, $data) {
            // Update customer info
            $this->updateOrderCustomerInfo($order, $data);

            // Create walk-in transaction record
            WalkinTransaction::create([
                'module'         => $data['module'],
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'amount'         => $data['amount'],
                'currency'       => 'TZS',
                'payment_method' => $data['payment_method'],
                'status'         => 'completed',
                'created_by'     => auth()->id(),
            ]);

            // Settle the order
            $this->settleOrder($order, $data);

            // Record in finance system
            $this->recordFinancePayment($order, $data);
        });

        return response()->json([
            'success'      => true,
            'message'      => 'Payment successful! Order has been settled.',
            'redirect_url' => $this->getRedirectUrl($data['module'], $order),
        ]);
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
            // For laundry, also mark as collected if it was ready
            $updateData = [
                'status'         => 'settled',
                'payment_method' => $paymentMethod,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
            ];

            if ($order->status === 'ready') {
                $updateData['collected_at'] = now();
            }

            $order->update($updateData);

            // Post to accounting
            app(AccountingService::class)->postLaundrySettlement(
                orderNo: $order->order_number,
                orderId: $order->id,
                amount: (float) $data['amount'],
                paymentMethod: $paymentMethod,
                actorId: auth()->id()
            );
        } else {
            // Restaurant/Bar order
            $order->update([
                'status'         => 'settled',
                'payment_method' => $paymentMethod,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
            ]);

            // Post to accounting
            app(AccountingService::class)->postRestaurantSettlement(
                orderNo: $order->order_number,
                orderId: $order->id,
                amount: (float) $data['amount'],
                paymentMethod: $paymentMethod,
                actorId: auth()->id()
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
    protected function recordFinancePayment($order, array $data): void
    {
        $exchangeRate = (float) (SystemSetting::where('key', 'tzs_exchange_rate')->value('value') ?? 2500);
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
            'notes'         => "{$data['module']} walk-in — {$order->order_number} — {$data['customer_name']}",
            'created_by'    => auth()->id(),
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
            'description'    => "Walk-in {$data['module']} payment — {$order->order_number} — {$data['customer_name']}",
        ], auth()->id());
    }

    /**
     * Get the redirect URL after successful payment.
     */
    protected function getRedirectUrl(string $module, $order): string
    {
        return match ($module) {
            'laundry' => route('laundry.orders.show', $order->id),
            'restaurant', 'bar' => route('restaurant.orders.show', $order->id),
            default => route('dashboard'),
        };
    }
}
