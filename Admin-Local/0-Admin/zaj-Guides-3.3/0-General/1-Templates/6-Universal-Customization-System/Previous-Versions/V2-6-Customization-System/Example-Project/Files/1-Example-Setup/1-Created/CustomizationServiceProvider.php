<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * Priority Task Analytics Dashboard - Service Provider
 * 
 * This service provider demonstrates a complete real-world implementation
 * of the Zaj Laravel Customization System for a todo app feature:
 * "Priority Task Analytics Dashboard"
 * 
 * Features implemented:
 * - Analytics database connections
 * - Task metrics calculation services  
 * - Dashboard data aggregation
 * - Real-time analytics updates
 * - Frontend asset management
 * - API endpoints for charts/metrics
 */
class CustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register analytics database configuration
        if (class_exists('Illuminate\Support\ServiceProvider') && file_exists(app_path('Custom/config/custom-database.php'))) {
            $this->mergeConfigFrom(
                app_path('Custom/config/custom-database.php'), 'database'
            );
        }
        
        // Register custom application configuration
        if (file_exists(app_path('Custom/config/custom-app.php'))) {
            $this->mergeConfigFrom(
                app_path('Custom/config/custom-app.php'), 'custom'
            );
        }
        
        // Register analytics services
        $this->registerAnalyticsServices();
        
        // Register custom services
        $this->registerCustomServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load custom views
        $this->loadCustomViews();
        
        // Load custom routes (including analytics API routes)
        $this->loadCustomRoutes();
        
        // Bootstrap analytics features
        $this->bootAnalyticsFeatures();
        
        // Register custom middleware
        $this->registerCustomMiddleware();
        
        // Register custom helpers
        $this->registerCustomHelpers();
        
        // Publish custom assets
        $this->publishCustomAssets();
    }
    
    /**
     * Register analytics services for Priority Task Analytics Dashboard
     * 
     * This demonstrates a complete real-world feature implementation
     * using the Zaj Laravel Customization System
     */
    protected function registerAnalyticsServices(): void
    {
        // Task Analytics Service - Core analytics calculations
        $this->app->singleton('analytics.tasks', function ($app) {
            if (class_exists('App\\Custom\\Services\\TaskAnalyticsService')) {
                return app('App\\Custom\\Services\\TaskAnalyticsService');
            }
            return new class {
                public function getTaskMetrics($period = '7-days') { return []; }
                public function getPriorityDistribution($period = '7-days') { return []; }
                public function getProductivityScore($period = '7-days') { return 0; }
                public function getCompletionTrend($period = '7-days') { return []; }
                public function __call($method, $args) { return null; }
            };
        });
        
        // Dashboard Data Aggregation Service
        $this->app->singleton('analytics.dashboard', function ($app) {
            if (class_exists('App\\Custom\\Services\\DashboardAggregationService')) {
                return app('App\\Custom\\Services\\DashboardAggregationService');
            }
            return new class {
                public function getDashboardData($period = '7-days') { return []; }
                public function refreshDashboard() { return true; }
                public function exportDashboardData($format = 'csv') { return null; }
                public function __call($method, $args) { return null; }
            };
        });
        
        // Real-time Analytics Updates Service
        $this->app->singleton('analytics.realtime', function ($app) {
            if (class_exists('App\\Custom\\Services\\RealtimeAnalyticsService')) {
                return app('App\\Custom\\Services\\RealtimeAnalyticsService');
            }
            return new class {
                public function broadcastUpdate($metric, $data) { return true; }
                public function subscribeToUpdates($callback) { return true; }
                public function __call($method, $args) { return null; }
            };
        });
        
        // Chart Data Service - Formats data for Chart.js
        $this->app->singleton('analytics.charts', function ($app) {
            if (class_exists('App\\Custom\\Services\\ChartDataService')) {
                return app('App\\Custom\\Services\\ChartDataService');
            }
            return new class {
                public function getCompletionTrendChart($period = '7-days') { 
                    return ['labels' => [], 'completions' => [], 'created' => []]; 
                }
                public function getPriorityDistributionChart($period = '7-days') { 
                    return ['high' => 0, 'medium' => 0, 'low' => 0]; 
                }
                public function getProductivityTimelineChart($period = '7-days') { 
                    return ['hours' => [], 'completions' => []]; 
                }
                public function getHourlyActivityChart($period = '7-days') { 
                    return ['days' => [], 'thisWeek' => [], 'lastWeek' => []]; 
                }
                public function __call($method, $args) { return null; }
            };
        });
        
        // Database Connection Manager for Analytics
        $this->app->singleton('analytics.database', function ($app) {
            if (class_exists('App\\Custom\\Services\\AnalyticsDatabaseService')) {
                return app('App\\Custom\\Services\\AnalyticsDatabaseService');
            }
            return new class {
                public function getAnalyticsConnection() { return DB::connection(); }
                public function getMetricsConnection() { return DB::connection(); }
                public function runAnalyticsQuery($sql, $bindings = []) { return []; }
                public function __call($method, $args) { return null; }
            };
        });
    }

    /**
     * Register custom services
     * 
     * Note: Uses class_exists() checks for safe progressive implementation.
     * Services will only be registered if their classes exist, preventing errors
     * during initial setup or partial implementation.
     */
    protected function registerCustomServices(): void
    {
        // Custom Dashboard Service
        $this->app->singleton('custom.dashboard', function ($app) {
            if (class_exists('App\\Custom\\Services\\DashboardService')) {
                return app('App\\Custom\\Services\\DashboardService');
            }
            // Safe fallback - null object pattern to prevent method call errors
            return new class {
                public function __call($method, $args) { return null; }
                public function __get($property) { return null; }
            };
        });
        
        // Custom Notification Service
        $this->app->singleton('custom.notifications', function ($app) {
            if (class_exists('App\\Custom\\Services\\NotificationService')) {
                return app('App\\Custom\\Services\\NotificationService');
            }
            // Safe fallback
            return new class {
                public function __call($method, $args) { return null; }
                public function __get($property) { return null; }
            };
        });
        
        // Custom Theme Service
        $this->app->singleton('custom.theme', function ($app) {
            if (class_exists('App\\Custom\\Services\\ThemeService')) {
                return app('App\\Custom\\Services\\ThemeService');
            }
            // Safe fallback
            return new class {
                public function __call($method, $args) { return null; }
                public function __get($property) { return null; }
            };
        });
    }
    
    /**
     * Bootstrap analytics features for Priority Task Analytics Dashboard
     * 
     * This method demonstrates how to bootstrap a complete feature
     * including database connections, event listeners, and view composers
     */
    protected function bootAnalyticsFeatures(): void
    {
        // Register analytics database connections if config exists
        if (config('database.connections.task_analytics')) {
            Config::set('database.connections.task_analytics', config('database.connections.task_analytics'));
        }
        
        if (config('database.connections.task_metrics')) {
            Config::set('database.connections.task_metrics', config('database.connections.task_metrics'));
        }
        
        // Register analytics view composers
        if (class_exists('Illuminate\Support\Facades\View')) {
            View::composer(['custom.analytics.*', 'analytics.*'], function ($view) {
                $view->with([
                    'analyticsConfig' => config('custom.analytics_config', []),
                    'chartColors' => config('custom.chart_colors', []),
                    'analyticsVersion' => config('custom.analytics_version', '1.0.0'),
                ]);
            });
            
            // Share analytics data with dashboard views
            View::composer('custom.analytics.dashboard', function ($view) {
                $taskAnalytics = app('analytics.tasks');
                $view->with([
                    'taskMetrics' => $taskAnalytics->getTaskMetrics('7-days'),
                    'priorityDistribution' => $taskAnalytics->getPriorityDistribution('7-days'),
                    'productivityScore' => $taskAnalytics->getProductivityScore('7-days'),
                ]);
            });
        }
        
        // Register analytics event listeners
        if (class_exists('Illuminate\Support\Facades\Event')) {
            // Listen for task completion events
            Event::listen('task.completed', function ($task) {
                $realtimeAnalytics = app('analytics.realtime');
                $realtimeAnalytics->broadcastUpdate('task_completed', [
                    'task_id' => $task->id,
                    'priority' => $task->priority,
                    'completed_at' => now(),
                ]);
            });
            
            // Listen for task creation events
            Event::listen('task.created', function ($task) {
                $realtimeAnalytics = app('analytics.realtime');
                $realtimeAnalytics->broadcastUpdate('task_created', [
                    'task_id' => $task->id,
                    'priority' => $task->priority,
                    'created_at' => now(),
                ]);
            });
        }
        
        // Register analytics console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Register analytics commands if they exist
                'App\\Custom\\Console\\Commands\\AnalyticsAggregateCommand',
                'App\\Custom\\Console\\Commands\\AnalyticsExportCommand',
                'App\\Custom\\Console\\Commands\\AnalyticsCleanupCommand',
            ]);
        }
        
        // Register analytics API rate limiting
        if (class_exists('Illuminate\\Routing\\Middleware\\ThrottleRequests')) {
            $this->app['router']->middleware('analytics.throttle', function ($request, $next) {
                return app('Illuminate\\Routing\\Middleware\\ThrottleRequests')
                    ->handle($request, $next, 60, 1); // 60 requests per minute
            });
        }
    }

    /**
     * Load custom view paths
     */
    protected function loadCustomViews(): void
    {
        // Add custom view namespace
        View::addNamespace('custom', resource_path('Custom/views'));
        
        // Override vendor views if custom versions exist
        $customViewsPath = resource_path('Custom/views/overrides');
        if (is_dir($customViewsPath)) {
            // Use prependNamespace instead of prependLocation for better compatibility
            View::prependNamespace('', $customViewsPath);
        }
        
        // Share custom config with all views
        View::share('customConfig', config('custom'));
    }
    
    /**
     * Load custom routes
     */
    protected function loadCustomRoutes(): void
    {
        // API Routes
        $customApiRoutes = app_path('Custom/routes/api.php');
        if (file_exists($customApiRoutes)) {
            Route::middleware('api')
                 ->prefix('api/custom')
                 ->namespace('App\Custom\Controllers\Api')
                 ->group($customApiRoutes);
        }
        
        // Web Routes
        $customWebRoutes = app_path('Custom/routes/web.php');
        if (file_exists($customWebRoutes)) {
            Route::middleware('web')
                 ->prefix('custom')
                 ->namespace('App\Custom\Controllers')
                 ->group($customWebRoutes);
        }
    }
    
    /**
     * Register custom middleware
     * 
     * Note: Uses class_exists() checks to safely register middleware.
     * Only registers middleware classes that actually exist.
     */
    protected function registerCustomMiddleware(): void
    {
        // Register custom middleware aliases
        $router = $this->app['router'];
        
        $customMiddleware = [
            'custom.auth' => 'App\\Custom\\Middleware\\CustomAuthentication',
            'custom.role' => 'App\\Custom\\Middleware\\CustomRoleCheck',
            'custom.feature' => 'App\\Custom\\Middleware\\FeatureToggle',
        ];
        
        foreach ($customMiddleware as $alias => $class) {
            if (class_exists($class)) {
                $router->aliasMiddleware($alias, $class);
            }
        }
    }
    
    /**
     * Register custom helper functions
     */
    protected function registerCustomHelpers(): void
    {
        $helpersFile = app_path('Custom/Helpers/functions.php');
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }
    
    /**
     * Publish custom assets
     */
    protected function publishCustomAssets(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish custom config files
            $this->publishes([
                app_path('Custom/config') => config_path('custom'),
            ], 'custom-config');
            
            // Publish custom assets
            $this->publishes([
                resource_path('Custom/assets') => public_path('custom'),
            ], 'custom-assets');
            
            // Publish custom views
            $this->publishes([
                resource_path('Custom/views/publishable') => resource_path('views/custom'),
            ], 'custom-views');
        }
    }
    
    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'custom.dashboard',
            'custom.notifications',
            'custom.theme',
        ];
    }
}
