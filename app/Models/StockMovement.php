<?php

namespace App\Models;

use App\Services\NotificationService;
use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    use HasUuid, HasSoftDelete;

    public $timestamps = false;

    protected $fillable = [
        'product_id', 'location_id', 'type', 'quantity',
        'quantity_before', 'quantity_after', 'unit_cost',
        'reference_type', 'reference_id', 'notes',
        'approved_by', 'created_by', 'created_at',
    ];

    protected $casts = [
        'quantity'        => 'decimal:3',
        'quantity_before' => 'decimal:3',
        'quantity_after'  => 'decimal:3',
        'unit_cost'       => 'decimal:2',
        'created_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    /**
     * THE CORE METHOD — every stock change goes through here.
     *
     * Wraps in DB::transaction with lockForUpdate().
     * Aborts 422 if stock would go negative.
     * Writes immutable before/after snapshot.
     */
    public static function record(array $params, string $actorId): self
    {
        return DB::transaction(function () use ($params, $actorId) {

            $level = StockLevel::where('product_id', $params['product_id'])
                               ->where('location_id', $params['location_id'])
                               ->lockForUpdate()
                               ->firstOrFail();

            $before = (float) $level->quantity;

            $increaseTypes = ['restock', 'transfer_in'];
            $decreaseTypes = ['sale', 'internal_use', 'damage', 'transfer_out', 'recipe_use'];

            if (in_array($params['type'], $increaseTypes)) {
                $after = $before + (float) $params['quantity'];
            } elseif (in_array($params['type'], $decreaseTypes)) {
                $after = $before - (float) $params['quantity'];
            } else {
                // adjustment — caller provides the exact target quantity
                $after              = (float) $params['new_quantity'];
                $params['quantity'] = abs($after - $before);
            }

            // HARD RULE: Stock can never go negative
            if ($after < 0) {
                abort(422, "Insufficient stock. Available: {$before}, Requested: {$params['quantity']}");
            }

            $level->update(['quantity' => $after, 'updated_at' => now()]);

            $movement = self::create([
                'product_id'      => $params['product_id'],
                'location_id'     => $params['location_id'],
                'type'            => $params['type'],
                'quantity'        => $params['quantity'],
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'unit_cost'       => $params['unit_cost'] ?? null,
                'reference_type'  => $params['reference_type'] ?? null,
                'reference_id'    => $params['reference_id'] ?? null,
                'notes'           => $params['notes'] ?? null,
                'approved_by'     => $params['approved_by'] ?? null,
                'created_by'      => $actorId,
                'created_at'      => now(),
            ]);

            // Fire low-stock notification if we just crossed below the reorder level
            $product = $level->product;
            if ($after <= $product->reorder_level && $before > $product->reorder_level) {
                self::sendLowStockAlert($product, $level->location, $after);
            }

            return $movement;
        });
    }

    private static function sendLowStockAlert(Product $product, StockLocation $location, float $qty): void
    {
        $notificationService = app(NotificationService::class);
        
        $managerIds = User::whereHas('role', fn ($q) => $q->where('name', 'store_manager'))
            ->pluck('id')
            ->toArray();

        $notificationService->createForUsers($managerIds, [
            'type'           => 'low_stock',
            'title'          => 'Low Stock Alert',
            'body'           => "{$product->name} at {$location->name} is low: {$qty} {$product->unit} remaining.",
            'reference_type' => 'product',
            'reference_id'   => $product->id,
            'action_url'     => route('store.products.show', $product->id),
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
