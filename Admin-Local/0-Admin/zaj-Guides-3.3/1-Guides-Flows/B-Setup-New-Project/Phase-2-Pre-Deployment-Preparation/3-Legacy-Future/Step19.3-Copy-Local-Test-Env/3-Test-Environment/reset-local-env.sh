#!/bin/bash

echo "=== Resetting Local Test Environment ==="

# Remove test workspace
echo "Removing test workspace..."
rm -rf test-workspace

# Remove any generated files
echo "Cleaning generated files..."
rm -f test-workspace/composer.lock 2>/dev/null || true
rm -f test-workspace/package-lock.json 2>/dev/null || true
rm -rf test-workspace/vendor 2>/dev/null || true
rm -rf test-workspace/node_modules 2>/dev/null || true
rm -rf test-workspace/public/build 2>/dev/null || true
rm -rf test-workspace/public/dist 2>/dev/null || true
rm -rf test-workspace/public/assets 2>/dev/null || true

# Recreate test workspace
echo "Recreating test workspace..."
./setup-test-workspace.sh

echo "✅ Local test environment reset successfully!"
echo "🔄 Fresh workspace created and ready for testing"
echo "📁 Test workspace: $(pwd)/test-workspace"
