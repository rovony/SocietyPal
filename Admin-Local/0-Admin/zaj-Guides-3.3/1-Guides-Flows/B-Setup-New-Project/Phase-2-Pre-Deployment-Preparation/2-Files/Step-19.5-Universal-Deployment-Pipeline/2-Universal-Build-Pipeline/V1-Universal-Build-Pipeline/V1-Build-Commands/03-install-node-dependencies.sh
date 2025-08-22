#!/bin/bash
set -e

echo "=== Installing Node.js Dependencies ==="

# Note: npm (npm ci) Uses package-lock.json
# Check if frontend build is required
if [ ! -f "package.json" ]; then
  echo "ℹ️ No package.json found, skipping Node.js dependencies"
  exit 0
fi

# Verify Node.js and npm
node --version
npm --version

# Install dependencies from package-lock.json (production + dev for building)
echo "Installing Node.js dependencies from package-lock.json..."
npm ci --no-audit --no-fund

echo "✅ Node.js dependencies installed"
