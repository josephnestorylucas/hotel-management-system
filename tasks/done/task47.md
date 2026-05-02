Task: Remove "MRK" from All Receipt Prints

Objective:
Ensure the string "MRK" is not printed on any receipt output across the system.

This task must:
- Remove "MRK" from all receipt print templates and views
- Ensure no receipt output contains "MRK" after changes
- Keep receipt layout and totals unchanged

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Receipt Print Sources

Before implementation, identify:
- All receipt templates (web, POS, print, PDF)
- Any shared receipt partials or components
- Any hardcoded "MRK" strings or config constants

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing receipt rendering patterns

---

### 2) Required Changes

Required behavior:
- Remove "MRK" from all receipt outputs
- Do not remove other currency symbols or identifiers
- If "MRK" is in a shared constant, update it without breaking other views

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) All receipt printouts no longer show "MRK"
2) Receipt totals and formatting are unchanged
3) No DB resets or data loss

---

Expected Outcome:

- Receipts print without the "MRK" label

Priority:
MEDIUM - Print correctness
