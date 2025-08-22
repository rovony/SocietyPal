#!/bin/bash
set -e

# PhaseA-Pre1: System Requirements Detection & Diagnosis  
# Purpose: Check server environment vs typical Laravel application requirements
# Run: FIRST - before any other scripts (Pre-Deployment Commands - Before Upload)
# Action: DETECT and WARN only - stops pipeline if critical system issues found
# Version 2.2 - PRODUCTION READY (System-level checks only)
echo "=== PhaseA-Pre1: Server Environment Analysis ==="

# Initialize variables
CRITICAL_ISSUES=0
WARNINGS=0
RECOMMENDATIONS=()

echo "🔍 Analyzing server environment for Laravel application compatibility..."

# Pre1-01: Server Composer Analysis
echo "=== Server Composer Environment ==="

echo "🔍 Current working directory: $(pwd)"
echo "📋 Checking server Composer installation..."

# Check current system Composer versions  
echo "🖥️ System Composer Analysis:"

# Check composer (default)
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version 2>/dev/null | head -1)
    echo "   composer: $COMPOSER_VERSION"
    
    # Extract major version
    COMPOSER_MAJOR=$(echo "$COMPOSER_VERSION" | grep -o "version [0-9]" | cut -d' ' -f2 2>/dev/null || echo "unknown")
    echo "   composer major version: $COMPOSER_MAJOR"
else
    echo "   composer: NOT FOUND"
    CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
fi

# Check composer2 (if available)
if command -v composer2 &> /dev/null; then
    COMPOSER2_VERSION=$(composer2 --version 2>/dev/null | head -1)
    echo "   composer2: $COMPOSER2_VERSION"
    
    COMPOSER2_MAJOR=$(echo "$COMPOSER2_VERSION" | grep -o "version [0-9]" | cut -d' ' -f2 2>/dev/null || echo "unknown")
    echo "   composer2 major version: $COMPOSER2_MAJOR"
else
    echo "   composer2: NOT FOUND"
fi

# Server Composer Recommendations
echo "🔬 Server Composer Status:"

if command -v composer2 &> /dev/null && [[ "$COMPOSER2_MAJOR" == "2" ]]; then
    echo "✅ EXCELLENT: composer2 (v2.x) available - supports modern Laravel applications"
    RECOMMENDATIONS+=("Use 'composer2' for Laravel 9+ applications")
elif [[ "$COMPOSER_MAJOR" == "2" ]]; then
    echo "✅ GOOD: composer (v2.x) available - supports modern Laravel applications"  
    RECOMMENDATIONS+=("Use 'composer' for Laravel 9+ applications")
elif [[ "$COMPOSER_MAJOR" == "1" ]]; then
    echo "⚠️ WARNING: Only Composer 1.x available - limited Laravel support"
    WARNINGS=$((WARNINGS + 1))
    RECOMMENDATIONS+=("Install Composer 2.x for Laravel 9+ support: curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer2")
    RECOMMENDATIONS+=("Composer 1.x only supports Laravel 8 and below")
else
    echo "❌ CRITICAL: No working Composer installation found"
    CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
    RECOMMENDATIONS+=("Install Composer: curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer")
fi

# Pre1-02: PHP Extensions Analysis
echo "=== PHP Extensions Analysis ==="

# Get current PHP version
PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2 | cut -d'-' -f1)
echo "🐘 Current PHP: $PHP_VERSION"

# Check PHP version compatibility with Laravel
echo "📋 Analyzing PHP version for Laravel compatibility..."

PHP_MAJOR_MINOR=$(echo "$PHP_VERSION" | cut -d'.' -f1-2)
case "$PHP_MAJOR_MINOR" in
    "8.3"|"8.2"|"8.1")
        echo "✅ EXCELLENT: PHP $PHP_VERSION supports Laravel 10+ and all modern features"
        ;;
    "8.0")
        echo "✅ GOOD: PHP $PHP_VERSION supports Laravel 9+ (consider upgrading to 8.1+)"
        RECOMMENDATIONS+=("Consider upgrading to PHP 8.1+ for better performance and security")
        ;;
    "7.4")
        echo "⚠️ WARNING: PHP $PHP_VERSION only supports Laravel 8 and below"
        WARNINGS=$((WARNINGS + 1))
        RECOMMENDATIONS+=("Upgrade to PHP 8.1+ for Laravel 9+ support")
        ;;
    "7.3"|"7.2"|"7.1"|"7.0")
        echo "❌ CRITICAL: PHP $PHP_VERSION is outdated and unsupported"
        CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
        RECOMMENDATIONS+=("Upgrade to PHP 8.1+ immediately for security and Laravel support")
        ;;
    *)
        echo "⚠️ UNKNOWN: PHP $PHP_VERSION compatibility unknown"
        WARNINGS=$((WARNINGS + 1))
        ;;
esac

# Check for essential Laravel PHP extensions
echo "🔌 Checking essential Laravel PHP extensions..."

REQUIRED_EXTENSIONS=(
    "openssl"
    "pdo"
    "mbstring"
    "tokenizer"
    "xml"
    "ctype"
    "json"
    "bcmath"
    "curl"
    "fileinfo"
    "zip"
)

# These are the essential extensions for most Laravel applications
echo "📦 Checking extensions required by typical Laravel applications..."

MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -qi "^$ext$" 2>/dev/null; then
        echo "   ✅ $ext: installed"
    else
        echo "   ❌ $ext: MISSING"
        MISSING_EXTENSIONS+=("$ext")
        CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    echo "❌ CRITICAL: Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    RECOMMENDATIONS+=("Install missing PHP extensions: apt-get install $(printf 'php-*%s ' "${MISSING_EXTENSIONS[@]}")")
fi

# Pre1-03: Additional Server Capabilities
echo "=== Additional Server Capabilities ==="

# Check MySQL/MariaDB client
if command -v mysql &> /dev/null; then
    MYSQL_VERSION=$(mysql --version 2>/dev/null | head -1)
    echo "✅ MySQL client: $MYSQL_VERSION"
else
    echo "⚠️ MySQL client not found - database operations may be limited"
    WARNINGS=$((WARNINGS + 1))
    RECOMMENDATIONS+=("Install MySQL client: apt-get install mysql-client")
fi

# Check Git
if command -v git &> /dev/null; then
    GIT_VERSION=$(git --version 2>/dev/null)
    echo "✅ Git: $GIT_VERSION"
else
    echo "❌ CRITICAL: Git not found - required for deployments"
    CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1))
    RECOMMENDATIONS+=("Install Git: apt-get install git")
fi

# Check Node.js (for frontend builds)
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version 2>/dev/null)
    echo "✅ Node.js: $NODE_VERSION"
    
    if command -v npm &> /dev/null; then
        NPM_VERSION=$(npm --version 2>/dev/null)
        echo "✅ NPM: $NPM_VERSION"
    else
        echo "⚠️ NPM not found - frontend builds may fail"
        WARNINGS=$((WARNINGS + 1))
    fi
else
    echo "ℹ️ Node.js not found - only needed for frontend builds"
fi

# Pre1-04: General Build Recommendations
echo "=== General Build Recommendations ==="

echo "💡 Based on server environment, recommended commands for Laravel applications:"

# Determine best composer command
if command -v composer2 &> /dev/null && [[ "$COMPOSER2_MAJOR" == "2" ]]; then
    RECOMMENDED_COMPOSER="composer2"
    echo "   🎯 For Laravel 9+: Use composer2 commands"
elif [[ "$COMPOSER_MAJOR" == "2" ]]; then
    RECOMMENDED_COMPOSER="composer"
    echo "   🎯 For Laravel 9+: Use composer commands"
elif [[ "$COMPOSER_MAJOR" == "1" ]]; then
    RECOMMENDED_COMPOSER="composer"
    echo "   ⚠️ For Laravel 8 only: Use composer commands (upgrade to Composer 2.x for newer Laravel)"
else
    RECOMMENDED_COMPOSER="composer (INSTALL REQUIRED)"
    echo "   ❌ Install Composer first"
fi

if [ "$RECOMMENDED_COMPOSER" != "composer (INSTALL REQUIRED)" ]; then
    echo ""
    echo "📋 Standard Laravel build commands:"
    echo "   1. Dependencies: $RECOMMENDED_COMPOSER install --no-dev --optimize-autoloader --no-interaction"
    echo "   2. Clear caches: $RECOMMENDED_COMPOSER clear-cache"
    echo "   3. Dump autoload: $RECOMMENDED_COMPOSER dump-autoload --optimize --no-dev"
    
    if command -v npm &> /dev/null; then
        echo "   4. Frontend deps: npm ci --production"
        echo "   5. Frontend build: npm run production"
    else
        echo "   4. Frontend: Node.js/NPM not available (install if needed for frontend builds)"
    fi
fi

# Pre1-05: Final Report
echo "=== Server Environment Analysis Summary ==="

echo "📊 Server Environment Status:"
echo "   🔴 Critical Issues: $CRITICAL_ISSUES"
echo "   🟡 Warnings: $WARNINGS"

if [ $CRITICAL_ISSUES -gt 0 ]; then
    echo ""
    echo "❌ DEPLOYMENT BLOCKED - Critical server issues must be resolved"
    echo ""
    echo "🔧 Required Server Actions:"
    for rec in "${RECOMMENDATIONS[@]}"; do
        echo "   • $rec"
    done
    echo ""
    echo "💡 After resolving server issues, re-run deployment"
    exit 1
elif [ $WARNINGS -gt 0 ]; then
    echo ""
    echo "⚠️ DEPLOYMENT PROCEEDING WITH WARNINGS"
    echo ""
    echo "🔧 Recommended Server Improvements:"
    for rec in "${RECOMMENDATIONS[@]}"; do
        echo "   • $rec"
    done
    echo ""
    echo "✅ Server environment acceptable - proceeding to deployment phases..."
else
    echo ""
    echo "✅ SERVER ENVIRONMENT OPTIMAL"
    echo "✅ All requirements satisfied - proceeding to deployment phases..."
fi

echo "=== PhaseA-Pre1 Server Analysis Complete ==="
echo "✅ PhaseA-Pre1 completed successfully"
