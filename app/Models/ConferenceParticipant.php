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
            $participant->access_token = Str::random(64);
            $participant->access_code = strtoupper(Str::random(8));
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
}