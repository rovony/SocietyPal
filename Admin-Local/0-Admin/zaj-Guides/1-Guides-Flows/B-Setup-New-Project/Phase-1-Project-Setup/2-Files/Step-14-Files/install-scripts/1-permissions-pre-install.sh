#!/bin/bash
# ===============================================
# PRE-INSTALLATION TEMPORARY PERMISSIONS
# Script: 1-permissions-pre-install.sh
# Purpose: Set temporary 777 permissions before CodeCanyon frontend installation/update
# Usage: ./1-permissions-pre-install.sh [--interactive] [--auto]
# ===============================================

set -e  # Exit on any error

# Configuration
SCRIPT_NAME="Pre-Installation Permissions"
LOG_FILE="install-permissions.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Logging function
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Error handling
error_exit() {
    log_message "${RED}❌ ERROR: $1${NC}"
    exit 1
}

# Success message
success_message() {
    log_message "${GREEN}✅ $1${NC}"
}

# Warning message  
warning_message() {
    log_message "${YELLOW}⚠️ $1${NC}"
}

# Info message
info_message() {
    log_message "${BLUE}ℹ️ $1${NC}"
}

# Header
header_message() {
    log_message "${PURPLE}🛠️ $SCRIPT_NAME${NC}"
    log_message "${BLUE}📅 $(date)${NC}"
}

# Check if running from Laravel project root
check_laravel_project() {
    if [[ ! -f "artisan" ]] || [[ ! -f "composer.json" ]]; then
        error_exit "This script must be run from Laravel project root directory (where artisan file exists)"
    fi
    
    if [[ ! -f ".env" ]] && [[ ! -f ".env.local" ]]; then
        warning_message "No .env file found - this is normal for fresh installations"
    fi
}

# Parse command line arguments
INTERACTIVE=false
AUTO_MODE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --interactive)
            INTERACTIVE=true
            shift
            ;;
        --auto)
            AUTO_MODE=true
            shift
            ;;
        -h|--help)
            echo "Usage: $0 [OPTIONS]"
            echo "Set temporary permissions for CodeCanyon frontend installation"
            echo ""
            echo "Options:"
            echo "  --interactive    Prompt for confirmation before each action"
            echo "  --auto          Run automatically without prompts (non-interactive)"
            echo "  -h, --help      Show this help message"
            exit 0
            ;;
        *)
            error_exit "Unknown option: $1. Use --help for usage information."
            ;;
    esac
done

# Interactive confirmation
confirm_action() {
    if [[ "$INTERACTIVE" == true ]]; then
        read -p "$(echo -e "${YELLOW}❓ $1 (y/N): ${NC}")" -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            info_message "Action skipped by user"
            return 1
        fi
    elif [[ "$AUTO_MODE" == false ]]; then
        # Default behavior - prompt for critical actions
        if [[ "$1" == *"DANGEROUS"* ]] || [[ "$1" == *"777"* ]]; then
            read -p "$(echo -e "${YELLOW}❓ $1 (y/N): ${NC}")" -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                info_message "Action skipped by user"
                return 1
            fi
        fi
    fi
    return 0
}

# Set temporary permissions
set_temp_permissions() {
    info_message "Setting temporary 777 permissions for installation..."
    
    # Core writable directories - TEMPORARY 777
    if confirm_action "Set 777 permissions on storage/ (TEMPORARY - will be reverted)"; then
        if [[ -d "storage" ]]; then
            chmod -R 777 storage/
            success_message "Set 777 permissions on storage/"
        else
            warning_message "storage/ directory not found"
        fi
    fi
    
    if confirm_action "Set 777 permissions on bootstrap/cache/ (TEMPORARY - will be reverted)"; then
        if [[ -d "bootstrap/cache" ]]; then
            chmod -R 777 bootstrap/cache/
            success_message "Set 777 permissions on bootstrap/cache/"
        else
            warning_message "bootstrap/cache/ directory not found"
        fi
    fi
    
    if confirm_action "Set 777 permissions on upload directories (TEMPORARY - will be reverted)"; then
        if [[ -d "public/user-uploads" ]]; then
            chmod -R 777 public/user-uploads/
            success_message "Set 777 permissions on public/user-uploads/"
        else
            info_message "public/user-uploads/ not found (normal for fresh installations)"
        fi
        
        if [[ -d "public/uploads" ]]; then
            chmod -R 777 public/uploads/
            success_message "Set 777 permissions on public/uploads/"
        else
            info_message "public/uploads/ not found (normal for fresh installations)"
        fi
    fi
    
    # Configuration directories (some installers modify these)
    if confirm_action "Set 777 permissions on config/ (TEMPORARY - will be reverted)"; then
        if [[ -d "config" ]]; then
            chmod -R 777 config/
            success_message "Set 777 permissions on config/"
        else
            warning_message "config/ directory not found"
        fi
    fi
    
    # Public directory (for asset generation)
    if confirm_action "Set 777 permissions on public/ (TEMPORARY - will be reverted)"; then
        if [[ -d "public" ]]; then
            chmod -R 777 public/
            success_message "Set 777 permissions on public/"
        else
            warning_message "public/ directory not found"
        fi
    fi
}

# Keep sensitive files secure
secure_sensitive_files() {
    info_message "Securing sensitive files..."
    
    # Secure .env files
    for env_file in .env .env.local .env.staging .env.production; do
        if [[ -f "$env_file" ]]; then
            chmod 600 "$env_file"
            success_message "Secured $env_file (600 permissions)"
        fi
    done
    
    # Secure database config if it exists
    if [[ -f "config/database.php" ]]; then
        chmod 600 config/database.php
        success_message "Secured config/database.php (600 permissions)"
    fi
}

# Verify permissions
verify_permissions() {
    info_message "Verifying applied permissions..."
    
    echo ""
    echo "📋 Current Permissions Status:"
    echo "=============================="
    
    [[ -d "storage" ]] && echo "Storage: $(ls -ld storage/ | awk '{print $1}')"
    [[ -d "bootstrap/cache" ]] && echo "Cache: $(ls -ld bootstrap/cache/ | awk '{print $1}')"
    [[ -d "config" ]] && echo "Config: $(ls -ld config/ | awk '{print $1}')"
    [[ -d "public" ]] && echo "Public: $(ls -ld public/ | awk '{print $1}')"
    [[ -f ".env" ]] && echo "Env: $(ls -l .env | awk '{print $1}')"
    
    echo ""
}

# Main execution
main() {
    header_message
    
    # Check if we're in a Laravel project
    check_laravel_project
    
    warning_message "⚠️ IMPORTANT: These permissions are TEMPORARY and will be REVERTED after installation"
    warning_message "⚠️ Run ./2-permissions-post-install.sh IMMEDIATELY after installation completes"
    
    if [[ "$AUTO_MODE" == false ]] && [[ "$INTERACTIVE" == false ]]; then
        echo ""
        read -p "$(echo -e "${YELLOW}Continue with pre-installation permissions setup? (y/N): ${NC}")" -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            info_message "Operation cancelled by user"
            exit 0
        fi
    fi
    
    echo ""
    
    # Set temporary permissions
    set_temp_permissions
    
    echo ""
    
    # Keep sensitive files secure
    secure_sensitive_files
    
    echo ""
    
    # Verify permissions
    verify_permissions
    
    echo ""
    success_message "🎯 Ready for frontend installation/update"
    warning_message "⚠️ CRITICAL: Run ./2-permissions-post-install.sh IMMEDIATELY after installation completes"
    
    # Create reminder file
    echo "TEMP_PERMISSIONS_SET=$(date)" > .permissions-temp-status
    echo "SCRIPT_VERSION=1.0" >> .permissions-temp-status
    
    info_message "📁 Created .permissions-temp-status file as reminder"
    info_message "📋 Check install-permissions.log for detailed logs"
}

# Run main function
main "$@"
