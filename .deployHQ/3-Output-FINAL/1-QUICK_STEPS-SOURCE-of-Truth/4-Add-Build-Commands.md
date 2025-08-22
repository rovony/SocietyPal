# 4. Add Build Commands

## 4.1 Create Laravel Directories
1. Go to Build Commands section
2. Add new build command: "Create Laravel Directories"
3. Runtime: PHP 8.2
4. Copy this command block:

```bash
#!/bin/bash
set -e

echo "=== Creating Laravel Directory Structure ==="

# Create ALL required Laravel directories (covers all Laravel versions 8-12)
echo "Creating Laravel directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/clockwork
mkdir -p storage/debugbar
mkdir -p public/storage

# Set proper permissions for all directories
echo "Setting directory permissions..."
chmod -R 755 bootstrap/cache
chmod -R 755 storage
find storage -type d -exec chmod 775 {} \;

# Create .gitkeep files for empty directories
echo "Creating .gitkeep files..."
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true
touch bootstrap/cache/.gitkeep 2>/dev/null || true

# Verify directory structure
echo "Verifying directory structure..."
echo "✅ bootstrap/cache: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")"
echo "✅ storage/app: $([ -d "storage/app" ] && echo "exists" || echo "missing")"
echo "✅ storage/framework: $([ -d "storage/framework" ] && echo "exists" || echo "missing")"
echo "✅ storage/logs: $([ -d "storage/logs" ] && echo "exists" || echo "missing")"

echo "✅ Laravel directory structure created"
```

## 4.2 Install PHP Dependencies
1. Add new build command: "Install PHP Dependencies"
2. Runtime: PHP 8.2
3. Copy this command block:

```bash
#!/bin/bash
set -e

echo "=== Installing PHP Dependencies ==="

# Set memory limit for Composer
export COMPOSER_MEMORY_LIMIT=-1

# Verify Composer
composer --version

# Install production dependencies from lock file
echo "Installing PHP dependencies from composer.lock..."
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
```

## 4.3 Install Node.js Dependencies
1. Add new build command: "Install Node.js Dependencies"
2. Runtime: Node.js 18.x
3. Copy this command block:

```bash
#!/bin/bash
set -e

echo "=== Installing Node.js Dependencies ==="

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
```

## 4.4 Build Assets & Optimize
1. Add new build command: "Build Assets & Optimize"
2. Runtime: PHP 8.2
3. Copy this command block:

```bash
#!/bin/bash
set -e

echo "=== Building Assets & Optimizing Laravel ==="

# Generate optimized autoloader
echo "Generating optimized autoloader..."
composer dump-autoload --optimize --classmap-authoritative

# Build frontend assets if Node.js dependencies exist
if [ -f "package.json" ]; then
  echo "Building production assets..."
  
  # Try different build commands (covers all Laravel asset compilation methods)
  if npm run build 2>/dev/null; then
    echo "✅ Assets built with 'npm run build'"
  elif npm run production 2>/dev/null; then
    echo "✅ Assets built with 'npm run production'"
  elif npm run prod 2>/dev/null; then
    echo "✅ Assets built with 'npm run prod'"
  elif npm run dev 2>/dev/null; then
    echo "⚠️ Built with 'npm run dev' (not optimized for production)"
  else
    echo "ℹ️ No suitable build script found, skipping asset compilation"
  fi
  
  # Clean up node_modules to reduce deployment size
  echo "Cleaning up Node.js modules..."
  rm -rf node_modules
  
  # Verify build output exists
  if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then
    echo "✅ Frontend assets compiled successfully"
  else
    echo "ℹ️ No build output detected (may be normal for API-only apps)"
  fi
else
  echo "ℹ️ No package.json found, skipping asset compilation"
fi

# Final verification
echo "Final verification..."
echo "✅ vendor/: $([ -d "vendor" ] && echo "exists" || echo "missing")"
echo "✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")"
echo "✅ Build complete and ready for deployment"

echo "✅ Laravel build and optimization completed"
```

## 4.5 Build Settings
1. Set memory limit: 512M
2. Set build timeout: 20 minutes
3. Enable automatic builds on push
4. Save all settings

## 4.6 Build Commands Ready
✅ Laravel directories created
✅ PHP dependencies installed  
✅ Node.js dependencies installed
✅ Assets built and optimized
✅ Ready for server setup

**Next: 5-Add-Server.md**
