<?php
// app/Http/Controllers/Procurement/GoodsReceivedNoteController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\LocalPurchaseOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\AccountingService;

class GoodsReceivedNoteController extends Controller
{
    public function index(Request $request): View
    {
        $grns = GoodsReceivedNote::with(['lpo', 'supplier', 'receiver', 'confirmer'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('procurement.grn.index', compact('grns'));
    }

    public function create(Request $request): View
    {
        $lpos = LocalPurchaseOrder::whereIn('status', ['sent', 'approved', 'partially_received'])
            ->with(['items.product', 'supplier'])
            ->orderBy('order_date', 'desc')
            ->get();

        $selectedLpo = null;
        if ($request->lpo_id) {
            $selectedLpo = LocalPurchaseOrder::with(['items.product', 'supplier'])
                ->findOrFail($request->lpo_id);
        }

        return view('procurement.grn.create', compact('lpos', 'selectedLpo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lpo_id' => 'required|uuid|exists:local_purchase_orders,id',
            'received_date' => 'required|date',
            'delivery_vehicle' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.lpo_item_id' => 'required|uuid|exists:local_purchase_order_items,id',
            'items.*.product_id' => 'nullable|uuid|exists:products,id',
            'items.*.item_name' => 'required|string|max:200',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity_ordered' => 'required|numeric|min:0',
            'items.*.quantity_received' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        $lpo = LocalPurchaseOrder::findOrFail($validated['lpo_id']);

        $grn = DB::transaction(function () use ($validated, $lpo) {
            $grn = GoodsReceivedNote::create([
                'lpo_id' => $validated['lpo_id'],
                'supplier_id' => $lpo->supplier_id,
                'supplier_name_manual' => $lpo->supplier_name_manual,
                'received_date' => $validated['received_date'],
                'delivery_vehicle' => $validated['delivery_vehicle'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'received_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                GoodsReceivedNoteItem::create([
                    'grn_id' => $grn->id,
                    'lpo_item_id' => $item['lpo_item_id'],
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'unit' => $item['unit'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => $item['quantity_received'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity_received'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $grn->load('items');
            $grn->recalculate();

            return $grn;
        });

        return redirect()
            ->route('procurement.grn.show', $grn)
            ->with('success', "GRN {$grn->grn_number} created successfully.");
    }

    public function show(GoodsReceivedNote $goodsReceivedNote): View
    {
        $goodsReceivedNote->load([
            'lpo.items',
            'supplier',
            'items.product',
            'items.lpoItem',
            'receiver',
            'confirmer'
        ]);

        return view('procurement.grn.show', compact('goodsReceivedNote'));
    }

    public function uploadReceipt(Request $request, GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        $request->validate([
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Delete old receipt if exists
        if ($goodsReceivedNote->receipt_path) {
            Storage::disk('public')->delete($goodsReceivedNote->receipt_path);
        }

        $path = $request->file('receipt')->store('grn-receipts', 'public');
        $goodsReceivedNote->update(['receipt_path' => $path]);

        return back()->with('success', 'Receipt uploaded successfully.');
    }

    public function submitForConfirmation(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        if ($goodsReceivedNote->status !== 'draft') {
            return back()->with('error', 'Only draft GRNs can be submitted for confirmation.');
        }

        $goodsReceivedNote->update(['status' => 'pending_confirmation']);

        return back()->with('success', 'GRN submitted for confirmation.');
    }

    public function confirm(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        if ($goodsReceivedNote->status !== 'pending_confirmation') {
            return back()->with('error', 'GRN is not pending confirmation.');
        }

        DB::transaction(function () use ($goodsReceivedNote) {
            $goodsReceivedNote->update([
                'status' => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            // CRITICAL: Push goods into stock
            $goodsReceivedNote->pushToStock(auth()->id());

            // Post to accounting journal
            app(AccountingService::class)->postGrnConfirmation(
                grnNo: $goodsReceivedNote->grn_number,
                grnId: $goodsReceivedNote->id,
                netAmount: (float) $goodsReceivedNote->subtotal,
                vatAmount: (float) $goodsReceivedNote->tax_amount,
                actorId: auth()->id()
            );
        });

        return back()->with('success', "GRN {$goodsReceivedNote->grn_number} confirmed. Stock levels updated successfully.");
    }

    public function reject(Request $request, GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        if ($goodsReceivedNote->status !== 'pending_confirmation') {
            return back()->with('error', 'GRN is not pending confirmation.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $goodsReceivedNote->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'confirmed_by' => auth()->id(),
        ]);

        return back()->with('success', 'GRN rejected.');
    }

    public function destroy(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        if ($goodsReceivedNote->status === 'confirmed') {
            return back()->with('error', 'Cannot delete confirmed GRN.');
        }

        // Delete receipt file if exists
        if ($goodsReceivedNote->receipt_path) {
            Storage::disk('public')->delete($goodsReceivedNote->receipt_path);
        }

        $number = $goodsReceivedNote->grn_number;
        $goodsReceivedNote->delete();

        return redirect()
            ->route('procurement.grn.index')
            ->with('success', "GRN {$number} deleted successfully.");
    }
}