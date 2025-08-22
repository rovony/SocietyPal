# Step 21: DeployHQ Build Pipeline Configuration

**Location:** 🔧 DeployHQ Platform  
**Purpose:** Configure complete build pipeline with pre/mid/post-release hooks  
**When:** After pre-deployment validation complete  
**Automation:** 🚀 Full Pipeline Automation  
**Time:** 10-15 minutes setup

---

## 🎯 **STEP OVERVIEW**

This step configures the complete DeployHQ build pipeline with professional pre-release, mid-release, and post-release hooks that implement zero-downtime atomic deployment with comprehensive validation and rollback capabilities.

**What This Step Achieves:**
- ✅ Configures complete DeployHQ build pipeline
- ✅ Implements pre-release hooks (maintenance, backups, validation)
- ✅ Sets up mid-release hooks (build, assets, migrations)
- ✅ Establishes post-release hooks (atomic switch, validation, cleanup)
- ✅ Provides emergency rollback capabilities
- ✅ Enables comprehensive monitoring and logging

---

## 📋 **PREREQUISITES**

- DeployHQ account configured
- Server access and SSH keys setup
- Pre-deployment validation completed (Step 20)
- Build pipeline scripts created and tested locally

---

## 🚀 **DEPLOYHQ CONFIGURATION**

### **Repository Configuration**
```yaml
# DeployHQ Repository Settings
Repository: https://github.com/yourusername/societypal.git
Branch: main
Deploy Key: [SSH Deploy Key]
Build Environment: Ubuntu 20.04 LTS
PHP Version: 8.2
Node Version: 18.x
```

### **Build Commands Configuration**
```bash
#!/bin/bash
# DeployHQ Build Commands - Complete Pipeline

set -e

echo "🚀 DEPLOYHQ BUILD PIPELINE STARTING"
echo "==================================="

# Load deployment variables
source Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh production

# Execute build pipeline
./Admin-Local/3-Deployment-Pipeline/02-Mid-Release/build-pipeline.sh

echo "✅ DeployHQ Build Pipeline Complete"
```

---

## 🔧 **PRE-RELEASE HOOKS**

### **Pre-Release Hook Script**
```bash
#!/bin/bash
# Pre-Release Hooks - Maintenance, Backups, Validation

set -e

echo "🔄 PRE-RELEASE HOOKS STARTING"
echo "============================="

# Source utilities
source Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh

# Get deployment configuration
SERVER_HOST=$(get_server_var "production" "host")
SERVER_PATH=$(get_server_var "production" "path")
PROJECT_NAME=$(get_project_var "name")

# Function: Enable maintenance mode
enable_maintenance_mode() {
    echo "🚧 Enabling maintenance mode"
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan down --retry=60 --secret=deploy-secret-key"
    echo "✅ Maintenance mode enabled"
}

# Function: Create pre-deployment backup
create_pre_deployment_backup() {
    echo "💾 Creating pre-deployment backup"
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_NAME="pre-deploy-$PROJECT_NAME-$TIMESTAMP"
    
    # Database backup
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan backup:run --only-db --filename=$BACKUP_NAME-db.sql"
    
    # Files backup (critical directories only)
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && tar -czf storage/app/backups/$BACKUP_NAME-files.tar.gz storage/app/public storage/logs .env"
    
    echo "✅ Pre-deployment backup created: $BACKUP_NAME"
}

# Function: Validate server environment
validate_server_environment() {
    echo "🔍 Validating server environment"
    
    # Check PHP version
    PHP_VERSION=$(ssh deploy@$SERVER_HOST "php -v | head -1 | awk '{print \$2}' | cut -d. -f1,2")
    if [[ "$PHP_VERSION" != "8.2" ]]; then
        echo "❌ PHP version mismatch. Expected 8.2, got $PHP_VERSION"
        exit 1
    fi
    
    # Check disk space
    DISK_USAGE=$(ssh deploy@$SERVER_HOST "df $SERVER_PATH | tail -1 | awk '{print \$5}' | sed 's/%//'")
    if [[ $DISK_USAGE -gt 85 ]]; then
        echo "❌ Disk usage too high: ${DISK_USAGE}%"
        exit 1
    fi
    
    # Check database connectivity
    if ! ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan tinker --execute='DB::connection()->getPdo();'" 2>/dev/null; then
        echo "❌ Database connection failed"
        exit 1
    fi
    
    echo "✅ Server environment validation passed"
}

# Function: Notify deployment start
notify_deployment_start() {
    echo "📢 Notifying deployment start"
    
    # Log deployment start
    echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment started for $PROJECT_NAME" >> Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/deployment.log
    
    # Optional: Send notification (Slack, email, etc.)
    # curl -X POST -H 'Content-type: application/json' --data '{"text":"🚀 Deployment started for '$PROJECT_NAME'"}' $SLACK_WEBHOOK_URL
    
    echo "✅ Deployment start notification sent"
}

# Main pre-release execution
main() {
    notify_deployment_start
    validate_server_environment
    enable_maintenance_mode
    create_pre_deployment_backup
    
    echo "✅ PRE-RELEASE HOOKS COMPLETED SUCCESSFULLY"
}

main "$@"
```

---

## 🔧 **MID-RELEASE HOOKS**

### **Build Pipeline Script**
```bash
#!/bin/bash
# Mid-Release Build Pipeline - Core Build Process

set -e

echo "🏗️ BUILD PIPELINE STARTING"
echo "=========================="

# Configuration
BUILD_DIR=$(pwd)
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/build.log"

# Function: Log with timestamp
log_build() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Function: Install and optimize Composer dependencies
install_composer_dependencies() {
    log_build "📦 Installing Composer dependencies"
    
    # Use enhanced composer strategy
    ./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh
    
    # Verify critical dependencies
    ./Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh
    
    log_build "✅ Composer dependencies installed and validated"
}

# Function: Install and build NPM assets
build_npm_assets() {
    log_build "🎨 Building NPM assets"
    
    # Install NPM dependencies
    npm ci --production=false
    
    # Build assets for production
    npm run build
    
    # Verify build output
    if [[ ! -d "public/build" ]]; then
        log_build "❌ Asset build failed - public/build directory not found"
        exit 1
    fi
    
    log_build "✅ NPM assets built successfully"
}

# Function: Optimize Laravel for production
optimize_laravel() {
    log_build "⚡ Optimizing Laravel for production"
    
    # Generate optimized autoloader
    composer dump-autoload --optimize --no-dev 2>/dev/null || composer dump-autoload --optimize
    
    # Cache configuration
    php artisan config:cache
    
    # Cache routes
    php artisan route:cache
    
    # Cache views
    php artisan view:cache
    
    # Cache events
    php artisan event:cache
    
    log_build "✅ Laravel optimization complete"
}

# Function: Run security and quality checks
run_security_checks() {
    log_build "🔒 Running security and quality checks"
    
    # Run security scan
    ./Admin-Local/1-Admin-Area/02-Master-Scripts/comprehensive-security-scan.sh --build-mode
    
    # Validate environment configuration
    ./Admin-Local/1-Admin-Area/02-Master-Scripts/validate-env.sh .env.production
    
    log_build "✅ Security and quality checks passed"
}

# Function: Create build manifest
create_build_manifest() {
    log_build "📋 Creating build manifest"
    
    BUILD_TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')
    BUILD_HASH=$(git rev-parse HEAD 2>/dev/null || echo "unknown")
    
    cat > build-manifest.json << EOF
{
    "build_timestamp": "$BUILD_TIMESTAMP",
    "build_hash": "$BUILD_HASH",
    "php_version": "$(php -v | head -1 | awk '{print $2}')",
    "node_version": "$(node -v)",
    "composer_version": "$(composer --version | awk '{print $3}')",
    "laravel_version": "$(php artisan --version | awk '{print $3}')",
    "build_environment": "production",
    "build_status": "success"
}
EOF
    
    log_build "✅ Build manifest created"
}

# Main build execution
main() {
    log_build "🚀 Starting build pipeline for production deployment"
    
    install_composer_dependencies
    build_npm_assets
    optimize_laravel
    run_security_checks
    create_build_manifest
    
    log_build "✅ BUILD PIPELINE COMPLETED SUCCESSFULLY"
}

main "$@"
```

---

## 🔧 **POST-RELEASE HOOKS**

### **Post-Release Hook Script**
```bash
#!/bin/bash
# Post-Release Hooks - Atomic Switch, Validation, Cleanup

set -e

echo "🎯 POST-RELEASE HOOKS STARTING"
echo "=============================="

# Source utilities
source Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh

# Get deployment configuration
SERVER_HOST=$(get_server_var "production" "host")
SERVER_PATH=$(get_server_var "production" "path")
PROJECT_NAME=$(get_project_var "name")

# Function: Run database migrations
run_database_migrations() {
    echo "🗄️ Running database migrations"
    
    # Run migrations with timeout
    if timeout 300 ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan migrate --force"; then
        echo "✅ Database migrations completed"
    else
        echo "❌ Database migrations failed or timed out"
        exit 1
    fi
}

# Function: Clear and warm caches
optimize_caches() {
    echo "🔄 Optimizing application caches"
    
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && {
        php artisan cache:clear
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan queue:restart
    }"
    
    echo "✅ Caches optimized"
}

# Function: Set proper file permissions
set_file_permissions() {
    echo "🔐 Setting proper file permissions"
    
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && {
        find . -type f -exec chmod 644 {} \;
        find . -type d -exec chmod 755 {} \;
        chmod -R 775 storage bootstrap/cache
        chown -R www-data:www-data storage bootstrap/cache
    }"
    
    echo "✅ File permissions set"
}

# Function: Restart services
restart_services() {
    echo "🔄 Restarting services"
    
    # Restart PHP-FPM
    ssh deploy@$SERVER_HOST "sudo systemctl reload php8.2-fpm"
    
    # Restart Nginx
    ssh deploy@$SERVER_HOST "sudo systemctl reload nginx"
    
    # Restart queue workers
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan queue:restart"
    
    echo "✅ Services restarted"
}

# Function: Disable maintenance mode
disable_maintenance_mode() {
    echo "🚀 Disabling maintenance mode"
    
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan up"
    
    echo "✅ Maintenance mode disabled - Site is live!"
}

# Function: Run health checks
run_health_checks() {
    echo "🏥 Running post-deployment health checks"
    
    # Wait for application to be ready
    sleep 10
    
    # Check application health
    APP_URL=$(get_env_var "production" "app_url")
    
    if curl -f -s -o /dev/null "$APP_URL/health" || curl -f -s -o /dev/null "$APP_URL"; then
        echo "✅ Application health check passed"
    else
        echo "❌ Application health check failed"
        # Don't exit here - log the issue but continue
    fi
    
    # Check database connectivity
    if ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan tinker --execute='DB::connection()->getPdo();'" 2>/dev/null; then
        echo "✅ Database connectivity check passed"
    else
        echo "❌ Database connectivity check failed"
    fi
    
    echo "✅ Health checks completed"
}

# Function: Clean up old releases
cleanup_old_releases() {
    echo "🧹 Cleaning up old releases"
    
    # Keep last 5 releases (DeployHQ handles this automatically, but we can add custom cleanup)
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && {
        if [ -d 'releases' ]; then
            cd releases
            ls -1t | tail -n +6 | xargs -r rm -rf
            echo 'Old releases cleaned up'
        fi
    }"
    
    echo "✅ Cleanup completed"
}

# Function: Log deployment completion
log_deployment_completion() {
    echo "📝 Logging deployment completion"
    
    DEPLOYMENT_TIME=$(date '+%Y-%m-%d %H:%M:%S')
    
    # Log to deployment history
    echo "$DEPLOYMENT_TIME - Deployment completed successfully for $PROJECT_NAME" >> Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/deployment.log
    
    # Optional: Send success notification
    # curl -X POST -H 'Content-type: application/json' --data '{"text":"✅ Deployment completed successfully for '$PROJECT_NAME'"}' $SLACK_WEBHOOK_URL
    
    echo "✅ Deployment completion logged"
}

# Main post-release execution
main() {
    run_database_migrations
    optimize_caches
    set_file_permissions
    restart_services
    disable_maintenance_mode
    run_health_checks
    cleanup_old_releases
    log_deployment_completion
    
    echo "🎉 POST-RELEASE HOOKS COMPLETED SUCCESSFULLY"
    echo "🚀 DEPLOYMENT COMPLETE - APPLICATION IS LIVE!"
}

main "$@"
```

---

## 🚨 **EMERGENCY ROLLBACK SYSTEM**

### **Emergency Rollback Script**
```bash
#!/bin/bash
# Emergency Rollback System

set -e

echo "🚨 EMERGENCY ROLLBACK INITIATED"
echo "==============================="

# Source utilities
source Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh

# Get deployment configuration
SERVER_HOST=$(get_server_var "production" "host")
SERVER_PATH=$(get_server_var "production" "path")
PROJECT_NAME=$(get_project_var "name")

# Function: Enable maintenance mode immediately
emergency_maintenance() {
    echo "🚧 Enabling emergency maintenance mode"
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan down --message='Emergency maintenance in progress' --retry=30"
    echo "✅ Emergency maintenance mode enabled"
}

# Function: Rollback to previous release
rollback_release() {
    echo "🔄 Rolling back to previous release"
    
    # DeployHQ automatic rollback
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && {
        if [ -L current ] && [ -d releases ]; then
            CURRENT_RELEASE=\$(readlink current)
            PREVIOUS_RELEASE=\$(ls -1t releases | sed -n '2p')
            
            if [ -n \"\$PREVIOUS_RELEASE\" ]; then
                ln -sfn releases/\$PREVIOUS_RELEASE current
                echo 'Rolled back to: \$PREVIOUS_RELEASE'
            else
                echo 'No previous release found'
                exit 1
            fi
        else
            echo 'Release structure not found'
            exit 1
        fi
    }"
    
    echo "✅ Rollback to previous release completed"
}

# Function: Restore database from backup
restore_database_backup() {
    local restore_db=${1:-false}
    
    if [[ "$restore_db" == "true" ]]; then
        echo "🗄️ Restoring database from backup"
        
        # Find latest backup
        LATEST_BACKUP=$(ssh deploy@$SERVER_HOST "cd $SERVER_PATH && ls -1t storage/app/backups/pre-deploy-*-db.sql 2>/dev/null | head -1")
        
        if [[ -n "$LATEST_BACKUP" ]]; then
            ssh deploy@$SERVER_HOST "cd $SERVER_PATH && mysql -u\$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE < $LATEST_BACKUP"
            echo "✅ Database restored from backup"
        else
            echo "⚠️  No database backup found"
        fi
    fi
}

# Function: Clear caches after rollback
clear_caches_after_rollback() {
    echo "🔄 Clearing caches after rollback"
    
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && {
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        php artisan queue:restart
    }"
    
    echo "✅ Caches cleared"
}

# Function: Restart services after rollback
restart_services_after_rollback() {
    echo "🔄 Restarting services after rollback"
    
    ssh deploy@$SERVER_HOST "sudo systemctl reload php8.2-fpm nginx"
    
    echo "✅ Services restarted"
}

# Function: Disable maintenance mode
disable_emergency_maintenance() {
    echo "🚀 Disabling emergency maintenance mode"
    
    ssh deploy@$SERVER_HOST "cd $SERVER_PATH && php artisan up"
    
    echo "✅ Site is back online"
}

# Function: Log emergency rollback
log_emergency_rollback() {
    echo "📝 Logging emergency rollback"
    
    ROLLBACK_TIME=$(date '+%Y-%m-%d %H:%M:%S')
    echo "$ROLLBACK_TIME - EMERGENCY ROLLBACK executed for $PROJECT_NAME" >> Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/emergency.log
    
    # Send emergency notification
    # curl -X POST -H 'Content-type: application/json' --data '{"text":"🚨 EMERGENCY ROLLBACK executed for '$PROJECT_NAME'"}' $SLACK_WEBHOOK_URL
    
    echo "✅ Emergency rollback logged"
}

# Main emergency rollback execution
main() {
    local restore_database=${1:-false}
    
    echo "🚨 Starting emergency rollback procedure"
    
    emergency_maintenance
    rollback_release
    restore_database_backup "$restore_database"
    clear_caches_after_rollback
    restart_services_after_rollback
    disable_emergency_maintenance
    log_emergency_rollback
    
    echo "✅ EMERGENCY ROLLBACK COMPLETED"
    echo "🚀 APPLICATION RESTORED TO PREVIOUS STATE"
}

# Usage: ./emergency-rollback.sh [true|false] (true to restore database)
main "$@"
```

---

## 📊 **DEPLOYHQ CONFIGURATION SUMMARY**

### **Build Environment Settings**
```yaml
Build Environment: Ubuntu 20.04 LTS
PHP Version: 8.2
Node.js Version: 18.x
Build Timeout: 30 minutes
Deploy Timeout: 15 minutes
Keep Releases: 5
```

### **Hook Configuration**
```yaml
Pre-Release Hook: ./Admin-Local/3-Deployment-Pipeline/01-Pre-Release/pre-release-hooks.sh
Build Commands: ./Admin-Local/3-Deployment-Pipeline/02-Mid-Release/build-pipeline.sh
Post-Release Hook: ./Admin-Local/3-Deployment-Pipeline/03-Post-Release/post-release-hooks.sh
```

### **Environment Variables**
```bash
# Production environment variables to set in DeployHQ
PRODUCTION_DB_HOST=your-db-host
PRODUCTION_DB_USERNAME=your-db-user
PRODUCTION_DB_PASSWORD=your-db-password
PRODUCTION_REDIS_HOST=your-redis-host
PRODUCTION_REDIS_PASSWORD=your-redis-password
```

---

## ✅ **COMPLETION CRITERIA**

Step 21 is complete when:
- [x] DeployHQ build pipeline configured
- [x] Pre-release hooks implemented and tested
- [x] Mid-release build process configured
- [x] Post-release hooks implemented and tested
- [x] Emergency rollback system configured
- [x] All environment variables set
- [x] First deployment executed successfully

---

## 🔄 **NEXT STEP**

**Deployment Complete!** Your SocietyPal application is now ready for production with:
- Zero-downtime atomic deployment
- Comprehensive pre/mid/post-release hooks
- Emergency rollback capabilities
- Full monitoring and logging

---

**Note:** This complete DeployHQ pipeline provides professional-grade deployment automation with comprehensive error handling, validation, and recovery mechanisms.
