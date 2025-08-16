<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Application Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains custom settings that override or extend
    | the base application configuration. Keep all customizations here to
    | maintain separation from vendor files.
    |
    */

    'name' => env('CUSTOM_APP_NAME', 'SocietyPal Custom'),
    
    'version' => '1.0.0',
    
    'debug' => env('CUSTOM_DEBUG', false),
    
    /*
    |--------------------------------------------------------------------------
    | Custom Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'enhanced_dashboard' => env('FEATURE_ENHANCED_DASHBOARD', true),
        'custom_notifications' => env('FEATURE_CUSTOM_NOTIFICATIONS', true),
        'advanced_reporting' => env('FEATURE_ADVANCED_REPORTING', false),
        'custom_themes' => env('FEATURE_CUSTOM_THEMES', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom UI Settings
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'theme' => env('CUSTOM_THEME', 'default'),
        'sidebar_collapsed' => env('CUSTOM_SIDEBAR_COLLAPSED', false),
        'show_custom_header' => env('CUSTOM_SHOW_HEADER', true),
        'items_per_page' => env('CUSTOM_ITEMS_PER_PAGE', 25),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Business Logic
    |--------------------------------------------------------------------------
    */
    'business' => [
        'society_types' => [
            'residential',
            'commercial',
            'mixed',
            'cooperative',
        ],
        
        'default_society_type' => 'residential',
        
        'custom_roles' => [
            'super_admin',
            'society_manager',
            'facility_manager',
            'security_guard',
        ],
        
        'payment_gateways' => [
            'stripe' => env('CUSTOM_STRIPE_ENABLED', false),
            'paypal' => env('CUSTOM_PAYPAL_ENABLED', false),
            'razorpay' => env('CUSTOM_RAZORPAY_ENABLED', true),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Integration Settings
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'sms_provider' => env('CUSTOM_SMS_PROVIDER', 'twilio'),
        'email_provider' => env('CUSTOM_EMAIL_PROVIDER', 'smtp'),
        'storage_provider' => env('CUSTOM_STORAGE_PROVIDER', 'local'),
        
        'third_party' => [
            'google_maps_key' => env('CUSTOM_GOOGLE_MAPS_KEY'),
            'analytics_id' => env('CUSTOM_ANALYTICS_ID'),
            'chat_widget' => env('CUSTOM_CHAT_WIDGET_ENABLED', false),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Rules
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'society_code_length' => 6,
        'phone_number_length' => 10,
        'password_min_length' => 8,
        'file_upload_max_size' => '10MB',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'dashboard_cache_ttl' => env('CUSTOM_DASHBOARD_CACHE_TTL', 300), // 5 minutes
        'reports_cache_ttl' => env('CUSTOM_REPORTS_CACHE_TTL', 3600), // 1 hour
        'user_preferences_ttl' => env('CUSTOM_USER_PREFS_TTL', 86400), // 24 hours
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'session_timeout' => env('CUSTOM_SESSION_TIMEOUT', 1440), // 24 hours in minutes
        'max_login_attempts' => env('CUSTOM_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('CUSTOM_LOCKOUT_DURATION', 900), // 15 minutes
        'force_https' => env('CUSTOM_FORCE_HTTPS', false),
        'enable_2fa' => env('CUSTOM_ENABLE_2FA', false),
    ],
];
