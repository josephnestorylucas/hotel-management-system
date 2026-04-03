<?php

return [
    // Page titles
    'title' => 'Kufulia',
    'laundry_services' => 'Huduma za Kufulia',
    'laundry_orders' => 'Oda za Kufulia',
    'new_order' => 'Oda Mpya',
    'new_laundry_order' => 'Oda Mpya ya Kufulia',
    'order_details' => 'Maelezo ya Oda',
    'price_list' => 'Orodha ya Bei',
    'laundry_price_list' => 'Orodha ya Bei za Kufulia',
    'daily_laundry_report' => 'Ripoti ya Kila Siku ya Kufulia',

    // Subtitles
    'manage_subtitle' => 'Simamia oda za kufulia, fuatilia hali na fanya vitu',
    'create_subtitle' => 'Unda oda mpya ya kufulia kwa mgeni wa hoteli au mteja wa kutembea',
    'price_list_subtitle' => 'Simamia huduma za kufulia na bei za vitu',
    'report_subtitle' => 'Tazama mapato ya kila siku ya kufulia na takwimu za oda',

    // Order status
    'status' => [
        'pending' => 'Inasubiri',
        'received' => 'Imepokelewa',
        'processing' => 'Inafanyiwa kazi',
        'ready' => 'Tayari',
        'delivered' => 'Imewasilishwa',
        'collected' => 'Imechukuliwa',
        'settled' => 'Imelipwa',
        'cancelled' => 'Imefutwa',
    ],

    // Services
    'services' => [
        'wash_fold' => 'Fua na Kukunja',
        'wash_iron' => 'Fua na Kupiga Pasi',
        'dry_clean' => 'Kusafisha Kavu',
        'iron_only' => 'Piga Pasi Tu',
        'express' => 'Huduma ya Haraka',
    ],

    // Items
    'items' => [
        'shirt' => 'Shati',
        'trousers' => 'Suruali',
        'dress' => 'Gauni',
        'suit' => 'Suti',
        'jacket' => 'Jaketi',
        'coat' => 'Koti',
        'bedsheet' => 'Shuka',
        'towel' => 'Taulo',
        'blanket' => 'Blanketi',
        'curtain' => 'Pazia',
        'underwear' => 'Nguo za Ndani',
        'socks' => 'Soksi',
    ],

    // Fields
    'fields' => [
        'guest_name' => 'Jina la Mgeni',
        'room_number' => 'Nambari ya Chumba',
        'booking' => 'Uhifadhi',
        'order_date' => 'Tarehe ya Oda',
        'pickup_date' => 'Tarehe ya Kuchukua',
        'delivery_date' => 'Tarehe ya Kuwasilisha',
        'item_count' => 'Idadi ya Vitu',
        'total_items' => 'Jumla ya Vitu',
        'total_amount' => 'Kiasi Jumla',
        'payment_status' => 'Hali ya Malipo',
        'paid' => 'Imelipwa',
        'unpaid' => 'Haijalipwa',
        'notes' => 'Maelezo',
        'special_instructions' => 'Maelekezo Maalum',
        'customer_name' => 'Jina la Mteja',
        'customer_phone' => 'Nambari ya Simu',
        'quantity' => 'Idadi',
        'service_item' => 'Huduma na Kitu',
        'unit_price' => 'Bei ya Kitu',
        'subtotal' => 'Jumla ndogo',
        'discount' => 'Punguzo',
        'payment_method' => 'Njia ya Malipo',
        'booking_id' => 'Nambari ya Uhifadhi',
        'turnaround' => 'muda',
    ],

    // Table headers
    'table' => [
        'order' => 'Oda',
        'customer' => 'Mteja',
        'type' => 'Aina',
        'items' => 'Vitu',
        'total' => 'Jumla',
        'status' => 'Hali',
        'expected_ready' => 'Itarajia Kuwa Tayari',
        'actions' => 'Vitendo',
        'service' => 'Huduma',
        'item' => 'Kitu',
        'qty' => 'Idadi',
        'price' => 'Bei (TZS)',
        'settled_by' => 'Amelipwa na',
        'time' => 'Wakati',
        'payment' => 'Malipo',
    ],

    // Customer types
    'customer_type' => [
        'guest' => 'Mgeni wa Hoteli',
        'walkin' => 'Mteja wa Kutembea',
        'all' => 'Wateja Wote',
        'hotel_guests' => 'Wageni wa Hoteli',
    ],

    // Sections
    'sections' => [
        'customer_details' => 'Maelezo ya Mteja',
        'laundry_items' => 'Vitu vya Kufulia',
        'order_items' => 'Vitu vya Oda',
        'order_information' => 'Maelezo ya Oda',
        'settle_payment' => 'Lipa Malipo',
        'overdue_orders' => 'Oda Zilizochelewa',
        'settled_orders' => 'Oda Zilizolipwa',
        'payment_breakdown' => 'Mgawanyo wa Malipo',
        'summary' => 'Muhtasari',
    ],

    // Actions
    'actions' => [
        'create_order' => 'Unda Oda',
        'process_order' => 'Fanya Oda',
        'start_processing' => 'Anza Kufanya',
        'mark_ready' => 'Weka Tayari',
        'mark_delivered' => 'Weka Imewasilishwa',
        'mark_collected' => 'Weka Imechukuliwa',
        'deliver_to_room' => 'Peleka Chumbani',
        'settle_payment' => 'Lipa Malipo',
        'confirm_payment' => 'Thibitisha Malipo',
        'cancel_order' => 'Futa Oda',
        'cancel' => 'Ghairi',
        'add_item' => 'Ongeza Kitu',
        'remove' => 'Ondoa',
        'remove_item' => 'Ondoa Kitu',
        'update_price' => 'Sasisha Bei',
        'save' => 'Hifadhi',
        'filter' => 'Chuja',
        'reset' => 'Weka upya',
        'back' => 'Rudi',
        'view' => 'Tazama',
        'view_report' => 'Tazama Ripoti',
    ],

    // Placeholders
    'placeholders' => [
        'search' => 'Oda #, jina, chumba, simu...',
        'select_booking' => '-- Chagua mgeni aliyeingia --',
        'auto_filled' => 'Imejazwa kutoka uhifadhi',
        'customer_name' => 'Jina la mteja',
        'phone' => 'mf. 0712 345 678',
        'instructions' => 'Madoa, vitu dhaifu, shughulikia kwa uangalifu...',
        'select_service' => 'Chagua huduma na kitu...',
        'item_notes' => 'Doa, dhaifu...',
        'new_item_name' => 'mf. Blazer',
        'price' => '5000',
    ],

    // Payment methods
    'payment' => [
        'cash' => 'Taslimu',
        'card' => 'Kadi',
        'charge_to_booking' => 'Weka kwa Uhifadhi',
        'cash_payments' => 'Malipo ya Taslimu',
        'card_payments' => 'Malipo ya Kadi',
        'charged_to_booking' => 'Imewekwa kwa Uhifadhi',
    ],

    // Info labels
    'info' => [
        'received_by' => 'Imepokelewa na',
        'received_at' => 'Imepokelewa',
        'expected_ready' => 'Itarajia kuwa tayari',
        'processed_by' => 'Imefanywa na',
        'ready_at' => 'Tayari saa',
        'delivered_at' => 'Imewasilishwa saa',
        'collected_at' => 'Imechukuliwa saa',
        'payment' => 'Malipo',
        'settled_by' => 'Imelipwa na',
        'settled_at' => 'Imelipwa saa',
        'estimated_total' => 'Jumla Inayokadiriwa',
        'total_tzs' => 'Jumla (TZS)',
        'room' => 'Chumba',
    ],

    // Report labels
    'reports' => [
        'daily_report' => 'Ripoti ya Kila Siku ya Kufulia',
        'report_date' => 'Tarehe ya Ripoti',
        'total_orders' => 'Jumla ya Oda',
        'total_revenue' => 'Jumla ya Mapato',
        'total_revenue_tzs' => 'Jumla ya Mapato (TZS)',
        'guest_revenue' => 'Mapato ya Wageni',
        'walkin_revenue' => 'Mapato ya Wateja wa Kutembea',
        'pending_orders' => 'Oda Zinazosubiri',
        'completed_orders' => 'Oda Zilizokamilika',
    ],

    // Messages
    'messages' => [
        'order_created' => 'Oda ya kufulia imeundwa kikamilifu!',
        'order_updated' => 'Oda imesasishwa kikamilifu!',
        'order_processed' => 'Oda inafanyiwa kazi.',
        'order_ready' => 'Oda iko tayari kuchukuliwa.',
        'order_delivered' => 'Oda imewasilishwa.',
        'order_collected' => 'Oda imechukuliwa.',
        'order_settled' => 'Malipo yamefanywa.',
        'order_cancelled' => 'Oda imefutwa.',
        'no_orders' => 'Hakuna oda zilizopatikana',
        'no_orders_subtitle' => 'Anza kwa kuunda oda yako ya kwanza ya kufulia.',
        'no_settled_orders' => 'Hakuna oda zilizolipwa',
        'no_settled_orders_date' => 'Hakuna oda zilizolipwa tarehe hii.',
        'overdue' => 'Imechelewa',
        'cancel_confirm' => 'Futa oda hii?',
        'confirm_remove' => 'Ondoa',
    ],

    // Additional keys
    'turnaround' => 'saa za kumaliza',
    'confirm_remove' => 'Ondoa :item?',

    // Navigation
    'nav' => [
        'orders' => 'Oda',
        'new_order' => 'Oda Mpya',
        'price_list' => 'Orodha ya Bei',
        'reports' => 'Ripoti',
        'dashboard' => 'Dashibodi',
    ],

    // Filters
    'filters' => [
        'all_customers' => 'Wateja Wote',
        'all_statuses' => 'Hali Zote',
    ],

    // New item form
    'new_item' => [
        'name' => 'Jina la Kitu Kipya',
        'price' => 'Bei (TZS)',
    ],

    // Legacy keys (for backward compatibility)
    'guest_name' => 'Jina la Mgeni',
    'room_number' => 'Nambari ya Chumba',
    'booking' => 'Uhifadhi',
    'order_date' => 'Tarehe ya Oda',
    'pickup_date' => 'Tarehe ya Kuchukua',
    'delivery_date' => 'Tarehe ya Kuwasilisha',
    'item_count' => 'Idadi ya Vitu',
    'total_items' => 'Jumla ya Vitu',
    'total_amount' => 'Kiasi Jumla',
    'payment_status' => 'Hali ya Malipo',
    'paid' => 'Imelipwa',
    'unpaid' => 'Haijalipwa',
    'notes' => 'Maelezo',
    'special_instructions' => 'Maelekezo Maalum',
    'create_order' => 'Unda Oda',
    'process_order' => 'Fanya Oda',
    'mark_ready' => 'Weka Tayari',
    'mark_delivered' => 'Weka Imewasilishwa',
    'mark_collected' => 'Weka Imechukuliwa',
    'settle_payment' => 'Lipa Malipo',
    'cancel_order' => 'Futa Oda',
    'add_item' => 'Ongeza Kitu',
    'remove_item' => 'Ondoa Kitu',
    'update_price' => 'Sasisha Bei',
    'daily_report' => 'Ripoti ya Kila Siku ya Kufulia',
    'report_date' => 'Tarehe ya Ripoti',
    'total_orders' => 'Jumla ya Oda',
    'total_revenue' => 'Jumla ya Mapato',
    'pending_orders' => 'Oda Zinazosubiri',
    'completed_orders' => 'Oda Zilizokamilika',
    'select_booking' => 'Chagua uhifadhi',
    'select_service' => 'Chagua aina ya huduma',
    'enter_quantity' => 'Ingiza idadi',
    'no_orders' => 'Hakuna oda za kufulia zilizopatikana.',
];
