You are a senior Laravel developer working داخل an existing Laravel + Blade system.

Your task is to implement a system-wide currency configuration and update Admin Settings functionality.

========================================
CRITICAL RULE (CONSISTENCY)
========================================
- Currency must be consistent across the ENTIRE system
- No hardcoded "$" or "TZS" anywhere
- All monetary values must use a centralized configuration

========================================
PROBLEM 1: HOURLY RATE CURRENCY CONTROL
========================================

Current Issue:
- Hourly rates are fixed in USD ($)
- No flexibility to switch currency

Required Solution:
1. Introduce a global currency setting:
   - Options:
     - USD
     - TZS

2. Store this setting in:
   - Database (preferred: settings table)
   OR
   - Config file if already used

3. Update ALL system areas:
   - Room pricing
   - Bookings
   - Billing
   - Any price display

4. Ensure:
   - Currency symbol updates dynamically
   - Values display correctly with selected currency
   - No mixed currencies across the system

5. Add helper logic:
   - Example:
     - getCurrencySymbol()
     - formatCurrency($amount)

========================================
PROBLEM 2: ADMIN SETTINGS (CURRENCY CONTROL)
========================================

Tasks:
1. Locate Admin Settings page
2. Add:
   - Currency selector (dropdown: USD / TZS)
3. Save selection to database
4. Ensure changes reflect immediately system-wide

========================================
PROBLEM 3: REMOVE DELETE ACCOUNT OPTION
========================================

Tasks:
1. Locate Admin Settings view
2. Remove:
   - "Delete Account" option/button
3. Ensure:
   - No backend route allows admin self-deletion

========================================
PROBLEM 4: ADD CHANGE PASSWORD FEATURE
========================================

Tasks:
1. In Admin Settings:
   - Add "Change Password" form

2. Fields:
   - Current password
   - New password
   - Confirm password

3. Backend:
   - Validate current password
   - Hash new password using Laravel Hash
   - Update securely

4. Ensure:
   - Proper validation messages
   - Secure handling

========================================
FILES TO MODIFY
========================================

- Settings controller
- Admin settings Blade view
- Any helper/util files (for currency formatting)
- Models (if settings stored in DB)
- Routes (for settings + password update)

========================================
CONSTRAINTS
========================================

- DO NOT hardcode currency anywhere
- DO NOT break existing pricing logic
- Reuse existing Blade layouts and styles
- Keep UI consistent with current design
- Use Laravel best practices

========================================
OUTPUT FORMAT
========================================

1. Files modified
2. Currency system implementation explanation
3. Admin settings changes
4. Password change implementation
5. Code diffs (important parts)

========================================
GOAL
========================================

- Admin can switch between USD and TZS
- Entire system reflects selected currency
- No delete account option in admin settings
- Admin can securely change password