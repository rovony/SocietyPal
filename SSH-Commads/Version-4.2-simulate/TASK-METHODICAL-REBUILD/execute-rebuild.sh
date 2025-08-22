#!/bin/bash
set -e

# Methodical Rebuild Execution Script
# Uses CodeCanyon source (not GitHub)

echo "🔧 Starting Methodical Rebuild Process..."
echo "======================================"

# Define paths
TASK_DIR="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root/SSH-Commads/Version-4.2-simulate/TASK-METHODICAL-REBUILD"
SOURCE_DIR="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/CodeCanyon-Downloads/1-First-Setup-v1.0.42/societypro-saas-1.0.41 2/script"

# Phase 1: Clean Slate Setup
echo ""
echo "📋 PHASE 1: CLEAN SLATE SETUP"
echo "=============================="

# Create release directory
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
RELEASE_DIR="$TASK_DIR/releases/test-$TIMESTAMP"

echo "Creating release directory: $RELEASE_DIR"
mkdir -p "$RELEASE_DIR"
cd "$RELEASE_DIR"

# Copy from CodeCanyon source
echo "Copying from CodeCanyon source..."
if [ -d "$SOURCE_DIR" ]; then
    cp -r "$SOURCE_DIR"/* .
    echo "✅ Source files copied successfully"
else
    echo "❌ Source directory not found: $SOURCE_DIR"
    exit 1
fi

# Document initial state
cat > rebuild-log.md << EOF
=== METHODICAL REBUILD LOG ===
Date: $(date)
Release Directory: $RELEASE_DIR
Source: CodeCanyon v1.0.42 (Clean Copy)
PHP Version: $(php --version | head -1)
Composer Version: $(composer --version)
Node Version: $(node --version 2>/dev/null || echo 'Not installed')

EOF

echo "✅ Phase 1 Complete - Clean environment ready"

# Phase 2: Clean Build Artifacts
echo ""
echo "🧹 PHASE 2: CLEANING BUILD ARTIFACTS"
echo "===================================="

echo "=== STEP 2.1: CLEANING BUILD ARTIFACTS ===" >> rebuild-log.md

# Remove all build artifacts
echo "Removing build artifacts..."
rm -rf vendor/ 2>/dev/null || true
rm -rf node_modules/ 2>/dev/null || true
rm -rf bootstrap/cache/* 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf public/build/ 2>/dev/null || true
rm -rf public/dist/ 2>/dev/null || true

echo "✅ All build artifacts removed" >> rebuild-log.md

# Verify clean state
echo "=== STEP 2.2: VERIFYING CLEAN STATE ===" >> rebuild-log.md
echo "Vendor directory: $([ -d vendor ] && echo 'EXISTS (ERROR)' || echo 'CLEAN ✅')" >> rebuild-log.md
echo "Node modules: $([ -d node_modules ] && echo 'EXISTS (ERROR)' || echo 'CLEAN ✅')" >> rebuild-log.md
echo "Bootstrap cache: $(ls bootstrap/cache/ 2>/dev/null | wc -l) files" >> rebuild-log.md
echo "" >> rebuild-log.md

echo "✅ Phase 2 Complete - Clean slate achieved"

# Phase 3: Methodical Dependency Installation
echo ""
echo "📦 PHASE 3: METHODICAL DEPENDENCY INSTALLATION"
echo "=============================================="

# Step 3.1: Install Production Dependencies First
echo "=== STEP 3.1: INSTALLING PRODUCTION DEPENDENCIES ===" >> rebuild-log.md

echo "Installing production dependencies..."
if composer install --no-dev --optimize-autoloader --no-interaction; then
    if [ -d "vendor" ]; then
        VENDOR_SIZE=$(du -sh vendor | cut -f1)
        VENDOR_FILES=$(find vendor -name "*.php" | wc -l)
        echo "✅ Production dependencies installed: $VENDOR_SIZE ($VENDOR_FILES PHP files)" >> rebuild-log.md
        echo "✅ Production dependencies installed: $VENDOR_SIZE ($VENDOR_FILES PHP files)"
    fi
else
    echo "❌ Production dependency installation FAILED" >> rebuild-log.md
    echo "❌ Production dependency installation FAILED"
fi

# Test Laravel framework
if php artisan --version >/dev/null 2>&1; then
    LARAVEL_VERSION=$(php artisan --version)
    echo "✅ Laravel framework working: $LARAVEL_VERSION" >> rebuild-log.md
    echo "✅ Laravel framework working: $LARAVEL_VERSION"
else
    echo "❌ Laravel framework NOT WORKING after production install" >> rebuild-log.md
    echo "❌ Laravel framework NOT WORKING after production install"
fi

# Step 3.2: Test for Dev Dependencies Need
echo "=== STEP 3.2: TESTING FOR DEV DEPENDENCIES NEED ===" >> rebuild-log.md

NEEDS_DEV=false

# Test if Faker is needed (key issue from deployment logs)
if [ -d "database" ] && grep -r "Faker\\\\" database/ >/dev/null 2>&1; then
    echo "⚠️ Faker detected in database files - dev dependencies needed" >> rebuild-log.md
    echo "⚠️ Faker detected in database files - dev dependencies needed"
    
    # Test if Faker is available
    if php -r "require_once 'vendor/autoload.php'; echo class_exists('Faker\\Generator') ? 'AVAILABLE' : 'MISSING';" | grep -q "AVAILABLE"; then
        echo "✅ Faker already available in production install" >> rebuild-log.md
        echo "✅ Faker already available in production install"
    else
        echo "❌ Faker MISSING - need dev dependencies" >> rebuild-log.md
        echo "❌ Faker MISSING - need dev dependencies"
        NEEDS_DEV=true
    fi
else
    echo "ℹ️ No Faker usage detected" >> rebuild-log.md
    echo "ℹ️ No Faker usage detected"
fi

# Test other dev dependency needs
if [ -f "config/app.php" ] && grep -q "debugbar" config/app.php 2>/dev/null; then
    echo "⚠️ Debugbar detected - dev dependencies needed" >> rebuild-log.md
    echo "⚠️ Debugbar detected - dev dependencies needed"
    NEEDS_DEV=true
fi

echo "Dev dependencies needed: $NEEDS_DEV" >> rebuild-log.md
echo "Dev dependencies needed: $NEEDS_DEV"

# Step 3.3: Install Dev Dependencies if Needed
echo "=== STEP 3.3: INSTALLING DEV DEPENDENCIES IF NEEDED ===" >> rebuild-log.md

if [ "$NEEDS_DEV" = "true" ]; then
    echo "Installing dev dependencies..." >> rebuild-log.md
    echo "Installing dev dependencies..."
    
    # Install all dependencies (including dev)
    if composer install --optimize-autoloader --no-interaction; then
        if [ -d "vendor" ]; then
            VENDOR_SIZE=$(du -sh vendor | cut -f1)
            VENDOR_FILES=$(find vendor -name "*.php" | wc -l)
            echo "✅ All dependencies installed: $VENDOR_SIZE ($VENDOR_FILES PHP files)" >> rebuild-log.md
            echo "✅ All dependencies installed: $VENDOR_SIZE ($VENDOR_FILES PHP files)"
        fi
        
        # Test Faker specifically
        if php -r "require_once 'vendor/autoload.php'; echo class_exists('Faker\\Generator') ? 'AVAILABLE' : 'MISSING';" | grep -q "AVAILABLE"; then
            echo "✅ Faker now available" >> rebuild-log.md
            echo "✅ Faker now available"
        else
            echo "❌ Faker still MISSING after dev install" >> rebuild-log.md
            echo "❌ Faker still MISSING after dev install"
        fi
    else
        echo "❌ Dev dependency installation FAILED" >> rebuild-log.md
        echo "❌ Dev dependency installation FAILED"
    fi
else
    echo "ℹ️ Dev dependencies not needed - production install sufficient" >> rebuild-log.md
    echo "ℹ️ Dev dependencies not needed - production install sufficient"
fi

echo "✅ Phase 3 Complete - Dependencies installed"

# Phase 4: Environment Setup
echo ""
echo "⚙️ PHASE 4: ENVIRONMENT SETUP"
echo "============================="

echo "=== STEP 4.1: ENVIRONMENT SETUP ===" >> rebuild-log.md

# Copy .env.example if no .env exists
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ Created .env from .env.example" >> rebuild-log.md
        echo "✅ Created .env from .env.example"
    else
        echo "❌ No .env.example found" >> rebuild-log.md
        echo "❌ No .env.example found"
    fi
fi

# Generate APP_KEY if missing
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate
    echo "✅ Generated APP_KEY" >> rebuild-log.md
    echo "✅ Generated APP_KEY"
fi

echo "✅ Phase 4 Complete - Environment configured"

# Phase 5: Testing & Verification
echo ""
echo "🧪 PHASE 5: TESTING & VERIFICATION"
echo "=================================="

echo "=== STEP 5.1: FRAMEWORK HEALTH CHECK ===" >> rebuild-log.md

# Test Laravel framework
if php artisan --version >/dev/null 2>&1; then
    LARAVEL_VERSION=$(php artisan --version)
    echo "✅ Laravel framework: $LARAVEL_VERSION" >> rebuild-log.md
    echo "✅ Laravel framework: $LARAVEL_VERSION"
else
    echo "❌ Laravel framework BROKEN" >> rebuild-log.md
    echo "❌ Laravel framework BROKEN"
fi

# Test autoloader
if php -r "require_once 'vendor/autoload.php'; echo class_exists('Illuminate\\Foundation\\Application') ? 'OK' : 'FAILED';" | grep -q "OK"; then
    echo "✅ Autoloader working" >> rebuild-log.md
    echo "✅ Autoloader working"
else
    echo "❌ Autoloader BROKEN" >> rebuild-log.md
    echo "❌ Autoloader BROKEN"
fi

# Test critical classes
echo "=== STEP 5.2: CRITICAL CLASS TESTING ===" >> rebuild-log.md
php -r "
require_once 'vendor/autoload.php';
\$classes = ['Illuminate\\Foundation\\Application', 'Faker\\Generator'];
foreach (\$classes as \$class) {
    echo \$class . ': ' . (class_exists(\$class) ? 'OK' : 'MISSING') . PHP_EOL;
}
" >> rebuild-log.md

# Test the specific migration that was failing
echo "=== STEP 5.3: SPECIFIC ISSUE TESTING ===" >> rebuild-log.md
echo "Testing problematic migration..." >> rebuild-log.md

# Test Faker in migration context
echo "Testing Faker in migration context..." >> rebuild-log.md
php -r "
require_once 'vendor/autoload.php';
try {
    \$faker = \Faker\Factory::create();
    echo 'Faker Factory: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Faker Factory: FAILED - ' . \$e->getMessage() . PHP_EOL;
}
" >> rebuild-log.md

echo "✅ Phase 5 Complete - Testing finished"

# Phase 6: Results Summary
echo ""
echo "📊 PHASE 6: RESULTS ANALYSIS"
echo "============================"

echo "=== FINAL RESULTS SUMMARY ===" >> rebuild-log.md
echo "Timestamp: $(date)" >> rebuild-log.md
echo "" >> rebuild-log.md

# Generate summary
echo "✅ METHODICAL REBUILD COMPLETE!"
echo ""
echo "📋 Results Summary:"
echo "- Release Directory: $RELEASE_DIR"
echo "- Log File: $RELEASE_DIR/rebuild-log.md"
echo ""
echo "🔍 Next Steps:"
echo "1. Review rebuild-log.md for detailed results"
echo "2. Identify what fixed the issues"
echo "3. Update build/deploy scripts based on findings"
echo ""
echo "📁 All files are in: $TASK_DIR"
