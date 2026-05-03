Task: Add "Mark All Read" to Notifications Modal

Objective:
Add a "Mark All Read" button to the notifications modal to allow users to mark all notifications as read at once.

This task must:
- Add a "Mark All Read" action in the notifications modal
- Mark all unread notifications as read for the current user
- Update the UI state without a full page reload when possible

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Notifications UI and Actions

Before implementation, identify:
- The notifications modal view and JS behavior
- Existing routes/controllers for marking notifications as read
- Current notification query and unread count logic

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing notification actions and endpoints

---

### 2) UI and Action

Required behavior:
- Add a "Mark All Read" button in the modal header or actions area
- Trigger an action that marks all unread notifications as read
- Update unread count and list state in the modal

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) "Mark All Read" marks all unread notifications for the user
2) Unread count updates immediately
3) Notification list reflects the read state
4) No DB resets or data loss

---

Expected Outcome:

- Users can clear all notifications from the modal in one action

Priority:
MEDIUM - Notifications usability
