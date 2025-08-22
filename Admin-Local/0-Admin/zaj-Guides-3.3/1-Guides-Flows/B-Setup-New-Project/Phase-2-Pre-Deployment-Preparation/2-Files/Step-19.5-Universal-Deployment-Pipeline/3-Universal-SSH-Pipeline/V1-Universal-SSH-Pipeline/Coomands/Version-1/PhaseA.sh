#!/bin/bash
set -e

# Phase A: Pre-Deployment Commands (Before Upload)
# Purpose: Prepare environment, backup, enter maintenance mode
# v1 - FIXED VERSION
echo "=== Phase A: Pre-Deployment Setup ==="

# A01: System Checks
echo "=== System Pre-flight Checks ==="
php --version || { echo "❌ PHP not found"; exit 1; }
composer --version || { echo "❌ Composer not found"; exit 1; }
node --version 2>/dev/null || echo "ℹ️ Node not required"

# Verify disk space
AVAILABLE_DISK=$(df -k "%path%" 2>/dev/null | awk 'NR==2 {print $4}')
if [ "$AVAILABLE_DISK" -lt 1048576 ]; then
    echo "⚠️ Warning: Low disk space"
fi

# A02: Initialize Shared Structure (First deployment safe)
echo "=== Initialize Shared Structure ==="
mkdir -p %shared_path%/storage/{app/public,framework/{cache/data,sessions,views},logs}
mkdir -p %shared_path%/bootstrap/cache
mkdir -p %shared_path%/backups
mkdir -p %shared_path%/public/.well-known

# Set permissions
chmod -R 775 %shared_path%/storage 2>/dev/null || true
chmod -R 775 %shared_path%/bootstrap/cache 2>/dev/null || true

# A03: Backup Current Release (if exists)
echo "=== Backup Current Release ==="
if [ -L "%current_path%" ] && [ -d "%current_path%" ]; then
    BACKUP_TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    BACKUP_DIR="%shared_path%/backups/release_${BACKUP_TIMESTAMP}"
    
    # Create compressed backup
    echo "Creating backup..."
    mkdir -p "$BACKUP_DIR"
    cd %path%/releases
    CURRENT_RELEASE=$(basename "$(readlink "%current_path%")")
    tar -czf "$BACKUP_DIR/release_${CURRENT_RELEASE}.tar.gz" "$CURRENT_RELEASE" 2>/dev/null || true
    echo "✅ Backup created: $BACKUP_DIR"
else
    echo "ℹ️ No current release to backup (first deployment)"
fi

# A04: Database Backup (FIXED VERSION)
echo "=== Database Backup ==="
if [ -f "%shared_path%/.env" ]; then
    echo "Parsing .env file for database credentials..."
    
    # Use PHP to safely parse .env file (more reliable than bash)
    cat > /tmp/parse_env.php << 'EOF'
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
    ENV_VARS=$(php /tmp/parse_env.php "%shared_path%/.env" 2>/dev/null)
    rm -f /tmp/parse_env.php
    
    if [ -n "$ENV_VARS" ]; then
        # Load the variables into the current shell
        eval "$ENV_VARS"
        
        echo "Database configuration:"
        echo "  Host: $DB_HOST"
        echo "  Port: $DB_PORT"
        echo "  Database: $DB_DATABASE"
        echo "  Username: $DB_USERNAME"
        
        if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
            BACKUP_FILE="%shared_path%/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz"
            echo "Creating database backup..."
            
            # Test database connection first
            if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null; then
                mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
                    --single-transaction --routines --triggers --add-drop-table \
                    "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"
                
                if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
                    BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
                    echo "✅ Database backed up: $BACKUP_FILE ($BACKUP_SIZE)"
                else
                    echo "⚠️ Database backup failed or empty"
                    rm -f "$BACKUP_FILE" 2>/dev/null
                fi
            else
                echo "⚠️ Cannot connect to database - skipping backup"
            fi
        else
            echo "⚠️ Incomplete database configuration in .env"
        fi
    else
        echo "⚠️ Failed to parse .env file"
    fi
else
    echo "ℹ️ No .env file yet (first deployment)"
fi

# A05: Enter Maintenance Mode
echo "=== Enter Maintenance Mode ==="
touch %path%/maintenance_mode_active

if [ -f "%current_path%/artisan" ]; then
    cd %current_path%
    # Try Laravel maintenance mode, but don't fail if it doesn't work
    php artisan down --render="errors::503" 2>/dev/null || echo "⚠️ Artisan maintenance failed (continuing)"
    echo "✅ Maintenance mode activated"
else
    echo "ℹ️ No current release (first deployment)"
fi

echo "✅ Phase A completed successfully"