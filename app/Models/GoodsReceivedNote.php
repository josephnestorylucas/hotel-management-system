<?php
// app/Models/GoodsReceivedNote.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class GoodsReceivedNote extends Model
{
    use HasUuid;

    protected $fillable = [
        'grn_number',
        'lpo_id',
        'supplier_id',
        'supplier_name_manual',
        'received_date',
        'delivery_vehicle',
        'driver_name',
        'subtotal',
        'tax_amount',
        'grand_total',
        'notes',
        'receipt_path',
        'status',
        'rejection_reason',
        'received_by',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'received_date' => 'date',
        'confirmed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (GoodsReceivedNote $grn) {
            if (empty($grn->grn_number)) {
                $count = self::whereDate('created_at', today())->count() + 1;
                $grn->grn_number = 'GRN-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = round($subtotal * 0.18, 2);
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'grand_total' => $subtotal + $tax,
        ]);
    }

    /**
     * CRITICAL: Push received goods into stock via StockMovement::record()
     * This is the bridge between Procurement and Store modules.
     */
    public function pushToStock(string $actorId): void
    {
        DB::transaction(function () use ($actorId) {
            foreach ($this->items as $item) {
                if (!$item->product_id) {
                    continue; // Skip items not linked to inventory
                }

                StockMovement::record([
                    'product_id' => $item->product_id,
                    'location_id' => StockLocation::mainStore()->id,
                    'type' => 'restock',
                    'quantity' => $item->quantity_received,
                    'unit_cost' => $item->unit_price,
                    'reference_type' => 'grn',
                    'reference_id' => $this->id,
                    'notes' => "GRN {$this->grn_number} from {$this->supplierName}",
                    'approved_by' => $actorId,
                ], $actorId);
            }

            // Update LPO status
            $this->lpo->update(['status' => 'fully_received']);
        });
    }

    public function lpo(): BelongsTo
    {
        return $this->belongsTo(LocalPurchaseOrder::class, 'lpo_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'grn_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getSupplierNameAttribute(): string
    {
        return $this->supplier?->name ?? $this->supplier_name_manual ?? 'N/A';
    }
}