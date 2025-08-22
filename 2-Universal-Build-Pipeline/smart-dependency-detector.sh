#!/bin/bash
# SMART DEPENDENCY DETECTOR v2.0
# Analyzes Laravel projects to determine exact build requirements
# Based on Expert 1 and Expert 2 approaches

set -e

echo "🔍 Smart Dependency Detector v2.0"
echo "=================================="

# Initialize detection variables
NEEDS_PHP_DEV=false
NEEDS_JS_DEV=false
PHP_DEV_PACKAGES=()
JS_DEV_PACKAGES=()
DETECTION_REASONS=()

# ============================================================================
# PHP DEPENDENCY DETECTION MATRIX
# ============================================================================

echo "🐘 Analyzing PHP dependencies..."

# Function to check if package is used in production code
check_package_usage() {
    local package_name="$1"
    local search_patterns="$2"
    local search_paths="$3"

    if echo "$search_paths" | xargs -I {} find {} -name "*.php" -exec grep -l "$search_patterns" {} \; 2>/dev/null | grep -v vendor/ | head -1; then
        return 0  # Found
    else
        return 1  # Not found
    fi
}

# 1. FAKER Detection (Expert 1)
if check_package_usage "fakerphp/faker" "Faker\\|faker" "database/migrations/ database/seeders/ app/ config/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("fakerphp/faker")
    DETECTION_REASONS+=("Faker used in migrations/production code")
    echo "  ✅ Faker detected in production code"
fi

# 2. DEBUGBAR Detection
if [ -f "config/debugbar.php" ]; then
    # Check if enabled in non-production environments
    if grep -q "APP_ENV.*staging\|APP_ENV.*demo" .env 2>/dev/null || [ "$APP_ENV" = "staging" ]; then
        NEEDS_PHP_DEV=true
        PHP_DEV_PACKAGES+=("barryvdh/laravel-debugbar")
        DETECTION_REASONS+=("Debugbar config exists for staging/demo")
        echo "  ✅ Debugbar detected for staging environment"
    fi
fi

# 3. TELESCOPE Detection
if [ -f "config/telescope.php" ] || check_package_usage "laravel/telescope" "Telescope::" "app/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("laravel/telescope")
    DETECTION_REASONS+=("Telescope used in production routes/controllers")
    echo "  ✅ Telescope detected in production routes"
fi

# 4. CLOCKWORK Detection
if [ -f "config/clockwork.php" ] || check_package_usage "itsgoingd/clockwork" "Clockwork::" "app/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("itsgoingd/clockwork")
    DETECTION_REASONS+=("Clockwork profiling enabled")
    echo "  ✅ Clockwork detected"
fi

# 5. RAY Detection (Spatie Ray)
if check_package_usage "spatie/laravel-ray" "Ray::" "app/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("spatie/laravel-ray")
    DETECTION_REASONS+=("Ray debugging calls in production code")
    echo "  ✅ Ray debugging detected"
fi

# 6. PHPUNIT Detection (rare but possible)
if check_package_usage "phpunit/phpunit" "PHPUnit\\|TestCase" "app/ routes/" || [ -f "routes/test.php" ]; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("phpunit/phpunit")
    DETECTION_REASONS+=("PHPUnit used in production routes/controllers")
    echo "  ✅ PHPUnit detected in production code"
fi

# 7. ENVIRONMENT-BASED Detection
case "$DEPLOY_TARGET" in
    "installer"|"demo")
        NEEDS_PHP_DEV=true
        DETECTION_REASONS+=("Installer/demo environment requires seeders")
        echo "  ✅ Installer/demo environment detected"
        ;;
    "staging")
        if [ -f "config/debugbar.php" ]; then
            NEEDS_PHP_DEV=true
            PHP_DEV_PACKAGES+=("barryvdh/laravel-debugbar")
            DETECTION_REASONS+=("Staging environment with debugbar config")
            echo "  ✅ Staging environment with debugging tools"
        fi
        ;;
esac

# ============================================================================
# NODE.JS DEPENDENCY DETECTION MATRIX
# ============================================================================

if [ -f "package.json" ]; then
    echo "🟢 Analyzing Node.js dependencies..."

    # 1. Build Tools Detection
    if grep -q '"vite"\|"webpack"\|"mix"\|"gulp"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("build-tools")
        NODE_REASONS+=("Build tools detected (vite/webpack/mix)")
        echo "  ✅ Build tools detected"
    fi

    # 2. CSS Framework Detection
    if grep -q '"tailwindcss"\|"bootstrap"\|"sass"\|"postcss"\|"autoprefixer"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("css-frameworks")
        NODE_REASONS+=("CSS framework compilation needed")
        echo "  ✅ CSS frameworks detected"
    fi

    # 3. TypeScript Detection
    if [ -f "tsconfig.json" ] || grep -q '"typescript"\|"@types/"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("typescript")
        NODE_REASONS+=("TypeScript compilation needed")
        echo "  ✅ TypeScript detected"
    fi

    # 4. Framework Detection
    if grep -q '"vue"\|"@vue/"\|"react"\|"@react/"\|"@inertiajs/"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("frontend-framework")
        NODE_REASONS+=("Frontend framework detected")
        echo "  ✅ Frontend framework detected"
    fi

    # 5. Testing Tools (for staging/demo)
    if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then
        if grep -q '"jest"\|"cypress"\|"playwright"\|"vitest"' package.json; then
            NEEDS_JS_DEV=true
            JS_DEV_PACKAGES+=("testing-tools")
            NODE_REASONS+=("Testing tools needed in staging/demo")
            echo "  ✅ Testing tools detected for staging/demo"
        fi
    fi
else
    echo "ℹ️ No package.json found - API-only Laravel app"
fi

# ============================================================================
# GENERATE DETECTION REPORT
# ============================================================================

echo ""
echo "📊 SMART DEPENDENCY DETECTION REPORT"
echo "===================================="
echo "PHP Dev Dependencies Needed: $NEEDS_PHP_DEV"
if [ "$NEEDS_PHP_DEV" = true ]; then
    echo "  Packages: ${PHP_DEV_PACKAGES[*]}"
fi

echo "JS Dev Dependencies Needed: $NEEDS_JS_DEV"
if [ "$NEEDS_JS_DEV" = true ]; then
    echo "  Categories: ${JS_DEV_PACKAGES[*]}"
    echo "  Reasons: ${NODE_REASONS[*]}"
fi

echo ""
echo "Detection Reasons: ${DETECTION_REASONS[*]}"

# ============================================================================
# EXPORT RESULTS FOR BUILD PIPELINE
# ============================================================================

# Export results to environment file for build pipeline
cat > .build-strategy << EOF
NEEDS_PHP_DEV=$NEEDS_PHP_DEV
NEEDS_JS_DEV=$NEEDS_JS_DEV
PHP_DEV_PACKAGES="${PHP_DEV_PACKAGES[*]}"
JS_DEV_PACKAGES="${JS_DEV_PACKAGES[*]}"
DETECTION_REASONS="${DETECTION_REASONS[*]}"
NODE_REASONS="${NODE_REASONS[*]}"
ASSET_BUNDLER=$ASSET_BUNDLER
LARAVEL_VERSION=$LARAVEL_VERSION
APP_ENV=$APP_ENV
DEPLOY_TARGET=$DEPLOY_TARGET
BUILD_MODE=$BUILD_MODE
EOF

echo "✅ Detection complete - results saved to .build-strategy"
echo "💡 Use this file in your build pipeline to make smart decisions"
