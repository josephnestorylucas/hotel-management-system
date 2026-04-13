Task: Refactor Roles and Permissions for Procurement & Store Modules + Add Managerial Oversight Views

Objective:
Separate responsibilities between Store Manager and Manager roles, enforce proper approval flow for LPOs, and introduce managerial oversight features for stock monitoring. Additionally, create/update views to reflect these changes.

Problem Summary:

* Store Manager is currently:

  * Adding suppliers ✅ (correct)
  * Creating LPOs ✅ (correct)
  * Accepting/Rejecting LPOs ❌ (incorrect)
* Approval authority should belong ONLY to Manager role
* Manager currently lacks visibility into stock levels and movements

---

### 1. Role Responsibility Fix

#### Store Manager (Correct Scope)

Keep:

* Create Suppliers
* Create LPO (Local Purchase Orders)
* View stock
* Manage stock operations (limited)

Remove:

* ❌ Accept LPO
* ❌ Reject LPO

---

#### Manager Role (New/Updated Scope)

Add:

* ✅ Approve (Accept) LPO
* ✅ Reject LPO
* ✅ View all procurement activities
* ✅ View and monitor stock levels
* ✅ View stock movement history (audit-level visibility)

---

### 2. LPO Approval Flow Fix

* LPO lifecycle should be:
  Draft → Submitted → Approved / Rejected

Flow:

1. Store Manager creates LPO (Draft)
2. Store Manager submits LPO
3. Manager:

   * Approves → triggers procurement + stock + accounting flow
   * Rejects → sends back with reason

Enforce:

* Only Manager can change status to Approved/Rejected
* Store Manager cannot override this

---

### 3. Permissions Update (RBAC)

Example:

Store Manager:

* create_supplier
* create_lpo
* submit_lpo
* view_stock

Manager:

* approve_lpo
* reject_lpo
* view_all_lpo
* view_stock
* view_stock_movements
* view_reports

---

### 4. View Creation / Update (IMPORTANT)

#### A. Store Manager Views

1. Supplier Management View

* Add / Edit suppliers

2. LPO Creation View

* Create LPO
* Add items, quantities, supplier

3. LPO List View

* Status tracking (Draft, Submitted, Approved, Rejected)
* NO approve/reject buttons

---

#### B. Manager Views (NEW)

1. LPO Approval Dashboard

* List of all submitted LPOs
* Actions:

  * Approve
  * Reject (with reason)
* Filters:

  * Status
  * Date
  * Supplier

2. Stock Overview Dashboard

* Current stock levels
* Low stock alerts
* Category-based grouping

3. Stock Movement View

* Full audit trail:

  * Incoming (procurement)
  * Outgoing (usage/sales)
* Fields:

  * Item
  * Quantity
  * Date
  * Source (LPO, adjustment, etc.)

---

### 5. Backend Logic Changes

* Restrict endpoints:

  * /lpo/approve → Manager only
  * /lpo/reject → Manager only

* Add validation:

  * Reject requests from unauthorized roles

* Ensure:

  * Approval triggers downstream processes (stock + accounting)

---

### 6. UI/UX Rules

* Hide approve/reject buttons from Store Manager UI
* Show clear status indicators:

  * Draft
  * Pending Approval
  * Approved
  * Rejected

---

### 7. Data Integrity

* Track:

  * approved_by (manager_id)
  * rejected_by (manager_id)
  * rejection_reason

---

### 8. Testing

* Ensure:

  * Store Manager cannot approve/reject via UI or API
  * Manager can fully control approval flow
  * Views reflect correct permissions

---

Expected Outcome:

* Clean separation of duties
* Proper approval workflow
* Manager has full oversight of procurement and stock
* System is audit-ready and scalable

Priority:
HIGH – Impacts procurement control and system integrity

Notes:

* Follow existing UI patterns and i18n (include Swahili translations by default)
* Avoid breaking existing procurement-stock-accounting integrations


and  also  add  the  new  views  and  the   new  entry  to  the  sidebar   so  lets  go  
