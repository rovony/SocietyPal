#!/bin/bash

# Post-Release Hook - DeployHQ SSH Hook
# Purpose: Activate new release and finalize deployment (no builds/installs)

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 POST-RELEASE HOOK${NC}"
echo "==================="

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
    echo "$(date '+%Y-%m-%d %H:%M:%S') - POST-RELEASE: $1" >> "$SHARED_PATH/logs/deployment.log"
}

# Verify we're in the new release directory
cd "$RELEASE_PATH"

# Verify Composer 2.x is available
COMPOSER_CMD="composer"
if command -v composer2 >/dev/null 2>&1; then
    COMPOSER_CMD="composer2"
fi

COMPOSER_VERSION=$($COMPOSER_CMD --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2 || echo "unknown")
if [[ ! "$COMPOSER_VERSION" =~ ^2\. ]]; then
    echo -e "${RED}❌ CRITICAL: Composer 2.x required for migrations${NC}"
    exit 1
fi

log_hook "Composer 2.x verified for migrations ($COMPOSER_VERSION)"

# Run database migrations
if php artisan migrate --force --no-interaction 2>/dev/null; then
    log_hook "Database migrations completed"
else
    echo -e "${RED}❌ CRITICAL: Database migrations failed${NC}"
    exit 1
fi

# Create storage link if needed
if [[ ! -L "public/storage" ]]; then
    php artisan storage:link 2>/dev/null || true
    log_hook "Storage link created"
fi

# Queue restart (if supervisor/horizon is running)
php artisan queue:restart 2>/dev/null || true
log_hook "Queue workers restarted"

# Final health check
if ! php artisan --version >/dev/null 2>&1; then
    echo -e "${RED}❌ CRITICAL: Laravel health check failed${NC}"
    exit 1
fi

# Update current symlink to new release (atomic switch)
ln -nfs "$RELEASE_PATH" "$CURRENT_PATH"
log_hook "Atomic symlink switch completed"

# Disable maintenance mode
php artisan up 2>/dev/null || true
log_hook "Maintenance mode disabled"

# Cleanup old releases (keep last 3)
cd "$RELEASES_PATH"
if [[ -d "$RELEASES_PATH" ]]; then
    ls -1t | grep "^20" | tail -n +4 | xargs -I {} rm -rf "{}" 2>/dev/null || true
    log_hook "Old releases cleaned up"
fi

# Final verification
DOMAIN=$(php artisan route:list --name=home 2>/dev/null | grep -o 'http[s]*://[^/]*' | head -1 || echo "http://$(hostname)")
if curl -s -o /dev/null -w "%{http_code}" "$DOMAIN" | grep -q "200\|302"; then
    log_hook "Application responding successfully"
else
    echo -e "${YELLOW}⚠️ WARNING: Application may not be responding correctly${NC}"
fi

log_hook "Post-release deployment completed"
echo -e "${GREEN}🎯 POST-RELEASE HOOK: ✅ COMPLETED${NC}"
