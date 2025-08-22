#!/bin/bash

echo "=== Resetting Local Test Environment ==="

# Remove test workspace
echo "Removing test workspace..."
rm -rf "../2-Playground/test-workspace"

# Remove any generated files
echo "Cleaning generated files..."
rm -f "../2-Playground/test-workspace/composer.lock" 2>/dev/null || true
rm -f "../2-Playground/test-workspace/package-lock.json" 2>/dev/null || true
rm -rf "../2-Playground/test-workspace/vendor" 2>/dev/null || true
rm -rf "../2-Playground/test-workspace/node_modules" 2>/dev/null || true
rm -rf "../2-Playground/test-workspace/public/build" 2>/dev/null || true
rm -rf "../2-Playground/test-workspace/public/dist" 2>/dev/null || true
rm -rf "../2-Playground/test-workspace/public/assets" 2>/dev/null || true

# Recreate test workspace
echo "Recreating test workspace..."
cd ../1-Setup-Files
./setup-test-workspace.sh
cd ../4-Local-Environment

echo "✅ Local test environment reset successfully!"
echo "🔄 Fresh workspace created and ready for testing"
echo "📁 Test workspace: ../2-Playground/test-workspace"
