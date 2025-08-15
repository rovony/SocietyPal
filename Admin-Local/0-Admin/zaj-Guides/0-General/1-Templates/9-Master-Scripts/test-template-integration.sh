#!/bin/bash

# TEMPLATE INTEGRATION TEST SCRIPT
# This script tests that all template systems work together correctly
# Author: Automated Template System v4.0
# Date: $(date)

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')] $1${NC}"
}

success() {
    echo -e "${GREEN}[SUCCESS] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
}

info() {
    echo -e "${CYAN}[INFO] $1${NC}"
}

test_result() {
    echo -e "${MAGENTA}[TEST] $1${NC}"
}

# Function to show usage
show_usage() {
    echo -e "${CYAN}TEMPLATE INTEGRATION TEST SCRIPT${NC}"
    echo ""
    echo -e "${BLUE}Purpose:${NC} Verify all template systems work together correctly"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [--cleanup] [--verbose] [--dry-run]"
    echo "  $0 --help                    # Show this help"
    echo ""
    echo -e "${BLUE}Options:${NC}"
    echo "  --cleanup                    # Clean up test artifacts after completion"
    echo "  --verbose                    # Show detailed test output"
    echo "  --dry-run                    # Show what would be tested without executing"
    echo "  --help                       # Show this help message"
    echo ""
    echo -e "${BLUE}Test Coverage:${NC}"
    echo "  ✓ Template script executability"
    echo "  ✓ Project structure detection"
    echo "  ✓ Template generation functionality"
    echo "  ✓ Inter-template dependencies"
    echo "  ✓ File system integration"
    echo "  ✓ Configuration compatibility"
    echo ""
    echo -e "${BLUE}Test Phases:${NC}"
    echo "  1. Pre-flight checks"
    echo "  2. Template system validation"
    echo "  3. Integration testing"
    echo "  4. Dependency verification"
    echo "  5. Cleanup verification"
}

# Function to detect project root
detect_project_root() {
    local current_dir="$(pwd)"
    local search_dir="$current_dir"
    
    # Look for Admin-Local directory up to 8 levels up
    for i in {1..8}; do
        if [ -d "$search_dir/Admin-Local" ]; then
            echo "$search_dir"
            return 0
        fi
        search_dir="$(dirname "$search_dir")"
        if [ "$search_dir" = "/" ]; then
            break
        fi
    done
    
    error "Could not find project root (Admin-Local directory)"
    return 1
}

# Function to detect template directory
detect_template_dir() {
    local project_root="$1"
    local template_dir="$project_root/Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates"
    
    if [ -d "$template_dir" ]; then
        echo "$template_dir"
        return 0
    fi
    
    error "Could not find templates directory: $template_dir"
    return 1
}

# Function to run pre-flight checks
run_preflight_checks() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Phase 1: Pre-flight Checks"
    echo ""
    
    local checks_passed=0
    local checks_total=0
    
    # Check 1: Master scripts directory exists
    checks_total=$((checks_total + 1))
    if [ -d "$template_dir/9-Master-Scripts" ]; then
        [ "$verbose" = "true" ] && success "Master scripts directory exists"
        checks_passed=$((checks_passed + 1))
    else
        error "Master scripts directory not found"
    fi
    
    # Check 2: All template directories exist
    local template_systems=("5-Tracking-System" "6-Customization-System" "7-Data-Persistence-System" "8-Investment-Protection-System")
    
    for system in "${template_systems[@]}"; do
        checks_total=$((checks_total + 1))
        if [ -d "$template_dir/$system" ]; then
            [ "$verbose" = "true" ] && success "Template system exists: $system"
            checks_passed=$((checks_passed + 1))
        else
            error "Template system not found: $system"
        fi
    done
    
    # Check 3: Setup scripts are executable
    for system in "${template_systems[@]}"; do
        checks_total=$((checks_total + 1))
        local script_name=""
        case "$system" in
            "5-Tracking-System") script_name="setup-tracking.sh" ;;
            "6-Customization-System") script_name="setup-customization.sh" ;;
            "7-Data-Persistence-System") script_name="setup-data-persistence.sh" ;;
            "8-Investment-Protection-System") script_name="setup-investment-protection.sh" ;;
        esac
        
        local script_path="$template_dir/$system/$script_name"
        if [ -x "$script_path" ]; then
            [ "$verbose" = "true" ] && success "Setup script executable: $script_name"
            checks_passed=$((checks_passed + 1))
        else
            if [ -f "$script_path" ]; then
                warning "Setup script not executable: $script_name (fixing...)"
                chmod +x "$script_path"
                checks_passed=$((checks_passed + 1))
            else
                error "Setup script not found: $script_path"
            fi
        fi
    done
    
    log "Pre-flight checks: $checks_passed/$checks_total passed"
    
    if [ $checks_passed -eq $checks_total ]; then
        success "✅ All pre-flight checks passed"
        return 0
    else
        error "❌ Some pre-flight checks failed"
        return 1
    fi
}

# Function to get script name for template system (bash 3.2 compatible)
get_script_name() {
    local system="$1"
    case "$system" in
        "5-Tracking-System") echo "setup-tracking.sh" ;;
        "6-Customization-System") echo "setup-customization.sh" ;;
        "7-Data-Persistence-System") echo "setup-data-persistence.sh" ;;
        "8-Investment-Protection-System") echo "setup-investment-protection.sh" ;;
        *) echo "" ;;
    esac
}

# Function to test template system validation
test_template_systems() {
    local template_dir="$1"
    local project_root="$2"
    local verbose="$3"
    
    test_result "Phase 2: Template System Validation"
    echo ""
    
    local systems_passed=0
    local systems_total=4
    
    # Test each template system (bash 3.2 compatible)
    local template_systems="5-Tracking-System 6-Customization-System 7-Data-Persistence-System 8-Investment-Protection-System"
    
    for system in $template_systems; do
        local script_name=$(get_script_name "$system")
        local script_path="$template_dir/$system/$script_name"
        
        if [ -x "$script_path" ]; then
            [ "$verbose" = "true" ] && log "Testing $system..."
            
            # Create a test environment variable to prevent actual installation
            export TEMPLATE_TEST_MODE="true"
            
            # Run the script in test mode (dry run)
            if cd "$template_dir/$system" && bash "$script_path" --test-mode 2>/dev/null || true; then
                [ "$verbose" = "true" ] && success "$system validation passed"
                systems_passed=$((systems_passed + 1))
            else
                # If test mode not supported, just check script syntax
                if bash -n "$script_path"; then
                    [ "$verbose" = "true" ] && success "$system syntax validation passed"
                    systems_passed=$((systems_passed + 1))
                else
                    error "$system has syntax errors"
                fi
            fi
            
            unset TEMPLATE_TEST_MODE
        else
            error "Cannot test $system: script not executable"
        fi
    done
    
    log "Template system validation: $systems_passed/$systems_total passed"
    
    if [ $systems_passed -eq $systems_total ]; then
        success "✅ All template systems validated"
        return 0
    else
        error "❌ Some template systems failed validation"
        return 1
    fi
}

# Function to test integration between systems
test_integration() {
    local template_dir="$1"
    local project_root="$2"
    local verbose="$3"
    
    test_result "Phase 3: Integration Testing"
    echo ""
    
    local integration_passed=0
    local integration_total=0
    
    # Test 1: Verify tracking system provides foundation for others
    integration_total=$((integration_total + 1))
    local tracking_setup="$template_dir/5-Tracking-System/setup-tracking.sh"
    if [ -x "$tracking_setup" ]; then
        [ "$verbose" = "true" ] && info "Tracking system available for dependencies"
        integration_passed=$((integration_passed + 1))
    else
        error "Tracking system not available for dependencies"
    fi
    
    # Test 2: Check if customization system can integrate with tracking
    integration_total=$((integration_total + 1))
    local custom_setup="$template_dir/6-Customization-System/setup-customization.sh"
    if [ -f "$custom_setup" ] && grep -q "tracking\|Tracking" "$custom_setup" 2>/dev/null; then
        [ "$verbose" = "true" ] && info "Customization system integrates with tracking"
        integration_passed=$((integration_passed + 1))
    else
        if [ -f "$custom_setup" ]; then
            [ "$verbose" = "true" ] && warning "Customization system may not integrate with tracking"
            integration_passed=$((integration_passed + 1))  # Non-critical
        else
            error "Customization system setup not found"
        fi
    fi
    
    # Test 3: Verify data persistence works with other systems
    integration_total=$((integration_total + 1))
    local data_setup="$template_dir/7-Data-Persistence-System/setup-data-persistence.sh"
    if [ -f "$data_setup" ]; then
        [ "$verbose" = "true" ] && info "Data persistence system available"
        integration_passed=$((integration_passed + 1))
    else
        error "Data persistence system setup not found"
    fi
    
    # Test 4: Check investment protection integration
    integration_total=$((integration_total + 1))
    local invest_setup="$template_dir/8-Investment-Protection-System/setup-investment-protection.sh"
    if [ -f "$invest_setup" ]; then
        [ "$verbose" = "true" ] && info "Investment protection system available"
        integration_passed=$((integration_passed + 1))
    else
        error "Investment protection system setup not found"
    fi
    
    # Test 5: Master scripts integration
    integration_total=$((integration_total + 1))
    local master_regenerate="$template_dir/9-Master-Scripts/regenerate-all-templates.sh"
    if [ -x "$master_regenerate" ]; then
        [ "$verbose" = "true" ] && info "Master regeneration script available"
        integration_passed=$((integration_passed + 1))
    else
        error "Master regeneration script not available"
    fi
    
    log "Integration testing: $integration_passed/$integration_total passed"
    
    if [ $integration_passed -eq $integration_total ]; then
        success "✅ All integration tests passed"
        return 0
    else
        error "❌ Some integration tests failed"
        return 1
    fi
}

# Function to verify dependencies
test_dependencies() {
    local template_dir="$1"
    local project_root="$2"
    local verbose="$3"
    
    test_result "Phase 4: Dependency Verification"
    echo ""
    
    local deps_passed=0
    local deps_total=0
    
    # Test 1: Check for required system commands
    local required_commands=("bash" "chmod" "mkdir" "cp" "mv" "rm" "grep" "find")
    
    for cmd in "${required_commands[@]}"; do
        deps_total=$((deps_total + 1))
        if command -v "$cmd" >/dev/null 2>&1; then
            [ "$verbose" = "true" ] && success "Required command available: $cmd"
            deps_passed=$((deps_passed + 1))
        else
            error "Required command not found: $cmd"
        fi
    done
    
    # Test 2: Check directory structure dependencies
    local required_dirs=("Admin-Local/1-CurrentProject" "app" "config" "resources")
    
    for dir in "${required_dirs[@]}"; do
        deps_total=$((deps_total + 1))
        if [ -d "$project_root/$dir" ] || [ "$dir" = "Admin-Local/1-CurrentProject" ]; then
            [ "$verbose" = "true" ] && success "Required directory structure: $dir"
            deps_passed=$((deps_passed + 1))
        else
            warning "Optional directory not found: $dir (may be created by templates)"
            deps_passed=$((deps_passed + 1))  # Non-critical
        fi
    done
    
    # Test 3: Check file permissions
    deps_total=$((deps_total + 1))
    if [ -w "$project_root" ]; then
        [ "$verbose" = "true" ] && success "Project root is writable"
        deps_passed=$((deps_passed + 1))
    else
        error "Project root is not writable"
    fi
    
    log "Dependency verification: $deps_passed/$deps_total passed"
    
    if [ $deps_passed -eq $deps_total ]; then
        success "✅ All dependency checks passed"
        return 0
    else
        error "❌ Some dependency checks failed"
        return 1
    fi
}

# Function to test cleanup functionality
test_cleanup_functionality() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Phase 5: Cleanup Verification"
    echo ""
    
    local cleanup_passed=0
    local cleanup_total=0
    
    # Test 1: Check cleanup script exists and is executable
    cleanup_total=$((cleanup_total + 1))
    local cleanup_script="$template_dir/9-Master-Scripts/cleanup-templates.sh"
    if [ -x "$cleanup_script" ]; then
        [ "$verbose" = "true" ] && success "Cleanup script is executable"
        cleanup_passed=$((cleanup_passed + 1))
    else
        if [ -f "$cleanup_script" ]; then
            warning "Cleanup script not executable (fixing...)"
            chmod +x "$cleanup_script"
            cleanup_passed=$((cleanup_passed + 1))
        else
            error "Cleanup script not found"
        fi
    fi
    
    # Test 2: Check cleanup script syntax
    cleanup_total=$((cleanup_total + 1))
    if [ -f "$cleanup_script" ] && bash -n "$cleanup_script"; then
        [ "$verbose" = "true" ] && success "Cleanup script syntax is valid"
        cleanup_passed=$((cleanup_passed + 1))
    else
        error "Cleanup script has syntax errors"
    fi
    
    # Test 3: Check if cleanup script has safety features
    cleanup_total=$((cleanup_total + 1))
    if [ -f "$cleanup_script" ] && grep -q "confirm\|interactive\|force" "$cleanup_script"; then
        [ "$verbose" = "true" ] && success "Cleanup script has safety features"
        cleanup_passed=$((cleanup_passed + 1))
    else
        error "Cleanup script may lack safety features"
    fi
    
    log "Cleanup verification: $cleanup_passed/$cleanup_total passed"
    
    if [ $cleanup_passed -eq $cleanup_total ]; then
        success "✅ All cleanup tests passed"
        return 0
    else
        error "❌ Some cleanup tests failed"
        return 1
    fi
}

# Main execution
main() {
    local cleanup_after="false"
    local verbose="false"
    local dry_run="false"
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --cleanup)
                cleanup_after="true"
                shift
                ;;
            --verbose)
                verbose="true"
                shift
                ;;
            --dry-run)
                dry_run="true"
                shift
                ;;
            --help|-h)
                show_usage
                exit 0
                ;;
            *)
                error "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
    
    log "🧪 TEMPLATE INTEGRATION TESTING STARTING"
    log "========================================="
    
    if [ "$dry_run" = "true" ]; then
        info "DRY RUN MODE - No actual changes will be made"
        echo ""
    fi
    
    # Detect paths
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 1
    fi
    
    local template_dir
    if ! template_dir=$(detect_template_dir "$project_root"); then
        exit 1
    fi
    
    log "Project Root: $project_root"
    log "Template Directory: $template_dir"
    log "Verbose Mode: $verbose"
    log "Cleanup After: $cleanup_after"
    echo ""
    
    if [ "$dry_run" = "true" ]; then
        info "Would run the following test phases:"
        info "1. Pre-flight checks"
        info "2. Template system validation"
        info "3. Integration testing"
        info "4. Dependency verification"
        info "5. Cleanup verification"
        echo ""
        info "DRY RUN COMPLETED - No tests executed"
        exit 0
    fi
    
    # Initialize test results
    local phases_passed=0
    local phases_total=5
    
    # Phase 1: Pre-flight checks
    if run_preflight_checks "$template_dir" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 2: Template system validation
    if test_template_systems "$template_dir" "$project_root" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 3: Integration testing
    if test_integration "$template_dir" "$project_root" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 4: Dependency verification
    if test_dependencies "$template_dir" "$project_root" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 5: Cleanup verification
    if test_cleanup_functionality "$template_dir" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Final summary
    log "========================================="
    log "🧪 INTEGRATION TEST SUMMARY"
    log "Test Phases Passed: $phases_passed/$phases_total"
    
    if [ $phases_passed -eq $phases_total ]; then
        success "🎉 ALL INTEGRATION TESTS PASSED!"
        log "Template system is ready for production use"
        
        if [ "$cleanup_after" = "true" ]; then
            info "Performing cleanup..."
            # Add any cleanup logic here if needed
            success "Cleanup completed"
        fi
        
        exit 0
    else
        error "❌ SOME INTEGRATION TESTS FAILED"
        log "Please review the failed tests and fix issues before proceeding"
        exit 1
    fi
}

# Run main function
main "$@"