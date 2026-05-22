<?php
// app/Models/ConferenceParticipant.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConferenceParticipant extends Model
{
    use HasUuid;

    const ROLE_ATTENDEE = 'attendee';
    const ROLE_SPEAKER = 'speaker';
    const ROLE_ORGANIZER = 'organizer';

    protected $fillable = [
        'conference_id',
        'guest_id',
        'name',
        'email',
        'phone',
        'role',
        'rsvp_status',
        'access_token',
        'access_code',
        'pass_number',
        'pass_type',
        'checked_in_count',
        'last_check_in_at',
    ];

    protected $casts = [
        'last_check_in_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->access_token)) {
                $participant->access_token = Str::random(64);
            }
            if (empty($participant->access_code)) {
                $participant->access_code = strtoupper(Str::random(8));
            }
            if (empty($participant->pass_type)) {
                $participant->pass_type = $participant->role ?? self::ROLE_ATTENDEE;
            }
        });
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function checkIn(): void
    {
        $this->increment('checked_in_count');
        $this->update(['last_check_in_at' => now()]);
    }

    public function hasCheckedIn(): bool
    {
        return $this->checked_in_count > 0;
    }

    public function getQrCodeDataAttribute(): string
    {
        return $this->access_token;
    }

    public function getQrCodeUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($this->access_token);
    }

    public function getPassTypeLabelAttribute(): string
    {
        return match ($this->pass_type) {
            'speaker' => 'Speaker',
            'organizer' => 'Organizer',
            default => 'Attendee',
        };
    }

    public function getPassTypeColorAttribute(): string
    {
        return match ($this->pass_type) {
            'speaker' => 'purple',
            'organizer' => 'green',
            default => 'blue',
        };
    }

    public function isSpeaker(): bool
    {
        return $this->role === self::ROLE_SPEAKER;
    }

    public function isOrganizer(): bool
    {
        return $this->role === self::ROLE_ORGANIZER;
    }

    public function isAttendee(): bool
    {
        return $this->role === self::ROLE_ATTENDEE;
    }
}
