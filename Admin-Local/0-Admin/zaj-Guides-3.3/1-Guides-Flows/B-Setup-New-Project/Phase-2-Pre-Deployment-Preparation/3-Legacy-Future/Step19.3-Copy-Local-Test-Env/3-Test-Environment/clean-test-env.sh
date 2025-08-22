#!/bin/bash

echo "=== Cleaning Test Environment ==="

# Stop and remove containers
echo "Stopping and removing containers..."
docker-compose down --volumes --remove-orphans

# Remove test workspace
echo "Removing test workspace..."
rm -rf test-workspace

# Remove Docker images (optional - uncomment if you want to remove images too)
# echo "Removing Docker images..."
# docker rmi php:8.2-cli composer:2.6 node:18-alpine nginx:alpine 2>/dev/null || true

# Remove any temporary files
echo "Cleaning temporary files..."
rm -f .env.test 2>/dev/null || true
rm -f test-workspace/.env 2>/dev/null || true

echo "✅ Test environment cleaned successfully!"
echo "🗑️  All containers, volumes, and test files removed"
echo "💡 To start fresh, run: ./setup-test-workspace.sh && ./start-test-env.sh"
