You are a Laravel backend engineer.

========================================
OBJECTIVE
========================================
Auto-create client on public booking.

========================================
STEP 1: FIND PUBLIC BOOKING FLOW
========================================

Search:
- public booking route

and  make  sure  it   follows  the  way  the  booking  or  reservation is  done  by the  frontdesk  so  as when onboarding  the  frontdesk  add  the   id  card  and  the  id  number  
========================================
STEP 2: CREATE CLIENT
========================================

- name
- phone
- email

========================================
STEP 3: LINK
========================================

- attach to booking

========================================
STEP 4: FRONTDESK WHEN  ONBOARDING  OR VERIFYING  THE ENTRY  MADE 
========================================

- add ID card on verification

========================================
OUTPUT
========================================

- Implementation

You are a full-stack Laravel 11 developer working on a hotel management system.
========================================
OBJECTIVE
Implement automatic guest client creation when a public booking is submitted, and require the frontdesk to attach the guest's ID card during booking verification.
========================================
STEP 1: TRACE PUBLIC BOOKING FLOW
Trace the full public booking flow:

Locate the public booking form and its controller (likely a PublicBookingController or similar in the bookings module)
Identify the point where the booking is saved to the database
Check if any guest/client record is being created at that point — if not, that is the missing implementation
Check the Guest model and Booking model relationship — confirm guest_id foreign key exists on bookings table or identify how guests are linked to bookings

========================================
STEP 2: IMPLEMENT AUTO GUEST CREATION
When a public booking is submitted implement the following:
Controller layer:

After the booking form is validated and saved, automatically create a Guest record using the details submitted in the public booking form (name, email, phone, etc.)
Link the newly created guest to the booking via guest_id or equivalent foreign key
If the guest already exists (matched by email or phone), reuse the existing guest record instead of creating a duplicate
Wrap the booking + guest creation in a database transaction so both succeed or both roll back

Model layer:

Verify the Guest model has all required fillable fields
Verify the Booking model has a belongsTo(Guest::class) relationship defined
If any migration is missing for linking guest to public booking, create it

========================================
STEP 3: BOOKING MANAGER VERIFICATION — ID CARD ATTACHMENT
When the booking manager opens a booking for verification implement the following:
Migration:

Add an id_card_path column (string, nullable) to the bookings table if it does not already exist
Run the migration

Controller layer:

In the booking verification method inside BookingController, add logic to handle ID card file upload
Validate the uploaded file: must be an image or PDF, max 2MB
Store the file in storage/app/public/id_cards/ using Laravel's storage system
Save the file path to id_card_path on the booking record
The booking verification should not be completable unless an ID card has been uploaded — enforce this validation

Route layer:

Ensure the verification route is protected and only accessible to roles with verification permissions (e.g. ADMIN, SUPERVISOR, FRONT_DESK)

========================================
STEP 4: UI
Public booking form (resources/views/bookings/public or equivalent):

Ensure the public form collects: full name, email, phone number, and any other fields needed to auto-create a guest record
Show a confirmation message after successful submission

Booking manager verification view (resources/views/bookings/):

Add an ID card upload field to the verification form
Show a preview of the uploaded ID card if one already exists
Mark the ID card field as required before the manager can mark the booking as verified
Display the guest details that were auto-created alongside the booking for the manager to confirm

========================================
STEP 5: VALIDATION & EDGE CASES
Handle the following edge cases:

Public booking submitted with an email/phone that already belongs to an existing guest — reuse that guest, do not duplicate
Manager tries to verify booking without uploading ID card — block and show validation error
File upload fails — rollback and show error message
Public booking submitted with incomplete guest details — validate all required guest fields before saving

========================================
OUTPUT
Provide:

Implementation plan: list of all files to be created or modified
Full code for every affected file (migration, model, controller, view, routes)
Clear explanation of each change and why it was made