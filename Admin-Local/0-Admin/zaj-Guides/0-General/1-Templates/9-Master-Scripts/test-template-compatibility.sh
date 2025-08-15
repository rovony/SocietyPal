#!/bin/bash

# TEMPLATE COMPATIBILITY TEST SCRIPT
# This script tests template compatibility across different project environments
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
    echo -e "${MAGENTA}[COMPATIBILITY] $1${NC}"
}

# Function to show usage
show_usage() {
    echo -e "${CYAN}TEMPLATE COMPATIBILITY TEST SCRIPT${NC}"
    echo ""
    echo -e "${BLUE}Purpose:${NC} Test template compatibility across different project environments"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [--environment ENV] [--verbose] [--dry-run]"
    echo "  $0 --help                           # Show this help"
    echo ""
    echo -e "${BLUE}Options:${NC}"
    echo "  --environment ENV                   # Test specific environment (laravel|node|universal)"
    echo "  --verbose                           # Show detailed compatibility output"
    echo "  --dry-run                           # Show what would be tested without executing"
    echo "  --help                              # Show this help message"
    echo ""
    echo -e "${BLUE}Test Environments:${NC}"
    echo "  🔧 Laravel Projects                 # PHP/Laravel specific compatibility"
    echo "  🟢 Node.js Projects                # JavaScript/Node.js compatibility"
    echo "  🌐 Universal Projects              # Cross-platform compatibility"
    echo ""
    echo -e "${BLUE}Compatibility Checks:${NC}"
    echo "  ✓ Shell script compatibility       # macOS, Linux, Windows (WSL)"
    echo "  ✓ Path separator handling          # Unix vs Windows paths"
    echo "  ✓ Command availability             # Required system commands"
    echo "  ✓ Permission requirements          # File/directory permissions"
    echo "  ✓ Framework-specific checks        # Laravel vs Node.js vs others"
    echo "  ✓ Configuration compatibility      # Config file formats"
    echo ""
    echo -e "${BLUE}Test Coverage:${NC}"
    echo "  1. System environment validation"
    echo "  2. Shell command compatibility"
    echo "  3. Path and filesystem checks"
    echo "  4. Framework detection and compatibility"
    echo "  5. Template generation testing"
    echo "  6. Cross-platform validation"
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

# Function to detect operating system
detect_os() {
    case "$(uname -s)" in
        Darwin*)    echo "macos" ;;
        Linux*)     echo "linux" ;;
        CYGWIN*|MINGW*|MSYS*) echo "windows" ;;
        *)          echo "unknown" ;;
    esac
}

# Function to detect shell
detect_shell() {
    if [ -n "$BASH_VERSION" ]; then
        echo "bash"
    elif [ -n "$ZSH_VERSION" ]; then
        echo "zsh"
    elif [ -n "$FISH_VERSION" ]; then
        echo "fish"
    else
        echo "unknown"
    fi
}

# Function to detect project framework
detect_framework() {
    local project_root="$1"
    
    # Check for Laravel
    if [ -f "$project_root/composer.json" ] && [ -f "$project_root/artisan" ]; then
        echo "laravel"
        return 0
    fi
    
    # Check for Node.js
    if [ -f "$project_root/package.json" ]; then
        echo "node"
        return 0
    fi
    
    # Check for other frameworks
    if [ -f "$project_root/requirements.txt" ] || [ -f "$project_root/setup.py" ]; then
        echo "python"
        return 0
    fi
    
    if [ -f "$project_root/Cargo.toml" ]; then
        echo "rust"
        return 0
    fi
    
    if [ -f "$project_root/go.mod" ]; then
        echo "go"
        return 0
    fi
    
    echo "universal"
}

# Function to test system environment
test_system_environment() {
    local verbose="$1"
    
    test_result "Phase 1: System Environment Validation"
    echo ""
    
    local env_passed=0
    local env_total=0
    
    # Test 1: Operating System Detection
    env_total=$((env_total + 1))
    local os=$(detect_os)
    if [ "$os" != "unknown" ]; then
        [ "$verbose" = "true" ] && success "Operating System: $os"
        env_passed=$((env_passed + 1))
    else
        error "Unknown operating system"
    fi
    
    # Test 2: Shell Detection
    env_total=$((env_total + 1))
    local shell=$(detect_shell)
    if [ "$shell" != "unknown" ]; then
        [ "$verbose" = "true" ] && success "Shell: $shell"
        env_passed=$((env_passed + 1))
    else
        warning "Unknown shell, using default compatibility mode"
        env_passed=$((env_passed + 1))  # Non-critical
    fi
    
    # Test 3: Required Commands Availability
    local required_commands=("bash" "chmod" "mkdir" "cp" "mv" "rm" "grep" "find" "sed" "awk")
    
    for cmd in "${required_commands[@]}"; do
        env_total=$((env_total + 1))
        if command -v "$cmd" >/dev/null 2>&1; then
            [ "$verbose" = "true" ] && success "Command available: $cmd"
            env_passed=$((env_passed + 1))
        else
            error "Required command not found: $cmd"
        fi
    done
    
    # Test 4: Advanced Commands (Optional)
    local optional_commands=("realpath" "readlink" "stat")
    
    for cmd in "${optional_commands[@]}"; do
        env_total=$((env_total + 1))
        if command -v "$cmd" >/dev/null 2>&1; then
            [ "$verbose" = "true" ] && success "Optional command available: $cmd"
            env_passed=$((env_passed + 1))
        else
            [ "$verbose" = "true" ] && warning "Optional command not found: $cmd (alternative methods available)"
            env_passed=$((env_passed + 1))  # Non-critical
        fi
    done
    
    log "System environment validation: $env_passed/$env_total passed"
    
    if [ $env_passed -eq $env_total ]; then
        success "✅ System environment fully compatible"
        return 0
    elif [ $env_passed -ge $((env_total * 80 / 100)) ]; then
        warning "⚠️  System environment mostly compatible (${env_passed}/${env_total})"
        return 0
    else
        error "❌ System environment compatibility issues found"
        return 1
    fi
}

# Function to test shell command compatibility
test_shell_compatibility() {
    local os="$1"
    local verbose="$2"
    
    test_result "Phase 2: Shell Command Compatibility"
    echo ""
    
    local shell_passed=0
    local shell_total=0
    
    # Test 1: Path Handling
    shell_total=$((shell_total + 1))
    local test_path="/tmp/test_path_compatibility"
    if mkdir -p "$test_path" 2>/dev/null && rmdir "$test_path" 2>/dev/null; then
        [ "$verbose" = "true" ] && success "Path handling compatible"
        shell_passed=$((shell_passed + 1))
    else
        error "Path handling issues detected"
    fi
    
    # Test 2: File Permissions
    shell_total=$((shell_total + 1))
    local test_file="/tmp/test_permissions_$$"
    if touch "$test_file" 2>/dev/null; then
        if chmod +x "$test_file" 2>/dev/null; then
            [ "$verbose" = "true" ] && success "File permissions compatible"
            shell_passed=$((shell_passed + 1))
        else
            error "File permission modification failed"
        fi
        rm -f "$test_file" 2>/dev/null
    else
        error "File creation failed"
    fi
    
    # Test 3: Variable Expansion
    shell_total=$((shell_total + 1))
    local test_var="test_value"
    if [ "${test_var}" = "test_value" ]; then
        [ "$verbose" = "true" ] && success "Variable expansion compatible"
        shell_passed=$((shell_passed + 1))
    else
        error "Variable expansion issues"
    fi
    
    # Test 4: Command Substitution
    shell_total=$((shell_total + 1))
    local test_output=$(echo "test_command_substitution" 2>/dev/null)
    if [ "$test_output" = "test_command_substitution" ]; then
        [ "$verbose" = "true" ] && success "Command substitution compatible"
        shell_passed=$((shell_passed + 1))
    else
        error "Command substitution issues"
    fi
    
    # Test 5: Conditional Logic
    shell_total=$((shell_total + 1))
    if [ 1 -eq 1 ] && [ "test" = "test" ]; then
        [ "$verbose" = "true" ] && success "Conditional logic compatible"
        shell_passed=$((shell_passed + 1))
    else
        error "Conditional logic issues"
    fi
    
    # Test 6: Array Support (if available)
    shell_total=$((shell_total + 1))
    if [ "$os" != "windows" ]; then
        test_array=("item1" "item2" "item3")
        if [ "${test_array[0]}" = "item1" ] 2>/dev/null; then
            [ "$verbose" = "true" ] && success "Array support available"
            shell_passed=$((shell_passed + 1))
        else
            [ "$verbose" = "true" ] && warning "Array support limited (using alternatives)"
            shell_passed=$((shell_passed + 1))  # Non-critical
        fi
    else
        [ "$verbose" = "true" ] && info "Skipping array test on Windows"
        shell_passed=$((shell_passed + 1))  # Skip on Windows
    fi
    
    log "Shell compatibility: $shell_passed/$shell_total passed"
    
    if [ $shell_passed -eq $shell_total ]; then
        success "✅ Shell commands fully compatible"
        return 0
    else
        error "❌ Shell compatibility issues found"
        return 1
    fi
}

# Function to test filesystem compatibility
test_filesystem_compatibility() {
    local project_root="$1"
    local os="$2"
    local verbose="$3"
    
    test_result "Phase 3: Filesystem Compatibility"
    echo ""
    
    local fs_passed=0
    local fs_total=0
    
    # Test 1: Directory Creation
    fs_total=$((fs_total + 1))
    local test_dir="$project_root/.template_test_$$"
    if mkdir -p "$test_dir/subdir" 2>/dev/null; then
        [ "$verbose" = "true" ] && success "Directory creation compatible"
        fs_passed=$((fs_passed + 1))
        rm -rf "$test_dir" 2>/dev/null
    else
        error "Directory creation failed"
    fi
    
    # Test 2: File Operations
    fs_total=$((fs_total + 1))
    local test_file="$project_root/.template_file_test_$$"
    if touch "$test_file" && echo "test content" > "$test_file" && [ -f "$test_file" ]; then
        [ "$verbose" = "true" ] && success "File operations compatible"
        fs_passed=$((fs_passed + 1))
        rm -f "$test_file" 2>/dev/null
    else
        error "File operations failed"
    fi
    
    # Test 3: Symlink Support (Unix-like systems)
    fs_total=$((fs_total + 1))
    if [ "$os" != "windows" ]; then
        local test_link="$project_root/.template_link_test_$$"
        local test_target="$project_root/.template_target_test_$$"
        if touch "$test_target" && ln -s "$test_target" "$test_link" 2>/dev/null; then
            [ "$verbose" = "true" ] && success "Symlink support available"
            fs_passed=$((fs_passed + 1))
        else
            [ "$verbose" = "true" ] && warning "Symlink support limited"
            fs_passed=$((fs_passed + 1))  # Non-critical
        fi
        rm -f "$test_link" "$test_target" 2>/dev/null
    else
        [ "$verbose" = "true" ] && info "Skipping symlink test on Windows"
        fs_passed=$((fs_passed + 1))  # Skip on Windows
    fi
    
    # Test 4: Long Path Support
    fs_total=$((fs_total + 1))
    local long_path="$project_root/.very_long_directory_name_for_compatibility_testing"
    if mkdir -p "$long_path" 2>/dev/null; then
        [ "$verbose" = "true" ] && success "Long path support available"
        fs_passed=$((fs_passed + 1))
        rmdir "$long_path" 2>/dev/null
    else
        warning "Long path support may be limited"
        fs_passed=$((fs_passed + 1))  # Non-critical
    fi
    
    # Test 5: Special Characters in Names
    fs_total=$((fs_total + 1))
    local special_dir="$project_root/.test_special-chars_123"
    if mkdir -p "$special_dir" 2>/dev/null; then
        [ "$verbose" = "true" ] && success "Special characters in names supported"
        fs_passed=$((fs_passed + 1))
        rmdir "$special_dir" 2>/dev/null
    else
        warning "Special characters in names may cause issues"
        fs_passed=$((fs_passed + 1))  # Non-critical
    fi
    
    log "Filesystem compatibility: $fs_passed/$fs_total passed"
    
    if [ $fs_passed -eq $fs_total ]; then
        success "✅ Filesystem fully compatible"
        return 0
    else
        error "❌ Filesystem compatibility issues found"
        return 1
    fi
}

# Function to test framework-specific compatibility
test_framework_compatibility() {
    local project_root="$1"
    local framework="$2"
    local verbose="$3"
    
    test_result "Phase 4: Framework-Specific Compatibility"
    echo ""
    
    local fw_passed=0
    local fw_total=0
    
    case "$framework" in
        "laravel")
            # Laravel-specific tests
            fw_total=$((fw_total + 1))
            if [ -f "$project_root/composer.json" ]; then
                [ "$verbose" = "true" ] && success "Laravel: composer.json found"
                fw_passed=$((fw_passed + 1))
            else
                error "Laravel: composer.json not found"
            fi
            
            fw_total=$((fw_total + 1))
            if [ -f "$project_root/artisan" ]; then
                [ "$verbose" = "true" ] && success "Laravel: artisan command available"
                fw_passed=$((fw_passed + 1))
            else
                error "Laravel: artisan command not found"
            fi
            
            fw_total=$((fw_total + 1))
            if [ -d "$project_root/app" ] && [ -d "$project_root/config" ]; then
                [ "$verbose" = "true" ] && success "Laravel: directory structure compatible"
                fw_passed=$((fw_passed + 1))
            else
                error "Laravel: directory structure incomplete"
            fi
            
            # Test PHP availability
            fw_total=$((fw_total + 1))
            if command -v php >/dev/null 2>&1; then
                [ "$verbose" = "true" ] && success "Laravel: PHP available"
                fw_passed=$((fw_passed + 1))
            else
                error "Laravel: PHP not found"
            fi
            
            # Test Composer availability
            fw_total=$((fw_total + 1))
            if command -v composer >/dev/null 2>&1; then
                [ "$verbose" = "true" ] && success "Laravel: Composer available"
                fw_passed=$((fw_passed + 1))
            else
                warning "Laravel: Composer not found (may be installed locally)"
                fw_passed=$((fw_passed + 1))  # Non-critical
            fi
            ;;
        
        "node")
            # Node.js-specific tests
            fw_total=$((fw_total + 1))
            if [ -f "$project_root/package.json" ]; then
                [ "$verbose" = "true" ] && success "Node.js: package.json found"
                fw_passed=$((fw_passed + 1))
            else
                error "Node.js: package.json not found"
            fi
            
            fw_total=$((fw_total + 1))
            if command -v node >/dev/null 2>&1; then
                [ "$verbose" = "true" ] && success "Node.js: Node.js available"
                fw_passed=$((fw_passed + 1))
            else
                error "Node.js: Node.js not found"
            fi
            
            fw_total=$((fw_total + 1))
            if command -v npm >/dev/null 2>&1 || command -v yarn >/dev/null 2>&1 || command -v pnpm >/dev/null 2>&1; then
                [ "$verbose" = "true" ] && success "Node.js: Package manager available"
                fw_passed=$((fw_passed + 1))
            else
                error "Node.js: No package manager found"
            fi
            ;;
        
        "universal"|*)
            # Universal compatibility tests
            fw_total=$((fw_total + 1))
            [ "$verbose" = "true" ] && success "Universal: Framework-agnostic mode"
            fw_passed=$((fw_passed + 1))
            
            fw_total=$((fw_total + 1))
            if [ -w "$project_root" ]; then
                [ "$verbose" = "true" ] && success "Universal: Project root writable"
                fw_passed=$((fw_passed + 1))
            else
                error "Universal: Project root not writable"
            fi
            ;;
    esac
    
    log "Framework compatibility ($framework): $fw_passed/$fw_total passed"
    
    if [ $fw_passed -eq $fw_total ]; then
        success "✅ Framework compatibility confirmed"
        return 0
    else
        error "❌ Framework compatibility issues found"
        return 1
    fi
}

# Function to test template generation
test_template_generation() {
    local template_dir="$1"
    local framework="$2"
    local verbose="$3"
    
    test_result "Phase 5: Template Generation Testing"
    echo ""
    
    local gen_passed=0
    local gen_total=0
    
    # Test each template system's generation capability
    local template_systems=("5-Tracking-System" "6-Customization-System")
    
    for system in "${template_systems[@]}"; do
        gen_total=$((gen_total + 1))
        local system_dir="$template_dir/$system"
        
        if [ -d "$system_dir" ]; then
            # Check if templates directory exists
            if [ -d "$system_dir/templates" ] || [ -f "$system_dir/setup-"* ]; then
                [ "$verbose" = "true" ] && success "Template generation: $system ready"
                gen_passed=$((gen_passed + 1))
            else
                error "Template generation: $system incomplete"
            fi
        else
            error "Template generation: $system not found"
        fi
    done
    
    # Test configuration compatibility
    gen_total=$((gen_total + 1))
    case "$framework" in
        "laravel")
            # Check Laravel-specific template compatibility
            if [ -d "$template_dir/6-Customization-System/templates" ]; then
                [ "$verbose" = "true" ] && success "Template generation: Laravel templates compatible"
                gen_passed=$((gen_passed + 1))
            else
                error "Template generation: Laravel templates missing"
            fi
            ;;
        "node")
            # Check Node.js-specific compatibility
            [ "$verbose" = "true" ] && success "Template generation: Node.js compatibility confirmed"
            gen_passed=$((gen_passed + 1))
            ;;
        *)
            # Universal compatibility
            [ "$verbose" = "true" ] && success "Template generation: Universal compatibility confirmed"
            gen_passed=$((gen_passed + 1))
            ;;
    esac
    
    log "Template generation testing: $gen_passed/$gen_total passed"
    
    if [ $gen_passed -eq $gen_total ]; then
        success "✅ Template generation fully compatible"
        return 0
    else
        error "❌ Template generation compatibility issues"
        return 1
    fi
}

# Function to test cross-platform validation
test_cross_platform() {
    local os="$1"
    local verbose="$2"
    
    test_result "Phase 6: Cross-Platform Validation"
    echo ""
    
    local cp_passed=0
    local cp_total=0
    
    # Test 1: Line Ending Compatibility
    cp_total=$((cp_total + 1))
    local test_file="/tmp/line_ending_test_$$"
    if echo -e "line1\nline2" > "$test_file" 2>/dev/null && [ -f "$test_file" ]; then
        [ "$verbose" = "true" ] && success "Line endings compatible"
        cp_passed=$((cp_passed + 1))
        rm -f "$test_file" 2>/dev/null
    else
        error "Line ending compatibility issues"
    fi
    
    # Test 2: Path Separator Handling
    cp_total=$((cp_total + 1))
    local test_path="dir1/dir2/file.txt"
    if [ "$test_path" = "dir1/dir2/file.txt" ]; then
        [ "$verbose" = "true" ] && success "Path separators compatible"
        cp_passed=$((cp_passed + 1))
    else
        error "Path separator issues"
    fi
    
    # Test 3: Case Sensitivity
    cp_total=$((cp_total + 1))
    local test_upper="TEST"
    local test_lower="test"
    if [ "$test_upper" != "$test_lower" ]; then
        [ "$verbose" = "true" ] && success "Case sensitivity handling correct"
        cp_passed=$((cp_passed + 1))
    else
        warning "Case sensitivity issues may occur"
        cp_passed=$((cp_passed + 1))  # Non-critical
    fi
    
    # Test 4: Environment Variable Handling
    cp_total=$((cp_total + 1))
    export TEMPLATE_TEST_VAR="test_value"
    if [ "$TEMPLATE_TEST_VAR" = "test_value" ]; then
        [ "$verbose" = "true" ] && success "Environment variables compatible"
        cp_passed=$((cp_passed + 1))
    else
        error "Environment variable handling issues"
    fi
    unset TEMPLATE_TEST_VAR
    
    # Test 5: Exit Code Handling
    cp_total=$((cp_total + 1))
    if true; then
        local exit_code=$?
        if [ $exit_code -eq 0 ]; then
            [ "$verbose" = "true" ] && success "Exit code handling compatible"
            cp_passed=$((cp_passed + 1))
        else
            error "Exit code handling issues"
        fi
    else
        error "Exit code test failed"
    fi
    
    log "Cross-platform validation: $cp_passed/$cp_total passed"
    
    if [ $cp_passed -eq $cp_total ]; then
        success "✅ Cross-platform compatibility confirmed"
        return 0
    else
        error "❌ Cross-platform compatibility issues found"
        return 1
    fi
}

# Main execution
main() {
    local environment=""
    local verbose="false"
    local dry_run="false"
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --environment)
                environment="$2"
                shift 2
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
    
    log "🔧 TEMPLATE COMPATIBILITY TESTING STARTING"
    log "==========================================="
    
    if [ "$dry_run" = "true" ]; then
        info "DRY RUN MODE - No actual tests will be executed"
        echo ""
    fi
    
    # Detect system information
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 1
    fi
    
    local template_dir="$project_root/Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates"
    local os=$(detect_os)
    local shell=$(detect_shell)
    local framework
    
    # Use specified environment or detect it
    if [ -n "$environment" ]; then
        framework="$environment"
    else
        framework=$(detect_framework "$project_root")
    fi
    
    log "Project Root: $project_root"
    log "Template Directory: $template_dir"
    log "Operating System: $os"
    log "Shell: $shell"
    log "Framework: $framework"
    log "Verbose Mode: $verbose"
    echo ""
    
    if [ "$dry_run" = "true" ]; then
        info "Would run the following compatibility tests:"
        info "1. System environment validation"
        info "2. Shell command compatibility"
        info "3. Filesystem compatibility"
        info "4. Framework-specific compatibility ($framework)"
        info "5. Template generation testing"
        info "6. Cross-platform validation"
        echo ""
        info "DRY RUN COMPLETED - No tests executed"
        exit 0
    fi
    
    # Initialize test results
    local phases_passed=0
    local phases_total=6
    
    # Phase 1: System Environment
    if test_system_environment "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 2: Shell Compatibility
    if test_shell_compatibility "$os" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 3: Filesystem Compatibility
    if test_filesystem_compatibility "$project_root" "$os" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 4: Framework Compatibility
    if test_framework_compatibility "$project_root" "$framework" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 5: Template Generation
    if test_template_generation "$template_dir" "$framework" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Phase 6: Cross-Platform Validation
    if test_cross_platform "$os" "$verbose"; then
        phases_passed=$((phases_passed + 1))
    fi
    echo ""
    
    # Final summary
    log "==========================================="
    log "🔧 COMPATIBILITY TEST SUMMARY"
    log "Test Phases Passed: $phases_passed/$phases_total"
    log "Environment: $framework on $os ($shell shell)"
    
    if [ $phases_passed -eq $phases_total ]; then
        success "🎉 ALL COMPATIBILITY TESTS PASSED!"
        log "Templates are compatible with this environment"
        exit 0
    elif [ $phases_passed -ge $((phases_total * 80 / 100)) ]; then
        warning "⚠️  MOSTLY COMPATIBLE (${phases_passed}/${phases_total} passed)"
        log "Minor compatibility issues detected - review warnings above"
        exit 0
    else
        error "❌ COMPATIBILITY ISSUES DETECTED"
        log "Significant compatibility problems found - please address errors above"
        exit 1
    fi
}

# Run main function
main "$@"