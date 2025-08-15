#!/bin/bash

# Laravel Customization System Setup Script
# Version: 1.0.0
# Description: Automated setup of the customization protection system for any Laravel project

set -euo pipefail  # Exit on error, undefined variables, pipe failures

# =============================================================================
# CONFIGURATION
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(pwd)"
TEMPLATE_DIR="${SCRIPT_DIR}/templates"
SCRIPTS_DIR="${SCRIPT_DIR}/scripts"

# Colors for output
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
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

# Check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check if we're in a Laravel project
is_laravel_project() {
    [[ -f "artisan" && -f "composer.json" ]]
}

# Check if customization system is already installed
is_already_installed() {
    [[ -d "app/Custom" && -f "app/Providers/CustomizationServiceProvider.php" && -f "webpack.custom.js" ]]
}

# =============================================================================
# MAIN FUNCTIONS
# =============================================================================

# Display banner
show_banner() {
    cat << 'EOF'
 ╔══════════════════════════════════════════════════════════════════════════════╗
 ║                       Laravel Customization System                          ║
 ║                              Setup Script v1.0                             ║
 ╠══════════════════════════════════════════════════════════════════════════════╣
 ║  🛡️  Complete isolation from vendor files                                  ║
 ║  🚀  Production-ready customization system                                  ║
 ║  🔧  Auto-detection and smart setup                                         ║
 ║  📦  Reusable across any Laravel project                                    ║
 ╚══════════════════════════════════════════════════════════════════════════════╝
EOF
    echo
}

# Pre-flight checks
preflight_checks() {
    log "Running pre-flight checks..."
    
    # Check if we're in Laravel project root
    if ! is_laravel_project; then
        error "This doesn't appear to be a Laravel project root directory"
        error "Please run this script from your Laravel project root (where artisan file is located)"
        exit 1
    fi
    
    # Check required commands
    local required_commands=("php" "composer")
    for cmd in "${required_commands[@]}"; do
        if ! command_exists "$cmd"; then
            error "Required command '$cmd' not found. Please install it first."
            exit 1
        fi
    done
    
    # Check PHP version (Laravel 8+ requires PHP 7.3+)
    local php_version
    php_version=$(php -r "echo PHP_VERSION;" 2>/dev/null)
    if [[ -n "$php_version" ]]; then
        info "PHP Version: $php_version"
    fi
    
    # Check if already installed
    if is_already_installed; then
        warn "Customization system appears to be already installed!"
        echo -n "Do you want to reinstall/update? (y/N): "
        read -r response
        if [[ ! "$response" =~ ^[Yy]$ ]]; then
            info "Installation cancelled by user."
            exit 0
        fi
        warn "Continuing with reinstallation..."
    fi
    
    success "Pre-flight checks passed!"
}

# Create directory structure
create_directory_structure() {
    log "Creating directory structure..."
    
    local directories=(
        "app/Custom/Commands"
        "app/Custom/Controllers"
        "app/Custom/Models"
        "app/Custom/Services"
        "app/Custom/Middleware"
        "app/Custom/Policies"
        "app/Custom/Rules"
        "app/Custom/Jobs"
        "app/Custom/Listeners"
        "app/Custom/Observers"
        "app/Custom/Traits"
        "app/Custom/Helpers"
        "app/Custom/Scripts"
        "app/Custom/Documentation"
        "app/Custom/config"
        "resources/Custom/css/utilities"
        "resources/Custom/js/components"
        "resources/Custom/views"
        "resources/Custom/images"
        "resources/Custom/fonts"
        "database/Custom/migrations"
        "database/Custom/seeders"
        "database/Custom/factories"
        "public/Custom/js"
        "public/Custom/css"
        "public/Custom/images"
        "public/Custom/fonts"
        "tests/Custom/Unit"
        "tests/Custom/Feature"
    )
    
    for dir in "${directories[@]}"; do
        if [[ ! -d "$dir" ]]; then
            mkdir -p "$dir"
            info "Created directory: $dir"
        fi
    done
    
    success "Directory structure created!"
}

# Copy template files
copy_template_files() {
    log "Copying template files..."
    
    if [[ ! -d "$TEMPLATE_DIR" ]]; then
        error "Template directory not found: $TEMPLATE_DIR"
        exit 1
    fi
    
    # Copy all template files, preserving structure
    find "$TEMPLATE_DIR" -type f -name "*.php" -o -name "*.js" -o -name "*.scss" -o -name "*.json" | while read -r file; do
        # Get relative path from template directory
        relative_path="${file#$TEMPLATE_DIR/}"
        target_path="$PROJECT_ROOT/$relative_path"
        target_dir=$(dirname "$target_path")
        
        # Create target directory if it doesn't exist
        [[ ! -d "$target_dir" ]] && mkdir -p "$target_dir"
        
        # Copy file
        cp "$file" "$target_path"
        info "Copied: $relative_path"
    done
    
    success "Template files copied!"
}

# Register service provider
register_service_provider() {
    log "Registering CustomizationServiceProvider..."
    
    local providers_file="bootstrap/providers.php"
    
    if [[ ! -f "$providers_file" ]]; then
        # Laravel < 11 - use config/app.php
        providers_file="config/app.php"
        if [[ ! -f "$providers_file" ]]; then
            error "Could not find providers configuration file"
            exit 1
        fi
    fi
    
    # Check if already registered
    if grep -q "CustomizationServiceProvider" "$providers_file"; then
        warn "CustomizationServiceProvider already registered in $providers_file"
        return 0
    fi
    
    if [[ "$providers_file" == "bootstrap/providers.php" ]]; then
        # Laravel 11+ format
        if grep -q "return \[" "$providers_file"; then
            # Add before the closing bracket
            sed -i.bak '/return \[/,/\];/s/\];/    App\\Providers\\CustomizationServiceProvider::class,\n];/' "$providers_file"
            success "Added CustomizationServiceProvider to $providers_file (Laravel 11+ format)"
        else
            error "Could not parse $providers_file format"
            exit 1
        fi
    else
        # Laravel < 11 format (config/app.php)
        if grep -q "'providers' => \[" "$providers_file"; then
            # Add to providers array
            sed -i.bak "/'providers' => \[/,/\],/s/\],/    App\\\\Providers\\\\CustomizationServiceProvider::class,\n    ],/" "$providers_file"
            success "Added CustomizationServiceProvider to $providers_file (Laravel < 11 format)"
        else
            error "Could not find providers array in $providers_file"
            exit 1
        fi
    fi
}

# Update package.json if needed
update_package_json() {
    log "Checking package.json for custom scripts..."
    
    if [[ -f "package.json" ]]; then
        # Check if custom build script exists
        if ! grep -q "custom:build" package.json; then
            info "Adding custom build scripts to package.json"
            # This is a simple approach - in production you might want to use jq
            warn "Please manually add these scripts to your package.json:"
            cat << 'EOF'

"scripts": {
    "custom:build": "webpack --config webpack.custom.js --mode=production",
    "custom:dev": "webpack --config webpack.custom.js --mode=development --watch",
    "custom:clean": "rm -rf public/Custom/js/* public/Custom/css/*"
}
EOF
        fi
    fi
}

# Run verification
run_verification() {
    log "Running installation verification..."
    
    if [[ -f "$SCRIPTS_DIR/verify-installation.sh" ]]; then
        bash "$SCRIPTS_DIR/verify-installation.sh"
    else
        # Basic verification
        local errors=0
        
        # Check key files exist
        local required_files=(
            "app/Custom/config/custom-app.php"
            "app/Custom/config/custom-database.php"
            "app/Providers/CustomizationServiceProvider.php"
            "webpack.custom.js"
            "resources/Custom/css/app.scss"
            "resources/Custom/js/app.js"
        )
        
        for file in "${required_files[@]}"; do
            if [[ ! -f "$file" ]]; then
                error "Required file missing: $file"
                ((errors++))
            fi
        done
        
        # Check service provider registration
        if ! grep -q "CustomizationServiceProvider" bootstrap/providers.php 2>/dev/null && 
           ! grep -q "CustomizationServiceProvider" config/app.php 2>/dev/null; then
            error "CustomizationServiceProvider not registered"
            ((errors++))
        fi
        
        if [[ $errors -eq 0 ]]; then
            success "Basic verification passed!"
        else
            error "Verification failed with $errors errors"
            exit 1
        fi
    fi
}

# Show next steps
show_next_steps() {
    cat << 'EOF'

 ╔══════════════════════════════════════════════════════════════════════════════╗
 ║                              🎉 SUCCESS!                                    ║
 ║                   Customization System Installed                            ║
 ╚══════════════════════════════════════════════════════════════════════════════╝

📋 NEXT STEPS:

1. 🏗️  BUILD CUSTOM ASSETS:
   npm run custom:build
   # or for development with watch:
   npm run custom:dev

2. 🔧 CLEAR LARAVEL CACHE:
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

3. 🧪 TEST THE SYSTEM:
   php artisan tinker
   > app('CustomizationServiceProvider')
   > config('custom-app.name')

4. 🎨 START CUSTOMIZING:
   - Add custom controllers in: app/Custom/Controllers/
   - Add custom views in: resources/Custom/views/
   - Add custom CSS in: resources/Custom/css/
   - Add custom JS in: resources/Custom/js/

5. 📖 DOCUMENTATION:
   Check the docs/ folder for detailed usage instructions.

🛡️  UPDATE SAFETY:
All custom files are isolated in Custom/ directories and will survive vendor updates!

EOF
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================

main() {
    show_banner
    
    # Parse command line arguments
    local skip_checks=false
    local force_install=false
    
    while [[ $# -gt 0 ]]; do
        case $1 in
            --skip-checks)
                skip_checks=true
                shift
                ;;
            --force)
                force_install=true
                shift
                ;;
            --help|-h)
                cat << 'EOF'
Laravel Customization System Setup

Usage: ./setup-customization.sh [OPTIONS]

OPTIONS:
    --skip-checks    Skip pre-flight checks (not recommended)
    --force          Force installation even if already installed
    --help, -h       Show this help message

EXAMPLES:
    ./setup-customization.sh                    # Normal installation
    ./setup-customization.sh --force            # Force reinstallation
    ./setup-customization.sh --skip-checks      # Skip pre-flight checks

EOF
                exit 0
                ;;
            *)
                error "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    
    # Run setup steps
    if [[ "$skip_checks" != true ]]; then
        preflight_checks
    fi
    
    create_directory_structure
    copy_template_files
    register_service_provider
    update_package_json
    run_verification
    show_next_steps
    
    success "Customization system setup completed successfully!"
}

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi