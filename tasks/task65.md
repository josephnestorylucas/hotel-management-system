Task: Simplify Menu Creation and Add Beverage Selection

Objective:
Simplify restaurant menu creation and allow adding drinks from store beverages, using the proven store module patterns.

This task must:
- Simplify menu creation flow
- Ensure menu creation works end-to-end
- Allow adding beverages/drinks from store items into menus

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Menu Creation Flow

Before implementation, identify:
- Current menu creation UI and validation
- Where menu creation fails or is too complex
- Store module beverage selection patterns

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse store module selection patterns

---

### 2) Menu Creation Changes

Required behavior:
- Streamline menu creation steps and fields
- Ensure successful menu creation and edit
- Add beverage selection from store items

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Menu creation completes without errors
2) Drinks from store can be added to menus
3) Menu edits preserve beverage selections
4) No DB resets or data loss

---

Expected Outcome:

- Menu creation is simpler and supports beverages from the store

Priority:
HIGH - Menu management
