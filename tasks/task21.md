Task: Create Insightful Accounting Report Center (Accounting Center)

Objective:
Create an Accounting Report Center that provides useful, decision-ready financial reports and insights for accountants/managers, without introducing new architecture.

Problem Summary:

* Accounting data exists (transactions, payments, charges, supplier payables), but reporting is fragmented.
* Accountant needs a single “Reports Center” with core financial statements + operational finance insights.

---

### 1. Reports Center Landing Page

Add a Reports Center page under Accountant navigation.

It should include:
- Report cards/links
- Date range selector (global for reports)
- Export action (CSV/PDF) only if exporting already exists; otherwise keep it view-only

---

### 2. Required Reports (Minimum Set)

A) Profit & Loss (Income Statement)
- Total revenue by module
- Total expenses
- Net profit

B) Balance Sheet (Basic)
- Assets (Cash/Bank, Inventory if tracked)
- Liabilities (Accounts Payable)
- Equity (if supported)

C) Cashflow Summary (Simple)
- Cash in vs cash out for a period
- Breakdown by payment method (cash, card, mobile, bank)

D) Accounts Payable Aging
- Outstanding payables grouped by age buckets
- Supplier totals

E) Receipts Summary
- Total receipts by date
- Total receipts by module
- Total receipts by payment method

---

### 3. Data Integrity Rules

- Reports must be computed from existing source-of-truth tables/models
- No hardcoded totals
- Ensure consistent currency handling (TZS/USD) and exchange-rate logic using existing system settings

---

### 4. Access Control

- Accountant can access all reports
- Manager can access reports if permitted
- Other roles must not access accounting reports

---

### 5. Performance Rules

- Use indexed date filtering
- Paginate large lists
- Avoid loading entire transaction tables into memory

---

### 6. i18n

- All report titles and labels must use translation system
- Add Swahili translations for new report labels

---

### 7. Testing (Manual Acceptance)

1) Reports Center loads
2) Date range filters correctly affect each report
3) Totals match transaction data for the same period
4) Authorization blocks non-accountant users

---

Expected Outcome:

* Accountant has a single place to view key financial reports
* Reports are accurate, consistent, and traceable

Priority:
HIGH – Needed for financial decision-making and audits

Notes:
- Keep scope to the minimum set above; do not add extra analytics features
- Reuse existing financial transaction structures
