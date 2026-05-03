Task: Checkout Pulls All Guest Payments and Removes MRK from Receipts

Objective:
Ensure checkout pulls all payments for the guest/user for finalization using the existing modal, and remove "MRK" from receipt printing.

This task must:
- Pull all pending payments for the guest/user during checkout
- Reuse the existing checkout modal for finalization
- Remove "MRK" from all receipt prints

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Checkout and Receipt Flow

Before implementation, identify:
- Checkout flow and how payments are gathered
- Existing modal used for finalization
- Receipt print templates and where "MRK" is rendered
- Where guest/user payment data is sourced

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing checkout and receipt patterns

---

### 2) Payment Aggregation at Checkout

Required behavior:
- Checkout pulls all pending payments for the guest/user
- Payments are presented in the existing modal for finalization
- Avoid duplicate or missing payments

---

### 3) Receipt Printing

Required behavior:
- Remove "MRK" from all receipt outputs
- Keep other currency labels intact

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Checkout loads all guest/user pending payments
2) Existing modal is used for finalization
3) Receipts print without "MRK"
4) No DB resets or data loss

---

Expected Outcome:

- Checkout consolidates all payments for finalization
- Receipts no longer show "MRK"

Priority:
HIGH - Checkout accuracy
