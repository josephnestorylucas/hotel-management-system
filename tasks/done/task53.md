Task: Enforce Supervisor Confirmation Before Laundry Completion

Objective:
Require supervisor confirmation in the laundry workflow before any laundry order can be marked completed.

This task must:
- Add a supervisor confirmation step in the laundry flow
- Prevent marking laundry orders as completed without supervisor approval
- Preserve existing laundry statuses and history

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Laundry Flow

Before implementation, identify:
- Current laundry statuses and transitions
- Where completion is triggered (UI, controller, job)
- Role permissions for laundry actions
- Any existing approval or confirmation patterns

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing status transitions and permissions where possible

---

### 2) Confirmation Step

Required behavior:
- Supervisor must confirm a processed order before completion
- Non-supervisor users cannot mark orders completed
- Confirmation action is logged with user and timestamp

---

### 3) Status Rules

Required behavior:
- Orders move to a "pending confirmation" state after processing
- Only supervisor can transition to "completed"
- Existing completion logic remains intact after confirmation

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Processed laundry cannot be completed without supervisor confirmation
2) Supervisor can confirm and complete the order
3) Status history reflects confirmation step
4) No DB resets or data loss

---

Expected Outcome:

- Laundry completion requires supervisor confirmation
- Status flow is consistent and auditable

Priority:
HIGH - Laundry workflow control
