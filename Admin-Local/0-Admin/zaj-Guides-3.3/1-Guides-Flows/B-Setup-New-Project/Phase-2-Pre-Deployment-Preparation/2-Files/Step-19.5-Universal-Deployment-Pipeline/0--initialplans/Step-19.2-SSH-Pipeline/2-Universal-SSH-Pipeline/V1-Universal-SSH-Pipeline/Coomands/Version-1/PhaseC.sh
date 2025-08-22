#!/bin/bash
set -e

# Phase C: Post-Deployment Commands (After Release)
# Purpose: Activate release, exit maintenance, cleanup, verify
# v1 - FIXED VERSION
echo "=== Phase C: Post-Deployment ==="

# C01: Setup Public Access (Hostinger/cPanel compatibility) - FIXED VERSION
echo "=== Setup Public Access ==="
DOMAIN_ROOT=$(dirname "%path%")
cd "$DOMAIN_ROOT"

# Handle public_html symlink for shared hosting with better error handling
if [ ! -L "public_html" ]; then
    if [ -d "public_html" ]; then
        # Backup existing public_html if it has content
        if [ "$(ls -A public_html 2>/dev/null)" ]; then
            BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)"
            echo "Backing up existing public_html to $BACKUP_NAME..."
            mv public_html "$BACKUP_NAME" || {
                echo "⚠️ Failed to backup public_html, removing instead..."
                rm -rf public_html
            }
            echo "✅ public_html backed up to $BACKUP_NAME"
        else
            echo "Removing empty public_html directory..."
            rm -rf public_html
        fi
    fi
    
    # Create symlink to current/public
    echo "Creating public_html symlink..."
    if ln -sf deploy/current/public public_html; then
        echo "✅ public_html symlink created"
    else
        echo "⚠️ Failed to create public_html symlink - check permissions"
        # Try alternative approach
        if [ -d "deploy/current/public" ]; then
            echo "Trying alternative symlink creation..."
            cd deploy/current && ln -sf public ../../public_html && cd ../..
            if [ -L "public_html" ]; then
                echo "✅ Alternative symlink creation successful"
            else
                echo "❌ All symlink attempts failed"
            fi
        fi
    fi
else
    # Verify symlink points to correct location
    CURRENT_TARGET=$(readlink public_html)
    if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then
        echo "Updating public_html symlink target..."
        rm -f public_html
        if ln -sf deploy/current/public public_html; then
            echo "✅ public_html symlink updated"
        else
            echo "⚠️ Failed to update public_html symlink"
        fi
    else
        echo "✅ public_html symlink already correct"
    fi
fi

# C02: Exit Maintenance Mode
echo "=== Exit Maintenance Mode ==="
rm -f %path%/maintenance_mode_active

if [ -f "%current_path%/artisan" ]; then
    cd %current_path%
    # Try to exit Laravel maintenance mode, but don't fail if it doesn't work
    if php artisan up 2>/dev/null; then
        echo "✅ Laravel maintenance mode deactivated"
    else
        echo "⚠️ Failed to exit Laravel maintenance mode via artisan (continuing)"
        echo "ℹ️ You may need to check the application manually"
    fi
else
    echo "ℹ️ No artisan file found, maintenance flag removed only"
fi

echo "✅ Maintenance mode deactivated"

# C03: Clear OpCache
echo "=== Clear OpCache ==="
if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" | grep -q "yes"; then
    php -r "opcache_reset();" 2>/dev/null || true
    echo "✅ OpCache cleared"
else
    echo "ℹ️ OpCache not available"
fi

# C04: Restart Queue Workers (if applicable)
echo "=== Restart Queue Workers ==="
cd %current_path%

if [ -f ".env" ]; then
    QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then
        if php artisan queue:restart 2>/dev/null; then
            echo "✅ Queue workers signaled to restart"
        else
            echo "⚠️ Queue restart failed (continuing)"
        fi
    else
        echo "ℹ️ Queue using sync driver (no workers)"
    fi
else
    echo "ℹ️ No .env file found"
fi

# C05: Health Checks (IMPROVED VERSION)
echo "=== Health Checks ==="
HEALTH_STATUS=0

# Check current symlink
if [ -L "%current_path%" ]; then
    echo "✅ Current symlink exists"
    CURRENT_TARGET=$(readlink "%current_path%")
    echo "  Target: $CURRENT_TARGET"
else
    echo "❌ Current symlink missing"
    HEALTH_STATUS=1
fi

# Check Laravel installation
if [ -f "%current_path%/artisan" ]; then
    cd %current_path%
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel installation working"
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
        echo "  Version: $LARAVEL_VERSION"
    else
        echo "❌ Laravel installation broken"
        HEALTH_STATUS=1
        # Try to identify the issue
        echo "  Debug info:"
        php artisan --version 2>&1 | head -3
    fi
else
    echo "⚠️ No artisan file found in current release"
fi

# Check critical files
echo "=== Critical File Checks ==="
[ -f "%current_path%/.env" ] && echo "✅ .env exists" || echo "❌ .env missing"
[ -L "%current_path%/storage" ] && echo "✅ Storage linked" || echo "❌ Storage not linked"
[ -d "%current_path%/vendor" ] && echo "✅ Vendor directory exists" || echo "❌ Vendor missing"

# Check web accessibility
if [ -f "%current_path%/.env" ]; then
    APP_URL=$(grep "^APP_URL=" %current_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then
        echo "Testing application response..."
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000")
        case $HTTP_CODE in
            200|201|301|302) 
                echo "✅ Application responding (HTTP $HTTP_CODE)" 
                ;;
            503) 
                echo "⚠️ Application in maintenance mode (HTTP $HTTP_CODE)" 
                ;;
            000) 
                echo "⚠️ Application not reachable" 
                ;;
            *) 
                echo "⚠️ Application returned HTTP $HTTP_CODE" 
                ;;
        esac
    else
        echo "ℹ️ Cannot test web accessibility (no APP_URL or curl)"
    fi
fi

# C06: Cleanup Old Releases
echo "=== Cleanup Old Releases ==="
cd %path%/releases
KEEP_RELEASES=3
TOTAL_RELEASES=$(ls -1t 2>/dev/null | wc -l)

if [ "$TOTAL_RELEASES" -gt "$KEEP_RELEASES" ]; then
    echo "Cleaning old releases (keeping $KEEP_RELEASES)..."
    RELEASES_TO_DELETE=$((TOTAL_RELEASES - KEEP_RELEASES))
    echo "Will delete $RELEASES_TO_DELETE old releases"
    
    ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD_RELEASE; do
        if [ -n "$OLD_RELEASE" ] && [ -d "$OLD_RELEASE" ]; then
            echo "Removing: $OLD_RELEASE"
            rm -rf "$OLD_RELEASE" 2>/dev/null || echo "⚠️ Failed to remove $OLD_RELEASE"
        fi
    done
    echo "✅ Old releases cleanup completed"
else
    echo "ℹ️ Only $TOTAL_RELEASES releases found, no cleanup needed"
fi

# C07: Log Deployment
echo "=== Log Deployment ==="
DEPLOYMENT_LOG="%shared_path%/deployments.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deployment completed - Release: %release_path% - Commit: %endrev%" >> "$DEPLOYMENT_LOG"

# Final status
if [ "$HEALTH_STATUS" -eq 0 ]; then
    echo "✅ Deployment completed successfully!"
else
    echo "⚠️ Deployment completed with warnings - check the issues above"
fi

echo "✅ Phase C completed successfully"
exit 0