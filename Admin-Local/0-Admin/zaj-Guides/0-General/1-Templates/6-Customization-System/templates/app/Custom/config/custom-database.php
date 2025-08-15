<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Database Configuration
    |--------------------------------------------------------------------------
    | Additional database connections and settings for custom features
    */

    'connections' => [
        'custom_analytics' => [
            'driver' => 'mysql',
            'url' => env('CUSTOM_ANALYTICS_DATABASE_URL'),
            'host' => env('CUSTOM_ANALYTICS_DB_HOST', '127.0.0.1'),
            'port' => env('CUSTOM_ANALYTICS_DB_PORT', '3306'),
            'database' => env('CUSTOM_ANALYTICS_DB_DATABASE', 'analytics'),
            'username' => env('CUSTOM_ANALYTICS_DB_USERNAME', 'forge'),
            'password' => env('CUSTOM_ANALYTICS_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'custom_',
            'strict' => true,
            'engine' => null,
        ],
        
        'custom_logs' => [
            'driver' => 'mysql', 
            'host' => env('CUSTOM_LOGS_DB_HOST', '127.0.0.1'),
            'port' => env('CUSTOM_LOGS_DB_PORT', '3306'),
            'database' => env('CUSTOM_LOGS_DB_DATABASE', 'logs'),
            'username' => env('CUSTOM_LOGS_DB_USERNAME', 'forge'),
            'password' => env('CUSTOM_LOGS_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'custom_logs_',
            'strict' => true,
        ],
    ],
    
    'custom_tables' => [
        'prefix' => env('CUSTOM_TABLE_PREFIX', 'custom_'),
        'suffix' => env('CUSTOM_TABLE_SUFFIX', ''),
        'naming_convention' => 'snake_case', // snake_case or camelCase
    ],
    
    // Migration settings
    'migrations' => [
        'auto_run' => env('CUSTOM_AUTO_MIGRATE', false),
        'backup_before' => env('CUSTOM_BACKUP_BEFORE_MIGRATE', true),
        'rollback_limit' => env('CUSTOM_MIGRATION_ROLLBACK_LIMIT', 5),
    ],
];