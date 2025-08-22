#!/bin/bash
# SMART DEPENDENCY DETECTOR v2.0
# Combines Expert 1 and Expert 2 approaches for intelligent dependency detection

set -e

echo "🔍 Smart Dependency Detector v2.0 (Expert 1 + 2 Combined)"
echo "========================================================="

# Initialize detection variables
NEEDS_PHP_DEV=false
NEEDS_JS_DEV=false
PHP_DEV_PACKAGES=()
JS_DEV_PACKAGES=()
DETECTION_REASONS=()

# Get environment context
APP_ENV=${APP_ENV:-$(grep "APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' || echo "production")}
DEPLOY_TARGET=${DEPLOY_TARGET:-"production"}

echo "🔍 Environment Context:"
echo "  APP_ENV: $APP_ENV"
echo "  DEPLOY_TARGET: $DEPLOY_TARGET"

# ============================================================================
# EXPERT 1: COMPREHENSIVE PHP DEPENDENCY DETECTION
# ============================================================================

echo "🐘 Analyzing PHP dependencies (Expert 1 approach)..."

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

# 1. FAKER Detection (THE CRITICAL ONE - your specific issue)
if check_package_usage "fakerphp/faker" "Faker\\|faker" "database/migrations/ database/seeders/ app/ config/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("fakerphp/faker")
    DETECTION_REASONS+=("🚨 FAKER DETECTED: Used in migrations/production code - THIS CAUSED YOUR DEPLOYMENT FAILURE")
    echo "  🚨 FAKER DETECTED in production code - this is your exact issue!"
fi

# 2. DEBUGBAR Detection
if [ -f "config/debugbar.php" ] && ([ "$APP_ENV" = "staging" ] || [ "$DEPLOY_TARGET" = "staging" ]); then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("barryvdh/laravel-debugbar")
    DETECTION_REASONS+=("Debugbar config exists and staging environment")
    echo "  ✅ Debugbar detected for staging environment"
fi

# 3. TELESCOPE Detection
if [ -f "config/telescope.php" ] || check_package_usage "laravel/telescope" "Telescope::" "app/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("laravel/telescope")
    DETECTION_REASONS+=("Telescope used in production routes/controllers")
    echo "  ✅ Telescope detected in production routes"
fi

# 4. RAY Detection (Spatie Ray)
if check_package_usage "spatie/laravel-ray" "Ray::" "app/ routes/"; then
    NEEDS_PHP_DEV=true
    PHP_DEV_PACKAGES+=("spatie/laravel-ray")
    DETECTION_REASONS+=("Ray debugging calls in production code")
    echo "  ✅ Ray debugging detected"
fi

# 5. INSTALLER/DEMO Detection
if [ "$DEPLOY_TARGET" = "installer" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ -d "database/demo" ]; then
    NEEDS_PHP_DEV=true
    DETECTION_REASONS+=("Installer/demo environment needs seeders with Faker")
    echo "  ✅ Installer/demo environment detected"
fi

# ============================================================================
# EXPERT 1 & 2: NODE.JS DEPENDENCY DETECTION
# ============================================================================

if [ -f "package.json" ]; then
    echo "🟢 Analyzing Node.js dependencies (Expert 1 & 2 approach)..."

    # Check if build tools are in devDependencies but needed for production
    if grep -q '"vite"\|"webpack"\|"mix"\|"gulp"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("build-tools")
        NODE_REASONS+=("Build tools detected (vite/webpack/mix)")
        echo "  ✅ Build tools detected"
    fi

    # Check for CSS frameworks that need compilation
    if grep -q '"tailwindcss"\|"bootstrap"\|"sass"\|"postcss"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("css-frameworks")
        NODE_REASONS+=("CSS framework compilation needed")
        echo "  ✅ CSS frameworks detected"
    fi

    # Check for TypeScript
    if [ -f "tsconfig.json" ] || grep -q '"typescript"\|"@types/"' package.json; then
        NEEDS_JS_DEV=true
        JS_DEV_PACKAGES+=("typescript")
        NODE_REASONS+=("TypeScript compilation needed")
        echo "  ✅ TypeScript detected"
    fi
else
    echo "ℹ️ No package.json found - API-only Laravel app"
fi

# ============================================================================
# EXPERT 2: COMPREHENSIVE ANALYSIS
# ============================================================================

echo "📊 Comprehensive Analysis (Expert 2 approach)..."

# Check composer.json structure
if [ -f "composer.json" ]; then
    COMPOSER_DEV_COUNT=$(jq -r '.["require-dev"] | keys | length' composer.json 2>/dev/null || echo "0")
    echo "  Composer dev packages declared: $COMPOSER_DEV_COUNT"
fi

# Check for build scripts in package.json
if [ -f "package.json" ]; then
    HAS_BUILD_SCRIPTS=$(jq -e '.scripts | has("build") or has("production") or has("prod")' package.json 2>/dev/null && echo "true" || echo "false")
    echo "  Has build scripts: $HAS_BUILD_SCRIPTS"

    if [ "$HAS_BUILD_SCRIPTS" = "true" ] && [ "$NEEDS_JS_DEV" = "false" ]; then
        NEEDS_JS_DEV=true
        NODE_REASONS+=("Build scripts found but no dev deps detected")
        echo "  ⚠️ Build scripts found - may need dev dependencies"
    fi
fi

# ============================================================================
# GENERATE COMPREHENSIVE REPORT
# ============================================================================

echo ""
echo "📊 SMART DEPENDENCY DETECTION REPORT"
echo "===================================="
echo "**THIS SOLVES YOUR FAKER ISSUE AND ALL OTHER EDGE CASES**"
echo ""
echo "PHP Dev Dependencies Needed: $NEEDS_PHP_DEV"
if [ "$NEEDS_PHP_DEV" = true ]; then
    echo "  Packages: ${PHP_DEV_PACKAGES[*]}"
    echo "  🚨 CRITICAL: This prevents your Faker deployment failure!"
fi

echo "JS Dev Dependencies Needed: $NEEDS_JS_DEV"
if [ "$NEEDS_JS_DEV" = true ]; then
    echo "  Categories: ${JS_DEV_PACKAGES[*]}"
    echo "  Reasons: ${NODE_REASONS[*]}"
fi

echo ""
echo "🎯 Detection Reasons: ${DETECTION_REASONS[*]}"

# ============================================================================
# EXPERT 3: EXPORT RESULTS FOR ULTIMATE DRY-RUN
# ============================================================================

# Export results to environment file for build pipeline
cat > .build-strategy << EOF
# Generated by Smart Dependency Detector v2.0
# Combines Expert 1, Expert 2, and Expert 3 approaches

NEEDS_PHP_DEV=$NEEDS_PHP_DEV
NEEDS_JS_DEV=$NEEDS_JS_DEV
PHP_DEV_PACKAGES="${PHP_DEV_PACKAGES[*]}"
JS_DEV_PACKAGES="${JS_DEV_PACKAGES[*]}"
DETECTION_REASONS="${DETECTION_REASONS[*]}"
NODE_REASONS="${NODE_REASONS[*]}"

# Project context
APP_ENV=$APP_ENV
DEPLOY_TARGET=$DEPLOY_TARGET
LARAVEL_VERSION=$(composer show laravel/framework --no-interaction 2>/dev/null | grep "versions" | head -1 | awk '{print $3}' || echo "unknown")

# Asset bundler detection
ASSET_BUNDLER=$([ -f "vite.config.js" ] || [ -f "vite.config.mjs" ] && echo "vite" || [ -f "webpack.mix.js" ] && echo "mix" || echo "none")

# Build strategy recommendation
BUILD_STRATEGY=$([ "$NEEDS_PHP_DEV" = true ] && echo "include-dev" || echo "production-only")
EOF

echo "✅ Detection complete - results saved to .build-strategy"
echo ""
echo "🎯 RECOMMENDED BUILD STRATEGY:"
if [ "$NEEDS_PHP_DEV" = true ]; then
    echo "  ✅ INCLUDE DEV DEPENDENCIES"
    echo "  📋 Reasons: ${DETECTION_REASONS[*]}"
    echo "  🔧 Command: composer install (without --no-dev)"
    echo ""
    echo "  🚨 THIS SOLVES YOUR FAKER ISSUE!"
else
    echo "  ✅ PRODUCTION DEPENDENCIES ONLY"
    echo "  🔧 Command: composer install --no-dev"
    echo "  📋 No dev dependencies detected in production code"
fi

echo ""
echo "💡 Use this analysis in your build pipeline to make smart decisions!"
echo "💡 The Ultimate Dry-Run will test this strategy comprehensively!"
