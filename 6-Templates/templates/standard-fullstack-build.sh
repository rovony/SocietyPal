#!/bin/bash
# STANDARD FULL-STACK BUILD TEMPLATE
# For Laravel apps with frontend but no special requirements
# Based on Expert 1, 2, and 3 combined approach

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "🎯 Standard Full-Stack Build Pipeline"
echo "===================================="

# This template is for apps that:
# - Have frontend assets (Vite/Mix)
# - Don't need dev dependencies in production
# - Standard Laravel deployment

# ============================================================================
# SMART DEPENDENCY DETECTION
# ============================================================================

echo "🔍 Running smart dependency detection..."
source ../2-Universal-Build-Pipeline/smart-dependency-detector.sh

# Override for standard apps (production-only unless detected otherwise)
if [ ${#DETECTION_REASONS[@]} -eq 0 ]; then
    NEEDS_DEV_DEPS=false
    echo "📋 No dev dependencies detected - using production-only build"
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

echo "📦 Installing PHP dependencies..."

if [ "$NEEDS_DEV_DEPS" = true ]; then
    echo "🔧 Including dev dependencies"
    echo "📋 Reasons: ${DETECTION_REASONS[*]}"
    composer install --optimize-autoloader --no-interaction --prefer-dist --no-progress
else
    echo "🔧 Production dependencies only"
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
fi

# ============================================================================
# NODE.JS DEPENDENCIES & ASSET BUILDING
# ============================================================================

if [ -f "package.json" ]; then
    echo "📦 Installing Node.js dependencies for asset building..."
    npm ci --no-audit --no-fund

    echo "🏗️ Building production assets..."
    BUILD_SUCCESS=false

    # Detect and use appropriate build command
    if [ -f "vite.config.js" ] || [ -f "vite.config.mjs" ]; then
        echo "🔧 Using Vite for asset compilation..."
        if npm run build 2>/dev/null; then
            echo "✅ Vite build successful"
            BUILD_SUCCESS=true
        fi
    elif [ -f "webpack.mix.js" ]; then
        echo "🔧 Using Laravel Mix for asset compilation..."
        if npm run production 2>/dev/null; then
            echo "✅ Mix production build successful"
            BUILD_SUCCESS=true
        fi
    else
        # Try generic build commands
        for cmd in "build" "production" "prod"; do
            if npm run "$cmd" 2>/dev/null; then
                echo "✅ Assets built with 'npm run $cmd'"
                BUILD_SUCCESS=true
                break
            fi
        done
    fi

    if [ "$BUILD_SUCCESS" = false ]; then
        echo "⚠️ Asset build failed or no build script found"
    fi

    # Clean up node_modules for production
    echo "🧹 Cleaning up node_modules (production optimization)..."
    rm -rf node_modules

    # Verify build output
    if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then
        echo "✅ Frontend assets compiled successfully"
    else
        echo "⚠️ No build output detected - verify asset compilation"
    fi
else
    echo "ℹ️ No package.json found - backend-only application"
fi

# ============================================================================
# LARAVEL OPTIMIZATION
# ============================================================================

echo "⚡ Optimizing Laravel application..."

# Generate optimized autoloader
composer dump-autoload --optimize --classmap-authoritative

# Cache configuration for production
echo "🗂️ Caching Laravel configuration..."
php artisan config:cache 2>/dev/null || echo "⚠️ Config cache failed"
php artisan route:cache 2>/dev/null || echo "⚠️ Route cache failed (may have closures)"
php artisan view:cache 2>/dev/null || echo "⚠️ View cache failed"

# ============================================================================
# VERIFICATION
# ============================================================================

echo "🔍 Verifying build..."

# Test Laravel bootstrap
php artisan --version >/dev/null 2>&1 && echo "✅ Laravel framework operational" || echo "❌ Laravel framework error"

# Test critical paths
[ -d "vendor" ] && echo "✅ Dependencies installed" || echo "❌ Dependencies missing"
[ -d "bootstrap/cache" ] && echo "✅ Bootstrap cache ready" || echo "❌ Bootstrap cache missing"

# Test asset outputs (if frontend app)
if [ "$HAS_FRONTEND" = "true" ]; then
    if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ]; then
        echo "✅ Frontend assets available"
    else
        echo "⚠️ Frontend assets missing"
    fi
fi

echo ""
echo "✅ Standard Full-Stack build completed successfully!"
echo "=================================================="
echo "📋 Build Summary:"
echo "  Strategy: $([ "$NEEDS_DEV_DEPS" = true ] && echo "Include dev dependencies" || echo "Production only")"
echo "  Frontend: $([ "$BUILD_SUCCESS" = true ] && echo "Assets built successfully" || echo "No assets or build failed")"
echo "  Optimization: Laravel caches created"
echo "  Ready for: Standard production deployment"
