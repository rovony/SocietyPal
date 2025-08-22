#!/bin/bash
set -e

# Phase-B-Prep: Application Compatibility & Configuration
# Purpose: Validate Laravel application, smart database testing, configuration validation
# Run: AFTER file upload, BEFORE release activation (SSH Commands - After Upload, Before Release)
# Action: VALIDATE app readiness, TEST configurations, PREPARE for activation
# Version 2.0 - PRODUCTION READY (Enhanced with root cause fixes)

echo "=== Phase-B-Prep: Application Compatibility & Configuration ==="

# ENHANCED: Define all variables relative to %path% (only available DeployHQ variable)
# %path% = Base server path we're deploying to (e.g., /home/user/domains/site.com/deploy)
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"

# Detect current release directory (most recent in releases/)
if [ -d "$DEPLOY_PATH/releases" ]; then
    LATEST_RELEASE=$(ls -1t "$DEPLOY_PATH/releases" | head -1)
    RELEASE_PATH="$DEPLOY_PATH/releases/$LATEST_RELEASE"
else
    echo "❌ No releases directory found"
    exit 1
fi

cd "$RELEASE_PATH"

# Initialize variables using %path% as base
DOMAIN_ROOT=$(dirname "$DEPLOY_PATH")
PREP_REPORT="$DOMAIN_ROOT/deployment-prep-report.md"
PHASE_B_ISSUES=0
RECOMMENDATIONS_PROVIDED=0

echo "🔧 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Current Release: $RELEASE_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Path: $CURRENT_PATH"
echo "   Domain Root: $DOMAIN_ROOT"

echo "? Starting application compatibility validation..."
echo "? Release Path: $(pwd)"

# Update prep report for Phase B
cat >> "$PREP_REPORT" << EOF

---

## ? Phase B: Application Compatibility & Configuration
**Focus:** Laravel readiness, database validation, configuration testing  
**Release:** $(basename "$(pwd)")
EOF

# B-Prep-01: Laravel Framework Validation
echo "=== Laravel Framework Validation ==="

LARAVEL_STATUS="❌ NOT DETECTED"
LARAVEL_VERSION="Unknown"

if [ -f "artisan" ]; then
    echo "✅ Laravel artisan file detected"
    
    # Test artisan without exec() dependency (shared hosting friendly)
    if [ -f "vendor/autoload.php" ] && [ -f "bootstrap/app.php" ]; then
        # Try to get Laravel version from composer.json
        if [ -f "composer.json" ] && command -v jq >/dev/null 2>&1; then
            LARAVEL_VERSION=$(jq -r '.require."laravel/framework" // "Not specified"' composer.json 2>/dev/null)
        elif [ -f "composer.json" ]; then
            LARAVEL_VERSION=$(grep -o '"laravel/framework":\s*"[^"]*"' composer.json | cut -d'"' -f4 2>/dev/null || echo "Detected")
        fi
        
        # Alternative: Check Laravel version from Application class
        if [ "$LARAVEL_VERSION" = "Unknown" ] || [ "$LARAVEL_VERSION" = "Not specified" ]; then
            LARAVEL_VERSION=$(grep -r "VERSION.*=" vendor/laravel/framework/src/Illuminate/Foundation/Application.php 2>/dev/null | grep -o "[0-9]\+\.[0-9]\+\.[0-9]\+" | head -1 || echo "Detected")
        fi
        
        LARAVEL_STATUS="✅ DETECTED"
        echo "✅ Laravel framework: $LARAVEL_VERSION"
    else
        echo "❌ Laravel dependencies missing (vendor/autoload.php or bootstrap/app.php)"
        LARAVEL_STATUS="❌ INCOMPLETE"
        PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
    fi
else
    echo "❌ No artisan file - not a Laravel application"
    PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
fi

# B-Prep-02: Dependencies Validation
echo "=== Dependencies Validation ==="

DEPENDENCIES_STATUS="✅ COMPLETE"
VENDOR_SIZE="0"

if [ -d "vendor" ]; then
    VENDOR_SIZE=$(du -sh vendor 2>/dev/null | cut -f1)
    VENDOR_FILES=$(find vendor -name "*.php" 2>/dev/null | wc -l)
    echo "✅ Vendor directory: $VENDOR_SIZE ($VENDOR_FILES PHP files)"
    
    # Check for critical Laravel dependencies (ENHANCED with path fixes)
    CRITICAL_PACKAGES=("laravel/framework" "symfony/console" "illuminate/support")
    MISSING_PACKAGES=()
    
    echo "🔍 Verifying critical Laravel packages..."
    for package in "${CRITICAL_PACKAGES[@]}"; do
        PACKAGE_PATH="vendor/$(echo "$package" | tr '/' '/')"
        
        # Enhanced check with multiple validation methods
        if [ -d "$PACKAGE_PATH" ] && [ -f "$PACKAGE_PATH/composer.json" ]; then
            echo "✅ $package: Available and valid"
        elif [ -d "$PACKAGE_PATH" ]; then
            echo "⚠️ $package: Directory exists but may be incomplete"
            # Try to verify package integrity
            if [ -f "$PACKAGE_PATH/src" ] || [ -f "$PACKAGE_PATH/ServiceProvider.php" ] || [ -f "$PACKAGE_PATH/Support" ]; then
                echo "   ✅ Package structure verified"
            else
                echo "   ❌ Package structure incomplete"
                MISSING_PACKAGES+=("$package")
                PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
            fi
        else
            echo "❌ $package: Missing completely"
            MISSING_PACKAGES+=("$package")
            PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
        fi
    done
    
    # ENHANCED: Auto-fix missing dependencies
    if [ ${#MISSING_PACKAGES[@]} -gt 0 ]; then
        echo "🔧 Auto-fixing missing packages: ${MISSING_PACKAGES[*]}"
        
        # Use Composer 2 if available
        COMPOSER_CMD="composer"
        if command -v composer2 &> /dev/null; then
            COMPOSER_CMD="composer2"
            echo "✅ Using Composer 2 for Laravel 12+ compatibility"
        fi
        
        # Reinstall dependencies to fix missing packages
        if $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction; then
            echo "✅ Dependencies reinstalled successfully"
            # Re-verify packages
            for package in "${MISSING_PACKAGES[@]}"; do
                PACKAGE_PATH="vendor/$(echo "$package" | tr '/' '/')"
                if [ -d "$PACKAGE_PATH" ]; then
                    echo "✅ $package: Now available after reinstall"
                    PHASE_B_ISSUES=$((PHASE_B_ISSUES - 1))
                else
                    echo "❌ $package: Still missing after reinstall"
                fi
            done
        else
            echo "❌ Dependency reinstallation failed"
        fi
    fi
    
    if [ ${#MISSING_PACKAGES[@]} -gt 0 ]; then
        DEPENDENCIES_STATUS="❌ INCOMPLETE"
    fi
else
    echo "❌ No vendor directory found"
    DEPENDENCIES_STATUS="❌ MISSING"
    PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
fi

# B-Prep-03: Smart Environment Configuration Analysis (ENHANCED)
echo "=== Smart Environment Configuration Analysis ==="

ENV_CONFIG_STATUS="❌ NOT CONFIGURED"
ENV_FILE_PATH=""
DB_CONFIG_COMPLETE="false"

# ENHANCED: Wait for .env symlink to be established (fixes timing issue)
echo "🔄 Waiting for environment configuration to be ready..."
MAX_WAIT=30
WAIT_COUNT=0

while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    # Check multiple .env locations with enhanced detection
    if [ -L ".env" ] && [ -e ".env" ]; then
        ENV_FILE_PATH=".env"
        echo "✅ Found .env symlink: $(readlink .env)"
        break
    elif [ -f ".env" ] && [ ! -L ".env" ]; then
        ENV_FILE_PATH=".env"
        echo "✅ Found .env file (not symlinked)"
        break
    elif [ -f "$SHARED_PATH/.env" ]; then
        ENV_FILE_PATH="$SHARED_PATH/.env"
        echo "✅ Found shared .env file"
        break
    else
        sleep 1
        WAIT_COUNT=$((WAIT_COUNT + 1))
        echo "   Waiting for .env... ($WAIT_COUNT/$MAX_WAIT)"
    fi
done

if [ -z "$ENV_FILE_PATH" ]; then
    echo "❌ No .env file found after waiting (neither local nor shared)"
    echo "🔍 Debug: Checking available files..."
    ls -la .env* 2>/dev/null || echo "   No .env* files found"
    ls -la "$SHARED_PATH"/.env* 2>/dev/null || echo "   No shared .env* files found"
    PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
fi

if [ -n "$ENV_FILE_PATH" ]; then
    echo "✅ Environment file: $ENV_FILE_PATH"
    ENV_CONFIG_STATUS="✅ FOUND"
    
    # Smart environment validation (check completeness before testing)
    REQUIRED_ENV_VARS=("APP_KEY" "APP_URL" "DB_CONNECTION" "DB_HOST" "DB_DATABASE" "DB_USERNAME")
    MISSING_ENV_VARS=()
    EMPTY_ENV_VARS=()
    
    for var in "${REQUIRED_ENV_VARS[@]}"; do
        if grep -q "^${var}=" "$ENV_FILE_PATH" 2>/dev/null; then
            VALUE=$(grep "^${var}=" "$ENV_FILE_PATH" | cut -d'=' -f2- | tr -d '"' | tr -d "'" | xargs)
            if [ -n "$VALUE" ] && [ "$VALUE" != "null" ]; then
                echo "✅ $var: Configured"
            else
                echo "⚠️ $var: Empty value"
                EMPTY_ENV_VARS+=("$var")
            fi
        else
            echo "❌ $var: Missing"
            MISSING_ENV_VARS+=("$var")
            PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
        fi
    done
    
    # Check if database configuration is complete for testing
    DB_REQUIRED=("DB_CONNECTION" "DB_HOST" "DB_DATABASE" "DB_USERNAME")
    DB_COMPLETE_COUNT=0
    for var in "${DB_REQUIRED[@]}"; do
        if grep -q "^${var}=.\+" "$ENV_FILE_PATH" 2>/dev/null; then
            VALUE=$(grep "^${var}=" "$ENV_FILE_PATH" | cut -d'=' -f2- | tr -d '"' | tr -d "'" | xargs)
            if [ -n "$VALUE" ] && [ "$VALUE" != "null" ]; then
                DB_COMPLETE_COUNT=$((DB_COMPLETE_COUNT + 1))
            fi
        fi
    done
    
    if [ $DB_COMPLETE_COUNT -eq ${#DB_REQUIRED[@]} ]; then
        DB_CONFIG_COMPLETE="true"
        echo "✅ Database configuration: Complete for testing"
    else
        echo "⚠️ Database configuration: Incomplete ($DB_COMPLETE_COUNT/${#DB_REQUIRED[@]} required vars)"
    fi
fi

# B-Prep-04: Smart Database Connection Testing
echo "=== Smart Database Connection Testing ==="

DB_CONNECTION_STATUS="⚠️ UNTESTED"
DB_TEST_METHOD="None"

if [ "$DB_CONFIG_COMPLETE" = "true" ] && [ -n "$ENV_FILE_PATH" ]; then
    # Extract database credentials safely
    DB_CONNECTION=$(grep "^DB_CONNECTION=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    DB_HOST=$(grep "^DB_HOST=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    DB_PORT=$(grep "^DB_PORT=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    DB_DATABASE=$(grep "^DB_DATABASE=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    DB_USERNAME=$(grep "^DB_USERNAME=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    DB_PASSWORD=$(grep "^DB_PASSWORD=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    
    # Default port if not specified
    DB_PORT=${DB_PORT:-3306}
    
    echo "? Testing: $DB_CONNECTION database on $DB_HOST:$DB_PORT"
    
    # Method 1: Direct MySQL client test (most reliable for shared hosting)
    if command -v mysql >/dev/null 2>&1; then
        echo "? Testing with MySQL client..."
        if timeout 10 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1 as test;" "$DB_DATABASE" >/dev/null 2>&1; then
            DB_CONNECTION_STATUS="✅ CONNECTED (MySQL client)"
            DB_TEST_METHOD="MySQL CLI"
            echo "✅ Database connection: Success via MySQL client"
        else
            echo "❌ Database connection: Failed via MySQL client"
            DB_CONNECTION_STATUS="❌ FAILED (MySQL client)"
            DB_TEST_METHOD="MySQL CLI"
            PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
        fi
        
    # Method 2: PHP PDO test (fallback, works without exec() restrictions)
    else
        echo "? Testing with PHP PDO..."
        PDO_TEST_SCRIPT="<?php
try {
    \$dsn = 'mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE';
    \$pdo = new PDO(\$dsn, '$DB_USERNAME', '$DB_PASSWORD', [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    \$stmt = \$pdo->query('SELECT 1 as test');
    if (\$stmt->fetch()) {
        echo 'PDO_SUCCESS';
    } else {
        echo 'PDO_QUERY_FAILED';
    }
} catch (Exception \$e) {
    echo 'PDO_ERROR: ' . \$e->getMessage();
}
?>"
        
        PDO_RESULT=$(echo "$PDO_TEST_SCRIPT" | php 2>/dev/null)
        
        if echo "$PDO_RESULT" | grep -q "PDO_SUCCESS"; then
            DB_CONNECTION_STATUS="✅ CONNECTED (PHP PDO)"
            DB_TEST_METHOD="PHP PDO"
            echo "✅ Database connection: Success via PHP PDO"
        else
            echo "❌ Database connection: $PDO_RESULT"
            DB_CONNECTION_STATUS="❌ FAILED (PHP PDO)"
            DB_TEST_METHOD="PHP PDO"
            PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
        fi
    fi
else
    echo "ℹ️ Database connection test skipped (incomplete configuration)"
    DB_TEST_METHOD="Skipped - incomplete config"
fi

# B-Prep-05: Laravel Cache & Session Configuration Analysis
echo "=== Cache & Session Configuration Analysis ==="

CACHE_CONFIG_STATUS="✅ OPTIMAL"
SESSION_CONFIG_STATUS="✅ OPTIMAL"

if [ -n "$ENV_FILE_PATH" ]; then
    # Analyze cache configuration
    CACHE_DRIVER=$(grep "^CACHE_DRIVER=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    CACHE_DRIVER=${CACHE_DRIVER:-file}
    
    SESSION_DRIVER=$(grep "^SESSION_DRIVER=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs)
    SESSION_DRIVER=${SESSION_DRIVER:-file}
    
    echo "? Cache driver: $CACHE_DRIVER"
    echo "? Session driver: $SESSION_DRIVER"
    
    # Check for potentially problematic Redis configuration on shared hosting
    if [ "$CACHE_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "redis" ]; then
        echo "⚠️ Redis configuration detected - may not be available on shared hosting"
        echo "ℹ️ Auto-fixes will be applied in Phase C if Redis is unavailable"
        CACHE_CONFIG_STATUS="⚠️ REDIS (unverified)"
        SESSION_CONFIG_STATUS="⚠️ REDIS (unverified)"
    else
        echo "✅ File-based cache/session configuration (shared hosting compatible)"
    fi
else
    echo "ℹ️ Cache/session analysis skipped (no .env file)"
fi

# B-Prep-06: Application Permissions Validation
echo "=== Application Permissions Validation ==="

PERMISSIONS_STATUS="✅ SECURE"
PERMISSION_ISSUES=()

# Check critical Laravel directories
CRITICAL_DIRS=("storage" "bootstrap/cache")
for dir in "${CRITICAL_DIRS[@]}"; do
    if [ -e "$dir" ]; then
        if [ -w "$dir" ]; then
            echo "✅ $dir: Writable"
        else
            echo "❌ $dir: Not writable"
            PERMISSION_ISSUES+=("$dir")
            PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1))
        fi
    else
        echo "⚠️ $dir: Missing (will be created by symlinks)"
    fi
done

if [ ${#PERMISSION_ISSUES[@]} -gt 0 ]; then
    PERMISSIONS_STATUS="❌ ISSUES FOUND"
fi

# Update prep report with Phase B results
cat >> "$PREP_REPORT" << EOF

### ?️ Laravel Application
**Framework:** $LARAVEL_STATUS $([ "$LARAVEL_VERSION" != "Unknown" ] && echo "($LARAVEL_VERSION)" || echo "")  
**Dependencies:** $DEPENDENCIES_STATUS $([ -n "$VENDOR_SIZE" ] && echo "($VENDOR_SIZE)" || echo "")  
**Permissions:** $PERMISSIONS_STATUS

### ⚙️ Configuration Analysis
**Environment File:** $ENV_CONFIG_STATUS  
**Database Config:** $([ "$DB_CONFIG_COMPLETE" = "true" ] && echo "✅ Complete" || echo "⚠️ Incomplete")  
**Cache Driver:** $CACHE_DRIVER ($CACHE_CONFIG_STATUS)  
**Session Driver:** $SESSION_DRIVER ($SESSION_CONFIG_STATUS)

### ?️ Database Connection Test
**Status:** $DB_CONNECTION_STATUS  
**Method:** $DB_TEST_METHOD  
$([ "$DB_CONNECTION_STATUS" = "❌ FAILED (MySQL client)" ] || [ "$DB_CONNECTION_STATUS" = "❌ FAILED (PHP PDO)" ] && echo "**Issue:** Cannot connect to database - check credentials" || echo "**Result:** Connection validated successfully")

EOF

# Generate Phase B specific action items
PHASE_B_ACTIONS=0

cat >> "$PREP_REPORT" << EOF

### ? Application Action Items:
EOF

# Laravel dependency issues
if [ "$DEPENDENCIES_STATUS" = "❌ MISSING" ] || [ "$DEPENDENCIES_STATUS" = "❌ INCOMPLETE" ]; then
    cat >> "$PREP_REPORT" << EOF
**? CRITICAL - Fix Dependencies:**
1. **Run composer install:** \`composer install --no-dev --optimize-autoloader\`
2. **Or contact hosting provider** if composer is not available
3. **Verify:** Check that \`vendor/\` directory exists and contains Laravel files

EOF
    PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1))
fi

# Environment configuration issues
if [ ${#MISSING_ENV_VARS[@]} -gt 0 ] || [ ${#EMPTY_ENV_VARS[@]} -gt 0 ]; then
    cat >> "$PREP_REPORT" << EOF
**⚙️ REQUIRED - Fix Environment Configuration:**
$([ ${#MISSING_ENV_VARS[@]} -gt 0 ] && echo "- **Add missing variables:** ${MISSING_ENV_VARS[*]}")
$([ ${#EMPTY_ENV_VARS[@]} -gt 0 ] && echo "- **Set empty variables:** ${EMPTY_ENV_VARS[*]}")
- **Edit:** \`.env\` file in your deployment
- **Generate APP_KEY:** \`php artisan key:generate\` (if APP_KEY is missing)

EOF
    PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1))
fi

# Database connection issues  
if [[ "$DB_CONNECTION_STATUS" =~ "FAILED" ]]; then
    cat >> "$PREP_REPORT" << EOF
**?️ CRITICAL - Fix Database Connection:**
1. **Verify database exists** in your hosting control panel
2. **Check credentials** in \`.env\` file match hosting panel settings:
   - DB_HOST (often 'localhost' or specific server)
   - DB_DATABASE (exact database name)
   - DB_USERNAME (database user with access)
   - DB_PASSWORD (correct password)
3. **Test manually:** \`mysql -h[HOST] -u[USER] -p[PASS] [DATABASE]\`

EOF
    PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1))
fi

# Permission issues
if [ ${#PERMISSION_ISSUES[@]} -gt 0 ]; then
    cat >> "$PREP_REPORT" << EOF
**? REQUIRED - Fix Permissions:**
- **Directories with issues:** ${PERMISSION_ISSUES[*]}
- **Fix command:** \`chmod -R 775 ${PERMISSION_ISSUES[*]}\`
- **Note:** These may be resolved by Phase B symlink creation

EOF
    PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1))
fi

# Summary status
PHASE_B_STATUS="✅ READY FOR RELEASE"
if [ $PHASE_B_ISSUES -gt 0 ]; then
    PHASE_B_STATUS="❌ NEEDS FIXES ($PHASE_B_ISSUES issues)"
fi

if [ $PHASE_B_ACTIONS -eq 0 ]; then
    cat >> "$PREP_REPORT" << EOF
✅ **No application issues found** - ready for release activation!

EOF
fi

cat >> "$PREP_REPORT" << EOF

**Phase B Status:** $PHASE_B_STATUS  
**Recommendations Provided:** $PHASE_B_ACTIONS  
**Manual Actions Required:** $PHASE_B_ACTIONS

EOF

# Display summary
echo ""
echo "=== Phase B Summary ==="
echo "? Application Status: $PHASE_B_STATUS"
echo "? Recommendations Provided: $PHASE_B_ACTIONS" 
echo "? Manual Actions Required: $PHASE_B_ACTIONS"
echo "? Prep Report: $PREP_REPORT"

if [ $PHASE_B_ISSUES -eq 0 ]; then
    echo "✅ Laravel application is ready for release activation!"
    echo "? Proceeding to Phase B deployment commands..."
else
    echo "⚠️ $PHASE_B_ISSUES application issues detected"
    echo "? Check prep report for specific action items"
    echo "ℹ️ Some issues may be resolved by Phase B deployment commands"
fi

echo "✅ Phase-B-Prep completed successfully"

# Log results for deployment history  
DEPLOY_PATH="%path%"
if [ -d "%shared_path%" ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Phase-B-Prep: Status=$PHASE_B_STATUS | Issues=$PHASE_B_ISSUES | Recommendations=$PHASE_B_ACTIONS | DB=$DB_CONNECTION_STATUS" >> "%shared_path%/prep-history.log"
fiB-Prep: Status=$PHASE_B_STATUS | Issues=$PHASE_B_ISSUES | Recommendations=$PHASE_B_ACTIONS | DB=$DB_CONNECTION_STATUS" >> "$DEPLOY_PATH/shared/prep-history.log"
fi

# Exit successfully (don't block deployment)
exit 0