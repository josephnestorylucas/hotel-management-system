<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaundryReportController extends Controller
{
    // GET /laundry/reports/daily
    public function daily(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $orders = LaundryOrder::with(['items.serviceItem.service', 'receiver', 'settler'])
            ->where('status', 'settled')
            ->whereDate('settled_at', $date)
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total'),
            'guest_revenue'  => $orders->where('customer_type', 'guest')->sum('total'),
            'walkin_revenue' => $orders->where('customer_type', 'walkin')->sum('total'),
            'cash'           => $orders->where('payment_method', 'cash')->sum('total'),
            'card'           => $orders->where('payment_method', 'card')->sum('total'),
            'charged'        => $orders->where('payment_method', 'charge_to_booking')->sum('total'),
        ];

        $overdueOrders = LaundryOrder::whereNotIn('status', ['settled', 'cancelled', 'ready', 'delivered', 'collected'])
            ->where('expected_ready_at', '<', now())
            ->with('items')
            ->get();

        return view('laundry.reports.daily', compact('orders', 'summary', 'date', 'overdueOrders'));
    }
}
