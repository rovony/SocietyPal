#!/bin/bash

# Pre-Release Hook - DeployHQ SSH Hook
# Purpose: Prepare server for deployment (no builds/installs)

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔧 PRE-RELEASE HOOK${NC}"
echo "=================="

# Define paths - Only %path% is available from DeployHQ
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"
RELEASES_PATH="$DEPLOY_PATH/releases"

echo "📍 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Path: $CURRENT_PATH"

# Log function
log_hook() {
    echo -e "${GREEN}✅ $1${NC}"
    mkdir -p "$SHARED_PATH/logs"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - PRE-RELEASE: $1" >> "$SHARED_PATH/logs/deployment.log"
}

# Create shared directories if first deployment
if [[ ! -d "$SHARED_PATH" ]]; then
    mkdir -p "$SHARED_PATH"/{storage/{app/public,logs,framework/{cache,sessions,views}},bootstrap/cache,public/{uploads,media,avatars},logs,backups}
    log_hook "Created shared directories structure"
fi

# Enable maintenance mode on current release
if [[ -L "$CURRENT_PATH" ]] && [[ -f "$CURRENT_PATH/artisan" ]]; then
    cd "$CURRENT_PATH"
    php artisan down --render="errors::503" --secret="deploy-secret-key" 2>/dev/null || true
    log_hook "Maintenance mode enabled"
else
    log_hook "No current release found - first deployment"
fi

# Create database backup
if [[ -f "$CURRENT_PATH/.env" ]]; then
    cd "$CURRENT_PATH"
    source .env 2>/dev/null || true
    
    if [[ -n "${DB_DATABASE:-}" ]] && command -v mysqldump >/dev/null 2>&1; then
        mkdir -p "$SHARED_PATH/backups"
        mysqldump -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" \
            --single-transaction --routines --triggers \
            "${DB_DATABASE}" > "$SHARED_PATH/backups/pre-deploy-$(date +%Y%m%d_%H%M%S).sql" 2>/dev/null || true
        log_hook "Database backup created"
    fi
fi

# Clear current release caches (if exists)
if [[ -L "$CURRENT_PATH" ]] && [[ -f "$CURRENT_PATH/artisan" ]]; then
    cd "$CURRENT_PATH"
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    log_hook "Current release caches cleared"
fi

# Set proper permissions for shared directories
chmod -R 755 "$SHARED_PATH/storage" 2>/dev/null || true
chmod -R 755 "$SHARED_PATH/bootstrap/cache" 2>/dev/null || true

log_hook "Pre-release preparation completed"
echo -e "${GREEN}🎯 PRE-RELEASE HOOK: ✅ COMPLETED${NC}"
