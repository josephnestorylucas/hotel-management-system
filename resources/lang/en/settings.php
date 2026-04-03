<?php

return [
    // Page title
    'title' => 'Settings',
    'system_settings' => 'System Settings',
    'general_settings' => 'General Settings',
    
    // Hotel Info
    'hotel_info' => 'Hotel Information',
    'hotel_name' => 'Hotel Name',
    'hotel_address' => 'Address',
    'hotel_phone' => 'Phone',
    'hotel_email' => 'Email',
    'hotel_website' => 'Website',
    'hotel_logo' => 'Logo',
    
    // Currency
    'currency_settings' => 'Currency Settings',
    'currency' => 'Currency',
    'currency_symbol' => 'Currency Symbol',
    'currency_code' => 'Currency Code',
    'exchange_rate' => 'Exchange Rate',
    
    // Sections (for admin settings page)
    'sections' => [
        'currency' => 'Currency Settings',
        'change_password' => 'Change Password',
        'profile_info' => 'Profile Information',
    ],
    
    // Subtitles (for admin settings page)
    'subtitles' => [
        'currency' => 'Configure system-wide currency display',
        'password' => 'Update your account password securely',
        'profile' => 'Update your account details',
    ],
    
    // Form fields (for admin settings page)
    'fields' => [
        'default_currency' => 'Default Currency',
        'tzs_exchange_rate' => 'TZS Exchange Rate',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
        'name' => 'Name',
        'email' => 'Email',
    ],
    
    // Hints (for admin settings page)
    'hints' => [
        'currency_usage' => 'This currency will be used across the entire system for displaying prices.',
        'exchange_rate' => 'Update daily for accurate currency conversion.',
        'password_requirements' => 'Minimum 8 characters, must include uppercase, lowercase, and numbers.',
    ],
    
    // Labels (for admin settings page)
    'labels' => [
        'usd_equals' => '1 USD =',
        'tzs' => 'TZS',
    ],
    
    // Tax
    'tax_settings' => 'Tax Settings',
    'tax_rate' => 'Tax Rate',
    'tax_name' => 'Tax Name',
    'tax_included' => 'Tax Included in Price',
    
    // Time & Date
    'timezone' => 'Timezone',
    'date_format' => 'Date Format',
    'time_format' => 'Time Format',
    
    // Booking
    'booking_settings' => 'Booking Settings',
    'check_in_time' => 'Check-in Time',
    'check_out_time' => 'Check-out Time',
    'allow_online_booking' => 'Allow Online Booking',
    'require_deposit' => 'Require Deposit',
    'deposit_percentage' => 'Deposit Percentage',
    'cancellation_policy' => 'Cancellation Policy',
    
    // Email
    'email_settings' => 'Email Settings',
    'smtp_host' => 'SMTP Host',
    'smtp_port' => 'SMTP Port',
    'smtp_username' => 'SMTP Username',
    'smtp_password' => 'SMTP Password',
    'from_email' => 'From Email',
    'from_name' => 'From Name',
    
    // Payment
    'payment_settings' => 'Payment Settings',
    'payment_methods' => 'Payment Methods',
    'cash' => 'Cash',
    'card' => 'Card',
    'mobile_money' => 'Mobile Money',
    'bank_transfer' => 'Bank Transfer',
    
    // Security
    'security_settings' => 'Security Settings',
    'password_policy' => 'Password Policy',
    'session_timeout' => 'Session Timeout',
    'two_factor_auth' => 'Two-Factor Authentication',
    
    // Actions
    'save_settings' => 'Save Settings',
    'save_currency' => 'Save Currency Settings',
    'change_password' => 'Change Password',
    'save_profile' => 'Save Profile',
    'reset_to_default' => 'Reset to Default',
    'export_settings' => 'Export Settings',
    'import_settings' => 'Import Settings',
    
    // Messages
    'settings_saved' => 'Settings saved successfully!',
    'settings_updated' => 'Settings updated successfully!',
    'settings_reset' => 'Settings have been reset to default.',
    'invalid_settings' => 'Invalid settings provided.',
];
