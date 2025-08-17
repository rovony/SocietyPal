#!/bin/bash

# Laravel Customization System Comprehensive Verification Script
# Version: 2.0.0
# Description: Advanced verification and testing of customization system installation and functionality

set -euo pipefail  # Exit on error, undefined variables, pipe failures

# =============================================================================
# CONFIGURATION
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(pwd)"

# Colors for output
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
readonly CYAN='\033[0;36m'
readonly MAGENTA='\033[0;35m'
readonly NC='\033[0m' # No Color

# Test counters and configuration
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0
WARNINGS=0
VERBOSE=false
PERFORMANCE_TESTS=false
DEEP_SCAN=false

# =============================================================================
# UTILITY FUNCTIONS
# =============================================================================

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
    ((WARNINGS++))
}

error() {
    echo -e "${RED}[ERROR] $1${NC}" >&2
}

success() {
    echo -e "${GREEN}[SUCCESS] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

detail() {
    if [[ "$VERBOSE" == true ]]; then
        echo -e "${CYAN}  → $1${NC}"
    fi
}

# Enhanced test runner with categories and timing
run_test() {
    local category="$1"
    local test_name="$2"
    local test_command="$3"
    local expected_result="${4:-pass}"
    local severity="${5:-error}"
    
    ((TOTAL_TESTS++))
    
    if [[ "$VERBOSE" == true ]]; then
        echo
        echo -e "${MAGENTA}[$category]${NC} 🔍 Testing: $test_name"
    else
        printf "%-50s " "[$category] $test_name..."
    fi
    
    local start_time=$(date +%s%N)
    local test_result=false
    local error_output=""
    
    # Capture both stdout and stderr for better error reporting
    if error_output=$(eval "$test_command" 2>&1); then
        test_result=true
    else
        test_result=false
    fi
    
    local end_time=$(date +%s%N)
    local duration=$(( (end_time - start_time) / 1000000 )) # Convert to milliseconds
    
    # Evaluate test result
    local test_passed=false
    if [[ "$test_result" == true && "$expected_result" == "pass" ]] || 
       [[ "$test_result" == false && "$expected_result" == "fail" ]]; then
        test_passed=true
    fi
    
    if [[ "$test_passed" == true ]]; then
        if [[ "$VERBOSE" == true ]]; then
            echo -e "${GREEN}✅ PASSED${NC} (${duration}ms)"
        else
            echo -e "${GREEN}PASS${NC}"
        fi
        ((PASSED_TESTS++))
    else
        if [[ "$severity" == "warning" ]]; then
            if [[ "$VERBOSE" == true ]]; then
                echo -e "${YELLOW}⚠️  WARNING${NC} (${duration}ms)"
                [[ -n "$error_output" ]] && detail "Details: $error_output"
            else
                echo -e "${YELLOW}WARN${NC}"
            fi
            ((WARNINGS++))
        else
            if [[ "$VERBOSE" == true ]]; then
                echo -e "${RED}❌ FAILED${NC} (${duration}ms)"
                [[ -n "$error_output" ]] && detail "Error: $error_output"
            else
                echo -e "${RED}FAIL${NC}"
            fi
            ((FAILED_TESTS++))
        fi
    fi
}

# =============================================================================
# VERIFICATION TESTS
# =============================================================================

# Check if we're in a Laravel project
check_laravel_project() {
    run_test "ENVIRONMENT" "Laravel project structure" \
        "[[ -f 'artisan' && -f 'composer.json' ]]" "pass"
    
    if [[ -f "artisan" && -f "composer.json" ]]; then
        # Get Laravel version if possible
        local laravel_version
        if laravel_version=$(php artisan --version 2>/dev/null | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -1); then
            info "Laravel version: $laravel_version"
        fi
    fi
}

# Directory structure tests
test_directory_structure() {
    local directories=(
        "app/Custom"
        "app/Custom/Controllers" 
        "app/Custom/Models"
        "app/Custom/Services"
        "app/Custom/config"
        "resources/Custom"
        "resources/Custom/css"
        "resources/Custom/js"
        "resources/Custom/views"
        "database/Custom"
        "database/Custom/migrations"
        "public/Custom"
        "tests/Custom"
    )
    
    for dir in "${directories[@]}"; do
        run_test "STRUCTURE" "Directory exists: $dir" "[[ -d '$dir' ]]" "pass"
    done
}

# File existence tests
test_file_existence() {
    local files=(
        "app/Providers/CustomizationServiceProvider.php"
        "webpack.custom.cjs"
        "app/Custom/config/custom-app.php"
        "app/Custom/config/custom-database.php"
        "resources/Custom/css/app.scss"
        "resources/Custom/css/utilities/_variables.scss"
        "resources/Custom/css/utilities/_mixins.scss"
        "resources/Custom/js/app.js"
        "resources/Custom/js/components/CustomDashboard.js"
    )
    
    for file in "${files[@]}"; do
        run_test "FILES" "File exists: $file" "[[ -f '$file' ]]" "pass"
    done
}

# PHP syntax and functionality tests
test_php_functionality() {
    # PHP syntax validation
    local php_files=(
        "app/Providers/CustomizationServiceProvider.php"
        "app/Custom/config/custom-app.php"
        "app/Custom/config/custom-database.php"
    )
    
    for file in "${php_files[@]}"; do
        if [[ -f "$file" ]]; then
            run_test "PHP" "Syntax validation: $file" \
                "php -l '$file' >/dev/null 2>&1" "pass"
        fi
    done
    
    # Service provider registration check
    run_test "PHP" "Service provider registered (Laravel 11+)" \
        "grep -q 'CustomizationServiceProvider' bootstrap/providers.php 2>/dev/null" "pass" "warning"
    
    run_test "PHP" "Service provider registered (Laravel <11)" \
        "grep -q 'CustomizationServiceProvider' config/app.php 2>/dev/null" "pass" "warning"
    
    # Laravel configuration tests
    run_test "LARAVEL" "Config cache generation" \
        "php artisan config:cache >/dev/null 2>&1" "pass"
    
    run_test "LARAVEL" "Config clear operation" \
        "php artisan config:clear >/dev/null 2>&1" "pass"
    
    run_test "LARAVEL" "Artisan commands load" \
        "php artisan list >/dev/null 2>&1" "pass"
}

# Node.js and build system tests
test_build_system() {
    if ! command -v node &> /dev/null; then
        warn "Node.js not found, skipping build system tests"
        return
    fi
    
    run_test "NODE" "Node.js available" "command -v node >/dev/null 2>&1" "pass"
    
    if [[ -f "package.json" ]]; then
        run_test "NODE" "package.json exists" "[[ -f 'package.json' ]]" "pass"
        
        # Check if dependencies are installed
        run_test "NODE" "NPM dependencies" \
            "npm list --depth=0 >/dev/null 2>&1" "pass" "warning"
        
        # Webpack configuration validation
        run_test "BUILD" "Webpack config syntax" \
            "node -e \"require('./webpack.custom.cjs')\" >/dev/null 2>&1" "pass"
        
        # Check for custom scripts in package.json
        local scripts=("custom:build" "custom:dev" "custom:clean")
        for script in "${scripts[@]}"; do
            run_test "BUILD" "Package script: $script" \
                "grep -q '\"$script\"' package.json 2>/dev/null" "pass" "warning"
        done
    fi
}

# File permissions and security tests
test_permissions_security() {
    local files=(
        "app/Custom/config/custom-app.php"
        "app/Custom/config/custom-database.php"
        "app/Providers/CustomizationServiceProvider.php"
        "webpack.custom.cjs"
    )
    
    for file in "${files[@]}"; do
        if [[ -f "$file" ]]; then
            run_test "SECURITY" "File readable: $file" "[[ -r '$file' ]]" "pass"
            run_test "SECURITY" "File writable: $file" "[[ -w '$file' ]]" "pass"
            
            # Check for secure permissions (not world-writable)
            run_test "SECURITY" "Secure permissions: $file" \
                "[[ ! -w '$file' || \$(stat -c '%a' '$file' 2>/dev/null | cut -c3) != '7' ]]" "pass" "warning"
        fi
    done
}

# Deep configuration validation
test_configuration_deep() {
    if [[ "$DEEP_SCAN" != true ]]; then
        return
    fi
    
    # Test custom configuration loading
    run_test "CONFIG" "Custom app config loads" \
        "php -r \"include 'app/Custom/config/custom-app.php'; echo 'OK';\" >/dev/null 2>&1" "pass"
    
    run_test "CONFIG" "Custom database config loads" \
        "php -r \"include 'app/Custom/config/custom-database.php'; echo 'OK';\" >/dev/null 2>&1" "pass"
    
    # Test service provider instantiation
    run_test "CONFIG" "Service provider instantiable" \
        "php -r \"require 'vendor/autoload.php'; new App\\Providers\\CustomizationServiceProvider(new Illuminate\\Foundation\\Application); echo 'OK';\" >/dev/null 2>&1" "pass" "warning"
}

# Performance and integration tests
test_performance() {
    if [[ "$PERFORMANCE_TESTS" != true ]]; then
        return
    fi
    
    info "Running performance tests..."
    
    # Test Laravel bootstrap time
    local start_time=$(date +%s%N)
    php artisan list >/dev/null 2>&1
    local end_time=$(date +%s%N)
    local bootstrap_time=$(( (end_time - start_time) / 1000000 ))
    
    run_test "PERFORMANCE" "Laravel bootstrap time (<2000ms)" \
        "[[ $bootstrap_time -lt 2000 ]]" "pass" "warning"
    
    detail "Bootstrap time: ${bootstrap_time}ms"
    
    # Test config cache performance
    if [[ -f "bootstrap/cache/config.php" ]]; then
        start_time=$(date +%s%N)
        php artisan config:clear >/dev/null 2>&1
        php artisan config:cache >/dev/null 2>&1
        end_time=$(date +%s%N)
        local cache_time=$(( (end_time - start_time) / 1000000 ))
        
        run_test "PERFORMANCE" "Config cache time (<1000ms)" \
            "[[ $cache_time -lt 1000 ]]" "pass" "warning"
        
        detail "Config cache time: ${cache_time}ms"
    fi
}

# Generate comprehensive report
generate_report() {
    local total_checks=$((TOTAL_TESTS))
    local success_rate=0
    
    if [[ $total_checks -gt 0 ]]; then
        success_rate=$(( (PASSED_TESTS * 100) / total_checks ))
    fi
    
    echo
    echo "════════════════════════════════════════════════════════════════════════════════"
    echo "                    CUSTOMIZATION SYSTEM VERIFICATION REPORT"
    echo "════════════════════════════════════════════════════════════════════════════════"
    echo
    echo "📊 TEST SUMMARY:"
    echo "   Total Tests: $TOTAL_TESTS"
    echo -e "   Passed: ${GREEN}$PASSED_TESTS${NC}"
    echo -e "   Failed: ${RED}$FAILED_TESTS${NC}"
    echo -e "   Warnings: ${YELLOW}$WARNINGS${NC}"
    echo "   Success Rate: $success_rate%"
    echo
    
    # Detailed status assessment
    if [[ $FAILED_TESTS -eq 0 ]]; then
        if [[ $WARNINGS -eq 0 ]]; then
            success "🎉 PERFECT INSTALLATION - All tests passed!"
            echo "   ✅ Customization system is fully functional and optimized"
            echo "   🚀 Ready for production use"
        else
            success "✅ GOOD INSTALLATION - All critical tests passed"
            echo "   ⚠️  $WARNINGS warning(s) detected - review recommended"
            echo "   🔧 Minor optimizations possible"
        fi
        echo
        echo "🎯 NEXT STEPS:"
        echo "   1. Test custom asset compilation: npm run custom:build"
        echo "   2. Verify custom configurations work in your application"
        echo "   3. Run integration tests with your specific use case"
        echo "   4. Monitor Laravel logs for any runtime issues"
        return 0
        
    elif [[ $success_rate -ge 80 ]]; then
        warn "⚠️  PARTIAL INSTALLATION - Most tests passed but issues detected"
        echo "   🔧 $FAILED_TESTS critical issue(s) need attention"
        echo "   📋 Review failed tests and run setup script if needed"
        return 1
        
    elif [[ $success_rate -ge 50 ]]; then
        error "❌ INCOMPLETE INSTALLATION - Major issues detected"
        echo "   🚨 $FAILED_TESTS test failures indicate serious problems"
        echo "   🔄 Re-run setup script with --force flag recommended"
        return 2
        
    else
        error "💥 CRITICAL FAILURE - Installation severely compromised"
        echo "   🚨 $FAILED_TESTS/$TOTAL_TESTS tests failed"
        echo "   🔄 Complete reinstallation required"
        return 3
    fi
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================

show_banner() {
    cat << 'EOF'
 ╔══════════════════════════════════════════════════════════════════════════════╗
 ║                    Laravel Customization System                             ║
 ║                      Comprehensive Verification v2.0                       ║
 ╠══════════════════════════════════════════════════════════════════════════════╣
 ║  🔍 Advanced system analysis and validation                                 ║
 ║  📊 Performance and integration testing                                     ║
 ║  🛡️  Security and permissions verification                                  ║
 ║  📈 Detailed reporting and recommendations                                  ║
 ╚══════════════════════════════════════════════════════════════════════════════╝

EOF
}

show_help() {
    cat << 'EOF'
Laravel Customization System Comprehensive Verification

Usage: ./verify-customization.sh [OPTIONS]

OPTIONS:
    --verbose, -v        Show detailed output and test progress
    --performance, -p    Run performance and timing tests
    --deep-scan, -d      Execute deep configuration validation
    --all, -a           Enable all test types (verbose, performance, deep-scan)
    --help, -h          Show this help message

EXAMPLES:
    ./verify-customization.sh                      # Basic verification
    ./verify-customization.sh --verbose            # Detailed output
    ./verify-customization.sh --performance        # Include performance tests
    ./verify-customization.sh --all                # Full comprehensive testing

EXIT CODES:
    0 - Perfect/Good installation (0-5 warnings)
    1 - Partial installation (80%+ success rate)
    2 - Incomplete installation (50-79% success rate)
    3 - Critical failure (<50% success rate)

CATEGORIES:
    ENVIRONMENT  - Laravel project detection and basic environment
    STRUCTURE    - Directory structure validation
    FILES        - File existence and accessibility
    PHP          - PHP syntax validation and Laravel integration
    NODE         - Node.js and build system validation
    BUILD        - Webpack and asset compilation testing
    SECURITY     - File permissions and security checks
    CONFIG       - Configuration loading and validation
    PERFORMANCE  - Timing and performance metrics

EOF
}

main() {
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --verbose|-v)
                VERBOSE=true
                shift
                ;;
            --performance|-p)
                PERFORMANCE_TESTS=true
                shift
                ;;
            --deep-scan|-d)
                DEEP_SCAN=true
                shift
                ;;
            --all|-a)
                VERBOSE=true
                PERFORMANCE_TESTS=true
                DEEP_SCAN=true
                shift
                ;;
            --help|-h)
                show_help
                exit 0
                ;;
            *)
                error "Unknown option: $1"
                echo "Use --help for usage information"
                exit 1
                ;;
        esac
    done
    
    show_banner
    
    # Check if we're in a Laravel project first
    if [[ ! -f "artisan" || ! -f "composer.json" ]]; then
        error "This doesn't appear to be a Laravel project root directory"
        error "Please run this script from your Laravel project root (where artisan file is located)"
        exit 1
    fi
    
    log "Starting comprehensive customization system verification..."
    [[ "$VERBOSE" == true ]] && info "Verbose mode enabled"
    [[ "$PERFORMANCE_TESTS" == true ]] && info "Performance tests enabled"
    [[ "$DEEP_SCAN" == true ]] && info "Deep scan enabled"
    echo
    
    # Run test suites
    check_laravel_project
    test_directory_structure
    test_file_existence
    test_php_functionality
    test_build_system
    test_permissions_security
    test_configuration_deep
    test_performance
    
    # Generate and show final report
    local exit_code
    generate_report
    exit_code=$?
    
    exit $exit_code
}

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi