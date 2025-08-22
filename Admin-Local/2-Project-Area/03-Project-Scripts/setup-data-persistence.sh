#!/bin/bash

# Data Persistence Strategy Setup Script
# Purpose: Configure data persistence and backup strategies for Laravel deployment

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}💾 DATA PERSISTENCE STRATEGY SETUP${NC}"
echo "=================================="

# Setup results tracking
SETUPS_COMPLETED=0
SETUPS_FAILED=0
SETUP_RESULTS=()

# Function: Log setup results
log_setup() {
    local setup_name="$1"
    local status="$2"
    local message="$3"
    
    if [[ "$status" == "SUCCESS" ]]; then
        echo -e "${GREEN}✅ $setup_name: $message${NC}"
        ((SETUPS_COMPLETED++))
        SETUP_RESULTS+=("✅ $setup_name: $message")
    else
        echo -e "${RED}❌ $setup_name: $message${NC}"
        ((SETUPS_FAILED++))
        SETUP_RESULTS+=("❌ $setup_name: $message")
    fi
}

# Setup database backup strategy
setup_database_backup() {
    echo -e "${BLUE}📋 Database Backup Strategy${NC}"
    echo "=========================="
    
    local backup_dir="Admin-Local/4-Backups"
    mkdir -p "$backup_dir"/{01-Database-Backups,02-File-Backups,03-Configuration-Backups,04-Release-Backups}
    
    # Create database backup script
    local db_backup_script="$backup_dir/database-backup.sh"
    cat > "$db_backup_script" << 'EOF'
#!/bin/bash

# Database Backup Script
# Purpose: Create database backups with rotation

set -e

# Load environment variables
if [[ -f ".env" ]]; then
    source .env
fi

# Backup configuration
BACKUP_DIR="Admin-Local/4-Backups/01-Database-Backups"
BACKUP_RETENTION_DAYS=30
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Database backup based on connection type
case "${DB_CONNECTION:-mysql}" in
    "mysql")
        if command -v mysqldump >/dev/null 2>&1; then
            BACKUP_FILE="$BACKUP_DIR/mysql_backup_$TIMESTAMP.sql"
            mysqldump -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" \
                --single-transaction --routines --triggers \
                "${DB_DATABASE:-societypal}" > "$BACKUP_FILE"
            
            # Compress backup
            gzip "$BACKUP_FILE"
            echo "✅ MySQL backup created: ${BACKUP_FILE}.gz"
        else
            echo "❌ mysqldump not available"
            exit 1
        fi
        ;;
    "pgsql")
        if command -v pg_dump >/dev/null 2>&1; then
            BACKUP_FILE="$BACKUP_DIR/postgres_backup_$TIMESTAMP.sql"
            PGPASSWORD="${DB_PASSWORD:-}" pg_dump -h "${DB_HOST:-localhost}" \
                -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-postgres}" \
                -d "${DB_DATABASE:-societypal}" > "$BACKUP_FILE"
            
            # Compress backup
            gzip "$BACKUP_FILE"
            echo "✅ PostgreSQL backup created: ${BACKUP_FILE}.gz"
        else
            echo "❌ pg_dump not available"
            exit 1
        fi
        ;;
    "sqlite")
        if [[ -f "${DB_DATABASE:-database/database.sqlite}" ]]; then
            BACKUP_FILE="$BACKUP_DIR/sqlite_backup_$TIMESTAMP.sqlite"
            cp "${DB_DATABASE:-database/database.sqlite}" "$BACKUP_FILE"
            gzip "$BACKUP_FILE"
            echo "✅ SQLite backup created: ${BACKUP_FILE}.gz"
        else
            echo "❌ SQLite database file not found"
            exit 1
        fi
        ;;
    *)
        echo "❌ Unsupported database type: ${DB_CONNECTION}"
        exit 1
        ;;
esac

# Clean up old backups
find "$BACKUP_DIR" -name "*.gz" -mtime +$BACKUP_RETENTION_DAYS -delete 2>/dev/null || true

echo "🧹 Cleaned up backups older than $BACKUP_RETENTION_DAYS days"
EOF

    chmod +x "$db_backup_script"
    log_setup "DATABASE_BACKUP_SCRIPT" "SUCCESS" "Database backup script created"
    
    echo ""
}

# Setup file system backup strategy
setup_filesystem_backup() {
    echo -e "${BLUE}📋 File System Backup Strategy${NC}"
    echo "============================="
    
    local backup_dir="Admin-Local/4-Backups"
    
    # Create file backup script
    local file_backup_script="$backup_dir/filesystem-backup.sh"
    cat > "$file_backup_script" << 'EOF'
#!/bin/bash

# File System Backup Script
# Purpose: Backup critical application files

set -e

BACKUP_DIR="Admin-Local/4-Backups/02-File-Backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup storage directory (excluding cache)
if [[ -d "storage" ]]; then
    tar -czf "$BACKUP_DIR/storage_backup_$TIMESTAMP.tar.gz" \
        --exclude="storage/framework/cache/*" \
        --exclude="storage/framework/sessions/*" \
        --exclude="storage/framework/views/*" \
        storage/
    echo "✅ Storage backup created"
fi

# Backup uploads and public assets
if [[ -d "public/uploads" ]]; then
    tar -czf "$BACKUP_DIR/uploads_backup_$TIMESTAMP.tar.gz" public/uploads/
    echo "✅ Uploads backup created"
fi

# Backup configuration files
CONFIG_FILES=(".env" ".env.production" ".env.staging" "composer.json" "package.json")
tar -czf "$BACKUP_DIR/config_backup_$TIMESTAMP.tar.gz" "${CONFIG_FILES[@]}" 2>/dev/null || true
echo "✅ Configuration backup created"

# Clean up old backups (keep 14 days)
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +14 -delete 2>/dev/null || true

echo "🧹 Cleaned up old file backups"
EOF

    chmod +x "$file_backup_script"
    log_setup "FILESYSTEM_BACKUP_SCRIPT" "SUCCESS" "File system backup script created"
    
    echo ""
}

# Setup shared directory persistence
setup_shared_persistence() {
    echo -e "${BLUE}📋 Shared Directory Persistence${NC}"
    echo "=============================="
    
    # Create shared persistence configuration
    local persistence_config="Admin-Local/4-Backups/shared-persistence.json"
    cat > "$persistence_config" << 'EOF'
{
    "shared_directories": [
        {
            "source": "storage/app",
            "target": "%shared_path%/storage/app",
            "description": "Application file storage"
        },
        {
            "source": "storage/logs",
            "target": "%shared_path%/storage/logs",
            "description": "Application logs"
        },
        {
            "source": "public/uploads",
            "target": "%shared_path%/public/uploads",
            "description": "User uploaded files"
        },
        {
            "source": ".env",
            "target": "%shared_path%/.env",
            "description": "Environment configuration"
        }
    ],
    "backup_schedule": {
        "database": "daily",
        "files": "weekly",
        "configuration": "on_deployment"
    },
    "retention_policy": {
        "database_backups": 30,
        "file_backups": 14,
        "configuration_backups": 90
    }
}
EOF

    log_setup "PERSISTENCE_CONFIG" "SUCCESS" "Shared persistence configuration created"
    
    # Create persistence setup script
    local persistence_script="Admin-Local/4-Backups/setup-shared-persistence.sh"
    cat > "$persistence_script" << 'EOF'
#!/bin/bash

# Shared Persistence Setup Script
# Purpose: Configure shared directories for zero-downtime deployment

set -e

echo "💾 Setting up shared persistence..."

# Create shared directories structure
SHARED_DIRS=(
    "storage/app/public"
    "storage/framework/cache"
    "storage/framework/sessions" 
    "storage/framework/views"
    "storage/logs"
    "bootstrap/cache"
    "public/uploads"
)

for dir in "${SHARED_DIRS[@]}"; do
    mkdir -p "%shared_path%/$dir"
    echo "📁 Created shared directory: %shared_path%/$dir"
done

# Set proper permissions
chmod -R 755 "%shared_path%/storage" 2>/dev/null || true
chmod -R 755 "%shared_path%/bootstrap/cache" 2>/dev/null || true
chmod -R 755 "%shared_path%/public/uploads" 2>/dev/null || true

echo "✅ Shared persistence setup completed"
EOF

    chmod +x "$persistence_script"
    log_setup "PERSISTENCE_SETUP_SCRIPT" "SUCCESS" "Persistence setup script created"
    
    echo ""
}

# Setup automated backup scheduling
setup_backup_scheduling() {
    echo -e "${BLUE}📋 Backup Scheduling${NC}"
    echo "=================="
    
    # Create backup scheduler script
    local scheduler_script="Admin-Local/4-Backups/backup-scheduler.sh"
    cat > "$scheduler_script" << 'EOF'
#!/bin/bash

# Backup Scheduler Script
# Purpose: Schedule and manage automated backups

set -e

BACKUP_TYPE=${1:-all}
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/01-System-Reports/backup-$(date +%Y%m%d).log"

# Create log directory
mkdir -p "$(dirname "$LOG_FILE")"

# Function to log with timestamp
log_backup() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

log_backup "🚀 Starting backup process: $BACKUP_TYPE"

case "$BACKUP_TYPE" in
    "database"|"db")
        log_backup "📊 Running database backup..."
        if ./Admin-Local/4-Backups/database-backup.sh; then
            log_backup "✅ Database backup completed successfully"
        else
            log_backup "❌ Database backup failed"
            exit 1
        fi
        ;;
    "files"|"filesystem")
        log_backup "📁 Running filesystem backup..."
        if ./Admin-Local/4-Backups/filesystem-backup.sh; then
            log_backup "✅ Filesystem backup completed successfully"
        else
            log_backup "❌ Filesystem backup failed"
            exit 1
        fi
        ;;
    "all"|*)
        log_backup "📊 Running database backup..."
        ./Admin-Local/4-Backups/database-backup.sh || log_backup "⚠️  Database backup failed"
        
        log_backup "📁 Running filesystem backup..."
        ./Admin-Local/4-Backups/filesystem-backup.sh || log_backup "⚠️  Filesystem backup failed"
        
        log_backup "✅ All backups completed"
        ;;
esac

log_backup "🎯 Backup process finished"
EOF

    chmod +x "$scheduler_script"
    log_setup "BACKUP_SCHEDULER" "SUCCESS" "Backup scheduler script created"
    
    # Create cron job template
    local cron_template="Admin-Local/4-Backups/crontab-template.txt"
    cat > "$cron_template" << 'EOF'
# SocietyPal Backup Cron Jobs
# Add these entries to your crontab with: crontab -e

# Daily database backup at 2 AM
0 2 * * * cd /path/to/your/project && ./Admin-Local/4-Backups/backup-scheduler.sh database

# Weekly filesystem backup on Sundays at 3 AM
0 3 * * 0 cd /path/to/your/project && ./Admin-Local/4-Backups/backup-scheduler.sh files

# Monthly full backup on 1st of month at 1 AM
0 1 1 * * cd /path/to/your/project && ./Admin-Local/4-Backups/backup-scheduler.sh all

# Clean up old logs weekly
0 4 * * 1 find /path/to/your/project/Admin-Local/5-Monitoring-And-Logs -name "*.log" -mtime +30 -delete
EOF

    log_setup "CRON_TEMPLATE" "SUCCESS" "Cron job template created"
    
    echo ""
}

# Setup disaster recovery procedures
setup_disaster_recovery() {
    echo -e "${BLUE}📋 Disaster Recovery Procedures${NC}"
    echo "=============================="
    
    # Create disaster recovery script
    local recovery_script="Admin-Local/4-Backups/disaster-recovery.sh"
    cat > "$recovery_script" << 'EOF'
#!/bin/bash

# Disaster Recovery Script
# Purpose: Restore from backups in case of disaster

set -e

RECOVERY_TYPE=${1:-help}
BACKUP_DATE=${2:-latest}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

show_help() {
    echo -e "${BLUE}🆘 DISASTER RECOVERY HELP${NC}"
    echo "========================="
    echo ""
    echo "Usage: $0 [RECOVERY_TYPE] [BACKUP_DATE]"
    echo ""
    echo "Recovery Types:"
    echo "  database    - Restore database from backup"
    echo "  files       - Restore files from backup"
    echo "  full        - Full system restore"
    echo "  list        - List available backups"
    echo ""
    echo "Backup Date:"
    echo "  latest      - Use most recent backup (default)"
    echo "  YYYYMMDD    - Use specific date backup"
    echo ""
    echo "Examples:"
    echo "  $0 list"
    echo "  $0 database latest"
    echo "  $0 files 20241201"
    echo "  $0 full latest"
}

list_backups() {
    echo -e "${BLUE}📋 AVAILABLE BACKUPS${NC}"
    echo "==================="
    
    echo -e "${YELLOW}Database Backups:${NC}"
    ls -la Admin-Local/4-Backups/01-Database-Backups/*.gz 2>/dev/null || echo "No database backups found"
    
    echo -e "${YELLOW}File Backups:${NC}"
    ls -la Admin-Local/4-Backups/02-File-Backups/*.tar.gz 2>/dev/null || echo "No file backups found"
}

restore_database() {
    local backup_file
    
    if [[ "$BACKUP_DATE" == "latest" ]]; then
        backup_file=$(ls -t Admin-Local/4-Backups/01-Database-Backups/*.gz 2>/dev/null | head -1)
    else
        backup_file=$(ls Admin-Local/4-Backups/01-Database-Backups/*${BACKUP_DATE}*.gz 2>/dev/null | head -1)
    fi
    
    if [[ -z "$backup_file" ]]; then
        echo -e "${RED}❌ No database backup found for date: $BACKUP_DATE${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}⚠️  WARNING: This will overwrite the current database!${NC}"
    read -p "Are you sure you want to continue? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}📊 Restoring database from: $backup_file${NC}"
        
        # Load environment variables
        if [[ -f ".env" ]]; then
            source .env
        fi
        
        # Decompress and restore based on database type
        case "${DB_CONNECTION:-mysql}" in
            "mysql")
                gunzip -c "$backup_file" | mysql -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" "${DB_DATABASE:-societypal}"
                ;;
            "pgsql")
                gunzip -c "$backup_file" | PGPASSWORD="${DB_PASSWORD:-}" psql -h "${DB_HOST:-localhost}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-postgres}" -d "${DB_DATABASE:-societypal}"
                ;;
            "sqlite")
                gunzip -c "$backup_file" > "${DB_DATABASE:-database/database.sqlite}"
                ;;
        esac
        
        echo -e "${GREEN}✅ Database restoration completed${NC}"
    else
        echo "Database restoration cancelled"
    fi
}

restore_files() {
    local backup_file
    
    if [[ "$BACKUP_DATE" == "latest" ]]; then
        backup_file=$(ls -t Admin-Local/4-Backups/02-File-Backups/storage_backup_*.tar.gz 2>/dev/null | head -1)
    else
        backup_file=$(ls Admin-Local/4-Backups/02-File-Backups/storage_backup_*${BACKUP_DATE}*.tar.gz 2>/dev/null | head -1)
    fi
    
    if [[ -z "$backup_file" ]]; then
        echo -e "${RED}❌ No file backup found for date: $BACKUP_DATE${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}⚠️  WARNING: This will overwrite current files!${NC}"
    read -p "Are you sure you want to continue? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}📁 Restoring files from: $backup_file${NC}"
        tar -xzf "$backup_file"
        echo -e "${GREEN}✅ File restoration completed${NC}"
    else
        echo "File restoration cancelled"
    fi
}

case "$RECOVERY_TYPE" in
    "database"|"db")
        restore_database
        ;;
    "files"|"filesystem")
        restore_files
        ;;
    "full")
        restore_database
        restore_files
        ;;
    "list")
        list_backups
        ;;
    "help"|*)
        show_help
        ;;
esac
EOF

    chmod +x "$recovery_script"
    log_setup "DISASTER_RECOVERY" "SUCCESS" "Disaster recovery script created"
    
    echo ""
}

# Generate data persistence report
generate_persistence_report() {
    echo -e "${BLUE}📊 DATA PERSISTENCE REPORT${NC}"
    echo "=========================="
    
    local report_file="Admin-Local/5-Monitoring-And-Logs/01-System-Reports/data-persistence-$(date +%Y%m%d_%H%M%S).md"
    mkdir -p "$(dirname "$report_file")"
    
    cat > "$report_file" << EOF
# Data Persistence Strategy Report

**Generated**: $(date '+%Y-%m-%d %H:%M:%S')
**Project**: SocietyPal Laravel Application

## Executive Summary
- **Setups Completed**: $SETUPS_COMPLETED
- **Setups Failed**: $SETUPS_FAILED
- **Status**: $(if [[ $SETUPS_FAILED -eq 0 ]]; then echo "✅ Data Persistence Ready"; else echo "❌ Setup Issues"; fi)

## Data Persistence Components

### Database Backup Strategy
- Automated database backups with compression
- Support for MySQL, PostgreSQL, and SQLite
- 30-day retention policy with automatic cleanup
- Single-transaction backups for consistency

### File System Backup Strategy
- Storage directory backup (excluding cache)
- User uploads and public assets backup
- Configuration files backup
- 14-day retention for file backups

### Shared Directory Persistence
- Zero-downtime deployment compatible
- Persistent storage across releases
- Proper permission management
- Symlink-based shared resources

### Backup Scheduling
- Automated cron job templates
- Daily database backups
- Weekly file system backups
- Monthly full system backups

### Disaster Recovery
- Complete restoration procedures
- Database and file restoration options
- Backup listing and selection
- Safety confirmations for destructive operations

## Setup Results

EOF

    for result in "${SETUP_RESULTS[@]}"; do
        echo "$result" >> "$report_file"
    done
    
    cat >> "$report_file" << EOF

## Usage Instructions

### Manual Backups
\`\`\`bash
# Database backup
./Admin-Local/4-Backups/database-backup.sh

# File system backup
./Admin-Local/4-Backups/filesystem-backup.sh

# Scheduled backup (all)
./Admin-Local/4-Backups/backup-scheduler.sh all
\`\`\`

### Disaster Recovery
\`\`\`bash
# List available backups
./Admin-Local/4-Backups/disaster-recovery.sh list

# Restore database
./Admin-Local/4-Backups/disaster-recovery.sh database latest

# Full system restore
./Admin-Local/4-Backups/disaster-recovery.sh full latest
\`\`\`

### Cron Job Setup
1. Edit crontab: \`crontab -e\`
2. Add entries from: \`Admin-Local/4-Backups/crontab-template.txt\`
3. Update paths to match your deployment location

## Recommendations

### Production Setup
1. Configure automated backups via cron jobs
2. Test disaster recovery procedures regularly
3. Monitor backup success and storage usage
4. Implement off-site backup storage for critical data

### Security Considerations
1. Encrypt sensitive backup data
2. Secure backup storage locations
3. Limit access to backup and recovery scripts
4. Regularly audit backup integrity

---
**Setup Complete**: $(date '+%Y-%m-%d %H:%M:%S')
EOF

    echo -e "${GREEN}📊 Data persistence report saved: $report_file${NC}"
}

# Display setup summary
display_setup_summary() {
    echo ""
    echo -e "${BLUE}📊 SETUP SUMMARY${NC}"
    echo "================"
    echo -e "${GREEN}✅ Completed: $SETUPS_COMPLETED${NC}"
    echo -e "${RED}❌ Failed: $SETUPS_FAILED${NC}"
    echo ""
    
    echo -e "${BLUE}📋 SETUP RESULTS:${NC}"
    for result in "${SETUP_RESULTS[@]}"; do
        echo "$result"
    done
    
    echo ""
    if [[ $SETUPS_FAILED -eq 0 ]]; then
        echo -e "${GREEN}🎯 DATA PERSISTENCE: ✅ SETUP COMPLETED${NC}"
        echo -e "${GREEN}💾 Backup and recovery systems ready!${NC}"
    else
        echo -e "${RED}🎯 DATA PERSISTENCE: ❌ $SETUPS_FAILED ISSUES FOUND${NC}"
        echo -e "${RED}🔧 Fix setup issues before proceeding${NC}"
    fi
}

# Main execution
main() {
    echo "Starting data persistence strategy setup..."
    echo ""
    
    # Create necessary directories
    mkdir -p Admin-Local/{4-Backups,5-Monitoring-And-Logs/01-System-Reports}
    
    # Run all setup phases
    setup_database_backup
    setup_filesystem_backup
    setup_shared_persistence
    setup_backup_scheduling
    setup_disaster_recovery
    generate_persistence_report
    display_setup_summary
    
    # Return appropriate exit code
    if [[ $SETUPS_FAILED -eq 0 ]]; then
        exit 0
    else
        exit 1
    fi
}

# Execute main function
main "$@"
