
You are working inside a production Laravel 11 system with an existing payment infrastructure.

The system currently has:
- ClickPesa integration (legacy / to be removed)
- Snippe integration (active and to be used going forward)
- A central payment flow already used across modules (checkout, walk-ins, etc.)

Your task is to COMPLETELY REMOVE ClickPesa and ensure ALL payment flows use Snippe ONLY.

DO NOT redesign payment architecture.
DO NOT break existing payment flows.
JUST CLEAN AND STANDARDIZE.

========================================
OBJECTIVE
========================================

1. Remove ALL ClickPesa-related logic
2. Ensure Snippe becomes the ONLY payment provider
3. Ensure all existing payment flows still work after removal

========================================
IMPLEMENTATION RULES
========================================

- DO NOT touch unrelated modules
- DO NOT remove transaction records/history
- DO NOT break checkout flow
- DO NOT introduce new providers
- ONLY redirect to Snippe

========================================
STEP 1: REMOVE CLICKPESA USAGE
========================================

Find ALL references to ClickPesa in:

- Controllers
- Services
- Jobs
- Helpers
- Config files
- Routes
- Webhooks

Remove:
- API calls
- Service classes
- Helper methods

========================================
STEP 2: CLEAN CONTROLLERS
========================================

Where ClickPesa is used:

- Replace logic with Snippe equivalent
OR
- Route through existing payment handler that already supports Snippe

Ensure:
- No controller directly depends on ClickPesa anymore

========================================
STEP 3: UPDATE PAYMENT FLOW
========================================

Ensure ALL payment entry points now use Snippe:

- Booking checkout
- Laundry payments
- Walk-in payments
- Reservation payments

There should be:
👉 ONE consistent payment path (Snippe)

========================================
STEP 4: REMOVE CONFIG
========================================

Remove from:

- config/services.php
- .env references (CLICKPESA_*)
- any config files related to ClickPesa

DO NOT touch Snippe config

========================================
STEP 5: WEBHOOK CLEANUP
========================================

Find webhook handlers:

- Remove ClickPesa webhook logic
- Ensure Snippe webhook remains working

========================================
STEP 6: DATABASE SAFETY
========================================

DO NOT delete:
- Old transactions
- Historical data

If provider field exists:
- Leave old records as "clickpesa"
- New ones must be "snippe"

========================================
STEP 7: UI CLEANUP
========================================

Remove from UI:

- ClickPesa options in dropdowns
- Any labels mentioning ClickPesa

Ensure:
- Only Snippe (or generic “Payment”) is shown

========================================
STEP 8: FALLBACK CHECK
========================================

Ensure there is NO place where:

- system tries to call ClickPesa
- system defaults to ClickPesa

Everything must fallback to Snippe

========================================
STEP 9: TEST FLOW
========================================

Test:

1. Booking checkout → uses Snippe
2. Laundry payment → uses Snippe
3. Walk-in payment → uses Snippe
4. No errors referencing ClickPesa
5. Webhooks still process correctly

========================================
STEP 10: FINAL VALIDATION
========================================

Confirm:

- Zero ClickPesa references remain in execution flow
- Snippe handles all payments
- No broken routes, imports, or classes

========================================
OUTPUT REQUIRED
========================================

1. List of removed ClickPesa files/components
2. Locations where logic was replaced
3. Confirmation all flows now use Snippe
4. Confirmation no runtime errors exist

========================================
GOAL
========================================

- One clean payment provider (Snippe)
- No legacy clutter
- Stable production system

REMOVE CLEANLY.
DO NOT BREAK SH*T.