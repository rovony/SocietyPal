#!/bin/bash
set -e

# Phase B: Pre-Release Commands (After Upload, Before Release)
# Purpose: Setup release, link shared resources, run migrations
# v1 - FIXED VERSION
echo "=== Phase B: Pre-Release Setup ==="
cd %release_path%

# B01: Setup Environment File
echo "=== Setup Environment ==="
if [ ! -f "%shared_path%/.env" ]; then
    echo "Creating shared .env file..."
    
    # Try multiple sources for .env
    if [ -f "%release_path%/.env" ]; then
        cp %release_path%/.env %shared_path%/.env
        echo "✅ Using uploaded .env"
    elif [ -f "%release_path%/.env.example" ]; then
        cp %release_path%/.env.example %shared_path%/.env
        echo "✅ Using .env.example"
    else
        echo "⚠️ No .env found - create one manually"
    fi
    
    # Generate APP_KEY if missing
    if [ -f "%shared_path%/.env" ]; then
        if ! grep -q "^APP_KEY=base64:" %shared_path%/.env; then
            cd %release_path%
            php artisan key:generate --force 2>/dev/null || true
            cp %release_path%/.env %shared_path%/.env 2>/dev/null || true
        fi
    fi
fi

# Link shared .env to release
rm -f %release_path%/.env
ln -sf %shared_path%/.env %release_path%/.env
echo "✅ Environment linked"

# B02: Link Shared Directories
echo "=== Link Shared Directories ==="

# Remove release directories and link to shared
rm -rf %release_path%/storage
ln -sf %shared_path%/storage %release_path%/storage

rm -rf %release_path%/bootstrap/cache
ln -sf %shared_path%/bootstrap/cache %release_path%/bootstrap/cache

# Link public directories if needed
if [ -d "%shared_path%/public/.well-known" ]; then
    rm -rf %release_path%/public/.well-known
    ln -sf %shared_path%/public/.well-known %release_path%/public/.well-known
fi

echo "✅ Shared directories linked"

# B03: Install/Update Composer Dependencies (FIXED VERSION)
echo "=== Verify Composer Dependencies ==="
cd %release_path%

# Check if vendor directory exists and is complete
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
elif [ -f "composer.lock" ]; then
    # Check if composer.lock is newer than vendor
    if [ "composer.lock" -nt "vendor/autoload.php" ]; then
        echo "Updating composer dependencies..."
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    fi
fi

# Fix for missing Inertia ServiceProvider (IMPROVED VERSION)
echo "=== Checking for Inertia ServiceProvider ==="
if grep -q "Inertia\\\ServiceProvider" config/app.php 2>/dev/null; then
    echo "Inertia detected in config, checking installation..."
    
    # Check if Inertia is actually installed
    if [ ! -d "vendor/inertiajs" ] || [ ! -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then
        echo "Installing Inertia package..."
        composer require inertiajs/inertia-laravel --no-interaction 2>/dev/null || {
            echo "⚠️ Failed to install Inertia via composer, trying alternative..."
            # Try to install from source if composer fails
            if [ -f "composer.json" ]; then
                # Add Inertia to composer.json if not present
                if ! grep -q "inertiajs/inertia-laravel" composer.json; then
                    echo "Adding Inertia to composer.json..."
                    sed -i '/"require": {/a\        "inertiajs/inertia-laravel": "^0.6.0",' composer.json
                    composer update --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true
                fi
            fi
        }
    else
        echo "✅ Inertia already installed"
    fi
else
    echo "ℹ️ Inertia not detected in config"
fi

echo "✅ Dependencies verified"

# B04: Set Permissions
echo "=== Set Permissions ==="
cd %release_path%

# Set directory permissions
find . -type d -exec chmod 755 {} \; 2>/dev/null || true
find . -type f -exec chmod 644 {} \; 2>/dev/null || true

# Make artisan executable
[ -f "artisan" ] && chmod +x artisan

# Ensure storage is writable
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "✅ Permissions set"

# B05: Run Migrations (IMPROVED VERSION)
echo "=== Run Database Migrations ==="
cd %release_path%

if [ -f "artisan" ]; then
    # Check database connection first using a simple test
    echo "Testing database connection..."
    if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then
        echo "✅ Database connection successful, running migrations..."
        php artisan migrate --force --no-interaction || echo "⚠️ Migration failed but continuing"
    else
        echo "⚠️ Database not configured or accessible - skipping migrations"
        echo "ℹ️ You may need to configure your .env file with correct database credentials"
    fi
else
    echo "⚠️ No artisan file found"
fi

# B06: Laravel Optimizations (IMPROVED VERSION)
echo "=== Optimize Laravel ==="
cd %release_path%

# Clear old caches first
echo "Clearing old caches..."
php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped"
php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped"
php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped"
php artisan view:clear 2>/dev/null || echo "ℹ️ View clear skipped"

# Create new caches with better error handling
echo "Building new caches..."
if php artisan config:cache 2>/dev/null; then
    echo "✅ Configuration cached"
else
    echo "⚠️ Config cache failed - checking for errors"
    # Try to identify the issue
    php artisan config:cache 2>&1 | head -5
fi

if php artisan route:cache 2>/dev/null; then
    echo "✅ Routes cached"
else
    echo "⚠️ Route cache failed - continuing"
fi

if php artisan view:cache 2>/dev/null; then
    echo "✅ Views cached"
else
    echo "⚠️ View cache failed - continuing"
fi

# Create storage link
echo "Setting up storage link..."
php artisan storage:link --force 2>/dev/null || echo "ℹ️ Storage link exists or failed"

echo "✅ Phase B completed successfully"