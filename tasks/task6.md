You are a debugging expert working on a Laravel 11 hotel management system.
========================================
OBJECTIVE
Fix missing laundry pricing in guest booking checkout. Laundry charges are not appearing in the consolidated bill and are not included in the total amount the guest needs to clear at checkout.
========================================
STEP 1: TRACE RELATIONSHIP
Trace the full data relationship chain:

booking → booking_charges → laundry_orders
Check the BookingCharge model: verify booking_id, charge_type, reference_id, amount, status fields
Check the LaundryOrder model: verify it correctly references a booking_id when the customer is a hotel guest
Confirm the foreign key relationships and Eloquent relationships are defined correctly across Booking, BookingCharge, and LaundryOrder models

========================================
STEP 2: VERIFY QUERY
Check the following in FinanceCheckoutController:

The query fetching unpaid charges: verify it includes charge_type = 'laundry' and is not accidentally filtering it out
Check all joins and eager loading — confirm laundry charges are loaded with with(['order', 'laundryOrder', 'createdBy'])
In LaundryOrderController verify BookingCharge::create() is called with correct booking_id (not null), correct charge_type => 'laundry', and correct amount
Verify at which laundry workflow state (received → processing → ready → delivered → collected → settled) the BookingCharge is created and confirm that state transition is actually being triggered

========================================
STEP 3: FIX CALCULATION
Ensure:

Laundry charge amount is included in the total bill calculation at checkout
The total sum query correctly aggregates all charge types including laundry, restaurant, and any other service charges
No status mismatch is causing laundry charges to be skipped (e.g. status set to something other than unpaid at creation)

========================================
STEP 4: UI
In resources/views/finance/ checkout view:

Show laundry as a separate line item in the bill breakdown table
Display laundry order reference, description, and amount
Ensure the grand total visibly includes laundry charges alongside all other charges

========================================
OUTPUT
Provide:

Root cause: exact file, method, and line where the bug originates
Fix: corrected code for every affected file with clear explanation of what was wrong and what was changed


Bug to Fix:
When a guest checks out, laundry charges are not appearing in the consolidated bill. The laundry pricing is not being added to the total amount the guest needs to clear at checkout. This is a cross-module billing bug involving the Laundry module, the Booking/Charges system, and the Finance/Checkout module.
What you need to do:

Trace the full laundry charge flow — starting from LaundryOrderController where the order is marked as settled/delivered, through BookingCharge creation, all the way to FinanceCheckoutController where the guest bill is consolidated
Check if BookingCharge::create() is actually being called with charge_type => 'laundry' when a laundry order is linked to a guest booking — verify the booking_id is being correctly resolved and is not null
In FinanceCheckoutController, check the query that fetches all unpaid charges for a booking — verify it is not filtering out or missing charge_type = 'laundry' records
Check the checkout view (resources/views/finance/) — verify laundry charges are being rendered in the bill breakdown table alongside restaurant and other charges
Check if laundry orders for walk-in customers vs hotel guests are being distinguished correctly — guest laundry must have a valid booking_id attached
Verify the status field on BookingCharge for laundry entries — confirm it is being set to 'unpaid' at creation so the checkout query picks it up
Check the laundry order workflow states (received → processing → ready → delivered → collected → settled) — identify at which state the BookingCharge is created and whether that state transition is actually being triggered in the controller
Fix all identified issues across all affected files and ensure the total bill shown at checkout correctly sums all charges including laundry, restaurant, and any other service charges

Expected Result:
When a guest checks out, the bill must show a full itemized breakdown including laundry charges with correct amounts, and the total must include all service charges consolidated into one final amount and  also  look if  the  bar  and resturant  charges  are  showing  up  