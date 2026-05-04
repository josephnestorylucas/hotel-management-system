Task: Align Finance Views Layout and Grant Manager Payments Access

Objective:
Fix finance views that are using incorrect layouts (e.g., /finance/refunds) to match the standard application layout, and ensure the manager can access /finance/payments.

This task must:
- Align finance views with the standard finance/application layout
- Fix out-of-scope finance layouts and sidebar usage
- Grant manager access to /finance/payments

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Finance Layouts and Access

Before implementation, identify:
- Finance views with incorrect layouts (refunds, payments, others)
- The standard layout/sidebar used for finance pages
- Current role permissions for finance routes

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing finance layout and access patterns

---

### 2) Layout Alignment

Required behavior:
- Finance views use the correct shared layout and sidebar
- Layout alignment matches other finance pages
- No styling regressions

---

### 3) Manager Access to Payments

Required behavior:
- Manager role can access /finance/payments
- Access control is consistent with finance module policies
- No access leaks to unauthorized roles

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) /finance/refunds uses the standard layout
2) Other finance views align with the same layout
3) Manager can access /finance/payments
4) No DB resets or data loss

---

Expected Outcome:

- Finance views are aligned with the correct layout
- Manager has proper access to finance payments

Priority:
HIGH - Finance UI and access
