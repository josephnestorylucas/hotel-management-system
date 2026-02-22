<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    // GET /store/stock/levels
    public function levels(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->get();

        $levels = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->when($request->search, fn ($q) => $q->where('products.name', 'like', '%' . $request->search . '%'))
            ->select('stock_levels.*')
            ->paginate(30);

        return view('store.stock.levels', compact('levels', 'locations'));
    }

    // GET /store/stock/restock
    public function restockForm(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.stock.restock', compact('products', 'locations'));
    }

    // POST /store/stock/restock
    public function restock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|uuid|exists:products,id',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'quantity'    => 'required|numeric|min:0.001',
            'unit_cost'   => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($data['product_id']);
        abort_if(! $product->is_active, 422, 'Cannot restock an inactive product.');

        StockMovement::record([
            'product_id'  => $data['product_id'],
            'location_id' => $data['location_id'],
            'type'        => 'restock',
            'quantity'    => $data['quantity'],
            'unit_cost'   => $data['unit_cost'] ?? null,
            'notes'       => $data['notes'] ?? null,
        ], auth()->id());

        return redirect()
            ->route('store.stock.levels')
            ->with('success', "Restocked {$data['quantity']} {$product->unit} of {$product->name}.");
    }

    // GET /store/stock/damage
    public function damageForm(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.stock.damage', compact('products', 'locations'));
    }

    // POST /store/stock/damage
    public function damage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|uuid|exists:products,id',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'quantity'    => 'required|numeric|min:0.001',
            'reason'      => 'required|string|max:255',
            'notes'       => 'nullable|string|max:500',
        ]);

        // BAR_MANAGER and KITCHEN_MANAGER are scoped to their own location
        $this->assertLocationAccess(auth()->user(), $data['location_id']);

        $product = Product::findOrFail($data['product_id']);

        StockMovement::record([
            'product_id'  => $data['product_id'],
            'location_id' => $data['location_id'],
            'type'        => 'damage',
            'quantity'    => $data['quantity'],
            'notes'       => $data['reason'] . ($data['notes'] ? ' | ' . $data['notes'] : ''),
        ], auth()->id());

        return redirect()
            ->route('store.stock.levels')
            ->with('success', "Damage of {$data['quantity']} {$product->unit} recorded for {$product->name}.");
    }

    private function assertLocationAccess($user, string $locationId): void
    {
        $role     = $user->role->name;
        $location = StockLocation::findOrFail($locationId);

        if ($role === 'bar_manager' && $location->code !== 'bar') {
            abort(403, 'BAR_MANAGER can only record damage at the bar location.');
        }

        if ($role === 'kitchen_manager' && $location->code !== 'kitchen') {
            abort(403, 'KITCHEN_MANAGER can only record damage at the kitchen location.');
        }
    }
}
