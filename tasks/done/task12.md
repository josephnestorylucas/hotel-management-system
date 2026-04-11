
You are working inside a production Laravel 11 hotel management system.

The system already has:
- Guests/customers
- Restaurant + Bar ordering flows
- Room bookings and a guest billing/folio (collectable billing) system
- Checkout/payment + receipt printing flows
- Stock/inventory concepts (locations/items/quantities)

Your task is to ADD bartender-only functionality for:
- Viewing bar stock
- Handling drink orders (walk-in guests, waiters, room service)
- Reporting damages/spillage/breakage
- Ensuring drink sales deduct stock automatically
- Ensuring drink charges go into the EXISTING billing flows

DO NOT redesign the system.
DO NOT remove or modify existing functionality.
ONLY ADD bartender functionality and CONNECT to existing billing/receipt/stock patterns.

========================================
OBJECTIVE
===================================
please add bartender module/view that enables a bartender to:

1) See bar stock (their assigned bar stock location)
2) Receive and process drink orders from:
   - Guests (walk-in)
   - Waiters (restaurant orders)
   - Room service (via HMS)
3) Check drink availability before confirming a drink order
4) Add drink charges into the EXISTING billing system:
   - Guest room bill / folio (collect later at checkout)
   - Restaurant bill (included in restaurant order totals)
   - Walk-in immediate payments + receipt
5) Record damages (breakages/spillage/expired items) and auto-deduct stock with an audit trail
6) Auto deduct stock when a sale is completed (and when damage is reported)

========================================
IMPLEMENTATION RULES
========================================

- DO NOT delete or change existing flows/controllers/models in a breaking way
- ADD new routes/views/controllers/services as needed for the bartender role
- REUSE existing models and billing patterns where possible (orders, charges, receipts, payments)
- NO hardcoded amounts or item lists
- All stock changes MUST be atomic and logged (transaction + audit record)
- If an upstream order is cancelled/unpaid, handle reversals safely (no negative stock)
- Use authorization/roles so only bartenders/managers can access bartender views

========================================
STEP 1: BARTENDER ACCESS + ENTRY POINT
========================================

edit a bartender-only entry point:
- A dedicated bartender page (dashboard) OR a dedicated menu section
- add  also  the   new sections  to  the  bartender  siderbar  
Authorization:
- Ensure only users with bartender role/permission can access bartender routes
- Managers/admins may also access for oversight

========================================
STEP 2: BARTENDER STOCK VIEW (BAR INVENTORY)
========================================

Create a bartender stock view that shows ONLY the bar stock location:
- Drink item name
- SKU/code (if available)
- Current quantity
- Unit of measure
- Reorder threshold (if system has it)
- Last updated timestamp

Requirements:
- Must reflect REAL stock (no fake values)
- Must be read-only for normal bartenders (editing stock should remain admin/stock-controller responsibility unless already allowed)

========================================
STEP 3: DAMAGE REPORTING (SPILLAGE/BREAKAGE/EXPIRED)
========================================

Add bartender functionality to report damaged stock:

Damage report form fields:
- Stock item (drink)
- Quantity damaged
- Reason (spillage/breakage/expired/other)
- Notes (optional)
- Reported by (current user)
- Date/time

Processing rules:
- Validate quantity > 0
- Validate sufficient stock exists before deduction
- Deduct stock immediately inside a DB transaction
- Create an immutable damage/audit record (who/what/qty/when/why)

Reporting:
- Provide a bartender view to list damage reports (filtering optional; keep minimal)

========================================
STEP 4: DRINK ORDER SOURCES + UNIFIED INBOX
========================================

Implement a bartender “Incoming Drink Orders” view.

It must show drink orders coming from:

A) Walk-in Guests:
- Created directly by bartender at time of sale

B) Waiters (Restaurant orders):
- Created from restaurant flow as a request to bar
- Must reference the original restaurant order (so billing goes to restaurant)

C) Room Service (HMS):
- Created as a request linked to a booking/room
- Must bill to guest room folio/collectable billing

Inbox list should show:
- Source (walk-in / restaurant / room service)
- Reference number (restaurant order number OR booking number OR walk-in ticket number)
- Items + quantities
- Requested time
- Status (pending/accepted/prepared/served/cancelled/rejected)

========================================
STEP 5: AVAILABILITY CHECK + ORDER CONFIRMATION
========================================

Before a bartender accepts/confirms a drink order:
- Check stock availability for every drink line item
- If insufficient stock:
  - Do not confirm
  - Provide a clear reason (out of stock / insufficient quantity)

Once accepted:
- Move status forward (e.g., pending → accepted)
- Prevent double-processing (idempotent updates)

========================================
STEP 6: BILLING INTEGRATION (MUST USE EXISTING FLOWS)
========================================

A) Walk-in Guest Billing (Immediate)
- Create a walk-in drink sale/ticket
- Collect payment immediately using the existing payment approach
- Generate/print a receipt using the existing receipt printing system
- Mark the walk-in order as paid/settled

B) Restaurant Billing (Waiter Orders)
- Ensure drink items end up on the restaurant bill:
  - Either add drink line-items to the existing restaurant order
  - OR create a linked bar order that is settled together with the restaurant bill
- The restaurant cashier/settlement flow must still work as-is
- Bartender’s action must NOT break restaurant totals, settlement, or receipt printing

C) Room Service / Guest Room Bill (Collectable Billing)
- When drinks are served for a booking:
  - Add the charge into the existing guest folio/collectable billing system
  - It must appear at checkout and be payable in the unified checkout flow
- Ensure the charge references the drink order for traceability

========================================
STEP 7: AUTO STOCK DEDUCTION (SALE COMPLETION)
========================================

Stock must be deducted automatically when the sale is actually completed:
- Walk-in: deduct on payment completion/settlement
- Restaurant/Room service: deduct when bartender marks “served/fulfilled” OR when the linked order is finalized (pick ONE consistent point; do not double-deduct)

Rules:
- Deduct inside a DB transaction
- Prevent double-deduction if the same order is saved twice
- If an order is cancelled AFTER deduction:
  - Create a stock reversal adjustment (if business rules allow) OR require manager override

========================================
STEP 8: RECEIPTS (WALK-IN) + TRACEABILITY (ALL SOURCES)
========================================

Walk-ins:
- Must produce a printable receipt
- Receipt must include: items, quantities, prices, totals, payment method, cashier/bartender

All sources:
- Ensure each drink order has a reference number
- Ensure billing records store the reference so finance can trace charges back to the drink order

========================================
STEP 9: ERROR HANDLING + AUDIT
========================================

- Drink order creation must not crash if optional data is missing (e.g., guest phone)
- If billing linkage fails, fail safely (do not lose the order) and log the error
- Log stock deduction and damage deductions (who/what/qty/when/order reference)

========================================
STEP 10: TEST FLOW (MANUAL ACCEPTANCE)
========================================

Test cases:

1) Bartender stock view loads and shows current quantities
2) Damage report:
   - report damage with valid quantity → stock decreases, audit record created
   - report damage with too-large quantity → blocked with validation error
3) Walk-in sale:
   - create walk-in drink order → pay → receipt generated → stock deducted
4) Waiter order:
   - restaurant requests drinks → bartender sees it → accepts → serves
   - drinks appear on restaurant bill → settlement works → receipt prints
5) Room service:
   - booking requests drinks → bartender serves
   - charge appears in guest folio/unified checkout → can be paid at checkout
6) Idempotency:
   - refreshing/duplicating submit does NOT double-deduct stock

========================================
OUTPUT REQUIRED
========================================

1) Bartender routes + views created (paths)
2) Stock view: what data is shown and from which stock location
3) Damage reporting: where records are stored + how stock is deducted
4) Drink order inbox: sources supported + statuses
5) Billing integration: how each source maps to (walk-in receipt / restaurant bill / guest folio)
6) Proof stock deduction triggers exactly once per sale

========================================
GOAL
========================================

- Bartenders can manage bar operations without touching admin stock tools
- All drink sales are billable through the system’s existing billing/receipt flows
- Stock is always accurate (sales + damages)
- No existing functionality is modified or broken

DO NOT OVERCOMPLICATE.
ADD ONLY WHAT IS REQUIRED FOR BARTENDER.
