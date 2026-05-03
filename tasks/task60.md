Task: Room Maintenance Workflow for Out of Order Status

Objective:
Add a maintenance workflow for rooms marked out_of_order by front desk, so supervisors can assign house help, capture a reason, confirm completion, and return the room to available status.

This task must:
- Allow front desk to set room status to out_of_order only
- Capture a reason when marking a room out_of_order
- Notify supervisor of maintenance requests
- Allow supervisor to assign house help and track status
- Allow supervisor to confirm maintenance and set room back to available
- Update or create views to support the workflow

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Current Room Status and Maintenance Flow

Before implementation, identify:
- Current room status change UI and permissions
- Existing maintenance or out_of_order handling (if any)
- Notification patterns for supervisor roles
- Existing dashboards/sidebars for supervisor and house help

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing status and assignment patterns

---

### 2) Front Desk Request

Required behavior:
- Front desk can set rooms to out_of_order only
- Require a reason when setting out_of_order
- Record user and timestamp

---

### 3) Supervisor Assignment

Required behavior:
- Supervisor sees out_of_order requests
- Supervisor assigns house help to maintenance task
- Supervisor can track maintenance status

---

### 4) House Help Workflow

Required behavior:
- House help sees assigned maintenance tasks
- House help can mark maintenance as done

---

### 5) Confirmation and Status Restore

Required behavior:
- Supervisor confirms maintenance completion
- Room status returns to available after confirmation
- Status history reflects request, assignment, and confirmation

---

### 6) UI and Navigation

Required behavior:
- Add tabs or dashboard widgets for maintenance requests
- Add sidebar entries for supervisor and house help maintenance views
- Update room status views to show out_of_order reason and current maintenance status

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Front desk can set room to out_of_order with a reason
2) Supervisor sees request and assigns house help
3) House help marks maintenance done
4) Supervisor confirms and room returns to available
5) Views and sidebars show maintenance workflow
6) No DB resets or data loss

---

Expected Outcome:

- Maintenance workflow is in place for out_of_order rooms
- Supervisor and house help can manage and confirm maintenance
- Room status restores to available after confirmation

Priority:
HIGH - Room maintenance flow
