Task: Build Restaurant POS Based on Bar POS

Objective:
Create a restaurant POS experience modeled on the bar POS, with accounting integration, manager visibility, walk-in sales support, and guest folio charging.

This task must:
- Use bar POS as reference for restaurant POS
- Record restaurant sales in accounting
- Make sales visible to restaurant manager
- Support local walk-in sales
- Support charging sales to guest folio as completed sales

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Bar POS and Restaurant POS

Before implementation, identify:
- Bar POS flow, layout, and data model
- Existing restaurant POS screens and routes (if any)
- Accounting posting flow for POS sales
- Manager reporting views for sales visibility
- Guest folio charging flow

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse bar POS patterns where possible

---

### 2) POS Flow and UI

Required behavior:
- Restaurant POS matches bar POS interaction flow
- Uses the same layout style and component patterns
- Works for walk-in and guest folio sales

---

### 3) Accounting and Manager Visibility

Required behavior:
- Restaurant POS sales post to accounting
- Restaurant manager can view all restaurant sales
- Avoid double posting or missing entries

---

### 4) Walk-ins and Folio Sales

Required behavior:
- Walk-in sales can be completed with standard payment methods
- Guest folio charges are recorded as completed sales
- Receipts and totals reflect correct payment type

---

### 5) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Restaurant POS works like bar POS
2) Walk-in sales complete successfully
3) Guest folio sales complete and appear in folio
4) Sales appear in accounting
5) Restaurant manager can view sales
6) No DB resets or data loss

---

Expected Outcome:

- Restaurant POS aligns with bar POS and supports accounting and manager visibility

Priority:
HIGH - Restaurant POS
