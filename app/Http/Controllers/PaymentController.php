<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\Payment;
use App\Services\Payment\PaymentEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController — handles payment initiation, callbacks, history, and refunds.
 */
class PaymentController extends Controller
{
    protected PaymentEngine $engine;

    public function __construct()
    {
        $this->engine = new PaymentEngine();
    }

    // ═══════════════════════════════════════════════════════════════
    //  PAYMENT FORM — show payment page for a booking
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the payment form for a booking.
     * Optionally pay a specific charge via ?charge_id=xxx
     */
    public function create(Request $request, Booking $booking)
    {
        $charge = null;
        $amount = $booking->total_amount;

        if ($request->filled('charge_id')) {
            $charge = BookingCharge::findOrFail($request->charge_id);
            $amount = $charge->amount;
        }

        $methods = $this->engine->supportedMethods();

        return view('payments.create', compact('booking', 'charge', 'amount', 'methods'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  INITIATE PAYMENT
    // ═══════════════════════════════════════════════════════════════

    /**
     * Initiate a payment via the provider.
     */
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:mobile,card,dynamic-qr',
            'phone_number'   => 'required_if:payment_method,mobile|nullable|string',
            'charge_id'      => 'nullable|uuid|exists:booking_charges,id',
        ]);

        $charge = null;
        if ($request->filled('charge_id')) {
            $charge = BookingCharge::findOrFail($request->charge_id);
        }

        $options = [];
        if ($request->filled('phone_number')) {
            $options['phone_number'] = $request->phone_number;
        }

        try {
            $payment = $this->engine->pay(
                booking: $booking,
                amount: (float) $request->amount,
                method: $request->payment_method,
                options: $options,
                charge: $charge,
                userId: auth()->id(),
            );

            if ($payment->isFailed()) {
                return back()->with('error', 'Payment initiation failed. Please try again.');
            }

            // For card payments — redirect to payment URL
            if ($payment->payment_url && in_array($request->payment_method, ['card'])) {
                return redirect()->away($payment->payment_url);
            }

            // For mobile / QR — show waiting page
            return redirect()->route('payments.status', $payment)
                ->with('success', 'Payment initiated! Please complete the payment on your device.');

        } catch (\Exception $e) {
            Log::error('Payment store exception', ['message' => $e->getMessage()]);
            return back()->with('error', 'An error occurred while processing your payment.');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  PAYMENT STATUS — polling page for mobile/QR
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the payment status page (used for mobile money / QR code payments).
     */
    public function status(Payment $payment)
    {
        $payment->load('booking', 'bookingCharge');
        return view('payments.status', compact('payment'));
    }

    /**
     * AJAX endpoint — check payment status.
     */
    public function checkStatus(Payment $payment)
    {
        // Verify with provider if still pending
        if ($payment->isPending()) {
            $payment = $this->engine->verify($payment);
        }

        return response()->json([
            'status'     => $payment->status,
            'successful' => $payment->isSuccessful(),
            'failed'     => $payment->isFailed(),
        ]);
    }

    /**
     * Trigger mobile money push (resend USSD prompt).
     */
    public function triggerPush(Payment $payment)
    {
        if (!$payment->isPending()) {
            return response()->json(['success' => false, 'error' => 'Payment is no longer pending.']);
        }

        $result = $this->engine->triggerPush($payment);

        return response()->json($result);
    }

    // ═══════════════════════════════════════════════════════════════
    //  CALLBACK — provider redirects back here (card / QR)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Handle redirect callback from the payment provider.
     */
    public function callback(Request $request)
    {
        $provider = $request->query('provider', 'snippe');
        $status   = $request->query('status', 'success');

        // Try to find the payment from query params
        $reference = $request->query('reference')
            ?? $request->query('session_id')
            ?? $request->query('token');

        if ($reference) {
            $payment = DB::transaction(function () use ($reference) {
                $payment = Payment::where('provider_reference', $reference)
                    ->lockForUpdate()
                    ->first();

                if ($payment && $payment->isPending()) {
                    $payment = $this->engine->verify($payment);
                }

                return $payment;
            });

            if ($payment) {
                return redirect()->route('payments.status', $payment);
            }
        }

        // Fallback — no payment found
        if ($status === 'cancel') {
            return redirect()->route('bookings.index')->with('warning', 'Payment was cancelled.');
        }

        return redirect()->route('bookings.index')->with('info', 'Payment callback received.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  PAYMENT HISTORY — for a booking
    // ═══════════════════════════════════════════════════════════════

    /**
     * List all payments for a booking.
     */
    public function index(Booking $booking)
    {
        $payments = $booking->payments()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPaid = $payments->where('status', 'successful')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');

        return view('payments.index', compact('booking', 'payments', 'totalPaid', 'totalPending'));
    }

    /**
     * Show a single payment receipt.
     */
    public function show(Payment $payment)
    {
        $payment->load('booking', 'bookingCharge', 'creator');
        return view('payments.show', compact('payment'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  REFUND
    // ═══════════════════════════════════════════════════════════════

    /**
     * Refund a payment.
     * Only admin and supervisor roles can process refunds.
     */
    public function refund(Request $request, Payment $payment)
    {
        // Authorization: only admin or supervisor can process refunds
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            Log::warning('Unauthorized refund attempt', [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'amount' => $request->amount,
            ]);
            abort(403, 'Only administrators and supervisors can process refunds.');
        }

        $request->validate([
            'amount' => 'nullable|numeric|min:1|max:' . $payment->amount,
        ]);

        try {
            $amount = $request->filled('amount') ? (float) $request->amount : null;
            $this->engine->refund($payment, $amount);

            return back()->with('success', 'Payment refunded successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
