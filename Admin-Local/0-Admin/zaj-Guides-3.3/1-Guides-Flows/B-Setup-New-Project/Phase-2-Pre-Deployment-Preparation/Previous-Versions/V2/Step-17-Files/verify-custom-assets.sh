#!/bin/bash
# Step 17 - Custom Assets Verification Script
# Lightweight asset verification

echo "🔍 Step 17: Custom Assets Verification"
echo "===================================="

# Check Custom Assets Directory
echo "1️⃣ Checking Custom assets directory..."
if [ -d "public/Custom" ]; then
    echo "   ✅ public/Custom/ directory exists"
    # Check for compiled assets
    if [ -f "public/Custom/js/app.js" ] && [ -f "public/Custom/css/app.css" ]; then
        echo "   ✅ Compiled custom assets found (JS: $(du -h public/Custom/js/app.js | cut -f1), CSS: $(du -h public/Custom/css/app.css | cut -f1))"
    else
        echo "   ⚠️  Compiled assets missing - run 'npm run custom:build'"
    fi
else
    echo "   ❌ public/Custom/ directory missing"
    exit 1
fi

# Check Source Assets
echo "2️⃣ Checking source asset files..."
if [ -d "resources/Custom" ]; then
    echo "   ✅ resources/Custom/ directory exists"
    if [ -f "resources/Custom/js/app.js" ] && [ -f "resources/Custom/css/app.scss" ]; then
        echo "   ✅ Source asset files found"
    else
        echo "   ⚠️  Some source asset files missing"
    fi
else
    echo "   ❌ resources/Custom/ directory missing"
    exit 1
fi

# Check webpack custom config
echo "3️⃣ Checking webpack custom configuration..."
if [ -f "webpack.custom.cjs" ]; then
    echo "   ✅ webpack.custom.cjs found"
else
    echo "   ❌ webpack.custom.cjs missing"
    exit 1
fi

# Check package.json custom scripts
echo "4️⃣ Checking custom build scripts..."
if grep -q "custom:build" package.json > /dev/null 2>&1; then
    echo "   ✅ custom:build script found in package.json"
else
    echo "   ❌ custom:build script missing in package.json"
    exit 1
fi

echo ""
echo "✅ Custom Assets Verification Complete"