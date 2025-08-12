# Backup Management

**Purpose:** Comprehensive backup strategy, automated backup systems, and data recovery procedures for Laravel applications.

**Use Case:** Data protection, disaster recovery, and business continuity planning

---

## **Analysis Source**

Based on **Laravel - Final Guides/V2/phase4_deployment_guide.md** backup systems and data persistence strategies.

---

## **1. Backup Strategy Overview**

### **1.1: Multi-Layer Backup Architecture**

**Three-Tier Backup System:**

```bash
cat > ~/backup_strategy.md << 'EOF'
# Comprehensive Backup Strategy

## Backup Layers

### Layer 1: Real-Time Protection
- **Shared persistent directories** (user uploads, sessions)
- **Symlinked storage** (Laravel storage directory)
- **Environment configurations** (.env files)

### Layer 2: Daily Automated Backups
- **Database snapshots** (compressed SQL dumps)
- **Application code** (current release state)
- **User-generated content** (uploads, files)
- **System configurations** (environment files)

### Layer 3: Long-Term Archival
- **Weekly consolidated backups**
- **Monthly full system snapshots**
- **Off-site backup synchronization** (optional)

## Recovery Time Objectives (RTO)
- **Emergency Rollback:** < 5 minutes
- **Database Recovery:** < 15 minutes
- **Full Application Recovery:** < 30 minutes
- **Complete Disaster Recovery:** < 60 minutes

## Recovery Point Objectives (RPO)
- **User Data:** < 24 hours (daily backups)
- **Application State:** Real-time (persistent storage)
- **System Configuration:** < 24 hours (daily backups)
EOF

echo "✅ Backup strategy documented"
```

---

## **2. Advanced Backup System**

### **2.1: Create Comprehensive Backup Manager**

```bash
cat > ~/backup_manager.sh << 'EOF'
#!/bin/bash

echo "💾 Advanced Backup Manager - $(date)"
echo "==================================="

# Configuration
DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"
BACKUP_ROOT="$HOME/backups"
DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"

# Backup types
BACKUP_TYPE="${1:-daily}"  # daily, weekly, monthly, manual
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory structure
mkdir -p "$BACKUP_ROOT"/{daily,weekly,monthly,manual}/{database,application,storage,configs}

case $BACKUP_TYPE in
    "daily")
        BACKUP_PATH="$BACKUP_ROOT/daily"
        RETENTION_DAYS=7
        ;;
    "weekly")
        BACKUP_PATH="$BACKUP_ROOT/weekly"
        RETENTION_DAYS=28
        ;;
    "monthly")
        BACKUP_PATH="$BACKUP_ROOT/monthly"
        RETENTION_DAYS=365
        ;;
    "manual")
        BACKUP_PATH="$BACKUP_ROOT/manual"
        RETENTION_DAYS=30
        ;;
    *)
        echo "❌ Invalid backup type. Use: daily, weekly, monthly, manual"
        exit 1
        ;;
esac

echo "📋 Backup Configuration:"
echo "Type: $BACKUP_TYPE"
echo "Path: $BACKUP_PATH"
echo "Retention: $RETENTION_DAYS days"

# Function: Database backup with verification
backup_database() {
    echo ""
    echo "📊 Database Backup:"
    echo "=================="

    local db_backup_file="$BACKUP_PATH/database/db_${BACKUP_DATE}.sql.gz"

    # Create database dump with compression
    echo "🗃️ Creating database dump..."
    if mysqldump -u "$DB_USER" -p"$DB_PASS" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --hex-blob \
        "$DB_NAME" | gzip > "$db_backup_file"; then

        # Verify backup integrity
        echo "🔍 Verifying backup integrity..."
        if gunzip -t "$db_backup_file"; then
            DB_SIZE=$(du -sh "$db_backup_file" | cut -f1)
            echo "✅ Database backup created: $(basename $db_backup_file) ($DB_SIZE)"

            # Test backup can be read
            TABLE_COUNT=$(gunzip -c "$db_backup_file" | grep -c "CREATE TABLE")
            echo "📋 Tables backed up: $TABLE_COUNT"

            return 0
        else
            echo "❌ Database backup verification failed"
            rm -f "$db_backup_file"
            return 1
        fi
    else
        echo "❌ Database backup failed"
        return 1
    fi
}

# Function: Application backup
backup_application() {
    echo ""
    echo "📦 Application Backup:"
    echo "====================="

    local app_backup_file="$BACKUP_PATH/application/app_${BACKUP_DATE}.tar.gz"

    # Backup current application state
    echo "📁 Creating application archive..."
    if tar -czf "$app_backup_file" \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        --exclude='node_modules' \
        --exclude='.git' \
        -C "$DOMAIN_PATH" current/; then

        APP_SIZE=$(du -sh "$app_backup_file" | cut -f1)
        echo "✅ Application backup created: $(basename $app_backup_file) ($APP_SIZE)"

        # Verify archive integrity
        if tar -tzf "$app_backup_file" >/dev/null 2>&1; then
            FILE_COUNT=$(tar -tzf "$app_backup_file" | wc -l)
            echo "📋 Files backed up: $FILE_COUNT"
            return 0
        else
            echo "❌ Application backup verification failed"
            rm -f "$app_backup_file"
            return 1
        fi
    else
        echo "❌ Application backup failed"
        return 1
    fi
}

# Function: Storage backup (user uploads, persistent data)
backup_storage() {
    echo ""
    echo "🗂️ Storage Backup:"
    echo "=================="

    local storage_backup_file="$BACKUP_PATH/storage/storage_${BACKUP_DATE}.tar.gz"

    if [ -d "$DOMAIN_PATH/shared" ]; then
        echo "📁 Creating storage archive..."
        if tar -czf "$storage_backup_file" \
            --exclude='framework/cache/*' \
            --exclude='framework/sessions/*' \
            --exclude='framework/views/*' \
            --exclude='logs/*.log' \
            -C "$DOMAIN_PATH" shared/; then

            STORAGE_SIZE=$(du -sh "$storage_backup_file" | cut -f1)
            echo "✅ Storage backup created: $(basename $storage_backup_file) ($STORAGE_SIZE)"

            # Verify archive integrity
            if tar -tzf "$storage_backup_file" >/dev/null 2>&1; then
                FILE_COUNT=$(tar -tzf "$storage_backup_file" | wc -l)
                echo "📋 Files backed up: $FILE_COUNT"
                return 0
            else
                echo "❌ Storage backup verification failed"
                rm -f "$storage_backup_file"
                return 1
            fi
        else
            echo "❌ Storage backup failed"
            return 1
        fi
    else
        echo "⚠️ Shared storage directory not found"
        return 1
    fi
}

# Function: Configuration backup
backup_configs() {
    echo ""
    echo "⚙️ Configuration Backup:"
    echo "======================="

    local config_backup_file="$BACKUP_PATH/configs/configs_${BACKUP_DATE}.tar.gz"

    # Create temporary directory for configs
    local temp_config_dir=$(mktemp -d)

    # Collect configuration files
    echo "📋 Collecting configuration files..."

    # Environment files
    if [ -f "$DOMAIN_PATH/shared/.env" ]; then
        cp "$DOMAIN_PATH/shared/.env" "$temp_config_dir/production.env"
    fi

    # Backup scripts
    cp "$HOME"/*.sh "$temp_config_dir/" 2>/dev/null || true

    # Crontab
    crontab -l > "$temp_config_dir/crontab.txt" 2>/dev/null || echo "No crontab" > "$temp_config_dir/crontab.txt"

    # SSH config (if exists)
    if [ -f "$HOME/.ssh/config" ]; then
        cp "$HOME/.ssh/config" "$temp_config_dir/ssh_config"
    fi

    # Create archive
    if tar -czf "$config_backup_file" -C "$temp_config_dir" .; then
        CONFIG_SIZE=$(du -sh "$config_backup_file" | cut -f1)
        echo "✅ Configuration backup created: $(basename $config_backup_file) ($CONFIG_SIZE)"

        # Cleanup temp directory
        rm -rf "$temp_config_dir"
        return 0
    else
        echo "❌ Configuration backup failed"
        rm -rf "$temp_config_dir"
        return 1
    fi
}

# Function: Cleanup old backups
cleanup_old_backups() {
    echo ""
    echo "🧹 Backup Cleanup:"
    echo "=================="

    echo "🗑️ Removing backups older than $RETENTION_DAYS days..."

    # Count before cleanup
    BEFORE_COUNT=$(find "$BACKUP_PATH" -type f -name "*.gz" | wc -l)
    BEFORE_SIZE=$(du -sh "$BACKUP_PATH" 2>/dev/null | cut -f1)

    # Remove old backups
    find "$BACKUP_PATH" -type f -name "*.gz" -mtime +$RETENTION_DAYS -delete

    # Count after cleanup
    AFTER_COUNT=$(find "$BACKUP_PATH" -type f -name "*.gz" | wc -l)
    AFTER_SIZE=$(du -sh "$BACKUP_PATH" 2>/dev/null | cut -f1)

    REMOVED_COUNT=$((BEFORE_COUNT - AFTER_COUNT))

    echo "📊 Cleanup Results:"
    echo "   Files removed: $REMOVED_COUNT"
    echo "   Files remaining: $AFTER_COUNT"
    echo "   Size before: $BEFORE_SIZE"
    echo "   Size after: $AFTER_SIZE"
}

# Function: Backup summary
backup_summary() {
    echo ""
    echo "📊 Backup Summary:"
    echo "=================="

    echo "🗂️ Backup Contents:"
    find "$BACKUP_PATH" -name "*${BACKUP_DATE}*" -type f -exec basename {} \; | sort

    echo ""
    echo "💾 Storage Usage:"
    du -sh "$BACKUP_PATH"/* 2>/dev/null | sort -hr

    echo ""
    echo "📅 Recent Backups:"
    find "$BACKUP_ROOT" -name "*.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -10 | while read timestamp filepath; do
        date -d "@$timestamp" "+%Y-%m-%d %H:%M" | tr -d '\n'
        echo " - $(basename $filepath)"
    done
}

# Main execution
echo "🚀 Starting $BACKUP_TYPE backup process..."

# Initialize counters
BACKUP_SUCCESS=0
BACKUP_TOTAL=0

# Execute backup components
echo ""
echo "📋 Backup Components:"

# Database backup
((BACKUP_TOTAL++))
if backup_database; then
    ((BACKUP_SUCCESS++))
fi

# Application backup
((BACKUP_TOTAL++))
if backup_application; then
    ((BACKUP_SUCCESS++))
fi

# Storage backup
((BACKUP_TOTAL++))
if backup_storage; then
    ((BACKUP_SUCCESS++))
fi

# Configuration backup
((BACKUP_TOTAL++))
if backup_configs; then
    ((BACKUP_SUCCESS++))
fi

# Cleanup old backups
cleanup_old_backups

# Generate summary
backup_summary

# Final results
echo ""
echo "✅ Backup Process Complete:"
echo "=========================="
echo "Success Rate: $BACKUP_SUCCESS/$BACKUP_TOTAL components"

if [ $BACKUP_SUCCESS -eq $BACKUP_TOTAL ]; then
    echo "🎉 All backup components successful"
    LOG_STATUS="SUCCESS"
else
    echo "⚠️ Some backup components failed"
    LOG_STATUS="PARTIAL"
fi

# Log backup completion
echo "$(date): $BACKUP_TYPE backup $LOG_STATUS - $BACKUP_SUCCESS/$BACKUP_TOTAL components" >> "$HOME/backup_manager.log"

echo ""
echo "📍 Backup Location: $BACKUP_PATH"
echo "📋 Log File: $HOME/backup_manager.log"
EOF

chmod +x ~/backup_manager.sh

echo "✅ Advanced backup manager created"
```

### **2.2: Create Backup Scheduling System**

```bash
# Remove old basic backup from cron
crontab -l | grep -v "backup_script.sh" | crontab -

# Add advanced backup scheduling
echo "⏰ Setting up advanced backup schedule..."

# Daily backups (2 AM)
(crontab -l 2>/dev/null; echo "0 2 * * * $HOME/backup_manager.sh daily >> $HOME/backup_daily.log 2>&1") | crontab -

# Weekly backups (Sunday 1 AM)
(crontab -l 2>/dev/null; echo "0 1 * * 0 $HOME/backup_manager.sh weekly >> $HOME/backup_weekly.log 2>&1") | crontab -

# Monthly backups (1st of month, 12 AM)
(crontab -l 2>/dev/null; echo "0 0 1 * * $HOME/backup_manager.sh monthly >> $HOME/backup_monthly.log 2>&1") | crontab -

echo "✅ Advanced backup schedule configured"

# Verify cron jobs
echo ""
echo "📋 Scheduled Backup Jobs:"
crontab -l | grep backup_manager
```

---

## **3. Backup Verification System**

### **3.1: Create Backup Verification Script**

```bash
cat > ~/backup_verification.sh << 'EOF'
#!/bin/bash

echo "🔍 Backup Verification System - $(date)"
echo "======================================="

BACKUP_ROOT="$HOME/backups"
VERIFICATION_LOG="$HOME/backup_verification.log"

# Function: Verify database backup
verify_database_backup() {
    local backup_file="$1"
    echo -n "📊 Database backup $(basename $backup_file)... "

    # Check file exists and not empty
    if [ ! -f "$backup_file" ] || [ ! -s "$backup_file" ]; then
        echo "❌ FAIL (missing/empty)"
        return 1
    fi

    # Check gzip integrity
    if ! gunzip -t "$backup_file" 2>/dev/null; then
        echo "❌ FAIL (corrupt gzip)"
        return 1
    fi

    # Check SQL content
    local table_count=$(gunzip -c "$backup_file" 2>/dev/null | grep -c "CREATE TABLE")
    if [ "$table_count" -lt 1 ]; then
        echo "❌ FAIL (no tables)"
        return 1
    fi

    echo "✅ PASS ($table_count tables)"
    return 0
}

# Function: Verify application backup
verify_application_backup() {
    local backup_file="$1"
    echo -n "📦 Application backup $(basename $backup_file)... "

    # Check file exists and not empty
    if [ ! -f "$backup_file" ] || [ ! -s "$backup_file" ]; then
        echo "❌ FAIL (missing/empty)"
        return 1
    fi

    # Check tar integrity
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ FAIL (corrupt tar)"
        return 1
    fi

    # Check for essential Laravel files
    local artisan_found=$(tar -tzf "$backup_file" 2>/dev/null | grep -c "artisan$")
    local composer_found=$(tar -tzf "$backup_file" 2>/dev/null | grep -c "composer.json$")

    if [ "$artisan_found" -lt 1 ] || [ "$composer_found" -lt 1 ]; then
        echo "❌ FAIL (missing Laravel files)"
        return 1
    fi

    local file_count=$(tar -tzf "$backup_file" 2>/dev/null | wc -l)
    echo "✅ PASS ($file_count files)"
    return 0
}

# Function: Verify storage backup
verify_storage_backup() {
    local backup_file="$1"
    echo -n "🗂️ Storage backup $(basename $backup_file)... "

    # Check file exists
    if [ ! -f "$backup_file" ]; then
        echo "❌ FAIL (missing)"
        return 1
    fi

    # Empty storage backup is acceptable
    if [ ! -s "$backup_file" ]; then
        echo "✅ PASS (empty)"
        return 0
    fi

    # Check tar integrity
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ FAIL (corrupt tar)"
        return 1
    fi

    local file_count=$(tar -tzf "$backup_file" 2>/dev/null | wc -l)
    echo "✅ PASS ($file_count files)"
    return 0
}

# Function: Verify configuration backup
verify_config_backup() {
    local backup_file="$1"
    echo -n "⚙️ Configuration backup $(basename $backup_file)... "

    # Check file exists and not empty
    if [ ! -f "$backup_file" ] || [ ! -s "$backup_file" ]; then
        echo "❌ FAIL (missing/empty)"
        return 1
    fi

    # Check tar integrity
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ FAIL (corrupt tar)"
        return 1
    fi

    local file_count=$(tar -tzf "$backup_file" 2>/dev/null | wc -l)
    echo "✅ PASS ($file_count files)"
    return 0
}

# Main verification process
echo "🔍 Starting backup verification process..."

VERIFICATION_PASSED=0
VERIFICATION_TOTAL=0

# Verify each backup type
for backup_type in daily weekly monthly manual; do
    backup_path="$BACKUP_ROOT/$backup_type"

    if [ ! -d "$backup_path" ]; then
        continue
    fi

    echo ""
    echo "📁 Verifying $backup_type backups:"
    echo "=================================="

    # Find recent backups (last 7 days)
    recent_backups=$(find "$backup_path" -name "*.gz" -mtime -7 2>/dev/null)

    if [ -z "$recent_backups" ]; then
        echo "⚠️ No recent $backup_type backups found"
        continue
    fi

    # Group by date
    for date_pattern in $(echo "$recent_backups" | grep -o '[0-9]\{8\}_[0-9]\{6\}' | sort -u); do
        echo ""
        echo "📅 Backup set: $date_pattern"

        # Find all components for this date
        db_backup=$(find "$backup_path/database" -name "*${date_pattern}*.gz" 2>/dev/null | head -1)
        app_backup=$(find "$backup_path/application" -name "*${date_pattern}*.gz" 2>/dev/null | head -1)
        storage_backup=$(find "$backup_path/storage" -name "*${date_pattern}*.gz" 2>/dev/null | head -1)
        config_backup=$(find "$backup_path/configs" -name "*${date_pattern}*.gz" 2>/dev/null | head -1)

        # Verify each component
        if [ -n "$db_backup" ]; then
            ((VERIFICATION_TOTAL++))
            if verify_database_backup "$db_backup"; then
                ((VERIFICATION_PASSED++))
            fi
        fi

        if [ -n "$app_backup" ]; then
            ((VERIFICATION_TOTAL++))
            if verify_application_backup "$app_backup"; then
                ((VERIFICATION_PASSED++))
            fi
        fi

        if [ -n "$storage_backup" ]; then
            ((VERIFICATION_TOTAL++))
            if verify_storage_backup "$storage_backup"; then
                ((VERIFICATION_PASSED++))
            fi
        fi

        if [ -n "$config_backup" ]; then
            ((VERIFICATION_TOTAL++))
            if verify_config_backup "$config_backup"; then
                ((VERIFICATION_PASSED++))
            fi
        fi
    done
done

# Summary
echo ""
echo "📊 Verification Summary:"
echo "======================="
echo "Total backups verified: $VERIFICATION_TOTAL"
echo "Verification passed: $VERIFICATION_PASSED"
echo "Verification failed: $((VERIFICATION_TOTAL - VERIFICATION_PASSED))"

PASS_RATE=$((VERIFICATION_TOTAL > 0 ? VERIFICATION_PASSED * 100 / VERIFICATION_TOTAL : 0))
echo "Pass rate: $PASS_RATE%"

if [ $PASS_RATE -ge 95 ]; then
    echo "🎉 BACKUP INTEGRITY: EXCELLENT"
    STATUS="EXCELLENT"
elif [ $PASS_RATE -ge 80 ]; then
    echo "✅ BACKUP INTEGRITY: GOOD"
    STATUS="GOOD"
elif [ $PASS_RATE -ge 60 ]; then
    echo "⚠️ BACKUP INTEGRITY: NEEDS ATTENTION"
    STATUS="WARNING"
else
    echo "❌ BACKUP INTEGRITY: CRITICAL ISSUES"
    STATUS="CRITICAL"
fi

# Storage summary
echo ""
echo "💾 Backup Storage Summary:"
echo "========================="
du -sh "$BACKUP_ROOT"/* 2>/dev/null | sort -hr

# Log verification results
echo "$(date): Backup verification - $VERIFICATION_PASSED/$VERIFICATION_TOTAL passed ($PASS_RATE%) - $STATUS" >> "$VERIFICATION_LOG"

echo ""
echo "✅ Backup verification completed"
echo "📋 Log: $VERIFICATION_LOG"
EOF

chmod +x ~/backup_verification.sh

echo "✅ Backup verification system created"
```

---

## **4. Backup Recovery System**

### **4.1: Create Selective Recovery Script**

```bash
cat > ~/backup_recovery.sh << 'EOF'
#!/bin/bash

echo "🔄 Backup Recovery System - $(date)"
echo "==================================="

BACKUP_ROOT="$HOME/backups"
DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"
DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"

# Recovery options
RECOVERY_TYPE="$1"  # database, application, storage, configs, full
BACKUP_DATE="$2"    # YYYYMMDD_HHMMSS or "latest"

# Function: Show usage
show_usage() {
    echo "Usage: $0 <recovery_type> <backup_date>"
    echo ""
    echo "Recovery Types:"
    echo "  database    - Restore database only"
    echo "  application - Restore application files only"
    echo "  storage     - Restore user uploads/storage only"
    echo "  configs     - Restore configuration files only"
    echo "  full        - Complete system restoration"
    echo ""
    echo "Backup Date:"
    echo "  latest      - Use most recent backup"
    echo "  YYYYMMDD_HHMMSS - Specific backup timestamp"
    echo ""
    echo "Examples:"
    echo "  $0 database latest"
    echo "  $0 full 20240315_143022"
    exit 1
}

# Validate parameters
if [ -z "$RECOVERY_TYPE" ] || [ -z "$BACKUP_DATE" ]; then
    show_usage
fi

# Function: Find backup file
find_backup_file() {
    local component="$1"
    local date_pattern="$2"

    if [ "$date_pattern" = "latest" ]; then
        find "$BACKUP_ROOT" -path "*/$component/*" -name "*.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-
    else
        find "$BACKUP_ROOT" -path "*/$component/*" -name "*${date_pattern}*.gz" -type f 2>/dev/null | head -1
    fi
}

# Function: Recover database
recover_database() {
    local backup_file="$1"
    echo ""
    echo "📊 Database Recovery:"
    echo "===================="

    if [ ! -f "$backup_file" ]; then
        echo "❌ Database backup not found: $backup_file"
        return 1
    fi

    echo "📥 Database backup: $(basename $backup_file)"
    echo "📅 Backup date: $(stat -c %y "$backup_file")"

    # Verify backup integrity
    echo "🔍 Verifying backup integrity..."
    if ! gunzip -t "$backup_file"; then
        echo "❌ Backup file is corrupted"
        return 1
    fi

    # Confirmation
    echo ""
    echo "⚠️ WARNING: This will replace the current database"
    read -p "🔴 Continue with database recovery? (type 'RECOVER' to confirm): " confirm

    if [ "$confirm" != "RECOVER" ]; then
        echo "❌ Database recovery cancelled"
        return 1
    fi

    # Create backup of current database
    echo "💾 Creating backup of current database..."
    current_backup="$HOME/backups/manual/database/pre_recovery_$(date +%Y%m%d_%H%M%S).sql.gz"
    mkdir -p "$(dirname $current_backup)"
    mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$current_backup"
    echo "✅ Current database backed up to: $(basename $current_backup)"

    # Restore database
    echo "🔄 Restoring database from backup..."
    if gunzip -c "$backup_file" | mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"; then
        echo "✅ Database recovery completed successfully"
        return 0
    else
        echo "❌ Database recovery failed"
        return 1
    fi
}

# Function: Recover application
recover_application() {
    local backup_file="$1"
    echo ""
    echo "📦 Application Recovery:"
    echo "======================="

    if [ ! -f "$backup_file" ]; then
        echo "❌ Application backup not found: $backup_file"
        return 1
    fi

    echo "📥 Application backup: $(basename $backup_file)"
    echo "📅 Backup date: $(stat -c %y "$backup_file")"

    # Verify backup integrity
    echo "🔍 Verifying backup integrity..."
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ Backup file is corrupted"
        return 1
    fi

    # Confirmation
    echo ""
    echo "⚠️ WARNING: This will replace the current application"
    read -p "🔴 Continue with application recovery? (type 'RECOVER' to confirm): " confirm

    if [ "$confirm" != "RECOVER" ]; then
        echo "❌ Application recovery cancelled"
        return 1
    fi

    # Create new release directory
    NEW_RELEASE="recovery-$(date +%Y%m%d_%H%M%S)"
    RELEASE_PATH="$DOMAIN_PATH/releases/$NEW_RELEASE"

    echo "🏗️ Creating new release: $NEW_RELEASE"
    mkdir -p "$RELEASE_PATH"

    # Extract backup
    echo "📦 Extracting application backup..."
    if tar -xzf "$backup_file" -C "$RELEASE_PATH" --strip-components=1; then
        echo "✅ Application files extracted"
    else
        echo "❌ Application extraction failed"
        rm -rf "$RELEASE_PATH"
        return 1
    fi

    # Link shared directories
    echo "🔗 Linking shared directories..."
    rm -rf "$RELEASE_PATH/storage"
    ln -sf "$DOMAIN_PATH/shared/storage" "$RELEASE_PATH/storage"
    ln -sf "$DOMAIN_PATH/shared/.env" "$RELEASE_PATH/.env"

    # Update current symlink
    echo "🔄 Updating current symlink..."
    ln -sfn "$RELEASE_PATH" "$DOMAIN_PATH/current"

    # Clear caches
    echo "🗑️ Clearing caches..."
    cd "$DOMAIN_PATH/current"
    php artisan cache:clear 2>/dev/null
    php artisan config:clear 2>/dev/null
    php artisan route:clear 2>/dev/null
    php artisan view:clear 2>/dev/null

    echo "✅ Application recovery completed successfully"
    return 0
}

# Function: Recover storage
recover_storage() {
    local backup_file="$1"
    echo ""
    echo "🗂️ Storage Recovery:"
    echo "==================="

    if [ ! -f "$backup_file" ]; then
        echo "❌ Storage backup not found: $backup_file"
        return 1
    fi

    echo "📥 Storage backup: $(basename $backup_file)"
    echo "📅 Backup date: $(stat -c %y "$backup_file")"

    # Verify backup integrity
    echo "🔍 Verifying backup integrity..."
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ Backup file is corrupted"
        return 1
    fi

    # Confirmation
    echo ""
    echo "⚠️ WARNING: This will replace user uploads and storage"
    read -p "🔴 Continue with storage recovery? (type 'RECOVER' to confirm): " confirm

    if [ "$confirm" != "RECOVER" ]; then
        echo "❌ Storage recovery cancelled"
        return 1
    fi

    # Backup current storage
    echo "💾 Creating backup of current storage..."
    current_backup="$HOME/backups/manual/storage/pre_recovery_$(date +%Y%m%d_%H%M%S).tar.gz"
    mkdir -p "$(dirname $current_backup)"
    tar -czf "$current_backup" -C "$DOMAIN_PATH" shared/
    echo "✅ Current storage backed up to: $(basename $current_backup)"

    # Restore storage
    echo "🔄 Restoring storage from backup..."
    if tar -xzf "$backup_file" -C "$DOMAIN_PATH" --overwrite; then
        echo "✅ Storage recovery completed successfully"
        return 0
    else
        echo "❌ Storage recovery failed"
        return 1
    fi
}

# Function: Recover configurations
recover_configs() {
    local backup_file="$1"
    echo ""
    echo "⚙️ Configuration Recovery:"
    echo "========================="

    if [ ! -f "$backup_file" ]; then
        echo "❌ Configuration backup not found: $backup_file"
        return 1
    fi

    echo "📥 Configuration backup: $(basename $backup_file)"
    echo "📅 Backup date: $(stat -c %y "$backup_file")"

    # Verify backup integrity
    echo "🔍 Verifying backup integrity..."
    if ! tar -tzf "$backup_file" >/dev/null 2>&1; then
        echo "❌ Backup file is corrupted"
        return 1
    fi

    # Create temporary extraction directory
    TEMP_DIR=$(mktemp -d)

    # Extract to temporary directory
    echo "📦 Extracting configuration backup..."
    if tar -xzf "$backup_file" -C "$TEMP_DIR"; then
        echo "✅ Configuration files extracted"
    else
        echo "❌ Configuration extraction failed"
        rm -rf "$TEMP_DIR"
        return 1
    fi

    # Show available configurations
    echo ""
    echo "📋 Available configurations:"
    ls -la "$TEMP_DIR"

    # Selective restoration
    echo ""
    echo "Select configurations to restore:"
    echo "1. Environment files (.env)"
    echo "2. Backup scripts"
    echo "3. Crontab"
    echo "4. SSH configuration"
    echo "5. All configurations"
    echo "6. Cancel"

    read -p "Choose option (1-6): " choice

    case $choice in
        1)
            if [ -f "$TEMP_DIR/production.env" ]; then
                cp "$TEMP_DIR/production.env" "$DOMAIN_PATH/shared/.env"
                echo "✅ Environment file restored"
            fi
            ;;
        2)
            cp "$TEMP_DIR"/*.sh "$HOME/" 2>/dev/null && chmod +x "$HOME"/*.sh
            echo "✅ Backup scripts restored"
            ;;
        3)
            if [ -f "$TEMP_DIR/crontab.txt" ]; then
                crontab "$TEMP_DIR/crontab.txt"
                echo "✅ Crontab restored"
            fi
            ;;
        4)
            if [ -f "$TEMP_DIR/ssh_config" ]; then
                mkdir -p "$HOME/.ssh"
                cp "$TEMP_DIR/ssh_config" "$HOME/.ssh/config"
                echo "✅ SSH configuration restored"
            fi
            ;;
        5)
            # Restore all configurations
            [ -f "$TEMP_DIR/production.env" ] && cp "$TEMP_DIR/production.env" "$DOMAIN_PATH/shared/.env"
            cp "$TEMP_DIR"/*.sh "$HOME/" 2>/dev/null && chmod +x "$HOME"/*.sh
            [ -f "$TEMP_DIR/crontab.txt" ] && crontab "$TEMP_DIR/crontab.txt"
            if [ -f "$TEMP_DIR/ssh_config" ]; then
                mkdir -p "$HOME/.ssh"
                cp "$TEMP_DIR/ssh_config" "$HOME/.ssh/config"
            fi
            echo "✅ All configurations restored"
            ;;
        6)
            echo "❌ Configuration recovery cancelled"
            ;;
        *)
            echo "❌ Invalid choice"
            ;;
    esac

    # Cleanup
    rm -rf "$TEMP_DIR"
    return 0
}

# Main recovery process
echo "🔍 Backup Recovery Process"
echo "Recovery Type: $RECOVERY_TYPE"
echo "Backup Date: $BACKUP_DATE"

case $RECOVERY_TYPE in
    "database")
        DB_BACKUP=$(find_backup_file "database" "$BACKUP_DATE")
        if [ -n "$DB_BACKUP" ]; then
            recover_database "$DB_BACKUP"
        else
            echo "❌ No database backup found for date: $BACKUP_DATE"
            exit 1
        fi
        ;;

    "application")
        APP_BACKUP=$(find_backup_file "application" "$BACKUP_DATE")
        if [ -n "$APP_BACKUP" ]; then
            recover_application "$APP_BACKUP"
        else
            echo "❌ No application backup found for date: $BACKUP_DATE"
            exit 1
        fi
        ;;

    "storage")
        STORAGE_BACKUP=$(find_backup_file "storage" "$BACKUP_DATE")
        if [ -n "$STORAGE_BACKUP" ]; then
            recover_storage "$STORAGE_BACKUP"
        else
            echo "❌ No storage backup found for date: $BACKUP_DATE"
            exit 1
        fi
        ;;

    "configs")
        CONFIG_BACKUP=$(find_backup_file "configs" "$BACKUP_DATE")
        if [ -n "$CONFIG_BACKUP" ]; then
            recover_configs "$CONFIG_BACKUP"
        else
            echo "❌ No configuration backup found for date: $BACKUP_DATE"
            exit 1
        fi
        ;;

    "full")
        echo ""
        echo "🚨 FULL SYSTEM RECOVERY"
        echo "======================"
        echo "This will perform complete system restoration:"
        echo "1. Database restoration"
        echo "2. Application restoration"
        echo "3. Storage restoration"
        echo "4. Configuration restoration"
        echo ""
        echo "⚠️ WARNING: This will replace ALL current data"
        read -p "🔴 Continue with full recovery? (type 'FULL-RECOVER' to confirm): " confirm

        if [ "$confirm" != "FULL-RECOVER" ]; then
            echo "❌ Full recovery cancelled"
            exit 1
        fi

        # Find all backup components
        DB_BACKUP=$(find_backup_file "database" "$BACKUP_DATE")
        APP_BACKUP=$(find_backup_file "application" "$BACKUP_DATE")
        STORAGE_BACKUP=$(find_backup_file "storage" "$BACKUP_DATE")
        CONFIG_BACKUP=$(find_backup_file "configs" "$BACKUP_DATE")

        RECOVERY_SUCCESS=0
        RECOVERY_TOTAL=0

        # Recover each component
        if [ -n "$DB_BACKUP" ]; then
            ((RECOVERY_TOTAL++))
            if recover_database "$DB_BACKUP"; then
                ((RECOVERY_SUCCESS++))
            fi
        fi

        if [ -n "$APP_BACKUP" ]; then
            ((RECOVERY_TOTAL++))
            if recover_application "$APP_BACKUP"; then
                ((RECOVERY_SUCCESS++))
            fi
        fi

        if [ -n "$STORAGE_BACKUP" ]; then
            ((RECOVERY_TOTAL++))
            if recover_storage "$STORAGE_BACKUP"; then
                ((RECOVERY_SUCCESS++))
            fi
        fi

        if [ -n "$CONFIG_BACKUP" ]; then
            ((RECOVERY_TOTAL++))
            if recover_configs "$CONFIG_BACKUP"; then
                ((RECOVERY_SUCCESS++))
            fi
        fi

        echo ""
        echo "📊 Full Recovery Results:"
        echo "========================"
        echo "Components recovered: $RECOVERY_SUCCESS/$RECOVERY_TOTAL"

        if [ $RECOVERY_SUCCESS -eq $RECOVERY_TOTAL ] && [ $RECOVERY_TOTAL -gt 0 ]; then
            echo "🎉 Full recovery completed successfully"
        else
            echo "⚠️ Full recovery completed with issues"
        fi
        ;;

    *)
        echo "❌ Invalid recovery type: $RECOVERY_TYPE"
        show_usage
        ;;
esac

# Log recovery action
echo "$(date): Recovery $RECOVERY_TYPE ($BACKUP_DATE) completed" >> "$HOME/backup_recovery.log"

echo ""
echo "✅ Recovery process completed"
echo "📋 Log: $HOME/backup_recovery.log"
EOF

chmod +x ~/backup_recovery.sh

echo "✅ Backup recovery system created"
```

---

## **5. Usage Instructions**

### **Daily Operations:**

```bash
# Manual backup (any type)
bash ~/backup_manager.sh daily
bash ~/backup_manager.sh weekly
bash ~/backup_manager.sh monthly
bash ~/backup_manager.sh manual

# Verify backup integrity
bash ~/backup_verification.sh

# Check backup status
ls -la ~/backups/daily/
ls -la ~/backups/weekly/
ls -la ~/backups/monthly/
```

### **Recovery Operations:**

```bash
# Selective recovery
bash ~/backup_recovery.sh database latest
bash ~/backup_recovery.sh application 20240315_143022
bash ~/backup_recovery.sh storage latest
bash ~/backup_recovery.sh configs latest

# Full system recovery
bash ~/backup_recovery.sh full latest
```

### **Monitoring:**

```bash
# Check backup logs
tail -50 ~/backup_daily.log
tail -50 ~/backup_verification.log
tail -50 ~/backup_recovery.log

# Check backup storage usage
du -sh ~/backups/*

# List recent backups
find ~/backups -name "*.gz" -mtime -7 | sort
```

---

## **6. Configuration Checklist**

- [ ] Advanced backup manager configured
- [ ] Backup scheduling automated (daily, weekly, monthly)
- [ ] Backup verification system operational
- [ ] Selective recovery system ready
- [ ] Full disaster recovery tested
- [ ] Database credentials updated
- [ ] Domain paths configured
- [ ] Backup retention policies set
- [ ] Storage monitoring active
- [ ] Recovery procedures documented

---

## **Related Files**

- **Server Monitoring:** [Server_Monitoring.md](Server_Monitoring.md)
- **Performance Monitoring:** [Performance_Monitoring.md](Performance_Monitoring.md)
- **Emergency Procedures:** [Emergency_Procedures.md](Emergency_Procedures.md)

---

## **Backup Quick Reference**

### **Create Backup:**

```bash
bash ~/backup_manager.sh daily
```

### **Verify Backups:**

```bash
bash ~/backup_verification.sh
```

### **Recover Database:**

```bash
bash ~/backup_recovery.sh database latest
```

### **Full Recovery:**

```bash
bash ~/backup_recovery.sh full latest
```
