Task: Record Restaurant Sales in Accounting and Improve Daily Sales Report

Objective:
Ensure restaurant sales are recorded in accounting modules and enhance the daily sales report with more detailed information.

This task must:
- Record restaurant sales in accounting
- Improve the daily sales report output
- Keep report styling and performance stable

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Sales Posting and Reporting

Before implementation, identify:
- How restaurant sales are currently saved
- Accounting posting flow and journal entries
- Current daily sales report data sources

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing accounting posting patterns

---

### 2) Accounting Integration

Required behavior:
- Restaurant sales post to accounting consistently
- Sales are linked to receipts or orders
- No double posting

---

### 3) Daily Sales Report Enhancements

Required behavior:
- Add more detailed fields to the report (items, totals, payment methods)
- Keep report view aligned with existing styles

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Restaurant sales appear in accounting
2) Daily sales report shows the added details
3) No DB resets or data loss

---

Expected Outcome:

- Restaurant sales are accounted for and reports are more informative

Priority:
HIGH - Financial reporting
