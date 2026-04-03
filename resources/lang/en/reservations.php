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
    'manage_subtitle' => 'Manage guest bookings and check-ins',
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
        'id_number' => 'ID/Passport Number',
        'nationality' => 'Nationality',
        'guest_photo' => 'Guest Photo',
        'assigned_room' => 'Assigned Room',
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
        'select_room' => 'Select a room (optional)',
        'not_assigned' => 'Not Assigned',
        'enter_first_name' => 'Enter first name',
        'enter_last_name' => 'Enter last name',
        'phone' => '+1 (555) 123-4567',
        'email' => 'guest@example.com',
        'enter_id_number' => 'Enter ID number',
        'nationality' => 'e.g., American, British',
        'amount' => '0.00',
    ],

    // Labels
    'labels' => [
        'nights' => 'nights',
        'per_night' => '/night',
        'no_email' => 'No email',
        'view_profile' => 'View Profile',
        'not_assigned' => 'Not Assigned',
        'legacy_reservation' => 'Legacy Reservation',
        'guest' => 'Guest',
        'photo_optional' => 'Optional, max 2MB, JPG/PNG',
        'room_optional' => 'Optional - can be assigned later',
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
    ],

    // Info
    'info' => [
        'reservation_tips' => 'Reservation Tips:',
        'tip_select_guest' => 'Select an existing guest or create a new one for this reservation',
        'tip_room_assignment' => 'Room can be assigned now or later based on availability',
        'tip_amount' => 'Total amount can be calculated based on room rate and stay duration',
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
];
