Task: Buffet Menus and Menu Images

Objective:
Support buffet menu creation and add images to menu items and buffet menus.

This task must:
- Allow creating buffet menus
- Add images for menu items and buffet menus
- Keep menu display consistent with current styling

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Menu Models and Media

Before implementation, identify:
- Current menu and buffet structures (if any)
- Existing media/image handling patterns
- Menu display views that need images

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing media handling logic

---

### 2) Buffet Menus

Required behavior:
- Create and manage buffet menus
- Buffet menus appear in relevant restaurant views

---

### 3) Menu Images

Required behavior:
- Upload or select images for menu items and buffet menus
- Display images in menu lists and detail views

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Buffet menus can be created and displayed
2) Menu images display correctly
3) No DB resets or data loss

---

Expected Outcome:

- Buffet menus and menu images are supported

Priority:
HIGH - Menu presentation
