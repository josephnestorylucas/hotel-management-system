<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSoftDelete;

class EventVenue extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'event_id',
        'conference_hall_id',
        'booking_id',
        'setup_type',
        'room_layout_notes',
        'special_requests',
        'expected_setup_start',
        'expected_setup_end',
        'teardown_start',
        'teardown_end',
        'status',
        'notes',
    ];

    protected $casts = [
        'expected_setup_start' => 'datetime',
        'expected_setup_end' => 'datetime',
        'teardown_start' => 'datetime',
        'teardown_end' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function conferenceHall(): BelongsTo
    {
        return $this->belongsTo(ConferenceHall::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ConferenceBooking::class, 'booking_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
