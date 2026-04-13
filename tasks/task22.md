Task: Accountant Creating Accounting Entries (Journal Entries) + Posting Controls

Objective:
Allow the Accountant to create and manage accounting entries (journal entries) safely:
- Create balanced debit/credit entries
- Post entries to ledger
- Prevent tampering after posting
- Maintain audit readiness

Problem Summary:

* System needs a standard way for accountants to record manual adjustments and non-module entries.
* Entries must be controlled to avoid unbalanced or editable posted records.

---

### 1. Journal Entry Data Rules

A Journal Entry must have:
- entry_no (unique)
- entry_date
- description
- source: manual / system
- status: draft / posted / reversed
- created_by
- posted_by
- posted_at

Journal Entry Lines:
- account_id (from chart of accounts)
- debit
- credit
- memo (optional)

Rules:
- Total debit must equal total credit before posting
- Draft entries can be edited; posted entries cannot
- Reversal creates a new reversing entry (do not delete)

---

### 2. Accountant Views

Create/verify:

1) Journal Entries List
- filter by date/status/source
- show totals and status

2) Create Journal Entry
- add multiple lines
- live validation (balanced totals)

3) Journal Entry Detail
- view lines
- post action
- reversal action (with reason)

---

### 3. Posting Controls

- Only Accountant (and Manager if allowed) can post
- Posting locks the entry
- Create a financial transaction/audit record when posting (if system uses such record)

---

### 4. Integration With Existing Modules (Non-breaking)

- System-generated entries (procurement, checkout, supplier payments) should link to journal entries where applicable
- Do not break existing auto-posting flows

---

### 5. Access Control

- Only accountant roles can create/post entries
- Other roles can only view if explicitly allowed

---

### 6. i18n

- All labels and statuses must be translatable
- Add Swahili translations for new labels

---

### 7. Testing (Manual Acceptance)

1) Draft entry can be saved and edited
2) Posting requires balanced debits/credits
3) Posted entry cannot be edited
4) Reversal creates a new reversing entry
5) Non-accountant cannot create or post

---

Expected Outcome:

* Accountant can create and post audit-safe journal entries
* Ledger integrity is protected

Priority:
HIGH – Core accounting functionality

Notes:
- Use existing chart of accounts; do not hardcode accounts
- Keep UI consistent with accounting center
