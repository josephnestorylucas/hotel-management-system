<?php

namespace App\Http\Controllers;

use App\Models\BookingCharge;
use App\Models\Booking;
use Illuminate\Http\Request;

/**
 * BookingChargeController
 * 
 * UNIFIED CHECKOUT FLOW:
 * - Charges should ONLY be marked as paid through the Finance Checkout process
 * - Direct marking of charges as paid is DISABLED to enforce checkout flow
 * - Use Finance Checkout at /finance/checkout/{booking} to settle all charges
 */
class BookingChargeController extends Controller
{
    /**
     * Display all charges for a booking
     */
    public function index(Request $request, Booking $booking)
    {
        $charges = $booking->bookingCharges()
            ->orderBy('created_at', 'desc')
            ->get();

        $unpaidTotal = $charges->where('status', 'unpaid')->sum('amount');
        $paidTotal = $charges->where('status', 'paid')->sum('amount');

        return view('booking-charges.index', compact('booking', 'charges', 'unpaidTotal', 'paidTotal'));
    }

    /**
     * Redirect to Finance Checkout instead of direct payment
     * 
     * DISABLED: Direct marking of charges as paid bypasses the unified checkout flow.
     * All payments must go through Finance Checkout.
     */
    public function markPaid(BookingCharge $bookingCharge)
    {
        // Redirect to checkout instead of marking paid directly
        return redirect()
            ->route('finance.checkout.show', $bookingCharge->booking_id)
            ->with('info', 'Please complete payment through the checkout process.');
    }

    /**
     * Redirect to Finance Checkout instead of bulk payment
     * 
     * DISABLED: Direct marking of all charges as paid bypasses the unified checkout flow.
     * All payments must go through Finance Checkout.
     */
    public function markAllPaid(Booking $booking)
    {
        // Redirect to checkout instead of marking all paid directly
        return redirect()
            ->route('finance.checkout.show', $booking->id)
            ->with('info', 'Please complete payment through the checkout process.');
    }

    /**
     * Redirect to Finance Checkout for settlement
     */
    public function proceedToCheckout(Booking $booking)
    {
        return redirect()->route('finance.checkout.show', $booking->id);
    }
}
