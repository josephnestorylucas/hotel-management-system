# HMS Major Rewrite: Booking vs Reservation Separation

> **Severity:** CRITICAL — Cross-module refactor touching DB schema, business logic, UI, and APIs  
> **Author:** Dady Utenga  
> **Project:** BIG LITE CODE — Hotel Management System  
> **Date:** 2026-05-27

---

## Core Concept Definitions (Source of Truth)

Before touching any code, burn these definitions into your brain and apply them **everywhere** consistently:

| Term | Definition | Key Behavior |
|---|---|---|
| **Reservation** | A future promise to hold a room for a guest | Guest is NOT yet in the hotel. Status: Pending → Confirmed → Cancelled / No-Show |
| **Booking** | An active stay record — check-in to check-out | Guest IS in the hotel or has completed their stay. Status: Checked-In → Checked-Out |

A **Reservation converts into a Booking** at check-in time. These are two separate lifecycle entities, not one confused table.

---

## Phase 0 — Audit Before Touching Anything

### STEP 1 — Map the contamination

Before writing a single line, do a full audit across the entire codebase:

- Search every model, controller/view, service, route, template, and migration for any use of the words `booking`, `reservation`, `reserve`, `book` — in variable names, method names, comments, DB column names, and URL routes.
- For each hit, classify it: Is it a Reservation concept? A Booking concept? Or genuinely mixed/ambiguous?
- Produce a written checklist of every file that needs to change. Do not proceed to Phase 1 without this list.

### STEP 2 — Identify all affected modules

Confirm which of the following modules reference the contaminated model(s):

- [ ] Front Desk
- [ ] Room Management
- [ ] Billing / Invoicing
- [ ] Guest Management
- [ ] Housekeeping
- [ ] Reporting / Analytics
- [ ] Notifications (Email/SMS)
- [ ] Calendar / Availability View
- [ ] API endpoints (internal or external)
- [ ] Any admin dashboard

Every module on this list is in scope. No exceptions.

---

## Phase 1 — Database Schema (Do This First, Everything Else Depends on It)

### STEP 3 — Create the Reservation table (if merged or missing)

The `reservations` table must contain:

- Guest reference (FK)
- Room reference (FK)
- Reserved check-in date
- Reserved check-out date
- Number of nights
- Number of guests
- Special requests / notes
- Status: `pending` | `confirmed` | `cancelled` | `no_show` | `converted`
- Source of reservation: walk-in, online, phone, agent
- Rate plan / pricing at time of reservation
- Deposit amount (if any)
- Created by (staff FK)
- Timestamps

### STEP 4 — Create / clean the Booking table

The `bookings` table must contain:

- Reservation reference (FK — nullable only if walk-in with no prior reservation)
- Guest reference (FK)
- Room reference (FK)
- Actual check-in datetime (staff-recorded, not estimated)
- Actual check-out datetime
- Status: `checked_in` | `checked_out` | `early_departure`
- Room rate applied
- Extra charges reference
- Checked-in by (staff FK)
- Checked-out by (staff FK)
- Timestamps

### STEP 5 — Migration strategy

- Write migrations that create the clean tables first.
- If data exists: write a data migration script that classifies existing records into the correct table based on their actual status/dates.
- Never delete old data without a rollback plan.
- Keep old columns with a `_deprecated` suffix during transition until all modules are confirmed clean.

---

## Phase 2 — Business Logic Layer

### STEP 6 — Reservation service / manager

This layer handles:

- Create a new reservation (validate room availability for date range, no double-booking)
- Confirm a reservation (change status, trigger notification)
- Cancel a reservation (apply cancellation policy, refund logic if deposit exists)
- Mark as No-Show (if guest doesn't arrive by cutoff time)
- Convert reservation → booking (check-in flow, see STEP 7)
- List all reservations by date range, status, or room

### STEP 7 — Check-In flow (Reservation → Booking conversion)

This is the critical bridge. The check-in process must:

1. Look up the existing confirmed reservation
2. Verify room is ready (housekeeping status check)
3. Verify guest identity
4. Record actual check-in datetime
5. Create a new `Booking` record linked to the reservation
6. Update reservation status to `converted`
7. Update room status to `occupied`
8. Trigger Front Desk notification / room key assignment

**Walk-in guests (no prior reservation):** Create a minimal reservation record first (status: `confirmed` immediately), then immediately proceed to check-in. Never skip the reservation record — it ensures audit trail and billing linkage.

### STEP 8 — Check-Out flow

The check-out process must:

1. Look up active booking for the room / guest
2. Consolidate all charges (room rate × nights, extras, minibar, laundry, etc.)
3. Generate final invoice linked to the booking
4. Record actual check-out datetime
5. Update booking status to `checked_out`
6. Update room status to `dirty` / trigger housekeeping
7. Release room from availability

---

## Phase 3 — Module-by-Module Fixes

Work through these one module at a time. Do not jump between modules mid-fix.

### STEP 9 — Front Desk

- Arrivals view: shows only **Reservations** with status `confirmed`, arriving today or overdue
- In-house view: shows only **Bookings** with status `checked_in`
- Departures view: shows **Bookings** expected to check out today
- The "Check-In" button must be on the Reservation, not a booking
- The "Check-Out" button must be on the Booking, not the reservation

### STEP 10 — Room Management

- Room availability calendar must query from `reservations` (for future blocks) and `bookings` (for current occupancy)
- Room status must reflect: `available` | `reserved` | `occupied` | `dirty` | `maintenance`
- A room that has a confirmed reservation must show as `reserved` (not available) for that date range
- A room with an active booking must show as `occupied`

### STEP 11 — Billing / Invoicing

- Invoices must be linked to **Bookings**, not reservations
- Deposits collected at reservation time must carry forward to the final booking invoice
- Proforma invoices can be linked to reservations (for advance payment)
- Final invoice is generated at check-out

### STEP 12 — Guest Management

- Guest profile must show two separate sections:
  - **Upcoming Reservations** (future, not yet checked in)
  - **Stay History** (past and current bookings)
- Guest search must search across both tables but present results clearly separated

### STEP 13 — Housekeeping

- Housekeeping tasks are triggered by Booking events (check-in dirty → clean, check-out dirty → clean)
- Reservation events only trigger housekeeping if room requires pre-arrival setup (VIP, special request)
- Housekeeping dashboard must not show reservation IDs as "booking" references

### STEP 14 — Reporting / Analytics

- Reservation reports: occupancy forecast, cancellation rates, no-show rates, source of business
- Booking reports: actual occupancy, ADR (Average Daily Rate), RevPAR, length of stay
- These are separate reports. Do not merge them.
- Any report that references "booking_id" must be reviewed — if it was previously pulling reservation data, fix the query

### STEP 15 — Notifications

- Reservation confirmed → send confirmation email to guest (with reservation ID)
- Check-in completed → send welcome message (with booking ID / room number)
- Check-out completed → send thank-you / invoice email
- Ensure notification templates reference the correct entity and use correct IDs

### STEP 16 — API Endpoints (if any)

- `/api/reservations/` — CRUD for reservations only
- `/api/bookings/` — CRUD for bookings only
- `/api/checkin/` — converts reservation to booking
- `/api/checkout/` — closes a booking
- No endpoint should accept both reservation and booking data in a mixed payload
- Review all existing endpoints and rename/split anything that is ambiguous

---

## Phase 4 — UI / Templates

### STEP 17 — Language cleanup across all UI

- Every label, button, heading, column header, and tooltip must use the correct term
- "Book a Room" on the front end means starting a **Reservation** (the guest is reserving for the future)
- "Check In" means converting that reservation to a **Booking**
- Never show "Booking #" for a reservation or "Reservation #" for a booking
- Update all breadcrumbs, page titles, and flash messages

### STEP 18 — Forms

- Reservation form: collects arrival/departure dates, guest info, room preference, special requests, deposit
- Check-In form (minimal): confirms guest identity, assigns exact room, records actual arrival time
- Check-Out form: shows charges summary, takes payment, confirms departure
- These must be three distinct forms, not one overloaded form

---

## Phase 5 — Testing (Usage Testing Feature)

### STEP 19 — Seed data for testing

Create a test data seeder that generates:

- 10 confirmed reservations (various date ranges, some arriving today)
- 5 active bookings (checked-in guests currently in house)
- 3 completed bookings (checked-out guests with invoices)
- 2 cancelled reservations
- 1 no-show reservation
- 2 walk-in bookings (no prior reservation)

### STEP 20 — Usage test scenarios

Write and execute the following test flows manually or as automated tests:

**Reservation flows:**
- [ ] Create a reservation for a future date → confirm it appears in arrivals list on that date
- [ ] Cancel a reservation → confirm room becomes available again
- [ ] Mark a reservation as no-show → confirm room is released and guest is flagged

**Check-In flows:**
- [ ] Check in a guest with an existing confirmed reservation → confirm booking is created, reservation status = `converted`, room status = `occupied`
- [ ] Walk-in guest check-in → confirm reservation is auto-created, then booking is created
- [ ] Attempt to check in when room is not clean → confirm it is blocked

**Check-Out flows:**
- [ ] Check out a guest → confirm booking status = `checked_out`, room status = `dirty`, invoice generated
- [ ] Early departure → confirm correct billing for nights stayed only

**Cross-module:**
- [ ] Confirm housekeeping sees the correct room status after check-in and check-out
- [ ] Confirm billing module can pull the correct invoice by booking ID
- [ ] Confirm guest profile shows correct reservation and booking history separately
- [ ] Confirm reports show accurate occupancy for a given date

### STEP 21 — Regression check

After all fixes:

- Go through every module listed in STEP 2
- Confirm no module is still referencing the wrong entity
- Confirm no old mixed-use variable names remain (`booking_id` where a reservation is meant, etc.)
- Run a final search for any remaining ambiguous terminology in the codebase

---

## Rules for This Rewrite

1. **Never mix Reservation and Booking logic in the same function.** If you catch yourself doing it, split the function.
2. **Every DB query must be explicit about which table it's hitting.** No ambiguous model reuse.
3. **Rename everything during the refactor** — do not leave old names "for now." Deferred renames become permanent confusion.
4. **One phase at a time.** Do not start Phase 2 without finishing and confirming Phase 1 is stable.
5. **Commit after each module fix** with a clear message: `fix(front-desk): use reservations for arrivals, bookings for in-house`
6. **No feature additions during this rewrite.** The usage testing feature (Phase 5) is the only exception.

---

## Deliverable Checklist

- [ ] Phase 0 audit completed and documented
- [ ] Clean `reservations` table with correct schema
- [ ] Clean `bookings` table with correct schema
- [ ] Data migration script written and tested
- [ ] Reservation service implemented
- [ ] Check-In flow (conversion) implemented
- [ ] Check-Out flow implemented
- [ ] Front Desk module fixed
- [ ] Room Management module fixed
- [ ] Billing module fixed
- [ ] Guest Management module fixed
- [ ] Housekeeping module fixed
- [ ] Reporting module fixed
- [ ] Notifications fixed
- [ ] API endpoints fixed and renamed
- [ ] All UI labels corrected
- [ ] All forms separated
- [ ] Seed data created
- [ ] All 12 test scenarios passing
- [ ] Regression check completed
- [ ] Codebase free of ambiguous booking/reservation terminology
