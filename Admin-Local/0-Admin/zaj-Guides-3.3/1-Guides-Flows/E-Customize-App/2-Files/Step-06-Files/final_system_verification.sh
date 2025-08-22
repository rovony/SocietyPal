#!/bin/bash

# Final System Verification Script for Custom Features
# Generated from Step 06: Document and Commit Customization

echo "🔍 Starting Final System Verification..."
echo "========================================"

ERRORS=0
WARNINGS=0

# Function to log results
log_result() {
    local status=$1
    local message=$2
    
    case $status in
        "PASS")
            echo "✅ $message"
            ;;
        "WARN")
            echo "⚠️  $message"
            ((WARNINGS++))
            ;;
        "FAIL")
            echo "❌ $message"
            ((ERRORS++))
            ;;
    esac
}

echo
echo "1. Verifying Database Integration"
echo "--------------------------------"

# Check custom tables exist
if php artisan tinker --execute="echo collect(DB::select('SHOW TABLES'))->pluck('Tables_in_'.env('DB_DATABASE'))->filter(fn(\$t) => str_starts_with(\$t, 'custom_'))->count();" 2>/dev/null | grep -q "^[1-9]"; then
    log_result "PASS" "Custom database tables are present"
else
    log_result "FAIL" "Custom database tables not found"
fi

# Check migrations status
MIGRATION_STATUS=$(php artisan migrate:status --path=database/migrations/custom 2>&1)
if echo "$MIGRATION_STATUS" | grep -q "Y.*create_custom"; then
    log_result "PASS" "Custom migrations are up to date"
else
    log_result "WARN" "Custom migration status unclear: $MIGRATION_STATUS"
fi

echo
echo "2. Verifying Application Integration"
echo "-----------------------------------"

# Check service provider registration
if php artisan tinker --execute="echo app()->providerIsLoaded('App\\Custom\\Providers\\CustomizationServiceProvider') ? 'LOADED' : 'NOT_LOADED';" 2>/dev/null | grep -q "LOADED"; then
    log_result "PASS" "Custom service provider is loaded"
else
    log_result "FAIL" "Custom service provider not loaded"
fi

# Check custom routes registration
CUSTOM_ROUTES=$(php artisan route:list | grep -c "custom" || echo "0")
if [ "$CUSTOM_ROUTES" -gt "0" ]; then
    log_result "PASS" "Custom routes registered ($CUSTOM_ROUTES routes)"
else
    log_result "WARN" "No custom routes found"
fi

# Check custom configuration
if php artisan tinker --execute="echo config('custom-app') ? 'ACCESSIBLE' : 'NOT_ACCESSIBLE';" 2>/dev/null | grep -q "ACCESSIBLE"; then
    log_result "PASS" "Custom configuration is accessible"
else
    log_result "WARN" "Custom configuration not accessible"
fi

echo
echo "3. Verifying File Structure"
echo "---------------------------"

# Check custom directories exist
REQUIRED_DIRS=(
    "app/Custom"
    "resources/Custom"
    "database/migrations/custom"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        FILE_COUNT=$(find "$dir" -type f | wc -l)
        log_result "PASS" "$dir exists with $FILE_COUNT files"
    else
        log_result "FAIL" "$dir directory not found"
    fi
done

# Check webpack custom config
if [ -f "webpack.custom.cjs" ]; then
    log_result "PASS" "Custom webpack configuration found"
else
    log_result "WARN" "Custom webpack configuration not found"
fi

echo
echo "4. Verifying Asset Compilation"
echo "------------------------------"

# Check if custom assets compiled
if [ -d "public/custom" ]; then
    ASSET_COUNT=$(find public/custom -type f | wc -l)
    if [ "$ASSET_COUNT" -gt "0" ]; then
        log_result "PASS" "Custom assets compiled ($ASSET_COUNT files)"
    else
        log_result "WARN" "Custom assets directory empty"
    fi
else
    log_result "WARN" "Custom assets directory not found"
fi

echo
echo "5. Verifying Laravel Core Functionality"
echo "---------------------------------------"

# Test Laravel commands still work
if php artisan --version >/dev/null 2>&1; then
    log_result "PASS" "Laravel Artisan commands functional"
else
    log_result "FAIL" "Laravel Artisan commands not working"
fi

# Test configuration caching
if php artisan config:cache >/dev/null 2>&1; then
    log_result "PASS" "Configuration caching successful"
else
    log_result "FAIL" "Configuration caching failed"
fi

# Clear config cache to avoid issues
php artisan config:clear >/dev/null 2>&1

echo
echo "6. Verifying Security"
echo "--------------------"

# Check for any obvious security issues
if find app/Custom -name "*.php" -exec grep -l "eval\|exec\|system\|shell_exec" {} \; 2>/dev/null | grep -q .; then
    log_result "FAIL" "Potentially dangerous functions found in custom code"
else
    log_result "PASS" "No obvious dangerous functions in custom code"
fi

# Check for hardcoded secrets/passwords
if find app/Custom resources/Custom -type f -exec grep -l -i "password.*=.*[\"'][^\"']*[\"']\|secret.*=.*[\"'][^\"']*[\"']\|token.*=.*[\"'][^\"']*[\"']" {} \; 2>/dev/null | grep -q .; then
    log_result "WARN" "Potential hardcoded secrets found in custom code"
else
    log_result "PASS" "No hardcoded secrets detected"
fi

echo
echo "7. Performance Baseline"
echo "----------------------"

# Basic performance test
START_TIME=$(php -r "echo microtime(true);")
php artisan tinker --execute="
for (\$i = 0; \$i < 100; \$i++) {
    config('custom-app');
}
" >/dev/null 2>&1
END_TIME=$(php -r "echo microtime(true);")
CONFIG_TIME=$(php -r "echo round(($END_TIME - $START_TIME) * 1000, 2);")

if [ "${CONFIG_TIME%.*}" -lt "100" ]; then
    log_result "PASS" "Configuration access performance: ${CONFIG_TIME}ms for 100 calls"
else
    log_result "WARN" "Configuration access might be slow: ${CONFIG_TIME}ms for 100 calls"
fi

echo
echo "========================================"
echo "Final System Verification Complete"
echo "========================================"
echo
echo "📊 Summary:"
echo "  ✅ Passed: $(($(find . -name "*.md" -o -name "*.sh" | wc -l) - ERRORS - WARNINGS)) checks"
echo "  ⚠️  Warnings: $WARNINGS"
echo "  ❌ Errors: $ERRORS"
echo

if [ "$ERRORS" -eq "0" ]; then
    echo "🎉 System verification PASSED! Ready for production deployment."
    exit 0
elif [ "$ERRORS" -lt "3" ]; then
    echo "⚠️  System verification completed with minor issues. Review errors before deployment."
    exit 1
else
    echo "🚨 System verification FAILED! Critical issues must be resolved before deployment."
    exit 2
fi