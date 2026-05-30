<?php
// app/Models/Role.php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model {
    use HasUuid, HasSoftDelete;

    protected $fillable = ['name', 'description'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Existing roles
    public const ADMIN = 'admin';
    public const FRONT_DESK = 'front_desk';
    public const MANAGER = 'manager';  // General Manager - Approvals and high-level oversight
    public const SUPERVISOR = 'supervisor';  // Supervisor - Day-to-day task supervision
    public const HOUSE_HELP = 'house_help';
    public const STORE_MANAGER = 'store_manager';
    public const STORE_KEEPER = 'store_keeper';

    // New roles for Bar / Restaurant modules
    public const RESTAURANT_MANAGER = 'restaurant_manager';
    public const WAITER = 'waiter';
    public const BAR_TENDER = 'bar_tender';
    public const LAUNDRY_MANAGER = 'laundry_manager';
    public const ACCOUNTANT = 'ACCOUNTANT';

    public static array $roles = [
        self::ADMIN              => 'System administrator with full access',
        self::FRONT_DESK         => 'Booking management and guest charges',
        self::MANAGER            => 'Approvals, reports, and high-level operational oversight',
        self::SUPERVISOR         => 'Day-to-day task supervision and staff coordination',
        self::HOUSE_HELP         => 'Internal usage requests only',
        self::STORE_MANAGER      => 'Full store control, pricing, all reports',
        self::STORE_KEEPER       => 'Stock operations, restock, fulfillments',
        self::RESTAURANT_MANAGER => 'Bar and restaurant inventory, menus, transfers, damage',
        self::WAITER             => 'Can view menus and create restaurant orders',
        self::BAR_TENDER         => 'Bar order taking and stock view',
        self::ACCOUNTANT         => 'Full financial records, reports, payroll, reconciliation',
    ];

    public static function seedRoles(): void {
        foreach (self::$roles as $name => $description) {
            self::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }

    public static function normalizeName(?string $name): ?string
    {
        return $name === null ? null : strtolower($name);
    }

    public static function matches(?string $actual, ?string $expected): bool
    {
        return self::normalizeName($actual) === self::normalizeName($expected);
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
