# Task 04 — Fix Menu Selection & Remove Mixed Elements

## Problem
The menu selection on the POS screen has mixed/broken elements. Items from different categories or types (normal items, buffet, bar, kitchen) are displayed together without proper separation, causing confusion for cashiers.

---

## Expected Behavior
- Menu items are displayed **by category** (clean, separated)
- Buffet items are on their **own separate screen** — NOT mixed with regular items
- Bar items and Kitchen items filtered correctly per POS context
- Options (Beef/Chicken, Milk/Black) appear as a **popup** after item selection
- No duplicate or ghost items visible

---

## Root Cause Areas to Check

1. Category filter not applied on menu query
2. `is_active = false` items still showing
3. Buffet items mixed into the regular item grid
4. `available_from / available_to` time filter not enforced
5. Menu item types (normal, option-based, buffet) not separated in UI

---

## Fix Checklist

### Step 1 — Fix Menu Query (Controller)

```php
// app/Http/Controllers/PosController.php

public function index()
{
    // ONLY active, non-buffet items for regular POS
    $categories = MenuCategory::where('is_active', true)
        ->orderBy('display_order')
        ->with(['items' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_buffet', false) // ← EXCLUDE buffet
                  ->where(function ($q) {
                      // Time availability filter
                      $now = now()->format('H:i:s');
                      $q->whereNull('available_from')
                        ->orWhere(function ($inner) use ($now) {
                            $inner->where('available_from', '<=', $now)
                                  ->where('available_to', '>=', $now);
                        });
                  })
                  ->orderBy('name');
        }])
        ->get()
        ->filter(fn($cat) => $cat->items->count() > 0); // hide empty categories

    return view('pos.index', compact('categories'));
}
```

- [ ] `is_active = false` items excluded
- [ ] `is_buffet = true` items excluded from regular POS
- [ ] `available_from / available_to` time window enforced
- [ ] Empty categories hidden from view
- [ ] Items sorted by name within each category

---

### Step 2 — Fix POS Category Tabs UI

```blade
{{-- resources/views/pos/index.blade.php --}}

{{-- Category tabs --}}
<div class="flex gap-2 overflow-x-auto pb-2 border-b mb-4">
    @foreach($categories as $category)
        <button 
            class="tab-btn px-4 py-2 rounded whitespace-nowrap
                   {{ $loop->first ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
            data-category="{{ $category->id }}">
            {{ $category->name }}
        </button>
    @endforeach
</div>

{{-- Items grid per category --}}
@foreach($categories as $category)
    <div class="category-panel grid grid-cols-3 gap-3"
         id="cat-{{ $category->id }}"
         style="{{ $loop->first ? '' : 'display:none' }}">
        
        @foreach($category->items as $item)
            <div class="menu-item-card bg-white rounded-lg shadow p-3 cursor-pointer hover:ring-2 hover:ring-blue-500"
                 data-id="{{ $item->id }}"
                 data-name="{{ $item->name }}"
                 data-price="{{ $item->base_price }}"
                 data-has-options="{{ $item->options->count() > 0 ? 'true' : 'false' }}"
                 onclick="selectItem(this)">
                
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         class="w-full h-24 object-cover rounded mb-2">
                @endif
                
                <p class="font-semibold text-sm">{{ $item->name }}</p>
                <p class="text-blue-600 font-bold">
                    TZS {{ number_format($item->base_price) }}
                </p>
            </div>
        @endforeach
    </div>
@endforeach
```

- [ ] Category tabs show — clicking switches item grid
- [ ] Items show name + price (+ image if uploaded)
- [ ] Buffet items NOT in this grid

---

### Step 3 — Options Popup (After Item Click)

```blade
{{-- Option selection modal --}}
<div id="option-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-80 shadow-xl">
        <h3 class="font-bold text-lg mb-4" id="option-modal-title">Select Option</h3>
        
        <div id="option-list" class="space-y-2">
            {{-- Populated by JS --}}
        </div>

        <button onclick="closeOptionModal()" class="mt-4 w-full btn btn-ghost">
            Cancel
        </button>
    </div>
</div>
```

```javascript
// JS to handle item click and show options

function selectItem(card) {
    const hasOptions = card.dataset.hasOptions === 'true';
    const itemId     = card.dataset.id;
    const itemName   = card.dataset.name;
    const itemPrice  = parseFloat(card.dataset.price);

    if (hasOptions) {
        // Fetch options and show modal
        fetch(`/pos/menu-items/${itemId}/options`)
            .then(res => res.json())
            .then(options => {
                showOptionModal(itemName, itemPrice, itemId, options);
            });
    } else {
        // Add directly to order
        addToOrder(itemId, itemName, itemPrice, null);
    }
}

function showOptionModal(name, basePrice, itemId, options) {
    document.getElementById('option-modal-title').textContent = `Select Option: ${name}`;
    
    const list = document.getElementById('option-list');
    list.innerHTML = '';

    options.forEach(opt => {
        const total = basePrice + opt.price_adjustment;
        const btn   = document.createElement('button');
        btn.className = 'w-full text-left p-3 rounded border hover:bg-blue-50 hover:border-blue-500';
        btn.innerHTML  = `
            <span class="font-medium">${opt.name}</span>
            <span class="float-right text-blue-600">TZS ${total.toLocaleString()}</span>
        `;
        btn.onclick = () => {
            addToOrder(itemId, `${name} (${opt.name})`, total, opt.id);
            closeOptionModal();
        };
        list.appendChild(btn);
    });

    document.getElementById('option-modal').classList.remove('hidden');
}

function closeOptionModal() {
    document.getElementById('option-modal').classList.add('hidden');
}

function addToOrder(itemId, name, price, optionId) {
    // Add to order cart (see POS cart logic)
    console.log('Adding:', { itemId, name, price, optionId });
}
```

- [ ] Clicking item with options shows popup modal
- [ ] Options show name + total price (base + adjustment)
- [ ] Clicking option adds to order and closes modal
- [ ] Clicking item without options adds directly

---

### Step 4 — Options API Endpoint

```php
// routes/web.php
Route::get('/pos/menu-items/{item}/options', [PosController::class, 'getItemOptions']);

// PosController.php
public function getItemOptions(MenuItem $item)
{
    $options = $item->options()
        ->where('is_active', true)
        ->get(['id', 'name', 'price_adjustment']);

    return response()->json($options);
}
```

- [ ] Route exists and returns JSON
- [ ] Only `is_active = true` options returned
- [ ] Returns `id`, `name`, `price_adjustment`

---

### Step 5 — Remove Buffet from Regular Menu Grid

```php
// Ensure buffet items have is_buffet = true in DB
// Migration if field doesn't exist:

Schema::table('menu_items', function (Blueprint $table) {
    $table->boolean('is_buffet')->default(false)->after('base_price');
});
```

- [ ] `is_buffet` column exists on `menu_items`
- [ ] All buffet items have `is_buffet = true` in database
- [ ] Regular POS query excludes `is_buffet = true`
- [ ] Buffet has its own separate screen (see Task 08)

---

### Step 6 — Time-Based Availability Enforcement

```php
// In MenuItem model
public function scopeAvailableNow($query)
{
    $now = now()->format('H:i:s');
    return $query->where(function ($q) use ($now) {
        $q->whereNull('available_from')
          ->orWhere(function ($inner) use ($now) {
              $inner->where('available_from', '<=', $now)
                    ->where('available_to', '>=', $now);
          });
    });
}
```

- [ ] `availableNow()` scope defined on MenuItem
- [ ] Scope used in POS query
- [ ] Items outside time window do NOT show on POS

---

## Files to Modify

| File | Change |
|------|--------|
| `app/Http/Controllers/PosController.php` | Fix index() query with filters |
| `resources/views/pos/index.blade.php` | Rebuild category tabs + item grid |
| `resources/views/pos/partials/option-modal.blade.php` | Add option popup |
| `public/js/pos.js` | Add selectItem(), showOptionModal() JS |
| `app/Models/MenuItem.php` | Add availableNow() scope |
| `routes/web.php` | Add /pos/menu-items/{item}/options route |
| `database/migrations/...` | Add is_buffet column if missing |

---

## Done When
- [ ] Category tabs show, clicking switches item display
- [ ] Only active, time-valid items show
- [ ] Buffet items NOT visible in regular POS grid
- [ ] Items with options show popup on click
- [ ] Items without options added directly to order
- [ ] No mixed/ghost elements visible
- [ ] Cashier can find any item quickly by category
