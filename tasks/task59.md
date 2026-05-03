Task: Checkout-to-Cleaning Workflow with Supervisor and House Help

Objective:
Implement the end-to-end room cleaning workflow using existing statuses only: available, needs_cleaning, out_of_order, occupied, reserved. Front desk checkout sets room status to needs_cleaning, supervisor gets notified and assigns cleaning to house help, house help completes cleaning, supervisor confirms, and room returns to available status. Add required tabs, dashboards, and sidebar entries for supervisor and house help.

This task must:
- Set room status to needs_cleaning on checkout
- Room is bookable only when status is available across all booking views
- Notify supervisor that rooms need cleaning
- Allow supervisor to select rooms, assign house help, and track status
- Allow house help to view assigned rooms and mark cleaning done
- Require supervisor confirmation before returning room to available
- Add tabs/dashboards/sidebar entries for supervisor and house help
- Add front desk hooks to trigger the workflow

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Room Status and Cleaning Flow

Before implementation, identify:
- Current room status transitions on checkout
- Existing housekeeping or cleaning modules (if any)
- Notification patterns for supervisor roles
- Existing dashboards/sidebars for supervisor and house help
- Role permissions and access control for room status changes

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing notification and status workflow patterns

---

### 2) Checkout Triggers

Required behavior:
- Front desk checkout sets room status to needs_cleaning
- Room is added to the cleaning queue after checkout
- Status change is auditable (user, timestamp)
- Non-available rooms are excluded from booking selection across all views

---

### 3) Supervisor Workflow

Required behavior:
- Supervisor receives notifications for rooms needing cleaning
- Supervisor can view a list of rooms requiring cleaning
- Supervisor assigns house help to specific rooms
- Supervisor can track cleaning status and confirm completion

---

### 4) House Help Workflow

Required behavior:
- House help can view assigned rooms
- House help can mark cleaning as done
- Completion triggers supervisor confirmation step

---

### 5) Status Confirmation

Required behavior:
- Only supervisor can confirm cleaning completion
- After confirmation, room status returns to available
- Status history reflects assignment and confirmation

---

### 6) UI and Navigation

Required behavior:
- Add dashboard widgets/tabs for supervisor and house help
- Add sidebar entries for cleaning queues and assignments
- Front desk UI reflects the dirty/unavailable status after checkout

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Checkout sets room to dirty/unavailable and adds to cleaning queue
2) Supervisor receives notification and assigns house help
3) House help marks cleaning done
4) Supervisor confirms and room returns to available
5) Rooms are bookable only when status is available across all booking views
6) Dashboards and sidebars show the new workflow
7) No DB resets or data loss

---

Expected Outcome:

- Full checkout-to-cleaning workflow is in place
- Supervisor and house help have clear UI and assignment flow
- Room status is accurate and auditable

Priority:
HIGH - Room turnover workflow
