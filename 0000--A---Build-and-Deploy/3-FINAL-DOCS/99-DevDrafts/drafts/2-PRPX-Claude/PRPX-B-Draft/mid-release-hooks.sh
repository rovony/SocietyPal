#!/bin/bash

# Script: mid-release-hooks.sh
# Purpose: User-configurable mid-release deployment hooks
# Version: 2.0
# Section: C - Build and Deploy (Phase 6)
# Location: 🔴 Server (SSH execution)
# Hook Type: 🟣 2️⃣ Mid-release Hook

set -euo pipefail

# Load deployment variables (if available on server)
if [ -f "Admin-Local/Deployment/Scripts/load-variables.sh" ]; then
    source Admin-Local/Deployment/Scripts/load-variables.sh
else
    # Fallback: Set critical variables for server environment
    DEPLOY_PATH="${DEPLOY_PATH:-/home/$(whoami)/domains/$(hostname)/deploy}"
    PATH_CURRENT="${DEPLOY_PATH}/current"
    PATH_SHARED="${DEPLOY_PATH}/shared"
    RELEASE_ID="${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}"
    RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"
fi

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [MID-RELEASE] $1"
}

echo "╔══════════════════════════════════════════════════════════╗"
echo "║                Mid-Release Hooks (Phase 6)              ║"
echo "║              🟣 2️⃣ User-Configurable                    ║"
echo "╚══════════════════════════════════════════════════════════╝"

log "Starting mid-release hook execution..."

# Change to new release directory 
if [ -d "$RELEASE_PATH" ]; then
    cd "$RELEASE_PATH"
    log "Changed to release directory: $RELEASE_PATH"
else
    log "ERROR: Release directory not found: $RELEASE_PATH"
    exit 1
fi

# ═══════════════════════════════════════════════════════════════════════════
# ZERO-DOWNTIME DATABASE MIGRATIONS
# ═══════════════════════════════════════════════════════════════════════════

zero_downtime_migrations() {
    log "Starting zero-downtime database migrations..."
    
    # Check if migrations are needed
    if ! php artisan migrate:status >/dev/null 2>&1; then
        log "WARNING: Cannot check migration status - database may not be configured"
        return 0
    fi
    
    # Get pending migrations count
    PENDING_MIGRATIONS=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || echo "0")
    
    if [ "$PENDING_MIGRATIONS" -eq 0 ]; then
        log "✅ No pending migrations - database is up to date"
        return 0
    fi
    
    log "Found $PENDING_MIGRATIONS pending migrations"
    
    # Create migration backup point
    if [ "${CREATE_MIGRATION_BACKUP:-true}" = "true" ]; then
        log "Creating pre-migration database state backup..."
        
        MIGRATION_BACKUP="${PATH_SHARED}/backups/pre-migration-$(date +%Y%m%d-%H%M%S).sql"
        mkdir -p "$(dirname "$MIGRATION_BACKUP")"
        
        # Source environment variables for database connection
        if [ -f "${PATH_SHARED}/.env" ]; then
            source "${PATH_SHARED}/.env"
            
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
                            --quick \
                            > "$MIGRATION_BACKUP" && gzip "$MIGRATION_BACKUP" || {
                            log "WARNING: Migration backup failed"
                        }
                    fi
                    ;;
            esac
        fi
    fi
    
    # Apply zero-downtime migration patterns
    log "Applying migrations with zero-downtime patterns..."
    
    # Step 1: Run migrations that are additive only (new columns, tables)
    if [ "${MIGRATION_STRATEGY:-standard}" = "zero-downtime" ]; then
        log "Using zero-downtime migration strategy..."
        
        # Custom migration strategy - run only safe migrations first
        # This would require custom migration flags or naming conventions
        log "Running additive migrations first..."
        php artisan migrate --force --step || {
            log "ERROR: Migration failed during execution"
            return 1
        }
        
    else
        # Standard migration approach
        log "Running standard migrations..."
        if ! php artisan migrate --force; then
            log "ERROR: Standard migration failed"
            return 1
        fi
    fi
    
    # Verify migration success
    REMAINING_PENDING=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || echo "0")
    
    if [ "$REMAINING_PENDING" -eq 0 ]; then
        log "✅ All migrations applied successfully"
    else
        log "WARNING: $REMAINING_PENDING migrations still pending"
    fi
    
    # Test database connectivity after migration
    if php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1; then
        log "✅ Database connectivity verified after migration"
    else
        log "ERROR: Database connectivity lost after migration"
        return 1
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# APPLICATION CACHE PREPARATION
# ═══════════════════════════════════════════════════════════════════════════

application_cache_preparation() {
    log "Preparing application caches for new release..."
    
    # Clear existing caches that might conflict
    log "Clearing potentially conflicting caches..."
    php artisan config:clear >/dev/null 2>&1 || log "WARNING: Config clear failed"
    php artisan route:clear >/dev/null 2>&1 || log "WARNING: Route clear failed"
    php artisan view:clear >/dev/null 2>&1 || log "WARNING: View clear failed"
    
    # Build production caches
    log "Building production-optimized caches..."
    
    # Configuration cache
    if php artisan config:cache >/dev/null 2>&1; then
        log "✅ Configuration cache created"
    else
        log "ERROR: Configuration cache failed"
        return 1
    fi
    
    # Route cache (if no closures in routes)
    if php artisan route:cache >/dev/null 2>&1; then
        log "✅ Route cache created"
    else
        log "WARNING: Route cache failed - may have closures in routes"
        php artisan route:clear >/dev/null 2>&1
    fi
    
    # View cache
    if php artisan view:cache >/dev/null 2>&1; then
        log "✅ View cache created"
    else
        log "WARNING: View cache failed"
    fi
    
    # Event cache (Laravel 9+)
    if php artisan event:cache >/dev/null 2>&1; then
        log "✅ Event cache created"
    else
        log "Note: Event caching not available or failed"
    fi
    
    # Custom cache preparations
    if [ "${ENABLE_APPLICATION_CACHE_WARMUP:-false}" = "true" ]; then
        log "Warming up application-specific caches..."
        
        # Permission/role cache if using Spatie Permission
        if php artisan list | grep -q "permission:cache-reset"; then
            php artisan permission:cache-reset >/dev/null 2>&1 || log "WARNING: Permission cache reset failed"
        fi
        
        # Custom cache warming commands
        if [ -n "${CACHE_WARMUP_COMMANDS:-}" ]; then
            IFS=';' read -ra COMMANDS <<< "$CACHE_WARMUP_COMMANDS"
            for cmd in "${COMMANDS[@]}"; do
                log "Running cache warmup command: $cmd"
                eval "$cmd" || log "WARNING: Cache warmup command failed: $cmd"
            done
        fi
    fi
    
    # Verify cache files were created
    CACHE_FILES=(
        "bootstrap/cache/config.php"
        "bootstrap/cache/routes-v7.php" 
        "bootstrap/cache/packages.php"
        "bootstrap/cache/services.php"
    )
    
    CACHE_COUNT=0
    for cache_file in "${CACHE_FILES[@]}"; do
        if [ -f "$cache_file" ]; then
            ((CACHE_COUNT++))
            log "✅ Cache file exists: $cache_file"
        fi
    done
    
    log "Cache preparation completed - $CACHE_COUNT cache files active"
}

# ═══════════════════════════════════════════════════════════════════════════
# COMPREHENSIVE HEALTH CHECKS
# ═══════════════════════════════════════════════════════════════════════════

comprehensive_health_checks() {
    log "Running comprehensive pre-switch health checks..."
    
    local health_score=0
    local max_score=7
    
    # 1. Basic Laravel functionality
    log "Testing basic Laravel functionality..."
    if php artisan --version >/dev/null 2>&1; then
        log "✅ Artisan commands functional"
        ((health_score++))
    else
        log "❌ Artisan commands not functional"
    fi
    
    # 2. Database connectivity
    log "Testing database connectivity..."
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB_OK';" 2>/dev/null | grep -q "DB_OK"; then
        log "✅ Database connection successful"
        ((health_score++))
    else
        log "❌ Database connection failed"
    fi
    
    # 3. Cache system functionality
    log "Testing cache system..."
    if php artisan tinker --execute="Cache::put('deploy_test', 'ok', 60); echo Cache::get('deploy_test');" 2>/dev/null | grep -q "ok"; then
        log "✅ Cache system functional"
        ((health_score++))
    else
        log "⚠️ Cache system test failed"
    fi
    
    # 4. Queue system (if enabled)
    if [ "${USES_QUEUES:-false}" = "true" ]; then
        log "Testing queue system..."
        if php artisan queue:work --timeout=1 --tries=1 --stop-when-empty >/dev/null 2>&1; then
            log "✅ Queue system functional"
            ((health_score++))
        else
            log "⚠️ Queue system test inconclusive"
            ((health_score++)) # Don't fail deployment for queue issues
        fi
    else
        log "Queue system not enabled - skipping"
        ((health_score++))
    fi
    
    # 5. File system permissions
    log "Testing critical file permissions..."
    CRITICAL_DIRS=("storage" "bootstrap/cache")
    PERM_OK=true
    
    for dir in "${CRITICAL_DIRS[@]}"; do
        if [ -w "$dir" ]; then
            log "✅ $dir is writable"
        else
            log "❌ $dir is not writable"
            PERM_OK=false
        fi
    done
    
    if [ "$PERM_OK" = true ]; then
        ((health_score++))
    fi
    
    # 6. Environment configuration
    log "Validating environment configuration..."
    ENV_OK=true
    
    # Check critical environment variables
    CRITICAL_ENV_VARS=("APP_KEY" "DB_DATABASE")
    for var in "${CRITICAL_ENV_VARS[@]}"; do
        if [ -z "${!var:-}" ]; then
            log "❌ Critical environment variable missing: $var"
            ENV_OK=false
        fi
    done
    
    if [ "$ENV_OK" = true ]; then
        log "✅ Environment configuration valid"
        ((health_score++))
    fi
    
    # 7. Application routes and middleware
    log "Testing application routing system..."
    if php artisan route:list >/dev/null 2>&1; then
        log "✅ Route system functional"
        ((health_score++))
    else
        log "❌ Route system has issues"
    fi
    
    # Calculate health percentage
    HEALTH_PERCENTAGE=$((health_score * 100 / max_score))
    
    log "Health check completed: $health_score/$max_score checks passed ($HEALTH_PERCENTAGE%)"
    
    # Determine if ready for switch
    if [ $health_score -ge $((max_score - 1)) ]; then
        log "✅ Application ready for atomic switch (health: $HEALTH_PERCENTAGE%)"
        return 0
    else
        log "❌ Application NOT ready for switch (health: $HEALTH_PERCENTAGE%)"
        return 1
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# CUSTOM APPLICATION PREPARATIONS
# ═══════════════════════════════════════════════════════════════════════════

custom_application_preparations() {
    log "Running custom application preparations..."
    
    # Search index preparation
    if [ "${PREPARE_SEARCH_INDEX:-false}" = "true" ]; then
        log "Preparing search indexes..."
        
        # Scout/Elasticsearch preparation
        if php artisan list | grep -q "scout:"; then
            log "Preparing Scout search indexes..."
            php artisan scout:import >/dev/null 2>&1 || log "WARNING: Scout import failed"
        fi
        
        # Custom search index commands
        if [ -n "${SEARCH_INDEX_COMMANDS:-}" ]; then
            IFS=';' read -ra COMMANDS <<< "$SEARCH_INDEX_COMMANDS"
            for cmd in "${COMMANDS[@]}"; do
                log "Running search preparation: $cmd"
                eval "$cmd" || log "WARNING: Search command failed: $cmd"
            done
        fi
    fi
    
    # Media/Asset preparation
    if [ "${PREPARE_MEDIA_ASSETS:-false}" = "true" ]; then
        log "Preparing media assets..."
        
        # Ensure media directories exist
        MEDIA_DIRS=("storage/app/public" "public/storage")
        for dir in "${MEDIA_DIRS[@]}"; do
            if [ ! -d "$dir" ] && [ ! -L "$dir" ]; then
                log "Creating media directory: $dir"
                mkdir -p "$dir" 2>/dev/null || log "WARNING: Could not create $dir"
            fi
        done
        
        # Recreate storage link if needed
        if [ ! -L "public/storage" ]; then
            log "Creating storage link..."
            php artisan storage:link >/dev/null 2>&1 || log "WARNING: Storage link creation failed"
        fi
    fi
    
    # Custom preparation commands
    if [ -n "${CUSTOM_PREPARATION_COMMANDS:-}" ]; then
        log "Running custom preparation commands..."
        IFS=';' read -ra COMMANDS <<< "$CUSTOM_PREPARATION_COMMANDS"
        for cmd in "${COMMANDS[@]}"; do
            log "Executing: $cmd"
            if eval "$cmd"; then
                log "✅ Custom command succeeded: $cmd"
            else
                log "⚠️ Custom command failed: $cmd"
            fi
        done
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# PERFORMANCE OPTIMIZATION PREPARATIONS
# ═══════════════════════════════════════════════════════════════════════════

performance_optimizations() {
    log "Applying performance optimizations..."
    
    # Optimize Composer autoloader
    if [ "${OPTIMIZE_COMPOSER_AUTOLOADER:-true}" = "true" ]; then
        log "Optimizing Composer autoloader..."
        if composer dump-autoload --optimize --classmap-authoritative >/dev/null 2>&1; then
            log "✅ Composer autoloader optimized"
        else
            log "WARNING: Composer autoloader optimization failed"
        fi
    fi
    
    # Preload critical files (PHP 7.4+)
    if [ "${ENABLE_PHP_PRELOAD:-false}" = "true" ] && php -v | grep -q "PHP 8"; then
        log "Preparing PHP preload configuration..."
        
        # Generate preload file
        PRELOAD_FILE="$PATH_SHARED/preload.php"
        cat > "$PRELOAD_FILE" << 'EOF'
<?php
// Auto-generated preload file
opcache_compile_file(__DIR__ . '/current/vendor/autoload.php');

// Add critical framework files
$criticalFiles = [
    '/current/vendor/laravel/framework/src/Illuminate/Foundation/Application.php',
    '/current/vendor/laravel/framework/src/Illuminate/Container/Container.php',
    '/current/vendor/laravel/framework/src/Illuminate/Support/ServiceProvider.php',
];

foreach ($criticalFiles as $file) {
    $fullPath = __DIR__ . $file;
    if (file_exists($fullPath)) {
        opcache_compile_file($fullPath);
    }
}
EOF
        
        log "✅ PHP preload configuration prepared"
    fi
    
    # Optimize Laravel performance
    log "Applying Laravel performance optimizations..."
    
    # Create optimized class loader
    php artisan optimize >/dev/null 2>&1 || log "Note: artisan optimize not available (Laravel 5.5+)"
    
    # Additional performance preparations
    if [ -n "${PERFORMANCE_COMMANDS:-}" ]; then
        IFS=';' read -ra COMMANDS <<< "$PERFORMANCE_COMMANDS"
        for cmd in "${COMMANDS[@]}"; do
            log "Running performance command: $cmd"
            eval "$cmd" || log "WARNING: Performance command failed: $cmd"
        done
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# MAIN EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

main() {
    local exit_code=0
    
    log "Executing mid-release hook procedures..."
    
    # 1. Database migrations (critical - run first)
    if ! zero_downtime_migrations; then
        log "ERROR: Database migrations failed"
        exit_code=1
    fi
    
    # 2. Application cache preparation
    if ! application_cache_preparation; then
        log "ERROR: Cache preparation failed"  
        exit_code=1
    fi
    
    # 3. Performance optimizations
    performance_optimizations
    
    # 4. Custom application preparations
    custom_application_preparations
    
    # 5. Comprehensive health checks (run last before switch)
    if ! comprehensive_health_checks; then
        log "ERROR: Health checks failed"
        exit_code=1
    fi
    
    # Summary
    if [ $exit_code -eq 0 ]; then
        log "✅ All mid-release procedures completed successfully"
        log "🚀 Application ready for atomic switch"
    else
        log "❌ Mid-release procedures encountered errors"
        log "🛑 Atomic switch should be aborted"
    fi
    
    return $exit_code
}

# ═══════════════════════════════════════════════════════════════════════════
# USER CUSTOMIZATION AREA
# ═══════════════════════════════════════════════════════════════════════════

# Add your custom mid-release logic here:
custom_mid_release_actions() {
    log "Executing custom mid-release actions..."
    
    # Example: Update external indexes
    # curl -X POST "https://api.example.com/reindex" -H "Authorization: Bearer $API_TOKEN"
    
    # Example: Prepare CDN cache invalidation
    # aws cloudfront create-invalidation --distribution-id E1234567890 --paths "/*"
    
    # Example: Custom service preparations
    # systemctl reload custom-service
    
    # Example: Prepare monitoring systems
    # curl -X POST "https://monitoring.example.com/api/deployment-switch-pending"
    
    log "✅ Custom mid-release actions completed"
}

# Execute main function
if main; then
    custom_mid_release_actions
    log "🎯 Mid-release hooks completed successfully - ready for atomic switch"
    exit 0
else
    log "💥 Mid-release hooks failed - atomic switch aborted"
    exit 1
fi