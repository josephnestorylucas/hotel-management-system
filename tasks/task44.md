Task: Add Product Images (Local or CDN)

Objective:
Allow products to have images. Images may be uploaded locally or referenced via a CDN URL. Images must appear in product management and be displayed in the Bar POS product list.

This task must:
- Support product images (upload or CDN URL)
- Show images in product lists and POS tiles
- Define and document where images are stored/served (local path or CDN)
- Ensure images render in all views where the product is shown
- Reuse existing media or storage patterns
- Keep layouts and styles consistent with current UI

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing Media Patterns

Before implementation, identify:
- Any existing media library usage
- Current product image fields (if any)
- Storage disks and URL generation patterns
- Where current product images (if any) are stored or referenced

Implementation rule:
- Reuse existing media library helpers or storage logic
- Avoid parallel image systems

---

### 2) Data Contract

Required behavior:
- Extend product schema to support image data

Required fields (adjust to existing naming conventions):
- image_path (local upload) or
- image_url (CDN or external)
- image_source (local|cdn) if needed

---

### 3) UI and Forms

Required behavior:
- Add image upload field to product create/edit screens
- Add image URL field for CDN use
- Validate allowed file types and URL format
- Show image preview in the form
- Show the stored image location/URL in the form (read-only helper text)

---

### 4) Image Usage Across Views

Required behavior:
- Show product image thumbnails in all product-related views (index, show, edit)
- Show product image thumbnails in POS product list
- Use a fallback placeholder if no image exists
- Ensure any receipt/print view that already shows product info also includes the image when appropriate

---

### 5) Storage Location and Access

Required behavior:
- Define the canonical storage path for local uploads (e.g., storage disk and folder)
- Ensure URLs resolve correctly in web views and POS
- Use existing storage helpers for public URLs

---

### 6) Migration Plan (No Data Loss)

Required behavior:
- Add columns with safe defaults
- Do not drop or clear existing data
- Provide rollback path

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Product can be saved with a local image upload
2) Product can be saved with a CDN image URL
3) Image appears in all product views and POS
4) Image URLs resolve correctly in the browser
5) No DB resets or data loss

---

Expected Outcome:

- Products support images from upload or CDN
- POS shows product thumbnails
- UI stays consistent with current styling

Priority:
MEDIUM - Visual clarity in POS
