#!/bin/bash
# UNIVERSAL BUILD COMMAND 01: Laravel Directory Setup
# Based on Expert 1, 2, and 3 research - Works for ALL Laravel apps

set -e

echo "🏗️ UNIVERSAL BUILD 01: Laravel Directory Setup"
echo "=============================================="
echo "📚 Based on Expert 1, 2, and 3 comprehensive research"
echo "🎯 Works for ANY Laravel application - API, full-stack, SaaS, installer"
echo ""

# Create essential Laravel directory structure
echo "📁 Creating Laravel directory structure..."

# Core directories (Expert 1, 2, 3 requirements)
mkdir -p bootstrap/cache
mkdir -p storage/{app/public,framework/{cache/data,sessions,testing,views},logs}
mkdir -p public/storage

# Additional directories for various Laravel app types (Expert 1 comprehensive coverage)
mkdir -p storage/{clockwork,debugbar,app-public} 2>/dev/null || true

# Set proper permissions for ALL hosting types (Expert 3 shared hosting compatibility)
echo "🔐 Setting directory permissions for universal compatibility..."
chmod -R 755 bootstrap/cache storage
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \; 2>/dev/null || true

# Create .gitkeep files for empty directories (Expert 2 optimization)
echo "📝 Creating .gitkeep files for version control..."
find storage bootstrap/cache -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true

# Verify directory structure (Expert 3 comprehensive validation)
echo "✅ Verifying directory structure..."
REQUIRED_DIRS=(
    "bootstrap/cache"
    "storage/app"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
    "public"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo "  ✅ $dir: exists"
    else
        echo "  ❌ $dir: missing"
        exit 1
    fi
done

# Check if storage is writable (Expert 3 critical for shared hosting)
if [ -w "storage" ]; then
    echo "  ✅ storage: writable"
else
    echo "  ⚠️ storage: not writable - fixing permissions..."
    chmod -R 775 storage
fi

echo ""
echo "✅ Universal Laravel directories created successfully!"
echo "🎯 Ready for ANY Laravel application deployment"
echo "📚 Covers: API-only, full-stack, SaaS, installer, staging, production"
