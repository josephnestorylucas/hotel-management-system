# 🍸🍽️ Bar & Restaurant Module — Laravel + Blade Implementation
### Built on top of Store Module · Same Laravel Folder Structure · No New Directories

> The Store module is already done. This module plugs into it.
> All stock deductions still go through `StockMovement::record()`.
> Sales work for hotel guests (charge to booking) and walk-in customers (cash/card).

---

## 📋 Table of Contents

1. [How This Module Relates to Store](#1-how-this-module-relates-to-store)
2. [File Map — Where Everything Goes](#2-file-map--where-everything-goes)
3. [Migrations](#3-migrations)
4. [Seeders](#4-seeders)
5. [Models](#5-models)
6. [Controllers](#6-controllers)
7. [Blade Views](#7-blade-views)
8. [Routes](#8-routes)
9. [Business Rules](#9-business-rules)
10. [Build Order](#10-build-order)

---

## 1. How This Module Relates to Store

```
STORE MODULE (already built)
    └── stock_levels          ← bar and kitchen locations already exist
    └── stock_movements       ← StockMovement::record() used for every deduction
    └── stock_transfers       ← RESTAURANT_MANAGER requests stock from main store
    └── products              ← ingredients and sellable items come from here

BAR & RESTAURANT MODULE (this file)
    └── menu_categories       ← group menu items (Drinks, Food, Cocktails, etc.)
    └── menu_items            ← what you sell (e.g. Grilled Tilapia, Vodka Tonic)
    └── menu_item_ingredients ← recipe: what stock is consumed per item sold
    └── tables                ← physical tables in bar/restaurant
    └── orders                ← a sale — can be guest (charge to booking) or walk-in (cash)
    └── order_items           ← line items on an order
```

### Key Design Decisions

- **One module, two sections.** Bar and Restaurant are not separate — they share the same orders, menu, and tables system. Section is determined by `location_id` on the table.
- **Sales to hotel guests** → `order_type = 'guest'` → charge added to booking via `booking_charges` table.
- **Sales to walk-in customers** → `order_type = 'walkin'` → settled immediately by CASHIER.
- **Stock deducted on order settlement**, not on order creation. This prevents stock from being permanently deducted for orders that get cancelled.
- **Recipes are optional.** A menu item does not have to have a recipe. If it does, stock is auto-deducted on settlement. If it does not, no stock movement is created.
- **RESTAURANT_MANAGER** manages both bar and restaurant menus, stock, and transfers.
- **BAR_TENDER** takes orders and can settle walk-in cash orders.
- **CASHIER** settles all orders and handles guest billing.
- **FRONT_DESK** can view and add charges to a guest booking.

---

## 2. File Map — Where Everything Goes

```
app/
├── Http/
│   └── Controllers/
│       └── Restaurant/                          ← new subfolder inside existing Controllers/
│           ├── MenuCategoryController.php
│           ├── MenuItemController.php
│           ├── TableController.php
│           ├── OrderController.php
│           └── ReportController.php
│
└── Models/                                      ← existing Models folder
    ├── MenuCategory.php
    ├── MenuItem.php
    ├── MenuItemIngredient.php
    ├── Table.php
    ├── Order.php
    ├── OrderItem.php
    └── BookingCharge.php

database/
├── migrations/
│   ├── xxxx_create_menu_categories_table.php
│   ├── xxxx_create_menu_items_table.php
│   ├── xxxx_create_menu_item_ingredients_table.php
│   ├── xxxx_create_tables_table.php
│   ├── xxxx_create_orders_table.php
│   ├── xxxx_create_order_items_table.php
│   └── xxxx_create_booking_charges_table.php
│
└── seeders/
    └── MenuCategorySeeder.php

resources/
└── views/
    └── restaurant/                              ← new subfolder inside existing views/
        ├── layout.blade.php
        ├── menu/
        │   ├── categories.blade.php
        │   ├── items/
        │   │   ├── index.blade.php
        │   │   ├── create.blade.php
        │   │   └── edit.blade.php
        ├── tables/
        │   └── index.blade.php
        ├── orders/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── show.blade.php
        └── reports/
            ├── daily-sales.blade.php
            └── popular-items.blade.php

routes/
└── web.php                                      ← add restaurant routes here
```

---

## 3. Migrations

---

**File:** `database/migrations/xxxx_create_menu_categories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);                    // Drinks, Food, Cocktails, Desserts
            $table->uuid('location_id');                    // bar or kitchen location
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('stock_locations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_categories');
    }
};
```

---

**File:** `database/migrations/xxxx_create_menu_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('selling_price', 10, 2);        // what the customer pays
            $table->boolean('is_available')->default(true); // toggled off when out of stock
            $table->boolean('is_active')->default(true);    // soft delete
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('menu_categories');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
```

---

**File:** `database/migrations/xxxx_create_menu_item_ingredients_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Recipe table: which products are consumed when this menu item is sold
        // Optional — not every menu item needs a recipe
        Schema::create('menu_item_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_item_id');
            $table->uuid('product_id');                     // from products table (store module)
            $table->decimal('quantity', 10, 4);             // e.g. 0.0500 = 50ml
            $table->string('unit', 30);                     // ml, g, piece, litre
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['menu_item_id', 'product_id']); // one row per ingredient per item
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_ingredients');
    }
};
```

---

**File:** `database/migrations/xxxx_create_tables_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id');                    // bar or kitchen/restaurant location
            $table->string('table_number', 20);             // T1, B3, R7, etc.
            $table->integer('capacity')->default(4);
            $table->enum('status', [
                'available',
                'occupied',
                'reserved',
                'cleaning',
            ])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->unique(['location_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
```

---

**File:** `database/migrations/xxxx_create_orders_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 30)->unique();   // e.g. BAR-2024-0042
            $table->uuid('location_id');                    // bar or kitchen location
            $table->uuid('table_id')->nullable();           // nullable for takeaway/room service
            $table->enum('order_type', [
                'guest',    // hotel guest — charge to booking
                'walkin',   // walk-in customer — pay immediately
            ]);
            $table->uuid('booking_id')->nullable();         // FK to bookings if order_type = guest
            $table->string('customer_name', 150)->nullable(); // for walk-in customers
            $table->enum('status', [
                'open',     // order is being built
                'sent',     // sent to bar/kitchen
                'ready',    // prepared and ready to serve
                'served',   // delivered to customer
                'settled',  // payment done / charged to booking
                'cancelled',
            ])->default('open');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', [
                'cash',
                'card',
                'charge_to_booking',
            ])->nullable();
            $table->text('notes')->nullable();
            $table->uuid('created_by');                     // BAR_TENDER or CASHIER
            $table->uuid('settled_by')->nullable();         // CASHIER or FRONT_DESK
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

---

**File:** `database/migrations/xxxx_create_order_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('menu_item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);           // price snapshot at time of order
            $table->decimal('subtotal', 10, 2);             // quantity * unit_price
            $table->text('notes')->nullable();              // special instructions e.g. "no ice"
            $table->enum('status', [
                'pending',
                'preparing',
                'ready',
                'served',
                'cancelled',
            ])->default('pending');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('menu_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

---

**File:** `database/migrations/xxxx_create_booking_charges_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Charges linked to a hotel booking — guest pays at checkout
        Schema::create('booking_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');                     // FK to your bookings table
            $table->uuid('order_id')->nullable();           // the restaurant order
            $table->enum('source', [
                'restaurant',   // food or drinks from bar/restaurant
                'store',        // item from main store
                'room_service', // future use
            ])->default('restaurant');
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->boolean('is_settled')->default(false);  // true when guest checks out
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['booking_id', 'is_settled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_charges');
    }
};
```

---

## 4. Seeders

**File:** `database/seeders/MenuCategorySeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $bar     = StockLocation::where('code', 'bar')->first();
        $kitchen = StockLocation::where('code', 'kitchen')->first();

        if (!$bar || !$kitchen) {
            $this->command->warn('Run StockLocationSeeder first.');
            return;
        }

        $categories = [
            // Bar categories
            ['name' => 'Spirits',    'location_id' => $bar->id,     'description' => 'Whisky, vodka, gin, rum'],
            ['name' => 'Beers',      'location_id' => $bar->id,     'description' => 'Bottled and draft beers'],
            ['name' => 'Cocktails',  'location_id' => $bar->id,     'description' => 'Mixed drinks and cocktails'],
            ['name' => 'Soft Drinks','location_id' => $bar->id,     'description' => 'Non-alcoholic beverages'],
            ['name' => 'Wines',      'location_id' => $bar->id,     'description' => 'Red, white, and sparkling wines'],
            // Kitchen/restaurant categories
            ['name' => 'Starters',  'location_id' => $kitchen->id, 'description' => 'Soups and starters'],
            ['name' => 'Mains',     'location_id' => $kitchen->id, 'description' => 'Main course meals'],
            ['name' => 'Grills',    'location_id' => $kitchen->id, 'description' => 'Grilled meats and fish'],
            ['name' => 'Desserts',  'location_id' => $kitchen->id, 'description' => 'Desserts and sweets'],
            ['name' => 'Breakfast', 'location_id' => $kitchen->id, 'description' => 'Breakfast menu items'],
        ];

        foreach ($categories as $cat) {
            MenuCategory::updateOrCreate(
                ['name' => $cat['name'], 'location_id' => $cat['location_id']],
                $cat
            );
        }
    }
}
```

Add to `DatabaseSeeder.php`:

```php
$this->call([
    RoleSeeder::class,
    StockLocationSeeder::class,
    SystemSettingsSeeder::class,
    MenuCategorySeeder::class,   // ← add this
]);
```

---

## 5. Models

**File:** `app/Models/MenuCategory.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'location_id', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function menuItems() { return $this->hasMany(MenuItem::class, 'category_id'); }
}
```

---

**File:** `app/Models/MenuItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id', 'name', 'description',
        'selling_price', 'is_available', 'is_active', 'created_by',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_available'  => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function category()    { return $this->belongsTo(MenuCategory::class, 'category_id'); }
    public function ingredients() { return $this->hasMany(MenuItemIngredient::class); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }

    // Check if all ingredients have enough stock in their location
    public function hasEnoughStock(int $qty = 1): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $locationId = $this->category->location_id;
            $level = StockLevel::where('product_id', $ingredient->product_id)
                               ->where('location_id', $locationId)
                               ->first();

            if (!$level || $level->available_qty < ($ingredient->quantity * $qty)) {
                return false;
            }
        }
        return true;
    }
}
```

---

**File:** `app/Models/MenuItemIngredient.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    use HasUuids;

    protected $fillable = ['menu_item_id', 'product_id', 'quantity', 'unit'];

    protected $casts = ['quantity' => 'decimal:4'];

    public function menuItem() { return $this->belongsTo(MenuItem::class); }
    public function product()  { return $this->belongsTo(Product::class); }
}
```

---

**File:** `app/Models/Table.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasUuids;

    protected $fillable = ['location_id', 'table_number', 'capacity', 'status', 'is_active'];

    public function location()     { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function activeOrder()  { return $this->hasOne(Order::class)->whereNotIn('status', ['settled', 'cancelled']); }
}
```

---

**File:** `app/Models/Order.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number', 'location_id', 'table_id', 'order_type',
        'booking_id', 'customer_name', 'status',
        'subtotal', 'discount', 'tax', 'total',
        'payment_method', 'notes', 'created_by', 'settled_by', 'settled_at',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'discount'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'total'      => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    // Auto-generate order number before creating
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $location = StockLocation::find($order->location_id);
            $prefix   = strtoupper(substr($location->code, 0, 3)); // BAR or KIT
            $count    = self::whereDate('created_at', today())->count() + 1;
            $order->order_number = $prefix . '-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // Recalculate totals from order items
    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount + $this->tax,
        ]);
    }

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function table()     { return $this->belongsTo(Table::class); }
    public function items()     { return $this->hasMany(OrderItem::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function settler()   { return $this->belongsTo(User::class, 'settled_by'); }
    public function charge()    { return $this->hasOne(BookingCharge::class); }
}
```

---

**File:** `app/Models/OrderItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id', 'menu_item_id', 'quantity', 'unit_price', 'subtotal', 'notes', 'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function order()    { return $this->belongsTo(Order::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}
```

---

**File:** `app/Models/BookingCharge.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BookingCharge extends Model
{
    use HasUuids;

    protected $fillable = [
        'booking_id', 'order_id', 'source',
        'description', 'amount', 'is_settled', 'created_by',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'is_settled'  => 'boolean',
    ];

    public function order()     { return $this->belongsTo(Order::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
```

---

## 6. Controllers

---

**File:** `app/Http/Controllers/Restaurant/MenuItemController.php`

```php
<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemIngredient;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    // GET /restaurant/menu
    public function index(Request $request): View
    {
        $categories = MenuCategory::with(['menuItems' => function ($q) {
            $q->where('is_active', true)->with('ingredients.product');
        }, 'location'])
            ->where('is_active', true)
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->get();

        return view('restaurant.menu.items.index', compact('categories'));
    }

    // GET /restaurant/menu/create
    public function create(): View
    {
        $categories = MenuCategory::with('location')->where('is_active', true)->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();

        return view('restaurant.menu.items.create', compact('categories', 'products'));
    }

    // POST /restaurant/menu
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id'            => 'required|uuid|exists:menu_categories,id',
            'name'                   => 'required|string|max:150',
            'description'            => 'nullable|string',
            'selling_price'          => 'required|numeric|min:0.01',
            // ingredients are optional
            'ingredients'            => 'nullable|array',
            'ingredients.*.product_id' => 'required_with:ingredients|uuid|exists:products,id',
            'ingredients.*.quantity'   => 'required_with:ingredients|numeric|min:0.0001',
            'ingredients.*.unit'       => 'required_with:ingredients|string|max:30',
        ]);

        DB::transaction(function () use ($data, $request) {
            $item = MenuItem::create([
                'category_id'   => $data['category_id'],
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'selling_price' => $data['selling_price'],
                'is_available'  => true,
                'created_by'    => auth()->id(),
            ]);

            if (!empty($data['ingredients'])) {
                foreach ($data['ingredients'] as $ing) {
                    MenuItemIngredient::create([
                        'menu_item_id' => $item->id,
                        'product_id'   => $ing['product_id'],
                        'quantity'     => $ing['quantity'],
                        'unit'         => $ing['unit'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', "Menu item '{$data['name']}' created.");
    }

    // GET /restaurant/menu/{menuItem}/edit
    public function edit(MenuItem $menuItem): View
    {
        $categories = MenuCategory::with('location')->where('is_active', true)->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $menuItem->load('ingredients.product');

        return view('restaurant.menu.items.edit', compact('menuItem', 'categories', 'products'));
    }

    // PUT /restaurant/menu/{menuItem}
    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'name'                     => 'sometimes|string|max:150',
            'description'              => 'sometimes|nullable|string',
            'selling_price'            => 'sometimes|numeric|min:0.01',
            'is_available'             => 'sometimes|boolean',
            'ingredients'              => 'nullable|array',
            'ingredients.*.product_id' => 'required_with:ingredients|uuid|exists:products,id',
            'ingredients.*.quantity'   => 'required_with:ingredients|numeric|min:0.0001',
            'ingredients.*.unit'       => 'required_with:ingredients|string|max:30',
        ]);

        DB::transaction(function () use ($data, $menuItem) {
            $menuItem->update([
                'name'          => $data['name']          ?? $menuItem->name,
                'description'   => $data['description']   ?? $menuItem->description,
                'selling_price' => $data['selling_price'] ?? $menuItem->selling_price,
                'is_available'  => $data['is_available']  ?? $menuItem->is_available,
            ]);

            // Replace ingredients entirely if provided
            if (isset($data['ingredients'])) {
                $menuItem->ingredients()->delete();
                foreach ($data['ingredients'] as $ing) {
                    MenuItemIngredient::create([
                        'menu_item_id' => $menuItem->id,
                        'product_id'   => $ing['product_id'],
                        'quantity'     => $ing['quantity'],
                        'unit'         => $ing['unit'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', 'Menu item updated.');
    }

    // DELETE /restaurant/menu/{menuItem}
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_active' => false]);

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', 'Menu item removed from menu.');
    }
}
```

---

**File:** `app/Http/Controllers/Restaurant/TableController.php`

```php
<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    // GET /restaurant/tables
    public function index(Request $request): View
    {
        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        $tables = Table::with(['location', 'activeOrder'])
            ->where('is_active', true)
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->orderBy('table_number')
            ->get();

        return view('restaurant.tables.index', compact('tables', 'locations'));
    }

    // POST /restaurant/tables
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_id'  => 'required|uuid|exists:stock_locations,id',
            'table_number' => 'required|string|max:20',
            'capacity'     => 'required|integer|min:1|max:20',
        ]);

        Table::create([...$data, 'status' => 'available']);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$data['table_number']} added.");
    }

    // POST /restaurant/tables/{table}/status
    public function updateStatus(Request $request, Table $table): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,cleaning',
        ]);

        $table->update(['status' => $request->status]);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$table->table_number} marked as {$request->status}.");
    }
}
```

---

**File:** `app/Http/Controllers/Restaurant/OrderController.php`

```php
<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    // GET /restaurant/orders
    public function index(Request $request): View
    {
        $orders = Order::with(['table', 'location', 'items', 'creator'])
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->when($request->date,        fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(30);

        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        return view('restaurant.orders.index', compact('orders', 'locations'));
    }

    // GET /restaurant/orders/create
    public function create(Request $request): View
    {
        $locations  = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();
        $tables     = Table::where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->get();
        $categories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->where('is_available', true)])
                          ->where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->get();

        return view('restaurant.orders.create', compact('locations', 'tables', 'categories'));
    }

    // POST /restaurant/orders
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_id'   => 'required|uuid|exists:stock_locations,id',
            'table_id'      => 'nullable|uuid|exists:tables,id',
            'order_type'    => 'required|in:guest,walkin',
            'booking_id'    => 'required_if:order_type,guest|nullable|uuid',
            'customer_name' => 'required_if:order_type,walkin|nullable|string|max:150',
            'notes'         => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.notes'        => 'nullable|string|max:255',
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'location_id'   => $data['location_id'],
                'table_id'      => $data['table_id'] ?? null,
                'order_type'    => $data['order_type'],
                'booking_id'    => $data['booking_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'status'        => 'open',
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $menuItem->selling_price,  // snapshot price
                    'subtotal'     => $menuItem->selling_price * $item['quantity'],
                    'notes'        => $item['notes'] ?? null,
                    'status'       => 'pending',
                ]);
            }

            // Mark table as occupied
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            $order->recalculate();

            return $order;
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    // GET /restaurant/orders/{order}
    public function show(Order $order): View
    {
        $order->load(['items.menuItem.ingredients.product', 'table', 'location', 'creator', 'settler']);

        return view('restaurant.orders.show', compact('order'));
    }

    // POST /restaurant/orders/{order}/send
    // Mark order as sent to kitchen/bar
    public function send(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Only open orders can be sent.');

        $order->update(['status' => 'sent']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order sent to preparation.');
    }

    // POST /restaurant/orders/{order}/ready
    // Mark order as ready to serve
    public function ready(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'sent', 422, 'Order must be sent before marking ready.');

        $order->update(['status' => 'ready']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as ready.');
    }

    // POST /restaurant/orders/{order}/serve
    // Mark as served — no stock deduction yet
    public function serve(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'ready', 422, 'Order must be ready before serving.');

        $order->update(['status' => 'served']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as served.');
    }

    // POST /restaurant/orders/{order}/settle
    // Settle payment — this is where stock is deducted
    public function settle(Request $request, Order $order): RedirectResponse
    {
        abort_if(!in_array($order->status, ['served', 'ready', 'sent', 'open']), 422, 'Order cannot be settled.');
        abort_if($order->status === 'settled', 422, 'Order already settled.');

        $request->validate([
            'payment_method' => 'required|in:cash,card,charge_to_booking',
            'booking_id'     => 'required_if:payment_method,charge_to_booking|nullable|uuid',
        ]);

        DB::transaction(function () use ($request, $order) {

            // 1. Deduct stock for every order item that has ingredients
            $order->load('items.menuItem.ingredients');

            foreach ($order->items as $orderItem) {
                if ($orderItem->status === 'cancelled') continue;

                foreach ($orderItem->menuItem->ingredients as $ingredient) {
                    $deductQty = $ingredient->quantity * $orderItem->quantity;

                    StockMovement::record([
                        'product_id'     => $ingredient->product_id,
                        'location_id'    => $order->location_id,
                        'type'           => 'recipe_use',
                        'quantity'       => $deductQty,
                        'reference_type' => 'order',
                        'reference_id'   => $order->id,
                        'notes'          => "Sold: {$orderItem->menuItem->name} × {$orderItem->quantity} | Order {$order->order_number}",
                    ], auth()->id());
                }
            }

            // 2. Mark order as settled
            $order->update([
                'status'         => 'settled',
                'payment_method' => $request->payment_method,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
                'booking_id'     => $request->booking_id ?? $order->booking_id,
            ]);

            // 3. If charge to booking — create booking charge record
            if ($request->payment_method === 'charge_to_booking') {
                $bookingId = $request->booking_id ?? $order->booking_id;

                abort_if(!$bookingId, 422, 'Booking ID is required for guest charges.');

                BookingCharge::create([
                    'booking_id'  => $bookingId,
                    'order_id'    => $order->id,
                    'source'      => 'restaurant',
                    'description' => "Order {$order->order_number} — {$order->items->count()} item(s)",
                    'amount'      => $order->total,
                    'is_settled'  => false,     // settled when guest checks out
                    'created_by'  => auth()->id(),
                ]);
            }

            // 4. Free up the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "Order {$order->order_number} settled successfully.");
    }

    // POST /restaurant/orders/{order}/cancel
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->status === 'settled', 422, 'Cannot cancel a settled order.');

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Free the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        return redirect()
            ->route('restaurant.orders.index')
            ->with('success', "Order {$order->order_number} cancelled.");
    }

    // POST /restaurant/orders/{order}/items
    // Add item to an open order
    public function addItem(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only add items to open orders.');

        $request->validate([
            'menu_item_id' => 'required|uuid|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string|max:255',
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        DB::transaction(function () use ($request, $order, $menuItem) {
            // If item already in order, increase quantity
            $existing = $order->items()->where('menu_item_id', $menuItem->id)->first();

            if ($existing) {
                $existing->update([
                    'quantity' => $existing->quantity + $request->quantity,
                    'subtotal' => $menuItem->selling_price * ($existing->quantity + $request->quantity),
                ]);
            } else {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $request->quantity,
                    'unit_price'   => $menuItem->selling_price,
                    'subtotal'     => $menuItem->selling_price * $request->quantity,
                    'notes'        => $request->notes,
                    'status'       => 'pending',
                ]);
            }

            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "{$menuItem->name} added to order.");
    }

    // DELETE /restaurant/orders/{order}/items/{orderItem}
    // Remove item from open order
    public function removeItem(Order $order, OrderItem $orderItem): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only remove items from open orders.');

        DB::transaction(function () use ($order, $orderItem) {
            $orderItem->update(['status' => 'cancelled']);
            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Item removed from order.');
    }
}
```

---

**File:** `app/Http/Controllers/Restaurant/ReportController.php`

```php
<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    // GET /restaurant/reports/daily-sales
    public function dailySales(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $orders = Order::with(['items.menuItem', 'location', 'table'])
            ->where('status', 'settled')
            ->whereDate('settled_at', $date)
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total'),
            'cash_revenue'   => $orders->where('payment_method', 'cash')->sum('total'),
            'card_revenue'   => $orders->where('payment_method', 'card')->sum('total'),
            'guest_charges'  => $orders->where('payment_method', 'charge_to_booking')->sum('total'),
        ];

        return view('restaurant.reports.daily-sales', compact('orders', 'summary', 'date'));
    }

    // GET /restaurant/reports/popular-items
    public function popularItems(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        $items = OrderItem::with('menuItem.category')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'settled')
            ->where('order_items.status', '!=', 'cancelled')
            ->whereDate('orders.settled_at', '>=', $dateFrom)
            ->whereDate('orders.settled_at', '<=', $dateTo)
            ->select([
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            ])
            ->groupBy('order_items.menu_item_id')
            ->orderByDesc('total_qty')
            ->take(20)
            ->get();

        return view('restaurant.reports.popular-items', compact('items', 'dateFrom', 'dateTo'));
    }
}
```

---

## 7. Blade Views

---

**File:** `resources/views/restaurant/layout.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurant') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">🍸🍽️ Bar & Restaurant</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('restaurant.orders.index') }}"  class="text-gray-600 hover:text-blue-600">Orders</a>
        <a href="{{ route('restaurant.orders.create') }}" class="text-gray-600 hover:text-blue-600">New Order</a>
        <a href="{{ route('restaurant.tables.index') }}"  class="text-gray-600 hover:text-blue-600">Tables</a>
        @if(auth()->user()->hasRole('RESTAURANT_MANAGER'))
        <a href="{{ route('restaurant.menu.index') }}"    class="text-gray-600 hover:text-blue-600">Menu</a>
        <a href="{{ route('restaurant.reports.daily-sales') }}" class="text-gray-600 hover:text-blue-600">Reports</a>
        @endif
    </div>
    <div class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ auth()->user()->role->name }}</div>
</nav>

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
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>
```

---

**File:** `resources/views/restaurant/orders/index.blade.php`

```blade
@extends('restaurant.layout')

@section('title', 'Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
    <a href="{{ route('restaurant.orders.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        + New Order
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6 flex-wrap">
    <select name="location_id" class="border rounded px-3 py-2 text-sm">
        <option value="">All Sections</option>
        @foreach($locations as $loc)
        <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>
            {{ $loc->name }}
        </option>
        @endforeach
    </select>
    @foreach(['','open','sent','ready','served','settled','cancelled'] as $s)
    <a href="{{ route('restaurant.orders.index', array_merge(request()->query(), $s ? ['status' => $s] : ['status' => ''])) }}"
       class="px-3 py-1.5 rounded text-sm border
              {{ request('status') === $s || (empty($s) && !request('status'))
                 ? 'bg-blue-600 text-white border-blue-600'
                 : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $s ? ucfirst($s) : 'All' }}
    </a>
    @endforeach
    <input type="date" name="date" value="{{ request('date') }}"
           class="border rounded px-3 py-2 text-sm">
    <button class="bg-gray-200 px-4 py-2 rounded text-sm hover:bg-gray-300">Filter</button>
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Order #</th>
                <th class="px-4 py-3 text-left text-gray-600">Section</th>
                <th class="px-4 py-3 text-left text-gray-600">Table</th>
                <th class="px-4 py-3 text-left text-gray-600">Type</th>
                <th class="px-4 py-3 text-right text-gray-600">Total</th>
                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                <th class="px-4 py-3 text-left text-gray-600">Time</th>
                <th class="px-4 py-3 text-center text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-medium">{{ $order->order_number }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $order->location->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $order->table?->table_number ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $order->order_type === 'guest' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($order->order_type) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-medium">{{ number_format($order->total, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($order->status === 'open')     bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'sent') bg-blue-100 text-blue-700
                        @elseif($order->status === 'ready') bg-orange-100 text-orange-700
                        @elseif($order->status === 'served') bg-indigo-100 text-indigo-700
                        @elseif($order->status === 'settled') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $order->created_at->format('H:i') }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('restaurant.orders.show', $order) }}"
                       class="text-blue-600 hover:underline text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
```

---

**File:** `resources/views/restaurant/orders/show.blade.php`

```blade
@extends('restaurant.layout')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Order {{ $order->order_number }}</h1>
        <p class="text-sm text-gray-400 mt-1">
            {{ $order->location->name }}
            @if($order->table) · Table {{ $order->table->table_number }} @endif
            · {{ $order->order_type === 'guest' ? 'Hotel Guest' : 'Walk-in Customer' }}
            @if($order->customer_name) · {{ $order->customer_name }} @endif
        </p>
    </div>

    {{-- Status badge --}}
    <span class="px-3 py-1.5 rounded-full text-sm font-medium
        @if($order->status === 'open')     bg-yellow-100 text-yellow-700
        @elseif($order->status === 'sent') bg-blue-100 text-blue-700
        @elseif($order->status === 'ready') bg-orange-100 text-orange-700
        @elseif($order->status === 'served') bg-indigo-100 text-indigo-700
        @elseif($order->status === 'settled') bg-green-100 text-green-700
        @else bg-gray-100 text-gray-500 @endif">
        {{ ucfirst($order->status) }}
    </span>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Order items --}}
    <div class="col-span-2">
        <div class="bg-white rounded shadow overflow-hidden mb-4">
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700">Items</h2>
                @if($order->status === 'open')
                <a href="#add-item" class="text-blue-600 text-sm hover:underline">+ Add Item</a>
                @endif
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500">Item</th>
                        <th class="px-4 py-2 text-right text-gray-500">Qty</th>
                        <th class="px-4 py-2 text-right text-gray-500">Price</th>
                        <th class="px-4 py-2 text-right text-gray-500">Subtotal</th>
                        <th class="px-4 py-2 text-center text-gray-500">Status</th>
                        @if($order->status === 'open')
                        <th class="px-4 py-2"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <tr class="{{ $item->status === 'cancelled' ? 'opacity-40 line-through' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->menuItem->name }}</div>
                            @if($item->notes)
                            <div class="text-xs text-gray-400">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($item->subtotal, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded {{ $item->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        @if($order->status === 'open' && $item->status !== 'cancelled')
                        <td class="px-4 py-3 text-center">
                            <form method="POST"
                                  action="{{ route('restaurant.orders.remove-item', [$order, $item]) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-xs hover:underline">Remove</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t bg-gray-50">
                    <tr>
                        <td colspan="{{ $order->status === 'open' ? 3 : 3 }}" class="px-4 py-2 text-right text-gray-500">Total</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">{{ number_format($order->total, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Workflow actions --}}
        @if($order->status !== 'settled' && $order->status !== 'cancelled')
        <div class="flex gap-3 flex-wrap">
            @if($order->status === 'open')
            <form method="POST" action="{{ route('restaurant.orders.send', $order) }}">
                @csrf
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Send to Kitchen/Bar
                </button>
            </form>
            @endif

            @if($order->status === 'sent' && auth()->user()->hasAnyRole(['RESTAURANT_MANAGER', 'BAR_TENDER']))
            <form method="POST" action="{{ route('restaurant.orders.ready', $order) }}">
                @csrf
                <button class="bg-orange-500 text-white px-4 py-2 rounded text-sm hover:bg-orange-600">
                    Mark Ready
                </button>
            </form>
            @endif

            @if($order->status === 'ready')
            <form method="POST" action="{{ route('restaurant.orders.serve', $order) }}">
                @csrf
                <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    Mark Served
                </button>
            </form>
            @endif

            @if(auth()->user()->hasAnyRole(['CASHIER', 'FRONT_DESK', 'BAR_TENDER', 'RESTAURANT_MANAGER']))
            <button onclick="document.getElementById('settle-form').classList.toggle('hidden')"
                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                Settle Order
            </button>
            @endif

            <form method="POST" action="{{ route('restaurant.orders.cancel', $order) }}"
                  onsubmit="return confirm('Cancel this order?')">
                @csrf
                <button class="bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">
                    Cancel
                </button>
            </form>
        </div>

        {{-- Settle form --}}
        <div id="settle-form" class="hidden mt-4 bg-white rounded shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Settle Payment</h3>
            <form method="POST" action="{{ route('restaurant.orders.settle', $order) }}">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" id="payment_method"
                                class="w-full border rounded px-3 py-2 text-sm"
                                onchange="document.getElementById('booking-field').classList.toggle('hidden', this.value !== 'charge_to_booking')">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="charge_to_booking" {{ $order->order_type === 'guest' ? 'selected' : '' }}>
                                Charge to Booking
                            </option>
                        </select>
                    </div>
                    <div id="booking-field" class="{{ $order->order_type === 'guest' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID</label>
                        <input type="text" name="booking_id" value="{{ $order->booking_id }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="mt-4 bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 text-sm">
                    Confirm Settlement — {{ number_format($order->total, 2) }}
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Order summary --}}
    <div class="space-y-4">
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Summary</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Items</dt>
                    <dd>{{ $order->items->where('status', '!=', 'cancelled')->count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Subtotal</dt>
                    <dd>{{ number_format($order->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Discount</dt>
                    <dd>{{ number_format($order->discount, 2) }}</dd>
                </div>
                <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                    <dt>Total</dt>
                    <dd>{{ number_format($order->total, 2) }}</dd>
                </div>
                @if($order->status === 'settled')
                <div class="flex justify-between text-green-700">
                    <dt>Payment</dt>
                    <dd>{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</dd>
                </div>
                <div class="flex justify-between text-gray-400 text-xs">
                    <dt>Settled at</dt>
                    <dd>{{ $order->settled_at?->format('H:i d M') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Order Info</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Created by</dt>
                    <dd>{{ $order->creator->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Created at</dt>
                    <dd>{{ $order->created_at->format('H:i d M Y') }}</dd>
                </div>
                @if($order->notes)
                <div>
                    <dt class="text-gray-500">Notes</dt>
                    <dd class="mt-1 text-gray-700">{{ $order->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection
```

---

**File:** `resources/views/restaurant/tables/index.blade.php`

```blade
@extends('restaurant.layout')

@section('title', 'Tables')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tables</h1>
</div>

{{-- Location tabs --}}
<div class="flex gap-3 mb-6">
    @foreach($locations as $loc)
    <a href="{{ route('restaurant.tables.index', ['location_id' => $loc->id]) }}"
       class="px-4 py-2 rounded text-sm border
              {{ request('location_id') === $loc->id
                 ? 'bg-blue-600 text-white border-blue-600'
                 : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $loc->name }}
    </a>
    @endforeach
</div>

{{-- Table grid --}}
<div class="grid grid-cols-4 gap-4">
    @forelse($tables as $table)
    <div class="bg-white rounded shadow p-4
                @if($table->status === 'available')   border-l-4 border-green-400
                @elseif($table->status === 'occupied') border-l-4 border-red-400
                @elseif($table->status === 'reserved') border-l-4 border-yellow-400
                @else border-l-4 border-gray-300 @endif">

        <div class="flex justify-between items-start mb-2">
            <span class="font-bold text-lg text-gray-800">{{ $table->table_number }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full
                @if($table->status === 'available')   bg-green-100 text-green-700
                @elseif($table->status === 'occupied') bg-red-100 text-red-600
                @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-700
                @else bg-gray-100 text-gray-500 @endif">
                {{ ucfirst($table->status) }}
            </span>
        </div>

        <p class="text-xs text-gray-400 mb-3">{{ $table->location->name }} · {{ $table->capacity }} seats</p>

        @if($table->activeOrder)
        <a href="{{ route('restaurant.orders.show', $table->activeOrder) }}"
           class="block text-center text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded hover:bg-blue-100 mb-2">
            View Order {{ $table->activeOrder->order_number }}
        </a>
        @else
        <a href="{{ route('restaurant.orders.create', ['table_id' => $table->id, 'location_id' => $table->location_id]) }}"
           class="block text-center text-xs bg-green-50 text-green-600 px-3 py-1.5 rounded hover:bg-green-100 mb-2">
            + New Order
        </a>
        @endif

        {{-- Quick status change --}}
        <form method="POST" action="{{ route('restaurant.tables.status', $table) }}">
            @csrf
            <select name="status" onchange="this.form.submit()"
                    class="w-full text-xs border rounded px-2 py-1 text-gray-500">
                @foreach(['available','occupied','reserved','cleaning'] as $s)
                <option value="{{ $s }}" {{ $table->status === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    @empty
    <div class="col-span-4 text-center py-12 text-gray-400">
        No tables found for this section.
    </div>
    @endforelse
</div>
@endsection
```

---

## 8. Routes

**File:** `routes/web.php` — add inside your existing `auth` middleware group.

```php
use App\Http\Controllers\Restaurant\MenuItemController;
use App\Http\Controllers\Restaurant\TableController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\Restaurant\ReportController as RestaurantReportController;

Route::middleware(['auth'])->prefix('restaurant')->name('restaurant.')->group(function () {

    // ── Menu ─────────────────────────────────────────────────────────────────
    Route::get('menu',                 [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('menu/create',          [MenuItemController::class, 'create'])->name('menu.create')
         ->middleware('role:RESTAURANT_MANAGER');
    Route::post('menu',                [MenuItemController::class, 'store'])->name('menu.store')
         ->middleware('role:RESTAURANT_MANAGER');
    Route::get('menu/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('menu.edit')
         ->middleware('role:RESTAURANT_MANAGER');
    Route::put('menu/{menuItem}',      [MenuItemController::class, 'update'])->name('menu.update')
         ->middleware('role:RESTAURANT_MANAGER');
    Route::delete('menu/{menuItem}',   [MenuItemController::class, 'destroy'])->name('menu.destroy')
         ->middleware('role:RESTAURANT_MANAGER');

    // ── Tables ────────────────────────────────────────────────────────────────
    Route::get('tables',                      [TableController::class, 'index'])->name('tables.index');
    Route::post('tables',                     [TableController::class, 'store'])->name('tables.store')
         ->middleware('role:RESTAURANT_MANAGER');
    Route::post('tables/{table}/status',      [TableController::class, 'updateStatus'])->name('tables.status');

    // ── Orders ────────────────────────────────────────────────────────────────
    Route::get('orders',                           [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create',                    [OrderController::class, 'create'])->name('orders.create')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');
    Route::post('orders',                          [OrderController::class, 'store'])->name('orders.store')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');
    Route::get('orders/{order}',                   [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/send',             [OrderController::class, 'send'])->name('orders.send')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');
    Route::post('orders/{order}/ready',            [OrderController::class, 'ready'])->name('orders.ready')
         ->middleware('role:BAR_TENDER,RESTAURANT_MANAGER');
    Route::post('orders/{order}/serve',            [OrderController::class, 'serve'])->name('orders.serve')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');
    Route::post('orders/{order}/settle',           [OrderController::class, 'settle'])->name('orders.settle')
         ->middleware('role:CASHIER,FRONT_DESK,BAR_TENDER,RESTAURANT_MANAGER');
    Route::post('orders/{order}/cancel',           [OrderController::class, 'cancel'])->name('orders.cancel')
         ->middleware('role:CASHIER,RESTAURANT_MANAGER');
    Route::post('orders/{order}/items',            [OrderController::class, 'addItem'])->name('orders.add-item')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');
    Route::delete('orders/{order}/items/{orderItem}', [OrderController::class, 'removeItem'])->name('orders.remove-item')
         ->middleware('role:BAR_TENDER,CASHIER,RESTAURANT_MANAGER');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('reports/daily-sales',  [RestaurantReportController::class, 'dailySales'])->name('reports.daily-sales')
         ->middleware('role:RESTAURANT_MANAGER,CASHIER');
    Route::get('reports/popular-items',[RestaurantReportController::class, 'popularItems'])->name('reports.popular-items')
         ->middleware('role:RESTAURANT_MANAGER');

});
```

---

## 9. Business Rules

| # | Rule | Where Enforced |
|---|---|---|
| 001 | Stock deducted on settlement only — not on order creation | `OrderController::settle()` calls `StockMovement::record()` |
| 002 | All stock deductions still go through `StockMovement::record()` | Never bypass the store module |
| 003 | Both stock deduction and booking charge in one transaction | `settle()` wraps everything in `DB::transaction()` |
| 004 | Price snapshot at time of order — menu price changes don't affect open orders | `unit_price` stored on `order_items` at creation |
| 005 | Cannot add items to a non-open order | `addItem()` checks `status === 'open'` |
| 006 | Cannot cancel a settled order | `cancel()` checks status |
| 007 | Guest orders create a `booking_charge` record | Created in `settle()` when `payment_method = charge_to_booking` |
| 008 | Menu items without ingredients still sell — no stock deduction | `ingredients` loop is skipped when collection is empty |
| 009 | Table freed automatically when order is settled or cancelled | `Table::update(['status' => 'available'])` in both paths |
| 010 | Menu items are soft deleted only | `destroy()` sets `is_active = false` |

---

## 10. Build Order

```bash
# Migrations
php artisan make:migration create_menu_categories_table
php artisan make:migration create_menu_items_table
php artisan make:migration create_menu_item_ingredients_table
php artisan make:migration create_tables_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_booking_charges_table
php artisan migrate

# Seed categories
php artisan db:seed --class=MenuCategorySeeder

# Build order (one at a time, test each before next)
# 1. MenuItemController  → views/restaurant/menu/   → routes
# 2. TableController     → views/restaurant/tables/ → routes
# 3. OrderController     → views/restaurant/orders/ → routes  ← most complex, test thoroughly
# 4. ReportController    → views/restaurant/reports/ → routes

# Critical test cases before going live:
# ✓ Create order with ingredients → settle → verify stock deducted at correct location
# ✓ Create guest order → settle as charge_to_booking → verify booking_charge row created
# ✓ Cancel order → verify table status returns to available
# ✓ Menu item with no ingredients → settle → no stock movement created (no crash)
# ✓ Try to add item to sent order → blocked
# ✓ Try to cancel settled order → blocked
```
