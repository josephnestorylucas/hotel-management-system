<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InternalUsageRequest;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    // GET /store/reports/stock-snapshot
    public function stockSnapshot(Request $request): View
    {
        $levels = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->select('stock_levels.*')
            ->paginate(30);

        return view('store.reports.stock-snapshot', compact('levels'));
    }

    // GET /store/reports/movements
    public function movements(Request $request): View
    {
        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->when($request->product_id,  fn ($q) => $q->where('product_id', $request->product_id))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->type,        fn ($q) => $q->where('type', $request->type))
            ->when($request->date_from,   fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,     fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(50);

        return view('store.reports.movements', compact('movements'));
    }

    // GET /store/reports/damage
    public function damage(Request $request): View
    {
        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->where('type', 'damage')
            ->when($request->date_from,   fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,     fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->latest('created_at')
            ->paginate(30);

        return view('store.reports.damage', compact('movements'));
    }
}
