#!/bin/bash
set -e

# Phase C-3: Comprehensive Health Check & Auto-Fix
# Purpose: Debug HTTP 500, ensure app functionality, auto-fix common issues
# Version 2.1 - PRODUCTION READY with Enhanced Reporting
# Run after Phase C to verify deployment health and auto-fix issues
echo "=== Phase C-3: Comprehensive Health Check & Auto-Fix ==="

cd %current_path%

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
    
    echo "🔧 Applying fix: $FIX_NAME"
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

echo "🔍 Starting comprehensive health checks..."

# C3-01: Basic Laravel Health Check
echo "=== Laravel Framework Health ==="

if [ -f "artisan" ]; then
    if php artisan --version >/dev/null 2>&1; then
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1)
        log_check "Laravel Framework" "PASS" "$LARAVEL_VERSION" "Core" "High"
    else
        log_check "Laravel Framework" "FAIL" "Artisan command failed" "Core" "Critical"
        # Try to get more info
        echo "  Debug: $(php artisan --version 2>&1 | head -1)"
        add_action_item "HIGH" "Fix Laravel Framework" "Artisan command failing - check PHP configuration and dependencies" "php artisan --version"
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
        echo "  Debug: $DB_TEST_RESULT"
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
    
    echo "🔍 Cache driver: $CACHE_DRIVER"
    
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
    
    echo "🔍 Session driver: $SESSION_DRIVER"
    
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
    
    echo "🔍 Queue connection: $QUEUE_CONNECTION"
    
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
    echo "🔍 Disabled PHP functions: $DISABLED_FUNCTIONS"
    
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
        echo "🔍 Testing: $APP_URL"
        
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000")
        
        case $HTTP_CODE in
            200|201|301|302)
                log_check "HTTP Response" "PASS" "Application responding (HTTP $HTTP_CODE)" "Core" "Critical"
                ;;
            500)
                log_check "HTTP Response" "FAIL" "HTTP 500 - Internal Server Error" "Core" "Critical"
                
                # Try to get more details from Laravel logs
                if [ -f "storage/logs/laravel.log" ]; then
                    echo "  🔍 Latest Laravel error:"
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
                echo "  💡 This may be a fresh deployment needing installation"
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
        echo "  🔍 Most recent critical error:"
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

echo "📊 Overall Health Score: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)"
echo "🔧 Auto-fixes Applied: $FIXES_APPLIED"
echo "⚠️ Issues Found: $ISSUES_FOUND"

# Determine overall status
if [ $HEALTH_PERCENTAGE -ge 90 ]; then
    OVERALL_STATUS="🟢 EXCELLENT"
    echo "✅ Your application is healthy and ready for production!"
elif [ $HEALTH_PERCENTAGE -ge 75 ]; then
    OVERALL_STATUS="🟡 GOOD"
    echo "✅ Your application is mostly healthy with minor issues."
elif [ $HEALTH_PERCENTAGE -ge 50 ]; then
    OVERALL_STATUS="🟠 NEEDS ATTENTION"
    echo "⚠️ Your application has issues that should be addressed."
else
    OVERALL_STATUS="🔴 CRITICAL"
    echo "❌ Your application has critical issues requiring immediate attention."
fi

echo "🎯 Status: $OVERALL_STATUS"

# C3-14: Actionable Recommendations
echo "=== Actionable Recommendations ==="

if [ $ISSUES_FOUND -gt 0 ]; then
    echo "🔧 Based on the health check results, here are specific actions to take:"
    echo ""
    
    # Provide specific recommendations based on findings
    if echo "$CACHE_RESULT $REDIS_RESULT $QUEUE_RESULT" | grep -q "ERROR.*Redis"; then
        echo "1. **Redis Configuration Issue Detected**"
        echo "   - Redis is configured but not available on this server"
        echo "   - Auto-fixes applied: Switched to file-based cache/sessions"
        echo "   - Action: Verify changes in .env file"
        echo ""
    fi
    
    if [ "$HTTP_CODE" = "500" ]; then
        echo "2. **HTTP 500 Error Active**"
        echo "   - Your website is returning server errors"
        echo "   - Check the Laravel error log above for specific details"
        echo "   - Action: Review storage/logs/laravel.log for error details"
        echo ""
    fi
    
    if echo "$DISABLED_FUNCTIONS" | grep -q "exec\|proc_open"; then
        echo "3. **Disabled PHP Functions**"
        echo "   - Some PHP functions are disabled by your hosting provider"
        echo "   - This is normal for shared hosting security"
        echo "   - Action: Ensure auto-fixes have been applied for storage links"
        echo ""
    fi
    
    echo "4. **General Recommendations**"
    echo "   - Monitor the Laravel error log: tail -f storage/logs/laravel.log"
    echo "   - Test your site: curl -I $APP_URL"
    echo "   - If issues persist, contact your hosting provider"
    echo ""
else
    echo "🎉 No issues found! Your application is healthy and ready for production."
fi

# C3-15: Create Comprehensive Issues Summary Report
echo "=== Creating Issues Summary Report ==="

# Initialize report paths (same folder as Phase C-2)
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DOMAIN_ROOT=$(dirname "%path%")
DOMAIN_NAME=$(basename "$DOMAIN_ROOT")
REPORT_DIR="$DOMAIN_ROOT/deployment-reports"
ISSUES_REPORT="$REPORT_DIR/health-issues-$TIMESTAMP.md"

# Create report directory if it doesn't exist
mkdir -p "$REPORT_DIR"

# Start the issues report
cat > "$ISSUES_REPORT" << EOF
# 🔍 Health Check Issues Summary

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
## 🎉 TL;DR - ALL GOOD!

✅ **No issues detected** - Your deployment is healthy and ready for production!

All $TOTAL_CHECKS health checks passed successfully. No manual intervention required.

EOF
else
    cat >> "$ISSUES_REPORT" << EOF
## ⚠️ TL;DR - ISSUES DETECTED

❌ **$ISSUES_FOUND issues found** that need your attention  
🔧 **$FIXES_APPLIED auto-fixes** have been applied automatically  
📊 **Health Score:** $HEALTH_PERCENTAGE% ($([ $HEALTH_PERCENTAGE -ge 75 ] && echo "Good" || echo "Needs Attention"))  

$([ $FIXES_APPLIED -gt 0 ] && echo "✅ Most common issues have been **automatically fixed**" || echo "⚠️ Manual intervention may be required")

EOF
fi

# Add detailed issues if any found
if [ $ISSUES_FOUND -gt 0 ]; then
    cat >> "$ISSUES_REPORT" << EOF

---

## 🚨 ISSUES BY CATEGORY

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

    # Add specific issue analysis based on what we detected
    cat >> "$ISSUES_REPORT" << EOF

---

## 🔧 AUTO-FIXES APPLIED

EOF

    if [ -f "/tmp/c3_fixes_log" ] && [ -s "/tmp/c3_fixes_log" ]; then
        cat "/tmp/c3_fixes_log" >> "$ISSUES_REPORT"
    fi

    if [ $FIXES_APPLIED -eq 0 ]; then
        echo "- No auto-fixes were applied" >> "$ISSUES_REPORT"
    fi

    cat >> "$ISSUES_REPORT" << EOF

---

## 🎯 ACTION ITEMS

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
                    PRIORITY_ICON="🔸"
                    [ "$priority" = "CRITICAL" ] && PRIORITY_ICON="🔴"
                    [ "$priority" = "HIGH" ] && PRIORITY_ICON="🟠"
                    [ "$priority" = "MEDIUM" ] && PRIORITY_ICON="🟡"
                    
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

## 🚀 QUICK REFERENCE COMMANDS

### Check Current Status:
\`\`\`bash
# Test website
curl -I https://$DOMAIN_NAME

# Check Laravel health
cd %current_path%
php artisan about

# View latest errors
tail -10 storage/logs/laravel.log
\`\`\`

### Common Fixes:
\`\`\`bash
# Fix Redis issues (if applicable)
cd %current_path%
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
\`\`\`

### Get Help:
- **Full Report:** \`cat $DOMAIN_ROOT/deployment-reports/latest-report.md\`
- **Error Logs:** \`tail -20 %current_path%/storage/logs/laravel.log\`
- **Health History:** \`cat %shared_path%/health-checks.log\`

---

**Next Steps:**
1. Address the action items above (if any)
2. Test your website: https://$DOMAIN_NAME
3. Monitor error logs: \`tail -f %current_path%/storage/logs/laravel.log\`

**Report Generated:** $(date '+%Y-%m-%d %H:%M:%S')  
**Complement to:** Phase C-2 Comprehensive Report (\`latest-report.md\`)
EOF

# C3-16: Create HTML Report for Enhanced Viewing
echo "=== Creating HTML Report ==="

HTML_REPORT="$REPORT_DIR/health-issues-$TIMESTAMP.html"

cat > "$HTML_REPORT" << EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Check Issues - $DOMAIN_NAME</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 40px;
            background: #f8f9fa;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .excellent { color: #28a745; }
        .good { color: #17a2b8; }
        .warning { color: #ffc107; }
        .critical { color: #dc3545; }
        
        .content {
            padding: 40px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .tldr {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .tldr.issues {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .tldr h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .issues-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .issue-category {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            border-left: 5px solid #dc3545;
        }
        
        .issue-category h4 {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .issue-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #dc3545;
        }
        
        .issue-item.warning {
            border-left-color: #ffc107;
        }
        
        .fix-item {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .fix-item.failed {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .action-items {
            display: grid;
            gap: 15px;
        }
        
        .action-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 5px solid #667eea;
        }
        
        .action-item.critical {
            border-left-color: #dc3545;
        }
        
        .action-item.high {
            border-left-color: #fd7e14;
        }
        
        .action-item.medium {
            border-left-color: #ffc107;
        }
        
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.9rem;
            margin: 15px 0;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .tab.active {
            border-bottom-color: #667eea;
            color: #667eea;
            font-weight: 600;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            border-top: 1px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .stats {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .content {
                padding: 20px;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .tab {
                flex: 1;
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Health Check Report</h1>
            <div class="subtitle">$DOMAIN_NAME • $(date '+%Y-%m-%d %H:%M:%S')</div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number $([ $HEALTH_PERCENTAGE -ge 90 ] && echo "excellent" || [ $HEALTH_PERCENTAGE -ge 75 ] && echo "good" || [ $HEALTH_PERCENTAGE -ge 50 ] && echo "warning" || echo "critical")">$HEALTH_PERCENTAGE%</div>
                <div class="stat-label">Health Score</div>
            </div>
            <div class="stat-card">
                <div class="stat-number $([ $ISSUES_FOUND -eq 0 ] && echo "excellent" || echo "warning")">$ISSUES_FOUND</div>
                <div class="stat-label">Issues Found</div>
            </div>
            <div class="stat-card">
                <div class="stat-number $([ $FIXES_APPLIED -gt 0 ] && echo "good" || echo "warning")">$FIXES_APPLIED</div>
                <div class="stat-label">Auto-Fixes Applied</div>
            </div>
            <div class="stat-card">
                <div class="stat-number good">$PASSED_CHECKS/$TOTAL_CHECKS</div>
                <div class="stat-label">Checks Passed</div>
            </div>
        </div>
        
        <div class="content">
EOF

# Add TL;DR section to HTML
if [ $ISSUES_FOUND -eq 0 ]; then
    cat >> "$HTML_REPORT" << EOF
            <div class="tldr">
                <h3>🎉 TL;DR - ALL GOOD!</h3>
                <p><strong>No issues detected</strong> - Your deployment is healthy and ready for production!</p>
                <p>All $TOTAL_CHECKS health checks passed successfully. No manual intervention required.</p>
            </div>
EOF
else
    cat >> "$HTML_REPORT" << EOF
            <div class="tldr issues">
                <h3>⚠️ TL;DR - ISSUES DETECTED</h3>
                <p><strong>$ISSUES_FOUND issues found</strong> that need your attention</p>
                <p><strong>$FIXES_APPLIED auto-fixes</strong> have been applied automatically</p>
                <p><strong>Health Score:</strong> $HEALTH_PERCENTAGE% ($([ $HEALTH_PERCENTAGE -ge 75 ] && echo "Good" || echo "Needs Attention"))</p>
                $([ $FIXES_APPLIED -gt 0 ] && echo "<p>✅ Most common issues have been <strong>automatically fixed</strong></p>" || echo "<p>⚠️ Manual intervention may be required</p>")
            </div>
EOF
fi

# Add tabbed content
cat >> "$HTML_REPORT" << EOF
            <div class="tabs">
                <button class="tab active" onclick="showTab('issues')">Issues by Category</button>
                <button class="tab" onclick="showTab('fixes')">Auto-Fixes Applied</button>
                <button class="tab" onclick="showTab('actions')">Action Items</button>
                <button class="tab" onclick="showTab('commands')">Quick Commands</button>
            </div>
            
            <div id="issues" class="tab-content active">
                <div class="section">
EOF

if [ $ISSUES_FOUND -gt 0 ] && [ -f "/tmp/c3_detailed_issues" ] && [ -s "/tmp/c3_detailed_issues" ]; then
    # Group issues by category for HTML
    CATEGORIES=($(cut -d'|' -f1 /tmp/c3_detailed_issues | sort -u))
    
    for CATEGORY in "${CATEGORIES[@]}"; do
        if [ -n "$CATEGORY" ]; then
            cat >> "$HTML_REPORT" << EOF
                    <div class="issue-category">
                        <h4>$CATEGORY Issues</h4>
EOF
            
            # Add issues for this category
            grep "^$CATEGORY|" /tmp/c3_detailed_issues | while IFS='|' read -r cat status check details impact; do
                ISSUE_CLASS="issue-item"
                [ "$status" = "WARNING" ] && ISSUE_CLASS="issue-item warning"
                
                cat >> "$HTML_REPORT" << EOF
                        <div class="$ISSUE_CLASS">
                            <strong>$check</strong> ($impact Impact)<br>
                            $details
                        </div>
EOF
            done
            
            echo "                    </div>" >> "$HTML_REPORT"
        fi
    done
else
    cat >> "$HTML_REPORT" << EOF
                    <div class="issue-category">
                        <h4>🎉 No Issues Found</h4>
                        <div class="issue-item" style="border-left-color: #28a745;">
                            <strong>All checks passed!</strong><br>
                            Your application is healthy and ready for production.
                        </div>
                    </div>
EOF
fi

cat >> "$HTML_REPORT" << EOF
                </div>
            </div>
            
            <div id="fixes" class="tab-content">
                <div class="section">
EOF

if [ $FIXES_APPLIED -gt 0 ] && [ -f "/tmp/c3_fixes_log" ] && [ -s "/tmp/c3_fixes_log" ]; then
    # Process fixes for HTML
    while IFS= read -r line; do
        if [[ "$line" =~ "✅" ]]; then
            echo "                    <div class=\"fix-item\">$line</div>" >> "$HTML_REPORT"
        elif [[ "$line" =~ "❌" ]]; then
            echo "                    <div class=\"fix-item failed\">$line</div>" >> "$HTML_REPORT"
        fi
    done < /tmp/c3_fixes_log
else
    cat >> "$HTML_REPORT" << EOF
                    <div class="fix-item">
                        <strong>No auto-fixes were applied</strong><br>
                        Either no issues were found that could be automatically fixed, or all issues require manual intervention.
                    </div>
EOF
fi

cat >> "$HTML_REPORT" << EOF
                </div>
            </div>
            
            <div id="actions" class="tab-content">
                <div class="section">
                    <div class="action-items">
EOF

if [ -f "/tmp/c3_action_items" ] && [ -s "/tmp/c3_action_items" ]; then
    # Process action items by priority for HTML
    PRIORITIES=("CRITICAL" "HIGH" "MEDIUM" "LOW")
    ACTION_COUNT=0
    
    for PRIORITY in "${PRIORITIES[@]}"; do
        PRIORITY_ITEMS=$(grep "^ACTION|$PRIORITY|" /tmp/c3_action_items 2>/dev/null || true)
        if [ -n "$PRIORITY_ITEMS" ]; then
            echo "$PRIORITY_ITEMS" | while IFS='|' read -r type priority title description command; do
                ACTION_COUNT=$((ACTION_COUNT + 1))
                PRIORITY_CLASS="action-item"
                [ "$priority" = "CRITICAL" ] && PRIORITY_CLASS="action-item critical"
                [ "$priority" = "HIGH" ] && PRIORITY_CLASS="action-item high"
                [ "$priority" = "MEDIUM" ] && PRIORITY_CLASS="action-item medium"
                
                cat >> "$HTML_REPORT" << EOF
                        <div class="$PRIORITY_CLASS">
                            <h4>$priority: $title</h4>
                            <p><strong>Problem:</strong> $description</p>
                            <p><strong>Action:</strong> $command</p>
                        </div>
EOF
            done
        fi
    done
else
    cat >> "$HTML_REPORT" << EOF
                        <div class="action-item" style="border-left-color: #28a745;">
                            <h4>✅ No Action Items</h4>
                            <p>All detected issues have been automatically resolved or no issues were found.</p>
                            <p>Monitor the application for any remaining issues.</p>
                        </div>
EOF
fi

cat >> "$HTML_REPORT" << EOF
                    </div>
                </div>
            </div>
            
            <div id="commands" class="tab-content">
                <div class="section">
                    <h3>Check Current Status:</h3>
                    <div class="code-block"># Test website
curl -I https://$DOMAIN_NAME

# Check Laravel health
cd %current_path%
php artisan about

# View latest errors
tail -10 storage/logs/laravel.log</div>
                    
                    <h3>Common Fixes:</h3>
                    <div class="code-block"># Fix Redis issues (if applicable)
cd %current_path%
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
php artisan view:cache</div>
                    
                    <h3>Get Help:</h3>
                    <div class="code-block"># Full deployment report
cat $DOMAIN_ROOT/deployment-reports/latest-report.md

# Error logs
tail -20 %current_path%/storage/logs/laravel.log

# Health history
cat %shared_path%/health-checks.log</div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Report Generated:</strong> $(date '+%Y-%m-%d %H:%M:%S')</p>
            <p><strong>Complement to:</strong> Phase C-2 Comprehensive Report (<code>latest-report.md</code>)</p>
            <p><strong>Next Steps:</strong> Address action items above (if any) → Test website → Monitor error logs</p>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
        
        // Auto-refresh health score color based on percentage
        document.addEventListener('DOMContentLoaded', function() {
            const healthScore = $HEALTH_PERCENTAGE;
            const healthElement = document.querySelector('.stat-number');
            
            if (healthScore >= 90) {
                healthElement.classList.add('excellent');
            } else if (healthScore >= 75) {
                healthElement.classList.add('good');
            } else if (healthScore >= 50) {
                healthElement.classList.add('warning');
            } else {
                healthElement.classList.add('critical');
            }
        });
    </script>
</body>
</html>
EOF

# Set permissions and create symlinks
chmod 644 "$ISSUES_REPORT" "$HTML_REPORT"

# Create latest issues symlinks
cd "$REPORT_DIR"
rm -f issues-latest.md issues-latest.html
ln -sf "$(basename "$ISSUES_REPORT")" issues-latest.md
ln -sf "$(basename "$HTML_REPORT")" issues-latest.html

echo "✅ Issues summary report created: $ISSUES_REPORT"
echo "✅ HTML report created: $HTML_REPORT"

# C3-17: Log Results
echo "=== Logging Results ==="

# Log health check results to shared directory
HEALTH_LOG="%shared_path%/health-checks.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health Check - Score: $HEALTH_PERCENTAGE% - Status: $OVERALL_STATUS - Issues: $ISSUES_FOUND - Fixes: $FIXES_APPLIED" >> "$HEALTH_LOG"

# Log to deployment reports directory as well
DEPLOYMENT_LOG="$REPORT_DIR/health-history.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health: $HEALTH_PERCENTAGE% | Status: $OVERALL_STATUS | Issues: $ISSUES_FOUND | Fixes: $FIXES_APPLIED | Report: $(basename "$ISSUES_REPORT")" >> "$DEPLOYMENT_LOG"

log_check "Health Check Logging" "PASS" "Results logged to multiple locations" "Monitoring" "Low"

echo "✅ Phase C-3 comprehensive health check completed!"
echo "📊 Final Score: $HEALTH_PERCENTAGE% - $OVERALL_STATUS"
echo "📄 Issues Report: $ISSUES_REPORT"
echo "🌐 HTML Report: $HTML_REPORT"
echo "🔗 Quick Access: $REPORT_DIR/issues-latest.md"
echo "🔗 HTML Access: $REPORT_DIR/issues-latest.html"

# Display quick summary
if [ $ISSUES_FOUND -eq 0 ]; then
    echo "🎉 No issues detected - deployment is healthy!"
else
    echo "⚠️ $ISSUES_FOUND issues found - check the issues report for details"
    echo "🔧 $FIXES_APPLIED auto-fixes have been applied"
    echo ""
    echo "📋 Quick Issues Summary:"
    if [ -f "/tmp/c3_issues_log" ] && [ -s "/tmp/c3_issues_log" ]; then
        head -3 /tmp/c3_issues_log | sed 's/^/  /'
        if [ $(wc -l < /tmp/c3_issues_log) -gt 3 ]; then
            echo "  ... and $(($(wc -l < /tmp/c3_issues_log) - 3)) more (see full report)"
        fi
    fi
fi

# C3-18: Cleanup temporary files
echo "=== Cleanup ==="
rm -f /tmp/c3_issues_log /tmp/c3_fixes_log /tmp/c3_detailed_issues /tmp/c3_action_items
echo "✅ Temporary files cleaned up"

# Exit with appropriate code (but don't fail deployment)
if [ $HEALTH_PERCENTAGE -ge 50 ]; then
    exit 0
else
    echo "⚠️ Critical issues detected but deployment continues"
    exit 0  # Don't fail deployment, just warn
fi

echo "✅ Phase C-2 completed successfully"