# Restaurant & Bar Module — Complete Guide

> Last updated: June 2026

---

## Table of Contents

1. [Module Overview](#1-module-overview)
2. [Data Model](#2-data-model)
3. [Order Lifecycle — Complete Flow](#3-order-lifecycle--complete-flow)
4. [Flow 1: Restaurant POS (Walk-In)](#4-flow-1-restaurant-pos-walk-in)
5. [Flow 2: Restaurant POS (Guest Folio)](#5-flow-2-restaurant-pos-guest-folio)
6. [Flow 3: Restaurant Manual Order](#6-flow-3-restaurant-manual-order)
7. [Flow 4: Bartender POS (Walk-In)](#7-flow-4-bartender-pos-walk-in)
8. [Flow 5: Bartender POS (Guest Folio)](#8-flow-5-bartender-pos-guest-folio)
9. [Flow 6: Bartender Order from Restaurant](#9-flow-6-bartender-order-from-restaurant)
10. [Flow 7: Reception Drink Request](#10-flow-7-reception-drink-request)
11. [Flow 8: Buffet Sale (Walk-In)](#11-flow-8-buffet-sale-walk-in)
12. [Flow 9: Buffet Sale (Guest Folio)](#12-flow-9-buffet-sale-guest-folio)
13. [Ticket Dispatch System](#13-ticket-dispatch-system)
14. [Stock Deduction Mechanism](#14-stock-deduction-mechanism)
15. [Billing Integration](#15-billing-integration)
16. [Receipt Generation](#16-receipt-generation)
17. [Accounting Integration](#17-accounting-integration)
18. [Key Services & Their Roles](#18-key-services--their-roles)
19. [Order Status Reference](#19-order-status-reference)
20. [Bartender Status Reference](#20-bartender-status-reference)
21. [API Endpoints](#21-api-endpoints)
22. [Known Issues & Bug-Prone Areas](#22-known-issues--bug-prone-areas)

---

## 1. Module Overview

The Restaurant & Bar module handles food and beverage ordering, preparation, serving, and billing across four entry points:

| Entry Point | Route Prefix | Controller | Auth Role | Purpose |
|---|---|---|---|---|
| Restaurant POS | `/restaurant/pos` | `OrderController@storePos` | waiter, restaurant_manager | Quick kitchen sales (walk-in or guest folio) |
| Restaurant Orders | `/restaurant/orders` | `OrderController@store` | waiter, restaurant_manager | Full table-side ordering workflow |
| Bartender POS | `/bartender/pos` | `BartenderController@storePos` | bar_tender, manager | Bar walk-in or guest folio sales |
| Reception Drinks | `/drinks/request` | `DrinkRequestController@store` | front_desk, supervisor | Room service drink requests sent to bar |

Plus two buffet paths:

| Entry Point | Route Prefix | Controller | Purpose |
|---|---|---|---|
| Restaurant Buffet | `/restaurant/buffet` | `BuffetController` | Manual buffet sales from restaurant manager |
| Buffet POS | `/buffet` | `BuffetPosController` | Quick buffet POS sales |

---

## 2. Data Model

### Core Entities

```
StockLocation (kitchen | bar)
  ├── MenuCategory
  │     └── MenuItem
  │           ├── MenuItemIngredient → Product (Store product)
  │           └── MenuOptionGroup ←→ MenuOptionValue (many-to-many via pivot)
  ├── Table
  └── StockLevel → Product

Order
  ├── location_id → StockLocation (kitchen or bar)
  ├── table_id → Table (nullable)
  ├── booking_id → Booking (nullable, for guest orders)
  ├── order_type: guest | walkin | dine_in | room_service | bar_tab | takeaway
  ├── order_source: walkin | restaurant | reception_drink | null
  ├── bartender_status: pending | accepted | prepared | served | cancelled | null
  ├── status: open → sent → ready → served → charged → settled → cancelled
  ├── payment_method: cash | card | mobile | mobile_money | charge_to_booking
  ├── stock_deducted_at, stock_reversed_at, billed_to_folio_at
  └── OrderItem[]
        ├── menu_item_id → MenuItem
        ├── item_name_snapshot, selected_options_snapshot, options_signature
        ├── quantity, base_unit_price, options_unit_price, unit_price, subtotal
        └── status: pending | cancelled

BookingCharge
  ├── booking_id → Booking
  ├── order_id → Order (nullable for buffet)
  ├── source: restaurant | bar | room_service | laundry | store | etc.
  ├── charge_type: restaurant | bar | room_service | etc.
  ├── reference_id (polymorphic: Order id or BuffetSale id)
  ├── amount (USD), amount_tzs (TZS), currency
  └── status: unpaid | paid

KitchenTicket → items filtered by destination=kitchen
BarTicket → items filtered by destination=bar
```

### Key Relationships

```
MenuItem.destination (kitchen | bar)
  → Determines which ticket type is created and which staff handles it

MenuItem.category.location_id → StockLocation
  → Determines where the item is prepared and which stock location is checked

Order.order_source + Order.location.code
  → If order_source='restaurant' AND location.code='bar':
       Bartender manages the order (isBartenderManagedBarOrder = true)
  → If order_source='reception_drink':
       Came from reception desk drink request
  → If order_source='walkin' or null:
       Direct POS sale (restaurant or bartender)

BookingCharge links Order → Booking for guest billing
RoomCharge links Order → Booking (alternative charge path)
```

---

## 3. Order Lifecycle — Complete Flow

### Unified Status Machine

```
                    ┌─────────────────────────────────────────────────┐
                    │                                                 │
                    │              ORDER STATUS FLOW                  │
                    │                                                 │
   ┌────────┐    ┌──────┐    ┌──────┐    ┌───────┐    ┌────────┐    ┌────────┐
   │ OPEN   │───▶│ SENT │───▶│ READY│───▶│ SERVED│───▶│ CHARGED│───▶│ SETTLED│
   └────────┘    └──────┘    └──────┘    └───────┘    └────────┘    └────────┘
       │             │           │           │             │              │
       │             │           │           │             │              │
       └─────────────┴───────────┴───────────┴─────────────┴──────────────┘
                                       │
                                       ▼
                                 ┌──────────┐
                                 │CANCELLED │
                                 └──────────┘
```

### Bartender Status (parallel, only for bar orders)

```
   ┌─────────┐    ┌─────────┐    ┌──────────┐    ┌────────┐
   │ PENDING │───▶│ ACCEPTED │───▶│ PREPARED │───▶│ SERVED │
   └─────────┘    └─────────┘    └──────────┘    └────────┘
       │                                                  │
       ▼                                                  │
   ┌─────────┐                                            │
   │REJECTED │                                            │
   └─────────┘                                            │
                                                          │
   ┌──────────┐                                           │
   │CANCELLED │◄──────────────────────────────────────────┘
   └──────────┘
```

---

## 4. Flow 1: Restaurant POS (Walk-In)

**Entry**: `POST /restaurant/pos` → `OrderController@storePos`

```
Waiter selects items on POS screen
        │
        ▼
storePos() validates request
  - payment_method = cash|card|mobile
  - items with menu_item_id + quantity
  - optional buffet_items
        │
        ▼
DB::transaction {
  │
  ├─ Order::create()
  │    location_id = kitchen
  │    order_type = 'walkin'
  │    order_source = 'walkin'
  │    status = 'open'
  │
  ├─ foreach items → OrderItem::create()
  │    unit_price = menuItem.selling_price
  │    (NO options support in POS flow)
  │
  ├─ order->recalculate()
  │    subtotal = sum(items.subtotal)
  │    total = subtotal - discount + tax
  │
  ├─ BarOrderStockService::deductForOrder()
  │    lockForUpdate on Order row
  │    check stock_deducted_at (idempotency)
  │    checkAvailability() on each item's ingredients
  │    abort(422) if insufficient
  │    StockMovement::record(type='recipe_use') for each ingredient
  │    set stock_deducted_at = now()
  │
  ├─ order->update(status='settled', settled_by, settled_at)
  │
  ├─ AccountingService::postRestaurantSettlement()
  │    DR: Cash(1100) or Bank(1200)
  │    CR: F&B Revenue(4200), VAT Payable(2200)
  │
  ├─ FinancePayment::create()
  │    payment_type='walkin', amount, currency='TZS'
  │
  ├─ FinancialTransaction::record()
  │    source_module='restaurant'
  │
  └─ ReceiptService::getOrCreateReceipt()
       Creates receipt from Order::toReceiptData()
}
        │
        ▼
Redirect to order show page
```

**Key Points**:
- Walk-in orders are **settled immediately** — no send/serve/settle steps
- Stock is deducted **at POS creation time**
- Receipt is generated immediately
- No loyalty points awarded for walk-in POS sales

---

## 5. Flow 2: Restaurant POS (Guest Folio)

**Entry**: `POST /restaurant/pos` → `OrderController@storePos` with `payment_method=charge_to_booking`

```
Waiter selects items on POS screen
        │
        ▼
storePos() validates request
  - payment_method = 'charge_to_booking'
  - booking_id required, booking must be 'checked_in'
        │
        ▼
DB::transaction {
  │
  ├─ Order::create()
  │    location_id = kitchen
  │    order_type = 'guest'
  │    order_source = 'walkin'
  │    booking_id = booking->id
  │    status = 'open'
  │
  ├─ foreach items → OrderItem::create()
  ├─ order->recalculate()
  │
  ├─ BarOrderStockService::deductForOrder()
  │    (same as walk-in — stock deducted immediately)
  │
  ├─ order->update(status='charged', billed_to_folio_at=now)
  │
  └─ ModuleBillingService::syncOrderCharge()
       Creates BookingCharge:
         source = 'restaurant'
         charge_type = 'restaurant'
         amount (USD), amount_tzs (TZS)
         status = 'unpaid'
}
        │
        ▼
Redirect to Finance Checkout page
```

**Key Points**:
- Guest orders go to `charged` status (NOT settled)
- Payment happens at Finance Checkout later (via `ModuleBillingService::finalizeCharges`)
- Receipt is created immediately (via `getOrCreateReceipt`)
- Loyalty points are **NOT** awarded here (only in `settle()` method which is for manual orders)

---

## 6. Flow 3: Restaurant Manual Order

**Entry**: `POST /restaurant/orders` → `OrderController@store`

This is the **traditional restaurant flow** with table ordering, kitchen tickets, and progressive status updates.

### Step 1: Create Order

```
Waiter creates order manually
  - Selects location (kitchen or bar)
  - Selects table, order_type, booking_id (for guest)
  - Selects menu items with options
        │
        ▼
store() validates and creates:
  │
  ├─ Order::create()
  │    status = 'open'
  │    order_source = 'restaurant' (if bar location) or null
  │    bartender_status = 'pending' (if bar location)
  │
  ├─ foreach items → OrderItem::create()
  │    Uses buildOrderItemPayload() which:
  │      - Validates menu_item is available
  │      - Validates menu_item belongs to order's location
  │      - Validates option selections (required, single vs multiple)
  │      - Calculates options_unit_price from selected option values
  │      - Generates options_signature for deduplication
  │
  ├─ If table_id: Table->update(status='occupied')
  ├─ order->recalculate()
  │
  └─ Redirect to order show page
```

### Step 2: Add/Remove Items (while OPEN)

```
addItem() — POST /restaurant/orders/{order}/items
  - Validates order is still 'open'
  - If same menu_item + same options_signature exists: merge (increase qty)
  - Otherwise: create new OrderItem
  - order->recalculate()

removeItem() — DELETE /restaurant/orders/{order}/items/{orderItem}
  - Validates order is still 'open'
  - Marks item status = 'cancelled'
  - order->recalculate()
```

### Step 3: Send to Kitchen/Bar

```
send() — POST /restaurant/orders/{order}/send
  - Validates status == 'open'
  - Sets status = 'sent'
  - OrderDispatchService::splitAndDispatch():
      ├─ Items with destination='kitchen' → KitchenTicket (status='pending')
      └─ Items with destination='bar'     → BarTicket (status='pending')
```

### Step 4a: Kitchen Flow

```
Kitchen staff see ticket in /restaurant/kitchen/queue
  ├─ markPreparing() → KitchenTicket.status = 'preparing'
  └─ markReady()     → KitchenTicket.status = 'ready'

NOTE: KitchenTicket status does NOT update Order status!
      The waiter must manually call ready() on the order.
```

### Step 4b: Bar Flow (from Restaurant)

```
Bar ticket appears in bartender inbox at /bartender/orders
  Bar drink items are NOT managed by restaurant staff.

  isBartenderManagedBarOrder() = true
  → ready() and serve() are BLOCKED for this order
  → Must be handled by bartender
```

### Step 5: Mark Ready (kitchen items only)

```
ready() — POST /restaurant/orders/{order}/ready
  - Validates NOT bartender-managed
  - Validates status == 'sent'
  - Sets status = 'ready'
```

### Step 6: Serve

```
serve() — POST /restaurant/orders/{order}/serve
  - Validates NOT bartender-managed
  - Validates status == 'ready'
  - If booking_id:
      ModuleBillingService::syncOrderCharge()
      → Creates/updates BookingCharge on guest folio
  - Sets status = 'served'
```

### Step 7: Settle (Guest Orders Only)

```
settle() — POST /restaurant/orders/{order}/settle
  - Validates order is NOT already charged/settled/cancelled
  - Validates booking_id exists
  - If bartender-managed: validates bartender_status == 'served'
  - DB::transaction {
      ├─ BarOrderStockService::deductForOrder() (idempotent)
      ├─ order->update(status='charged', payment_method='charge_to_booking')
      ├─ ModuleBillingService::syncOrderCharge()
      └─ Table->update(status='available') if table_id
    }
  - Loyalty points: floor(total / 10000) * 50
  - Redirect to Finance Checkout
```

**Walk-in manual orders**: Use the payment modal (WalkinPaymentController), NOT the settle endpoint.

### Step 8: Cancel (if needed)

```
cancel() — POST /restaurant/orders/{order}/cancel
  - Cannot cancel settled orders
  - If stock_deducted_at && !stock_reversed_at:
      BarOrderStockService::reverseForCancelledOrder()
        → StockMovement::record(type='restock') for each ingredient
        → Set stock_reversed_at = now()
  - ModuleBillingService::voidChargeForOrder()
      → Deletes unpaid BookingCharge rows
  - Set status = 'cancelled'
  - Free table if applicable
```

---

## 7. Flow 4: Bartender POS (Walk-In)

**Entry**: `POST /bartender/pos` → `BartenderController@storePos`

```
Bartender selects drinks on POS screen
        │
        ▼
storePos() validates:
  - payment_method = cash|card|mobile
  - items with menu_item_id + quantity
        │
        ▼
DB::transaction {
  │
  ├─ Order::create()
  │    location_id = bar
  │    order_type = 'walkin'
  │    order_source = 'walkin'
  │    bartender_status = 'prepared'  ← Already prepared since bartender makes it
  │    status = 'open'
  │
  ├─ foreach items → OrderItem::create()
  │    NO options support in bar POS
  │    unit_price = menuItem.selling_price
  │    subtotal = unit_price * quantity
  │
  ├─ order->recalculate()
  │
  ├─ BarOrderStockService::deductForOrder()
  │    Locks Order row, checks idempotency
  │    Checks availability, records recipe_use movements
  │    Sets stock_deducted_at
  │
  ├─ order->update(
  │    bartender_status = 'served',
  │    bartender_status_updated_at = now(),
  │    status = 'settled',
  │    settled_by = auth()->id(),
  │    settled_at = now()
  │  )
  │
  └─ ReceiptService::getOrCreateReceipt(order)
}
        │
        ▼
No accounting entry or FinancePayment created!
⚠️  BUG: Walk-in bar POS sales do NOT create:
    - FinancePayment record
    - FinancialTransaction record
    - AccountingService journal entry
```

**Key Points**:
- Walk-in bar orders are **settled immediately** (stock deducted, status='settled')
- `bartender_status` goes straight from `prepared` to `served`
- Stock is deducted at creation time
- ⚠️ No FinancePayment, FinancialTransaction, or accounting entry is created for walk-in bar sales

---

## 8. Flow 5: Bartender POS (Guest Folio)

**Entry**: `POST /bartender/pos` → `BartenderController@storePos` with `payment_method=charge_to_booking`

```
DB::transaction {
  │
  ├─ Order::create()
  │    order_type = 'guest'
  │    order_source = 'walkin'
  │    bartender_status = 'prepared'
  │    booking_id = booking->id
  │
  ├─ foreach items → OrderItem::create()
  ├─ order->recalculate()
  ├─ BarOrderStockService::deductForOrder()
  │
  ├─ order->update(
  │    bartender_status = 'served',
  │    status = 'charged',
  │    billed_to_folio_at = now()
  │  )
  │
  └─ ModuleBillingService::syncOrderCharge()
       source = 'bar'
       charge_type = 'bar'
}
        │
        ▼
Redirect to order show page
```

---

## 9. Flow 6: Bartender Order from Restaurant

When a restaurant order contains bar items (destination='bar'), the bartender must handle those items.

### Order Creation (Restaurant Side)

```
OrderController@store → location=bar → order_source='restaurant', bartender_status='pending'

OrderDispatchService::splitAndDispatch():
  Kitchen items → KitchenTicket
  Bar items → BarTicket

Order appears in bartender inbox at /bartender/orders
```

### Bartender Processing

```
acceptOrder() — POST /bartender/orders/{order}/accept
  - Validates bartender_status == 'pending'
  - Checks stock availability via BarOrderStockService
  - Sets bartender_status = 'accepted'

prepareOrder() — POST /bartender/orders/{order}/prepare
  - Validates bartender_status == 'accepted'
  - Sets bartender_status = 'prepared'

serveOrder() — POST /bartender/orders/{order}/serve
  - Validates bartender_status == 'prepared'
  - Checks stock availability again
  - DB::transaction {
      ├─ BarOrderStockService::deductForOrder()
      ├─ order->update(bartender_status='served', status='served')
      └─ If order_source IN ['room_service', 'reception_drink']:
            ModuleBillingService::syncOrderCharge()
    }
```

**Important**: For restaurant-sourced bar orders (order_source='restaurant'), `serveOrder()` sets `status='served'` instead of 'charged'. The billing sync is NOT called because the billing for these orders happens when the restaurant waiter calls `settle()` on the whole order.

---

## 10. Flow 7: Reception Drink Request

**Entry**: `POST /drinks/request` → `DrinkRequestController@store`

```
Receptionist selects checked-in guest and drink products
        │
        ▼
DB::transaction {
  │
  ├─ Order::create()
  │    location_id = bar
  │    order_type = 'guest'
  │    order_source = 'reception_drink'
  │    booking_id = booking->id
  │    bartender_status = 'pending'
  │    status = 'open'
  │
  ├─ foreach items:
  │    Product lookup by name + product_type='bar'
  │    If no MenuItem exists for this product:
  │      MenuItem::create() on-the-fly (⚠️ potential race condition)
  │    OrderItem::create()
  │
  └─ order->update(subtotal, total)
}

Order appears in bartender's drink inbox at /bartender/drink-inbox

Bartender processes: accept → prepare → serve
  On serve: stock deducted + billing synced
```

**Key Points**:
- MenuItems are auto-created from Products if missing (race condition risk)
- No options support in drink requests
- Order goes directly to bartender (no kitchen ticket)
- No `recalculate()` is called — subtotal and total are manually set

---

## 11. Flow 8: Buffet Sale (Walk-In)

**Entry**: `POST /restaurant/buffet/sales` via `BuffetController@store` or `POST /buffet` via `BuffetPosController@store`

```
Waiter selects buffet package, adults/children count
        │
        ▼
BuffetController@store (or BuffetPosController@store):
  - Validates package availability (BuffetController only, NOT BuffetPosController!)
  - Creates BuffetSale with status='pending'
        │
        ▼
Waiter settles via: POST /restaurant/buffet/sales/{sale}/settle-walkin
        │
        ▼
BuffetController@settleWalkin():
  DB::transaction {
    ├─ FinancePayment::create()
    ├─ FinancialTransaction::record()
    ├─ AccountingService::postRestaurantSettlement()
    │    DR: Cash(1100) or Bank(1200)
    │    CR: F&B Revenue(4200), VAT Payable(2200)
    ├─ buffetSale->update(status='settled', settled_by, settled_at)
    └─ ReceiptService::getOrCreateReceipt(sale)
  }
```

**BuffetPosController@store differences**:
- Handles both walk-in and guest folio in one transaction
- Walk-in: settles immediately with FinancePayment + FinancialTransaction + Accounting + Receipt
- Guest: creates BookingCharge immediately, no separate charge step needed

---

## 12. Flow 9: Buffet Sale (Guest Folio)

```
BuffetController@chargeToBooking() OR BuffetPosController@store(charge_to_booking)
        │
        ▼
DB::transaction {
  ├─ BookingCharge::updateOrCreate()
  │    source = 'restaurant'
  │    charge_type = 'restaurant'
  │    amount (USD), amount_tzs (TZS)
  │    status = 'unpaid'
  │
  └─ buffetSale->update(status='charged')
}
        │
        ▼
Redirect to Finance Checkout for payment
At checkout: ModuleBillingService::finalizeCharges()
  └─ Sets buffetSale status='settled'
     Posts accounting entry
```

---

## 13. Ticket Dispatch System

**Service**: `OrderDispatchService::splitAndDispatch()`

Called from: `OrderController@send()` when order status changes from `open` → `sent`

```
Order loaded with items.menuItem
        │
        ├─ Filter items where destination='kitchen' AND status != 'cancelled'
        │    → KitchenTicket::create()
        │       items = JSON array of {id, name, quantity, notes, options}
        │       status = 'pending'
        │
        └─ Filter items where destination='bar' AND status != 'cancelled'
             → BarTicket::create()
                items = JSON array of {id, name, quantity, notes, options}
                status = 'pending'
```

**Ticket Status Flow**:
```
pending → preparing → ready
```

Kitchen tickets are viewed at `/restaurant/kitchen/queue`
Bar tickets are viewed at `/restaurant/bar/queue`

**IMPORTANT**: Ticket status changes do NOT propagate to Order status. The waiter must manually progress the order through ready/serve.

---

## 14. Stock Deduction Mechanism

**Service**: `BarOrderStockService`

### checkAvailability()

```
For each non-cancelled OrderItem:
  For each MenuItemIngredient (ingredient):
    Find StockLevel where product_id + location_id
    required = ingredient.quantity * orderItem.quantity
    available = StockLevel.available_qty
    If available < required → abort with error
```

### deductForOrder()

```
DB::transaction {
  ├─ Order::lockForUpdate() → abort if already deducted (idempotency)
  ├─ checkAvailability() → abort(422) if insufficient
  │
  ├─ For each non-cancelled item's ingredients:
  │    StockMovement::record(
  │      type = 'recipe_use',
  │      quantity = ingredient.quantity * item.quantity
  │    )
  │    → This updates StockLevel.quantity via lockForUpdate
  │    → Aborts if stock would go negative
  │
  └─ order->update(stock_deducted_at = now())
}
```

### reverseForCancelledOrder()

```
DB::transaction {
  ├─ Order::lockForUpdate() → skip if not deducted or already reversed
  │
  ├─ For each non-cancelled item's ingredients:
  │    StockMovement::record(
  │      type = 'restock',
  │      quantity = ingredient.quantity * item.quantity
  │    )
  │
  └─ order->update(stock_reversed_at = now())
}
```

### Stock Deduction Timing by Flow

| Flow | When Stock is Deducted |
|---|---|
| Restaurant POS (walk-in) | Immediately at order creation |
| Restaurant POS (guest) | Immediately at order creation |
| Bartender POS (walk-in) | Immediately at order creation |
| Bartender POS (guest) | Immediately at order creation |
| Restaurant manual settle | At settle() call |
| Bartender serve from restaurant | At serveOrder() call |
| Bartender serve from reception | At serveOrder() call |

---

## 15. Billing Integration

**Service**: `ModuleBillingService`

### syncOrderCharge()

```
Called when:
  - OrderController@serve() for guest orders (status='served')
  - OrderController@storePos() for guest orders (status='charged')
  - BartenderController@serveOrder() for reception/room_service orders
  - BartenderController@storePos() for guest orders
  - OrderController@settle() for guest manual orders

Logic:
  1. Skip if no booking_id or status='cancelled'
  2. Determine charge_type and source from order location/source:
     - order_source='room_service' → charge_type='room_service', source='room_service'
     - location.code contains 'bar' → charge_type='bar', source='bar'
     - Otherwise → charge_type='restaurant', source='restaurant'
  3. Convert TZS total to USD using CurrencyHelper::getExchangeRate()
  4. If existing unpaid charge: update it
  5. If existing paid charge: log warning, return unchanged
  6. Otherwise: create new BookingCharge (status='unpaid')
  7. Set order->billed_to_folio_at = now()
```

### Finalize at Checkout

```
ModuleBillingService::finalizeCharges()
Called during Finance Checkout when guest actually pays.

For each charge:
  If charge_type IN ['restaurant', 'bar', 'room_service']:
    ├─ Find Order by charge.order_id or charge.reference_id
    ├─ order->update(status='settled', settled_by, settled_at)
    └─ AccountingService::postRestaurantSettlement()

  If charge_type = 'laundry':
    ├─ Find LaundryOrder
    └─ Update status, post accounting

  If charge_type = 'restaurant' AND no order_id (buffet):
    └─ buffetSale->update(status='settled')
```

### voidChargeForOrder()

```
Called on order cancellation.
Deletes unpaid BookingCharge rows for the order.
Does NOT void paid charges.
```

---

## 16. Receipt Generation

**Service**: `ReceiptService`

```
ReceiptService::getOrCreateReceipt(model)
  ├─ If receipt already exists for this model → return existing
  └─ Otherwise: create from model.toReceiptData()

Order::toReceiptData() returns:
  - receipt_no = order_number
  - module = 'bar' or 'restaurant' (based on location.slug)
  - Items from order.items (non-cancelled)
  - Totals, payment info, cashier name

BuffetSale::toReceiptData() returns:
  - receipt_no = sale_number
  - module = 'restaurant'
  - Single item with package name and counts

Receipt number format: Uses order_number or sale_number directly
```

**Idempotency**: `getOrCreateReceipt()` checks for existing receipt first, preventing duplicates.

---

## 17. Accounting Integration

**Service**: `AccountingService::postRestaurantSettlement()`

```
For every settled order/buffet:
  DR: Cash(1100) or Bank(1200) — full amount
  CR: F&B Revenue(4200)       — net amount (total / 1.18)
  CR: VAT Payable(2200)       — VAT amount (total - net)

VAT calculation: divides by 1.18 (18% VAT inclusive)
Cash account code: '1100' for cash/mobile_money, '1200' for card

Journal entry is balanced (debits = credits)
Source: 'restaurant', reference: order_number
```

**Gap**: Walk-in bar POS sales (`BartenderController@storePos`) do NOT create accounting entries, FinancePayment records, or FinancialTransaction records. Only walk-in restaurant POS sales go through the full financial trail.

---

## 18. Key Services & Their Roles

| Service | File | Purpose |
|---|---|---|
| `BarOrderStockService` | `app/Services/Bartender/BarOrderStockService.php` | Stock check, deduction, and reversal for orders |
| `OrderDispatchService` | `app/Services/OrderDispatchService.php` | Splits order items into KitchenTicket/BarTicket based on destination |
| `ModuleBillingService` | `app/Services/Billing/ModuleBillingService.php` | Syncs BookingCharge, voids charges, finalizes at checkout |
| `AccountingService` | `app/Services/AccountingService.php` | Posts double-entry journal entries for settlements |
| `ReceiptService` | `app/Services/ReceiptService.php` | Idempotent receipt creation from ReceiptPrintable models |
| `CurrencyHelper` | `app/Helpers/CurrencyHelper.php` | TZS↔USD conversion using system exchange rate |

---

## 19. Order Status Reference

| Status | Meaning | Next Valid States |
|---|---|---|
| `open` | Created, items can be added/removed | sent, cancelled, charged (POS), settled (POS) |
| `sent` | Dispatched to kitchen/bar | ready, cancelled |
| `ready` | Kitchen/bar has prepared the food | served, cancelled |
| `served` | Delivered to table/guest | charged, settled (via finalize), cancelled |
| `charged` | Added to guest folio (BookingCharge created) | settled (via finalize) |
| `settled` | Fully paid/completed | — (terminal) |
| `cancelled` | Cancelled, stock reversed if applicable | — (terminal) |

---

## 20. Bartender Status Reference

| Status | Meaning | Next Valid States |
|---|---|---|
| `pending` | Awaiting bartender action | accepted, rejected, cancelled |
| `accepted` | Bartender acknowledged | prepared |
| `prepared` | Drink is ready | served |
| `served` | Delivered to guest/room | — (terminal) |
| `rejected` | Bartender declined the order | — |
| `cancelled` | Order cancelled | — (terminal) |

---

## 21. API Endpoints

### Restaurant Module (`/restaurant`)

| Method | Route | Controller | Description |
|---|---|---|---|
| GET | `/restaurant/pos` | OrderController@pos | POS screen |
| POST | `/restaurant/pos` | OrderController@storePos | Process POS sale |
| GET | `/restaurant/orders` | OrderController@index | List orders |
| GET | `/restaurant/orders/create` | OrderController@create | Create order form |
| POST | `/restaurant/orders` | OrderController@store | Create order |
| GET | `/restaurant/orders/{order}` | OrderController@show | Order detail |
| POST | `/restaurant/orders/{order}/send` | OrderController@send | Send to kitchen/bar |
| POST | `/restaurant/orders/{order}/ready` | OrderController@ready | Mark ready |
| POST | `/restaurant/orders/{order}/serve` | OrderController@serve | Mark served |
| POST | `/restaurant/orders/{order}/settle` | OrderController@settle | Settle/charge to booking |
| POST | `/restaurant/orders/{order}/cancel` | OrderController@cancel | Cancel order |
| POST | `/restaurant/orders/{order}/items` | OrderController@addItem | Add item |
| DELETE | `/restaurant/orders/{order}/items/{item}` | OrderController@removeItem | Remove item |
| GET | `/restaurant/kitchen/queue` | KitchenController@queue | Kitchen queue |
| POST | `/restaurant/kitchen/tickets/{ticket}/preparing` | KitchenController@markPreparing | Mark preparing |
| POST | `/restaurant/kitchen/tickets/{ticket}/ready` | KitchenController@markReady | Mark ready |
| GET | `/restaurant/bar/queue` | BarController@queue | Bar ticket queue |
| GET | `/restaurant/bar/tabs` | BarController@tabs | Bar tab orders |
| POST | `/restaurant/bar/tickets/{ticket}/preparing` | BarController@markPreparing | Mark preparing |
| POST | `/restaurant/bar/tickets/{ticket}/ready` | BarController@markReady | Mark ready |

### Bartender Module (`/bartender`)

| Method | Route | Controller | Description |
|---|---|---|---|
| GET | `/bartender` | BartenderController@dashboard | Dashboard |
| GET | `/bartender/pos` | BartenderController@pos | Bar POS screen |
| POST | `/bartender/pos` | BartenderController@storePos | Process bar POS sale |
| GET | `/bartender/orders` | BartenderController@inbox | Orders inbox |
| GET | `/bartender/orders/{order}` | BartenderController@showOrder | Order detail |
| POST | `/bartender/orders/{order}/accept` | BartenderController@acceptOrder | Accept order |
| POST | `/bartender/orders/{order}/prepare` | BartenderController@prepareOrder | Prepare order |
| POST | `/bartender/orders/{order}/serve` | BartenderController@serveOrder | Serve order |
| POST | `/bartender/orders/{order}/reject` | BartenderController@rejectOrder | Reject order |
| POST | `/bartender/orders/{order}/cancel` | BartenderController@cancelOrder | Cancel order |
| GET | `/bartender/drink-inbox` | BartenderController@drinkInbox | Reception drink orders |
| GET | `/bartender/stock` | BartenderController@stock | Bar stock levels |
| GET | `/bartender/walkin-sales` | BartenderController@walkinSalesReport | Walk-in sales report |

### Other Entry Points

| Method | Route | Controller | Description |
|---|---|---|---|
| GET | `/drinks/request` | DrinkRequestController@create | Drink request form |
| POST | `/drinks/request` | DrinkRequestController@store | Submit drink request |
| GET | `/buffet` | BuffetPosController@index | Buffet POS screen |
| POST | `/buffet` | BuffetPosController@store | Process buffet sale |
| GET | `/restaurant/buffet/sales` | BuffetController@index | Buffet sales list |
| POST | `/restaurant/buffet/sales` | BuffetController@store | Create buffet sale |
| POST | `/restaurant/buffet/sales/{sale}/charge-booking` | BuffetController@chargeToBooking | Charge buffet to folio |
| POST | `/restaurant/buffet/sales/{sale}/settle-walkin` | BuffetController@settleWalkin | Settle buffet walk-in |

---

## 22. Known Issues & Bug-Prone Areas

### Critical Issues

| # | Issue | Location | Impact |
|---|---|---|---|
| 1 | **Walk-in bar POS sales missing financial records** | `BartenderController@storePos` lines ~300-370 | No FinancePayment, FinancialTransaction, or AccountingService journal entry is created for walk-in bar sales. Only the order gets status='settled' and a receipt. Food walk-in POS sales create all three. |
| 2 | **Order number race condition** | `Order::booted()` ~line 44 | `$count = self::whereDate('created_at', today())->count() + 1` is not atomic. Two concurrent orders can get the same order_number. |
| 3 | **BuffetSale number race condition** | `BuffetSale::booted()` ~line 48 | Same race condition as Order numbers. |
| 4 | **POS null receipt crash** | `OrderController@storePos` ~line 274 | `getOrCreateReceipt($order ?? $buffetSales[0] ?? null)` — if both are null, TypeError is thrown. |

### Stock Issues

| # | Issue | Location | Impact |
|---|---|---|---|
| 5 | **Kitchen stock and ingredient stock are disconnected** | `KitchenStockController` vs `BarOrderStockService` | Kitchen staff see different numbers than the ingredient-based deduction system. |
| 6 | **Stock deducted at different points depending on flow** | Multiple controllers | POS flows deduct at creation; manual orders deduct at settle/serve. This means walk-in POS orders immediately reduce stock, while manual orders reduce stock much later — creating availability discrepancies. |

### Billing Issues

| # | Issue | Location | Impact |
|---|---|---|---|
| 7 | **No booking date validation on charge_to_booking** | `OrderController@storePos`, `BartenderController@storePos` | Only checks `$booking->status === 'checked_in'`, doesn't validate booking dates overlap with today. |
| 8 | **Double-settlement risk for guest orders** | `OrderController@settle` and `ModuleBillingService::finalizeCharges` | Both can mark an order as 'settled'. Concurrent requests could cause double-charging. |
| 9 | **serveOrder() error handling doesn't rollback** | `BartenderController@serveOrder` ~line 356-369 | Catches `\Throwable`, stores `billing_error`, but doesn't rollback DB changes. Partial state possible. |

### Data Integrity Issues

| # | Issue | Location | Impact |
|---|---|---|---|
| 10 | **`syncBarProductsToMenu()` runs on every POS load** | `BartenderController@pos` ~line 151 | Auto-creates MenuItems from Products with no transaction wrapping. Partial creation possible. |
| 11 | **DrinkRequestController creates MenuItem on-the-fly without dedup** | `DrinkRequestController@store` ~line 98-108 | Concurrent drink requests for the same product could create duplicate MenuItems. |
| 12 | **MenuOptionGroup soft-delete leaves pivot rows** | `MenuOptionGroupController@destroy` ~line 147-149 | Soft-deleted option groups remain in `menu_item_option_group` pivot table. |
| 13 | **Order tax is always 0** | `Order::recalculate()` | Tax is never calculated during order placement, but `AccountingService::postRestaurantSettlement()` extracts 18% VAT from the total. This creates a mismatch where the order says 0 tax but accounting splits it as if tax is included. |

### Validation Gaps

| # | Issue | Location | Impact |
|---|---|---|---|
| 14 | **POS storePos doesn't validate menu items belong to kitchen** | `OrderController@storePos` ~line 196-208 | Manual `store()` validates this in `buildOrderItemPayload()`, but POS doesn't. Wrong-location items could be ordered. |
| 15 | **No stock pre-validation in POS** | `OrderController@storePos`, `BartenderController@storePos` | Stock availability is only checked inside `deductForOrder()`. If insufficient stock, the entire transaction aborts with 422 — no friendly "out of stock" feedback before attempting. |
| 16 | **BuffetPosController@store doesn't validate package availability** | `BuffetPosController@store` | `BuffetController@store` calls `isPackageAvailableNow()`, but `BuffetPosController@store` does NOT. |

---

## 23. Bug Fix: Restaurant Cannot Progress Bar Orders (RESOLVED)

### Problem

When a restaurant order was created at the bar location (`location_id = bar`, `order_source = 'restaurant'`), the `isBartenderManagedBarOrder()` check blocked `ready()` and `serve()` for the ENTIRE order, preventing restaurant staff from managing the order lifecycle. The error was:

```
Symfony\Component\HttpKernel\Exception\HttpException
Bar drink orders are prepared from the bartender desk.
```

### Root Cause

`OrderController@ready()` and `OrderController@serve()` called `abort_if($this->isBartenderManagedBarOrder($order), 422, ...)` which returned `true` for any order where `order_source === 'restaurant'` AND `location.code` contains 'bar'. This blocked ALL restaurant workflow actions for bar orders, with no way for restaurant staff to progress the order.

The `bartender_status` field was intended to track bar item preparation independently, but the hard block prevented the restaurant from managing the overall order lifecycle at all.

### Fix Applied

**File:** `app/Http/Controllers/Restaurant/OrderController.php`

1. **Removed** the `isBartenderManagedBarOrder()` check from `ready()` — Restaurant staff can now mark orders as ready regardless of whether they contain bar items.

2. **Removed** the `isBartenderManagedBarOrder()` check from `serve()` — Restaurant staff can now mark orders as served. The bartender handles bar item preparation through their own independent `bartender_status` flow.

3. **Kept** the `isBartenderManagedBarOrder()` check in `settle()` — Before billing a guest, the bartender must confirm drinks were served (`bartender_status === 'served'`). This ensures guests aren't charged for undelivered drinks.

4. **Updated** the order show view (`resources/views/restaurant/orders/show.blade.php`) to:
   - Display bartender status badge for bar orders with a link to the bartender order view
   - Show an informational banner when bar items haven't been served yet
   - Only show the walk-in redirect message when bartender hasn't served the bar items

### How the Two Flows Now Work Together

**Restaurant flow (manages overall order):**
```
open → sent → ready → served → charged/settled
```

**Bartender flow (manages bar item preparation):**
```
pending → accepted → prepared → served
```

These are independent. The restaurant can progress `status` while the bartender progresses `bartender_status`. The only coupling point is `settle()`, which requires `bartender_status === 'served'` before billing guest orders with bar items.