<?php
// app/Http/Controllers/Procurement/GoodsReceivedNoteController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\LocalPurchaseOrder;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ProcurementIntegrationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GoodsReceivedNoteController extends Controller
{
    public function index(Request $request): View
    {
        $grns = GoodsReceivedNote::with(['lpo', 'supplier', 'receiver', 'confirmer', 'accountingEntry', 'items.stockMovement'])
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
                'status' => GoodsReceivedNote::STATUS_DRAFT,
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
            'items.stockMovement',
            'receiver',
            'confirmer',
            'accountingEntry'
            ,'approver'
            ,'rejector'
        ]);

        return view('procurement.grn.show', compact('goodsReceivedNote'));
    }

    public function print(GoodsReceivedNote $goodsReceivedNote): View
    {
        $goodsReceivedNote->load(['supplier', 'lpo', 'items.product', 'receiver', 'confirmer', 'approver']);

        return view('procurement.grn.print', compact('goodsReceivedNote'));
    }

    public function edit(GoodsReceivedNote $goodsReceivedNote): View
    {
        abort_unless(auth()->user()?->hasRole(Role::STORE_KEEPER), 403);

        if (! in_array($goodsReceivedNote->status, [
            GoodsReceivedNote::STATUS_DRAFT,
            GoodsReceivedNote::STATUS_REJECTED,
        ], true)) {
            return redirect()
                ->route('procurement.grn.show', $goodsReceivedNote)
                ->with('error', 'Only draft or returned GRNs can be edited.');
        }

        $goodsReceivedNote->load([
            'lpo',
            'supplier',
            'items.product',
            'items.lpoItem',
        ]);

        return view('procurement.grn.edit', compact('goodsReceivedNote'));
    }

    public function update(Request $request, GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        abort_unless(auth()->user()?->hasRole(Role::STORE_KEEPER), 403);

        if (! in_array($goodsReceivedNote->status, [
            GoodsReceivedNote::STATUS_DRAFT,
            GoodsReceivedNote::STATUS_REJECTED,
        ], true)) {
            return redirect()
                ->route('procurement.grn.show', $goodsReceivedNote)
                ->with('error', 'Only draft or returned GRNs can be updated.');
        }

        $validated = $request->validate([
            'received_date' => 'required|date',
            'delivery_vehicle' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|uuid|exists:goods_received_note_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($goodsReceivedNote, $validated) {
            $existingItemIds = $goodsReceivedNote->items()->pluck('id')->sort()->values()->all();
            $submittedItemIds = collect($validated['items'])->pluck('id')->sort()->values()->all();

            if ($existingItemIds !== $submittedItemIds) {
                throw ValidationException::withMessages([
                    'items' => 'Submitted GRN items do not match the current GRN.',
                ]);
            }

            $goodsReceivedNote->update([
                'received_date' => $validated['received_date'],
                'delivery_vehicle' => $validated['delivery_vehicle'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = GoodsReceivedNoteItem::findOrFail($itemData['id']);

                $qty = (float) $itemData['quantity_received'];
                $price = (float) $itemData['unit_price'];

                $item->update([
                    'quantity_received' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $qty * $price,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $goodsReceivedNote->load('items');
            $goodsReceivedNote->recalculate();
        });

        if ($request->hasFile('receipt')) {
            if ($goodsReceivedNote->receipt_path) {
                Storage::disk('public')->delete($goodsReceivedNote->receipt_path);
            }

            $path = $request->file('receipt')->store('grn-receipts', 'public');
            $goodsReceivedNote->update(['receipt_path' => $path]);
        }

        return redirect()
            ->route('procurement.grn.show', $goodsReceivedNote)
            ->with('success', "GRN {$goodsReceivedNote->grn_number} updated successfully.");
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
        if (! in_array($goodsReceivedNote->status, [
            GoodsReceivedNote::STATUS_DRAFT,
            GoodsReceivedNote::STATUS_REJECTED,
        ], true)) {
            return back()->with('error', 'Only draft or returned GRNs can be submitted for confirmation.');
        }

        $goodsReceivedNote->update([
            'status' => GoodsReceivedNote::STATUS_SUBMITTED,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'confirmed_by' => null,
            'confirmed_at' => null,
        ]);

        return back()->with('success', 'GRN submitted for confirmation.');
    }

    public function confirm(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        abort_unless(auth()->user()?->hasRole(Role::STORE_KEEPER), 403);

        try {
            app(ProcurementIntegrationService::class)->confirmGrn($goodsReceivedNote, (string) auth()->id());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', "GRN {$goodsReceivedNote->grn_number} confirmed and submitted for manager approval.");
    }

    public function approve(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        abort_unless(auth()->user()?->hasRole(Role::MANAGER), 403);

        if ($goodsReceivedNote->status === GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER) {
            $goodsReceivedNote->update(['status' => GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL]);
            $goodsReceivedNote->refresh();
        }

        try {
            app(ProcurementIntegrationService::class)->approveGrn($goodsReceivedNote, (string) auth()->id());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', "GRN {$goodsReceivedNote->grn_number} approved. Stock and accounting updates completed.");
    }

    public function reject(Request $request, GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        abort_unless(auth()->user()?->hasRole(Role::MANAGER), 403);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        try {
            app(ProcurementIntegrationService::class)->rejectGrn(
                $goodsReceivedNote,
                (string) auth()->id(),
                $validated['rejection_reason']
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', 'GRN rejected.');
    }

    public function destroy(GoodsReceivedNote $goodsReceivedNote): RedirectResponse
    {
        if ($goodsReceivedNote->status === GoodsReceivedNote::STATUS_APPROVED) {
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
