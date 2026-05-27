<?php

return [
    'title' => 'Uhifadhi',
    'booking' => 'Uhifadhi',
    'bookings' => 'Uhifadhi',
    'subtitle' => 'Simamia uhifadhi wa wageni, kuingia na kutoka',
    
    // Actions
    'new_booking' => 'Uhifadhi Mpya',
    'create_booking' => 'Unda Uhifadhi',
    'edit_booking' => 'Hariri Uhifadhi',
    'view_booking' => 'Tazama Uhifadhi',
    'cancel_booking' => 'Ghairi Uhifadhi',
    'confirm_booking' => 'Thibitisha Uhifadhi',
    'quick_actions' => 'Vitendo vya Haraka',
    'all_bookings' => 'Uhifadhi Wote',
    'todays_checkins' => 'Wanaoingia Leo',
    'todays_checkouts' => 'Wanaotoka Leo',
    'hall_bookings' => 'Uhifadhi wa Ukumbi',
    'current_guests' => 'Wageni wa Sasa',
    'check_in' => 'Sajili',
    'check_out' => 'Ondoa',
    'cancel' => 'Ghairi',
    'filter' => 'Chuja',
    'reset' => 'Weka Upya',
    
    // Stats
    'stats' => [
        'total' => 'Jumla',
        'checked_in' => 'Waliingia',
        'checked_out' => 'Waliotoka',
        'today_checkins' => 'Wanaoingia Leo',
        'today_checkouts' => 'Wanaotoka Leo',
    ],
    
    // Filters
    'filters' => [
        'search_placeholder' => 'Tafuta jina, barua pepe, nambari ya uhifadhi...',
        'all_statuses' => 'Hali Zote',
        'all_sources' => 'Vyanzo Vyote',
        'from_date' => 'Tarehe ya kuanzia',
    ],
    
    // Sources
    'sources' => [
        'online' => 'Mtandaoni',
        'frontdesk' => 'Mapokezi',
        'phone' => 'Simu',
        'walkin' => 'Mgeni wa Moja kwa Moja',
    ],
    
    // Table
    'table' => [
        'booking' => 'Uhifadhi',
        'guest' => 'Mgeni',
        'room' => 'Chumba',
        'check_in' => 'Kuingia',
        'check_out' => 'Kutoka',
        'amount' => 'Kiasi',
        'source' => 'Chanzo',
        'status' => 'Hali',
        'actions' => 'Vitendo',
        'not_assigned' => 'Haijawekwa',
        'nights' => 'usiku',
    ],
    
    // Messages
    'messages' => [
        'no_bookings_found' => 'Hakuna uhifadhi uliopatikana',
        'no_bookings_subtitle' => 'Anza kwa kuunda uhifadhi wako wa kwanza.',
        'confirm_cancel' => 'Una uhakika unataka kughairi uhifadhi huu?',
        'created' => 'Uhifadhi umeundwa kikamilifu.',
        'updated' => 'Uhifadhi umesasishwa kikamilifu.',
        'cancelled' => 'Uhifadhi umefutwa kikamilifu.',
        'checked_in' => 'Mgeni ameingia kikamilifu.',
        'checked_out' => 'Mgeni ametoka kikamilifu.',
        'no_available_rooms' => 'Hakuna vyumba vinavyopatikana kwa tarehe zilizochaguliwa.',
        'room_not_available' => 'Chumba hiki hakipatikani kwa tarehe zilizochaguliwa.',
        'loading_rooms' => 'Inatafuta vyumba vinavyopatikana...',
    ],
    
    // Fields
    'fields' => [
        'guest' => 'Mgeni',
        'room' => 'Chumba',
        'room_type' => 'Aina ya Chumba',
        'check_in_date' => 'Tarehe ya Kuingia',
        'check_out_date' => 'Tarehe ya Kutoka',
        'nights' => 'Usiku',
        'adults' => 'Watu wazima',
        'children' => 'Watoto',
        'rate' => 'Bei',
        'total_amount' => 'Jumla',
        'paid_amount' => 'Kiasi Kilicholipwa',
        'balance' => 'Salio',
        'payment_status' => 'Hali ya Malipo',
        'booking_status' => 'Hali ya Uhifadhi',
        'source' => 'Chanzo cha Uhifadhi',
        'special_requests' => 'Maombi Maalum',
    ],
    
    // Status
    'status' => [
        'pending' => 'Inasubiri',
        'confirmed' => 'Imethibitishwa',
        'checked_in' => 'Ameingia',
        'checked_out' => 'Ametoka',
        'cancelled' => 'Imefutwa',
        'no_show' => 'Hakuja',
    ],
    
    // Payment status
    'payment' => [
        'unpaid' => 'Haijalipwa',
        'partial' => 'Sehemu',
        'paid' => 'Imelipwa',
        'refunded' => 'Imerudishwa',
    ],
    
    // Sections
    'sections' => [
        'guest_info' => 'Taarifa za Mgeni',
        'booking_details' => 'Maelezo ya Uhifadhi',
        'room_details' => 'Maelezo ya Chumba',
        'payment_info' => 'Taarifa za Malipo',
        'charges' => 'Gharama',
    ],

    // Form labels
    'form' => [
        'select_existing_guest' => 'Chagua Mgeni Aliyepo',
        'create_new_guest' => 'Unda Mgeni Mpya',
        'choose_guest' => '-- Chagua Mgeni --',
        'first_name' => 'Jina la Kwanza',
        'last_name' => 'Jina la Mwisho',
        'email' => 'Barua Pepe',
        'phone' => 'Simu',
        'id_number' => 'Nambari ya Kitambulisho',
        'nationality' => 'Uraia',
        'number_of_guests' => 'Idadi ya Wageni',
        'change_guest_optional' => 'Badilisha Mgeni (Hiari)',
        'keep_current_guest' => '-- Weka Mgeni wa Sasa --',
        'special_requests_placeholder' => 'Maombi yoyote maalum au maelezo...',
        'select_dates_hint' => '(Chagua tarehe kwanza kuona vyumba vinavyopatikana)',
    ],

    // Placeholders
    'placeholders' => [
        'first_name' => 'Juma',
        'last_name' => 'Mwangi',
        'email' => 'juma@mifano.com',
        'phone' => '+255 700-000-000',
        'id_number' => 'Nambari ya kitambulisho',
        'nationality' => 'Uraia',
    ],

    // Page specific
    'walk_in_checkin' => 'Usajili wa Moja kwa Moja',
    'walk_in_description' => 'Sajili mgeni wa moja kwa moja moja kwa moja',
    'stay_details' => 'Maelezo ya Kukaa',
    'pricing_notes' => 'Bei na Maelezo',
    'per_night' => '/usiku',
    'search_rooms_hint' => 'Chagua tarehe ya kuingia na kutoka, kisha tafuta vyumba.',
    'status_checked_in' => 'Hali: Ameingia',
    'walkin_checked_in_immediately' => 'Wageni wa moja kwa moja wanaingia mara moja',
    'check_in_guest' => 'Sajili Mgeni',
    'current' => 'Sasa',
    'update_booking' => 'Sasisha Uhifadhi',
    'edit' => 'Hariri',
    'back' => 'Nyuma',
    'view_guest_profile' => 'Tazama Wasifu wa Mgeni',
    'reservation_label' => 'Uhifadhi wa Awali',
    'public_booking' => 'Uhifadhi wa Umma (Hakuna Wasifu)',
    'country' => 'Nchi',
    'duration' => 'Muda',
    'guests' => 'Wageni',
    'room_number' => 'Nambari ya Chumba',
    'floor' => 'Ghorofa',
    'building' => 'Jengo',
    'rate_per_night' => 'Bei/Usiku',
    'no_room_assigned' => 'Hakuna chumba kilichopewa',
    'payment_summary' => 'Muhtasari wa Malipo',
    'additional_information' => 'Taarifa za Ziada',
    'cancellation_reason' => 'Sababu ya Kughairi',
    'created_by' => 'Imeundwa Na',
    'confirmed_at' => 'Imethibitishwa Saa',
    'cancelled_at' => 'Imefutwa Saa',
    'created_at_label' => 'Imeundwa',
];
