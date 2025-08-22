#!/bin/bash
set -e

# Phase-A-Prep: Server Environment Validation & Setup
# Purpose: Validate server environment, provide clear instructions for fixes
# Run: FIRST - before any other scripts (Pre-Deployment Commands - Before Upload)
# Action: DETECT, REPORT, and INSTRUCT - ensures server readiness
# Version 3.0 - PRODUCTION READY (Enhanced with %path% variable only)

echo "=== Phase-A-Prep: Server Environment Validation & Setup ==="

# ENHANCED: Define all variables relative to %path% (only available DeployHQ variable)
# %path% = Base server path we're deploying to (e.g., /home/user/domains/site.com/deploy)
DEPLOY_PATH="%path%"
SHARED_PATH="$DEPLOY_PATH/shared"
CURRENT_PATH="$DEPLOY_PATH/current"
DOMAIN_ROOT=$(dirname "$DEPLOY_PATH")

echo "🔧 Path Variables (derived from %path%):"
echo "   Base Deploy Path: $DEPLOY_PATH"
echo "   Shared Path: $SHARED_PATH"
echo "   Domain Root: $DOMAIN_ROOT"

# Initialize variables using enhanced path detection
PREP_REPORT="$DOMAIN_ROOT/deployment-prep-report.md"
PHASE_A_ISSUES=0
RECOMMENDATIONS_PROVIDED=0

echo "? Starting server environment validation..."
echo "? Prep Report: $PREP_REPORT"

# Initialize prep report
cat > "$PREP_REPORT" << EOF
# ? Deployment Preparation Report
**Domain:** $(basename "$DOMAIN_ROOT")  
**Started:** $(date '+%Y-%m-%d %H:%M:%S')  
**Status:** ? In Progress

---

## ? Phase A: Server Environment Validation
**Focus:** Server readiness, hosting panel instructions, manual recommendations
EOF

# A-Prep-01: Critical PHP Extensions Check (Enhanced from existing scripts)
echo "=== Critical PHP Extensions Analysis ==="

# Focus only on extensions that cause immediate failures
CRITICAL_EXTENSIONS=("pdo" "pdo_mysql" "openssl" "mbstring" "curl")
CRITICAL_MISSING=()
NICE_TO_HAVE_MISSING=()

# Check critical extensions
for ext in "${CRITICAL_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^$ext$"; then
        CRITICAL_MISSING+=("$ext")
        PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
        echo "❌ CRITICAL: $ext extension missing"
    else
        echo "✅ $ext extension available"
    fi
done

# Check nice-to-have extensions (won't block deployment)
NICE_TO_HAVE=("zip" "bcmath" "fileinfo" "xml" "ctype" "json" "tokenizer")
for ext in "${NICE_TO_HAVE[@]}"; do
    if ! php -m | grep -qi "^$ext$"; then
        NICE_TO_HAVE_MISSING+=("$ext")
        echo "⚠️ RECOMMENDED: $ext extension missing (optional)"
    fi
done

# A-Prep-02: Smart Composer 2 Detection & Recommendations
echo "=== Smart Composer 2 Detection & Recommendations ==="

COMPOSER_STATUS="✅ OPTIMAL"
COMPOSER_RECOMMENDATION=""

# Smart Composer 2 Detection Logic
if command -v composer2 &> /dev/null; then
    COMPOSER2_VERSION=$(composer2 --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2)
    echo "✅ OPTIMAL: composer2 available server-wide ($COMPOSER2_VERSION)"
    COMPOSER_STATUS="✅ OPTIMAL (server-wide)"
    COMPOSER_RECOMMENDATION="Use existing: \`composer2 install --no-dev --optimize-autoloader\`"
    
elif composer --version 2>/dev/null | grep -q "version 2\."; then
    COMPOSER_VERSION=$(composer --version | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2)
    echo "✅ GOOD: composer (v2) available server-wide ($COMPOSER_VERSION)"
    COMPOSER_STATUS="✅ GOOD (server-wide v2)"
    COMPOSER_RECOMMENDATION="Use existing: \`composer install --no-dev --optimize-autoloader\`"
    
elif command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2 || echo "unknown")
    echo "⚠️ LEGACY: composer v1.x detected ($COMPOSER_VERSION)"
    echo "ℹ️ Laravel 9+ requires Composer 2.x for optimal performance"
    COMPOSER_STATUS="⚠️ LEGACY VERSION"
    COMPOSER_RECOMMENDATION="**Upgrade needed:** Install Composer 2 per-domain"
    PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
    
else
    echo "❌ MISSING: No Composer installation found"
    COMPOSER_STATUS="❌ NOT FOUND"
    COMPOSER_RECOMMENDATION="**Installation required:** Install Composer 2 per-domain"
    PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
fi

# Domain-specific installation path recommendation
if [ "$COMPOSER_STATUS" = "⚠️ LEGACY VERSION" ] || [ "$COMPOSER_STATUS" = "❌ NOT FOUND" ]; then
    echo "? Recommended installation location: $DOMAIN_ROOT/composer2"
    echo "ℹ️ This will be accessible from all releases and deployments"
fi

# A-Prep-03: Enhanced Shared Directory Structure Analysis
echo "=== Enhanced Shared Directory Structure Analysis ==="

SHARED_BASE="$SHARED_PATH"
SHARED_STRUCTURE_STATUS="✅ OPTIMAL"

# Check for additional shared directories that might be beneficial
RECOMMENDED_DIRS=(
    "logs/apache2"
    "logs/php" 
    "cache/sessions"
    "cache/views"
    "public/.well-known/acme-challenge"
    "storage/debugbar"
    "storage/clockwork"
)

MISSING_OPTIONAL_DIRS=()
for dir in "${RECOMMENDED_DIRS[@]}"; do
    FULL_PATH="$SHARED_BASE/$dir"
    if [ -d "$FULL_PATH" ]; then
        echo "✅ Optional: shared/$dir (present)"
    else
        echo "ℹ️ Optional: shared/$dir (could be added)"
        MISSING_OPTIONAL_DIRS+=("$dir")
    fi
done

if [ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ]; then
    echo "? ${#MISSING_OPTIONAL_DIRS[@]} optional directories could be created for enhanced functionality"
    SHARED_STRUCTURE_STATUS="✅ GOOD (optional dirs available)"
else
    echo "✅ All optional shared directories already present"
fi

# A-Prep-04: PHP Configuration Analysis
echo "=== PHP Configuration Analysis ==="

PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2 | cut -d'-' -f1)
echo "? PHP Version: $PHP_VERSION"

# Check PHP version compatibility with modern Laravel
PHP_STATUS="✅ EXCELLENT"
if [[ "$PHP_VERSION" =~ ^8\.[2-3] ]]; then
    echo "✅ PHP $PHP_VERSION - Excellent for Laravel 10+"
elif [[ "$PHP_VERSION" =~ ^8\.[0-1] ]]; then
    echo "✅ PHP $PHP_VERSION - Good for Laravel 9+"
    PHP_STATUS="✅ GOOD"
elif [[ "$PHP_VERSION" =~ ^7\. ]]; then
    echo "⚠️ PHP $PHP_VERSION - Legacy version"
    PHP_STATUS="⚠️ LEGACY"
    PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
else
    echo "❌ PHP $PHP_VERSION - Unsupported"
    PHP_STATUS="❌ UNSUPPORTED" 
    PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
fi

# Check critical PHP settings
MEMORY_LIMIT=$(php -r "echo ini_get('memory_limit');")
MAX_EXECUTION_TIME=$(php -r "echo ini_get('max_execution_time');")
echo "? Memory Limit: $MEMORY_LIMIT"
echo "? Max Execution Time: ${MAX_EXECUTION_TIME}s"

PHP_CONFIG_STATUS="✅ OPTIMAL"
if [[ "$MEMORY_LIMIT" =~ ^[0-9]+M$ ]]; then
    MEMORY_MB=$(echo "$MEMORY_LIMIT" | sed 's/M//')
    if [ "$MEMORY_MB" -lt 256 ]; then
        echo "⚠️ Memory limit may be low for Laravel applications"
        PHP_CONFIG_STATUS="⚠️ LOW MEMORY"
        PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
    fi
fi

# A-Prep-05: Server Capabilities Check
echo "=== Server Capabilities Analysis ==="

# Check for essential server tools
SERVER_TOOLS_STATUS="✅ COMPLETE"
MISSING_TOOLS=()

# Essential tools for Laravel deployment
TOOLS=("curl" "git" "mysql")
for tool in "${TOOLS[@]}"; do
    if command -v "$tool" &> /dev/null; then
        VERSION=$(eval "$tool --version 2>/dev/null | head -1" || echo "available")
        echo "✅ $tool: $VERSION"
    else
        echo "❌ $tool: Missing"
        MISSING_TOOLS+=("$tool")
        PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1))
    fi
done

if [ ${#MISSING_TOOLS[@]} -gt 0 ]; then
    SERVER_TOOLS_STATUS="❌ INCOMPLETE"
fi

# Check Node.js (optional for frontend builds)
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    echo "✅ Node.js: $NODE_VERSION (for frontend builds)"
else
    echo "ℹ️ Node.js: Not available (only needed for frontend builds)"
fi

# Update prep report with Phase A results
cat >> "$PREP_REPORT" << EOF

### ? Critical Extensions Status
$([ ${#CRITICAL_MISSING[@]} -eq 0 ] && echo "✅ **All critical extensions present**" || echo "❌ **Missing critical extensions:** ${CRITICAL_MISSING[*]}")
$([ ${#NICE_TO_HAVE_MISSING[@]} -eq 0 ] && echo "✅ **All recommended extensions present**" || echo "⚠️ **Missing recommended:** ${NICE_TO_HAVE_MISSING[*]} (optional)")

### ?️ Composer Environment  
**Status:** $COMPOSER_STATUS  
**Recommendation:** $COMPOSER_RECOMMENDATION

### ? PHP Configuration
**Version:** $PHP_VERSION ($PHP_STATUS)  
**Memory:** $MEMORY_LIMIT  
**Config:** $PHP_CONFIG_STATUS

### ?️ Server Tools
**Status:** $SERVER_TOOLS_STATUS
$([ ${#MISSING_TOOLS[@]} -gt 0 ] && echo "**Missing:** ${MISSING_TOOLS[*]}" || echo "**Available:** curl, git, mysql ✅")

### ? Shared Infrastructure  
**Structure:** $SHARED_STRUCTURE_STATUS  
$([ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ] && echo "**Optional directories available:** ${#MISSING_OPTIONAL_DIRS[@]} could be added" || echo "**All recommended directories present**")

EOF

# Generate specific action items based on findings
cat >> "$PREP_REPORT" << EOF

### ? Action Items Required:
EOF

ACTION_ITEMS=0

# Critical extensions action items
if [ ${#CRITICAL_MISSING[@]} -gt 0 ]; then
    cat >> "$PREP_REPORT" << EOF
**? CRITICAL - Enable PHP Extensions:**
1. **Login to your hosting control panel**
2. **Navigate to:** PHP Settings → Extensions (or PHP Selector)
3. **Enable these extensions:** ${CRITICAL_MISSING[*]}
4. **Save changes** and wait 1-2 minutes for activation
5. **Verify with:** \`php -m | grep -E '(${CRITICAL_MISSING[*]// /|})'\`

EOF
    ACTION_ITEMS=$((ACTION_ITEMS + 1))
fi

# Composer installation instructions
if [ "$COMPOSER_STATUS" = "⚠️ LEGACY VERSION" ] || [ "$COMPOSER_STATUS" = "❌ NOT FOUND" ]; then
    cat >> "$PREP_REPORT" << EOF
**? REQUIRED - Install Composer 2 (Per-Domain):**
1. **Navigate to domain root:** \`cd $DOMAIN_ROOT/\`
2. **Download Composer 2:** \`curl -sS https://getcomposer.org/installer | php\`
3. **Rename for clarity:** \`mv composer.phar composer2\`
4. **Make executable:** \`chmod +x composer2\`
5. **Test installation:** \`./composer2 --version\`
6. **Usage from releases:** \`../../composer2 install --no-dev --optimize-autoloader\`

**Alternative (if you have sudo access):**
1. **System-wide install:** \`sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer2\`

EOF
    ACTION_ITEMS=$((ACTION_ITEMS + 1))
fi

# Server tools action items
if [ ${#MISSING_TOOLS[@]} -gt 0 ]; then
    cat >> "$PREP_REPORT" << EOF
**?️ REQUIRED - Install Server Tools:**
- **Missing tools:** ${MISSING_TOOLS[*]}
- **Contact your hosting provider** to install these essential tools
- **Alternative:** Some hosting providers have these in non-standard locations

EOF
    ACTION_ITEMS=$((ACTION_ITEMS + 1))
fi

# PHP upgrade recommendation
if [ "$PHP_STATUS" = "⚠️ LEGACY" ] || [ "$PHP_STATUS" = "❌ UNSUPPORTED" ]; then
    cat >> "$PREP_REPORT" << EOF
**? RECOMMENDED - Upgrade PHP:**
- **Current:** PHP $PHP_VERSION 
- **Recommended:** PHP 8.1+ for Laravel 9+, PHP 8.2+ for Laravel 10+
- **Action:** Update via hosting control panel → PHP Settings

EOF
    ACTION_ITEMS=$((ACTION_ITEMS + 1))
fi

# Optional shared directories
if [ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ]; then
    cat >> "$PREP_REPORT" << EOF
**? OPTIONAL - Enhanced Shared Directories:**
Create additional shared directories for enhanced functionality:
\`\`\`bash
cd $SHARED_PATH
$(for dir in "${MISSING_OPTIONAL_DIRS[@]}"; do echo "mkdir -p $dir && chmod 775 $dir"; done)
\`\`\`

EOF
    ACTION_ITEMS=$((ACTION_ITEMS + 1))
fi

# Summary status
PHASE_A_STATUS="✅ READY"
if [ $PHASE_A_ISSUES -gt 0 ]; then
    PHASE_A_STATUS="❌ NEEDS ATTENTION ($PHASE_A_ISSUES issues)"
fi

if [ $ACTION_ITEMS -eq 0 ]; then
    cat >> "$PREP_REPORT" << EOF
✅ **No critical actions required** - server environment is ready!

EOF
fi

cat >> "$PREP_REPORT" << EOF

**Phase A Status:** $PHASE_A_STATUS  
**Recommendations Provided:** $ACTION_ITEMS  
**Manual Actions Required:** $ACTION_ITEMS

EOF

# Display summary
echo ""
echo "=== Phase A Summary ==="
echo "? Server Status: $PHASE_A_STATUS"
echo "? Recommendations Provided: $ACTION_ITEMS"
echo "? Manual Actions Required: $ACTION_ITEMS"
echo "? Prep Report: $PREP_REPORT"

if [ $PHASE_A_ISSUES -eq 0 ]; then
    echo "✅ Server environment is ready for Laravel deployment!"
    echo "? Proceeding to Phase A deployment commands..."
else
    echo "⚠️ $PHASE_A_ISSUES server issues detected"
    echo "? Check prep report for specific action items"
    echo "ℹ️ Some issues can be resolved during deployment, others need hosting provider assistance"
fi

echo "✅ Phase-A-Prep completed successfully"

# Log results for deployment history
if [ -d "$SHARED_PATH" ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Phase-A-Prep: Status=$PHASE_A_STATUS | Issues=$PHASE_A_ISSUES | Recommendations=$ACTION_ITEMS" >> "$SHARED_PATH/prep-history.log"
fi

# Exit successfully (don't block deployment even if issues found)
exit 0