#!/bin/bash
# SocietyPal - DeployHQ Build Commands
# Zero-downtime Laravel deployment with Faker fix

set -e

echo "🚀 DeployHQ Build Pipeline Starting..."

# Install PHP dependencies (with Faker in production)
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Install Node.js dependencies and build assets
echo "🎨 Building frontend assets..."
npm ci --production
npm run build

# Generate optimized autoloader
echo "⚡ Optimizing autoloader..."
composer dump-autoload --optimize

# Cache Laravel configuration
echo "🔧 Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Cache events (Laravel 11+)
php artisan event:cache 2>/dev/null || true

echo "✅ DeployHQ Build Pipeline Complete"