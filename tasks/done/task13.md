
You are working inside a production Laravel 11 hotel management system.

The system already has:
- Unified guest checkout/folio screen (Finance checkout)
- BookingCharge-based billing (charges grouped by type)
- Module orders (Restaurant, Bar, Laundry, Room service, Store, etc.)
- Receipt printing + payment recording

Your task is to ENFORCE that ALL module billings/charges are completed properly.

This means:
- Every module sale/order that should be billable MUST create a billing record (BookingCharge or an equivalent existing billing record)
- Any module that can be “settled/closed” MUST only settle through the correct billing/payment path
- No booking/guest can complete checkout while there are unpaid module charges

DO NOT redesign architecture.
DO NOT create a new billing system.
EXTEND what exists and CONNECT missing enforcement points.

========================================
OBJECTIVE
========================================

Guarantee billing completeness across modules so that:

1) Every billable module action produces a charge that appears in the correct place:
   - Guest folio/unified checkout for room-related/booking-related charges
   - Immediate settlement flows for walk-ins where applicable

2) Modules cannot silently bypass billing:
   - No “served/completed/closed” state should exist while the billing record is missing (if the module is billable)
   - No “settled/paid” state should exist without an actual payment/checkout settlement record

3) Unified checkout is authoritative:
   - Checkout payment marks all related charges as paid
   - Related module orders are finalized/settled only after billing is paid

========================================
IMPLEMENTATION RULES
========================================

- DO NOT break existing module workflows
- DO NOT remove existing statuses; only add enforcement/validation and missing linking
- REUSE existing models (Booking, BookingCharge, Checkout, Order, LaundryOrder, FinancePayment, FinancialTransaction, Receipt)
- Keep logic out of controllers when possible; use existing service/events patterns if present
- All enforcement must be safe:
  - If billing fails, do not lose the order
  - Show a clear validation message
  - Log errors

========================================
STEP 1: DEFINE “BILLABLE” MODULE ACTIONS
========================================

List all modules and actions that must always be billed:
- Restaurant orders
- Bar drink orders
- Room service orders
- Laundry orders
- Store sales
- Any other existing revenue modules

For each module, define:
- When the billing record must be created (charge creation point)
- What the “reference” linking fields are (order_id/reference_id/booking_id)
- What statuses mean (pending/served/settled/cancelled)

========================================
STEP 2: STANDARDIZE CHARGE CREATION (BookingCharge)
========================================

Ensure each billable module creates a BookingCharge when it is linked to a booking/room:
- charge_type must be consistent (e.g., restaurant, bar, room_service, laundry, store)
- description must include order reference
- amount must match module totals
- status must start as unpaid

Rules:
- NEVER create duplicate BookingCharges for the same order
- If module totals change (items added/removed), update the existing unpaid charge amount to match
- Once charge is paid, module totals should be locked OR changes must create an adjustment charge (use simplest approach that matches existing patterns)

========================================
STEP 3: ENFORCE “NO SETTLEMENT WITHOUT BILLING”
========================================

For each module settlement/completion endpoint:

A) Booking-linked orders
- If a booking_id exists:
  - Prevent marking the module order as settled/paid directly
  - Ensure it remains billable via guest folio/unified checkout

B) Walk-in orders
- If no booking_id exists:
  - Require immediate payment settlement before completion
  - Require a receipt/transaction record as proof of payment

Validation requirements:
- Attempting to close/complete a billable order without billing must fail with a user-friendly message

========================================
STEP 4: ENFORCE “NO CHECKOUT COMPLETION WITH UNPAID CHARGES”
========================================

Enforce that:
- A booking cannot be checked out / completed / closed if there are unpaid BookingCharges

Implementation:
- Add validation at the final checkout completion step
- Also validate at any “booking status → checked-out” transitions

========================================
STEP 5: MODULE FINALIZATION MUST FOLLOW CHECKOUT
========================================

Ensure unified checkout completion performs:
- Mark all BookingCharges as paid
- Finalize/settle related module orders (restaurant/bar/room_service/laundry/etc.)
- Record payments + financial transactions

Rules:
- Finalization must be idempotent (running twice must not double-settle)
- Any module already settled should be skipped safely

========================================
STEP 6: EDGE CASES + ADJUSTMENTS
========================================

Handle these cases safely:

1) Cancelled orders
- If order is cancelled before billing: cancel/void the unpaid charge
- If order is cancelled after billing: follow existing refund/adjustment approach (do not invent a new one)

2) Partial payments (if supported)
- Ensure remaining balance is represented correctly
- Ensure module orders do not finalize until fully settled

3) Duplicate submissions
- Prevent duplicate charges and duplicate settlements

========================================
STEP 7: VISIBILITY (MINIMAL)
========================================

Add minimal visibility for managers/cashiers to spot incomplete billing:
- A list/report of:
  - module orders marked completed but missing charges (should be zero after fixes)
  - unpaid charges per booking

Keep it minimal and consistent with existing finance/admin pages.

========================================
STEP 8: TEST FLOW (MANUAL ACCEPTANCE)
========================================

Test cases:

1) Restaurant (booking-linked): create order → BookingCharge created → appears in folio → paid at checkout → order settles
2) Bar (booking-linked): create/serve drinks → charge appears → paid at checkout → order settles
3) Laundry (booking-linked): create order → charge appears → paid at checkout → order settles
4) Walk-in sale: cannot complete without payment record + receipt
5) Attempt to checkout booking with unpaid charges → blocked
6) No duplicates: refreshing pages / double submit does not create extra charges or double settle

========================================
OUTPUT REQUIRED
========================================

1) List of modules enforced + what was changed for each
2) Where charge creation is standardized
3) Where settlement is blocked without billing/payment
4) Proof unified checkout remains the single source of truth
5) Confirmation bookings cannot close with unpaid charges

========================================
GOAL
========================================

- No lost revenue from unbilled module activity
- Unified checkout/folio is always accurate
- Module “settled/paid” states always match real payments
- No broken existing workflows

KEEP IT CLEAN.
NO NEW BILLING SYSTEM.
ONLY ENFORCE AND CONNECT EXISTING FLOWS.
