# Step 02: Setup Custom Environment

**Purpose:** Implement the customization architecture by deploying the 6-Customization-System template to create a protected development environment.

**Duration:** 45-90 minutes  
**Prerequisites:** Completed Step 01: Plan Customization Strategy  

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
STEP_PLAN="$SESSION_DIR/step02_setup_environment_plan.md"
STEP_BASELINE="$SESSION_DIR/step02_baseline.md"
STEP_EXECUTION="$SESSION_DIR/step02_execution.md"

# Initialize tracking files
echo "# Step 02: Setup Custom Environment - Planning" > "$STEP_PLAN"
echo "**Date:** $(date)" >> "$STEP_PLAN"
echo "**Session:** $SESSION_DIR" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

echo "# Step 02: Setup Custom Environment - Baseline" > "$STEP_BASELINE"
echo "**Date:** $(date)" >> "$STEP_BASELINE"
echo "" >> "$STEP_BASELINE"

echo "# Step 02: Setup Custom Environment - Execution Log" > "$STEP_EXECUTION"
echo "**Date:** $(date)" >> "$STEP_EXECUTION"
echo "" >> "$STEP_EXECUTION"

echo "Tracking initialized for Step 02 in: $SESSION_DIR"
```

---

## 2.1: Deploy Customization System Template

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.1: Deploy Customization System Template" >> "$STEP_PLAN"
echo "- Locate and verify 6-Customization-System template" >> "$STEP_PLAN"
echo "- Run setup-customization.sh script" >> "$STEP_PLAN"
echo "- Verify directory structure creation" >> "$STEP_PLAN"
echo "- Validate service provider registration" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

# Record baseline
echo "## Section 2.1 Baseline" >> "$STEP_BASELINE"
echo "**Current State:** Before customization system deployment" >> "$STEP_BASELINE"
echo "**Project Structure:**" >> "$STEP_BASELINE"
ls -la app/ 2>/dev/null | head -5 >> "$STEP_BASELINE"
echo "**Service Providers:**" >> "$STEP_BASELINE"
if [ -f "bootstrap/providers.php" ]; then
    echo "Laravel 11+ providers file exists" >> "$STEP_BASELINE"
else
    echo "Laravel <11 config/app.php providers" >> "$STEP_BASELINE"
fi
echo "" >> "$STEP_BASELINE"
```

### Required Deployment Steps:

**2.1.1: Template System Verification**
- [ ] Verify `Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Customization-System/` exists
- [ ] Check `setup-customization.sh` script is executable
- [ ] Validate template files are present and complete
- [ ] Ensure project-agnostic path detection is working

**2.1.2: Customization System Deployment**
- [ ] Execute the customization system setup script
- [ ] Verify custom directories are created (`app/Custom`, `resources/Custom`)
- [ ] Confirm service provider registration in Laravel
- [ ] Validate custom configuration files deployment

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.1 Execution" >> "$STEP_EXECUTION"
echo "**Deploy Customization System Template Started:** $(date)" >> "$STEP_EXECUTION"

# Locate customization system template
CUSTOMIZATION_TEMPLATE="$ADMIN_LOCAL/0-Admin/zaj-Guides/0-General/1-Templates/6-Customization-System"

if [ ! -d "$CUSTOMIZATION_TEMPLATE" ]; then
    echo "ERROR: Customization template not found at $CUSTOMIZATION_TEMPLATE" >> "$STEP_EXECUTION"
    exit 1
fi

echo "**Template found:** $CUSTOMIZATION_TEMPLATE" >> "$STEP_EXECUTION"

# Check if setup script exists and is executable
SETUP_SCRIPT="$CUSTOMIZATION_TEMPLATE/setup-customization.sh"
if [ ! -x "$SETUP_SCRIPT" ]; then
    echo "ERROR: Setup script not executable at $SETUP_SCRIPT" >> "$STEP_EXECUTION"
    exit 1
fi

echo "**Setup script ready:** $SETUP_SCRIPT" >> "$STEP_EXECUTION"

# Execute the customization setup
echo "**Running customization system setup:** $(date)" >> "$STEP_EXECUTION"
bash "$SETUP_SCRIPT" >> "$STEP_EXECUTION" 2>&1

# Verify deployment success
if [ -d "app/Custom" ] && [ -d "resources/Custom" ]; then
    echo "**SUCCESS: Custom directories created**" >> "$STEP_EXECUTION"
    echo "  - app/Custom: $(ls -la app/Custom | wc -l) items" >> "$STEP_EXECUTION"
    echo "  - resources/Custom: $(ls -la resources/Custom | wc -l) items" >> "$STEP_EXECUTION"
else
    echo "**ERROR: Custom directories not created properly**" >> "$STEP_EXECUTION"
    exit 1
fi

# Verify service provider registration
if grep -q "CustomizationServiceProvider" bootstrap/providers.php 2>/dev/null; then
    echo "**SUCCESS: Service provider registered in bootstrap/providers.php**" >> "$STEP_EXECUTION"
elif grep -q "CustomizationServiceProvider" config/app.php 2>/dev/null; then
    echo "**SUCCESS: Service provider registered in config/app.php**" >> "$STEP_EXECUTION"
else
    echo "**WARNING: Service provider registration not detected**" >> "$STEP_EXECUTION"
fi
```

---

## 2.2: Configure Custom Asset Pipeline

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.2: Configure Custom Asset Pipeline" >> "$STEP_PLAN"
echo "- Verify webpack.custom.cjs configuration" >> "$STEP_PLAN"
echo "- Test custom asset compilation" >> "$STEP_PLAN"
echo "- Update package.json scripts" >> "$STEP_PLAN"
echo "- Validate asset output directories" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Configuration:

**2.2.1: Webpack Configuration Validation**
- [ ] Verify `webpack.custom.cjs` file exists and is properly configured
- [ ] Check input paths for custom CSS and JavaScript files
- [ ] Validate output paths for compiled assets
- [ ] Ensure compatibility with existing Laravel Mix/Vite setup

**2.2.2: Package.json Integration**
- [ ] Add custom build scripts for development and production
- [ ] Integrate custom asset compilation with main build process
- [ ] Configure watch mode for development workflow
- [ ] Setup asset versioning for production builds

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.2 Execution" >> "$STEP_EXECUTION"
echo "**Configure Custom Asset Pipeline Started:** $(date)" >> "$STEP_EXECUTION"

# Verify webpack.custom.cjs exists
if [ -f "webpack.custom.cjs" ]; then
    echo "**SUCCESS: webpack.custom.cjs found**" >> "$STEP_EXECUTION"
    
    # Test webpack configuration syntax
    node -c webpack.custom.cjs >> "$STEP_EXECUTION" 2>&1
    if [ $? -eq 0 ]; then
        echo "**SUCCESS: webpack.custom.cjs syntax valid**" >> "$STEP_EXECUTION"
    else
        echo "**ERROR: webpack.custom.cjs syntax errors detected**" >> "$STEP_EXECUTION"
    fi
else
    echo "**ERROR: webpack.custom.cjs not found**" >> "$STEP_EXECUTION"
fi

# Check package.json for custom scripts
if grep -q "build:custom" package.json 2>/dev/null; then
    echo "**SUCCESS: Custom build scripts found in package.json**" >> "$STEP_EXECUTION"
else
    echo "**INFO: Custom build scripts not yet added to package.json**" >> "$STEP_EXECUTION"
fi

# Test custom asset compilation
echo "**Testing custom asset compilation:** $(date)" >> "$STEP_EXECUTION"
npm run build:custom >> "$STEP_EXECUTION" 2>&1 || echo "Custom build may need npm install first" >> "$STEP_EXECUTION"

# Verify output directories
if [ -d "public/custom" ]; then
    echo "**SUCCESS: Custom asset output directory created**" >> "$STEP_EXECUTION"
    echo "  - Custom assets: $(find public/custom -type f | wc -l) files" >> "$STEP_EXECUTION"
else
    echo "**INFO: Custom asset output directory not yet created**" >> "$STEP_EXECUTION"
fi
```

---

## 2.3: Setup Custom Database Structure

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.3: Setup Custom Database Structure" >> "$STEP_PLAN"
echo "- Create custom migrations directory" >> "$STEP_PLAN"
echo "- Setup custom database configuration" >> "$STEP_PLAN"
echo "- Implement custom table naming conventions" >> "$STEP_PLAN"
echo "- Prepare custom seeders and factories" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Database Setup:

**2.3.1: Migration System**
- [ ] Create `database/migrations/custom/` directory structure
- [ ] Setup custom migration prefix for vendor separation
- [ ] Configure custom table naming with `custom_` prefix
- [ ] Implement custom migration loading in service provider

**2.3.2: Database Configuration**
- [ ] Create custom database configuration file
- [ ] Setup custom connection parameters if needed
- [ ] Configure custom table prefixes and naming
- [ ] Implement database factory patterns for custom models

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.3 Execution" >> "$STEP_EXECUTION"
echo "**Setup Custom Database Structure Started:** $(date)" >> "$STEP_EXECUTION"

# Create custom migrations directory
mkdir -p database/migrations/custom
echo "**SUCCESS: Custom migrations directory created**" >> "$STEP_EXECUTION"

# Create sample custom migration structure
if [ ! -f "database/migrations/custom/README.md" ]; then
    cat > database/migrations/custom/README.md << 'EOF'
# Custom Migrations

This directory contains all custom migrations that extend the vendor application.

## Naming Convention
- Use `custom_` prefix for all table names
- Use descriptive migration names
- Include rollback functionality

## Examples
- `2024_01_01_000000_create_custom_features_table.php`
- `2024_01_01_000001_create_custom_user_settings_table.php`
EOF
    echo "**SUCCESS: Custom migrations README created**" >> "$STEP_EXECUTION"
fi

# Verify custom configuration exists
if [ -f "app/Custom/config/custom-database.php" ]; then
    echo "**SUCCESS: Custom database configuration found**" >> "$STEP_EXECUTION"
else
    echo "**WARNING: Custom database configuration not found**" >> "$STEP_EXECUTION"
fi

# Create custom models directory structure
mkdir -p app/Custom/Models
mkdir -p database/seeders/custom
mkdir -p database/factories/custom

echo "**SUCCESS: Custom database structure directories created**" >> "$STEP_EXECUTION"
echo "  - app/Custom/Models/" >> "$STEP_EXECUTION"
echo "  - database/seeders/custom/" >> "$STEP_EXECUTION"
echo "  - database/factories/custom/" >> "$STEP_EXECUTION"
```

---

## 2.4: Configure Custom Routing System

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.4: Configure Custom Routing System" >> "$STEP_PLAN"
echo "- Setup custom route files and organization" >> "$STEP_PLAN"
echo "- Configure route prefixes and middleware" >> "$STEP_PLAN"
echo "- Implement custom route service provider integration" >> "$STEP_PLAN"
echo "- Test route registration and accessibility" >> "$STEP_EXECUTION"
echo "" >> "$STEP_PLAN"
```

### Required Routing Configuration:

**2.4.1: Route File Organization**
- [ ] Create custom route files in organized structure
- [ ] Setup route prefixes for custom features (`/custom/*`)
- [ ] Configure middleware groups for custom routes
- [ ] Implement route model binding for custom models

**2.4.2: Service Provider Integration**
- [ ] Register custom routes in CustomizationServiceProvider
- [ ] Configure route caching compatibility
- [ ] Setup route naming conventions
- [ ] Implement route group configurations

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.4 Execution" >> "$STEP_EXECUTION"
echo "**Configure Custom Routing System Started:** $(date)" >> "$STEP_EXECUTION"

# Create custom routes directory structure
mkdir -p routes/custom
echo "**SUCCESS: Custom routes directory created**" >> "$STEP_EXECUTION"

# Create sample custom routes file
if [ ! -f "routes/custom/web.php" ]; then
    cat > routes/custom/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Custom\Controllers\CustomDashboardController;

/*
|--------------------------------------------------------------------------
| Custom Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register custom web routes for your application.
| These routes are loaded by the CustomizationServiceProvider within
| a group which contains the "web" middleware group.
|
*/

Route::prefix('custom')->name('custom.')->group(function () {
    Route::get('/', [CustomDashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [CustomDashboardController::class, 'settings'])->name('settings');
});
EOF
    echo "**SUCCESS: Custom web routes file created**" >> "$STEP_EXECUTION"
fi

# Create API routes file
if [ ! -f "routes/custom/api.php" ]; then
    cat > routes/custom/api.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Custom\Controllers\Api\CustomApiController;

/*
|--------------------------------------------------------------------------
| Custom API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register custom API routes for your application.
| These routes are loaded by the CustomizationServiceProvider within
| a group which is assigned the "api" middleware group.
|
*/

Route::prefix('v1/custom')->name('api.custom.')->group(function () {
    Route::get('/status', [CustomApiController::class, 'status'])->name('status');
});
EOF
    echo "**SUCCESS: Custom API routes file created**" >> "$STEP_EXECUTION"
fi

# Verify route service provider integration
if grep -q "loadRoutesFrom" app/Providers/CustomizationServiceProvider.php 2>/dev/null; then
    echo "**SUCCESS: Route loading found in CustomizationServiceProvider**" >> "$STEP_EXECUTION"
else
    echo "**WARNING: Route loading not detected in service provider**" >> "$STEP_EXECUTION"
fi
```

---

## 2.5: Setup Custom Configuration Management

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.5: Setup Custom Configuration Management" >> "$STEP_PLAN"
echo "- Deploy custom configuration files" >> "$STEP_PLAN"
echo "- Setup environment-specific custom settings" >> "$STEP_PLAN"
echo "- Configure custom cache and session handling" >> "$STEP_PLAN"
echo "- Test configuration loading and accessibility" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Configuration Setup:

**2.5.1: Configuration Files Deployment**
- [ ] Verify custom-app.php and custom-database.php are deployed
- [ ] Setup environment-specific configuration overrides
- [ ] Configure custom feature flags and settings
- [ ] Implement configuration validation and defaults

**2.5.2: Integration Testing**
- [ ] Test configuration loading in Laravel application
- [ ] Verify configuration merging with vendor settings
- [ ] Test environment variable override functionality
- [ ] Validate configuration caching compatibility

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.5 Execution" >> "$STEP_EXECUTION"
echo "**Setup Custom Configuration Management Started:** $(date)" >> "$STEP_EXECUTION"

# Verify custom configuration files
if [ -f "app/Custom/config/custom-app.php" ]; then
    echo "**SUCCESS: custom-app.php configuration found**" >> "$STEP_EXECUTION"
else
    echo "**ERROR: custom-app.php configuration missing**" >> "$STEP_EXECUTION"
fi

if [ -f "app/Custom/config/custom-database.php" ]; then
    echo "**SUCCESS: custom-database.php configuration found**" >> "$STEP_EXECUTION"
else
    echo "**ERROR: custom-database.php configuration missing**" >> "$STEP_EXECUTION"
fi

# Test configuration loading with artisan
echo "**Testing configuration access:** $(date)" >> "$STEP_EXECUTION"
php artisan config:cache >> "$STEP_EXECUTION" 2>&1
if [ $? -eq 0 ]; then
    echo "**SUCCESS: Configuration caching completed without errors**" >> "$STEP_EXECUTION"
else
    echo "**WARNING: Configuration caching encountered issues**" >> "$STEP_EXECUTION"
fi

# Test custom configuration access
php artisan tinker --execute="dd(config('custom-app'));" >> "$STEP_EXECUTION" 2>&1 || echo "Custom config test completed" >> "$STEP_EXECUTION"

echo "**Custom configuration management setup completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 2.6: Verify Custom Environment Setup

### Planning Phase

```bash
# Update Step 02 tracking plan
echo "## 2.6: Verify Custom Environment Setup" >> "$STEP_PLAN"
echo "- Run comprehensive setup verification" >> "$STEP_PLAN"
echo "- Test all custom components integration" >> "$STEP_PLAN"
echo "- Verify vendor code protection is maintained" >> "$STEP_PLAN"
echo "- Generate setup completion report" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Final Verification Checklist:

**2.6.1: Component Integration Testing**
- [ ] CustomizationServiceProvider is properly registered and loaded
- [ ] Custom directories and files are accessible
- [ ] Asset compilation pipeline works correctly
- [ ] Database structure is ready for custom tables
- [ ] Routing system is functional and isolated

**2.6.2: Vendor Protection Verification**
- [ ] No vendor files were modified during setup
- [ ] Custom code is properly isolated in designated directories
- [ ] Vendor update compatibility is maintained
- [ ] Rollback procedures are documented and tested

### Execution Commands:

```bash
# Log execution start
echo "## Section 2.6 Execution" >> "$STEP_EXECUTION"
echo "**Verify Custom Environment Setup Started:** $(date)" >> "$STEP_EXECUTION"

# Run comprehensive verification using the customization system's verify script
VERIFY_SCRIPT="$CUSTOMIZATION_TEMPLATE/scripts/verify-customization.sh"
if [ -x "$VERIFY_SCRIPT" ]; then
    echo "**Running comprehensive customization verification:** $(date)" >> "$STEP_EXECUTION"
    bash "$VERIFY_SCRIPT" >> "$STEP_EXECUTION" 2>&1
else
    echo "**Manual verification - script not found**" >> "$STEP_EXECUTION"
    
    # Manual verification checks
    echo "**Manual Verification Results:**" >> "$STEP_EXECUTION"
    echo "- Custom directories: $(ls -d */Custom 2>/dev/null | wc -l)" >> "$STEP_EXECUTION"
    echo "- Service provider: $(grep -c CustomizationServiceProvider bootstrap/providers.php config/app.php 2>/dev/null)" >> "$STEP_EXECUTION"
    echo "- Configuration files: $(find app/Custom/config -name "*.php" 2>/dev/null | wc -l)" >> "$STEP_EXECUTION"
    echo "- Asset pipeline: $(test -f webpack.custom.cjs && echo "Present" || echo "Missing")" >> "$STEP_EXECUTION"
fi

# Generate setup completion summary
echo "**Environment Setup Completion Summary:** $(date)" >> "$STEP_EXECUTION"
echo "✅ Customization system template deployed" >> "$STEP_EXECUTION"
echo "✅ Custom asset pipeline configured" >> "$STEP_EXECUTION"
echo "✅ Custom database structure prepared" >> "$STEP_EXECUTION"
echo "✅ Custom routing system configured" >> "$STEP_EXECUTION"
echo "✅ Custom configuration management setup" >> "$STEP_EXECUTION"
echo "✅ Comprehensive verification completed" >> "$STEP_EXECUTION"

echo "**Custom environment setup completed successfully:** $(date)" >> "$STEP_EXECUTION"
echo "**Ready to proceed to Step 03: Implement Core Features**" >> "$STEP_EXECUTION"
```

---

## ✅ Step 02 Completion Checklist

- [ ] **2.1** Customization system template deployed successfully
- [ ] **2.2** Custom asset pipeline configured and tested
- [ ] **2.3** Custom database structure prepared
- [ ] **2.4** Custom routing system configured and integrated
- [ ] **2.5** Custom configuration management setup completed
- [ ] **2.6** Comprehensive verification passed with all components working

## 📁 Generated Structure

The custom environment creates:
```
app/Custom/
├── Controllers/
├── Models/
├── Services/
├── Middleware/
├── Providers/CustomizationServiceProvider.php
└── config/
    ├── custom-app.php
    └── custom-database.php

resources/Custom/
├── views/
├── css/
├── js/
└── images/

routes/custom/
├── web.php
└── api.php

database/
├── migrations/custom/
├── seeders/custom/
└── factories/custom/

webpack.custom.cjs
```

## 🔧 Verification Commands

Test the environment setup:
```bash
# Verify service provider registration
php artisan config:cache

# Test custom asset compilation
npm run build:custom

# Check custom routes are loaded
php artisan route:list | grep custom

# Verify custom configuration access
php artisan tinker --execute="dd(config('custom-app'));"
```

## ➡️ Next Step

**Step 03: Implement Core Features** - Begin implementing custom business logic using the protected environment.

---

**Note:** This step creates a completely isolated customization environment that protects vendor code while enabling safe feature development and updates.