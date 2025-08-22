<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Priority Task Analytics Database Configuration
    |--------------------------------------------------------------------------
    | Database connections specifically for the Priority Task Analytics Dashboard
    | This allows analytics data to be stored separately from main application data
    */

    'connections' => [
        'task_analytics' => [
            'driver' => 'mysql',
            'url' => env('TASK_ANALYTICS_DATABASE_URL'),
            'host' => env('TASK_ANALYTICS_DB_HOST', '127.0.0.1'),
            'port' => env('TASK_ANALYTICS_DB_PORT', '3306'),
            'database' => env('TASK_ANALYTICS_DB_DATABASE', 'todo_analytics'),
            'username' => env('TASK_ANALYTICS_DB_USERNAME', 'forge'),
            'password' => env('TASK_ANALYTICS_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'analytics_',
            'strict' => true,
            'engine' => null,
        ],
        
        'task_metrics' => [
            'driver' => 'mysql', 
            'host' => env('TASK_METRICS_DB_HOST', '127.0.0.1'),
            'port' => env('TASK_METRICS_DB_PORT', '3306'),
            'database' => env('TASK_METRICS_DB_DATABASE', 'todo_metrics'),
            'username' => env('TASK_METRICS_DB_USERNAME', 'forge'),
            'password' => env('TASK_METRICS_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'metrics_',
            'strict' => true,
        ],
    ],
    
    'analytics_tables' => [
        'prefix' => env('ANALYTICS_TABLE_PREFIX', 'analytics_'),
        'suffix' => env('ANALYTICS_TABLE_SUFFIX', ''),
        'naming_convention' => 'snake_case',
        
        // Table definitions for Priority Task Analytics
        'task_completions' => 'task_completions',
        'priority_distributions' => 'priority_distributions', 
        'productivity_metrics' => 'productivity_metrics',
        'daily_summaries' => 'daily_summaries',
        'user_analytics' => 'user_analytics',
    ],
    
    // Analytics-specific settings
    'analytics_config' => [
        'retention_days' => env('ANALYTICS_RETENTION_DAYS', 365),
        'aggregation_interval' => env('ANALYTICS_AGGREGATION_INTERVAL', 'daily'),
        'real_time_updates' => env('ANALYTICS_REAL_TIME', true),
        'cache_duration' => env('ANALYTICS_CACHE_DURATION', 3600), // 1 hour
    ],
    
    // Migration settings
    'migrations' => [
        'auto_run' => env('ANALYTICS_AUTO_MIGRATE', false),
        'backup_before' => env('ANALYTICS_BACKUP_BEFORE_MIGRATE', true),
        'analytics_schema_path' => database_path('analytics_migrations'),
    ],
];
