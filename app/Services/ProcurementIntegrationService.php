<?php

namespace App\Services;

use App\Models\GoodsReceivedNote;
use App\Models\LocalPurchaseOrder;
use App\Models\StockLocation;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProcurementIntegrationService
{
    public function approveLpo(LocalPurchaseOrder $lpo, string $actorId): LocalPurchaseOrder
    {
        if ($lpo->status !== 'pending_approval') {
            throw ValidationException::withMessages([
                'status' => 'LPO is not pending approval.',
            ]);
        }

        return DB::transaction(function () use ($lpo, $actorId) {
            $lpo->update([
                'status' => 'approved',
                'approved_by' => $actorId,
                'approved_at' => now(),
            ]);

            return $lpo->fresh(['items', 'supplier']);
        });
    }

    public function confirmGrn(GoodsReceivedNote $grn, string $actorId): GoodsReceivedNote
    {
        if ($grn->status !== GoodsReceivedNote::STATUS_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'GRN is not ready for storekeeper confirmation.',
            ]);
        }

        return DB::transaction(function () use ($grn, $actorId) {
            $grn = GoodsReceivedNote::lockForUpdate()->findOrFail($grn->id);

            $grn->update([
                'status' => GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                'confirmed_by' => $actorId,
                'confirmed_at' => now(),
            ]);

            return $grn->fresh();
        });
    }

    public function approveGrn(GoodsReceivedNote $grn, string $actorId): GoodsReceivedNote
    {
        if (! in_array($grn->status, [
            GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
            GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
        ], true)) {
            throw ValidationException::withMessages([
                'status' => 'GRN is not pending manager approval.',
            ]);
        }

        try {
            return DB::transaction(function () use ($grn, $actorId) {
                $grn = GoodsReceivedNote::with(['items.product', 'items.lpoItem', 'lpo.items'])
                    ->lockForUpdate()
                    ->findOrFail($grn->id);

                if ($grn->status === GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER) {
                    $grn->update(['status' => GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL]);
                    $grn->refresh();
                }

                foreach ($grn->items as $item) {
                    if (! $item->product_id) {
                        continue;
                    }

                    $incoming = (float) $item->quantity_received;

                    if ($incoming <= 0) {
                        if ($lpoItem = $item->lpoItem) {
                            $lpoItem->update([
                                'received_quantity' => round(((float) $lpoItem->received_quantity) + $incoming, 3),
                            ]);
                        }
                        continue;
                    }

                    $lpoItem = $item->lpoItem;
                    if ($lpoItem) {
                        $alreadyReceived = (float) $lpoItem->received_quantity;
                        $ordered = (float) $lpoItem->quantity;

                        if (($alreadyReceived + $incoming) > $ordered + 0.0001) {
                            throw ValidationException::withMessages([
                                'items' => "Received quantity for {$item->item_name} exceeds the ordered quantity.",
                            ]);
                        }
                    }

                    if (! $item->stock_movement_id) {
                        $movement = StockMovement::record([
                            'product_id' => $item->product_id,
                            'location_id' => StockLocation::mainStore()->id,
                            'type' => 'restock',
                            'quantity' => $item->quantity_received,
                            'unit_cost' => $item->unit_price,
                            'reference_type' => 'grn_item',
                            'reference_id' => $item->id,
                            'notes' => "GRN {$grn->grn_number} from {$grn->supplierName}",
                            'approved_by' => $actorId,
                        ], $actorId);

                        $item->update(['stock_movement_id' => $movement->id]);
                    }

                    if ($lpoItem) {
                        $lpoItem->update([
                            'received_quantity' => round(((float) $lpoItem->received_quantity) + ((float) $item->quantity_received), 3),
                        ]);
                    }
                }

                if (! $grn->accounting_journal_entry_id) {
                    $entry = app(AccountingService::class)->postGrnConfirmation(
                        $grn->grn_number,
                        $grn->id,
                        $grn->supplier_id,
                        (float) $grn->subtotal,
                        (float) $grn->tax_amount,
                        $actorId,
                    );

                    $grn->accounting_journal_entry_id = $entry->id;
                }

                app(SupplierPayablesService::class)->ensurePayableFromGrn($grn, $actorId);

                $grn->status = GoodsReceivedNote::STATUS_APPROVED;
                $grn->approved_by = $actorId;
                $grn->approved_at = now();
                $grn->rejected_by = null;
                $grn->rejected_at = null;
                $grn->rejection_reason = null;
                $grn->save();

                $this->syncLpoReceivingStatus($grn->lpo->fresh('items'));

                return $grn->fresh(['items.lpoItem', 'lpo.items']);
            });
        } catch (Throwable $e) {
            Log::error('Procurement integration failed during GRN approval', [
                'grn_id' => $grn->id,
                'grn_number' => $grn->grn_number,
                'actor_id' => $actorId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function rejectGrn(GoodsReceivedNote $grn, string $actorId, string $reason): GoodsReceivedNote
    {
        if (! in_array($grn->status, [
            GoodsReceivedNote::STATUS_SUBMITTED,
            GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
            GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
        ], true)) {
            throw ValidationException::withMessages([
                'status' => 'GRN cannot be rejected in its current status.',
            ]);
        }

        return DB::transaction(function () use ($grn, $actorId, $reason) {
            $grn = GoodsReceivedNote::lockForUpdate()->findOrFail($grn->id);

            $grn->update([
                'status' => GoodsReceivedNote::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'rejected_by' => $actorId,
                'rejected_at' => now(),
            ]);

            return $grn->fresh();
        });
    }

    public function syncLpoReceivingStatus(LocalPurchaseOrder $lpo): void
    {
        $items = $lpo->items;

        if ($items->isEmpty()) {
            return;
        }

        $allReceived = $items->every(fn ($item) => (float) $item->received_quantity >= (float) $item->quantity);
        $anyReceived = $items->contains(fn ($item) => (float) $item->received_quantity > 0);

        $status = $allReceived ? 'fully_received' : ($anyReceived ? 'partially_received' : ($lpo->status === 'approved' ? 'approved' : $lpo->status));

        if ($lpo->status !== $status) {
            $lpo->update(['status' => $status]);
        }
    }
}
