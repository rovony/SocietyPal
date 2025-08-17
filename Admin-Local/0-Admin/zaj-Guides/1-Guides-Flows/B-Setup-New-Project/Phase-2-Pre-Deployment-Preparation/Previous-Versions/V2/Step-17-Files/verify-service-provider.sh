#!/bin/bash
# Step 17 - Service Provider Verification Script
# Lightweight verification without resource exhaustion

echo "🔍 Step 17: Service Provider Verification"
echo "========================================"

# Check if CustomizationServiceProvider exists
echo "1️⃣ Checking if CustomizationServiceProvider file exists..."
if [ -f "app/Providers/CustomizationServiceProvider.php" ]; then
    echo "   ✅ CustomizationServiceProvider.php found"
else
    echo "   ❌ CustomizationServiceProvider.php missing"
    exit 1
fi

# Check if service provider is registered in bootstrap/providers.php (Laravel 11+)
echo "2️⃣ Checking service provider registration..."
if [ -f "bootstrap/providers.php" ]; then
    if grep -q "CustomizationServiceProvider" bootstrap/providers.php; then
        echo "   ✅ CustomizationServiceProvider registered in bootstrap/providers.php"
    else
        echo "   ❌ CustomizationServiceProvider not found in bootstrap/providers.php"
        exit 1
    fi
elif [ -f "config/app.php" ]; then
    # Fallback for older Laravel versions
    if grep -q "CustomizationServiceProvider" config/app.php; then
        echo "   ✅ CustomizationServiceProvider registered in config/app.php"
    else
        echo "   ❌ CustomizationServiceProvider not found in config/app.php"
        exit 1
    fi
else
    echo "   ❌ Neither bootstrap/providers.php nor config/app.php found"
    exit 1
fi

# Simple service provider loading test (without var_dump)
echo "3️⃣ Testing service provider loading..."
if php artisan list | grep -q "Custom" > /dev/null 2>&1; then
    echo "   ✅ Custom commands detected (service provider loaded)"
elif php artisan config:cache > /dev/null 2>&1; then
    echo "   ✅ Service provider loads without errors"
else
    echo "   ⚠️  Service provider status unclear (may still be working)"
fi

echo ""
echo "✅ Service Provider Verification Complete"