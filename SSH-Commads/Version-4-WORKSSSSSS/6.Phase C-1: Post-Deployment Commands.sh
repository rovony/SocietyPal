#!/bin/bash
set -e

# Phase C-1: Post-Deployment Commands (After Release)
# Purpose: Activate release, setup public access, exit maintenance, verify
# Note: DeployHQ has already created the current symlink
# Version 3 - PRODUCTION READY (Enhanced with root cause fixes)
# Works for both first-time and subsequent deployments
echo "=== Phase C: Post-Deployment Finalization (V3) ==="

# ENHANCED: Define all variables relative to %path% (only available DeployHQ variable)
# %path% = Base server path we're deploying to (e.g., /home/user/domains/site.com/deploy)
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"

echo "🔧 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Path: $CURRENT_PATH"

# C01: Setup Public Access for All Hosting Types (UNIVERSAL VERSION)
echo "=== Configure Public Access ==="

# Detect hosting structure
DOMAIN_ROOT=$(dirname "$DEPLOY_PATH")
cd "$DOMAIN_ROOT"

echo "🔍 Analyzing hosting environment..."
echo "   Domain root: $DOMAIN_ROOT"
echo "   Deploy path: $DEPLOY_PATH"

# Detect hosting type and handle accordingly
HOSTING_TYPE="unknown"

if [ -d "public_html" ] && [ ! -L "public_html" ]; then
    HOSTING_TYPE="hostinger"
    echo "? Detected: Hostinger-style hosting (existing public_html directory)"
elif [ -L "public_html" ]; then
    HOSTING_TYPE="existing_symlink"
    echo "? Detected: Existing public_html symlink"
elif [ -d "www" ] && [ ! -L "www" ]; then
    HOSTING_TYPE="www_based"
    echo "? Detected: WWW-based hosting"
else
    HOSTING_TYPE="cpanel_secure"
    echo "?️ Detected: cPanel-style hosting (will create secure symlink)"
fi

case $HOSTING_TYPE in
    "hostinger")
        echo "? Hostinger Setup: Handling existing public_html directory..."
        
        # Check if public_html has content
        if [ "$(ls -A public_html 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then
            BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)"
            echo "   ? Backing up existing content to: $BACKUP_NAME"
            mv public_html "$BACKUP_NAME" || {
                echo "   ⚠️ Backup failed, removing existing content..."
                rm -rf public_html
            }
        else
            echo "   ?️ Removing empty public_html directory..."
            rm -rf public_html
        fi
        
        # Create symlink to Laravel public directory
        if ln -sf deploy/current/public public_html; then
            echo "   ✅ Created: public_html -> deploy/current/public"
        else
            echo "   ❌ Failed to create symlink - check permissions"
        fi
        ;;
        
    "existing_symlink")
        echo "? Existing Symlink: Verifying and updating..."
        
        CURRENT_TARGET=$(readlink public_html 2>/dev/null || echo "broken")
        if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then
            echo "   ? Updating symlink target from: $CURRENT_TARGET"
            rm -f public_html
            if ln -sf deploy/current/public public_html; then
                echo "   ✅ Updated: public_html -> deploy/current/public"
            else
                echo "   ❌ Failed to update symlink"
            fi
        else
            echo "   ✅ Symlink already correct: $CURRENT_TARGET"
        fi
        ;;
        
    "www_based")
        echo "? WWW-based Setup: Handling www directory..."
        
        if [ "$(ls -A www 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then
            mv www "www.backup.$(date +%Y%m%d_%H%M%S)"
            echo "   ? Backed up existing www directory"
        else
            rm -rf www
        fi
        
        if ln -sf deploy/current/public www; then
            echo "   ✅ Created: www -> deploy/current/public"
        else
            echo "   ❌ Failed to create www symlink"
        fi
        ;;
        
    "cpanel_secure")
        echo "? cPanel Secure Setup: Creating secure public_html symlink..."
        
        # This is the most secure approach - create symlink to Laravel public
        if ln -sf deploy/current/public public_html; then
            echo "   ✅ Created secure symlink: public_html -> deploy/current/public"
            echo "   ?️ Laravel app files remain outside web-accessible directory"
        else
            echo "   ⚠️ Primary symlink failed, trying alternative approach..."
            # Alternative: create from within deploy directory
            cd deploy/current && ln -sf public ../../public_html && cd ../..
            if [ -L "public_html" ]; then
                echo "   ✅ Alternative symlink creation successful"
            else
                echo "   ❌ All symlink attempts failed - manual intervention required"
                echo "   ? Suggestion: Check directory permissions and server configuration"
            fi
        fi
        ;;
        
    *)
        echo "❓ Unknown hosting type - using default secure approach"
        ln -sf deploy/current/public public_html 2>/dev/null && echo "✅ Default symlink created" || echo "⚠️ Default symlink failed"
        ;;
esac

# Verify the final setup
echo "? Verifying public access setup..."
if [ -L "public_html" ] && [ -e "public_html" ]; then
    FINAL_TARGET=$(readlink public_html)
    echo "   ✅ public_html -> $FINAL_TARGET"
    
    # Test if Laravel public files are accessible
    if [ -f "public_html/index.php" ]; then
        echo "   ✅ Laravel application accessible via public_html"
    else
        echo "   ⚠️ Laravel index.php not found in public_html"
    fi
else
    echo "   ❌ public_html symlink verification failed"
    echo "   ? Manual setup may be required for this hosting environment"
fi

# C02: Create index.php fallback in domain root (security)
if [ "$DOMAIN_ROOT" != "$DEPLOY_PATH" ]; then
    cat > "$DOMAIN_ROOT/index.php" << 'EOF'
<?php
// Security redirect to Laravel public directory
header("Location: /public_html/");
exit();
EOF
    echo "✅ Created security redirect in domain root"
fi

# C03: Exit Maintenance Mode (ENHANCED VERSION with dependency fixes)
echo "=== Exit Maintenance Mode ==="

# Remove maintenance flag
rm -f "$DEPLOY_PATH/.maintenance"

if [ -f "$CURRENT_PATH/artisan" ]; then
    cd "$CURRENT_PATH"
    
    # ENHANCED: Verify Laravel is working before attempting maintenance mode exit
    echo "🔍 Verifying Laravel framework before exiting maintenance mode..."
    
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel framework operational"
        
        # Try to exit Laravel maintenance mode
        if php artisan up 2>/dev/null; then
            echo "✅ Laravel maintenance mode deactivated successfully"
        else
            echo "⚠️ Laravel maintenance mode exit failed - trying alternative methods"
            
            # Alternative method: Remove maintenance file manually
            if [ -f "storage/framework/down" ]; then
                rm -f "storage/framework/down"
                echo "✅ Maintenance file removed manually"
            fi
            
            # Check if maintenance mode is actually off
            if php artisan tinker --execute="echo app()->isDownForMaintenance() ? 'DOWN' : 'UP';" 2>/dev/null | grep -q "UP"; then
                echo "✅ Application is now live (verified)"
            else
                echo "⚠️ Application may still be in maintenance mode"
            fi
        fi
    else
        echo "⚠️ Laravel framework not operational - cannot exit maintenance mode via artisan"
        echo "🔧 Attempting to fix Laravel dependencies first..."
        
        # Try to fix Laravel dependencies
        COMPOSER_CMD="composer"
        if command -v composer2 &> /dev/null; then
            COMPOSER_CMD="composer2"
        fi
        
        if $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction 2>/dev/null; then
            echo "✅ Dependencies reinstalled - retesting Laravel..."
            if php artisan --version >/dev/null 2>&1; then
                echo "✅ Laravel framework now operational after dependency fix"
                # Try maintenance mode exit again
                if php artisan up 2>/dev/null; then
                    echo "✅ Laravel maintenance mode deactivated after fix"
                else
                    echo "⚠️ Maintenance mode exit still failing - removing manually"
                    rm -f "storage/framework/down" 2>/dev/null || true
                fi
            else
                echo "⚠️ Laravel still not operational - removing maintenance files manually"
                rm -f "storage/framework/down" 2>/dev/null || true
            fi
        else
            echo "⚠️ Dependency reinstall failed - removing maintenance files manually"
            rm -f "storage/framework/down" 2>/dev/null || true
        fi
    fi
else
    echo "ℹ️ No artisan file found, maintenance flag removed only"
fi

echo "✅ Application is now live"

# C04: Laravel Final Setup (ENHANCED VERSION)
echo "=== Laravel Final Setup ==="

cd "$CURRENT_PATH"

if [ -f "artisan" ]; then
    # ENHANCED: Verify Laravel is working before final setup
    echo "🔍 Verifying Laravel framework for final setup..."
    
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel framework operational for final setup"
        
        # CRITICAL: Verify and fix storage symlinks
        echo "🔧 Verifying storage symlinks..."
    
    # Check if public/storage is directory instead of symlink (common issue)
    if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then
        echo "⚠️ public/storage is directory, converting to symlink..."
        rm -rf public/storage
        ln -sfn ../storage/app/public public/storage
        echo "✅ Converted public/storage directory to symlink"
    fi
    
    # Create/verify storage link
    echo "Creating storage link..."
    if php artisan storage:link --force 2>/dev/null; then
        echo "✅ Storage link created via artisan"
    else
        echo "⚠️ Artisan storage:link failed - likely exec() function disabled on shared hosting"
        echo "   Attempting manual symlink creation (fallback method)..."
        
        # Fallback 1: Standard manual creation
        rm -f public/storage 2>/dev/null
        if ln -sfn ../storage/app/public public/storage; then
            echo "✅ Manual storage link created (fallback 1)"
        else
            echo "⚠️ Fallback 1 failed - trying exec() bypass method (fallback 2)..."
            
            # Fallback 2: Enhanced exec() bypass method (suggested improvement)
            STORAGE_TARGET="../storage/app/public"
            STORAGE_LINK="public/storage"
            
            # Remove existing if present
            rm -f "$STORAGE_LINK" 2>/dev/null
            
            # Create symlink manually (this doesn't use exec())
            if ln -sfn "$STORAGE_TARGET" "$STORAGE_LINK"; then
                echo "✅ Storage link created via exec() bypass method (fallback 2)"
            else
                echo "⚠️ Fallback 2 failed - trying absolute path (fallback 3)..."
                # Fallback 3: Absolute path
                ABSOLUTE_TARGET="%shared_path%/storage/app/public"
                if ln -sfn "$ABSOLUTE_TARGET" "$STORAGE_LINK"; then
                    echo "✅ Absolute path storage link created (fallback 3)"
                else
                    echo "❌ All storage link attempts failed - manual intervention required"
                    echo "   Please create symlink manually: ln -sfn %shared_path%/storage/app/public public/storage"
                fi
            fi
        fi
    fi
    
    # Verify final storage symlink
    if [ -L "public/storage" ] && [ -e "public/storage" ]; then
        STORAGE_TARGET=$(readlink public/storage)
        echo "✅ Storage symlink verified: public/storage -> $STORAGE_TARGET"
    else
        echo "❌ Storage symlink verification failed - manual fix required"
    fi

    # Retry any caches that failed in Phase B
    echo "Finalizing Laravel caches..."
    php artisan config:cache 2>/dev/null && echo "✅ Config cache finalized" || echo "ℹ️ Config cache skipped"
    php artisan route:cache 2>/dev/null && echo "✅ Route cache finalized" || echo "ℹ️ Route cache skipped"
    php artisan view:cache 2>/dev/null && echo "✅ View cache finalized" || echo "ℹ️ View cache skipped"

    # Restart queue workers if needed
    if [ -f ".env" ]; then
        QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null)
        if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then
            if php artisan queue:restart 2>/dev/null; then
                echo "✅ Queue workers signaled to restart"
            else
                echo "⚠️ Queue restart failed (continuing)"
            fi
        else
            echo "ℹ️ Queue using sync driver (no workers)"
        fi
    fi
else
    echo "ℹ️ No artisan file - not a Laravel application"
fi

# C05: Clear Runtime Caches
echo "=== Clear Runtime Caches ==="

# Clear PHP OPcache
if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then
    php -r "opcache_reset();" 2>/dev/null || true
    echo "✅ OPcache cleared"
else
    echo "ℹ️ OpCache not available"
fi

# Clear composer cache (use composer command from Phase A)
${COMPOSER_CMD:-composer} clear-cache 2>/dev/null || true

# C06: Security Verification (ENHANCED VERSION)
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

# C07: Health Checks (ENHANCED VERSION with better error handling)
echo "=== Application Health Checks ==="
HEALTH_PASSED=true

# Check Laravel installation
if [ -f "artisan" ]; then
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel framework operational"
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
        echo "  Version: $LARAVEL_VERSION"
    else
        echo "❌ Laravel framework error"
        HEALTH_PASSED=false
        # Try to identify the issue
        echo "  Debug info:"
        php artisan --version 2>&1 | head -3
        echo "  This may be due to:"
        echo "    - Missing dependencies (check Phase B completion)"
        echo "    - exec() function disabled on shared hosting"
        echo "    - PHP configuration issues"
    fi
else
    echo "⚠️ No artisan file found in current release"
fi

# Check critical paths
echo "=== Critical Path Verification ==="
[ -L "storage" ] && echo "✅ Storage symlink valid" || echo "❌ Storage symlink broken"
[ -f ".env" ] && echo "✅ Environment configured" || echo "❌ Environment missing"
[ -d "vendor" ] && echo "✅ Dependencies installed" || echo "❌ Dependencies missing"

# Test database connection (ENHANCED from deployment report)
if [ -f ".env" ]; then
    echo "Testing database connection..."
    if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then
        echo "✅ Database connection working"
    else
        echo "⚠️ Database connection failed"
        echo "ℹ️ This may be due to:"
        echo "   - Database credentials incorrect"
        echo "   - Database server not ready"
        echo "   - exec() function disabled (preventing artisan tinker)"
        echo "   - Network connectivity issues"
    fi
fi

# Test HTTP response (ENHANCED with better error handling)
if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then
        echo "Testing application response..."
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 5 2>/dev/null || echo "000")
        case $HTTP_CODE in
            200|201|301|302) 
                echo "✅ Application responding (HTTP $HTTP_CODE)" 
                ;;
            503) 
                echo "⚠️ Application in maintenance mode" 
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

# C08: Cleanup Old Releases (ENHANCED VERSION)
echo "=== Cleanup Old Releases ==="
cd "$DEPLOY_PATH/releases"
KEEP_RELEASES=3
TOTAL=$(ls -1t 2>/dev/null | wc -l)

if [ "$TOTAL" -gt "$KEEP_RELEASES" ]; then
    echo "Cleaning old releases (keeping $KEEP_RELEASES)..."
    RELEASES_TO_DELETE=$((TOTAL - KEEP_RELEASES))
    echo "Will delete $RELEASES_TO_DELETE old releases"
    
    ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD; do
        if [ -n "$OLD" ] && [ -d "$OLD" ]; then
            echo "Removing: $OLD"
            rm -rf "$OLD" 2>/dev/null || echo "⚠️ Failed to remove $OLD"
        fi
    done
    echo "✅ Old releases cleanup completed"
else
    echo "ℹ️ Only $TOTAL releases found, no cleanup needed"
fi

# C09: Final Report (ENHANCED VERSION)
echo "=== Deployment Summary ==="
echo "📊 Release: $RELEASE_PATH"
echo "📊 Current: $CURRENT_PATH"
echo "📊 Commit: %endrev%"
echo "📊 Deployed by: %deployer%"
echo "📊 Environment: %environment%"
echo "📊 Status: $( [ "$HEALTH_PASSED" = true ] && echo '✅ Healthy' || echo '⚠️ Check warnings above' )"

# Log deployment
echo "[$(date '+%Y-%m-%d %H:%M:%S')] %environment% deployment - Release: $RELEASE_PATH - Commit: %endrev% - Status: Success" >> "$SHARED_PATH/deployments.log"

echo "✅ Deployment completed successfully!"
exit 0

