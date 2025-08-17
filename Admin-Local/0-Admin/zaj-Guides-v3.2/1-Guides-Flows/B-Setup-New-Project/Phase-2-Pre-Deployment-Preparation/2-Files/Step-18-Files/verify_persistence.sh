#!/bin/bash

echo "🔍 Verifying data persistence setup..."

SHARED_PATH="${1:-../shared}"
CURRENT_PATH="$(pwd)"

# Check shared directory structure
echo "📁 Checking shared directory structure..."
for dir in "storage" "public"; do
    if [ -d "$SHARED_PATH/$dir" ]; then
        echo "✅ $SHARED_PATH/$dir exists"
    else
        echo "❌ $SHARED_PATH/$dir missing"
    fi
done

# Check symlinks in current release
echo "🔗 Checking symlinks..."
for link in "storage" "public" ".env"; do
    if [ -L "$link" ]; then
        TARGET=$(readlink "$link")
        echo "✅ $link -> $TARGET"
    else
        echo "❌ $link is not a symlink"
    fi
done

# Check permissions
echo "🔒 Checking permissions..."
if [ -d "$SHARED_PATH/storage" ]; then
    STORAGE_PERMS=$(stat -f "%Mp%Lp" "$SHARED_PATH/storage" 2>/dev/null || stat -c "%a" "$SHARED_PATH/storage" 2>/dev/null)
    echo "ℹ️ Storage permissions: $STORAGE_PERMS"
fi

# Check Laravel storage link
if [ -f "artisan" ] && [ -L "public/storage" ]; then
    echo "✅ Laravel storage link exists"
else
    echo "ℹ️ Laravel storage link not found (may not be needed)"
fi

# Test write permissions
echo "🧪 Testing write permissions..."
TEST_FILE="$SHARED_PATH/storage/logs/persistence_test_$(date +%s).txt"
if echo "test" > "$TEST_FILE" 2>/dev/null; then
    echo "✅ Write permissions working"
    rm -f "$TEST_FILE"
else
    echo "❌ Write permissions failed"
fi

echo "✅ Persistence verification complete"