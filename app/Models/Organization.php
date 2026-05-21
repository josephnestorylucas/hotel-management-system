<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Organization extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'type',
        'registration_number',
        'tax_id',
        'address',
        'city',
        'country',
        'postal_code',
        'email',
        'phone',
        'website',
        'logo_path',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone',
        'status',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function conferenceBookings(): HasMany
    {
        return $this->hasMany(ConferenceBooking::class);
    }

    public function eventStaff(): HasManyThrough
    {
        return $this->hasManyThrough(EventStaff::class, Event::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function verify(): void
    {
        $this->update(['verified_at' => now()]);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->country, $this->postal_code])
            ->filter()
            ->implode(', ');
    }
}
