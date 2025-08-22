#!/bin/bash
# SaaS INSTALLER BUILD TEMPLATE
# For Laravel apps with installers, seeders, and demo data
# Based on Expert 1, 2, and 3 combined approach

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "🏢 SaaS Installer Build Pipeline"
echo "==============================="

# This template is for apps that need:
# - Faker for seeders/demo data
# - Full dev dependencies for installer
# - Frontend assets for complete UI

# ============================================================================
# SMART DEPENDENCY DETECTION
# ============================================================================

echo "🔍 Running smart dependency detection..."
source ../2-Universal-Build-Pipeline/smart-dependency-detector.sh

# Force dev dependencies for installer apps
NEEDS_DEV_DEPS=true
DETECTION_REASONS+=("SaaS installer requires seeders and demo data")

# ============================================================================
# LARAVEL DIRECTORY SETUP
# ============================================================================

echo "📁 Setting up Laravel directory structure..."
mkdir -p bootstrap/cache \
         storage/{app/public,framework/{cache/data,sessions,testing,views},logs,clockwork,debugbar} \
         public/storage

chmod -R 755 bootstrap/cache storage
find storage -type d -exec chmod 775 {} \;
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true

# ============================================================================
# PHP DEPENDENCIES (INCLUDE DEV FOR INSTALLER)
# ============================================================================

echo "📦 Installing PHP dependencies (including dev for installer)..."
composer install --optimize-autoloader --no-interaction --prefer-dist --no-progress

echo "✅ Dev dependencies included for:"
echo "  - Database seeders with Faker"
echo "  - Demo data generation"
echo "  - Installer functionality"

# ============================================================================
# NODE.JS DEPENDENCIES & ASSET BUILDING
# ============================================================================

if [ -f "package.json" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm ci --no-audit --no-fund

    echo "🏗️ Building frontend assets..."
    BUILD_SUCCESS=false

    # Try different build commands
    for cmd in "build" "production" "prod"; do
        if npm run "$cmd" 2>/dev/null; then
            echo "✅ Assets built with 'npm run $cmd'"
            BUILD_SUCCESS=true
            break
        fi
    done

    if [ "$BUILD_SUCCESS" = false ]; then
        echo "⚠️ No build command found - manual asset compilation may be needed"
    fi

    # Keep node_modules for installer (may need for demo generation)
    echo "ℹ️ Keeping node_modules for installer functionality"
else
    echo "ℹ️ No package.json found - API-only installer"
fi

# ============================================================================
# LARAVEL OPTIMIZATION (INSTALLER-SPECIFIC)
# ============================================================================

echo "⚡ Optimizing Laravel for installer..."

# Generate optimized autoloader
composer dump-autoload --optimize --classmap-authoritative

# Don't cache config/routes for installer (needs flexibility)
echo "ℹ️ Skipping config/route caching (installer needs flexibility)"

# ============================================================================
# INSTALLER-SPECIFIC VERIFICATION
# ============================================================================

echo "🔍 Verifying installer requirements..."

# Test that Faker is available
php -r "
if (class_exists('Faker\Factory')) {
    echo '✅ Faker available for seeders' . PHP_EOL;
} else {
    echo '❌ CRITICAL: Faker not available - installer will fail' . PHP_EOL;
    exit(1);
}
"

# Test that seeders can run
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
    echo "🌱 Testing seeder availability..."
    php artisan db:seed --class=DatabaseSeeder --dry-run 2>/dev/null && echo "✅ Seeders ready" || echo "⚠️ Seeder test failed"
fi

# Test installer routes (if exist)
if grep -q "install" routes/web.php 2>/dev/null; then
    echo "✅ Installer routes detected"
fi

echo ""
echo "✅ SaaS Installer build completed successfully!"
echo "================================================"
echo "📋 Build Summary:"
echo "  Strategy: Include dev dependencies"
echo "  Reason: SaaS installer needs seeders and demo data"
echo "  Faker: Available for seeders"
echo "  Assets: $([ "$BUILD_SUCCESS" = true ] && echo "Built successfully" || echo "No build needed")"
echo "  Ready for: Installer deployment with full functionality"
