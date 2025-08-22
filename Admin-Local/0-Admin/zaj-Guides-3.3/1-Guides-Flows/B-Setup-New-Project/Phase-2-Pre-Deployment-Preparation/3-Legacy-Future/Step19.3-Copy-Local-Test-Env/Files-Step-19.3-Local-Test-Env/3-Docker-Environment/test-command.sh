#!/bin/bash

if [ $# -eq 0 ]; then
    echo "Usage: ./test-command.sh <script-name>"
    echo ""
    echo "Available scripts:"
    echo "  01-create-laravel-directories"
    echo "  02-install-php-dependencies"
    echo "  03-install-node-dependencies"
    echo "  04-build-assets-optimize"
    echo ""
    echo "Example: ./test-command.sh 01-create-laravel-directories"
    exit 1
fi

SCRIPT_NAME=$1
SCRIPT_PATH="../../Step-19.2-SSH-Pipeline/2-Universal-SSH-Pipeline/V1-Universal-SSH-Pipeline/V1-Build-Commands/${SCRIPT_NAME}.sh"

if [ ! -f "$SCRIPT_PATH" ]; then
    echo "❌ Script not found: $SCRIPT_PATH"
    exit 1
fi

echo "=== Testing Build Command: $SCRIPT_NAME ==="

# Check if environment is running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Test environment is not running. Start it first with: ./start-test-env.sh"
    exit 1
fi

# Copy the script to the test workspace
echo "Copying script to test environment..."
cp "$SCRIPT_PATH" "../2-Playground/test-workspace/"

# Make it executable
chmod +x "../2-Playground/test-workspace/${SCRIPT_NAME}.sh"

# Execute the script in the appropriate container
case $SCRIPT_NAME in
    01-create-laravel-directories)
        echo "Running in PHP container..."
        docker exec -it laravel-build-test bash -c "cd /workspace && ./${SCRIPT_NAME}.sh"
        ;;
    02-install-php-dependencies)
        echo "Running in Composer container..."
        docker exec -it composer-test bash -c "cd /workspace && ./${SCRIPT_NAME}.sh"
        ;;
    03-install-node-dependencies)
        echo "Running in Node.js container..."
        docker exec -it node-test sh -c "cd /workspace && ./${SCRIPT_NAME}.sh"
        ;;
    04-build-assets-optimize)
        echo "Running in PHP container..."
        docker exec -it laravel-build-test bash -c "cd /workspace && ./${SCRIPT_NAME}.sh"
        ;;
    *)
        echo "❌ Unknown script: $SCRIPT_NAME"
        exit 1
        ;;
esac

echo ""
echo "✅ Command testing completed!"
echo "📁 Check ../2-Playground/test-workspace/ for results"
echo "🔄 To reset and test again: ./reset-test-env.sh"
