#!/bin/bash

# Script: comprehensive-env-check.sh
# Purpose: Comprehensive Laravel environment analysis
# Version: 2.0
# Section: A - Project Setup
# Location: 🟢 Local Machine

set -euo pipefail

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/load-variables.sh"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Comprehensive Laravel Environment Analysis           ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Create analysis report
REPORT="$DEPLOY_WORKSPACE/Logs/env-analysis-$(date +%Y%m%d-%H%M%S).md"
mkdir -p "$(dirname "$REPORT")"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "Starting comprehensive environment analysis..."

# Initialize report
cat > "$REPORT" << EOF
# Environment Analysis Report

**Generated:** $(date)
**Project:** $PROJECT_NAME
**Analysis Type:** Comprehensive Laravel Environment Check

---

EOF

# 1. PHP Analysis
log "Analyzing PHP configuration..."
echo "## PHP Configuration" >> "$REPORT"

# PHP Version
echo "### Version Information" >> "$REPORT"
PHP_CURRENT=$(php -v | head -n1)
echo "- **Current Version:** $PHP_CURRENT" >> "$REPORT"

if [ -f "$PATH_LOCAL_MACHINE/composer.json" ]; then
    PHP_REQUIRED=$(grep -oP '"php":\s*"[^"]*"' "$PATH_LOCAL_MACHINE/composer.json" 2>/dev/null | cut -d'"' -f4 || echo "Not specified")
    echo "- **Required Version:** $PHP_REQUIRED" >> "$REPORT"
fi

# Check PHP extensions
echo "### Required Extensions" >> "$REPORT"
REQUIRED_EXTENSIONS=(
    "bcmath" "ctype" "curl" "dom" "fileinfo" 
    "json" "mbstring" "openssl" "pcre" "pdo"
    "tokenizer" "xml" "zip" "gd" "intl"
)

MISSING_EXTENSIONS=()
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -qi "^$ext$"; then
        echo "- ✅ **$ext** - Available" >> "$REPORT"
    else
        MISSING_EXTENSIONS+=("$ext")
        echo "- ❌ **$ext** - **MISSING**" >> "$REPORT"
    fi
done

# Check disabled functions
echo "### Function Availability" >> "$REPORT"
REQUIRED_FUNCTIONS=("exec" "shell_exec" "proc_open" "symlink")
DISABLED_FUNCTIONS=()
for func in "${REQUIRED_FUNCTIONS[@]}"; do
    if php -r "if(function_exists('$func')) { exit(0); } else { exit(1); }" 2>/dev/null; then
        echo "- ✅ **$func()** - Enabled" >> "$REPORT"
    else
        DISABLED_FUNCTIONS+=("$func")
        echo "- ❌ **$func()** - **DISABLED**" >> "$REPORT"
    fi
done

# 2. Composer Analysis
log "Analyzing Composer configuration..."
echo "" >> "$REPORT"
echo "## Composer Configuration" >> "$REPORT"

COMPOSER_CURRENT="unknown"
if command -v composer >/dev/null 2>&1; then
    COMPOSER_CURRENT=$(composer --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+' || echo "unknown")
    echo "- **Current Version:** $COMPOSER_CURRENT" >> "$REPORT"
    
    # Check Composer major version
    COMPOSER_MAJOR=$(echo "$COMPOSER_CURRENT" | cut -d'.' -f1)
    echo "- **Major Version:** $COMPOSER_MAJOR" >> "$REPORT"
else
    echo "- **Status:** ❌ Composer not installed" >> "$REPORT"
fi

# Check lock file version compatibility
if [ -f "$PATH_LOCAL_MACHINE/composer.lock" ]; then
    LOCK_VERSION=$(grep -m1 '"plugin-api-version"' "$PATH_LOCAL_MACHINE/composer.lock" | cut -d'"' -f4 2>/dev/null || echo "unknown")
    echo "- **Lock File Plugin API:** $LOCK_VERSION" >> "$REPORT"
    
    if [[ "$LOCK_VERSION" == 2.* ]] && [[ "$COMPOSER_MAJOR" == "1" ]]; then
        echo "- ⚠️ **CRITICAL WARNING:** Composer 2 required but version 1 detected!" >> "$REPORT"
    fi
fi

# 3. Node.js Analysis (if frontend exists)
if [ -f "$PATH_LOCAL_MACHINE/package.json" ]; then
    log "Analyzing Node.js configuration..."
    echo "" >> "$REPORT"
    echo "## Node.js Configuration" >> "$REPORT"
    
    if command -v node >/dev/null 2>&1; then
        NODE_CURRENT=$(node -v 2>/dev/null)
        echo "- **Node Version:** $NODE_CURRENT" >> "$REPORT"
    else
        echo "- **Node Status:** ❌ Not installed" >> "$REPORT"
    fi
    
    if command -v npm >/dev/null 2>&1; then
        NPM_CURRENT=$(npm -v 2>/dev/null)
        echo "- **NPM Version:** $NPM_CURRENT" >> "$REPORT"
    else
        echo "- **NPM Status:** ❌ Not installed" >> "$REPORT"
    fi
    
    # Check for build scripts
    echo "### Build Scripts Analysis" >> "$REPORT"
    cd "$PATH_LOCAL_MACHINE"
    
    if grep -q '"build"' package.json 2>/dev/null; then
        echo "- ✅ **build** script found" >> "$REPORT"
    else
        echo "- ⚠️ **build** script missing" >> "$REPORT"
    fi
    
    if grep -q '"production"' package.json 2>/dev/null; then
        echo "- ✅ **production** script found" >> "$REPORT"
    fi
    
    if grep -q '"dev"' package.json 2>/dev/null; then
        echo "- ✅ **dev** script found" >> "$REPORT"
    fi
    
    # Detect bundler type
    if grep -q '"vite"' package.json 2>/dev/null; then
        echo "- 📦 **Bundler Detected:** Vite" >> "$REPORT"
    elif grep -q '"laravel-mix"' package.json 2>/dev/null; then
        echo "- 📦 **Bundler Detected:** Laravel Mix" >> "$REPORT"
    elif grep -q '"webpack"' package.json 2>/dev/null; then
        echo "- 📦 **Bundler Detected:** Webpack" >> "$REPORT"
    else
        echo "- 📦 **Bundler:** Unknown or none detected" >> "$REPORT"
    fi
fi

# 4. Laravel-specific checks
log "Analyzing Laravel configuration..."
echo "" >> "$REPORT"
echo "## Laravel Configuration" >> "$REPORT"

cd "$PATH_LOCAL_MACHINE"

if [ -f "artisan" ]; then
    LARAVEL_VERSION=$(php artisan --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+' || echo "Unknown")
    echo "- **Laravel Version:** $LARAVEL_VERSION" >> "$REPORT"
    
    # Test basic artisan functionality
    if php artisan list >/dev/null 2>&1; then
        echo "- ✅ **Artisan Commands:** Functional" >> "$REPORT"
    else
        echo "- ❌ **Artisan Commands:** Not functional" >> "$REPORT"
    fi
else
    echo "- ❌ **Laravel Status:** artisan file not found" >> "$REPORT"
fi

# Check for common Laravel packages
echo "### Detected Laravel Packages" >> "$REPORT"
LARAVEL_PACKAGES=(
    "config/telescope.php:📡 Laravel Telescope"
    "config/debugbar.php:🔍 Laravel Debugbar"
    "config/horizon.php:🎯 Laravel Horizon"
    "config/sanctum.php:🔐 Laravel Sanctum"
    "config/jetstream.php:✈️ Laravel Jetstream"
    "config/livewire.php:⚡ Livewire"
    "config/inertia.php:🔄 Inertia.js"
    "config/fortify.php:🏰 Laravel Fortify"
    "config/passport.php:📝 Laravel Passport"
)

for package_check in "${LARAVEL_PACKAGES[@]}"; do
    config_file="${package_check%%:*}"
    package_name="${package_check#*:}"
    
    if [ -f "$config_file" ]; then
        echo "- $package_name" >> "$REPORT"
    fi
done

# 5. Database Configuration Check
echo "### Database Configuration" >> "$REPORT"
if [ -f ".env" ]; then
    DB_CONNECTION=$(grep -E "^DB_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' || echo "Not configured")
    echo "- **Database Driver:** $DB_CONNECTION" >> "$REPORT"
    
    if [ "$DB_CONNECTION" != "Not configured" ]; then
        if php artisan migrate:status >/dev/null 2>&1; then
            echo "- ✅ **Database Connection:** Functional" >> "$REPORT"
        else
            echo "- ⚠️ **Database Connection:** Cannot verify" >> "$REPORT"
        fi
    fi
else
    echo "- ⚠️ **.env file not found**" >> "$REPORT"
fi

# 6. Generate Action Items
echo "" >> "$REPORT"
echo "## ⚠️ Action Items Required" >> "$REPORT"

ACTION_ITEMS=()

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    echo "### Missing PHP Extensions" >> "$REPORT"
    echo "Install the following PHP extensions:" >> "$REPORT"
    echo '```bash' >> "$REPORT"
    for ext in "${MISSING_EXTENSIONS[@]}"; do
        echo "# Ubuntu/Debian:" >> "$REPORT"
        echo "sudo apt-get install php${PHP_VERSION}-${ext}" >> "$REPORT"
        echo "# macOS with Homebrew:" >> "$REPORT"
        echo "# Extensions usually included with php installation" >> "$REPORT"
    done
    echo '```' >> "$REPORT"
    ACTION_ITEMS+=("Install ${#MISSING_EXTENSIONS[@]} missing PHP extensions")
fi

if [ ${#DISABLED_FUNCTIONS[@]} -gt 0 ]; then
    echo "### Disabled PHP Functions" >> "$REPORT"
    echo "The following required functions are disabled:" >> "$REPORT"
    for func in "${DISABLED_FUNCTIONS[@]}"; do
        echo "- $func()" >> "$REPORT"
    done
    echo "**Note:** Contact hosting provider to enable these functions or configure alternative deployment methods." >> "$REPORT"
    ACTION_ITEMS+=("Enable ${#DISABLED_FUNCTIONS[@]} disabled PHP functions")
fi

if [[ "$COMPOSER_MAJOR" == "1" ]] && [[ "$LOCK_VERSION" == 2.* ]]; then
    echo "### Upgrade Composer" >> "$REPORT"
    echo "Composer 2 is required but version 1 is installed:" >> "$REPORT"
    echo '```bash' >> "$REPORT"
    echo 'composer self-update --2' >> "$REPORT"
    echo '```' >> "$REPORT"
    ACTION_ITEMS+=("Upgrade Composer to version 2")
fi

if [ ! -f "$PATH_LOCAL_MACHINE/.env" ]; then
    echo "### Environment Configuration" >> "$REPORT"
    echo "Create .env file from .env.example and configure:" >> "$REPORT"
    echo '```bash' >> "$REPORT"
    echo 'cp .env.example .env' >> "$REPORT"
    echo 'php artisan key:generate' >> "$REPORT"
    echo '```' >> "$REPORT"
    ACTION_ITEMS+=("Configure .env file")
fi

# 7. Summary and Recommendations
echo "" >> "$REPORT"
echo "## 📊 Analysis Summary" >> "$REPORT"

TOTAL_ISSUES=$((${#MISSING_EXTENSIONS[@]} + ${#DISABLED_FUNCTIONS[@]}))
if [ "$COMPOSER_MAJOR" = "1" ] && [[ "$LOCK_VERSION" == 2.* ]]; then
    ((TOTAL_ISSUES++))
fi

if [ $TOTAL_ISSUES -eq 0 ]; then
    echo "### ✅ Environment Status: READY" >> "$REPORT"
    echo "All requirements are met for Laravel deployment." >> "$REPORT"
    ENVIRONMENT_STATUS="READY"
else
    echo "### ⚠️ Environment Status: NEEDS ATTENTION" >> "$REPORT"
    echo "**Total Issues Found:** $TOTAL_ISSUES" >> "$REPORT"
    echo "" >> "$REPORT"
    echo "**Required Actions:**" >> "$REPORT"
    for item in "${ACTION_ITEMS[@]}"; do
        echo "- $item" >> "$REPORT"
    done
    ENVIRONMENT_STATUS="NEEDS_ATTENTION"
fi

echo "### Next Steps" >> "$REPORT"
echo "1. Address all action items listed above" >> "$REPORT"
echo "2. Re-run this analysis after making changes" >> "$REPORT"
echo "3. Proceed to dependency analysis once environment is ready" >> "$REPORT"

# Display results
log "Environment analysis completed"
echo ""
echo "📋 Analysis Summary:"
echo "  - PHP Extensions Missing: ${#MISSING_EXTENSIONS[@]}"
echo "  - PHP Functions Disabled: ${#DISABLED_FUNCTIONS[@]}"
echo "  - Total Issues: $TOTAL_ISSUES"
echo "  - Environment Status: $ENVIRONMENT_STATUS"
echo ""
echo "📁 Full report saved to: $REPORT"

# Display critical issues
if [ $TOTAL_ISSUES -gt 0 ]; then
    echo ""
    echo "⚠️ CRITICAL ISSUES DETECTED:"
    for item in "${ACTION_ITEMS[@]}"; do
        echo "  - $item"
    done
    echo ""
    echo "🔧 Please address these issues before proceeding to the next step."
    exit 1
else
    echo ""
    echo "✅ Environment analysis passed - ready for dependency analysis!"
fi