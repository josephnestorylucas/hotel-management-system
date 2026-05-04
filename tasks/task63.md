Task: Restaurant Manager Bar Stock Access (Bar Products Only)

Objective:
Give the restaurant manager access to bar stock levels for bar products only, with proper permissions and visibility.

This task must:
- Allow restaurant manager to view bar stock and stock levels
- Limit access to bar products only (no full bar inventory control)
- Keep existing stock rules intact for other roles

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Stock Access and Roles

Before implementation, identify:
- Current bar stock views and routes
- Restaurant manager role permissions
- How products are classified as bar products

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing stock and role checks

---

### 2) Access and Scope

Required behavior:
- Restaurant manager can view bar stock levels
- Only bar products are visible
- No edit or destructive actions unless already allowed

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Restaurant manager can open bar stock levels
2) Only bar products are listed
3) Other roles and permissions remain unchanged
4) No DB resets or data loss

---

Expected Outcome:

- Restaurant manager has bar product stock visibility without broader access

Priority:
HIGH - Restaurant manager access
