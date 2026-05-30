<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasSoftDelete;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BuffetPackage extends Model implements HasMedia
{
    use HasUuid, HasSoftDelete, InteractsWithMedia;

    protected $fillable = [
        'name',
        'adult_price',
        'child_price',
        'available_days',
        'start_time',
        'end_time',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'available_days' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('buffet_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('buffet_image')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(400)
            ->height(250)
            ->sharpen(10)
            ->performOnCollections('buffet_image')
            ->nonQueued();
    }

    public function getImageAttribute(): string
    {
        if ($this->hasMedia('buffet_image')) {
            return $this->getFirstMediaUrl('buffet_image', 'medium')
                ?: $this->getFirstMediaUrl('buffet_image');
        }
        return '';
    }

    public function getThumbAttribute(): string
    {
        if ($this->hasMedia('buffet_image')) {
            return $this->getFirstMediaUrl('buffet_image', 'thumb')
                ?: $this->getFirstMediaUrl('buffet_image');
        }
        return '';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function buffetSales(): HasMany
    {
        return $this->hasMany(BuffetSale::class, 'buffet_package_id');
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'buffet_package_menu_item');
    }
}

