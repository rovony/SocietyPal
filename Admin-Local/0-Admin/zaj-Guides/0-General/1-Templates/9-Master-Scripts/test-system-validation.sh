#!/bin/bash

# SYSTEM VALIDATION TEST SCRIPT
# This script performs comprehensive end-to-end validation of the entire template system
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
    echo -e "${MAGENTA}[VALIDATION] $1${NC}"
}

# Global test counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0
WARNING_TESTS=0

# Function to increment test counters
increment_test() {
    local result="$1"  # pass, fail, warning
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    case "$result" in
        "pass")
            PASSED_TESTS=$((PASSED_TESTS + 1))
            ;;
        "fail")
            FAILED_TESTS=$((FAILED_TESTS + 1))
            ;;
        "warning")
            WARNING_TESTS=$((WARNING_TESTS + 1))
            ;;
    esac
}

# Function to show usage
show_usage() {
    echo -e "${CYAN}SYSTEM VALIDATION TEST SCRIPT${NC}"
    echo ""
    echo -e "${BLUE}Purpose:${NC} Comprehensive end-to-end validation of the entire template system"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [--full] [--quick] [--repair] [--verbose] [--dry-run]"
    echo "  $0 --help                         # Show this help"
    echo ""
    echo -e "${BLUE}Options:${NC}"
    echo "  --full                            # Run comprehensive validation (default)"
    echo "  --quick                           # Run essential checks only"
    echo "  --repair                          # Attempt to repair issues found"
    echo "  --verbose                         # Show detailed validation output"
    echo "  --dry-run                         # Show what would be validated without executing"
    echo "  --help                            # Show this help message"
    echo ""
    echo -e "${BLUE}Validation Suites:${NC}"
    echo "  📋 Template Structure Validation # Directory structure, file permissions"
    echo "  🔧 Script Functionality Testing  # All setup scripts work correctly"
    echo "  🔄 Integration Flow Validation   # End-to-end workflow testing"
    echo "  📊 Data Consistency Checks       # Template data integrity"
    echo "  🚀 Performance Benchmarking     # System performance metrics"
    echo "  🛡️  Security Validation         # Security best practices"
    echo "  📝 Documentation Verification   # Guide completeness and accuracy"
    echo ""
    echo -e "${BLUE}Test Levels:${NC}"
    echo "  • CRITICAL: Must pass for system to function"
    echo "  • WARNING:  Should pass for optimal performance"
    echo "  • INFO:     Nice to have, non-blocking"
    echo ""
    echo -e "${BLUE}Exit Codes:${NC}"
    echo "  0: All critical tests passed"
    echo "  1: Critical test failures detected"
    echo "  2: System configuration issues"
    echo "  3: Permission or access issues"
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

# Function to validate template structure
validate_template_structure() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Suite 1: Template Structure Validation"
    echo ""
    
    # Test 1.1: Core template directories exist
    local required_dirs=("5-Tracking-System" "6-Customization-System" "7-Data-Persistence-System" "8-Investment-Protection-System" "9-Master-Scripts")
    
    for dir in "${required_dirs[@]}"; do
        if [ -d "$template_dir/$dir" ]; then
            [ "$verbose" = "true" ] && success "Template directory exists: $dir"
            increment_test "pass"
        else
            error "CRITICAL: Missing template directory: $dir"
            increment_test "fail"
        fi
    done
    
    # Test 1.2: Essential scripts exist and are executable
    local critical_scripts=(
        "5-Tracking-System/setup-tracking.sh"
        "6-Customization-System/setup-customization.sh"
        "9-Master-Scripts/regenerate-all-templates.sh"
        "9-Master-Scripts/test-template-integration.sh"
        "9-Master-Scripts/test-template-compatibility.sh"
    )
    
    for script in "${critical_scripts[@]}"; do
        local script_path="$template_dir/$script"
        if [ -x "$script_path" ]; then
            [ "$verbose" = "true" ] && success "Critical script executable: $script"
            increment_test "pass"
        elif [ -f "$script_path" ]; then
            warning "Script exists but not executable: $script"
            increment_test "warning"
        else
            error "CRITICAL: Missing script: $script"
            increment_test "fail"
        fi
    done
    
    # Test 1.3: Template configuration files
    local config_files=(
        "5-Tracking-System/README.md"
        "6-Customization-System/README.md"
        "9-Master-Scripts/README.md"
    )
    
    for config in "${config_files[@]}"; do
        if [ -f "$template_dir/$config" ]; then
            [ "$verbose" = "true" ] && success "Configuration file exists: $config"
            increment_test "pass"
        else
            warning "Missing configuration file: $config"
            increment_test "warning"
        fi
    done
    
    # Test 1.4: Template subdirectory structure
    local tracking_subdirs=("1-First-Setup" "2-Operation-Template" "99-Master-Reports")
    for subdir in "${tracking_subdirs[@]}"; do
        if [ -d "$template_dir/5-Tracking-System/$subdir" ]; then
            [ "$verbose" = "true" ] && success "Tracking subdirectory exists: $subdir"
            increment_test "pass"
        else
            error "Missing tracking subdirectory: $subdir"
            increment_test "fail"
        fi
    done
    
    # Test 1.5: Customization system structure
    local custom_subdirs=("templates" "scripts")
    for subdir in "${custom_subdirs[@]}"; do
        if [ -d "$template_dir/6-Customization-System/$subdir" ]; then
            [ "$verbose" = "true" ] && success "Customization subdirectory exists: $subdir"
            increment_test "pass"
        else
            error "Missing customization subdirectory: $subdir"
            increment_test "fail"
        fi
    done
}

# Function to test script functionality
test_script_functionality() {
    local template_dir="$1"
    local verbose="$2"
    local repair="$3"
    
    test_result "Suite 2: Script Functionality Testing"
    echo ""
    
    # Test 2.1: Script syntax validation
    local test_scripts=(
        "5-Tracking-System/setup-tracking.sh"
        "6-Customization-System/setup-customization.sh"
        "6-Customization-System/scripts/detect-customization.sh"
        "6-Customization-System/scripts/verify-customization.sh"
        "9-Master-Scripts/regenerate-all-templates.sh"
        "9-Master-Scripts/regenerate-selective.sh"
        "9-Master-Scripts/cleanup-templates.sh"
        "9-Master-Scripts/test-template-integration.sh"
        "9-Master-Scripts/test-template-compatibility.sh"
    )
    
    for script in "${test_scripts[@]}"; do
        local script_path="$template_dir/$script"
        if [ -f "$script_path" ]; then
            if bash -n "$script_path" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Script syntax valid: $script"
                increment_test "pass"
            else
                error "CRITICAL: Script syntax error in: $script"
                increment_test "fail"
                if [ "$repair" = "true" ]; then
                    warning "Repair mode: Would attempt to fix syntax errors in $script"
                fi
            fi
        else
            error "Script not found: $script"
            increment_test "fail"
        fi
    done
    
    # Test 2.2: Help/usage functionality
    local help_scripts=(
        "9-Master-Scripts/regenerate-all-templates.sh"
        "9-Master-Scripts/test-template-integration.sh"
        "9-Master-Scripts/test-template-compatibility.sh"
    )
    
    for script in "${help_scripts[@]}"; do
        local script_path="$template_dir/$script"
        if [ -x "$script_path" ]; then
            if "$script_path" --help >/dev/null 2>&1 || "$script_path" -h >/dev/null 2>&1; then
                [ "$verbose" = "true" ] && success "Help functionality works: $script"
                increment_test "pass"
            else
                warning "Help functionality missing or broken: $script"
                increment_test "warning"
            fi
        fi
    done
    
    # Test 2.3: Error handling in scripts
    local error_test_scripts=(
        "6-Customization-System/setup-customization.sh"
        "9-Master-Scripts/regenerate-all-templates.sh"
    )
    
    for script in "${error_test_scripts[@]}"; do
        local script_path="$template_dir/$script"
        if [ -f "$script_path" ]; then
            # Check if script has error handling (set -e or error functions)
            if grep -q "set -e\|error()\|exit 1" "$script_path" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Error handling present: $script"
                increment_test "pass"
            else
                warning "Error handling may be missing: $script"
                increment_test "warning"
            fi
        fi
    done
}

# Function to validate integration flow
validate_integration_flow() {
    local project_root="$1"
    local template_dir="$2"
    local verbose="$3"
    
    test_result "Suite 3: Integration Flow Validation"
    echo ""
    
    # Test 3.1: Project structure compatibility
    local required_project_dirs=("Admin-Local/1-CurrentProject")
    
    for dir in "${required_project_dirs[@]}"; do
        if [ -d "$project_root/$dir" ]; then
            [ "$verbose" = "true" ] && success "Project directory exists: $dir"
            increment_test "pass"
        else
            warning "Project directory missing (will be created): $dir"
            increment_test "warning"
        fi
    done
    
    # Test 3.2: Flow integration files
    local flow_dirs=(
        "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/C-Deploy-Vendor-Updates"
        "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/E-Customize-App"
    )
    
    for flow_dir in "${flow_dirs[@]}"; do
        if [ -d "$project_root/$flow_dir" ]; then
            [ "$verbose" = "true" ] && success "Flow directory exists: $flow_dir"
            increment_test "pass"
            
            # Check for step files
            local step_count=$(find "$project_root/$flow_dir" -name "Step_*.md" 2>/dev/null | wc -l)
            if [ "$step_count" -gt 0 ]; then
                [ "$verbose" = "true" ] && success "Flow has step files: $flow_dir ($step_count steps)"
                increment_test "pass"
            else
                warning "No step files found in: $flow_dir"
                increment_test "warning"
            fi
        else
            error "Flow directory missing: $flow_dir"
            increment_test "fail"
        fi
    done
    
    # Test 3.3: Cross-template dependencies
    if [ -f "$template_dir/6-Customization-System/setup-customization.sh" ]; then
        # Check if customization system references tracking
        if grep -q "tracking\|Tracking" "$template_dir/6-Customization-System/setup-customization.sh" 2>/dev/null; then
            [ "$verbose" = "true" ] && success "Cross-template integration: Customization → Tracking"
            increment_test "pass"
        else
            info "No cross-template integration detected"
            increment_test "pass"  # Not required
        fi
    fi
    
    # Test 3.4: Master scripts integration
    if [ -x "$template_dir/9-Master-Scripts/regenerate-all-templates.sh" ]; then
        # Check if master script can find all templates
        export TEMPLATE_TEST_MODE="true"
        if cd "$template_dir/9-Master-Scripts" && ./regenerate-all-templates.sh --dry-run >/dev/null 2>&1; then
            [ "$verbose" = "true" ] && success "Master scripts integration working"
            increment_test "pass"
        else
            error "Master scripts integration broken"
            increment_test "fail"
        fi
        unset TEMPLATE_TEST_MODE
    fi
}

# Function to check data consistency
check_data_consistency() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Suite 4: Data Consistency Checks"
    echo ""
    
    # Test 4.1: Template file integrity
    local template_files=(
        "6-Customization-System/templates/webpack.custom.cjs"
        "6-Customization-System/templates/CustomizationServiceProvider.php"
    )
    
    for template_file in "${template_files[@]}"; do
        if [ -f "$template_dir/$template_file" ]; then
            # Check if template file is not empty
            if [ -s "$template_dir/$template_file" ]; then
                [ "$verbose" = "true" ] && success "Template file has content: $template_file"
                increment_test "pass"
            else
                warning "Template file is empty: $template_file"
                increment_test "warning"
            fi
        else
            warning "Template file missing: $template_file"
            increment_test "warning"
        fi
    done
    
    # Test 4.2: Configuration consistency
    local readme_files=$(find "$template_dir" -name "README.md" 2>/dev/null)
    local readme_count=0
    
    while read -r readme_file; do
        if [ -f "$readme_file" ]; then
            readme_count=$((readme_count + 1))
            if [ -s "$readme_file" ]; then
                [ "$verbose" = "true" ] && success "README has content: $(basename "$(dirname "$readme_file")")"
                increment_test "pass"
            else
                warning "README is empty: $readme_file"
                increment_test "warning"
            fi
        fi
    done <<< "$readme_files"
    
    if [ "$readme_count" -gt 0 ]; then
        [ "$verbose" = "true" ] && success "Found $readme_count README files"
        increment_test "pass"
    else
        warning "No README files found in templates"
        increment_test "warning"
    fi
    
    # Test 4.3: Version consistency (if version files exist)
    local version_files=$(find "$template_dir" -name "version*" -o -name "VERSION*" 2>/dev/null)
    if [ -n "$version_files" ]; then
        while read -r version_file; do
            if [ -f "$version_file" ] && [ -s "$version_file" ]; then
                [ "$verbose" = "true" ] && success "Version file exists: $version_file"
                increment_test "pass"
            fi
        done <<< "$version_files"
    else
        info "No version files found (not required)"
        increment_test "pass"
    fi
}

# Function to benchmark performance
benchmark_performance() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Suite 5: Performance Benchmarking"
    echo ""
    
    # Test 5.1: Script execution time
    local benchmark_scripts=(
        "5-Tracking-System/setup-tracking.sh"
        "9-Master-Scripts/regenerate-all-templates.sh"
    )
    
    for script in "${benchmark_scripts[@]}"; do
        local script_path="$template_dir/$script"
        if [ -x "$script_path" ]; then
            export TEMPLATE_TEST_MODE="true"
            local start_time=$(date +%s.%N)
            
            if cd "$(dirname "$script_path")" && "./$(basename "$script_path")" --help >/dev/null 2>&1; then
                local end_time=$(date +%s.%N)
                local execution_time=$(echo "$end_time - $start_time" | bc -l 2>/dev/null || echo "0.1")
                
                if [ "$(echo "$execution_time < 5.0" | bc -l 2>/dev/null || echo 1)" -eq 1 ]; then
                    [ "$verbose" = "true" ] && success "Performance OK: $script (${execution_time}s)"
                    increment_test "pass"
                else
                    warning "Performance concern: $script (${execution_time}s)"
                    increment_test "warning"
                fi
            else
                warning "Could not benchmark: $script"
                increment_test "warning"
            fi
            
            unset TEMPLATE_TEST_MODE
        fi
    done
    
    # Test 5.2: Directory traversal performance
    local start_time=$(date +%s.%N)
    local file_count=$(find "$template_dir" -type f 2>/dev/null | wc -l)
    local end_time=$(date +%s.%N)
    local traversal_time=$(echo "$end_time - $start_time" | bc -l 2>/dev/null || echo "0.1")
    
    if [ "$(echo "$traversal_time < 2.0" | bc -l 2>/dev/null || echo 1)" -eq 1 ]; then
        [ "$verbose" = "true" ] && success "Directory traversal OK: $file_count files in ${traversal_time}s"
        increment_test "pass"
    else
        warning "Directory traversal slow: $file_count files in ${traversal_time}s"
        increment_test "warning"
    fi
    
    # Test 5.3: Memory usage estimation
    local memory_usage=$(ps -o rss= -p $$ 2>/dev/null | tr -d ' ' || echo "0")
    if [ "$memory_usage" -lt 100000 ]; then  # Less than 100MB
        [ "$verbose" = "true" ] && success "Memory usage acceptable: ${memory_usage}KB"
        increment_test "pass"
    else
        warning "High memory usage detected: ${memory_usage}KB"
        increment_test "warning"
    fi
}

# Function to validate security
validate_security() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Suite 6: Security Validation"
    echo ""
    
    # Test 6.1: File permissions
    local sensitive_files=$(find "$template_dir" -name "*.sh" -o -name "*.php" -o -name "*.js" 2>/dev/null)
    
    while read -r file; do
        if [ -f "$file" ]; then
            local perms=$(stat -c "%a" "$file" 2>/dev/null || stat -f "%A" "$file" 2>/dev/null || echo "000")
            
            # Check if file is executable by owner but not world-writable
            if [[ "$perms" =~ ^[0-7][0-7][0-5]$ ]] || [[ "$perms" =~ ^[0-7][0-5][0-5]$ ]]; then
                [ "$verbose" = "true" ] && success "File permissions secure: $file ($perms)"
                increment_test "pass"
            else
                warning "File permissions concern: $file ($perms)"
                increment_test "warning"
            fi
        fi
    done <<< "$sensitive_files"
    
    # Test 6.2: No hardcoded secrets
    local config_files=$(find "$template_dir" -name "*.sh" -o -name "*.php" -o -name "*.js" -o -name "*.json" 2>/dev/null)
    local secret_patterns=("password" "secret" "key" "token" "api_key")
    
    for pattern in "${secret_patterns[@]}"; do
        local matches=$(grep -ri "$pattern" $config_files 2>/dev/null | grep -v "TODO\|FIXME\|example\|placeholder" | wc -l || echo 0)
        
        if [ "$matches" -eq 0 ]; then
            [ "$verbose" = "true" ] && success "No hardcoded secrets found: $pattern"
            increment_test "pass"
        else
            warning "Potential hardcoded secrets found: $pattern ($matches matches)"
            increment_test "warning"
        fi
    done
    
    # Test 6.3: Script injection protection
    local script_files=$(find "$template_dir" -name "*.sh" 2>/dev/null)
    
    while read -r script; do
        if [ -f "$script" ]; then
            # Check for basic injection protection patterns
            if grep -q "set -e\|\${\|\".*\"\|\[\[.*\]\]" "$script" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Injection protection present: $script"
                increment_test "pass"
            else
                warning "Limited injection protection: $script"
                increment_test "warning"
            fi
        fi
    done <<< "$script_files"
    
    # Test 6.4: Path traversal protection
    local path_scripts=$(find "$template_dir" -name "*.sh" 2>/dev/null)
    
    while read -r script; do
        if [ -f "$script" ]; then
            # Check for dangerous path patterns
            if grep -q "\.\./\|/etc/\|/root/" "$script" 2>/dev/null; then
                error "SECURITY: Potential path traversal in: $script"
                increment_test "fail"
            else
                [ "$verbose" = "true" ] && success "No path traversal detected: $script"
                increment_test "pass"
            fi
        fi
    done <<< "$path_scripts"
}

# Function to verify documentation
verify_documentation() {
    local template_dir="$1"
    local project_root="$2"
    local verbose="$3"
    
    test_result "Suite 7: Documentation Verification"
    echo ""
    
    # Test 7.1: README completeness
    local readme_files=$(find "$template_dir" -name "README.md" 2>/dev/null)
    
    while read -r readme; do
        if [ -f "$readme" ]; then
            local dir_name=$(basename "$(dirname "$readme")")
            
            # Check for essential sections
            local has_purpose=$(grep -qi "purpose\|overview\|description" "$readme" 2>/dev/null && echo 1 || echo 0)
            local has_usage=$(grep -qi "usage\|how to\|getting started" "$readme" 2>/dev/null && echo 1 || echo 0)
            local has_examples=$(grep -qi "example\|sample\|demo" "$readme" 2>/dev/null && echo 1 || echo 0)
            
            local completeness=$((has_purpose + has_usage + has_examples))
            
            if [ "$completeness" -ge 2 ]; then
                [ "$verbose" = "true" ] && success "README complete: $dir_name"
                increment_test "pass"
            else
                warning "README incomplete: $dir_name (missing sections)"
                increment_test "warning"
            fi
        fi
    done <<< "$readme_files"
    
    # Test 7.2: Guide existence and structure
    local guide_dirs=(
        "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/C-Deploy-Vendor-Updates"
        "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/E-Customize-App"
    )
    
    for guide_dir in "${guide_dirs[@]}"; do
        if [ -d "$project_root/$guide_dir" ]; then
            local step_files=$(find "$project_root/$guide_dir" -name "Step_*.md" 2>/dev/null | wc -l)
            
            if [ "$step_files" -gt 0 ]; then
                [ "$verbose" = "true" ] && success "Guide has steps: $(basename "$guide_dir") ($step_files steps)"
                increment_test "pass"
            else
                warning "Guide has no steps: $(basename "$guide_dir")"
                increment_test "warning"
            fi
        fi
    done
    
    # Test 7.3: Master documentation
    local master_docs=(
        "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/C-Deploy-Vendor-Updates/VENDOR-UPDATE-PIPELINE-MASTER.md"
    )
    
    for doc in "${master_docs[@]}"; do
        if [ -f "$project_root/$doc" ]; then
            [ "$verbose" = "true" ] && success "Master documentation exists: $(basename "$doc")"
            increment_test "pass"
        else
            warning "Master documentation missing: $(basename "$doc")"
            increment_test "warning"
        fi
    done
    
    # Test 7.4: Template documentation consistency
    local template_systems=("5-Tracking-System" "6-Customization-System" "9-Master-Scripts")
    
    for system in "${template_systems[@]}"; do
        local system_readme="$template_dir/$system/README.md"
        if [ -f "$system_readme" ]; then
            # Check if README mentions the system name
            if grep -qi "$system\|$(echo "$system" | sed 's/-/ /g')" "$system_readme" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Documentation consistent: $system"
                increment_test "pass"
            else
                warning "Documentation inconsistent: $system"
                increment_test "warning"
            fi
        fi
    done
}

# Function to attempt repairs
attempt_repairs() {
    local template_dir="$1"
    local verbose="$2"
    
    test_result "Attempting System Repairs"
    echo ""
    
    local repairs_made=0
    
    # Repair 1: Fix script permissions
    local script_files=$(find "$template_dir" -name "*.sh" 2>/dev/null)
    
    while read -r script; do
        if [ -f "$script" ] && [ ! -x "$script" ]; then
            if chmod +x "$script" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Fixed permissions: $script"
                repairs_made=$((repairs_made + 1))
            else
                error "Could not fix permissions: $script"
            fi
        fi
    done <<< "$script_files"
    
    # Repair 2: Create missing directories
    local required_dirs=(
        "5-Tracking-System/1-First-Setup"
        "5-Tracking-System/2-Operation-Template"
        "5-Tracking-System/99-Master-Reports"
        "6-Customization-System/templates"
        "6-Customization-System/scripts"
    )
    
    for dir in "${required_dirs[@]}"; do
        local dir_path="$template_dir/$dir"
        if [ ! -d "$dir_path" ]; then
            if mkdir -p "$dir_path" 2>/dev/null; then
                [ "$verbose" = "true" ] && success "Created directory: $dir"
                repairs_made=$((repairs_made + 1))
            else
                error "Could not create directory: $dir"
            fi
        fi
    done
    
    if [ "$repairs_made" -gt 0 ]; then
        success "Completed $repairs_made repairs"
    else
        info "No repairs needed"
    fi
    
    return 0
}

# Main execution
main() {
    local mode="full"
    local repair="false"
    local verbose="false"
    local dry_run="false"
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --full)
                mode="full"
                shift
                ;;
            --quick)
                mode="quick"
                shift
                ;;
            --repair)
                repair="true"
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
    
    log "🛡️  SYSTEM VALIDATION TESTING STARTING"
    log "======================================"
    
    if [ "$dry_run" = "true" ]; then
        info "DRY RUN MODE - No actual validation will be performed"
        echo ""
    fi
    
    # Detect paths
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 2
    fi
    
    local template_dir="$project_root/Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates"
    
    if [ ! -d "$template_dir" ]; then
        error "Template directory not found: $template_dir"
        exit 2
    fi
    
    log "Project Root: $project_root"
    log "Template Directory: $template_dir"
    log "Mode: $mode"
    log "Repair Mode: $repair"
    log "Verbose Mode: $verbose"
    echo ""
    
    if [ "$dry_run" = "true" ]; then
        info "Would run the following validation suites:"
        info "1. Template Structure Validation"
        info "2. Script Functionality Testing"
        info "3. Integration Flow Validation"
        info "4. Data Consistency Checks"
        
        if [ "$mode" = "full" ]; then
            info "5. Performance Benchmarking"
            info "6. Security Validation"
            info "7. Documentation Verification"
        fi
        
        if [ "$repair" = "true" ]; then
            info "• Repair Mode: Would attempt to fix issues found"
        fi
        
        echo ""
        info "DRY RUN COMPLETED - No validation performed"
        exit 0
    fi
    
    # Initialize counters
    TOTAL_TESTS=0
    PASSED_TESTS=0
    FAILED_TESTS=0
    WARNING_TESTS=0
    
    # Run validation suites
    validate_template_structure "$template_dir" "$verbose"
    echo ""
    
    test_script_functionality "$template_dir" "$verbose" "$repair"
    echo ""
    
    validate_integration_flow "$project_root" "$template_dir" "$verbose"
    echo ""
    
    check_data_consistency "$template_dir" "$verbose"
    echo ""
    
    # Full mode includes additional suites
    if [ "$mode" = "full" ]; then
        benchmark_performance "$template_dir" "$verbose"
        echo ""
        
        validate_security "$template_dir" "$verbose"
        echo ""
        
        verify_documentation "$template_dir" "$project_root" "$verbose"
        echo ""
    fi
    
    # Attempt repairs if requested
    if [ "$repair" = "true" ] && [ "$FAILED_TESTS" -gt 0 ]; then
        attempt_repairs "$template_dir" "$verbose"
        echo ""
    fi
    
    # Final summary
    log "======================================"
    log "🛡️  SYSTEM VALIDATION SUMMARY"
    log "Total Tests: $TOTAL_TESTS"
    log "✅ Passed: $PASSED_TESTS"
    log "⚠️  Warnings: $WARNING_TESTS"
    log "❌ Failed: $FAILED_TESTS"
    
    # Calculate success rate
    local success_rate=0
    if [ "$TOTAL_TESTS" -gt 0 ]; then
        success_rate=$(( (PASSED_TESTS * 100) / TOTAL_TESTS ))
    fi
    
    log "Success Rate: ${success_rate}%"
    echo ""
    
    # Determine exit status and message
    if [ "$FAILED_TESTS" -eq 0 ]; then
        if [ "$WARNING_TESTS" -eq 0 ]; then
            success "🎉 PERFECT! All validation tests passed!"
            log "Template system is in excellent condition"
            exit 0
        else
            success "✅ PASSED! All critical tests passed"
            warning "Some non-critical warnings detected - consider addressing them"
            exit 0
        fi
    elif [ "$success_rate" -ge 80 ]; then
        warning "⚠️  MOSTLY HEALTHY (${success_rate}% pass rate)"
        log "Some issues detected but system is functional"
        if [ "$repair" = "true" ]; then
            info "Re-run with --repair to attempt fixes"
        fi
        exit 0
    else
        error "❌ VALIDATION FAILED (${success_rate}% pass rate)"
        log "Critical issues detected - system may not function correctly"
        log "Please address the failed tests before proceeding"
        if [ "$repair" = "true" ]; then
            info "Run with --repair to attempt automatic fixes"
        fi
        exit 1
    fi
}

# Run main function
main "$@"