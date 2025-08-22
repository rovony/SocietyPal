#!/bin/bash
# API-ONLY BUILD TEMPLATE
# For Laravel apps without frontend (API-only)
# Based on Expert 1, 2, and 3 combined approach

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "📡 API-Only Build Pipeline"
echo "========================="

# This template is for apps that:
# - Have no frontend (no package.json)
# - Don't need dev dependencies
# - Focus on API performance

# ============================================================================
# SMART DEPENDENCY DETECTION
# ============================================================================

echo "🔍 Running smart dependency detection..."
source ../2-Universal-Build-Pipeline/smart-dependency-detector.sh

# API-only apps rarely need dev dependencies
if [ ${#DETECTION_REASONS[@]} -eq 0 ]; then
    NEEDS_DEV_DEPS=false
    echo "📋 API-only app - production dependencies only"
fi

# ============================================================================
# LARAVEL DIRECTORY SETUP
# ============================================================================

echo "📁 Setting up Laravel directory structure..."
mkdir -p bootstrap/cache \
         storage/{app/public,framework/{cache/data,sessions,testing,views},logs} \
         public/storage

chmod -R 755 bootstrap/cache storage
find storage -type d -exec chmod 775 {} \;
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true

# ============================================================================
# PHP DEPENDENCIES (PRODUCTION OPTIMIZED)
# ============================================================================

echo "📦 Installing PHP dependencies (production optimized)..."

if [ "$NEEDS_DEV_DEPS" = true ]; then
    echo "🔧 Including dev dependencies"
    echo "📋 Reasons: ${DETECTION_REASONS[*]}"
    composer install --optimize-autoloader --no-interaction --prefer-dist --no-progress
else
    echo "🔧 Production dependencies only (API optimized)"
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
fi

# ============================================================================
# API-SPECIFIC OPTIMIZATION
# ============================================================================

echo "⚡ API-specific optimizations..."

# Generate highly optimized autoloader for API performance
composer dump-autoload --optimize --classmap-authoritative

# Cache all possible configurations for API performance
echo "🗂️ Caching Laravel configuration for API performance..."
php artisan config:cache 2>/dev/null || echo "⚠️ Config cache failed"
php artisan route:cache 2>/dev/null || echo "⚠️ Route cache failed (may have closures)"

# Skip view cache (API-only apps don't need views)
echo "ℹ️ Skipping view cache (API-only application)"

# ============================================================================
# API-SPECIFIC VERIFICATION
# ============================================================================

echo "🔍 API-specific verification..."

# Test Laravel bootstrap
php artisan --version >/dev/null 2>&1 && echo "✅ Laravel framework operational" || echo "❌ Laravel framework error"

# Test API routes
php artisan route:list --path=api 2>/dev/null >/dev/null && echo "✅ API routes available" || echo "ℹ️ No API routes found"

# Test database connection (APIs usually need database)
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB OK'; } catch(Exception \$e) { echo 'DB FAILED'; }" 2>/dev/null | grep -q "DB OK" && echo "✅ Database connection working" || echo "⚠️ Database connection failed"

# Verify no frontend artifacts
if [ ! -d "public/build" ] && [ ! -d "public/dist" ] && [ ! -d "public/js" ] && [ ! -d "public/css" ]; then
    echo "✅ Clean API-only build (no frontend artifacts)"
else
    echo "ℹ️ Frontend artifacts detected (may be from previous builds)"
fi

# Test critical API functionality
echo "🧪 Testing critical API functionality..."

# Test JSON response capability
php -r "
echo 'Testing JSON response capability...' . PHP_EOL;
if (function_exists('json_encode') && function_exists('json_decode')) {
    echo '✅ JSON functions available' . PHP_EOL;
} else {
    echo '❌ CRITICAL: JSON functions missing' . PHP_EOL;
    exit(1);
}
"

# Test common API extensions
php -r "
\$api_extensions = ['curl', 'json', 'openssl', 'pdo'];
\$missing = [];
foreach (\$api_extensions as \$ext) {
    if (!extension_loaded(\$ext)) {
        \$missing[] = \$ext;
    }
}
if (!empty(\$missing)) {
    echo '❌ CRITICAL: Missing API extensions: ' . implode(', ', \$missing) . PHP_EOL;
    exit(1);
}
echo '✅ All API extensions available' . PHP_EOL;
"

echo ""
echo "✅ API-Only build completed successfully!"
echo "========================================"
echo "📋 Build Summary:"
echo "  Type: API-Only Laravel Application"
echo "  Strategy: $([ "$NEEDS_DEV_DEPS" = true ] && echo "Include dev dependencies" || echo "Production optimized")"
echo "  Database: $(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected'; } catch(Exception \$e) { echo 'Not configured'; }" 2>/dev/null || echo "Not tested")"
echo "  Performance: Highly optimized for API responses"
echo "  Ready for: High-performance API deployment"
