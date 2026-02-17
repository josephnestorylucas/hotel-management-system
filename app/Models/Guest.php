<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Guest extends Model implements HasMedia
{
    use HasUuid, InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'id_number',
        'address',
        'nationality',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Register media collections for the guest.
     * - guest_photo: Single file collection for profile photo
     * - id_documents: Multiple files collection for identification documents
     */
    public function registerMediaCollections(): void
    {
        // Single profile photo collection
        $this->addMediaCollection('guest_photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        // Multiple ID documents collection
        $this->addMediaCollection('id_documents')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);
    }

    /**
     * Register media conversions for automatic thumbnail generation.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Thumbnail conversion for profile photos
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('guest_photo')
            ->nonQueued();

        // Medium size conversion for profile photos
        $this->addMediaConversion('medium')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('guest_photo')
            ->nonQueued();

        // Thumbnail for ID documents (images only)
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->performOnCollections('id_documents')
            ->nonQueued();
    }

    /**
     * Get the full name of the guest.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the reservations for the guest.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the bookings for the guest.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }

    /**
     * Get the guest's photo URL using Spatie Media Library.
     * Returns the original photo URL or null if not set.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('guest_photo') ?: null;
    }

    /**
     * Get the guest's photo thumbnail URL.
     * Returns the thumbnail conversion URL or null if not set.
     */
    public function getPhotoThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('guest_photo', 'thumb') ?: null;
    }

    /**
     * Get the guest's photo medium URL.
     * Returns the medium conversion URL or null if not set.
     */
    public function getPhotoMediumUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('guest_photo', 'medium') ?: null;
    }

    /**
     * Check if guest has a photo.
     */
    public function hasPhoto(): bool
    {
        return $this->hasMedia('guest_photo');
    }

    /**
     * Get the first ID document URL (for backward compatibility).
     * Returns the first document URL or null if not set.
     */
    public function getIdDocumentUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('id_documents') ?: null;
    }

    /**
     * Get the first ID document thumbnail URL.
     */
    public function getIdDocumentThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('id_documents', 'thumb') ?: null;
    }

    /**
     * Get all ID documents.
     * Returns a collection of media items.
     */
    public function getIdDocumentsAttribute()
    {
        return $this->getMedia('id_documents');
    }

    /**
     * Check if guest has ID documents.
     */
    public function hasIdDocuments(): bool
    {
        return $this->hasMedia('id_documents');
    }

    /**
     * Get count of ID documents.
     */
    public function getIdDocumentsCountAttribute(): int
    {
        return $this->getMedia('id_documents')->count();
    }

    public function conferenceBookings(): HasMany
    {
        return $this->hasMany(ConferenceBooking::class);
    }

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    public function conferenceParticipations(): HasMany
    {
        return $this->hasMany(ConferenceParticipant::class);
    }
}
