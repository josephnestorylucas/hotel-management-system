<?php
// app/Models/User.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Guarded attributes — role_id and is_active must be set explicitly,
     * never via mass assignment from user input.
     */
    protected $guarded_extra = ['role_id', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role && strtoupper($this->role->name) === strtoupper($role);
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array(strtoupper($this->role->name), array_map('strtoupper', $roles));
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === Role::ADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role && $this->role->name === Role::SUPERVISOR;
    }

    public function isFrontDesk(): bool
    {
        return $this->role && $this->role->name === Role::FRONT_DESK;
    }

    public function isHouseHelp(): bool
    {
        return $this->role && $this->role->name === Role::HOUSE_HELP;
    }

    public function isStoreManager(): bool
    {
        return $this->role && $this->role->name === Role::STORE_MANAGER;
    }

    // Keep backward compat — old code may call isManager()
    public function isManager(): bool
    {
        return $this->isStoreManager();
    }

    public function isStoreKeeper(): bool
    {
        return $this->role && $this->role->name === Role::STORE_KEEPER;
    }

    public function isRestaurantManager(): bool
    {
        return $this->role && $this->role->name === Role::RESTAURANT_MANAGER;
    }

    public function isBarTender(): bool
    {
        return $this->role && $this->role->name === Role::BAR_TENDER;
    }

    public function isCashier(): bool
    {
        return $this->role && $this->role->name === Role::CASHIER;
    }

    public function laundryTasks(): HasMany
    {
        return $this->hasMany(LaundryTask::class, 'assigned_to');
    }

    public function createdLaundryTasks(): HasMany
    {
        return $this->hasMany(LaundryTask::class, 'created_by');
    }

    /**
     * Get latest in-app notifications for the notification bell.
     */
    public function latestNotifications()
    {
        return $this->hasMany(StoreNotification::class)
                    ->latest('created_at')
                    ->limit(10);
    }

    /**
     * Get unread notification count.
     */
    public function unreadNotificationCount(): int
    {
        return StoreNotification::where('user_id', $this->id)
            ->where('is_read', false)
            ->count();
    }
}