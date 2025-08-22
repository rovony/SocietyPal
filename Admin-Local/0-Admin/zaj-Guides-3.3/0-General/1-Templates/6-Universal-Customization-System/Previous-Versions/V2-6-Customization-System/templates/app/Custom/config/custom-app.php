<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Application Settings
    |--------------------------------------------------------------------------
    | These settings extend the base application configuration without
    | modifying vendor files. They will persist through ALL updates.
    */

    'name' => env('CUSTOM_APP_NAME', config('app.name')),
    'version' => env('CUSTOM_APP_VERSION', '1.0.0'),
    'environment' => env('CUSTOM_ENV_MODE', 'development'),
    
    // Visual branding
    'branding' => [
        'logo' => env('CUSTOM_LOGO_PATH', '/Custom/images/logo.png'),
        'logo_dark' => env('CUSTOM_LOGO_DARK_PATH', '/Custom/images/logo-dark.png'),
        'favicon' => env('CUSTOM_FAVICON_PATH', '/Custom/images/favicon.ico'),
        'theme_color' => env('CUSTOM_THEME_COLOR', '#3490dc'),
        'accent_color' => env('CUSTOM_ACCENT_COLOR', '#f39c12'),
        'company_name' => env('CUSTOM_COMPANY_NAME', 'Your Company'),
    ],
    
    // Feature toggles (easy on/off switches)
    'features' => [
        'custom_dashboard' => env('CUSTOM_DASHBOARD_ENABLED', false),
        'custom_auth' => env('CUSTOM_AUTH_ENABLED', false),
        'custom_notifications' => env('CUSTOM_NOTIFICATIONS_ENABLED', false),
        'custom_reports' => env('CUSTOM_REPORTS_ENABLED', false),
        'saas_mode' => env('SAAS_MODE_ENABLED', false),
        'multi_tenant' => env('MULTI_TENANT_ENABLED', false),
        'api_access' => env('CUSTOM_API_ENABLED', false),
        'webhooks' => env('CUSTOM_WEBHOOKS_ENABLED', false),
    ],
    
    // Third-party integrations
    'integrations' => [
        'stripe' => env('CUSTOM_STRIPE_ENABLED', false),
        'paypal' => env('CUSTOM_PAYPAL_ENABLED', false),
        'mailchimp' => env('CUSTOM_MAILCHIMP_ENABLED', false),
        'analytics' => env('CUSTOM_ANALYTICS_ENABLED', false),
        'social_login' => env('CUSTOM_SOCIAL_LOGIN_ENABLED', false),
        'sms_service' => env('CUSTOM_SMS_ENABLED', false),
    ],
    
    // Business rules and limits
    'limits' => [
        'max_users' => env('CUSTOM_MAX_USERS', 1000),
        'max_storage_mb' => env('CUSTOM_MAX_STORAGE_MB', 1024),
        'api_rate_limit' => env('CUSTOM_API_RATE_LIMIT', 60),
        'upload_max_size' => env('CUSTOM_UPLOAD_MAX_SIZE', '10M'),
        'session_lifetime' => env('CUSTOM_SESSION_LIFETIME', 120),
    ],
    
    // Performance settings
    'performance' => [
        'cache_driver' => env('CUSTOM_CACHE_DRIVER', 'file'),
        'queue_connection' => env('CUSTOM_QUEUE_CONNECTION', 'sync'),
        'enable_compression' => env('CUSTOM_COMPRESSION_ENABLED', true),
        'enable_minification' => env('CUSTOM_MINIFICATION_ENABLED', true),
    ],
];