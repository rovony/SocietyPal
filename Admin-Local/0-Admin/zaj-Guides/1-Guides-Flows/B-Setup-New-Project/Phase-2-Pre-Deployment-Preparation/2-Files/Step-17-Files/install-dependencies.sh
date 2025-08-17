#!/bin/bash

# Step 17 - Install Custom Dependencies Script
# Universal Laravel project dependency installer for customization system
# Author: Auto-generated Step 17 Enhancement
# Usage: bash install-dependencies.sh

set -euo pipefail

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

# Function to check if package is installed
is_package_installed() {
    local package_name="$1"
    npm list "$package_name" 2>/dev/null | grep -q "$package_name@" || return 1
}

# Function to install npm dependencies
install_npm_dependencies() {
    log "Checking and installing NPM dependencies..."
    
    # List of required packages for customization system
    local required_packages=(
        "laravel-mix"
        "sass"
        "sass-loader"
    )
    
    local missing_packages=()
    
    # Check which packages are missing
    for package in "${required_packages[@]}"; do
        if ! is_package_installed "$package"; then
            missing_packages+=("$package")
        else
            success "$package is already installed"
        fi
    done
    
    # Install missing packages
    if [ ${#missing_packages[@]} -eq 0 ]; then
        success "All required NPM packages are already installed"
    else
        log "Installing missing packages: ${missing_packages[*]}"
        npm install --save-dev "${missing_packages[@]}" || error "Failed to install NPM packages"
        success "NPM dependencies installed successfully"
    fi
}

# Function to verify npm installation
verify_npm_installation() {
    log "Verifying NPM installation..."
    
    # Check if npm is available
    if ! command -v npm &> /dev/null; then
        error "NPM is not installed or not in PATH"
    fi
    
    # Check if package.json exists
    if [ ! -f "package.json" ]; then
        error "package.json not found. Are you in the Laravel project root?"
    fi
    
    success "NPM environment verified"
}

# Function to run npm install
run_npm_install() {
    log "Running npm install to ensure all dependencies are up to date..."
    
    if npm install; then
        success "npm install completed successfully"
    else
        error "npm install failed"
    fi
}

# Main execution
main() {
    log "🚀 Starting Step 17 Dependency Installation"
    echo "================================================"
    
    # Verify environment
    verify_npm_installation
    
    # Run npm install first
    run_npm_install
    
    # Install custom dependencies
    install_npm_dependencies
    
    echo "================================================"
    success "✨ Step 17 dependency installation completed successfully!"
    echo ""
    log "Next step: Run 'npm run custom:build' to build custom assets"
}

# Execute main function
main "$@"