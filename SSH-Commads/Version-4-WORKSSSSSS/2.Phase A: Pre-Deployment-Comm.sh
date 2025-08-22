#!/bin/bash
set -e

# Phase A: Pre-Deployment Commands (Before Upload)
# Purpose: System checks, backups, maintenance mode, environment preparation
# Version 3 - PRODUCTION READY (Enhanced with %path% variable only)
echo "=== Phase A: Pre-Deployment Setup (V3) ==="

# ENHANCED: Define all variables relative to %path% (only available DeployHQ variable)
# %path% = Base server path we're deploying to (e.g., /home/user/domains/site.com/deploy)
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"

echo "🔧 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Current Path: $CURRENT_PATH"

# A01: System Pre-flight Checks
echo "=== System Pre-flight Checks ==="
php --version || { echo "❌ PHP not found"; exit 1; }

# Check for Composer 2 (required for Laravel 12+)
if command -v composer2 &> /dev/null; then
    composer2 --version
    echo "✅ Using Composer 2 for Laravel 12+ compatibility"
    export COMPOSER_CMD="composer2"
elif composer --version | grep -q "version 2\."; then
    composer --version
    echo "✅ Composer 2 detected"
    export COMPOSER_CMD="composer"
else
    composer --version
    echo "⚠️ WARNING: Composer 1.x detected, Laravel 12+ requires Composer 2.x"
    echo "ℹ️ Will attempt to use available composer, but may cause issues"
    export COMPOSER_CMD="composer"
fi

node --version 2>/dev/null || echo "ℹ️ Node not required"

# Check disk space
AVAILABLE_DISK=$(df -k "$DEPLOY_PATH" 2>/dev/null | awk 'NR==2 {print $4}')
if [ "$AVAILABLE_DISK" -lt 524288 ]; then  # 512MB minimum
    echo "❌ Insufficient disk space"
    exit 1
fi

# A02: Create Universal Shared Structure (COMPREHENSIVE)
echo "=== Initialize Universal Shared Structure ==="

# Core Laravel directories (standard)
mkdir -p "$SHARED_PATH/storage/{app/public,framework/{cache/data,sessions,views},logs}"
mkdir -p "$SHARED_PATH/bootstrap/cache"
mkdir -p "$SHARED_PATH/backups"
mkdir -p "$SHARED_PATH/public/.well-known"  # For SSL certificates

# 1. User Content Variations Pattern (UNIVERSAL COVERAGE)
echo "Creating user content directories..."
mkdir -p "$SHARED_PATH/public/upload"        # Covers: upload
mkdir -p "$SHARED_PATH/public/uploads"       # Covers: uploads  
mkdir -p "$SHARED_PATH/public/uploaded"      # Covers: uploaded
mkdir -p "$SHARED_PATH/public/user-upload"   # Covers: user-upload
mkdir -p "$SHARED_PATH/public/user-uploads"  # Covers: user-uploads
mkdir -p "$SHARED_PATH/public/media"         # Covers: media
mkdir -p "$SHARED_PATH/public/medias"        # Covers: medias
mkdir -p "$SHARED_PATH/public/avatar"        # Covers: avatar
mkdir -p "$SHARED_PATH/public/avatars"       # Covers: avatars
mkdir -p "$SHARED_PATH/public/clientAvatar"  # Covers: clientAvatar
mkdir -p "$SHARED_PATH/public/attachment"    # Covers: attachment
mkdir -p "$SHARED_PATH/public/attachments"   # Covers: attachments
mkdir -p "$SHARED_PATH/public/document"      # Covers: document
mkdir -p "$SHARED_PATH/public/documents"     # Covers: documents
mkdir -p "$SHARED_PATH/public/file"          # Covers: file
mkdir -p "$SHARED_PATH/public/files"         # Covers: files
mkdir -p "$SHARED_PATH/public/images"        # Covers: images (user-generated)

# 2. User Generated Content (DYNAMIC CONTENT)
echo "Creating generated content directories..."
mkdir -p "$SHARED_PATH/public/qrcode"        # Generated QR codes
mkdir -p "$SHARED_PATH/public/qrcodes"       # Generated QR codes (plural)
mkdir -p "$SHARED_PATH/public/barcode"       # Generated barcodes
mkdir -p "$SHARED_PATH/public/barcodes"      # Generated barcodes (plural)
mkdir -p "$SHARED_PATH/public/certificate"   # Generated certificates
mkdir -p "$SHARED_PATH/public/certificates"  # Generated certificates (plural)
mkdir -p "$SHARED_PATH/public/report"        # Generated reports
mkdir -p "$SHARED_PATH/public/reports"       # Generated reports (plural)
mkdir -p "$SHARED_PATH/public/temp"          # Temporary files
mkdir -p "$SHARED_PATH/public/temporary"     # Temporary files (alternative)

# 3. CodeCanyon Application Specific (PRESERVATION PATTERNS)
echo "Creating CodeCanyon-specific directories..."
mkdir -p "$SHARED_PATH/storage/app"           # For modules_statuses.json (CRITICAL!)
mkdir -p "$SHARED_PATH/Modules"               # Custom modules (root level)
mkdir -p "$SHARED_PATH/public/favicon"        # Custom favicons

# Set proper permissions for all shared directories
echo "Setting permissions for shared directories..."
chmod -R 775 "$SHARED_PATH/storage"
chmod -R 775 "$SHARED_PATH/bootstrap/cache"
chmod 755 "$SHARED_PATH/backups"
chmod 755 "$SHARED_PATH/public/.well-known"

# User content permissions (writable)
chmod -R 775 "$SHARED_PATH/public/upload"*
chmod -R 775 "$SHARED_PATH/public/user-upload"*
chmod -R 775 "$SHARED_PATH/public/media"*
chmod -R 775 "$SHARED_PATH/public/avatar"*
chmod -R 775 "$SHARED_PATH/public/attachment"*
chmod -R 775 "$SHARED_PATH/public/document"*
chmod -R 775 "$SHARED_PATH/public/file"*
chmod -R 775 "$SHARED_PATH/public/images"

# Generated content permissions (writable)
chmod -R 775 "$SHARED_PATH/public/qrcode"*
chmod -R 775 "$SHARED_PATH/public/barcode"*
chmod -R 775 "$SHARED_PATH/public/certificate"*
chmod -R 775 "$SHARED_PATH/public/report"*
chmod -R 775 "$SHARED_PATH/public/temp"*

# CodeCanyon specific permissions
chmod -R 775 "$SHARED_PATH/storage/app"       # CRITICAL for modules_statuses.json
chmod -R 775 "$SHARED_PATH/Modules"
chmod 755 "$SHARED_PATH/public/favicon"

echo "✅ Shared structure ready for DeployHQ symlinks"

# A03: Backup Current Production (if exists)
echo "=== Backup Current Release ==="
if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then
    TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    BACKUP_DIR="$SHARED_PATH/backups/release_${TIMESTAMP}"
    mkdir -p "$BACKUP_DIR"
    
    # Quick backup of critical files only
    cd "$CURRENT_PATH"
    tar -czf "$BACKUP_DIR/app_backup.tar.gz" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage' \
        --exclude='bootstrap/cache' \
        app config database resources routes 2>/dev/null || true
    
    echo "✅ Backup created: $BACKUP_DIR"
    
    # Keep only last 5 backups
    cd "$SHARED_PATH/backups"
    ls -t | tail -n +6 | xargs rm -rf 2>/dev/null || true
fi

# A04: Database Backup (ENHANCED VERSION - FIXED from deployment report)
echo "=== Database Backup ==="
if [ -f "$SHARED_PATH/.env" ]; then
    echo "Parsing .env file for database credentials..."
    
    # Use PHP to safely parse .env file (more reliable than bash)
    cat > /tmp/parse_env_v2.php << 'EOF'
<?php
if (empty($argv[1]) || !is_file($argv[1])) {
    exit(1);
}

$lines = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    if (strpos($line, '=') === false) {
        continue;
    }
    
    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);
    
    // Remove quotes if present
    if (preg_match('/^"(.*)"$/', $value, $matches)) {
        $value = $matches[1];
    } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) {
        $value = $matches[1];
    }
    
    $env[$name] = $value;
}

// Output only the variables we need
echo "DB_CONNECTION=" . ($env['DB_CONNECTION'] ?? 'mysql') . "\n";
echo "DB_HOST=" . ($env['DB_HOST'] ?? 'localhost') . "\n";
echo "DB_PORT=" . ($env['DB_PORT'] ?? '3306') . "\n";
echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? '') . "\n";
echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? '') . "\n";
echo "DB_PASSWORD=" . ($env['DB_PASSWORD'] ?? '') . "\n";
EOF

    # Parse the .env file and extract variables
    ENV_VARS=$(php /tmp/parse_env_v2.php "$SHARED_PATH/.env" 2>/dev/null)
    rm -f /tmp/parse_env_v2.php
    
    if [ -n "$ENV_VARS" ]; then
        # Load the variables into the current shell
        eval "$ENV_VARS"
        
        echo "Database configuration:"
        echo "  Host: $DB_HOST"
        echo "  Port: $DB_PORT"
        echo "  Database: $DB_DATABASE"
        echo "  Username: $DB_USERNAME"
        
        if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
            BACKUP_FILE="$SHARED_PATH/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz"
            echo "Creating database backup..."
            
            # Test database connection first (ENHANCED from deployment report)
            if command -v mysql &> /dev/null; then
                # Test connection with timeout
                if timeout 10 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null; then
                    echo "✅ Database connection test passed"
                    
                    # Create backup with better error handling
                    if mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
                        --single-transaction --routines --triggers --add-drop-table \
                        "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"; then
                        
                        if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
                            BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
                            echo "✅ Database backed up: $BACKUP_FILE ($BACKUP_SIZE)"
                            
                            # Set secure permissions on backup
                            chmod 600 "$BACKUP_FILE"
                        else
                            echo "⚠️ Database backup failed or empty"
                            rm -f "$BACKUP_FILE" 2>/dev/null
                        fi
                    else
                        echo "⚠️ mysqldump failed - check credentials and permissions"
                        rm -f "$BACKUP_FILE" 2>/dev/null
                    fi
                else
                    echo "⚠️ Cannot connect to database - skipping backup"
                    echo "ℹ️ This may be normal for first deployment or if database is not ready"
                fi
            else
                echo "⚠️ mysql client not available - skipping database backup"
                echo "ℹ️ Install mysql-client package if database backups are needed"
            fi
        else
            echo "⚠️ Incomplete database configuration in .env"
            echo "ℹ️ Ensure DB_DATABASE and DB_USERNAME are set"
        fi
    else
        echo "⚠️ Failed to parse .env file"
        echo "ℹ️ Check .env file syntax and permissions"
    fi
else
    echo "ℹ️ No .env file found - skipping database backup"
    echo "ℹ️ This is normal for first deployment"
fi

# A05: Enter Maintenance Mode (ENHANCED VERSION)
echo "=== Enter Maintenance Mode ==="
# Create maintenance flag for all hosting types
touch "$DEPLOY_PATH/.maintenance"

# Try Laravel maintenance if current release exists
if [ -f "$CURRENT_PATH/artisan" ]; then
    cd "$CURRENT_PATH"
    # Enhanced error handling for artisan down command
    if php artisan down --render="errors::503" 2>/dev/null; then
        echo "✅ Laravel maintenance mode activated"
    else
        echo "⚠️ Laravel maintenance mode failed, using maintenance flag only"
        echo "ℹ️ This may be due to missing dependencies (will be fixed in Phase B)"
    fi
else
    echo "ℹ️ No current release (first deployment) - using maintenance flag only"
fi

echo "✅ Phase A completed successfully"
