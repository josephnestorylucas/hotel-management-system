<?php

namespace App\Http\Controllers\Finance;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\Checkout;
use App\Models\FinancialTransaction;
use App\Models\FinancePayment;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Services\Billing\ModuleBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AccountingService;

class CheckoutController extends Controller
{
    public function __construct(
        protected ModuleBillingService $moduleBillingService
    ) {
    }

    /**
     * GET /finance/checkout/{booking}
     * Show the guest folio — all charges grouped by type.
     * 
     * UNIFIED CHECKOUT FLOW:
     * This is the SINGLE checkout page where ALL charges are aggregated and paid.
     */
    public function show(Booking $booking): View
    {
        $this->moduleBillingService->syncBookingChargesForBooking($booking, (string) Auth::id());

        // Get existing pending/draft checkout, or create new pending one
        $checkout = Checkout::where('booking_id', $booking->id)
            ->whereIn('status', ['pending', 'draft'])
            ->first();

        if (!$checkout) {
            $checkout = Checkout::create([
                'booking_id'   => $booking->id,
                'status'       => 'pending',
                'initiated_by' => (string) Auth::id(),
            ]);
        }

        // Get exchange rate from currency helper (cached)
        $exchangeRate = CurrencyHelper::getExchangeRate();

        // Fetch all unpaid charges grouped by type
        // This includes ALL charge types: restaurant, bar, laundry, room_service, etc.
        $charges = BookingCharge::where('booking_id', $booking->id)
            ->where('status', 'unpaid')
            ->with(['order', 'laundryOrder', 'createdBy'])
            ->orderBy('charge_type')
            ->orderBy('created_at')
            ->get();

        $chargesByType = $charges->groupBy('charge_type');

        // Recalculate totals fresh
        $checkout->calculateTotals();
        $checkout->refresh();

        return view('finance.checkout.show', compact(
            'booking', 'checkout', 'charges', 'chargesByType', 'exchangeRate'
        ));
    }

    /**
     * POST /finance/checkout/{checkout}/process
     * Process the actual payment and complete checkout.
     * 
     * UNIFIED CHECKOUT FLOW:
     * 1. Process payment
     * 2. Mark all BookingCharges as paid
     * 3. Finalize related module orders (Restaurant, Laundry) as 'settled'
     * 4. Record financial transactions
     */
    public function process(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if(in_array($checkout->status, ['completed', 'cancelled']), 422, 'This checkout is already completed or cancelled.');

        $data = $request->validate([
            'payment_method'  => 'required|in:cash_usd,cash_tzs,card_usd,card_tzs,split',
            'discount_usd'    => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
            // Split payment fields
            'cash_usd_amount' => 'required_if:payment_method,split|nullable|numeric|min:0',
            'card_usd_amount' => 'required_if:payment_method,split|nullable|numeric|min:0',
            'cash_tzs_amount' => 'nullable|numeric|min:0',
            'card_tzs_amount' => 'nullable|numeric|min:0',
        ]);

        $exchangeRate = (float) $checkout->exchange_rate;

        DB::transaction(function () use ($data, $checkout, $exchangeRate) {
            $this->moduleBillingService->syncBookingChargesForBooking($checkout->booking, (string) Auth::id());

            $checkout->calculateTotals();
            $checkout->refresh();


            // Apply discount if given
            if (!empty($data['discount_usd'])) {
                $checkout->update(['discount_usd' => $data['discount_usd']]);
                $checkout->calculateTotals();
                $checkout->refresh();
            }

            $grandTotalUsd = (float) $checkout->grand_total_usd;

            // Calculate how much was paid in USD equivalent
            $paidCashUsd = 0;
            $paidCardUsd = 0;

            switch ($data['payment_method']) {
                case 'cash_usd':
                    $paidCashUsd = $grandTotalUsd;
                    break;
                case 'card_usd':
                    $paidCardUsd = $grandTotalUsd;
                    break;
                case 'cash_tzs':
                    $paidCashUsd = round($checkout->grand_total_tzs / $exchangeRate, 2);
                    break;
                case 'card_tzs':
                    $paidCardUsd = round($checkout->grand_total_tzs / $exchangeRate, 2);
                    break;
                case 'split':
                    $paidCashUsd = (float) ($data['cash_usd_amount'] ?? 0);
                    $paidCardUsd = (float) ($data['card_usd_amount'] ?? 0);
                    // Add TZS amounts converted to USD
                    $paidCashUsd += round((float) ($data['cash_tzs_amount'] ?? 0) / $exchangeRate, 2);
                    $paidCardUsd += round((float) ($data['card_tzs_amount'] ?? 0) / $exchangeRate, 2);
                    break;
            }

            $totalPaidUsd = $paidCashUsd + $paidCardUsd;
            $changeDueUsd = max(0, $totalPaidUsd - $grandTotalUsd);

            abort_if($totalPaidUsd + 0.001 < $grandTotalUsd, 422, 'Payment is not enough to complete checkout. Settle the full folio balance first.');

            // Map payment method for module settlement
            $paymentMethodMap = [
                'cash_usd' => 'cash',
                'cash_tzs' => 'cash',
                'card_usd' => 'card',
                'card_tzs' => 'card',
                'split'    => 'split',
            ];
            $modulePaymentMethod = $paymentMethodMap[$data['payment_method']] ?? 'cash';

            // Update checkout record
            $checkout->update([
                'status'         => 'completed',
                'payment_method' => $data['payment_method'],
                'paid_cash_usd'  => $paidCashUsd,
                'paid_card_usd'  => $paidCardUsd,
                'paid_cash_tzs'  => round($paidCashUsd * $exchangeRate, 2),
                'paid_card_tzs'  => round($paidCardUsd * $exchangeRate, 2),
                'total_paid_usd' => $totalPaidUsd,
                'change_due_usd' => $changeDueUsd,
                'notes'          => $data['notes'] ?? null,
                'completed_by'   => (string) Auth::id(),
                'completed_at'   => now(),
            ]);

            // Get all unpaid charges before marking them paid
            $unpaidCharges = BookingCharge::where('booking_id', $checkout->booking_id)
                ->where('status', 'unpaid')
                ->get();

            // Mark all booking charges as paid
            BookingCharge::where('booking_id', $checkout->booking_id)
                ->where('status', 'unpaid')
                ->update([
                    'status'      => 'paid',
                    'checkout_id' => $checkout->id,
                ]);

            // FINALIZE RELATED MODULE ORDERS
            // This ensures orders in Restaurant, Bar, Laundry are marked as 'settled'
            $this->moduleBillingService->finalizeCharges($unpaidCharges, $modulePaymentMethod, (string) Auth::id());

            // Create the payment record
            $paymentCurrency = match ($data['payment_method']) {
                'cash_tzs', 'card_tzs' => 'TZS',
                default => 'USD',
            };

            $paymentAmount = $paymentCurrency === 'TZS'
                ? (float) $checkout->grand_total_tzs
                : $grandTotalUsd;

            $financeMethod = $data['payment_method'] === 'split'
                ? 'split'
                : (str_contains($data['payment_method'], 'cash') ? 'cash' : 'card');

            $payment = FinancePayment::create([
                'payment_type'  => 'checkout',
                'checkout_id'   => $checkout->id,
                'booking_id'    => $checkout->booking_id,
                'currency'      => $paymentCurrency,
                'amount'        => $paymentAmount,
                'amount_usd'    => $grandTotalUsd,
                'exchange_rate' => $exchangeRate,
                'method'        => $financeMethod,
                'status'        => 'completed',
                'created_by'    => (string) Auth::id(),
                'paid_at'       => now(),
            ]);

            // Write immutable financial transaction record
            FinancialTransaction::record([
                'type'           => 'checkout_payment',
                'source_module'  => 'accommodation',
                'payment_id'     => $payment->id,
                'booking_id'     => $checkout->booking_id,
                'currency'       => $payment->currency,
                'amount'         => $paymentAmount,
                'amount_usd'     => $grandTotalUsd,
                'exchange_rate'  => $exchangeRate,
                'payment_method' => $financeMethod,
                'description'    => "Guest checkout — Receipt {$checkout->receipt_number}",
            ], (string) Auth::id());

            // Post to accounting journal (room revenue)
            $booking = Booking::find($checkout->booking_id);
            $paymentMethod = $paymentMethodMap[$data['payment_method']] ?? 'cash';
            app(AccountingService::class)->postBookingSettlement(
                bookingRef: $booking->booking_number,
                bookingId: $booking->id,
                amount: (float) $checkout->grand_total_tzs,
                paymentMethod: $paymentMethod,
                actorId: (string) Auth::id()
            );
        });

        return redirect()
            ->route('finance.receipt.guest', $checkout)
            ->with('success', 'Checkout completed. Receipt ready.');
    }

    /**
     * Finalize related module orders after checkout payment.
     * 
     * This marks:
     * - Restaurant/Bar orders as 'settled'
     * - Laundry orders as 'settled'
     * - Other module orders as appropriate
     */
    /**
     * POST /finance/checkout/{checkout}/add-charge
     * Manually add a charge to a checkout that is still pending.
     */
    public function addCharge(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if(!in_array($checkout->status, ['pending', 'draft']), 422, 'Cannot add charges to a completed or cancelled checkout.');

        $data = $request->validate([
            'charge_type' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        $exchangeRate = CurrencyHelper::getExchangeRate();

        BookingCharge::create([
            'booking_id'  => $checkout->booking_id,
            'checkout_id' => $checkout->id,
            'charge_type' => $data['charge_type'],
            'description' => $data['description'],
            'amount'      => $data['amount'],
            'currency'    => 'USD',
            'amount_tzs'  => round($data['amount'] * $exchangeRate, 2),
            'status'      => 'unpaid',
            'created_by'  => (string) Auth::id(),
        ]);

        return redirect()
            ->route('finance.checkout.show', $checkout->booking)
            ->with('success', 'Charge added to folio.');
    }

    /**
     * POST /finance/checkout/{checkout}/draft
     * Save checkout as draft for later completion.
     */
    public function saveDraft(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if(in_array($checkout->status, ['completed', 'cancelled']), 422, 'Cannot draft a completed or cancelled checkout.');

        $this->moduleBillingService->syncBookingChargesForBooking($checkout->booking, (string) Auth::id());
        $checkout->calculateTotals();
        $checkout->refresh();

        $checkout->update([
            'status' => 'draft',
            'notes'  => $request->input('notes') ?: $checkout->notes,
        ]);

        return redirect()
            ->route('finance.dashboard')
            ->with('success', 'Checkout saved as draft. You can return to complete it later.');
    }
}
