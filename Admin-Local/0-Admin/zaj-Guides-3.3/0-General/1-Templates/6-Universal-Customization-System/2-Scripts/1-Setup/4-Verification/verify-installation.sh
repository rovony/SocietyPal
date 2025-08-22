#!/bin/bash
# Laravel Customization System - Verification Script
# Version: 1.0.0
# Description: Comprehensive verification of customization system installation

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}✅ Laravel Customization System - Verification Script${NC}"
echo "===================================================="

# Check if we're in a Laravel project
if [[ ! -f "artisan" ]]; then
    echo -e "${RED}❌ Error: Not in Laravel project root (artisan not found)${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Laravel project detected${NC}"

# Initialize counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

function run_test() {
    local test_name="$1"
    local test_command="$2"
    local expected_result="$3"
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    echo
    echo "🔍 Testing: $test_name"
    
    if eval "$test_command"; then
        if [[ "$expected_result" == "pass" ]]; then
            echo -e "${GREEN}✅ PASSED: $test_name${NC}"
            PASSED_TESTS=$((PASSED_TESTS + 1))
        else
            echo -e "${RED}❌ FAILED: $test_name (unexpected pass)${NC}"
            FAILED_TESTS=$((FAILED_TESTS + 1))
        fi
    else
        if [[ "$expected_result" == "fail" ]]; then
            echo -e "${GREEN}✅ PASSED: $test_name (expected fail)${NC}"
            PASSED_TESTS=$((PASSED_TESTS + 1))
        else
            echo -e "${RED}❌ FAILED: $test_name${NC}"
            FAILED_TESTS=$((FAILED_TESTS + 1))
        fi
    fi
}

# Test 1: Directory Structure
run_test "Custom directories exist" "[[ -d 'app/Custom' && -d 'resources/Custom' ]]" "pass"

# Test 2: Configuration Files
run_test "Custom config files exist" "[[ -f 'app/Custom/config/custom-app.php' && -f 'app/Custom/config/custom-database.php' ]]" "pass"

# Test 3: Service Provider
run_test "CustomizationServiceProvider exists" "[[ -f 'app/Providers/CustomizationServiceProvider.php' ]]" "pass"

# Test 4: Service Provider Registration
run_test "CustomizationServiceProvider registered" "grep -q 'CustomizationServiceProvider' bootstrap/providers.php" "pass"

# Test 5: Webpack Configuration
run_test "Custom webpack config exists" "[[ -f 'webpack.custom.cjs' ]]" "pass"

# Test 6: CSS Structure
run_test "Custom CSS files exist" "[[ -f 'resources/Custom/css/app.scss' && -f 'resources/Custom/css/utilities/_variables.scss' && -f 'resources/Custom/css/utilities/_mixins.scss' ]]" "pass"

# Test 7: JavaScript Structure
run_test "Custom JS files exist" "[[ -f 'resources/Custom/js/app.js' && -f 'resources/Custom/js/components/CustomDashboard.js' ]]" "pass"

# Test 8: Laravel Syntax Check
run_test "PHP syntax validation" "php -l app/Providers/CustomizationServiceProvider.php > /dev/null 2>&1" "pass"

# Test 9: Configuration Loading
run_test "Custom configurations loadable" "php artisan config:cache > /dev/null 2>&1 && php artisan config:clear > /dev/null 2>&1" "pass"

# Test 10: Service Provider Loading
run_test "Service provider loads without errors" "php artisan list > /dev/null 2>&1" "pass"

# Advanced Tests (if available)
if command -v node &> /dev/null && [[ -f "package.json" ]]; then
    # Test 11: NPM Dependencies
    run_test "NPM dependencies installed" "npm list --depth=0 > /dev/null 2>&1" "pass"
    
    # Test 12: Webpack compilation test
    run_test "Custom webpack compilation" "node -e \"require('./webpack.custom.cjs')\" > /dev/null 2>&1" "pass"
fi

# Test 13: File Permissions
run_test "Proper file permissions" "[[ -r 'app/Custom/config/custom-app.php' && -w 'app/Custom/config/custom-app.php' ]]" "pass"

# Summary Report
echo
echo "📊 VERIFICATION SUMMARY"
echo "======================"
echo -e "Total tests: ${BLUE}${TOTAL_TESTS}${NC}"
echo -e "Passed: ${GREEN}${PASSED_TESTS}${NC}"
echo -e "Failed: ${RED}${FAILED_TESTS}${NC}"

if [[ $FAILED_TESTS -eq 0 ]]; then
    echo
    echo -e "${GREEN}🎉 ALL TESTS PASSED! Customization system is fully functional.${NC}"
    echo
    echo "🚀 Next steps:"
    echo "   1. Run 'npm run dev' to compile custom assets"
    echo "   2. Check Laravel logs for any runtime issues"
    echo "   3. Test custom configurations in your application"
    exit 0
else
    echo
    echo -e "${RED}❌ VERIFICATION FAILED: ${FAILED_TESTS} test(s) failed${NC}"
    echo
    echo "🔧 Troubleshooting:"
    echo "   1. Review failed tests above"
    echo "   2. Re-run setup script if needed:"
    echo "      bash Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Customization-System/setup-customization.sh"
    echo "   3. Check Laravel error logs"
    exit 1
fi