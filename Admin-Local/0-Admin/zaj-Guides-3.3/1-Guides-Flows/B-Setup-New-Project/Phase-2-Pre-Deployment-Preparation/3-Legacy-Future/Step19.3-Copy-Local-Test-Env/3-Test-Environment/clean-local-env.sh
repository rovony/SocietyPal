#!/bin/bash

echo "=== Cleaning Local Test Environment ==="

# Remove test workspace
echo "Removing test workspace..."
rm -rf test-workspace

# Remove any temporary files
echo "Cleaning temporary files..."
rm -f .env.test 2>/dev/null || true
rm -f test-workspace/.env 2>/dev/null || true

echo "✅ Local test environment cleaned successfully!"
echo "🗑️  All test files removed"
echo "💡 To start fresh, run: ./setup-local-env.sh"
