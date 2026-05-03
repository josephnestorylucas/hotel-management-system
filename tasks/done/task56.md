Task: Add Bar POS Charges to Guest Folio

Objective:
Ensure bar POS drink charges can be added to a guest folio, similar to how laundry charges are handled.

This task must:
- Add bar POS charges to the guest folio when selected
- Keep existing POS payment flows intact
- Align bar POS folio behavior with laundry folio behavior

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Bar POS and Folio Flow

Before implementation, identify:
- Current bar POS order flow and payment logic
- How laundry charges are added to guest folios
- Existing folio models, routes, and services
- Any role or permission checks for folio access

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing folio and charge-posting patterns

---

### 2) Folio Posting

Required behavior:
- Bar POS allows charging drinks to a guest folio
- Charges post to the correct guest folio with item details
- Status reflects pending/charge-to-folio state where applicable

---

### 3) UI and Receipt Behavior

Required behavior:
- POS UI clearly shows the "charge to folio" option
- Receipt indicates charge to folio when used
- No duplicate charges on retry

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Bar POS can add a drink order to a guest folio
2) Folio shows the new charge with correct details
3) Laundry and bar folio flows are consistent
4) No DB resets or data loss

---

Expected Outcome:

- Bar POS drink charges can be posted to guest folios reliably
- Folio and receipt reflect bar charges correctly

Priority:
HIGH - POS and folio integration
