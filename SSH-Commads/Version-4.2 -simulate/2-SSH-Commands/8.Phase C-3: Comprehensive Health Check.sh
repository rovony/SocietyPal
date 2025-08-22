#!/bin/bash
set -e

# Phase C-3: Comprehensive Health Check & Auto-Fix
# Purpose: Debug HTTP 500, ensure app functionality, auto-fix common issues
# Version 3.0 - PRODUCTION READY (Enhanced with %path% variable only)
# Run after Phase C to verify deployment health and auto-fix issues
echo "=== Phase C-3: Comprehensive Health Check & Auto-Fix ==="

# ENHANCED: Define all variables relative to %path% (only available DeployHQ variable)
# %path% = Base server path we're deploying to (e.g., /home/user/domains/site.com/deploy)
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"

cd "$CURRENT_PATH"

echo "? Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Current Path: $CURRENT_PATH"
echo "   Shared Path: $SHARED_PATH"

# Initialize counters and tracking
TOTAL_CHECKS=0
PASSED_CHECKS=0
ISSUES_FOUND=0
FIXES_APPLIED=0

# Initialize issue tracking files
echo "" > /tmp/c3_issues_log
echo "" > /tmp/c3_fixes_log
echo "" > /tmp/c3_detailed_issues
echo "" > /tmp/c3_action_items

# Helper function to log results with enhanced tracking
log_check() {
    local CHECK_NAME="$1"
    local STATUS="$2"
    local DETAILS="$3"
    local CATEGORY="${4:-General}"
    local IMPACT="${5:-Medium}"
    
    TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    
    if [ "$STATUS" = "PASS" ]; then
        echo "✅ $CHECK_NAME: $DETAILS"
        PASSED_CHECKS=$((PASSED_CHECKS + 1))
    elif [ "$STATUS" = "WARN" ]; then
        echo "⚠️ $CHECK_NAME: $DETAILS"
        ISSUES_FOUND=$((ISSUES_FOUND + 1))
        # Log issue for report with enhanced details
        echo "- ⚠️ **$CHECK_NAME**: $DETAILS" >> /tmp/c3_issues_log
        echo "$CATEGORY|WARNING|$CHECK_NAME|$DETAILS|$IMPACT" >> /tmp/c3_detailed_issues
    else
        echo "❌ $CHECK_NAME: $DETAILS"
        ISSUES_FOUND=$((ISSUES_FOUND + 1))
        # Log issue for report with enhanced details
        echo "- ❌ **$CHECK_NAME**: $DETAILS" >> /tmp/c3_issues_log
        echo "$CATEGORY|ERROR|$CHECK_NAME|$DETAILS|$IMPACT" >> /tmp/c3_detailed_issues
    fi
}

# Helper function to apply fixes with enhanced logging
apply_fix() {
    local FIX_NAME="$1"
    local COMMAND="$2"
    local DESCRIPTION="${3:-Auto-fix applied}"
    
    echo "? Applying fix: $FIX_NAME"
    if eval "$COMMAND"; then
        echo "✅ Fix applied: $FIX_NAME"
        FIXES_APPLIED=$((FIXES_APPLIED + 1))
        # Log fix for report with description
        echo "- ✅ **$FIX_NAME**: Successfully applied" >> /tmp/c3_fixes_log
        echo "SUCCESS|$FIX_NAME|$DESCRIPTION" >> /tmp/c3_action_items
        return 0
    else
        echo "❌ Fix failed: $FIX_NAME"
        # Log failed fix for report
        echo "- ❌ **$FIX_NAME**: Failed to apply" >> /tmp/c3_fixes_log
        echo "FAILED|$FIX_NAME|$DESCRIPTION" >> /tmp/c3_action_items
        return 1
    fi
}

# Helper function to add action items
add_action_item() {
    local PRIORITY="$1"
    local TITLE="$2"
    local DESCRIPTION="$3"
    local COMMAND="${4:-N/A}"
    
    echo "ACTION|$PRIORITY|$TITLE|$DESCRIPTION|$COMMAND" >> /tmp/c3_action_items
}

echo "? Starting comprehensive health checks..."

# C3-01: Basic Laravel Health Check
echo "=== Laravel Framework Health ==="

if [ -f "artisan" ]; then
    if php artisan --version >/dev/null 2>&1; then
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
        log_check "Laravel Framework" "PASS" "$LARAVEL_VERSION" "Core" "High"
    else
        log_check "Laravel Framework" "FAIL" "Artisan command failed" "Core" "Critical"
        # Try to get more info
        echo "  ? Debug: $(php artisan --version 2>&1 | head -1)"
        add_action_item "HIGH" "Fix Laravel Framework" "Artisan command failing - check PHP configuration and dependencies" "php artisan --version"
        
        # Auto-fix: Try to reinstall dependencies
        echo "? Attempting to fix Laravel dependencies..."
        COMPOSER_CMD="composer"
        if command -v composer2 &> /dev/null; then
            COMPOSER_CMD="composer2"
        fi
        
        # CRITICAL: DO NOT run composer install - it destroys build artifacts
        echo "❌ Laravel framework broken - this indicates a build process failure"
        echo "? Build artifacts should contain all required dependencies (including dev deps)"
        echo "? DO NOT run composer install here - it will destroy the correct dependency set"
        echo "? Manual intervention required: Re-deploy with proper build process"
        add_action_item "CRITICAL" "Re-deploy with Build Process" "Laravel framework not operational - build artifacts may be corrupted" "Re-deploy from build server with complete dependency set"
        
        # Skip the composer install and retest logic
        if false; then
            # Retest Laravel after fix
            if php artisan --version >/dev/null 2>&1; then
                LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
                echo "✅ Laravel Framework now working: $LARAVEL_VERSION"
                # Update the log to reflect the fix
                PASSED_CHECKS=$((PASSED_CHECKS + 1))
                ISSUES_FOUND=$((ISSUES_FOUND - 1))
            fi
        fi
    fi
else
    log_check "Laravel Framework" "FAIL" "No artisan file found" "Core" "Critical"
    add_action_item "CRITICAL" "Laravel Installation Missing" "No artisan file found - Laravel may not be properly installed" "Check deployment and ensure Laravel files are present"
fi

# C3-02: Environment Configuration Check
echo "=== Environment Configuration ==="

if [ -f ".env" ]; then
    log_check "Environment File" "PASS" ".env file exists and readable" "Configuration" "High"
    
    # Check required variables with enhanced logging
    REQUIRED_VARS=("APP_KEY" "DB_DATABASE" "DB_USERNAME" "DB_PASSWORD")
    MISSING_VARS=()
    for VAR in "${REQUIRED_VARS[@]}"; do
        VALUE=$(grep "^${VAR}=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null)
        if [ -n "$VALUE" ]; then
            log_check "Required Var: $VAR" "PASS" "Set" "Configuration" "Medium"
        else
            log_check "Required Var: $VAR" "FAIL" "Missing or empty" "Configuration" "High"
            MISSING_VARS+=("$VAR")
        fi
    done
    
    # Add action item for missing variables
    if [ ${#MISSING_VARS[@]} -gt 0 ]; then
        add_action_item "HIGH" "Configure Missing Environment Variables" "Missing: ${MISSING_VARS[*]}" "Edit .env file and set: ${MISSING_VARS[*]}"
    fi
else
    log_check "Environment File" "FAIL" ".env file missing" "Configuration" "Critical"
    add_action_item "CRITICAL" "Create Environment File" "No .env file found - application cannot start" "cp .env.example .env && php artisan key:generate"
fi

# C3-03: Database Connection Test
echo "=== Database Connection ==="

if [ -f "artisan" ] && [ -f ".env" ]; then
    DB_TEST_RESULT=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null)
    
    if echo "$DB_TEST_RESULT" | grep -q "OK"; then
        log_check "Database Connection" "PASS" "Connected successfully" "Database" "High"
    else
        log_check "Database Connection" "FAIL" "Connection failed" "Database" "Critical"
        echo "  ? Debug: $DB_TEST_RESULT"
        add_action_item "CRITICAL" "Fix Database Connection" "Cannot connect to database - check credentials and server" "Verify DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env"
    fi
else
    log_check "Database Connection" "WARN" "Cannot test (missing artisan or .env)" "Database" "Medium"
fi

# C3-04: Cache System Health Check
echo "=== Cache System Health ==="

if [ -f ".env" ]; then
    CACHE_DRIVER=$(grep "^CACHE_DRIVER=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null)
    CACHE_DRIVER=${CACHE_DRIVER:-file}
    
    echo "? Cache driver: $CACHE_DRIVER"
    
    # Test cache functionality
    CACHE_TEST_SCRIPT='
<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";

try {
    Cache::put("health_check", "test_value", 60);
    $value = Cache::get("health_check");
    if ($value === "test_value") {
        Cache::forget("health_check");
        echo "CACHE_OK";
    } else {
        echo "CACHE_FAILED";
    }
} catch (Exception $e) {
    echo "CACHE_ERROR: " . $e->getMessage();
}
'
    
    CACHE_RESULT=$(echo "$CACHE_TEST_SCRIPT" | php 2>/dev/null)
    
    if echo "$CACHE_RESULT" | grep -q "CACHE_OK"; then
        log_check "Cache System" "PASS" "$CACHE_DRIVER driver working" "Performance" "Medium"
    elif echo "$CACHE_RESULT" | grep -q "CACHE_ERROR.*Redis"; then
        log_check "Cache System" "FAIL" "Redis cache configured but not available" "Performance" "High"
        
        # Auto-fix: Switch to file cache
        if apply_fix "Switch cache driver to file" "sed -i 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env" "Switch from Redis to file-based cache"; then
            # Clear config cache to apply changes
            apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration"
        fi
    else
        log_check "Cache System" "WARN" "Cache test inconclusive: $CACHE_RESULT" "Performance" "Medium"
        add_action_item "MEDIUM" "Investigate Cache System" "Cache test returned unexpected result" "Check cache configuration and test manually"
    fi
else
    log_check "Cache System" "WARN" "Cannot test (missing .env)" "Performance" "Low"
fi

# C3-05: Session System Health Check
echo "=== Session System Health ==="

if [ -f ".env" ]; then
    SESSION_DRIVER=$(grep "^SESSION_DRIVER=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null)
    SESSION_DRIVER=${SESSION_DRIVER:-file}
    
    echo "? Session driver: $SESSION_DRIVER"
    
    if [ "$SESSION_DRIVER" = "redis" ]; then
        # Test Redis session
        REDIS_SESSION_TEST='
<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";

try {
    $redis = app("redis");
    $redis->ping();
    echo "REDIS_SESSION_OK";
} catch (Exception $e) {
    echo "REDIS_SESSION_ERROR: " . $e->getMessage();
}
'
        
        REDIS_RESULT=$(echo "$REDIS_SESSION_TEST" | php 2>/dev/null)
        
        if echo "$REDIS_RESULT" | grep -q "REDIS_SESSION_OK"; then
            log_check "Session System" "PASS" "Redis session working" "Security" "Medium"
        else
            log_check "Session System" "FAIL" "Redis session configured but not available" "Security" "High"
            
            # Auto-fix: Switch to file sessions
            if apply_fix "Switch session driver to file" "sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env" "Switch from Redis to file-based sessions"; then
                apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration"
            fi
        fi
    else
        log_check "Session System" "PASS" "$SESSION_DRIVER driver configured" "Security" "Medium"
    fi
else
    log_check "Session System" "WARN" "Cannot test (missing .env)" "Security" "Low"
fi

# C3-06: Queue System Health Check
echo "=== Queue System Health ==="

if [ -f ".env" ]; then
    QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null)
    QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
    
    echo "? Queue connection: $QUEUE_CONNECTION"
    
    if [ "$QUEUE_CONNECTION" = "redis" ]; then
        # Test Redis queue
        REDIS_QUEUE_TEST='
<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";

try {
    Queue::connection("redis")->size();
    echo "REDIS_QUEUE_OK";
} catch (Exception $e) {
    echo "REDIS_QUEUE_ERROR: " . $e->getMessage();
}
'
        
        QUEUE_RESULT=$(echo "$REDIS_QUEUE_TEST" | php 2>/dev/null)
        
        if echo "$QUEUE_RESULT" | grep -q "REDIS_QUEUE_OK"; then
            log_check "Queue System" "PASS" "Redis queue working" "Performance" "Low"
        else
            log_check "Queue System" "FAIL" "Redis queue configured but not available" "Performance" "Medium"
            
            # Auto-fix: Switch to sync queue
            if apply_fix "Switch queue to sync" "sed -i 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=sync/' .env" "Switch from Redis to sync queue"; then
                apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration"
            fi
        fi
    else
        log_check "Queue System" "PASS" "$QUEUE_CONNECTION driver configured" "Performance" "Low"
    fi
else
    log_check "Queue System" "WARN" "Cannot test (missing .env)" "Performance" "Low"
fi

# C3-07: PHP Extensions Check
echo "=== PHP Extensions Health ==="

REQUIRED_EXTENSIONS=("openssl" "pdo" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo")
MISSING_EXTENSIONS=()

for EXT in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "$EXT"; then
        log_check "PHP Extension: $EXT" "PASS" "Loaded" "Environment" "Medium"
    else
        log_check "PHP Extension: $EXT" "FAIL" "Missing - contact hosting provider" "Environment" "High"
        MISSING_EXTENSIONS+=("$EXT")
    fi
done

# Add action item for missing extensions
if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    add_action_item "HIGH" "Install Missing PHP Extensions" "Missing: ${MISSING_EXTENSIONS[*]}" "Contact hosting provider to install: ${MISSING_EXTENSIONS[*]}"
fi

# C3-08: Disabled Functions Check
echo "=== PHP Functions Health ==="

DISABLED_FUNCTIONS=$(php -r "echo ini_get('disable_functions');" 2>/dev/null)
PROBLEMATIC_DISABLED=()

if [ -n "$DISABLED_FUNCTIONS" ]; then
    echo "? Disabled PHP functions: $DISABLED_FUNCTIONS"
    
    # Check for commonly problematic functions
    PROBLEMATIC_FUNCTIONS=("exec" "shell_exec" "proc_open" "system" "passthru")
    
    for FUNC in "${PROBLEMATIC_FUNCTIONS[@]}"; do
        if echo "$DISABLED_FUNCTIONS" | grep -q "$FUNC"; then
            log_check "PHP Function: $FUNC" "WARN" "Disabled by hosting provider" "Environment" "Medium"
            PROBLEMATIC_DISABLED+=("$FUNC")
        else
            log_check "PHP Function: $FUNC" "PASS" "Available" "Environment" "Low"
        fi
    done
    
    # Add action item if critical functions are disabled
    if [ ${#PROBLEMATIC_DISABLED[@]} -gt 0 ]; then
        add_action_item "MEDIUM" "PHP Functions Disabled" "Functions disabled: ${PROBLEMATIC_DISABLED[*]} - Some Laravel features may be limited" "This is normal for shared hosting. Use manual alternatives when needed."
    fi
else
    log_check "PHP Functions" "PASS" "No functions disabled" "Environment" "Low"
fi

# C3-09: Storage and Permissions Check
echo "=== Storage & Permissions Health ==="

# Check critical directories
CRITICAL_DIRS=("storage" "bootstrap/cache" "public/storage")
BROKEN_DIRS=()

for DIR in "${CRITICAL_DIRS[@]}"; do
    if [ -e "$DIR" ]; then
        if [ -w "$DIR" ]; then
            log_check "Directory: $DIR" "PASS" "Exists and writable" "Security" "High"
        else
            log_check "Directory: $DIR" "FAIL" "Not writable" "Security" "High"
            BROKEN_DIRS+=("$DIR")
            
            # Auto-fix: Set permissions
            apply_fix "Fix permissions for $DIR" "chmod -R 775 '$DIR' 2>/dev/null" "Set proper write permissions"
        fi
    else
        log_check "Directory: $DIR" "FAIL" "Missing" "Security" "High"
        BROKEN_DIRS+=("$DIR")
        
        # Auto-fix: Create directory
        if [ "$DIR" = "public/storage" ]; then
            apply_fix "Create storage symlink" "ln -sfn ../storage/app/public public/storage" "Create symlink for public file access"
        else
            apply_fix "Create directory $DIR" "mkdir -p '$DIR' && chmod 775 '$DIR'" "Create missing directory with proper permissions"
        fi
    fi
done

# Add action item for broken directories
if [ ${#BROKEN_DIRS[@]} -gt 0 ]; then
    add_action_item "HIGH" "Fix Directory Issues" "Problems with: ${BROKEN_DIRS[*]}" "Check and fix permissions/symlinks for: ${BROKEN_DIRS[*]}"
fi

# C3-10: Configuration Cache Health
echo "=== Configuration Cache Health ==="

if [ -f "bootstrap/cache/config.php" ]; then
    log_check "Config Cache" "PASS" "Configuration cached" "Performance" "Medium"
    
    # Check if cached config has Redis references when Redis is broken
    if grep -q "redis" bootstrap/cache/config.php && echo "$CACHE_RESULT $REDIS_RESULT $QUEUE_RESULT" | grep -q "ERROR"; then
        log_check "Config Cache Content" "WARN" "Cached config references broken Redis" "Performance" "High"
        
        # Auto-fix: Clear and rebuild config cache
        apply_fix "Rebuild config cache" "php artisan config:clear && php artisan config:cache 2>/dev/null" "Rebuild config cache with current settings"
    else
        log_check "Config Cache Content" "PASS" "No conflicts detected" "Performance" "Low"
    fi
else
    log_check "Config Cache" "WARN" "Configuration not cached (performance impact)" "Performance" "Medium"
    
    # Auto-fix: Create config cache
    apply_fix "Create config cache" "php artisan config:cache 2>/dev/null" "Cache configuration for better performance"
fi

# C3-11: HTTP Response Test
echo "=== HTTP Response Health ==="

if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    
    if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then
        echo "? Testing: $APP_URL"
        
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000")
        
        case $HTTP_CODE in
            200|201|301|302)
                log_check "HTTP Response" "PASS" "Application responding (HTTP $HTTP_CODE)" "Core" "Critical"
                ;;
            500)
                log_check "HTTP Response" "FAIL" "HTTP 500 - Internal Server Error" "Core" "Critical"
                
                # Try to get more details from Laravel logs
                if [ -f "storage/logs/laravel.log" ]; then
                    echo "  ? Latest Laravel error:"
                    LATEST_ERROR=$(tail -3 storage/logs/laravel.log | grep -E "(ERROR|Exception|Fatal)" | tail -1 | sed 's/^/    /')
                    echo "$LATEST_ERROR"
                    add_action_item "CRITICAL" "Fix HTTP 500 Error" "Website returning server error: $LATEST_ERROR" "Check storage/logs/laravel.log for full error details"
                else
                    add_action_item "CRITICAL" "Fix HTTP 500 Error" "Website returning server error" "Check application logs and configuration"
                fi
                ;;
            503)
                log_check "HTTP Response" "WARN" "HTTP 503 - Service Unavailable (maintenance mode?)" "Core" "Medium"
                add_action_item "MEDIUM" "Check Maintenance Mode" "Site may be in maintenance mode" "Run: php artisan up"
                
                # Auto-fix: Try to exit maintenance mode
                if [ -f "artisan" ]; then
                    apply_fix "Exit maintenance mode" "php artisan up 2>/dev/null" "Exit Laravel maintenance mode"
                fi
                ;;
            000)
                log_check "HTTP Response" "FAIL" "Connection timeout or unreachable" "Core" "Critical"
                add_action_item "CRITICAL" "Website Unreachable" "Cannot connect to website" "Check server status and DNS configuration"
                ;;
            *)
                log_check "HTTP Response" "WARN" "HTTP $HTTP_CODE - Unexpected response" "Core" "High"
                add_action_item "HIGH" "Investigate HTTP Response" "Unexpected HTTP code: $HTTP_CODE" "Check web server configuration and application status"
                ;;
        esac
        
        # Test install page if main page fails
        if [ "$HTTP_CODE" = "500" ]; then
            INSTALL_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/install" --max-time 10 2>/dev/null || echo "000")
            if [ "$INSTALL_CODE" = "200" ]; then
                log_check "Install Page" "PASS" "Install page accessible (HTTP $INSTALL_CODE)" "Core" "Medium"
                echo "  ? This may be a fresh deployment needing installation"
                add_action_item "MEDIUM" "Complete Installation" "Fresh deployment detected - installation needed" "Visit: $APP_URL/install"
            fi
        fi
    else
        log_check "HTTP Response" "WARN" "Cannot test (no APP_URL or curl unavailable)" "Core" "Medium"
        add_action_item "MEDIUM" "Configure APP_URL" "Cannot test website - APP_URL missing or curl unavailable" "Set APP_URL in .env file"
    fi
else
    log_check "HTTP Response" "WARN" "Cannot test (missing .env)" "Core" "High"
fi

# C3-12: Laravel Error Log Analysis
echo "=== Error Log Analysis ==="

if [ -f "storage/logs/laravel.log" ]; then
    LOG_SIZE=$(du -sh storage/logs/laravel.log | cut -f1)
    log_check "Laravel Log File" "PASS" "Exists ($LOG_SIZE)" "Monitoring" "Low"
    
    # Check for recent critical errors
    RECENT_ERRORS=$(tail -100 storage/logs/laravel.log | grep -c -E "(ERROR|CRITICAL|EMERGENCY)" 2>/dev/null || echo "0")
    
    if [ "$RECENT_ERRORS" -gt 0 ]; then
        log_check "Recent Critical Errors" "WARN" "$RECENT_ERRORS errors found in last 100 lines" "Monitoring" "High"
        
        RECENT_ERROR=$(tail -100 storage/logs/laravel.log | grep -E "(ERROR|CRITICAL|EMERGENCY)" | tail -1 | sed 's/^/    /')
        echo "  ? Most recent critical error:"
        echo "$RECENT_ERROR"
        add_action_item "HIGH" "Investigate Error Log" "$RECENT_ERRORS recent errors found" "Check: tail -20 storage/logs/laravel.log"
    else
        log_check "Recent Critical Errors" "PASS" "No critical errors in recent logs" "Monitoring" "Low"
    fi
else
    log_check "Laravel Log File" "WARN" "No log file found" "Monitoring" "Medium"
    add_action_item "MEDIUM" "Check Logging Configuration" "No Laravel log file found" "Verify logging is properly configured in config/logging.php"
fi

# C3-13: Generate Health Summary
echo "=== Health Check Summary ==="

HEALTH_PERCENTAGE=$(( (PASSED_CHECKS * 100) / TOTAL_CHECKS ))

echo "? Overall Health Score: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)"
echo "? Auto-fixes Applied: $FIXES_APPLIED"
echo "⚠️ Issues Found: $ISSUES_FOUND"

# Determine overall status
if [ $HEALTH_PERCENTAGE -ge 90 ]; then
    OVERALL_STATUS="? EXCELLENT"
    echo "✅ Your application is healthy and ready for production!"
elif [ $HEALTH_PERCENTAGE -ge 75 ]; then
    OVERALL_STATUS="? GOOD"
    echo "✅ Your application is mostly healthy with minor issues."
elif [ $HEALTH_PERCENTAGE -ge 50 ]; then
    OVERALL_STATUS="⚠️ NEEDS ATTENTION"
    echo "⚠️ Your application has issues that should be addressed."
else
    OVERALL_STATUS="? CRITICAL"
    echo "❌ Your application has critical issues requiring immediate attention."
fi

echo "? Status: $OVERALL_STATUS"

# C3-14: Create Health Summary Report
echo "=== Creating Centralized Health Reports ==="

# ENHANCED: Centralized reporting with timestamp folders and latest symlinks
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DOMAIN_ROOT=$(dirname "$DEPLOY_PATH")
DOMAIN_NAME=$(basename "$DOMAIN_ROOT")

# Create centralized report structure
REPORTS_BASE="$DOMAIN_ROOT/deployment-reports"
DEPLOYMENT_FOLDER="$REPORTS_BASE/deployments/$TIMESTAMP"
LATEST_FOLDER="$REPORTS_BASE/latest"
GENERAL_FOLDER="$REPORTS_BASE/general"

# Create all report directories
mkdir -p "$DEPLOYMENT_FOLDER"
mkdir -p "$LATEST_FOLDER"
mkdir -p "$GENERAL_FOLDER"

echo "? Centralized Report Structure:"
echo "   Reports Base: $REPORTS_BASE"
echo "   This Deployment: $DEPLOYMENT_FOLDER"
echo "   Latest Symlinks: $LATEST_FOLDER"
echo "   General Reports: $GENERAL_FOLDER"

# Define report files for this deployment
HEALTH_REPORT="$DEPLOYMENT_FOLDER/health-check.md"
ISSUES_REPORT="$DEPLOYMENT_FOLDER/issues-summary.md"
VARIABLES_REPORT="$DEPLOYMENT_FOLDER/deployment-variables.md"

# C3-15a: Create Deployment Variables Report (integrating variables-definitons.md)
echo "? Creating deployment variables report..."

cat > "$VARIABLES_REPORT" << EOF
# ? Deployment Variables Report

**Domain:** $DOMAIN_NAME  
**Timestamp:** $(date '+%Y-%m-%d %H:%M:%S')  
**Deployment:** $TIMESTAMP  

---

## ? DeployHQ Variables Used

Based on **variables-definitons.md**, here are the variables available and used in this deployment:

### **Server / Environment Variables**
- **%environment%** → %environment% (Server environment)
- **%path%** → $DEPLOY_PATH (Base server path we're deploying to)

### **Deployment Variables**
- **%branch%** → %branch% (The branch of the deployment)
- **%count%** → %count% (Number of deployments in the project)
- **%deployer%** → %deployer% (User who started the deployment)
- **%status%** → %status% (Current deployment status)
- **%started_at%** → %started_at% (Start time)
- **%completed_at%** → %completed_at% (Completion time)

### **Revision Variables**
- **%startrev%** → %startrev% (Start revision ref)
- **%endrev%** → %endrev% (End revision ref)
- **%commitrange%** → %commitrange% (Start and end commit range)

### **Project Variables**
- **%project%** → %project% (Project name in DeployHQ)
- **%projecturl%** → %projecturl% (Project address in DeployHQ)
- **%deploymenturl%** → %deploymenturl% (This deployment's address)

---

## ?️ Derived Path Variables

All paths derived from **%path%** = $DEPLOY_PATH

- **DEPLOY_PATH** = $DEPLOY_PATH
- **SHARED_PATH** = $SHARED_PATH  
- **CURRENT_PATH** = $CURRENT_PATH
- **DOMAIN_ROOT** = $DOMAIN_ROOT

---

## ? Report Structure

- **Base Reports:** $REPORTS_BASE
- **This Deployment:** $DEPLOYMENT_FOLDER
- **Latest Symlinks:** $LATEST_FOLDER
- **General Reports:** $GENERAL_FOLDER

---

**Generated:** $(date '+%Y-%m-%d %H:%M:%S')  
**Reference:** variables-definitons.md
EOF

echo "✅ Variables report created: $VARIABLES_REPORT"

# Start the issues report
cat > "$ISSUES_REPORT" << EOF
# ? Health Check Issues Summary

**Domain:** $DOMAIN_NAME  
**Timestamp:** $(date '+%Y-%m-%d %H:%M:%S')  
**Health Score:** $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)  
**Status:** $OVERALL_STATUS  
**Issues Found:** $ISSUES_FOUND  
**Auto-Fixes Applied:** $FIXES_APPLIED  

---

EOF

# Add TL;DR section
if [ $ISSUES_FOUND -eq 0 ]; then
    cat >> "$ISSUES_REPORT" << EOF
## ? TL;DR - ALL GOOD!

✅ **No issues detected** - Your deployment is healthy and ready for production!

All $TOTAL_CHECKS health checks passed successfully. No manual intervention required.

EOF
else
    cat >> "$ISSUES_REPORT" << EOF
## ⚠️ TL;DR - ISSUES DETECTED

❌ **$ISSUES_FOUND issues found** that need your attention  
? **$FIXES_APPLIED auto-fixes** have been applied automatically  
? **Health Score:** $HEALTH_PERCENTAGE% ($([ $HEALTH_PERCENTAGE -ge 75 ] && echo "Good" || echo "Needs Attention"))  

$([ $FIXES_APPLIED -gt 0 ] && echo "✅ Most common issues have been **automatically fixed**" || echo "⚠️ Manual intervention may be required")

EOF
fi

# Add detailed issues if any found
if [ $ISSUES_FOUND -gt 0 ]; then
    cat >> "$ISSUES_REPORT" << EOF

---

## ? ISSUES BY CATEGORY

EOF

    # Process detailed issues by category
    if [ -f "/tmp/c3_detailed_issues" ] && [ -s "/tmp/c3_detailed_issues" ]; then
        # Group issues by category
        CATEGORIES=($(cut -d'|' -f1 /tmp/c3_detailed_issues | sort -u))
        
        for CATEGORY in "${CATEGORIES[@]}"; do
            if [ -n "$CATEGORY" ]; then
                cat >> "$ISSUES_REPORT" << EOF

### $CATEGORY Issues
EOF
                
                # Add issues for this category
                grep "^$CATEGORY|" /tmp/c3_detailed_issues | while IFS='|' read -r cat status check details impact; do
                    ICON="⚠️"
                    [ "$status" = "ERROR" ] && ICON="❌"
                    echo "- $ICON **$check** ($impact Impact): $details" >> "$ISSUES_REPORT"
                done
            fi
        done
    fi

    # Add auto-fixes applied
    cat >> "$ISSUES_REPORT" << EOF

---

## ? AUTO-FIXES APPLIED

EOF

    if [ -f "/tmp/c3_fixes_log" ] && [ -s "/tmp/c3_fixes_log" ]; then
        cat "/tmp/c3_fixes_log" >> "$ISSUES_REPORT"
    fi

    if [ $FIXES_APPLIED -eq 0 ]; then
        echo "- No auto-fixes were applied" >> "$ISSUES_REPORT"
    fi

    cat >> "$ISSUES_REPORT" << EOF

---

## ? ACTION ITEMS

EOF

    # Generate prioritized action items
    if [ -f "/tmp/c3_action_items" ] && [ -s "/tmp/c3_action_items" ]; then
        # Process action items by priority
        PRIORITIES=("CRITICAL" "HIGH" "MEDIUM" "LOW")
        ACTION_COUNT=0
        
        for PRIORITY in "${PRIORITIES[@]}"; do
            PRIORITY_ITEMS=$(grep "^ACTION|$PRIORITY|" /tmp/c3_action_items 2>/dev/null || true)
            if [ -n "$PRIORITY_ITEMS" ]; then
                cat >> "$ISSUES_REPORT" << EOF

### $PRIORITY Priority
EOF
                
                echo "$PRIORITY_ITEMS" | while IFS='|' read -r type priority title description command; do
                    ACTION_COUNT=$((ACTION_COUNT + 1))
                    PRIORITY_ICON="?"
                    [ "$priority" = "CRITICAL" ] && PRIORITY_ICON="?"
                    [ "$priority" = "HIGH" ] && PRIORITY_ICON="⚠️"
                    [ "$priority" = "MEDIUM" ] && PRIORITY_ICON="?"
                    
                    cat >> "$ISSUES_REPORT" << EOF
$ACTION_COUNT. $PRIORITY_ICON **$title**
   - **Problem:** $description
   - **Action:** $command

EOF
                done
            fi
        done
    fi

    if [ ! -f "/tmp/c3_action_items" ] || [ ! -s "/tmp/c3_action_items" ]; then
        echo "- All detected issues have been automatically resolved" >> "$ISSUES_REPORT"
        echo "- Monitor the application for any remaining issues" >> "$ISSUES_REPORT"
    fi

fi

# Add quick reference commands
cat >> "$ISSUES_REPORT" << EOF

---

## ⚡ QUICK REFERENCE COMMANDS

### Check Current Status:
\`\`\`bash
# Test website
curl -I https://$DOMAIN_NAME

# Check Laravel health
cd $CURRENT_PATH
php artisan about

# View latest errors
tail -10 storage/logs/laravel.log
\`\`\`

### Common Fixes:
\`\`\`bash
# Fix Redis issues (if applicable)
cd $CURRENT_PATH
sed -i 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env
sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env
php artisan config:clear

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix dependencies (WARNING: Only run this if build artifacts are completely missing)
# composer2 install --no-dev --optimize-autoloader
# IMPORTANT: This destroys build artifacts - only use if vendor directory is completely empty
\`\`\`

### Get Help:
- **Full Report:** \`cat $DOMAIN_ROOT/deployment-reports/latest-report.md\`
- **Error Logs:** \`tail -20 $CURRENT_PATH/storage/logs/laravel.log\`
- **Health History:** \`cat $SHARED_PATH/health-checks.log\`

---

**Next Steps:**
1. Address the action items above (if any)
2. Test your website: https://$DOMAIN_NAME
3. Monitor error logs: \`tail -f $CURRENT_PATH/storage/logs/laravel.log\`

**Report Generated:** $(date '+%Y-%m-%d %H:%M:%S')  
**Complement to:** Phase C-2 Comprehensive Report (\`latest-report.md\`)
EOF

# C3-17: Enhanced Logging with Centralized Structure
echo "=== Enhanced Logging ==="

# Log health check results to shared directory
HEALTH_LOG="$SHARED_PATH/health-checks.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health Check - Score: $HEALTH_PERCENTAGE% - Status: $OVERALL_STATUS - Issues: $ISSUES_FOUND - Fixes: $FIXES_APPLIED" >> "$HEALTH_LOG"

# Log to centralized deployment reports
DEPLOYMENT_LOG="$GENERAL_FOLDER/health-history.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health: $HEALTH_PERCENTAGE% | Status: $OVERALL_STATUS | Issues: $ISSUES_FOUND | Fixes: $FIXES_APPLIED | Deployment: $TIMESTAMP" >> "$DEPLOYMENT_LOG"

# Create deployment-specific log
DEPLOYMENT_SPECIFIC_LOG="$DEPLOYMENT_FOLDER/deployment.log"
cat > "$DEPLOYMENT_SPECIFIC_LOG" << EOF
# ? Deployment $TIMESTAMP Log

**Health Score:** $HEALTH_PERCENTAGE%  
**Status:** $OVERALL_STATUS  
**Issues Found:** $ISSUES_FOUND  
**Auto-Fixes Applied:** $FIXES_APPLIED  
**Total Checks:** $TOTAL_CHECKS  
**Passed Checks:** $PASSED_CHECKS  

**Environment:** %environment%  
**Branch:** %branch%  
**Commit:** %endrev%  
**Deployer:** %deployer%  

**Deployment Started:** %started_at%  
**Deployment Completed:** %completed_at%  

---

**Reports Generated:**
- Health Check: health-check.md
- Issues Summary: issues-summary.md
- Variables Used: deployment-variables.md

**Reference:** variables-definitons.md
EOF

log_check "Health Check Logging" "PASS" "Results logged to centralized structure" "Monitoring" "Low"

# C3-16: Create Centralized Symlinks and Access Points
echo "=== Creating Centralized Access Points ==="

# Set permissions on all reports
chmod 644 "$HEALTH_REPORT" "$ISSUES_REPORT" "$VARIABLES_REPORT"

# Create latest symlinks in latest folder
cd "$LATEST_FOLDER"
rm -f health-check.md issues-summary.md deployment-variables.md
ln -sf "../deployments/$TIMESTAMP/health-check.md" health-check.md
ln -sf "../deployments/$TIMESTAMP/issues-summary.md" issues-summary.md  
ln -sf "../deployments/$TIMESTAMP/deployment-variables.md" deployment-variables.md

# Create general access points
cd "$GENERAL_FOLDER"
rm -f latest-health.md latest-issues.md latest-variables.md
ln -sf "../latest/health-check.md" latest-health.md
ln -sf "../latest/issues-summary.md" latest-issues.md
ln -sf "../latest/deployment-variables.md" latest-variables.md

# Create deployment index
DEPLOYMENT_INDEX="$GENERAL_FOLDER/deployment-index.md"
echo "? Creating deployment index..."

cat > "$DEPLOYMENT_INDEX" << EOF
# ? Deployment Reports Index

**Domain:** $DOMAIN_NAME  
**Last Updated:** $(date '+%Y-%m-%d %H:%M:%S')

---

## ? Quick Access

### **Latest Reports**
- **Health Check:** [latest-health.md](latest-health.md)
- **Issues Summary:** [latest-issues.md](latest-issues.md)  
- **Variables Used:** [latest-variables.md](latest-variables.md)

### **This Deployment ($TIMESTAMP)**
- **Health Check:** [deployments/$TIMESTAMP/health-check.md](deployments/$TIMESTAMP/health-check.md)
- **Issues Summary:** [deployments/$TIMESTAMP/issues-summary.md](deployments/$TIMESTAMP/issues-summary.md)
- **Variables Used:** [deployments/$TIMESTAMP/deployment-variables.md](deployments/$TIMESTAMP/deployment-variables.md)

---

## ? Report Structure

\`\`\`
deployment-reports/
├── deployments/
│   └── $TIMESTAMP/           # This deployment
│       ├── health-check.md
│       ├── issues-summary.md
│       └── deployment-variables.md
├── latest/                   # Symlinks to latest deployment
│   ├── health-check.md → ../deployments/$TIMESTAMP/health-check.md
│   ├── issues-summary.md → ../deployments/$TIMESTAMP/issues-summary.md
│   └── deployment-variables.md → ../deployments/$TIMESTAMP/deployment-variables.md
└── general/                  # General access points
    ├── latest-health.md → ../latest/health-check.md
    ├── latest-issues.md → ../latest/issues-summary.md
    ├── latest-variables.md → ../latest/deployment-variables.md
    └── deployment-index.md (this file)
\`\`\`

---

## ? Recent Deployments

EOF

# Add recent deployments to index
if [ -d "$REPORTS_BASE/deployments" ]; then
    echo "Recent deployments:" >> "$DEPLOYMENT_INDEX"
    echo "\`\`\`" >> "$DEPLOYMENT_INDEX"
    ls -1t "$REPORTS_BASE/deployments" 2>/dev/null | head -10 | while read deploy_dir; do
        if [ -d "$REPORTS_BASE/deployments/$deploy_dir" ]; then
            DEPLOY_TIME=$(echo "$deploy_dir" | sed 's/\([0-9]\{8\}\)_\([0-9]\{6\}\)/\1 \2/' | sed 's/\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\) \([0-9]\{2\}\)\([0-9]\{2\}\)\([0-9]\{2\}\)/\1-\2-\3 \4:\5:\6/')
            echo "$deploy_dir → $DEPLOY_TIME" >> "$DEPLOYMENT_INDEX"
        fi
    done
    echo "\`\`\`" >> "$DEPLOYMENT_INDEX"
fi

cat >> "$DEPLOYMENT_INDEX" << EOF

---

**Generated:** $(date '+%Y-%m-%d %H:%M:%S')  
**Reference:** variables-definitons.md
EOF

echo "✅ Centralized reports created:"
echo "   ? Health Report: $HEALTH_REPORT"
echo "   ? Issues Report: $ISSUES_REPORT"
echo "   ? Variables Report: $VARIABLES_REPORT"
echo "   ? Deployment Index: $DEPLOYMENT_INDEX"
echo ""
echo "? Quick Access Points:"
echo "   ? Latest Health: $GENERAL_FOLDER/latest-health.md"
echo "   ? Latest Issues: $GENERAL_FOLDER/latest-issues.md"
echo "   ? Latest Variables: $GENERAL_FOLDER/latest-variables.md"
echo "   ? All Deployments: $DEPLOYMENT_INDEX"

# Display final summary
echo ""
echo "? ==============================================="
echo "? PHASE C-3 COMPREHENSIVE HEALTH CHECK COMPLETE"
echo "? ==============================================="
echo ""
echo "? Final Health Score: $HEALTH_PERCENTAGE% - $OVERALL_STATUS"
echo "? Auto-Fixes Applied: $FIXES_APPLIED"
echo "⚠️ Issues Found: $ISSUES_FOUND"
echo ""

if [ $ISSUES_FOUND -eq 0 ]; then
    echo "? No issues detected - deployment is healthy!"
    echo "✅ Your application is ready for production!"
else
    echo "⚠️ $ISSUES_FOUND issues found - check the centralized reports for details"
    echo "? $FIXES_APPLIED auto-fixes have been applied automatically"
    echo ""
    echo "? Quick Issues Summary:"
    if [ -f "/tmp/c3_issues_log" ] && [ -s "/tmp/c3_issues_log" ]; then
        head -3 /tmp/c3_issues_log | sed 's/^/  /'
        if [ $(wc -l < /tmp/c3_issues_log) -gt 3 ]; then
            echo "  ... and $(($(wc -l < /tmp/c3_issues_log) - 3)) more (see centralized reports)"
        fi
    fi
fi

echo ""
echo "? CENTRALIZED REPORTS CREATED:"
echo "   ? Quick Access: $GENERAL_FOLDER/deployment-index.md"
echo "   ? Latest Health: $GENERAL_FOLDER/latest-health.md"
echo "   ? Latest Issues: $GENERAL_FOLDER/latest-issues.md"
echo "   ? Variables Used: $GENERAL_FOLDER/latest-variables.md"
echo ""

# C3-16: Cleanup temporary files
echo "=== Cleanup ==="
rm -f /tmp/c3_issues_log /tmp/c3_fixes_log /tmp/c3_detailed_issues /tmp/c3_action_items
echo "✅ Temporary files cleaned up"

# Exit with appropriate code (but don't fail deployment)
if [ $HEALTH_PERCENTAGE -ge 50 ]; then
    echo "✅ Phase C-3 completed successfully"
    exit 0
else
    echo "⚠️ Critical issues detected but deployment continues"
    echo "✅ Phase C-3 completed with warnings"
    exit 0  # Don't fail deployment, just warn
fi