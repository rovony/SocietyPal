#!/bin/bash
# SETUP LOCAL TESTING WITH ACT
# Enables local GitHub Actions testing without pushing to GitHub

set -e

echo "🎯 Setting up Local GitHub Actions Testing with Act"
echo "=================================================="

# Check if act is installed
if ! command -v act &> /dev/null; then
    echo "📦 Installing act (GitHub Actions local runner)..."

    # Detect OS and install act
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        if command -v brew &> /dev/null; then
            brew install act
            echo "✅ Act installed via Homebrew"
        else
            echo "❌ Please install Homebrew first: https://brew.sh/"
            exit 1
        fi
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        # Linux
        curl -s https://raw.githubusercontent.com/nektos/act/master/install.sh | sudo bash
        echo "✅ Act installed via install script"
    else
        echo "❌ Unsupported OS. Please install act manually: https://github.com/nektos/act"
        exit 1
    fi
else
    echo "✅ Act already installed: $(act --version)"
fi

# Create act configuration
echo "⚙️ Creating act configuration..."

# Create .actrc file for consistent settings
cat > .actrc << 'EOF'
# Act configuration for Laravel testing
--platform ubuntu-latest=catthehacker/ubuntu:act-latest
--container-architecture linux/amd64
--job ultimate-dry-run
EOF

# Create act secrets file (template)
cat > .secrets << 'EOF'
# Add any secrets your workflow needs
# DEPLOY_HOST=your-server.com
# DEPLOY_USER=deployer
# SSH_PRIVATE_KEY=your-ssh-key
EOF

echo "✅ Act configuration created"
echo ""
echo "📋 Local Testing Commands:"
echo ""
echo "🔧 Test Build Pipeline Only:"
echo "act workflow_dispatch -W ../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml --input test-phase=build-only --input php-version=8.2 --input node-version=18"
echo ""
echo "🔧 Test Runtime Dependencies:"
echo "act workflow_dispatch -W ../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml --input test-phase=runtime-only --input php-version=8.1"
echo ""
echo "🔧 Test SSH Pipeline:"
echo "act workflow_dispatch -W ../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml --input test-phase=ssh-only"
echo ""
echo "🔧 Test Full Pipeline:"
echo "act workflow_dispatch -W ../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml --input test-phase=full --input php-version=8.2 --input php-version-server=8.1 --input node-version=18"
echo ""
echo "🔧 Test Edge Cases Only:"
echo "act workflow_dispatch -W ../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml --input test-phase=edge-cases-only"
echo ""
echo "💡 Pro Tip: Add --dry-run flag to see what would run without actually running it"
echo "💡 Pro Tip: Add --list flag to see all available workflows"
