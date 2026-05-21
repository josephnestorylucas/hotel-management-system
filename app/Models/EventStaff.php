<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStaff extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'user_id',
        'role',
        'display_bio',
        'display_photo',
        'session_ids',
        'permissions',
        'assigned_at',
        'removed_at',
    ];

    protected $casts = [
        'session_ids' => 'array',
        'permissions' => 'array',
        'assigned_at' => 'datetime',
        'removed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('removed_at');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function isRemoved(): bool
    {
        return $this->removed_at !== null;
    }

    public function remove(): void
    {
        $this->update(['removed_at' => now()]);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) return false;
        if (isset($this->permissions['can_manage_everything']) && $this->permissions['can_manage_everything']) return true;
        return isset($this->permissions[$permission]) && $this->permissions[$permission];
    }
}
