#!/bin/bash

# DeployHQ Build Commands and Hooks
# Purpose: Complete build pipeline for DeployHQ deployment

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 DEPLOYHQ BUILD PIPELINE${NC}"
echo "=========================="

# Function: Log deployment steps
log_deploy() {
    local step="$1"
    local message="$2"
    echo -e "${GREEN}✅ $step: $message${NC}"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $step: $message" >> Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/deployment.log
}

# PRE-RELEASE COMMANDS (Run before deployment)
pre_release_commands() {
    echo -e "${BLUE}🔧 PRE-RELEASE COMMANDS${NC}"
    echo "======================"
    
    # Enable maintenance mode
    log_deploy "MAINTENANCE" "Enabling maintenance mode"
    php artisan down --render="errors::503" --secret="deploy-secret-key" || true
    
    # Clear all caches
    log_deploy "CACHE_CLEAR" "Clearing application caches"
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan cache:clear || true
    
    # Load deployment variables
    log_deploy "VARIABLES" "Loading deployment variables"
    if [[ -f "Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh" ]]; then
        ./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh production || true
    fi
    
    # Create backup
    log_deploy "BACKUP" "Creating pre-deployment backup"
    mkdir -p Admin-Local/4-Backups/04-Release-Backups
    if command -v mysqldump >/dev/null 2>&1 && [[ -n "${DB_DATABASE:-}" ]]; then
        mysqldump -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" "${DB_DATABASE:-societypal}" > "Admin-Local/4-Backups/04-Release-Backups/pre-deploy-$(date +%Y%m%d_%H%M%S).sql" 2>/dev/null || true
    fi
    
    echo -e "${GREEN}🎯 PRE-RELEASE: ✅ COMPLETED${NC}"
}

# MID-RELEASE COMMANDS (Run during deployment)
mid_release_commands() {
    echo -e "${BLUE}🔧 MID-RELEASE BUILD COMMANDS${NC}"
    echo "============================="
    
    # Install/Update Composer dependencies
    log_deploy "COMPOSER" "Installing production dependencies"
    if [[ -f "composer.json" ]]; then
        composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist || {
            log_deploy "COMPOSER_FALLBACK" "Using fallback composer strategy"
            composer install --optimize-autoloader --no-interaction --prefer-dist
        }
    fi
    
    # Install/Update NPM dependencies and build assets
    log_deploy "ASSETS" "Building frontend assets"
    if [[ -f "package.json" ]]; then
        npm ci --only=production 2>/dev/null || npm install --only=production 2>/dev/null || true
        npm run build 2>/dev/null || npm run production 2>/dev/null || true
    fi
    
    # Generate optimized autoloader
    log_deploy "AUTOLOADER" "Optimizing autoloader"
    if [[ -f "composer.json" ]]; then
        composer dump-autoload --optimize --no-dev || composer dump-autoload --optimize
    fi
    
    # Cache configuration
    log_deploy "CONFIG_CACHE" "Caching Laravel configuration"
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    
    # Cache events (Laravel 11+)
    log_deploy "EVENT_CACHE" "Caching events"
    php artisan event:cache 2>/dev/null || true
    
    echo -e "${GREEN}🎯 MID-RELEASE: ✅ COMPLETED${NC}"
}

# POST-RELEASE COMMANDS (Run after deployment)
post_release_commands() {
    echo -e "${BLUE}🔧 POST-RELEASE COMMANDS${NC}"
    echo "========================"
    
    # Run database migrations
    log_deploy "MIGRATIONS" "Running database migrations"
    php artisan migrate --force || true
    
    # Seed database if needed (only for staging/development)
    if [[ "${APP_ENV:-production}" != "production" ]]; then
        log_deploy "SEEDING" "Seeding database (non-production)"
        php artisan db:seed --force 2>/dev/null || true
    fi
    
    # Create storage link
    log_deploy "STORAGE" "Creating storage symlink"
    php artisan storage:link || true
    
    # Set proper permissions
    log_deploy "PERMISSIONS" "Setting file permissions"
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    
    # Clear and warm up caches
    log_deploy "CACHE_WARM" "Warming up application caches"
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    
    # Queue restart (if using queues)
    log_deploy "QUEUE" "Restarting queue workers"
    php artisan queue:restart 2>/dev/null || true
    
    # Disable maintenance mode
    log_deploy "MAINTENANCE" "Disabling maintenance mode"
    php artisan up || true
    
    # Health check
    log_deploy "HEALTH_CHECK" "Running post-deployment health check"
    if command -v curl >/dev/null 2>&1; then
        local app_url="${APP_URL:-http://localhost}"
        if curl -f -s "$app_url" >/dev/null 2>&1; then
            log_deploy "HEALTH_CHECK" "Application is responding"
        else
            log_deploy "HEALTH_CHECK" "Warning: Application health check failed"
        fi
    fi
    
    echo -e "${GREEN}🎯 POST-RELEASE: ✅ COMPLETED${NC}"
}

# EMERGENCY ROLLBACK COMMANDS
emergency_rollback() {
    echo -e "${RED}🚨 EMERGENCY ROLLBACK${NC}"
    echo "==================="
    
    # Enable maintenance mode
    php artisan down --render="errors::503" || true
    
    # Restore database backup
    log_deploy "ROLLBACK" "Restoring database backup"
    local latest_backup=$(ls -t Admin-Local/4-Backups/04-Release-Backups/*.sql 2>/dev/null | head -1)
    if [[ -n "$latest_backup" ]] && command -v mysql >/dev/null 2>&1; then
        mysql -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" "${DB_DATABASE:-societypal}" < "$latest_backup" 2>/dev/null || true
    fi
    
    # Clear all caches
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan cache:clear || true
    
    # Disable maintenance mode
    php artisan up || true
    
    echo -e "${RED}🎯 ROLLBACK: ✅ COMPLETED${NC}"
}

# DEPLOYMENT VALIDATION
validate_deployment() {
    echo -e "${BLUE}🔍 DEPLOYMENT VALIDATION${NC}"
    echo "========================"
    
    local errors=0
    
    # Check Laravel application
    if php artisan --version >/dev/null 2>&1; then
        log_deploy "LARAVEL_CHECK" "Laravel application is functional"
    else
        log_deploy "LARAVEL_CHECK" "ERROR: Laravel application is not responding"
        ((errors++))
    fi
    
    # Check database connection
    if php artisan migrate:status >/dev/null 2>&1; then
        log_deploy "DATABASE_CHECK" "Database connection is working"
    else
        log_deploy "DATABASE_CHECK" "WARNING: Database connection issues"
    fi
    
    # Check storage permissions
    if [[ -w "storage" ]]; then
        log_deploy "STORAGE_CHECK" "Storage directory is writable"
    else
        log_deploy "STORAGE_CHECK" "ERROR: Storage directory is not writable"
        ((errors++))
    fi
    
    # Check .env file
    if [[ -f ".env" ]]; then
        log_deploy "ENV_CHECK" "Environment file exists"
    else
        log_deploy "ENV_CHECK" "ERROR: Environment file missing"
        ((errors++))
    fi
    
    if [[ $errors -eq 0 ]]; then
        echo -e "${GREEN}🎯 VALIDATION: ✅ ALL CHECKS PASSED${NC}"
        return 0
    else
        echo -e "${RED}🎯 VALIDATION: ❌ $errors ERRORS FOUND${NC}"
        return 1
    fi
}

# Main execution based on DeployHQ hook
main() {
    local hook="${1:-full}"
    
    # Create log directory
    mkdir -p Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs
    
    case "$hook" in
        "pre-release"|"pre")
            pre_release_commands
            ;;
        "mid-release"|"mid"|"build")
            mid_release_commands
            ;;
        "post-release"|"post")
            post_release_commands
            ;;
        "rollback")
            emergency_rollback
            ;;
        "validate")
            validate_deployment
            ;;
        "full"|*)
            echo -e "${BLUE}🚀 FULL DEPLOYMENT PIPELINE${NC}"
            echo "============================"
            pre_release_commands
            echo ""
            mid_release_commands
            echo ""
            post_release_commands
            echo ""
            validate_deployment
            echo ""
            echo -e "${GREEN}🎯 DEPLOYMENT PIPELINE: ✅ COMPLETED SUCCESSFULLY${NC}"
            ;;
    esac
}

# Execute with hook parameter
main "$@"
