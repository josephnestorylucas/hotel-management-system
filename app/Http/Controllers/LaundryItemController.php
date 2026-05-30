<?php

namespace App\Http\Controllers;

use App\Models\LaundryItem;
use Illuminate\Http\Request;

class LaundryItemController extends Controller
{
    public function index()
    {
        $items = LaundryItem::orderBy('name')->paginate(20);
        return view('laundry-items.index', compact('items'));
    }

    public function create()
    {
        return view('laundry-items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        LaundryItem::create($validated);

        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item created successfully.');
    }

    public function edit(LaundryItem $laundryItem)
    {
        return view('laundry-items.edit', compact('laundryItem'));
    }

    public function update(Request $request, LaundryItem $laundryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $laundryItem->update($validated);

        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item updated successfully.');
    }

    public function destroy(LaundryItem $laundryItem)
    {
        $this->softDelete($laundryItem);

        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item archived successfully.');
    }

    public function archived()
    {
        $records = LaundryItem::onlyDeleted()->latest('deleted_at')->paginate(20);
        return view('laundry-items.archived', compact('records'));
    }

    public function restore(LaundryItem $laundryItem)
    {
        $this->restoreModel($laundryItem);
        return redirect()->route('laundry-items.index')->with('success', 'Laundry item restored successfully.');
    }
}
