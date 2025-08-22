#!/bin/bash
# V2 UNIVERSAL BUILD COMMAND 01: Create Laravel Directories
# Enhanced version with smart detection and error handling

set -e

echo "🏗️ V2 Universal Build Command 01: Create Laravel Directories"
echo "============================================================"

# Create Laravel directory structure
echo "📁 Creating Laravel directory structure..."

# Essential directories
mkdir -p bootstrap/cache
mkdir -p storage/{app/public,framework/{cache/data,sessions,testing,views},logs}
mkdir -p public/storage

# Set proper permissions
echo "🔐 Setting directory permissions..."
chmod -R 755 bootstrap/cache storage
find storage -type d -exec chmod 775 {} \;

# Create .gitkeep files for empty directories
echo "📝 Creating .gitkeep files..."
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true

# Verify directory structure
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

# Check if storage is writable
if [ -w "storage" ]; then
    echo "  ✅ storage: writable"
else
    echo "  ⚠️ storage: not writable - fixing permissions..."
    chmod -R 775 storage
fi

echo ""
echo "✅ Laravel directories created and configured successfully!"
echo "📁 All required directories are present and writable"
