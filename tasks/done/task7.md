You are a Laravel RBAC engineer.

========================================
OBJECTIVE
========================================
Restrict laundry discount to Manager only.

========================================
STEP 1: FIND DISCOUNT LOGIC
========================================

Search:
- "discount"

========================================
STEP 2: BACKEND CHECK
========================================

Allow only:
- Manager role

========================================
STEP 3: UI
========================================

- Hide from frontdesk

========================================
OUTPUT
========================================

- Role restriction

You are a debugging expert working on a Laravel 11 hotel management system.
========================================
OBJECTIVE
Remove laundry discount capability from the Front Desk role and restrict it to the Laundry Manager role only.
========================================
STEP 1: TRACE PERMISSION
Trace the full permission chain:

Locate where laundry discount is applied in LaundryOrderController or LaundryServiceController
Identify the method/action that handles discount application on laundry orders
Check if there is any role check currently guarding the discount feature — if none exists that is the bug
Check the 10 defined roles: FRONT_DESK must be blocked, MANAGER must be allowed

========================================
STEP 2: VERIFY CURRENT ACCESS
Check the following:

Routes in /laundry/* — identify the route handling discount application and check its middleware
Check if can(), Gate::allows(), $this->authorize(), or any middleware is currently used to guard the discount action
Check the Blade views in resources/views/laundry/ — identify where the discount input field or button is rendered and if it is conditionally shown based on role

========================================
STEP 3: FIX AUTHORIZATION
Apply the fix across all layers:
Controller layer:

In the discount method inside LaundryOrderController, add role authorization so only LAUNDRY_MANAGER can apply discounts
Return a 403 forbidden response if FRONT_DESK or any unauthorized role attempts to access it

Route layer:

Add or update middleware on the discount route inside /laundry/* to restrict access to LAUNDRY_MANAGER only

Policy/Gate layer:

If a LaundryPolicy or Gate definition exists, update it to explicitly deny FRONT_DESK and allow LAUNDRY_MANAGER
If no policy exists, create the authorization check directly in the controller

========================================
STEP 4: UI
In resources/views/laundry/:

Hide the discount input field, button, or form section from FRONT_DESK users using Blade role checks
Ensure LAUNDRY_MANAGER still sees and can use the discount UI normally
Optionally show a message to FRONT_DESK like "Discounts can only be applied by the Laundry Manager"

========================================
OUTPUT
Provide:

Root cause: exact file, method, and line where the permission gap exists
Fix: corrected code for every affected file (controller, route, view) with clear explanation of what was changed and why