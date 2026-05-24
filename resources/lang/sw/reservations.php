<?php

return [
    // Page titles
    'title' => 'Mahifadhi',
    'reservations' => 'Mahifadhi',
    'new_reservation' => 'Hifadhi Mpya',
    'create_reservation' => 'Unda Hifadhi',
    'edit_reservation' => 'Hariri Hifadhi',
    'delete_reservation' => 'Futa Hifadhi',
    'update_reservation' => 'Sasisha Hifadhi',

    // Subtitles
    'manage_subtitle' => 'Simamia mahifadhi ya wageni na kuingia',
    'create_subtitle' => 'Ongeza hifadhi mpya ya mgeni',

    // Status labels
    'status' => [
        'pending' => 'Inasubiri',
        'confirmed' => 'Imethibitishwa',
        'converted' => 'Imebadilishwa',
        'cancelled' => 'Imefutwa',
        'no_show' => 'Hakuonekana',
    ],

    // Table headers
    'table' => [
        'reservation' => 'Hifadhi',
        'guest' => 'Mgeni',
        'room' => 'Chumba',
        'check_in' => 'Kuingia',
        'check_out' => 'Kutoka',
        'amount' => 'Kiasi',
        'status' => 'Hali',
        'actions' => 'Vitendo',
    ],

    // Fields
    'fields' => [
        'guest_id' => 'Mgeni',
        'room_id' => 'Chumba',
        'check_in_date' => 'Tarehe ya Kuingia',
        'check_out_date' => 'Tarehe ya Kutoka',
        'number_of_guests' => 'Idadi ya Wageni',
        'estimated_amount' => 'Kiasi Kinachokadiriwa ($)',
        'status' => 'Hali ya Hifadhi',
        'guest_first_name' => 'Jina la Kwanza',
        'guest_last_name' => 'Jina la Mwisho',
        'phone_number' => 'Nambari ya Simu',
        'email' => 'Barua Pepe',
        'id_type' => 'Aina ya Kitambulisho',
        'id_number' => 'Nambari ya Kitambulisho/Pasipoti',
        'nationality' => 'Uraia',
        'id_photo' => 'Picha ya Kitambulisho',
        'assigned_room' => 'Chumba Kilichopangwa',
    ],

    // ID Types
    'id_types' => [
        'passport' => 'Pasipoti',
        'driver_license' => 'Leseni ya Dereva',
        'nida' => 'Nambari ya NIDA',
    ],

    // Sections
    'sections' => [
        'guest_information' => 'Taarifa za Mgeni',
        'reservation_details' => 'Maelezo ya Hifadhi',
        'reservation_information' => 'Taarifa za Hifadhi',
    ],

    // Guest type options
    'guest_type' => [
        'existing' => 'Chagua Mgeni Aliyepo',
        'new' => 'Unda Mgeni Mpya',
    ],

    // Change guest options
    'change_guest' => [
        'keep' => 'Weka Mgeni wa Sasa',
        'select' => 'Badilisha Mgeni',
        'new' => 'Unda Mgeni Mpya',
    ],

    // Placeholders
    'placeholders' => [
        'select_guest' => '-- Chagua mgeni --',
        'select_room' => 'Chagua chumba',
        'select_id_type' => '-- Chagua aina ya kitambulisho --',
        'select_nationality' => '-- Chagua uraia --',
        'not_assigned' => 'Hakuna Chumba',
        'enter_first_name' => 'Ingiza jina la kwanza',
        'enter_last_name' => 'Ingiza jina la mwisho',
        'phone' => '+255 712 345 678',
        'phone_number' => '712 345 678',
        'email' => 'mgeni@mfano.com',
        'enter_id_number' => 'Ingiza nambari ya kitambulisho',
        'passport_number' => 'Ingiza nambari ya pasipoti',
        'license_number' => 'Ingiza nambari ya leseni',
        'nida_number' => 'Ingiza nambari ya NIDA',
        'nationality' => 'mf., Mtanzania, Mkenya',
        'amount' => '0.00',
    ],

    // Nationality Groups
    'nationality_groups' => [
        'east_africa' => 'Afrika Mashariki',
        'southern_africa' => 'Afrika Kusini',
        'west_africa' => 'Afrika Magharibi',
        'north_africa' => 'Afrika Kaskazini',
        'europe' => 'Ulaya',
        'americas' => 'Amerika',
        'asia_oceania' => 'Asia & Oceania',
        'middle_east' => 'Mashariki ya Kati',
        'other' => 'Nyingine',
    ],

    // Labels
    'labels' => [
        'nights' => 'usiku',
        'night' => 'usiku',
        'per_night' => '/usiku',
        'no_email' => 'Hakuna barua pepe',
        'view_profile' => 'Tazama Wasifu',
        'not_assigned' => 'Hakuna Chumba',
        'legacy_reservation' => 'Hifadhi ya Zamani',
        'guest' => 'Mgeni',
        'estimated_total' => 'Jumla Inayokadiriwa',
        'upload_id_photo' => 'Pakia Picha ya Kitambulisho',
        'change_photo' => 'Badilisha Picha',
    ],

    // Actions
    'actions' => [
        'edit' => 'Hariri',
        'cancel' => 'Ghairi',
        'confirm' => 'Thibitisha',
        'check_in' => 'Kuingia',
        'delete' => 'Futa',
        'create' => 'Unda Hifadhi',
        'update' => 'Sasisha Hifadhi',
        'delete_reservation' => 'Futa Hifadhi',
    ],

    // Messages
    'messages' => [
        'no_reservations' => 'Hakuna mahifadhi bado',
        'no_reservations_subtitle' => 'Anza kwa kuunda hifadhi yako ya kwanza.',
        'confirm_cancel' => 'Una uhakika unataka kufuta hifadhi hii?',
        'confirm_delete' => 'Una uhakika unataka kufuta hifadhi hii?',
        'new_guest_created' => 'Rekodi mpya ya mgeni itaundwa kiotomatiki. Unaweza kuongeza maelezo zaidi (hati za kitambulisho, anwani) kwa kuhariri wasifu wa mgeni baadaye.',
    ],

    // Modal
    'modal' => [
        'upload_id_photo' => 'Pakia Picha ya Kitambulisho',
        'upload_id_photo_desc' => 'Pakia picha wazi ya hati ya kitambulisho cha mgeni',
        'click_to_upload' => 'Bofya kupakia au buruta na udondoshe',
        'cancel' => 'Ghairi',
        'confirm' => 'Thibitisha',
    ],

    // Info
    'info' => [
        'reservation_tips' => 'Vidokezo vya Hifadhi:',
        'tip_select_guest' => 'Chagua mgeni aliyepo au unda mpya kwa hifadhi hii',
        'tip_room_assignment' => 'Ukumbi wa chumba unahitajika kwa mahifadhi yote',
        'tip_amount' => 'Kiasi jumla kinahesabiwa kiotomatiki kulingana na bei ya chumba na muda wa kukaa',
        'tip_auto_number' => 'Nambari ya kipekee ya hifadhi itazalishwa kiotomatiki',
        'created_by' => 'Imeundwa Na',
        'created' => 'Imeundwa',
        'last_updated' => 'Ilisasishwa Mwisho',
        'stay_duration' => 'Muda wa Kukaa',
        'cant_find_guest' => 'Huwezi kupata mgeni?',
        'create_new_guest' => 'Unda mgeni mpya',
        'or' => 'au',
        'add_full_profile' => 'Ongeza wasifu kamili wa mgeni',
    ],
];
