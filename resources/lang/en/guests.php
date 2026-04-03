<?php

return [
    // Page titles
    'title' => 'Guests',
    'all_guests' => 'All Guests',
    'add_guest' => 'Add Guest',
    'edit_guest' => 'Edit Guest',
    'guest_details' => 'Guest Details',
    'guest_profile' => 'Guest Profile',
    'new_guest' => 'New Guest',
    'add_new_guest' => 'Add New Guest',
    'create_guest' => 'Create Guest',
    'update_guest' => 'Update Guest',
    'delete_guest' => 'Delete Guest',

    // Subtitles & descriptions
    'subtitle' => 'Manage hotel guests and their information',
    'create_subtitle' => 'Create a new guest profile for hotel reservations',

    // Fields
    'fields' => [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'email_address' => 'Email Address',
        'phone' => 'Phone Number',
        'phone_alt' => 'Alternative Phone',
        'nationality' => 'Nationality',
        'country' => 'Country',
        'city' => 'City',
        'address' => 'Address',
        'postal_code' => 'Postal Code',
        'id_type' => 'ID Type',
        'id_number' => 'ID Number',
        'id_passport_number' => 'ID/Passport Number',
        'passport' => 'Passport',
        'national_id' => 'National ID',
        'driving_license' => 'Driving License',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        'company' => 'Company',
        'company_name' => 'Company Name',
        'occupation' => 'Occupation',
        'special_requests' => 'Special Requests',
        'notes' => 'Notes',
        'vip' => 'VIP Guest',
        'blacklisted' => 'Blacklisted',
        'dob_short' => 'DOB',
    ],

    // Sections
    'sections' => [
        'personal_information' => 'Personal Information',
        'contact_information' => 'Contact Information',
        'identification' => 'Identification',
        'guest_photo' => 'Guest Photo',
        'id_documents' => 'ID Documents',
        'guest_info' => 'Guest Information',
        'reservation_history' => 'Reservation History',
        'personal_details' => 'Personal Details',
    ],

    // Table headers
    'table' => [
        'guest' => 'Guest',
        'contact' => 'Contact',
        'id_number' => 'ID Number',
        'nationality' => 'Nationality',
        'reservations' => 'Reservations',
        'actions' => 'Actions',
        'reservation' => 'Reservation',
        'room' => 'Room',
        'check_in' => 'Check-In',
        'check_out' => 'Check-Out',
        'amount' => 'Amount',
        'status' => 'Status',
    ],

    // Actions
    'actions' => [
        'search' => 'Search',
        'clear' => 'Clear',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'create' => 'Create',
        'update' => 'Update',
        'new_booking' => 'New Booking',
        'create_booking' => 'Create Booking',
        'book_room' => 'Book Room',
        'view_profile' => 'View Profile',
        'back_to_guests' => 'Back to Guests',
    ],

    // Filters
    'filters' => [
        'search_placeholder' => 'Search by name, email, phone, or ID number...',
        'search' => 'Search',
        'clear' => 'Clear',
        'all' => 'All',
        'filter' => 'Filter',
    ],

    // Messages
    'messages' => [
        'no_guests' => 'No guests found',
        'no_guests_subtitle' => 'Create a new guest to get started',
        'no_reservations' => 'No reservations yet',
        'no_reservations_subtitle' => 'Create a new booking for this guest',
        'no_email' => 'No email',
        'no_email_provided' => 'No email provided',
        'not_assigned' => 'Not Assigned',
        'guest_created' => 'Guest created successfully!',
        'guest_updated' => 'Guest updated successfully!',
        'guest_deleted' => 'Guest deleted successfully!',
        'guest_not_found' => 'Guest not found.',
        'search_by_name_email_phone' => 'Search by name, email, or phone...',
        'confirm_delete' => 'Are you sure you want to delete this guest?',
    ],

    // Stats
    'stats' => [
        'total_guests' => 'Total Guests',
        'total_reservations' => 'Total Reservations',
        'total_stays' => 'Total Stays',
        'total_spent' => 'Total Spent',
        'last_visit' => 'Last Visit',
        'first_visit' => 'First Visit',
        'loyalty_points' => 'Loyalty Points',
    ],

    // Forms
    'forms' => [
        'optional' => 'Optional',
        'required' => 'Required',
        'photo_placeholder' => 'Optional, max 2MB, JPG/PNG',
        'photo_replace_placeholder' => 'Optional, max 2MB',
        'documents_placeholder' => 'Optional, max 5MB each, JPG/PNG/PDF, multiple allowed',
        'documents_add_placeholder' => 'Optional, max 5MB each, multiple allowed',
        'first_name_placeholder' => 'Enter first name',
        'last_name_placeholder' => 'Enter last name',
        'nationality_placeholder' => 'e.g., American, British',
        'phone_placeholder' => '+1 (555) 123-4567',
        'email_placeholder' => 'guest@example.com',
        'address_placeholder' => 'Enter full address',
        'id_number_placeholder' => 'Enter ID or passport number',
        'profile_photo' => 'Profile Photo',
        'upload_photo' => 'Upload Photo',
        'replace_photo' => 'Replace Photo',
        'current_photo' => 'Current Photo',
        'remove_photo' => 'Remove current photo',
        'upload_documents' => 'Upload ID Documents',
        'add_documents' => 'Add ID Documents',
        'current_documents' => 'Current Documents',
    ],

    // Info
    'info' => [
        'important_notes' => 'Important Notes:',
        'note_guest_info' => 'Guest information will be used for all future reservations',
        'note_email_unique' => 'Email must be unique',
        'note_multiple_documents' => 'You can upload multiple ID documents (passport, driver\'s license, etc.)',
        'note_supported_formats' => 'Supported formats: JPG, PNG for photos; JPG, PNG, PDF for documents',
        'registered' => 'Registered',
        'last_updated' => 'Last Updated',
        'guest_since' => 'Guest since',
        'nights' => 'nights',
        'reservation_count' => 'total reservations',
    ],

    // Documents
    'documents' => 'Documents',
    'id_document' => 'ID Document',
    'upload_id' => 'Upload ID',
    'view_document' => 'View Document',
    'remove' => 'Remove',

    // Validation
    'email_required' => 'Email address is required.',
    'phone_required' => 'Phone number is required.',
    'name_required' => 'Name is required.',
];
