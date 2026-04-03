<?php

return [
    // Page titles
    'title' => 'Laundry',
    'laundry_services' => 'Laundry Services',
    'laundry_orders' => 'Laundry Orders',
    'new_order' => 'New Order',
    'new_laundry_order' => 'New Laundry Order',
    'order_details' => 'Order Details',
    'price_list' => 'Price List',
    'laundry_price_list' => 'Laundry Price List',
    'daily_laundry_report' => 'Daily Laundry Report',

    // Subtitles
    'manage_subtitle' => 'Manage laundry orders, track status and process items',
    'create_subtitle' => 'Create a new laundry order for a hotel guest or walk-in customer',
    'price_list_subtitle' => 'Manage laundry services and item pricing',
    'report_subtitle' => 'View daily laundry revenue and order statistics',

    // Order status
    'status' => [
        'pending' => 'Pending',
        'received' => 'Received',
        'processing' => 'Processing',
        'ready' => 'Ready',
        'delivered' => 'Delivered',
        'collected' => 'Collected',
        'settled' => 'Settled',
        'cancelled' => 'Cancelled',
    ],

    // Services
    'services' => [
        'wash_fold' => 'Wash & Fold',
        'wash_iron' => 'Wash & Iron',
        'dry_clean' => 'Dry Clean',
        'iron_only' => 'Iron Only',
        'express' => 'Express Service',
    ],

    // Items
    'items' => [
        'shirt' => 'Shirt',
        'trousers' => 'Trousers',
        'dress' => 'Dress',
        'suit' => 'Suit',
        'jacket' => 'Jacket',
        'coat' => 'Coat',
        'bedsheet' => 'Bedsheet',
        'towel' => 'Towel',
        'blanket' => 'Blanket',
        'curtain' => 'Curtain',
        'underwear' => 'Underwear',
        'socks' => 'Socks',
    ],

    // Fields
    'fields' => [
        'guest_name' => 'Guest Name',
        'room_number' => 'Room Number',
        'booking' => 'Booking',
        'order_date' => 'Order Date',
        'pickup_date' => 'Pickup Date',
        'delivery_date' => 'Delivery Date',
        'item_count' => 'Item Count',
        'total_items' => 'Total Items',
        'total_amount' => 'Total Amount',
        'payment_status' => 'Payment Status',
        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
        'notes' => 'Notes',
        'special_instructions' => 'Special Instructions',
        'customer_name' => 'Customer Name',
        'customer_phone' => 'Phone Number',
        'quantity' => 'Quantity',
        'service_item' => 'Service & Item',
        'unit_price' => 'Unit Price',
        'subtotal' => 'Subtotal',
        'discount' => 'Discount',
        'payment_method' => 'Payment Method',
        'booking_id' => 'Booking ID',
        'turnaround' => 'turnaround',
    ],

    // Table headers
    'table' => [
        'order' => 'Order',
        'customer' => 'Customer',
        'type' => 'Type',
        'items' => 'Items',
        'total' => 'Total',
        'status' => 'Status',
        'expected_ready' => 'Expected Ready',
        'actions' => 'Actions',
        'service' => 'Service',
        'item' => 'Item',
        'qty' => 'Qty',
        'price' => 'Price (TZS)',
        'settled_by' => 'Settled By',
        'time' => 'Time',
        'payment' => 'Payment',
    ],

    // Customer types
    'customer_type' => [
        'guest' => 'Hotel Guest',
        'walkin' => 'Walk-in',
        'all' => 'All Customers',
        'hotel_guests' => 'Hotel Guests',
    ],

    // Sections
    'sections' => [
        'customer_details' => 'Customer Details',
        'laundry_items' => 'Laundry Items',
        'order_items' => 'Order Items',
        'order_information' => 'Order Information',
        'settle_payment' => 'Settle Payment',
        'overdue_orders' => 'Overdue Orders',
        'settled_orders' => 'Settled Orders',
        'payment_breakdown' => 'Payment Breakdown',
        'summary' => 'Summary',
    ],

    // Actions
    'actions' => [
        'create_order' => 'Create Order',
        'process_order' => 'Process Order',
        'start_processing' => 'Start Processing',
        'mark_ready' => 'Mark Ready',
        'mark_delivered' => 'Mark Delivered',
        'mark_collected' => 'Mark Collected',
        'deliver_to_room' => 'Deliver to Room',
        'settle_payment' => 'Settle Payment',
        'confirm_payment' => 'Confirm Payment',
        'cancel_order' => 'Cancel Order',
        'cancel' => 'Cancel',
        'add_item' => 'Add Item',
        'remove' => 'Remove',
        'remove_item' => 'Remove Item',
        'update_price' => 'Update Price',
        'save' => 'Save',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'back' => 'Back',
        'view' => 'View',
        'view_report' => 'View Report',
    ],

    // Placeholders
    'placeholders' => [
        'search' => 'Order #, name, room, phone...',
        'select_booking' => '-- Select checked-in guest --',
        'auto_filled' => 'Auto-filled from booking',
        'customer_name' => 'Customer name',
        'phone' => 'e.g. 0712 345 678',
        'instructions' => 'Stains, delicate items, handle with care...',
        'select_service' => 'Select service & item...',
        'item_notes' => 'Stain, delicate...',
        'new_item_name' => 'e.g. Blazer',
        'price' => '5000',
    ],

    // Payment methods
    'payment' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'charge_to_booking' => 'Charge to Booking',
        'cash_payments' => 'Cash Payments',
        'card_payments' => 'Card Payments',
        'charged_to_booking' => 'Charged to Booking',
    ],

    // Info labels
    'info' => [
        'received_by' => 'Received by',
        'received_at' => 'Received at',
        'expected_ready' => 'Expected ready',
        'processed_by' => 'Processed by',
        'ready_at' => 'Ready at',
        'delivered_at' => 'Delivered at',
        'collected_at' => 'Collected at',
        'payment' => 'Payment',
        'settled_by' => 'Settled by',
        'settled_at' => 'Settled at',
        'estimated_total' => 'Estimated Total',
        'total_tzs' => 'Total (TZS)',
        'room' => 'Room',
    ],

    // Report labels
    'reports' => [
        'daily_report' => 'Daily Laundry Report',
        'report_date' => 'Report Date',
        'total_orders' => 'Total Orders',
        'total_revenue' => 'Total Revenue',
        'total_revenue_tzs' => 'Total Revenue (TZS)',
        'guest_revenue' => 'Guest Revenue',
        'walkin_revenue' => 'Walk-in Revenue',
        'pending_orders' => 'Pending Orders',
        'completed_orders' => 'Completed Orders',
    ],

    // Messages
    'messages' => [
        'order_created' => 'Laundry order created successfully!',
        'order_updated' => 'Order updated successfully!',
        'order_processed' => 'Order is now being processed.',
        'order_ready' => 'Order is ready for pickup.',
        'order_delivered' => 'Order has been delivered.',
        'order_collected' => 'Order has been collected.',
        'order_settled' => 'Payment has been settled.',
        'order_cancelled' => 'Order has been cancelled.',
        'no_orders' => 'No orders found',
        'no_orders_subtitle' => 'Get started by creating your first laundry order.',
        'no_settled_orders' => 'No settled orders',
        'no_settled_orders_date' => 'No settled orders on this date.',
        'overdue' => 'Overdue',
        'cancel_confirm' => 'Cancel this order?',
        'confirm_remove' => 'Remove',
    ],

    // Additional keys
    'turnaround' => 'h turnaround',
    'confirm_remove' => 'Remove :item?',

    // Navigation
    'nav' => [
        'orders' => 'Orders',
        'new_order' => 'New Order',
        'price_list' => 'Price List',
        'reports' => 'Reports',
        'dashboard' => 'Dashboard',
    ],

    // Filters
    'filters' => [
        'all_customers' => 'All Customers',
        'all_statuses' => 'All Statuses',
    ],

    // New item form
    'new_item' => [
        'name' => 'New Item Name',
        'price' => 'Price (TZS)',
    ],

    // Legacy keys (for backward compatibility)
    'guest_name' => 'Guest Name',
    'room_number' => 'Room Number',
    'booking' => 'Booking',
    'order_date' => 'Order Date',
    'pickup_date' => 'Pickup Date',
    'delivery_date' => 'Delivery Date',
    'item_count' => 'Item Count',
    'total_items' => 'Total Items',
    'total_amount' => 'Total Amount',
    'payment_status' => 'Payment Status',
    'paid' => 'Paid',
    'unpaid' => 'Unpaid',
    'notes' => 'Notes',
    'special_instructions' => 'Special Instructions',
    'create_order' => 'Create Order',
    'process_order' => 'Process Order',
    'mark_ready' => 'Mark Ready',
    'mark_delivered' => 'Mark Delivered',
    'mark_collected' => 'Mark Collected',
    'settle_payment' => 'Settle Payment',
    'cancel_order' => 'Cancel Order',
    'add_item' => 'Add Item',
    'remove_item' => 'Remove Item',
    'update_price' => 'Update Price',
    'daily_report' => 'Daily Laundry Report',
    'report_date' => 'Report Date',
    'total_orders' => 'Total Orders',
    'total_revenue' => 'Total Revenue',
    'pending_orders' => 'Pending Orders',
    'completed_orders' => 'Completed Orders',
    'select_booking' => 'Select a booking',
    'select_service' => 'Select service type',
    'enter_quantity' => 'Enter quantity',
    'no_orders' => 'No laundry orders found.',
];
