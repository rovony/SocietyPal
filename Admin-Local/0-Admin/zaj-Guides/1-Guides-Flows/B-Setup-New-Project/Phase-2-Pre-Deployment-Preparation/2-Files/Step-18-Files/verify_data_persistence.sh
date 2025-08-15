#!/bin/bash

# 🔍 Lightning-Fast Data Persistence Verification
# Comprehensive health check in under 5 seconds

set -e

SHARED_PATH="${1:-../shared}"
RELEASE_PATH="${2:-$(pwd)}"

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${PURPLE}🔍 Lightning-Fast Data Persistence Verification${NC}"
echo -e "${BLUE}⚡ Checking system in under 5 seconds...${NC}"
echo ""

PASSED=0
FAILED=0
WARNINGS=0

pass() {
    echo -e "${GREEN}✅ $1${NC}"
    ((PASSED++))
}

fail() {
    echo -e "${RED}❌ $1${NC}"
    ((FAILED++))
}

warn() {
    echo -e "${YELLOW}⚠️ $1${NC}"
    ((WARNINGS++))
}

info() {
    echo -e "${CYAN}ℹ️ $1${NC}"
}

# 1. Check Shared Directory Structure
echo -e "${CYAN}📁 Checking shared directory structure...${NC}"
for dir in "storage" "public" "config-backups"; do
    if [ -d "$SHARED_PATH/$dir" ]; then
        pass "Shared directory exists: $dir"
    else
        fail "Missing shared directory: $dir"
    fi
done

# 2. Check Symlinks
echo -e "\n${CYAN}🔗 Checking symlinks...${NC}"
cd "$RELEASE_PATH"
for link in "storage" "public" ".env"; do
    if [ -L "$link" ]; then
        target=$(readlink "$link")
        if [ -e "$link" ]; then
            pass "Valid symlink: $link -> $target"
        else
            fail "Broken symlink: $link -> $target"
        fi
    else
        fail "Missing symlink: $link"
    fi
done

# 3. Check Permissions
echo -e "\n${CYAN}🔒 Checking permissions...${NC}"
if [ -w "$SHARED_PATH/storage" ] && [ -w "$SHARED_PATH/public" ]; then
    pass "Shared directories are writable"
else
    fail "Shared directories have permission issues"
fi

# 4. Check Framework Detection
echo -e "\n${CYAN}🎯 Checking framework detection...${NC}"
if [ -f "$SHARED_PATH/.persistence-config" ]; then
    source "$SHARED_PATH/.persistence-config"
    pass "Framework detected: $FRAMEWORK"
    info "Build exclusions: ${BUILD_EXCLUSIONS[*]}"
else
    warn "No framework configuration found"
fi

# 5. Check User Data
echo -e "\n${CYAN}📊 Checking user data...${NC}"
user_data_found=false
for pattern in "uploads" "media" "files" "invoices" "reports"; do
    if [ -d "$SHARED_PATH/public/$pattern" ]; then
        file_count=$(find "$SHARED_PATH/public/$pattern" -type f 2>/dev/null | wc -l)
        if [ "$file_count" -gt 0 ]; then
            pass "User data found: $pattern ($file_count files)"
            user_data_found=true
        else
            info "Empty user directory: $pattern"
        fi
    fi
done

if [ "$user_data_found" = false ]; then
    warn "No user data found (this is normal for fresh installations)"
fi

# 6. Check Build Directories
echo -e "\n${CYAN}🔧 Checking build directories...${NC}"
build_dirs_ok=true
for dir in "css" "js" "build"; do
    if [ -d "public/$dir" ]; then
        pass "Build directory exists: public/$dir"
    else
        warn "Build directory missing: public/$dir (will be created during build)"
        build_dirs_ok=false
    fi
done

# 7. Test Write Permissions
echo -e "\n${CYAN}🧪 Testing write permissions...${NC}"
test_file="$SHARED_PATH/storage/logs/persistence_test_$(date +%s).log"
if echo "Persistence test: $(date)" > "$test_file" 2>/dev/null; then
    pass "Write permissions working"
    rm -f "$test_file"
else
    fail "Write permissions failed"
fi

# 8. Check Monitoring Tools
echo -e "\n${CYAN}🛠️ Checking monitoring tools...${NC}"
if [ -f "$SHARED_PATH/health-check.sh" ] && [ -x "$SHARED_PATH/health-check.sh" ]; then
    pass "Health check tool available"
else
    warn "Health check tool missing or not executable"
fi

if [ -f "$SHARED_PATH/emergency-recovery.sh" ] && [ -x "$SHARED_PATH/emergency-recovery.sh" ]; then
    pass "Emergency recovery tool available"
else
    warn "Emergency recovery tool missing or not executable"
fi

# 9. Laravel-specific Checks
if [ -f "artisan" ]; then
    echo -e "\n${CYAN}🎯 Laravel-specific checks...${NC}"
    
    if [ -L "public/storage" ]; then
        pass "Laravel storage link exists"
    else
        warn "Laravel storage link missing (run: php artisan storage:link)"
    fi
    
    if [ -d "bootstrap/cache" ]; then
        pass "Bootstrap cache directory exists"
    else
        warn "Bootstrap cache directory missing"
    fi
fi

# Results Summary
echo ""
echo -e "${PURPLE}📊 Verification Results${NC}"
echo "================================"
echo -e "${GREEN}✅ Passed: $PASSED${NC}"
echo -e "${RED}❌ Failed: $FAILED${NC}"
echo -e "${YELLOW}⚠️ Warnings: $WARNINGS${NC}"

if [ "$FAILED" -eq 0 ]; then
    if [ "$WARNINGS" -eq 0 ]; then
        echo ""
        echo -e "${GREEN}🎉 PERFECT! Your data persistence system is 100% healthy.${NC}"
        echo -e "${CYAN}💡 Ready for production deployments.${NC}"
        exit 0
    else
        echo ""
        echo -e "${YELLOW}✅ GOOD! System is working with minor warnings.${NC}"
        echo -e "${CYAN}💡 Consider addressing warnings above.${NC}"
        exit 0
    fi
else
    echo ""
    echo -e "${RED}🚨 ISSUES DETECTED! Please fix the failed checks above.${NC}"
    echo -e "${CYAN}🛠️ Emergency recovery: bash $SHARED_PATH/emergency-recovery.sh${NC}"
    exit 1
fi