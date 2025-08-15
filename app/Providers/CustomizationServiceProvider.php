<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge all custom configurations
        $this->mergeConfigFrom(app_path('Custom/config/custom-app.php'), 'custom');
        $this->mergeConfigFrom(app_path('Custom/config/custom-database.php'), 'custom-database');
        
        // Register custom services and bindings
        $this->registerCustomServices();
        
        // Register custom commands
        $this->registerCustomCommands();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load custom components
        $this->loadCustomRoutes();
        $this->loadCustomViews(); 
        $this->loadCustomMigrations();
        $this->loadCustomBladeDirectives();
        
        // Register custom middleware
        $this->registerCustomMiddleware();
        
        // Load custom database connections
        $this->configureCustomDatabases();
        
        // Log customization layer activation
        Log::info('✅ Custom Layer Activated', [
            'version' => config('custom.version', '1.0.0'),
            'features_enabled' => array_keys(array_filter(config('custom.features', []))),
            'integrations_active' => array_keys(array_filter(config('custom.integrations', []))),
        ]);
    }

    /**
     * Load custom routes with priority over vendor routes
     */
    protected function loadCustomRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $routeFile = app_path('Custom/routes/web.php');
                if (file_exists($routeFile)) {
                    require $routeFile;
                }
            });
            
        Route::middleware('api')
            ->prefix('api/custom')
            ->group(function () {
                $apiRouteFile = app_path('Custom/routes/api.php');
                if (file_exists($apiRouteFile)) {
                    require $apiRouteFile;
                }
            });
    }

    /**
     * Load custom view paths with priority
     */
    protected function loadCustomViews(): void
    {
        // Custom views take precedence over vendor views
        View::addLocation(resource_path('Custom/views'));
        
        // Add view composers for custom data
        View::composer('*', function ($view) {
            $view->with([
                'customConfig' => config('custom', []),
                'customBranding' => config('custom.branding', []),
                'isCustomLayer' => true,
            ]);
        });
    }
    
    /**
     * Load custom migrations
     */
    protected function loadCustomMigrations(): void
    {
        $this->loadMigrationsFrom(database_path('Custom/migrations'));
    }
    
    /**
     * Register custom Blade directives
     */
    protected function loadCustomBladeDirectives(): void
    {
        // @customAsset directive for custom asset paths
        Blade::directive('customAsset', function ($expression) {
            return "<?php echo asset('Custom/' . {$expression}); ?>";
        });
        
        // @ifCustomFeature directive for feature toggles
        Blade::directive('ifCustomFeature', function ($expression) {
            return "<?php if(config('custom.features.{$expression}', false)): ?>";
        });
        
        Blade::directive('endifCustomFeature', function () {
            return "<?php endif; ?>";
        });
        
        // @customConfig directive for easy config access
        Blade::directive('customConfig', function ($expression) {
            return "<?php echo config('custom.{$expression}'); ?>";
        });
    }
    
    /**
     * Register custom services
     */
    protected function registerCustomServices(): void
    {
        // Example: Custom user service
        // $this->app->bind(UserServiceInterface::class, CustomUserService::class);
        
        // Register custom singletons
        // $this->app->singleton('custom.service', CustomService::class);
    }
    
    /**
     * Register custom commands
     */
    protected function registerCustomCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add custom artisan commands here
                // \App\Custom\Commands\CustomInstallCommand::class,
                // \App\Custom\Commands\CustomVerifyCommand::class,
            ]);
        }
    }
    
    /**
     * Register custom middleware
     */
    protected function registerCustomMiddleware(): void
    {
        // Register custom middleware
        // $this->app['router']->aliasMiddleware('custom.auth', \App\Custom\Middleware\CustomAuthMiddleware::class);
    }
    
    /**
     * Configure custom database connections
     */
    protected function configureCustomDatabases(): void
    {
        $customConnections = config('custom-database.connections', []);
        
        foreach ($customConnections as $name => $config) {
            config(["database.connections.{$name}" => $config]);
        }
    }
}