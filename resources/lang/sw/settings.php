<?php

return [
    // Page title
    'title' => 'Mipangilio',
    'system_settings' => 'Mipangilio ya Mfumo',
    'general_settings' => 'Mipangilio ya Jumla',
    
    // Hotel Info
    'hotel_info' => 'Taarifa za Hoteli',
    'hotel_name' => 'Jina la Hoteli',
    'hotel_address' => 'Anwani',
    'hotel_phone' => 'Simu',
    'hotel_email' => 'Barua Pepe',
    'hotel_website' => 'Tovuti',
    'hotel_logo' => 'Nembo',
    
    // Currency
    'currency_settings' => 'Mipangilio ya Sarafu',
    'currency' => 'Sarafu',
    'currency_symbol' => 'Alama ya Sarafu',
    'currency_code' => 'Nambari ya Sarafu',
    'exchange_rate' => 'Kiwango cha Kubadilishana',
    
    // Sections (for admin settings page)
    'sections' => [
        'currency' => 'Mipangilio ya Sarafu',
        'change_password' => 'Badilisha Nenosiri',
        'profile_info' => 'Taarifa za Wasifu',
        'sms' => 'Mtoa Huduma wa SMS',
        'email' => 'Mipangilio ya Barua Pepe',
        'snipe' => 'Malipo ya Snipe',
        'azampesa' => 'Malipo ya AzamPesa',
    ],
    
    // Subtitles (for admin settings page)
    'subtitles' => [
        'currency' => 'Weka mipangilio ya sarafu kwa mfumo mzima',
        'password' => 'Sasisha nenosiri la akaunti yako kwa usalama',
        'profile' => 'Sasisha maelezo ya akaunti yako',
        'sms' => 'Sanidi taarifa za mtoa huduma wa SMS',
        'email' => 'Dhibiti mipangilio ya barua pepe',
        'snipe' => 'Dhibiti muunganisho wa malipo ya Snipe',
        'azampesa' => 'Dhibiti muunganisho wa malipo ya AzamPesa',
    ],
    
    // Form fields (for admin settings page)
    'fields' => [
        'default_currency' => 'Sarafu ya Kawaida',
        'tzs_exchange_rate' => 'Kiwango cha Kubadilisha TZS',
        'current_password' => 'Nenosiri la Sasa',
        'new_password' => 'Nenosiri Jipya',
        'confirm_password' => 'Thibitisha Nenosiri Jipya',
        'name' => 'Jina',
        'email' => 'Barua Pepe',
        'sms_provider_key' => 'Kitambulisho cha Mtoa SMS',
        'sms_sender_id' => 'Kitambulisho cha Mtumaji SMS',
        'sms_api_key' => 'Ufunguo wa API ya SMS',
        'sms_base_url' => 'Kiungo cha Msingi cha SMS',
        'sms_is_enabled' => 'Washa Mtoa SMS',
        'mail_driver' => 'Aina ya Barua',
        'mail_host' => 'Seva ya Barua',
        'mail_port' => 'Bandari ya Barua',
        'mail_username' => 'Jina la Mtumiaji wa Barua',
        'mail_password' => 'Nenosiri la Barua',
        'mail_encryption' => 'Usimbaji wa Barua',
        'mail_from_address' => 'Anwani ya Mtumaji',
        'mail_from_name' => 'Jina la Mtumaji',
        'mail_is_enabled' => 'Washa Uwasilishaji wa Barua',
        'snipe_base_url' => 'Kiungo cha Msingi cha Snipe',
        'snipe_api_key' => 'Ufunguo wa API wa Snipe',
        'snipe_api_secret' => 'Siri ya API ya Snipe',
        'snipe_webhook_secret' => 'Siri ya Webhook ya Snipe',
        'snipe_is_enabled' => 'Washa Malipo ya Snipe',
        'azampesa_base_url' => 'Kiungo cha Msingi cha AzamPesa',
        'azampesa_app_name' => 'Jina la Programu ya AzamPesa',
        'azampesa_client_id' => 'Kitambulisho cha Mteja wa AzamPesa',
        'azampesa_client_secret' => 'Siri ya Mteja wa AzamPesa',
        'azampesa_is_enabled' => 'Washa Malipo ya AzamPesa',
    ],
    
    // Hints (for admin settings page)
    'hints' => [
        'currency_usage' => 'Sarafu hii itatumika katika mfumo mzima kuonyesha bei.',
        'exchange_rate' => 'Sasisha kila siku kwa ubadilishaji sahihi wa sarafu.',
        'password_requirements' => 'Angalau herufi 8, lazima iwe na herufi kubwa, ndogo, na nambari.',
        'secret_masked' => 'Acha tupu ili kuhifadhi thamani ya siri iliyopo.',
        'secret_saved' => 'Siri imehifadhiwa',
        'secret_required' => 'Siri inahitajika ukiwasha',
        'secret_optional' => 'Siri ni ya hiari',
    ],
    
    // Labels (for admin settings page)
    'labels' => [
        'usd_equals' => '1 USD =',
        'tzs' => 'TZS',
    ],
    
    // Tax
    'tax_settings' => 'Mipangilio ya Kodi',
    'tax_rate' => 'Kiwango cha Kodi',
    'tax_name' => 'Jina la Kodi',
    'tax_included' => 'Kodi Imejumuishwa kwenye Bei',
    
    // Time & Date
    'timezone' => 'Eneo la Saa',
    'date_format' => 'Muundo wa Tarehe',
    'time_format' => 'Muundo wa Muda',
    
    // Booking
    'booking_settings' => 'Mipangilio ya Uhifadhi',
    'check_in_time' => 'Muda wa Kuingia',
    'check_out_time' => 'Muda wa Kutoka',
    'allow_online_booking' => 'Ruhusu Uhifadhi Mtandaoni',
    'require_deposit' => 'Hitaji Amana',
    'deposit_percentage' => 'Asilimia ya Amana',
    'cancellation_policy' => 'Sera ya Kufuta',
    
    // Email
    'email_settings' => 'Mipangilio ya Barua Pepe',
    'smtp_host' => 'Seva ya SMTP',
    'smtp_port' => 'Bandari ya SMTP',
    'smtp_username' => 'Jina la SMTP',
    'smtp_password' => 'Nenosiri la SMTP',
    'from_email' => 'Barua Pepe ya Mtumaji',
    'from_name' => 'Jina la Mtumaji',
    
    // Payment
    'payment_settings' => 'Mipangilio ya Malipo',
    'payment_methods' => 'Njia za Malipo',
    'cash' => 'Pesa Taslimu',
    'card' => 'Kadi',
    'mobile_money' => 'Pesa za Simu',
    'bank_transfer' => 'Uhamisho wa Benki',
    
    // Security
    'security_settings' => 'Mipangilio ya Usalama',
    'password_policy' => 'Sera ya Nenosiri',
    'session_timeout' => 'Muda wa Kikao',
    'two_factor_auth' => 'Uthibitishaji wa Hatua Mbili',
    
    // Actions
    'save_settings' => 'Hifadhi Mipangilio',
    'save_currency' => 'Hifadhi Mipangilio ya Sarafu',
    'save_sms' => 'Hifadhi Mipangilio ya SMS',
    'save_email' => 'Hifadhi Mipangilio ya Barua',
    'save_snipe' => 'Hifadhi Mipangilio ya Snipe',
    'change_password' => 'Badilisha Nenosiri',
    'save_profile' => 'Hifadhi Wasifu',
    'reset_to_default' => 'Weka Upya',
    'export_settings' => 'Hamisha Mipangilio',
    'import_settings' => 'Ingiza Mipangilio',
    
    // Messages
    'settings_saved' => 'Mipangilio imehifadhiwa kikamilifu!',
    'settings_updated' => 'Mipangilio imesasishwa kikamilifu!',
    'settings_reset' => 'Mipangilio imewekwa upya.',
    'invalid_settings' => 'Mipangilio batili.',
];
