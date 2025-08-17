#!/bin/bash
# ===============================================
# EMERGENCY SECURITY RECOVERY
# Script: permissions-emergency-security.sh
# Purpose: Immediate security recovery if permissions were left insecure
# Usage: ./permissions-emergency-security.sh [--interactive] [--auto] [--env=local|staging|production]
# ===============================================

set -e  # Exit on any error

# Configuration
SCRIPT_NAME="Emergency Security Recovery"
LOG_FILE="emergency-security.log"

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
    log_message "${RED}🚨 $SCRIPT_NAME${NC}"
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
            echo "Emergency security recovery for dangerous file permissions"
            echo ""
            echo "Options:"
            echo "  --interactive         Prompt for confirmation before each action"
            echo "  --auto               Run automatically without prompts (emergency mode)"
            echo "  --env=ENV            Force specific environment (local|staging|production)"
            echo "  -h, --help           Show this help message"
            echo ""
            echo "Examples:"
            echo "  $0                   # Interactive emergency recovery"
            echo "  $0 --auto           # Immediate emergency lockdown (no prompts)"
            echo "  $0 --env=production  # Force production-level security"
            echo ""
            echo "This script performs immediate security lockdown and should be used when:"
            echo "  - You forgot to run post-installation security script"
            echo "  - Dangerous 777 permissions were left on the system"
            echo "  - You need immediate security recovery"
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

# Detect environment (with production default for security)
detect_environment() {
    if [[ -n "$FORCE_ENV" ]]; then
        ENV_TYPE="$FORCE_ENV"
        warning_message "🔧 Environment forced to: $ENV_TYPE"
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
            warning_message "⚠️ Unknown APP_ENV - defaulting to PRODUCTION security for safety"
        fi
    else
        ENV_TYPE="production"
        warning_message "⚠️ No .env file found - defaulting to PRODUCTION security for safety"
    fi
    
    # Set permissions based on environment (defaulting to production for security)
    case $ENV_TYPE in
        "local")
            PERM_STORAGE="775"
            PERM_CACHE="775"
            PERM_UPLOADS="775"
            ;;
        *)
            PERM_STORAGE="755"
            PERM_CACHE="755"
            PERM_UPLOADS="755"
            ;;
    esac
    
    info_message "📋 Emergency permissions: Storage=$PERM_STORAGE, Cache=$PERM_CACHE, Uploads=$PERM_UPLOADS"
}

# Immediate security audit
immediate_security_audit() {
    warning_message "🔍 IMMEDIATE SECURITY AUDIT - Checking for dangerous permissions..."
    
    # Count dangerous files and directories
    DANGEROUS_FILES=$(find . -type f -perm 777 2>/dev/null | wc -l | tr -d ' ')
    DANGEROUS_DIRS=$(find . -type d -perm 777 2>/dev/null | grep -v "^\\.$" | wc -l | tr -d ' ')
    
    info_message "Found $DANGEROUS_FILES files with 777 permissions"
    info_message "Found $DANGEROUS_DIRS directories with 777 permissions"
    
    if [[ $DANGEROUS_FILES -gt 0 ]] || [[ $DANGEROUS_DIRS -gt 0 ]]; then
        warning_message "❌ CRITICAL: Dangerous 777 permissions detected!"
        
        if [[ $DANGEROUS_FILES -gt 0 ]]; then
            warning_message "Files with 777 permissions (showing first 10):"
            find . -type f -perm 777 2>/dev/null | head -10
        fi
        
        if [[ $DANGEROUS_DIRS -gt 0 ]]; then
            warning_message "Directories with 777 permissions (showing first 10):"
            find . -type d -perm 777 2>/dev/null | grep -v "^\\.$" | head -10
        fi
        
        return 1  # Indicates dangerous permissions found
    else
        success_message "✅ No dangerous 777 permissions found"
        return 0  # Safe
    fi
}

# Emergency lockdown of all dangerous permissions
emergency_lockdown() {
    warning_message "🚨 PERFORMING EMERGENCY SECURITY LOCKDOWN"
    
    if confirm_action "Fix ALL dangerous 777 file permissions to 644"; then
        FIXED_FILES=$(find . -type f -perm 777 -exec chmod 644 {} \; -print 2>/dev/null | wc -l | tr -d ' ')
        success_message "Fixed $FIXED_FILES files with dangerous 777 permissions"
    fi
    
    if confirm_action "Fix ALL dangerous 777 directory permissions to 755"; then
        FIXED_DIRS=$(find . -type d -perm 777 -exec chmod 755 {} \; -print 2>/dev/null | grep -v "^\\.$" | wc -l | tr -d ' ')
        success_message "Fixed $FIXED_DIRS directories with dangerous 777 permissions"
    fi
}

# Apply environment-appropriate permissions
apply_environment_permissions() {
    warning_message "🔧 Applying $ENV_TYPE environment permissions..."
    
    # Core Laravel directories
    if confirm_action "Set storage/ permissions to $PERM_STORAGE"; then
        if [[ -d "storage" ]]; then
            chmod -R "$PERM_STORAGE" storage/
            success_message "Set storage/ to $PERM_STORAGE permissions"
        fi
    fi
    
    if confirm_action "Set bootstrap/cache/ permissions to $PERM_CACHE"; then
        if [[ -d "bootstrap/cache" ]]; then
            chmod -R "$PERM_CACHE" bootstrap/cache/
            success_message "Set bootstrap/cache/ to $PERM_CACHE permissions"
        fi
    fi
    
    if confirm_action "Set upload directories permissions to $PERM_UPLOADS"; then
        [[ -d "public/user-uploads" ]] && chmod -R "$PERM_UPLOADS" public/user-uploads/
        [[ -d "public/uploads" ]] && chmod -R "$PERM_UPLOADS" public/uploads/
        success_message "Set upload directories to $PERM_UPLOADS permissions"
    fi
    
    # Secure configuration and public directories
    if confirm_action "Secure config/ and public/ directories"; then
        if [[ -d "config" ]]; then
            chmod -R 755 config/
            find config/ -type f -exec chmod 644 {} \; 2>/dev/null || true
            success_message "Secured config/ directory"
        fi
        
        if [[ -d "public" ]]; then
            chmod -R 755 public/
            find public/ -type f -exec chmod 644 {} \; 2>/dev/null || true
            success_message "Secured public/ directory"
        fi
    fi
}

# Secure critical files
secure_critical_files() {
    warning_message "🔐 Securing critical files..."
    
    # Secure all .env files
    for env_file in .env .env.local .env.staging .env.production; do
        if [[ -f "$env_file" ]]; then
            chmod 600 "$env_file"
            success_message "Secured $env_file (600 permissions)"
        fi
    done
    
    # Secure database config
    if [[ -f "config/database.php" ]]; then
        chmod 600 config/database.php
        success_message "Secured config/database.php (600 permissions)"
    fi
    
    # Secure other sensitive config files
    for config_file in config/mail.php config/services.php config/broadcasting.php; do
        if [[ -f "$config_file" ]]; then
            chmod 644 "$config_file"
            success_message "Secured $config_file (644 permissions)"
        fi
    done
}

# Final verification
final_verification() {
    info_message "🔍 FINAL SECURITY VERIFICATION"
    
    # Re-check for dangerous permissions
    REMAINING_DANGEROUS_FILES=$(find . -type f -perm 777 2>/dev/null | wc -l | tr -d ' ')
    REMAINING_DANGEROUS_DIRS=$(find . -type d -perm 777 2>/dev/null | grep -v "^\\.$" | wc -l | tr -d ' ')
    
    if [[ $REMAINING_DANGEROUS_FILES -eq 0 ]] && [[ $REMAINING_DANGEROUS_DIRS -eq 0 ]]; then
        success_message "✅ No dangerous 777 permissions remaining"
    else
        warning_message "❌ WARNING: Still found dangerous permissions after emergency lockdown"
        [[ $REMAINING_DANGEROUS_FILES -gt 0 ]] && warning_message "Files with 777: $REMAINING_DANGEROUS_FILES"
        [[ $REMAINING_DANGEROUS_DIRS -gt 0 ]] && warning_message "Directories with 777: $REMAINING_DANGEROUS_DIRS"
    fi
    
    # Show final permissions summary
    echo ""
    echo "🔒 EMERGENCY SECURITY RECOVERY SUMMARY:"
    echo "======================================"
    echo "Environment: $ENV_TYPE"
    echo "Recovery Date: $(date)"
    
    [[ -d "storage" ]] && echo "Storage permissions: $(ls -ld storage/ | awk '{print $1}')"
    [[ -d "bootstrap/cache" ]] && echo "Cache permissions: $(ls -ld bootstrap/cache/ | awk '{print $1}')"
    [[ -d "config" ]] && echo "Config permissions: $(ls -ld config/ | awk '{print $1}')"
    [[ -d "public" ]] && echo "Public permissions: $(ls -ld public/ | awk '{print $1}')"
    [[ -f ".env" ]] && echo "Env file permissions: $(ls -l .env | awk '{print $1}')"
    
    echo ""
}

# Cleanup emergency status
cleanup() {
    # Remove temporary status files that might be left over
    [[ -f ".permissions-temp-status" ]] && rm -f .permissions-temp-status
    
    # Create emergency recovery record
    echo "EMERGENCY_RECOVERY_DATE=$(date)" > .emergency-recovery-log
    echo "RECOVERY_ENVIRONMENT=$ENV_TYPE" >> .emergency-recovery-log
    echo "SCRIPT_VERSION=1.0" >> .emergency-recovery-log
    
    info_message "📋 Created emergency recovery log: .emergency-recovery-log"
}

# Main execution
main() {
    header_message
    
    # Check if we're in a Laravel project
    check_laravel_project
    
    warning_message "🚨 EMERGENCY SECURITY RECOVERY MODE ACTIVATED"
    warning_message "This script will immediately secure dangerous file permissions"
    
    if [[ "$AUTO_MODE" == false ]]; then
        echo ""
        read -p "$(echo -e "${RED}⚠️ Continue with emergency security recovery? (y/N): ${NC}")" -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            info_message "Emergency recovery cancelled by user"
            exit 0
        fi
    fi
    
    echo ""
    
    # Detect environment
    detect_environment
    
    echo ""
    
    # Immediate security audit
    if immediate_security_audit; then
        info_message "✅ No immediate security threats detected, but proceeding with security hardening"
    else
        warning_message "🚨 CRITICAL SECURITY THREATS DETECTED - Proceeding with emergency lockdown"
    fi
    
    echo ""
    
    # Emergency lockdown of dangerous permissions
    emergency_lockdown
    
    echo ""
    
    # Apply environment-appropriate permissions
    apply_environment_permissions
    
    echo ""
    
    # Secure critical files
    secure_critical_files
    
    echo ""
    
    # Final verification
    final_verification
    
    # Cleanup
    cleanup
    
    echo ""
    success_message "🎯 EMERGENCY SECURITY RECOVERY COMPLETE"
    success_message "🔒 Your application has been secured with $ENV_TYPE-appropriate permissions"
    
    info_message "📋 Check emergency-security.log for detailed logs"
    warning_message "💡 Consider reviewing your installation/deployment procedures to prevent future security issues"
}

# Run main function
main "$@"
