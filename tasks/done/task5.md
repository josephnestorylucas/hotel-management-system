You are a senior Laravel + Blade + JavaScript engineer working inside an existing codebase.

Your task is to locate and fix a bug in the notification dropdown/modal behavior.

Problem:
- When clicking the notification icon, the notification modal opens
- But clicking the icon again does NOT close/minimize it
- Expected behavior is a toggle (open/close on same button click)

Instructions:

1. Search the codebase for:
   - Notification icon trigger (likely in layouts/app.blade.php or navbar Blade files)
   - Notification modal/dropdown component (Blade view or partials)
   - Any related JavaScript handling click events

2. Identify the issue:
   - Missing toggle logic (only opening, no closing)
   - Incorrect event listeners
   - State not being tracked (no active/open flag)
   - Conflicts with Bootstrap, Tailwind, or custom JS

3. Implement a fix:
   - Add proper toggle behavior using JavaScript
   - Ensure:
     - Clicking icon toggles visibility (open/close)
     - Clicking outside closes the modal
     - No duplicate event listeners
   - Use clean selectors (IDs or classes)

4. Apply changes ONLY in:
   - Blade layout files (e.g., layouts/app.blade.php)
   - Notification-related Blade views or partials
   - Associated inline or linked JavaScript

5. Do NOT:
   - Introduce new frameworks (no Vue/React)
   - Break existing UI behavior
   - Modify unrelated components

6. Output:
   - Show the files you modified
   - Show before vs after code (diff style preferred)
   - Brief explanation of the fix

Goal:
Make the notification dropdown behave like a proper toggle button with clean, minimal, production-ready code.