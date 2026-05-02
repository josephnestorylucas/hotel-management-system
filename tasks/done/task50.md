Task: Fix Front Desk Real Data Fetching

Objective:
Ensure the front desk dashboard and related panels fetch and display real data instead of empty or missing information.

This task must:
- Fetch real data for front desk views and widgets
- Replace placeholders or empty states that never resolve
- Ensure data loads for the correct front desk role scope

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Data Sources and Views

Before implementation, identify:
- Front desk pages, widgets, and sidebar panels showing no data
- Controllers, API routes, or AJAX calls that power those views
- Any role-based filters or scopes affecting front desk data
- Any failing requests or errors in logs

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing query scopes and services where possible

---

### 2) Data Fetching

Required behavior:
- Real data loads for front desk views (bookings, guests, check-ins, check-outs)
- Empty states are shown only when data truly does not exist
- Loading indicators stop after success or failure

---

### 3) Access and Scoping

Required behavior:
- Front desk role can access the required data endpoints
- Data is scoped correctly (e.g., current date, active stays, assigned rooms)
- No sensitive data leaks to unauthorized roles

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Front desk dashboard loads with real data
2) Each front desk widget shows data or a valid empty state
3) No infinite loading states
4) No DB resets or data loss

---

Expected Outcome:

- Front desk screens show real, accurate data
- Loading and empty states behave correctly

Priority:
HIGH - Front desk operations
