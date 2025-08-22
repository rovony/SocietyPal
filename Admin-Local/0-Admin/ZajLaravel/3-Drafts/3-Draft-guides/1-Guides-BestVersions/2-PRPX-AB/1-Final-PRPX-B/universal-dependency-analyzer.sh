#!/bin/bash

# Script: universal-dependency-analyzer.sh
# Purpose: Universal Laravel dependency analyzer with pattern-based detection
# Version: 2.0
# Section: A - Project Setup
# Location: 🟢 Local Machine

set -euo pipefail

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/load-variables.sh"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Universal Laravel Dependency Analyzer                ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Change to project directory
cd "$PATH_LOCAL_MACHINE"

# Create comprehensive report
REPORT="$DEPLOY_WORKSPACE/Logs/dependency-analysis-$(date +%Y%m%d-%H%M%S).md"
mkdir -p "$(dirname "$REPORT")"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "Starting universal dependency analysis..."

# Initialize report
cat > "$REPORT" << EOF
# Universal Dependency Analysis Report

**Generated:** $(date)
**Project:** $PROJECT_NAME
**Analysis Type:** Universal Laravel Dependency Analysis

---

EOF

# Track packages that need to be moved
declare -a MOVE_TO_PROD
declare -a SUSPICIOUS_PACKAGES
declare -a ANALYSIS_WARNINGS

# Define packages and their usage patterns (expanded from Claude 4)
declare -A PACKAGE_PATTERNS=(
    ["fakerphp/faker"]="Faker\\\\Factory|faker\\(\\)|fake\\(\\)|\\$faker"
    ["laravel/telescope"]="TelescopeServiceProvider|telescope|Telescope"
    ["barryvdh/laravel-debugbar"]="DebugbarServiceProvider|debugbar|Debugbar"
    ["laravel/dusk"]="DuskServiceProvider|dusk|Browser::"
    ["nunomaduro/collision"]="collision|Collision"
    ["pestphp/pest"]="pest|Pest\\\\|test\\(|it\\("
    ["phpunit/phpunit"]="PHPUnit|TestCase|extends.*TestCase"
    ["mockery/mockery"]="Mockery|\\$mock|mock\\("
    ["laravel/sail"]="sail|Sail"
    ["laravel/pint"]="pint|Pint"
    ["spatie/laravel-ignition"]="ignition|Ignition"
    ["barryvdh/laravel-ide-helper"]="ide-helper|IdeHelper"
    ["laravel/tinker"]="tinker|Tinker"
    ["spatie/laravel-ray"]="ray\\(|Ray"
)

log "Analyzing dev dependencies usage patterns..."

# 1. Analyze common dev packages that might be needed in production
echo "## Dev Dependencies Analysis" >> "$REPORT"
echo "### Pattern-based Detection Results" >> "$REPORT"

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "❌ **ERROR:** composer.json not found in $PATH_LOCAL_MACHINE" >> "$REPORT"
    log "ERROR: composer.json not found"
    exit 1
fi

# Parse require-dev section
DEV_DEPS=()
if jq -e '.["require-dev"]' composer.json >/dev/null 2>&1; then
    while IFS= read -r package; do
        DEV_DEPS+=("$package")
    done < <(jq -r '.["require-dev"] | keys[]' composer.json 2>/dev/null || true)
fi

log "Found ${#DEV_DEPS[@]} packages in require-dev section"

# Check each pattern
for package in "${!PACKAGE_PATTERNS[@]}"; do
    pattern="${PACKAGE_PATTERNS[$package]}"
    
    # Check if package is in require-dev
    package_in_dev=false
    for dev_pkg in "${DEV_DEPS[@]}"; do
        if [[ "$dev_pkg" == "$package" ]]; then
            package_in_dev=true
            break
        fi
    done
    
    if [ "$package_in_dev" = true ]; then
        echo "" >> "$REPORT"
        echo "#### 📦 Analyzing: \`$package\` (in require-dev)" >> "$REPORT"
        
        # Check usage in production code paths
        USAGE_FOUND=false
        USAGE_LOCATIONS=()
        
        # Check in app directory
        if find app/ -name "*.php" -type f 2>/dev/null | head -20 | xargs grep -l -E "$pattern" 2>/dev/null | grep -v test; then
            echo "⚠️ **Used in app/ directory**" >> "$REPORT"
            USAGE_LOCATIONS+=("app/")
            USAGE_FOUND=true
        fi
        
        # Check in database directory
        if find database/ -name "*.php" -type f 2>/dev/null | head -20 | xargs grep -l -E "$pattern" 2>/dev/null | grep -v test; then
            echo "⚠️ **Used in database/ directory**" >> "$REPORT"
            USAGE_LOCATIONS+=("database/")
            USAGE_FOUND=true
        fi
        
        # Check in config directory
        if find config/ -name "*.php" -type f 2>/dev/null | head -20 | xargs grep -l -E "$pattern" 2>/dev/null | grep -v test; then
            echo "⚠️ **Used in config/ directory**" >> "$REPORT"
            USAGE_LOCATIONS+=("config/")
            USAGE_FOUND=true
        fi
        
        # Check in routes directory
        if find routes/ -name "*.php" -type f 2>/dev/null | head -20 | xargs grep -l -E "$pattern" 2>/dev/null | grep -v test; then
            echo "⚠️ **Used in routes/ directory**" >> "$REPORT"
            USAGE_LOCATIONS+=("routes/")
            USAGE_FOUND=true
        fi
        
        # Check service providers registration
        if grep -r "$package" config/app.php bootstrap/providers.php 2>/dev/null | grep -v "//"; then
            echo "⚠️ **Registered in service providers**" >> "$REPORT"
            USAGE_LOCATIONS+=("service-providers")
            USAGE_FOUND=true
        fi
        
        # Special check for Faker in seeders (most common issue)
        if [[ "$package" == "fakerphp/faker" ]]; then
            if find database/seeders/ -name "*.php" -type f 2>/dev/null | xargs grep -l -E "$pattern" 2>/dev/null; then
                echo "🔍 **Special Detection:** Faker usage in seeders detected" >> "$REPORT"
                USAGE_FOUND=true
            fi
        fi
        
        if [ "$USAGE_FOUND" = true ]; then
            MOVE_TO_PROD+=("$package")
            echo "❌ **ACTION REQUIRED:** Move \`$package\` to production dependencies" >> "$REPORT"
            echo "**Usage detected in:** ${USAGE_LOCATIONS[*]}" >> "$REPORT"
        else
            echo "✅ **Status:** Not used in production code paths" >> "$REPORT"
        fi
    fi
done

# 2. Check for auto-discovery packages
echo "" >> "$REPORT"
echo "## Laravel Auto-Discovery Analysis" >> "$REPORT"

if [ -f "composer.lock" ]; then
    echo "### Packages with Laravel Auto-Discovery Features" >> "$REPORT"
    
    # Check for packages with Laravel extra section
    if command -v jq >/dev/null 2>&1; then
        AUTO_DISCOVERY_PACKAGES=()
        while IFS= read -r pkg; do
            if [[ -n "$pkg" ]] && [[ "$pkg" != "null" ]]; then
                AUTO_DISCOVERY_PACKAGES+=("$pkg")
                
                # Check if it's in require-dev
                package_in_dev=false
                for dev_pkg in "${DEV_DEPS[@]}"; do
                    if [[ "$dev_pkg" == "$pkg" ]]; then
                        package_in_dev=true
                        break
                    fi
                done
                
                if [ "$package_in_dev" = true ]; then
                    echo "⚠️ **$pkg** - Has auto-discovery but in require-dev" >> "$REPORT"
                    SUSPICIOUS_PACKAGES+=("$pkg")
                else
                    echo "✅ **$pkg** - Properly in production dependencies" >> "$REPORT"
                fi
            fi
        done < <(jq -r '.packages[]? | select(.extra.laravel != null) | .name' composer.lock 2>/dev/null || true)
        
        if [ ${#AUTO_DISCOVERY_PACKAGES[@]} -eq 0 ]; then
            echo "No packages with Laravel auto-discovery detected." >> "$REPORT"
        fi
    else
        echo "⚠️ jq not available - cannot analyze auto-discovery packages" >> "$REPORT"
    fi
else
    echo "⚠️ composer.lock not found - cannot analyze auto-discovery" >> "$REPORT"
fi

# 3. Environment-specific package analysis
echo "" >> "$REPORT"
echo "## Environment-Specific Package Analysis" >> "$REPORT"

# Check for Telescope
if [ -f "config/telescope.php" ]; then
    echo "### Laravel Telescope Configuration" >> "$REPORT"
    echo "Telescope detected. Ensure proper production configuration:" >> "$REPORT"
    echo '```php' >> "$REPORT"
    echo '// In AppServiceProvider::register()' >> "$REPORT"
    echo 'if ($this->app->environment("local")) {' >> "$REPORT"
    echo '    $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);' >> "$REPORT"
    echo '    $this->app->register(TelescopeServiceProvider::class);' >> "$REPORT"
    echo '}' >> "$REPORT"
    echo '```' >> "$REPORT"
    
    # Check if Telescope is in production deps
    if jq -e '.require["laravel/telescope"]' composer.json >/dev/null 2>&1; then
        echo "✅ Telescope properly in production dependencies" >> "$REPORT"
    else
        echo "⚠️ Consider if Telescope should be in production dependencies" >> "$REPORT"
    fi
fi

# Check for Debugbar
if [ -f "config/debugbar.php" ]; then
    echo "### Laravel Debugbar Configuration" >> "$REPORT"
    echo "Debugbar detected. Ensure it's disabled in production:" >> "$REPORT"
    echo '```env' >> "$REPORT"
    echo 'DEBUGBAR_ENABLED=false' >> "$REPORT"
    echo '```' >> "$REPORT"
fi

# Check for Horizon
if [ -f "config/horizon.php" ]; then
    echo "### Laravel Horizon Configuration" >> "$REPORT"
    echo "✅ Horizon detected - typically needs to be in production dependencies" >> "$REPORT"
fi

# 4. Generate comprehensive fix commands
echo "" >> "$REPORT"
echo "## 🔧 Recommended Actions" >> "$REPORT"

if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    echo "### Move Packages to Production Dependencies" >> "$REPORT"
    echo "**Packages requiring relocation:** ${#MOVE_TO_PROD[@]}" >> "$REPORT"
    echo "" >> "$REPORT"
    echo "Run these commands to fix dependency issues:" >> "$REPORT"
    echo '```bash' >> "$REPORT"
    echo "# Remove from require-dev and add to require" >> "$REPORT"
    for pkg in "${MOVE_TO_PROD[@]}"; do
        echo "composer remove --dev \"$pkg\"" >> "$REPORT"
        echo "composer require \"$pkg\"" >> "$REPORT"
        echo "" >> "$REPORT"
    done
    echo '```' >> "$REPORT"
    
    echo "**Alternative single command:**" >> "$REPORT"
    echo '```bash' >> "$REPORT"
    echo "# Move all at once" >> "$REPORT"
    for pkg in "${MOVE_TO_PROD[@]}"; do
        echo "composer remove --dev \"$pkg\" && composer require \"$pkg\"" >> "$REPORT"
    done
    echo '```' >> "$REPORT"
fi

if [ ${#SUSPICIOUS_PACKAGES[@]} -gt 0 ]; then
    echo "### Review Suspicious Packages" >> "$REPORT"
    echo "The following packages have auto-discovery but are in require-dev:" >> "$REPORT"
    for pkg in "${SUSPICIOUS_PACKAGES[@]}"; do
        echo "- **$pkg** - Review if needed in production" >> "$REPORT"
    done
fi

# 5. Analysis Summary
echo "" >> "$REPORT"
echo "## 📊 Analysis Summary" >> "$REPORT"
echo "- **Total Dev Packages Analyzed:** ${#DEV_DEPS[@]}" >> "$REPORT"
echo "- **Packages Requiring Move to Production:** ${#MOVE_TO_PROD[@]}" >> "$REPORT"
echo "- **Suspicious Auto-Discovery Packages:** ${#SUSPICIOUS_PACKAGES[@]}" >> "$REPORT"
echo "- **Analysis Warnings:** ${#ANALYSIS_WARNINGS[@]}" >> "$REPORT"

# Generate status
ANALYSIS_STATUS="PASSED"
if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    ANALYSIS_STATUS="ACTION_REQUIRED"
fi

echo "- **Overall Status:** $ANALYSIS_STATUS" >> "$REPORT"

# 6. Next Steps
echo "" >> "$REPORT"
echo "## 🎯 Next Steps" >> "$REPORT"
if [ "$ANALYSIS_STATUS" = "ACTION_REQUIRED" ]; then
    echo "1. **Fix dependency classification issues** using commands above" >> "$REPORT"
    echo "2. **Re-run this analysis** after making changes" >> "$REPORT"
    echo "3. **Commit updated composer.json and composer.lock**" >> "$REPORT"
    echo "4. **Proceed to production dependency verification**" >> "$REPORT"
else
    echo "1. ✅ **Dependency analysis passed** - no issues detected" >> "$REPORT"
    echo "2. **Proceed to production dependency verification**" >> "$REPORT"
fi

# Display results and offer auto-fix
log "Dependency analysis completed"
echo ""
echo "📋 Analysis Summary:"
echo "  - Dev Packages Analyzed: ${#DEV_DEPS[@]}"
echo "  - Packages Need Moving: ${#MOVE_TO_PROD[@]}"
echo "  - Suspicious Packages: ${#SUSPICIOUS_PACKAGES[@]}"
echo "  - Analysis Status: $ANALYSIS_STATUS"
echo ""
echo "📁 Full report saved to: $REPORT"

# Auto-fix prompt
if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    echo ""
    echo "⚠️ CRITICAL: Found ${#MOVE_TO_PROD[@]} packages that need to be moved to production!"
    echo "Affected packages:"
    for pkg in "${MOVE_TO_PROD[@]}"; do
        echo "  - $pkg"
    done
    echo ""
    read -p "🔧 Would you like to auto-fix these issues now? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo ""
        log "Applying automatic fixes..."
        for pkg in "${MOVE_TO_PROD[@]}"; do
            log "Moving $pkg to production dependencies..."
            if composer remove --dev "$pkg" && composer require "$pkg"; then
                log "✅ Successfully moved $pkg"
            else
                log "❌ Failed to move $pkg"
            fi
        done
        echo ""
        log "✅ Dependency fixes applied! Remember to commit composer.json and composer.lock"
        echo ""
        echo "🔄 Re-running analysis to verify fixes..."
        exec "$0"
    else
        echo ""
        log "Manual fix required. Please run the commands from the report."
        exit 1
    fi
else
    echo ""
    log "✅ Dependency analysis passed - no issues detected!"
fi