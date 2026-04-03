
You are working inside a production Laravel 11 hotel management system.

The system already has:
- A Finance module handling checkout
- BookingCharge as the central billing mechanism
- Multiple service modules (booking, reservation, restaurant, bar, laundry)

Problem:
Some modules are finalizing transactions WITHOUT going through the unified checkout process.

Your task is to ENFORCE a single checkout flow across ALL billable modules.

DO NOT redesign architecture.
DO NOT duplicate checkout logic.
USE the existing Finance checkout system.

========================================
OBJECTIVE
========================================

Ensure that ALL billable actions are completed through a checkout process that:

1. Aggregates charges
2. Processes payment
3. Records transactions
4. Marks services as settled

Modules to enforce:
- Booking
- Reservation
- Restaurant
- Bar
- Laundry (guest-based only, not walk-in)

========================================
IMPLEMENTATION RULES
========================================

- DO NOT allow direct “settle” without checkout (for booked guests)
- ALL charges must go through BookingCharge
- Checkout must be the ONLY place where payment is finalized
- Walk-ins remain separate (handled independently)

========================================
STEP 1: IDENTIFY BYPASS FLOWS
========================================

Find places where:

- Orders are marked “settled” directly
- Payments are processed inside module controllers
- No redirect to checkout occurs

Focus on:

- RestaurantController / OrderController
- Bar-related controllers
- Booking / Reservation finalization

========================================
STEP 2: STANDARDIZE CHARGE CREATION
========================================

Ensure ALL billable services create:

BookingCharge entries with:

- booking_id
- charge_type (restaurant, bar, laundry, reservation, etc.)
- reference_id
- amount
- status = unpaid

NO direct payment at this stage

========================================
STEP 3: REMOVE DIRECT PAYMENT LOGIC
========================================

From modules:

- Remove payment handling
- Remove direct “paid” status updates

Replace with:

→ Create charge ONLY

========================================
STEP 4: REDIRECT TO CHECKOUT
========================================

After creating charges:

- Redirect user to Finance checkout page

Example flow:

Service Used → Charge Created → Redirect to Checkout

========================================
STEP 5: VERIFY CHECKOUT AGGREGATION
========================================

Checkout must:

- Fetch ALL unpaid BookingCharge records
- Include ALL modules:
  - restaurant
  - bar
  - laundry
  - reservation

Ensure no charge_type is excluded

========================================
STEP 6: PAYMENT HANDLING
========================================

ONLY checkout should:

- Call Snippe
- Process payment
- Update transaction status

========================================
STEP 7: POST-PAYMENT FINALIZATION
========================================

After successful payment:

- Mark BookingCharge as paid
- Update related modules:

Restaurant/Bar:
- order → settled

Laundry:
- order → settled

Reservation/Booking:
- status → confirmed/paid

========================================
STEP 8: UI CONSISTENCY
========================================

Ensure:

- All modules use same “Proceed to Checkout” action
- No “Pay Now” scattered across modules
- Clear user flow

========================================
STEP 9: EDGE CASES
========================================

Handle:

- Multiple charges across modules
- Partial usage before checkout
- Returning to checkout later

========================================
STEP 10: WALK-IN EXCEPTION
========================================

IMPORTANT:

Walk-ins:
- DO NOT use BookingCharge
- DO NOT use checkout page
- Use direct payment modal (already implemented)

========================================
STEP 11: TEST FLOW
========================================

Test:

Booking:
- Add services → go to checkout → pay → success

Restaurant (guest):
- Order → charge created → checkout → pay

Bar:
- Same flow

Laundry (guest):
- Charge → checkout → pay

========================================
STEP 12: FINAL VALIDATION
========================================

Confirm:

- No module settles without checkout (guest-based)
- All payments happen in ONE place
- Transactions are properly recorded
- No duplicated payment logic

========================================
OUTPUT REQUIRED
========================================

1. Modules updated to use checkout
2. Removed direct payment points
3. Confirmation all charges flow through BookingCharge
4. Confirmation checkout handles all billing

========================================
GOAL
========================================

- One checkout system
- Clean billing flow
- No inconsistencies
- Production-safe behavior

FORCE EVERYTHING THROUGH CHECKOUT.
NO SHORTCUTS.