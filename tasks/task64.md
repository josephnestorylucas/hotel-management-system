Task: Decouple Restaurant from Bar While Keeping Drink Ordering

Objective:
Separate restaurant and bar modules in navigation and grouping while keeping drink ordering available in restaurant (water, wine, beverages).

This task must:
- Remove shared grouping between restaurant and bar modules
- Keep restaurant drink ordering functional
- Ensure restaurant and bar remain distinct modules

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Module Coupling

Before implementation, identify:
- Where restaurant and bar share navigation or layout
- Any shared routes, controllers, or views
- Drink ordering flow in restaurant POS

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing ordering logic

---

### 2) Module Separation

Required behavior:
- Restaurant views and sidebars do not link or group with bar
- Bar remains independent in navigation
- Restaurant can still order drinks

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Restaurant sidebar shows only restaurant items
2) Bar sidebar remains separate
3) Restaurant drink orders still work
4) No DB resets or data loss

---

Expected Outcome:

- Restaurant and bar are distinct while drink ordering remains available

Priority:
HIGH - Module separation
