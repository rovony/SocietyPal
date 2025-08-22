#!/bin/bash

# Manual Composer Lock Fix
# Purpose: Manually update composer.lock to include Faker in production dependencies

set -e

echo "🔧 Manual Composer Lock Fix..."
echo "=============================="

# Verify we're in the project root
if [[ ! -f "composer.json" ]]; then
    echo "❌ Error: composer.json not found. Run this script from project root."
    exit 1
fi

# Backup current lock file
if [[ -f "composer.lock" ]]; then
    cp composer.lock composer.lock.backup
    echo "📦 Backed up existing composer.lock"
fi

echo "⚠️ PHP/Composer not available locally."
echo "📝 Manual steps to fix composer.lock:"
echo ""
echo "1. On a system with PHP/Composer installed, run:"
echo "   composer update --lock --no-install"
echo ""
echo "2. Or delete composer.lock and let DeployHQ regenerate it:"
echo "   rm composer.lock"
echo "   git add composer.lock"
echo "   git commit -m 'Remove composer.lock - let DeployHQ regenerate'"
echo ""
echo "3. Update DeployHQ build command to handle missing lock file:"

cat > updated-build-commands.sh << 'EOF'
#!/bin/bash
# SocietyPal - DeployHQ Build Commands (Lock File Safe)
set -e

echo "🚀 DeployHQ Build Pipeline Starting..."

# Handle missing or outdated lock file
if [[ ! -f "composer.lock" ]] || ! composer validate --no-check-all --strict; then
    echo "🔄 Regenerating composer.lock..."
    composer update --lock --no-install
fi

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
EOF

chmod +x updated-build-commands.sh
echo "✅ Created updated-build-commands.sh with lock file handling"

echo ""
echo "🎯 Recommended approach:"
echo "   Option A: Delete composer.lock and let DeployHQ regenerate it"
echo "   Option B: Use updated build commands that handle lock file issues"
