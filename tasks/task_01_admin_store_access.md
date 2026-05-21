# Task 01 — Fix Admin Access to Store Pages

## Problem
Admin role is incorrectly accessing store pages that should be restricted or role-scoped.

---

## Root Cause Investigation

Check the following:

1. **Middleware on store routes** — are they protected with correct role guards?
2. **Role hierarchy** — does admin inherit all roles including store/POS?
3. **Sidebar nav** — is the store link shown for admin when it shouldn't be?
4. **Policy / Gate** — are store page policies checked at controller level?

---

## Fix Checklist

### Step 1 — Audit Store Routes
```php
// routes/web.php
// Check which middleware wraps store routes

Route::middleware(['auth', 'role:cashier,supervisor'])->group(function () {
    Route::get('/store', [StoreController::class, 'index']);
    // ...
});
```

- [ ] Identify which roles should access store pages
- [ ] Confirm `role:admin` is NOT included unless intentional
- [ ] Check for wildcard middleware that allows all authenticated users

---

### Step 2 — Check Middleware Logic
```php
// app/Http/Middleware/RoleMiddleware.php

public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    $userRole = auth()->user()->role; // or roles if many-to-many

    if (!in_array($userRole, $roles)) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
```

- [ ] Verify middleware correctly checks role
- [ ] Verify `admin` is blocked from store if not in allowed roles
- [ ] Verify `abort(403)` fires correctly (not silent redirect to wrong page)

---

### Step 3 — Fix Sidebar Navigation
```blade
{{-- layouts/sidebar.blade.php --}}

@if(in_array(auth()->user()->role, ['cashier', 'supervisor', 'front_desk']))
    <li>
        <a href="{{ route('store.index') }}">Store / POS</a>
    </li>
@endif
```

- [ ] Remove store link from admin sidebar if admin should not see it
- [ ] Test: login as admin, confirm store link is hidden
- [ ] Test: login as cashier, confirm store link is visible

---

### Step 4 — Controller-Level Guard (Defense in Depth)
```php
// app/Http/Controllers/StoreController.php

public function __construct()
{
    $this->middleware('role:cashier,supervisor,front_desk');
}
```

- [ ] Add constructor middleware to StoreController
- [ ] Add `Gate::authorize()` or Policy check if needed

---

### Step 5 — Test Scenarios

| Role | Store Access | Expected |
|------|-------------|----------|
| admin | ❌ | 403 Forbidden |
| supervisor | ✅ | OK |
| cashier | ✅ | OK |
| front_desk | depends | check spec |
| housekeeping | ❌ | 403 Forbidden |

- [ ] Test each role manually
- [ ] Confirm 403 shows proper error page (not blank or crash)

---

## Files to Modify

| File | Change |
|------|--------|
| `routes/web.php` | Fix middleware on store route group |
| `app/Http/Middleware/RoleMiddleware.php` | Verify role check logic |
| `resources/views/layouts/sidebar.blade.php` | Hide store link for admin |
| `app/Http/Controllers/StoreController.php` | Add constructor middleware guard |

---

## Done When
- [ ] Admin cannot access store pages (gets 403)
- [ ] Cashier/Supervisor can access store normally
- [ ] Sidebar does not show store link for admin
- [ ] All role tests pass
