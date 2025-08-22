#!/bin/bash

echo "=== Resetting Test Environment ==="

# Stop containers
echo "Stopping containers..."
docker-compose down

# Remove test workspace
echo "Removing test workspace..."
rm -rf test-workspace

# Remove any generated files
echo "Cleaning generated files..."
rm -f test-workspace/composer.lock
rm -f test-workspace/package-lock.json
rm -rf test-workspace/vendor
rm -rf test-workspace/node_modules
rm -rf test-workspace/public/build
rm -rf test-workspace/public/dist
rm -rf test-workspace/public/assets

# Recreate test workspace
echo "Recreating test workspace..."
./setup-test-workspace.sh

# Start environment again
echo "Starting environment..."
docker-compose up -d

echo "✅ Test environment reset successfully!"
echo "🔄 Fresh workspace created and ready for testing"
echo "🌐 Nginx available at: http://localhost:8080"
