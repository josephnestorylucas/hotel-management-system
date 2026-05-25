<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
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
        'id_type',
        'address',
        'nationality',
        'date_of_birth',
        'loyalty_points',
        'loyalty_tier',
        'tier_upgraded_at',
        'total_stays',
        'total_spent',
        'organization_id',
        'total_events_attended',
        'event_preferences',
        'communication_preferences',
    ];

    protected $casts = [
        'date_of_birth'    => 'date',
        'total_spent'      => 'decimal:2',
        'tier_upgraded_at' => 'datetime',
        'event_preferences' => 'array',
        'communication_preferences' => 'array',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // ═══ LOYALTY PROGRAM ═══

    /**
     * Get all loyalty transactions for this guest.
     */
    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    /**
     * Add loyalty points, update tier, fire notifications if tier changes.
     */
    public function addPoints(int $points, string $source, ?string $referenceId = null, ?string $actorId = null): void
    {
        $oldTier = $this->loyalty_tier ?? 'none';

        $this->increment('loyalty_points', $points);
        $this->refresh();

        $newTier = $this->calculateTier($this->loyalty_points);

        // Update tier if changed
        if ($newTier !== $oldTier) {
            $this->update([
                'loyalty_tier'     => $newTier,
                'tier_upgraded_at' => now(),
            ]);

            // Send upgrade notifications
            $this->sendTierUpgradeNotifications($newTier);
        }

        // Log the transaction
        LoyaltyTransaction::create([
            'guest_id'      => $this->id,
            'type'          => 'earn',
            'points'        => $points,
            'balance_after' => $this->loyalty_points,
            'source'        => $source,
            'reference_id'  => $referenceId,
            'created_by'    => $actorId ?? (auth()->check() ? auth()->id() : null),
            'created_at'    => now(),
        ]);
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemPoints(int $points, string $source, ?string $referenceId = null, ?string $notes = null): bool
    {
        if ($this->loyalty_points < $points) {
            return false;
        }

        $this->decrement('loyalty_points', $points);
        $this->refresh();

        LoyaltyTransaction::create([
            'guest_id'      => $this->id,
            'type'          => 'redeem',
            'points'        => -$points,
            'balance_after' => $this->loyalty_points,
            'source'        => $source,
            'reference_id'  => $referenceId,
            'notes'         => $notes,
            'created_by'    => auth()->check() ? auth()->id() : null,
            'created_at'    => now(),
        ]);

        return true;
    }

    /**
     * Calculate the loyalty tier based on points.
     */
    public function calculateTier(int $points): string
    {
        if ($points >= 5000) return 'Platinum';
        if ($points >= 2000) return 'Gold';
        if ($points >= 500)  return 'Silver';
        return 'none';
    }

    /**
     * Get the discount percentage based on loyalty tier.
     */
    public function getLoyaltyDiscountPercent(): float
    {
        return match ($this->loyalty_tier) {
            'Silver'   => 5.0,
            'Gold'     => 10.0,
            'Platinum' => 15.0,
            default    => 0.0,
        };
    }

    /**
     * Send tier upgrade notifications (email + SMS).
     */
    private function sendTierUpgradeNotifications(string $tier): void
    {
        $data = [
            'guest_name'   => $this->full_name,
            'tier'         => $tier,
            'points'       => $this->loyalty_points,
            'member_since' => $this->created_at->format('d M Y'),
        ];

        // Email
        if ($this->email) {
            Mail::to($this->email)->queue(new \App\Mail\LoyaltyUpgradeMail($data));
        }

        // SMS
        if ($this->phone_number) {
            \App\Jobs\SendSmsJob::dispatch(
                $this->phone_number,
                "Grand Hotel: Congratulations {$this->full_name}! You've reached {$tier} Member status. Thank you for your loyalty!"
            )->onQueue('notifications');
        }
    }
}
