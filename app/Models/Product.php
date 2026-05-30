<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasUuid, HasSoftDelete, InteractsWithMedia;

    protected $fillable = [
        'name', 'barcode', 'sku', 'description', 'category', 'product_type', 'unit',
        'cost_price', 'selling_price', 'reorder_level', 'varieties', 'image_url', 'is_active', 'created_by',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'reorder_level' => 'integer',
        'varieties'     => 'array',
        'product_type'  => 'string',
        'deleted_at'    => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('product_image')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('product_image')
            ->nonQueued();
    }

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

    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }

        return $this->getFirstMediaUrl('product_image') ?: null;
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        if (!empty($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }

        return $this->getFirstMediaUrl('product_image', 'thumb') ?: null;
    }

    public function getImageMediumUrlAttribute(): ?string
    {
        if (!empty($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }

        return $this->getFirstMediaUrl('product_image', 'medium') ?: null;
    }

    public function hasImage(): bool
    {
        if (!empty($this->attributes['image_url'])) {
            return true;
        }

        return $this->hasMedia('product_image');
    }

    public function hasLocalImage(): bool
    {
        return $this->hasMedia('product_image');
    }

    public function getIsCdnImageAttribute(): bool
    {
        return !empty($this->attributes['image_url']);
    }

    public function getImageWithFallbackAttribute(): string
    {
        if ($this->hasImage()) {
            return $this->image_thumb_url ?? $this->image_url;
        }

        return asset('images/product-placeholder.svg');
    }

    public function getMediumImageWithFallbackAttribute(): string
    {
        if ($this->hasImage()) {
            return $this->image_medium_url ?? $this->image_url;
        }

        return asset('images/product-placeholder.svg');
    }

    public function menuItem()
    {
        return $this->hasOne(MenuItem::class, 'name', 'name')
            ->where('is_active', true);
    }

    public static function findByBarcode(string $barcode): ?self
    {
        return static::where('barcode', $barcode)->first();
    }
}
