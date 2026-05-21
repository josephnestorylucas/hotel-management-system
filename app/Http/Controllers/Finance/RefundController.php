<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WalkinTransaction;
use App\Services\Payment\StandardizedPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * RefundController — Handles full and partial refunds.
 *
 * Supports refunds for:
 * - Online payments (Payment model via AzamPesa)
 * - Walk-in transactions (WalkinTransaction model)
 *
 * Features:
 * - Full and partial refunds
 * - Refund validation (cannot exceed paid amount)
 * - Transaction recording for audit
 * - Role-based access (manager+)
 */
class RefundController extends Controller
{
    protected StandardizedPaymentService $paymentService;

    public function __construct(StandardizedPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display the refunds management page.
     * Lists all refundable transactions.
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $type = $request->get('type', 'all'); // all, online, walkin
        $status = $request->get('status', 'refundable'); // refundable, refunded, all
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Online payments query
        $paymentsQuery = Payment::with(['booking', 'booking.guest', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($status === 'refundable') {
            $paymentsQuery->where('status', Payment::STATUS_SUCCESSFUL);
        } elseif ($status === 'refunded') {
            $paymentsQuery->whereIn('status', [Payment::STATUS_REFUNDED, Payment::STATUS_PARTIALLY_REFUNDED]);
        }

        if ($search) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('provider_reference', 'like', "%{$search}%")
                  ->orWhereHas('booking', function ($bq) use ($search) {
                      $bq->where('booking_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom) {
            $paymentsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $paymentsQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Walk-in transactions query
        $walkinQuery = WalkinTransaction::with(['creator'])
            ->orderBy('created_at', 'desc');

        if ($status === 'refundable') {
            $walkinQuery->where('status', 'completed')
                ->whereNotNull('provider_reference');
        } elseif ($status === 'refunded') {
            $walkinQuery->where('status', 'refunded');
        } else {
            $walkinQuery->whereNotNull('provider_reference');
        }

        if ($search) {
            $walkinQuery->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('provider_reference', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $walkinQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $walkinQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Execute queries based on type filter
        $payments = ($type === 'all' || $type === 'online') 
            ? $paymentsQuery->paginate(15, ['*'], 'payments_page')
            : collect();

        $walkinTransactions = ($type === 'all' || $type === 'walkin') 
            ? $walkinQuery->paginate(15, ['*'], 'walkin_page')
            : collect();

        // Statistics
        $stats = [
            'total_refundable_payments' => Payment::where('status', Payment::STATUS_SUCCESSFUL)->count(),
            'total_refundable_walkin'   => WalkinTransaction::where('status', 'completed')
                ->whereNotNull('provider_reference')->count(),
            'total_refunded_today'      => Payment::whereDate('refunded_at', today())->count() +
                WalkinTransaction::where('status', 'refunded')
                    ->whereDate('updated_at', today())->count(),
        ];

        return view('finance.refunds.index', compact(
            'payments',
            'walkinTransactions',
            'type',
            'status',
            'search',
            'dateFrom',
            'dateTo',
            'stats'
        ));
    }

    /**
     * Show refund form for an online payment.
     */
    public function showPayment(Payment $payment): View
    {
        $payment->load(['booking', 'booking.guest', 'creator']);

        // Calculate refund limits
        $totalRefunded = (float) ($payment->refund_metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $payment->amount - $totalRefunded;

        $validation = $this->paymentService->validateRefund($payment);

        return view('finance.refunds.show-payment', compact(
            'payment',
            'totalRefunded',
            'maxRefundable',
            'validation'
        ));
    }

    /**
     * Show refund form for a walk-in transaction.
     */
    public function showWalkin(WalkinTransaction $transaction): View
    {
        $transaction->load(['creator']);

        $totalRefunded = (float) ($transaction->metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $transaction->amount - $totalRefunded;

        $canRefund = $transaction->isCompleted() && 
                     !empty($transaction->provider_reference) && 
                     $maxRefundable > 0;

        return view('finance.refunds.show-walkin', compact(
            'transaction',
            'totalRefunded',
            'maxRefundable',
            'canRefund'
        ));
    }

    /**
     * Process refund for an online payment.
     */
    public function processPaymentRefund(Request $request, Payment $payment): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'refund_type'   => 'required|in:full,partial',
            'refund_amount' => 'required_if:refund_type,partial|nullable|numeric|min:0.01',
            'reason'        => 'nullable|string|max:500',
        ]);

        $amount = $data['refund_type'] === 'full' ? null : (float) $data['refund_amount'];

        $result = $this->paymentService->processRefund(
            $payment,
            $amount,
            $data['reason'] ?? null,
            auth()->id()
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('finance.refunds.index')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Process refund for a walk-in transaction.
     */
    public function processWalkinRefund(Request $request, WalkinTransaction $transaction): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'refund_type'   => 'required|in:full,partial',
            'refund_amount' => 'required_if:refund_type,partial|nullable|numeric|min:0.01',
            'reason'        => 'nullable|string|max:500',
        ]);

        $amount = $data['refund_type'] === 'full' ? null : (float) $data['refund_amount'];

        $result = $this->paymentService->processWalkinRefund(
            $transaction,
            $amount,
            $data['reason'] ?? null,
            auth()->id()
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('finance.refunds.index')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * API endpoint: Get refund validation for a payment.
     */
    public function validatePaymentRefund(Request $request, Payment $payment): JsonResponse
    {
        $amount = $request->get('amount') ? (float) $request->get('amount') : null;
        $validation = $this->paymentService->validateRefund($payment, $amount);

        $totalRefunded = (float) ($payment->refund_metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $payment->amount - $totalRefunded;

        return response()->json([
            'valid'           => $validation['valid'],
            'error'           => $validation['error'] ?? null,
            'total_refunded'  => $totalRefunded,
            'max_refundable'  => $maxRefundable,
            'original_amount' => (float) $payment->amount,
        ]);
    }

    /**
     * API endpoint: Get refund validation for a walk-in transaction.
     */
    public function validateWalkinRefund(Request $request, WalkinTransaction $transaction): JsonResponse
    {
        $totalRefunded = (float) ($transaction->metadata['total_refunded'] ?? 0);
        $maxRefundable = (float) $transaction->amount - $totalRefunded;

        $canRefund = $transaction->isCompleted() && 
                     !empty($transaction->provider_reference) && 
                     $maxRefundable > 0;

        $error = null;
        if (!$transaction->isCompleted()) {
            $error = 'Transaction is not completed';
        } elseif (empty($transaction->provider_reference)) {
            $error = 'No provider reference - cash transactions cannot be refunded via this system';
        } elseif ($maxRefundable <= 0) {
            $error = 'Transaction has already been fully refunded';
        }

        $requestedAmount = $request->get('amount') ? (float) $request->get('amount') : null;
        if ($canRefund && $requestedAmount !== null && $requestedAmount > $maxRefundable) {
            $canRefund = false;
            $error = "Cannot refund more than {$maxRefundable}";
        }

        return response()->json([
            'valid'           => $canRefund,
            'error'           => $error,
            'total_refunded'  => $totalRefunded,
            'max_refundable'  => $maxRefundable,
            'original_amount' => (float) $transaction->amount,
        ]);
    }
}
