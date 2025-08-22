#!/bin/bash

# Mid-Release Hook - DeployHQ SSH Hook  
# Purpose: Setup new release after build transfer (no builds/installs)

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔗 MID-RELEASE HOOK${NC}"
echo "=================="

# Define paths - Only %path% is available from DeployHQ
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"
RELEASES_PATH="$DEPLOY_PATH/releases"

# Get current release path (DeployHQ creates timestamped release directory)
RELEASE_PATH=$(find "$RELEASES_PATH" -maxdepth 1 -type d -name "20*" | sort | tail -1)

echo "📍 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Release: $RELEASE_PATH"

# Log function
log_hook() {
    echo -e "${GREEN}✅ $1${NC}"
    mkdir -p "$SHARED_PATH/logs"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - MID-RELEASE: $1" >> "$SHARED_PATH/logs/deployment.log"
}

# Verify we're in the new release directory
cd "$RELEASE_PATH"

# Verify build artifacts exist
if [[ ! -d "vendor" ]] || [[ ! -f "vendor/autoload.php" ]]; then
    echo -e "${RED}❌ CRITICAL: Build artifacts missing in release${NC}"
    exit 1
fi

log_hook "Build artifacts verified in release"

# Create symlinks to shared directories
ln -nfs "$SHARED_PATH/storage" storage
ln -nfs "$SHARED_PATH/bootstrap/cache" bootstrap/cache
ln -nfs "$SHARED_PATH/public/uploads" public/uploads
ln -nfs "$SHARED_PATH/public/media" public/media
ln -nfs "$SHARED_PATH/public/avatars" public/avatars

log_hook "Shared directory symlinks created"

# Copy or symlink environment file
if [[ -f "$SHARED_PATH/.env.production" ]]; then
    ln -nfs "$SHARED_PATH/.env.production" .env
    log_hook "Production environment linked"
elif [[ -f "$SHARED_PATH/.env" ]]; then
    ln -nfs "$SHARED_PATH/.env" .env  
    log_hook "Shared environment linked"
else
    echo -e "${YELLOW}⚠️ WARNING: No environment file found in shared directory${NC}"
fi

# Verify Composer version (enforce 2.x)
COMPOSER_CMD="composer"
if command -v composer2 >/dev/null 2>&1; then
    COMPOSER_CMD="composer2"
fi

COMPOSER_VERSION=$($COMPOSER_CMD --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2 || echo "unknown")
if [[ ! "$COMPOSER_VERSION" =~ ^2\. ]]; then
    echo -e "${RED}❌ CRITICAL: Composer 2.x required, found: $COMPOSER_VERSION${NC}"
    exit 1
fi

log_hook "Composer 2.x verified ($COMPOSER_VERSION)"

# Verify Laravel can boot
if ! php artisan --version >/dev/null 2>&1; then
    echo -e "${RED}❌ CRITICAL: Laravel cannot boot in new release${NC}"
    exit 1
fi

log_hook "Laravel boot verification passed"

# Set proper permissions
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true

log_hook "File permissions set"
echo -e "${GREEN}🎯 MID-RELEASE HOOK: ✅ COMPLETED${NC}"
