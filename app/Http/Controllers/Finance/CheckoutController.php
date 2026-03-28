<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\Checkout;
use App\Models\FinancialTransaction;
use App\Models\FinancePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AccountingService;

class CheckoutController extends Controller
{
    /**
     * GET /finance/checkout/{booking}
     * Show the guest folio — all charges grouped by type.
     */
    public function show(Booking $booking): View
    {
        // Get or create a pending checkout for this booking
        $checkout = Checkout::firstOrCreate(
            ['booking_id' => $booking->id, 'status' => 'pending'],
            ['initiated_by' => auth()->id()]
        );

        // Get exchange rate
        $exchangeRate = (float) (DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500);

        // Fetch all unpaid charges grouped by type
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
     */
    public function process(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if($checkout->status === 'completed', 422, 'This checkout is already completed.');

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
                'completed_by'   => auth()->id(),
                'completed_at'   => now(),
            ]);

            // Mark all booking charges as paid
            BookingCharge::where('booking_id', $checkout->booking_id)
                ->where('status', 'unpaid')
                ->update([
                    'status'      => 'paid',
                    'checkout_id' => $checkout->id,
                ]);

            // Create the payment record
            $payment = FinancePayment::create([
                'payment_type'  => 'checkout',
                'checkout_id'   => $checkout->id,
                'booking_id'    => $checkout->booking_id,
                'currency'      => str_contains($data['payment_method'], 'tzs') ? 'TZS' : 'USD',
                'amount'        => $grandTotalUsd,
                'amount_usd'    => $grandTotalUsd,
                'exchange_rate' => $exchangeRate,
                'method'        => str_contains($data['payment_method'], 'cash') ? 'cash' : 'card',
                'status'        => 'completed',
                'created_by'    => auth()->id(),
                'paid_at'       => now(),
            ]);

            // Write immutable financial transaction record
            FinancialTransaction::record([
                'type'           => 'checkout_payment',
                'source_module'  => 'accommodation',
                'payment_id'     => $payment->id,
                'booking_id'     => $checkout->booking_id,
                'currency'       => $payment->currency,
                'amount'         => $grandTotalUsd,
                'amount_usd'     => $grandTotalUsd,
                'exchange_rate'  => $exchangeRate,
                'payment_method' => $payment->method,
                'description'    => "Guest checkout — Receipt {$checkout->receipt_number}",
            ], auth()->id());

            // Post to accounting journal (room revenue)
            $booking = Booking::find($checkout->booking_id);
            $paymentMethodMap = [
                'cash_usd' => 'cash',
                'cash_tzs' => 'cash',
                'card_usd' => 'card',
                'card_tzs' => 'card',
                'split' => 'cash',
            ];
            $paymentMethod = $paymentMethodMap[$data['payment_method']] ?? 'cash';
            app(AccountingService::class)->postBookingSettlement(
                bookingRef: $booking->booking_number,
                bookingId: $booking->id,
                amount: (float) $checkout->grand_total_tzs,
                paymentMethod: $paymentMethod,
                actorId: auth()->id()
            );
        });

        return redirect()
            ->route('finance.receipt.guest', $checkout)
            ->with('success', 'Checkout completed. Receipt ready.');
    }

    /**
     * POST /finance/checkout/{checkout}/add-charge
     * Manually add a charge to a checkout that is still pending.
     */
    public function addCharge(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if($checkout->status !== 'pending', 422, 'Cannot add charges to a completed checkout.');

        $data = $request->validate([
            'charge_type' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        $exchangeRate = (float) (DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500);

        BookingCharge::create([
            'booking_id'  => $checkout->booking_id,
            'checkout_id' => $checkout->id,
            'charge_type' => $data['charge_type'],
            'description' => $data['description'],
            'amount'      => $data['amount'],
            'currency'    => 'USD',
            'amount_tzs'  => round($data['amount'] * $exchangeRate, 2),
            'status'      => 'unpaid',
            'created_by'  => auth()->id(),
        ]);

        return redirect()
            ->route('finance.checkout.show', $checkout->booking)
            ->with('success', 'Charge added to folio.');
    }
}
