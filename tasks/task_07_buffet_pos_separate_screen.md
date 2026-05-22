# Task 07 — Buffet POS — Separate Screen & Sales Flow

## Problem
Buffet is mixed with regular menu items in the POS. Buffet must have its own dedicated screen with a completely different flow — count-based (adults/children), not item-based.

---

## Core Concept

```
NORMAL POS       →  Select Item → Quantity → Add to Order → Pay
BUFFET POS       →  Select Session → Enter Adults → Enter Children → Auto Total → Pay
```

These are TWO separate flows. Never mix them.

---

## Buffet Sessions Table (Verify / Create)

```php
Schema::create('buffet_sessions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');                     // "Breakfast Buffet"
    $table->text('description')->nullable();
    $table->time('start_time');                 // 06:00
    $table->time('end_time');                   // 11:00
    $table->decimal('adult_price', 10, 2);      // 15000.00
    $table->decimal('child_price', 10, 2);      // 8000.00
    $table->string('days_available')->nullable(); // "Mon,Tue,Wed,Thu,Fri"
    $table->boolean('is_active')->default(true);
    $table->foreignUuid('created_by')->constrained('users');
    $table->timestamps();
});
```

---

## Buffet Sales Tables

```php
// buffet_sales — master transaction
Schema::create('buffet_sales', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('buffet_session_id')->constrained();
    $table->string('sale_number')->unique();     // BUF-20250615-001
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
    $table->enum('payment_method', ['cash', 'card', 'mobile_money'])->nullable();
    $table->string('customer_name')->nullable();
    $table->text('notes')->nullable();
    $table->decimal('price_snapshot_adult', 10, 2); // capture price at time of sale
    $table->decimal('price_snapshot_child', 10, 2);
    $table->foreignUuid('cashier_id')->constrained('users');
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});

// buffet_sale_items — breakdown by entry type
Schema::create('buffet_sale_items', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('buffet_sale_id')->constrained()->cascadeOnDelete();
    $table->enum('entry_type', ['adult', 'child', 'complimentary']);
    $table->integer('quantity');
    $table->decimal('unit_price', 10, 2);       // snapshot price at time of sale
    $table->decimal('total', 10, 2);             // quantity × unit_price
    $table->timestamps();
});
```

**Important:** Always save price snapshot. Never recalculate from current session price after sale.

---

## Buffet POS Controller

```php
// app/Http/Controllers/BuffetPosController.php

class BuffetPosController extends Controller
{
    // Show buffet POS screen
    public function index()
    {
        $now = now()->format('H:i:s');
        $day = strtolower(now()->format('D')); // mon, tue, etc.

        $sessions = BuffetSession::where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->get()
            ->filter(function ($session) use ($day) {
                // Check if today is an available day
                if (!$session->days_available) return true;
                $days = array_map('strtolower', explode(',', $session->days_available));
                return in_array($day, $days);
            });

        return view('buffet.pos', compact('sessions'));
    }

    // Process buffet sale
    public function store(Request $request)
    {
        $request->validate([
            'buffet_session_id' => 'required|exists:buffet_sessions,id',
            'adults'            => 'required|integer|min:0',
            'children'          => 'required|integer|min:0',
            'customer_name'     => 'nullable|string|max:150',
            'notes'             => 'nullable|string',
        ]);

        $adults   = (int) $request->adults;
        $children = (int) $request->children;

        if ($adults === 0 && $children === 0) {
            return back()->withErrors(['adults' => 'At least one person required.']);
        }

        $session = BuffetSession::findOrFail($request->buffet_session_id);

        $adultTotal   = $adults   * $session->adult_price;
        $childTotal   = $children * $session->child_price;
        $subtotal     = $adultTotal + $childTotal;

        // Create buffet sale
        $sale = BuffetSale::create([
            'buffet_session_id'      => $session->id,
            'sale_number'            => 'BUF-' . now()->format('Ymd') . '-' . str_pad(BuffetSale::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT),
            'subtotal'               => $subtotal,
            'discount'               => 0,
            'total'                  => $subtotal,
            'payment_status'         => 'pending',
            'customer_name'          => $request->customer_name,
            'notes'                  => $request->notes,
            'price_snapshot_adult'   => $session->adult_price,
            'price_snapshot_child'   => $session->child_price,
            'cashier_id'             => auth()->id(),
        ]);

        // Create line items
        if ($adults > 0) {
            $sale->items()->create([
                'entry_type' => 'adult',
                'quantity'   => $adults,
                'unit_price' => $session->adult_price,
                'total'      => $adultTotal,
            ]);
        }

        if ($children > 0) {
            $sale->items()->create([
                'entry_type' => 'child',
                'quantity'   => $children,
                'unit_price' => $session->child_price,
                'total'      => $childTotal,
            ]);
        }

        return redirect()
            ->route('buffet.payment', $sale)
            ->with('success', 'Buffet sale created. Proceed to payment.');
    }

    // Payment screen
    public function payment(BuffetSale $sale)
    {
        return view('buffet.payment', compact('sale'));
    }

    // Record payment
    public function pay(Request $request, BuffetSale $sale)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,mobile_money',
            'amount_tendered' => 'required|numeric|min:' . $sale->total,
        ]);

        $sale->update([
            'payment_status'  => 'paid',
            'payment_method'  => $request->payment_method,
            'paid_at'         => now(),
        ]);

        return redirect()
            ->route('buffet.receipt', $sale)
            ->with('success', 'Payment recorded.');
    }

    // Receipt
    public function receipt(BuffetSale $sale)
    {
        return view('buffet.receipt', compact('sale'));
    }
}
```

---

## Buffet POS View

```blade
{{-- resources/views/buffet/pos.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">🍽️ Buffet POS</h1>
        <a href="{{ route('pos.index') }}" class="btn btn-ghost btn-sm">
            ← Regular POS
        </a>
    </div>

    @if($sessions->isEmpty())
        <div class="alert alert-info">
            <span>No buffet sessions active at this time.</span>
        </div>
    @else
        <form method="POST" action="{{ route('buffet.store') }}">
            @csrf
            
            {{-- Step 1: Session Selection --}}
            <div class="card shadow mb-6">
                <div class="card-body">
                    <h2 class="card-title text-base mb-3">Select Buffet</h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($sessions as $session)
                            <label class="cursor-pointer">
                                <input type="radio" name="buffet_session_id" 
                                       value="{{ $session->id }}" class="hidden peer" required>
                                <div class="border-2 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-400 transition">
                                    <p class="font-bold">{{ $session->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }}
                                    </p>
                                    <p class="text-sm mt-1">
                                        Adult: <strong>TZS {{ number_format($session->adult_price) }}</strong>
                                    </p>
                                    <p class="text-sm">
                                        Child: <strong>TZS {{ number_format($session->child_price) }}</strong>
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Step 2: People Count --}}
            <div class="card shadow mb-6">
                <div class="card-body">
                    <h2 class="card-title text-base mb-4">Enter Count</h2>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-semibold text-lg">Adults</p>
                            <p class="text-sm text-gray-500">Full price</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="adjustCount('adults', -1)"
                                class="w-10 h-10 rounded-full bg-gray-200 text-xl font-bold hover:bg-gray-300">−</button>
                            <input type="number" name="adults" id="adults" value="0" min="0"
                                class="w-16 text-center text-2xl font-bold border-0 outline-none" readonly>
                            <button type="button" onclick="adjustCount('adults', 1)"
                                class="w-10 h-10 rounded-full bg-blue-600 text-white text-xl font-bold hover:bg-blue-700">+</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-semibold text-lg">Children</p>
                            <p class="text-sm text-gray-500">Child price</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="adjustCount('children', -1)"
                                class="w-10 h-10 rounded-full bg-gray-200 text-xl font-bold hover:bg-gray-300">−</button>
                            <input type="number" name="children" id="children" value="0" min="0"
                                class="w-16 text-center text-2xl font-bold border-0 outline-none" readonly>
                            <button type="button" onclick="adjustCount('children', 1)"
                                class="w-10 h-10 rounded-full bg-blue-600 text-white text-xl font-bold hover:bg-blue-700">+</button>
                        </div>
                    </div>

                    <input type="text" name="customer_name" placeholder="Customer name (optional)"
                        class="input input-bordered w-full mt-2">
                </div>
            </div>

            {{-- Step 3: Live Total --}}
            <div class="card shadow bg-blue-50 border border-blue-200 mb-6">
                <div class="card-body">
                    <div class="flex justify-between text-lg">
                        <span>Adults: <span id="adult-count">0</span></span>
                        <span id="adult-total">TZS 0</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span>Children: <span id="child-count">0</span></span>
                        <span id="child-total">TZS 0</span>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="flex justify-between text-2xl font-bold text-blue-700">
                        <span>TOTAL</span>
                        <span id="grand-total">TZS 0</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
                Proceed to Payment →
            </button>
        </form>
    @endif
</div>

<script>
// Session pricing data
const sessions = @json($sessions->keyBy('id')->map(fn($s) => [
    'adult_price' => $s->adult_price,
    'child_price' => $s->child_price,
]));

function getSelectedSession() {
    const checked = document.querySelector('input[name="buffet_session_id"]:checked');
    return checked ? sessions[checked.value] : null;
}

function adjustCount(field, delta) {
    const input = document.getElementById(field);
    const newVal = Math.max(0, parseInt(input.value) + delta);
    input.value = newVal;
    updateTotal();
}

function updateTotal() {
    const session  = getSelectedSession();
    const adults   = parseInt(document.getElementById('adults').value) || 0;
    const children = parseInt(document.getElementById('children').value) || 0;

    if (!session) {
        document.getElementById('grand-total').textContent = 'TZS 0';
        return;
    }

    const adultTotal = adults * session.adult_price;
    const childTotal = children * session.child_price;
    const grandTotal = adultTotal + childTotal;

    document.getElementById('adult-count').textContent  = adults;
    document.getElementById('child-count').textContent  = children;
    document.getElementById('adult-total').textContent  = 'TZS ' + adultTotal.toLocaleString();
    document.getElementById('child-total').textContent  = 'TZS ' + childTotal.toLocaleString();
    document.getElementById('grand-total').textContent  = 'TZS ' + grandTotal.toLocaleString();
}

// Update total when session changes
document.querySelectorAll('input[name="buffet_session_id"]').forEach(radio => {
    radio.addEventListener('change', updateTotal);
});
</script>
@endsection
```

---

## Routes

```php
// routes/web.php

Route::middleware(['auth', 'role:cashier,supervisor,admin'])
    ->prefix('buffet')
    ->name('buffet.')
    ->group(function () {
        Route::get('/',               [BuffetPosController::class, 'index'])   ->name('index');
        Route::post('/',              [BuffetPosController::class, 'store'])   ->name('store');
        Route::get('/{sale}/payment', [BuffetPosController::class, 'payment'])->name('payment');
        Route::post('/{sale}/pay',    [BuffetPosController::class, 'pay'])     ->name('pay');
        Route::get('/{sale}/receipt', [BuffetPosController::class, 'receipt'])->name('receipt');
    });
```

---

## Sidebar Link (Cashier)

```blade
{{-- In cashier sidebar --}}
<li>
    <a href="{{ route('pos.index') }}">🛒 Regular POS</a>
</li>
<li>
    <a href="{{ route('buffet.index') }}">🍽️ Buffet POS</a>
</li>
```

---

## Files to Create

| File | Purpose |
|------|---------|
| `app/Http/Controllers/BuffetPosController.php` | All buffet POS logic |
| `app/Models/BuffetSale.php` | Sale model |
| `app/Models/BuffetSaleItem.php` | Line items |
| `database/migrations/create_buffet_sales_table.php` | Sales table |
| `database/migrations/create_buffet_sale_items_table.php` | Items table |
| `resources/views/buffet/pos.blade.php` | Main POS screen |
| `resources/views/buffet/payment.blade.php` | Payment screen |
| `resources/views/buffet/receipt.blade.php` | Receipt view |

---

## Done When
- [ ] Buffet POS at `/buffet` — completely separate from `/pos`
- [ ] Only active, time-valid sessions shown
- [ ] Adults/children count with +/- buttons
- [ ] Live total updates as count changes
- [ ] Session price snapshotted at time of sale
- [ ] Payment screen collects method + amount tendered
- [ ] Receipt generated after payment
- [ ] Sidebar has both "Regular POS" and "Buffet POS" links
- [ ] Buffet sales tracked separately from regular orders
