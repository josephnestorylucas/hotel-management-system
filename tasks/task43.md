Task: Manage Bar Products for POS (Store-Owned Catalog)

Objective:
Enable adding and maintaining bar products so they appear in the Bar POS. Store roles must be able to create and manage bar products, similar to store products, including multiple varieties.

This task must:
- Add a bar product catalog that feeds the Bar POS product list
- Allow store roles to create, edit, and manage bar products
- Support product varieties (size, brand, bottle/can, etc.)
- Reuse existing product patterns, layouts, and styles
- Preserve existing bar sales logic and pricing rules

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing Product Patterns

Before implementation, identify:
- Current store product CRUD screens and models
- Any existing bar product models or categories
- POS product loading and filters
- Current role permissions for store managers/keepers

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing product forms and validation rules where possible

---

### 2) Bar Product Data Contract

Required behavior:
- Define fields for bar products
- Keep pricing and tax handling consistent with existing product logic

Required fields (adjust to existing naming conventions):
- name
- sku (if used)
- category (bar)
- unit (bottle, glass, can, etc.)
- price
- is_active
- varieties (optional collection: size, label, price override)

---

### 3) UI and Views

Required behavior:
- Create bar product management screens (index, create, edit)
- Reuse existing layouts, cards, and form styles
- Ensure the UI mirrors store product management patterns

---

### 4) POS Integration

Required behavior:
- Bar POS product list includes active bar products
- Varieties appear as selectable options if present
- Inactive products do not appear in POS

---

### 5) Access Control

Required behavior:
- Only store roles can manage bar products
- Bartenders can see products in POS but cannot edit them

---

### 6) Migration Plan (No Data Loss)

Required behavior:
- Add migrations for bar product fields or tables without dropping data
- Use additive migrations only (no destructive changes)
- Provide a safe rollback path

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Store roles can create and edit bar products
2) Products appear in Bar POS immediately when active
3) Varieties can be added and selected in POS
4) Pricing and tax behavior match existing rules
5) No existing data is removed or reset

---

Expected Outcome:

- Store roles manage bar products
- Bar POS shows a full bar catalog with varieties
- No regressions in sales or pricing

Priority:
HIGH - Bar catalog setup and POS readiness
