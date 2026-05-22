<?php
// app/Models/Conference.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    use HasUuid;

    protected $fillable = [
        'conference_booking_id',
        'institution_id',
        'guest_id',
        'title',
        'description',
        'conference_fee',
        'hall_name',
        'venue_notes',
        'start_datetime',
        'end_datetime',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'conference_fee' => 'decimal:2',
    ];

    public function conferenceBooking(): BelongsTo
    {
        return $this->belongsTo(ConferenceBooking::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConferenceParticipant::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['scheduled', 'ongoing']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'ongoing']);
    }

    public function getDisplayVenueAttribute(): string
    {
        if ($this->hall_name) {
            return $this->hall_name;
        }
        if ($this->conferenceBooking && $this->conferenceBooking->conferenceHall) {
            return $this->conferenceBooking->conferenceHall->name;
        }
        return 'TBA';
    }

    public function getNextPassNumber(): int
    {
        return ($this->participants()->max('pass_number') ?? 0) + 1;
    }
}
