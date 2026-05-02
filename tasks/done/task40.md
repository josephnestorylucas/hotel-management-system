Task: Fix LPO Printing Format and Totals

Objective:
Fix LPO (Local Purchase Order) printing so it uses a dedicated print layout instead of the edit/view form. The printout must include supplier details and a clear total cost summary in a new, consistent format.

This task must:
- Use a print-only layout for LPOs (do not print the edit/view form)
- Show supplier details on the printout
- Show total cost and itemized line totals
- Preserve existing LPO creation and approval flows
- Reuse existing templating and print helpers where possible

Do not change LPO business rules or approval logic.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing LPO Print Patterns

Before implementation, identify:
- Any existing LPO view/print templates and routes
- How totals are calculated for LPO items
- Existing supplier data sources and formatting
- Any shared print styles or helpers used by other modules

Implementation rule:
- Follow current architecture and naming conventions
- Avoid duplicating existing print helpers

---

### 2) LPO Print Data Contract

Required behavior:
- Define the data needed by the new LPO print view
- Keep calculations and formatting consistent with existing LPO logic

Required fields (adjust to existing naming conventions):
- lpo_number
- issue_date
- status
- supplier_name
- supplier_contact (phone/email)
- supplier_address
- requested_by
- line_items: name, quantity, unit_price, line_total
- subtotal
- taxes (name, rate, amount) if applicable
- discounts (name, amount) if applicable
- total_cost
- notes (optional)

---

### 3) Print Layout and Styling

Required behavior:
- Create a dedicated print-friendly layout for LPOs
- Include supplier details near the header
- Include itemized table with line totals
- Include subtotal, taxes/discounts (if used), and total cost
- Ensure the layout prints cleanly on A4

Use existing fonts, spacing, and branding tokens where available.

---

### 4) Printing Flow

Required behavior:
- Add a print action that renders the print-only view
- Ensure browser print uses the new layout
- Strip UI controls and navigation from the print output

Implementation rule:
- Reuse existing print routes or helpers if present

---

### 5) Security and Access Control

Required behavior:
- Restrict LPO print access to authorized roles
- Validate LPO access server-side

---

### 6) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Printing an LPO uses the new print layout, not the edit/view form
2) Supplier details are visible on the printout
3) Line totals and total cost are displayed and correct
4) Print output is clean and consistent on A4
5) Existing LPO flows remain intact

---

Expected Outcome:

- LPO printing uses a dedicated print layout
- Supplier details and total cost are shown clearly
- No regressions in LPO creation or approval flows

Priority:
HIGH - Procurement audit and clarity
