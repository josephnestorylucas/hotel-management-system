Task: Fix Role-Based View Rendering for Accountant (Dashboard, Sidebar, and Access Control)

Objective:
Ensure that users with the Accountant role are shown the correct dashboard, sidebar, and views instead of Front Desk views. Enforce proper role-based routing and UI rendering across the system.

Problem Summary:

* When logging in as Accountant:

  * System incorrectly loads Front Desk dashboard and sidebar ❌
* Indicates:

  * Broken role-based routing OR
  * Incorrect role-to-view mapping OR
  * Missing Accountant UI configuration

---

### 1. Role-Based Routing Fix (CRITICAL)

* On login:

  * Detect user role (accountant)
  * Redirect to:
    → /accountant/dashboard

* Prevent fallback to:

  * /frontdesk/*
  * Any unrelated role routes

* Ensure backend returns correct role in auth payload:
  Example:
  {
  "user": {...},
  "role": "accountant"
  }

---

### 2. Sidebar Rendering Fix

* Implement dynamic sidebar based on role:

IF role == "accountant":
Load Accountant Sidebar

* Remove any hardcoded or default sidebar fallback

---

### 3. Accountant Sidebar (REQUIRED ITEMS)

Include:

* Dashboard
* Financial Overview
* Transactions / Journal Entries
* Accounts Payable
* Accounts Receivable
* Expense Management
* Reports (Profit/Loss, Balance Sheet)
* Audit Logs (optional but recommended)

---

### 4. Accountant Dashboard (CREATE/VERIFY)

Dashboard must include:

* Summary cards:

  * Total Revenue
  * Total Expenses
  * Net Profit
* Recent Transactions
* Pending Payments (AP/AR)
* Quick financial insights

---

### 5. Accountant Views (CREATE IF MISSING)

Ensure the following views exist and are accessible:

1. Transactions View
2. Journal Entries View
3. Accounts Payable View
4. Accounts Receivable View
5. Expense Tracking View
6. Financial Reports View

---

### 6. Access Control (RBAC Enforcement)

* Accountant should ONLY access:

  * Financial modules

* Accountant should NOT access:

  * Front Desk views
  * Store / Procurement (unless explicitly allowed)

* Protect routes:

  * /frontdesk/* → block accountant
  * /accountant/* → allow accountant only

---

### 7. Frontend Fix

* Ensure:

  * Role is stored (context, redux, or state)
  * Conditional rendering based on role
  * No default fallback to Front Desk UI

Example:
if (role === "accountant") {
render(AccountantLayout)
}

---

### 8. Backend Fix

* Verify:

  * Role is correctly assigned in DB
  * Auth API returns correct role
  * Middleware enforces role-based access

---

### 9. Testing

* Login as Accountant:

  * Must see Accountant dashboard + sidebar

* Login as Front Desk:

  * Must NOT see Accountant views

* Test route protection manually and via API

---

### 10. UI/UX Rules

* No mixed-role UI
* Clean separation of layouts per role
* Consistent navigation structure

---

Expected Outcome:

* Accountant sees ONLY accountant dashboard and features
* No leakage of Front Desk UI
* Proper role-based system behavior across frontend and backend

Priority:
HIGH – Core RBAC functionality is broken

Notes:

* Follow existing i18n system → include Swahili translations by default
* Do not hardcode roles; use centralized role management logic
