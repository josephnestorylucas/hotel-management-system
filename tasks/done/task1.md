You are a senior Laravel + Blade + JavaScript engineer working inside an existing system.

Your task is to FIX the dashboard time so it updates LIVE without requiring a page refresh.

========================================
CURRENT ISSUE (CRITICAL)
========================================
- Time is rendered once from the backend (PHP/Blade)
- It remains static until manual page refresh
- Previous fix attempts did NOT work

========================================
STRICT REQUIREMENTS (MUST FOLLOW)
========================================

1. REMOVE STATIC TIME DEPENDENCY
- Find where time is rendered using PHP (e.g., now(), date())
- Replace it with a dynamic frontend-driven solution

2. IMPLEMENT GUARANTEED LIVE UPDATE
- Use pure JavaScript (NO AJAX)
- Use:
  - setInterval()
  - new Date()

3. ENSURE EXECUTION WORKS
- Script MUST:
  - Run after DOM is fully loaded
  - NOT depend on missing elements
  - NOT be blocked by Blade sections

Use:
document.addEventListener('DOMContentLoaded', function () {
    // logic here
});

4. TARGET ELEMENT (MANDATORY)
- Ensure time element exists:
  Example:
    <span id="liveTime"></span>

- If missing → create it in the correct Blade layout

5. UPDATE EVERY SECOND
- Use ONE setInterval only
- Prevent duplicate intervals

6. FORMAT TIME PROPERLY
- Format:
  - 12-hour format (HH:MM:SS AM/PM)
- Example:
  12:07:45 AM

7. APPLY GLOBALLY
- Implement inside:
  - layouts/app.blade.php (or shared layout)

- So ALL dashboards inherit it automatically

========================================
ANTI-PATTERNS (DO NOT DO)
========================================

- DO NOT use AJAX polling
- DO NOT fetch time from backend repeatedly
- DO NOT leave PHP time rendering in place
- DO NOT duplicate scripts across multiple views

========================================
VALIDATION CHECK (VERY IMPORTANT)
========================================

After implementation:
- Time must:
  ✅ Change every second
  ✅ Start immediately on page load
  ✅ Work without refreshing
  ✅ Work on all dashboards

========================================
FILES TO MODIFY
========================================

- layouts/app.blade.php (primary)
- Any shared header/dashboard partial

========================================
OUTPUT FORMAT
========================================

1. File(s) modified
2. Before vs After (time rendering)
3. Final JavaScript code
4. Explanation of why previous implementation failed

========================================
GOAL
========================================

- Fully dynamic live time
- No refresh required EVER
- Clean, centralized implementation