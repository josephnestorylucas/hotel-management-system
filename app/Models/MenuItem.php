<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MenuItem extends Model implements HasMedia
{
    use HasUuid, InteractsWithMedia;

    protected $fillable = [
        'category_id', 'name', 'description',
        'selling_price', 'is_available', 'service_location_tag', 'destination',
        'is_buffet', 'available_from', 'available_until',
        'is_active', 'created_by', 'varieties',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_available'  => 'boolean',
        'is_active'     => 'boolean',
        'is_buffet'     => 'boolean',
        'varieties'     => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('menu_item_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('menu_item_image')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('menu_item_image')
            ->nonQueued();
    }

    public function getImageAttribute(): string
    {
        if ($this->hasMedia('menu_item_image')) {
            return $this->getFirstMediaUrl('menu_item_image', 'medium')
                ?: $this->getFirstMediaUrl('menu_item_image');
        }
        return '';
    }

    public function getThumbAttribute(): string
    {
        if ($this->hasMedia('menu_item_image')) {
            return $this->getFirstMediaUrl('menu_item_image', 'thumb')
                ?: $this->getFirstMediaUrl('menu_item_image');
        }
        return '';
    }

    public function category()    { return $this->belongsTo(MenuCategory::class, 'category_id'); }
    public function ingredients() { return $this->hasMany(MenuItemIngredient::class); }
    public function optionGroups()
    {
        return $this->belongsToMany(MenuOptionGroup::class, 'menu_item_option_group')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeAvailableNow($query)
    {
        return $query->where('is_active', true)
            ->where('is_available', true)
            ->where(function ($q) {
                $q->whereNull('available_from')
                  ->orWhere('available_from', '<=', now()->format('H:i:s'));
            })
            ->where(function ($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', now()->format('H:i:s'));
            });
    }

    public function scopeForKitchen($query)
    {
        return $query->where('destination', 'kitchen');
    }

    public function scopeForBar($query)
    {
        return $query->where('destination', 'bar');
    }

    public function scopeExcludeBuffet($query)
    {
        return $query->where('is_buffet', false);
    }

    /**
     * Check if all ingredients have enough stock in their location.
     */
    public function hasEnoughStock(int $qty = 1): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $locationId = $this->category->location_id;
            $level = StockLevel::where('product_id', $ingredient->product_id)
                               ->where('location_id', $locationId)
                               ->first();

            if (!$level || $level->available_qty < ($ingredient->quantity * $qty)) {
                return false;
            }
        }
        return true;
    }
}
