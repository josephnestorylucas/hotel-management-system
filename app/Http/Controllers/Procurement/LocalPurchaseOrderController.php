<?php
// app/Http/Controllers/Procurement/LocalPurchaseOrderController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LocalPurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $lpos = LocalPurchaseOrder::with(['supplier', 'creator', 'approver'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('procurement.lpo.index', compact('lpos'));
    }

    public function create(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('procurement.lpo.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'supplier_name_manual' => 'nullable|string|max:200',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|uuid|exists:products,id',
            'items.*.item_name' => 'required|string|max:200',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        $lpo = DB::transaction(function () use ($validated) {
            $lpo = LocalPurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name_manual' => $validated['supplier_name_manual'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                LocalPurchaseOrderItem::create([
                    'lpo_id' => $lpo->id,
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $lpo->load('items');
            $lpo->recalculate();

            return $lpo;
        });

        return redirect()
            ->route('procurement.lpo.show', $lpo)
            ->with('success', "LPO {$lpo->lpo_number} created successfully.");
    }

    public function show(LocalPurchaseOrder $localPurchaseOrder): View
    {
        $localPurchaseOrder->load(['supplier', 'items.product', 'creator', 'approver', 'goodsReceivedNotes']);

        return view('procurement.lpo.show', compact('localPurchaseOrder'));
    }

    public function edit(LocalPurchaseOrder $localPurchaseOrder): View
    {
        if (!in_array($localPurchaseOrder->status, ['draft', 'rejected'])) {
            abort(403, 'Cannot edit LPO in current status.');
        }

        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $localPurchaseOrder->load('items.product');

        return view('procurement.lpo.edit', compact('localPurchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if (!in_array($localPurchaseOrder->status, ['draft', 'rejected'])) {
            abort(403, 'Cannot edit LPO in current status.');
        }

        $validated = $request->validate([
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'supplier_name_manual' => 'nullable|string|max:200',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|uuid|exists:products,id',
            'items.*.item_name' => 'required|string|max:200',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($localPurchaseOrder, $validated) {
            $localPurchaseOrder->update([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name_manual' => $validated['supplier_name_manual'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            // Delete old items and create new ones
            $localPurchaseOrder->items()->delete();

            foreach ($validated['items'] as $item) {
                LocalPurchaseOrderItem::create([
                    'lpo_id' => $localPurchaseOrder->id,
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $localPurchaseOrder->load('items');
            $localPurchaseOrder->recalculate();
        });

        return redirect()
            ->route('procurement.lpo.show', $localPurchaseOrder)
            ->with('success', "LPO {$localPurchaseOrder->lpo_number} updated successfully.");
    }

    public function submitForApproval(LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if ($localPurchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft LPOs can be submitted for approval.');
        }

        $localPurchaseOrder->update(['status' => 'pending_approval']);

        return back()->with('success', 'LPO submitted for approval.');
    }

    public function approve(LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if ($localPurchaseOrder->status !== 'pending_approval') {
            return back()->with('error', 'LPO is not pending approval.');
        }

        $localPurchaseOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "LPO {$localPurchaseOrder->lpo_number} approved successfully.");
    }

    public function reject(Request $request, LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if ($localPurchaseOrder->status !== 'pending_approval') {
            return back()->with('error', 'LPO is not pending approval.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $localPurchaseOrder->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'LPO rejected.');
    }

    public function markSent(LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if ($localPurchaseOrder->status !== 'approved') {
            return back()->with('error', 'Only approved LPOs can be marked as sent.');
        }

        $localPurchaseOrder->update(['status' => 'sent']);

        return back()->with('success', "LPO {$localPurchaseOrder->lpo_number} marked as sent to supplier.");
    }

    public function destroy(LocalPurchaseOrder $localPurchaseOrder): RedirectResponse
    {
        if (!in_array($localPurchaseOrder->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Cannot delete LPO in current status.');
        }

        $number = $localPurchaseOrder->lpo_number;
        $localPurchaseOrder->delete();

        return redirect()
            ->route('procurement.lpo.index')
            ->with('success', "LPO {$number} deleted successfully.");
    }
}