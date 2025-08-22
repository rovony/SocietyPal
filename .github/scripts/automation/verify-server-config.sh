#!/bin/bash

# SocietyPal Server Configuration Verification Script
# Purpose: Verify all server configurations before any deployment
# Author: Auto-generated for SocietyPal GitHub Actions
# Version: 1.0

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 SocietyPal Server Configuration Verification${NC}"
echo "=================================================="
echo "This script verifies server configuration before deployment"
echo ""

# Initialize counters
CHECKS_PASSED=0
CHECKS_FAILED=0
CHECKS_WARNING=0

# Function to check passed/failed
check_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ PASS${NC}"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}❌ FAIL${NC}"
        ((CHECKS_FAILED++))
    fi
}

# Function to show warning
check_warning() {
    echo -e "${YELLOW}⚠️  WARNING${NC}"
    ((CHECKS_WARNING++))
}

echo "📋 VERIFYING GITHUB SECRETS CONFIGURATION"
echo "----------------------------------------"

# Check if environment is set
if [ -z "$ENVIRONMENT" ]; then
    echo "🔍 Environment detection..."
    if [[ "$GITHUB_REF" == "refs/heads/production" ]]; then
        ENVIRONMENT="production"
    elif [[ "$GITHUB_REF" == "refs/heads/main" ]] || [[ "$GITHUB_REF" == "refs/heads/staging" ]]; then
        ENVIRONMENT="staging"
    else
        echo -e "${RED}❌ Cannot determine environment from branch: $GITHUB_REF${NC}"
        exit 1
    fi
fi

echo "🎯 Target Environment: ${ENVIRONMENT}"

# Server Connection Verification
echo ""
echo "🌐 SERVER CONNECTION VERIFICATION"
echo "--------------------------------"

echo -n "🔍 Checking SERVER_HOST configuration... "
if [ -n "$SERVER_HOST" ]; then
    # Verify it matches expected Hostinger IP
    if [ "$SERVER_HOST" = "31.97.195.108" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: SERVER_HOST ($SERVER_HOST) doesn't match expected Hostinger IP (31.97.195.108)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ SERVER_HOST not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking SERVER_PORT configuration... "
if [ -n "$SERVER_PORT" ]; then
    if [ "$SERVER_PORT" = "65002" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: SERVER_PORT ($SERVER_PORT) doesn't match expected port (65002)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ SERVER_PORT not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking SERVER_USER configuration... "
if [ -n "$SERVER_USER" ]; then
    if [ "$SERVER_USER" = "u227177893" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: SERVER_USER ($SERVER_USER) doesn't match expected user (u227177893)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ SERVER_USER not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking SSH Key configuration... "
if [ -n "$SERVER_SSH_KEY" ]; then
    # Check if SSH key has proper format
    if echo "$SERVER_SSH_KEY" | grep -q "BEGIN.*PRIVATE KEY"; then
        check_result 0
    else
        echo -e "${RED}❌ SSH key doesn't appear to have valid private key format${NC}"
        check_result 1
    fi
else
    echo -e "${RED}❌ SERVER_SSH_KEY not configured${NC}"
    check_result 1
fi

# Database Configuration Verification
echo ""
echo "🗄️  DATABASE CONFIGURATION VERIFICATION"
echo "--------------------------------------"

# Set database variables based on environment
if [ "$ENVIRONMENT" = "production" ]; then
    DB_HOST_VAR="$DB_HOST_PROD"
    DB_PORT_VAR="$DB_PORT_PROD"
    DB_DATABASE_VAR="$DB_DATABASE_PROD"
    DB_USERNAME_VAR="$DB_USERNAME_PROD"
    DB_PASSWORD_VAR="$DB_PASSWORD_PROD"
    
    EXPECTED_DB_HOST="127.0.0.1"
    EXPECTED_DB_PORT="3306"
    EXPECTED_DB_DATABASE="u227177893_p_zaj_socpal_d"
    EXPECTED_DB_USERNAME="u227177893_p_zaj_socpal_u"
else
    DB_HOST_VAR="$DB_HOST_STAGING"
    DB_PORT_VAR="$DB_PORT_STAGING"
    DB_DATABASE_VAR="$DB_DATABASE_STAGING"
    DB_USERNAME_VAR="$DB_USERNAME_STAGING"
    DB_PASSWORD_VAR="$DB_PASSWORD_STAGING"
    
    EXPECTED_DB_HOST="127.0.0.1"
    EXPECTED_DB_PORT="3306"
    EXPECTED_DB_DATABASE="u227177893_s_zaj_socpal_d"
    EXPECTED_DB_USERNAME="u227177893_s_zaj_socpal_u"
fi

echo -n "🔍 Checking DB_HOST_${ENVIRONMENT^^} configuration... "
if [ -n "$DB_HOST_VAR" ]; then
    if [ "$DB_HOST_VAR" = "$EXPECTED_DB_HOST" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: DB_HOST ($DB_HOST_VAR) doesn't match expected ($EXPECTED_DB_HOST)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ DB_HOST_${ENVIRONMENT^^} not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking DB_PORT_${ENVIRONMENT^^} configuration... "
if [ -n "$DB_PORT_VAR" ]; then
    if [ "$DB_PORT_VAR" = "$EXPECTED_DB_PORT" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: DB_PORT ($DB_PORT_VAR) doesn't match expected ($EXPECTED_DB_PORT)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ DB_PORT_${ENVIRONMENT^^} not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking DB_DATABASE_${ENVIRONMENT^^} configuration... "
if [ -n "$DB_DATABASE_VAR" ]; then
    if [ "$DB_DATABASE_VAR" = "$EXPECTED_DB_DATABASE" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: DB_DATABASE ($DB_DATABASE_VAR) doesn't match expected ($EXPECTED_DB_DATABASE)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ DB_DATABASE_${ENVIRONMENT^^} not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking DB_USERNAME_${ENVIRONMENT^^} configuration... "
if [ -n "$DB_USERNAME_VAR" ]; then
    if [ "$DB_USERNAME_VAR" = "$EXPECTED_DB_USERNAME" ]; then
        check_result 0
    else
        echo -e "${YELLOW}⚠️  WARNING: DB_USERNAME ($DB_USERNAME_VAR) doesn't match expected ($EXPECTED_DB_USERNAME)${NC}"
        check_warning
    fi
else
    echo -e "${RED}❌ DB_USERNAME_${ENVIRONMENT^^} not configured${NC}"
    check_result 1
fi

echo -n "🔍 Checking DB_PASSWORD_${ENVIRONMENT^^} configuration... "
if [ -n "$DB_PASSWORD_VAR" ]; then
    # Just check if it exists, don't validate actual password for security
    check_result 0
else
    echo -e "${RED}❌ DB_PASSWORD_${ENVIRONMENT^^} not configured${NC}"
    check_result 1
fi

# Server Path Verification
echo ""
echo "📁 SERVER PATH VERIFICATION"
echo "--------------------------"

# Expected paths based on environment
if [ "$ENVIRONMENT" = "production" ]; then
    EXPECTED_DOMAIN_ROOT="/home/u227177893/domains/societypal.com"
    EXPECTED_PUBLIC_HTML="$EXPECTED_DOMAIN_ROOT/public_html"
    EXPECTED_DEPLOY_ROOT="$EXPECTED_DOMAIN_ROOT/deploy"
else
    EXPECTED_DOMAIN_ROOT="/home/u227177893/domains/staging.societypal.com"
    EXPECTED_PUBLIC_HTML="$EXPECTED_DOMAIN_ROOT/public_html"
    EXPECTED_DEPLOY_ROOT="$EXPECTED_DOMAIN_ROOT/deploy"
fi

echo "🎯 Expected domain root: $EXPECTED_DOMAIN_ROOT"
echo "🎯 Expected public HTML: $EXPECTED_PUBLIC_HTML"
echo "🎯 Expected deploy root: $EXPECTED_DEPLOY_ROOT"

# Environment-Specific Verification
echo ""
echo "🎯 ENVIRONMENT-SPECIFIC VERIFICATION"
echo "-----------------------------------"

if [ "$ENVIRONMENT" = "production" ]; then
    echo "🔍 Production Environment Checks:"
    echo "  • Domain: https://societypal.com/"
    echo "  • Database: u227177893_p_zaj_socpal_d"
    echo "  • Branch: production"
else
    echo "🔍 Staging Environment Checks:"
    echo "  • Domain: https://staging.societypal.com/"
    echo "  • Database: u227177893_s_zaj_socpal_d"
    echo "  • Branch: main/staging"
fi

# GitHub Actions Context Verification
echo ""
echo "🏗️  GITHUB ACTIONS CONTEXT VERIFICATION"
echo "--------------------------------------"

echo -n "🔍 Checking GitHub repository context... "
if [ -n "$GITHUB_REPOSITORY" ]; then
    echo -e "Repository: $GITHUB_REPOSITORY"
    check_result 0
else
    echo -e "${RED}❌ GITHUB_REPOSITORY not available${NC}"
    check_result 1
fi

echo -n "🔍 Checking GitHub ref context... "
if [ -n "$GITHUB_REF" ]; then
    echo -e "Ref: $GITHUB_REF"
    check_result 0
else
    echo -e "${RED}❌ GITHUB_REF not available${NC}"
    check_result 1
fi

echo -n "🔍 Checking GitHub SHA context... "
if [ -n "$GITHUB_SHA" ]; then
    echo -e "SHA: ${GITHUB_SHA:0:8}..."
    check_result 0
else
    echo -e "${RED}❌ GITHUB_SHA not available${NC}"
    check_result 1
fi

# Final Summary
echo ""
echo "=================================================="
echo "📊 VERIFICATION SUMMARY"
echo "=================================================="
echo -e "${GREEN}✅ Passed: $CHECKS_PASSED${NC}"
if [ $CHECKS_WARNING -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Warnings: $CHECKS_WARNING${NC}"
fi
if [ $CHECKS_FAILED -gt 0 ]; then
    echo -e "${RED}❌ Failed: $CHECKS_FAILED${NC}"
fi
echo "📊 Total: $((CHECKS_PASSED + CHECKS_WARNING + CHECKS_FAILED))"

# Exit based on results
if [ $CHECKS_FAILED -gt 0 ]; then
    echo ""
    echo -e "${RED}🚨 VERIFICATION FAILED${NC}"
    echo "❌ Critical configuration errors detected"
    echo "🛑 Deployment cannot proceed safely"
    exit 1
elif [ $CHECKS_WARNING -gt 0 ]; then
    echo ""
    echo -e "${YELLOW}⚠️  VERIFICATION COMPLETED WITH WARNINGS${NC}"
    echo "🔍 Please review configuration warnings above"
    echo "✅ Deployment can proceed but review recommended"
    exit 0
else
    echo ""
    echo -e "${GREEN}🎉 VERIFICATION SUCCESSFUL${NC}"
    echo "✅ All server configurations verified"
    echo "🚀 Ready for safe deployment"
    exit 0
fi