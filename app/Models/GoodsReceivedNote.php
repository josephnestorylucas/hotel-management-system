<?php
// app/Models/GoodsReceivedNote.php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class GoodsReceivedNote extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_CONFIRMED_BY_STOREKEEPER = 'confirmed_by_storekeeper';
    public const STATUS_PENDING_MANAGER_APPROVAL = 'pending_manager_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

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
        'accounting_journal_entry_id',
        'status',
        'rejection_reason',
        'received_by',
        'confirmed_by',
        'confirmed_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'received_date' => 'date',
        'confirmed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'deleted_at' => 'datetime',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function accountingEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }

    public function getSupplierNameAttribute(): string
    {
        return $this->supplier?->name ?? $this->supplier_name_manual ?? 'N/A';
    }

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    public function toReceiptData(): array
    {
        $this->loadMissing(['items.product', 'supplier', 'lpo', 'receiver']);

        $items = $this->items->map(function ($item) {
            return [
                'name'       => $item->product?->name ?? $item->item_name ?? 'Item',
                'details'    => $item->description ?? '',
                'quantity'   => $item->quantity_received ?? 1,
                'unit_price' => (float) ($item->unit_price ?? 0),
                'amount'     => (float) ($item->subtotal ?? 0),
            ];
        })->toArray();

        return [
            'receipt_no'            => $this->grn_number,
            'issued_at'             => $this->received_date ? $this->received_date->startOfDay() : $this->created_at,
            'module'                => 'procurement',
            'customer_name'         => $this->supplier_name,
            'customer_phone'        => $this->supplier?->phone ?? null,
            'items'                 => $items,
            'subtotal'              => (float) $this->subtotal,
            'discount'              => 0.0,
            'tax'                   => (float) $this->tax_amount,
            'total'                 => (float) $this->grand_total,
            'amount_paid'           => $this->isPaid() ? (float) $this->grand_total : 0.0,
            'balance'               => $this->isPaid() ? 0.0 : (float) $this->grand_total,
            'currency'              => 'TZS',
            'payment_method'        => null,
            'payment_status'        => $this->isPaid() ? 'paid' : 'unpaid',
            'transaction_reference' => $this->lpo?->lpo_number ?? null,
            'cashier'               => $this->receiver?->name,
            'notes'                 => $this->notes,
        ];
    }

    public function getReceiptModule(): string
    {
        return 'procurement';
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
