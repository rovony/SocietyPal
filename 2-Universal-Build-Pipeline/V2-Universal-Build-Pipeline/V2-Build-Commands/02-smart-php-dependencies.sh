#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 02: Smart PHP Dependencies Installation
# Uses intelligent detection to determine if dev dependencies are needed

set -e
export COMPOSER_MEMORY_LIMIT=-1

echo "🐘 V2 Universal Build Command 02: Smart PHP Dependencies"
echo "========================================================"

# Source the smart dependency detector
DETECTOR_PATH="../../v2/smart-dependency-detector.sh"
if [ -f "$DETECTOR_PATH" ]; then
    echo "🔍 Running smart dependency detection..."
    source "$DETECTOR_PATH"
else
    echo "⚠️ Smart detector not found, using fallback logic..."

    # Fallback detection
    NEEDS_PHP_DEV=false
    if grep -r "Faker\|faker" database/ app/ 2>/dev/null >/dev/null; then
        NEEDS_PHP_DEV=true
        echo "🚨 Faker detected in code - dev dependencies needed"
    fi
fi

# Install dependencies based on detection
echo "📦 Installing PHP dependencies..."

if [ "$NEEDS_PHP_DEV" = true ]; then
    echo "🔧 SMART MODE: Installing ALL dependencies (dev needed in production)"
    echo "📋 Reasons: ${DETECTION_REASONS[*]:-"Fallback detection found dev usage"}"

    composer install \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --no-progress

    echo "✅ All dependencies installed (including dev)"

else
    echo "🔧 SMART MODE: Installing PRODUCTION dependencies only"
    echo "📋 No dev dependencies detected in production code"

    composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --no-progress

    echo "✅ Production dependencies installed"
fi

# Verify installation
echo "🔍 Verifying installation..."

if [ -d "vendor" ]; then
    echo "  ✅ vendor directory exists"
else
    echo "  ❌ vendor directory missing"
    exit 1
fi

if [ -f "vendor/autoload.php" ]; then
    echo "  ✅ Composer autoloader available"
else
    echo "  ❌ Composer autoloader missing"
    exit 1
fi

# Test Laravel framework availability
php -r "
require_once 'vendor/autoload.php';
if (class_exists('Illuminate\Foundation\Application')) {
    echo '  ✅ Laravel framework loaded' . PHP_EOL;
} else {
    echo '  ❌ Laravel framework not available' . PHP_EOL;
    exit(1);
}
"

echo ""
echo "✅ PHP dependencies installed and verified successfully!"
echo "🎯 Strategy: $([ "$NEEDS_PHP_DEV" = true ] && echo "Include dev dependencies" || echo "Production only")"
