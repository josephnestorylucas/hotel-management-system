<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\Checkout;
use App\Models\FinancialTransaction;
use App\Models\FinancePayment;
use App\Models\LaundryOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    /**
     * GET /finance/dashboard
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        // Revenue by source module
        $revenueByModule = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('source_module', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('source_module')
            ->get();

        // Revenue by payment method
        $revenueByMethod = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('payment_method', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('payment_method')
            ->get();

        // Daily revenue trend (last 30 days)
        $dailyRevenue = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', today()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_usd) as total_usd')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Today's summary
        $todaySummary = [
            'total_revenue'    => FinancialTransaction::whereDate('created_at', today())
                ->where('type', '!=', 'refund')->sum('amount_usd'),
            'checkout_revenue' => FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'checkout_payment')->sum('amount_usd'),
            'walkin_revenue'   => FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'walkin_sale')->sum('amount_usd'),
            'cash_total'       => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'cash')->sum('amount_usd'),
            'card_total'       => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'card')->sum('amount_usd'),
        ];

        // Outstanding charges (unpaid booking charges)
        $outstandingTotal = BookingCharge::where('status', 'unpaid')->sum('amount');

        // Recent transactions
        $recentTransactions = FinancialTransaction::with(['payment', 'actor'])
            ->latest('created_at')
            ->take(15)
            ->get();

        $ordersMissingCharges = Order::with(['booking', 'location'])
            ->whereNotNull('booking_id')
            ->whereIn('status', ['served', 'charged'])
            ->whereDoesntHave('charge')
            ->latest('created_at')
            ->take(10)
            ->get();

        $laundryMissingCharges = LaundryOrder::with(['booking', 'booking.bookingCharges'])
            ->whereNotNull('booking_id')
            ->whereIn('status', ['ready', 'delivered', 'charged'])
            ->whereNotIn('status', ['settled', 'cancelled'])
            ->latest('created_at')
            ->take(30)
            ->get()
            ->filter(function (LaundryOrder $order) {
                return !$order->booking?->bookingCharges
                    ? true
                    : !$order->booking->bookingCharges->contains(fn ($charge) => $charge->source === 'laundry' && $charge->reference_id === $order->id);
            })
            ->take(10)
            ->values();

        $unpaidChargesByBooking = BookingCharge::with('booking')
            ->where('status', 'unpaid')
            ->select('booking_id', DB::raw('COUNT(*) as charge_count'), DB::raw('SUM(amount_tzs) as total_tzs'))
            ->groupBy('booking_id')
            ->orderByDesc('total_tzs')
            ->take(10)
            ->get();

        // Draft checkouts that can be completed
        $draftCheckouts = Checkout::with(['booking.room', 'booking.guest'])
            ->where('status', 'draft')
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('finance.dashboard.index', compact(
            'revenueByModule', 'revenueByMethod', 'dailyRevenue',
            'todaySummary', 'outstandingTotal', 'recentTransactions',
            'dateFrom', 'dateTo', 'ordersMissingCharges', 'laundryMissingCharges', 'unpaidChargesByBooking',
            'draftCheckouts'
        ));
    }
}
