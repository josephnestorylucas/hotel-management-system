Task: Accountant Receipts Management (View, Reprint, Link to Transactions)

Objective:
Give the Accountant a dedicated receipts management area to:
- View all receipts generated across modules
- Search/filter by date, module, receipt number, customer/supplier, status
- Reprint receipts safely (idempotent — reprint does not create a new receipt)
- Ensure every receipt links back to its source transaction/order/payment

Problem Summary:

* Receipts exist in different modules, but accountants need a single, consistent place to manage them.
* Receipts must be traceable and printable for audits and daily reconciliation.

---

### 1. Receipt Index (Accounting Center)

Create an Accountant view:
- Receipts list (all modules)
- Columns:
  - receipt_no
  - module/source
  - issued_at
  - customer/supplier name
  - amount
  - payment method
  - status: paid/unpaid/cancelled/refunded (if supported)
  - cashier/actor

Filters:
- date range
- module
- receipt number
- payment method
- status

---

### 2. Receipt Detail Page

Receipt detail must show:
- Header + totals
- Line items (if available)
- Source linkage:
  - booking/order/payment id
  - financial transaction id

Actions:
- Print / Reprint
- Download PDF (optional; only if PDF exists already in system)

Rules:
- Reprint must reuse the SAME receipt_no
- Never regenerate new receipt numbers on reprint

---

### 3. Cross-Module Linking (REQUIRED)

Ensure each receipt record (or receipt view) can navigate to:
- the source module record (order, checkout, supplier payment, etc.)
- the finance transaction record

If linkage is missing:
- add reference fields (non-breaking)
- backfill where possible

---

### 4. Access Control

- Receipts management is Accountant-only (and Manager if allowed)
- Front Desk/Storekeeper should not access the accounting receipts center unless explicitly required

---

### 5. Daily Reconciliation Support (Minimal)

Add a minimal summary section:
- Total receipts today
- Total amount today
- Split by payment method

Keep this minimal; do not build a new analytics engine.

---

### 6. Error Handling

- If receipt source record is missing, show a safe fallback (do not crash)
- Log missing linkage for cleanup

---

### 7. Testing (Manual Acceptance)

1) Receipts list loads and includes multiple modules
2) Filters work (date + module + receipt_no)
3) Receipt detail shows totals and source links
4) Reprint prints same receipt number
5) Authorization blocks non-accountant users

---

Expected Outcome:

* Accountant can manage and reprint receipts from one place
* Every receipt is traceable to a payment/transaction

Priority:
MEDIUM–HIGH – Supports auditing and reconciliation

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Do not modify existing receipt numbering rules; enforce idempotent printing
