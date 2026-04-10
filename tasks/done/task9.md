You are a Laravel UI/backend engineer.

========================================
OBJECTIVE
========================================
Complete guest viewing for frontdesk.

========================================
STEP 1: LIST VIEW
========================================

- show guests

========================================
STEP 2: FILTER
========================================

- name
- phone

========================================
STEP 3: DETAILS
========================================

- history
- bookings

========================================
STEP 4: ACCESS CONTROL
========================================

- frontdesk only

========================================
OUTPUT
========================================

- views + logic

You are a full-stack Laravel 11 developer working on a hotel management system.
========================================
OBJECTIVE
Complete and finish the guest viewing functionality for the Front Desk role so they can fully view all guest information, current bookings, service charges, and history.
========================================
STEP 1: TRACE CURRENT STATE
Trace what already exists:

Locate GuestController and identify which methods are already implemented (index, show, create, edit, etc.)
Check routes in /guests/* — identify which routes exist and which are missing or incomplete
Check resources/views/guests/ — identify which views exist and which are empty, missing, or broken
Check role middleware on guest routes — confirm FRONT_DESK has access to view-related routes
Identify exactly what is missing or incomplete in the current guest viewing implementation

========================================
STEP 2: VERIFY DATA RELATIONSHIPS
Ensure all necessary data is being loaded when viewing a guest:

Guest → Booking (current and past bookings)
Guest → BookingCharge (all service charges: laundry, restaurant, conference etc.)
Guest → LaundryOrder (laundry history)
Guest → loyalty points/transactions if loyalty model exists
In GuestController@show verify eager loading is correctly loading all relationships with with([]) to avoid N+1 queries
Check if booking_id and guest_id foreign keys are correctly used across all related models

========================================
STEP 3: IMPLEMENT MISSING FUNCTIONALITY
Complete the following in GuestController:
index method:

Return paginated list of all guests
Support search by name, email, phone number
Show guest status (currently checked in, checked out, upcoming booking)

show method:

Load full guest profile including personal details, photo, documents
Load current active booking with room details
Load all past bookings with dates and status
Load all service charges broken down by type (room, laundry, restaurant, conference)
Load outstanding balance (total unpaid charges)
Load loyalty points balance if loyalty system exists

Missing methods (implement if not present):

Any method needed to support the full guest view that is currently missing

Route layer:

Ensure all guest viewing routes are defined and protected with correct role middleware allowing FRONT_DESK access
No create/edit/delete routes should be exposed to FRONT_DESK — view only

========================================
STEP 4: UI
Complete the following views in resources/views/guests/:
index.blade.php:

Guest list table with columns: name, email, phone, current room, booking status, check-in date, check-out date
Search bar to filter guests by name, email, or phone
Pagination
Clickable row or view button linking to guest detail page
Role-restricted: hide any create/edit/delete buttons from FRONT_DESK

show.blade.php:

Guest profile card: photo, full name, email, phone, ID card preview if uploaded
Current booking section: room number, floor, building, check-in/check-out dates, booking status
Service charges breakdown table: itemized list of all charges (room rate, laundry, restaurant, conference) with amounts and dates
Outstanding balance prominently displayed
Booking history table: list of all past stays with dates and total billed
Laundry order history table if applicable
Loyalty points balance section if applicable
All sections cleanly styled with Tailwind CSS consistent with the rest of the system UI

========================================
STEP 5: EDGE CASES
Handle the following:

Guest has no active booking — show "No active booking" gracefully
Guest has zero service charges — show empty state message
Guest photo or ID card not uploaded — show a placeholder avatar
Search returns no results — show empty state message
FRONT_DESK tries to access edit or delete route — return 403 forbidden

========================================
OUTPUT
Provide:

List of all files to be created or modified
Full code for every affected file (controller, views, routes)
Clear explanation of what was missing, what was added, and why