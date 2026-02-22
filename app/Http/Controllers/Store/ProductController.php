<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    // GET /store/products
    public function index(Request $request): View
    {
        $products = Product::with('stockLevels.location')
            ->where('is_active', true)
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate(20);

        return view('store.products.index', compact('products'));
    }

    // GET /store/products/create
    public function create(): View
    {
        return view('store.products.create');
    }

    // POST /store/products
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'sku'           => 'nullable|string|max:50|unique:products,sku',
            'description'   => 'nullable|string',
            'category'      => 'nullable|string|max:100',
            'unit'          => 'required|string|max:30',
            'cost_price'    => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        if (empty($data['sku'])) {
            $prefix      = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['name']), 0, 3));
            $data['sku'] = $prefix . '-' . strtoupper(substr(uniqid(), -6));
        }

        $data['created_by'] = auth()->id();

        // Product::booted() auto-creates stock_levels for all active locations
        $product = Product::create($data);

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', "Product '{$product->name}' created successfully.");
    }

    // GET /store/products/{product}
    public function show(Product $product): View
    {
        $product->load('stockLevels.location', 'createdBy');

        $recentMovements = StockMovement::where('product_id', $product->id)
            ->with('location', 'actor')
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('store.products.show', compact('product', 'recentMovements'));
    }

    // GET /store/products/{product}/edit
    public function edit(Product $product): View
    {
        return view('store.products.edit', compact('product'));
    }

    // PUT /store/products/{product}
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|string|max:150',
            'description'   => 'sometimes|nullable|string',
            'category'      => 'sometimes|nullable|string|max:100',
            'unit'          => 'sometimes|string|max:30',
            'cost_price'    => 'sometimes|numeric|min:0.01',
            'selling_price' => 'sometimes|numeric|min:0.01',
            'reorder_level' => 'sometimes|integer|min:0',
        ]);

        $product->update($data);

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    // DELETE /store/products/{product} — soft deactivate only
    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return redirect()
            ->route('store.products.index')
            ->with('success', "Product '{$product->name}' deactivated.");
    }
}
