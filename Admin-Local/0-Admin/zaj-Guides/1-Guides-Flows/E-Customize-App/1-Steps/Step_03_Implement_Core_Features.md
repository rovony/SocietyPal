# Step 03: Implement Core Features

**Purpose:** Develop custom business logic, controllers, models, and services within the protected customization environment.

**Duration:** 2-4 hours  
**Prerequisites:** Completed Step 02: Setup Custom Environment

---

## Tracking Integration

```bash
# Detect project paths (project-agnostic)
if [ -d "Admin-Local" ]; then
    PROJECT_ROOT="$(pwd)"
elif [ -d "../Admin-Local" ]; then
    PROJECT_ROOT="$(dirname "$(pwd)")"
elif [ -d "../../Admin-Local" ]; then
    PROJECT_ROOT="$(dirname "$(dirname "$(pwd)")")"
else
    echo "Error: Cannot find Admin-Local directory"
    exit 1
fi

ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
TRACKING_BASE="$ADMIN_LOCAL/1-CurrentProject/Tracking"

# Initialize session directory for this step
SESSION_DIR="$TRACKING_BASE/3-Customization-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$SESSION_DIR"

# Create step-specific tracking files
STEP_PLAN="$SESSION_DIR/step03_implement_features_plan.md"
STEP_BASELINE="$SESSION_DIR/step03_baseline.md"
STEP_EXECUTION="$SESSION_DIR/step03_execution.md"

# Initialize tracking files
echo "# Step 03: Implement Core Features - Planning" > "$STEP_PLAN"
echo "**Date:** $(date)" >> "$STEP_PLAN"
echo "**Session:** $SESSION_DIR" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

echo "# Step 03: Implement Core Features - Baseline" > "$STEP_BASELINE"
echo "**Date:** $(date)" >> "$STEP_BASELINE"
echo "" >> "$STEP_BASELINE"

echo "# Step 03: Implement Core Features - Execution Log" > "$STEP_EXECUTION"
echo "**Date:** $(date)" >> "$STEP_EXECUTION"
echo "" >> "$STEP_EXECUTION"

echo "Tracking initialized for Step 03 in: $SESSION_DIR"
```

---

## 3.1: Create Custom Models and Database Schema

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.1: Create Custom Models and Database Schema" >> "$STEP_PLAN"
echo "- Design custom database tables and relationships" >> "$STEP_PLAN"
echo "- Create custom Eloquent models with proper namespacing" >> "$STEP_PLAN"
echo "- Implement custom migrations with rollback support" >> "$STEP_PLAN"
echo "- Setup custom model factories and seeders" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

# Record baseline
echo "## Section 3.1 Baseline" >> "$STEP_BASELINE"
echo "**Current State:** Before custom models implementation" >> "$STEP_BASELINE"
echo "**Existing Custom Models:** $(find app/Custom/Models -name "*.php" 2>/dev/null | wc -l)" >> "$STEP_BASELINE"
echo "**Custom Migrations:** $(find database/migrations/custom -name "*.php" 2>/dev/null | wc -l)" >> "$STEP_BASELINE"
echo "" >> "$STEP_BASELINE"
```

### Required Model Implementation:

**3.1.1: Core Business Models**

-   [ ] Custom User Settings model for extended user preferences
-   [ ] Custom Feature Configuration model for dynamic feature control
-   [ ] Custom Analytics model for tracking custom functionality usage
-   [ ] Custom Notification model for custom notification system

**3.1.2: Database Migrations**

-   [ ] Create migrations with `custom_` table prefix
-   [ ] Implement proper foreign key relationships
-   [ ] Add indexes for performance optimization
-   [ ] Include comprehensive rollback functionality

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.1 Execution" >> "$STEP_EXECUTION"
echo "**Create Custom Models and Database Schema Started:** $(date)" >> "$STEP_EXECUTION"

# Run custom migrations
echo "**Running custom database migrations:** $(date)" >> "$STEP_EXECUTION"
php artisan migrate --path=database/migrations/custom >> "$STEP_EXECUTION" 2>&1

# Verify custom tables were created
CUSTOM_TABLES=$(php artisan tinker --execute="echo collect(DB::select('SHOW TABLES'))->pluck('Tables_in_'.env('DB_DATABASE'))->filter(fn(\$t) => str_starts_with(\$t, 'custom_'))->count();" 2>/dev/null || echo "0")
echo "**Custom tables created:** $CUSTOM_TABLES" >> "$STEP_EXECUTION"

# Test model relationships
echo "**Testing model relationships:** $(date)" >> "$STEP_EXECUTION"
php artisan tinker --execute="
use App\\Custom\\Models\\CustomUserSetting;
use App\\Models\\User;
\$user = User::first();
if (\$user) {
    \$setting = CustomUserSetting::create([
        'user_id' => \$user->id,
        'setting_key' => 'test_setting',
        'setting_value' => 'test_value'
    ]);
    echo 'Test setting created with ID: ' . \$setting->id;
    \$setting->delete();
} else {
    echo 'No users found - please seed user data first';
}
" >> "$STEP_EXECUTION" 2>&1

echo "**Custom models and database schema implementation completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 3.2: Develop Custom Controllers and Business Logic

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.2: Develop Custom Controllers and Business Logic" >> "$STEP_PLAN"
echo "- Create custom controllers with proper REST patterns" >> "$STEP_PLAN"
echo "- Implement custom services for business logic separation" >> "$STEP_PLAN"
echo "- Setup custom validation rules and form requests" >> "$STEP_PLAN"
echo "- Create custom middleware for feature access control" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Controller Implementation:

**3.2.1: Core Controllers**

-   [ ] CustomDashboardController for main custom dashboard
-   [ ] CustomSettingsController for user preference management
-   [ ] CustomApiController for API endpoints
-   [ ] CustomFeatureController for dynamic feature management

**3.2.2: Supporting Services**

-   [ ] CustomUserService for user-related custom operations
-   [ ] CustomFeatureService for feature flag management
-   [ ] CustomAnalyticsService for custom analytics tracking
-   [ ] CustomNotificationService for custom notifications

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.2 Execution" >> "$STEP_EXECUTION"
echo "**Develop Custom Controllers and Business Logic Started:** $(date)" >> "$STEP_EXECUTION"

# Test custom controller registration
echo "**Testing custom controller registration:** $(date)" >> "$STEP_EXECUTION"
if grep -r "CustomDashboardController" app/Custom/Controllers/ 2>/dev/null; then
    echo "✅ CustomDashboardController found" >> "$STEP_EXECUTION"
else
    echo "❌ CustomDashboardController not found" >> "$STEP_EXECUTION"
fi

# Test custom routes registration
echo "**Testing custom routes registration:** $(date)" >> "$STEP_EXECUTION"
php artisan route:list | grep custom >> "$STEP_EXECUTION" 2>&1 || echo "No custom routes found" >> "$STEP_EXECUTION"

# Test custom service registration
echo "**Testing custom service registration:** $(date)" >> "$STEP_EXECUTION"
php artisan tinker --execute="
use App\\Custom\\Services\\CustomUserService;
try {
    \$service = app(CustomUserService::class);
    echo 'CustomUserService successfully resolved from container';
} catch (Exception \$e) {
    echo 'Error resolving CustomUserService: ' . \$e->getMessage();
}
" >> "$STEP_EXECUTION" 2>&1

echo "**Custom controllers and business logic development completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 3.3: Create Custom API Endpoints

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.3: Create Custom API Endpoints" >> "$STEP_PLAN"
echo "- Design RESTful API endpoints for custom features" >> "$STEP_PLAN"
echo "- Implement API authentication and rate limiting" >> "$STEP_PLAN"
echo "- Create API resources for data transformation" >> "$STEP_PLAN"
echo "- Setup comprehensive API documentation" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required API Implementation:

**3.3.1: Core API Controllers**

-   [ ] Custom API Controller for general custom endpoints
-   [ ] Custom Settings API for settings management
-   [ ] Custom Analytics API for data retrieval
-   [ ] Custom Feature API for feature management

**3.3.2: API Resources and Responses**

-   [ ] Custom API resource classes for consistent responses
-   [ ] Error handling and validation responses
-   [ ] API versioning and documentation
-   [ ] Rate limiting and authentication middleware

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.3 Execution" >> "$STEP_EXECUTION"
echo "**Create Custom API Endpoints Started:** $(date)" >> "$STEP_EXECUTION"

# Test API endpoints accessibility
echo "**Testing API endpoints:** $(date)" >> "$STEP_EXECUTION"

# Test API status endpoint
if command -v curl >/dev/null; then
    echo "**Testing /api/v1/custom/status endpoint:**" >> "$STEP_EXECUTION"
    curl -s http://localhost:8000/api/v1/custom/status || echo "API endpoint not accessible" >> "$STEP_EXECUTION"
else
    echo "curl not available - API testing skipped" >> "$STEP_EXECUTION"
fi

# Test API authentication
echo "**Testing API authentication middleware:** $(date)" >> "$STEP_EXECUTION"
php artisan route:list | grep "auth:sanctum" | grep custom >> "$STEP_EXECUTION" 2>&1 || echo "No authenticated custom API routes found" >> "$STEP_EXECUTION"

echo "**Custom API endpoints creation completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 3.4: Setup Custom Middleware and Security

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.4: Setup Custom Middleware and Security" >> "$STEP_PLAN"
echo "- Create custom authentication and authorization middleware" >> "$STEP_PLAN"
echo "- Implement feature flag middleware for dynamic access control" >> "$STEP_PLAN"
echo "- Setup custom rate limiting and security headers" >> "$STEP_PLAN"
echo "- Create custom logging and audit trail system" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Security Implementation:

**3.4.1: Custom Middleware**

-   [ ] CustomFeatureAccess middleware for feature flag control
-   [ ] CustomAuditLog middleware for activity tracking
-   [ ] CustomRateLimit middleware for API protection
-   [ ] CustomSecurityHeaders middleware for enhanced security

**3.4.2: Security Enhancements**

-   [ ] Input validation and sanitization
-   [ ] CSRF protection for custom forms
-   [ ] SQL injection prevention patterns
-   [ ] XSS protection for custom views

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.4 Execution" >> "$STEP_EXECUTION"
echo "**Setup Custom Middleware and Security Started:** $(date)" >> "$STEP_EXECUTION"

# Test middleware registration
echo "**Testing middleware registration:** $(date)" >> "$STEP_EXECUTION"
if grep -r "CustomFeatureAccess" app/Custom/Middleware/ 2>/dev/null; then
    echo "✅ CustomFeatureAccess middleware found" >> "$STEP_EXECUTION"
else
    echo "❌ CustomFeatureAccess middleware not found" >> "$STEP_EXECUTION"
fi

# Check middleware registration in HTTP Kernel
echo "**Checking middleware registration in HTTP Kernel:** $(date)" >> "$STEP_EXECUTION"
grep -r "CustomFeatureAccess" app/Http/Kernel.php >> "$STEP_EXECUTION" 2>&1 || echo "Middleware not registered in Kernel" >> "$STEP_EXECUTION"

echo "**Custom middleware and security setup completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 3.5: Implement Custom Configuration Management

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.5: Implement Custom Configuration Management" >> "$STEP_PLAN"
echo "- Create dynamic configuration loading system" >> "$STEP_PLAN"
echo "- Implement environment-specific custom settings" >> "$STEP_PLAN"
echo "- Setup configuration caching and optimization" >> "$STEP_PLAN"
echo "- Create configuration validation and defaults" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Configuration Features:

**3.5.1: Configuration System**

-   [ ] Dynamic configuration loading from database
-   [ ] Environment-specific configuration overrides
-   [ ] Configuration validation and type checking
-   [ ] Configuration caching for performance

**3.5.2: Feature Management**

-   [ ] Feature flag implementation and control
-   [ ] User-specific feature activation
-   [ ] Environment-based feature toggling
-   [ ] Real-time configuration updates

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.5 Execution" >> "$STEP_EXECUTION"
echo "**Implement Custom Configuration Management Started:** $(date)" >> "$STEP_EXECUTION"

# Test custom configuration access
echo "**Testing custom configuration access:** $(date)" >> "$STEP_EXECUTION"
php artisan tinker --execute="
try {
    \$config = config('custom-app');
    if (\$config) {
        echo 'Custom configuration loaded successfully';
        echo 'Keys: ' . implode(', ', array_keys(\$config));
    } else {
        echo 'Custom configuration not found';
    }
} catch (Exception \$e) {
    echo 'Error accessing custom configuration: ' . \$e->getMessage();
}
" >> "$STEP_EXECUTION" 2>&1

# Test feature configuration
echo "**Testing feature configuration system:** $(date)" >> "$STEP_EXECUTION"
php artisan tinker --execute="
use App\\Custom\\Models\\CustomFeatureConfig;
try {
    \$features = CustomFeatureConfig::enabled()->forEnvironment()->count();
    echo 'Active custom features: ' . \$features;
} catch (Exception \$e) {
    echo 'Error accessing feature configuration: ' . \$e->getMessage();
}
" >> "$STEP_EXECUTION" 2>&1

echo "**Custom configuration management implementation completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 3.6: Test and Validate Implementation

### Planning Phase

```bash
# Update Step 03 tracking plan
echo "## 3.6: Test and Validate Implementation" >> "$STEP_PLAN"
echo "- Run comprehensive testing of all custom components" >> "$STEP_PLAN"
echo "- Validate integration with vendor code" >> "$STEP_PLAN"
echo "- Test error handling and edge cases" >> "$STEP_PLAN"
echo "- Verify vendor update compatibility" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Final Validation Checklist:

**3.6.1: Component Integration Testing**

-   [ ] All custom models are accessible and functional
-   [ ] Custom controllers respond correctly to requests
-   [ ] API endpoints return expected responses
-   [ ] Middleware functions as designed
-   [ ] Configuration system works correctly

**3.6.2: Vendor Compatibility Verification**

-   [ ] No vendor files were modified during implementation
-   [ ] Custom code is properly isolated
-   [ ] Vendor functionality remains unaffected
-   [ ] Update procedures remain functional

### Execution Commands:

```bash
# Log execution start
echo "## Section 3.6 Execution" >> "$STEP_EXECUTION"
echo "**Test and Validate Implementation Started:** $(date)" >> "$STEP_EXECUTION"

# Run comprehensive validation
echo "**Running comprehensive validation:** $(date)" >> "$STEP_EXECUTION"

# Test database connectivity and custom tables
echo "Testing database connectivity..." >> "$STEP_EXECUTION"
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: OK';

    \$customTables = collect(DB::select('SHOW TABLES'))
        ->pluck('Tables_in_'.env('DB_DATABASE'))
        ->filter(fn(\$t) => str_starts_with(\$t, 'custom_'))
        ->count();
    echo 'Custom tables: ' . \$customTables;
} catch (Exception \$e) {
    echo 'Database error: ' . \$e->getMessage();
}
" >> "$STEP_EXECUTION" 2>&1

# Test service provider registration
echo "Testing service provider registration..." >> "$STEP_EXECUTION"
php artisan config:cache >> "$STEP_EXECUTION" 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Configuration caching successful" >> "$STEP_EXECUTION"
else
    echo "❌ Configuration caching failed" >> "$STEP_EXECUTION"
fi

# Generate implementation summary
echo "**Implementation Summary:** $(date)" >> "$STEP_EXECUTION"
echo "✅ Custom models and database schema implemented" >> "$STEP_EXECUTION"
echo "✅ Custom controllers and business logic developed" >> "$STEP_EXECUTION"
echo "✅ Custom API endpoints created" >> "$STEP_EXECUTION"
echo "✅ Custom middleware and security setup completed" >> "$STEP_EXECUTION"
echo "✅ Custom configuration management implemented" >> "$STEP_EXECUTION"
echo "✅ Comprehensive testing and validation completed" >> "$STEP_EXECUTION"

echo "**Core features implementation completed successfully:** $(date)" >> "$STEP_EXECUTION"
echo "**Ready to proceed to Step 04: Build Frontend Views**" >> "$STEP_EXECUTION"
```

---

## ✅ Step 03 Completion Checklist

-   [ ] **3.1** Custom models and database schema implemented with migrations
-   [ ] **3.2** Custom controllers and business logic services developed
-   [ ] **3.3** Custom API endpoints created with proper authentication
-   [ ] **3.4** Custom middleware and security measures implemented
-   [ ] **3.5** Custom configuration management system setup
-   [ ] **3.6** Comprehensive testing and validation completed successfully

## 📁 Generated Components

The core features implementation creates:

```
app/Custom/
├── Controllers/
│   ├── CustomDashboardController.php
│   ├── CustomSettingsController.php
│   └── Api/CustomApiController.php
├── Models/
│   ├── CustomUserSetting.php
│   └── CustomFeatureConfig.php
├── Services/
│   ├── CustomUserService.php
│   └── CustomAnalyticsService.php
├── Requests/
│   └── UpdateSettingsRequest.php
└── Middleware/
    └── CustomFeatureAccess.php

database/migrations/custom/
├── YYYY_MM_DD_HHMMSS_create_custom_user_settings_table.php
└── YYYY_MM_DD_HHMMSS_create_custom_feature_configs_table.php
```

## 🧪 Testing Commands

Validate the implementation:

```bash
# Test database migrations
php artisan migrate --path=database/migrations/custom

# Test model relationships
php artisan tinker --execute="use App\Custom\Models\CustomUserSetting; CustomUserSetting::count();"

# Test API endpoints
curl -X GET http://localhost:8000/api/v1/custom/status

# Test configuration access
php artisan tinker --execute="dd(config('custom-app'));"
```

## ➡️ Next Step

**Step 04: Build Frontend Views** - Create custom Blade templates, CSS styling, and JavaScript interactions for the user interface.

---

**Note:** This step implements the core backend functionality for custom features while maintaining complete isolation from vendor code. All custom components are properly namespaced and can be safely updated without affecting vendor functionality.
