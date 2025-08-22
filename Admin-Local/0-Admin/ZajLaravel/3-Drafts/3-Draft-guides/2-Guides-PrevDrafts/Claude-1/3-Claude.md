# Complete Laravel Zero-Downtime Deployment Flow

```
I've created a comprehensive Laravel Zero-Downtime Deployment Flow that addresses all your requirements. Here are the key improvements:

## **Major Enhancements Included:**

### 1. **Path Variables System**
- Clear definition of `%path-localMachine%`, `%path-server%`, and `%path-Builder-VM%`
- Example paths using your actual project structure
- Dynamic path handling for different build scenarios

### 2. **Visual Step Identification**
- 🟢 **Local Machine** steps
- 🟡 **Builder VM** steps  
- 🔴 **Server** steps
- 🟣 **User-configurable hooks** (1️⃣ Pre-release, 2️⃣ Mid-release, 3️⃣ Post-release)

### 3. **Dependency Management Solutions**
- **Pre-deployment analysis** to detect dev dependencies needed in production
- **Smart installation** that automatically determines whether to include dev dependencies
- **Composer version forcing** to handle servers defaulting to v1
- **Fix commands** provided when issues are detected

### 4. **Complete Edge Case Coverage**
- Shared hosting with `public_html` handling
- Build fallback when builder VM is unavailable
- Zero-downtime migration patterns
- OPcache clearing strategies
- Rollback procedures

### 5. **Verification Steps Throughout**
Each phase includes verification with expected results:
```bash
✅ Dependencies installed
✅ Build validation passed
✅ Application responding correctly
```

### 6. **Universal Compatibility**
- Works for Laravel with or without JavaScript
- Same steps for first and subsequent deployments
- Supports multiple build strategies (Local, DeployHQ, GitHub Actions)

## **Critical Additions to Your Local Setup (Section A & B):**

Before pushing to GitHub, you should add these steps to your local workflow:

### **Step 07.1a: Dependency Analysis** (Add after Step 07)
Run the dependency analysis script to detect issues like Faker in seeders before committing.

### **Step 14.5: Version Lock File** (New step in Section B)
Create a version lock file that captures exact versions of PHP, Composer, Node, and NPM.

### **Step 15.3: Production Build Test** (Enhance existing Step 16)
Add the pre-build validation to catch dev dependency issues before deployment.

## **Key Solutions Implemented:**

### **For Faker Issue:**
```bash
# Automatic detection and fix
if grep -r "Faker" database/seeders; then
    composer remove --dev fakerphp/faker
    composer require fakerphp/faker
fi
```

### **For Composer Version:**
```bash
# Force Composer 2 on any environment
if composer --version | grep -q "version 1"; then
    composer self-update --2
fi
```

### **For Build Artifact Corruption:**
The smart dependency installation (Step 2.2) prevents this by analyzing needs before building.

## **Deployment Config File:**
The flow starts with a `deployment-config.json` that makes everything reusable for different projects - just update the variables.

## **Quick Usage:**

1. **Initial Setup** (one time):
   - Run Steps 0-4 on local machine
   - Fix any dependency issues detected
   - Commit to GitHub

2. **Each Deployment**:
   - Follow Phases 1-10
   - The same steps work for first and subsequent deployments
   - Conditional logic (`mkdir -p`, symlink checks) handles existing resources

3. **If Issues Occur**:
   - Rollback script provided in Critical Pitfalls section
   - Health checks automatically trigger rollback if needed

This complete flow ensures zero-downtime deployments while preventing all the issues you've experienced. The beginner-friendly format with clear paths, verification steps, and expected results makes it easy to follow and debug.

```


## Initial Setup: Path Variables & Configuration

### Define Project Variables [0-init-vars]
**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`

1. **Create deployment configuration file**
   ```bash
   # Path: /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
   
   # Create Admin-Local/Deployment directory
   mkdir -p Admin-Local/Deployment
   cd Admin-Local/Deployment
   
   # Create deployment-config.json
   cat > deployment-config.json << 'EOF'
   {
     "project": {
       "name": "SocietyPal",
       "type": "laravel-with-js",
       "has_frontend": true
     },
     "paths": {
       "local_machine": "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root",
       "server_domain": "/home/u227177893/domains/dev.societypal.com",
       "server_deploy": "/home/u227177893/domains/dev.societypal.com/deploy",
       "builder_vm": "${BUILD_SERVER_PATH:-/tmp/build}"
     },
     "repository": {
       "url": "git@github.com:rovony/SocietyPal.git",
       "branch": "main",
       "commit_start": "${COMMIT_START}",
       "commit_end": "${COMMIT_END}"
     },
     "versions": {
       "php": "8.2",
       "composer": "2",
       "node": "18",
       "npm": "9"
     },
     "deployment": {
       "strategy": "deployHQ",
       "keep_releases": 5,
       "shared_folders": [
         "storage/app/public",
         "storage/logs",
         "storage/framework/cache",
         "storage/framework/sessions",
         "storage/framework/views",
         "public/uploads",
         "public/user-content"
       ]
     }
   }
   EOF
   ```
   
   **Expected Result:**
   ```
   ✅ Created: Admin-Local/Deployment/deployment-config.json
   ✅ All path variables defined and ready for use
   ```

2. **Load variables for session**
   ```bash
   # Source variables into current session
   export PATH_LOCAL_MACHINE="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
   export PATH_SERVER="/home/u227177893/domains/dev.societypal.com/deploy"
   export PATH_BUILDER="${BUILD_SERVER_PATH:-$PATH_LOCAL_MACHINE/build-tmp}"
   export GITHUB_REPO="git@github.com:rovony/SocietyPal.git"
   export DEPLOY_BRANCH="main"
   ```

---

## Pre-Deployment: Local Preparation

### Step 1: Dependency Analysis & Version Detection [1-analyze-deps] 
**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`

1. **Detect and document version requirements**
   ```bash
   cd $PATH_LOCAL_MACHINE
   
   # Create analysis script
   cat > Admin-Local/Deployment/analyze-environment.sh << 'EOF'
   #!/bin/bash
   echo "=== Environment & Dependency Analysis ==="
   
   # 1. Check PHP version and extensions
   echo "📋 PHP Requirements:"
   php -v | head -n1
   php -m | grep -E "pdo|openssl|mbstring|curl|xml|json|bcmath|gd" | tr '\n' ' '
   echo ""
   
   # 2. Check Composer version requirement
   COMPOSER_VERSION=$(composer --version | grep -oP '\d+\.\d+\.\d+' | cut -d. -f1)
   echo "📦 Composer version: $COMPOSER_VERSION"
   
   if grep -q "composer-runtime-api.*^2" composer.json 2>/dev/null; then
       echo "⚠️  Project REQUIRES Composer 2.x"
       if [ "$COMPOSER_VERSION" = "1" ]; then
           echo "❌ CRITICAL: Composer 1 detected, but app needs Composer 2!"
           echo "Fix: composer self-update --2"
       fi
   fi
   
   # 3. Check for dev dependencies used in production
   echo ""
   echo "🔍 Checking dev dependencies usage..."
   
   # Check Faker usage
   if grep -r "Faker\|factory(" database/seeders database/migrations 2>/dev/null | grep -v "//"; then
       echo "⚠️  Faker is used in seeders/migrations"
       if grep '"fakerphp/faker"' composer.json | grep -q "require-dev"; then
           echo "❌ Faker is in require-dev but needed in production!"
           echo "Fix: composer remove --dev fakerphp/faker && composer require fakerphp/faker"
       fi
   fi
   
   # 4. Check Node/NPM versions
   if [ -f "package.json" ]; then
       echo ""
       echo "📦 Node.js Requirements:"
       node -v
       npm -v
   fi
   
   # 5. Generate version lock file
   cat > Admin-Local/Deployment/version-lock.txt << LOCK
   PHP_VERSION=$(php -r "echo PHP_VERSION;")
   COMPOSER_VERSION=$COMPOSER_VERSION
   NODE_VERSION=$(node -v 2>/dev/null)
   NPM_VERSION=$(npm -v 2>/dev/null)
   ANALYZED_AT=$(date)
   LOCK
   
   echo ""
   echo "✅ Analysis complete. Check Admin-Local/Deployment/version-lock.txt"
   EOF
   
   chmod +x Admin-Local/Deployment/analyze-environment.sh
   ./Admin-Local/Deployment/analyze-environment.sh
   ```
   
   **Expected Result:**
   ```
   === Environment & Dependency Analysis ===
   📋 PHP Requirements:
   PHP 8.2.10
   bcmath curl gd json mbstring openssl pdo xml
   
   📦 Composer version: 2
   
   🔍 Checking dev dependencies usage...
   ⚠️  Faker is used in seeders/migrations
   
   ✅ Analysis complete
   ```

2. **Fix dev dependencies if needed**
   ```bash
   # If Faker issue detected, fix it NOW before proceeding
   if grep -q "Faker is in require-dev" Admin-Local/Deployment/analyze-environment.sh.log 2>/dev/null; then
       echo "🔧 Moving Faker to production dependencies..."
       composer remove --dev fakerphp/faker
       composer require fakerphp/faker
       echo "✅ Faker moved to production dependencies"
   fi
   ```

### Step 2: Create Deployment Ignore Files [2-deploy-ignore]
**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`

1. **Create .deployignore file**
   ```bash
   cd $PATH_LOCAL_MACHINE
   
   cat > .deployignore << 'EOF'
   # Deployment-specific ignores (in addition to .gitignore)
   
   # Environment files (use server-specific)
   .env
   .env.local
   .env.testing
   
   # Build artifacts that will be generated on builder
   /public/build/
   /public/hot
   /public/mix-manifest.json
   
   # Development files
   /tests/
   /phpunit.xml
   /.phpunit.result.cache
   /phpstan.neon
   
   # Documentation
   /docs/
   README.md
   
   # CI/CD configs (if not needed on server)
   /.github/
   /.gitlab-ci.yml
   
   # Local deployment files
   /Admin-Local/Deployment/EnvFiles/
   EOF
   
   echo "✅ Created .deployignore"
   ```

### Step 3: Prepare Environment Files [3-env-setup]
**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`

1. **Create environment-specific files**
   ```bash
   cd $PATH_LOCAL_MACHINE
   mkdir -p Admin-Local/Deployment/EnvFiles
   
   # Create production .env
   cp .env.example Admin-Local/Deployment/EnvFiles/.env.production
   
   # Edit production environment
   cat >> Admin-Local/Deployment/EnvFiles/.env.production << 'EOF'
   
   # Production-specific settings
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://dev.societypal.com
   
   # Ensure logging is set appropriately
   LOG_CHANNEL=daily
   LOG_LEVEL=error
   
   # Cache and session drivers
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   EOF
   
   echo "✅ Environment files prepared in Admin-Local/Deployment/EnvFiles/"
   ```

### Step 4: Git Commit Preparation [4-git-prep]
**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`

1. **Ensure lock files are committed**
   ```bash
   cd $PATH_LOCAL_MACHINE
   
   # Check if lock files exist
   if [ ! -f "composer.lock" ]; then
       echo "❌ composer.lock missing! Run: composer install"
       exit 1
   fi
   
   if [ -f "package.json" ] && [ ! -f "package-lock.json" ]; then
       echo "❌ package-lock.json missing! Run: npm install"
       exit 1
   fi
   
   # Add lock files to git
   git add composer.lock package-lock.json 2>/dev/null
   git commit -m "chore: update dependency lock files"
   ```

2. **Commit deployment configurations**
   ```bash
   git add .deployignore
   git add Admin-Local/Deployment/deployment-config.json
   git commit -m "feat: add deployment configuration"
   git push origin main
   
   # Capture commit hash for deployment
   export DEPLOY_COMMIT=$(git rev-parse HEAD)
   echo "📌 Deployment commit: $DEPLOY_COMMIT"
   ```

---

## Phase 1: Prepare Build Environment [build-prep]

### Step 1.1: Pre-Build Environment Preparation [1.1-prebuild]
**Location:** 🟡 Run on Builder VM (or 🟢 Local if no builder)  
**Path:** `%path-Builder-VM%` or `%path-localMachine%/build-tmp`

1. **Initialize build environment**
   ```bash
   # Determine build location
   if [ -z "$BUILD_SERVER_PATH" ]; then
       echo "⚠️  No builder VM available, using local build"
       BUILD_PATH="$PATH_LOCAL_MACHINE/build-tmp"
       BUILD_MODE="local"
   else
       BUILD_PATH="$PATH_BUILDER"
       BUILD_MODE="remote"
   fi
   
   # Create clean build directory
   rm -rf $BUILD_PATH
   mkdir -p $BUILD_PATH
   cd $BUILD_PATH
   
   echo "🏗️ Build mode: $BUILD_MODE"
   echo "📁 Build path: $BUILD_PATH"
   ```

### Step 1.2: Version Alignment [1.2-versions]
**Location:** 🟡 Run on Builder VM (or 🟢 Local)  
**Path:** `%path-Builder-VM%`

1. **Ensure correct versions**
   ```bash
   cd $BUILD_PATH
   
   # Load required versions
   source $PATH_LOCAL_MACHINE/Admin-Local/Deployment/version-lock.txt
   
   # Check PHP version
   CURRENT_PHP=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
   REQUIRED_PHP="8.2"
   if [ "$CURRENT_PHP" != "$REQUIRED_PHP" ]; then
       echo "⚠️  PHP version mismatch: $CURRENT_PHP vs $REQUIRED_PHP"
   fi
   
   # Force Composer 2 if needed
   if composer --version | grep -q "version 1"; then
       echo "🔄 Upgrading to Composer 2..."
       composer self-update --2
   fi
   
   echo "✅ Version check complete"
   ```

### Step 1.3: Repository Clone [1.3-clone]
**Location:** 🟡 Run on Builder VM (or 🟢 Local)  
**Path:** `%path-Builder-VM%`

1. **Clone repository**
   ```bash
   cd $BUILD_PATH
   
   git clone --depth=1 --branch=$DEPLOY_BRANCH $GITHUB_REPO .
   git checkout $DEPLOY_COMMIT
   
   echo "✅ Repository cloned at commit: $(git rev-parse --short HEAD)"
   ```

---

## Phase 2: Build Application [build-app]

### Step 2.1: Cache Restoration [2.1-cache]
**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`

1. **Restore cached dependencies (if available)**
   ```bash
   cd $BUILD_PATH
   
   # Check for cached vendor directory
   if [ -d "/cache/vendor-$PROJECT_NAME" ]; then
       echo "♻️ Restoring cached vendor directory..."
       cp -r /cache/vendor-$PROJECT_NAME vendor
   fi
   
   # Check for cached node_modules
   if [ -d "/cache/node_modules-$PROJECT_NAME" ]; then
       echo "♻️ Restoring cached node_modules..."
       cp -r /cache/node_modules-$PROJECT_NAME node_modules
   fi
   ```

### Step 2.2: Smart Dependency Installation [2.2-deps]
**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`

1. **Analyze and install PHP dependencies**
   ```bash
   cd $BUILD_PATH
   
   # Check if we need dev dependencies in production
   NEEDS_DEV=false
   if grep -r "Faker\|factory(" database/seeders 2>/dev/null | grep -v "//"; then
       if ! grep '"fakerphp/faker"' composer.json | grep -v "require-dev"; then
           echo "⚠️  Dev dependencies needed in production!"
           NEEDS_DEV=true
       fi
   fi
   
   # Install based on analysis
   if [ "$NEEDS_DEV" = true ]; then
       echo "📦 Installing WITH dev dependencies (required for production)"
       composer install \
           --optimize-autoloader \
           --classmap-authoritative \
           --no-interaction
   else
       echo "📦 Installing production-only dependencies"
       composer install \
           --no-dev \
           --optimize-autoloader \
           --classmap-authoritative \
           --no-interaction
   fi
   ```
   
   **Expected Result:**
   ```
   Installing dependencies from lock file
   Package operations: 89 installs, 0 updates, 0 removals
   Generating optimized autoload files
   ✅ Dependencies installed
   ```

2. **Install Node dependencies (if frontend exists)**
   ```bash
   if [ -f "package.json" ]; then
       echo "📦 Installing Node dependencies..."
       npm ci --production=false  # Need dev deps for building
       echo "✅ Node modules installed"
   fi
   ```

### Step 2.3: Asset Compilation [2.3-assets]
**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`

1. **Build frontend assets**
   ```bash
   cd $BUILD_PATH
   
   if [ -f "package.json" ]; then
       # Check for build script
       if grep -q '"build"' package.json; then
           echo "🔨 Building production assets..."
           npm run build
       elif grep -q '"production"' package.json; then
           npm run production
       fi
       
       # Remove dev dependencies after build
       echo "🧹 Cleaning node_modules..."
       rm -rf node_modules
       npm ci --production=true
   fi
   ```

### Step 2.4: Laravel Optimizations [2.4-optimize]
**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`

1. **Cache Laravel configurations**
   ```bash
   cd $BUILD_PATH
   
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   
   # Optimize autoloader
   composer dump-autoload --optimize --classmap-authoritative
   
   echo "✅ Laravel optimizations complete"
   ```

### Step 2.5: Build Validation [2.5-validate]
**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`

1. **Validate build integrity**
   ```bash
   cd $BUILD_PATH
   
   # Check critical files
   CRITICAL_FILES=(
       "bootstrap/app.php"
       "artisan"
       "vendor/autoload.php"
       "bootstrap/cache/config.php"
   )
   
   for file in "${CRITICAL_FILES[@]}"; do
       if [ ! -f "$file" ]; then
           echo "❌ Missing critical file: $file"
           exit 1
       fi
   done
   
   # Test application boots
   php artisan --version || exit 1
   
   echo "✅ Build validation passed"
   ```

---

## Phase 3: Package & Transfer [package-transfer]

### Step 3.1: Build Artifact Creation [3.1-package]
**Location:** 🟡 Run on Builder VM (or 🟢 Local)  
**Path:** `%path-Builder-VM%`

1. **Create deployment package**
   ```bash
   cd $BUILD_PATH
   
   # Read .deployignore and create exclude list
   EXCLUDES="--exclude=.git --exclude=.github --exclude=tests --exclude=.env"
   
   if [ -f ".deployignore" ]; then
       while IFS= read -r line; do
           [[ "$line" =~ ^#.*$ || -z "$line" ]] && continue
           EXCLUDES="$EXCLUDES --exclude=$line"
       done < .deployignore
   fi
   
   # Create tarball
   tar $EXCLUDES -czf ../release-$(date +%Y%m%d%H%M%S).tar.gz .
   
   echo "✅ Deployment package created"
   ```

### Step 3.2: Server Connection [3.2-connect]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

1. **Establish connection and verify**
   ```bash
   # SSH to server
   ssh user@server << 'ENDSSH'
   
   # Check disk space
   DISK_USAGE=$(df -h /home | awk 'NR==2 {print $5}' | sed 's/%//')
   if [ $DISK_USAGE -gt 90 ]; then
       echo "⚠️  Warning: Disk usage is ${DISK_USAGE}%"
   fi
   
   # Verify deployment directory structure
   DEPLOY_PATH="/home/u227177893/domains/dev.societypal.com/deploy"
   mkdir -p $DEPLOY_PATH/{releases,shared,backups}
   
   echo "✅ Server ready for deployment"
   ENDSSH
   ```

### Step 3.3: Release Directory Creation [3.3-release-dir]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

1. **Create timestamped release directory**
   ```bash
   # On server
   RELEASE_ID=$(date +%Y%m%d%H%M%S)
   RELEASE_PATH="$PATH_SERVER/releases/$RELEASE_ID"
   
   mkdir -p "$RELEASE_PATH"
   chmod 755 "$RELEASE_PATH"
   
   echo "📁 Release directory: $RELEASE_PATH"
   ```

### Step 3.4: File Transfer [3.4-transfer]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`

1. **Transfer and extract files**
   ```bash
   # Transfer package to server
   scp release-*.tar.gz user@server:$RELEASE_PATH/
   
   # Extract on server
   ssh user@server << ENDSSH
   cd $RELEASE_PATH
   tar -xzf release-*.tar.gz
   rm release-*.tar.gz
   
   # Set permissions
   chown -R www-data:www-data .
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod -R 775 storage bootstrap/cache
   chmod +x artisan
   
   echo "✅ Files transferred and permissions set"
   ENDSSH
   ```

---

## Phase 4: Configure Release [configure]

### Step 4.1: Shared Resources Setup [4.1-shared]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

1. **Create and link shared directories**
   ```bash
   DEPLOY_PATH="$PATH_SERVER"
   RELEASE_PATH="$DEPLOY_PATH/releases/$RELEASE_ID"
   
   # Create shared directories (first deployment only)
   SHARED_DIRS=(
       "storage/app/public"
       "storage/logs"
       "storage/framework/cache"
       "storage/framework/sessions"
       "storage/framework/views"
       "public/uploads"
       "public/user-content"
   )
   
   for dir in "${SHARED_DIRS[@]}"; do
       mkdir -p "$DEPLOY_PATH/shared/$dir"
   done
   
   # Remove release directories and create symlinks
   cd $RELEASE_PATH
   rm -rf storage
   ln -nfs "$DEPLOY_PATH/shared/storage" storage
   
   # Handle custom upload directories
   if [ -d "public/uploads" ]; then
       rm -rf public/uploads
       ln -nfs "$DEPLOY_PATH/shared/public/uploads" public/uploads
   fi
   
   echo "✅ Shared resources linked"
   ```

### Step 4.2: Environment Configuration [4.2-env]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

1. **Setup environment file**
   ```bash
   # Upload .env if first deployment
   if [ ! -f "$DEPLOY_PATH/shared/.env" ]; then
       # Upload from local Admin-Local/Deployment/EnvFiles/
       scp $PATH_LOCAL_MACHINE/Admin-Local/Deployment/EnvFiles/.env.production \
           user@server:$DEPLOY_PATH/shared/.env
       
       ssh user@server "chmod 600 $DEPLOY_PATH/shared/.env"
   fi
   
   # Link .env to release
   cd $RELEASE_PATH
   ln -nfs "$DEPLOY_PATH/shared/.env" .env
   
   # Verify .env is NOT in release directory as a file
   if [ -f ".env" ] && [ ! -L ".env" ]; then
       echo "⚠️  Removing .env file (should be symlink)"
       rm .env
       ln -nfs "$DEPLOY_PATH/shared/.env" .env
   fi
   
   echo "✅ Environment configured"
   ```

---

## Phase 5: Pre-Release Hooks [5-pre-release] 🟣 1️⃣
**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current` (existing release)

### Step 5.1: Database Backup [5.1-backup]
1. **Create database backup**
   ```bash
   cd $DEPLOY_PATH/current
   
   # Read database credentials from .env
   source .env
   
   # Create backup
   BACKUP_FILE="$DEPLOY_PATH/backups/db-$(date +%Y%m%d%H%M%S).sql"
   mysqldump -h$DB_HOST -u$DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_FILE
   gzip $BACKUP_FILE
   
   echo "✅ Database backed up to: ${BACKUP_FILE}.gz"
   ```

### Step 5.2: Maintenance Mode [5.2-maintenance]
1. **Enable maintenance mode (optional)**
   ```bash
   cd $DEPLOY_PATH/current
   
   # Enable with secret bypass
   php artisan down --render="errors::503" \
                    --secret="deploy-secret-key" \
                    --retry=60
   
   echo "🚧 Maintenance mode enabled"
   ```

---

## Phase 6: Mid-Release Hooks [6-mid-release] 🟣 2️⃣
**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}` (new release)

### Step 6.1: Database Migrations [6.1-migrate]
1. **Run zero-downtime migrations**
   ```bash
   cd $RELEASE_PATH
   
   # Check migration status
   php artisan migrate:status
   
   # Run migrations (zero-downtime pattern)
   php artisan migrate --force --step
   
   echo "✅ Migrations completed"
   ```

### Step 6.2: Cache Preparation [6.2-cache]
1. **Prepare application caches**
   ```bash
   cd $RELEASE_PATH
   
   # Re-cache with production .env
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   echo "✅ Caches prepared"
   ```

### Step 6.3: Health Checks [6.3-health]
1. **Verify new release**
   ```bash
   cd $RELEASE_PATH
   
   # Test database connection
   php artisan tinker --execute="DB::connection()->getPdo() ? 'Connected' : 'Failed'"
   
   # Test migrations
   php artisan migrate:status || exit 1
   
   # Custom health check
   php -r "
       require 'vendor/autoload.php';
       \$app = require_once 'bootstrap/app.php';
       \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
       \$kernel->bootstrap();
       echo 'Application boots successfully';
   "
   
   echo "✅ Health checks passed"
   ```

---

## Phase 7: Atomic Release Switch [7-switch]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

### Step 7.1: Symlink Update [7.1-symlink]
1. **Atomic switch to new release**
   ```bash
   cd $DEPLOY_PATH
   
   # Store old release for potential rollback
   OLD_RELEASE=$(readlink current)
   
   # Atomic symlink update (ZERO-DOWNTIME MOMENT)
   ln -nfs "releases/$RELEASE_ID" current-tmp
   mv -Tf current-tmp current
   
   # Handle public_html if it exists (shared hosting)
   if [ -d "/home/u227177893/public_html" ]; then
       # Backup existing public_html
       if [ ! -L "/home/u227177893/public_html" ]; then
           mv /home/u227177893/public_html /home/u227177893/public_html.backup
       fi
       
       # Create symlink to current/public
       ln -nfs "$DEPLOY_PATH/current/public" /home/u227177893/public_html
   fi
   
   echo "⚡ Release switched atomically"
   echo "Old release: $OLD_RELEASE"
   echo "New release: releases/$RELEASE_ID"
   ```

---

## Phase 8: Post-Release Hooks [8-post-release] 🟣 3️⃣
**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current`

### Step 8.1: Cache Invalidation [8.1-opcache]
1. **Clear OPcache**
   ```bash
   cd $DEPLOY_PATH/current
   
   # Method 1: Using cachetool
   if command -v cachetool &> /dev/null; then
       cachetool opcache:reset --fcgi=/var/run/php/php8.2-fpm.sock
   fi
   
   # Method 2: Web endpoint
   curl -s "https://dev.societypal.com/opcache-clear?token=deploy-secret"
   
   # Method 3: PHP-FPM reload (if possible)
   sudo service php8.2-fpm reload 2>/dev/null || true
   
   echo "✅ OPcache cleared"
   ```

### Step 8.2: Queue & Worker Restart [8.2-workers]
1. **Restart background services**
   ```bash
   cd $DEPLOY_PATH/current
   
   # Restart queue workers
   php artisan queue:restart
   
   # If using Horizon
   if [ -f "config/horizon.php" ]; then
       php artisan horizon:terminate
   fi
   
   # If using Supervisor
   sudo supervisorctl restart all 2>/dev/null || true
   
   echo "✅ Background services restarted"
   ```

### Step 8.3: Final Validation [8.3-validate]
1. **Verify deployment success**
   ```bash
   # Health check endpoint
   HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://dev.societypal.com/health)
   
   if [ "$HTTP_STATUS" != "200" ]; then
       echo "❌ Health check failed! Status: $HTTP_STATUS"
       echo "🔄 Rolling back..."
       
       # Rollback symlink
       ln -nfs "$OLD_RELEASE" current
       
       exit 1
   fi
   
   echo "✅ Application responding correctly"
   ```

### Step 8.4: Exit Maintenance [8.4-up]
1. **Restore access**
   ```bash
   cd $DEPLOY_PATH/current
   
   php artisan up
   
   echo "✅ Application is live!"
   ```

---

## Phase 9: Cleanup [9-cleanup]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

### Step 9.1: Old Releases Cleanup [9.1-releases]
1. **Remove old releases**
   ```bash
   cd $DEPLOY_PATH/releases
   
   # Keep only last 5 releases
   KEEP_RELEASES=5
   RELEASE_COUNT=$(ls -1t | wc -l)
   
   if [ $RELEASE_COUNT -gt $KEEP_RELEASES ]; then
       ls -1t | tail -n +$((KEEP_RELEASES + 1)) | xargs rm -rf
       echo "🧹 Cleaned old releases"
   fi
   ```

### Step 9.2: Cache Cleanup [9.2-cache]
1. **Clean temporary files**
   ```bash
   # Clean build artifacts on builder/local
   if [ "$BUILD_MODE" = "local" ]; then
       rm -rf $PATH_LOCAL_MACHINE/build-tmp
   fi
   
   # Clean old backups (keep 30 days)
   find $DEPLOY_PATH/backups -name "*.gz" -mtime +30 -delete
   
   echo "✅ Cleanup complete"
   ```

---

## Phase 10: Finalization [10-finalize]
**Location:** 🔴 Run on Server  
**Path:** `%path-server%`

### Step 10.1: Deployment Logging [10.1-log]
1. **Record deployment**
   ```bash
   cat >> $DEPLOY_PATH/deployment.log << EOF
   =====================================
   Deployment: $RELEASE_ID
   Timestamp: $(date)
   Commit: $DEPLOY_COMMIT
   Branch: $DEPLOY_BRANCH
   Status: SUCCESS
   Duration: ${SECONDS}s
   =====================================
   EOF
   
   echo "📝 Deployment logged"
   ```

### Step 10.2: Notifications [10.2-notify]
1. **Send notifications**
   ```bash
   # Webhook notification
   curl -X POST https://hooks.slack.com/services/YOUR/WEBHOOK/URL \
        -H 'Content-Type: application/json' \
        -d "{
             \"text\": \"✅ Deployment successful!\",
             \"fields\": [
               {\"title\": \"Release\", \"value\": \"$RELEASE_ID\"},
               {\"title\": \"Commit\", \"value\": \"$DEPLOY_COMMIT\"}
             ]
           }"
   
   echo "📨 Notifications sent"
   ```

---

## Critical Pitfalls & Solutions

### Composer Version Issues
```bash
# Always force Composer 2 on server
alias composer="php /usr/local/bin/composer2"

# Or install per-domain
curl -sS https://getcomposer.org/installer | php -- --install-dir=/home/u227177893/bin --filename=composer2
```

### Dev Dependencies in Production
```bash
# Detection script (run before build)
grep -r "Faker\|factory\|Telescope\|Debugbar" database/ app/ | grep -v "//"

# Fix: Move to production
composer remove --dev package-name
composer require package-name
```

### Shared Hosting Limitations
```bash
# When exec() is disabled
# Create PHP script for artisan commands
<?php
// artisan-web.php
$_SERVER['argv'] = ['artisan', 'migrate', '--force'];
require_once __DIR__.'/artisan';
```

### Zero-Downtime Migrations
```php
// Migration strategy
// Deploy 1: Add new column
Schema::table('users', function ($table) {
    $table->string('email_new')->nullable();
});

// Deploy 2: Backfill data
DB::statement('UPDATE users SET email_new = email');

// Deploy 3: Switch to new column
// Deploy 4: Drop old column
```

### Rollback Procedures
```bash
# Quick rollback script
#!/bin/bash
PREVIOUS=$(ls -1t $DEPLOY_PATH/releases | head -2 | tail -1)
ln -nfs "releases/$PREVIOUS" current
php artisan up
echo "Rolled back to: $PREVIOUS"
```

---

## Build Strategy Variations

### Local Build Only
```bash
# When no builder VM available
BUILD_PATH="$PATH_LOCAL_MACHINE/build-tmp"
# Continue with Phase 2 locally
# Then SCP the tarball to server
```

### DeployHQ Integration
```yaml
# deployHQ.yaml configuration
build_commands:
  - composer install --optimize-autoloader
  - npm ci && npm run build
  
build_cache:
  - vendor
  - node_modules
  
shared_files:
  - .env
  
shared_directories:
  - storage
  - public/uploads
```

### GitHub Actions
```yaml
# .github/workflows/deploy.yml
name: Deploy
on:
  push:
    branches: [main]
    
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Build
        run: |
          composer install --no-dev --optimize-autoloader
          npm ci && npm run build
      - name: Deploy
        run: |
          # Run deployment script
```

---

## Verification Checklist

After deployment, verify:
- [ ] Site loads without errors
- [ ] Database migrations successful
- [ ] User uploads preserved
- [ ] Queue workers running
- [ ] Logs writing correctly
- [ ] OPcache cleared
- [ ] SSL certificate valid
- [ ] Maintenance mode disabled
- [ ] Old releases cleaned
- [ ] Deployment logged

---

## Quick Reference Summary

1. **[0-init-vars]** Define project variables
2. **[1-analyze-deps]** Analyze dependencies & versions
3. **[2-deploy-ignore]** Create deployment ignore files
4. **[3-env-setup]** Prepare environment files
5. **[4-git-prep]** Git commit preparation
6. **[1.1-1.3]** Prepare build environment
7. **[2.1-2.5]** Build application
8. **[3.1-3.4]** Package & transfer
9. **[4.1-4.2]** Configure release
10. **[5.1-5.2]** Pre-release hooks 🟣 1️⃣
11. **[6.1-6.3]** Mid-release hooks 🟣 2️⃣
12. **[7.1]** Atomic switch
13. **[8.1-8.4]** Post-release hooks 🟣 3️⃣
14. **[9.1-9.2]** Cleanup
15. **[10.1-10.2]** Finalization

This flow works identically for first and subsequent deployments using conditional logic (`mkdir -p`, symlink checks, etc.).