#!/bin/bash

echo "╔═══════════════════════════════════════════════════════════╗"
echo "║     Universal Laravel Dependency Analyzer v2.0          ║"
echo "╚═══════════════════════════════════════════════════════════╝"

# Load deployment variables
source Admin-Local/Deployment/Scripts/load-variables.sh

# Create comprehensive analysis report
mkdir -p Admin-Local/Deployment/Logs
REPORT="Admin-Local/Deployment/Logs/dependency-analysis-$(date +%Y%m%d-%H%M%S).md"

echo "# Universal Dependency Analysis Report" > $REPORT
echo "Generated: $(date)" >> $REPORT
echo "Project: $PROJECT_NAME" >> $REPORT
echo "" >> $REPORT

# Check for Faker usage in seeders/migrations
echo "## ⚠️ Dev Dependencies in Production Analysis" >> $REPORT
MOVE_TO_PROD=()

if grep -r "Faker\\Factory\\|faker()" database/seeders database/migrations 2>/dev/null | grep -v "// *test\\|#\\|/\\*"; then
    echo "### 🚨 Faker Usage Detected" >> $REPORT
    echo "Faker is used in seeders/migrations. Move to production dependencies:" >> $REPORT
    echo '```bash' >> $REPORT
    echo 'composer remove --dev fakerphp/faker && composer require fakerphp/faker' >> $REPORT
    echo '```' >> $REPORT
    MOVE_TO_PROD+=("fakerphp/faker")
fi

# Security Vulnerability Scan
echo "" >> $REPORT
echo "## 🔒 Security Analysis" >> $REPORT

if command -v composer >/dev/null 2>&1; then
    echo "### Composer Security Audit" >> $REPORT
    composer audit --format=table >> $REPORT 2>/dev/null || echo "- No security vulnerabilities found" >> $REPORT
fi

# Auto-fix option
if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    echo ""
    echo "⚠️  Found ${#MOVE_TO_PROD[@]} dev dependencies used in production!"
    echo "📋 Review the report at: $REPORT"
    read -p "Auto-fix now? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        for pkg in "${MOVE_TO_PROD[@]}"; do
            composer remove --dev $pkg
            composer require $pkg
        done
        echo "✅ Dependencies moved to production"
    fi
else
    echo "✅ No dev dependencies found in production code" >> $REPORT
fi

echo "📋 Full report saved to: $REPORT"