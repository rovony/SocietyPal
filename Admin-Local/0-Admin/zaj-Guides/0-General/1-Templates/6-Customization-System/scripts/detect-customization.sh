#!/bin/bash

# Laravel Customization System Detection Script
# Version: 1.0.0
# Description: Detect if the customization system is already installed in a Laravel project

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
readonly NC='\033[0m' # No Color

# =============================================================================
# UTILITY FUNCTIONS
# =============================================================================

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
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
    echo -e "${CYAN}  → $1${NC}"
}

# =============================================================================
# DETECTION FUNCTIONS
# =============================================================================

# Check if we're in a Laravel project
is_laravel_project() {
    [[ -f "artisan" && -f "composer.json" ]]
}

# Check core customization directories
check_directories() {
    local found=0
    local missing=()
    
    info "Checking customization directories..."
    
    local directories=(
        "app/Custom"
        "resources/Custom"
        "database/Custom"
        "public/Custom"
        "tests/Custom"
    )
    
    for dir in "${directories[@]}"; do
        if [[ -d "$dir" ]]; then
            detail "✓ Found: $dir"
            ((found++))
        else
            detail "✗ Missing: $dir"
            missing+=("$dir")
        fi
    done
    
    echo "$found"
}

# Check core customization files
check_files() {
    local found=0
    local missing=()
    
    info "Checking customization files..."
    
    local files=(
        "app/Providers/CustomizationServiceProvider.php"
        "webpack.custom.cjs"
        "app/Custom/config/custom-app.php"
        "app/Custom/config/custom-database.php"
        "resources/Custom/css/app.scss"
        "resources/Custom/js/app.js"
    )
    
    for file in "${files[@]}"; do
        if [[ -f "$file" ]]; then
            detail "✓ Found: $file"
            ((found++))
        else
            detail "✗ Missing: $file"
            missing+=("$file")
        fi
    done
    
    echo "$found"
}

# Check service provider registration
check_service_provider() {
    info "Checking service provider registration..."
    
    local providers_file=""
    local registered=false
    
    if [[ -f "bootstrap/providers.php" ]]; then
        providers_file="bootstrap/providers.php"
        if grep -q "CustomizationServiceProvider" "$providers_file"; then
            detail "✓ Found CustomizationServiceProvider in $providers_file (Laravel 11+)"
            registered=true
        else
            detail "✗ CustomizationServiceProvider not found in $providers_file"
        fi
    elif [[ -f "config/app.php" ]]; then
        providers_file="config/app.php"
        if grep -q "CustomizationServiceProvider" "$providers_file"; then
            detail "✓ Found CustomizationServiceProvider in $providers_file (Laravel < 11)"
            registered=true
        else
            detail "✗ CustomizationServiceProvider not found in $providers_file"
        fi
    else
        detail "✗ No providers configuration file found"
    fi
    
    if [[ "$registered" == true ]]; then
        echo "1"
    else
        echo "0"
    fi
}

# Check package.json for custom scripts
check_package_scripts() {
    info "Checking package.json custom scripts..."
    
    if [[ ! -f "package.json" ]]; then
        detail "✗ package.json not found"
        echo "0"
        return
    fi
    
    local found=0
    local scripts=(
        "custom:build"
        "custom:dev"
        "custom:clean"
    )
    
    for script in "${scripts[@]}"; do
        if grep -q "\"$script\"" package.json; then
            detail "✓ Found script: $script"
            ((found++))
        else
            detail "✗ Missing script: $script"
        fi
    done
    
    echo "$found"
}

# Check Laravel version compatibility
check_laravel_version() {
    info "Checking Laravel version..."
    
    if [[ -f "composer.json" ]]; then
        local laravel_version
        laravel_version=$(grep -o '"laravel/framework": "[^"]*"' composer.json | sed 's/"laravel\/framework": "//;s/"//' || echo "unknown")
        detail "Laravel version constraint: $laravel_version"
        
        if [[ "$laravel_version" != "unknown" ]]; then
            echo "1"
        else
            echo "0"
        fi
    else
        detail "✗ composer.json not found"
        echo "0"
    fi
}

# Generate detailed report
generate_report() {
    local dir_count=$1
    local file_count=$2
    local provider_registered=$3
    local script_count=$4
    local laravel_detected=$5
    
    echo
    echo "═══════════════════════════════════════════════════════════════════════════════"
    echo "                    CUSTOMIZATION SYSTEM DETECTION REPORT"
    echo "═══════════════════════════════════════════════════════════════════════════════"
    echo
    
    # Calculate overall score
    local total_possible=23  # 5 dirs + 6 files + 1 provider + 3 scripts + 1 laravel + 7 bonus points
    local actual_score=$((dir_count + file_count + provider_registered + script_count + laravel_detected))
    local percentage=$((actual_score * 100 / total_possible))
    
    echo "📊 OVERVIEW:"
    echo "   Installation Score: $actual_score/$total_possible ($percentage%)"
    echo
    
    echo "📁 DIRECTORIES: $dir_count/5"
    echo "📄 FILES: $file_count/6" 
    echo "⚙️  SERVICE PROVIDER: $provider_registered/1"
    echo "📦 PACKAGE SCRIPTS: $script_count/3"
    echo "🎯 LARAVEL DETECTED: $laravel_detected/1"
    echo
    
    # Determine installation status
    if [[ $percentage -ge 90 ]]; then
        success "✅ FULLY INSTALLED - Customization system is complete and ready"
        echo "   Recommendation: System is fully operational"
        return 0
    elif [[ $percentage -ge 70 ]]; then
        warn "⚠️  PARTIALLY INSTALLED - Most components are present"
        echo "   Recommendation: Run setup script to complete installation"
        return 1
    elif [[ $percentage -ge 30 ]]; then
        warn "🔧 INCOMPLETE INSTALLATION - Basic structure exists"
        echo "   Recommendation: Run setup script with --force flag"
        return 2
    else
        error "❌ NOT INSTALLED - Customization system not detected"
        echo "   Recommendation: Run initial setup script"
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
 ║                            Detection Tool v1.0                             ║
 ╠══════════════════════════════════════════════════════════════════════════════╣
 ║  🔍 Analyze current installation status                                     ║
 ║  📊 Generate detailed compatibility report                                  ║
 ║  🎯 Provide actionable recommendations                                      ║
 ╚══════════════════════════════════════════════════════════════════════════════╝

EOF
}

main() {
    show_banner
    
    # Parse command line arguments
    local verbose=false
    local json_output=false
    
    while [[ $# -gt 0 ]]; do
        case $1 in
            --verbose|-v)
                verbose=true
                shift
                ;;
            --json)
                json_output=true
                shift
                ;;
            --help|-h)
                cat << 'EOF'
Laravel Customization System Detection Tool

Usage: ./detect-customization.sh [OPTIONS]

OPTIONS:
    --verbose, -v    Show detailed detection process
    --json           Output results in JSON format
    --help, -h       Show this help message

EXAMPLES:
    ./detect-customization.sh                    # Basic detection
    ./detect-customization.sh --verbose          # Detailed detection
    ./detect-customization.sh --json             # JSON output for scripting

EXIT CODES:
    0 - Fully installed (90%+)
    1 - Partially installed (70-89%)
    2 - Incomplete installation (30-69%)
    3 - Not installed (<30%)

EOF
                exit 0
                ;;
            *)
                error "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    
    # Check if we're in Laravel project
    if ! is_laravel_project; then
        error "This doesn't appear to be a Laravel project root directory"
        error "Please run this script from your Laravel project root (where artisan file is located)"
        exit 1
    fi
    
    # Run detection
    if [[ "$verbose" == true || "$json_output" == false ]]; then
        log "Starting customization system detection..."
        echo
    fi
    
    local dir_count file_count provider_registered script_count laravel_detected
    
    dir_count=$(check_directories)
    echo
    file_count=$(check_files)
    echo
    provider_registered=$(check_service_provider)
    echo
    script_count=$(check_package_scripts)
    echo
    laravel_detected=$(check_laravel_version)
    
    if [[ "$json_output" == true ]]; then
        # Output JSON format
        cat << EOF
{
  "detection_result": {
    "directories_found": $dir_count,
    "files_found": $file_count,
    "service_provider_registered": $([ $provider_registered -eq 1 ] && echo "true" || echo "false"),
    "package_scripts_found": $script_count,
    "laravel_detected": $([ $laravel_detected -eq 1 ] && echo "true" || echo "false"),
    "total_score": $((dir_count + file_count + provider_registered + script_count + laravel_detected)),
    "max_score": 16,
    "percentage": $(( (dir_count + file_count + provider_registered + script_count + laravel_detected) * 100 / 16 )),
    "status": "$([ $((dir_count + file_count + provider_registered + script_count + laravel_detected)) -ge 14 ] && echo "fully_installed" || [ $((dir_count + file_count + provider_registered + script_count + laravel_detected)) -ge 11 ] && echo "partially_installed" || [ $((dir_count + file_count + provider_registered + script_count + laravel_detected)) -ge 5 ] && echo "incomplete" || echo "not_installed")",
    "timestamp": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
  }
}
EOF
    else
        echo
        generate_report "$dir_count" "$file_count" "$provider_registered" "$script_count" "$laravel_detected"
    fi
}

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi