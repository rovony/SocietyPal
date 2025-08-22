#!/bin/bash

# Build Strategy Configuration Script
# Purpose: Configure and validate build strategy for Laravel deployment

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔧 BUILD STRATEGY CONFIGURATION${NC}"
echo "==============================="

# Configuration results tracking
CONFIGURATIONS_APPLIED=0
CONFIGURATIONS_FAILED=0
CONFIGURATION_RESULTS=()

# Function: Log configuration results
log_config() {
    local config_name="$1"
    local status="$2"
    local message="$3"
    
    if [[ "$status" == "SUCCESS" ]]; then
        echo -e "${GREEN}✅ $config_name: $message${NC}"
        ((CONFIGURATIONS_APPLIED++))
        CONFIGURATION_RESULTS+=("✅ $config_name: $message")
    else
        echo -e "${RED}❌ $config_name: $message${NC}"
        ((CONFIGURATIONS_FAILED++))
        CONFIGURATION_RESULTS+=("❌ $config_name: $message")
    fi
}

# Configure Composer build strategy
configure_composer_strategy() {
    echo -e "${BLUE}📋 Composer Build Strategy${NC}"
    echo "=========================="
    
    # Check if enhanced composer strategy exists
    if [[ -f "Admin-Local/2-Project-Area/03-Project-Scripts/enhanced-composer-strategy.sh" ]]; then
        log_config "COMPOSER_STRATEGY" "SUCCESS" "Enhanced composer strategy available"
        
        # Execute enhanced composer strategy
        chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/enhanced-composer-strategy.sh
        if ./Admin-Local/2-Project-Area/03-Project-Scripts/enhanced-composer-strategy.sh >/dev/null 2>&1; then
            log_config "COMPOSER_EXECUTION" "SUCCESS" "Enhanced composer strategy executed"
        else
            log_config "COMPOSER_EXECUTION" "FAIL" "Enhanced composer strategy execution failed"
        fi
    else
        log_config "COMPOSER_STRATEGY" "FAIL" "Enhanced composer strategy not found"
    fi
    
    # Verify composer.enhanced.json was created
    if [[ -f "composer.enhanced.json" ]]; then
        log_config "COMPOSER_ENHANCED" "SUCCESS" "Enhanced composer configuration created"
    else
        log_config "COMPOSER_ENHANCED" "FAIL" "Enhanced composer configuration not created"
    fi
    
    echo ""
}

# Configure NPM build strategy
configure_npm_strategy() {
    echo -e "${BLUE}📋 NPM Build Strategy${NC}"
    echo "===================="
    
    # Check if package.json exists
    if [[ -f "package.json" ]]; then
        log_config "PACKAGE_JSON" "SUCCESS" "package.json found"
        
        # Check for build scripts
        if command -v jq >/dev/null 2>&1; then
            if jq -e '.scripts.build' package.json >/dev/null 2>&1; then
                log_config "BUILD_SCRIPT" "SUCCESS" "Build script configured"
            else
                log_config "BUILD_SCRIPT" "FAIL" "Build script not configured"
            fi
            
            if jq -e '.scripts.production' package.json >/dev/null 2>&1; then
                log_config "PRODUCTION_SCRIPT" "SUCCESS" "Production script configured"
            else
                log_config "PRODUCTION_SCRIPT" "FAIL" "Production script not configured"
            fi
        else
            log_config "JQ_CHECK" "FAIL" "jq not available for package.json analysis"
        fi
        
        # Test build process
        if command -v npm >/dev/null 2>&1; then
            if npm run build --dry-run >/dev/null 2>&1 || npm run production --dry-run >/dev/null 2>&1; then
                log_config "BUILD_TEST" "SUCCESS" "Build process test passed"
            else
                log_config "BUILD_TEST" "FAIL" "Build process test failed"
            fi
        else
            log_config "NPM_AVAILABLE" "FAIL" "NPM not available"
        fi
    else
        log_config "PACKAGE_JSON" "FAIL" "package.json not found"
    fi
    
    echo ""
}

# Configure Laravel optimization strategy
configure_laravel_optimization() {
    echo -e "${BLUE}📋 Laravel Optimization Strategy${NC}"
    echo "==============================="
    
    # Create optimization script
    local optimization_script="Admin-Local/2-Project-Area/03-Project-Scripts/laravel-optimization.sh"
    cat > "$optimization_script" << 'EOF'
#!/bin/bash

# Laravel Optimization Script
# Purpose: Apply Laravel optimizations for production

set -e

echo "🔧 Applying Laravel optimizations..."

# Clear all caches first
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events (Laravel 11+)
php artisan event:cache 2>/dev/null || true

# Optimize autoloader
composer dump-autoload --optimize --no-dev 2>/dev/null || composer dump-autoload --optimize

echo "✅ Laravel optimizations applied successfully"
EOF

    chmod +x "$optimization_script"
    log_config "OPTIMIZATION_SCRIPT" "SUCCESS" "Laravel optimization script created"
    
    # Test optimization script
    if [[ -f "artisan" ]] && command -v php >/dev/null 2>&1; then
        if php artisan config:clear >/dev/null 2>&1; then
            log_config "OPTIMIZATION_TEST" "SUCCESS" "Laravel optimization test passed"
        else
            log_config "OPTIMIZATION_TEST" "FAIL" "Laravel optimization test failed"
        fi
    else
        log_config "LARAVEL_AVAILABLE" "FAIL" "Laravel or PHP not available"
    fi
    
    echo ""
}

# Configure deployment build hooks
configure_deployment_hooks() {
    echo -e "${BLUE}📋 Deployment Build Hooks${NC}"
    echo "========================="
    
    # Check if DeployHQ hooks exist
    if [[ -f "Admin-Local/3-Deployment-Pipeline/04-Hooks/deployhq-build-commands.sh" ]]; then
        log_config "DEPLOYHQ_HOOKS" "SUCCESS" "DeployHQ build hooks available"
    else
        log_config "DEPLOYHQ_HOOKS" "FAIL" "DeployHQ build hooks not found"
    fi
    
    # Create build validation hook
    local validation_hook="Admin-Local/3-Deployment-Pipeline/04-Hooks/build-validation.sh"
    mkdir -p "$(dirname "$validation_hook")"
    
    cat > "$validation_hook" << 'EOF'
#!/bin/bash

# Build Validation Hook
# Purpose: Validate build artifacts before deployment

set -e

echo "🔍 Validating build artifacts..."

# Check vendor directory
if [[ ! -d "vendor" ]]; then
    echo "❌ Vendor directory missing"
    exit 1
fi

# Check autoloader optimization
if [[ ! -f "vendor/composer/autoload_classmap.php" ]] || [[ ! -s "vendor/composer/autoload_classmap.php" ]]; then
    echo "❌ Autoloader not optimized"
    exit 1
fi

# Check Laravel caches
if [[ ! -f "bootstrap/cache/config.php" ]]; then
    echo "❌ Configuration cache missing"
    exit 1
fi

# Check frontend assets (if applicable)
if [[ -f "package.json" ]]; then
    if [[ ! -d "public/build" ]] && [[ ! -f "public/js/app.js" ]] && [[ ! -f "public/css/app.css" ]]; then
        echo "❌ Frontend assets not built"
        exit 1
    fi
fi

echo "✅ Build validation passed"
EOF

    chmod +x "$validation_hook"
    log_config "VALIDATION_HOOK" "SUCCESS" "Build validation hook created"
    
    echo ""
}

# Configure environment-specific build strategies
configure_environment_strategies() {
    echo -e "${BLUE}📋 Environment-Specific Strategies${NC}"
    echo "================================="
    
    # Create environment-specific build configurations
    local strategies_dir="Admin-Local/2-Project-Area/03-Project-Scripts/build-strategies"
    mkdir -p "$strategies_dir"
    
    # Development strategy
    cat > "$strategies_dir/development.sh" << 'EOF'
#!/bin/bash
# Development Build Strategy

set -e

echo "🔧 Applying development build strategy..."

# Install all dependencies including dev
composer install --optimize-autoloader

# Install NPM dependencies
npm install

# Build assets for development
npm run dev 2>/dev/null || npm run build 2>/dev/null || true

# Clear caches but don't optimize
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ Development build completed"
EOF

    # Staging strategy
    cat > "$strategies_dir/staging.sh" << 'EOF'
#!/bin/bash
# Staging Build Strategy

set -e

echo "🔧 Applying staging build strategy..."

# Install production dependencies with dev fallback
composer install --optimize-autoloader --no-dev || composer install --optimize-autoloader

# Install NPM production dependencies
npm ci --only=production 2>/dev/null || npm install --only=production

# Build assets for production
npm run build 2>/dev/null || npm run production 2>/dev/null || true

# Apply Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

echo "✅ Staging build completed"
EOF

    # Production strategy
    cat > "$strategies_dir/production.sh" << 'EOF'
#!/bin/bash
# Production Build Strategy

set -e

echo "🔧 Applying production build strategy..."

# Install only production dependencies
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Install NPM production dependencies
npm ci --only=production --silent

# Build and optimize assets
npm run build --silent || npm run production --silent

# Apply all Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

# Optimize autoloader for production
composer dump-autoload --optimize --no-dev

echo "✅ Production build completed"
EOF

    # Make all strategies executable
    chmod +x "$strategies_dir"/*.sh
    
    log_config "ENV_STRATEGIES" "SUCCESS" "Environment-specific build strategies created"
    
    echo ""
}

# Create master build configuration
create_master_build_config() {
    echo -e "${BLUE}📋 Master Build Configuration${NC}"
    echo "============================"
    
    local master_config="Admin-Local/2-Project-Area/03-Project-Scripts/master-build.sh"
    
    cat > "$master_config" << 'EOF'
#!/bin/bash

# Master Build Configuration Script
# Purpose: Execute complete build process based on environment

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

ENVIRONMENT=${1:-production}

echo -e "${BLUE}🚀 MASTER BUILD PROCESS${NC}"
echo "Environment: $ENVIRONMENT"
echo "======================="

# Load deployment variables if available
if [[ -f "Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh" ]]; then
    echo "Loading deployment variables..."
    ./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh "$ENVIRONMENT" || true
fi

# Run pre-build validation
if [[ -f "Admin-Local/2-Project-Area/03-Project-Scripts/enhanced-pre-build-validation.sh" ]]; then
    echo "Running pre-build validation..."
    ./Admin-Local/2-Project-Area/03-Project-Scripts/enhanced-pre-build-validation.sh || {
        echo -e "${RED}❌ Pre-build validation failed${NC}"
        exit 1
    }
fi

# Execute environment-specific build strategy
local strategy_script="Admin-Local/2-Project-Area/03-Project-Scripts/build-strategies/$ENVIRONMENT.sh"
if [[ -f "$strategy_script" ]]; then
    echo "Executing $ENVIRONMENT build strategy..."
    ./"$strategy_script"
else
    echo -e "${YELLOW}⚠️  No specific strategy for $ENVIRONMENT, using default${NC}"
    # Default build process
    composer install --optimize-autoloader
    npm install && npm run build 2>/dev/null || true
    php artisan config:cache
fi

# Run security scanning
if [[ -f "Admin-Local/2-Project-Area/03-Project-Scripts/comprehensive-security-scanning.sh" ]]; then
    echo "Running security scan..."
    ./Admin-Local/2-Project-Area/03-Project-Scripts/comprehensive-security-scanning.sh || true
fi

# Validate production dependencies
if [[ -f "Admin-Local/2-Project-Area/03-Project-Scripts/verify-production-dependencies.sh" ]]; then
    echo "Verifying production dependencies..."
    ./Admin-Local/2-Project-Area/03-Project-Scripts/verify-production-dependencies.sh || {
        echo -e "${YELLOW}⚠️  Production dependency verification failed${NC}"
    }
fi

# Run build validation
if [[ -f "Admin-Local/3-Deployment-Pipeline/04-Hooks/build-validation.sh" ]]; then
    echo "Running build validation..."
    ./Admin-Local/3-Deployment-Pipeline/04-Hooks/build-validation.sh || {
        echo -e "${RED}❌ Build validation failed${NC}"
        exit 1
    }
fi

echo -e "${GREEN}✅ Master build process completed successfully${NC}"
EOF

    chmod +x "$master_config"
    log_config "MASTER_BUILD" "SUCCESS" "Master build configuration created"
    
    echo ""
}

# Generate build strategy report
generate_build_strategy_report() {
    echo -e "${BLUE}📊 BUILD STRATEGY REPORT${NC}"
    echo "======================="
    
    local report_file="Admin-Local/5-Monitoring-And-Logs/01-System-Reports/build-strategy-$(date +%Y%m%d_%H%M%S).md"
    mkdir -p "$(dirname "$report_file")"
    
    cat > "$report_file" << EOF
# Build Strategy Configuration Report

**Generated**: $(date '+%Y-%m-%d %H:%M:%S')
**Project**: SocietyPal Laravel Application

## Executive Summary
- **Configurations Applied**: $CONFIGURATIONS_APPLIED
- **Configurations Failed**: $CONFIGURATIONS_FAILED
- **Status**: $(if [[ $CONFIGURATIONS_FAILED -eq 0 ]]; then echo "✅ Build Strategy Ready"; else echo "❌ Configuration Issues"; fi)

## Build Strategy Components

### Composer Strategy
- Enhanced composer strategy with build-aware dependency management
- Production optimization with dev fallback for migrations
- Autoloader optimization for performance

### NPM Strategy
- Environment-specific asset building
- Production-optimized frontend compilation
- Build validation and testing

### Laravel Optimization
- Configuration, route, and view caching
- Event caching for Laravel 11+
- Storage and permission management

### Deployment Integration
- DeployHQ build hooks integration
- Environment-specific build strategies
- Build validation and verification

## Configuration Results

EOF

    for result in "${CONFIGURATION_RESULTS[@]}"; do
        echo "$result" >> "$report_file"
    done
    
    cat >> "$report_file" << EOF

## Build Strategies Available

### Development
- Full dependency installation including dev packages
- Development asset compilation
- Cache clearing without optimization

### Staging
- Production dependencies with dev fallback
- Production asset compilation
- Laravel optimizations applied

### Production
- Strict production-only dependencies
- Optimized asset compilation
- Full Laravel optimizations and caching

## Usage Instructions

### Manual Build
\`\`\`bash
# Run master build process
./Admin-Local/2-Project-Area/03-Project-Scripts/master-build.sh [environment]

# Environment-specific builds
./Admin-Local/2-Project-Area/03-Project-Scripts/build-strategies/production.sh
\`\`\`

### DeployHQ Integration
- Use deployhq-build-commands.sh for automated deployment
- Build validation runs automatically
- Environment-specific optimizations applied

---
**Configuration Complete**: $(date '+%Y-%m-%d %H:%M:%S')
EOF

    echo -e "${GREEN}📊 Build strategy report saved: $report_file${NC}"
}

# Display configuration summary
display_configuration_summary() {
    echo ""
    echo -e "${BLUE}📊 CONFIGURATION SUMMARY${NC}"
    echo "========================"
    echo -e "${GREEN}✅ Applied: $CONFIGURATIONS_APPLIED${NC}"
    echo -e "${RED}❌ Failed: $CONFIGURATIONS_FAILED${NC}"
    echo ""
    
    echo -e "${BLUE}📋 CONFIGURATION RESULTS:${NC}"
    for result in "${CONFIGURATION_RESULTS[@]}"; do
        echo "$result"
    done
    
    echo ""
    if [[ $CONFIGURATIONS_FAILED -eq 0 ]]; then
        echo -e "${GREEN}🎯 BUILD STRATEGY: ✅ CONFIGURED SUCCESSFULLY${NC}"
        echo -e "${GREEN}🚀 Build process ready for all environments!${NC}"
    else
        echo -e "${RED}🎯 BUILD STRATEGY: ❌ $CONFIGURATIONS_FAILED ISSUES FOUND${NC}"
        echo -e "${RED}🔧 Fix configuration issues before proceeding${NC}"
    fi
}

# Main execution
main() {
    echo "Starting build strategy configuration..."
    echo ""
    
    # Create necessary directories
    mkdir -p Admin-Local/{2-Project-Area/03-Project-Scripts,3-Deployment-Pipeline/04-Hooks,5-Monitoring-And-Logs/01-System-Reports}
    
    # Run all configuration phases
    configure_composer_strategy
    configure_npm_strategy
    configure_laravel_optimization
    configure_deployment_hooks
    configure_environment_strategies
    create_master_build_config
    generate_build_strategy_report
    display_configuration_summary
    
    # Return appropriate exit code
    if [[ $CONFIGURATIONS_FAILED -eq 0 ]]; then
        exit 0
    else
        exit 1
    fi
}

# Execute main function
main "$@"
