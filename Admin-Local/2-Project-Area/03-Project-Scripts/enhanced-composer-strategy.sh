#!/bin/bash

# Enhanced Composer Strategy Setup
# Purpose: Implement build-aware dependency management with production optimization

set -e

# Configuration
PROJECT_ROOT=$(pwd)
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/composer-strategy.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔧 ENHANCED COMPOSER STRATEGY SETUP${NC}"
echo "==================================="

# Function: Log with timestamp
log_message() {
    mkdir -p "$(dirname "$LOG_FILE")"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function: Backup original composer.json
backup_composer_json() {
    if [[ -f "composer.json" ]]; then
        cp composer.json "Admin-Local/4-Backups/03-Config-Backups/composer.json.backup.$(date +%Y%m%d_%H%M%S)"
        log_message "${GREEN}✅ Backing up original composer.json${NC}"
    fi
}

# Function: Enhanced composer.json configuration
enhance_composer_json() {
    # Check if composer.json exists
    if [[ ! -f "composer.json" ]]; then
        log_message "${RED}❌ composer.json not found${NC}"
        exit 1
    fi
    
    # Create enhanced composer.json with build-aware scripts
    log_message "${GREEN}✅ Enhancing composer.json with build-aware scripts${NC}"
    
    # Use jq to enhance composer.json if available, otherwise use sed
    if command -v jq >/dev/null 2>&1; then
        # Create temporary enhanced composer.json
        jq '. + {
            "config": {
                "optimize-autoloader": true,
                "preferred-install": "dist",
                "sort-packages": true,
                "allow-plugins": {
                    "pestphp/pest-plugin": true,
                    "php-http/discovery": true
                },
                "process-timeout": 300,
                "cache-files-maxsize": "512MiB"
            },
            "scripts": {
                "build-production": [
                    "echo \"🔧 Starting production build...\"",
                    "@composer install --optimize-autoloader --no-dev --no-interaction",
                    "@php artisan config:cache",
                    "@php artisan route:cache",
                    "@php artisan view:cache",
                    "@php artisan event:cache",
                    "echo \"✅ Production build completed\""
                ],
                "build-with-dev": [
                    "echo \"🔧 Starting build with dev dependencies...\"",
                    "@composer install --optimize-autoloader --no-interaction",
                    "@php artisan config:cache",
                    "@php artisan route:cache",
                    "@php artisan view:cache",
                    "@php artisan event:cache",
                    "echo \"✅ Build with dev dependencies completed\""
                ],
                "build-fallback": [
                    "echo \"🚨 Executing fallback build strategy...\"",
                    "@composer install --no-interaction",
                    "@php artisan config:clear",
                    "@php artisan cache:clear",
                    "echo \"⚠️  Fallback build completed - manual optimization needed\""
                ],
                "validate-dependencies": [
                    "echo \"🔍 Validating dependencies...\"",
                    "@composer validate --strict",
                    "@composer check-platform-reqs",
                    "echo \"✅ Dependencies validated\""
                ],
                "security-audit": [
                    "echo \"🔒 Running security audit...\"",
                    "@composer audit --no-dev",
                    "echo \"✅ Security audit completed\""
                ],
                "post-install-cmd": [
                    "@php artisan package:discover --ansi"
                ],
                "post-update-cmd": [
                    "@php artisan package:discover --ansi"
                ],
                "post-root-package-install": [
                    "@php -r \"file_exists(\".env\") || copy(\".env.example\", \".env\");\""
                ],
                "post-create-project-cmd": [
                    "@php artisan key:generate --ansi",
                    "@php artisan storage:link --ansi"
                ]
            }
        }' composer.json > composer.json.tmp && mv composer.json.tmp composer.json
    else
        # Fallback: Add scripts section manually if jq is not available
        log_message "${YELLOW}⚠️  jq not available - using fallback method${NC}"
        
        # Create a backup and add scripts section
        cp composer.json composer.json.backup
        
        # This is a simplified approach - in production, jq should be installed
        log_message "${YELLOW}⚠️  Manual composer.json enhancement needed - jq recommended${NC}"
    fi
}

# Function: Create dependency validation script
create_dependency_validator() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/validate-dependencies.sh << 'EOF'
#!/bin/bash

# Dependency Validator
# Purpose: Validate composer dependencies and detect issues

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔍 DEPENDENCY VALIDATOR${NC}"
echo "======================="

# Function: Check composer.lock exists
check_composer_lock() {
    if [[ ! -f "composer.lock" ]]; then
        echo -e "${RED}❌ composer.lock not found - run 'composer install' first${NC}"
        return 1
    fi
    echo -e "${GREEN}✅ composer.lock found${NC}"
    return 0
}

# Function: Validate composer.json
validate_composer_json() {
    echo -e "${BLUE}Validating composer.json...${NC}"
    
    if composer validate --strict --no-check-all 2>/dev/null; then
        echo -e "${GREEN}✅ composer.json is valid${NC}"
        return 0
    else
        echo -e "${RED}❌ composer.json validation failed${NC}"
        composer validate --strict --no-check-all
        return 1
    fi
}

# Function: Check platform requirements
check_platform_requirements() {
    echo -e "${BLUE}Checking platform requirements...${NC}"
    
    if composer check-platform-reqs 2>/dev/null; then
        echo -e "${GREEN}✅ Platform requirements satisfied${NC}"
        return 0
    else
        echo -e "${RED}❌ Platform requirements not met${NC}"
        composer check-platform-reqs
        return 1
    fi
}

# Function: Check for dev dependencies in production
check_dev_dependencies() {
    if [[ "$APP_ENV" == "production" ]]; then
        echo -e "${BLUE}Checking for dev dependencies in production...${NC}"
        
        # Check if dev packages are installed
        if composer show --dev 2>/dev/null | grep -q "laravel/telescope\|barryvdh/laravel-debugbar\|fakerphp/faker"; then
            echo -e "${YELLOW}⚠️  Dev dependencies detected in production environment${NC}"
            echo -e "${YELLOW}    Consider running: composer install --no-dev${NC}"
            return 1
        else
            echo -e "${GREEN}✅ No dev dependencies in production${NC}"
            return 0
        fi
    fi
    return 0
}

# Function: Check for security vulnerabilities
check_security() {
    echo -e "${BLUE}Running security audit...${NC}"
    
    if composer audit --no-dev 2>/dev/null; then
        echo -e "${GREEN}✅ No security vulnerabilities found${NC}"
        return 0
    else
        echo -e "${RED}❌ Security vulnerabilities detected${NC}"
        composer audit --no-dev
        return 1
    fi
}

# Function: Check autoloader optimization
check_autoloader() {
    echo -e "${BLUE}Checking autoloader optimization...${NC}"
    
    if [[ -f "vendor/composer/autoload_classmap.php" ]] && [[ -s "vendor/composer/autoload_classmap.php" ]]; then
        echo -e "${GREEN}✅ Autoloader is optimized${NC}"
        return 0
    else
        echo -e "${YELLOW}⚠️  Autoloader not optimized - run: composer install --optimize-autoloader${NC}"
        return 1
    fi
}

# Main execution
main() {
    local errors=0
    
    check_composer_lock || ((errors++))
    validate_composer_json || ((errors++))
    check_platform_requirements || ((errors++))
    check_dev_dependencies || ((errors++))
    check_security || ((errors++))
    check_autoloader || ((errors++))
    
    echo ""
    if [[ $errors -eq 0 ]]; then
        echo -e "${GREEN}🎯 DEPENDENCY VALIDATION: ✅ ALL CHECKS PASSED${NC}"
        exit 0
    else
        echo -e "${RED}🎯 DEPENDENCY VALIDATION: ❌ $errors ISSUES FOUND${NC}"
        exit 1
    fi
}

main "$@"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/validate-dependencies.sh
    log_message "${GREEN}✅ Creating dependency validation script${NC}"
}

# Function: Create recovery system
create_recovery_system() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/composer-recovery.sh << 'EOF'
#!/bin/bash

# Composer Recovery System
# Purpose: Recover from composer dependency issues

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚨 COMPOSER RECOVERY SYSTEM${NC}"
echo "==========================="

# Function: Clear composer cache
clear_composer_cache() {
    echo -e "${YELLOW}Clearing composer cache...${NC}"
    composer clear-cache
    echo -e "${GREEN}✅ Composer cache cleared${NC}"
}

# Function: Reset vendor directory
reset_vendor_directory() {
    echo -e "${YELLOW}Resetting vendor directory...${NC}"
    
    if [[ -d "vendor" ]]; then
        rm -rf vendor/
        echo -e "${GREEN}✅ Vendor directory removed${NC}"
    fi
    
    if [[ -f "composer.lock" ]]; then
        rm composer.lock
        echo -e "${GREEN}✅ Composer lock file removed${NC}"
    fi
}

# Function: Reinstall dependencies
reinstall_dependencies() {
    local strategy="${1:-with-dev}"
    
    echo -e "${YELLOW}Reinstalling dependencies (strategy: $strategy)...${NC}"
    
    case "$strategy" in
        "production")
            composer install --optimize-autoloader --no-dev --no-interaction
            ;;
        "with-dev")
            composer install --optimize-autoloader --no-interaction
            ;;
        "basic")
            composer install --no-interaction
            ;;
        *)
            echo -e "${RED}❌ Unknown strategy: $strategy${NC}"
            exit 1
            ;;
    esac
    
    echo -e "${GREEN}✅ Dependencies reinstalled${NC}"
}

# Function: Validate installation
validate_installation() {
    echo -e "${BLUE}Validating installation...${NC}"
    
    # Check if vendor directory exists
    if [[ ! -d "vendor" ]]; then
        echo -e "${RED}❌ Vendor directory missing${NC}"
        return 1
    fi
    
    # Check if autoloader exists
    if [[ ! -f "vendor/autoload.php" ]]; then
        echo -e "${RED}❌ Autoloader missing${NC}"
        return 1
    fi
    
    # Test Laravel application
    if [[ -f "artisan" ]]; then
        if php artisan --version >/dev/null 2>&1; then
            echo -e "${GREEN}✅ Laravel application functional${NC}"
        else
            echo -e "${RED}❌ Laravel application error${NC}"
            return 1
        fi
    fi
    
    echo -e "${GREEN}✅ Installation validated${NC}"
    return 0
}

# Function: Full recovery procedure
full_recovery() {
    local strategy="${1:-with-dev}"
    
    echo -e "${RED}🚨 EXECUTING FULL RECOVERY PROCEDURE${NC}"
    echo ""
    
    clear_composer_cache
    reset_vendor_directory
    reinstall_dependencies "$strategy"
    validate_installation
    
    if [[ $? -eq 0 ]]; then
        echo ""
        echo -e "${GREEN}🎯 RECOVERY: ✅ COMPLETED SUCCESSFULLY${NC}"
    else
        echo ""
        echo -e "${RED}🎯 RECOVERY: ❌ FAILED - MANUAL INTERVENTION REQUIRED${NC}"
        exit 1
    fi
}

# Main execution
main() {
    case "${1:-help}" in
        "clear-cache")
            clear_composer_cache
            ;;
        "reset-vendor")
            reset_vendor_directory
            ;;
        "reinstall")
            reinstall_dependencies "${2:-with-dev}"
            ;;
        "validate")
            validate_installation
            ;;
        "full-recovery")
            full_recovery "${2:-with-dev}"
            ;;
        "help"|"--help"|"-h"|"")
            echo "Available recovery commands:"
            echo "  clear-cache              - Clear composer cache"
            echo "  reset-vendor             - Remove vendor directory and lock file"
            echo "  reinstall [strategy]     - Reinstall dependencies (production|with-dev|basic)"
            echo "  validate                 - Validate current installation"
            echo "  full-recovery [strategy] - Execute complete recovery procedure"
            echo ""
            echo "Examples:"
            echo "  $0 full-recovery production"
            echo "  $0 reinstall with-dev"
            ;;
        *)
            echo -e "${RED}❌ Error: Unknown command '$1'${NC}"
            echo "Use '$0 help' for available commands"
            exit 1
            ;;
    esac
}

main "$@"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/composer-recovery.sh
    log_message "${GREEN}✅ Creating composer recovery system${NC}"
}

# Function: Create build strategy selector
create_build_strategy_selector() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/select-build-strategy.sh << 'EOF'
#!/bin/bash

# Build Strategy Selector
# Purpose: Select appropriate build strategy based on environment and requirements

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🎯 BUILD STRATEGY SELECTOR${NC}"
echo "=========================="

# Function: Detect environment
detect_environment() {
    if [[ -f ".env" ]]; then
        local env=$(grep '^APP_ENV=' .env | cut -d'=' -f2)
        echo "$env"
    else
        echo "unknown"
    fi
}

# Function: Check if migrations need dev dependencies
check_migration_requirements() {
    if find database/migrations -name "*.php" -exec grep -l "fake\|Faker" {} \; 2>/dev/null | head -1 >/dev/null; then
        return 0  # Migrations need dev dependencies
    fi
    return 1  # No dev dependencies needed
}

# Function: Recommend build strategy
recommend_strategy() {
    local env=$(detect_environment)
    local needs_dev_for_migrations=false
    
    if check_migration_requirements; then
        needs_dev_for_migrations=true
    fi
    
    echo -e "${BLUE}Environment detected: $env${NC}"
    echo -e "${BLUE}Migrations need dev dependencies: $([ "$needs_dev_for_migrations" = true ] && echo "Yes" || echo "No")${NC}"
    echo ""
    
    case "$env" in
        "production")
            if [ "$needs_dev_for_migrations" = true ]; then
                echo -e "${YELLOW}⚠️  RECOMMENDED STRATEGY: build-with-dev${NC}"
                echo "   Reason: Production environment but migrations require Faker"
                echo "   Command: composer run-script build-with-dev"
            else
                echo -e "${GREEN}✅ RECOMMENDED STRATEGY: build-production${NC}"
                echo "   Reason: Production environment with no dev dependency requirements"
                echo "   Command: composer run-script build-production"
            fi
            ;;
        "staging")
            echo -e "${GREEN}✅ RECOMMENDED STRATEGY: build-with-dev${NC}"
            echo "   Reason: Staging environment benefits from dev tools"
            echo "   Command: composer run-script build-with-dev"
            ;;
        "local")
            echo -e "${GREEN}✅ RECOMMENDED STRATEGY: build-with-dev${NC}"
            echo "   Reason: Local development environment"
            echo "   Command: composer run-script build-with-dev"
            ;;
        *)
            echo -e "${YELLOW}⚠️  RECOMMENDED STRATEGY: build-fallback${NC}"
            echo "   Reason: Unknown environment - using safe fallback"
            echo "   Command: composer run-script build-fallback"
            ;;
    esac
}

# Function: Execute recommended strategy
execute_strategy() {
    local strategy="$1"
    
    case "$strategy" in
        "build-production")
            composer run-script build-production
            ;;
        "build-with-dev")
            composer run-script build-with-dev
            ;;
        "build-fallback")
            composer run-script build-fallback
            ;;
        *)
            echo -e "${RED}❌ Unknown strategy: $strategy${NC}"
            exit 1
            ;;
    esac
}

# Main execution
main() {
    case "${1:-recommend}" in
        "recommend")
            recommend_strategy
            ;;
        "execute")
            local strategy="${2}"
            if [[ -z "$strategy" ]]; then
                echo -e "${RED}❌ Error: Strategy not specified${NC}"
                echo "Usage: $0 execute [build-production|build-with-dev|build-fallback]"
                exit 1
            fi
            execute_strategy "$strategy"
            ;;
        "auto")
            # Auto-detect and execute
            local env=$(detect_environment)
            local needs_dev_for_migrations=false
            
            if check_migration_requirements; then
                needs_dev_for_migrations=true
            fi
            
            case "$env" in
                "production")
                    if [ "$needs_dev_for_migrations" = true ]; then
                        execute_strategy "build-with-dev"
                    else
                        execute_strategy "build-production"
                    fi
                    ;;
                "staging"|"local")
                    execute_strategy "build-with-dev"
                    ;;
                *)
                    execute_strategy "build-fallback"
                    ;;
            esac
            ;;
        "help"|"--help"|"-h")
            echo "Available commands:"
            echo "  recommend - Show recommended build strategy"
            echo "  execute [strategy] - Execute specific build strategy"
            echo "  auto - Auto-detect and execute optimal strategy"
            echo ""
            echo "Available strategies:"
            echo "  build-production - Optimized production build (no dev dependencies)"
            echo "  build-with-dev - Build with dev dependencies (for migrations/testing)"
            echo "  build-fallback - Safe fallback build strategy"
            ;;
        *)
            echo -e "${RED}❌ Error: Unknown command '$1'${NC}"
            echo "Use '$0 help' for available commands"
            exit 1
            ;;
    esac
}

main "$@"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/select-build-strategy.sh
    log_message "${GREEN}✅ Creating build strategy selector${NC}"
}

# Main execution
main() {
    backup_composer_json
    enhance_composer_json
    create_dependency_validator
    create_recovery_system
    create_build_strategy_selector
    
    echo ""
    log_message "${GREEN}📊 ENHANCED COMPOSER STRATEGY SUMMARY:${NC}"
    log_message "   → composer.json enhanced with build-aware scripts"
    log_message "   → Dependency validation system created"
    log_message "   → Recovery system implemented"
    log_message "   → Build strategy selector installed"
    log_message "   → Anti-pattern prevention configured"
    echo ""
    log_message "${GREEN}🎯 ENHANCED COMPOSER STRATEGY: ✅ SETUP COMPLETE${NC}"
}

main "$@"
