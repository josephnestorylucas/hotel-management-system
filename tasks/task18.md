Task: Implement Supplier Payables (Accounts Payable) Application + Supplier Payment Finalization

Objective:
Enable the Accountant to fully manage supplier payables (Accounts Payable) end-to-end:
- Create/track supplier invoices or payable balances
- Apply payments against supplier payables (full/partial)
- Finalize supplier payments with strict audit trail
- Ensure procurement/GRN postings correctly feed Accounts Payable

Problem Summary:

* Supplier purchases create liabilities (Accounts Payable), but the accountant lacks a complete, standardized workflow to:
  * View supplier balances
  * Apply payments against specific supplier payables
  * Track partial payments and remaining balances
  * Finalize a supplier payment and lock it for audit

---

### 1. Accounts Payable Core Data Model (REQUIRED)

Ensure the system has clear, consistent records for:

A) Supplier Payable
- supplier_id
- reference (invoice number, GRN number, LPO number, or system-generated)
- payable_date
- currency
- amount_total
- amount_paid
- balance
- status: unpaid / partial / paid / cancelled
- source_module: procurement
- source_reference_id (links to GRN/LPO/invoice)
- created_by

B) Supplier Payment
- supplier_id
- payment_date
- currency
- amount
- method (cash, bank, mobile, card) — use existing payment methods where available
- reference (bank slip / transaction reference)
- status: draft / pending_approval (optional) / posted / cancelled
- posted_by
- posted_at
- notes

C) Payment Allocation (Apply Payment)
- supplier_payment_id
- supplier_payable_id
- allocated_amount
- created_by

Rules:
- Balance must never go negative
- A payable cannot be marked paid unless balance == 0
- Allocations must never exceed payable balance
- Deleting posted records must be disallowed; use reversals/cancellations instead

---

### 2. Accountant Views (CREATE/VERIFY)

Add accountant screens under Accounting Center:

1) Accounts Payable Dashboard
- Total outstanding AP
- AP aging buckets (0–30, 31–60, 61–90, 90+)
- Recent supplier payments

2) Supplier Payables List
- Filter by supplier, status, date range
- Show totals, paid, balance

3) Supplier Payable Detail
- Payable header + source reference
- Allocation history (all payments applied)

4) Supplier Payment Create (Draft)
- Choose supplier
- Enter amount, method, reference
- Save as draft

5) Apply Payment (Allocation Screen)
- Select a supplier payment
- Show all unpaid/partial payables for that supplier
- Allow allocation to one or multiple payables
- Support partial allocation

---

### 3. Apply Supplier Payables (CORE REQUIREMENT)

Implement the apply logic:

- Accountant selects a Supplier Payment
- Accountant allocates payment amount to one/multiple payables
- System updates:
  - payable.amount_paid
  - payable.balance
  - payable.status
  - supplier overall balance (computed or materialized)

Enforce:
- All updates must run inside a DB transaction
- Allocation must be idempotent (avoid duplicate apply)

---

### 4. Finalize Supplier Payments (POSTING)

Add a “Finalize/Post Payment” action:

- Only Accountant (or Finance Manager, if role exists) can finalize
- Once finalized/posted:
  - Payment becomes immutable (no edits)
  - Only cancellation/reversal is allowed with a reason

Posting requirements:
- Create a financial transaction record (existing pattern)
- Create accounting journal entries (if system supports it):
  - Debit Accounts Payable
  - Credit Cash/Bank

---

### 5. Integration With Procurement / GRN (IMPORTANT)

Ensure AP is created/updated from procurement flow:
- When GRN is confirmed (goods received) and valued:
  - Create Supplier Payable (or increase supplier balance)
- Link payable to:
  - supplier
  - GRN/LPO reference

Do not duplicate liabilities:
- If payable already exists for a GRN/invoice reference, reuse it

---

### 6. Access Control (RBAC)

Accountant permissions:
- view_ap
- create_supplier_payment
- apply_supplier_payment
- post_supplier_payment
- view_supplier_ledger

Non-accountants:
- Must not finalize/pay suppliers

---

### 7. Error Handling + Audit Trail

- Log all posting/applying failures
- Track actor for:
  - created_by
  - applied_by
  - posted_by
- Store notes/reasons for cancellations

---

### 8. Testing (Manual Acceptance)

1) Create payable from procurement/GRN → appears in AP list
2) Create supplier payment draft → saved
3) Apply payment partially → payable status becomes partial, balance correct
4) Apply remaining payment → payable status becomes paid
5) Finalize/post payment → becomes immutable and journal/financial record created
6) Attempt to over-allocate → blocked
7) Attempt to edit posted payment → blocked

---

Expected Outcome:

* Accountant can manage supplier liabilities and payments end-to-end
* Supplier balances are accurate and auditable
* Procurement/GRN properly feeds Accounts Payable

Priority:
HIGH – Financial control and audit integrity

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Avoid hardcoding accounts; use configured chart-of-accounts mapping if present
- Do not break existing procurement-stock-accounting integration
