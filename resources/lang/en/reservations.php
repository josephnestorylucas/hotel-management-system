<?php

return [
    // Page titles
    'title' => 'Reservations',
    'reservations' => 'Reservations',
    'new_reservation' => 'New Reservation',
    'create_reservation' => 'Create Reservation',
    'edit_reservation' => 'Edit Reservation',
    'delete_reservation' => 'Delete Reservation',
    'update_reservation' => 'Update Reservation',

    // Subtitles
    'manage_subtitle' => 'Manage guest reservations',
    'create_subtitle' => 'Add a new guest reservation',

    // Status labels
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'converted' => 'Converted',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
    ],

    // Table headers
    'table' => [
        'reservation' => 'Reservation',
        'guest' => 'Guest',
        'room' => 'Room',
        'check_in' => 'Check-In',
        'check_out' => 'Check-Out',
        'amount' => 'Amount',
        'status' => 'Status',
        'actions' => 'Actions',
    ],

    // Fields
    'fields' => [
        'guest_id' => 'Guest',
        'room_id' => 'Room',
        'check_in_date' => 'Check-In Date',
        'check_out_date' => 'Check-Out Date',
        'number_of_guests' => 'Number of Guests',
        'estimated_amount' => 'Estimated Amount ($)',
        'status' => 'Reservation Status',
        'guest_first_name' => 'First Name',
        'guest_last_name' => 'Last Name',
        'phone_number' => 'Phone Number',
        'email' => 'Email Address',
        'id_type' => 'ID Type',
        'id_number' => 'ID/Passport Number',
        'nationality' => 'Nationality',
        'id_photo' => 'ID Photo',
        'assigned_room' => 'Assigned Room',
    ],

    // ID Types
    'id_types' => [
        'passport' => 'Passport',
        'driver_license' => 'Driver License',
        'nida' => 'NIDA Number',
    ],

    // Sections
    'sections' => [
        'guest_information' => 'Guest Information',
        'reservation_details' => 'Reservation Details',
        'reservation_information' => 'Reservation Information',
    ],

    // Guest type options
    'guest_type' => [
        'existing' => 'Select Existing Guest',
        'new' => 'Create New Guest',
    ],

    // Change guest options
    'change_guest' => [
        'keep' => 'Keep Current Guest',
        'select' => 'Change Guest',
        'new' => 'Create New Guest',
    ],

    // Placeholders
    'placeholders' => [
        'select_guest' => '-- Select a guest --',
        'select_room' => 'Select a room',
        'select_id_type' => '-- Select ID type --',
        'select_nationality' => '-- Select nationality --',
        'not_assigned' => 'Not Assigned',
        'enter_first_name' => 'Enter first name',
        'enter_last_name' => 'Enter last name',
        'phone' => '+1 (555) 123-4567',
        'phone_number' => '712 345 678',
        'email' => 'guest@example.com',
        'enter_id_number' => 'Enter ID number',
        'passport_number' => 'Enter passport number',
        'license_number' => 'Enter driver license number',
        'nida_number' => 'Enter NIDA number',
        'nationality' => 'e.g., American, British',
        'amount' => '0.00',
    ],

    // Nationality Groups
    'nationality_groups' => [
        'east_africa' => 'East Africa',
        'southern_africa' => 'Southern Africa',
        'west_africa' => 'West Africa',
        'north_africa' => 'North Africa',
        'europe' => 'Europe',
        'americas' => 'Americas',
        'asia_oceania' => 'Asia & Oceania',
        'middle_east' => 'Middle East',
        'other' => 'Other',
    ],

    // Labels
    'labels' => [
        'nights' => 'nights',
        'night' => 'night',
        'per_night' => '/night',
        'no_email' => 'No email',
        'view_profile' => 'View Profile',
        'not_assigned' => 'Not Assigned',
        'legacy_reservation' => 'Legacy Reservation',
        'guest' => 'Guest',
        'estimated_total' => 'Estimated Total',
        'upload_id_photo' => 'Upload ID Photo',
        'change_photo' => 'Change Photo',
    ],

    // Actions
    'actions' => [
        'edit' => 'Edit',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
        'check_in' => 'Check In',
        'delete' => 'Delete',
        'create' => 'Create Reservation',
        'update' => 'Update Reservation',
        'delete_reservation' => 'Delete Reservation',
    ],

    // Messages
    'messages' => [
        'no_reservations' => 'No reservations yet',
        'no_reservations_subtitle' => 'Get started by creating your first reservation.',
        'confirm_cancel' => 'Are you sure you want to cancel this reservation?',
        'confirm_delete' => 'Are you sure you want to delete this reservation?',
        'new_guest_created' => 'A new guest record will be created automatically. You can add additional details (ID documents, address) by editing the guest profile later.',
        'loading_rooms' => 'Searching for available rooms...',
        'no_available_rooms' => 'No rooms available for selected dates.',
    ],

    // Modal
    'modal' => [
        'upload_id_photo' => 'Upload ID Photo',
        'upload_id_photo_desc' => 'Upload a clear photo of the guest\'s identification document',
        'click_to_upload' => 'Click to upload or drag and drop',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
    ],

    // Info
    'info' => [
        'reservation_tips' => 'Reservation Tips:',
        'tip_select_guest' => 'Select an existing guest or create a new one for this reservation',
        'tip_room_assignment' => 'Room assignment is required for all reservations',
        'tip_amount' => 'Total amount is automatically calculated based on room rate and stay duration',
        'tip_auto_number' => 'A unique reservation number will be generated automatically',
        'created_by' => 'Created By',
        'created' => 'Created',
        'last_updated' => 'Last Updated',
        'stay_duration' => 'Stay Duration',
        'cant_find_guest' => "Can't find the guest?",
        'create_new_guest' => 'Create a new guest',
        'or' => 'or',
        'add_full_profile' => 'Add full guest profile',
    ],

    // Form labels
    'form' => [
        'select_dates_hint' => '(Select dates first to see available rooms)',
        'search_rooms_hint' => 'Select check-in and check-out dates, then search for rooms.',
    ],
];
