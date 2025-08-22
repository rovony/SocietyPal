#!/bin/bash
# Laravel Customization System - Detection Script
# Version: 1.0.0
# Description: Detects existing customization system setup and provides status

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Laravel Customization System - Detection Script${NC}"
echo "================================================="

# Check if we're in a Laravel project
if [[ ! -f "artisan" ]]; then
    echo -e "${RED}❌ Error: Not in Laravel project root (artisan not found)${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Laravel project detected${NC}"

# Initialize status variables
DIRECTORIES_EXIST=0
SERVICE_PROVIDER_EXISTS=0
SERVICE_PROVIDER_REGISTERED=0
WEBPACK_CONFIG_EXISTS=0
TOTAL_CHECKS=4
PASSED_CHECKS=0

# Check for customization directories
echo
echo "🔍 Checking customization directories..."
if [[ -d "app/Custom" ]] && [[ -d "resources/Custom" ]]; then
    echo -e "${GREEN}✅ Customization directories exist${NC}"
    DIRECTORIES_EXIST=1
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo -e "${RED}❌ Customization directories missing${NC}"
fi

# Check for CustomizationServiceProvider
echo
echo "🔍 Checking CustomizationServiceProvider..."
if [[ -f "app/Providers/CustomizationServiceProvider.php" ]]; then
    echo -e "${GREEN}✅ CustomizationServiceProvider exists${NC}"
    SERVICE_PROVIDER_EXISTS=1
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo -e "${RED}❌ CustomizationServiceProvider missing${NC}"
fi

# Check if service provider is registered
echo
echo "🔍 Checking service provider registration..."
if grep -q "CustomizationServiceProvider" bootstrap/providers.php 2>/dev/null; then
    echo -e "${GREEN}✅ CustomizationServiceProvider registered${NC}"
    SERVICE_PROVIDER_REGISTERED=1
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo -e "${RED}❌ CustomizationServiceProvider not registered${NC}"
fi

# Check for webpack custom config
echo
echo "🔍 Checking webpack custom configuration..."
if [[ -f "webpack.custom.js" ]]; then
    echo -e "${GREEN}✅ Custom webpack configuration exists${NC}"
    WEBPACK_CONFIG_EXISTS=1
    PASSED_CHECKS=$((PASSED_CHECKS + 1))
else
    echo -e "${RED}❌ Custom webpack configuration missing${NC}"
fi

# Summary
echo
echo "📊 DETECTION SUMMARY"
echo "===================="
echo -e "Total checks: ${BLUE}${TOTAL_CHECKS}${NC}"
echo -e "Passed checks: ${GREEN}${PASSED_CHECKS}${NC}"
echo -e "Failed checks: ${RED}$((TOTAL_CHECKS - PASSED_CHECKS))${NC}"

# Determine setup status
if [[ $PASSED_CHECKS -eq $TOTAL_CHECKS ]]; then
    echo -e "${GREEN}✅ FULLY CONFIGURED: Customization system is complete${NC}"
    exit 0
elif [[ $PASSED_CHECKS -gt 0 ]]; then
    echo -e "${YELLOW}⚠️  PARTIALLY CONFIGURED: Some components missing${NC}"
    echo
    echo "🔧 To complete setup, run:"
    echo "   bash Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Customization-System/setup-customization.sh"
    exit 1
else
    echo -e "${RED}❌ NOT CONFIGURED: No customization system detected${NC}"
    echo
    echo "🚀 To install, run:"
    echo "   bash Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Customization-System/setup-customization.sh"
    exit 2
fi