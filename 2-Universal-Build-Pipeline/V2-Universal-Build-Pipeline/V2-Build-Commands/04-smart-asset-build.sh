#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 04: Smart Asset Building & Optimization
# Detects build system and compiles assets accordingly

set -e

echo "🎨 V2 Universal Build Command 04: Smart Asset Building"
echo "====================================================="

# Check if frontend assets exist
if [ ! -f "package.json" ]; then
    echo "ℹ️ No package.json found - skipping asset building"
    echo "✅ API-only application - no assets to build"
    exit 0
fi

# Detect asset bundler
ASSET_BUNDLER="none"
BUILD_COMMAND=""

if [ -f "vite.config.js" ] || [ -f "vite.config.mjs" ]; then
    ASSET_BUNDLER="vite"
    BUILD_COMMAND="build"
    echo "🔧 Detected: Vite asset bundler"
elif [ -f "webpack.mix.js" ]; then
    ASSET_BUNDLER="mix"
    BUILD_COMMAND="production"
    echo "🔧 Detected: Laravel Mix asset bundler"
else
    echo "🔧 No specific bundler detected - checking package.json scripts..."
fi

# Try to find build command from package.json
if [ -z "$BUILD_COMMAND" ]; then
    if jq -e '.scripts.build' package.json >/dev/null 2>&1; then
        BUILD_COMMAND="build"
        echo "📋 Found 'build' script in package.json"
    elif jq -e '.scripts.production' package.json >/dev/null 2>&1; then
        BUILD_COMMAND="production"
        echo "📋 Found 'production' script in package.json"
    elif jq -e '.scripts.prod' package.json >/dev/null 2>&1; then
        BUILD_COMMAND="prod"
        echo "📋 Found 'prod' script in package.json"
    else
        echo "⚠️ No build script found - skipping asset compilation"
        exit 0
    fi
fi

# Build assets
echo "🏗️ Building production assets..."
echo "📋 Running: npm run $BUILD_COMMAND"

if npm run "$BUILD_COMMAND"; then
    echo "✅ Asset build completed successfully"
    BUILD_SUCCESS=true
else
    echo "❌ Asset build failed"
    BUILD_SUCCESS=false
fi

# Verify build output
echo "🔍 Verifying build output..."

BUILD_OUTPUT_FOUND=false

# Check common output directories
if [ "$ASSET_BUNDLER" = "vite" ]; then
    if [ -d "public/build" ] && [ -f "public/build/manifest.json" ]; then
        echo "  ✅ Vite build output found in public/build/"
        BUILD_OUTPUT_FOUND=true
    fi
elif [ "$ASSET_BUNDLER" = "mix" ]; then
    if [ -d "public/js" ] || [ -d "public/css" ]; then
        echo "  ✅ Mix build output found in public/js/ and public/css/"
        BUILD_OUTPUT_FOUND=true
    fi
else
    # Generic check for common output directories
    for dir in "public/build" "public/dist" "public/js" "public/css" "public/assets"; do
        if [ -d "$dir" ]; then
            echo "  ✅ Build output found in $dir/"
            BUILD_OUTPUT_FOUND=true
            break
        fi
    done
fi

# Final verification
if [ "$BUILD_SUCCESS" = true ] && [ "$BUILD_OUTPUT_FOUND" = true ]; then
    echo ""
    echo "✅ Asset building completed successfully!"
    echo "🎯 Bundler: $ASSET_BUNDLER"
    echo "📦 Build command: npm run $BUILD_COMMAND"
    echo "📁 Output verified and ready for production"
elif [ "$BUILD_SUCCESS" = true ] && [ "$BUILD_OUTPUT_FOUND" = false ]; then
    echo ""
    echo "⚠️ Build completed but output verification failed"
    echo "💡 Assets may have been built to a different location"
    echo "🔍 Manual verification recommended"
else
    echo ""
    echo "❌ Asset building failed"
    echo "💡 Check build configuration and dependencies"
    exit 1
fi
