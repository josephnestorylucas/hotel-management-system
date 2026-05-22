# Task 05 — Separate Bar and Kitchen Modules

## Problem
Bar and Kitchen are currently mixed together. They must be completely separated — different queues, different screens, different item destinations, different staff access.

---

## Separation Principle

```
menu_items.destination = 'kitchen'  →  KOT → Kitchen Screen
menu_items.destination = 'bar'      →  BOT → Bar Screen
```

Kitchen staff NEVER see bar tickets.  
Bartenders NEVER see kitchen tickets.

---

## Step 1 — Add Destination Field to menu_items

```bash
php artisan make:migration add_destination_to_menu_items_table
```

```php
Schema::table('menu_items', function (Blueprint $table) {
    $table->enum('destination', ['kitchen', 'bar'])
          ->default('kitchen')
          ->after('is_buffet');
});
```

- [ ] Migration created and run
- [ ] All existing items defaulted to `kitchen`
- [ ] Bar items (drinks, alcohol, cocktails) updated to `bar`

### Update Existing Bar Items
```sql
UPDATE menu_items
SET destination = 'bar'
WHERE category_id IN (
    SELECT id FROM menu_categories
    WHERE name IN ('Drinks', 'Alcohol', 'Cocktails', 'Beer', 'Juice', 'Soda')
);
```

- [ ] Bar items updated in DB
- [ ] Kitchen items confirmed as `kitchen`

---

## Step 2 — Menu Item Form (Admin) — Show Destination Selector

```blade
{{-- resources/views/menu_items/create.blade.php --}}

<div class="form-group">
    <label class="font-semibold">Prepared By</label>
    <div class="flex gap-4 mt-2">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="destination" value="kitchen" checked>
            <span>🍳 Kitchen</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="destination" value="bar">
            <span>🍺 Bar</span>
        </label>
    </div>
    <p class="text-xs text-gray-500 mt-1">
        Where should this item be prepared?
    </p>
</div>
```

- [ ] Destination radio buttons on menu item create/edit form
- [ ] Default: Kitchen
- [ ] Saved correctly to `menu_items.destination`

---

## Step 3 — Kitchen Screen (KOT Queue)

### Route
```php
Route::middleware(['auth', 'role:kitchen_staff,supervisor,admin'])
    ->prefix('kitchen')
    ->group(function () {
        Route::get('/queue', [KitchenController::class, 'queue'])->name('kitchen.queue');
        Route::post('/tickets/{ticket}/preparing', [KitchenController::class, 'markPreparing']);
        Route::post('/tickets/{ticket}/ready', [KitchenController::class, 'markReady']);
    });
```

### Controller
```php
// app/Http/Controllers/KitchenController.php

public function queue()
{
    $tickets = KitchenTicket::with('order.table')
        ->whereIn('status', ['pending', 'preparing'])
        ->orderBy('created_at', 'asc') // oldest first
        ->get();

    return view('kitchen.queue', compact('tickets'));
}

public function markPreparing(KitchenTicket $ticket)
{
    $ticket->update([
        'status'        => 'preparing',
        'started_at'    => now(),
    ]);
    return back()->with('success', 'Marked as preparing.');
}

public function markReady(KitchenTicket $ticket)
{
    $ticket->update([
        'status'    => 'ready',
        'ready_at'  => now(),
    ]);
    // TODO: notify waiter (future: websocket/sound)
    return back()->with('success', 'Order ready!');
}
```

### Kitchen Queue View
```blade
{{-- resources/views/kitchen/queue.blade.php --}}

<div class="min-h-screen bg-gray-900 text-white p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">🍳 Kitchen Queue</h1>
        <span class="text-gray-400">Auto-refreshes every 15s</span>
    </div>

    @if($tickets->isEmpty())
        <div class="text-center text-gray-500 py-20">
            <p class="text-5xl mb-4">✅</p>
            <p class="text-xl">No pending orders</p>
        </div>
    @else
        <div class="grid grid-cols-3 gap-4">
            @foreach($tickets as $ticket)
                <div class="rounded-lg p-4 border-2
                    {{ $ticket->status === 'pending' ? 'bg-red-900 border-red-500' : 'bg-yellow-900 border-yellow-500' }}">
                    
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-lg font-bold">
                                Table {{ $ticket->order->table->number ?? 'Takeaway' }}
                            </p>
                            <p class="text-xs text-gray-300">
                                {{ $ticket->created_at->format('H:i') }} 
                                ({{ $ticket->created_at->diffForHumans() }})
                            </p>
                        </div>
                        <span class="badge {{ $ticket->status === 'pending' ? 'badge-error' : 'badge-warning' }}">
                            {{ strtoupper($ticket->status) }}
                        </span>
                    </div>

                    <ul class="space-y-1 mb-4">
                        @foreach(json_decode($ticket->items) as $item)
                            <li class="text-sm">
                                {{ $item->quantity }}× {{ $item->name }}
                                @if($item->notes) <span class="text-yellow-300">({{ $item->notes }})</span> @endif
                            </li>
                        @endforeach
                    </ul>

                    @if($ticket->status === 'pending')
                        <form method="POST" action="{{ route('kitchen.preparing', $ticket) }}">
                            @csrf
                            <button class="w-full bg-yellow-500 text-black font-bold py-2 rounded">
                                Start Preparing
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('kitchen.ready', $ticket) }}">
                            @csrf
                            <button class="w-full bg-green-500 text-black font-bold py-2 rounded">
                                Mark Ready ✓
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Auto-refresh --}}
<script>
    setTimeout(() => window.location.reload(), 15000);
</script>
```

- [ ] Kitchen screen at `/kitchen/queue`
- [ ] Shows ONLY KOTs (kitchen tickets)
- [ ] Color coded: red = pending, yellow = preparing, green = ready
- [ ] Chef marks "Preparing" → "Ready"
- [ ] Auto-refreshes every 15 seconds
- [ ] Kitchen staff role cannot see bar screen

---

## Step 4 — Bar Screen (BOT Queue)

### Route
```php
Route::middleware(['auth', 'role:bartender,supervisor,admin'])
    ->prefix('bar')
    ->group(function () {
        Route::get('/queue', [BarController::class, 'queue'])->name('bar.queue');
        Route::get('/tabs', [BarController::class, 'tabs'])->name('bar.tabs');
        Route::post('/tickets/{ticket}/ready', [BarController::class, 'markReady']);
    });
```

### Controller
```php
// app/Http/Controllers/BarController.php

public function queue()
{
    $tickets = BarTicket::with('order.table')
        ->whereIn('status', ['pending', 'preparing'])
        ->orderBy('created_at', 'asc')
        ->get();

    return view('bar.queue', compact('tickets'));
}

public function markReady(BarTicket $ticket)
{
    $ticket->update([
        'status'   => 'ready',
        'ready_at' => now(),
    ]);
    return back()->with('success', 'Drinks ready!');
}
```

### Bar Queue View
```blade
{{-- resources/views/bar/queue.blade.php --}}
{{-- Same structure as kitchen but:
     - Title: 🍺 Bar Queue
     - Different color scheme (blue/indigo)
     - Bar items only shown
     - Simpler: items served immediately (no "preparing" step needed)
--}}
```

- [ ] Bar screen at `/bar/queue`
- [ ] Shows ONLY BOTs (bar tickets)
- [ ] Bartender marks "Ready" (bar is immediate — no preparing step)
- [ ] Auto-refreshes every 15 seconds
- [ ] Bartender role cannot see kitchen screen

---

## Step 5 — Separate Sidebar Links

```blade
{{-- Kitchen staff sidebar --}}
@if(auth()->user()->role === 'kitchen_staff')
    <li>
        <a href="{{ route('kitchen.queue') }}">🍳 Kitchen Queue</a>
    </li>
@endif

{{-- Bartender sidebar --}}
@if(auth()->user()->role === 'bartender')
    <li>
        <a href="{{ route('bar.queue') }}">🍺 Bar Queue</a>
    </li>
    <li>
        <a href="{{ route('bar.tabs') }}">📋 Bar Tabs</a>
    </li>
@endif
```

- [ ] Kitchen staff only sees Kitchen Queue link
- [ ] Bartender only sees Bar Queue + Bar Tabs links
- [ ] Supervisor/Admin sees both
- [ ] No cross-contamination of screens

---

## Step 6 — Order Dispatch Logic (Auto-Split)

```php
// app/Services/OrderDispatchService.php

class OrderDispatchService
{
    public function dispatch(Order $order): void
    {
        $items = $order->items()->with('menuItem')->get();

        $kitchenItems = $items->filter(fn($i) => $i->menuItem->destination === 'kitchen');
        $barItems     = $items->filter(fn($i) => $i->menuItem->destination === 'bar');

        if ($kitchenItems->isNotEmpty()) {
            KitchenTicket::create([
                'order_id'   => $order->id,
                'table_id'   => $order->table_id,
                'items'      => $kitchenItems->map(fn($i) => [
                    'name'     => $i->menuItem->name,
                    'quantity' => $i->quantity,
                    'notes'    => $i->notes,
                ])->toJson(),
                'status'     => 'pending',
                'printed_at' => now(),
            ]);
        }

        if ($barItems->isNotEmpty()) {
            BarTicket::create([
                'order_id'   => $order->id,
                'table_id'   => $order->table_id,
                'items'      => $barItems->map(fn($i) => [
                    'name'     => $i->menuItem->name,
                    'quantity' => $i->quantity,
                    'notes'    => $i->notes,
                ])->toJson(),
                'status'     => 'pending',
                'printed_at' => now(),
            ]);
        }
    }
}
```

- [ ] `OrderDispatchService` created
- [ ] Called after order is confirmed/submitted
- [ ] Kitchen items → KitchenTicket only
- [ ] Bar items → BarTicket only
- [ ] Items with mixed (one order has both) → split correctly

---

## Files to Create / Modify

| File | Action |
|------|--------|
| `database/migrations/..._add_destination_to_menu_items.php` | New migration |
| `app/Models/MenuItem.php` | Add `destination` to fillable |
| `app/Models/KitchenTicket.php` | Create model |
| `app/Models/BarTicket.php` | Create model |
| `app/Http/Controllers/KitchenController.php` | Create controller |
| `app/Http/Controllers/BarController.php` | Create controller |
| `app/Services/OrderDispatchService.php` | Create service |
| `resources/views/kitchen/queue.blade.php` | Create view |
| `resources/views/bar/queue.blade.php` | Create view |
| `resources/views/layouts/sidebar.blade.php` | Add role-based links |
| `routes/web.php` | Add kitchen + bar routes |

---

## Done When
- [ ] `menu_items.destination` column exists
- [ ] All bar items set to `destination = bar`
- [ ] All food items set to `destination = kitchen`
- [ ] KitchenTickets created with only kitchen items
- [ ] BarTickets created with only bar items
- [ ] Kitchen screen at `/kitchen/queue` — kitchen staff only
- [ ] Bar screen at `/bar/queue` — bartender only
- [ ] Sidebar links respect role
- [ ] Supervisor can see both screens
