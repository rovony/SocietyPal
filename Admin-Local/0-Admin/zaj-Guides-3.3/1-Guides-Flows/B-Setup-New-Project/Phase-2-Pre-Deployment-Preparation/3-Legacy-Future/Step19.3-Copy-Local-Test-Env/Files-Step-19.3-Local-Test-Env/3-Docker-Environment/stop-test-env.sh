#!/bin/bash

echo "=== Stopping Docker Test Environment ==="

# Stop containers
echo "Stopping Docker containers..."
docker-compose down

echo "✅ Docker test environment stopped successfully!"
echo "💡 To start again, run: ./start-test-env.sh"
