# HMS Floating Bug Reporter — Implementation Plan

> **Feature:** Floating bug report widget for testers  
> **Stack:** Laravel Blade + Alpine.js + Dedicated SQLite DB  
> **Date:** 2026-05-27

---

## Architecture Overview

```
[Floating Button] → [Click] → [Modal Form] → [Laravel API Route] → [bugs.sqlite]
                                                                         ↓
                                                              [View bugs anytime via
                                                               simple bugs dashboard]
```

---

## STEP 1 — SQLite Database Setup

### Create the separate DB file

```
database/bugs.sqlite   ← dedicated file, never touches main DB
```

### Add connection in `config/database.php`

Add a second connection named `bugs` pointing to `bugs.sqlite`.

### Create a dedicated migration (run on the bugs connection)

**Table: `bug_reports`**

| Column | Type | Notes |
|---|---|---|
| `id` | integer PK | auto-increment |
| `title` | string | short bug name |
| `details` | text | full description |
| `page_url` | string | auto-captured current URL |
| `module` | string | which HMS module (dropdown) |
| `severity` | string | low / medium / high / critical |
| `reported_by` | string | tester name (simple text, no auth required) |
| `status` | string | open / acknowledged / fixed (default: open) |
| `created_at` | timestamp | auto |
| `updated_at` | timestamp | auto |

Run migration explicitly on the `bugs` connection — never touches main DB.

---

## STEP 2 — Laravel Backend

### Model: `BugReport.php`

- Uses `bugs` DB connection explicitly
- Fillable: title, details, page_url, module, severity, reported_by, status

### Routes (in `routes/web.php`)

```
POST   /bug-reports          → BugReportController@store   (save bug)
GET    /bug-reports          → BugReportController@index   (view all bugs — protected)
PATCH  /bug-reports/{id}     → BugReportController@update  (mark status)
DELETE /bug-reports/{id}     → BugReportController@destroy (delete)
```

### Controller: `BugReportController.php`

- `store()` — validate + save to bugs.sqlite, return JSON response
- `index()` — list all bugs with filters (module, severity, status)
- `update()` — change status only
- `destroy()` — delete a bug report

---

## STEP 3 — Blade Widget (include in master layout)

### File: `resources/views/components/bug-reporter.blade.php`

Include once in your master layout (`layouts/app.blade.php`) before `</body>`:

```blade
@include('components.bug-reporter')
```

### Widget structure (Alpine.js powered)

```
[🐛 floating button — fixed bottom-right]
    ↓ click
[Modal overlay]
    ├── Title input (required)
    ├── Module dropdown (Front Desk / Rooms / Billing / Guests / Housekeeping / Reports / Other)
    ├── Severity dropdown (Low / Medium / High / Critical)
    ├── Details textarea
    ├── Your name input
    ├── Page URL (auto-filled, hidden)
    └── [Submit] [Cancel]
```

### Alpine.js state

```javascript
{
  open: false,
  loading: false,
  success: false,
  form: {
    title: '',
    module: '',
    severity: 'medium',
    details: '',
    reported_by: '',
    page_url: window.location.href
  }
}
```

### Behavior

- Button always visible, fixed position, `z-index: 9999`
- Click → modal slides in
- Submit → POST to `/bug-reports` via fetch (no page reload)
- Success → show "Bug reported! ✓" message for 2 seconds → modal closes → form resets
- Error → show error message inline
- ESC key closes modal
- Clicking backdrop closes modal

---

## STEP 4 — Bug Dashboard

### File: `resources/views/bugs/index.blade.php`

Simple admin-only page at `/bug-reports`:

**Features:**
- Table of all bugs with columns: ID, Title, Module, Severity, Page, Reported By, Date, Status
- Color-coded severity badges (red=critical, orange=high, yellow=medium, green=low)
- Filter by: module, severity, status
- Inline status update dropdown per row
- Delete button per row
- Export to CSV button (simple loop, no package needed)
- Bug count summary at top: Total / Open / Fixed

**No auth complexity** — protect with a simple middleware check or `.env` flag `BUG_DASHBOARD_ENABLED=true`

---

## STEP 5 — Styling

Use existing Tailwind classes from HMS — no new CSS needed.

**Floating button:** red background, bug emoji or exclamation icon, rounded-full, shadow-lg, fixed bottom-6 right-6  
**Modal:** white card, centered, max-w-lg, backdrop blur  
**Severity badges:** use Tailwind color utilities  

---

## File Checklist

```
config/database.php                          ← add bugs connection
database/bugs.sqlite                         ← create empty file
database/migrations/bugs/                    ← separate migrations folder
  └── create_bug_reports_table.php
app/Models/BugReport.php
app/Http/Controllers/BugReportController.php
resources/views/components/bug-reporter.blade.php
resources/views/bugs/index.blade.php
routes/web.php                               ← add 4 routes
```

---

## Implementation Order

1. DB connection config + create `bugs.sqlite` file
2. Migration + run it on bugs connection
3. Model
4. Controller + routes
5. Blade widget component
6. Include in master layout
7. Bug dashboard view
8. Test full flow

---

## Notes

- `bugs.sqlite` should be in `.gitignore` if you don't want bug data in version control
- The widget appears on **every page** automatically via master layout
- No extra packages needed — pure Laravel + Alpine.js + Tailwind
- To view bugs: just visit `/bug-reports` while testing
