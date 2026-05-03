Task: Remove MRK from Laundry Receipts and Print Guest Folio Receipts

Objective:
Remove the "MRK" label from laundry receipts and enable printing guest folio receipts at finalization using the same models/patterns as laundry payments.

This task must:
- Remove "MRK" from all laundry receipt outputs
- Enable printing guest folio receipts when finalizing payments
- Reuse the same models/patterns used for laundry payment receipts

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Receipt Templates and Models

Before implementation, identify:
- Laundry receipt templates and where "MRK" is rendered
- Guest folio receipt templates (if any)
- Models/services used for laundry payment receipts
- Where receipt printing is triggered at finalization

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing receipt rendering and printing logic

---

### 2) Laundry Receipt Label

Required behavior:
- Remove "MRK" from laundry receipt views and prints
- Keep other currency labels intact

---

### 3) Guest Folio Receipt Printing

Required behavior:
- Guest folio receipts can be printed when finalizing payments
- Receipt uses the same model/data pattern as laundry payments
- Receipt shows correct guest, items, totals, and payment details

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Laundry receipts no longer show "MRK"
2) Guest folio receipts print at finalization
3) Guest folio receipts use the same model/data pattern as laundry payments
4) No DB resets or data loss

---

Expected Outcome:

- Laundry receipts are free of the "MRK" label
- Guest folio receipts print correctly using consistent models

Priority:
HIGH - Receipt accuracy
