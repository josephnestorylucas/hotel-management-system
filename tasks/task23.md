Task: Restrict Store Manager to Only Move Stock Between Locations (No Direct Stock Editing)

Objective:
Enforce separation of duties in Inventory so that the Store Manager can ONLY move stock between locations (transfers), and cannot directly adjust quantities in a way that bypasses audit trails.

Problem Summary:

* Stock operations risk being inconsistent if store managers can manually edit quantities.
* The store manager should be limited to controlled transfers between stock locations.

---

### 1. Permissions / RBAC (STRICT)

Store Manager:
- view_stock
- create_stock_transfer
- submit_stock_transfer
- view_stock_transfers

Store Manager must NOT have:
- direct_stock_adjustment
- delete_stock_transactions
- edit_stock_quantities

Manager/Admin:
- approve transfers (optional)
- adjust stock (if already supported)

---

### 2. Stock Transfer Flow (Required)

Create/verify a controlled transfer process:

1) Create transfer draft
- from_location
- to_location
- items + quantities
- notes

2) Submit transfer
- lock draft edits

3) Confirm/complete transfer
- deduct from source
- add to destination
- record movement lines

Rules:
- Must be atomic (DB transaction)
- Must validate available stock in source
- Must create an audit record (actor + timestamp)

---

### 3. UI Changes

Store Manager UI must:
- Hide any “edit quantity” buttons
- Provide:
  - Stock overview (read-only)
  - Transfers list
  - Create transfer

---

### 4. Audit + Reporting

Transfers must record:
- created_by
- confirmed_by
- confirmed_at
- reference number
- movement lines

---

### 5. Testing (Manual Acceptance)

1) Store manager cannot directly edit stock quantities (UI + API)
2) Store manager can create and complete a transfer
3) Transfer fails if stock insufficient
4) Stock updates correctly in both locations after completion

---

Expected Outcome:

* Stock movements are controlled, traceable, and audit-ready
* Store manager role cannot bypass inventory integrity

Priority:
HIGH – Prevents stock manipulation and data inconsistency

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Do not break existing procurement/GRN stock updates
