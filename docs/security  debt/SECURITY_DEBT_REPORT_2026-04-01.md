# Security Debt Report
Date: 2026-04-01  
Project: `hotel-management-system`  
Reviewer: Codex (static code review + dependency audit)

## Scope & Method
- Reviewed routes, middleware, controllers, models, migrations, and upload paths.
- Ran dependency audits:
  - `npm audit --json` (found vulnerabilities)
  - `composer audit --format=json` (no advisories)
- Focused on exploitable issues: access control, payment integrity, data exposure, and known CVEs.

---

## Executive Summary
The most serious security debt is in authorization boundaries and payment/charge integrity checks. Several endpoints allow broader access than intended, and some route/model combinations do not enforce parent-child ownership. This creates risk of unauthorized operational actions and financial data tampering.  

Priority for tomorrow: fix route-level authorization first, then fix payment/charge binding checks, then close data exposure and dependency CVEs.

---

## Findings (Prioritized)

## 1) Missing role restrictions on sensitive module endpoints
Severity: **High**

Evidence:
- [routes/web.php](C:\Users\DADY\Desktop\projects\hotel-management-system\routes\web.php:182) `laundry/orders` index has no role middleware.
- [routes/web.php](C:\Users\DADY\Desktop\projects\hotel-management-system\routes\web.php:190) `laundry/orders/{laundryOrder}` show has no role middleware.
- [routes/web.php](C:\Users\DADY\Desktop\projects\hotel-management-system\routes\web.php:375) through [routes/web.php](C:\Users\DADY\Desktop\projects\hotel-management-system\routes\web.php:386) restaurant order create/show/send/ready/serve/cancel/add/remove mostly have no role middleware.
- These are inside `auth` group, so any authenticated user can hit them.

Impact:
- Cross-department data exposure and unauthorized operational actions (restaurant/laundry workflows).

Fix:
- Add explicit role middleware to every route in laundry/restaurant groups.
- Restrict read and write operations separately (least privilege).

---

## 2) IDOR: `OrderItem` not verified to belong to provided `Order`
Severity: **High**

Evidence:
- [app/Http/Controllers/Restaurant/OrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Restaurant\OrderController.php:331) `removeItem(Order $order, OrderItem $orderItem)`
- [app/Http/Controllers/Restaurant/OrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Restaurant\OrderController.php:336) directly updates `$orderItem` without checking `$orderItem->order_id === $order->id`.

Impact:
- A user can remove/cancel items from other orders by supplying another valid `orderItem` UUID.

Fix:
- Enforce parent-child binding check and abort `403` on mismatch.
- Prefer nested scoped binding (`Route::scopeBindings()` / scoped resource bindings).

---

## 3) Payment integrity flaw: `charge_id` not bound to selected `booking`
Severity: **High**

Evidence:
- [app/Http/Controllers/PaymentController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\PaymentController.php:38) and [app/Http/Controllers/PaymentController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\PaymentController.php:65) load `BookingCharge::findOrFail($request->charge_id)` only.
- No check that `booking_charges.booking_id === $booking->id`.
- [app/Services/Payment/PaymentEngine.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Services\Payment\PaymentEngine.php:152) marks linked charge paid after successful verify.

Impact:
- Can attach/settle a charge from another booking.
- Financial records can be tampered across guests/bookings.

Fix:
- Resolve charge from booking relation: `$booking->bookingCharges()->whereKey($request->charge_id)->firstOrFail()`.
- Enforce amount consistency (exact charge amount or controlled partial payment policy).

---

## 4) Booking charge can be marked paid without booking ownership validation
Severity: **High**

Evidence:
- [app/Http/Controllers/BookingChargeController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\BookingChargeController.php:23) `markPaid(BookingCharge $bookingCharge)` directly marks paid.
- Route does not include booking context for this endpoint: [routes/web.php](C:\Users\DADY\Desktop\projects\hotel-management-system\routes\web.php:220).

Impact:
- A user with allowed role in payments area can mark arbitrary charges paid if UUID is known.

Fix:
- Change route to nested booking charge context or verify ownership in controller.
- Add policy authorization for charge payment transitions.

---

## 5) Cross-booking charge injection in laundry/restaurant settlement
Severity: **High**

Evidence:
- [app/Http/Controllers/Laundry/LaundryOrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Laundry\LaundryOrderController.php:268) `booking_id` only validated as UUID when charging to booking.
- [app/Http/Controllers/Laundry/LaundryOrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Laundry\LaundryOrderController.php:305) creates `BookingCharge` using provided `booking_id` without ownership/state check.
- [app/Http/Controllers/Restaurant/OrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Restaurant\OrderController.php:177) similar for restaurant settlement.
- [app/Http/Controllers/Restaurant/OrderController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Restaurant\OrderController.php:225) creates charge with provided booking ID.

Impact:
- Charges can be posted to the wrong booking/guest, intentionally or accidentally.

Fix:
- Require `exists:bookings,id` and verify booking is valid for current order context (same guest/room and active state as business rules require).

---

## 6) Procurement receipts stored on public disk (possible sensitive document exposure)
Severity: **Medium**

Evidence:
- [app/Http/Controllers/Procurement/GoodsReceivedNoteController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Procurement\GoodsReceivedNoteController.php:130) stores receipt under `public` disk.
- [resources/views/procurement/grn/show.blade.php](C:\Users\DADY\Desktop\projects\hotel-management-system\resources\views\procurement\grn\show.blade.php:123) exposes direct `Storage::url(...)` link.

Impact:
- GRN receipts (supplier financial documents) can be accessible via direct URL if leaked/guessed.

Fix:
- Store receipts on `private` disk and serve via authorized controller downloads.
- Generate short-lived signed URLs if direct links are required.

---

## 7) Default seeded credentials committed in code and seeded by default
Severity: **Medium**

Evidence:
- [database/seeders/UserSeeder.php](C:\Users\DADY\Desktop\projects\hotel-management-system\database\seeders\UserSeeder.php:21), [database/seeders/UserSeeder.php](C:\Users\DADY\Desktop\projects\hotel-management-system\database\seeders\UserSeeder.php:29), [database/seeders/UserSeeder.php](C:\Users\DADY\Desktop\projects\hotel-management-system\database\seeders\UserSeeder.php:37), [database/seeders/UserSeeder.php](C:\Users\DADY\Desktop\projects\hotel-management-system\database\seeders\UserSeeder.php:45) use static `password`.
- [database/seeders/DatabaseSeeder.php](C:\Users\DADY\Desktop\projects\hotel-management-system\database\seeders\DatabaseSeeder.php:10) always calls `UserSeeder`.

Impact:
- If seeding is run in non-dev environments, predictable credentials can enable account takeover.

Fix:
- Gate demo users behind environment check (`local/testing` only).
- Generate random passwords in seeders and print once to console for local use.

---

## 8) Relationship integrity missing in laundry service item update/remove
Severity: **Medium**

Evidence:
- [app/Http/Controllers/Laundry/LaundryServiceController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Laundry\LaundryServiceController.php:43) `updateItem(Request $request, LaundryService $service, LaundryServiceItem $item)`.
- [app/Http/Controllers/Laundry/LaundryServiceController.php](C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\Laundry\LaundryServiceController.php:55) `removeItem(...)`.
- No check that `$item->laundry_service_id === $service->id`.

Impact:
- Wrong service items can be modified/deactivated through crafted route parameters.

Fix:
- Enforce parent-child ownership check or use scoped bindings.

---

## 9) Dependency vulnerabilities in Node ecosystem
Severity: **High/Moderate** (depends on package)

Source: `npm audit --json`

High:
- `axios` vulnerable to DoS via `__proto__` mergeConfig (`GHSA-43fc-jf86-j433`)  
  Affected: `<=1.13.4`
- `picomatch` method injection + ReDoS (`GHSA-3v7f-55p6-f55p`, `GHSA-c2c7-rcm5-vvqj`)  
  Affected: `<=2.3.1` and `4.0.0 - 4.0.3`
- `rollup` path traversal arbitrary file write (`GHSA-mw96-cpmx-2vgc`)  
  Affected: `4.0.0 - 4.58.0`

Moderate:
- `esbuild` dev-server request exposure (`GHSA-67mh-4wv8-2f99`) via `vite`.

Fix:
- Run targeted upgrades (`npm audit fix` then pin/upgrade direct deps).
- Validate major bump path for `vite` (audit suggests `vite@8.0.3`).

---

## 10) Committed temporary media artifacts in repository
Severity: **Low**

Evidence:
- Tracked files:
  - `storage/media-library/temp/Dty8p0EJOSGKM66VSMeOYIsAPeYScvYI/Un7tIDXFEH2PpPTzW3MzmAHEINX5J3Kithumb.png`
  - `storage/media-library/temp/Dty8p0EJOSGKM66VSMeOYIsAPeYScvYI/X94DFygS1iuLdGfGSAzstV3ywq0JqTiP.png`

Impact:
- Potential accidental data disclosure and repository hygiene drift.

Fix:
- Remove tracked temp files and ignore temp subtree in `.gitignore`.

---

## Tomorrow’s Fix Plan (Practical Order)
1. Lock down route authorization for laundry and restaurant endpoints.
2. Fix parent-child binding checks (`order/orderItem`, `service/item`, charge ownership checks).
3. Fix payment/charge integrity checks (`charge_id` scoped to booking, booking validation in settlements).
4. Move GRN receipts to private storage + authorized download endpoint.
5. Restrict demo/default credentials to local/testing only.
6. Patch npm vulnerabilities and re-run audit.

---

## Audit Command Outputs
- `npm audit --json`: **5 vulnerabilities** (3 high, 2 moderate).
- `composer audit --format=json`: **0 advisories**, **0 abandoned**.

