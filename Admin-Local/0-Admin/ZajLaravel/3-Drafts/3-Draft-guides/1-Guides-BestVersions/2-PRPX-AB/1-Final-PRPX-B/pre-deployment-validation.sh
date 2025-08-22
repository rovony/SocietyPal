#!/bin/bash

# Script: pre-deployment-validation.sh
# Purpose: Comprehensive 10-point pre-deployment validation checklist
# Version: 2.0  
# Section: B - Prepare for Build and Deployment
# Location: 🟢 Local Machine

set -euo pipefail

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/load-variables.sh"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║           Pre-Deployment Validation Checklist           ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Change to project directory
cd "$PATH_LOCAL_MACHINE"

# Initialize validation results
VALIDATION_REPORT="$DEPLOY_WORKSPACE/Logs/pre-deployment-validation-$(date +%Y%m%d-%H%M%S).md"
mkdir -p "$(dirname "$VALIDATION_REPORT")"

FAILED_CHECKS=()
PASSED_CHECKS=()
WARNING_CHECKS=()
TOTAL_CHECKS=10

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Initialize report
cat > "$VALIDATION_REPORT" << EOF
# Pre-Deployment Validation Report

**Generated:** $(date)
**Project:** $PROJECT_NAME
**Validation Type:** Comprehensive 10-Point Pre-Deployment Checklist

---

EOF

log "🔍 Running comprehensive 10-point validation checklist..."
echo ""

# Helper function for check results
check_result() {
    local check_name="$1"
    local check_status="$2" 
    local check_details="$3"
    local check_number="$4"
    
    if [ "$check_status" = "PASS" ]; then
        echo "✅ [$check_number/10] $check_name: PASSED"
        echo "## ✅ Check $check_number: $check_name" >> "$VALIDATION_REPORT"
        echo "**Status:** PASSED" >> "$VALIDATION_REPORT"
        echo "**Details:** $check_details" >> "$VALIDATION_REPORT"
        PASSED_CHECKS+=("$check_name")
    elif [ "$check_status" = "WARN" ]; then
        echo "⚠️ [$check_number/10] $check_name: WARNING" 
        echo "## ⚠️ Check $check_number: $check_name" >> "$VALIDATION_REPORT"
        echo "**Status:** WARNING" >> "$VALIDATION_REPORT"
        echo "**Details:** $check_details" >> "$VALIDATION_REPORT"
        WARNING_CHECKS+=("$check_name")
    else
        echo "❌ [$check_number/10] $check_name: FAILED"
        echo "## ❌ Check $check_number: $check_name" >> "$VALIDATION_REPORT"
        echo "**Status:** FAILED" >> "$VALIDATION_REPORT" 
        echo "**Details:** $check_details" >> "$VALIDATION_REPORT"
        FAILED_CHECKS+=("$check_name")
    fi
    echo "" >> "$VALIDATION_REPORT"
}

# Check 1: Environment Configuration Validation
log "1/10 - Validating Environment Configuration..."
ENV_STATUS="PASS"
ENV_DETAILS=""

if [ ! -f ".env" ]; then
    ENV_STATUS="FAIL"
    ENV_DETAILS="Missing .env file in project root"
elif ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d'=' -f2 | tr -d '\"')" ]; then
    ENV_STATUS="FAIL"
    ENV_DETAILS="APP_KEY not set or empty in .env file"
elif ! grep -q "^DB_" .env; then
    ENV_STATUS="WARN"
    ENV_DETAILS="Database configuration appears to be missing in .env"
else
    # Check for production-ready values
    APP_ENV=$(grep -E "^APP_ENV=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    APP_DEBUG=$(grep -E "^APP_DEBUG=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    
    if [ "$APP_ENV" = "production" ] && [ "$APP_DEBUG" = "false" ]; then
        ENV_DETAILS="Environment properly configured for production deployment"
    else
        ENV_DETAILS="Environment file present with APP_ENV=$APP_ENV, APP_DEBUG=$APP_DEBUG"
    fi
fi
check_result "Environment Configuration" "$ENV_STATUS" "$ENV_DETAILS" "1"

# Check 2: Dependencies Installation and Validation
log "2/10 - Validating Dependencies Installation..."
DEPS_STATUS="PASS"
DEPS_DETAILS=""

if [ ! -d "vendor" ] || [ ! -f "composer.lock" ]; then
    DEPS_STATUS="FAIL"
    DEPS_DETAILS="PHP dependencies not installed - run: composer install"
elif [ -f "package.json" ] && ([ ! -d "node_modules" ] || [ ! -f "package-lock.json" ]); then
    DEPS_STATUS="FAIL"
    DEPS_DETAILS="Node.js dependencies not installed - run: npm install"
else
    # Test production dependencies installation capability
    if composer install --no-dev --dry-run > /tmp/composer-prod-test.log 2>&1; then
        # Count installed packages
        COMPOSER_PACKAGES=$(find vendor/ -name "composer.json" | wc -l 2>/dev/null || echo "unknown")
        if [ -d "node_modules" ]; then
            NPM_PACKAGES=$(find node_modules/ -maxdepth 1 -type d | wc -l 2>/dev/null || echo "unknown")
            DEPS_DETAILS="Dependencies installed - PHP: $COMPOSER_PACKAGES packages, Node: $NPM_PACKAGES packages, production installation validated"
        else
            DEPS_DETAILS="Dependencies installed - PHP: $COMPOSER_PACKAGES packages, production installation validated"
        fi
    else
        DEPS_STATUS="FAIL"
        DEPS_DETAILS="Production dependency installation would fail - check /tmp/composer-prod-test.log"
    fi
fi
check_result "Dependencies Installation" "$DEPS_STATUS" "$DEPS_DETAILS" "2"

# Check 3: Database Connectivity and Migration Status
log "3/10 - Validating Database Connection and Migrations..."
DB_STATUS="PASS"
DB_DETAILS=""

if php artisan migrate:status > /tmp/migration-status.log 2>&1; then
    MIGRATION_COUNT=$(php artisan migrate:status 2>/dev/null | grep -c "Ran" || echo "0")
    PENDING_COUNT=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || echo "0")
    
    if [ "$PENDING_COUNT" -gt 0 ]; then
        DB_STATUS="WARN"
        DB_DETAILS="Database connected, $MIGRATION_COUNT migrations applied, $PENDING_COUNT pending migrations"
    else
        DB_DETAILS="Database connected successfully, $MIGRATION_COUNT migrations applied, no pending migrations"
    fi
else
    DB_STATUS="WARN"
    DB_DETAILS="Database connection test failed or not configured - check configuration"
fi
check_result "Database Connectivity" "$DB_STATUS" "$DB_DETAILS" "3"

# Check 4: Build Process Validation
log "4/10 - Validating Build Process..."
BUILD_STATUS="PASS"
BUILD_DETAILS=""

# Test Laravel optimization commands
BUILD_ERRORS=()

if ! php artisan config:cache > /dev/null 2>&1; then
    BUILD_ERRORS+=("config:cache failed")
fi

if ! php artisan route:cache > /dev/null 2>&1; then
    BUILD_ERRORS+=("route:cache failed")  
fi

if ! php artisan view:cache > /dev/null 2>&1; then
    BUILD_ERRORS+=("view:cache failed")
fi

# Clear caches after testing
php artisan config:clear > /dev/null 2>&1 || true
php artisan route:clear > /dev/null 2>&1 || true  
php artisan view:clear > /dev/null 2>&1 || true

if [ ${#BUILD_ERRORS[@]} -gt 0 ]; then
    BUILD_STATUS="FAIL"
    BUILD_DETAILS="Laravel optimization failed: $(IFS=', '; echo "${BUILD_ERRORS[*]}")"
else
    # Test frontend build if applicable
    if [ -f "package.json" ] && (grep -q '"build"' package.json || grep -q '"production"' package.json); then
        if command -v npm >/dev/null 2>&1; then
            BUILD_DETAILS="Laravel optimization successful, frontend build capability detected"
        else
            BUILD_STATUS="WARN"
            BUILD_DETAILS="Laravel optimization successful, but npm not available for frontend builds"
        fi
    else
        BUILD_DETAILS="Laravel optimization successful, no frontend build detected"
    fi
fi
check_result "Build Process" "$BUILD_STATUS" "$BUILD_DETAILS" "4"

# Check 5: Security Configuration
log "5/10 - Validating Security Configuration..."
SEC_STATUS="PASS"
SEC_DETAILS=""

SECURITY_ISSUES=()

# Check APP_DEBUG setting for production
if grep -q "APP_DEBUG=true" .env 2>/dev/null; then
    SECURITY_ISSUES+=("APP_DEBUG=true (should be false in production)")
fi

# Check APP_ENV setting
APP_ENV_VALUE=$(grep -E "^APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "")
if [ -z "$APP_ENV_VALUE" ]; then
    SECURITY_ISSUES+=("APP_ENV not set")
elif [ "$APP_ENV_VALUE" = "local" ] || [ "$APP_ENV_VALUE" = "testing" ]; then
    SECURITY_ISSUES+=("APP_ENV=$APP_ENV_VALUE (should be 'production' or 'staging' for deployment)")
fi

# Check for HTTPS configuration
if ! grep -q "APP_URL=https" .env 2>/dev/null && ! grep -q "FORCE_HTTPS=true" .env 2>/dev/null; then
    SECURITY_ISSUES+=("HTTPS enforcement not configured (consider setting APP_URL=https:// or FORCE_HTTPS=true)")
fi

# Check for sensitive data exposure
if [ -f ".env" ] && grep -q ".env" .gitignore 2>/dev/null; then
    SECURITY_BONUS="✅ .env properly excluded from git"
else
    SECURITY_ISSUES+=("Ensure .env is in .gitignore to prevent sensitive data exposure")
fi

if [ ${#SECURITY_ISSUES[@]} -gt 0 ]; then
    SEC_STATUS="WARN"
    SEC_DETAILS="Security issues detected: $(IFS='; '; echo "${SECURITY_ISSUES[*]}")"
else
    SEC_DETAILS="Security configuration validated successfully. $SECURITY_BONUS"
fi
check_result "Security Configuration" "$SEC_STATUS" "$SEC_DETAILS" "5"

# Check 6: File Permissions and Storage
log "6/10 - Validating File Permissions..." 
PERM_STATUS="PASS"
PERM_DETAILS=""

STORAGE_DIRS=("storage/app" "storage/framework" "storage/logs" "bootstrap/cache")
PERM_ISSUES=0
PERMISSION_DETAILS=()

for dir in "${STORAGE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        if [ -w "$dir" ]; then
            PERMISSION_DETAILS+=("$dir: writable ✅")
        else
            PERMISSION_DETAILS+=("$dir: NOT writable ❌")
            ((PERM_ISSUES++))
        fi
    else
        PERMISSION_DETAILS+=("$dir: missing directory ⚠️")
        ((PERM_ISSUES++))
    fi
done

if [ $PERM_ISSUES -eq 0 ]; then
    PERM_DETAILS="All critical directories have proper write permissions: $(IFS=', '; echo "${PERMISSION_DETAILS[*]}")"
else
    PERM_STATUS="FAIL"
    PERM_DETAILS="$PERM_ISSUES permission issues detected: $(IFS=', '; echo "${PERMISSION_DETAILS[*]}")"
fi
check_result "File Permissions" "$PERM_STATUS" "$PERM_DETAILS" "6"

# Check 7: Git Repository Status
log "7/10 - Validating Git Repository Status..."
GIT_STATUS="PASS" 
GIT_DETAILS=""

if [ ! -d ".git" ]; then
    GIT_STATUS="FAIL"
    GIT_DETAILS="Not a git repository - version control is required for deployment"
else
    # Check for uncommitted changes
    if [ -n "$(git status --porcelain 2>/dev/null)" ]; then
        UNCOMMITTED_COUNT=$(git status --porcelain 2>/dev/null | wc -l)
        GIT_STATUS="WARN"
        GIT_DETAILS="$UNCOMMITTED_COUNT uncommitted changes detected - should commit before deployment"
    else
        CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "detached")
        CURRENT_COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
        GIT_DETAILS="Repository clean, branch: $CURRENT_BRANCH, commit: $CURRENT_COMMIT"
    fi
fi
check_result "Git Repository Status" "$GIT_STATUS" "$GIT_DETAILS" "7"

# Check 8: Configuration Files Validation  
log "8/10 - Validating Configuration Files..."
CONFIG_STATUS="PASS"
CONFIG_DETAILS=""

CRITICAL_FILES=("config/app.php" "config/database.php" "composer.json" ".env.example")
MISSING_CONFIGS=()
PRESENT_CONFIGS=()

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        PRESENT_CONFIGS+=("$file")
    else
        MISSING_CONFIGS+=("$file")
    fi
done

# Additional Laravel-specific checks
if [ -f "artisan" ]; then
    PRESENT_CONFIGS+=("artisan")
else
    MISSING_CONFIGS+=("artisan")
fi

if [ ${#MISSING_CONFIGS[@]} -gt 0 ]; then
    CONFIG_STATUS="FAIL"
    CONFIG_DETAILS="Missing critical files: $(IFS=', '; echo "${MISSING_CONFIGS[*]}"). Present: $(IFS=', '; echo "${PRESENT_CONFIGS[*]}")"
else
    CONFIG_DETAILS="All critical configuration files present: $(IFS=', '; echo "${PRESENT_CONFIGS[*]}")"
fi
check_result "Configuration Files" "$CONFIG_STATUS" "$CONFIG_DETAILS" "8"

# Check 9: Deployment Variables and Admin-Local Structure
log "9/10 - Validating Deployment Configuration..."
DEPLOY_STATUS="PASS"
DEPLOY_DETAILS=""

REQUIRED_STRUCTURE=(
    "Admin-Local/Deployment/Configs/deployment-variables.json"
    "Admin-Local/Deployment/Scripts/load-variables.sh" 
    "Admin-Local/Deployment/Scripts/comprehensive-env-check.sh"
    "Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh"
)

MISSING_STRUCTURE=()
PRESENT_STRUCTURE=()

for item in "${REQUIRED_STRUCTURE[@]}"; do
    if [ -f "$item" ]; then
        PRESENT_STRUCTURE+=("$(basename "$item")")
    else
        MISSING_STRUCTURE+=("$(basename "$item")")
    fi
done

if [ ${#MISSING_STRUCTURE[@]} -gt 0 ]; then
    DEPLOY_STATUS="FAIL"
    DEPLOY_DETAILS="Missing Admin-Local components: $(IFS=', '; echo "${MISSING_STRUCTURE[*]}")"
else
    # Validate JSON structure
    if jq empty Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null; then
        PROJECT_CONFIG_NAME=$(jq -r '.project.name' Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null || echo "Unknown")
        DEPLOY_DETAILS="Deployment configuration complete for project: $PROJECT_CONFIG_NAME. Components: $(IFS=', '; echo "${PRESENT_STRUCTURE[*]}")"
    else
        DEPLOY_STATUS="FAIL"
        DEPLOY_DETAILS="Invalid JSON in deployment-variables.json"
    fi
fi
check_result "Deployment Configuration" "$DEPLOY_STATUS" "$DEPLOY_DETAILS" "9"

# Check 10: Application Health and Basic Functionality
log "10/10 - Validating Application Health..."
HEALTH_STATUS="PASS"
HEALTH_DETAILS=""

HEALTH_TESTS=()

# Test basic Laravel functionality
if php artisan --version > /dev/null 2>&1; then
    HEALTH_TESTS+=("artisan commands functional")
else
    HEALTH_STATUS="FAIL"
    HEALTH_TESTS+=("artisan commands NOT functional")
fi

if php artisan route:list > /dev/null 2>&1; then
    HEALTH_TESTS+=("routing system functional")
else
    HEALTH_STATUS="WARN"
    HEALTH_TESTS+=("routing system has issues")
fi

# Test basic PHP syntax
if php -l artisan > /dev/null 2>&1; then
    HEALTH_TESTS+=("PHP syntax valid")
else
    HEALTH_STATUS="FAIL"
    HEALTH_TESTS+=("PHP syntax errors detected")
fi

# Test storage link
if [ -L "public/storage" ]; then
    HEALTH_TESTS+=("storage link present")
elif [ -d "public/storage" ]; then
    HEALTH_TESTS+=("storage directory exists (not symlinked)")
else
    HEALTH_TESTS+=("storage link missing (run: php artisan storage:link)")
fi

HEALTH_DETAILS="Application health tests: $(IFS=', '; echo "${HEALTH_TESTS[*]}")"
check_result "Application Health" "$HEALTH_STATUS" "$HEALTH_DETAILS" "10"

# Generate Final Summary
echo "" >> "$VALIDATION_REPORT"
echo "## 📊 Validation Summary" >> "$VALIDATION_REPORT"
echo "- **Total Checks:** $TOTAL_CHECKS" >> "$VALIDATION_REPORT"
echo "- **Passed:** ${#PASSED_CHECKS[@]}" >> "$VALIDATION_REPORT"
echo "- **Warnings:** ${#WARNING_CHECKS[@]}" >> "$VALIDATION_REPORT"
echo "- **Failed:** ${#FAILED_CHECKS[@]}" >> "$VALIDATION_REPORT"
echo "" >> "$VALIDATION_REPORT"

# Determine overall status
OVERALL_STATUS="READY"
if [ ${#FAILED_CHECKS[@]} -gt 0 ]; then
    OVERALL_STATUS="BLOCKED"
elif [ ${#WARNING_CHECKS[@]} -gt 0 ]; then
    OVERALL_STATUS="CAUTION" 
fi

echo "- **Overall Status:** $OVERALL_STATUS" >> "$VALIDATION_REPORT"

# Display final results
echo ""
echo "════════════════════════════════════════════════════════════"
echo ""

if [ "$OVERALL_STATUS" = "READY" ]; then
    echo "## ✅ DEPLOYMENT READY" >> "$VALIDATION_REPORT"
    echo "All validation checks passed successfully. Project is ready for deployment." >> "$VALIDATION_REPORT"
    
    echo "🎉 ALL VALIDATION CHECKS PASSED!"
    echo "✅ Total: ${#PASSED_CHECKS[@]}/$TOTAL_CHECKS checks passed"
    echo "📋 Project is ready for zero-error deployment"
    log "Deployment readiness: CONFIRMED"
    
elif [ "$OVERALL_STATUS" = "CAUTION" ]; then
    echo "## ⚠️ DEPLOYMENT READY WITH WARNINGS" >> "$VALIDATION_REPORT"
    echo "Core functionality validated but warnings present:" >> "$VALIDATION_REPORT"
    for warning in "${WARNING_CHECKS[@]}"; do
        echo "- $warning" >> "$VALIDATION_REPORT"
    done
    
    echo "⚠️ VALIDATION PASSED WITH WARNINGS"
    echo "✅ Passed: ${#PASSED_CHECKS[@]}/$TOTAL_CHECKS"
    echo "⚠️ Warnings: ${#WARNING_CHECKS[@]} (review recommended)"
    log "Deployment readiness: READY WITH CAUTION"
    
else
    echo "## ❌ DEPLOYMENT BLOCKED" >> "$VALIDATION_REPORT"
    echo "Critical issues must be resolved before deployment:" >> "$VALIDATION_REPORT"
    for failed_check in "${FAILED_CHECKS[@]}"; do
        echo "- **$failed_check**" >> "$VALIDATION_REPORT"
    done
    
    echo "❌ VALIDATION FAILED!"
    echo "✅ Passed: ${#PASSED_CHECKS[@]}/$TOTAL_CHECKS"
    echo "⚠️ Warnings: ${#WARNING_CHECKS[@]}"
    echo "❌ Failed: ${#FAILED_CHECKS[@]}"
    echo ""
    echo "Critical issues detected:"
    for failed_check in "${FAILED_CHECKS[@]}"; do
        echo "  - $failed_check"
    done
    log "Deployment readiness: BLOCKED"
fi

echo ""
echo "📁 Full validation report: $VALIDATION_REPORT"

# Exit with appropriate code
if [ "$OVERALL_STATUS" = "BLOCKED" ]; then
    exit 1
else
    exit 0
fi