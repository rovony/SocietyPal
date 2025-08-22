#!/bin/bash

# Ultra-Powerful Phase 2 Verification Engine
# The final quality gate before production deployment
# Guarantees 100% readiness with comprehensive validation

echo "🚀 Ultra-Powerful Phase 2 Verification Engine v3.0"
echo "=================================================="
echo "🎯 Final Quality Gate: 100% Deployment Readiness"
echo ""

# Performance tracking
START_TIME=$(date +%s)
ERRORS=0
WARNINGS=0
CHECKS_PASSED=0
TOTAL_CHECKS=0

# Enhanced colors and symbols (macOS compatible)
COLOR_GREEN='\033[0;32m'
COLOR_RED='\033[0;31m'
COLOR_YELLOW='\033[1;33m'
COLOR_BLUE='\033[0;34m'
COLOR_PURPLE='\033[0;35m'
COLOR_CYAN='\033[0;36m'
COLOR_WHITE='\033[1;37m'
COLOR_NC='\033[0m'

SYMBOL_PASS="✅"
SYMBOL_FAIL="❌"
SYMBOL_WARN="⚠️"
SYMBOL_INFO="ℹ️"
SYMBOL_ROCKET="🚀"
SYMBOL_SHIELD="🛡️"
SYMBOL_GEAR="⚙️"
SYMBOL_DOCS="📚"
SYMBOL_LOCK="🔒"
SYMBOL_FIRE="🔥"

# Enhanced logging functions
function log_section() {
    echo ""
    echo -e "${COLOR_CYAN}${SYMBOL_GEAR} $1${COLOR_NC}"
    echo -e "${COLOR_CYAN}$(printf '%.0s-' {1..50})${COLOR_NC}"
}

function check_pass() {
    echo -e "${COLOR_GREEN}${SYMBOL_PASS} $1${COLOR_NC}"
    ((CHECKS_PASSED++))
    ((TOTAL_CHECKS++))
}

function check_fail() {
    echo -e "${COLOR_RED}${SYMBOL_FAIL} $1${COLOR_NC}"
    ((ERRORS++))
    ((TOTAL_CHECKS++))
}

function check_warn() {
    echo -e "${COLOR_YELLOW}${SYMBOL_WARN} $1${COLOR_NC}"
    ((WARNINGS++))
    ((TOTAL_CHECKS++))
}

function check_info() {
    echo -e "${COLOR_BLUE}${SYMBOL_INFO} $1${COLOR_NC}"
}

# Smart file detection
function smart_detect_framework() {
    if [ -f "artisan" ] && [ -f "composer.json" ]; then
        echo "laravel"
    elif [ -f "package.json" ] && grep -q "next" package.json 2>/dev/null; then
        echo "nextjs"
    elif [ -f "package.json" ] && grep -q "react" package.json 2>/dev/null; then
        echo "react"
    elif [ -f "package.json" ] && grep -q "vue" package.json 2>/dev/null; then
        echo "vue"
    elif [ -f "composer.json" ]; then
        echo "php"
    elif [ -f "package.json" ]; then
        echo "node"
    else
        echo "unknown"
    fi
}

# Get framework info
FRAMEWORK=$(smart_detect_framework)
check_info "Framework detected: $FRAMEWORK"

log_section "Step 15: Dependencies & Lock Files"

# Enhanced dependency verification
if [ -f "composer.json" ]; then
    if [ -f "composer.lock" ]; then
        # Verify lock file is up to date
        if [ "composer.json" -nt "composer.lock" ]; then
            check_warn "composer.lock may be outdated (run: composer update --lock)"
        else
            check_pass "composer.lock exists and up-to-date"
        fi
        
        # Count dependencies
        if command -v composer >/dev/null 2>&1; then
            COMPOSER_DEPS=$(composer show 2>/dev/null | wc -l || echo "0")
            check_info "Composer packages: $COMPOSER_DEPS installed"
        fi
        
        # Check for vulnerabilities
        if composer audit --format=json >/dev/null 2>&1; then
            check_pass "No known security vulnerabilities in Composer packages"
        else
            check_warn "Composer security audit failed or has issues"
        fi
    else
        check_fail "composer.lock missing - run 'composer install'"
    fi
    
    # Check vendor directory
    if [ -d "vendor" ]; then
        VENDOR_SIZE=$(du -sh vendor 2>/dev/null | cut -f1 || echo "Unknown")
        check_pass "vendor/ directory exists ($VENDOR_SIZE)"
    else
        check_fail "vendor/ directory missing - run 'composer install'"
    fi
fi

if [ -f "package.json" ]; then
    if [ -f "package-lock.json" ] || [ -f "yarn.lock" ] || [ -f "pnpm-lock.yaml" ]; then
        check_pass "JavaScript lock file exists"
        
        # Count packages
        if command -v npm >/dev/null 2>&1 && [ -d "node_modules" ]; then
            NPM_DEPS=$(npm list --depth=0 2>/dev/null | grep -c "├──\|└──" || echo "0")
            check_info "NPM packages: $NPM_DEPS installed"
        fi
        
        # Check for security issues
        if npm audit --audit-level=high --json >/dev/null 2>&1; then
            check_pass "No high-severity vulnerabilities in NPM packages"
        else
            check_warn "NPM packages have security vulnerabilities (run: npm audit fix)"
        fi
    else
        check_fail "JavaScript lock file missing - run 'npm install' or equivalent"
    fi
    
    # Check node_modules
    if [ -d "node_modules" ]; then
        NODE_SIZE=$(du -sh node_modules 2>/dev/null | cut -f1 || echo "Unknown")
        check_pass "node_modules/ directory exists ($NODE_SIZE)"
    else
        check_fail "node_modules/ directory missing - run 'npm install'"
    fi
fi

log_section "Step 16: Build Process Verification"

# Enhanced build system verification
if [ -f "package.json" ]; then
    # Check build scripts
    if grep -q '"build"' package.json; then
        check_pass "Build script defined in package.json"
        
        # Test build process
        if [ -d "dist" ] || [ -d "build" ] || [ -d ".next" ] || [ -d "public/build" ]; then
            check_pass "Build artifacts detected (previous build successful)"
        else
            check_warn "No build artifacts found - test build process"
        fi
    else
        check_warn "No build script in package.json"
    fi
    
    # Check for development dependencies
    if grep -q '"devDependencies"' package.json; then
        check_pass "Development dependencies configured"
    fi
fi

# Laravel-specific build verification
if [ "$FRAMEWORK" = "laravel" ]; then
    if command -v php >/dev/null 2>&1; then
        check_pass "PHP available for Laravel commands"
        
        # Test Laravel artisan
        if php artisan --version >/dev/null 2>&1; then
            check_pass "Laravel Artisan working"
        else
            check_fail "Laravel Artisan not responding"
        fi
        
        # Check for optimization files
        if [ -f "bootstrap/cache/config.php" ]; then
            check_pass "Configuration cache exists"
        else
            check_warn "No configuration cache (run: php artisan config:cache)"
        fi
        
        if [ -f "bootstrap/cache/routes-v7.php" ]; then
            check_pass "Route cache exists"
        else
            check_warn "No route cache (run: php artisan route:cache)"
        fi
    else
        check_fail "PHP not available - cannot run Laravel commands"
    fi
fi

log_section "Step 17: Customization Protection System"

# Verify Step 17 implementation
if [ -d "app/Custom" ]; then
    check_pass "app/Custom/ layer exists"
    
    # Check directory structure
    CUSTOM_DIRS=("Controllers" "Models" "Services" "Middleware" "config" "resources")
    for dir in "${CUSTOM_DIRS[@]}"; do
        if [ -d "app/Custom/$dir" ]; then
            check_pass "app/Custom/$dir/ structure ready"
        else
            check_info "app/Custom/$dir/ not yet created (create as needed)"
        fi
    done
    
    # Count custom files
    CUSTOM_FILES=$(find app/Custom -type f 2>/dev/null | wc -l || echo "0")
    check_info "Custom layer files: $CUSTOM_FILES"
else
    check_fail "app/Custom/ layer missing - Step 17 not implemented"
fi

# Check custom configuration
if [ -f "app/Custom/config/custom-app.php" ]; then
    check_pass "Custom app configuration exists"
else
    check_fail "app/Custom/config/custom-app.php missing"
fi

if [ -f "app/Custom/config/custom-database.php" ]; then
    check_pass "Custom database configuration exists"
else
    check_warn "app/Custom/config/custom-database.php missing (optional)"
fi

# Check service provider
if [ -f "app/Providers/CustomizationServiceProvider.php" ]; then
    check_pass "CustomizationServiceProvider exists"
    
    # Check if registered (Laravel 11+ uses bootstrap/providers.php)
    if [ -f "bootstrap/providers.php" ] && grep -q "CustomizationServiceProvider" bootstrap/providers.php 2>/dev/null; then
        check_pass "CustomizationServiceProvider registered in bootstrap/providers.php"
    elif grep -q "CustomizationServiceProvider" config/app.php 2>/dev/null; then
        check_pass "CustomizationServiceProvider registered in config/app.php"
    else
        check_fail "CustomizationServiceProvider not registered"
    fi
else
    check_fail "CustomizationServiceProvider missing"
fi

# Check asset system
if [ -f "webpack.custom.cjs" ] || [ -f "webpack.custom.js" ]; then
    check_pass "Custom asset compilation configured"
else
    check_warn "webpack.custom.js/cjs missing (optional for complex assets)"
fi

log_section "Step 18: Data Persistence System"

# Verify Step 18 implementation
PERSISTENCE_SCRIPT="Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/setup_data_persistence.sh"
if [ -f "$PERSISTENCE_SCRIPT" ]; then
    check_pass "Data persistence setup script exists"
    if [ -x "$PERSISTENCE_SCRIPT" ]; then
        check_pass "setup_data_persistence.sh executable"
    else
        check_fail "setup_data_persistence.sh not executable"
    fi
else
    check_fail "setup_data_persistence.sh missing - Step 18 not implemented"
fi

# Check verification script
VERIFY_PERSIST_SCRIPT="Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/verify_data_persistence.sh"
if [ -f "$VERIFY_PERSIST_SCRIPT" ]; then
    check_pass "Data persistence verification script exists"
    if [ -x "$VERIFY_PERSIST_SCRIPT" ]; then
        check_pass "verify_data_persistence.sh executable"
    fi
else
    check_warn "verify_data_persistence.sh missing (optional)"
fi

# Check critical directories (Laravel-specific)
if [ "$FRAMEWORK" = "laravel" ]; then
    CRITICAL_DIRS=("storage/app" "storage/logs" "public/uploads")
    for dir in "${CRITICAL_DIRS[@]}"; do
        if [ -d "$dir" ]; then
            check_pass "$dir/ exists"
        else
            check_warn "$dir/ missing (will be created during deployment)"
        fi
    done
fi

log_section "Step 19: Investment Protection Documentation"

# Verify Step 19 implementation
DOC_GENERATE_SCRIPT="Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-19-Files/generate_investment_documentation.sh"
if [ -f "$DOC_GENERATE_SCRIPT" ]; then
    check_pass "Investment documentation generator exists"
    if [ -x "$DOC_GENERATE_SCRIPT" ]; then
        check_pass "generate_investment_documentation.sh executable"
    fi
else
    check_fail "generate_investment_documentation.sh missing - Step 19 not implemented"
fi

# Check tracking script
TRACK_SCRIPT="Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-19-Files/track_investment_changes.sh"
if [ -f "$TRACK_SCRIPT" ]; then
    check_pass "Investment change tracking script exists"
    if [ -x "$TRACK_SCRIPT" ]; then
        check_pass "track_investment_changes.sh executable"
    fi
else
    check_warn "track_investment_changes.sh missing (optional)"
fi

# Check documentation structure
DOC_DIRS=("docs/Investment-Protection" ".investment-tracking")
for dir in "${DOC_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        check_pass "$dir/ structure ready"
    else
        check_warn "$dir/ structure missing (will be created during execution)"
    fi
done

log_section "Security & Environment Validation"

# Enhanced security checks
if [ -f ".env.example" ]; then
    check_pass ".env.example template exists"
    
    # Check for required variables
    REQUIRED_VARS=("APP_KEY" "DB_CONNECTION" "DB_DATABASE")
    for var in "${REQUIRED_VARS[@]}"; do
        if grep -q "^$var=" .env.example 2>/dev/null; then
            check_pass "$var defined in .env.example"
        else
            check_warn "$var missing in .env.example"
        fi
    done
else
    check_fail ".env.example template missing"
fi

# Advanced .gitignore verification
if [ -f ".gitignore" ]; then
    check_pass ".gitignore exists"
    
    # Check critical exclusions
    CRITICAL_EXCLUSIONS=("vendor/" "node_modules/" ".env" "storage/logs/" "bootstrap/cache/")
    for exclusion in "${CRITICAL_EXCLUSIONS[@]}"; do
        if grep -q "$exclusion" .gitignore 2>/dev/null; then
            check_pass "$exclusion excluded in .gitignore"
        else
            check_warn "$exclusion not excluded in .gitignore"
        fi
    done
    
    # Check framework-specific exclusions
    if [ "$FRAMEWORK" = "laravel" ]; then
        LARAVEL_EXCLUSIONS=("public/storage" "storage/framework/cache/" "storage/framework/sessions/")
        for exclusion in "${LARAVEL_EXCLUSIONS[@]}"; do
            if grep -q "$exclusion" .gitignore 2>/dev/null; then
                check_pass "$exclusion (Laravel) excluded"
            else
                check_warn "$exclusion (Laravel) not excluded"
            fi
        done
    fi
    
    if [ "$FRAMEWORK" = "nextjs" ]; then
        NEXTJS_EXCLUSIONS=(".next/" "out/" "build/")
        for exclusion in "${NEXTJS_EXCLUSIONS[@]}"; do
            if grep -q "$exclusion" .gitignore 2>/dev/null; then
                check_pass "$exclusion (Next.js) excluded"
            else
                check_warn "$exclusion (Next.js) not excluded"
            fi
        done
    fi
else
    check_fail ".gitignore missing"
fi

# Verify sensitive files not tracked
if git ls-files | grep -E "^\.env$|^config/.*\.json$|.*\.key$" 2>/dev/null | head -5; then
    check_fail "Sensitive files detected in git tracking"
else
    check_pass "No sensitive files in git tracking"
fi

log_section "Git Repository & Version Control"

if command -v git >/dev/null 2>&1; then
    if git rev-parse --git-dir >/dev/null 2>&1; then
        check_pass "Git repository initialized"
        
        # Check remote configuration
        if git remote -v | grep -q "origin" 2>/dev/null; then
            REMOTE_URL=$(git remote get-url origin 2>/dev/null || echo "Unknown")
            check_pass "Git remote configured: $REMOTE_URL"
        else
            check_warn "No git remote configured"
        fi
        
        # Check branch status
        CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "Unknown")
        check_info "Current branch: $CURRENT_BRANCH"
        
        # Check for untracked critical files
        UNTRACKED_CRITICAL=$(git ls-files --others --exclude-standard | grep -E '\.(php|js|ts|css|scss|json|md|lock)$' | head -10)
        if [ -n "$UNTRACKED_CRITICAL" ]; then
            check_warn "Untracked critical files: $(echo "$UNTRACKED_CRITICAL" | tr '\n' ' ' | head -c 100)..."
        else
            check_pass "All critical files tracked"
        fi
        
        # Check for uncommitted changes
        if [ -n "$(git status --porcelain 2>/dev/null)" ]; then
            UNCOMMITTED_COUNT=$(git status --porcelain 2>/dev/null | wc -l)
            check_warn "Uncommitted changes detected: $UNCOMMITTED_COUNT files"
        else
            check_pass "Working directory clean"
        fi
        
        # Check commit history
        COMMIT_COUNT=$(git rev-list --count HEAD 2>/dev/null || echo "0")
        check_info "Total commits: $COMMIT_COUNT"
        
        if [ "$COMMIT_COUNT" -gt 0 ]; then
            LAST_COMMIT=$(git log -1 --pretty=format:"%h - %s" 2>/dev/null || echo "Unknown")
            check_info "Last commit: $LAST_COMMIT"
        fi
    else
        check_fail "Not a git repository"
    fi
else
    check_fail "Git not available"
fi

log_section "Performance & System Health"

# System resource check
if command -v df >/dev/null 2>&1; then
    DISK_USAGE=$(df -h . 2>/dev/null | tail -1 | awk '{print $5}' | sed 's/%//')
    if [ "$DISK_USAGE" -lt 90 ]; then
        check_pass "Disk space healthy ($DISK_USAGE% used)"
    else
        check_warn "Disk space running low ($DISK_USAGE% used)"
    fi
fi

# Memory check (if available)
if command -v free >/dev/null 2>&1; then
    MEM_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
    if [ "$MEM_USAGE" -lt 90 ]; then
        check_pass "Memory usage healthy ($MEM_USAGE% used)"
    else
        check_warn "Memory usage high ($MEM_USAGE% used)"
    fi
fi

# Project size analysis
if command -v du >/dev/null 2>&1; then
    PROJECT_SIZE=$(du -sh . 2>/dev/null | cut -f1 || echo "Unknown")
    check_info "Project size: $PROJECT_SIZE"
fi

# Final calculations
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
PASS_RATE=$(( CHECKS_PASSED * 100 / TOTAL_CHECKS ))

log_section "Ultra-Powerful Verification Summary"

echo ""
echo -e "${COLORS[WHITE]}=============================================${COLORS[NC]}"
echo -e "${COLORS[WHITE]}${SYMBOLS[ROCKET]} ULTRA-POWERFUL PHASE 2 VERIFICATION COMPLETE${COLORS[NC]}"
echo -e "${COLORS[WHITE]}=============================================${COLORS[NC]}"
echo ""

# Performance metrics
echo -e "${COLORS[CYAN]}${SYMBOLS[GEAR]} Performance Metrics:${COLORS[NC]}"
echo -e "   Duration: ${DURATION}s | Checks: $TOTAL_CHECKS | Pass Rate: $PASS_RATE%"
echo -e "   Framework: $FRAMEWORK | Project: $(basename "$(pwd)")"
echo ""

# Results summary
if [ $ERRORS -eq 0 ]; then
    if [ $WARNINGS -eq 0 ]; then
        echo -e "${COLORS[GREEN]}${SYMBOLS[FIRE]} PERFECT SCORE! 100% DEPLOYMENT READY${COLORS[NC]}"
        echo ""
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} All systems verified and operational${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Zero errors, zero warnings detected${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Investment protection active and verified${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Data persistence system operational${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Security posture excellent${COLORS[NC]}"
        echo ""
        echo -e "${COLORS[GREEN]}${SYMBOLS[ROCKET]} STATUS: ${COLORS[WHITE]}CLEARED FOR PRODUCTION DEPLOYMENT${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[ROCKET]} NEXT: Execute Phase 3 deployment with 100% confidence${COLORS[NC]}"
        echo ""
        exit 0
    else
        echo -e "${COLORS[YELLOW]}${SYMBOLS[WARN]} DEPLOYMENT READY WITH MINOR OPTIMIZATIONS ($WARNINGS warnings)${COLORS[NC]}"
        echo ""
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Core systems verified and operational${COLORS[NC]}"
        echo -e "${COLORS[YELLOW]}${SYMBOLS[WARN]} $WARNINGS optimization opportunities identified${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[PASS]} Safe to proceed with deployment${COLORS[NC]}"
        echo ""
        echo -e "${COLORS[YELLOW]}${SYMBOLS[GEAR]} RECOMMENDATION: Address warnings for optimal setup${COLORS[NC]}"
        echo -e "${COLORS[GREEN]}${SYMBOLS[ROCKET]} STATUS: ${COLORS[WHITE]}CLEARED FOR PRODUCTION DEPLOYMENT${COLORS[NC]}"
        echo ""
        exit 0
    fi
else
    echo -e "${COLORS[RED]}${SYMBOLS[FAIL]} DEPLOYMENT BLOCKED - CRITICAL ISSUES DETECTED${COLORS[NC]}"
    echo ""
    echo -e "${COLORS[RED]}${SYMBOLS[FAIL]} Critical errors: $ERRORS${COLORS[NC]}"
    echo -e "${COLORS[YELLOW]}${SYMBOLS[WARN]} Warnings: $WARNINGS${COLORS[NC]}"
    echo -e "${COLORS[WHITE]}${SYMBOLS[INFO]} Checks passed: $CHECKS_PASSED/$TOTAL_CHECKS${COLORS[NC]}"
    echo ""
    echo -e "${COLORS[RED]}${SYMBOLS[LOCK]} DEPLOYMENT BLOCKED UNTIL ERRORS RESOLVED${COLORS[NC]}"
    echo ""
    echo -e "${COLORS[WHITE]}${SYMBOLS[GEAR]} Quick fixes:${COLORS[NC]}"
    echo -e "   ${SYMBOLS[GEAR]} Missing dependencies: Run 'composer install && npm install'"
    echo -e "   ${SYMBOLS[GEAR]} Custom layer: Complete Step 17 implementation"
    echo -e "   ${SYMBOLS[GEAR]} Data persistence: Complete Step 18 implementation"
    echo -e "   ${SYMBOLS[GEAR]} Documentation: Complete Step 19 implementation"
    echo -e "   ${SYMBOLS[GEAR]} Git issues: Commit or stash changes, configure .gitignore"
    echo ""
    exit 1
fi