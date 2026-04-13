Task: Fix Integration Between Procurement, Stock, and Accounting Modules

Objective:
Resolve the disconnection between Procurement, Stock (Inventory), and Accounting modules. These modules must work as a unified system with consistent data flow, ensuring all transactions are reflected across modules in real time.

Problem Summary:

* Procurement and Stock modules are currently operating independently
* Stock movements are not fully linked to procurement actions
* Financial/accounting module is not properly reflecting procurement or stock transactions
* This leads to inconsistencies in inventory levels, costs, and financial records

Requirements:

1. System Flow Analysis (MANDATORY FIRST STEP)

* Trace the full lifecycle:
  Procurement → Goods Receipt → Stock Update → Financial Entry
* Identify all broken or missing links between:

  * Purchase Orders
  * Goods Received Notes (GRN)
  * Inventory updates
  * Accounting journal entries

2. Procurement → Stock Integration

* When a purchase is approved and goods are received:

  * Automatically update inventory quantities
  * Link GRN to stock records
* Ensure:

  * No manual duplication of stock entries
  * Each procurement record maps to a stock transaction

3. Stock → Accounting Integration

* Every stock movement must trigger financial impact:

  * Incoming stock → Debit Inventory account
  * Supplier liability → Credit Accounts Payable
* Ensure proper journal entries are created automatically

4. Procurement → Accounting Integration

* On purchase order approval / invoice:

  * Create or update Accounts Payable entries
  * Track supplier balances correctly

5. Data Consistency & Integrity

* Enforce relationships between:

  * procurement_id
  * stock_transaction_id
  * accounting_entry_id
* Use foreign keys or references where applicable
* Prevent orphan records

6. Real-Time Synchronization

* Changes in one module must reflect immediately in others
* Avoid delayed or batch-only syncing unless explicitly designed

7. Error Handling & Logging

* Log failures in:

  * Stock updates
  * Financial postings
* Ensure rollback or compensation logic exists for failed transactions

8. Refactor / Fix Existing Data

* Audit current data:

  * Identify mismatches between stock and procurement
  * Identify missing accounting entries
* Provide migration or repair scripts if necessary

9. API / Service Layer Alignment

* Ensure endpoints/services:

  * Do not allow isolated operations (e.g., stock update without procurement context)
* Introduce centralized service logic if needed

10. Testing Requirements

* Add integration tests covering:

  * Full procurement-to-accounting flow
  * Edge cases (partial deliveries, returns, cancellations)

Expected Outcome:

* Procurement, Stock, and Accounting operate as a single connected system
* Inventory levels always match procurement records
* Financial statements accurately reflect all stock and procurement activities
* No manual reconciliation required

Priority:
HIGH – This affects core business logic and financial accuracy. Fix ASAP.

Notes:

* Avoid quick fixes; ensure proper architectural alignment
* Maintain scalability for future modules (e.g., sales, warehouse, auditing)
