# GitHub Copilot Instructions - Laravel Customization Protection System

## Project Overview

This is a **Laravel marketplace application** (CodeCanyon/ThemeForest type) implementing a sophisticated **three-layer customization protection system** to safeguard client investments and prevent customization loss during vendor updates.

### 🚨 CRITICAL PROJECT TYPE IDENTIFICATION

**This is a Type B Project: Marketplace Application with Customization Protection**

-   Original vendor/marketplace files MUST NEVER be modified directly
-   All customizations MUST use the protected `app/Custom/` layer
-   Updates from original vendor/author will overwrite direct modifications
-   Client customizations represent significant business value requiring protection

## Core Architecture Principles

### 🏗️ Three-Layer Protection System

1. **Vendor Layer** (PROTECTED - Never Edit)

    - `app/Http/Controllers/` - Original marketplace controllers
    - `app/Models/` - Original marketplace models
    - `resources/views/` - Original marketplace views
    - `routes/web.php` - Original marketplace routes

2. **Custom Layer** (SAFE - Always Use)

    - `app/Custom/Controllers/` - Custom controllers extending/replacing vendor
    - `app/Custom/Models/` - Custom models and relationships
    - `resources/Custom/views/` - Custom views overriding vendor
    - `app/Custom/routes/` - Custom routes with priority

3. **Integration Layer** (BRIDGE)
    - `app/Providers/CustomizationServiceProvider.php` - Service provider connecting layers
    - `config/custom.php` - Custom configuration isolated from vendor config
    - `webpack.custom.cjs` - Custom asset build system

### 🔧 CustomizationServiceProvider Integration

The `CustomizationServiceProvider` acts as the integration bridge:

-   Loads custom routes with **priority over vendor routes**
-   Registers custom view paths that **override vendor views**
-   Merges custom configurations safely
-   Enables feature toggles and custom Blade directives

## Development Rules & Patterns

### ❌ FORBIDDEN Actions

```php
// NEVER edit vendor files directly
app/Http/Controllers/UserController.php        // ❌ Vendor file
app/Models/User.php                           // ❌ Vendor file
resources/views/dashboard/index.blade.php     // ❌ Vendor file
routes/web.php                               // ❌ Vendor file
```

### ✅ REQUIRED Patterns

```php
// ALWAYS use custom layer for new features
app/Custom/Controllers/BusinessUserController.php    // ✅ Protected custom
app/Custom/Models/BusinessUser.php                  // ✅ Protected custom
resources/Custom/views/dashboard/custom.blade.php   // ✅ Protected custom
app/Custom/routes/web.php                           // ✅ Protected custom
```

### 🔄 Route Overriding Pattern

```php
// In app/Custom/routes/web.php
Route::get('/dashboard', [CustomDashboardController::class, 'index'])
    ->name('dashboard'); // This overrides vendor dashboard route
```

### 🎨 View Overriding Pattern

```php
// Custom views automatically take precedence via service provider
resources/Custom/views/dashboard/index.blade.php  // Overrides vendor view
```

### 📦 Service Integration Pattern

```php
// In CustomizationServiceProvider
$this->app->bind(UserServiceInterface::class, CustomUserService::class);
// Routes traffic from vendor interface to custom implementation
```

## Asset Management System

### 🔨 Build System Architecture

-   **Vendor Assets**: Compiled via standard Laravel Vite (`npm run build`)
-   **Custom Assets**: Compiled via custom webpack (`npm run custom:build`)
-   **Separation**: Custom assets in `public/Custom/` prevent vendor conflicts

### 📝 Custom Build Scripts

```bash
npm run custom:dev        # Development build with watch
npm run custom:build      # Production build
npm run custom:watch      # Development with file watching
npm run custom:clean      # Remove compiled custom assets
```

### 🎯 Asset Structure

```
public/
├── build/           # Vendor assets (managed by vendor)
└── Custom/          # Custom assets (update-safe)
    ├── js/
    ├── css/
    └── images/
```

## Configuration Management

### 🔧 Custom Configuration System

```php
// config/custom.php - Isolated from vendor configurations
return [
    'version' => '1.0.0',
    'features' => [
        'dashboard' => true,
        'analytics' => false,
    ],
    'branding' => [
        'company_name' => 'Client Company',
        'theme_color' => '#007bff',
    ],
];
```

### 🎛️ Feature Toggle Pattern

```blade
{{-- In views --}}
@if(config('custom.features.dashboard'))
    @include('custom::dashboard.widgets')
@endif

{{-- Using custom directive --}}
@ifCustomFeature('analytics')
    <div id="analytics-dashboard"></div>
@endifCustomFeature
```

## Database Patterns

### 📊 Custom Migrations

-   Location: `database/Custom/migrations/`
-   Auto-loaded by `CustomizationServiceProvider`
-   Isolated from vendor migrations

### 🔗 Model Extensions

```php
// app/Custom/Models/BusinessUser.php
namespace App\Custom\Models;

use App\Models\User as VendorUser;

class BusinessUser extends VendorUser
{
    // Custom functionality while inheriting vendor behavior
    protected $fillable = [...parent::$fillable, 'business_id'];

    public function businessMetrics()
    {
        // Custom relationship
        return $this->hasMany(BusinessMetric::class);
    }
}
```

## Testing & Quality Assurance

### 🧪 Testing Strategy

-   Custom functionality tests in `tests/Custom/`
-   Feature tests verify integration layer works correctly
-   Ensure vendor updates don't break custom functionality

### 📋 Quality Checklist

-   [ ] All custom code in `app/Custom/` structure
-   [ ] `CustomizationServiceProvider` properly registered
-   [ ] Custom routes override vendor routes correctly
-   [ ] Custom views render with proper data
-   [ ] Asset compilation works independently
-   [ ] Configuration loading functions correctly

## Update Safety Procedures

### 🛡️ Pre-Update Protection

1. **Backup Custom Layer**: Archive entire `app/Custom/` directory
2. **Document Customizations**: Update `CUSTOMIZATIONS.md` with business value
3. **Test Custom Layer**: Verify all custom features work independently
4. **Version Control**: Commit custom layer state before vendor update

### 🔄 Post-Update Verification

1. **Service Provider**: Ensure `CustomizationServiceProvider` still registered
2. **Route Priority**: Verify custom routes still override vendor routes
3. **View Overrides**: Confirm custom views still render correctly
4. **Asset Compilation**: Test custom asset build process
5. **Feature Functionality**: Validate all custom features work

## Common Patterns & Examples

### 🎯 Custom Controller Pattern

```php
// app/Custom/Controllers/BusinessDashboardController.php
namespace App\Custom\Controllers;

use App\Http\Controllers\Controller;
use App\Custom\Services\BusinessMetricsService;

class BusinessDashboardController extends Controller
{
    public function index(BusinessMetricsService $metrics)
    {
        $data = $metrics->getDashboardData();

        // Return custom view that overrides vendor dashboard
        return view('custom::dashboard.business', compact('data'));
    }
}
```

### 🔧 Custom Service Pattern

```php
// app/Custom/Services/BusinessMetricsService.php
namespace App\Custom\Services;

use App\Custom\Models\BusinessUser;

class BusinessMetricsService
{
    public function getDashboardData(): array
    {
        return [
            'total_users' => BusinessUser::count(),
            'active_users' => BusinessUser::active()->count(),
            // Custom business logic
        ];
    }
}
```

### 🎨 Custom Blade Components

```blade
{{-- resources/Custom/views/components/business-metric.blade.php --}}
<div class="metric-card">
    <h3>{{ $title }}</h3>
    <div class="metric-value">{{ $value }}</div>
    <span class="metric-change {{ $trend }}">{{ $change }}</span>
</div>
```

## Emergency Procedures

### 🚨 Customization Loss Recovery

1. **Identify Lost Customizations**: Check `CUSTOMIZATIONS.md` inventory
2. **Restore from Backup**: Copy from `Admin-Local/backups_local/`
3. **Re-register Service Provider**: Ensure integration layer restored
4. **Verify Functionality**: Test all custom features
5. **Document Incident**: Update protection procedures

### 🔧 Integration Layer Repair

1. **Check Service Provider Registration**: `config/app.php` or `bootstrap/providers.php`
2. **Verify File Permissions**: Ensure custom files readable
3. **Clear Application Cache**: `php artisan config:clear`
4. **Rebuild Custom Assets**: `npm run custom:setup`
5. **Test Route Priority**: Confirm custom routes work

## Performance Considerations

### ⚡ Optimization Guidelines

-   Custom routes loaded after vendor routes for proper override
-   Asset compilation separate to avoid vendor build conflicts
-   Configuration caching includes custom configs via merge strategy
-   Database queries optimized for custom relationships
-   Feature toggles prevent loading unused custom functionality

### 📊 Monitoring Points

-   Custom layer service provider boot time
-   Asset compilation performance (vendor vs custom)
-   Route resolution priority (custom overrides working)
-   Memory usage with custom services loaded
-   Database performance with custom relationships

---

## Quick Reference Commands

```bash
# Development
npm run custom:dev              # Build custom assets for development
php artisan config:clear        # Clear config cache after custom changes

# Production
npm run custom:build            # Build optimized custom assets
php artisan config:cache        # Cache including custom configurations

# Maintenance
npm run custom:clean            # Remove compiled custom assets
php artisan route:list          # Verify custom route priority

# Testing
php artisan test tests/Custom/  # Run custom functionality tests
```

**Remember**: This system protects significant client investment in customizations. Always use the custom layer, never edit vendor files directly, and maintain the integration service provider for seamless operation.
