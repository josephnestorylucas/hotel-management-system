Task: Remove Front Desk Mirroring from Restaurant Waiter Side

Objective:
Remove the restaurant waiter side UI/pages that mirror front desk functionality.

This task must:
- Identify and remove restaurant waiter views that duplicate front desk
- Keep restaurant waiter features that are not front desk mirrors
- Avoid changes to unrelated modules

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Waiter vs Front Desk

Before implementation, identify:
- Restaurant waiter routes and views
- Which waiter pages mirror front desk functionality
- Any shared components or layouts

Implementation rule:
- Follow existing architecture and naming conventions
- Remove only the mirrored front desk pieces

---

### 2) Removal Scope

Required behavior:
- Remove or hide mirrored front desk views from waiter side
- Ensure waiter navigation no longer links to those views
- Keep waiter-specific pages intact

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Mirrored front desk views no longer appear for waiter
2) Waiter navigation remains functional
3) No unrelated behavior regressions
4) No DB resets or data loss

---

Expected Outcome:

- Restaurant waiter UI no longer mirrors front desk

Priority:
HIGH - Role clarity
