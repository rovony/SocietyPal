#!/bin/bash

echo "=== Starting Test Environment ==="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Start the test environment
echo "Starting Docker containers..."
docker-compose up -d

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 5

# Check container status
echo "Container status:"
docker-compose ps

echo ""
echo "✅ Test environment started successfully!"
echo "🌐 Nginx available at: http://localhost:8080"
echo "🐳 Containers running:"
echo "   - PHP/Test Environment: laravel-build-test"
echo "   - Composer: composer-test"
echo "   - Node.js: node-test"
echo "   - Nginx: nginx-test"
echo ""
echo "📋 Useful commands:"
echo "   ./test-command.sh <script-name>  - Test a specific build script"
echo "   ./reset-test-env.sh              - Reset the environment"
echo "   ./clean-test-env.sh              - Clean up everything"
echo "   ./stop-test-env.sh               - Stop the environment"
