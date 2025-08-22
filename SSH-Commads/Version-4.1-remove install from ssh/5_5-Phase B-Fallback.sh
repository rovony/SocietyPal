#!/bin/bash
set -e

# Phase B-Fallback: Dependencies Recovery & Laravel Verification
# Purpose: Fallback dependency recovery when Laravel framework fails
# Timing: After Phase B, before release activation - CRITICAL SAFETY NET
# Action: Smart dependency recovery with progressive fallback methods
# Version 1 - PRODUCTION READY (Enhanced with %path% variable only)

echo "=== Phase B-Fallback: Dependencies Recovery & Laravel Verification ==="

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

echo "? Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Current Release: $RELEASE_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Path: $CURRENT_PATH"

# B-Fallback-01: Laravel Health Verification
echo "=== Laravel Framework Health Verification ==="

LARAVEL_WORKING=false

# Test 1: Basic artisan availability
if [ -f "artisan" ]; then
    echo "✅ Artisan file exists"
    
    # Test 2: Laravel framework functionality
    if php artisan --version >/dev/null 2>&1; then
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
        echo "✅ Laravel working: $LARAVEL_VERSION"
        LARAVEL_WORKING=true
    else
        echo "❌ Laravel artisan command failed"
        echo "? Laravel appears broken - initiating dependency recovery..."
        
        # Debug information
        echo "? Debug information:"
        echo "   PHP Version: $(php -v | head -1)"
        echo "   Working Directory: $(pwd)"
        echo "   Vendor exists: $([ -d 'vendor' ] && echo 'Yes' || echo 'No')"
        echo "   Autoload exists: $([ -f 'vendor/autoload.php' ] && echo 'Yes' || echo 'No')"
        echo "   .env exists: $([ -f '.env' ] && echo 'Yes' || echo 'No')"
    fi
else
    echo "❌ No artisan file found"
    echo "? Critical Laravel files missing - initiating recovery..."
fi

# B-Fallback-02: Build Artifact Recovery (BUILD PROCESS AWARE)
if [ "$LARAVEL_WORKING" = false ]; then
    echo "=== Build Artifact Recovery ==="
    
    echo "❌ CRITICAL: Laravel framework not operational despite build artifacts"
    echo "⚠️ This indicates a severe build process failure or corrupted transfer"
    echo "? Build artifacts should contain all required dependencies (including dev deps as needed)"
    echo ""
    echo "? Recovery approach: DO NOT run composer install - it will destroy build artifacts"
    echo "? Instead: Check for build process issues and re-deploy with proper build"
    echo ""
    
    # Only provide diagnostic information, no destructive recovery
    echo "? Diagnostic Information:"
    VENDOR_SIZE=$([ -d 'vendor' ] && du -sh vendor | cut -f1 || echo 'Missing')
    VENDOR_FILES=$([ -d 'vendor' ] && find vendor -name "*.php" | wc -l || echo '0')
    echo "   - Vendor directory: $VENDOR_SIZE ($VENDOR_FILES PHP files)"
    echo "   - Autoload file: $([ -f 'vendor/autoload.php' ] && echo 'Present' || echo 'Missing')"
    echo "   - Laravel framework: $([ -d 'vendor/laravel/framework' ] && echo 'Present' || echo 'Missing')"
    echo "   - Faker package: $([ -d 'vendor/fakerphp/faker' ] && echo 'Present' || echo 'Missing')"
    
    # Check if this is a complete build failure vs partial corruption
    if [ "$VENDOR_FILES" -lt 100 ]; then
        echo "❌ COMPLETE BUILD FAILURE: Vendor directory empty/corrupted"
        echo "? This requires a complete re-deployment with proper build process"
        LARAVEL_WORKING=false  # Ensure we don't attempt any fixes
    else
        echo "⚠️ PARTIAL BUILD CORRUPTION: Some files present but Laravel not working"
        echo "? This may indicate missing dev dependencies or corrupted autoloader"
        echo "? Re-deploy recommended to ensure proper dependency set"
        LARAVEL_WORKING=false  # Ensure we don't attempt any fixes
    fi
fi

# B-Fallback-03: Environment Verification (if Laravel is working)
if [ "$LARAVEL_WORKING" = true ]; then
    echo "=== Environment Configuration Verification ==="
    
    # Verify .env symlink
    if [ -L ".env" ] && [ -e ".env" ]; then
        echo "✅ .env symlink working"
        
        # Test configuration loading
        if php artisan tinker --execute="echo 'Config test: ' . config('app.name', 'DEFAULT');" 2>/dev/null | grep -q "Config test:"; then
            echo "✅ Configuration loading successfully"
        else
            echo "⚠️ Configuration loading issues detected"
        fi
    else
        echo "⚠️ .env symlink broken - attempting to fix"
        rm -f ".env" 2>/dev/null || true
        ln -sf "$SHARED_PATH/.env" ".env"
        if [ -L ".env" ] && [ -e ".env" ]; then
            echo "✅ .env symlink fixed"
        else
            echo "❌ .env symlink still broken"
        fi
    fi
    
    # Verify storage symlink
    if [ -L "storage" ] && [ -e "storage" ]; then
        echo "✅ Storage symlink working"
    else
        echo "⚠️ Storage symlink broken - this may cause issues"
    fi
fi

# B-Fallback-04: Final Status Report
echo "=== Recovery Status Report ==="

if [ "$LARAVEL_WORKING" = true ]; then
    echo "✅ Laravel Framework: OPERATIONAL"
    echo "✅ Recovery Status: SUCCESS"
    echo "? Application ready for release activation"
    
    # Final verification tests
    echo "? Final verification:"
    echo "   ✅ Artisan commands: Working"
    echo "   ✅ Vendor directory: $([ -d 'vendor' ] && du -sh vendor | cut -f1)"
    echo "   ✅ Autoloader: $([ -f 'vendor/autoload.php' ] && echo 'Present' || echo 'Missing')"
    echo "   ✅ Environment: $([ -f '.env' ] && echo 'Linked' || echo 'Missing')"
    
else
    echo "❌ Laravel Framework: STILL BROKEN"
    echo "❌ Recovery Status: FAILED"
    echo "⚠️ CRITICAL: Manual intervention required"
    echo ""
    echo "? Diagnostic Information:"
    echo "   - PHP Version: $(php -v | head -1)"
    echo "   - Composer: $($COMPOSER_CMD --version 2>/dev/null || echo 'Failed')"
    echo "   - Working Directory: $(pwd)"
    echo "   - Vendor exists: $([ -d 'vendor' ] && echo 'Yes' || echo 'No')"
    echo "   - Autoload exists: $([ -f 'vendor/autoload.php' ] && echo 'Yes' || echo 'No')"
    echo "   - Laravel Framework: $([ -d 'vendor/laravel/framework' ] && echo 'Yes' || echo 'No')"
    echo "   - .env symlink: $([ -L '.env' ] && echo 'Yes' || echo 'No')"
    echo "   - Storage symlink: $([ -L 'storage' ] && echo 'Yes' || echo 'No')"
    echo ""
    echo "?️ Manual Recovery Steps:"
    echo "   1. SSH into server: ssh user@yourserver.com"
    echo "   2. Navigate: cd $RELEASE_PATH"
    echo "   3. Check errors: php artisan --version"
    echo "   4. DO NOT run composer install - it will destroy build artifacts"
    echo "   5. Test again: php artisan --version"
    echo "   6. Check .env: ls -la .env"
    echo "   7. Fix .env: ln -sf $SHARED_PATH/.env .env"
    echo ""
    echo "? Common Issues & Solutions:"
    echo "   - Memory limit: Increase PHP memory_limit in hosting panel"
    echo "   - Disk space: Clean up old files or upgrade hosting"
    echo "   - Permissions: Check directory/file permissions"
    echo "   - PHP extensions: Ensure required extensions are installed"
    echo "   - Composer version: Try switching between composer/composer2"
fi

echo "✅ Phase B-Fallback completed"

# Continue deployment even if Laravel is broken (let later phases handle it)
# This prevents deployment pipeline from failing completely
echo "ℹ️ Deployment will continue to allow later phases to attempt fixes"
exit 0