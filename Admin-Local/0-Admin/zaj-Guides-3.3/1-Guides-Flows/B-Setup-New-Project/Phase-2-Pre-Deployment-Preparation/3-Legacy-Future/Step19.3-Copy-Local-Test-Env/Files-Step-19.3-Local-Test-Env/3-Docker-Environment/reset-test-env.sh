#!/bin/bash

echo "=== Resetting Docker Test Environment ==="

# Stop containers
echo "Stopping containers..."
docker-compose down

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
cd ../3-Docker-Environment

# Start environment again
echo "Starting environment..."
docker-compose up -d

echo "✅ Docker test environment reset successfully!"
echo "🔄 Fresh workspace created and ready for testing"
echo "🌐 Nginx available at: http://localhost:8080"
