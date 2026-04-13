Task: GRN Workflow Enforcement — Manager Accept/Reject + Storekeeper Confirm

Objective:
Enforce a proper Goods Received Note (GRN) workflow with clear separation of duties:
- Storekeeper confirms physical receipt and quantities
- Manager accepts (approves) or rejects GRN for control and audit
- Downstream effects (stock updates + accounting/AP posting) happen only after correct approvals

Problem Summary:

* GRN is a critical control point between procurement and inventory/accounting.
* Without the correct roles approving/confirming, stock and payables can become inaccurate.

---

### 1. Roles and Responsibilities (STRICT)

Storekeeper:
- Confirm GRN receipt (quantities received, condition, notes)
- Cannot accept/reject GRN

Manager:
- Accept/Approve GRN
- Reject GRN (with reason)

Store Manager:
- Can create LPO and view GRN status
- Cannot accept/reject GRN (unless explicitly allowed by business rules)

---

### 2. GRN Status Lifecycle

Define and enforce statuses:
- Draft
- Submitted
- Confirmed by Storekeeper
- Pending Manager Approval
- Approved
- Rejected

Rules:
- Only storekeeper can move to “Confirmed by Storekeeper”
- Only manager can move to “Approved” or “Rejected”
- Rejection must require a reason

---

### 3. UI Requirements

A) Storekeeper GRN Confirmation View
- List assigned/pending GRNs
- Confirm quantities received per line
- Attach notes (damage/short delivery)
- Submit confirmation

B) Manager GRN Approval Dashboard
- List GRNs pending approval
- View GRN details + storekeeper confirmation
- Actions:
  - Approve
  - Reject (reason required)

C) Store Manager View
- Read-only visibility of GRN status
- No approve/reject buttons

---

### 4. Stock and Accounting Enforcement (Non-breaking)

Enforce downstream updates:

- Stock should only be increased when GRN is fully approved (or at the exact approval point already used in the system)
- Accounts Payable should only be posted when GRN is approved (or when supplier invoice is recorded, depending on existing rules)

Do not duplicate postings:
- Ensure idempotency so re-approving does not double-add stock or double-create payables

---

### 5. Audit Trail

Record:
- confirmed_by (storekeeper)
- confirmed_at
- approved_by (manager)
- approved_at
- rejected_by (manager)
- rejected_at
- rejection_reason

---

### 6. Access Control

- Protect all endpoints:
  - /grn/confirm → storekeeper only
  - /grn/approve → manager only
  - /grn/reject → manager only

---

### 7. Testing (Manual Acceptance)

1) Storekeeper can confirm GRN; manager cannot confirm
2) Manager can approve/reject; storekeeper cannot approve/reject
3) Rejection requires reason
4) Stock increases only after approval (or per enforced rule)
5) AP/accounting posting happens only once
6) Unauthorized role attempts are blocked (UI + API)

---

Expected Outcome:

* GRN flow is controlled and auditable
* Stock and accounting updates are accurate and cannot be bypassed

Priority:
HIGH – Core procurement/stock/accounting integrity

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Keep changes minimal; do not redesign procurement module
