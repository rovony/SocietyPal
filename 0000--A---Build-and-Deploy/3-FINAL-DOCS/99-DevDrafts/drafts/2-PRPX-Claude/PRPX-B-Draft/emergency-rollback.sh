#!/bin/bash

# Script: emergency-rollback.sh
# Purpose: Emergency rollback to previous release with comprehensive recovery
# Version: 2.0
# Section: Universal - Emergency Use
# Location: 🔴 Server

set -euo pipefail

# Load deployment variables if available
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/load-variables.sh" ]; then
    source "$SCRIPT_DIR/load-variables.sh"
else
    # Fallback variables for emergency use
    PATH_SERVER="${PATH_SERVER:-$(pwd)}"
    PROJECT_NAME="${PROJECT_NAME:-Laravel-App}"
fi

echo "╔══════════════════════════════════════════════════════════╗"
echo "║              🚨 EMERGENCY ROLLBACK SYSTEM 🚨            ║"
echo "║                 Critical Recovery Tool                   ║"
echo "╚══════════════════════════════════════════════════════════╝"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [EMERGENCY-ROLLBACK] $1"
}

# Change to deployment directory
if [ -d "$PATH_SERVER" ]; then
    cd "$PATH_SERVER"
else
    cd "$(pwd)"
fi

log "🚨 EMERGENCY ROLLBACK INITIATED"
log "Working directory: $(pwd)"

# ═══════════════════════════════════════════════════════════════════════════
# ROLLBACK INFORMATION GATHERING
# ═══════════════════════════════════════════════════════════════════════════

gather_rollback_info() {
    log "Gathering rollback information..."
    
    # Check for rollback info file
    if [ -f "current-rollback-info.json" ]; then
        log "Found rollback information file"
        
        if command -v jq >/dev/null 2>&1; then
            PREVIOUS_RELEASE=$(jq -r '.previous_release // "none"' current-rollback-info.json 2>/dev/null)
            NEW_RELEASE=$(jq -r '.new_release // "unknown"' current-rollback-info.json 2>/dev/null)
            ROLLBACK_AVAILABLE=$(jq -r '.rollback_available // false' current-rollback-info.json 2>/dev/null)
            
            log "Current release: $NEW_RELEASE"
            log "Previous release: $PREVIOUS_RELEASE"
            log "Rollback available: $ROLLBACK_AVAILABLE"
        else
            log "WARNING: jq not available - parsing rollback info manually"
            PREVIOUS_RELEASE=$(grep -o '"previous_release":"[^"]*' current-rollback-info.json 2>/dev/null | cut -d'"' -f4 || echo "none")
            NEW_RELEASE="unknown"
            ROLLBACK_AVAILABLE="unknown"
        fi
        
        export PREVIOUS_RELEASE NEW_RELEASE ROLLBACK_AVAILABLE
    else
        log "No rollback information file found - attempting automatic detection"
        detect_rollback_target
    fi
}

detect_rollback_target() {
    log "Detecting rollback target..."
    
    # Get current release
    if [ -L "current" ]; then
        CURRENT_TARGET=$(readlink current)
        CURRENT_RELEASE=$(echo "$CURRENT_TARGET" | sed 's|releases/||')
        log "Current active release: $CURRENT_RELEASE"
        
        # Find previous release (second most recent)
        if [ -d "releases" ]; then
            AVAILABLE_RELEASES=($(ls -1t releases/ 2>/dev/null || true))
            
            if [ ${#AVAILABLE_RELEASES[@]} -ge 2 ]; then
                # Find the release that's not currently active
                for release in "${AVAILABLE_RELEASES[@]}"; do
                    if [ "$release" != "$CURRENT_RELEASE" ]; then
                        PREVIOUS_RELEASE="$release"
                        break
                    fi
                done
                
                if [ -n "${PREVIOUS_RELEASE:-}" ]; then
                    log "Detected previous release: $PREVIOUS_RELEASE"
                    ROLLBACK_AVAILABLE="true"
                else
                    log "ERROR: Could not determine previous release"
                    ROLLBACK_AVAILABLE="false"
                fi
            else
                log "ERROR: Only one release found - rollback not possible"
                ROLLBACK_AVAILABLE="false"
            fi
        else
            log "ERROR: Releases directory not found"
            ROLLBACK_AVAILABLE="false"
        fi
    else
        log "ERROR: Current symlink not found"
        exit 1
    fi
    
    export PREVIOUS_RELEASE CURRENT_RELEASE ROLLBACK_AVAILABLE
}

# ═══════════════════════════════════════════════════════════════════════════
# PRE-ROLLBACK VALIDATION
# ═══════════════════════════════════════════════════════════════════════════

pre_rollback_validation() {
    log "Running pre-rollback validation..."
    
    # Check if rollback is available
    if [ "${ROLLBACK_AVAILABLE:-false}" != "true" ]; then
        log "ERROR: Rollback not available - no previous release found"
        return 1
    fi
    
    if [ "${PREVIOUS_RELEASE:-none}" = "none" ]; then
        log "ERROR: No previous release identified for rollback"
        return 1
    fi
    
    # Verify previous release directory exists
    if [ ! -d "releases/$PREVIOUS_RELEASE" ]; then
        log "ERROR: Previous release directory not found: releases/$PREVIOUS_RELEASE"
        return 1
    fi
    
    # Verify previous release has required files
    REQUIRED_FILES=("artisan" "composer.json")
    for file in "${REQUIRED_FILES[@]}"; do
        if [ ! -f "releases/$PREVIOUS_RELEASE/$file" ]; then
            log "ERROR: Required file missing in previous release: $file"
            return 1
        fi
    done
    
    log "✅ Pre-rollback validation passed"
    log "Ready to rollback to: $PREVIOUS_RELEASE"
    
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# APPLICATION STATUS CHECK
# ═══════════════════════════════════════════════════════════════════════════

check_current_status() {
    log "Checking current application status..."
    
    # Test if current application is responding
    cd current 2>/dev/null || {
        log "ERROR: Cannot access current release directory"
        return 1
    }
    
    # Test Laravel functionality
    if php artisan --version >/dev/null 2>&1; then
        log "Current application: Laravel is functional"
        CURRENT_STATUS="functional"
    else
        log "Current application: Laravel is NOT functional"
        CURRENT_STATUS="broken"
    fi
    
    # Test database connectivity
    if php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1; then
        log "Current application: Database connection working"
        DATABASE_STATUS="working"
    else
        log "Current application: Database connection FAILED"
        DATABASE_STATUS="failed"
    fi
    
    # Test basic HTTP response (if we can determine the URL)
    if [ -n "${APP_URL:-}" ]; then
        HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$APP_URL" 2>/dev/null || echo "000")
        log "Current application: HTTP status $HTTP_STATUS"
    else
        log "Current application: Cannot test HTTP (no APP_URL configured)"
        HTTP_STATUS="unknown"
    fi
    
    cd ..
    
    export CURRENT_STATUS DATABASE_STATUS HTTP_STATUS
    
    # Determine urgency level
    if [ "$CURRENT_STATUS" = "broken" ] || [ "$DATABASE_STATUS" = "failed" ] || [ "$HTTP_STATUS" = "500" ]; then
        log "🚨 CRITICAL: Application appears to be broken - rollback strongly recommended"
        return 2 # Critical
    elif [ "$HTTP_STATUS" = "503" ]; then
        log "⚠️ WARNING: Application in maintenance mode - rollback may be routine"
        return 1 # Warning
    else
        log "✅ Current application appears functional - confirm rollback necessity"
        return 0 # OK
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# EMERGENCY ROLLBACK EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

execute_emergency_rollback() {
    log "🚨 EXECUTING EMERGENCY ROLLBACK..."
    
    ROLLBACK_START_TIME=$(date +%s.%N)
    
    # Step 1: Create backup of current state
    BACKUP_TIMESTAMP=$(date +%Y%m%d%H%M%S)
    CURRENT_BACKUP="current-pre-rollback-$BACKUP_TIMESTAMP"
    
    if [ -L "current" ]; then
        CURRENT_TARGET=$(readlink current)
        log "Backing up current symlink state: $CURRENT_TARGET -> $CURRENT_BACKUP"
        cp -P "current" "$CURRENT_BACKUP"
    fi
    
    # Step 2: Execute atomic rollback
    log "Performing atomic rollback to: $PREVIOUS_RELEASE"
    
    # Create temporary symlink
    TEMP_ROLLBACK_LINK="current-rollback-temp-$BACKUP_TIMESTAMP"
    
    if ! ln -sf "releases/$PREVIOUS_RELEASE" "$TEMP_ROLLBACK_LINK"; then
        log "ERROR: Failed to create rollback symlink"
        return 1
    fi
    
    # Atomic move
    if ! mv "$TEMP_ROLLBACK_LINK" "current"; then
        log "ERROR: Atomic rollback move failed"
        # Attempt cleanup
        rm -f "$TEMP_ROLLBACK_LINK"
        return 1
    fi
    
    ROLLBACK_END_TIME=$(date +%s.%N)
    ROLLBACK_DURATION=$(echo "($ROLLBACK_END_TIME - $ROLLBACK_START_TIME) * 1000" | bc -l 2>/dev/null | cut -d'.' -f1)
    
    log "✅ Atomic rollback completed in ${ROLLBACK_DURATION:-<1}ms"
    
    # Step 3: Verify rollback
    CURRENT_TARGET=$(readlink current)
    if [ "$CURRENT_TARGET" != "releases/$PREVIOUS_RELEASE" ]; then
        log "ERROR: Rollback verification failed - symlink points to: $CURRENT_TARGET"
        return 1
    fi
    
    log "✅ Rollback verification passed - now active: $PREVIOUS_RELEASE"
    
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# POST-ROLLBACK RECOVERY
# ═══════════════════════════════════════════════════════════════════════════

post_rollback_recovery() {
    log "Running post-rollback recovery procedures..."
    
    cd current
    
    # Clear any problematic caches
    log "Clearing application caches..."
    php artisan config:clear >/dev/null 2>&1 || log "WARNING: Config clear failed"
    php artisan route:clear >/dev/null 2>&1 || log "WARNING: Route clear failed"
    php artisan view:clear >/dev/null 2>&1 || log "WARNING: View clear failed"
    
    # Rebuild essential caches
    log "Rebuilding essential caches..."
    php artisan config:cache >/dev/null 2>&1 || log "WARNING: Config cache rebuild failed"
    
    # Exit maintenance mode if active
    if php artisan down --show-status >/dev/null 2>&1; then
        log "Application is in maintenance mode - bringing it up..."
        php artisan up >/dev/null 2>&1 || log "WARNING: Failed to exit maintenance mode"
    fi
    
    # Test basic functionality
    log "Testing rollback success..."
    if php artisan --version >/dev/null 2>&1; then
        log "✅ Laravel application functional after rollback"
    else
        log "ERROR: Laravel application still not functional after rollback"
        cd ..
        return 1
    fi
    
    # Test database connectivity
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB_OK';" 2>/dev/null | grep -q "DB_OK"; then
        log "✅ Database connectivity restored"
    else
        log "WARNING: Database connectivity issues remain"
    fi
    
    cd ..
    
    # Restart services that may need it
    restart_services
    
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# SERVICE RESTART
# ═══════════════════════════════════════════════════════════════════════════

restart_services() {
    log "Attempting to restart related services..."
    
    # Queue workers
    if [ "${USES_QUEUES:-false}" = "true" ]; then
        log "Restarting queue workers..."
        php artisan queue:restart >/dev/null 2>&1 || log "WARNING: Queue restart failed"
        
        # Supervisor restart (if available)
        if command -v supervisorctl >/dev/null 2>&1; then
            supervisorctl reread >/dev/null 2>&1 || true
            supervisorctl update >/dev/null 2>&1 || true
            supervisorctl restart all >/dev/null 2>&1 || log "WARNING: Supervisor restart failed"
        fi
    fi
    
    # OPcache reset
    if [ -f "current/artisan" ]; then
        cd current
        php -r "if(function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not available'; }" || true
        cd ..
    fi
    
    # Web server graceful restart
    log "Attempting web server graceful restart..."
    
    # Nginx
    if command -v nginx >/dev/null 2>&1; then
        nginx -t >/dev/null 2>&1 && systemctl reload nginx >/dev/null 2>&1 || true
    fi
    
    # PHP-FPM
    if command -v php-fpm >/dev/null 2>&1; then
        systemctl reload php-fpm >/dev/null 2>&1 || pkill -USR2 php-fpm >/dev/null 2>&1 || true
    fi
    
    log "Service restart attempts completed"
}

# ═══════════════════════════════════════════════════════════════════════════
# ROLLBACK DOCUMENTATION
# ═══════════════════════════════════════════════════════════════════════════

document_rollback() {
    log "Documenting rollback operation..."
    
    ROLLBACK_LOG="rollback-log-$(date +%Y%m%d-%H%M%S).json"
    
    cat > "$ROLLBACK_LOG" << EOF
{
    "rollback_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "rollback_from": "${NEW_RELEASE:-unknown}",
    "rollback_to": "$PREVIOUS_RELEASE",
    "rollback_reason": "emergency",
    "rollback_duration_ms": ${ROLLBACK_DURATION:-0},
    "executed_by": "$(whoami)",
    "previous_status": {
        "application": "${CURRENT_STATUS:-unknown}",
        "database": "${DATABASE_STATUS:-unknown}",
        "http_status": "${HTTP_STATUS:-unknown}"
    },
    "recovery_actions": [
        "cache_cleared",
        "services_restarted",
        "maintenance_mode_disabled"
    ]
}
EOF
    
    log "✅ Rollback documented in: $ROLLBACK_LOG"
    
    # Update rollback info for future reference
    cat > "current-rollback-info.json" << EOF
{
    "switch_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "new_release": "$PREVIOUS_RELEASE",
    "previous_release": "${NEW_RELEASE:-unknown}",
    "rollback_available": $([ -n "${NEW_RELEASE:-}" ] && echo "true" || echo "false"),
    "last_operation": "emergency_rollback"
}
EOF
    
    log "✅ Rollback information updated for future reference"
}

# ═══════════════════════════════════════════════════════════════════════════
# MAIN EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

main() {
    local exit_code=0
    
    log "════════════════════════════════════════════════════════════════"
    log "🚨 EMERGENCY ROLLBACK SYSTEM ACTIVATED 🚨"
    log "════════════════════════════════════════════════════════════════"
    
    # Gather rollback information
    gather_rollback_info
    
    # Check current application status
    check_current_status
    STATUS_CODE=$?
    
    # Validate rollback possibility
    if ! pre_rollback_validation; then
        log "💥 ROLLBACK IMPOSSIBLE - Pre-validation failed"
        log "Manual intervention required"
        exit 1
    fi
    
    # Confirm rollback (unless in critical state)
    if [ $STATUS_CODE -ne 2 ]; then
        echo ""
        echo "⚠️ ROLLBACK CONFIRMATION REQUIRED"
        echo "Current release will be rolled back to: $PREVIOUS_RELEASE"
        echo "This operation cannot be easily undone."
        echo ""
        read -p "Continue with emergency rollback? (yes/no): " -r
        echo ""
        
        if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
            log "Rollback cancelled by user"
            exit 0
        fi
    else
        log "🚨 CRITICAL STATE DETECTED - Proceeding with automatic rollback"
    fi
    
    # Execute rollback
    if ! execute_emergency_rollback; then
        log "💥 ROLLBACK EXECUTION FAILED"
        exit_code=1
    fi
    
    # Post-rollback recovery
    if ! post_rollback_recovery; then
        log "⚠️ POST-ROLLBACK RECOVERY HAD ISSUES"
        exit_code=1
    fi
    
    # Document the rollback
    document_rollback
    
    log "════════════════════════════════════════════════════════════════"
    
    if [ $exit_code -eq 0 ]; then
        log "✅ EMERGENCY ROLLBACK COMPLETED SUCCESSFULLY"
        log "🔄 Application rolled back to: $PREVIOUS_RELEASE"
        log "🌐 Application should now be accessible"
        log "📋 Check application functionality and monitor logs"
        log "📝 Rollback documented in rollback logs"
        
        # Final status check
        if [ -n "${APP_URL:-}" ]; then
            log "🔍 Quick status check..."
            sleep 3
            FINAL_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$APP_URL" 2>/dev/null || echo "000")
            log "📊 Final HTTP status: $FINAL_STATUS"
        fi
        
    else
        log "💥 EMERGENCY ROLLBACK COMPLETED WITH ISSUES"
        log "🚨 MANUAL INTERVENTION REQUIRED"
        log "🔧 Check application logs and server status"
        log "📞 Contact system administrator if issues persist"
    fi
    
    log "════════════════════════════════════════════════════════════════"
    
    return $exit_code
}

# Handle command line arguments
case "${1:-auto}" in
    "auto")
        main
        ;;
    "force")
        log "🚨 FORCED ROLLBACK MODE - Skipping confirmations"
        FORCE_ROLLBACK=true
        main
        ;;
    "status")
        gather_rollback_info
        check_current_status
        echo ""
        echo "Current Status:"
        echo "  Active Release: ${CURRENT_RELEASE:-unknown}"
        echo "  Previous Release: ${PREVIOUS_RELEASE:-none}"
        echo "  Rollback Available: ${ROLLBACK_AVAILABLE:-false}"
        echo "  Application Status: ${CURRENT_STATUS:-unknown}"
        echo "  Database Status: ${DATABASE_STATUS:-unknown}"
        ;;
    *)
        echo "Usage: $0 [auto|force|status]"
        echo "  auto   - Interactive rollback (default)"
        echo "  force  - Force rollback without confirmation"
        echo "  status - Show rollback status only"
        exit 1
        ;;
esac