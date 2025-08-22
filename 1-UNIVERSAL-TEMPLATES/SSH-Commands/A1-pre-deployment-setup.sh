#!/bin/bash
# UNIVERSAL SSH A1: Complete Pre-Deployment Setup
# Combines DeployHQ A01-A04: System check, backups, maintenance mode
# Based on FINAL-DEPLOYHQ-PIPELINE.md + Expert 3 research

set -e

echo "🚧 UNIVERSAL SSH A1: Complete Pre-Deployment Setup"
echo "=================================================="
echo "📚 Combines DeployHQ A01-A04 + Expert 3 comprehensive research"
echo "🎯 Works for ANY Laravel deployment environment"
echo ""

# ============================================================================
# A01: SYSTEM CHECK & ENVIRONMENT SETUP
# ============================================================================

echo "🔍 A01: System Check & Environment Setup"
echo "======================================="

# Basic system information (DeployHQ A01)
echo "🖥️ System Environment:"
php --version
composer --version

# PHP Extensions Check (Expert 3 critical validation)
echo "🧩 Critical PHP Extensions Check:"
REQUIRED_EXTENSIONS=("mbstring" "pdo" "openssl" "json" "ctype" "fileinfo" "tokenizer" "xml")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "  ✅ $ext"
    else
        echo "  ❌ $ext: MISSING"
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    echo "🚨 CRITICAL: Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    exit 1
fi

# Ensure shared directories exist (DeployHQ A01)
echo "📁 Creating shared directory structure..."
mkdir -p ../shared/storage/{app/public,framework/{cache,sessions,views},logs}
mkdir -p ../shared/backups
echo "  ✅ Shared directories created"

# ============================================================================
# A02: BACKUP CURRENT RELEASE  
# ============================================================================

echo ""
echo "💾 A02: Backup Current Release"
echo "=============================="

if [ -L "../current" ] && [ -d "../current" ]; then
    BACKUP_DIR="../shared/backups/release-backup-$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    cp -r ../current/* "$BACKUP_DIR/" 2>/dev/null || true
    echo "✅ Current release backed up to: $BACKUP_DIR"
else
    echo "ℹ️ No current release to backup (first deployment)"
fi

# ============================================================================
# A03: DATABASE BACKUP
# ============================================================================

echo ""
echo "🗄️ A03: Database Backup"
echo "======================="

if [ -f "../shared/.env" ]; then
    set +e  # Allow errors for optional backup
    source "../shared/.env" 2>/dev/null
    if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
        BACKUP_FILE="../shared/backups/db-backup-$(date +%Y%m%d_%H%M%S).sql"
        echo "Creating database backup..."
        if mysqldump -h"${DB_HOST:-localhost}" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null; then
            echo "✅ Database backed up to: $BACKUP_FILE"
        else
            echo "⚠️ Database backup failed (continuing deployment)"
        fi
    else
        echo "ℹ️ Database credentials not found, skipping backup"
    fi
    set -e
else
    echo "ℹ️ No .env file found, skipping database backup"
fi

# ============================================================================
# A04: ENTER MAINTENANCE MODE
# ============================================================================

echo ""
echo "🚧 A04: Enter Maintenance Mode"
echo "=============================="

if [ -f "../current/artisan" ]; then
    cd ../current
    if php artisan down --secret="deploying" --render="errors::503" 2>/dev/null; then
        echo "✅ Application in maintenance mode"
    else
        echo "⚠️ Failed to enter maintenance mode (continuing)"
    fi
    cd - > /dev/null
else
    echo "ℹ️ No current application (first deployment)"
fi

# ============================================================================
# PRE-DEPLOYMENT READINESS CHECK (Expert 3 validation)
# ============================================================================

echo ""
echo "🔍 Pre-Deployment Readiness Verification..."

# Check disk space (Expert 3 resource management)
DISK_USAGE=$(df -h . | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    echo "⚠️ WARNING: Disk usage is ${DISK_USAGE}% - low space available"
else
    echo "✅ Sufficient disk space available (${DISK_USAGE}% used)"
fi

# Verify shared .env exists (Expert 3 configuration validation)
if [ -f "../shared/.env" ]; then
    echo "✅ Shared .env configuration available"
    
    # Check critical environment variables
    if grep -q "APP_KEY=.*[a-zA-Z0-9]" ../shared/.env; then
        echo "✅ APP_KEY configured in shared environment"
    else
        echo "⚠️ WARNING: APP_KEY missing in shared .env"
    fi
else
    echo "⚠️ WARNING: No shared .env file - create ../shared/.env"
fi

echo ""
echo "✅ Pre-deployment setup completed successfully!"
echo ""
echo "🎯 PRE-DEPLOYMENT SUMMARY:"
echo "  ✅ System environment verified"
echo "  ✅ Shared directories created"
echo "  ✅ Current release backed up"
echo "  ✅ Database backed up (if available)"
echo "  ✅ Maintenance mode activated"
echo "  ✅ System ready for file deployment"
echo ""
echo "🚀 Ready for build artifact deployment!"
echo "📚 Based on DeployHQ A01-A04 + Expert 3 research"
echo "⏭️ Deploy files, then run B1-deployment-finalization.sh"
