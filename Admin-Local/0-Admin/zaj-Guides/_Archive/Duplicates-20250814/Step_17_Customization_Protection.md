# Step 17: Customization Protection System

**Create a robust system to protect customizations from vendor updates**

> 📋 **Analysis Source:** CodeCanyon best practices + Laravel customization patterns + Future-proofing strategies
>
> 🎯 **Purpose:** Establish a bulletproof customization layer that survives vendor updates and scales for SaaS
>
> ⚠️ **Critical:** This prevents losing months of work during CodeCanyon updates

---

## **Overview: The Customization Challenge**

**The Problem:**
- CodeCanyon updates overwrite customizations
- Direct vendor file modifications break on updates
- No clear separation between vendor and custom code
- Updates become risky and time-consuming

**The Solution:**
- Layered customization architecture
- Update-safe coding practices
- Version-controlled custom overlay system
- Automated customization testing

---

## **Phase 1: Custom Directory Structure**

### **1.1 Create Customization Directories**

```bash
echo "🏗️ Phase 1.1: Setting up customization directory structure..."

# Create main customization directories
mkdir -p app/Custom/{Controllers,Models,Services,Middleware,Rules,Events,Listeners}
mkdir -p resources/Custom/{views,js,css,images}
mkdir -p database/Custom/{migrations,seeders,factories}
mkdir -p config/Custom
mkdir -p routes/Custom
mkdir -p public/Custom/{css,js,images,assets}
mkdir -p storage/Custom/{logs,cache,uploads}

# Create customization tracking files
touch app/Custom/README.md
touch resources/Custom/README.md
touch database/Custom/README.md
touch config/Custom/README.md
touch routes/Custom/README.md

echo "✅ Customization directory structure created"
```

### **1.2 Create Custom Namespace Registration**

```bash
echo "🔧 Phase 1.2: Setting up custom namespace registration..."

# Create custom service provider for registering customizations
cat > app/Custom/CustomizationServiceProvider.php << 'EOF'
<?php

namespace App\Custom;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class CustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register custom configurations
        $this->mergeCustomConfigs();
        
        // Register custom services
        $this->registerCustomServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load custom routes
        $this->loadCustomRoutes();
        
        // Register custom views
        $this->registerCustomViews();
        
        // Register custom commands
        $this->registerCustomCommands();
        
        // Load custom migrations
        $this->loadCustomMigrations();
    }

    /**
     * Merge custom configuration files
     */
    protected function mergeCustomConfigs(): void
    {
        $customConfigPath = app_path('Custom/config');
        
        if (is_dir($customConfigPath)) {
            foreach (glob($customConfigPath . '/*.php') as $configFile) {
                $configName = basename($configFile, '.php');
                $this->mergeConfigFrom($configFile, $configName);
            }
        }
    }

    /**
     * Load custom routes safely
     */
    protected function loadCustomRoutes(): void
    {
        $customRoutesPath = base_path('routes/Custom');
        
        if (is_dir($customRoutesPath)) {
            // Web routes
            if (file_exists($customRoutesPath . '/web.php')) {
                Route::middleware('web')
                    ->namespace('App\Custom\Controllers')
                    ->group($customRoutesPath . '/web.php');
            }
            
            // API routes
            if (file_exists($customRoutesPath . '/api.php')) {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace('App\Custom\Controllers')
                    ->group($customRoutesPath . '/api.php');
            }
        }
    }

    /**
     * Register custom view paths
     */
    protected function registerCustomViews(): void
    {
        $customViewsPath = resource_path('Custom/views');
        
        if (is_dir($customViewsPath)) {
            View::addLocation($customViewsPath);
        }
    }

    /**
     * Register custom Artisan commands
     */
    protected function registerCustomCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $commandsPath = app_path('Custom/Commands');
            
            if (is_dir($commandsPath)) {
                foreach (glob($commandsPath . '/*.php') as $commandFile) {
                    require_once $commandFile;
                }
            }
        }
    }

    /**
     * Load custom migrations
     */
    protected function loadCustomMigrations(): void
    {
        $customMigrationsPath = database_path('Custom/migrations');
        
        if (is_dir($customMigrationsPath)) {
            $this->loadMigrationsFrom($customMigrationsPath);
        }
    }

    /**
     * Register custom services
     */
    protected function registerCustomServices(): void
    {
        $servicesPath = app_path('Custom/Services');
        
        if (is_dir($servicesPath)) {
            foreach (glob($servicesPath . '/*.php') as $serviceFile) {
                $className = basename($serviceFile, '.php');
                $fullClassName = 'App\\Custom\\Services\\' . $className;
                
                if (class_exists($fullClassName)) {
                    $this->app->singleton($fullClassName);
                }
            }
        }
    }
}
EOF

echo "✅ CustomizationServiceProvider created"
```

### **1.3 Register Custom Service Provider**

```bash
echo "📝 Phase 1.3: Registering CustomizationServiceProvider..."

# Check if we need to add to config/app.php
if ! grep -q "App\Custom\CustomizationServiceProvider::class" config/app.php; then
    # Create a safe backup
    cp config/app.php config/app.php.backup-step17
    
    # Add the service provider before the Application Service Providers comment
    sed -i.tmp '/Application Service Providers.../i\
        App\\Custom\\CustomizationServiceProvider::class,' config/app.php
    
    echo "✅ CustomizationServiceProvider registered in config/app.php"
else
    echo "ℹ️ CustomizationServiceProvider already registered"
fi
```

---

## **Phase 2: Configuration Layer Protection**

### **2.1 Create Custom Configuration System**

```bash
echo "⚙️ Phase 2.1: Creating custom configuration layer..."

# Create custom configuration directory
mkdir -p app/Custom/config

# Create custom app configuration extensions
cat > app/Custom/config/custom-app.php << 'EOF'
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Application Settings
    |--------------------------------------------------------------------------
    | These settings extend the base application configuration without
    | modifying vendor files. They will persist through updates.
    */

    'name' => env('CUSTOM_APP_NAME', config('app.name')),
    
    'branding' => [
        'logo' => env('CUSTOM_LOGO_PATH', '/Custom/images/logo.png'),
        'favicon' => env('CUSTOM_FAVICON_PATH', '/Custom/images/favicon.ico'),
        'theme_color' => env('CUSTOM_THEME_COLOR', '#3490dc'),
    ],
    
    'features' => [
        'custom_dashboard' => env('CUSTOM_DASHBOARD_ENABLED', false),
        'custom_auth' => env('CUSTOM_AUTH_ENABLED', false),
        'custom_notifications' => env('CUSTOM_NOTIFICATIONS_ENABLED', false),
        'saas_mode' => env('SAAS_MODE_ENABLED', false),
    ],
    
    'integrations' => [
        'stripe' => env('CUSTOM_STRIPE_ENABLED', false),
        'paypal' => env('CUSTOM_PAYPAL_ENABLED', false),
        'mailchimp' => env('CUSTOM_MAILCHIMP_ENABLED', false),
        'analytics' => env('CUSTOM_ANALYTICS_ENABLED', false),
    ],
    
    'limits' => [
        'max_users' => env('CUSTOM_MAX_USERS', 1000),
        'max_storage_mb' => env('CUSTOM_MAX_STORAGE_MB', 1024),
        'api_rate_limit' => env('CUSTOM_API_RATE_LIMIT', 60),
    ],
];
EOF

# Create custom database configuration
cat > app/Custom/config/custom-database.php << 'EOF'
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
            'unix_socket' => env('CUSTOM_ANALYTICS_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'custom_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    
    'custom_tables' => [
        'prefix' => env('CUSTOM_TABLE_PREFIX', 'custom_'),
        'suffix' => env('CUSTOM_TABLE_SUFFIX', ''),
    ],
];
EOF

# Create environment extensions
cat >> .env.local << 'EOF'

# Custom Configuration Settings
CUSTOM_APP_NAME="SocietyPal Pro"
CUSTOM_LOGO_PATH="/Custom/images/logo.png"
CUSTOM_FAVICON_PATH="/Custom/images/favicon.ico"
CUSTOM_THEME_COLOR="#6366f1"

# Custom Features
CUSTOM_DASHBOARD_ENABLED=true
CUSTOM_AUTH_ENABLED=false
CUSTOM_NOTIFICATIONS_ENABLED=true
SAAS_MODE_ENABLED=false

# Custom Integrations
CUSTOM_STRIPE_ENABLED=false
CUSTOM_PAYPAL_ENABLED=false
CUSTOM_MAILCHIMP_ENABLED=false
CUSTOM_ANALYTICS_ENABLED=true

# Custom Limits
CUSTOM_MAX_USERS=1000
CUSTOM_MAX_STORAGE_MB=2048
CUSTOM_API_RATE_LIMIT=120

# Custom Database
CUSTOM_TABLE_PREFIX=custom_
CUSTOM_ANALYTICS_DATABASE_URL=
EOF

echo "✅ Custom configuration system created"
```

---

## **Phase 3: Asset Customization System**

### **3.1 Create Custom Asset Pipeline**

```bash
echo "🎨 Phase 3.1: Setting up custom asset pipeline..."

# Create custom webpack configuration
cat > webpack.custom.js << 'EOF'
const mix = require('laravel-mix');
const path = require('path');

/*
|--------------------------------------------------------------------------
| Custom Asset Compilation
|--------------------------------------------------------------------------
| This configuration handles custom assets separately from vendor assets,
| ensuring they don't get overwritten during updates.
*/

// Custom CSS compilation
if (mix.inProduction()) {
    mix.sass('resources/Custom/css/app.scss', 'public/Custom/css/app.css')
       .options({
           processCssUrls: false,
           postCss: [require('autoprefixer')]
       });
} else {
    mix.sass('resources/Custom/css/app.scss', 'public/Custom/css/app.css');
}

// Custom JavaScript compilation
mix.js('resources/Custom/js/app.js', 'public/Custom/js/app.js');

// Custom Vue.js components (if needed)
if (fs.existsSync('resources/Custom/js/components')) {
    mix.js('resources/Custom/js/components.js', 'public/Custom/js/components.js');
}

// Version files for cache busting
if (mix.inProduction()) {
    mix.version(['public/Custom/css/app.css', 'public/Custom/js/app.js']);
}

// Custom asset copying
mix.copyDirectory('resources/Custom/images', 'public/Custom/images');

// Source maps for development
if (!mix.inProduction()) {
    mix.sourceMaps();
}

module.exports = mix;
EOF

# Create custom SCSS structure
mkdir -p resources/Custom/css/{components,layouts,pages,utilities}

# Create main custom SCSS file
cat > resources/Custom/css/app.scss << 'EOF'
/*
|--------------------------------------------------------------------------
| Custom Application Styles
|--------------------------------------------------------------------------
| This file contains all custom styles that extend or override the base
| application. These styles will persist through vendor updates.
*/

// Custom Variables
@import 'utilities/variables';

// Custom Components
@import 'components/buttons';
@import 'components/forms';
@import 'components/modals';
@import 'components/cards';

// Custom Layouts
@import 'layouts/header';
@import 'layouts/sidebar';
@import 'layouts/footer';

// Custom Pages
@import 'pages/dashboard';
@import 'pages/auth';
@import 'pages/profile';

// Custom Utilities
@import 'utilities/helpers';
@import 'utilities/responsive';
EOF

# Create custom variables
cat > resources/Custom/css/utilities/_variables.scss << 'EOF'
/*
|--------------------------------------------------------------------------
| Custom Variables
|--------------------------------------------------------------------------
| Define custom colors, fonts, and other design tokens
*/

// Brand Colors
$custom-primary: #6366f1;
$custom-secondary: #8b5cf6;
$custom-success: #10b981;
$custom-warning: #f59e0b;
$custom-danger: #ef4444;
$custom-info: #3b82f6;

// Typography
$custom-font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
$custom-font-size-base: 1rem;
$custom-line-height-base: 1.5;

// Spacing
$custom-space-xs: 0.25rem;
$custom-space-sm: 0.5rem;
$custom-space-md: 1rem;
$custom-space-lg: 1.5rem;
$custom-space-xl: 2rem;

// Borders
$custom-border-radius: 0.375rem;
$custom-border-width: 1px;
$custom-border-color: #e5e7eb;

// Shadows
$custom-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
$custom-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
$custom-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
EOF

# Create base component styles
mkdir -p resources/Custom/css/components
touch resources/Custom/css/components/{_buttons.scss,_forms.scss,_modals.scss,_cards.scss}
mkdir -p resources/Custom/css/layouts  
touch resources/Custom/css/layouts/{_header.scss,_sidebar.scss,_footer.scss}
mkdir -p resources/Custom/css/pages
touch resources/Custom/css/pages/{_dashboard.scss,_auth.scss,_profile.scss}
mkdir -p resources/Custom/css/utilities
touch resources/Custom/css/utilities/{_helpers.scss,_responsive.scss}

# Create custom JavaScript structure
mkdir -p resources/Custom/js/{components,utilities,pages}

# Create main custom JS file
cat > resources/Custom/js/app.js << 'EOF'
/*
|--------------------------------------------------------------------------
| Custom Application JavaScript
|--------------------------------------------------------------------------
| This file contains custom JavaScript that extends the base application.
| It loads after vendor scripts and will persist through updates.
*/

// Import custom utilities
import './utilities/helpers';
import './utilities/api';
import './utilities/notifications';

// Import custom components
import './components/customDashboard';
import './components/customAuth';
import './components/customModals';

// Import custom page scripts
import './pages/dashboard';
import './pages/profile';

// Custom application initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom SocietyPal initialization');
    
    // Initialize custom features based on configuration
    if (window.CustomConfig && window.CustomConfig.features) {
        const features = window.CustomConfig.features;
        
        if (features.custom_dashboard) {
            initCustomDashboard();
        }
        
        if (features.custom_notifications) {
            initCustomNotifications();
        }
        
        if (features.saas_mode) {
            initSaaSMode();
        }
    }
});

// Custom feature initializers
function initCustomDashboard() {
    console.log('Initializing custom dashboard features');
    // Custom dashboard logic here
}

function initCustomNotifications() {
    console.log('Initializing custom notifications');
    // Custom notifications logic here
}

function initSaaSMode() {
    console.log('Initializing SaaS mode features');
    // SaaS-specific logic here
}

// Export for global access
window.CustomSocietyPal = {
    initCustomDashboard,
    initCustomNotifications,
    initSaaSMode
};
EOF

# Create empty component files
touch resources/Custom/js/components/{customDashboard.js,customAuth.js,customModals.js}
touch resources/Custom/js/utilities/{helpers.js,api.js,notifications.js}
touch resources/Custom/js/pages/{dashboard.js,profile.js}

echo "✅ Custom asset pipeline created"
```

### **3.2 Create Asset Integration Helper**

```bash
echo "🔗 Phase 3.2: Creating asset integration helper..."

# Create Blade helper for custom assets
mkdir -p app/Custom/Helpers

cat > app/Custom/Helpers/AssetHelper.php << 'EOF'
<?php

namespace App\Custom\Helpers;

use Illuminate\Support\Facades\File;

class AssetHelper
{
    /**
     * Get custom asset URL with version busting
     */
    public static function customAsset($path)
    {
        $manifestPath = public_path('Custom/mix-manifest.json');
        
        if (File::exists($manifestPath)) {
            $manifest = json_decode(File::get($manifestPath), true);
            $path = $manifest[$path] ?? $path;
        }
        
        return asset('Custom' . $path);
    }
    
    /**
     * Include custom CSS
     */
    public static function customCSS($file = 'app.css')
    {
        $path = "/css/{$file}";
        $url = self::customAsset($path);
        
        return "<link rel=\"stylesheet\" href=\"{$url}\">";
    }
    
    /**
     * Include custom JavaScript
     */
    public static function customJS($file = 'app.js')
    {
        $path = "/js/{$file}";
        $url = self::customAsset($path);
        
        return "<script src=\"{$url}\"></script>";
    }
    
    /**
     * Get custom image URL
     */
    public static function customImage($file)
    {
        return self::customAsset("/images/{$file}");
    }
    
    /**
     * Check if custom asset exists
     */
    public static function customAssetExists($path)
    {
        return File::exists(public_path("Custom{$path}"));
    }
    
    /**
     * Get configuration-based assets
     */
    public static function getConfigAssets()
    {
        $config = config('custom-app.branding', []);
        
        return [
            'logo' => $config['logo'] ?? '/Custom/images/logo.png',
            'favicon' => $config['favicon'] ?? '/Custom/images/favicon.ico',
        ];
    }
}
EOF

# Create Blade directive
cat > app/Custom/BladeDirectives.php << 'EOF'
<?php

namespace App\Custom;

use Illuminate\Support\Facades\Blade;
use App\Custom\Helpers\AssetHelper;

class BladeDirectives
{
    public static function register()
    {
        // Custom asset directive
        Blade::directive('customAsset', function ($expression) {
            return "<?php echo App\\Custom\\Helpers\\AssetHelper::customAsset({$expression}); ?>";
        });
        
        // Custom CSS directive
        Blade::directive('customCSS', function ($expression) {
            return "<?php echo App\\Custom\\Helpers\\AssetHelper::customCSS({$expression}); ?>";
        });
        
        // Custom JS directive
        Blade::directive('customJS', function ($expression) {
            return "<?php echo App\\Custom\\Helpers\\AssetHelper::customJS({$expression}); ?>";
        });
        
        // Custom image directive
        Blade::directive('customImage', function ($expression) {
            return "<?php echo App\\Custom\\Helpers\\AssetHelper::customImage({$expression}); ?>";
        });
        
        // Configuration-based feature checks
        Blade::directive('customFeature', function ($expression) {
            return "<?php if(config('custom-app.features.{$expression}')): ?>";
        });
        
        Blade::directive('endcustomFeature', function () {
            return "<?php endif; ?>";
        });
    }
}
EOF

echo "✅ Asset integration helper created"
```

---

## **Phase 4: Database Customization Protection**

### **4.1 Create Custom Migration System**

```bash
echo "🗄️ Phase 4.1: Setting up custom database system..."

# Create custom migration base class
cat > app/Custom/Database/CustomMigration.php << 'EOF'
<?php

namespace App\Custom\Database;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class CustomMigration extends Migration
{
    /**
     * Get the custom table prefix
     */
    protected function getCustomPrefix(): string
    {
        return config('custom-database.custom_tables.prefix', 'custom_');
    }
    
    /**
     * Get a prefixed table name
     */
    protected function customTable(string $name): string
    {
        return $this->getCustomPrefix() . $name;
    }
    
    /**
     * Create a custom table
     */
    protected function createCustomTable(string $name, callable $callback): void
    {
        Schema::create($this->customTable($name), function (Blueprint $table) use ($callback) {
            $table->id();
            $callback($table);
            $table->timestamps();
        });
    }
    
    /**
     * Drop a custom table
     */
    protected function dropCustomTable(string $name): void
    {
        Schema::dropIfExists($this->customTable($name));
    }
    
    /**
     * Check if vendor table exists (for safe extending)
     */
    protected function vendorTableExists(string $name): bool
    {
        return Schema::hasTable($name);
    }
    
    /**
     * Safely add columns to vendor table
     */
    protected function extendVendorTable(string $tableName, callable $callback): void
    {
        if ($this->vendorTableExists($tableName)) {
            Schema::table($tableName, $callback);
        }
    }
}
EOF

# Create example custom migration
cat > database/Custom/migrations/2024_01_01_000000_create_custom_settings_table.php << 'EOF'
<?php

use App\Custom\Database\CustomMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends CustomMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createCustomTable('settings', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->boolean('is_public')->default(false);
            $table->json('metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropCustomTable('settings');
    }
};
EOF

# Create example vendor table extension migration
cat > database/Custom/migrations/2024_01_01_000001_extend_users_table.php << 'EOF'
<?php

use App\Custom\Database\CustomMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends CustomMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely extend the vendor users table
        $this->extendVendorTable('users', function (Blueprint $table) {
            $table->json('custom_preferences')->nullable();
            $table->string('custom_plan')->nullable();
            $table->timestamp('custom_trial_ends_at')->nullable();
            $table->boolean('custom_is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->extendVendorTable('users', function (Blueprint $table) {
            $table->dropColumn([
                'custom_preferences',
                'custom_plan',
                'custom_trial_ends_at',
                'custom_is_admin'
            ]);
        });
    }
};
EOF

echo "✅ Custom migration system created"
```

### **4.2 Create Custom Model Base Classes**

```bash
echo "📊 Phase 4.2: Creating custom model base classes..."

# Create custom model base class
cat > app/Custom/Models/CustomModel.php << 'EOF'
<?php

namespace App\Custom\Models;

use Illuminate\Database\Eloquent\Model;

abstract class CustomModel extends Model
{
    /**
     * Get the custom table prefix
     */
    protected function getCustomPrefix(): string
    {
        return config('custom-database.custom_tables.prefix', 'custom_');
    }
    
    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return $this->table ?? $this->getCustomPrefix() . str_replace(
            '\\', '', snake_case(str_replace($this->getCustomNamespace(), '', get_class($this)))
        );
    }
    
    /**
     * Get the custom namespace
     */
    protected function getCustomNamespace(): string
    {
        return 'App\\Custom\\Models\\';
    }
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Add global scopes for custom models
        static::addGlobalScope('custom', function ($query) {
            // Add any global custom logic here
        });
    }
}
EOF

# Create custom settings model
cat > app/Custom/Models/CustomSetting.php << 'EOF'
<?php

namespace App\Custom\Models;

class CustomSetting extends CustomModel
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'is_public',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean'
    ];
    
    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return static::castValue($setting->value, $setting->type);
    }
    
    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', bool $isPublic = false): bool
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'is_public' => $isPublic
            ]
        ) ? true : false;
    }
    
    /**
     * Cast value to appropriate type
     */
    protected static function castValue($value, string $type)
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array' => json_decode($value, true),
            'json' => json_decode($value),
            default => $value
        };
    }
    
    /**
     * Get public settings (safe for frontend)
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->pluck('value', 'key')
            ->toArray();
    }
}
EOF

# Create vendor model extension trait
cat > app/Custom/Traits/HasCustomFields.php << 'EOF'
<?php

namespace App\Custom\Traits;

trait HasCustomFields
{
    /**
     * Get a custom field value
     */
    public function getCustomField(string $key, $default = null)
    {
        $customData = $this->custom_preferences ?? [];
        
        return data_get($customData, $key, $default);
    }
    
    /**
     * Set a custom field value
     */
    public function setCustomField(string $key, $value): bool
    {
        $customData = $this->custom_preferences ?? [];
        data_set($customData, $key, $value);
        
        $this->custom_preferences = $customData;
        
        return $this->save();
    }
    
    /**
     * Remove a custom field
     */
    public function removeCustomField(string $key): bool
    {
        $customData = $this->custom_preferences ?? [];
        
        if (isset($customData[$key])) {
            unset($customData[$key]);
            $this->custom_preferences = $customData;
            return $this->save();
        }
        
        return true;
    }
    
    /**
     * Get all custom fields
     */
    public function getCustomFields(): array
    {
        return $this->custom_preferences ?? [];
    }
    
    /**
     * Cast custom preferences attribute
     */
    public function getCustomPreferencesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
    
    /**
     * Set custom preferences attribute
     */
    public function setCustomPreferencesAttribute($value)
    {
        $this->attributes['custom_preferences'] = $value ? json_encode($value) : null;
    }
}
EOF

echo "✅ Custom model base classes created"
```

---

## **Phase 5: Update-Safe Customization Patterns**

### **5.1 Create Customization Testing System**

```bash
echo "🧪 Phase 5.1: Creating customization testing system..."

# Create custom test base class
cat > tests/Custom/CustomTestCase.php << 'EOF'
<?php

namespace Tests\Custom;

use Tests\TestCase;
use App\Custom\Models\CustomSetting;

abstract class CustomTestCase extends TestCase
{
    /**
     * Setup custom testing environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run custom migrations
        $this->artisan('migrate:fresh', [
            '--path' => 'database/Custom/migrations'
        ]);
        
        // Seed custom data
        $this->seedCustomData();
    }
    
    /**
     * Seed custom test data
     */
    protected function seedCustomData(): void
    {
        CustomSetting::create([
            'key' => 'test_setting',
            'value' => 'test_value',
            'type' => 'string',
            'is_public' => false
        ]);
    }
    
    /**
     * Assert custom functionality works
     */
    protected function assertCustomFunctionality(): void
    {
        $this->assertTrue(true, 'Custom functionality test base');
    }
    
    /**
     * Test vendor compatibility
     */
    protected function assertVendorCompatibility(): void
    {
        // Test that custom code doesn't break vendor functionality
        $this->assertTrue(
            class_exists('App\\User') || class_exists('App\\Models\\User'),
            'Vendor User model should exist'
        );
    }
}
EOF

# Create customization resilience tests
cat > tests/Custom/CustomizationResilienceTest.php << 'EOF'
<?php

namespace Tests\Custom;

use App\Custom\Models\CustomSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomizationResilienceTest extends CustomTestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function custom_settings_work_correctly()
    {
        // Test setting values
        $this->assertTrue(CustomSetting::set('test_key', 'test_value'));
        
        // Test getting values
        $this->assertEquals('test_value', CustomSetting::get('test_key'));
        
        // Test default values
        $this->assertEquals('default', CustomSetting::get('nonexistent', 'default'));
    }
    
    /** @test */
    public function custom_configurations_load_properly()
    {
        $this->assertIsArray(config('custom-app'));
        $this->assertArrayHasKey('branding', config('custom-app'));
        $this->assertArrayHasKey('features', config('custom-app'));
    }
    
    /** @test */
    public function custom_service_provider_registers_correctly()
    {
        $providers = app()->getLoadedProviders();
        $this->assertArrayHasKey('App\\Custom\\CustomizationServiceProvider', $providers);
    }
    
    /** @test */
    public function custom_assets_are_accessible()
    {
        // Test asset helper
        $assetUrl = \App\Custom\Helpers\AssetHelper::customAsset('/css/app.css');
        $this->assertStringContains('Custom/css/app.css', $assetUrl);
    }
    
    /** @test */
    public function vendor_functionality_remains_intact()
    {
        $this->assertVendorCompatibility();
        
        // Add specific vendor function tests here
        // Example: Test that original routes still work
        // Example: Test that original models still function
    }
    
    /** @test */
    public function custom_database_migrations_work()
    {
        // Test custom table exists
        $this->assertTrue(\Schema::hasTable('custom_settings'));
        
        // Test custom columns were added to vendor tables
        if (\Schema::hasTable('users')) {
            $this->assertTrue(\Schema::hasColumn('users', 'custom_preferences'));
        }
    }
}
EOF

echo "✅ Customization testing system created"
```

### **5.2 Create Update Verification Script**

```bash
echo "🔍 Phase 5.2: Creating update verification script..."

# Create update verification script
cat > app/Custom/Scripts/verify-customizations.php << 'EOF'
#!/usr/bin/env php
<?php

/**
 * Customization Verification Script
 * 
 * This script verifies that all customizations are intact and working
 * after a vendor update. Run this after every CodeCanyon update.
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class CustomizationVerifier
{
    private $errors = [];
    private $warnings = [];
    private $successes = [];
    
    public function verify()
    {
        echo "🔍 Verifying customizations after vendor update...\n\n";
        
        $this->verifyDirectoryStructure();
        $this->verifyConfigurations();
        $this->verifyAssets();
        $this->verifyDatabase();
        $this->verifyServiceProviders();
        $this->runCustomTests();
        
        $this->displayResults();
        
        return count($this->errors) === 0;
    }
    
    private function verifyDirectoryStructure()
    {
        echo "📁 Verifying directory structure...\n";
        
        $requiredDirs = [
            'app/Custom',
            'resources/Custom',
            'database/Custom/migrations',
            'config/Custom',
            'routes/Custom',
            'public/Custom',
            'storage/Custom'
        ];
        
        foreach ($requiredDirs as $dir) {
            if (is_dir(base_path($dir))) {
                $this->successes[] = "Directory exists: {$dir}";
            } else {
                $this->errors[] = "Missing directory: {$dir}";
            }
        }
    }
    
    private function verifyConfigurations()
    {
        echo "⚙️ Verifying configurations...\n";
        
        $configFiles = [
            'app/Custom/config/custom-app.php',
            'app/Custom/config/custom-database.php'
        ];
        
        foreach ($configFiles as $file) {
            if (file_exists(base_path($file))) {
                $this->successes[] = "Config file exists: {$file}";
            } else {
                $this->errors[] = "Missing config file: {$file}";
            }
        }
        
        // Test if configurations load properly
        try {
            $customApp = config('custom-app');
            if ($customApp) {
                $this->successes[] = "Custom app config loads successfully";
            } else {
                $this->warnings[] = "Custom app config is empty";
            }
        } catch (Exception $e) {
            $this->errors[] = "Custom app config failed to load: " . $e->getMessage();
        }
    }
    
    private function verifyAssets()
    {
        echo "🎨 Verifying custom assets...\n";
        
        $assetDirs = [
            'public/Custom/css',
            'public/Custom/js',
            'public/Custom/images'
        ];
        
        foreach ($assetDirs as $dir) {
            if (is_dir(base_path($dir))) {
                $this->successes[] = "Asset directory exists: {$dir}";
            } else {
                $this->warnings[] = "Asset directory missing (may need rebuild): {$dir}";
            }
        }
        
        // Check if asset helper works
        try {
            $assetUrl = \App\Custom\Helpers\AssetHelper::customAsset('/css/app.css');
            if ($assetUrl) {
                $this->successes[] = "Asset helper working correctly";
            }
        } catch (Exception $e) {
            $this->errors[] = "Asset helper failed: " . $e->getMessage();
        }
    }
    
    private function verifyDatabase()
    {
        echo "🗄️ Verifying database customizations...\n";
        
        try {
            // Check custom tables
            if (\Schema::hasTable('custom_settings')) {
                $this->successes[] = "Custom settings table exists";
            } else {
                $this->errors[] = "Custom settings table missing";
            }
            
            // Check vendor table extensions
            if (\Schema::hasTable('users') && \Schema::hasColumn('users', 'custom_preferences')) {
                $this->successes[] = "Users table custom columns exist";
            } else {
                $this->warnings[] = "Users table custom columns may be missing";
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Database verification failed: " . $e->getMessage();
        }
    }
    
    private function verifyServiceProviders()
    {
        echo "🔌 Verifying service providers...\n";
        
        $providers = app()->getLoadedProviders();
        if (isset($providers['App\\Custom\\CustomizationServiceProvider'])) {
            $this->successes[] = "CustomizationServiceProvider is loaded";
        } else {
            $this->errors[] = "CustomizationServiceProvider is not loaded";
        }
    }
    
    private function runCustomTests()
    {
        echo "🧪 Running custom tests...\n";
        
        try {
            // Run a quick test of custom functionality
            $setting = \App\Custom\Models\CustomSetting::get('test_verification');
            \App\Custom\Models\CustomSetting::set('test_verification', 'working');
            $verify = \App\Custom\Models\CustomSetting::get('test_verification');
            
            if ($verify === 'working') {
                $this->successes[] = "Custom models working correctly";
            } else {
                $this->errors[] = "Custom models not working correctly";
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Custom test failed: " . $e->getMessage();
        }
    }
    
    private function displayResults()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "CUSTOMIZATION VERIFICATION RESULTS\n";
        echo str_repeat("=", 60) . "\n\n";
        
        if (!empty($this->successes)) {
            echo "✅ SUCCESSES:\n";
            foreach ($this->successes as $success) {
                echo "   • {$success}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo