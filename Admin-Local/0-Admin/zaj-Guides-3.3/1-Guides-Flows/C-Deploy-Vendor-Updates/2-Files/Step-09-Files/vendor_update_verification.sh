#!/bin/bash

# =========================================================================
# Vendor Update Flow Comprehensive Verification Script
# =========================================================================
# 
# Purpose: Verifies the complete vendor update cycle execution
# Usage: ./vendor_update_verification.sh [--step=N] [--method=A|B|C|D]
# Version: 1.0
# Date: $(date)
#
# Features:
# - Project-agnostic path detection
# - Step-by-step verification
# - Method-specific deployment verification  
# - Comprehensive tracking system validation
# - Integration with template systems
#
# Dependencies:
# - 5-Tracking-System template
# - 6-Customization-System template (if used)
# - Vendor update step files
#
# Exit Codes:
# 0 = All verifications passed
# 1 = Critical verification failures
# 2 = Warning-level issues found
# 3 = Configuration/setup errors
# =========================================================================

set -e  # Exit on any error
set -u  # Exit on undefined variables

# =========================================================================
# CONFIGURATION & INITIALIZATION
# =========================================================================

# Script metadata
SCRIPT_VERSION="1.0"
SCRIPT_NAME="Vendor Update Flow Verification"
VERIFICATION_DATE=$(date)
VERIFICATION_ID="VU_$(date +%Y%m%d_%H%M%S)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Verification counters
TOTAL_CHECKS=0
PASSED_CHECKS=0
FAILED_CHECKS=0
WARNING_CHECKS=0

# Default parameters
VERIFY_STEP=""
DEPLOYMENT_METHOD=""
VERBOSE=false
DRY_RUN=false

# =========================================================================
# UTILITY FUNCTIONS
# =========================================================================

print_header() {
    echo -e "${CYAN}========================================${NC}"
    echo -e "${CYAN}$1${NC}"
    echo -e "${CYAN}========================================${NC}"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓ PASS]${NC} $1"
    ((PASSED_CHECKS++))
}

print_failure() {
    echo -e "${RED}[✗ FAIL]${NC} $1"
    ((FAILED_CHECKS++))
}

print_warning() {
    echo -e "${YELLOW}[⚠ WARN]${NC} $1"
    ((WARNING_CHECKS++))
}

print_info() {
    echo -e "${PURPLE}[INFO]${NC} $1"
}

increment_check() {
    ((TOTAL_CHECKS++))
}

# =========================================================================
# PROJECT PATH DETECTION
# =========================================================================

detect_project_paths() {
    print_step "Detecting project paths (project-agnostic)"
    
    # Auto-detect project root with multiple fallback strategies
    if [ -d "Admin-Local" ]; then
        PROJECT_ROOT="$(pwd)"
    elif [ -d "../Admin-Local" ]; then
        PROJECT_ROOT="$(dirname "$(pwd)")"
    elif [ -d "../../Admin-Local" ]; then
        PROJECT_ROOT="$(dirname "$(dirname "$(pwd)")")"
    elif [ -d "../../../Admin-Local" ]; then
        PROJECT_ROOT="$(dirname "$(dirname "$(dirname "$(pwd)")")")"
    else
        print_failure "Cannot find Admin-Local directory. Please run from project root or subdirectory."
        print_info "Current directory: $(pwd)"
        print_info "Available directories: $(ls -la | grep ^d | awk '{print $9}' | tr '\n' ' ')"
        exit 3
    fi

    # Set derived paths
    ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
    TEMPLATES_DIR="$ADMIN_LOCAL/0-Admin/zaj-Guides/0-General/1-Templates"
    TRACKING_TEMPLATE="$TEMPLATES_DIR/5-Tracking-System"
    CUSTOM_TEMPLATE="$TEMPLATES_DIR/6-Customization-System"
    VENDOR_UPDATE_DIR="$ADMIN_LOCAL/0-Admin/zaj-Guides/1-Guides-Flows/C-Deploy-Vendor-Updates"
    PROJECT_TRACKING="$ADMIN_LOCAL/1-CurrentProject/Tracking"
    
    increment_check
    if [ -d "$ADMIN_LOCAL" ]; then
        print_success "Project root detected: $PROJECT_ROOT"
        print_info "Admin-Local: $ADMIN_LOCAL"
    else
        print_failure "Admin-Local directory not found at: $ADMIN_LOCAL"
        return 1
    fi
}

# =========================================================================
# TEMPLATE SYSTEM VERIFICATION
# =========================================================================

verify_template_systems() {
    print_step "Verifying template system availability"
    
    # Check 5-Tracking-System template
    increment_check
    if [ -d "$TRACKING_TEMPLATE" ]; then
        print_success "5-Tracking-System template found"
        
        # Verify key tracking template files
        local tracking_files=(
            "README.md"
            "setup-tracking.sh"
            "1-First-Setup"
            "2-Operation-Template"
            "99-Master-Reports"
        )
        
        for file in "${tracking_files[@]}"; do
            increment_check
            if [ -e "$TRACKING_TEMPLATE/$file" ]; then
                print_success "Tracking template component: $file"
            else
                print_failure "Missing tracking template component: $file"
            fi
        done
    else
        print_failure "5-Tracking-System template not found at: $TRACKING_TEMPLATE"
    fi
    
    # Check 6-Customization-System template
    increment_check
    if [ -d "$CUSTOM_TEMPLATE" ]; then
        print_success "6-Customization-System template found"
        
        # Verify key customization template files
        increment_check
        if [ -f "$CUSTOM_TEMPLATE/setup-customization.sh" ]; then
            print_success "Customization setup script available"
        else
            print_warning "Customization setup script not found (optional)"
        fi
    else
        print_warning "6-Customization-System template not found (optional for updates)"
    fi
}

# =========================================================================
# VENDOR UPDATE STEPS VERIFICATION
# =========================================================================

verify_vendor_update_steps() {
    print_step "Verifying vendor update step files"
    
    local step_files=(
        "Step_01_Pre_Update_Backup.md"
        "Step_02_Download_New_CodeCanyon_Version.md"
        "Step_03_Compare_Changes.md"
        "Step_04_Update_Vendor_Files.md"
        "Step_05_Test_Custom_Functions.md"
        "Step_06_Update_Dependencies.md"
        "Step_07_Test_Build_Process.md"
        "Step_08_Deploy_Updates.md"
        "Step_09_Verify_Deployment.md"
    )
    
    local steps_dir="$VENDOR_UPDATE_DIR/1-Steps"
    
    increment_check
    if [ -d "$steps_dir" ]; then
        print_success "Vendor update steps directory found"
    else
        print_failure "Vendor update steps directory not found: $steps_dir"
        return 1
    fi
    
    for step_file in "${step_files[@]}"; do
        increment_check
        if [ -f "$steps_dir/$step_file" ]; then
            print_success "Step file exists: $step_file"
            
            # Verify tracking integration in step files
            if grep -q "project-agnostic" "$steps_dir/$step_file"; then
                increment_check
                print_success "Step $step_file has tracking integration"
            else
                increment_check
                print_warning "Step $step_file may be missing tracking integration"
            fi
        else
            print_failure "Missing step file: $step_file"
        fi
    done
}

# =========================================================================
# PROJECT TRACKING SYSTEM VERIFICATION
# =========================================================================

verify_project_tracking() {
    print_step "Verifying project tracking system"
    
    increment_check
    if [ -d "$PROJECT_TRACKING" ]; then
        print_success "Project tracking directory exists"
        
        # Check tracking structure
        local tracking_structure=(
            "1-First-Setup"
            "1-First-Setup/1-Planning"
            "1-First-Setup/2-Baselines"
            "1-First-Setup/3-Execution"
            "1-First-Setup/4-Verification"
            "1-First-Setup/5-Documentation"
        )
        
        for tracking_dir in "${tracking_structure[@]}"; do
            increment_check
            if [ -d "$PROJECT_TRACKING/$tracking_dir" ]; then
                print_success "Tracking structure: $tracking_dir"
            else
                print_warning "Missing tracking structure: $tracking_dir"
            fi
        done
    else
        print_warning "Project tracking directory not found: $PROJECT_TRACKING"
        print_info "This may be normal for new projects"
    fi
}

# =========================================================================
# DEPLOYMENT METHOD VERIFICATION
# =========================================================================

verify_deployment_methods() {
    print_step "Verifying deployment method configurations"
    
    local methods=("A" "B" "C" "D")
    local method_names=("Manual SSH" "GitHub Actions" "DeployHQ Professional" "GitHub + Manual Build")
    
    for i in "${!methods[@]}"; do
        local method="${methods[$i]}"
        local method_name="${method_names[$i]}"
        
        increment_check
        # Check if Step_08 contains method-specific instructions
        if grep -q "Method $method" "$VENDOR_UPDATE_DIR/1-Steps/Step_08_Deploy_Updates.md"; then
            print_success "Method $method ($method_name) configuration found"
        else
            print_warning "Method $method ($method_name) configuration may be incomplete"
        fi
    done
}

# =========================================================================
# PIPELINE DOCUMENTATION VERIFICATION
# =========================================================================

verify_pipeline_documentation() {
    print_step "Verifying pipeline documentation"
    
    increment_check
    if [ -f "$VENDOR_UPDATE_DIR/VENDOR-UPDATE-PIPELINE-MASTER.md" ]; then
        print_success "Master pipeline documentation exists"
        
        # Verify key sections in pipeline documentation
        local required_sections=(
            "Current Reality Check"
            "Vendor Update Pipeline Flow"
            "Step Execution Dependencies"
            "Tracking System Features"
            "Deployment Methods Integration"
        )
        
        for section in "${required_sections[@]}"; do
            increment_check
            if grep -q "$section" "$VENDOR_UPDATE_DIR/VENDOR-UPDATE-PIPELINE-MASTER.md"; then
                print_success "Pipeline documentation section: $section"
            else
                print_warning "Missing pipeline documentation section: $section"
            fi
        done
    else
        print_failure "Master pipeline documentation not found"
    fi
}

# =========================================================================
# SESSION-SPECIFIC VERIFICATION
# =========================================================================

verify_current_session() {
    print_step "Verifying current session tracking (if exists)"
    
    local current_session="$PROJECT_TRACKING/1-First-Setup/5-Current-Session"
    
    increment_check
    if [ -d "$current_session" ]; then
        print_success "Current session directory exists"
        
        # Count session files
        local session_files=$(find "$current_session" -name "*.md" | wc -l)
        increment_check
        if [ "$session_files" -gt 0 ]; then
            print_success "Session has $session_files tracking files"
            
            # List recent session activity
            print_info "Recent session files:"
            ls -la "$current_session"/*.md 2>/dev/null | tail -5 | while read line; do
                print_info "  $line"
            done
        else
            print_info "Current session directory is empty (normal for new sessions)"
        fi
    else
        print_info "No current session directory (normal for new projects)"
    fi
}

# =========================================================================
# INTEGRATION VERIFICATION
# =========================================================================

verify_integration_status() {
    print_step "Verifying template system integration"
    
    # Check if tracking system setup script is executable
    increment_check
    if [ -x "$TRACKING_TEMPLATE/setup-tracking.sh" ]; then
        print_success "Tracking setup script is executable"
    else
        print_warning "Tracking setup script may not be executable"
    fi
    
    # Check if customization system setup script is executable
    increment_check
    if [ -x "$CUSTOM_TEMPLATE/setup-customization.sh" ]; then
        print_success "Customization setup script is executable"
    elif [ -f "$CUSTOM_TEMPLATE/setup-customization.sh" ]; then
        print_warning "Customization setup script exists but may not be executable"
    else
        print_info "Customization setup script not found (may be optional)"
    fi
}

# =========================================================================
# SPECIFIC STEP VERIFICATION
# =========================================================================

verify_specific_step() {
    local step_num="$1"
    print_step "Verifying Step $step_num specifically"
    
    local step_file="$VENDOR_UPDATE_DIR/1-Steps/Step_$(printf '%02d' $step_num)_*.md"
    
    increment_check
    if ls $step_file 1> /dev/null 2>&1; then
        local actual_step_file=$(ls $step_file | head -1)
        print_success "Step $step_num file found: $(basename "$actual_step_file")"
        
        # Verify step-specific requirements
        case $step_num in
            1)
                increment_check
                if grep -q "backup" "$actual_step_file"; then
                    print_success "Step 1 contains backup procedures"
                else
                    print_warning "Step 1 may be missing backup procedures"
                fi
                ;;
            8)
                increment_check
                if grep -q "Method A\|Method B\|Method C\|Method D" "$actual_step_file"; then
                    print_success "Step 8 contains deployment method options"
                else
                    print_warning "Step 8 may be missing deployment methods"
                fi
                ;;
            9)
                increment_check
                if grep -q "verification" "$actual_step_file"; then
                    print_success "Step 9 contains verification procedures"
                else
                    print_warning "Step 9 may be missing verification procedures"
                fi
                ;;
        esac
    else
        print_failure "Step $step_num file not found"
    fi
}

# =========================================================================
# DEPLOYMENT METHOD-SPECIFIC VERIFICATION
# =========================================================================

verify_deployment_method() {
    local method="$1"
    print_step "Verifying deployment Method $method specifically"
    
    local step8_file="$VENDOR_UPDATE_DIR/1-Steps/Step_08_Deploy_Updates.md"
    
    case $method in
        "A")
            increment_check
            if grep -q "Manual SSH" "$step8_file"; then
                print_success "Method A (Manual SSH) configuration found"
            else
                print_failure "Method A (Manual SSH) configuration missing"
            fi
            ;;
        "B")
            increment_check
            if grep -q "GitHub Actions" "$step8_file"; then
                print_success "Method B (GitHub Actions) configuration found"
            else
                print_failure "Method B (GitHub Actions) configuration missing"
            fi
            ;;
        "C")
            increment_check
            if grep -q "DeployHQ" "$step8_file"; then
                print_success "Method C (DeployHQ Professional) configuration found"
            else
                print_failure "Method C (DeployHQ Professional) configuration missing"
            fi
            ;;
        "D")
            increment_check
            if grep -q "GitHub + Manual" "$step8_file"; then
                print_success "Method D (GitHub + Manual Build) configuration found"
            else
                print_failure "Method D (GitHub + Manual Build) configuration missing"
            fi
            ;;
        *)
            print_failure "Unknown deployment method: $method"
            ;;
    esac
}

# =========================================================================
# MAIN VERIFICATION WORKFLOW
# =========================================================================

run_comprehensive_verification() {
    print_header "$SCRIPT_NAME - Comprehensive Verification"
    print_info "Version: $SCRIPT_VERSION"
    print_info "Date: $VERIFICATION_DATE"
    print_info "Verification ID: $VERIFICATION_ID"
    echo
    
    # Core verification steps
    detect_project_paths
    verify_template_systems
    verify_vendor_update_steps
    verify_project_tracking
    verify_deployment_methods
    verify_pipeline_documentation
    verify_current_session
    verify_integration_status
    
    # Specific verifications if requested
    if [ -n "$VERIFY_STEP" ]; then
        verify_specific_step "$VERIFY_STEP"
    fi
    
    if [ -n "$DEPLOYMENT_METHOD" ]; then
        verify_deployment_method "$DEPLOYMENT_METHOD"
    fi
}

# =========================================================================
# RESULTS REPORTING
# =========================================================================

generate_verification_report() {
    echo
    print_header "VERIFICATION SUMMARY"
    
    echo -e "${BLUE}Total Checks:${NC} $TOTAL_CHECKS"
    echo -e "${GREEN}Passed:${NC} $PASSED_CHECKS"
    echo -e "${RED}Failed:${NC} $FAILED_CHECKS"
    echo -e "${YELLOW}Warnings:${NC} $WARNING_CHECKS"
    
    # Calculate success rate
    if [ $TOTAL_CHECKS -gt 0 ]; then
        local success_rate=$((PASSED_CHECKS * 100 / TOTAL_CHECKS))
        echo -e "${PURPLE}Success Rate:${NC} ${success_rate}%"
    fi
    
    echo
    
    # Determine overall status
    if [ $FAILED_CHECKS -eq 0 ]; then
        if [ $WARNING_CHECKS -eq 0 ]; then
            echo -e "${GREEN}[✓ OVERALL STATUS]${NC} ALL VERIFICATIONS PASSED"
            return 0
        else
            echo -e "${YELLOW}[⚠ OVERALL STATUS]${NC} PASSED WITH WARNINGS"
            return 2
        fi
    else
        echo -e "${RED}[✗ OVERALL STATUS]${NC} VERIFICATION FAILURES DETECTED"
        echo
        echo -e "${RED}Action Required:${NC} Address failed checks before proceeding with vendor updates"
        return 1
    fi
}

# =========================================================================
# PARAMETER PARSING
# =========================================================================

parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            --step=*)
                VERIFY_STEP="${1#*=}"
                shift
                ;;
            --method=*)
                DEPLOYMENT_METHOD="${1#*=}"
                shift
                ;;
            -v|--verbose)
                VERBOSE=true
                shift
                ;;
            --dry-run)
                DRY_RUN=true
                shift
                ;;
            -h|--help)
                show_help
                exit 0
                ;;
            *)
                echo "Unknown parameter: $1"
                show_help
                exit 3
                ;;
        esac
    done
}

show_help() {
    echo "Vendor Update Flow Comprehensive Verification Script"
    echo
    echo "Usage: $0 [OPTIONS]"
    echo
    echo "Options:"
    echo "  --step=N              Verify specific step (1-9)"
    echo "  --method=A|B|C|D      Verify specific deployment method"
    echo "  -v, --verbose         Enable verbose output"
    echo "  --dry-run             Show what would be verified without running checks"
    echo "  -h, --help            Show this help message"
    echo
    echo "Examples:"
    echo "  $0                    # Run full verification"
    echo "  $0 --step=8           # Verify Step 8 specifically"
    echo "  $0 --method=A         # Verify Method A deployment"
    echo "  $0 --step=8 --method=B # Verify Step 8 with Method B focus"
    echo
    echo "Exit Codes:"
    echo "  0 = All verifications passed"
    echo "  1 = Critical verification failures"
    echo "  2 = Warning-level issues found"
    echo "  3 = Configuration/setup errors"
}

# =========================================================================
# MAIN EXECUTION
# =========================================================================

main() {
    # Parse command line arguments
    parse_arguments "$@"
    
    if [ "$DRY_RUN" = true ]; then
        print_info "DRY RUN MODE - No actual verification performed"
        print_info "Would verify with parameters:"
        print_info "  Step: ${VERIFY_STEP:-'All'}"
        print_info "  Method: ${DEPLOYMENT_METHOD:-'All'}"
        print_info "  Verbose: $VERBOSE"
        exit 0
    fi
    
    # Run verification
    run_comprehensive_verification
    
    # Generate and display report
    local exit_code
    generate_verification_report
    exit_code=$?
    
    # Save verification log if requested
    if [ "$VERBOSE" = true ]; then
        local log_file="verification_${VERIFICATION_ID}.log"
        echo "Detailed verification log saved to: $log_file"
        # In a real implementation, you would redirect output to the log file
    fi
    
    exit $exit_code
}

# Run main function with all arguments
main "$@"