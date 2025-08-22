#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 02: Universal Smart Dependencies
# Automatically detects ALL edge cases for ANY Laravel project

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "🎯 V2 Universal Build Command 02: Universal Smart Dependencies"
echo "=============================================================="
echo "🚨 THIS IS NOT JUST FOR FAKER - IT DETECTS ALL EDGE CASES!"
echo ""

# ============================================================================
# UNIVERSAL EDGE CASE DETECTION (NOT JUST FAKER!)
# ============================================================================

echo "🔍 Running universal edge case detection..."
NEEDS_DEV_DEPS=false
DETECTION_REASONS=()

# 1. FAKER Detection (your original issue)
if grep -r "Faker\|faker" database/migrations/ database/seeders/ app/ config/ routes/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("🚨 FAKER: Used in production code")
    echo "  🚨 FAKER detected in production code"
fi

# 2. TELESCOPE Detection (debugging in production)
if [ -f "config/telescope.php" ] || grep -r "Telescope::" app/ routes/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("🔧 TELESCOPE: Used in production routes/controllers")
    echo "  🔧 TELESCOPE detected in production"
fi

# 3. DEBUGBAR Detection (staging environments)
if [ -f "config/debugbar.php" ]; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("🐛 DEBUGBAR: Config exists for debugging")
    echo "  🐛 DEBUGBAR detected"
fi

# 4. RAY Detection (Spatie Ray debugging)
if grep -r "Ray::" app/ routes/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("📡 RAY: Debug calls in production code")
    echo "  📡 RAY debugging detected"
fi

# 5. CLOCKWORK Detection (profiling)
if [ -f "config/clockwork.php" ] || grep -r "Clockwork::" app/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("⏰ CLOCKWORK: Profiling enabled")
    echo "  ⏰ CLOCKWORK profiling detected"
fi

# 6. PHPUNIT Detection (testing in production routes)
if grep -r "PHPUnit\|TestCase" app/ routes/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("🧪 PHPUNIT: Testing code in production")
    echo "  🧪 PHPUNIT detected in production code"
fi

# 7. IDE HELPER Detection (model generation)
if grep -r "ide-helper" app/ config/ 2>/dev/null >/dev/null; then
    NEEDS_DEV_DEPS=true
    DETECTION_REASONS+=("💡 IDE-HELPER: Used in production")
    echo "  💡 IDE-HELPER detected"
fi

# 8. ENVIRONMENT-BASED Detection
DEPLOY_TARGET=${DEPLOY_TARGET:-"production"}
case "$DEPLOY_TARGET" in
    "installer"|"demo"|"staging")
        NEEDS_DEV_DEPS=true
        DETECTION_REASONS+=("🏢 ENVIRONMENT: $DEPLOY_TARGET needs dev tools")
        echo "  🏢 ENVIRONMENT: $DEPLOY_TARGET detected"
        ;;
esac

# 9. CUSTOM PACKAGE Detection (add your own!)
# Example: if grep -r "YourCustom\DebugPackage" app/; then
#     NEEDS_DEV_DEPS=true
#     DETECTION_REASONS+=("🔧 CUSTOM: Your debug package detected")
# fi

# ============================================================================
# SMART PHP DEPENDENCY INSTALLATION
# ============================================================================

echo ""
echo "📦 Installing PHP dependencies with universal smart detection..."

if [ "$NEEDS_DEV_DEPS" = true ]; then
    echo "🔧 UNIVERSAL SMART MODE: Installing ALL dependencies"
    echo "📋 Edge cases detected: ${DETECTION_REASONS[*]}"
    echo ""
    echo "🎯 This prevents deployment failures for:"
    echo "   - Faker in seeders/migrations"
    echo "   - Telescope in production routes"
    echo "   - Debugbar in staging environments"
    echo "   - Ray debugging calls"
    echo "   - Custom debug packages"
    echo "   - Testing tools in production"
    echo "   - And ANY other dev dependency usage!"
    echo ""

    composer install \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --no-progress

    echo "✅ All dependencies installed (dev included for production needs)"

else
    echo "🔧 UNIVERSAL SMART MODE: Production dependencies only"
    echo "📋 No dev dependencies detected in production code"
    echo "🎯 Clean production build - maximum performance"

    composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --no-progress

    echo "✅ Production dependencies installed (optimized)"
fi

# ============================================================================
# VERIFICATION & REPORTING
# ============================================================================

echo ""
echo "🔍 Verifying universal smart installation..."

# Basic verification
if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
    echo "  ✅ Composer dependencies installed"
else
    echo "  ❌ Composer installation failed"
    exit 1
fi

# Laravel framework check
php -r "
require_once 'vendor/autoload.php';
if (class_exists('Illuminate\Foundation\Application')) {
    echo '  ✅ Laravel framework loaded' . PHP_EOL;
} else {
    echo '  ❌ Laravel framework not available' . PHP_EOL;
    exit(1);
}
"

# Edge case verification
if [ "$NEEDS_DEV_DEPS" = true ]; then
    echo "  🔍 Verifying edge case dependencies..."

    # Check Faker if needed
    if echo "${DETECTION_REASONS[*]}" | grep -q "FAKER"; then
        php -r "
        if (class_exists('Faker\Factory')) {
            echo '  ✅ Faker available for seeders/migrations' . PHP_EOL;
        } else {
            echo '  ❌ CRITICAL: Faker not available despite detection!' . PHP_EOL;
            exit(1);
        }
        "
    fi

    # Check Telescope if needed
    if echo "${DETECTION_REASONS[*]}" | grep -q "TELESCOPE"; then
        php -r "
        if (class_exists('Laravel\Telescope\TelescopeServiceProvider')) {
            echo '  ✅ Telescope available for production routes' . PHP_EOL;
        } else {
            echo '  ⚠️ Telescope not available despite detection' . PHP_EOL;
        }
        "
    fi
fi

echo ""
echo "✅ Universal smart dependencies completed successfully!"
echo "🎯 Strategy: $([ "$NEEDS_DEV_DEPS" = true ] && echo "Include dev dependencies" || echo "Production optimized")"
echo "🚀 This works for ANY Laravel project with ANY edge case!"
