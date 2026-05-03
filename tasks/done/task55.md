Task: Fix Front Desk Current Guests View

Objective:
Restore the Current Guests view and functionality for front desk so it no longer redirects to bookings and provides the correct current guests list.

This task must:
- Provide a dedicated Current Guests view for front desk
- Stop redirecting Current Guests to bookings
- Ensure current guest data loads correctly

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Guests Flow

Before implementation, identify:
- Current Guests route and any redirect logic
- Front desk sidebar link target for Current Guests
- Existing guests or bookings queries used for current stays
- Any missing views or controllers for Current Guests

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing guest/booking query logic where possible

---

### 2) View and Routing

Required behavior:
- Create a Current Guests view for front desk
- Route should load the Current Guests list directly
- Use existing layout and styling patterns

---

### 3) Data Loading

Required behavior:
- List currently checked-in guests with room and stay details
- Respect front desk access rules
- Provide empty state when no current guests exist

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Front desk Current Guests link opens the correct view
2) Current guests list loads without redirecting to bookings
3) Data is accurate for checked-in guests
4) No DB resets or data loss

---

Expected Outcome:

- Front desk can access a working Current Guests view
- Current guests list is accurate and accessible

Priority:
HIGH - Front desk operations
