Task: Remove Order Creation from Restaurant Manager

Objective:
Remove the "Order" and "Create New Order" actions from the restaurant manager UI.

This task must:
- Remove order creation actions from restaurant manager views
- Keep existing order viewing/reporting intact if needed
- Avoid changes to unrelated modules

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Order Actions

Before implementation, identify:
- Restaurant manager views that show order creation actions
- Routes/controllers tied to order creation
- Any role-based access checks for order creation

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing permission patterns

---

### 2) UI and Access Removal

Required behavior:
- Remove create order buttons/links from restaurant manager UI
- Prevent access to create order routes for restaurant manager
- Keep other roles unaffected

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Restaurant manager cannot see or access create order actions
2) Other roles remain unaffected
3) No DB resets or data loss

---

Expected Outcome:

- Restaurant manager cannot create orders via the UI

Priority:
MEDIUM - Access control
