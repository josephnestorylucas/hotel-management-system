# Task 06 — Restaurant Manager & Cashier Flows + Simplified Kitchen Stock

## Overview
Implement clean, role-scoped flows for Restaurant Manager and Cashier. Kitchen stock uses **simple manual monitoring** — no ingredient tracking, no recipe deduction.

---

## Part A — Restaurant Manager Flow

### Manager Dashboard
```
Login
    ↓ Manage Menu Categories
    ↓ Manage Menu Items
    ↓ Manage Buffet Options
    ↓ Manage Bar Inventory (simple stock)
    ↓ View Sales Reports
    ↓ Manage Transfers / Damages
```

---

### Step 1 — Menu Categories Management



- [ ] Create form: category, name, price, destination (kitchen/bar), image, time window, buffet flag
- [ ] Edit: change price, update image, toggle active
- [ ] Never delete — set `is_active = false`
- [ ] List: filterable by category, destination, status

---

#### Flow
```
Manager Opens Menu Item
    ↓ Click "Manage Options"
    ↓ See linked options
    ↓ Add existing option OR create new
    ↓ Set price adjustment
    ↓ Save
```

#### Examples

| Item | Option | Extra Price |
|------|--------|-------------|
| Pilau | Beef | +2,000 |
| Pilau | Chicken | +3,000 |
| Pilau | Beans | +1,000 |
| Tea | Milk | +500 |
| Tea | Black | 0 |

- [ ] Options pool (global, reusable)
- [ ] Attach options to specific items
- [ ] Set price adjustment per item-option pair
- [ ] Remove option from item (not delete from pool)

---

```
Manager Opens Buffet Module
    ↓ Create Buffet Session
    ↓ Name: Lunch Buffet
    ↓ Time: 12:00 PM – 4:00 PM
    ↓ Adult: 25,000
    ↓ Child: 15,000
    ↓ Save
```

- [ ] Buffet sessions list
- [ ] Create/edit session
- [ ] Time window + pricing
- [ ] Active/inactive toggle

---

### Step 5 — Simple Kitchen Stock (NO Recipe Tracking)

#### Philosophy
```
DO NOT:
❌ Track rice deductions per plate
❌ Track meat per serving
❌ Calculate cooking analytics
❌ Recipe-based inventory

DO:
✅ Simple stock levels (Rice Bags: 10, Cooking Oil: 5L)
✅ Manager manually adds purchases
✅ Manager records damages/transfers
✅ Low stock alerts
```

```

#### Manager Interface
```
Kitchen Stock
    ↓ [Rice Bags] Current: 8 bags | Min: 3 bags → Status: OK ✅
    ↓ [Cooking Oil] Current: 1 L | Min: 5 L → Status: LOW ⚠️
    ↓ [Meat Stock] Current: 2 kg | Min: 5 kg → Status: LOW ⚠️

Actions per item:
    + Record Purchase (+10 bags)
    - Record Damage (-2 bags, reason: "expired")
    ↔ Record Transfer (-3 bags, to: "Event Kitchen")
    ✏️ Manual Adjustment (set exact value, with reason)
```

``

- [ ] Kitchen stock list shows current qty + status (ok/low)
- [ ] Manager records: purchase, damage, transfer, adjustment
- [ ] Each movement logged with: who, when, quantity, notes
- [ ] Low stock items highlighted in red/orange
- [ ] Stock history per item (movement log)

---

### Step 6 — Sales Reports

- [ ] Daily sales summary (total revenue, orders count, payment methods)
- [ ] Sales by category (which category earned most)
- [ ] Sales by item (top 10 items)
- [ ] Buffet sales report (adults, children, revenue)
- [ ] Date range filter (today, this week, this month, custom)
- [ ] Export as PDF or CSV

---

## Part B — Cashier Flow

### Flow
```
Open POS
    ↓ Select: Table / Walk-In / Room
    ↓ Add Menu Items
    ↓ Generate Bill
    ↓ Take Payment
    ↓ Print Receipt
    ↓ Complete Sale
```

### Cashier Checklist

- [ ] POS screen is default landing page for cashier role
- [ ] Can select: Table, Walk-In (no table), Room (post to folio)
- [ ] Menu items shown by category tabs
- [ ] Options popup for applicable items
- [ ] Running total visible on right panel
- [ ] "Generate Bill" → shows invoice preview
- [ ] Payment: cash, card, mobile money (M-Pesa/Tigopesa)
- [ ] Print receipt after payment
- [ ] Order marked as closed
- [ ] Cashier cannot access:
  - Manager reports (except their own shift sales)
  - Menu management
  - Kitchen stock
  - Settings

---



## Done When
- [ ] Manager can create categories, items, options, buffet sessions
- [ ] Kitchen stock management (simple, no recipes)
- [ ] Sales reports with date filter + export
- [ ] Cashier POS clean and role-scoped
- [ ] Permissions enforced per role
- [ ] No role can access pages outside their scope
