#!/bin/bash
set -e

echo "=== Creating Laravel Directory Structure ==="

# Create ALL required Laravel directories (covers all Laravel versions 8-12)
echo "Creating Laravel directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/clockwork
mkdir -p storage/debugbar
mkdir -p public/storage

# Set proper permissions for all directories
echo "Setting directory permissions..."
chmod -R 755 bootstrap/cache
chmod -R 755 storage
find storage -type d -exec chmod 775 {} \;

# Create .gitkeep files for empty directories
echo "Creating .gitkeep files..."
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true
touch bootstrap/cache/.gitkeep 2>/dev/null || true

# Verify directory structure
echo "Verifying directory structure..."
echo "✅ bootstrap/cache: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")"
echo "✅ storage/app: $([ -d "storage/app" ] && echo "exists" || echo "missing")"
echo "✅ storage/framework: $([ -d "storage/framework" ] && echo "exists" || echo "missing")"
echo "✅ storage/logs: $([ -d "storage/logs" ] && echo "exists" || echo "missing")"

echo "✅ Laravel directory structure created"
