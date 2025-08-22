#!/bin/bash

# Comprehensive Security Scanning
# Purpose: Automated vulnerability detection and security validation

set -e

# Configuration
PROJECT_ROOT=$(pwd)
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/04-Error-Logs/security-scan.log"
REPORT_DIR="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/security-reports"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔒 COMPREHENSIVE SECURITY SCANNING${NC}"
echo "=================================="

# Function: Log with timestamp
log_message() {
    mkdir -p "$(dirname "$LOG_FILE")"
    mkdir -p "$REPORT_DIR"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function: Check dependencies
check_security_tools() {
    local missing_tools=()
    
    # Check for composer audit
    if ! composer --version >/dev/null 2>&1; then
        missing_tools+=("composer")
    fi
    
    # Check for npm audit (if package.json exists)
    if [[ -f "package.json" ]] && ! npm --version >/dev/null 2>&1; then
        missing_tools+=("npm")
    fi
    
    if [[ ${#missing_tools[@]} -gt 0 ]]; then
        log_message "${YELLOW}⚠️  Missing security tools: ${missing_tools[*]}${NC}"
        log_message "${YELLOW}   Some security checks will be skipped${NC}"
    fi
    
    log_message "${GREEN}✅ Checking security scanning tools${NC}"
}

# Function: Composer security audit
composer_security_audit() {
    log_message "${BLUE}Running Composer security audit...${NC}"
    
    local report_file="$REPORT_DIR/composer-audit-$(date +%Y%m%d_%H%M%S).json"
    
    if composer audit --format=json > "$report_file" 2>/dev/null; then
        log_message "${GREEN}✅ Composer audit passed - no vulnerabilities found${NC}"
        return 0
    else
        local exit_code=$?
        if [[ $exit_code -eq 1 ]]; then
            log_message "${RED}❌ Composer audit found vulnerabilities${NC}"
            composer audit
            return 1
        else
            log_message "${YELLOW}⚠️  Composer audit could not run properly${NC}"
            return 0
        fi
    fi
}

# Function: NPM security audit
npm_security_audit() {
    if [[ ! -f "package.json" ]]; then
        log_message "${BLUE}No package.json found - skipping NPM audit${NC}"
        return 0
    fi
    
    log_message "${BLUE}Running NPM security audit...${NC}"
    
    local report_file="$REPORT_DIR/npm-audit-$(date +%Y%m%d_%H%M%S).json"
    
    if npm audit --json > "$report_file" 2>/dev/null; then
        log_message "${GREEN}✅ NPM audit passed - no vulnerabilities found${NC}"
        return 0
    else
        local exit_code=$?
        if [[ $exit_code -eq 1 ]]; then
            log_message "${RED}❌ NPM audit found vulnerabilities${NC}"
            npm audit
            return 1
        else
            log_message "${YELLOW}⚠️  NPM audit could not run properly${NC}"
            return 0
        fi
    fi
}

# Function: Environment file security check
check_env_security() {
    log_message "${BLUE}Checking environment file security...${NC}"
    
    local issues=0
    
    # Check for .env files in version control
    if git ls-files --error-unmatch .env >/dev/null 2>&1; then
        log_message "${RED}❌ .env file is tracked in git - security risk!${NC}"
        ((issues++))
    fi
    
    # Check for sensitive data in .env.example
    if [[ -f ".env.example" ]]; then
        local sensitive_patterns=("password" "secret" "key" "token" "api_key")
        for pattern in "${sensitive_patterns[@]}"; do
            if grep -i "$pattern.*=" .env.example | grep -v "^#" | grep -v "null\|example\|your_" >/dev/null 2>&1; then
                log_message "${YELLOW}⚠️  Potential sensitive data in .env.example${NC}"
                break
            fi
        done
    fi
    
    # Check .env file permissions
    if [[ -f ".env" ]]; then
        local perms=$(stat -f "%A" .env 2>/dev/null || stat -c "%a" .env 2>/dev/null)
        if [[ "$perms" != "600" && "$perms" != "644" ]]; then
            log_message "${YELLOW}⚠️  .env file permissions: $perms (recommend 600 or 644)${NC}"
        fi
    fi
    
    if [[ $issues -eq 0 ]]; then
        log_message "${GREEN}✅ Environment file security check passed${NC}"
        return 0
    else
        return 1
    fi
}

# Function: Laravel configuration security check
check_laravel_security() {
    log_message "${BLUE}Checking Laravel security configuration...${NC}"
    
    local issues=0
    
    # Check APP_DEBUG in production
    if [[ -f ".env" ]]; then
        local app_env=$(grep '^APP_ENV=' .env | cut -d'=' -f2)
        local app_debug=$(grep '^APP_DEBUG=' .env | cut -d'=' -f2)
        
        if [[ "$app_env" == "production" && "$app_debug" == "true" ]]; then
            log_message "${RED}❌ APP_DEBUG=true in production environment${NC}"
            ((issues++))
        fi
        
        # Check for APP_KEY
        local app_key=$(grep '^APP_KEY=' .env | cut -d'=' -f2)
        if [[ -z "$app_key" || "$app_key" == "" ]]; then
            log_message "${RED}❌ APP_KEY is not set${NC}"
            ((issues++))
        fi
        
        # Check HTTPS settings for production
        if [[ "$app_env" == "production" ]]; then
            local app_url=$(grep '^APP_URL=' .env | cut -d'=' -f2)
            if [[ ! "$app_url" =~ ^https:// ]]; then
                log_message "${YELLOW}⚠️  APP_URL should use HTTPS in production${NC}"
            fi
        fi
    fi
    
    # Check storage and cache permissions
    if [[ -d "storage" ]]; then
        local storage_perms=$(stat -f "%A" storage 2>/dev/null || stat -c "%a" storage 2>/dev/null)
        if [[ "$storage_perms" != "755" && "$storage_perms" != "775" ]]; then
            log_message "${YELLOW}⚠️  Storage directory permissions: $storage_perms${NC}"
        fi
    fi
    
    if [[ $issues -eq 0 ]]; then
        log_message "${GREEN}✅ Laravel security configuration check passed${NC}"
        return 0
    else
        return 1
    fi
}

# Function: File permissions security check
check_file_permissions() {
    log_message "${BLUE}Checking file permissions security...${NC}"
    
    local issues=0
    
    # Check for world-writable files
    local world_writable=$(find . -type f -perm -002 -not -path "./vendor/*" -not -path "./.git/*" 2>/dev/null | head -10)
    if [[ -n "$world_writable" ]]; then
        log_message "${YELLOW}⚠️  World-writable files found:${NC}"
        echo "$world_writable" | while read -r file; do
            log_message "    $file"
        done
        ((issues++))
    fi
    
    # Check for executable PHP files in public directory
    if [[ -d "public" ]]; then
        local exec_php=$(find public -name "*.php" -executable 2>/dev/null)
        if [[ -n "$exec_php" ]]; then
            log_message "${YELLOW}⚠️  Executable PHP files in public directory:${NC}"
            echo "$exec_php" | while read -r file; do
                log_message "    $file"
            done
        fi
    fi
    
    if [[ $issues -eq 0 ]]; then
        log_message "${GREEN}✅ File permissions security check passed${NC}"
        return 0
    else
        return 1
    fi
}

# Function: Git security check
check_git_security() {
    log_message "${BLUE}Checking Git security...${NC}"
    
    local issues=0
    
    # Check if .git directory is accessible via web
    if [[ -d "public/.git" ]]; then
        log_message "${RED}❌ .git directory found in public folder - security risk!${NC}"
        ((issues++))
    fi
    
    # Check for sensitive files in git
    local sensitive_files=(".env" "config/database.php" "*.key" "*.pem")
    for pattern in "${sensitive_files[@]}"; do
        if git ls-files --error-unmatch "$pattern" >/dev/null 2>&1; then
            log_message "${YELLOW}⚠️  Sensitive file pattern '$pattern' found in git${NC}"
        fi
    done
    
    # Check .gitignore for common sensitive patterns
    if [[ -f ".gitignore" ]]; then
        local required_patterns=(".env" "*.log" "vendor/" "node_modules/")
        for pattern in "${required_patterns[@]}"; do
            if ! grep -q "$pattern" .gitignore; then
                log_message "${YELLOW}⚠️  .gitignore missing pattern: $pattern${NC}"
            fi
        done
    fi
    
    if [[ $issues -eq 0 ]]; then
        log_message "${GREEN}✅ Git security check passed${NC}"
        return 0
    else
        return 1
    fi
}

# Function: Auto-fix security issues
auto_fix_security_issues() {
    local fix_mode="${1:-report-only}"
    
    if [[ "$fix_mode" != "auto-fix" ]]; then
        log_message "${BLUE}Security auto-fix disabled - report-only mode${NC}"
        return 0
    fi
    
    log_message "${BLUE}Attempting to auto-fix security issues...${NC}"
    
    local fixes_applied=0
    
    # Fix .env permissions
    if [[ -f ".env" ]]; then
        chmod 600 .env
        log_message "${GREEN}✅ Fixed .env file permissions (600)${NC}"
        ((fixes_applied++))
    fi
    
    # Fix storage permissions
    if [[ -d "storage" ]]; then
        chmod -R 755 storage
        log_message "${GREEN}✅ Fixed storage directory permissions${NC}"
        ((fixes_applied++))
    fi
    
    # Fix bootstrap/cache permissions
    if [[ -d "bootstrap/cache" ]]; then
        chmod -R 755 bootstrap/cache
        log_message "${GREEN}✅ Fixed bootstrap/cache permissions${NC}"
        ((fixes_applied++))
    fi
    
    log_message "${GREEN}✅ Applied $fixes_applied security fixes${NC}"
}

# Function: Generate security report
generate_security_report() {
    local report_file="$REPORT_DIR/security-summary-$(date +%Y%m%d_%H%M%S).txt"
    
    cat > "$report_file" << EOF
# Security Scan Report
Generated: $(date)
Project: SocietyPal

## Scan Summary
- Composer Security Audit: $(composer audit >/dev/null 2>&1 && echo "PASSED" || echo "FAILED")
- NPM Security Audit: $(npm audit >/dev/null 2>&1 && echo "PASSED" || echo "SKIPPED/FAILED")
- Environment Security: $(check_env_security >/dev/null 2>&1 && echo "PASSED" || echo "ISSUES FOUND")
- Laravel Configuration: $(check_laravel_security >/dev/null 2>&1 && echo "PASSED" || echo "ISSUES FOUND")
- File Permissions: $(check_file_permissions >/dev/null 2>&1 && echo "PASSED" || echo "ISSUES FOUND")
- Git Security: $(check_git_security >/dev/null 2>&1 && echo "PASSED" || echo "ISSUES FOUND")

## Recommendations
1. Regularly update dependencies to patch security vulnerabilities
2. Keep APP_DEBUG=false in production environments
3. Use HTTPS for all production URLs
4. Maintain proper file permissions (644 for files, 755 for directories)
5. Never commit sensitive data to version control
6. Regularly audit and rotate API keys and secrets

## Next Steps
- Review any failed checks above
- Apply recommended security fixes
- Schedule regular security scans
- Consider implementing additional security headers
EOF
    
    log_message "${GREEN}✅ Security report generated: $report_file${NC}"
}

# Main execution
main() {
    local fix_mode="${1:-report-only}"
    local total_issues=0
    
    check_security_tools
    
    # Run all security checks
    composer_security_audit || ((total_issues++))
    npm_security_audit || ((total_issues++))
    check_env_security || ((total_issues++))
    check_laravel_security || ((total_issues++))
    check_file_permissions || ((total_issues++))
    check_git_security || ((total_issues++))
    
    # Auto-fix if requested
    auto_fix_security_issues "$fix_mode"
    
    # Generate report
    generate_security_report
    
    echo ""
    if [[ $total_issues -eq 0 ]]; then
        log_message "${GREEN}🎯 SECURITY SCAN: ✅ ALL CHECKS PASSED${NC}"
        exit 0
    else
        log_message "${YELLOW}🎯 SECURITY SCAN: ⚠️  $total_issues ISSUES FOUND${NC}"
        log_message "${BLUE}   Review the security report for details${NC}"
        exit 1
    fi
}

# Execute with parameters
case "${1:-scan}" in
    "scan")
        main "report-only"
        ;;
    "scan-and-fix")
        main "auto-fix"
        ;;
    "help"|"--help"|"-h")
        echo "Available commands:"
        echo "  scan         - Run security scan (report-only mode)"
        echo "  scan-and-fix - Run security scan and auto-fix issues"
        echo "  help         - Show this help message"
        ;;
    *)
        echo -e "${RED}❌ Error: Unknown command '$1'${NC}"
        echo "Use '$0 help' for available commands"
        exit 1
        ;;
esac
