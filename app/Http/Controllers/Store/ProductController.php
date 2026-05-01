<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('stockLevels.location')
            ->where('is_active', true)
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->product_type, fn ($q) => $q->where('product_type', $request->product_type))
            ->latest()
            ->paginate(20);

        return view('store.products.index', compact('products'));
    }

    public function create(): View
    {
        $bar = StockLocation::bar();
        $menuCategories = MenuCategory::query()
            ->where('location_id', $bar->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('store.products.create', compact('menuCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:150',
            'sku'              => 'nullable|string|max:50|unique:products,sku',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'product_type'     => 'nullable|in:normal,bar',
            'menu_category_id' => 'required_if:product_type,bar|nullable|uuid|exists:menu_categories,id',
            'unit'             => 'required|string|max:30',
            'cost_price'       => 'required|numeric|min:0.01',
            'selling_price'    => 'required|numeric|min:0.01',
            'reorder_level'    => 'nullable|integer|min:0',
            'varieties'        => 'nullable|json',
        ]);

        if (empty($data['sku'])) {
            $prefix      = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['name']), 0, 3));
            $data['sku'] = $prefix . '-' . strtoupper(substr(uniqid(), -6));
        }

        $data['created_by'] = auth()->id();
        $varieties = $this->parseVarieties($request->input('varieties'));

        $product = DB::transaction(function () use ($data, $varieties) {
            $product = Product::create([
                'name'          => $data['name'],
                'sku'           => $data['sku'],
                'description'   => $data['description'] ?? null,
                'category'      => $data['category'] ?? null,
                'product_type'  => $data['product_type'] ?? null,
                'unit'          => $data['unit'],
                'cost_price'    => $data['cost_price'],
                'selling_price' => $data['selling_price'],
                'reorder_level' => $data['reorder_level'] ?? 0,
                'varieties'     => $varieties,
                'is_active'     => true,
                'created_by'    => $data['created_by'],
            ]);

            // Auto-create MenuItem for Bar products so they appear in Bar POS
            if ($data['product_type'] === 'bar' && !empty($data['menu_category_id'])) {
                MenuItem::create([
                    'category_id'   => $data['menu_category_id'],
                    'name'          => $data['name'],
                    'description'   => $data['description'] ?? null,
                    'selling_price' => $data['selling_price'],
                    'is_available'  => true,
                    'is_active'     => true,
                    'varieties'     => $varieties,
                    'created_by'    => $data['created_by'],
                ]);
            }

            return $product;
        });

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', "Product '{$product->name}' created successfully.");
    }

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

    public function edit(Product $product): View
    {
        $bar = StockLocation::bar();
        $menuCategories = MenuCategory::query()
            ->where('location_id', $bar->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('store.products.edit', compact('product', 'menuCategories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'sometimes|string|max:150',
            'description'      => 'sometimes|nullable|string',
            'category'         => 'sometimes|nullable|string|max:100',
            'product_type'     => 'sometimes|nullable|in:normal,bar',
            'menu_category_id' => 'required_if:product_type,bar|nullable|uuid|exists:menu_categories,id',
            'unit'             => 'sometimes|string|max:30',
            'cost_price'       => 'sometimes|numeric|min:0.01',
            'selling_price'    => 'sometimes|numeric|min:0.01',
            'reorder_level'    => 'sometimes|integer|min:0',
            'varieties'        => 'nullable|json',
        ]);

        $varieties = $request->has('varieties')
            ? $this->parseVarieties($request->input('varieties'))
            : $product->varieties;

        DB::transaction(function () use ($product, $data, $varieties) {
            $product->update([
                'name'          => $data['name'] ?? $product->name,
                'description'   => $data['description'] ?? $product->description,
                'category'      => $data['category'] ?? $product->category,
                'product_type'  => $data['product_type'] ?? $product->product_type,
                'unit'          => $data['unit'] ?? $product->unit,
                'cost_price'    => $data['cost_price'] ?? $product->cost_price,
                'selling_price' => $data['selling_price'] ?? $product->selling_price,
                'reorder_level' => $data['reorder_level'] ?? $product->reorder_level,
                'varieties'     => $varieties,
            ]);

            $menuItem = $product->menuItem;

            if ($data['product_type'] === 'bar' && !empty($data['menu_category_id'])) {
                if ($menuItem) {
                    $menuItem->update([
                        'category_id'   => $data['menu_category_id'],
                        'name'          => $product->name,
                        'description'   => $product->description,
                        'selling_price' => $product->selling_price,
                        'varieties'     => $product->varieties,
                        'is_active'     => true,
                    ]);
                } else {
                    MenuItem::create([
                        'category_id'   => $data['menu_category_id'],
                        'name'          => $product->name,
                        'description'   => $product->description ?? null,
                        'selling_price' => $product->selling_price,
                        'is_available'  => true,
                        'is_active'     => true,
                        'varieties'     => $product->varieties ?: null,
                        'created_by'    => $product->created_by,
                    ]);
                }
            } elseif ($menuItem && $data['product_type'] === 'normal') {
                // If switched from Bar to Normal, deactivate the MenuItem
                $menuItem->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            $product->update(['is_active' => false]);

            $menuItem = $product->menuItem;
            if ($menuItem) {
                $menuItem->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('store.products.index')
            ->with('success', "Product '{$product->name}' deactivated.");
    }

    protected function parseVarieties(?string $json): ?array
    {
        if (blank($json)) {
            return null;
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            return null;
        }

        return array_values(array_filter($decoded, fn ($v) => !empty($v['label'])));
    }
}
