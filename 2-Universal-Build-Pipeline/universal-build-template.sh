#!/bin/bash
# UNIVERSAL LARAVEL BUILD PIPELINE v4.0
# Combines Expert 1, Expert 2, and Expert 3 approaches
# Works with ANY Laravel application - detects and installs only needed dependencies

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "🚀 Universal Laravel Build Pipeline v4.0"
echo "Combining Expert 1, 2, and 3 approaches for 100% compatibility"
echo "================================================"

# ============================================================================
# PHASE 1: ENVIRONMENT & PROJECT DETECTION (EXPERT 1 & 2 ENHANCED)
# ============================================================================

APP_ENV=${APP_ENV:-$(grep "APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' || echo "production")}
DEPLOY_TARGET=${DEPLOY_TARGET:-"production"}  # production, staging, installer, demo
BUILD_MODE=${BUILD_MODE:-"auto"}              # auto, minimal, full

echo "🔍 Environment: $APP_ENV"
echo "🔍 Deploy Target: $DEPLOY_TARGET"
echo "🔍 Build Mode: $BUILD_MODE"

# Detect Laravel version and requirements
LARAVEL_VERSION=$(composer show laravel/framework --no-interaction 2>/dev/null | grep "versions" | head -1 | awk '{print $3}' || echo "unknown")
PHP_CONSTRAINT=$(jq -r '.require.php // "^8.0"' composer.json 2>/dev/null || echo "^8.0")

# Detect asset bundler
if [ -f "vite.config.js" ] || [ -f "vite.config.mjs" ]; then
  ASSET_BUNDLER="vite"
elif [ -f "webpack.mix.js" ]; then
  ASSET_BUNDLER="mix"
else
  ASSET_BUNDLER="none"
fi

echo "📋 Project Analysis:"
echo "  Laravel: $LARAVEL_VERSION"
echo "  PHP Constraint: $PHP_CONSTRAINT"
echo "  Asset Bundler: $ASSET_BUNDLER"

# ============================================================================
# PHASE 2: SMART PHP DEPENDENCY DETECTION (EXPERT 1 ENHANCED)
# ============================================================================

echo "🔍 Analyzing PHP dependency requirements..."

NEEDS_DEV_DEPS=false
REQUIRED_DEV_PACKAGES=()
DETECTION_REASONS=()

# Function to check if package is used in production code
check_package_usage() {
    local package_name="$1"
    local search_patterns="$2"
    local search_paths="$3"

    if echo "$search_paths" | xargs -I {} find {} -name "*.php" -exec grep -l "$search_patterns" {} \; 2>/dev/null | grep -v vendor/ | grep -v node_modules/ | head -1; then
        return 0  # Found
    else
        return 1  # Not found
    fi
}

# FAKER Detection (most common issue)
if check_package_usage "fakerphp/faker" "Faker\\|faker" "database/migrations/ database/seeders/ app/ config/ routes/"; then
    NEEDS_DEV_DEPS=true
    REQUIRED_DEV_PACKAGES+=("fakerphp/faker")
    DETECTION_REASONS+=("Faker used in migrations/production code")
fi

# DEBUGBAR Detection
if [ -f "config/debugbar.php" ] && ([ "$APP_ENV" = "staging" ] || [ "$DEPLOY_TARGET" = "staging" ]); then
    NEEDS_DEV_DEPS=true
    REQUIRED_DEV_PACKAGES+=("barryvdh/laravel-debugbar")
    DETECTION_REASONS+=("Debugbar config exists and staging environment")
fi

# TELESCOPE Detection
if [ -f "config/telescope.php" ] || check_package_usage "laravel/telescope" "Telescope::" "app/ routes/"; then
    NEEDS_DEV_DEPS=true
    REQUIRED_DEV_PACKAGES+=("laravel/telescope")
    DETECTION_REASONS+=("Telescope used in production routes/controllers")
fi

# RAY Detection (Spatie Ray)
if check_package_usage "spatie/laravel-ray" "Ray::" "app/ routes/"; then
    NEEDS_DEV_DEPS=true
    REQUIRED_DEV_PACKAGES+=("spatie/laravel-ray")
    DETECTION_REASONS+=("Ray debugging calls in production code")
fi

# INSTALLER/DEMO Detection
if [ "$DEPLOY_TARGET" = "installer" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ -d "database/demo" ]; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("Installer/demo environment needs seeders")
fi

# ============================================================================
# PHASE 3: SMART NODE.JS DEPENDENCY DETECTION (EXPERT 1 & 2)
# ============================================================================

NEEDS_JS_DEV=false
NODE_REASONS=()

if [ -f "package.json" ]; then
    echo "🔍 Analyzing Node.js dependency requirements..."

    # Check if build tools are in devDependencies but needed for production
    if grep -q '"vite"\|"webpack"\|"mix"\|"gulp"' package.json; then
        NEEDS_JS_DEV=true
        NODE_REASONS+=("Build tools detected (vite/webpack/mix)")
    fi

    # Check for CSS frameworks that need compilation
    if grep -q '"tailwindcss"\|"bootstrap"\|"sass"\|"postcss"' package.json; then
        NEEDS_JS_DEV=true
        NODE_REASONS+=("CSS framework compilation needed")
    fi

    # Check for TypeScript
    if [ -f "tsconfig.json" ] || grep -q '"typescript"\|"@types/"' package.json; then
        NEEDS_JS_DEV=true
        NODE_REASONS+=("TypeScript compilation needed")
    fi

    # Check for testing in production (rare but possible)
    if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then
        if grep -q '"jest"\|"cypress"\|"playwright"\|"vitest"' package.json; then
            NEEDS_JS_DEV=true
            NODE_REASONS+=("Testing tools needed in staging/demo")
        fi
    fi
fi

# ============================================================================
# PHASE 4: LARAVEL DIRECTORY SETUP (UNIVERSAL)
# ============================================================================

echo "📁 Setting up Laravel directory structure..."
mkdir -p bootstrap/cache \
         storage/{app/public,framework/{cache/data,sessions,testing,views},logs,clockwork,debugbar} \
         public/storage

chmod -R 755 bootstrap/cache storage
find storage -type d -exec chmod 775 {} \;
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true

# ============================================================================
# PHASE 5: SMART PHP DEPENDENCY INSTALLATION
# ============================================================================

echo "📦 Smart PHP Dependency Installation Strategy:"

if [ "$BUILD_MODE" = "full" ]; then
    echo "🔧 FULL MODE: Installing ALL dependencies (forced)"
    composer install --optimize-autoloader --no-interaction --prefer-dist --no-progress
elif [ "$BUILD_MODE" = "minimal" ]; then
    echo "🔧 MINIMAL MODE: Installing production dependencies only (forced)"
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
elif [ "$NEEDS_DEV_DEPS" = true ]; then
    echo "🔧 SMART MODE: Installing ALL dependencies"
    echo "📋 Reasons: ${DETECTION_REASONS[*]}"
    echo "📦 Required dev packages: ${REQUIRED_DEV_PACKAGES[*]}"
    composer install --optimize-autoloader --no-interaction --prefer-dist --no-progress
else
    echo "🔧 SMART MODE: Installing PRODUCTION dependencies only"
    echo "📋 No dev dependencies detected in production code"
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
fi

# ============================================================================
# PHASE 6: SMART NODE.JS DEPENDENCY INSTALLATION
# ============================================================================

if [ -f "package.json" ]; then
    echo "📦 Smart Node.js Dependency Installation Strategy:"

    if [ "$BUILD_MODE" = "full" ]; then
        echo "🔧 FULL MODE: Installing ALL Node.js dependencies"
        npm ci --no-audit --no-fund
    elif [ "$BUILD_MODE" = "minimal" ]; then
        echo "🔧 MINIMAL MODE: Installing production Node.js dependencies only"
        npm ci --production --no-audit --no-fund
    elif [ "$NEEDS_JS_DEV" = true ]; then
        echo "🔧 SMART MODE: Installing ALL Node.js dependencies"
        echo "📋 Reasons: ${NODE_REASONS[*]}"
        npm ci --no-audit --no-fund
    else
        echo "🔧 SMART MODE: Installing PRODUCTION Node.js dependencies only"
        npm ci --production --no-audit --no-fund
    fi

    # ASSET BUILDING
    echo "🏗️ Building frontend assets..."
    BUILD_SUCCESS=false

    # Try different build commands in order of preference (EXPERT 2)
    for cmd in "build" "production" "prod" "compile"; do
        if npm run "$cmd" 2>/dev/null; then
            echo "✅ Assets built with 'npm run $cmd'"
            BUILD_SUCCESS=true
            break
        fi
    done

    if [ "$BUILD_SUCCESS" = false ]; then
        echo "ℹ️ No suitable build script found, checking for manual build commands..."
        # Check for common build tools
        if command -v vite >/dev/null 2>&1; then
            vite build && BUILD_SUCCESS=true
        elif command -v webpack >/dev/null 2>&1; then
            webpack --mode=production && BUILD_SUCCESS=true
        fi
    fi

    [ "$BUILD_SUCCESS" = false ] && echo "⚠️ No asset build performed"

    # EXPERT 2: CLEANUP for production
    if [ "$NEEDS_JS_DEV" = false ] || [ "$BUILD_MODE" = "minimal" ]; then
        echo "🧹 Cleaning up node_modules..."
        rm -rf node_modules
    fi

    # VERIFY build output
    if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then
        echo "✅ Frontend assets compiled successfully"
    else
        echo "ℹ️ No build output detected (may be API-only app)"
    fi
else
    echo "ℹ️ No package.json found - API-only Laravel app"
fi

# ============================================================================
# PHASE 7: LARAVEL OPTIMIZATION
# ============================================================================

echo "⚡ Optimizing Laravel application..."

# Generate optimized autoloader (EXPERT 2)
composer dump-autoload --optimize --classmap-authoritative

# Cache configuration if not in local development
if [ "$APP_ENV" != "local" ]; then
    php artisan config:cache 2>/dev/null || echo "⚠️ Config cache failed (may be normal for some apps)"
    php artisan route:cache 2>/dev/null || echo "ℹ️ Route cache skipped (closures detected or API-only)"
    php artisan view:cache 2>/dev/null || echo "ℹ️ View cache skipped (no views or API-only)"
fi

# ============================================================================
# PHASE 8: COMPREHENSIVE VERIFICATION (EXPERT 3)
# ============================================================================

echo "🔍 Comprehensive verification..."

php -r "
require_once 'vendor/autoload.php';
echo '🔍 Laravel Framework: ' . (class_exists('Illuminate\Foundation\Application') ? '✅ OK' : '❌ MISSING') . PHP_EOL;

// Check for specific requirements based on detection
\$issues = [];

// Faker check
if (file_exists('database/migrations')) {
    \$fakerInMigrations = shell_exec('find database/migrations/ -name \"*.php\" -exec grep -l \"Faker\" {} \; 2>/dev/null');
    if (\$fakerInMigrations && !class_exists('Faker\Factory')) {
        \$issues[] = 'Faker required by migrations but not available';
    } elseif (\$fakerInMigrations) {
        echo '🔍 Faker (migrations): ✅ OK' . PHP_EOL;
    }
}

// Debugbar check
if (file_exists('config/debugbar.php') && getenv('APP_ENV') !== 'production') {
    if (!class_exists('Barryvdh\Debugbar\ServiceProvider')) {
        \$issues[] = 'Debugbar config exists but package not available';
    } else {
        echo '🔍 Debugbar: ✅ OK' . PHP_EOL;
    }
}

// Telescope check
if (file_exists('config/telescope.php')) {
    if (!class_exists('Laravel\Telescope\TelescopeServiceProvider')) {
        \$issues[] = 'Telescope config exists but package not available';
    } else {
        echo '🔍 Telescope: ✅ OK' . PHP_EOL;
    }
}

if (!empty(\$issues)) {
    echo '❌ CRITICAL ISSUES DETECTED:' . PHP_EOL;
    foreach (\$issues as \$issue) {
        echo '   - ' . \$issue . PHP_EOL;
    }
    exit(1);
}

echo '✅ All required dependencies verified' . PHP_EOL;
"

echo "✅ Universal Laravel build completed successfully!"
echo "================================================"
echo "📋 Build Summary:"
echo "  Strategy: $([ "$NEEDS_DEV_DEPS" = true ] && echo "Include dev dependencies" || echo "Production only")"
echo "  Reasons: ${DETECTION_REASONS[*]}"
echo "  Asset Bundler: $ASSET_BUNDLER"
echo "  Build Success: $([ "$BUILD_SUCCESS" = true ] && echo "Yes" || echo "No assets built")"
