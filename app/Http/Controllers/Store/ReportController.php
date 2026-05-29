<?php

namespace App\Http\Controllers\Store;

use App\Exports\StockMovementsExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // GET /store/reports/stock-snapshot
    public function stockSnapshot(Request $request): View
    {
        $user = auth()->user();
        $isRestaurantManager = $user->hasRole('restaurant_manager');

        if ($isRestaurantManager) {
            $barLocation = StockLocation::bar();
            $locations = collect([$barLocation]);
        } else {
            $locations = StockLocation::where('is_active', true)->orderBy('name')->get();
        }

        $categories = Product::query()
            ->where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $levelsQuery = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true);

        $summaryQuery = StockLevel::query()
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true);

        if ($isRestaurantManager) {
            $levelsQuery->where('stock_levels.location_id', $barLocation->id)
                        ->where('products.product_type', 'bar');
            $summaryQuery->where('stock_levels.location_id', $barLocation->id)
                         ->where('products.product_type', 'bar');
        } else {
            $levelsQuery->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id));
            $summaryQuery->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id));
        }

        $levelsQuery->when($request->category, fn ($q) => $q->where('products.category', $request->category));
        $summaryQuery->when($request->category, fn ($q) => $q->where('products.category', $request->category));

        $levels = $levelsQuery->select('stock_levels.*')->paginate(30);

        $totalProducts = (clone $summaryQuery)->count();
        $lowStockCount = (clone $summaryQuery)
            ->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level')
            ->where('stock_levels.quantity', '>', 0)
            ->count();
        $outOfStockCount = (clone $summaryQuery)
            ->where('stock_levels.quantity', '<=', 0)
            ->count();

        return view('store.reports.stock-snapshot', compact(
            'levels',
            'locations',
            'categories',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    // GET /store/reports/movements
    public function movements(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();

        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->when($request->product_id,  fn ($q) => $q->where('product_id', $request->product_id))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->type,        fn ($q) => $q->where('type', $request->type))
            ->when($request->from,        fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,          fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('created_at')
            ->paginate(50);

        return view('store.reports.movements', compact('movements', 'locations'));
    }

    // GET /store/reports/movements/print
    public function movementsPrint(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();

        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->when($request->product_id,  fn ($q) => $q->where('product_id', $request->product_id))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->type,        fn ($q) => $q->where('type', $request->type))
            ->when($request->from,        fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,          fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('created_at')
            ->get();

        $filterSummary = [];
        if ($request->from) $filterSummary['from'] = $request->from;
        if ($request->to) $filterSummary['to'] = $request->to;
        if ($request->type) $filterSummary['type'] = ucwords(str_replace('_', ' ', $request->type));
        if ($request->location_id) {
            $filterSummary['location'] = $locations->firstWhere('id', $request->location_id)?->name ?? '';
        }

        return view('store.reports.movements-print', compact('movements', 'filterSummary'));
    }

    // GET /store/reports/movements/export/excel
    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date'  => $request->input('from'),
            'end_date'    => $request->input('to'),
            'type'        => $request->input('type'),
            'location_id' => $request->input('location_id'),
        ];

        $filename = 'stock-movements-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new StockMovementsExport($filters), $filename);
    }

    // GET /store/reports/damage
    public function damage(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();

        $damages = StockMovement::with(['product', 'location', 'actor'])
            ->where('type', 'damage')
            ->when($request->from,        fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,          fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->latest('created_at')
            ->paginate(30);

        $summaryQuery = StockMovement::query()
            ->with('product')
            ->where('type', 'damage')
            ->when($request->from,        fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,          fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id));

        $totalDamageCount = (clone $summaryQuery)->count();
        $totalDamageCost = (clone $summaryQuery)
            ->get()
            ->sum(fn ($damage) => (float) $damage->quantity * (float) ($damage->product->cost_price ?? 0));

        return view('store.reports.damage', compact(
            'damages',
            'locations',
            'totalDamageCount',
            'totalDamageCost'
        ));
    }
}
