Task: Unified Receipt Design and Printing Across Modules (Except Booking and Bar)

Objective:
Create a single, well-designed receipt system and printing flow that is used across all modules except Booking and Bar. The system must be consistent, printable, and reusable, while respecting each module's data needs and existing architecture.

This task must:
- Provide a unified receipt layout and printing flow for all modules (excluding Booking and Bar)
- Reuse existing rendering, templating, and storage patterns
- Support consistent branding, totals, taxes, and line item formatting
- Work for both on-screen view and print/PDF output
- Keep module-specific data where required, but standardize structure and styling

DO NOT implement receipts for Booking or Bar.
DO NOT break current module flows or payment logic.
EXTEND existing patterns for views, services, and printing where present.

---

### 1) Discovery First: Find Existing Receipt/Invoice Patterns

Before implementation, identify:
- Any existing receipt or invoice templates and printing flows
- Module-specific receipt views or PDF generation logic
- How taxes, discounts, tips, and service charges are calculated
- Current branding assets (logo, address, contact info) and where they live
- Storage or audit requirements for receipts

Implementation rule:
- Follow existing architecture and naming conventions
- Avoid creating parallel or duplicate receipt systems

---

### 2) Scope and Module Coverage

Required behavior:
- Apply to all modules except Booking and Bar
- Ensure each module can map its data into the unified receipt format

Notes:
- If a module lacks receipts today, add the new unified receipt flow
- If a module has its own receipts today, refactor to use the shared layout

---

### 3) Unified Receipt Data Contract

Required behavior:
- Define a shared receipt data structure (DTO or array shape) that all modules map to
- Include common fields and allow module-specific extra sections

Required fields (adjust to existing data conventions):
- receipt_number
- issue_date
- customer_name
- customer_contact (phone/email)
- cashier_or_user
- currency
- line_items: name, quantity, unit_price, subtotal
- subtotal
- taxes (name, rate, amount)
- discounts (name, amount)
- service_charges (name, amount)
- total
- payment_method(s)
- amount_paid
- balance_or_change

Optional fields (module-specific):
- reference_id
- notes
- room_or_facility_reference
- delivery_or_pickup_info

---

### 4) Receipt Layout and Styling

Required behavior:
- Create one primary receipt layout (HTML/Blade) used by all modules
- Ensure print styling is clean and consistent
- Use existing fonts, colors, and spacing tokens

Layout requirements:
- Header with hotel branding, address, and contact info
- Clear receipt number and issue date
- Customer and transaction metadata section
- Line items table
- Totals summary with tax/discount breakdown
- Payment details and balance/change
- Footer with thank you message and policy note (if existing)

---

### 5) Printing and Export

Required behavior:
- Provide a print-ready view (browser print) and a PDF option if already supported
- Ensure print CSS removes unnecessary UI elements
- Handle long receipts with page breaks

Implementation rule:
- Reuse existing print/PDF libraries or helpers
- Do not add new dependencies unless explicitly approved

---

### 6) Receipt Numbering and Audit Trail

Required behavior:
- Ensure receipts have a unique, human-friendly number
- Link receipts to the source transaction and module
- Preserve any current audit logging or history tracking

If a numbering system already exists:
- Reuse it and keep numbering consistent

---

### 7) Localization and Currency

Required behavior:
- Respect existing localization (EN/SW) patterns
- Format currency and dates consistently
- Avoid hard-coded currency symbols or date formats

---

### 8) Security and Access Control

Required behavior:
- Restrict receipt access to authorized roles per module
- Ensure users cannot access receipts from other modules or tenants
- Validate all receipt requests server-side

---

### 9) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Each module (except Booking and Bar) can generate a receipt
2) The unified layout is used everywhere
3) Print output is clean, readable, and consistent
4) Totals, taxes, discounts, and payments match existing calculations
5) Receipt numbering is unique and consistent
6) Localization works for labels, currency, and dates
7) Access control prevents unauthorized receipt access
8) Existing module behavior remains intact

---

Expected Outcome:

- One unified receipt design and print flow used across all modules except Booking and Bar
- Consistent branding, totals, and formatting
- Reliable printing and optional PDF output
- No regressions in module transactions or accounting logic

Priority:
HIGH - Cross-module consistency and audit quality

Notes:
- Keep changes minimal and aligned with existing architecture
- Reuse existing services, views, and helpers where possible


Continue  opencode -s ses_221383aecffeh20Q3pQ2zd8F5b