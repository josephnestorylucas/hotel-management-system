
You are working inside a production Laravel 11 hotel management system.

The system already supports:
- Guest-based billing via BookingCharge
- Unified checkout for booked guests
- Laundry, restaurant, and bar modules
- Snippe payment integration

Problem:
Walk-in customers (non-booked users) are NOT properly handled in payment flow.

Your task is to IMPLEMENT a consistent walk-in payment flow across:
- Laundry
- Restaurant
- Bar

DO NOT redesign the system.
DO NOT break existing guest booking flows.

========================================
OBJECTIVE
========================================

1. When settling walk-in orders:
   - Show payment modal
   - Capture customer identity (name + phone OR card)

2. Process payment using Snippe

3. After successful payment:
   - Mark order as settled
   - Complete service (e.g., release clothes, close order)

4. Ensure same pattern works for:
   - Laundry
   - Restaurant
   - Bar

========================================
IMPLEMENTATION RULES
========================================

- DO NOT mix walk-in with booking flow
- DO NOT create BookingCharge for walk-ins
- Walk-ins are standalone transactions
- MUST capture customer identity before payment
- MUST NOT allow settlement without payment

========================================
STEP 1: IDENTIFY WALK-IN FLOWS
========================================

Locate in:

- Laundry module (walk-in orders)
- Restaurant orders (non room charge)
- Bar orders (non room charge)

Find:
- "settle", "pay", or "complete order" actions

========================================
STEP 2: TRIGGER PAYMENT MODAL
========================================

On clicking "settle" for walk-in:

- OPEN modal instead of direct settlement

Modal must include:

- Customer Name (required)
- Phone Number (for USSD) OR
- Card input (for card payment)

========================================
STEP 3: VALIDATION
========================================

Require:

- name
- (phone OR card)

Reject:
- empty submissions
- missing payment method

========================================
STEP 4: PAYMENT PROCESSING
========================================

On submit:

- Send request to backend
- Call Snippe payment flow

Pass:

- amount
- customer_name
- customer_phone (if USSD)
- reference (order ID)

========================================
STEP 5: TRANSACTION RECORD
========================================

Ensure transaction is recorded with:

- type = walk-in
- module = laundry / restaurant / bar
- amount
- customer_name
- customer_phone
- status

========================================
STEP 6: SUCCESS FLOW
========================================

ONLY after successful payment:

- Mark order as:
  settled / completed

Laundry:
- Mark clothes as collected / returned

Restaurant/Bar:
- Mark order as settled

========================================
STEP 7: FAILURE HANDLING
========================================

If payment fails:

- DO NOT settle order
- Show error message
- Allow retry

========================================
STEP 8: UI FEEDBACK
========================================

- Disable submit button during processing
- Show loading state
- Show success/failure message

========================================
STEP 9: CONSISTENCY ACROSS MODULES
========================================

Ensure SAME behavior for:

- Laundry walk-in
- Restaurant walk-in
- Bar walk-in

NO separate logic styles

========================================
STEP 10: EDGE CASES
========================================

Handle:

- Multiple items/orders
- Large totals
- Missing phone (force card instead)

========================================
STEP 11: TEST FLOW
========================================

Test:

Laundry:
1. Create walk-in order
2. Click settle → modal appears
3. Enter data → pay
4. Clothes marked as collected

Restaurant:
1. Create walk-in order
2. Settle → modal
3. Pay → order closed

Bar:
Same flow as restaurant

========================================
STEP 12: FINAL VALIDATION
========================================

Confirm:

- No walk-in bypasses payment
- No order is settled without payment
- Customer data is always captured
- Transactions are recorded

========================================
OUTPUT REQUIRED
========================================

1. Where modal was added
2. Backend endpoint handling payment
3. Confirmation all 3 modules behave the same
4. Confirmation orders only close after payment

========================================
GOAL
========================================

- Walk-ins handled cleanly
- Payments enforced
- No data loss
- No inconsistent flows

MAKE WALK-IN FLOW FEEL FIRST-CLASS.
NOT A HACK.