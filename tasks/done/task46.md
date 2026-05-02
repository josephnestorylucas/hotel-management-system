Task: Enforce Reception-Only Drink Orders and POS Sale Finalization

Objective:
Clarify and enforce the drink order flow (reception-only requests) and ensure POS sales are finalized with a payment method, receipt  because  now  when  finalising  we  are  ging  to the  drink orders   completion, and guest-linked closure data.

This task must:
- Treat drink orders as reception-originated requests only
- Require POS sales to be finalized with cash, mobile, or card
- Finalize receipts at payment completion
- Support guest-bound sales that are completed by the guest later
- Capture customer name and phone number at conclusion
- Show guest completion in the relevant modules

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Order and POS Flows

Before implementation, identify:
- Where drink orders are created today and who can create them
- Current POS sale flow and payment handling
- Receipt generation and completion triggers
- Guest binding logic and where guest completion is recorded
- Which modules should display the final guest conclusion

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing receipt, payment, and order status patterns

---

### 2) Drink Orders: Reception Only

Required behavior:
- Only reception roles can create drink orders
- Bar or other roles cannot create drink orders
- Orders created elsewhere must be blocked or redirected
- Audit the UI and routes to enforce role-based access

---

### 3) POS Sale Finalization and Payments

Required behavior:
- When a product is selected for sale, user must finalize with a payment method
- Allowed methods: cash, mobile, card (use existing enums or configs)
- Sale is not completed until a payment method is recorded
- Receipt is finalized at payment completion

---

### 4) Guest-Bound Sales and Completion

Required behavior:
- If a sale is bound to a guest, allow it to be completed by the guest later
- Record the guest completion status and timestamp
- Display guest completion in the relevant modules (identify the target modules during discovery)

---

### 5) Conclusion Details

Required behavior:
- At conclusion, capture customer name and phone number
- Validate phone number format consistently with existing rules
- Persist data with the finalized receipt or sale record

---

### 6) Migration Plan (No Data Loss)

Required behavior:
- Add any needed columns with safe defaults
- Do not drop or clear existing data
- Provide rollback path

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Reception can create drink orders; other roles cannot
2) POS sale cannot complete without payment method
3) Receipt finalizes only after payment completion
4) Guest-bound sale can be completed later by the guest
5) Customer name and phone are captured at conclusion
6) Guest completion is visible in the correct modules
7) No DB resets or data loss

---

Expected Outcome:

- Drink orders are strictly reception-only
- POS sales always finalize with a payment method
- Receipts are complete and guest conclusions are visible where needed

Priority:
HIGH - Core sales and service flow


Hmserver@2025#