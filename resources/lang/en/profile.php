<?php

return [
    // Page titles
    'title' => 'Profile',
    'page_title' => 'Profile Settings',
    
    // Section headers
    'sections' => [
        'profile_information' => 'Profile Information',
        'change_password' => 'Change Password',
        'update_password' => 'Update Password',
        'delete_account' => 'Delete Account',
    ],
    
    // Subtitles/descriptions
    'subtitles' => [
        'profile_info' => "Update your account's profile information and email address.",
        'password_security' => 'Update your account password securely',
        'password_hint' => 'Ensure your account is using a long, random password to stay secure.',
        'password_requirements' => 'Minimum 8 characters, must include uppercase, lowercase, and numbers.',
        'delete_warning' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',
        'delete_confirm_warning' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
    ],
    
    // Form fields
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'confirm_new_password' => 'Confirm New Password',
        'password' => 'Password',
    ],
    
    // Actions
    'actions' => [
        'save' => 'Save',
        'save_changes' => 'Save Changes',
        'change_password' => 'Change Password',
        'delete_account' => 'Delete Account',
        'cancel' => 'Cancel',
    ],
    
    // Messages
    'messages' => [
        'saved' => 'Saved.',
        'profile_updated' => 'Profile updated successfully.',
        'password_updated' => 'Password updated successfully.',
        'email_unverified' => 'Your email address is unverified.',
        'resend_verification' => 'Click here to re-send the verification email.',
        'verification_sent' => 'A new verification link has been sent to your email address.',
        'confirm_delete' => 'Are you sure you want to delete your account?',
    ],
];
