#!/bin/bash

echo "╔═══════════════════════════════════════════════════════════╗"
echo "║     Comprehensive Laravel Environment Analysis           ║"
echo "╚═══════════════════════════════════════════════════════════╝"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

# Create analysis report
REPORT="Admin-Local/Deployment/Logs/env-analysis-$(date +%Y%m%d-%H%M%S).md"

echo "# Environment Analysis Report" > $REPORT
echo "Generated: $(date)" >> $REPORT
echo "" >> $REPORT

# 1. PHP Analysis
echo "## PHP Configuration" >> $REPORT
echo "### Version" >> $REPORT
PHP_CURRENT=$(php -v | head -n1)
PHP_REQUIRED=$(grep '"php":' composer.json 2>/dev/null | sed 's/.*"php":[[:space:]]*"\([^"]*\)".*/\1/')
echo "- Current: $PHP_CURRENT" >> $REPORT
echo "- Required: ${PHP_REQUIRED:-Not specified}" >> $REPORT

# Check PHP extensions
echo "### Required Extensions" >> $REPORT
REQUIRED_EXTENSIONS=(
    "bcmath" "ctype" "curl" "dom" "fileinfo"
    "json" "mbstring" "openssl" "pcre" "pdo"
    "tokenizer" "xml" "zip" "gd" "intl"
)

MISSING_EXTENSIONS=()
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^$ext$"; then
        MISSING_EXTENSIONS+=("$ext")
        echo "- ❌ $ext (MISSING)" >> $REPORT
    else
        echo "- ✅ $ext" >> $REPORT
    fi
done

# 2. Composer Analysis
echo "" >> $REPORT
echo "## Composer Configuration" >> $REPORT
COMPOSER_CURRENT=$(composer --version 2>/dev/null | sed -n 's/.*Composer version \([0-9.]*\).*/\1/p')
echo "- Current Version: $COMPOSER_CURRENT" >> $REPORT

# 3. Laravel-specific checks
echo "" >> $REPORT
echo "## Laravel Configuration" >> $REPORT
if [ -f "artisan" ]; then
    LARAVEL_VERSION=$(php artisan --version 2>/dev/null | sed -n 's/.*Laravel Framework \([0-9.]*\).*/\1/p')
    echo "- Laravel Version: ${LARAVEL_VERSION:-Unknown}" >> $REPORT
fi

# Generate action items
echo "" >> $REPORT
echo "## ⚠️ Action Items" >> $REPORT

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    echo "### Missing PHP Extensions" >> $REPORT
    echo "Install the following PHP extensions:" >> $REPORT
    for ext in "${MISSING_EXTENSIONS[@]}"; do
        echo "- sudo apt-get install php${PHP_VERSION}-${ext}" >> $REPORT
    done
fi

echo ""
echo "📋 Full report saved to: $REPORT"
cat $REPORT