<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'sku', 'description', 'category', 'product_type', 'unit',
        'cost_price', 'selling_price', 'reorder_level', 'varieties', 'is_active', 'created_by',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'reorder_level' => 'integer',
        'varieties'     => 'array',
        'product_type'  => 'string',
    ];

    /**
     * Auto-create a stock_levels row for every active location when product is created.
     */
    protected static function booted(): void
    {
        static::created(function (Product $product) {
            StockLocation::where('is_active', true)->get()->each(function ($location) use ($product) {
                StockLevel::firstOrCreate(
                    ['product_id' => $product->id, 'location_id' => $location->id],
                    ['quantity' => 0, 'reserved_qty' => 0]
                );
            });
        });
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function menuItem()
    {
        return $this->hasOne(MenuItem::class, 'name', 'name')
            ->where('is_active', true);
    }
}
