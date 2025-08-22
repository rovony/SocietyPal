#!/usr/bin/env bash
set -euo pipefail

# =============================================================================
# SSH COMMAND preA: Version Compatibility Check
# =============================================================================
# Group: 3
# Name: G3-SSH COMMAND preA: Version Compatibility Check-inDev
# Desc: Checks server versions (PHP, Composer, Node.js) against app requirements
#       to identify potential compatibility issues before deployment. Compares
#       server environment with build environment and app constraints.
# =============================================================================

echo "=== Server Version Compatibility Check ==="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to compare version numbers
version_compare() {
    if [[ $1 == $2 ]]; then
        echo "0"
    elif [[ $1 > $2 ]]; then
        echo "1"
    else
        echo "-1"
    fi
}

# Function to extract version number from string
extract_version() {
    echo "$1" | grep -oE '[0-9]+\.[0-9]+(\.[0-9]+)?' | head -1
}

echo ""
echo "🔍 Checking server environment versions..."

# 1. PHP Version Check
echo ""
echo "${BLUE}📋 PHP Version Analysis:${NC}"
SERVER_PHP_VERSION=$(php -v | head -1 | grep -oE '[0-9]+\.[0-9]+(\.[0-9]+)?' | head -1)
echo "  Server PHP Version: ${GREEN}$SERVER_PHP_VERSION${NC}"

# Check if composer.json exists locally to get constraints
if [ -f "composer.json" ]; then
    # Try to extract PHP constraint from composer.json
    if command -v jq >/dev/null 2>&1; then
        PHP_CONSTRAINT=$(jq -r '.require.php // "Not specified"' composer.json 2>/dev/null)
    else
        # Fallback without jq
        PHP_CONSTRAINT=$(grep -o '"php":\s*"[^"]*"' composer.json 2>/dev/null | cut -d'"' -f4 || echo "Not specified")
    fi
    echo "  App PHP Requirement: ${YELLOW}$PHP_CONSTRAINT${NC}"
    
    # Basic compatibility check
    if [[ "$PHP_CONSTRAINT" == *"^8."* ]] && [[ "$SERVER_PHP_VERSION" == 7.* ]]; then
        echo "  ${RED}❌ CRITICAL: App requires PHP 8.x but server has $SERVER_PHP_VERSION${NC}"
    elif [[ "$PHP_CONSTRAINT" == *"^7."* ]] && [[ "$SERVER_PHP_VERSION" == 8.* ]]; then
        echo "  ${YELLOW}⚠️ WARNING: App designed for PHP 7.x but server has $SERVER_PHP_VERSION${NC}"
    else
        echo "  ${GREEN}✅ PHP version appears compatible${NC}"
    fi
else
    echo "  ${YELLOW}⚠️ No composer.json found - run from Laravel project root${NC}"
fi

# 2. PHP Extensions Check
echo ""
echo "${BLUE}🧩 PHP Extensions Check:${NC}"
required_extensions=("mbstring" "pdo" "pdo_mysql" "openssl" "json" "ctype" "fileinfo" "tokenizer" "xml" "curl" "zip" "bcmath")
missing_extensions=()

for ext in "${required_extensions[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "  ${GREEN}✅ $ext${NC}"
    else
        echo "  ${RED}❌ $ext (missing)${NC}"
        missing_extensions+=("$ext")
    fi
done

if [ ${#missing_extensions[@]} -gt 0 ]; then
    echo "  ${RED}🚨 CRITICAL: Missing extensions: ${missing_extensions[*]}${NC}"
    echo "  ${YELLOW}💡 Contact hosting provider to install missing extensions${NC}"
fi

# 3. Composer Version Check
echo ""
echo "${BLUE}📦 Composer Version Analysis:${NC}"
if command -v composer >/dev/null 2>&1; then
    SERVER_COMPOSER_VERSION=$(composer --version | grep -oE '[0-9]+\.[0-9]+(\.[0-9]+)?' | head -1)
    echo "  Server Composer Version: ${GREEN}$SERVER_COMPOSER_VERSION${NC}"
    
    # Check for Composer 2.x vs 1.x compatibility
    if [[ "$SERVER_COMPOSER_VERSION" == 1.* ]]; then
        echo "  ${YELLOW}⚠️ WARNING: Composer 1.x detected - consider upgrading to 2.x${NC}"
        echo "  ${YELLOW}💡 Some packages may require Composer 2.x${NC}"
    else
        echo "  ${GREEN}✅ Modern Composer version${NC}"
    fi
else
    echo "  ${RED}❌ Composer not found on server${NC}"
    echo "  ${YELLOW}💡 Install Composer or contact hosting provider${NC}"
fi

# 4. Node.js Version Check (if package.json exists)
echo ""
if [ -f "package.json" ]; then
    echo "${BLUE}🟢 Node.js Version Analysis:${NC}"
    if command -v node >/dev/null 2>&1; then
        SERVER_NODE_VERSION=$(node --version | grep -oE '[0-9]+\.[0-9]+(\.[0-9]+)?' | head -1)
        echo "  Server Node.js Version: ${GREEN}$SERVER_NODE_VERSION${NC}"
        
        # Check if package.json specifies engines
        if command -v jq >/dev/null 2>&1; then
            NODE_CONSTRAINT=$(jq -r '.engines.node // "Not specified"' package.json 2>/dev/null)
        else
            # Fallback without jq
            NODE_CONSTRAINT=$(grep -A2 '"engines"' package.json 2>/dev/null | grep -o '"node":\s*"[^"]*"' | cut -d'"' -f4 || echo "Not specified")
        fi
        echo "  App Node.js Requirement: ${YELLOW}$NODE_CONSTRAINT${NC}"
        
        # Basic Node.js compatibility check
        if [[ "$NODE_CONSTRAINT" != "Not specified" ]] && [[ "$NODE_CONSTRAINT" == *">="* ]]; then
            REQUIRED_VERSION=$(echo "$NODE_CONSTRAINT" | grep -oE '[0-9]+')
            SERVER_MAJOR=$(echo "$SERVER_NODE_VERSION" | cut -d'.' -f1)
            if [ "$SERVER_MAJOR" -lt "$REQUIRED_VERSION" ]; then
                echo "  ${RED}❌ CRITICAL: App requires Node.js $REQUIRED_VERSION+ but server has $SERVER_NODE_VERSION${NC}"
            else
                echo "  ${GREEN}✅ Node.js version compatible${NC}"
            fi
        else
            echo "  ${GREEN}✅ Node.js version check passed${NC}"
        fi
        
        # Check npm
        if command -v npm >/dev/null 2>&1; then
            NPM_VERSION=$(npm --version)
            echo "  Server npm Version: ${GREEN}$NPM_VERSION${NC}"
        else
            echo "  ${RED}❌ npm not found${NC}"
        fi
    else
        echo "  ${RED}❌ Node.js not found on server${NC}"
        echo "  ${YELLOW}💡 Install Node.js or contact hosting provider${NC}"
    fi
else
    echo "${BLUE}🟢 Node.js Check:${NC} ${YELLOW}Skipped (no package.json - API-only app)${NC}"
fi

# 5. Memory Limits Check
echo ""
echo "${BLUE}🧠 Memory Configuration Analysis:${NC}"
CLI_MEMORY=$(php -r "echo ini_get('memory_limit');")
echo "  CLI Memory Limit: ${GREEN}$CLI_MEMORY${NC}"

# Check if memory limit is sufficient
if [[ "$CLI_MEMORY" == "-1" ]]; then
    echo "  ${GREEN}✅ Unlimited memory (ideal for deployment commands)${NC}"
elif [[ "$CLI_MEMORY" =~ ^[0-9]+M$ ]]; then
    MEMORY_MB=$(echo "$CLI_MEMORY" | sed 's/M//')
    if [ "$MEMORY_MB" -lt 128 ]; then
        echo "  ${RED}❌ CRITICAL: Memory limit too low ($CLI_MEMORY) - need at least 128M${NC}"
    elif [ "$MEMORY_MB" -lt 256 ]; then
        echo "  ${YELLOW}⚠️ WARNING: Low memory limit ($CLI_MEMORY) - may cause issues${NC}"
    else
        echo "  ${GREEN}✅ Memory limit sufficient for deployment${NC}"
    fi
fi

# 6. Disk Space Check
echo ""
echo "${BLUE}💾 Disk Space Analysis:${NC}"
DISK_USAGE=$(df -h . | tail -1 | awk '{print $5}' | sed 's/%//')
AVAILABLE_SPACE=$(df -h . | tail -1 | awk '{print $4}')
echo "  Current Directory Usage: ${GREEN}$DISK_USAGE%${NC}"
echo "  Available Space: ${GREEN}$AVAILABLE_SPACE${NC}"

if [ "$DISK_USAGE" -gt 90 ]; then
    echo "  ${RED}❌ CRITICAL: Disk usage very high ($DISK_USAGE%)${NC}"
elif [ "$DISK_USAGE" -gt 80 ]; then
    echo "  ${YELLOW}⚠️ WARNING: Disk usage high ($DISK_USAGE%)${NC}"
else
    echo "  ${GREEN}✅ Disk space sufficient${NC}"
fi

# 7. Database Check (basic connectivity)
echo ""
echo "${BLUE}🗄️ Database Connectivity:${NC}"
if [ -f ".env" ]; then
    DB_CONNECTION=$(grep "^DB_CONNECTION=" .env 2>/dev/null | cut -d'=' -f2 || echo "Not found")
    echo "  Database Type: ${GREEN}$DB_CONNECTION${NC}"
    
    # Test basic database connectivity
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" 2>/dev/null | grep -q "successfully"; then
        echo "  ${GREEN}✅ Database connection working${NC}"
    else
        echo "  ${YELLOW}⚠️ Could not verify database connection${NC}"
        echo "  ${YELLOW}💡 Check database credentials and server availability${NC}"
    fi
else
    echo "  ${YELLOW}⚠️ No .env file found${NC}"
fi

# 8. Final Summary
echo ""
echo "======================================="
echo "${BLUE}📊 COMPATIBILITY SUMMARY:${NC}"
echo "======================================="

# Count issues
CRITICAL_ISSUES=0
WARNINGS=0

# Check for critical issues in the output
if grep -q "❌ CRITICAL" <(set +x; exec 2>&1; eval "$(declare -f)"); then
    CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
fi

if [ ${#missing_extensions[@]} -gt 0 ]; then
    CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
fi

echo "Critical Issues: ${RED}$CRITICAL_ISSUES${NC}"
echo "Warnings: ${YELLOW}$WARNINGS${NC}"

if [ $CRITICAL_ISSUES -eq 0 ]; then
    echo ""
    echo "${GREEN}🎉 SERVER COMPATIBILITY: EXCELLENT${NC}"
    echo "${GREEN}✅ Server environment ready for Laravel deployment${NC}"
    echo ""
    echo "${BLUE}Next Steps:${NC}"
    echo "  1. Run your build commands"
    echo "  2. Deploy application files"
    echo "  3. Run deployment commands"
else
    echo ""
    echo "${RED}🚨 SERVER COMPATIBILITY: ISSUES DETECTED${NC}"
    echo "${RED}❌ Critical issues must be resolved before deployment${NC}"
    echo ""
    echo "${BLUE}Required Actions:${NC}"
    echo "  1. Fix critical issues listed above"
    echo "  2. Re-run this compatibility check"
    echo "  3. Contact hosting provider if needed"
fi

echo ""
echo "✅ Version compatibility check completed!"



