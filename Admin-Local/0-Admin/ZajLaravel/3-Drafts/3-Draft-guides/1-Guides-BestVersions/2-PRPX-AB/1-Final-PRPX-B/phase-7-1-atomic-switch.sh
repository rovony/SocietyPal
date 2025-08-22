#!/bin/bash

# Script: phase-7-1-atomic-switch.sh
# Purpose: Execute atomic deployment switch with rollback preparation
# Version: 2.0
# Section: C - Build and Deploy (Phase 7)
# Location: 🔴 Server

set -euo pipefail

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/load-variables.sh"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║            Phase 7.1: Atomic Release Switch             ║"
echo "║                  ⚡ Critical Operation                   ║"
echo "╚══════════════════════════════════════════════════════════╝"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [ATOMIC-SWITCH] $1"
}

# Change to deployment directory
cd "$PATH_SERVER"

log "Starting atomic release switch..."
log "Current directory: $(pwd)"
log "Release to activate: $RELEASE_ID"

# ═══════════════════════════════════════════════════════════════════════════
# PRE-SWITCH VALIDATION
# ═══════════════════════════════════════════════════════════════════════════

pre_switch_validation() {
    log "Running pre-switch validation..."
    
    # Verify new release directory exists
    if [ ! -d "releases/$RELEASE_ID" ]; then
        log "ERROR: Release directory not found: releases/$RELEASE_ID"
        return 1
    fi
    
    # Verify new release has required files
    REQUIRED_FILES=("artisan" "composer.json" "bootstrap/app.php")
    for file in "${REQUIRED_FILES[@]}"; do
        if [ ! -f "releases/$RELEASE_ID/$file" ]; then
            log "ERROR: Required file missing in new release: $file"
            return 1
        fi
    done
    
    # Check if current symlink exists
    if [ -L "current" ]; then
        OLD_RELEASE=$(readlink current | sed 's|releases/||')
        log "Current active release: $OLD_RELEASE"
        export OLD_RELEASE
    else
        log "No current release detected - this appears to be first deployment"
        export OLD_RELEASE=""
    fi
    
    # Verify sufficient disk space for switch operation
    AVAILABLE_SPACE=$(df . | awk 'NR==2 {print $4}')
    if [ "$AVAILABLE_SPACE" -lt 102400 ]; then # 100MB
        log "WARNING: Low disk space detected: ${AVAILABLE_SPACE}KB available"
    fi
    
    log "✅ Pre-switch validation completed"
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# ATOMIC SYMLINK UPDATE
# ═══════════════════════════════════════════════════════════════════════════

atomic_symlink_update() {
    log "Executing atomic symlink update..."
    
    # Create timestamp for this operation
    SWITCH_TIMESTAMP=$(date +%Y%m%d%H%M%S)
    
    # Step 1: Create new symlink with temporary name
    TEMP_LINK="current-switching-$SWITCH_TIMESTAMP"
    log "Creating temporary symlink: $TEMP_LINK -> releases/$RELEASE_ID"
    
    if ! ln -sf "releases/$RELEASE_ID" "$TEMP_LINK"; then
        log "ERROR: Failed to create temporary symlink"
        return 1
    fi
    
    # Step 2: Verify temporary symlink
    if [ ! -L "$TEMP_LINK" ]; then
        log "ERROR: Temporary symlink was not created successfully"
        return 1
    fi
    
    TEMP_TARGET=$(readlink "$TEMP_LINK")
    if [ "$TEMP_TARGET" != "releases/$RELEASE_ID" ]; then
        log "ERROR: Temporary symlink points to wrong target: $TEMP_TARGET"
        rm -f "$TEMP_LINK"
        return 1
    fi
    
    log "✅ Temporary symlink verified"
    
    # Step 3: Atomic move operation
    log "Performing atomic symlink switch..."
    
    # Record exact time of switch
    ATOMIC_SWITCH_TIME=$(date +%s.%N)
    
    if ! mv "$TEMP_LINK" "current"; then
        log "ERROR: Atomic move operation failed"
        rm -f "$TEMP_LINK"
        return 1
    fi
    
    ATOMIC_SWITCH_END_TIME=$(date +%s.%N)
    
    # Calculate switch duration in milliseconds
    SWITCH_DURATION=$(echo "($ATOMIC_SWITCH_END_TIME - $ATOMIC_SWITCH_TIME) * 1000" | bc -l 2>/dev/null | cut -d'.' -f1)
    
    log "✅ Atomic switch completed in ${SWITCH_DURATION:-<1}ms"
    
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# POST-SWITCH VERIFICATION
# ═══════════════════════════════════════════════════════════════════════════

post_switch_verification() {
    log "Running post-switch verification..."
    
    # Verify current symlink points to new release
    if [ ! -L "current" ]; then
        log "ERROR: 'current' symlink does not exist after switch"
        return 1
    fi
    
    CURRENT_TARGET=$(readlink current)
    if [ "$CURRENT_TARGET" != "releases/$RELEASE_ID" ]; then
        log "ERROR: 'current' symlink points to wrong release: $CURRENT_TARGET"
        return 1
    fi
    
    log "✅ Symlink verification passed - current -> $CURRENT_TARGET"
    
    # Test basic application functionality
    cd current
    
    # Test artisan command
    if ! php artisan --version >/dev/null 2>&1; then
        log "ERROR: Laravel artisan not functional after switch"
        cd ..
        return 1
    fi
    
    log "✅ Laravel functionality verified after switch"
    
    # Test file permissions
    CRITICAL_DIRS=("storage" "bootstrap/cache")
    for dir in "${CRITICAL_DIRS[@]}"; do
        if [ ! -w "$dir" ]; then
            log "ERROR: Critical directory not writable after switch: $dir"
            cd ..
            return 1
        fi
    done
    
    log "✅ File permissions verified after switch"
    
    # Test configuration loading
    if ! php artisan config:show app.name >/dev/null 2>&1; then
        log "WARNING: Configuration loading test failed (non-critical)"
    else
        log "✅ Configuration loading verified"
    fi
    
    cd ..
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# ROLLBACK PREPARATION
# ═══════════════════════════════════════════════════════════════════════════

rollback_preparation() {
    log "Preparing rollback capability..."
    
    # Create rollback information file
    ROLLBACK_INFO="current-rollback-info.json"
    
    cat > "$ROLLBACK_INFO" << EOF
{
    "switch_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "new_release": "$RELEASE_ID",
    "previous_release": "${OLD_RELEASE:-none}",
    "switch_duration_ms": ${SWITCH_DURATION:-0},
    "rollback_command": "ln -sf releases/${OLD_RELEASE:-$RELEASE_ID} current",
    "rollback_available": $([ -n "$OLD_RELEASE" ] && echo "true" || echo "false")
}
EOF
    
    log "✅ Rollback information saved to $ROLLBACK_INFO"
    
    # Create quick rollback script
    if [ -n "$OLD_RELEASE" ]; then
        cat > "quick-rollback.sh" << EOF
#!/bin/bash
# Quick rollback script - generated during deployment
set -euo pipefail

echo "Rolling back to previous release: $OLD_RELEASE"
ln -sf "releases/$OLD_RELEASE" current
echo "Rollback completed - current release: \$(readlink current)"
echo "Remember to restart services if needed"
EOF
        chmod +x "quick-rollback.sh"
        log "✅ Quick rollback script created: quick-rollback.sh"
    else
        log "Note: No previous release available for rollback"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# SYMLINK INTEGRITY CHECK
# ═══════════════════════════════════════════════════════════════════════════

symlink_integrity_check() {
    log "Running comprehensive symlink integrity check..."
    
    # Check current symlink
    if [ -L "current" ]; then
        LINK_TARGET=$(readlink current)
        ABSOLUTE_TARGET=$(readlink -f current)
        
        if [ -d "$ABSOLUTE_TARGET" ]; then
            log "✅ Current symlink is valid: current -> $LINK_TARGET"
        else
            log "ERROR: Current symlink target does not exist: $LINK_TARGET"
            return 1
        fi
    else
        log "ERROR: Current symlink is missing"
        return 1
    fi
    
    # Check shared resource symlinks within the release
    cd current
    
    SHARED_LINKS=(
        "storage"
        ".env"
    )
    
    BROKEN_LINKS=()
    VALID_LINKS=()
    
    for link in "${SHARED_LINKS[@]}"; do
        if [ -L "$link" ]; then
            if [ -e "$link" ]; then
                VALID_LINKS+=("$link")
            else
                BROKEN_LINKS+=("$link")
            fi
        fi
    done
    
    if [ ${#BROKEN_LINKS[@]} -gt 0 ]; then
        log "ERROR: Broken symlinks detected: ${BROKEN_LINKS[*]}"
        cd ..
        return 1
    fi
    
    if [ ${#VALID_LINKS[@]} -gt 0 ]; then
        log "✅ Shared resource symlinks verified: ${VALID_LINKS[*]}"
    fi
    
    cd ..
    return 0
}

# ═══════════════════════════════════════════════════════════════════════════
# WEB SERVER NOTIFICATION
# ═══════════════════════════════════════════════════════════════════════════

web_server_notification() {
    log "Notifying web server of deployment switch..."
    
    # Nginx reload (graceful)
    if command -v nginx >/dev/null 2>&1 && [ -f "/etc/nginx/nginx.conf" ]; then
        if nginx -t >/dev/null 2>&1; then
            if systemctl reload nginx >/dev/null 2>&1 || service nginx reload >/dev/null 2>&1; then
                log "✅ Nginx gracefully reloaded"
            else
                log "WARNING: Nginx reload failed (may not have permission)"
            fi
        else
            log "WARNING: Nginx configuration test failed - not reloading"
        fi
    fi
    
    # Apache reload (if available)
    if command -v apache2ctl >/dev/null 2>&1; then
        if apache2ctl configtest >/dev/null 2>&1; then
            if systemctl reload apache2 >/dev/null 2>&1 || service apache2 reload >/dev/null 2>&1; then
                log "✅ Apache gracefully reloaded"
            else
                log "WARNING: Apache reload failed (may not have permission)"
            fi
        fi
    fi
    
    # PHP-FPM reload (if available)
    if command -v php-fpm >/dev/null 2>&1; then
        if systemctl reload php-fpm >/dev/null 2>&1 || pkill -USR2 php-fpm >/dev/null 2>&1; then
            log "✅ PHP-FPM gracefully reloaded"
        else
            log "Note: PHP-FPM reload not available or failed"
        fi
    fi
    
    log "Web server notification completed"
}

# ═══════════════════════════════════════════════════════════════════════════
# MAIN EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

main() {
    local exit_code=0
    
    # Record start time for metrics
    SWITCH_START_TIME=$(date +%s)
    
    log "═══════════════════════════════════════════════════════════════"
    log "ATOMIC RELEASE SWITCH - CRITICAL OPERATION STARTING"
    log "═══════════════════════════════════════════════════════════════"
    
    # Pre-switch validation
    if ! pre_switch_validation; then
        log "ERROR: Pre-switch validation failed"
        exit 1
    fi
    
    # Execute atomic symlink update
    if ! atomic_symlink_update; then
        log "ERROR: Atomic symlink update failed"
        exit 1
    fi
    
    # Post-switch verification
    if ! post_switch_verification; then
        log "ERROR: Post-switch verification failed"
        log "WARNING: Application may be in inconsistent state"
        exit_code=1
    fi
    
    # Symlink integrity check
    if ! symlink_integrity_check; then
        log "ERROR: Symlink integrity check failed"
        exit_code=1
    fi
    
    # Prepare rollback capability
    rollback_preparation
    
    # Notify web server
    web_server_notification
    
    # Calculate total operation time
    SWITCH_END_TIME=$(date +%s)
    TOTAL_SWITCH_TIME=$((SWITCH_END_TIME - SWITCH_START_TIME))
    
    log "═══════════════════════════════════════════════════════════════"
    if [ $exit_code -eq 0 ]; then
        log "✅ ATOMIC SWITCH COMPLETED SUCCESSFULLY"
        log "🚀 New release is now active: $RELEASE_ID"
        log "⚡ Switch completed in ${SWITCH_DURATION:-<1}ms"
        log "📊 Total operation time: ${TOTAL_SWITCH_TIME}s"
        if [ -n "$OLD_RELEASE" ]; then
            log "🔄 Rollback available to: $OLD_RELEASE"
        fi
        log "🌐 Public access updated - zero downtime achieved"
    else
        log "❌ ATOMIC SWITCH COMPLETED WITH ERRORS"
        log "🚨 Manual verification required"
        log "🔄 Consider rollback if application is unstable"
    fi
    log "═══════════════════════════════════════════════════════════════"
    
    return $exit_code
}

# Execute main function
main "$@"