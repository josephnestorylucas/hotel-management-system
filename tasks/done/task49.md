Task: Fix Guests List and Search Loading State

Objective:
Resolve the issue where the Guests page shows "guests.search_guests Loading..." and "Current Guests Loading..." indefinitely.

This task must:
- Ensure guest search and current guests load correctly
- Replace any raw translation keys showing in the UI
- Provide proper loading and error states

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Guests Data Flow

Before implementation, identify:
- The Guests page view/template and JS entry point
- API endpoints or controllers used for guest search and current guests
- Translation keys and locale files for guest labels
- Any failing requests in logs or browser console

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing data fetch and error handling patterns

---

### 2) Data Loading

Required behavior:
- Guests list loads and renders current guests
- Search results load and render correctly
- Loading indicator stops once data is returned or fails

---

### 3) Translation Keys

Required behavior:
- Ensure labels display correctly (no raw keys like "guests.search_guests")
- Validate locale entries exist for all guest labels

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Guests page loads without infinite loading
2) Current guests list displays data
3) Search returns results and updates the list
4) Labels show human-readable text (no raw keys)
5) No DB resets or data loss

---

Expected Outcome:

- Guests page loads correctly with working search and current guests list
- Loading indicators and labels render as expected

Priority:
HIGH - Guest operations
