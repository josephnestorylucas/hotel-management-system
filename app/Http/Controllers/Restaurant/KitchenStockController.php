<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\KitchenStockItem;
use App\Models\KitchenStockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KitchenStockController extends Controller
{
    public function index(): View
    {
        $items = KitchenStockItem::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return $item;
            });

        return view('manager.kitchen-stock.index', compact('items'));
    }

    public function create(): View
    {
        return view('manager.kitchen-stock.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:50',
            'current_quantity' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|numeric|min:0',
        ]);

        KitchenStockItem::create($data);

        return redirect()->route('manager.kitchen-stock.index')
            ->with('success', 'Stock item created.');
    }

    public function show(KitchenStockItem $item): View
    {
        $movements = $item->movements()->with('recordedBy')->paginate(25);

        return view('manager.kitchen-stock.show', compact('item', 'movements'));
    }

    public function edit(KitchenStockItem $item): View
    {
        return view('manager.kitchen-stock.edit', compact('item'));
    }

    public function update(Request $request, KitchenStockItem $item): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:50',
            'current_quantity' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|numeric|min:0',
        ]);

        $item->update($data);

        return redirect()->route('manager.kitchen-stock.index')
            ->with('success', 'Stock item updated.');
    }

    public function destroy(KitchenStockItem $item): RedirectResponse
    {
        $item->update(['is_active' => false]);
        $this->softDelete($item);

        return redirect()->route('manager.kitchen-stock.index')
            ->with('success', 'Stock item deleted.');
    }

    public function archived(): View
    {
        $records = KitchenStockItem::onlyDeleted()->latest('deleted_at')->paginate(20);
        return view('manager.kitchen-stock.archived', compact('records'));
    }

    public function restore(KitchenStockItem $item): RedirectResponse
    {
        $item->update(['is_active' => true]);
        $this->restoreModel($item);
        return redirect()->route('manager.kitchen-stock.index')->with('success', 'Stock item restored successfully.');
    }

    public function recordMovement(Request $request, KitchenStockItem $item): RedirectResponse
    {
        $data = $request->validate([
            'movement_type' => 'required|in:purchase,damage,transfer,adjustment',
            'quantity' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        $change = in_array($data['movement_type'], ['damage', 'transfer'])
            ? -abs((float) $data['quantity'])
            : abs((float) $data['quantity']);

        if ($data['movement_type'] === 'adjustment') {
            $newQuantity = (float) $data['quantity'];
            $change = $newQuantity - $item->current_quantity;
        }

        $item->increment('current_quantity', $change);

        KitchenStockMovement::create([
            'kitchen_stock_item_id' => $item->id,
            'movement_type' => $data['movement_type'],
            'quantity' => $change,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => (string) Auth::id(),
        ]);

        return redirect()->route('manager.kitchen-stock.show', $item)
            ->with('success', 'Stock movement recorded.');
    }
}
