#!/bin/bash

# Phase 2 Completion Verification Script
# Verifies all preparation work is complete before deployment

echo "🔍 Verifying Phase 2 completion..."
echo "======================================="

ERRORS=0
WARNINGS=0

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

function check_pass() {
    echo -e "${GREEN}✅ $1${NC}"
}

function check_fail() {
    echo -e "${RED}❌ $1${NC}"
    ((ERRORS++))
}

function check_warn() {
    echo -e "${YELLOW}⚠️ $1${NC}"
    ((WARNINGS++))
}

echo "📦 Step 15: Dependencies & Lock Files"
echo "-----------------------------------"

# Check composer.lock
if [ -f "composer.lock" ]; then
    check_pass "composer.lock exists"
else
    check_fail "composer.lock missing - run 'composer install'"
fi

# Check package-lock.json (if applicable)
if [ -f "package.json" ]; then
    if [ -f "package-lock.json" ]; then
        check_pass "package-lock.json exists"
    else
        check_fail "package-lock.json missing - run 'npm install'"
    fi
else
    check_pass "No package.json (PHP-only project)"
fi

# Check vendor directory exists
if [ -d "vendor" ]; then
    check_pass "vendor/ directory exists"
else
    check_fail "vendor/ directory missing - run 'composer install'"
fi

echo ""
echo "🧪 Step 16: Build Process"
echo "------------------------"

# Check if build process can run
if [ -f "package.json" ]; then
    if command -v npm >/dev/null; then
        check_pass "npm available for builds"
    else
        check_fail "npm not available - cannot build frontend assets"
    fi
else
    check_pass "No frontend build needed"
fi

# Check for Laravel optimizations
if [ -f "artisan" ]; then
    if command -v php >/dev/null; then
        check_pass "php available for Laravel optimizations"
    else
        check_fail "php not available - cannot run Laravel commands"
    fi
fi

echo ""
echo "🛡️ Step 17: Customization Protection"
echo "-----------------------------------"

# Check custom directories
if [ -d "app/Custom" ]; then
    check_pass "app/Custom/ directory exists"
else
    check_fail "app/Custom/ directory missing"
fi

# Check custom config
if [ -f "config/custom.php" ]; then
    check_pass "config/custom.php exists"
else
    check_fail "config/custom.php missing"
fi

# Check service provider
if [ -f "app/Providers/CustomLayerServiceProvider.php" ]; then
    check_pass "CustomLayerServiceProvider exists"
else
    check_fail "CustomLayerServiceProvider missing"
fi

# Check service provider registration
if grep -q "CustomLayerServiceProvider" config/app.php 2>/dev/null; then
    check_pass "CustomLayerServiceProvider registered"
else
    check_fail "CustomLayerServiceProvider not registered in config/app.php"
fi

echo ""
echo "💾 Step 18: Data Persistence"
echo "---------------------------"

# Check persistence script
if [ -f "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh" ]; then
    check_pass "link_persistent_dirs.sh exists"
    if [ -x "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh" ]; then
        check_pass "link_persistent_dirs.sh is executable"
    else
        check_fail "link_persistent_dirs.sh not executable - run 'chmod +x Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh'"
    fi
else
    check_fail "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh missing"
fi

# Check verification script
if [ -f "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/verify_persistence.sh" ]; then
    check_pass "verify_persistence.sh exists"
    if [ -x "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/verify_persistence.sh" ]; then
        check_pass "verify_persistence.sh is executable"
    else
        check_warn "verify_persistence.sh not executable"
    fi
else
    check_warn "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/verify_persistence.sh missing (optional)"
fi

# Check data persistence documentation
if [ -f "DATA_PERSISTENCE.md" ]; then
    check_pass "DATA_PERSISTENCE.md exists"
else
    check_fail "DATA_PERSISTENCE.md missing"
fi

echo ""
echo "📚 Step 19: Documentation"
echo "------------------------"

# Check documentation directory
if [ -d "Admin-Local/myDocs" ]; then
    check_pass "Admin-Local/myDocs/ directory exists"
else
    check_fail "Admin-Local/myDocs/ directory missing"
fi

# Check critical documentation files
DOCS=(
    "Admin-Local/myDocs/CUSTOMIZATIONS.md"
    "Admin-Local/myDocs/DATA_PERSISTENCE.md"
    "Admin-Local/myDocs/DEPLOYMENT_PROCEDURES.md"
    "Admin-Local/myDocs/TEAM_HANDOFF.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        check_pass "$(basename "$doc") exists"
    else
        check_fail "$doc missing"
    fi
done

# Check main README
if [ -f "README.md" ]; then
    check_pass "README.md exists"
else
    check_fail "README.md missing"
fi

echo ""
echo "🔒 Environment & Security"
echo "------------------------"

# Check .env.example
if [ -f ".env.example" ]; then
    check_pass ".env.example template exists"
else
    check_fail ".env.example missing"
fi

# Check .gitignore
if [ -f ".gitignore" ]; then
    check_pass ".gitignore exists"
    
    # Check critical exclusions
    if grep -q "vendor/" .gitignore; then
        check_pass "vendor/ excluded in .gitignore"
    else
        check_fail "vendor/ not excluded in .gitignore"
    fi
    
    if grep -q "node_modules/" .gitignore; then
        check_pass "node_modules/ excluded in .gitignore"
    else
        check_warn "node_modules/ not excluded in .gitignore"
    fi
    
    if grep -q "\.env$" .gitignore; then
        check_pass ".env excluded in .gitignore"
    else
        check_fail ".env not excluded in .gitignore"
    fi
else
    check_fail ".gitignore missing"
fi

# Check that .env is not committed
if git ls-files | grep -q "^\.env$" 2>/dev/null; then
    check_fail ".env file is tracked by git (SECURITY RISK)"
else
    check_pass ".env file not tracked by git"
fi

echo ""
echo "📊 Git Repository Status"
echo "-----------------------"

# Check git status
if command -v git >/dev/null; then
    if git rev-parse --git-dir >/dev/null 2>&1; then
        check_pass "Git repository initialized"
        
        # Check if there are untracked important files
        UNTRACKED=$(git ls-files --others --exclude-standard | grep -E '\.(php|js|css|md|json|lock)$' | head -5)
        if [ -n "$UNTRACKED" ]; then
            check_warn "Untracked important files detected: $(echo $UNTRACKED | tr '\n' ' ')"
        else
            check_pass "No untracked important files"
        fi
    else
        check_fail "Not a git repository"
    fi
else
    check_fail "Git not available"
fi

echo ""
echo "======================================="
echo "📋 PHASE 2 VERIFICATION SUMMARY"
echo "======================================="

if [ $ERRORS -eq 0 ]; then
    if [ $WARNINGS -eq 0 ]; then
        echo -e "${GREEN}🎉 PHASE 2 COMPLETE! Ready for deployment${NC}"
        echo ""
        echo -e "${GREEN}✅ All preparation steps verified successfully${NC}"
        echo -e "${GREEN}✅ Repository ready for Phase 3 deployment${NC}"
        echo ""
        echo "🚀 Next: Choose deployment strategy and proceed to Phase 3"
        exit 0
    else
        echo -e "${YELLOW}⚠️ PHASE 2 MOSTLY COMPLETE (${WARNINGS} warnings)${NC}"
        echo ""
        echo "🔧 Address warnings above for optimal setup"
        echo "✅ Can proceed to deployment with current status"
        exit 0
    fi
else
    echo -e "${RED}❌ PHASE 2 INCOMPLETE (${ERRORS} errors, ${WARNINGS} warnings)${NC}"
    echo ""
    echo -e "${RED}🛑 Fix errors above before proceeding to deployment${NC}"
    echo ""
    echo "💡 Common fixes:"
    echo "   - Run 'composer install' for missing composer.lock"
    echo "   - Run 'npm install' for missing package-lock.json"
    echo "   - Complete customization protection setup"
    echo "   - Create missing documentation files"
    exit 1
fi