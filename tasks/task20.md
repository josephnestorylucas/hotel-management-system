Task: Add Icons to Accountant Sidebar + Ensure Consistent Navigation

Objective:
Improve the accountant sidebar navigation by adding appropriate icons to each accountant menu item, while preserving role-based rendering rules.

Problem Summary:

* Accountant UI exists/being created, but the sidebar lacks icons or has inconsistent icons.
* Icons must be consistent with the existing design system and not introduce new UI patterns.

---

### 1. Sidebar Items (Accountant)

Ensure the Accountant sidebar includes (and each has an icon):
- Dashboard
- Financial Overview
- Transactions
- Journal Entries
- Accounts Payable
- Accounts Receivable
- Supplier Payments
- Receipts
- Reports Center
- Audit Logs (if present)

---

### 2. Icon Rules (STRICT)

- Use only the icon library already used in the project
- Do not add a new icon package
- Ensure icons are visually consistent (same style/size)

---

### 3. Role-Based Rendering (Must Not Break)

- Icons must render only inside the accountant layout/sidebar
- Do not affect Front Desk / Store Manager / Manager sidebars

---

### 4. i18n

- Sidebar labels must use existing translation system
- Add Swahili translations for any new labels

---

### 5. Testing

1) Login as Accountant:
- sidebar renders with icons on each item

2) Login as other roles:
- no accountant icons/links appear

---

Expected Outcome:

* Accountant sidebar looks complete and consistent
* No role leakage across layouts

Priority:
MEDIUM – UX improvement but important for usability

Notes:
- Keep changes minimal; do not redesign the whole UI
