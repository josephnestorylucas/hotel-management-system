<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasUuid;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'id_number',
        'id_document',
        'address',
        'nationality',
        'date_of_birth',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

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
     * Get the guest's photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    /**
     * Get the guest's ID document URL.
     */
    public function getIdDocumentUrlAttribute(): ?string
    {
        if ($this->id_document) {
            return asset('storage/' . $this->id_document);
        }
        return null;
    }
}
