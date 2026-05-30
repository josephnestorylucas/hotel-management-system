<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\StockLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuCategoryController extends Controller
{
    public function index(): View
    {
        $categories = MenuCategory::with('location')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        return view('restaurant.menu.categories.index', compact('categories', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        MenuCategory::create([
            'name' => $request->name,
            'location_id' => $request->location_id,
            'description' => $request->description,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('general.restaurant.messages.category_created'));
    }

    public function update(Request $request, MenuCategory $menuCategory): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $menuCategory->update([
            'name' => $request->name,
            'location_id' => $request->location_id,
            'description' => $request->description,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('general.restaurant.messages.category_updated'));
    }

    public function destroy(MenuCategory $menuCategory): RedirectResponse
    {
        $menuCategory->update(['is_active' => false]);
        $this->softDelete($menuCategory);

        return back()->with('success', __('general.restaurant.messages.category_archived'));
    }
}

