#!/bin/bash

if [ $# -eq 0 ]; then
    echo "Usage: ./test-local-command.sh <script-name>"
    echo ""
    echo "Available scripts:"
    echo "  01-create-laravel-directories"
    echo "  02-install-php-dependencies"
    echo "  03-install-node-dependencies"
    echo "  04-build-assets-optimize"
    echo ""
    echo "Example: ./test-local-command.sh 01-create-laravel-directories"
    exit 1
fi

SCRIPT_NAME=$1
SCRIPT_PATH="../../Step-19.2-SSH-Pipeline/2-Universal-SSH-Pipeline/V1-Universal-SSH-Pipeline/V1-Build-Commands/${SCRIPT_NAME}.sh"

if [ ! -f "$SCRIPT_PATH" ]; then
    echo "❌ Script not found: $SCRIPT_PATH"
    exit 1
fi

echo "=== Testing Build Command Locally: $SCRIPT_NAME ==="

# Check if test workspace exists
if [ ! -d "../2-Playground/test-workspace" ]; then
    echo "❌ Test workspace not found. Set up local environment first:"
    echo "   ./setup-local-env.sh"
    exit 1
fi

# Copy the script to the test workspace
echo "Copying script to test workspace..."
cp "$SCRIPT_PATH" "../2-Playground/test-workspace/"

# Make it executable
chmod +x "../2-Playground/test-workspace/${SCRIPT_NAME}.sh"

# Change to test workspace and run the script
echo "Running script in test workspace..."
cd "../2-Playground/test-workspace"

# Execute the script
echo "Executing: ./${SCRIPT_NAME}.sh"
./${SCRIPT_NAME}.sh

# Return to original directory
cd ../../4-Local-Environment

echo ""
echo "✅ Local command testing completed!"
echo "📁 Check ../2-Playground/test-workspace/ for results"
echo "🔄 To reset and test again: ./reset-local-env.sh"
