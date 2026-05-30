<?php
// app/Models/LaundryTask.php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryTask extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'task_number',
        'booking_id',
        'assigned_to',
        'created_by',
        'guest_name',
        'room_number',
        'description',
        'status',
        'is_amenity',
        'cost',
        'collected_at',
        'completed_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'is_amenity' => 'boolean',
        'cost' => 'decimal:2',
        'collected_at' => 'datetime',
        'completed_at' => 'datetime',
        'returned_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            $task->task_number = 'LND-' . strtoupper(Str::random(10));
        });
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    // Methods
    public function markAsInProgress(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'in_progress',
            'collected_at' => now(),
        ]);
    }

    public function markAsCompleted(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsReturned(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        return $this->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);
    }

    public function canBeMarkedAsReturned(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeMarkedAsCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canBeMarkedAsInProgress(): bool
    {
        return $this->status === 'pending';
    }

    // Accessors
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'purple',
            'returned' => 'green',
            default => 'gray',
        };
    }
}