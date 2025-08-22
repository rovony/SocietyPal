# 09 - SSH Commands Library

**Professional SSH automation scripts for Laravel deployment workflows**

## 📋 Library Overview

This library contains production-ready SSH commands and scripts for automating Laravel deployment processes. Commands are organized by deployment phase and tested for reliability.

## 🔄 Deployment Phases

### Phase A: Before Changes (Pre-deployment)
Commands executed immediately once connected to the server, before any files are transferred.

### Phase B: Before Release Link (Zero-downtime preparation)  
Commands executed after files are uploaded but before the new release becomes active.

### Phase C: After Changes (Post-deployment)
Commands executed at the end of deployment after files have been uploaded and the release is active.

## 📚 Command Categories

### 🔍 System Checks
- **A01**: System Preflight Checks
- **A02**: Backup Current Release
- **A03**: Database Backup
- **A04**: Maintenance Mode

### 🔗 Environment Setup
- **B01**: Initialize Shared Environment
- **B02**: Link Shared Resources
- **B03**: Create Directory Structure
- **B04**: Database Migrations
- **B05**: Application Optimization

### ✅ Post-Deployment
- **C01**: Storage Symlink Creation
- **C02**: Service Restart
- **C03**: Exit Maintenance Mode
- **C04**: Health Checks
- **C05**: Cleanup Operations

## 🚀 Core Command Library

### Phase A: Pre-Deployment Commands

#### A01 - System Preflight Checks

```bash
#!/bin/bash
# A01: System Pre-flight Checks
# Execution: Every deployment
# Purpose: Validate server environment and requirements
# Timeout: 5 minutes

echo "=== System Pre-flight Checks ==="

REQUIRED_COMMANDS=("php" "git" "composer" "curl" "find" "sed" "grep" "date" "touch" "rm" "ln" "mkdir" "chmod" "du" "tail" "xargs" "wc" "awk" "df")
FAILED_CHECKS=0

for cmd in "${REQUIRED_COMMANDS[@]}"; do
  if ! command -v "$cmd" &> /dev/null; then
    echo "⚠️ WARNING: Required command '$cmd' not found."
    FAILED_CHECKS=$((FAILED_CHECKS + 1))
  else
    echo "✅ Command '$cmd' found"
  fi
done

# Check PHP version
if command -v php &> /dev/null; then
  PHP_VERSION=$(php -r "echo PHP_VERSION;")
  if ! php -r "exit(version_compare(PHP_VERSION, '8.0.0', '>=') ? 0 : 1);" &> /dev/null; then
    echo "⚠️ WARNING: PHP version $PHP_VERSION < 8.0.0"
    FAILED_CHECKS=$((FAILED_CHECKS + 1))
  else
    echo "✅ PHP version $PHP_VERSION meets requirements"
  fi
fi

# Check disk space
MIN_DISK_KB=1048576  # 1GB
AVAILABLE_DISK_KB=$(df -k . 2>/dev/null | awk 'NR==2 {print $4}' || echo "999999999")
if [ "$AVAILABLE_DISK_KB" -lt "$MIN_DISK_KB" ]; then
  echo "⚠️ WARNING: Low disk space available"
  FAILED_CHECKS=$((FAILED_CHECKS + 1))
else
  echo "✅ Sufficient disk space available"
fi

if [ "$FAILED_CHECKS" -ne 0 ]; then
  echo "⚠️ Pre-flight completed with $FAILED_CHECKS warning(s)"
else
  echo "✅ All pre-flight checks passed"
fi

exit 0
```

#### A02 - Backup Current Release

```bash
#!/bin/bash
# A02: Backup Current Release
# Execution: Every deployment
# Purpose: Backup current release files and prepare backup directories
# Timeout: 5 minutes

echo "=== Backup Current Release ==="

# Use deployment path variables
DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
SHARED_PATH="$DEPLOY_BASE/shared"
CURRENT_PATH="$DEPLOY_BASE/current"

# Create backup directory structure
BACKUP_ROOT="$SHARED_PATH/backups"
DEPLOYMENT_TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
CURRENT_BACKUP_DIR="$BACKUP_ROOT/deploy_production_${DEPLOYMENT_TIMESTAMP}"

echo "Creating backup directories..."
mkdir -p "$BACKUP_ROOT" || { echo "❌ Failed to create backup root"; exit 1; }
mkdir -p "$CURRENT_BACKUP_DIR" || { echo "❌ Failed to create deployment backup dir"; exit 1; }

# Backup current release files (if current release exists)
if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then
  echo "🔄 Backing up current release files..."
  
  # Get the actual release directory name
  CURRENT_RELEASE=$(basename "$(readlink "$CURRENT_PATH")")
  BACKUP_NAME="release_${CURRENT_RELEASE}_backup_$(date +%Y%m%d_%H%M%S)"
  
  echo "Creating backup of current release: $CURRENT_RELEASE"
  
  # Create a compressed backup of the current release
  cd "$DEPLOY_BASE/releases" || exit 1
  if tar -czf "$CURRENT_BACKUP_DIR/$BACKUP_NAME.tar.gz" "$CURRENT_RELEASE" 2>/dev/null; then
    BACKUP_SIZE=$(du -sh "$CURRENT_BACKUP_DIR/$BACKUP_NAME.tar.gz" | cut -f1)
    echo "✅ Current release backed up: $BACKUP_NAME.tar.gz ($BACKUP_SIZE)"
  else
    echo "⚠️ Failed to create release backup, continuing..."
  fi
else
  echo "ℹ️ No current release to backup (first deployment)"
fi

# Handle public_html backup (for hosting providers)
cd "$DEPLOY_BASE" || exit 1
if [ -d "public_html" ] && [ ! -L "public_html" ]; then
  echo "🔄 Backing up existing public_html directory..."
  BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)"
  mv public_html "$CURRENT_BACKUP_DIR/$BACKUP_NAME"
  echo "✅ public_html backed up to: $CURRENT_BACKUP_DIR/$BACKUP_NAME"
else
  echo "ℹ️ No public_html directory to backup (already symlinked)"
fi

# Set permissions
chmod 755 "$BACKUP_ROOT"
chmod 755 "$CURRENT_BACKUP_DIR"

# Store backup path for other commands
echo "$CURRENT_BACKUP_DIR" > "$SHARED_PATH/.current_backup_dir"

echo "✅ Backup system initialized: $CURRENT_BACKUP_DIR"
```

#### A03 - Database Backup

```bash
#!/bin/bash
# A03: Database Backup
# Execution: Every deployment
# Purpose: Create database backup before deployment
# Timeout: 10 minutes

echo "=== Database Backup ==="

# Get backup directory from previous command
SHARED_PATH="$HOME/domains/$(basename $PWD)/deploy/shared"
BACKUP_DIR=$(cat "$SHARED_PATH/.current_backup_dir" 2>/dev/null || echo "$SHARED_PATH/backups/$(date +%Y%m%d_%H%M%S)")

# Check for Laravel application and .env file
if [ -f "$SHARED_PATH/.env" ]; then
  echo "🔄 Creating database backup..."
  
  # Extract database configuration from .env
  DB_CONNECTION=$(grep "^DB_CONNECTION=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  DB_HOST=$(grep "^DB_HOST=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  DB_PORT=$(grep "^DB_PORT=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  DB_DATABASE=$(grep "^DB_DATABASE=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  DB_USERNAME=$(grep "^DB_USERNAME=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  DB_PASSWORD=$(grep "^DB_PASSWORD=" "$SHARED_PATH/.env" | cut -d'=' -f2)
  
  if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_DATABASE" ]; then
    BACKUP_FILE="$BACKUP_DIR/database_backup_$(date +%Y%m%d_%H%M%S).sql"
    
    # Create MySQL backup
    if mysqldump -h"${DB_HOST:-localhost}" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null; then
      BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
      echo "✅ Database backup created: $(basename $BACKUP_FILE) ($BACKUP_SIZE)"
      
      # Compress the backup
      gzip "$BACKUP_FILE"
      echo "✅ Database backup compressed: $(basename $BACKUP_FILE).gz"
    else
      echo "⚠️ Database backup failed, continuing deployment..."
    fi
  else
    echo "ℹ️ Non-MySQL database or no database configured"
  fi
else
  echo "ℹ️ No .env file found, skipping database backup"
fi

echo "✅ Database backup process completed"
```

#### A04 - Enter Maintenance Mode

```bash
#!/bin/bash
# A04: Enter Maintenance Mode
# Execution: Every deployment
# Purpose: Put Laravel application in maintenance mode during deployment
# Timeout: 2 minutes

echo "=== Enter Maintenance Mode ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
CURRENT_PATH="$DEPLOY_BASE/current"

if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then
  echo "🔄 Enabling Laravel maintenance mode..."
  
  cd "$CURRENT_PATH" || exit 1
  
  # Check if artisan exists
  if [ -f "artisan" ]; then
    # Put application in maintenance mode with custom message
    php artisan down --message="Application is being updated. Please check back in a few minutes." --retry=60
    
    if [ $? -eq 0 ]; then
      echo "✅ Maintenance mode enabled"
      echo "ℹ️ Custom maintenance message set"
    else
      echo "⚠️ Failed to enable maintenance mode, continuing..."
    fi
  else
    echo "ℹ️ No artisan file found, skipping maintenance mode"
  fi
else
  echo "ℹ️ No current release found, skipping maintenance mode"
fi

echo "✅ Maintenance mode process completed"
```

### Phase B: Pre-Release Commands

#### B01 - Initialize Shared Environment

```bash
#!/bin/bash
# B01: Initialize Shared Environment
# Execution: Every deployment
# Purpose: Create and configure shared directories and environment
# Timeout: 3 minutes

echo "=== Initialize Shared Environment ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
SHARED_PATH="$DEPLOY_BASE/shared"

# Create shared directory structure
echo "🔄 Creating shared directory structure..."
mkdir -p "$SHARED_PATH/storage/app/public"
mkdir -p "$SHARED_PATH/storage/framework/cache"
mkdir -p "$SHARED_PATH/storage/framework/sessions"
mkdir -p "$SHARED_PATH/storage/framework/views"
mkdir -p "$SHARED_PATH/storage/logs"

# Set proper permissions
chmod -R 775 "$SHARED_PATH/storage"

# Initialize .env file if it doesn't exist
if [ ! -f "$SHARED_PATH/.env" ]; then
  echo "🔄 Creating initial .env file..."
  
  if [ -f ".env.example" ]; then
    cp .env.example "$SHARED_PATH/.env"
    echo "✅ .env file created from .env.example"
    echo "⚠️ IMPORTANT: Configure .env file with production settings!"
  else
    echo "⚠️ No .env.example found, creating minimal .env"
    cat > "$SHARED_PATH/.env" << 'EOF'
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
EOF
    echo "⚠️ CRITICAL: Update .env file with actual production settings!"
  fi
else
  echo "✅ Shared .env file already exists"
fi

# Set proper permissions
chmod 644 "$SHARED_PATH/.env"

echo "✅ Shared environment initialized"
```

#### B02 - Link Shared Resources

```bash
#!/bin/bash
# B02: Link Shared Environment
# Execution: Every deployment
# Purpose: Link shared resources to current release
# Timeout: 2 minutes

echo "=== Link Shared Environment ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
SHARED_PATH="$DEPLOY_BASE/shared"

# Link .env file
echo "🔗 Linking shared .env file..."
if [ -f "$SHARED_PATH/.env" ]; then
  rm -f .env
  ln -s "$SHARED_PATH/.env" .env
  echo "✅ .env file linked"
else
  echo "⚠️ Shared .env file not found!"
fi

# Link storage directory
echo "🔗 Linking shared storage directory..."
if [ -d "$SHARED_PATH/storage" ]; then
  rm -rf storage
  ln -s "$SHARED_PATH/storage" storage
  echo "✅ Storage directory linked"
else
  echo "⚠️ Shared storage directory not found!"
fi

# Verify links
echo "🔍 Verifying symbolic links..."
if [ -L ".env" ] && [ -e ".env" ]; then
  echo "✅ .env link verified"
else
  echo "❌ .env link broken"
fi

if [ -L "storage" ] && [ -d "storage" ]; then
  echo "✅ Storage link verified"
else
  echo "❌ Storage link broken"
fi

echo "✅ Shared resources linked"
```

#### B04 - Run Database Migrations

```bash
#!/bin/bash
# B04: Run Database Migrations
# Execution: Every deployment
# Purpose: Execute Laravel database migrations safely
# Timeout: 10 minutes

echo "=== Run Database Migrations ==="

# Check if artisan exists
if [ ! -f "artisan" ]; then
  echo "⚠️ No artisan file found, skipping migrations"
  exit 0
fi

# Check database connectivity
echo "🔍 Testing database connectivity..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';" 2>/dev/null; then
  echo "✅ Database connection verified"
else
  echo "❌ Database connection failed!"
  echo "⚠️ Skipping migrations due to database connectivity issues"
  exit 0
fi

# Check for pending migrations
echo "🔍 Checking for pending migrations..."
PENDING_MIGRATIONS=$(php artisan migrate:status --pending 2>/dev/null | grep -c "N/A" || echo "0")

if [ "$PENDING_MIGRATIONS" -gt 0 ]; then
  echo "🔄 Found $PENDING_MIGRATIONS pending migration(s)"
  echo "🔄 Running database migrations..."
  
  # Run migrations with force flag (no interaction)
  if php artisan migrate --force --no-interaction; then
    echo "✅ Database migrations completed successfully"
    
    # Show migration status
    echo "📊 Current migration status:"
    php artisan migrate:status | tail -5
  else
    echo "❌ Database migration failed!"
    echo "🔄 Attempting to rollback..."
    php artisan migrate:rollback --force --no-interaction
    exit 1
  fi
else
  echo "ℹ️ No pending migrations found"
fi

echo "✅ Migration process completed"
```

#### B05 - Optimize Application

```bash
#!/bin/bash
# B05: Optimize Application
# Execution: Every deployment
# Purpose: Cache configurations and optimize Laravel application
# Timeout: 5 minutes

echo "=== Optimize Application ==="

# Check if artisan exists
if [ ! -f "artisan" ]; then
  echo "⚠️ No artisan file found, skipping optimization"
  exit 0
fi

echo "🔄 Optimizing Laravel application..."

# Clear existing caches
echo "🧹 Clearing existing caches..."
php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped"
php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped"
php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped"
php artisan view:clear 2>/dev/null || echo "ℹ️ View clear skipped"

# Cache configurations
echo "⚡ Caching configurations..."
php artisan config:cache
if [ $? -eq 0 ]; then
  echo "✅ Configuration cached"
else
  echo "⚠️ Configuration caching failed"
fi

# Cache routes
echo "⚡ Caching routes..."
php artisan route:cache
if [ $? -eq 0 ]; then
  echo "✅ Routes cached"
else
  echo "⚠️ Route caching failed"
fi

# Cache views
echo "⚡ Caching views..."
php artisan view:cache
if [ $? -eq 0 ]; then
  echo "✅ Views cached"
else
  echo "⚠️ View caching failed"
fi

# Run general optimization
echo "⚡ Running general optimization..."
php artisan optimize
if [ $? -eq 0 ]; then
  echo "✅ General optimization completed"
else
  echo "⚠️ General optimization failed"
fi

# Verify optimization
echo "🔍 Verifying optimization..."
if [ -f "bootstrap/cache/config.php" ]; then
  echo "✅ Config cache file exists"
fi

if [ -f "bootstrap/cache/routes-v7.php" ]; then
  echo "✅ Route cache file exists"
fi

if [ -f "bootstrap/cache/compiled.php" ]; then
  echo "✅ Compiled views cache exists"
fi

echo "✅ Application optimization completed"
```

### Phase C: Post-Deployment Commands

#### C01 - Create Storage Symlink

```bash
#!/bin/bash
# C01: Create Storage Symlink
# Execution: Every deployment
# Purpose: Create public storage symlink for file access
# Timeout: 2 minutes

echo "=== Create Storage Symlink ==="

# Check if artisan exists
if [ ! -f "artisan" ]; then
  echo "⚠️ No artisan file found, skipping storage link"
  exit 0
fi

echo "🔗 Creating storage symlink..."

# Remove existing symlink if it exists
if [ -L "public/storage" ]; then
  echo "🔄 Removing existing storage symlink..."
  rm public/storage
fi

# Create storage symlink
php artisan storage:link

if [ $? -eq 0 ]; then
  echo "✅ Storage symlink created successfully"
  
  # Verify the symlink
  if [ -L "public/storage" ] && [ -d "public/storage" ]; then
    echo "✅ Storage symlink verified"
    echo "📁 Symlink target: $(readlink public/storage)"
  else
    echo "⚠️ Storage symlink verification failed"
  fi
else
  echo "⚠️ Storage symlink creation failed"
fi

echo "✅ Storage symlink process completed"
```

#### C03 - Exit Maintenance Mode

```bash
#!/bin/bash
# C03: Exit Maintenance Mode
# Execution: Every deployment
# Purpose: Bring Laravel application out of maintenance mode
# Timeout: 2 minutes

echo "=== Exit Maintenance Mode ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
CURRENT_PATH="$DEPLOY_BASE/current"

if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then
  echo "🔄 Disabling Laravel maintenance mode..."
  
  cd "$CURRENT_PATH" || exit 1
  
  # Check if artisan exists
  if [ -f "artisan" ]; then
    # Bring application out of maintenance mode
    php artisan up
    
    if [ $? -eq 0 ]; then
      echo "✅ Maintenance mode disabled"
      echo "✅ Application is now live"
    else
      echo "⚠️ Failed to disable maintenance mode"
    fi
  else
    echo "ℹ️ No artisan file found, skipping maintenance mode exit"
  fi
else
  echo "ℹ️ No current release found"
fi

echo "✅ Maintenance mode exit completed"
```

#### C04 - Health Checks

```bash
#!/bin/bash
# C04: Health Checks
# Execution: Every deployment
# Purpose: Verify application health after deployment
# Timeout: 5 minutes

echo "=== Health Checks ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
CURRENT_PATH="$DEPLOY_BASE/current"

if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then
  cd "$CURRENT_PATH" || exit 1
  
  echo "🔍 Running application health checks..."
  
  # Check if artisan exists and Laravel is working
  if [ -f "artisan" ]; then
    echo "🔍 Testing Laravel application..."
    
    # Test artisan command
    if php artisan --version > /dev/null 2>&1; then
      echo "✅ Laravel artisan working"
      LARAVEL_VERSION=$(php artisan --version)
      echo "ℹ️ $LARAVEL_VERSION"
    else
      echo "❌ Laravel artisan not working"
    fi
    
    # Test database connectivity
    echo "🔍 Testing database connectivity..."
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" 2>/dev/null | grep -q "Database OK"; then
      echo "✅ Database connection working"
    else
      echo "⚠️ Database connection issues detected"
    fi
    
    # Check application configuration
    echo "🔍 Checking application configuration..."
    if php artisan about 2>/dev/null | grep -q "Environment"; then
      echo "✅ Application configuration loaded"
      echo "📊 Application info:"
      php artisan about | head -5
    else
      echo "⚠️ Application configuration issues"
    fi
  else
    echo "ℹ️ No artisan file found"
  fi
  
  # Check file permissions
  echo "🔍 Checking file permissions..."
  if [ -w "storage/logs" ]; then
    echo "✅ Storage directory writable"
  else
    echo "⚠️ Storage directory not writable"
  fi
  
  if [ -w "bootstrap/cache" ]; then
    echo "✅ Bootstrap cache directory writable"
  else
    echo "⚠️ Bootstrap cache directory not writable"
  fi
  
  # Check web server accessibility (if possible)
  if command -v curl > /dev/null 2>&1; then
    echo "🔍 Testing web server response..."
    # Try to get the domain from .env or use localhost
    DOMAIN=$(grep "^APP_URL=" .env 2>/dev/null | cut -d'=' -f2 | sed 's|https://||' | sed 's|http://||' || echo "localhost")
    
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://$DOMAIN" 2>/dev/null || echo "000")
    if [ "$HTTP_STATUS" = "200" ]; then
      echo "✅ Web server responding (HTTP 200)"
    elif [ "$HTTP_STATUS" != "000" ]; then
      echo "⚠️ Web server responding with HTTP $HTTP_STATUS"
    else
      echo "ℹ️ Could not test web server response"
    fi
  fi
else
  echo "ℹ️ No current release found for health checks"
fi

echo "✅ Health checks completed"
```

#### C07 - Cleanup Old Releases

```bash
#!/bin/bash
# C07: Cleanup Old Releases
# Execution: Every deployment
# Purpose: Remove old deployment releases to save disk space
# Timeout: 3 minutes

echo "=== Cleanup Old Releases ==="

DEPLOY_BASE="$HOME/domains/$(basename $PWD)/deploy"
RELEASES_PATH="$DEPLOY_BASE/releases"
KEEP_RELEASES=3  # Number of releases to keep

if [ -d "$RELEASES_PATH" ]; then
  cd "$RELEASES_PATH" || exit 1
  
  echo "🔍 Checking for old releases to cleanup..."
  
  # Count total releases
  TOTAL_RELEASES=$(ls -1 | wc -l)
  echo "📊 Total releases found: $TOTAL_RELEASES"
  
  if [ "$TOTAL_RELEASES" -gt "$KEEP_RELEASES" ]; then
    RELEASES_TO_DELETE=$((TOTAL_RELEASES - KEEP_RELEASES))
    echo "🧹 Removing $RELEASES_TO_DELETE old release(s)..."
    
    # Get list of releases to delete (oldest first)
    OLD_RELEASES=$(ls -t | tail -n +$((KEEP_RELEASES + 1)))
    
    for release in $OLD_RELEASES; do
      if [ -d "$release" ]; then
        RELEASE_SIZE=$(du -sh "$release" | cut -f1)
        echo "🗑️ Removing old release: $release ($RELEASE_SIZE)"
        rm -rf "$release"
        
        if [ $? -eq 0 ]; then
          echo "✅ Removed $release"
        else
          echo "⚠️ Failed to remove $release"
        fi
      fi
    done
    
    echo "✅ Cleanup completed"
  else
    echo "ℹ️ No cleanup needed (keeping $KEEP_RELEASES releases)"
  fi
  
  # Show remaining releases
  echo "📊 Remaining releases:"
  ls -t | head -$KEEP_RELEASES | while read release; do
    RELEASE_SIZE=$(du -sh "$release" | cut -f1)
    echo "   📦 $release ($RELEASE_SIZE)"
  done
  
  # Show total disk usage
  TOTAL_SIZE=$(du -sh . | cut -f1)
  echo "📊 Total releases disk usage: $TOTAL_SIZE"
else
  echo "ℹ️ No releases directory found"
fi

echo "✅ Release cleanup completed"
```

## 🛠️ Platform-Specific Command Sets

### DeployHQ Professional Commands

For DeployHQ deployments, use these optimized command sequences:

```bash
# DeployHQ Pre-deployment
A01-System-Preflight-Checks
A02-Backup-Current-Release
A03-Database-Backup
A04-Enter-Maintenance-Mode

# DeployHQ Pre-release
B01-Initialize-Shared-Environment
B02-Link-Shared-Resources
B04-Run-Database-Migrations
B05-Optimize-Application

# DeployHQ Post-deployment
C01-Create-Storage-Symlink
C03-Exit-Maintenance-Mode
C04-Health-Checks
C07-Cleanup-Old-Releases
```

### GitHub Actions Commands

For CI/CD workflows, use these automation-friendly commands:

```bash
# Minimal command set for automated deployments
A01-System-Preflight-Checks
B02-Link-Shared-Resources
B04-Run-Database-Migrations
B05-Optimize-Application
C01-Create-Storage-Symlink
C04-Health-Checks
```

## 📋 Usage Examples

### Complete Deployment Script

```bash
#!/bin/bash
# Complete Laravel deployment script using SSH commands library

set -e  # Exit on any error

echo "🚀 Starting Laravel deployment..."

# Phase A: Pre-deployment
echo "📋 Phase A: Pre-deployment checks..."
bash A01-System-Preflight-Checks.sh
bash A02-Backup-Current-Release.sh
bash A03-Database-Backup.sh
bash A04-Enter-Maintenance-Mode.sh

# Phase B: Pre-release setup
echo "📋 Phase B: Pre-release setup..."
bash B01-Initialize-Shared-Environment.sh
bash B02-Link-Shared-Resources.sh
bash B04-Run-Database-Migrations.sh
bash B05-Optimize-Application.sh

# Phase C: Post-deployment
echo "📋 Phase C: Post-deployment..."
bash C01-Create-Storage-Symlink.sh
bash C03-Exit-Maintenance-Mode.sh
bash C04-Health-Checks.sh
bash C07-Cleanup-Old-Releases.sh

echo "🎉 Laravel deployment completed successfully!"
```

### Quick Deployment (Essential Commands Only)

```bash
#!/bin/bash
# Quick deployment with essential commands only

echo "⚡ Quick Laravel deployment..."

# Essential pre-checks
bash A01-System-Preflight-Checks.sh

# Core deployment tasks
bash B02-Link-Shared-Resources.sh
bash B04-Run-Database-Migrations.sh
bash B05-Optimize-Application.sh

# Essential post-deployment
bash C01-Create-Storage-Symlink.sh
bash C04-Health-Checks.sh

echo "✅ Quick deployment completed!"
```

## 🔧 Customization Guide

### Environment Variables

Set these variables to customize command behavior:

```bash
# Deployment configuration
export KEEP_RELEASES=5          # Number of releases to keep
export BACKUP_RETENTION_DAYS=30 # Days to keep backups
export DEPLOYMENT_TIMEOUT=600   # Deployment timeout in seconds

# Database configuration
export DB_BACKUP_COMPRESSION=true  # Compress database backups
export DB_MIGRATION_TIMEOUT=300    # Migration timeout in seconds

# Application configuration
export OPTIMIZE_LEVEL=full      # full|basic|minimal
export HEALTH_CHECK_RETRIES=3   # Number of health check retries
```

### Custom Command Template

```bash
#!/bin/bash
# [Command ID]: [Command Name]
# Execution: [When to run]
# Purpose: [What this command does]
# Timeout: [Maximum execution time]

echo "=== [Command Name] ==="

# Error handling
set -e
trap 'echo "❌ [Command Name] failed at line $LINENO"' ERR

# Pre-checks
echo "🔍 Pre-checks..."
# Add pre-check logic here

# Main execution
echo "🔄 [Main action description]..."
# Add main command logic here

# Post-verification
echo "✅ [Action completed]..."
# Add verification logic here

echo "✅ [Command Name] completed"
```

## 🆘 Troubleshooting

### Common Issues

#### Permission Errors
```bash
# Fix common permission issues
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

#### Database Connection Issues
```bash
# Test database connectivity
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: OK';
} catch (Exception \$e) {
    echo 'Database error: ' . \$e->getMessage();
}
"
```

#### Symlink Problems
```bash
# Fix broken symlinks
rm -f .env public/storage
ln -s ../shared/.env .env
php artisan storage:link
```

---

**🎯 Ready to automate your Laravel deployments?**

Choose the appropriate command set for your deployment platform and customize as needed!

👉 **Popular Command Sets:**
- [DeployHQ Professional Commands](./deployhq-commands.md) for enterprise deployments
- [GitHub Actions Commands](./github-actions-commands.md) for CI/CD automation
- [Manual Deployment Commands](./manual-deployment-commands.md) for traditional hosting
