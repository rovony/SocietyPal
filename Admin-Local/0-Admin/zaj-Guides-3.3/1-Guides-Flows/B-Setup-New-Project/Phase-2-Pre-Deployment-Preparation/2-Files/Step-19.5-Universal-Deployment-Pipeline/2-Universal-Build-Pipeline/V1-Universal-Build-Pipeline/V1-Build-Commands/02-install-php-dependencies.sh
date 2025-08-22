#!/bin/bash
set -e

echo "=== Installing PHP Dependencies ==="

# Set memory limit for Composer
export COMPOSER_MEMORY_LIMIT=-1

# Verify Composer
composer --version

# Install production dependencies
echo "Installing PHP dependencies..."
composer install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction \
  --prefer-dist \
  --no-progress

# Verify installation
echo "Verifying Composer installation..."
if composer validate --no-check-publish --quiet; then
  echo "✅ Composer dependencies valid"
else
  echo "⚠️ Composer validation warnings (continuing)"
fi

echo "✅ PHP dependencies installed"
