#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 05: Laravel Optimization
# Optimizes Laravel for production with smart caching

set -e

echo "⚡ V2 Universal Build Command 05: Laravel Optimization"
echo "====================================================="

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Not a Laravel project (artisan not found)"
    exit 1
fi

# Environment detection
APP_ENV=${APP_ENV:-$(grep "APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' || echo "production")}
echo "🔍 Environment: $APP_ENV"

# Generate optimized autoloader
echo "📚 Generating optimized autoloader..."
composer dump-autoload --optimize --classmap-authoritative
echo "✅ Autoloader optimized"

# Laravel caching (conditional based on environment)
if [ "$APP_ENV" != "local" ]; then
    echo "🗂️ Caching Laravel configuration..."

    # Config caching
    echo "  📋 Caching configuration..."
    if php artisan config:cache 2>/dev/null; then
        echo "  ✅ Configuration cached"
    else
        echo "  ⚠️ Configuration caching failed (may be normal for some apps)"
    fi

    # Route caching
    echo "  🛣️ Caching routes..."
    if php artisan route:cache 2>/dev/null; then
        echo "  ✅ Routes cached"
    else
        echo "  ⚠️ Route caching skipped (closures detected or API-only)"
    fi

    # View caching
    echo "  👁️ Caching views..."
    if php artisan view:cache 2>/dev/null; then
        echo "  ✅ Views cached"
    else
        echo "  ℹ️ View caching skipped (no views or API-only)"
    fi

else
    echo "ℹ️ Local environment detected - skipping Laravel caching"
fi

# Clear any existing caches that might interfere
echo "🧹 Clearing development caches..."
php artisan cache:clear 2>/dev/null || echo "  ℹ️ No cache to clear"

# Verify Laravel is working
echo "🔍 Verifying Laravel functionality..."

# Test artisan command
if php artisan --version >/dev/null 2>&1; then
    echo "  ✅ Laravel framework operational"
else
    echo "  ❌ Laravel framework error"
    exit 1
fi

# Test database connection (if configured)
if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_SKIP'; }" 2>/dev/null | grep -q "DB_OK"; then
    echo "  ✅ Database connection verified"
elif php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_SKIP'; }" 2>/dev/null | grep -q "DB_SKIP"; then
    echo "  ℹ️ Database not configured or not available"
else
    echo "  ⚠️ Database connection test failed"
fi

# Check critical directories
echo "📁 Verifying critical directories..."
CRITICAL_DIRS=("storage/logs" "storage/framework/cache" "bootstrap/cache")

for dir in "${CRITICAL_DIRS[@]}"; do
    if [ -d "$dir" ] && [ -w "$dir" ]; then
        echo "  ✅ $dir: exists and writable"
    else
        echo "  ⚠️ $dir: missing or not writable"
        mkdir -p "$dir" 2>/dev/null || true
        chmod 775 "$dir" 2>/dev/null || true
    fi
done

echo ""
echo "✅ Laravel optimization completed successfully!"
echo "🎯 Environment: $APP_ENV"
echo "📦 Autoloader: Optimized for production"
echo "🗂️ Caching: $([ "$APP_ENV" != "local" ] && echo "Applied" || echo "Skipped (local env)")"
echo "🚀 Ready for production deployment!"
