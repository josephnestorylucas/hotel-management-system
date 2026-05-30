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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            'barcode'          => 'nullable|string|max:50|unique:products,barcode',
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
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_url'        => 'nullable|url|max:500',
        ]);

        if (empty($data['sku'])) {
            $prefix      = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['name']), 0, 3));
            $data['sku'] = $prefix . '-' . strtoupper(Str::random(6));
        }

        $data['created_by'] = auth()->id();
        $varieties = $this->parseVarieties($request->input('varieties'));

        $product = DB::transaction(function () use ($data, $varieties, $request) {
            unset($data['image_file']);

            $product = Product::create([
                'name'          => $data['name'],
                'barcode'       => $data['barcode'] ?? null,
                'sku'           => $data['sku'],
                'description'   => $data['description'] ?? null,
                'category'      => $data['category'] ?? null,
                'product_type'  => $data['product_type'] ?? null,
                'unit'          => $data['unit'],
                'cost_price'    => $data['cost_price'],
                'selling_price' => $data['selling_price'],
                'reorder_level' => $data['reorder_level'] ?? 0,
                'varieties'     => $varieties,
                'image_url'     => $data['image_url'] ?? null,
                'is_active'     => true,
                'created_by'    => $data['created_by'],
            ]);

            if ($request->hasFile('image_file')) {
                $product->addMediaFromRequest('image_file')
                    ->toMediaCollection('product_image');
            }

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
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_url'        => 'nullable|url|max:500',
            'remove_image'     => 'nullable|boolean',
        ]);

        $varieties = $request->has('varieties')
            ? $this->parseVarieties($request->input('varieties'))
            : $product->varieties;

        DB::transaction(function () use ($product, $data, $varieties, $request) {
            $updateData = [
                'name'          => $data['name'] ?? $product->name,
                'description'   => $data['description'] ?? $product->description,
                'category'      => $data['category'] ?? $product->category,
                'product_type'  => $data['product_type'] ?? $product->product_type,
                'unit'          => $data['unit'] ?? $product->unit,
                'cost_price'    => $data['cost_price'] ?? $product->cost_price,
                'selling_price' => $data['selling_price'] ?? $product->selling_price,
                'reorder_level' => $data['reorder_level'] ?? $product->reorder_level,
                'varieties'     => $varieties,
            ];

            if ($request->hasFile('image_file')) {
                $product->addMediaFromRequest('image_file')
                    ->toMediaCollection('product_image');
                $updateData['image_url'] = null;
            } elseif ($request->has('remove_image') && $request->boolean('remove_image')) {
                $product->clearMediaCollection('product_image');
                $updateData['image_url'] = null;
            } elseif ($request->has('image_url')) {
                $updateData['image_url'] = !empty($data['image_url']) ? $data['image_url'] : null;
            }

            $product->update($updateData);

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
            $this->softDelete($product);

            $menuItem = $product->menuItem;
            if ($menuItem) {
                $menuItem->update(['is_active' => false]);
                $this->softDelete($menuItem);
            }
        });

        return redirect()
            ->route('store.products.index')
            ->with('success', "Product '{$product->name}' deleted.");
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

    public function archived()
    {
        $products = Product::onlyDeleted()->with('stockLevels.location')->latest('deleted_at')->paginate(20);

        return view('store.products.archived', compact('products'));
    }

    public function restore(Product $product)
    {
        $this->restoreModel($product);

        return redirect()->route('store.products.index')->with('success', 'Product restored successfully.');
    }

    public function lookupByBarcode(Request $request)
    {
        try {
            $request->validate(['barcode' => 'required|string|max:50']);

            $product = Product::findByBarcode($request->barcode);

            if ($product) {
                $product->load('stockLevels.location');

                return response()->json([
                    'found'  => true,
                    'source' => 'local',
                    'product' => [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'barcode'       => $product->barcode,
                        'sku'           => $product->sku,
                        'unit'          => $product->unit,
                        'cost_price'    => $product->cost_price,
                        'selling_price' => $product->selling_price,
                        'stock'         => $product->stockLevels->sum('quantity'),
                    ],
                ]);
            }

            $response = Http::timeout(5)
                ->get("https://world.openfoodfacts.org/api/v0/product/{$request->barcode}.json");

            if ($response->successful() && $response->json('status') === 1) {
                $data = $response->json('product');

                return response()->json([
                    'found'  => true,
                    'source' => 'openfoodfacts',
                    'product' => [
                        'name'   => data_get($data, 'product_name', ''),
                        'brand'  => data_get($data, 'brands', ''),
                        'barcode' => $request->barcode,
                        'image'  => data_get($data, 'image_small_url', ''),
                    ],
                ]);
            }

            return response()->json([
                'found'   => false,
                'source'  => null,
                'barcode' => $request->barcode,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'found' => false,
                'error' => true,
            ], 500);
        }
    }

    public function storeScanned(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'barcode'       => 'required|string|max:50|unique:products,barcode',
            'cost_price'    => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01',
            'quantity'      => 'nullable|numeric|min:0',
        ]);

        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['name']), 0, 3));
        $sku = $prefix . '-' . strtoupper(Str::random(6));

        $product = Product::create([
            'name'          => $data['name'],
            'barcode'       => $data['barcode'],
            'sku'           => $sku,
            'unit'          => 'piece',
            'cost_price'    => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'reorder_level' => 0,
            'is_active'     => true,
            'created_by'    => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
}
