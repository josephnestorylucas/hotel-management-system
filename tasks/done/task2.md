
You are working inside a production Laravel 11 hotel management system.

The system already has:
- Unified checkout (FinanceCheckoutController)
- BookingCharge-based billing
- Snippe payment integration (active)
- Customer data (Guest model)
- Organization/business config (system-level settings)

Your task is to STANDARDIZE payment requests so that:

- Customer payments use CUSTOMER data
- System payments use ORGANIZATION data
- Refunds are supported and recorded

DO NOT redesign architecture.
DO NOT introduce new payment providers.
EXTEND the existing payment flow using Snippe.

========================================
OBJECTIVE
========================================

1. Ensure ALL payment requests use correct identity:
   - Customer → use guest name + phone
   - Organization → use system/business details

2. Support:
   - USSD payments (phone-based)
   - Card payments

3. Add refund capability:
   - Partial and full refunds
   - Proper transaction tracking

========================================
IMPLEMENTATION RULES
========================================

- DO NOT hardcode customer data
- ALWAYS pull from existing models
- DO NOT bypass checkout system
- ALL transactions must be recorded
- Keep logic consistent across modules

========================================
STEP 1: IDENTIFY PAYMENT ENTRY POINT
========================================

Locate where payments are triggered:

- Finance checkout
- Walk-in payments
- Laundry / restaurant settlements

Ensure ALL flows pass through a common handler/service.

========================================
STEP 2: CUSTOMER DATA USAGE
========================================

When payment is for a guest/customer:

Extract from:
- Guest model OR
- Booking relation

Use:
- name
- phone number

Pass these to Snippe request:

- phone → for USSD
- name → for transaction metadata

Ensure:
- No manual input overrides existing customer data unless necessary

========================================
STEP 3: ORGANIZATION DATA USAGE
========================================

For system-level payments (e.g., internal, bulk, or fallback):

Use:
- system/business name
- registered phone/email (from config or settings table)

Ensure:
- This is only used when NO customer is attached

========================================
STEP 4: USSD PAYMENT FLOW
========================================

- Use customer phone
- Trigger Snippe USSD request
- Mark transaction as:
  pending → success/failed

Ensure:
- Phone format is valid
- No empty phone allowed

========================================
STEP 5: CARD PAYMENT FLOW
========================================

- Accept card input (existing UI)
- Send through Snippe
- Attach:
  - customer name
  - booking reference

========================================
STEP 6: TRANSACTION RECORDING
========================================

Ensure ALL payments create/update transaction records with:

- amount
- reference
- provider = snippe
- status (pending, success, failed)
- customer_name
- customer_phone
- booking_id (if exists)
- metadata (json)

DO NOT skip transaction logging

========================================
STEP 7: REFUND IMPLEMENTATION
========================================

Add refund capability:

- Full refund
- Partial refund
-  and  add  the  views  and  the  controller  for  refund  
Flow:

1. Select transaction
2. Trigger refund request via Snippe
3. Update record:
   - status = refunded OR partially_refunded

Store:
- refund_amount
- refund_reference
- timestamp

========================================
STEP 8: REFUND VALIDATION
========================================

Ensure:

- Cannot refund more than paid amount
- Cannot refund already refunded transaction fully
- Partial refunds accumulate correctly

========================================
STEP 9: UI/LOGIC INTEGRATION
========================================

Where refunds are triggered:

- Finance module
- Admin/manager actions

Ensure:
- Only authorized roles can refund

========================================
STEP 10: ERROR HANDLING
========================================

- Wrap all payment + refund calls in try/catch
- Log failures
- DO NOT break checkout flow

========================================
STEP 11: CONSISTENCY CHECK
========================================

Ensure:

- Booking payments → use guest data
- Walk-in → use entered customer data
- Internal/system → use org data

========================================
STEP 12: TEST FLOW
========================================

Test:

1. Booking payment → uses guest phone
2. Walk-in payment → uses entered phone
3. USSD request works
4. Card payment works
5. Refund full → works plsu  ui  and  views   for  the  manager  and  add to the  manager  sidebar  
6. Refund partial → works
7. Invalid refund → blocked

========================================
OUTPUT REQUIRED
========================================

1. Where payment logic was standardized
2. How customer vs org data is handled
3. Refund implementation details
4. Confirmation all flows use Snippe correctly

========================================
GOAL
========================================

- Payments always tied to correct identity
- Clean transaction records
- Refunds fully supported
- No broken flows

KEEP IT CLEAN.
NO SHORTCUTS.
NO NEW ARCHITECTURE.