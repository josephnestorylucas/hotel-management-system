Task: Fix Front Desk Conference Hall Management

Objective:
Allow front desk users to add and edit conference halls, ensure the front desk sidebar includes the conference hall management entry, and verify the module works end to end.

This task must:
- Allow front desk to create and edit conference halls
- Ensure front desk can access conference hall management from the sidebar
- Fix any missing views or routes related to conference hall management
- Validate conference hall management works for front desk roles

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Access and Views

Before implementation, identify:
- Current role permissions for front desk and conference hall routes
- Sidebar menu configuration for front desk
- Missing or misnamed view templates (e.g., show, create, edit)
- Controller methods and routes for conference hall management

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing permissions, policies, and menu patterns

---

### 2) Front Desk Permissions

Required behavior:
- Front desk role can access conference hall index, create, edit, and show
- Block access only where explicitly intended
- Ensure middleware and policies allow front desk where required

---

### 3) Sidebar Access

Required behavior:
- Front desk sidebar includes conference hall management
- Link points to the correct route
- Menu visibility follows existing role-based logic

---

### 4) Missing Views and Errors

Required behavior:
- Fix missing view errors such as conference-halls.show
- Ensure all conference hall views exist and render correctly
- Keep layouts consistent with current UI

---

### 5) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Front desk can open conference hall management from the sidebar
2) Front desk can create a conference hall
3) Front desk can edit a conference hall
4) Front desk can view conference hall details without errors
5) No DB resets or data loss

---

Expected Outcome:

- Front desk can manage conference halls without access or view errors
- Sidebar navigation works correctly for conference hall management

Priority:
HIGH - Front desk operations
