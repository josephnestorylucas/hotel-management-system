<?php

namespace App\Http\Controllers;

use App\Models\BookingCharge;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingChargeController extends Controller
{
    public function index(Request $request, Booking $booking)
    {
        $charges = $booking->bookingCharges()
            ->orderBy('created_at', 'desc')
            ->get();

        $unpaidTotal = $charges->where('status', 'unpaid')->sum('amount');
        $paidTotal = $charges->where('status', 'paid')->sum('amount');

        return view('booking-charges.index', compact('booking', 'charges', 'unpaidTotal', 'paidTotal'));
    }

    public function markPaid(BookingCharge $bookingCharge)
    {
        $bookingCharge->markAsPaid();

        return back()->with('success', 'Charge marked as paid.');
    }

    public function markAllPaid(Booking $booking)
    {
        $booking->bookingCharges()->unpaid()->update(['status' => 'paid']);

        return back()->with('success', 'All charges marked as paid.');
    }
}
