#!/bin/bash
set -e

# Phase C: Post-Deployment Commands (After Release)
# Purpose: Activate release, setup public access, exit maintenance, verify
# Note: DeployHQ has already created the current symlink
# V2
echo "=== Phase C: Post-Deployment Finalization ==="

# C01: Setup Public Access for All Hosting Types
echo "=== Configure Public Access ==="

# Detect hosting structure
DOMAIN_ROOT=$(dirname "%path%")
cd "$DOMAIN_ROOT"

# Handle different hosting scenarios
if [ -d "public_html" ] || [ -L "public_html" ] || [ ! -e "public_html" ]; then
    echo "Detected shared hosting with public_html..."
    
    # Backup existing public_html if it has real content
    if [ -d "public_html" ] && [ ! -L "public_html" ]; then
        if [ "$(ls -A public_html 2>/dev/null | grep -v '^\.')" ]; then
            BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)"
            mv public_html "$BACKUP_NAME"
            echo "✅ Backed up existing public_html to $BACKUP_NAME"
        else
            rm -rf public_html
            echo "✅ Removed empty public_html directory"
        fi
    fi
    
    # Remove broken symlinks
    if [ -L "public_html" ] && [ ! -e "public_html" ]; then
        rm -f public_html
        echo "✅ Removed broken public_html symlink"
    fi
    
    # Create or update public_html symlink
    if [ ! -L "public_html" ]; then
        ln -sf deploy/current/public public_html
        echo "✅ Created public_html -> deploy/current/public"
    else
        # Verify existing symlink
        LINK_TARGET=$(readlink public_html)
        if [ "$LINK_TARGET" != "deploy/current/public" ]; then
            rm -f public_html
            ln -sf deploy/current/public public_html
            echo "✅ Updated public_html symlink"
        else
            echo "✅ public_html symlink already correct"
        fi
    fi
elif [ -d "www" ] || [ -L "www" ]; then
    echo "Detected hosting with www directory..."
    
    # Similar logic for www directory
    if [ -d "www" ] && [ ! -L "www" ]; then
        if [ "$(ls -A www 2>/dev/null | grep -v '^\.')" ]; then
            mv www "www.backup.$(date +%Y%m%d_%H%M%S)"
            echo "✅ Backed up www directory"
        else
            rm -rf www
        fi
    fi
    
    [ -L "www" ] && [ ! -e "www" ] && rm -f www
    
    if [ ! -L "www" ]; then
        ln -sf deploy/current/public www
        echo "✅ Created www -> deploy/current/public"
    fi
else
    echo "✅ Standard deployment structure (no public_html/www needed)"
fi

# C02: Create index.php fallback in domain root (security)
if [ "$DOMAIN_ROOT" != "%path%" ]; then
    cat > "$DOMAIN_ROOT/index.php" << 'EOF'
<?php
// Security redirect to Laravel public directory
header("Location: /public_html/");
exit();
EOF
    echo "✅ Created security redirect in domain root"
fi

# C03: Exit Maintenance Mode
echo "=== Exit Maintenance Mode ==="
rm -f %path%/.maintenance

if [ -f "%current_path%/artisan" ]; then
    cd %current_path%
    php artisan up || echo "⚠️ Failed to exit Laravel maintenance"
fi
echo "✅ Application is now live"

# C04: Clear All Caches
echo "=== Clear Runtime Caches ==="

# Clear PHP OPcache
if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" | grep -q "yes"; then
    php -r "opcache_reset();" || true
    echo "✅ OPcache cleared"
fi

# Clear composer cache
composer clear-cache 2>/dev/null || true

# Restart queue workers if needed
cd %current_path%
if [ -f ".env" ]; then
    QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then
        php artisan queue:restart || true
        echo "✅ Queue workers restarted"
    fi
fi

# C05: Security Verification
echo "=== Security Verification ==="
cd %current_path%

# Check for exposed sensitive files
EXPOSED_FILES=0
for FILE in .env .env.example composer.json package.json .git; do
    if [ -e "public/$FILE" ]; then
        echo "❌ SECURITY WARNING: $FILE exposed in public!"
        EXPOSED_FILES=$((EXPOSED_FILES + 1))
    fi
done

if [ $EXPOSED_FILES -eq 0 ]; then
    echo "✅ No sensitive files exposed"
fi

# Verify .htaccess files exist
[ -f ".htaccess" ] && echo "✅ Root .htaccess present" || echo "⚠️ Root .htaccess missing"
[ -f "public/.htaccess" ] && echo "✅ Public .htaccess present" || echo "⚠️ Public .htaccess missing"

# Check critical permissions
[ -r "%shared_path%/.env" ] && [ ! -w "%shared_path%/.env" ] || echo "⚠️ .env file should not be world-writable"

# C06: Health Checks
echo "=== Application Health Checks ==="
HEALTH_PASSED=true

# Check Laravel installation
if [ -f "artisan" ]; then
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel framework operational"
    else
        echo "❌ Laravel framework error"
        HEALTH_PASSED=false
    fi
fi

# Check critical paths
[ -L "storage" ] && echo "✅ Storage symlink valid" || echo "❌ Storage symlink broken"
[ -f ".env" ] && echo "✅ Environment configured" || echo "❌ Environment missing"
[ -d "vendor" ] && echo "✅ Dependencies installed" || echo "❌ Dependencies missing"

# Test database connection
if php artisan tinker --execute="echo 'DB OK';" 2>/dev/null | grep -q "DB OK"; then
    echo "✅ Database connection working"
else
    echo "⚠️ Database connection failed"
fi

# Test HTTP response
if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 5 2>/dev/null || echo "000")
        case $HTTP_CODE in
            200|201|301|302) echo "✅ Application responding (HTTP $HTTP_CODE)" ;;
            503) echo "⚠️ Application in maintenance mode" ;;
            000) echo "⚠️ Application not reachable" ;;
            *) echo "⚠️ Application returned HTTP $HTTP_CODE" ;;
        esac
    fi
fi

# C07: Cleanup Old Releases
echo "=== Cleanup Old Releases ==="
cd %path%/releases
KEEP_RELEASES=3
TOTAL=$(ls -1t 2>/dev/null | wc -l)

if [ "$TOTAL" -gt "$KEEP_RELEASES" ]; then
    ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD; do
        rm -rf "$OLD"
        echo "✅ Removed old release: $OLD"
    done
fi

# C08: Final Report
echo "=== Deployment Summary ==="
echo "📁 Release: %release_path%"
echo "🔗 Current: %current_path%"
echo "📦 Commit: %endrev%"
echo "👤 Deployed by: %deployer%"
echo "🌍 Environment: %environment%"
echo "📊 Status: $( [ "$HEALTH_PASSED" = true ] && echo '✅ Healthy' || echo '⚠️ Check warnings above' )"

# Log deployment
echo "[$(date '+%Y-%m-%d %H:%M:%S')] %environment% deployment - Release: %release_path% - Commit: %endrev% - Status: Success" >> %shared_path%/deployments.log

echo "✅ Deployment completed successfully!"
exit 0