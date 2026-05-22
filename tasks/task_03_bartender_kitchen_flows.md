# Task 03 — Implement Bartender & Kitchen Flows

## Overview
Implement the full order routing system that splits orders between Kitchen (KOT) and Bar (BOT), supports room service, bar tabs, and quick/takeaway orders.

---

## Core Models Required

```
Customer / Guest
Table
Order
OrderItem
KitchenTicket (KOT)
BarTicket (BOT)
Invoice
Payment
InventoryMovement
Shift
CashDrawer
Staff
```

---

## Flow 1 — Normal Dine-In Order

```
Customer Arrives
    ↓ Host/Waiter Assigns Table
    ↓ Waiter Creates Order
    ↓ Items Added To Order
    ↓ System Splits:
        → Kitchen Items → KitchenTicket (KOT)
        → Bar Items     → BarTicket (BOT)
    ↓ Kitchen/Bar Prepare Items
    ↓ Items Marked Ready
    ↓ Waiter Serves Customer
    ↓ Customer Requests Bill
    ↓ Invoice Generated
    ↓ Payment Collected
    ↓ Sale Finalized
    ↓ Inventory Deducted
    ↓ Receipt Printed
    ↓ Order Closed
```

### Implementation Checklist

#### Orders
- [ ] `orders` table: `id, table_id, order_type (dine_in/room_service/bar_tab/takeaway), status, cashier_id, waiter_id, timestamps`
- [ ] `order_items` table: `id, order_id, menu_item_id, quantity, unit_price, destination (kitchen/bar), status (pending/preparing/ready/served), notes`
- [ ] Order status flow: `open → processing → ready → served → billed → closed`

#### Auto-Split Logic
```php
// OrderItem destination is set by menu_item.destination

// When order is submitted:
public function splitAndDispatch(Order $order)
{
    $kitchenItems = $order->items->where('destination', 'kitchen');
    $barItems     = $order->items->where('destination', 'bar');

    if ($kitchenItems->count() > 0) {
        KitchenTicket::create([
            'order_id'   => $order->id,
            'table_id'   => $order->table_id,
            'items'      => $kitchenItems->toJson(),
            'status'     => 'pending',
            'printed_at' => now(),
        ]);
    }

    if ($barItems->count() > 0) {
        BarTicket::create([
            'order_id'   => $order->id,
            'table_id'   => $order->table_id,
            'items'      => $barItems->toJson(),
            'status'     => 'pending',
            'printed_at' => now(),
        ]);
    }
}
```

- [ ] `menu_items` must have a `destination` field: `enum('kitchen', 'bar')`
- [ ] Auto-split fires when order status changes from `open` to `processing`
- [ ] Both KOT and BOT print/display immediately

#### KOT (Kitchen Order Ticket)
- [ ] `kitchen_tickets` table: `id, order_id, table_id, items (json), status (pending/preparing/ready), notes, printed_at`
- [ ] Kitchen screen shows all pending KOTs
- [ ] Chef clicks "Preparing" → status updates
- [ ] Chef clicks "Ready" → waiter notified
- [ ] Kitchen view: `/kitchen/queue` (auto-refresh every 15s)

#### BOT (Bar Order Ticket)
- [ ] `bar_tickets` table: `id, order_id, table_id, items (json), status (pending/preparing/ready), notes, printed_at`
- [ ] Bar screen shows all pending BOTs
- [ ] Bartender marks items ready
- [ ] Bar view: `/bar/queue` (auto-refresh every 15s)

---

## Flow 2 — Room Service Order

```
Guest Calls Room Service
    ↓ Staff Creates Room Service Order
    ↓ Room Number Validated
    ↓ Charges Linked To Room Folio
    ↓ Kitchen/Bar Tickets Generated
    ↓ Items Prepared
    ↓ Delivered To Room
    ↓ Guest Signs / PIN Verification
    ↓ Charges Posted To Room
    ↓ Final Payment Happens During Checkout
```

### Implementation Checklist

- [ ] Order type: `room_service`
- [ ] `order.room_id` FK to `room_bookings`
- [ ] Validate room is occupied (active booking exists)
- [ ] Post charges to guest folio: `guest_folios` table or `room_charges`
- [ ] KOT/BOT generated same as dine-in
- [ ] Delivery confirmation: `order.delivered_at` timestamp
- [ ] Guest verification: PIN or staff signature flag (`order.guest_confirmed = true`)
- [ ] Room folio entries auto-created:
```php
RoomCharge::create([
    'room_booking_id' => $room->current_booking_id,
    'order_id'        => $order->id,
    'description'     => 'Room Service - Order #' . $order->id,
    'amount'          => $order->total,
    'charged_at'      => now(),
]);
```
- [ ] Charges settle automatically during room checkout

---

## Flow 3 — Bar Tab

```
Customer Sits
    ↓ Bartender Opens Tab
    ↓ Drinks Added
    ↓ Items Served Immediately
    ↓ Running Tab Updated
    ↓ Customer Pays
    ↓ Invoice Generated
    ↓ Sale Closed
```

### Implementation Checklist

- [ ] Order type: `bar_tab`
- [ ] Tab stays `open` until customer requests bill
- [ ] Items added progressively (multiple `order_items` inserts)
- [ ] Running total visible on bar screen in real-time
- [ ] BOT generated per item add (or in batch)
- [ ] Customer closes tab → Invoice generated → Payment → Closed
- [ ] Bar Tab view at `/bar/tabs` (list of open tabs by seat/name)

---

## Flow 4 — Quick Sale / Takeaway

```
Cashier Creates Quick Order
    ↓ Items Selected
    ↓ Payment FIRST
    ↓ Kitchen/Bar Ticket Generated
    ↓ Preparation
    ↓ Pickup
    ↓ Order Closed
```

### Implementation Checklist

- [ ] Order type: `takeaway`
- [ ] No table assigned (`table_id = null`)
- [ ] Payment collected BEFORE KOT/BOT generated
- [ ] On payment success → trigger `splitAndDispatch()`
- [ ] Customer name optional (`order.customer_name`)
- [ ] Pickup notification on kitchen/bar when ready
- [ ] Order status: `paid → preparing → ready → picked_up`

---

## Database Migrations Checklist

```bash
php artisan make:migration add_destination_to_menu_items_table
php artisan make:migration create_kitchen_tickets_table
php artisan make:migration create_bar_tickets_table
php artisan make:migration add_order_type_to_orders_table
php artisan make:migration create_room_charges_table
```

- [ ] `menu_items.destination` enum: `kitchen | bar`
- [ ] `kitchen_tickets` table created
- [ ] `bar_tickets` table created
- [ ] `orders.order_type` enum: `dine_in | room_service | bar_tab | takeaway`
- [ ] `orders.room_id` nullable FK
- [ ] `room_charges` table created

---

## Views Checklist

| View | Route | Purpose |
|------|-------|---------|
| Kitchen Queue | `/kitchen/queue` | KOTs pending/preparing |
| Bar Queue | `/bar/queue` | BOTs pending/serving |
| Bar Tabs | `/bar/tabs` | Open bar tabs |
| Room Service | `/pos/room-service` | Room order creation |
| Quick Sale | `/pos/quick-sale` | Takeaway POS |

---

## Permissions Checklist

| Role | Access |
|------|--------|
| waiter | Create dine-in orders |
| bartender | Bar queue, bar tabs |
| kitchen_staff | Kitchen queue only |
| cashier | All POS, payments |
| supervisor | All views + reports |

---

## Done When
- [ ] Dine-in order routes items to kitchen and/or bar correctly
- [ ] Kitchen screen shows only KOTs
- [ ] Bar screen shows only BOTs
- [ ] Room service posts to guest folio
- [ ] Bar tab can be opened, items added, then closed
- [ ] Takeaway collects payment first, then sends ticket
- [ ] All order types produce correct Invoice at close
