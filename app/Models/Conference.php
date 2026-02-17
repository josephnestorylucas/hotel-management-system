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
        'guest_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function conferenceBooking(): BelongsTo
    {
        return $this->belongsTo(ConferenceBooking::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id');
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
}