# Zaj Laravel Customization System Setup - Step by Step Guide

## Overview

This guide provides detailed steps to set up the Zaj customization system for any Laravel project, ensuring clean separation between vendor code and custom modifications.

## Prerequisites

-   Fresh Laravel installation (any version 8.x+)
-   Git repository initialized
-   Development environment setup
-   Backup of original codebase

---

## Phase 1: Initial Setup

### Step 1: Create Custom Directory Structure

```bash
# Create main custom directories
mkdir -p app/Custom/config
mkdir -p app/Custom/Controllers
mkdir -p app/Custom/Models
mkdir -p app/Custom/Services
mkdir -p app/Custom/Middleware
mkdir -p app/Custom/Helpers

# Create custom resource directories
mkdir -p resources/Custom/css
mkdir -p resources/Custom/css/utilities
mkdir -p resources/Custom/js
mkdir -p resources/Custom/js/components
mkdir -p resources/Custom/views

# Create custom database directories
mkdir -p database/Custom/migrations
mkdir -p database/Custom/seeders

# Create documentation directories
mkdir -p docs/Custom/features
mkdir -p docs/Custom/api
```

### Step 2: Create Custom Configuration Files

```bash
# Copy and create custom config files
cp config/app.php app/Custom/config/custom-app.php
cp config/database.php app/Custom/config/custom-database.php
```

### Step 3: Create CustomizationServiceProvider

```bash
# Create the main service provider
php artisan make:provider CustomizationServiceProvider
```

### Step 4: Setup Custom Webpack Configuration

```bash
# Create custom webpack config
touch webpack.custom.cjs
```

### Step 5: Initialize Investment Protection

```bash
# Create tracking directories
mkdir -p .investment-tracking/baselines
mkdir -p .investment-tracking/changes
mkdir -p docs/Investment-Protection

# Generate baseline fingerprint
find . -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.blade.php" | \
  xargs md5sum > .investment-tracking/baselines/original-codebase.fingerprint
```

---

## Phase 2: Configuration Setup

### Step 6: Configure CustomizationServiceProvider

Edit `app/Providers/CustomizationServiceProvider.php`:

-   Register custom config files
-   Register custom view paths
-   Register custom routes
-   Register custom middleware

### Step 7: Register Service Provider

Add to `bootstrap/providers.php`:

```php
App\Providers\CustomizationServiceProvider::class,
```

### Step 8: Configure Custom Assets

Edit `webpack.custom.cjs`:

-   Setup custom CSS/JS compilation
-   Configure asset paths
-   Setup hot reload for custom assets

### Step 9: Update package.json

Add custom build scripts:

```json
{
    "scripts": {
        "custom:dev": "webpack --config webpack.custom.cjs --mode development --watch",
        "custom:build": "webpack --config webpack.custom.cjs --mode production"
    }
}
```

---

## Phase 3: Testing & Validation

### Step 10: Test Custom Configuration

```bash
# Clear caches
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Test configuration loading
php artisan config:cache
```

### Step 11: Test Custom Assets

```bash
# Build custom assets
npm run custom:build

# Verify asset compilation
ls -la public/custom/
```

### Step 12: Create Test Custom Component

Create a simple custom dashboard widget to verify the system works.

### Step 13: Document Setup

-   Update investment protection documentation
-   Create feature documentation template
-   Document custom file locations

---

## Phase 4: Production Readiness

### Step 14: Setup Deployment Scripts

Create scripts for:

-   Custom asset deployment
-   Custom migration deployment
-   Investment protection verification

### Step 15: Create Backup Strategy

-   Backup original vendor files
-   Document customization points
-   Create rollback procedures

### Step 16: Final Verification

Run comprehensive tests:

-   System functionality
-   Custom features
-   Asset loading
-   Database integrity

---

## Success Criteria

✅ **Setup Complete When:**

-   [ ] All custom directories created
-   [ ] CustomizationServiceProvider registered and working
-   [ ] Custom assets compile successfully
-   [ ] Test custom component renders
-   [ ] Investment protection tracking active
-   [ ] Documentation complete
-   [ ] Backup strategy in place

---

## Next Steps

After setup completion, proceed to:

1. **🆕 Study Complete Example** - `Files/2-Complete-Feature-Example/` - Priority Task Analytics Dashboard showing ALL components working together
2. **Phase 2 Preparation Guide** - Plan your customizations
3. **Investment Protection Setup** - Implement tracking
4. **Custom Feature Development** - Start building

---

## Troubleshooting

### Common Issues:

-   **Service Provider not loading**: Check `bootstrap/providers.php`
-   **Assets not compiling**: Verify `webpack.custom.cjs` syntax
-   **Views not found**: Check custom view path registration
-   **Config not loading**: Clear and rebuild cache

### Support Files:

-   🆕 **Complete Example**: `Files/2-Complete-Feature-Example/` - Production-ready analytics dashboard
-   **Basic Setup**: `Files/1-Example-Setup/` - Core system files
-   **Traditional Example**: `Files/Example-Customization/` - SocietyPal widget example
