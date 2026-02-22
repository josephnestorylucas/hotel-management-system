# 🏨 Hotel Store Module — Laravel + Blade Implementation
### Standard Laravel Folders · Blade Views · No New Directories · Controller-Centered

> Every file goes exactly where Laravel expects it. No new directories created.
> Blade views, web routes, standard folder layout throughout.

---

## 📋 Table of Contents

1. [File Map — Where Everything Goes](#1-file-map--where-everything-goes)
2. [Middleware](#2-middleware)
3. [Migrations](#3-migrations)
4. [Seeders](#4-seeders)
5. [Models](#5-models)
6. [Controllers](#6-controllers)
7. [Blade Views](#7-blade-views)
8. [Routes](#8-routes)
9. [Business Rules](#9-business-rules)
10. [Build Order](#10-build-order)

---

## 1. File Map — Where Everything Goes

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Store/                               ← subfolder inside existing Controllers/
│   │       ├── ProductController.php
│   │       ├── StockController.php
│   │       ├── AdjustmentController.php
│   │       ├── InternalRequestController.php
│   │       ├── StockTransferController.php
│   │       └── ReportController.php
│   └── Middleware/
│       └── CheckRole.php                        ← existing Middleware folder
│
└── Models/                                      ← existing Models folder
    ├── Role.php
    ├── StockLocation.php
    ├── Product.php
    ├── StockLevel.php
    ├── StockMovement.php
    ├── StockAdjustment.php
    ├── InternalUsageRequest.php
    ├── StockTransfer.php
    └── StoreNotification.php

database/
├── migrations/                                  ← existing migrations folder
│   ├── xxxx_create_roles_table.php
│   ├── xxxx_add_role_id_to_users_table.php
│   ├── xxxx_create_stock_locations_table.php
│   ├── xxxx_create_products_table.php
│   ├── xxxx_create_stock_levels_table.php
│   ├── xxxx_create_stock_movements_table.php
│   ├── xxxx_create_stock_adjustments_table.php
│   ├── xxxx_create_internal_usage_requests_table.php
│   ├── xxxx_create_stock_transfers_table.php
│   ├── xxxx_create_store_notifications_table.php
│   └── xxxx_create_system_settings_table.php
│
└── seeders/                                     ← existing seeders folder
    ├── RoleSeeder.php
    ├── StockLocationSeeder.php
    ├── SystemSettingsSeeder.php
    └── DatabaseSeeder.php

resources/
└── views/                                       ← existing views folder
    └── store/                                   ← subfolder inside existing views/
        ├── layout.blade.php
        ├── products/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   ├── edit.blade.php
        │   └── show.blade.php
        ├── stock/
        │   ├── levels.blade.php
        │   ├── restock.blade.php
        │   └── damage.blade.php
        ├── adjustments/
        │   ├── index.blade.php
        │   └── create.blade.php
        ├── internal-requests/
        │   ├── index.blade.php
        │   └── create.blade.php
        ├── transfers/
        │   ├── index.blade.php
        │   └── create.blade.php
        └── reports/
            ├── stock-snapshot.blade.php
            ├── movements.blade.php
            └── damage.blade.php

routes/
└── web.php                                      ← existing web routes file
```

---

## 2. Middleware

**File:** `app/Http/Middleware/CheckRole.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
```

**Register in `app/Http/Kernel.php`:**

```php
// Inside $routeMiddleware array
'role' => \App\Http\Middleware\CheckRole::class,
```

**Add role relationship to existing User model `app/Models/User.php`:**

```php
// Add this relationship to your existing User model
public function role()
{
    return $this->belongsTo(\App\Models\Role::class);
}

// Helper to check role anywhere in Blade or controllers
public function hasRole(string $role): bool
{
    return $this->role?->name === $role;
}

public function hasAnyRole(array $roles): bool
{
    return in_array($this->role?->name, $roles);
}
```

---

## 3. Migrations

All files go in `database/migrations/`. Run in the order listed.

---

**File:** `database/migrations/xxxx_create_roles_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();  // STORE_MANAGER, STORE_KEEPER, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

---

**File:** `database/migrations/xxxx_add_role_id_to_users_table.php`

> Skip if role_id already exists on your users table.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('role_id')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'is_active']);
        });
    }
};
```

---

**File:** `database/migrations/xxxx_create_stock_locations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 20)->unique();  // main_store | bar | kitchen
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_locations');
    }
};
```

---

**File:** `database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('sku', 50)->unique();
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();  // beverages, toiletries, food, cleaning
            $table->string('unit', 30);                   // bottle, kg, piece, litre, pack
            $table->decimal('cost_price', 10, 2);         // purchase/supply cost
            $table->decimal('selling_price', 10, 2);      // guest-facing price
            $table->integer('reorder_level')->default(0); // low stock alert threshold
            $table->boolean('is_active')->default(true);  // soft delete — never hard delete
            $table->uuid('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

---

**File:** `database/migrations/xxxx_create_stock_levels_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One row per product per location.
        // NEVER update quantity directly — always via StockMovement::record()
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('location_id');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('reserved_qty', 10, 3)->default(0); // held for pending orders
            $table->timestamp('last_counted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('stock_locations')->cascadeOnDelete();

            $table->unique(['product_id', 'location_id']); // one row per product-location only
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
```

---

**File:** `database/migrations/xxxx_create_stock_movements_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Immutable audit ledger. No updates. No deletes. Ever.
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('location_id');
            $table->enum('type', [
                'restock',      // incoming from supplier
                'sale',         // guest purchase
                'internal_use', // housekeeping/maintenance consumption
                'damage',       // broken, expired, spoiled
                'adjustment',   // manual correction
                'transfer_out', // leaving this location
                'transfer_in',  // arriving at this location
                'recipe_use',   // ingredient consumed by bar/kitchen recipe
            ]);
            $table->decimal('quantity', 10, 3);          // always positive
            $table->decimal('quantity_before', 10, 3);   // snapshot before movement
            $table->decimal('quantity_after', 10, 3);    // snapshot after movement
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->enum('reference_type', [
                'booking', 'internal_request', 'transfer', 'order', 'manual'
            ])->nullable();
            $table->uuid('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by');
            $table->timestamp('created_at');             // no updated_at — immutable record

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['product_id', 'location_id']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
```

---

**File:** `database/migrations/xxxx_create_stock_adjustments_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('location_id');
            $table->decimal('previous_qty', 10, 3);
            $table->decimal('new_qty', 10, 3);
            $table->decimal('difference', 10, 3);        // new_qty - previous_qty
            $table->text('reason');                      // mandatory — why adjusted
            $table->boolean('requires_approval')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected', 'applied'])->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
```

---

**File:** `database/migrations/xxxx_create_internal_usage_requests_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internal_usage_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('department', 100);            // housekeeping, maintenance, admin
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 3);
            $table->enum('status', [
                'pending',    // submitted by HOUSE_HELP
                'approved',   // SUPERVISOR approved
                'rejected',   // SUPERVISOR rejected
                'fulfilled',  // STORE_KEEPER dispatched
                'cancelled',  // requester cancelled
            ])->default('pending');
            $table->text('reason')->nullable();
            $table->uuid('requested_by');               // HOUSE_HELP
            $table->uuid('approved_by')->nullable();    // SUPERVISOR
            $table->uuid('fulfilled_by')->nullable();   // STORE_KEEPER
            $table->text('rejected_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('requested_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_usage_requests');
    }
};
```

---

**File:** `database/migrations/xxxx_create_stock_transfers_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('from_location_id');             // always main_store initially
            $table->uuid('to_location_id');               // bar or kitchen
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 3);
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed', 'cancelled'
            ])->default('pending');
            $table->text('reason')->nullable();
            $table->uuid('requested_by');               // BAR_MANAGER or KITCHEN_MANAGER
            $table->uuid('approved_by')->nullable();
            $table->uuid('fulfilled_by')->nullable();   // STORE_KEEPER
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('from_location_id')->references('id')->on('stock_locations');
            $table->foreign('to_location_id')->references('id')->on('stock_locations');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('requested_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
```

---

**File:** `database/migrations/xxxx_create_store_notifications_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('type', 100);               // low_stock, pending_request, fulfilled
            $table->string('title', 255);
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->string('reference_type', 50)->nullable();
            $table->uuid('reference_id')->nullable();
            $table->string('action_url', 500)->nullable(); // blade route to link to
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_notifications');
    }
};
```

---

**File:** `database/migrations/xxxx_create_system_settings_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value');
            $table->text('description')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
```

---

## 4. Seeders

**File:** `database/seeders/RoleSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // Store module roles
            ['name' => 'STORE_MANAGER',   'description' => 'Full store control, pricing, all reports'],
            ['name' => 'STORE_KEEPER',    'description' => 'Stock operations, restock, fulfillments'],
            ['name' => 'HOUSE_HELP',      'description' => 'Internal usage requests only'],
            ['name' => 'SUPERVISOR',      'description' => 'Approvals and operational oversight'],
            // Bar module roles — defined now, used when Bar module is built
            ['name' => 'BAR_MANAGER',     'description' => 'Bar inventory and menu management'],
            ['name' => 'BAR_TENDER',      'description' => 'Bar order taking and stock view'],
            // Restaurant module roles — defined now, used when Restaurant module is built
            ['name' => 'KITCHEN_MANAGER', 'description' => 'Kitchen inventory and recipe management'],
            ['name' => 'WAITER',          'description' => 'Table order taking'],
            // Shared roles
            ['name' => 'CASHIER',         'description' => 'Payment settlement and shift reports'],
            ['name' => 'FRONT_DESK',      'description' => 'Booking management and guest charges'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
```

---

**File:** `database/seeders/StockLocationSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class StockLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Main Store', 'code' => 'main_store', 'description' => 'Central hotel inventory'],
            ['name' => 'Bar',        'code' => 'bar',        'description' => 'Bar section inventory'],
            ['name' => 'Kitchen',    'code' => 'kitchen',    'description' => 'Kitchen section inventory'],
        ];

        foreach ($locations as $loc) {
            StockLocation::updateOrCreate(['code' => $loc['code']], $loc);
        }
    }
}
```

---

**File:** `database/seeders/SystemSettingsSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'         => 'adjustment_approval_threshold',
                'value'       => '50',
                'description' => 'Adjustments above this unit count require STORE_MANAGER approval',
            ],
            [
                'key'         => 'low_stock_alert_enabled',
                'value'       => 'true',
                'description' => 'Toggle low stock notifications on/off',
            ],
        ];

        foreach ($settings as $s) {
            DB::table('system_settings')->updateOrInsert(['key' => $s['key']], $s);
        }
    }
}
```

---

**File:** `database/seeders/DatabaseSeeder.php` — update the existing file

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        StockLocationSeeder::class,
        SystemSettingsSeeder::class,
    ]);
}
```

---

## 5. Models

All models go in `app/Models/` — the standard location.

---

**File:** `app/Models/Role.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
```

---

**File:** `app/Models/StockLocation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // Handy static helpers so controllers never hardcode IDs
    public static function mainStore(): self
    {
        return static::where('code', 'main_store')->firstOrFail();
    }

    public static function bar(): self
    {
        return static::where('code', 'bar')->firstOrFail();
    }

    public static function kitchen(): self
    {
        return static::where('code', 'kitchen')->firstOrFail();
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'location_id');
    }
}
```

---

**File:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'sku', 'description', 'category', 'unit',
        'cost_price', 'selling_price', 'reorder_level', 'is_active', 'created_by',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'reorder_level' => 'integer',
    ];

    // Auto-create a stock_levels row for every active location when product is created
    protected static function booted(): void
    {
        static::created(function (Product $product) {
            StockLocation::where('is_active', true)->get()->each(function ($location) use ($product) {
                StockLevel::firstOrCreate(
                    ['product_id'  => $product->id, 'location_id' => $location->id],
                    ['quantity'    => 0, 'reserved_qty' => 0]
                );
            });
        });
    }

    public function stockLevels()   { return $this->hasMany(StockLevel::class); }
    public function movements()     { return $this->hasMany(StockMovement::class); }
    public function createdBy()     { return $this->belongsTo(User::class, 'created_by'); }
}
```

---

**File:** `app/Models/StockLevel.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['product_id', 'location_id', 'quantity', 'reserved_qty', 'last_counted_at'];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'reserved_qty' => 'decimal:3',
    ];

    // available = total minus anything reserved for pending orders
    public function getAvailableQtyAttribute(): float
    {
        return (float) $this->quantity - (float) $this->reserved_qty;
    }

    public function product()  { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(StockLocation::class, 'location_id'); }
}
```

---

**File:** `app/Models/StockMovement.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'product_id', 'location_id', 'type', 'quantity',
        'quantity_before', 'quantity_after', 'unit_cost',
        'reference_type', 'reference_id', 'notes',
        'approved_by', 'created_by', 'created_at',
    ];

    protected $casts = [
        'quantity'        => 'decimal:3',
        'quantity_before' => 'decimal:3',
        'quantity_after'  => 'decimal:3',
        'unit_cost'       => 'decimal:2',
        'created_at'      => 'datetime',
    ];

    /**
     * THE CORE METHOD — every stock change goes through here.
     *
     * Wraps in DB::transaction with lockForUpdate().
     * Aborts 422 if stock would go negative.
     * Writes immutable before/after snapshot.
     *
     * @param array $params {
     *   product_id, location_id, type, quantity,
     *   new_quantity (adjustment type only),
     *   unit_cost?, reference_type?, reference_id?,
     *   notes?, approved_by?
     * }
     * @param string $actorId  auth()->id()
     */
    public static function record(array $params, string $actorId): self
    {
        return DB::transaction(function () use ($params, $actorId) {

            $level = StockLevel::where('product_id', $params['product_id'])
                               ->where('location_id', $params['location_id'])
                               ->lockForUpdate()
                               ->firstOrFail();

            $before = (float) $level->quantity;

            $increaseTypes = ['restock', 'transfer_in'];
            $decreaseTypes = ['sale', 'internal_use', 'damage', 'transfer_out', 'recipe_use'];

            if (in_array($params['type'], $increaseTypes)) {
                $after = $before + (float) $params['quantity'];
            } elseif (in_array($params['type'], $decreaseTypes)) {
                $after = $before - (float) $params['quantity'];
            } else {
                // adjustment — caller provides the exact target quantity
                $after             = (float) $params['new_quantity'];
                $params['quantity'] = abs($after - $before);
            }

            // HARD RULE: Stock can never go negative
            if ($after < 0) {
                abort(422, "Insufficient stock. Available: {$before}, Requested: {$params['quantity']}");
            }

            $level->update(['quantity' => $after, 'updated_at' => now()]);

            $movement = self::create([
                'product_id'      => $params['product_id'],
                'location_id'     => $params['location_id'],
                'type'            => $params['type'],
                'quantity'        => $params['quantity'],
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'unit_cost'       => $params['unit_cost'] ?? null,
                'reference_type'  => $params['reference_type'] ?? null,
                'reference_id'    => $params['reference_id'] ?? null,
                'notes'           => $params['notes'] ?? null,
                'approved_by'     => $params['approved_by'] ?? null,
                'created_by'      => $actorId,
                'created_at'      => now(),
            ]);

            // Fire low-stock notification if we just crossed below the reorder level
            $product = $level->product;
            if ($after <= $product->reorder_level && $before > $product->reorder_level) {
                self::sendLowStockAlert($product, $level->location, $after);
            }

            return $movement;
        });
    }

    private static function sendLowStockAlert(Product $product, StockLocation $location, float $qty): void
    {
        User::whereHas('role', fn($q) => $q->where('name', 'STORE_MANAGER'))
            ->get()
            ->each(function ($manager) use ($product, $location, $qty) {
                StoreNotification::create([
                    'user_id'        => $manager->id,
                    'type'           => 'low_stock',
                    'title'          => 'Low Stock Alert',
                    'body'           => "{$product->name} at {$location->name} is low: {$qty} {$product->unit} remaining.",
                    'reference_type' => 'product',
                    'reference_id'   => $product->id,
                    'action_url'     => route('store.products.show', $product->id),
                    'created_at'     => now(),
                ]);
            });
    }

    public function product()  { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function actor()    { return $this->belongsTo(User::class, 'created_by'); }
}
```

---

**File:** `app/Models/StockAdjustment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id', 'location_id', 'previous_qty', 'new_qty',
        'difference', 'reason', 'requires_approval', 'status', 'approved_by', 'created_by',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'previous_qty'      => 'decimal:3',
        'new_qty'           => 'decimal:3',
        'difference'        => 'decimal:3',
    ];

    public function product()  { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
```

---

**File:** `app/Models/InternalUsageRequest.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InternalUsageRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'department', 'product_id', 'quantity', 'status', 'reason',
        'requested_by', 'approved_by', 'fulfilled_by',
        'rejected_reason', 'approved_at', 'fulfilled_at',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'approved_at'  => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function product()   { return $this->belongsTo(Product::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function fulfiller() { return $this->belongsTo(User::class, 'fulfilled_by'); }
}
```

---

**File:** `app/Models/StockTransfer.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasUuids;

    protected $fillable = [
        'from_location_id', 'to_location_id', 'product_id', 'quantity',
        'status', 'reason', 'requested_by', 'approved_by', 'fulfilled_by', 'completed_at',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'completed_at' => 'datetime',
    ];

    public function product()      { return $this->belongsTo(Product::class); }
    public function fromLocation() { return $this->belongsTo(StockLocation::class, 'from_location_id'); }
    public function toLocation()   { return $this->belongsTo(StockLocation::class, 'to_location_id'); }
    public function requester()    { return $this->belongsTo(User::class, 'requested_by'); }
    public function fulfiller()    { return $this->belongsTo(User::class, 'fulfilled_by'); }
}
```

---

**File:** `app/Models/StoreNotification.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $table = 'store_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'body',
        'is_read', 'reference_type', 'reference_id', 'action_url', 'created_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
```

---

## 6. Controllers

All in `app/Http/Controllers/Store/`. Namespace: `App\Http\Controllers\Store`.

---

**File:** `app/Http/Controllers/Store/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    // GET /store/products
    public function index(Request $request): View
    {
        $products = Product::with('stockLevels.location')
            ->where('is_active', true)
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate(20);

        return view('store.products.index', compact('products'));
    }

    // GET /store/products/create
    public function create(): View
    {
        return view('store.products.create');
    }

    // POST /store/products
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'sku'           => 'nullable|string|max:50|unique:products,sku',
            'description'   => 'nullable|string',
            'category'      => 'nullable|string|max:100',
            'unit'          => 'required|string|max:30',
            'cost_price'    => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        if (empty($data['sku'])) {
            $prefix      = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['name']), 0, 3));
            $data['sku'] = $prefix . '-' . strtoupper(substr(uniqid(), -6));
        }

        $data['created_by'] = auth()->id();

        // Product::booted() auto-creates stock_levels for all active locations
        $product = Product::create($data);

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', "Product '{$product->name}' created successfully.");
    }

    // GET /store/products/{product}
    public function show(Product $product): View
    {
        $product->load('stockLevels.location', 'createdBy');

        $recentMovements = StockMovement::where('product_id', $product->id)
            ->with('location', 'actor')
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('store.products.show', compact('product', 'recentMovements'));
    }

    // GET /store/products/{product}/edit
    public function edit(Product $product): View
    {
        return view('store.products.edit', compact('product'));
    }

    // PUT /store/products/{product}
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|string|max:150',
            'description'   => 'sometimes|nullable|string',
            'category'      => 'sometimes|nullable|string|max:100',
            'unit'          => 'sometimes|string|max:30',
            'cost_price'    => 'sometimes|numeric|min:0.01',
            'selling_price' => 'sometimes|numeric|min:0.01',
            'reorder_level' => 'sometimes|integer|min:0',
        ]);

        $product->update($data);

        return redirect()
            ->route('store.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    // DELETE /store/products/{product}
    // Soft deactivate only — never hard delete
    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return redirect()
            ->route('store.products.index')
            ->with('success', "Product '{$product->name}' deactivated.");
    }
}
```

---

**File:** `app/Http/Controllers/Store/StockController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    // GET /store/stock/levels
    public function levels(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->get();

        $levels = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->when($request->search, fn($q) => $q->where('products.name', 'like', '%' . $request->search . '%'))
            ->select('stock_levels.*')
            ->paginate(30);

        return view('store.stock.levels', compact('levels', 'locations'));
    }

    // GET /store/stock/restock
    public function restockForm(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.stock.restock', compact('products', 'locations'));
    }

    // POST /store/stock/restock
    public function restock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|uuid|exists:products,id',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'quantity'    => 'required|numeric|min:0.001',
            'unit_cost'   => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($data['product_id']);
        abort_if(!$product->is_active, 422, 'Cannot restock an inactive product.');

        StockMovement::record([
            'product_id'  => $data['product_id'],
            'location_id' => $data['location_id'],
            'type'        => 'restock',
            'quantity'    => $data['quantity'],
            'unit_cost'   => $data['unit_cost'] ?? null,
            'notes'       => $data['notes'] ?? null,
        ], auth()->id());

        return redirect()
            ->route('store.stock.levels')
            ->with('success', "Restocked {$data['quantity']} {$product->unit} of {$product->name}.");
    }

    // GET /store/stock/damage
    public function damageForm(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.stock.damage', compact('products', 'locations'));
    }

    // POST /store/stock/damage
    public function damage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|uuid|exists:products,id',
            'location_id' => 'required|uuid|exists:stock_locations,id',
            'quantity'    => 'required|numeric|min:0.001',
            'reason'      => 'required|string|max:255',
            'notes'       => 'nullable|string|max:500',
        ]);

        // BAR_MANAGER and KITCHEN_MANAGER are scoped to their own location
        $this->assertLocationAccess(auth()->user(), $data['location_id']);

        $product = Product::findOrFail($data['product_id']);

        StockMovement::record([
            'product_id'  => $data['product_id'],
            'location_id' => $data['location_id'],
            'type'        => 'damage',
            'quantity'    => $data['quantity'],
            'notes'       => $data['reason'] . ($data['notes'] ? ' | ' . $data['notes'] : ''),
        ], auth()->id());

        return redirect()
            ->route('store.stock.levels')
            ->with('success', "Damage of {$data['quantity']} {$product->unit} recorded for {$product->name}.");
    }

    private function assertLocationAccess($user, string $locationId): void
    {
        $role     = $user->role->name;
        $location = StockLocation::findOrFail($locationId);

        if ($role === 'BAR_MANAGER' && $location->code !== 'bar') {
            abort(403, 'BAR_MANAGER can only record damage at the bar location.');
        }

        if ($role === 'KITCHEN_MANAGER' && $location->code !== 'kitchen') {
            abort(403, 'KITCHEN_MANAGER can only record damage at the kitchen location.');
        }
    }
}
```

---

**File:** `app/Http/Controllers/Store/AdjustmentController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreNotification;
use App\Models\StockAdjustment;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdjustmentController extends Controller
{
    // GET /store/adjustments
    public function index(Request $request): View
    {
        $adjustments = StockAdjustment::with(['product', 'location', 'creator', 'approver'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('store.adjustments.index', compact('adjustments'));
    }

    // GET /store/adjustments/create
    public function create(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.adjustments.create', compact('products', 'locations'));
    }

    // POST /store/adjustments
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'   => 'required|uuid|exists:products,id',
            'location_id'  => 'required|uuid|exists:stock_locations,id',
            'new_quantity' => 'required|numeric|min:0',
            'reason'       => 'required|string|min:5|max:500',
        ]);

        $threshold  = (int) DB::table('system_settings')
                              ->where('key', 'adjustment_approval_threshold')
                              ->value('value') ?? 50;

        $currentQty = (float) StockLevel::where('product_id', $data['product_id'])
                                        ->where('location_id', $data['location_id'])
                                        ->value('quantity') ?? 0;

        $difference    = $data['new_quantity'] - $currentQty;
        $needsApproval = abs($difference) >= $threshold
                      && auth()->user()->role->name !== 'STORE_MANAGER';

        $adjustment = StockAdjustment::create([
            'product_id'        => $data['product_id'],
            'location_id'       => $data['location_id'],
            'previous_qty'      => $currentQty,
            'new_qty'           => $data['new_quantity'],
            'difference'        => $difference,
            'reason'            => $data['reason'],
            'requires_approval' => $needsApproval,
            'status'            => $needsApproval ? 'pending' : 'applied',
            'created_by'        => auth()->id(),
        ]);

        if ($needsApproval) {
            User::whereHas('role', fn($q) => $q->where('name', 'STORE_MANAGER'))
                ->get()
                ->each(fn($m) => StoreNotification::create([
                    'user_id'        => $m->id,
                    'type'           => 'pending_adjustment',
                    'title'          => 'Large Adjustment Needs Approval',
                    'body'           => abs($difference) . " unit adjustment pending. Reason: {$data['reason']}",
                    'reference_type' => 'stock_adjustment',
                    'reference_id'   => $adjustment->id,
                    'action_url'     => route('store.adjustments.index'),
                    'created_at'     => now(),
                ]));

            return redirect()
                ->route('store.adjustments.index')
                ->with('info', 'Adjustment submitted for manager approval.');
        }

        StockMovement::record([
            'product_id'   => $data['product_id'],
            'location_id'  => $data['location_id'],
            'type'         => 'adjustment',
            'new_quantity' => $data['new_quantity'],
            'notes'        => $data['reason'],
            'approved_by'  => auth()->id(),
        ], auth()->id());

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment applied.');
    }

    // POST /store/adjustments/{adjustment}/approve
    public function approve(StockAdjustment $adjustment): RedirectResponse
    {
        abort_if($adjustment->status !== 'pending', 422, 'Only pending adjustments can be approved.');

        StockMovement::record([
            'product_id'   => $adjustment->product_id,
            'location_id'  => $adjustment->location_id,
            'type'         => 'adjustment',
            'new_quantity' => (float) $adjustment->new_qty,
            'notes'        => $adjustment->reason,
            'approved_by'  => auth()->id(),
        ], auth()->id());

        $adjustment->update(['status' => 'applied', 'approved_by' => auth()->id()]);

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment approved and applied.');
    }

    // POST /store/adjustments/{adjustment}/reject
    public function reject(StockAdjustment $adjustment): RedirectResponse
    {
        abort_if($adjustment->status !== 'pending', 422, 'Only pending adjustments can be rejected.');

        $adjustment->update(['status' => 'rejected', 'approved_by' => auth()->id()]);

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment rejected.');
    }
}
```

---

**File:** `app/Http/Controllers/Store/InternalRequestController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InternalUsageRequest;
use App\Models\StoreNotification;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InternalRequestController extends Controller
{
    // GET /store/internal-requests
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $role  = $user->role->name;

        $requests = InternalUsageRequest::with(['product', 'requester', 'approver', 'fulfiller'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($role === 'HOUSE_HELP', fn($q) => $q->where('requested_by', $user->id))
            ->latest()
            ->paginate(20);

        return view('store.internal-requests.index', compact('requests'));
    }

    // GET /store/internal-requests/create
    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('store.internal-requests.create', compact('products'));
    }

    // POST /store/internal-requests
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
            'department' => 'required|string|max:100',
            'reason'     => 'nullable|string|max:500',
        ]);

        $req = InternalUsageRequest::create([
            ...$data,
            'status'       => 'pending',
            'requested_by' => auth()->id(),
        ]);

        User::whereHas('role', fn($q) => $q->where('name', 'SUPERVISOR'))
            ->get()
            ->each(fn($sup) => StoreNotification::create([
                'user_id'        => $sup->id,
                'type'           => 'pending_request',
                'title'          => 'New Internal Usage Request',
                'body'           => "{$req->department} needs: {$req->product->name} × {$req->quantity} {$req->product->unit}",
                'reference_type' => 'internal_usage_request',
                'reference_id'   => $req->id,
                'action_url'     => route('store.internal-requests.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request submitted successfully.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/approve
    public function approve(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be approved.');

        $internalUsageRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        User::whereHas('role', fn($q) => $q->where('name', 'STORE_KEEPER'))
            ->get()
            ->each(fn($k) => StoreNotification::create([
                'user_id'        => $k->id,
                'type'           => 'request_approved',
                'title'          => 'Request Ready to Fulfill',
                'body'           => "{$internalUsageRequest->product->name} × {$internalUsageRequest->quantity} for {$internalUsageRequest->department}",
                'reference_type' => 'internal_usage_request',
                'reference_id'   => $internalUsageRequest->id,
                'action_url'     => route('store.internal-requests.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request approved.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/reject
    public function reject(Request $request, InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be rejected.');

        $request->validate(['reason' => 'required|string|max:500']);

        $internalUsageRequest->update([
            'status'          => 'rejected',
            'approved_by'     => auth()->id(),
            'rejected_reason' => $request->reason,
        ]);

        StoreNotification::create([
            'user_id'        => $internalUsageRequest->requested_by,
            'type'           => 'request_rejected',
            'title'          => 'Your Request Was Rejected',
            'body'           => "Request for {$internalUsageRequest->product->name} rejected. Reason: {$request->reason}",
            'reference_type' => 'internal_usage_request',
            'reference_id'   => $internalUsageRequest->id,
            'action_url'     => route('store.internal-requests.index'),
            'created_at'     => now(),
        ]);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request rejected.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/fulfill
    public function fulfill(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        // HARD RULE: Cannot fulfill without supervisor approval
        abort_if(
            $internalUsageRequest->status !== 'approved',
            422,
            'Request must be approved by a Supervisor before fulfillment.'
        );

        DB::transaction(function () use ($internalUsageRequest) {
            StockMovement::record([
                'product_id'     => $internalUsageRequest->product_id,
                'location_id'    => StockLocation::mainStore()->id,
                'type'           => 'internal_use',
                'quantity'       => $internalUsageRequest->quantity,
                'reference_type' => 'internal_request',
                'reference_id'   => $internalUsageRequest->id,
                'approved_by'    => $internalUsageRequest->approved_by,
                'notes'          => "Dispatched to {$internalUsageRequest->department}",
            ], auth()->id());

            $internalUsageRequest->update([
                'status'       => 'fulfilled',
                'fulfilled_by' => auth()->id(),
                'fulfilled_at' => now(),
            ]);
        });

        StoreNotification::create([
            'user_id'        => $internalUsageRequest->requested_by,
            'type'           => 'request_fulfilled',
            'title'          => 'Your Request Has Been Fulfilled',
            'body'           => "{$internalUsageRequest->product->name} dispatched to {$internalUsageRequest->department}.",
            'reference_type' => 'internal_usage_request',
            'reference_id'   => $internalUsageRequest->id,
            'action_url'     => route('store.internal-requests.index'),
            'created_at'     => now(),
        ]);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request fulfilled. Stock updated.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/cancel
    public function cancel(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->requested_by !== auth()->id(), 403);
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be cancelled.');

        $internalUsageRequest->update(['status' => 'cancelled']);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request cancelled.');
    }
}
```

---

**File:** `app/Http/Controllers/Store/StockTransferController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreNotification;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    // GET /store/transfers
    public function index(Request $request): View
    {
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('store.transfers.index', compact('transfers'));
    }

    // GET /store/transfers/create
    public function create(): View
    {
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $locations  = StockLocation::where('code', '!=', 'main_store')->where('is_active', true)->get();

        return view('store.transfers.create', compact('products', 'locations'));
    }

    // POST /store/transfers
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'       => 'required|uuid|exists:products,id',
            'to_location_code' => 'required|in:bar,kitchen',
            'quantity'         => 'required|numeric|min:0.001',
            'reason'           => 'nullable|string|max:500',
        ]);

        $fromLocation = StockLocation::mainStore();
        $toLocation   = StockLocation::where('code', $data['to_location_code'])->firstOrFail();

        $transfer = StockTransfer::create([
            'product_id'       => $data['product_id'],
            'from_location_id' => $fromLocation->id,
            'to_location_id'   => $toLocation->id,
            'quantity'         => $data['quantity'],
            'status'           => 'pending',
            'reason'           => $data['reason'] ?? null,
            'requested_by'     => auth()->id(),
        ]);

        User::whereHas('role', fn($q) => $q->whereIn('name', ['STORE_KEEPER', 'STORE_MANAGER']))
            ->get()
            ->each(fn($m) => StoreNotification::create([
                'user_id'        => $m->id,
                'type'           => 'pending_transfer',
                'title'          => 'Stock Transfer Requested',
                'body'           => "{$transfer->product->name} × {$transfer->quantity} requested for {$toLocation->name}",
                'reference_type' => 'stock_transfer',
                'reference_id'   => $transfer->id,
                'action_url'     => route('store.transfers.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer request submitted.');
    }

    // POST /store/transfers/{stockTransfer}/fulfill
    public function fulfill(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if(
            !in_array($stockTransfer->status, ['pending', 'approved']),
            422,
            'Only pending or approved transfers can be fulfilled.'
        );

        $sourceLevel = StockLevel::where('product_id', $stockTransfer->product_id)
                                 ->where('location_id', $stockTransfer->from_location_id)
                                 ->first();

        abort_if(
            !$sourceLevel || $sourceLevel->available_qty < $stockTransfer->quantity,
            422,
            'Insufficient stock at main store. Available: ' . ($sourceLevel?->available_qty ?? 0)
        );

        DB::transaction(function () use ($stockTransfer) {
            StockMovement::record([
                'product_id'     => $stockTransfer->product_id,
                'location_id'    => $stockTransfer->from_location_id,
                'type'           => 'transfer_out',
                'quantity'       => $stockTransfer->quantity,
                'reference_type' => 'transfer',
                'reference_id'   => $stockTransfer->id,
                'notes'          => "Transfer out to {$stockTransfer->toLocation->name}",
            ], auth()->id());

            StockMovement::record([
                'product_id'     => $stockTransfer->product_id,
                'location_id'    => $stockTransfer->to_location_id,
                'type'           => 'transfer_in',
                'quantity'       => $stockTransfer->quantity,
                'reference_type' => 'transfer',
                'reference_id'   => $stockTransfer->id,
                'notes'          => "Transfer in from {$stockTransfer->fromLocation->name}",
            ], auth()->id());

            $stockTransfer->update([
                'status'       => 'completed',
                'fulfilled_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer completed. Stock moved.');
    }

    // POST /store/transfers/{stockTransfer}/reject
    public function reject(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if($stockTransfer->status !== 'pending', 422, 'Only pending transfers can be rejected.');

        $stockTransfer->update(['status' => 'rejected']);

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer rejected.');
    }
}
```

---

**File:** `app/Http/Controllers/Store/ReportController.php`

```php
<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InternalUsageRequest;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    // GET /store/reports/stock-snapshot
    public function stockSnapshot(Request $request): View
    {
        $levels = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->select('stock_levels.*')
            ->paginate(30);

        return view('store.reports.stock-snapshot', compact('levels'));
    }

    // GET /store/reports/movements
    public function movements(Request $request): View
    {
        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->when($request->product_id,  fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->when($request->type,        fn($q) => $q->where('type', $request->type))
            ->when($request->date_from,   fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,     fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(50);

        return view('store.reports.movements', compact('movements'));
    }

    // GET /store/reports/damage
    public function damage(Request $request): View
    {
        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->where('type', 'damage')
            ->when($request->date_from,   fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,     fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->latest('created_at')
            ->paginate(30);

        return view('store.reports.damage', compact('movements'));
    }
}
```

---

## 7. Blade Views

All views go inside `resources/views/store/`. The `store/` subfolder sits inside the existing `views/` directory.

---

**File:** `resources/views/store/layout.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Nav --}}
<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">🏨 Store Module</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('store.products.index') }}"        class="text-gray-600 hover:text-blue-600">Products</a>
        <a href="{{ route('store.stock.levels') }}"          class="text-gray-600 hover:text-blue-600">Stock</a>
        <a href="{{ route('store.adjustments.index') }}"     class="text-gray-600 hover:text-blue-600">Adjustments</a>
        <a href="{{ route('store.internal-requests.index') }}" class="text-gray-600 hover:text-blue-600">Requests</a>
        <a href="{{ route('store.transfers.index') }}"       class="text-gray-600 hover:text-blue-600">Transfers</a>
        @if(auth()->user()->hasAnyRole(['STORE_MANAGER', 'SUPERVISOR']))
        <a href="{{ route('store.reports.movements') }}"     class="text-gray-600 hover:text-blue-600">Reports</a>
        @endif
    </div>
    <div class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ auth()->user()->role->name }}</div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Page content --}}
<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>
```

---

**File:** `resources/views/store/products/index.blade.php`

```blade
@extends('store.layout')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Products</h1>
    @if(auth()->user()->hasRole('STORE_MANAGER'))
    <a href="{{ route('store.products.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + New Product
    </a>
    @endif
</div>

{{-- Search & filter --}}
<form method="GET" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search products..."
           class="border rounded px-3 py-2 text-sm flex-1">
    <select name="category" class="border rounded px-3 py-2 text-sm">
        <option value="">All Categories</option>
        <option value="beverages"  {{ request('category') === 'beverages' ? 'selected' : '' }}>Beverages</option>
        <option value="food"       {{ request('category') === 'food' ? 'selected' : '' }}>Food</option>
        <option value="toiletries" {{ request('category') === 'toiletries' ? 'selected' : '' }}>Toiletries</option>
        <option value="cleaning"   {{ request('category') === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
    </select>
    <button class="bg-gray-200 px-4 py-2 rounded text-sm hover:bg-gray-300">Filter</button>
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Name</th>
                <th class="px-4 py-3 text-left text-gray-600">SKU</th>
                <th class="px-4 py-3 text-left text-gray-600">Category</th>
                <th class="px-4 py-3 text-left text-gray-600">Unit</th>
                <th class="px-4 py-3 text-right text-gray-600">Cost Price</th>
                <th class="px-4 py-3 text-right text-gray-600">Selling Price</th>
                <th class="px-4 py-3 text-center text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $product->sku }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $product->category ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $product->unit }}</td>
                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($product->cost_price, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($product->selling_price, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('store.products.show', $product) }}"
                       class="text-blue-600 hover:underline text-xs mr-2">View</a>
                    @if(auth()->user()->hasRole('STORE_MANAGER'))
                    <a href="{{ route('store.products.edit', $product) }}"
                       class="text-yellow-600 hover:underline text-xs">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
```

---

**File:** `resources/views/store/products/create.blade.php`

```blade
@extends('store.layout')

@section('title', 'Create Product')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Product</h1>

    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('store.products.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border rounded px-3 py-2 text-sm @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU (auto-generated if empty)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="w-full border rounded px-3 py-2 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select unit...</option>
                        @foreach(['bottle','kg','piece','litre','pack','box','can','sachet'] as $unit)
                        <option value="{{ $unit }}" {{ old('unit') === $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select category...</option>
                        @foreach(['beverages','food','toiletries','cleaning','stationery','other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}"
                           step="0.01" min="0.01" required
                           class="w-full border rounded px-3 py-2 text-sm @error('cost_price') border-red-400 @enderror">
                    @error('cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price') }}"
                           step="0.01" min="0.01" required
                           class="w-full border rounded px-3 py-2 text-sm @error('selling_price') border-red-400 @enderror">
                    @error('selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}"
                           min="0" class="w-full border rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Alert will fire when stock drops to or below this number.</p>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 text-sm">
                    Create Product
                </button>
                <a href="{{ route('store.products.index') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded hover:bg-gray-300 text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
```

---

**File:** `resources/views/store/products/show.blade.php`

```blade
@extends('store.layout')

@section('title', $product->name)

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
        <p class="text-sm text-gray-400 font-mono mt-1">{{ $product->sku }}</p>
    </div>
    <div class="flex gap-2">
        @if(auth()->user()->hasRole('STORE_MANAGER'))
        <a href="{{ route('store.products.edit', $product) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded text-sm hover:bg-yellow-600">Edit</a>
        @endif
        @if(auth()->user()->hasAnyRole(['STORE_KEEPER', 'STORE_MANAGER']))
        <a href="{{ route('store.stock.restock-form') }}"
           class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Restock</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    {{-- Product details --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Details</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Category</dt><dd>{{ $product->category ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Unit</dt><dd>{{ $product->unit }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Cost Price</dt><dd>{{ number_format($product->cost_price, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Selling Price</dt><dd class="font-medium">{{ number_format($product->selling_price, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Reorder Level</dt><dd>{{ $product->reorder_level }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
                <dd>
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Stock by location --}}
    <div class="col-span-2 bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Stock by Location</h2>
        <table class="w-full text-sm">
            <thead><tr class="border-b">
                <th class="pb-2 text-left text-gray-500">Location</th>
                <th class="pb-2 text-right text-gray-500">Quantity</th>
                <th class="pb-2 text-right text-gray-500">Reserved</th>
                <th class="pb-2 text-right text-gray-500">Available</th>
                <th class="pb-2 text-center text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($product->stockLevels as $level)
                <tr>
                    <td class="py-2">{{ $level->location->name }}</td>
                    <td class="py-2 text-right">{{ $level->quantity }}</td>
                    <td class="py-2 text-right text-gray-400">{{ $level->reserved_qty }}</td>
                    <td class="py-2 text-right font-medium">{{ $level->available_qty }}</td>
                    <td class="py-2 text-center">
                        @if($level->quantity <= $product->reorder_level)
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Low</span>
                        @else
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Recent movements --}}
<div class="bg-white rounded shadow p-5">
    <h2 class="font-semibold text-gray-700 mb-3">Recent Movements</h2>
    <table class="w-full text-sm">
        <thead><tr class="border-b">
            <th class="pb-2 text-left text-gray-500">Date</th>
            <th class="pb-2 text-left text-gray-500">Type</th>
            <th class="pb-2 text-left text-gray-500">Location</th>
            <th class="pb-2 text-right text-gray-500">Quantity</th>
            <th class="pb-2 text-right text-gray-500">Before</th>
            <th class="pb-2 text-right text-gray-500">After</th>
            <th class="pb-2 text-left text-gray-500">By</th>
            <th class="pb-2 text-left text-gray-500">Notes</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($recentMovements as $m)
            <tr>
                <td class="py-2 text-gray-400 text-xs">{{ $m->created_at->format('d M Y H:i') }}</td>
                <td class="py-2">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        @if(in_array($m->type, ['restock','transfer_in'])) bg-green-100 text-green-700
                        @elseif(in_array($m->type, ['damage','transfer_out'])) bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ str_replace('_', ' ', $m->type) }}
                    </span>
                </td>
                <td class="py-2 text-gray-500">{{ $m->location->name }}</td>
                <td class="py-2 text-right">{{ $m->quantity }}</td>
                <td class="py-2 text-right text-gray-400">{{ $m->quantity_before }}</td>
                <td class="py-2 text-right font-medium">{{ $m->quantity_after }}</td>
                <td class="py-2 text-gray-500 text-xs">{{ $m->actor->name ?? '—' }}</td>
                <td class="py-2 text-gray-400 text-xs">{{ Str::limit($m->notes, 40) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-6 text-center text-gray-400">No movements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
```

---

**File:** `resources/views/store/internal-requests/index.blade.php`

```blade
@extends('store.layout')

@section('title', 'Internal Requests')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Internal Usage Requests</h1>
    @if(auth()->user()->hasRole('HOUSE_HELP'))
    <a href="{{ route('store.internal-requests.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        + New Request
    </a>
    @endif
</div>

{{-- Status filter --}}
<form method="GET" class="flex gap-3 mb-6">
    @foreach(['', 'pending', 'approved', 'fulfilled', 'rejected', 'cancelled'] as $status)
    <a href="{{ route('store.internal-requests.index', $status ? ['status' => $status] : []) }}"
       class="px-3 py-1.5 rounded text-sm border
              {{ request('status') === $status || (empty($status) && !request('status'))
                 ? 'bg-blue-600 text-white border-blue-600'
                 : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $status ? ucfirst($status) : 'All' }}
    </a>
    @endforeach
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Date</th>
                <th class="px-4 py-3 text-left text-gray-600">Department</th>
                <th class="px-4 py-3 text-left text-gray-600">Product</th>
                <th class="px-4 py-3 text-right text-gray-600">Qty</th>
                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                <th class="px-4 py-3 text-left text-gray-600">Requested By</th>
                <th class="px-4 py-3 text-center text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $req->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">{{ $req->department }}</td>
                <td class="px-4 py-3 font-medium">{{ $req->product->name }}</td>
                <td class="px-4 py-3 text-right">{{ $req->quantity }} {{ $req->product->unit }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($req->status === 'pending')   bg-yellow-100 text-yellow-700
                        @elseif($req->status === 'approved')  bg-blue-100 text-blue-700
                        @elseif($req->status === 'fulfilled') bg-green-100 text-green-700
                        @elseif($req->status === 'rejected')  bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($req->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->requester->name }}</td>
                <td class="px-4 py-3 text-center space-x-2">
                    @if($req->status === 'pending' && auth()->user()->hasRole('SUPERVISOR'))
                    <form method="POST" action="{{ route('store.internal-requests.approve', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('store.internal-requests.reject', $req) }}" class="inline"
                          onsubmit="document.getElementById('reject-reason-{{ $req->id }}').value = prompt('Rejection reason:') ?? ''; return true;">
                        @csrf
                        <input type="hidden" id="reject-reason-{{ $req->id }}" name="reason">
                        <button class="text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Reject</button>
                    </form>
                    @endif

                    @if($req->status === 'approved' && auth()->user()->hasRole('STORE_KEEPER'))
                    <form method="POST" action="{{ route('store.internal-requests.fulfill', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Fulfill</button>
                    </form>
                    @endif

                    @if($req->status === 'pending' && $req->requested_by === auth()->id())
                    <form method="POST" action="{{ route('store.internal-requests.cancel', $req) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-gray-400 text-white px-2 py-1 rounded hover:bg-gray-500">Cancel</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $requests->withQueryString()->links() }}</div>
</div>
@endsection
```

---

**File:** `resources/views/store/transfers/index.blade.php`

```blade
@extends('store.layout')

@section('title', 'Stock Transfers')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Stock Transfers</h1>
    @if(auth()->user()->hasAnyRole(['BAR_MANAGER', 'KITCHEN_MANAGER']))
    <a href="{{ route('store.transfers.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        + Request Transfer
    </a>
    @endif
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Date</th>
                <th class="px-4 py-3 text-left text-gray-600">Product</th>
                <th class="px-4 py-3 text-right text-gray-600">Qty</th>
                <th class="px-4 py-3 text-left text-gray-600">From</th>
                <th class="px-4 py-3 text-left text-gray-600">To</th>
                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                <th class="px-4 py-3 text-center text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transfers as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $t->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $t->product->name }}</td>
                <td class="px-4 py-3 text-right">{{ $t->quantity }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->fromLocation->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->toLocation->name }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($t->status === 'pending')   bg-yellow-100 text-yellow-700
                        @elseif($t->status === 'completed') bg-green-100 text-green-700
                        @elseif($t->status === 'rejected')  bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($t->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center space-x-1">
                    @if($t->status === 'pending' && auth()->user()->hasAnyRole(['STORE_KEEPER', 'STORE_MANAGER']))
                    <form method="POST" action="{{ route('store.transfers.fulfill', $t) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Fulfill</button>
                    </form>
                    <form method="POST" action="{{ route('store.transfers.reject', $t) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Reject</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No transfers found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $transfers->withQueryString()->links() }}</div>
</div>
@endsection
```

---

## 8. Routes

**File:** `routes/web.php` — add inside your existing `auth` middleware group.

```php
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StockController;
use App\Http\Controllers\Store\AdjustmentController;
use App\Http\Controllers\Store\InternalRequestController;
use App\Http\Controllers\Store\StockTransferController;
use App\Http\Controllers\Store\ReportController;

Route::middleware(['auth'])->prefix('store')->name('store.')->group(function () {

    // ── Products ──────────────────────────────────────────────────────────────
    Route::get('products',                  [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create',           [ProductController::class, 'create'])->name('products.create')
         ->middleware('role:STORE_MANAGER');
    Route::post('products',                 [ProductController::class, 'store'])->name('products.store')
         ->middleware('role:STORE_MANAGER');
    Route::get('products/{product}',        [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit',   [ProductController::class, 'edit'])->name('products.edit')
         ->middleware('role:STORE_MANAGER');
    Route::put('products/{product}',        [ProductController::class, 'update'])->name('products.update')
         ->middleware('role:STORE_MANAGER');
    Route::delete('products/{product}',     [ProductController::class, 'destroy'])->name('products.destroy')
         ->middleware('role:STORE_MANAGER');

    // ── Stock ─────────────────────────────────────────────────────────────────
    Route::get('stock/levels',              [StockController::class, 'levels'])->name('stock.levels')
         ->middleware('role:STORE_MANAGER,STORE_KEEPER');
    Route::get('stock/restock',             [StockController::class, 'restockForm'])->name('stock.restock-form')
         ->middleware('role:STORE_KEEPER,STORE_MANAGER');
    Route::post('stock/restock',            [StockController::class, 'restock'])->name('stock.restock')
         ->middleware('role:STORE_KEEPER,STORE_MANAGER');
    Route::get('stock/damage',              [StockController::class, 'damageForm'])->name('stock.damage-form')
         ->middleware('role:STORE_KEEPER,STORE_MANAGER,BAR_MANAGER,KITCHEN_MANAGER');
    Route::post('stock/damage',             [StockController::class, 'damage'])->name('stock.damage')
         ->middleware('role:STORE_KEEPER,STORE_MANAGER,BAR_MANAGER,KITCHEN_MANAGER');

    // ── Adjustments ───────────────────────────────────────────────────────────
    Route::get('adjustments',              [AdjustmentController::class, 'index'])->name('adjustments.index')
         ->middleware('role:STORE_MANAGER,SUPERVISOR');
    Route::get('adjustments/create',       [AdjustmentController::class, 'create'])->name('adjustments.create')
         ->middleware('role:STORE_MANAGER,SUPERVISOR,BAR_MANAGER,KITCHEN_MANAGER');
    Route::post('adjustments',             [AdjustmentController::class, 'store'])->name('adjustments.store')
         ->middleware('role:STORE_MANAGER,SUPERVISOR,BAR_MANAGER,KITCHEN_MANAGER');
    Route::post('adjustments/{adjustment}/approve', [AdjustmentController::class, 'approve'])->name('adjustments.approve')
         ->middleware('role:STORE_MANAGER');
    Route::post('adjustments/{adjustment}/reject',  [AdjustmentController::class, 'reject'])->name('adjustments.reject')
         ->middleware('role:STORE_MANAGER');

    // ── Internal Requests ─────────────────────────────────────────────────────
    Route::get('internal-requests',                [InternalRequestController::class, 'index'])->name('internal-requests.index');
    Route::get('internal-requests/create',         [InternalRequestController::class, 'create'])->name('internal-requests.create')
         ->middleware('role:HOUSE_HELP');
    Route::post('internal-requests',               [InternalRequestController::class, 'store'])->name('internal-requests.store')
         ->middleware('role:HOUSE_HELP');
    Route::post('internal-requests/{internalUsageRequest}/approve', [InternalRequestController::class, 'approve'])->name('internal-requests.approve')
         ->middleware('role:SUPERVISOR');
    Route::post('internal-requests/{internalUsageRequest}/reject',  [InternalRequestController::class, 'reject'])->name('internal-requests.reject')
         ->middleware('role:SUPERVISOR');
    Route::post('internal-requests/{internalUsageRequest}/fulfill', [InternalRequestController::class, 'fulfill'])->name('internal-requests.fulfill')
         ->middleware('role:STORE_KEEPER');
    Route::post('internal-requests/{internalUsageRequest}/cancel',  [InternalRequestController::class, 'cancel'])->name('internal-requests.cancel');

    // ── Stock Transfers ───────────────────────────────────────────────────────
    Route::get('transfers',                [StockTransferController::class, 'index'])->name('transfers.index')
         ->middleware('role:STORE_MANAGER,STORE_KEEPER,BAR_MANAGER,KITCHEN_MANAGER');
    Route::get('transfers/create',         [StockTransferController::class, 'create'])->name('transfers.create')
         ->middleware('role:BAR_MANAGER,KITCHEN_MANAGER');
    Route::post('transfers',               [StockTransferController::class, 'store'])->name('transfers.store')
         ->middleware('role:BAR_MANAGER,KITCHEN_MANAGER');
    Route::post('transfers/{stockTransfer}/fulfill', [StockTransferController::class, 'fulfill'])->name('transfers.fulfill')
         ->middleware('role:STORE_KEEPER,STORE_MANAGER');
    Route::post('transfers/{stockTransfer}/reject',  [StockTransferController::class, 'reject'])->name('transfers.reject')
         ->middleware('role:STORE_MANAGER');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('reports/stock-snapshot',   [ReportController::class, 'stockSnapshot'])->name('reports.stock-snapshot')
         ->middleware('role:STORE_MANAGER,STORE_KEEPER');
    Route::get('reports/movements',        [ReportController::class, 'movements'])->name('reports.movements')
         ->middleware('role:STORE_MANAGER,STORE_KEEPER');
    Route::get('reports/damage',           [ReportController::class, 'damage'])->name('reports.damage')
         ->middleware('role:STORE_MANAGER,SUPERVISOR');

});
```

---

## 9. Business Rules

| # | Rule | Where Enforced |
|---|---|---|
| 001 | Stock cannot go negative | `StockMovement::record()` aborts 422 |
| 002 | No direct UPDATE on stock_levels | Every write goes through `StockMovement::record()` |
| 003 | All stock ops inside DB::transaction() | `StockMovement::record()` always wraps in transaction |
| 004 | Internal usage must be approved first | `fulfill()` checks `status === 'approved'` |
| 005 | Large adjustments need STORE_MANAGER | `AdjustmentController` checks system_settings threshold |
| 006 | Products with history are never deleted | `destroy()` sets `is_active = false` only |
| 007 | Prices cannot be zero or negative | `validate()` rules `min:0.01` |
| 008 | Transfers are atomic — both movements or neither | `StockTransferController::fulfill()` uses DB::transaction() |
| 009 | BAR/KITCHEN_MANAGER scoped to their location | `assertLocationAccess()` in StockController |
| 010 | available_qty = quantity − reserved_qty | Accessor on StockLevel model |

---

## 10. Build Order

```bash
# Phase 0 — Foundation
php artisan make:migration create_roles_table
php artisan make:migration add_role_id_to_users_table
php artisan make:migration create_stock_locations_table
php artisan make:migration create_system_settings_table
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=StockLocationSeeder
php artisan db:seed --class=SystemSettingsSeeder
# Register CheckRole in Kernel.php
# Add role() and hasRole() to User model

# Phase 1 — Products
php artisan make:migration create_products_table
php artisan make:migration create_stock_levels_table
php artisan migrate
# Create Product, StockLevel models
# Create ProductController → views/store/products/ → routes

# Phase 2 — Stock Ledger (Most Critical)
php artisan make:migration create_stock_movements_table
php artisan migrate
# Build StockMovement::record() first — test it in isolation
# Create StockController → views/store/stock/ → routes

# Phase 3 — Adjustments
php artisan make:migration create_stock_adjustments_table
php artisan migrate
# AdjustmentController → views/store/adjustments/ → routes

# Phase 4 — Internal Requests
php artisan make:migration create_internal_usage_requests_table
php artisan migrate
# InternalRequestController → views/store/internal-requests/ → routes

# Phase 5 — Transfers
php artisan make:migration create_stock_transfers_table
php artisan migrate
# StockTransferController → views/store/transfers/ → routes

# Phase 6 — Reports
# ReportController → views/store/reports/ → routes

# Phase 7+ — Bar & Restaurant
# Only after Phase 6 is done and tested.
# Bar and Kitchen reuse StockMovement::record() for all stock deductions.
```

---

*Store first. Blade views. Standard Laravel folders. No new directories.*
*Bar and Restaurant plug in on top of this foundation.*
