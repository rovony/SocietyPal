#!/bin/bash

# SocietyPal GitHub Actions Setup Validation Script
# This script validates the complete GitHub Actions CI/CD setup
# Run this script to ensure all components are properly configured

set -e

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 SocietyPal GitHub Actions Setup Validation${NC}"
echo -e "${BLUE}===============================================${NC}"
echo ""

# Counters
TOTAL_CHECKS=0
PASSED_CHECKS=0
FAILED_CHECKS=0
WARNING_CHECKS=0

# Function to perform a check
check_item() {
    local description="$1"
    local test_command="$2"
    local level="$3" # info, warning, critical
    
    TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    
    echo -n "🔍 $description... "
    
    if eval "$test_command" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ PASS${NC}"
        PASSED_CHECKS=$((PASSED_CHECKS + 1))
        return 0
    else
        if [[ "$level" == "critical" ]]; then
            echo -e "${RED}❌ FAIL${NC}"
            FAILED_CHECKS=$((FAILED_CHECKS + 1))
        elif [[ "$level" == "warning" ]]; then
            echo -e "${YELLOW}⚠️  WARN${NC}"
            WARNING_CHECKS=$((WARNING_CHECKS + 1))
        else
            echo -e "${BLUE}ℹ️  INFO${NC}"
        fi
        return 1
    fi
}

echo -e "${PURPLE}📁 Checking GitHub Actions Directory Structure${NC}"
echo "------------------------------------------------"

check_item "GitHub Actions workflows directory exists" "[ -d .github/workflows ]" "critical"
check_item "GitHub Actions scripts directory exists" "[ -d .github/scripts ]" "critical"
check_item "Main deployment workflow exists" "[ -f .github/workflows/deploy.yml ]" "critical"
check_item "Manual deployment workflow exists" "[ -f .github/workflows/manual-deploy.yml ]" "warning"
check_item "Migration check script exists" "[ -f .github/scripts/check-migrations.sh ]" "critical"
check_item "Rollback script exists" "[ -f .github/scripts/rollback.sh ]" "warning"
check_item "Deployment secrets documentation exists" "[ -f .github/DEPLOYMENT_SECRETS.md ]" "info"

echo ""
echo -e "${PURPLE}🔧 Checking Script Permissions and Executability${NC}"
echo "------------------------------------------------"

# Make scripts executable
if [ -f .github/scripts/check-migrations.sh ]; then
    chmod +x .github/scripts/check-migrations.sh
    check_item "Migration check script is executable" "[ -x .github/scripts/check-migrations.sh ]" "critical"
fi

if [ -f .github/scripts/rollback.sh ]; then
    chmod +x .github/scripts/rollback.sh
    check_item "Rollback script is executable" "[ -x .github/scripts/rollback.sh ]" "warning"
fi

if [ -f .github/scripts/validate-setup.sh ]; then
    chmod +x .github/scripts/validate-setup.sh
    check_item "Setup validation script is executable" "[ -x .github/scripts/validate-setup.sh ]" "info"
fi

echo ""
echo -e "${PURPLE}📦 Checking Laravel Project Structure${NC}"
echo "------------------------------------"

check_item "Composer configuration exists" "[ -f composer.json ]" "critical"
check_item "Package.json configuration exists" "[ -f package.json ]" "critical"
check_item "Laravel artisan command exists" "[ -f artisan ]" "critical"
check_item "Database migrations directory exists" "[ -d database/migrations ]" "warning"
check_item "Resources directory exists" "[ -d resources ]" "critical"
check_item "Public directory exists" "[ -d public ]" "critical"
check_item "Storage directory exists" "[ -d storage ]" "warning"
check_item "Bootstrap cache directory exists" "[ -d bootstrap/cache ]" "warning"

echo ""
echo -e "${PURPLE}🔧 Checking Configuration Files${NC}"
echo "------------------------------"

check_item "Environment example file exists" "[ -f .env.example ]" "warning"
check_item "Vite configuration exists" "[ -f vite.config.js ]" "critical"
check_item "Tailwind configuration exists" "[ -f tailwind.config.js ]" "info"
check_item "PostCSS configuration exists" "[ -f postcss.config.js ]" "info"

echo ""
echo -e "${PURPLE}🔍 Analyzing Workflow Configuration${NC}"
echo "----------------------------------"

if [ -f .github/workflows/deploy.yml ]; then
    check_item "Deploy workflow has PHP 8.2 specification" "grep -q 'PHP_VERSION.*8.2' .github/workflows/deploy.yml" "critical"
    check_item "Deploy workflow has Node.js 18 specification" "grep -q 'NODE_VERSION.*18' .github/workflows/deploy.yml" "critical"
    check_item "Deploy workflow has environment detection" "grep -q 'github.ref_name' .github/workflows/deploy.yml" "critical"
    check_item "Deploy workflow has secrets configuration" "grep -q 'secrets\.' .github/workflows/deploy.yml" "critical"
    check_item "Deploy workflow has rollback capability" "grep -q 'rollback\|Rollback' .github/workflows/deploy.yml" "warning"
    check_item "Deploy workflow has health checks" "grep -q 'health\|Health' .github/workflows/deploy.yml" "warning"
fi

echo ""
echo -e "${PURPLE}🗄️ Checking Database Migration Safety${NC}"
echo "------------------------------------"

if [ -d database/migrations ] && [ -f .github/scripts/check-migrations.sh ]; then
    echo "🔍 Running migration safety check..."
    if ./.github/scripts/check-migrations.sh >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Migration safety check completed successfully${NC}"
        PASSED_CHECKS=$((PASSED_CHECKS + 1))
    else
        migration_result=$?
        if [ $migration_result -eq 2 ]; then
            echo -e "${RED}❌ CRITICAL: Destructive migrations detected${NC}"
            FAILED_CHECKS=$((FAILED_CHECKS + 1))
        elif [ $migration_result -eq 1 ]; then
            echo -e "${YELLOW}⚠️  WARNING: Potentially risky migrations detected${NC}"
            WARNING_CHECKS=$((WARNING_CHECKS + 1))
        fi
    fi
    TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
else
    echo -e "${BLUE}ℹ️  Skipping migration check (no migrations or script not found)${NC}"
fi

echo ""
echo -e "${PURPLE}📋 Required GitHub Secrets Checklist${NC}"
echo "----------------------------------"

echo "The following secrets need to be configured in GitHub:"
echo ""

secrets=(
    "SERVER_HOST (31.97.195.108)"
    "SERVER_USER (u227177893)"
    "SERVER_PORT (65002)"
    "SERVER_SSH_KEY (Complete SSH private key)"
    "DB_HOST_PROD (127.0.0.1)"
    "DB_PORT_PROD (3306)"
    "DB_DATABASE_PROD (u227177893_p_zaj_socpal_d)"
    "DB_USERNAME_PROD (u227177893_p_zaj_socpal_u)"
    "DB_PASSWORD_PROD (Production DB password)"
    "DB_HOST_STAGING (127.0.0.1)"
    "DB_PORT_STAGING (3306)"
    "DB_DATABASE_STAGING (u227177893_s_zaj_socpal_d)"
    "DB_USERNAME_STAGING (u227177893_s_zaj_socpal_u)"
    "DB_PASSWORD_STAGING (Staging DB password)"
)

for secret in "${secrets[@]}"; do
    echo "  🔐 $secret"
done

echo ""
echo -e "${PURPLE}🚀 Deployment Triggers Configuration${NC}"
echo "----------------------------------"

echo "📋 Automatic deployment triggers:"
echo "  • Staging: Push to 'main' or 'staging' branches"
echo "  • Production: Push to 'production' branch"
echo ""
echo "📋 Manual deployment triggers:"
echo "  • Go to Actions tab → 'SocietyPal Deployment Pipeline' → 'Run workflow'"
echo "  • Emergency rollback: Actions tab → 'Manual Emergency Deployment'"
echo ""

echo -e "${PURPLE}🎯 Next Steps After This Validation${NC}"
echo "--------------------------------"

if [ $FAILED_CHECKS -gt 0 ]; then
    echo -e "${RED}❌ CRITICAL ISSUES FOUND${NC}"
    echo "1. Fix all critical issues before proceeding"
    echo "2. Re-run this validation script"
    echo "3. Configure GitHub secrets"
    echo "4. Test deployment on staging"
elif [ $WARNING_CHECKS -gt 0 ]; then
    echo -e "${YELLOW}⚠️  WARNINGS FOUND${NC}"
    echo "1. Review and address warnings if needed"
    echo "2. Configure GitHub secrets"
    echo "3. Test deployment on staging"
    echo "4. Deploy to production when ready"
else
    echo -e "${GREEN}✅ ALL CHECKS PASSED${NC}"
    echo "1. Configure GitHub secrets (see DEPLOYMENT_SECRETS.md)"
    echo "2. Create SSH key pair and add to server"
    echo "3. Test staging deployment"
    echo "4. Deploy to production when ready"
fi

echo ""
echo -e "${BLUE}===============================================${NC}"
echo -e "${BLUE}📊 VALIDATION SUMMARY${NC}"
echo -e "${BLUE}===============================================${NC}"
echo -e "${GREEN}✅ Passed: $PASSED_CHECKS${NC}"
echo -e "${YELLOW}⚠️  Warnings: $WARNING_CHECKS${NC}"
echo -e "${RED}❌ Failed: $FAILED_CHECKS${NC}"
echo -e "${BLUE}📊 Total: $TOTAL_CHECKS${NC}"
echo ""

if [ $FAILED_CHECKS -eq 0 ]; then
    echo -e "${GREEN}🎉 GitHub Actions CI/CD setup is ready for deployment!${NC}"
    echo -e "${GREEN}📖 Next: Configure secrets using .github/DEPLOYMENT_SECRETS.md${NC}"
    exit 0
else
    echo -e "${RED}🚨 Setup validation failed. Please fix critical issues before proceeding.${NC}"
    exit 1
fi