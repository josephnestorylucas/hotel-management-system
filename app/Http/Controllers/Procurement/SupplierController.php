<?php
// app/Http/Controllers/Procurement/SupplierController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount(['purchaseOrders', 'goodsReceivedNotes'])
            ->orderBy('name')
            ->paginate(20);

        return view('procurement.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('procurement.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'tin_number' => 'nullable|string|max:50',
            'vrn_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        $supplier = Supplier::create($validated);

        return redirect()
            ->route('procurement.suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' created successfully.");
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchaseOrders' => function ($query) {
            $query->latest()->take(10);
        }, 'goodsReceivedNotes' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('procurement.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('procurement.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'tin_number' => 'nullable|string|max:50',
            'vrn_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('procurement.suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    public function archived(): View
    {
        $suppliers = Supplier::onlyDeleted()->latest('deleted_at')->paginate(20);

        return view('procurement.suppliers.archived', compact('suppliers'));
    }

    public function restore(Supplier $supplier): RedirectResponse
    {
        $this->restoreModel($supplier);

        return redirect()->route('procurement.suppliers.index')->with('success', 'Supplier restored successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchaseOrders()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with existing purchase orders.');
        }

        $name = $supplier->name;
        $this->softDelete($supplier);

        return redirect()
            ->route('procurement.suppliers.index')
            ->with('success', "Supplier '{$name}' deleted successfully.");
    }
}