<?php
// app/Models/ConferenceBooking.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class ConferenceBooking extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_number',
        'conference_hall_id',
        'institution_id',
        'guest_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_cost',
        'status',
        'created_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            $booking->booking_number = 'CNF-' . strtoupper(uniqid());
        });
    }

    public function conferenceHall(): BelongsTo
    {
        return $this->belongsTo(ConferenceHall::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conference(): HasOne
    {
        return $this->hasOne(Conference::class);
    }

    public function getEndTimeWithBufferAttribute(): Carbon
    {
        return Carbon::parse($this->end_time)->addMinutes(30);
    }

    public function getDurationInHoursAttribute(): float
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $start->diffInHours($end, true);
    }
}