<?php
// app/Models/Role.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model {
    use HasUuid;

    protected $fillable = ['name', 'description'];

    // Existing roles
    public const ADMIN = 'admin';
    public const FRONT_DESK = 'front_desk';
    public const SUPERVISOR = 'supervisor';
    public const HOUSE_HELP = 'house_help';
    public const STORE_MANAGER = 'store_manager';
    public const STORE_KEEPER = 'store_keeper';

    // New roles for Bar / Restaurant / Cashier modules
    public const RESTAURANT_MANAGER = 'restaurant_manager';
    public const BAR_TENDER = 'bar_tender';
    public const CASHIER = 'cashier';
    public const LAUNDRY_MANAGER = 'laundry_manager';
    public const ACCOUNTANT = 'ACCOUNTANT';

    public static array $roles = [
        self::ADMIN              => 'System administrator with full access',
        self::FRONT_DESK         => 'Booking management and guest charges',
        self::SUPERVISOR         => 'Approvals and operational oversight',
        self::HOUSE_HELP         => 'Internal usage requests only',
        self::STORE_MANAGER      => 'Full store control, pricing, all reports',
        self::STORE_KEEPER       => 'Stock operations, restock, fulfillments',
        self::RESTAURANT_MANAGER => 'Bar and restaurant inventory, menus, transfers, damage',
        self::BAR_TENDER         => 'Bar order taking and stock view',
        self::CASHIER            => 'Payment settlement and shift reports',
        self::LAUNDRY_MANAGER    => 'Manages laundry pricing, reports, and full order oversight',
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

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}