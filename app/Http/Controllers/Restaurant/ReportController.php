<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BuffetSale;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * GET /restaurant/reports/daily-sales
     */
    public function dailySales(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $orders = Order::with(['items.menuItem', 'location', 'table', 'creator'])
            ->where(function ($q) {
                $q->where('status', 'settled')
                  ->orWhere('status', 'charged');
            })
            ->where(function ($q) use ($date) {
                $q->whereDate('settled_at', $date)
                  ->orWhere(function ($sq) use ($date) {
                      $sq->where('status', 'charged')
                         ->whereDate('billed_to_folio_at', $date);
                  });
            })
            ->latest('settled_at')
            ->get();

        $summary = [
            'total_orders'    => $orders->count(),
            'total_revenue'   => $orders->sum('total'),
            'total_subtotal'  => $orders->sum('subtotal'),
            'total_tax'       => $orders->sum('tax'),
            'total_items_qty' => $orders->sum(fn($o) => $o->items->sum('quantity')),
            'cash_revenue'    => $orders->where('payment_method', 'cash')->sum('total'),
            'card_revenue'    => $orders->where('payment_method', 'card')->sum('total'),
            'mobile_revenue'  => $orders->whereIn('payment_method', ['mobile', 'mobile_money'])->sum('total'),
            'guest_charges'   => $orders->where('payment_method', 'charge_to_booking')->sum('total'),
        ];

        // Sales by category
        $salesByCategory = OrderItem::with('menuItem.category')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['settled', 'charged'])
            ->where('order_items.status', '!=', 'cancelled')
            ->where(function ($q) use ($date) {
                $q->whereDate('orders.settled_at', $date)
                  ->orWhere(function ($sq) use ($date) {
                      $sq->where('orders.status', 'charged')
                         ->whereDate('orders.billed_to_folio_at', $date);
                  });
            })
            ->select([
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            ])
            ->groupBy('order_items.menu_item_id')
            ->get()
            ->groupBy(fn($item) => $item->menuItem->category->name ?? 'Uncategorized')
            ->map(fn($items) => [
                'quantity' => $items->sum('total_qty'),
                'revenue'  => $items->sum('total_revenue'),
            ])
            ->sortByDesc('revenue');

        // Buffet sales for the day
        $buffetSales = BuffetSale::with('package')
            ->where(function ($q) use ($date) {
                $q->whereDate('settled_at', $date)
                  ->orWhere(function ($sq) use ($date) {
                      $sq->where('status', 'charged')
                         ->whereDate('created_at', $date);
                  });
            })
            ->get();

        $buffetSummary = [
            'count'      => $buffetSales->count(),
            'adults'     => $buffetSales->sum('adults_count'),
            'children'   => $buffetSales->sum('children_count'),
            'revenue'    => $buffetSales->sum('total_amount'),
        ];

        return view('restaurant.reports.daily-sales', compact('orders', 'summary', 'date', 'salesByCategory', 'buffetSummary'));
    }

    /**
     * GET /restaurant/reports/popular-items
     */
    public function popularItems(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        $items = OrderItem::with('menuItem.category')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['settled', 'charged'])
            ->where('order_items.status', '!=', 'cancelled')
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereDate('orders.settled_at', '>=', $dateFrom)
                  ->whereDate('orders.settled_at', '<=', $dateTo)
                  ->orWhere(function ($sq) use ($dateFrom, $dateTo) {
                      $sq->where('orders.status', 'charged')
                         ->whereDate('orders.billed_to_folio_at', '>=', $dateFrom)
                         ->whereDate('orders.billed_to_folio_at', '<=', $dateTo);
                  });
            })
            ->select([
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            ])
            ->groupBy('order_items.menu_item_id')
            ->orderByDesc('total_qty')
            ->take(20)
            ->get();

        return view('restaurant.reports.popular-items', compact('items', 'dateFrom', 'dateTo'));
    }
}
