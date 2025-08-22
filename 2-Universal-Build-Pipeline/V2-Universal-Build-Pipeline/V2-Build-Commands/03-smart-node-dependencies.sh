#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 03: Smart Node.js Dependencies Installation
# Detects if frontend build is needed and installs accordingly

set -e

echo "🟢 V2 Universal Build Command 03: Smart Node.js Dependencies"
echo "============================================================"

# Check if this is a frontend application
if [ ! -f "package.json" ]; then
    echo "ℹ️ No package.json found - API-only Laravel application"
    echo "✅ Skipping Node.js dependencies (not needed)"
    exit 0
fi

# Detect asset bundler
ASSET_BUNDLER="none"
if [ -f "vite.config.js" ] || [ -f "vite.config.mjs" ]; then
    ASSET_BUNDLER="vite"
    echo "🔧 Detected asset bundler: Vite"
elif [ -f "webpack.mix.js" ]; then
    ASSET_BUNDLER="mix"
    echo "🔧 Detected asset bundler: Laravel Mix"
else
    echo "🔧 No specific asset bundler detected"
fi

# Determine if dev dependencies are needed for build
NEEDS_JS_DEV=false
NODE_REASONS=()

# Check for build tools
if grep -q '"vite"\|"webpack"\|"mix"\|"gulp"' package.json; then
    NEEDS_JS_DEV=true
    NODE_REASONS+=("Build tools detected")
fi

# Check for CSS frameworks
if grep -q '"tailwindcss"\|"bootstrap"\|"sass"\|"postcss"' package.json; then
    NEEDS_JS_DEV=true
    NODE_REASONS+=("CSS framework compilation needed")
fi

# Check for TypeScript
if [ -f "tsconfig.json" ] || grep -q '"typescript"\|"@types/"' package.json; then
    NEEDS_JS_DEV=true
    NODE_REASONS+=("TypeScript compilation needed")
fi

# Check for build scripts
if grep -q '"build"\|"production"\|"prod"' package.json; then
    NEEDS_JS_DEV=true
    NODE_REASONS+=("Build scripts detected")
fi

# Install dependencies
echo "📦 Installing Node.js dependencies..."

if [ "$NEEDS_JS_DEV" = true ]; then
    echo "🔧 SMART MODE: Installing ALL Node.js dependencies (build tools needed)"
    echo "📋 Reasons: ${NODE_REASONS[*]}"

    npm ci --no-audit --no-fund
    echo "✅ All Node.js dependencies installed"

else
    echo "🔧 SMART MODE: Installing PRODUCTION Node.js dependencies only"
    npm ci --production --no-audit --no-fund
    echo "✅ Production Node.js dependencies installed"
fi

# Verify installation
echo "🔍 Verifying installation..."

if [ -d "node_modules" ]; then
    echo "  ✅ node_modules directory exists"
else
    echo "  ❌ node_modules directory missing"
    exit 1
fi

# Test build tools availability if needed
if [ "$NEEDS_JS_DEV" = true ]; then
    if [ "$ASSET_BUNDLER" = "vite" ]; then
        if npm list vite >/dev/null 2>&1; then
            echo "  ✅ Vite available for building"
        else
            echo "  ⚠️ Vite not available - may need to move from devDependencies"
        fi
    elif [ "$ASSET_BUNDLER" = "mix" ]; then
        if npm list laravel-mix >/dev/null 2>&1; then
            echo "  ✅ Laravel Mix available for building"
        else
            echo "  ⚠️ Laravel Mix not available - may need to move from devDependencies"
        fi
    fi
fi

echo ""
echo "✅ Node.js dependencies installed and verified successfully!"
echo "🎯 Strategy: $([ "$NEEDS_JS_DEV" = true ] && echo "Include dev dependencies for build" || echo "Production only")"
