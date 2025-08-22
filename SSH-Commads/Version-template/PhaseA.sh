#!/bin/bash
set -e

# Phase A: Pre-Deployment Commands (Before Upload)
# Purpose: System checks, backups, maintenance mode
# Note: DeployHQ handles file upload and release directory creation
# V2
echo "=== Phase A: Pre-Deployment Setup ==="

# A01: System Pre-flight Checks
echo "=== System Checks ==="
php --version || { echo "❌ PHP not found"; exit 1; }
composer --version || { echo "❌ Composer not found"; exit 1; }

# Check disk space
AVAILABLE_DISK=$(df -k "%path%" 2>/dev/null | awk 'NR==2 {print $4}')
if [ "$AVAILABLE_DISK" -lt 524288 ]; then  # 512MB minimum
    echo "❌ Insufficient disk space"
    exit 1
fi

# A02: Create Shared Structure (if not exists)
echo "=== Initialize Shared Structure ==="
# Only create if doesn't exist - DeployHQ handles the main structure
if [ ! -d "%shared_path%" ]; then
    mkdir -p %shared_path%/storage/{app/public,framework/{cache/data,sessions,views},logs}
    mkdir -p %shared_path%/bootstrap/cache
    mkdir -p %shared_path%/backups
    mkdir -p %shared_path%/public/uploads  # For user uploads
    echo "✅ Created shared directories"
fi

# A03: Backup Current Production (if exists)
echo "=== Backup Current Release ==="
if [ -L "%current_path%" ] && [ -d "%current_path%" ]; then
    TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    BACKUP_DIR="%shared_path%/backups/release_${TIMESTAMP}"
    mkdir -p "$BACKUP_DIR"
    
    # Quick backup of critical files only
    cd %current_path%
    tar -czf "$BACKUP_DIR/app_backup.tar.gz" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage' \
        --exclude='bootstrap/cache' \
        app config database resources routes 2>/dev/null || true
    
    echo "✅ Backup created: $BACKUP_DIR"
    
    # Keep only last 5 backups
    cd %shared_path%/backups
    ls -t | tail -n +6 | xargs rm -rf 2>/dev/null || true
fi

# A04: Database Backup
echo "=== Database Backup ==="
if [ -f "%shared_path%/.env" ]; then
    # Safe env parsing
    DB_CONNECTION=$(grep "^DB_CONNECTION=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "mysql")
    DB_HOST=$(grep "^DB_HOST=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "localhost")
    DB_PORT=$(grep "^DB_PORT=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "3306")
    DB_DATABASE=$(grep "^DB_DATABASE=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    DB_USERNAME=$(grep "^DB_USERNAME=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    DB_PASSWORD=$(grep "^DB_PASSWORD=" %shared_path%/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    
    if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
        BACKUP_FILE="%shared_path%/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz"
        mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
            --single-transaction --no-tablespaces --skip-lock-tables \
            "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE" || echo "⚠️ DB backup failed"
        
        if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
            echo "✅ Database backed up"
        else
            rm -f "$BACKUP_FILE" 2>/dev/null
        fi
    fi
fi

# A05: Enter Maintenance Mode
echo "=== Enter Maintenance Mode ==="
# Create maintenance flag for all hosting types
touch %path%/.maintenance

# Try Laravel maintenance if current release exists
if [ -f "%current_path%/artisan" ]; then
    cd %current_path%
    php artisan down --render="errors::503" 2>/dev/null || true
fi

echo "✅ Phase A completed successfully"