# 08 - Build Commands Library

**Comprehensive collection of build commands for Laravel deployment optimization**

## 📋 Library Overview

This library contains optimized build commands for different deployment scenarios, environments, and hosting providers. All commands are tested and ready for production use.

## 🔧 Command Categories

### 📦 PHP Dependencies
- Production Composer installation
- Autoloader optimization
- Dependency verification
- Security auditing

### 🎨 Frontend Assets
- Node.js dependency management
- Asset compilation and minification
- Build verification
- Cleanup optimization

### ⚡ Laravel Optimization
- Configuration caching
- Route caching
- View compilation
- OPcache optimization

### 🔒 Security Hardening
- File permission setting
- Sensitive file removal
- Environment sanitization
- Security header configuration

## 📚 Available Command Sets

### Standard Build Commands

#### 🏗️ [PHP Production Build](./php-production-build.md)
- Composer production installation
- Autoloader optimization
- Dependency verification

#### 🎨 [Frontend Asset Build](./frontend-asset-build.md)  
- NPM production installation
- Asset compilation
- Build verification and cleanup

#### ⚡ [Laravel Application Optimization](./laravel-optimization.md)
- Configuration caching
- Route caching
- View compilation
- Performance optimization

### Environment-Specific Commands

#### 🧪 [Staging Environment Build](./staging-build.md)
- Debug-enabled configuration
- Development dependencies
- Testing verification

#### 🚀 [Production Environment Build](./production-build.md)
- Security-hardened configuration
- Performance optimizations
- Monitoring setup

### Platform-Specific Commands

#### 🏭 [DeployHQ Build Commands](./deployhq-builds.md)
- DeployHQ-specific optimizations
- Professional build features
- Enterprise configurations

#### 🤖 [GitHub Actions Build Commands](./github-actions-builds.md)
- CI/CD pipeline optimizations
- Action-specific configurations
- Parallel build strategies

#### 💻 [Local Build Commands](./local-builds.md)
- Development environment builds
- Local testing optimizations
- Debug configurations

## 🚀 Quick Start

### Basic Laravel Build Sequence

```bash
# 1. PHP Dependencies
composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction \
  --no-progress

# 2. Frontend Assets (if needed)
if [ -f "package.json" ]; then
  npm ci --only=production
  npm run build
  rm -rf node_modules
fi

# 3. Laravel Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 4. Security Cleanup
rm -f .env.example .env.testing
find . -name "*.log" -delete
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

### Advanced Production Build

```bash
# 1. Environment Preparation
echo "memory_limit = 512M" >> /etc/php/8.2/cli/php.ini
export NODE_OPTIONS="--max_old_space_size=4096"

# 2. Dependencies with Verification
composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --audit

composer dump-autoload --optimize --classmap-authoritative

# 3. Frontend Assets with Optimization
if [ -f "package.json" ]; then
  npm ci --only=production --no-audit --no-fund
  npm run build
  
  # Verify build output
  if [ -d "public/build" ]; then
    echo "✅ Frontend build successful"
    du -sh public/build/*
  fi
  
  rm -rf node_modules
fi

# 4. Laravel Full Optimization
cp .env.example .env.build
echo "APP_KEY=$(openssl rand -base64 32)" >> .env.build
mv .env.build .env

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# OPcache preloading (if configured)
if [ -f "config/opcache.php" ]; then
  php artisan opcache:compile
fi

rm .env

# 5. Security and Performance
rm -f .env.example .env.testing .env.backup
rm -rf tests/ .github/workflows/test.yml
rm -f composer.lock.backup package-lock.json.backup

find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Verify final package
echo "📦 Final build verification:"
php artisan --version
ls -la bootstrap/cache/
du -sh * | sort -hr | head -10
```

## 🛠️ Build Command Templates

### Template Structure

Each command template includes:

```bash
#!/bin/bash
# Build Command: [Name]
# Environment: [staging|production|development]
# Platform: [deployhq|github-actions|local|universal]
# Purpose: [Description]
# Execution Time: [Estimated time]

set -e  # Exit on any error

echo "🚀 Starting [Build Name]..."

# Pre-build checks
echo "🔍 Pre-build verification..."
# Add verification commands

# Main build process
echo "🏗️ Building application..."
# Add main build commands

# Post-build verification
echo "✅ Post-build verification..."
# Add verification commands

echo "🎉 Build completed successfully!"
```

### Error Handling

```bash
# Robust error handling
build_error_handler() {
    echo "❌ Build failed at line $1"
    echo "🔍 Command: $2"
    echo "📋 Exit code: $3"
    
    # Cleanup on failure
    rm -f .env.build .env.temp
    
    exit $3
}

trap 'build_error_handler $LINENO "$BASH_COMMAND" $?' ERR
```

## 📊 Build Optimization Strategies

### Performance Optimization

#### Parallel Processing
```bash
# Parallel composer and npm installs
(composer install --no-dev --optimize-autoloader) &
COMPOSER_PID=$!

if [ -f "package.json" ]; then
    (npm ci --only=production) &
    NPM_PID=$!
fi

# Wait for composer
wait $COMPOSER_PID

# Wait for npm if it was started
if [ ! -z "$NPM_PID" ]; then
    wait $NPM_PID
    npm run build
    rm -rf node_modules
fi
```

#### Caching Strategies
```bash
# Leverage build caches
if [ -d ".composer-cache" ]; then
    export COMPOSER_CACHE_DIR=".composer-cache"
fi

if [ -d ".npm-cache" ]; then
    npm config set cache .npm-cache
fi
```

### Memory Optimization

```bash
# Optimize for large applications
export COMPOSER_MEMORY_LIMIT=-1
export NODE_OPTIONS="--max_old_space_size=4096"

# PHP memory settings
echo "memory_limit = 1G" >> /etc/php/8.2/cli/php.ini
echo "max_execution_time = 300" >> /etc/php/8.2/cli/php.ini
```

## 🔍 Build Verification

### Quality Checks

```bash
# PHP syntax verification
echo "🔍 Verifying PHP syntax..."
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; | grep -v "No syntax errors"

# Composer validation
echo "🔍 Validating Composer configuration..."
composer validate --no-check-publish

# Laravel application verification
echo "🔍 Verifying Laravel application..."
php artisan --version
php artisan about | head -5

# Frontend build verification
if [ -d "public/build" ]; then
    echo "🔍 Verifying frontend build..."
    ls -la public/build/
    echo "📊 Build size: $(du -sh public/build | cut -f1)"
fi
```

### Performance Benchmarks

```bash
# Application performance check
echo "⚡ Performance benchmarks:"
time php artisan route:list > /dev/null
time php artisan config:show > /dev/null

# File system performance
echo "📁 File system metrics:"
echo "Total files: $(find . -type f | wc -l)"
echo "Total size: $(du -sh . | cut -f1)"
echo "Vendor size: $(du -sh vendor/ | cut -f1)"
```

## 📋 Platform-Specific Guides

Choose your deployment platform for optimized build commands:

- **[DeployHQ Professional](./deployhq-builds.md)** - Enterprise build optimization
- **[GitHub Actions](./github-actions-builds.md)** - CI/CD pipeline builds  
- **[Local Development](./local-builds.md)** - Development environment builds
- **[Traditional Hosting](./traditional-hosting-builds.md)** - cPanel/shared hosting builds

## 🆘 Troubleshooting

### Common Build Issues

#### Memory Errors
```bash
# Increase PHP memory limit
echo "memory_limit = 2G" >> /etc/php/8.2/cli/php.ini

# Increase Node.js memory
export NODE_OPTIONS="--max_old_space_size=8192"
```

#### Permission Issues
```bash
# Fix file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

#### Dependency Conflicts
```bash
# Clear composer cache
composer clear-cache

# Remove lock file and reinstall
rm composer.lock
composer install

# Update dependencies
composer update --with-dependencies
```

---

**🎯 Ready to optimize your Laravel builds?**

Choose the appropriate build command set for your deployment platform and environment!

👉 **Popular Choices:**
- [DeployHQ Professional Builds](./deployhq-builds.md) for enterprise deployments
- [GitHub Actions Builds](./github-actions-builds.md) for team collaboration
- [Production Build Optimization](./production-build.md) for performance
