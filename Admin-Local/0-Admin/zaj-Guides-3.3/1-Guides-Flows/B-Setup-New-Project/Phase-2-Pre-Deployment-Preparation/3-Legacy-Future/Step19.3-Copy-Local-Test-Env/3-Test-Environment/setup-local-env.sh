#!/bin/bash

echo "=== Setting up Local Test Environment (No Docker) ==="

# Check if required tools are available
echo "Checking required tools..."

# Check PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
    echo "✅ PHP found: $(php -v | head -n1)"
else
    echo "❌ PHP not found. Please install PHP 8.1+ first."
    echo "   macOS: brew install php"
    echo "   Ubuntu: sudo apt install php8.2-cli"
    echo "   Windows: Download from https://windows.php.net/"
    exit 1
fi

# Check Composer
if command -v composer &> /dev/null; then
    echo "✅ Composer found: $(composer --version | head -n1)"
else
    echo "❌ Composer not found. Please install Composer first."
    echo "   https://getcomposer.org/download/"
    exit 1
fi

# Check Node.js
if command -v node &> /dev/null; then
    echo "✅ Node.js found: $(node --version)"
else
    echo "⚠️  Node.js not found. Some build commands will be skipped."
    echo "   macOS: brew install node"
    echo "   Ubuntu: sudo apt install nodejs npm"
    echo "   Windows: Download from https://nodejs.org/"
fi

# Check npm
if command -v npm &> /dev/null; then
    echo "✅ npm found: $(npm --version)"
else
    echo "⚠️  npm not found. Some build commands will be skipped."
fi

echo ""
echo "Setting up local test workspace..."

# Create test workspace
./setup-test-workspace.sh

echo ""
echo "✅ Local test environment ready!"
echo "📁 Test workspace: $(pwd)/test-workspace"
echo ""
echo "🚀 To test build commands:"
echo "   ./test-local-command.sh <script-name>"
echo ""
echo "🔄 To reset: ./reset-local-env.sh"
echo "🧹 To clean: ./clean-local-env.sh"
