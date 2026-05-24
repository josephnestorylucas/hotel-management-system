<?php

return [
    'title' => 'Bookings',
    'booking' => 'Booking',
    'bookings' => 'Bookings',
    'subtitle' => 'Manage guest bookings, check-ins and check-outs',
    
    // Actions
    'new_booking' => 'New Booking',
    'create_booking' => 'Create Booking',
    'edit_booking' => 'Edit Booking',
    'view_booking' => 'View Booking',
    'cancel_booking' => 'Cancel Booking',
    'confirm_booking' => 'Confirm Booking',
    'quick_actions' => 'Quick Actions',
    'new_reservation' => 'New Reservation',
    'all_reservations' => 'All Reservations',
    'all_bookings' => 'All Bookings',
    'todays_checkins' => "Today's Check-ins",
    'todays_checkouts' => "Today's Check-outs",
    'hall_bookings' => 'Hall Bookings',
    'conferences' => 'Conferences',
    'current_guests' => 'Current Guests',
    'check_in' => 'Check In',
    'check_out' => 'Check Out',
    'cancel' => 'Cancel',
    'filter' => 'Filter',
    'reset' => 'Reset',
    
    // Stats
    'stats' => [
        'total' => 'Total',
        'checked_in' => 'Checked In',
        'checked_out' => 'Checked Out',
        'today_checkins' => 'Today Check-ins',
        'today_checkouts' => 'Today Check-outs',
    ],
    
    // Filters
    'filters' => [
        'search_placeholder' => 'Search name, email, booking #...',
        'all_statuses' => 'All Statuses',
        'all_sources' => 'All Sources',
        'from_date' => 'From date',
    ],
    
    // Sources
    'sources' => [
        'online' => 'Online',
        'frontdesk' => 'Front Desk',
        'phone' => 'Phone',
        'walkin' => 'Walk-in',
    ],
    
    // Table
    'table' => [
        'booking' => 'Booking',
        'guest' => 'Guest',
        'room' => 'Room',
        'check_in' => 'Check-In',
        'check_out' => 'Check-Out',
        'amount' => 'Amount',
        'source' => 'Source',
        'status' => 'Status',
        'actions' => 'Actions',
        'not_assigned' => 'Not Assigned',
        'nights' => 'nights',
    ],
    
    // Messages
    'messages' => [
        'no_bookings_found' => 'No bookings found',
        'no_bookings_subtitle' => 'Get started by creating your first booking.',
        'confirm_cancel' => 'Are you sure you want to cancel this booking?',
        'created' => 'Booking created successfully.',
        'updated' => 'Booking updated successfully.',
        'cancelled' => 'Booking cancelled successfully.',
        'checked_in' => 'Guest checked in successfully.',
        'checked_out' => 'Guest checked out successfully.',
        'no_available_rooms' => 'No rooms available for selected dates.',
        'room_not_available' => 'This room is not available for the selected dates.',
        'loading_rooms' => 'Searching for available rooms...',
    ],
    
    // Fields
    'fields' => [
        'guest' => 'Guest',
        'room' => 'Room',
        'room_type' => 'Room Type',
        'check_in_date' => 'Check In Date',
        'check_out_date' => 'Check Out Date',
        'nights' => 'Nights',
        'adults' => 'Adults',
        'children' => 'Children',
        'rate' => 'Rate',
        'total_amount' => 'Total Amount',
        'paid_amount' => 'Paid Amount',
        'balance' => 'Balance',
        'payment_status' => 'Payment Status',
        'booking_status' => 'Booking Status',
        'source' => 'Booking Source',
        'special_requests' => 'Special Requests',
    ],
    
    // Status
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'checked_in' => 'Checked In',
        'checked_out' => 'Checked Out',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
    ],
    
    // Payment status
    'payment' => [
        'unpaid' => 'Unpaid',
        'partial' => 'Partial',
        'paid' => 'Paid',
        'refunded' => 'Refunded',
    ],
    
    // Sections
    'sections' => [
        'guest_info' => 'Guest Information',
        'booking_details' => 'Booking Details',
        'room_details' => 'Room Details',
        'payment_info' => 'Payment Information',
        'charges' => 'Charges',
    ],

    // Form labels
    'form' => [
        'select_existing_guest' => 'Select Existing Guest',
        'create_new_guest' => 'Create New Guest',
        'choose_guest' => '-- Choose a Guest --',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'id_number' => 'ID Number',
        'nationality' => 'Nationality',
        'number_of_guests' => 'Number of Guests',
        'change_guest_optional' => 'Change Guest (Optional)',
        'keep_current_guest' => '-- Keep Current Guest --',
        'special_requests_placeholder' => 'Any special requests or notes...',
        'select_dates_hint' => '(Select dates first to see available rooms)',
    ],

    // Placeholders
    'placeholders' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1 555-000-0000',
        'id_number' => 'ID number',
        'nationality' => 'Nationality',
    ],

    // Page specific
    'walk_in_checkin' => 'Walk-in Check-in',
    'walk_in_description' => 'Check in a walk-in guest directly',
    'stay_details' => 'Stay Details',
    'pricing_notes' => 'Pricing & Notes',
    'per_night' => '/night',
    'search_rooms_hint' => 'Select check-in and check-out dates, then search for rooms.',
    'status_checked_in' => 'Status: Checked In',
    'walkin_checked_in_immediately' => 'Walk-in guests are checked in immediately',
    'check_in_guest' => 'Check In Guest',
    'current' => 'Current',
    'update_booking' => 'Update Booking',
    'edit' => 'Edit',
    'back' => 'Back',
    'reservation_label' => 'Reservation',
    'view_guest_profile' => 'View Guest Profile',
    'public_booking' => 'Public Booking (No Profile)',
    'country' => 'Country',
    'duration' => 'Duration',
    'guests' => 'Guests',
    'room_number' => 'Room Number',
    'floor' => 'Floor',
    'building' => 'Building',
    'rate_per_night' => 'Rate/Night',
    'no_room_assigned' => 'No room assigned',
    'payment_summary' => 'Payment Summary',
    'additional_information' => 'Additional Information',
    'cancellation_reason' => 'Cancellation Reason',
    'created_by' => 'Created By',
    'confirmed_at' => 'Confirmed At',
    'cancelled_at' => 'Cancelled At',
    'created_at_label' => 'Created',
];
