
You are working inside a production Laravel 11 hotel management system.

The system already has:
- Booking and reservation flows
- Guest/customer data captured
- Notification infrastructure (SMS + email exists but not fully wired)
- Queue support (for async jobs)

Your task is to COMPLETE and STANDARDIZE the sending of confirmation notifications.

DO NOT redesign the system.
DO NOT create new architecture.
EXTEND what exists and CONNECT missing parts.

========================================
OBJECTIVE
========================================

Ensure that whenever a booking or reservation is successfully created or confirmed:

1. The customer receives:
   - SMS confirmation
   - Email confirmation

2. The notification must include:
   - Customer name
   - Booking/reservation reference
   - Dates (check-in / check-out)
   - Basic summary (room/service)

3. The notification must trigger automatically from the existing flow.

========================================
IMPLEMENTATION RULES
========================================

- DO NOT hardcode values
- USE existing models (Booking, Guest, Reservation)
- USE existing notification or SMS services already in the system
- If queue is enabled → use queued jobs
- Keep logic OUT of controllers as much as possible
- Prefer events, listeners, or service layer if already used

========================================
STEP 1: HOOK INTO BOOKING FLOW
========================================

Locate where booking is:
- created OR
- confirmed OR
- checked-in

At the FINAL successful point:

Trigger notification dispatch.

DO NOT trigger before DB commit.

========================================
STEP 2: HOOK INTO RESERVATION FLOW
========================================

Locate reservation creation/confirmation.

At success point:
- Trigger same notification logic

========================================
STEP 3: REUSE EXISTING SERVICES
========================================

Find:
- SMS service (AfricasTalking or similar)
- Email system (Laravel Mail / Notification)

USE them.

DO NOT build a new provider.

========================================
STEP 4: CREATE NOTIFICATION CLASS
========================================

If not existing:

Create a notification like:
- BookingConfirmationNotification

It should support:
- email
- sms (if system supports custom channel)

========================================
STEP 5: MESSAGE CONTENT
========================================

SMS (short format):
- "Hello {name}, your booking #{ref} is confirmed from {checkin} to {checkout}"

Email (detailed):
- Greeting
- Booking reference
- Dates
- Summary
- Simple closing

========================================
STEP 6: CUSTOMER DATA SOURCE
========================================

Pull from:
- Guest model
- OR booking-related user

Ensure:
- phone exists for SMS
- email exists for email

If missing:
- skip that channel safely (no crash)

========================================
STEP 7: QUEUE HANDLING
========================================

If queues are active:

- Dispatch notifications via queue
- Do NOT block request

If not:
- fallback to sync send

========================================
STEP 8: ERROR HANDLING
========================================

- Wrap sending in try/catch
- Log failures
- DO NOT break booking flow if notification fails

========================================
STEP 9: AVOID DUPLICATES
========================================

Ensure:
- Notification is sent ONLY ONCE per booking/reservation

Avoid:
- double triggers from multiple saves

========================================
STEP 10: TEST FLOW
========================================

Test cases:

1. Create booking → SMS + email sent
2. Create reservation → SMS + email sent
3. Missing phone → email still sends
4. Missing email → SMS still sends
5. Failure in SMS → booking still succeeds

========================================
STEP 11: LOGGING
========================================

Log:
- success send
- failure send

========================================
STEP 12: CLEAN INTEGRATION
========================================

Final flow should be:

Booking/Reservation Created
        ↓
Notification Triggered
        ↓
SMS + Email Sent

========================================
OUTPUT REQUIRED
========================================

1. Where notifications are triggered
2. Notification class created/used
3. Services reused (SMS + Email)
4. Confirmation that both channels work

========================================
GOAL
========================================

- Every customer gets confirmation instantly
- No manual sending
- No broken flows
- Clean integration into existing system

DO NOT OVERCOMPLICATE.
JUST CONNECT THE SYSTEM PROPERLY.