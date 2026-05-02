Task: Fix GRN Printing Format in Store

Objective:
Fix GRN (Goods Received Note) printing in the Store section so it uses a dedicated print layout instead of the edit/view form. The printout must include supplier details, received items, and a clear totals summary in a new, consistent format.

This task must:
- Use a print-only layout for GRNs (do not print the edit/view form)
- Show supplier details on the printout
- Show received quantities and line totals
- Show totals summary (subtotal, taxes/discounts if applicable, total)
- Preserve existing GRN creation, approval, and inventory update flows
- Reuse existing templating and print helpers where possible

Do not change GRN business rules, inventory logic, or approval workflow.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing GRN Print Patterns

Before implementation, identify:
- Any existing GRN view/print templates and routes
- How GRN item totals are calculated
- Existing supplier data sources and formatting
- Any shared print styles or helpers used by other modules

Implementation rule:
- Follow current architecture and naming conventions
- Avoid duplicating existing print helpers

---

### 2) GRN Print Data Contract

Required behavior:
- Define the data needed by the new GRN print view
- Keep calculations and formatting consistent with existing GRN logic

Required fields (adjust to existing naming conventions):
- grn_number
- issue_date
- status
- supplier_name
- supplier_contact (phone/email)
- supplier_address
- received_by
- reference_po_or_lpo (optional)
- line_items: name, received_quantity, unit_price, line_total
- subtotal
- taxes (name, rate, amount) if applicable
- discounts (name, amount) if applicable
- total_cost
- notes (optional)

---

### 3) Print Layout and Styling

Required behavior:
- Create a dedicated print-friendly layout for GRNs
- Include supplier details near the header
- Include itemized table with received quantities and line totals
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
- Restrict GRN print access to authorized roles
- Validate GRN access server-side

---

### 6) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Printing a GRN uses the new print layout, not the edit/view form
2) Supplier details are visible on the printout
3) Received quantities, line totals, and total cost are displayed and correct
4) Print output is clean and consistent on A4
5) Existing GRN flows remain intact

---

Expected Outcome:

- GRN printing uses a dedicated print layout
- Supplier details and totals are shown clearly
- No regressions in GRN creation or inventory flows

Priority:
HIGH - Store receiving audit and clarity
