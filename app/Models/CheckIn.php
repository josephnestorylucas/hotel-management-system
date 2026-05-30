<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSoftDelete;

class CheckIn extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'attendance_id',
        'event_schedule_id',
        'check_in_type',
        'check_in_method',
        'staff_id',
        'ip_address',
        'device_info',
        'location',
        'verification_notes',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function eventSchedule(): BelongsTo
    {
        return $this->belongsTo(EventSchedule::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeByAttendance($query, string $attendanceId)
    {
        return $query->where('attendance_id', $attendanceId);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereIn('check_in_type', ['entry', 'exit']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
