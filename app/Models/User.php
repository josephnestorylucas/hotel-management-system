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

    protected $fillable = ['name', 'email', 'password', 'phone'];

    /**
     * Guard sensitive attributes from mass assignment.
     */
    protected $guarded = [
        'role_id',
        'is_active',
        'must_change_password',
        'password_reset_requested_at',
        'password_reset_completed_at',
        'password_reset_phone',
        'email_verified_at',
    ];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password_reset_requested_at' => 'datetime',
            'password_reset_completed_at' => 'datetime',
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
        return Role::matches($this->role?->name, $role);
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        $roleName = $this->roleName();

        if ($roleName === null) {
            return false;
        }

        foreach ($roles as $role) {
            if (Role::matches($roleName, $role)) {
                return true;
            }
        }

        return false;
    }

    public function roleName(): ?string
    {
        return Role::normalizeName($this->role?->name);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isSupervisor(): bool
    {
        return $this->hasRole(Role::SUPERVISOR);
    }

    public function isGeneralManager(): bool
    {
        return $this->hasRole(Role::MANAGER);
    }

    public function isFrontDesk(): bool
    {
        return $this->hasRole(Role::FRONT_DESK);
    }

    public function isHouseHelp(): bool
    {
        return $this->hasRole(Role::HOUSE_HELP);
    }

    public function isStoreManager(): bool
    {
        return $this->hasRole(Role::STORE_MANAGER);
    }

    // Keep backward compat — old code may call isManager()
    public function isManager(): bool
    {
        return $this->isStoreManager();
    }

    public function isStoreKeeper(): bool
    {
        return $this->hasRole(Role::STORE_KEEPER);
    }

    public function isRestaurantManager(): bool
    {
        return $this->hasRole(Role::RESTAURANT_MANAGER);
    }

    public function isWaiter(): bool
    {
        return $this->hasRole(Role::WAITER);
    }

    public function isBarTender(): bool
    {
        return $this->hasRole(Role::BAR_TENDER);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(Role::ACCOUNTANT);
    }

    public function dashboardRouteName(): string
    {
        return match ($this->roleName()) {
            Role::normalizeName(Role::ACCOUNTANT) => 'accountant.dashboard',
            default => 'dashboard',
        };
    }

    public function sidebarView(): string
    {
        return match ($this->roleName()) {
            Role::normalizeName(Role::ADMIN) => 'shared.sidebar.admin',
            Role::normalizeName(Role::MANAGER) => 'shared.sidebar.manager',
            Role::normalizeName(Role::STORE_MANAGER) => 'shared.sidebar.store-manager',
            Role::normalizeName(Role::SUPERVISOR) => 'shared.sidebar.supervisor',
            Role::normalizeName(Role::HOUSE_HELP) => 'shared.sidebar.house-help',
            Role::normalizeName(Role::STORE_KEEPER) => 'shared.sidebar.store-keeper',
            Role::normalizeName(Role::RESTAURANT_MANAGER) => 'shared.sidebar.restaurant-manager',
            Role::normalizeName(Role::BAR_TENDER) => 'shared.sidebar.bar-tender',
            Role::normalizeName(Role::WAITER) => 'shared.sidebar.waiter',
            Role::normalizeName(Role::ACCOUNTANT) => 'shared.sidebar.accountant',
            default => 'shared.sidebar.front-desk',
        };
    }

    public function displayRoleName(): string
    {
        return str_replace('_', ' ', (string) $this->roleName());
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
