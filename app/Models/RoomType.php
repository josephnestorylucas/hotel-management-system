<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RoomType extends Model implements HasMedia
{
    use HasUuid, InteractsWithMedia;

    protected $fillable = ['name', 'code', 'base_rate', 'currency', 'max_occupancy', 'description'];
    
    protected $casts = [
        'base_rate' => 'decimal:2',
    ];

    protected $appends = ['price_per_night', 'formatted_rate'];

    /**
     * Register media collections for the room type.
     * - room_type_image: Single main image for the room type
     * - room_type_gallery: Multiple additional images
     */
    public function registerMediaCollections(): void
    {
        // Single main image collection
        $this->addMediaCollection('room_type_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);

        // Multiple gallery images collection
        $this->addMediaCollection('room_type_gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
    }

    /**
     * Register media conversions for automatic thumbnail generation.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Thumbnail conversion for main image
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('room_type_image', 'room_type_gallery')
            ->nonQueued();

        // Medium size conversion for main image
        $this->addMediaConversion('medium')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('room_type_image', 'room_type_gallery')
            ->nonQueued();

        // Large size conversion for gallery display
        $this->addMediaConversion('large')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('room_type_image', 'room_type_gallery')
            ->nonQueued();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get the price per night (alias for base_rate), converted to system currency.
     * Provides compatibility with views referencing price_per_night.
     */
    public function getPricePerNightAttribute(): float
    {
        $storedCurrency = $this->currency ?? 'USD';
        $systemCurrency = CurrencyHelper::getDefaultCurrency();

        if ($storedCurrency === $systemCurrency) {
            return (float) $this->base_rate;
        }

        return CurrencyHelper::convert((float) $this->base_rate, $storedCurrency, $systemCurrency);
    }

    /**
     * Get the base rate formatted in the system default currency,
     * converting from the stored currency if needed.
     */
    public function getFormattedRateAttribute(): string
    {
        $storedCurrency = $this->currency ?? 'USD';
        $systemCurrency = CurrencyHelper::getDefaultCurrency();
        $amount = (float) $this->base_rate;

        if ($storedCurrency === $systemCurrency) {
            return CurrencyHelper::formatCurrency($amount, $systemCurrency);
        }

        $converted = CurrencyHelper::convert($amount, $storedCurrency, $systemCurrency);

        return CurrencyHelper::formatCurrency($converted, $systemCurrency);
    }

    /**
     * Get the main image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('room_type_image') ?: null;
    }

    /**
     * Get the main image thumbnail URL.
     */
    public function getImageThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('room_type_image', 'thumb') ?: null;
    }

    /**
     * Get the main image medium URL.
     */
    public function getImageMediumUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('room_type_image', 'medium') ?: null;
    }

    /**
     * Get the main image large URL.
     */
    public function getImageLargeUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('room_type_image', 'large') ?: null;
    }

    /**
     * Check if room type has a main image.
     */
    public function hasImage(): bool
    {
        return $this->hasMedia('room_type_image');
    }

    /**
     * Get the image URL with fallback to default placeholder.
     */
    public function getImageWithFallbackAttribute(): string
    {
        if ($this->hasImage()) {
            return $this->getFirstMediaUrl('room_type_image');
        }
        return asset('images/room-placeholder.svg');
    }

    /**
     * Get the medium image URL with fallback to default placeholder.
     */
    public function getMediumImageWithFallbackAttribute(): string
    {
        if ($this->hasImage()) {
            return $this->getFirstMediaUrl('room_type_image', 'medium') ?: $this->getFirstMediaUrl('room_type_image');
        }
        return asset('images/room-placeholder.svg');
    }

    /**
     * Get the thumbnail image URL with fallback to default placeholder.
     */
    public function getThumbImageWithFallbackAttribute(): string
    {
        if ($this->hasImage()) {
            return $this->getFirstMediaUrl('room_type_image', 'thumb') ?: $this->getFirstMediaUrl('room_type_image');
        }
        return asset('images/room-placeholder.svg');
    }

    /**
     * Get all gallery images.
     */
    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('room_type_gallery');
    }

    /**
     * Check if room type has gallery images.
     */
    public function hasGalleryImages(): bool
    {
        return $this->hasMedia('room_type_gallery');
    }

    /**
     * Get count of gallery images.
     */
    public function getGalleryImagesCountAttribute(): int
    {
        return $this->getMedia('room_type_gallery')->count();
    }
}