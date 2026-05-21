<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSchedule extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'session_number',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'speaker_name',
        'speaker_email',
        'session_type',
        'max_capacity',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>=', now());
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('session_type', $type);
    }

    public function isOngoing(): bool
    {
        return now()->between($this->start_datetime, $this->end_datetime);
    }

    public function getDurationMinutesAttribute(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }
}
