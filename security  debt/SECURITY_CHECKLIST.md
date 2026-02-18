# Security Remediation Checklist

## Critical Issues - Fix Immediately ⚠️

- [ ] **Rate Limiting on Authentication**
  - File: `routes/web.php`
  - Add: `->middleware('throttle:5,1')` to login route
  - Add: `->middleware('throttle:3,60')` to register route
  - Add: `->middleware('throttle:3,60')` to forgot-password route

- [ ] **Disable Public Registration**
  - File: `app/Http/Controllers/Auth/RegisterController.php`
  - Change: `'is_active' => false` (line 32)
  - Remove: `Auth::login($user);` (line 35)
  - Change redirect message to require approval

- [ ] **Fix Webhook Security**
  - File: `app/Services/Payment/SnippeProvider.php`
  - Implement proper HMAC signature verification in `validateWebhook()`
  - Use: `hash_equals()` for timing-safe comparison

- [ ] **Add Booking Authorization**
  - File: `app/Http/Controllers/BookingController.php`
  - Add authorization check in: `show()`, `edit()`, `update()`, `destroy()`
  - Check: User is admin/supervisor OR booking creator

- [ ] **Add Payment Authorization**
  - File: `app/Http/Controllers/PaymentController.php`
  - Add authorization check in: `refund()`, `show()`
  - Check: User is admin OR booking owner

- [ ] **Fix SQL Injection in Search**
  - File: `app/Http/Controllers/BookingController.php` (lines 177-185)
  - File: `app/Http/Controllers/GuestController.php` (lines 19-28)
  - Escape wildcards: `str_replace(['%', '_'], ['\%', '\_'], $search)`

- [ ] **Fix Race Condition in Booking**
  - File: `app/Http/Controllers/BookingController.php`
  - Wrap booking creation in `DB::transaction()` with `lockForUpdate()`

## High Priority - Fix This Week 🔥

- [ ] **Update Vulnerable Dependencies**
  ```bash
  npm update axios@latest  # Update to 1.13.5+
  ```

- [ ] **Fix Mass Assignment in User Model**
  - File: `app/Models/User.php`
  - Remove `role_id` and `is_active` from `$fillable`
  - Add to `$guarded` instead

- [ ] **Improve Role Middleware**
  - File: `app/Http/Middleware/RoleMiddleware.php`
  - Add null check for user role
  - Add logging for unauthorized attempts

- [ ] **Strengthen Session Security**
  - File: `config/session.php`
  - Set: `'encrypt' => true`
  - Set: `'lifetime' => 30` (30 minutes)
  - Set: `'expire_on_close' => true`

- [ ] **Add Security Logging**
  - Add logging to: `LoginController`, `UserController`, `PaymentController`
  - Log: Failed logins, role changes, payment operations, authorization failures

- [ ] **Improve Password Policy**
  - File: `app/Http/Controllers/UserController.php`
  - Use: `Password::min(10)->letters()->mixedCase()->numbers()->symbols()`

- [ ] **Add Rate Limiting to Public Booking**
  - File: `routes/web.php`
  - Add: `->middleware('throttle:10,60')` to booking store route

- [ ] **Fix IDOR in Reservations**
  - File: `app/Http/Controllers/ReservationController.php`
  - Add authorization checks in: `update()`, `edit()`, `destroy()`

## Medium Priority - Fix This Month 📋

- [ ] **Add Security Headers Middleware**
  - Create: `app/Http/Middleware/SecurityHeaders.php`
  - Add headers: CSP, X-Frame-Options, X-Content-Type-Options, HSTS

- [ ] **Improve File Upload Security**
  - File: `app/Http/Controllers/GuestController.php`
  - Reduce max file size to 1MB for photos
  - Add file content validation
  - Implement virus scanning if possible

- [ ] **Add Account Lockout**
  - File: `app/Http/Controllers/Auth/LoginController.php`
  - Lock account after 10 failed attempts
  - Use Cache to track attempts

- [ ] **Improve Password Reset**
  - File: `app/Http/Controllers/ForgotPasswordController.php`
  - Always return same message (prevent enumeration)
  - Add rate limiting

- [ ] **Add Phone Number Validation**
  - File: `app/Http/Controllers/PaymentController.php`
  - Add regex validation for international format

- [ ] **Enable Email Verification**
  - Implement Laravel's email verification
  - Require verification before account access

- [ ] **Add Pagination Limits**
  - File: Multiple controllers
  - Cap maximum items per page to 100

- [ ] **Disable Debug Mode**
  - File: `.env.example`
  - Change: `APP_DEBUG=false`
  - Change: `APP_ENV=production`

- [ ] **Add CSRF Token Check**
  - Verify all forms include `@csrf` directive
  - Ensure webhook exclusion is documented

## Code Review Checklist ✓

- [ ] All user inputs validated and sanitized
- [ ] All database queries use parameter binding
- [ ] All authorization checks in place
- [ ] All sensitive operations logged
- [ ] No secrets in code (use .env)
- [ ] CSRF protection on all forms
- [ ] Rate limiting on public endpoints
- [ ] File uploads properly validated
- [ ] Sessions properly secured
- [ ] Error messages don't leak info

## Testing Checklist 🧪

- [ ] Test rate limiting with Burp Suite
- [ ] Test IDOR vulnerabilities (access other users' data)
- [ ] Test SQL injection in search fields
- [ ] Test file upload with malicious files
- [ ] Test concurrent booking of same room
- [ ] Test webhook with fake signatures
- [ ] Test password reset enumeration
- [ ] Test privilege escalation attempts
- [ ] Test XSS in all input fields
- [ ] Test session hijacking

## Deployment Checklist 🚀

Before production deployment:

- [ ] All Critical issues fixed and tested
- [ ] All High issues fixed and tested
- [ ] Dependencies updated to latest secure versions
- [ ] Debug mode disabled
- [ ] HTTPS enforced
- [ ] Security headers configured
- [ ] Database backups configured
- [ ] Monitoring and alerting set up
- [ ] Incident response plan documented
- [ ] Security audit passed

## Resources

- OWASP Top 10: https://owasp.org/www-project-top-ten/
- Laravel Security Best Practices: https://laravel.com/docs/security
- PCI-DSS Requirements: https://www.pcisecuritystandards.org/

---

**Last Updated:** 2024
**Status:** ⛔ NOT PRODUCTION READY
