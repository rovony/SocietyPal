#!/bin/bash

# Script: pre-release-hooks.sh
# Purpose: User-configurable pre-release deployment hooks
# Version: 2.0
# Section: C - Build and Deploy (Phase 5)
# Location: 🔴 Server (SSH execution)
# Hook Type: 🟣 1️⃣ Pre-release Hook

set -euo pipefail

# Load deployment variables (if available on server)
if [ -f "Admin-Local/Deployment/Scripts/load-variables.sh" ]; then
    source Admin-Local/Deployment/Scripts/load-variables.sh
else
    # Fallback: Set critical variables manually for server environment
    DEPLOY_PATH="${DEPLOY_PATH:-/home/$(whoami)/domains/$(hostname)/deploy}"
    PATH_CURRENT="${DEPLOY_PATH}/current"
    PATH_SHARED="${DEPLOY_PATH}/shared"
fi

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [PRE-RELEASE] $1"
}

echo "╔══════════════════════════════════════════════════════════╗"
echo "║                Pre-Release Hooks (Phase 5)              ║"
echo "║              🟣 1️⃣ User-Configurable                    ║"
echo "╚══════════════════════════════════════════════════════════╝"

log "Starting pre-release hook execution..."

# Change to current release directory
if [ -d "$PATH_CURRENT" ]; then
    cd "$PATH_CURRENT"
    log "Changed to current release directory: $PATH_CURRENT"
else
    log "WARNING: Current release directory not found: $PATH_CURRENT"
    log "This may be the first deployment - continuing anyway..."
fi

# ═══════════════════════════════════════════════════════════════════════════
# MAINTENANCE MODE MANAGEMENT
# ═══════════════════════════════════════════════════════════════════════════

maintenance_mode() {
    log "Checking maintenance mode configuration..."
    
    # Check if maintenance mode is enabled in configuration
    if [ "${ENABLE_MAINTENANCE_MODE:-false}" = "true" ]; then
        log "Enabling maintenance mode..."
        
        # Create maintenance mode with custom message and bypass secret
        php artisan down \
            --render="errors::503" \
            --secret="${DEPLOY_SECRET:-deploy-$(date +%H%M%S)}" \
            --retry=60 \
            --refresh=15 || {
            log "ERROR: Failed to enable maintenance mode"
            return 1
        }
        
        log "✅ Maintenance mode enabled"
        log "Bypass URL: ${APP_URL:-https://your-domain.com}/${DEPLOY_SECRET:-deploy-$(date +%H%M%S)}"
        
        # Wait briefly to ensure maintenance page is active
        sleep 2
        
        # Test maintenance mode is active
        if php artisan inspire >/dev/null 2>&1; then
            log "✅ Application still accessible via artisan during maintenance"
        fi
        
    else
        log "Maintenance mode disabled in configuration - skipping"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# DATABASE BACKUP PROCEDURES
# ═══════════════════════════════════════════════════════════════════════════

database_backup() {
    log "Starting database backup procedures..."
    
    # Check if database backup is enabled
    if [ "${ENABLE_DB_BACKUP:-true}" = "true" ]; then
        
        # Ensure backup directory exists
        BACKUP_DIR="${PATH_SHARED}/backups"
        mkdir -p "$BACKUP_DIR"
        
        # Get database configuration from environment
        if [ -f "${PATH_SHARED}/.env" ]; then
            source "${PATH_SHARED}/.env"
        else
            log "WARNING: Cannot source .env file - backup may fail"
        fi
        
        # Generate backup filename
        BACKUP_FILE="${BACKUP_DIR}/pre-deploy-$(date +%Y%m%d-%H%M%S).sql"
        
        log "Creating database backup: $(basename "$BACKUP_FILE")"
        
        # Perform database backup based on connection type
        case "${DB_CONNECTION:-mysql}" in
            "mysql")
                if command -v mysqldump >/dev/null 2>&1; then
                    mysqldump \
                        -h"${DB_HOST:-127.0.0.1}" \
                        -P"${DB_PORT:-3306}" \
                        -u"${DB_USERNAME}" \
                        -p"${DB_PASSWORD}" \
                        "${DB_DATABASE}" \
                        --single-transaction \
                        --routines \
                        --triggers \
                        > "$BACKUP_FILE" || {
                        log "ERROR: MySQL backup failed"
                        return 1
                    }
                else
                    log "WARNING: mysqldump not available - skipping database backup"
                    return 0
                fi
                ;;
            "pgsql")
                if command -v pg_dump >/dev/null 2>&1; then
                    PGPASSWORD="${DB_PASSWORD}" pg_dump \
                        -h "${DB_HOST:-127.0.0.1}" \
                        -p "${DB_PORT:-5432}" \
                        -U "${DB_USERNAME}" \
                        -d "${DB_DATABASE}" \
                        --no-password \
                        > "$BACKUP_FILE" || {
                        log "ERROR: PostgreSQL backup failed"
                        return 1
                    }
                else
                    log "WARNING: pg_dump not available - skipping database backup"
                    return 0
                fi
                ;;
            "sqlite")
                if [ -f "${DB_DATABASE}" ]; then
                    cp "${DB_DATABASE}" "${BACKUP_FILE%.sql}.sqlite" || {
                        log "ERROR: SQLite backup failed"
                        return 1
                    }
                else
                    log "WARNING: SQLite database file not found - skipping backup"
                    return 0
                fi
                ;;
            *)
                log "WARNING: Unsupported database type: ${DB_CONNECTION} - skipping backup"
                return 0
                ;;
        esac
        
        # Compress backup if it was created
        if [ -f "$BACKUP_FILE" ]; then
            gzip "$BACKUP_FILE"
            BACKUP_SIZE=$(du -h "${BACKUP_FILE}.gz" | cut -f1)
            log "✅ Database backup completed: $(basename "${BACKUP_FILE}.gz") (${BACKUP_SIZE})"
            
            # Clean up old backups (keep last 7 days)
            find "$BACKUP_DIR" -name "pre-deploy-*.sql.gz" -mtime +7 -delete 2>/dev/null || true
            log "Old backups cleaned (keeping last 7 days)"
        fi
        
    else
        log "Database backup disabled in configuration - skipping"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# CACHE WARMUP PREPARATIONS
# ═══════════════════════════════════════════════════════════════════════════

cache_warmup_prep() {
    log "Preparing cache warmup strategies..."
    
    # Pre-compile commonly used views
    if [ "${ENABLE_VIEW_PRECOMPILE:-false}" = "true" ]; then
        log "Pre-compiling critical views..."
        
        # Get list of most frequently accessed routes (if analytics available)
        CRITICAL_ROUTES=(
            "/"
            "/home"
            "/dashboard"
            "/login"
            "/register"
        )
        
        # Add custom critical routes from configuration
        if [ -n "${CRITICAL_ROUTES_CUSTOM:-}" ]; then
            IFS=',' read -ra CUSTOM_ROUTES <<< "$CRITICAL_ROUTES_CUSTOM"
            CRITICAL_ROUTES+=("${CUSTOM_ROUTES[@]}")
        fi
        
        log "Critical routes for warmup: ${CRITICAL_ROUTES[*]}"
        
        # Cache configuration for warmup
        php artisan config:cache >/dev/null 2>&1 || log "WARNING: Config cache failed during prep"
        php artisan route:cache >/dev/null 2>&1 || log "WARNING: Route cache failed during prep"
        
    else
        log "View pre-compilation disabled - skipping"
    fi
    
    # Prepare Redis/cache connections
    if [ "${ENABLE_CACHE_PREP:-true}" = "true" ]; then
        log "Testing cache connections..."
        
        # Test Redis connection if configured
        if [ "${CACHE_DRIVER:-}" = "redis" ]; then
            if php artisan tinker --execute="Cache::store('redis')->put('deployment_test', 'ok', 10); echo Cache::store('redis')->get('deployment_test');" 2>/dev/null | grep -q "ok"; then
                log "✅ Redis cache connection verified"
            else
                log "WARNING: Redis cache connection test failed"
            fi
        fi
        
        # Test database cache connection
        if [ "${CACHE_DRIVER:-}" = "database" ]; then
            php artisan cache:table >/dev/null 2>&1 || log "Cache table may need to be created"
        fi
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# EXTERNAL SERVICE NOTIFICATIONS
# ═══════════════════════════════════════════════════════════════════════════

external_notifications() {
    log "Sending external service notifications..."
    
    # Slack notification
    if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
        log "Sending Slack notification..."
        
        DEPLOYMENT_MESSAGE="🚀 Starting deployment for ${PROJECT_NAME:-Application}
        
*Environment:* $(php artisan env 2>/dev/null || echo 'Production')
*Branch:* ${DEPLOY_BRANCH:-main}  
*Commit:* ${TARGET_COMMIT:-latest}
*Time:* $(date)
*Status:* Pre-deployment preparations started"

        curl -X POST "${SLACK_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"text\": \"$DEPLOYMENT_MESSAGE\",
                \"username\": \"Deploy Bot\",
                \"icon_emoji\": \":rocket:\"
            }" >/dev/null 2>&1 || log "WARNING: Slack notification failed"
        
        log "✅ Slack notification sent"
    fi
    
    # Discord webhook
    if [ -n "${DISCORD_WEBHOOK_URL:-}" ]; then
        log "Sending Discord notification..."
        
        curl -X POST "${DISCORD_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"content\": \"🚀 **Deployment Starting** - ${PROJECT_NAME:-Application} deployment preparations began at $(date)\"
            }" >/dev/null 2>&1 || log "WARNING: Discord notification failed"
        
        log "✅ Discord notification sent"
    fi
    
    # Email notification (if configured)
    if [ -n "${DEPLOYMENT_EMAIL:-}" ] && command -v mail >/dev/null 2>&1; then
        echo "Deployment started for ${PROJECT_NAME:-Application} at $(date)" | \
            mail -s "Deployment Started - ${PROJECT_NAME:-Application}" "${DEPLOYMENT_EMAIL}" || \
            log "WARNING: Email notification failed"
        
        log "✅ Email notification sent to ${DEPLOYMENT_EMAIL}"
    fi
    
    # Custom webhook
    if [ -n "${CUSTOM_WEBHOOK_URL:-}" ]; then
        log "Sending custom webhook notification..."
        
        curl -X POST "${CUSTOM_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"event\": \"deployment_started\",
                \"project\": \"${PROJECT_NAME:-unknown}\",
                \"environment\": \"$(php artisan env 2>/dev/null || echo 'production')\",
                \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\",
                \"branch\": \"${DEPLOY_BRANCH:-main}\",
                \"commit\": \"${TARGET_COMMIT:-latest}\"
            }" >/dev/null 2>&1 || log "WARNING: Custom webhook failed"
        
        log "✅ Custom webhook notification sent"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# CUSTOM PRE-DEPLOYMENT VALIDATIONS
# ═══════════════════════════════════════════════════════════════════════════

custom_validations() {
    log "Running custom pre-deployment validations..."
    
    # Check disk space
    if [ "${CHECK_DISK_SPACE:-true}" = "true" ]; then
        AVAILABLE_SPACE=$(df "${DEPLOY_PATH}" | awk 'NR==2 {gsub(/%/, "", $5); print $4}')
        REQUIRED_SPACE=${REQUIRED_DISK_SPACE:-1048576} # 1GB default
        
        if [ "$AVAILABLE_SPACE" -lt "$REQUIRED_SPACE" ]; then
            log "ERROR: Insufficient disk space - Available: ${AVAILABLE_SPACE}KB, Required: ${REQUIRED_SPACE}KB"
            return 1
        else
            log "✅ Sufficient disk space available: $((AVAILABLE_SPACE/1024))MB"
        fi
    fi
    
    # Check server load
    if [ "${CHECK_SERVER_LOAD:-false}" = "true" ] && command -v uptime >/dev/null 2>&1; then
        LOAD_1MIN=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
        MAX_LOAD=${MAX_DEPLOYMENT_LOAD:-2.0}
        
        if (( $(echo "$LOAD_1MIN > $MAX_LOAD" | bc -l) )); then
            log "WARNING: High server load detected: $LOAD_1MIN (max: $MAX_LOAD)"
            log "Consider postponing deployment until load decreases"
        else
            log "✅ Server load acceptable: $LOAD_1MIN"
        fi
    fi
    
    # Custom validation command
    if [ -n "${CUSTOM_VALIDATION_COMMAND:-}" ]; then
        log "Running custom validation command..."
        if eval "$CUSTOM_VALIDATION_COMMAND"; then
            log "✅ Custom validation passed"
        else
            log "ERROR: Custom validation failed"
            return 1
        fi
    fi
    
    # Check external services availability
    if [ -n "${EXTERNAL_SERVICES_CHECK:-}" ]; then
        IFS=',' read -ra SERVICES <<< "$EXTERNAL_SERVICES_CHECK"
        for service in "${SERVICES[@]}"; do
            log "Checking external service: $service"
            if curl -f -s -o /dev/null --max-time 10 "$service/health" || curl -f -s -o /dev/null --max-time 10 "$service"; then
                log "✅ External service accessible: $service"
            else
                log "WARNING: External service may be unreachable: $service"
            fi
        done
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# MAIN EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

main() {
    local exit_code=0
    
    # Execute all pre-release procedures
    log "Executing pre-release hook procedures..."
    
    # 1. Custom validations (run first to catch issues early)
    if ! custom_validations; then
        log "ERROR: Custom validations failed"
        exit_code=1
    fi
    
    # 2. Database backup (critical - run early)
    if ! database_backup; then
        log "ERROR: Database backup failed"
        exit_code=1
    fi
    
    # 3. External notifications (inform stakeholders)
    external_notifications
    
    # 4. Cache warmup preparations
    cache_warmup_prep
    
    # 5. Maintenance mode (run last to minimize downtime)
    if ! maintenance_mode; then
        log "ERROR: Maintenance mode setup failed"
        exit_code=1
    fi
    
    # Summary
    if [ $exit_code -eq 0 ]; then
        log "✅ All pre-release procedures completed successfully"
        log "🚀 Ready to proceed with deployment"
    else
        log "❌ Pre-release procedures encountered errors"
        log "🛑 Review errors before proceeding"
    fi
    
    return $exit_code
}

# ═══════════════════════════════════════════════════════════════════════════
# USER CUSTOMIZATION AREA
# ═══════════════════════════════════════════════════════════════════════════

# Add your custom pre-release logic here:
custom_pre_release_actions() {
    log "Executing custom pre-release actions..."
    
    # Example: Clear specific caches
    # php artisan cache:clear --tags=user-data
    
    # Example: Notify monitoring systems
    # curl -X POST https://monitoring.example.com/api/deployment-started
    
    # Example: Scale up infrastructure
    # aws ecs update-service --cluster prod --service web --desired-count 4
    
    # Example: Custom database preparations
    # php artisan custom:prepare-deployment
    
    log "✅ Custom pre-release actions completed"
}

# Execute main function
if main; then
    custom_pre_release_actions
    log "🎯 Pre-release hooks completed successfully"
    exit 0
else
    log "💥 Pre-release hooks failed - deployment should be aborted"
    exit 1
fi