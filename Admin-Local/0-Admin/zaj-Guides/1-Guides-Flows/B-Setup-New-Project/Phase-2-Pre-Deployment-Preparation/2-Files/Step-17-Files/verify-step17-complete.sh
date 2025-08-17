#!/bin/bash
# Step 17 - Master Verification Script
# Comprehensive but lightweight verification of customization system

echo "🛡️ Step 17: Customization Protection System - Master Verification"
echo "=============================================================="

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOTAL_CHECKS=0
PASSED_CHECKS=0
FAILED_CHECKS=0

# Function to run verification and track results
run_verification() {
    local script_name="$1"
    local script_path="$SCRIPT_DIR/$script_name"
    
    if [ -f "$script_path" ]; then
        echo ""
        echo "🔍 Running $script_name..."
        if bash "$script_path"; then
            PASSED_CHECKS=$((PASSED_CHECKS + 1))
            echo "   ✅ $script_name: PASSED"
        else
            FAILED_CHECKS=$((FAILED_CHECKS + 1))
            echo "   ❌ $script_name: FAILED"
        fi
        TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    else
        echo "   ⚠️  Script $script_name not found"
        FAILED_CHECKS=$((FAILED_CHECKS + 1))
        TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    fi
}

# Run individual verification scripts
run_verification "verify-service-provider.sh"
run_verification "verify-custom-assets.sh"

# Additional quick checks
echo ""
echo "🔍 Additional System Checks..."

# Check Laravel version compatibility
TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
if php artisan --version > /dev/null 2>&1; then
    LARAVEL_VERSION=$(php artisan --version | head -n1)
    echo "   ✅ Laravel Framework: $LARAVEL_VERSION"
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo "   ❌ Laravel Framework: Not accessible"
    FAILED_CHECKS=$((FAILED_CHECKS + 1))
fi

# Check if custom config loads without errors
TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
echo "   🔍 Testing custom configuration loading..."
if php artisan config:cache > /dev/null 2>&1; then
    echo "   ✅ Custom configuration: Loads without errors"
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo "   ❌ Custom configuration: Loading errors detected"
    FAILED_CHECKS=$((FAILED_CHECKS + 1))
fi

# Check custom directory structure
TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
if [ -d "app/Custom" ] && [ -d "resources/Custom" ]; then
    echo "   ✅ Custom directory structure: Complete"
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo "   ❌ Custom directory structure: Incomplete"
    FAILED_CHECKS=$((FAILED_CHECKS + 1))
fi

# Final Results
echo ""
echo "📊 VERIFICATION SUMMARY"
echo "======================"
echo "Total Checks: $TOTAL_CHECKS"
echo "Passed: $PASSED_CHECKS"
echo "Failed: $FAILED_CHECKS"

if [ $FAILED_CHECKS -eq 0 ]; then
    echo ""
    echo "🎉 ALL CHECKS PASSED! Step 17 customization system is fully functional."
    echo ""
    echo "✅ Next Steps:"
    echo "   - Step 17 is COMPLETE"
    echo "   - Ready to proceed to Step 18: Data Persistence & Backup System"
    echo ""
    exit 0
else
    echo ""
    echo "⚠️  Some checks failed. Please review the issues above before proceeding."
    echo ""
    exit 1
fi