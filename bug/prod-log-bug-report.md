# Production Log Bug Report

Source: laravel.log
Window: 2026-05-27 to 2026-05-29

## Summary

- Total distinct issues: 6
- Total occurrences (errors and warnings): 28

## Issues

### 1) Bug reports database is read-only
- Severity: High
- Evidence: 2026-05-27 13:11:51, 13:11:52
- Error: SQLSTATE[HY000] General error: 8 attempt to write a readonly database
- Context: insert into bug_reports on the bugs SQLite connection

### 2) SMS provider not configured
- Severity: Medium
- Evidence: 2026-05-27 14:01:25; 2026-05-29 19:57:44; 2026-05-29 20:17:07
- Error: SMS not sent: provider not configured
- Impact: SMS notifications fail while email succeeds

### 3) AzamPesa token generation failed (missing credentials)
- Severity: High
- Evidence: 2026-05-28 15:02:16; 2026-05-28 15:02:47
- Error: validation errors: AppName, ClientId, ClientSecret required
- Impact: card and mobile payments cannot start

### 4) Missing class App\Http\Controllers\Store\Str
- Severity: High
- Evidence: 2026-05-28 15:19:08; 2026-05-28 15:21:20
- Error: Class "App\\Http\\Controllers\\Store\\Str" not found
- Context: ProductController.php:64

### 5) Missing view procurement.lpo.edit
- Severity: High
- Evidence: 2026-05-28 19:42:04
- Error: View [procurement.lpo.edit] not found
- Context: LocalPurchaseOrderController.php:115

### 6) Unauthorized role access attempts
- Severity: Low
- Evidence: 18 warnings across 2026-05-27 to 2026-05-29
- Error: Unauthorized role access attempt
- Affected paths:
  - finance/walkin-payment/process (9)
  - restaurant/bar/tabs (3)
  - restaurant/bar/queue (1)
  - reservations (1)
  - rooms (2)
  - finance/checkout/* (2)
- Note: Could be misuse or misconfigured role permissions
