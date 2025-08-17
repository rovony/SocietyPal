#!/bin/bash
# ===============================================
# POST-INSTALLATION SECURITY LOCKDOWN
# Script: 2-permissions-post-install.sh
# Purpose: Restore secure permissions after CodeCanyon frontend installation/update
# Usage: ./2-permissions-post-install.sh [--interactive] [--auto] [--env=local|staging|production]
# ===============================================

set -e  # Exit on any error

# Configuration
SCRIPT_NAME="Post-Installation Security Lockdown"
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
    log_message "${PURPLE}🔒 $SCRIPT_NAME${NC}"
    log_message "${BLUE}📅 $(date)${NC}"
}

# Check if running from Laravel project root
check_laravel_project() {
    if [[ ! -f "artisan" ]] || [[ ! -f "composer.json" ]]; then
        error_exit "This script must be run from Laravel project root directory (where artisan file exists)"
    fi
}

# Parse command line arguments
INTERACTIVE=false
AUTO_MODE=false
FORCE_ENV=""

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
        --env=*)
            FORCE_ENV="${1#*=}"
            shift
            ;;
        -h|--help)
            echo "Usage: $0 [OPTIONS]"
            echo "Restore secure permissions after CodeCanyon frontend installation"
            echo ""
            echo "Options:"
            echo "  --interactive         Prompt for confirmation before each action"
            echo "  --auto               Run automatically without prompts (non-interactive)"
            echo "  --env=ENV            Force specific environment (local|staging|production)"
            echo "  -h, --help           Show this help message"
            echo ""
            echo "Examples:"
            echo "  $0                   # Interactive mode with environment auto-detection"
            echo "  $0 --auto           # Non-interactive with auto-detection"
            echo "  $0 --env=production  # Force production security settings"
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
    fi
    return 0
}

# Detect environment
detect_environment() {
    if [[ -n "$FORCE_ENV" ]]; then
        ENV_TYPE="$FORCE_ENV"
        info_message "🔧 Environment forced to: $ENV_TYPE"
        return
    fi
    
    if [[ -f ".env" ]]; then
        if grep -q "APP_ENV=local" .env; then
            ENV_TYPE="local"
            info_message "🏠 Detected: LOCAL environment"
        elif grep -q "APP_ENV=staging" .env; then
            ENV_TYPE="staging"
            info_message "🌐 Detected: STAGING environment"
        elif grep -q "APP_ENV=production" .env; then
            ENV_TYPE="production"
            info_message "🚀 Detected: PRODUCTION environment"
        else
            ENV_TYPE="production"
            warning_message "⚠️ Unknown APP_ENV in .env - defaulting to PRODUCTION security"
        fi
    elif [[ -f ".env.local" ]]; then
        ENV_TYPE="local"
        info_message "🏠 Detected: LOCAL environment (.env.local found)"
    else
        ENV_TYPE="production"
        warning_message "⚠️ No .env file found - defaulting to PRODUCTION security"
    fi
    
    # Set permissions based on environment
    case $ENV_TYPE in
        "local")
            PERM_STORAGE="775"
            PERM_CACHE="775"
            PERM_UPLOADS="775"
            ;;
        "staging"|"production")
            PERM_STORAGE="755"
            PERM_CACHE="755"
            PERM_UPLOADS="755"
            ;;
        *)
            PERM_STORAGE="755"
            PERM_CACHE="755"
            PERM_UPLOADS="755"
            warning_message "Unknown environment, using production permissions"
            ;;
    esac
    
    info_message "📋 Using permissions: Storage=$PERM_STORAGE, Cache=$PERM_CACHE, Uploads=$PERM_UPLOADS"
}

# Revert core directories to appropriate permissions
revert_core_permissions() {
    info_message "🔧 Setting $ENV_TYPE environment permissions..."
    
    if confirm_action "Revert storage/ permissions to $PERM_STORAGE"; then
        if [[ -d "storage" ]]; then
            chmod -R "$PERM_STORAGE" storage/
            success_message "Reverted storage/ to $PERM_STORAGE permissions"
        else
            warning_message "storage/ directory not found"
        fi
    fi
    
    if confirm_action "Revert bootstrap/cache/ permissions to $PERM_CACHE"; then
        if [[ -d "bootstrap/cache" ]]; then
            chmod -R "$PERM_CACHE" bootstrap/cache/
            success_message "Reverted bootstrap/cache/ to $PERM_CACHE permissions"
        else
            warning_message "bootstrap/cache/ directory not found"
        fi
    fi
    
    if confirm_action "Revert upload directory permissions to $PERM_UPLOADS"; then
        if [[ -d "public/user-uploads" ]]; then
            chmod -R "$PERM_UPLOADS" public/user-uploads/
            success_message "Reverted public/user-uploads/ to $PERM_UPLOADS permissions"
        fi
        
        if [[ -d "public/uploads" ]]; then
            chmod -R "$PERM_UPLOADS" public/uploads/
            success_message "Reverted public/uploads/ to $PERM_UPLOADS permissions"
        fi
    fi
}

# Secure configuration and public directories
secure_directories() {
    info_message "🔒 Securing configuration and public directories..."
    
    if confirm_action "Secure config/ directory (755 for dirs, 644 for files)"; then
        if [[ -d "config" ]]; then
            chmod -R 755 config/
            find config/ -type f -exec chmod 644 {} \;
            success_message "Secured config/ directory"
        else
            warning_message "config/ directory not found"
        fi
    fi
    
    if confirm_action "Secure public/ directory (755 for dirs, 644 for files)"; then
        if [[ -d "public" ]]; then
            chmod -R 755 public/
            find public/ -type f -name "*.php" -exec chmod 644 {} \;
            find public/ -type f -name "*.js" -exec chmod 644 {} \;
            find public/ -type f -name "*.css" -exec chmod 644 {} \;
            success_message "Secured public/ directory"
        else
            warning_message "public/ directory not found"
        fi
    fi
}

# Ensure sensitive files remain secure
secure_sensitive_files() {
    info_message "🔐 Securing sensitive files..."
    
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

# Set proper ownership
set_ownership() {
    info_message "👤 Setting proper file ownership..."
    
    if [[ "$ENV_TYPE" == "local" ]]; then
        # Local development (Herd/macOS)
        if confirm_action "Set local development ownership ($(whoami):staff)"; then
            chown -R "$(whoami):staff" storage/ bootstrap/cache/ 2>/dev/null || true
            [[ -d "public/user-uploads" ]] && chown -R "$(whoami):staff" public/user-uploads/ 2>/dev/null || true
            [[ -d "public/uploads" ]] && chown -R "$(whoami):staff" public/uploads/ 2>/dev/null || true
            success_message "Set local development ownership"
        fi
    else
        # Production/Staging
        warning_message "⚠️ MANUAL ACTION REQUIRED: Set proper ownership for $ENV_TYPE environment"
        warning_message "Example: chown -R www-data:www-data storage/ bootstrap/cache/ public/user-uploads/ public/uploads/"
        info_message "💡 This must be done by a user with appropriate sudo privileges"
    fi
}

# Security audit
perform_security_audit() {
    info_message "🔍 Performing security audit..."
    
    # Check for dangerous 777 permissions
    DANGEROUS_FILES=$(find . -type f -perm 777 2>/dev/null | wc -l | tr -d ' ')
    DANGEROUS_DIRS=$(find . -type d -perm 777 2>/dev/null | grep -v "^\\.$" | wc -l | tr -d ' ')
    
    if [[ $DANGEROUS_FILES -gt 0 ]] || [[ $DANGEROUS_DIRS -gt 0 ]]; then
        warning_message "❌ WARNING: Found files/directories with dangerous 777 permissions"
        if [[ $DANGEROUS_FILES -gt 0 ]]; then
            warning_message "Files with 777 permissions: $DANGEROUS_FILES"
            find . -type f -perm 777 2>/dev/null | head -10
        fi
        if [[ $DANGEROUS_DIRS -gt 0 ]]; then
            warning_message "Directories with 777 permissions: $DANGEROUS_DIRS"
            find . -type d -perm 777 2>/dev/null | grep -v "^\\.$" | head -10
        fi
        
        if confirm_action "Fix dangerous 777 permissions automatically"; then
            find . -type f -perm 777 -exec chmod 644 {} \; 2>/dev/null || true
            find . -type d -perm 777 -exec chmod 755 {} \; 2>/dev/null || true
            success_message "Fixed dangerous 777 permissions"
        fi
    else
        success_message "✅ No files with dangerous 777 permissions found"
    fi
}

# Verification summary
verification_summary() {
    info_message "📋 Final permissions verification..."
    
    echo ""
    echo "🔒 POST-INSTALLATION SECURITY VERIFICATION:"
    echo "=========================================="
    echo "Environment: $ENV_TYPE"
    
    [[ -d "storage" ]] && echo "Storage permissions: $(ls -ld storage/ | awk '{print $1}')"
    [[ -d "bootstrap/cache" ]] && echo "Cache permissions: $(ls -ld bootstrap/cache/ | awk '{print $1}')"
    [[ -d "config" ]] && echo "Config permissions: $(ls -ld config/ | awk '{print $1}')"
    [[ -d "public" ]] && echo "Public permissions: $(ls -ld public/ | awk '{print $1}')"
    [[ -f ".env" ]] && echo "Env file permissions: $(ls -l .env | awk '{print $1}')"
    
    echo ""
}

# Cleanup temporary files
cleanup() {
    if [[ -f ".permissions-temp-status" ]]; then
        rm -f .permissions-temp-status
        info_message "🧹 Removed temporary permissions status file"
    fi
}

# Main execution
main() {
    header_message
    
    # Check if we're in a Laravel project
    check_laravel_project
    
    # Check if pre-install was run
    if [[ -f ".permissions-temp-status" ]]; then
        info_message "📋 Found temporary permissions status file - proceeding with security lockdown"
    else
        warning_message "⚠️ No temporary permissions status file found"
        warning_message "⚠️ This suggests pre-install script may not have been run"
        if [[ "$AUTO_MODE" == false ]]; then
            read -p "$(echo -e "${YELLOW}Continue anyway? (y/N): ${NC}")" -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                info_message "Operation cancelled by user"
                exit 0
            fi
        fi
    fi
    
    echo ""
    
    # Detect environment
    detect_environment
    
    echo ""
    
    # Revert core directories to appropriate permissions
    revert_core_permissions
    
    echo ""
    
    # Secure configuration and public directories
    secure_directories
    
    echo ""
    
    # Ensure sensitive files remain secure
    secure_sensitive_files
    
    echo ""
    
    # Set proper ownership
    set_ownership
    
    echo ""
    
    # Perform security audit
    perform_security_audit
    
    echo ""
    
    # Verification summary
    verification_summary
    
    # Cleanup
    cleanup
    
    echo ""
    success_message "🎯 INSTALLATION SECURITY LOCKDOWN COMPLETE"
    success_message "🔒 Your application is now secured with $ENV_TYPE-appropriate permissions"
    
    info_message "📋 Check install-permissions.log for detailed logs"
}

# Run main function
main "$@"
