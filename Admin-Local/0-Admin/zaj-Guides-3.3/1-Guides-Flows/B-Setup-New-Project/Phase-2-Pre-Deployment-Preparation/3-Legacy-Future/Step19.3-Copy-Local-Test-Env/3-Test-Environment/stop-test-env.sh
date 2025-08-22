#!/bin/bash

echo "=== Stopping Test Environment ==="

# Stop containers
echo "Stopping Docker containers..."
docker-compose down

echo "✅ Test environment stopped successfully!"
echo "💡 To start again, run: ./start-test-env.sh"
