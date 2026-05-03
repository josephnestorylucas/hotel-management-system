Task: Fix Finance Checkout Flow, Sidebar, Drafts, and Laundry MRK Label

Objective:
Resolve issues in finance checkout and laundry completion: remove "MRK" from laundry completion, restore sidebar and consistent styling on the finance checkout view, add draft saving for unpaid sales, and ensure folios are accessible to front desk for completion.

This task must:
- Remove "MRK" from laundry completion views/receipts
- Restore sidebar and consistent layout on finance checkout view
- Show which user is completing sales only where appropriate
- Allow saving checkout as draft and complete later at checkout
- Surface unpaid items for completion in finance checkout
- Ensure folios are accessible to front desk and used to complete payments
- Ensure booking additions flow into folios for processing
- When charging to guest folio, update status and add entry to guest folio for processing
- Update related views to reflect guest folio status changes

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Flow and Views

Before implementation, identify:
- Finance checkout view for /finance/checkout/{id}
- Sidebar layout usage and any missing layout wrapper
- Where laundry completion displays currency labels
- Current sale completion and user attribution logic
- Draft or pending sale/checkout states (if any)
- Folio models, routes, and front desk access control
- How bookings attach charges to folios

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing receipt, folio, and payment patterns

---

### 2) Laundry Completion Label

Required behavior:
- Remove "MRK" from laundry completion screens and receipts
- Keep other currency labels intact

---

### 3) Finance Checkout Layout and Sidebar

Required behavior:
- Finance checkout view uses the standard layout with sidebar
- Styling matches existing application style
- User attribution is shown only when required by current UX rules

---

### 4) Draft and Completion Flow

Required behavior:
- Allow saving checkout as draft when customer has not paid
- Drafts appear in finance checkout for completion later
- Completing a draft finalizes the sale and receipt

---

### 5) Folio Access and Booking Flow

Required behavior:
- Front desk can access folios to complete payments
- Adding charges to a booking is reflected in the folio for processing
- Folio completion updates the related booking and receipts
- Charging to guest folio updates status and adds entry to the guest folio queue
- Guest folio views show the updated status and entries for processing

---

### 6) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Laundry completion no longer shows "MRK"
2) Finance checkout view has sidebar and correct styling
3) Unpaid items can be saved as drafts and later completed
4) Drafts appear in the finance checkout completion list
5) Front desk can access folios and complete payments
6) Booking charges flow into folios for processing
7) Charging to guest folio updates status and appears in guest folio list
8) Guest folio views reflect updated statuses
9) No DB resets or data loss

---

Expected Outcome:

- Laundry completion is free of the "MRK" label
- Finance checkout is styled correctly, supports drafts, and completes unpaid items
- Folio-based payment completion works for front desk and bookings

Priority:
HIGH - Finance and checkout flow
