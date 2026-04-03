<?php

return [
    'title' => 'Rooms',
    'room' => 'Room',
    'rooms' => 'Rooms',
    'new_room' => 'New Room',
    'create_room' => 'Create Room',
    'edit_room' => 'Edit Room',
    'delete_room' => 'Delete Room',
    'update_room' => 'Update Room',
    'add_room' => 'Add Room',
    'view_room' => 'View Room',
    'manage_subtitle' => 'Manage hotel rooms and availability',
    'add_new_subtitle' => 'Add a new room to your inventory',
    'update_subtitle' => 'Update room information',

    // Fields
    'fields' => [
        'room_number' => 'Room Number',
        'room_type' => 'Room Type',
        'floor' => 'Floor',
        'capacity' => 'Capacity',
        'base_rate' => 'Base Rate',
        'status' => 'Status',
        'amenities' => 'Amenities',
        'description' => 'Description',
        'max_occupancy' => 'Max Occupancy',
        'location' => 'Location',
        'type' => 'Type',
        'rate' => 'Rate',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'created' => 'Created',
    ],

    // Table headers
    'table' => [
        'room' => 'Room',
        'location' => 'Location',
        'type' => 'Type',
        'rate' => 'Rate',
        'status' => 'Status',
        'active' => 'Active',
        'actions' => 'Actions',
    ],

    // Status labels
    'status' => [
        'available' => 'Available',
        'occupied' => 'Occupied',
        'dirty' => 'Dirty',
        'cleaning' => 'Cleaning',
        'maintenance' => 'Maintenance',
        'out_of_order' => 'Out of Order',
        'reserved' => 'Reserved',
        'needs_cleaning' => 'Needs Cleaning',
    ],

    // Room types
    'types' => [
        'standard' => 'Standard Room',
        'deluxe' => 'Deluxe Room',
        'suite' => 'Suite',
        'executive' => 'Executive Suite',
        'presidential' => 'Presidential Suite',
    ],

    // Filters
    'filters' => [
        'search' => 'Search',
        'filter' => 'Filter',
        'all' => 'All',
        'select_floor' => 'Select a floor',
        'select_room_type' => 'Select a room type',
        'room_number_placeholder' => 'e.g., 101, 102, A-201',
    ],

    // Actions
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'create' => 'Create',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'confirm_delete' => 'Are you sure?',
        'confirm_delete_room' => 'Are you sure you want to delete this room?',
    ],

    // Messages
    'messages' => [
        'created' => 'Room created successfully.',
        'updated' => 'Room updated successfully.',
        'deleted' => 'Room deleted successfully.',
        'status_changed' => 'Room status changed successfully.',
        'no_rooms' => 'No rooms yet',
        'no_rooms_subtitle' => 'Get started by creating your first room.',
        'room_currently' => 'Room is currently',
        'status_warning' => 'Please ensure there are no active reservations before changing the status.',
    ],

    // Info sections
    'info' => [
        'room_numbering_tips' => 'Room Numbering Tips:',
        'floor_prefix' => 'Use floor number as prefix (e.g., 101, 201)',
        'consistent_numbering' => 'Keep numbering consistent across floors',
        'building_codes' => 'Consider building codes for multi-building properties',
        'room_information' => 'Room Information',
    ],
];
