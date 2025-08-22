# Universal Laravel Deployment Flow - Enhanced Version

## **SECTION A MODIFICATIONS: Project Setup**

### **NEW Step 03.2: Environment and Version Alignment Check**
**Purpose:** Detect and document all version requirements before any code work
**When:** Immediately after setting up local project structure

```bash
#!/bin/bash
# save as: check-environment-versions.sh

echo "=== Environment Version Detection ==="

# 1. Detect PHP version requirement
PHP_VERSION=$(grep -oP '"php":\s*"[^"]*"' composer.json 2>/dev/null | cut -d'"' -f4)
echo "Required PHP: ${PHP_VERSION:-Not specified}"
echo "Current PHP: $(php -v | head -n1)"

# 2. Detect Composer version requirement
if [ -f "composer.lock" ]; then
    COMPOSER_LOCK_VERSION=$(grep -m1 '"plugin-api-version"' composer.lock | cut -d'"' -f4)
    echo "Composer lock version: ${COMPOSER_LOCK_VERSION}"
fi
echo "Current Composer: $(composer --version)"

# 3. Detect if Composer 2 is required
if grep -q "composer-runtime-api" composer.json 2>/dev/null || \
   grep -q '"composer/composer":\s*"^2' composer.json 2>/dev/null; then
    echo "⚠️  This project requires Composer 2.x"
    NEEDS_COMPOSER_2=true
fi

# 4. Create version requirements file
cat > Admin-Local/1-CurrentProject/version-requirements.txt << EOF
PHP_REQUIRED=${PHP_VERSION:-8.1.*}
COMPOSER_REQUIRED=${NEEDS_COMPOSER_2:+2.x}
NODE_REQUIRED=$(grep -oP '"node":\s*"[^"]*"' package.json 2>/dev/null | cut -d'"' -f4)
NPM_REQUIRED=$(grep -oP '"npm":\s*"[^"]*"' package.json 2>/dev/null | cut -d'"' -f4)
DETECTED_AT=$(date)
EOF

# 5. Force Composer 2 if needed
if [ "$NEEDS_COMPOSER_2" = true ] && composer --version | grep -q "version 1"; then
    echo "🔄 Upgrading to Composer 2..."
    composer self-update --2
fi
```

### **MODIFIED Step 07: Install Project Dependencies (Move Earlier)**
**Change:** Move this step BEFORE Step 06.1 to ensure dependencies are available for analysis

### **NEW Step 07.1: Dependency Usage Analysis**
**Purpose:** Detect which dev dependencies are actually used in production code
**When:** Right after installing dependencies

```bash
#!/bin/bash
# save as: analyze-dependency-usage.sh

echo "=== Analyzing Dev Dependency Usage in Production Code ==="

# 1. Get list of dev dependencies
DEV_DEPS=$(composer show --dev --name-only 2>/dev/null)

# 2. Create analysis report
mkdir -p Admin-Local/1-CurrentProject/dependency-analysis
REPORT="Admin-Local/1-CurrentProject/dependency-analysis/dev-deps-in-production.md"

echo "# Dev Dependencies Used in Production Code" > $REPORT
echo "Generated: $(date)" >> $REPORT
echo "" >> $REPORT

# 3. Check for Faker usage in seeders/migrations
if grep -r "Faker\\\Factory\|faker()" database/seeders database/migrations 2>/dev/null | grep -v "// *test\|#\|/\*"; then
    echo "## ⚠️ Faker Usage Detected" >> $REPORT
    echo "Faker is used in seeders/migrations. Move to production dependencies:" >> $REPORT
    echo '```json' >> $REPORT
    echo '"fakerphp/faker": "^1.23"' >> $REPORT
    echo '```' >> $REPORT
    echo "" >> $REPORT
    MOVE_TO_PROD+=("fakerphp/faker")
fi

# 4. Check for IDE Helper usage in production commands
if grep -r "ide-helper:generate" app/Console 2>/dev/null; then
    echo "## ⚠️ IDE Helper Usage Detected" >> $REPORT
    echo "IDE Helper commands found in console. Consider moving to production." >> $REPORT
    MOVE_TO_PROD+=("barryvdh/laravel-ide-helper")
fi

# 5. Check for Telescope usage
if [ -f "config/telescope.php" ] && grep -q "TELESCOPE_ENABLED.*true" .env.production 2>/dev/null; then
    echo "## ⚠️ Telescope Enabled in Production" >> $REPORT
    echo "Telescope is enabled in production. Move to production dependencies." >> $REPORT
    MOVE_TO_PROD+=("laravel/telescope")
fi

# 6. Check for Debugbar usage
if [ -f "config/debugbar.php" ] && grep -q "DEBUGBAR_ENABLED.*true" .env.production 2>/dev/null; then
    echo "## ⚠️ Debugbar Enabled in Production" >> $REPORT
    echo "Debugbar is enabled in production. Move to production dependencies." >> $REPORT
    MOVE_TO_PROD+=("barryvdh/laravel-debugbar")
fi

# 7. Generic check for any dev package classes used in app/ directory
for package in $DEV_DEPS; do
    NAMESPACE=$(echo $package | sed 's/-/\\/g' | sed 's/\//\\/g')
    if grep -r "use.*$NAMESPACE\|$NAMESPACE::" app/ database/seeders database/migrations 2>/dev/null | grep -v "// *test\|#\|/\*"; then
        echo "## ⚠️ $package Usage Detected" >> $REPORT
        echo "This dev dependency is used in production code." >> $REPORT
        echo "" >> $REPORT
        MOVE_TO_PROD+=("$package")
    fi
done

# 8. Generate composer command to fix
if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    echo "## 🔧 Fix Commands" >> $REPORT
    echo '```bash' >> $REPORT
    echo "# Remove from require-dev" >> $REPORT
    for pkg in "${MOVE_TO_PROD[@]}"; do
        echo "composer remove --dev $pkg" >> $REPORT
    done
    echo "" >> $REPORT
    echo "# Add to require" >> $REPORT
    for pkg in "${MOVE_TO_PROD[@]}"; do
        echo "composer require $pkg" >> $REPORT
    done
    echo '```' >> $REPORT
    
    # Auto-fix option
    echo ""
    echo "⚠️  Found ${#MOVE_TO_PROD[@]} dev dependencies used in production!"
    echo "Review the report at: $REPORT"
    read -p "Auto-fix now? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        for pkg in "${MOVE_TO_PROD[@]}"; do
            composer remove --dev $pkg
            composer require $pkg
        done
        echo "✅ Dependencies moved to production"
    fi
else
    echo "✅ No dev dependencies found in production code" >> $REPORT
fi
```

---

## **SECTION B MODIFICATIONS: Pre-Deployment Preparation**

### **NEW Step 14.1: Composer Version Strategy Setup**
**Purpose:** Configure Composer for production compatibility
**When:** Before Step 15

```bash
#!/bin/bash
# save as: setup-composer-strategy.sh

echo "=== Setting Up Composer Production Strategy ==="

# 1. Check if composer.json needs modification for v2
if ! grep -q '"config"' composer.json; then
    echo "Adding config section to composer.json..."
    jq '. + {"config": {}}' composer.json > composer.tmp && mv composer.tmp composer.json
fi

# 2. Add production optimizations
jq '.config += {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true,
    "classmap-authoritative": true,
    "apcu-autoloader": true,
    "platform-check": false
}' composer.json > composer.tmp && mv composer.tmp composer.json

# 3. Handle plugin compatibility for Composer 2
if composer --version | grep -q "version 2"; then
    # Get all plugins and add to allow-plugins
    PLUGINS=$(composer show -s | grep "composer-plugin" -B2 | grep "name" | cut -d: -f2 | tr -d ' ')
    
    for plugin in $PLUGINS; do
        jq --arg plugin "$plugin" '.config."allow-plugins"[$plugin] = true' composer.json > composer.tmp
        mv composer.tmp composer.json
    done
fi

# 4. Add platform requirements
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
jq --arg ver "$PHP_VERSION" '.config.platform.php = $ver' composer.json > composer.tmp
mv composer.tmp composer.json

echo "✅ Composer configured for production"
```

### **NEW Step 15.2: Production Dependency Verification**
**Purpose:** Final check that all required dependencies are in production
**When:** After installing dependencies

```bash
#!/bin/bash
# save as: verify-production-deps.sh

echo "=== Verifying Production Dependencies ==="

# 1. Simulate production install
rm -rf vendor-test
COMPOSER_VENDOR_DIR=vendor-test composer install --no-dev --no-scripts

# 2. Test critical classes
CRITICAL_CLASSES=(
    "Faker\Factory"           # If seeders use it
    "Illuminate\Support\Str"  # Core Laravel
    "Illuminate\Foundation\Application"
)

MISSING=()
for class in "${CRITICAL_CLASSES[@]}"; do
    CLASS_FILE="vendor-test/$(echo $class | tr '\\' '/' | tr '[:upper:]' '[:lower:]').php"
    if [ ! -f "$CLASS_FILE" ]; then
        # Check if it's a namespaced class
        NAMESPACE_DIR="vendor-test/$(echo $class | rev | cut -d'\' -f2- | rev | tr '\\' '/' | tr '[:upper:]' '[:lower:]')"
        if [ ! -d "$NAMESPACE_DIR" ]; then
            MISSING+=("$class")
        fi
    fi
done

# 3. Test migrations and seeders
if [ -f "artisan" ]; then
    echo "Testing migrations and seeders..."
    COMPOSER_VENDOR_DIR=vendor-test php artisan migrate:status --no-interaction 2>&1 | tee migration-test.log
    
    if grep -q "Class.*not found" migration-test.log; then
        echo "❌ Missing classes detected in migrations/seeders!"
        grep "Class.*not found" migration-test.log
        
        # Extract missing class names
        MISSING_FROM_TEST=$(grep -oP "Class \"\K[^\"]*" migration-test.log)
        for miss in $MISSING_FROM_TEST; do
            PACKAGE=$(composer search --no-interaction "$miss" 2>/dev/null | head -1 | cut -d' ' -f1)
            if [ ! -z "$PACKAGE" ]; then
                echo "  Suggested fix: composer require $PACKAGE"
            fi
        done
    fi
fi

# 4. Cleanup
rm -rf vendor-test migration-test.log

# 5. Report
if [ ${#MISSING[@]} -gt 0 ]; then
    echo "❌ Missing production dependencies detected!"
    for miss in "${MISSING[@]}"; do
        echo "  - $miss"
    done
    exit 1
else
    echo "✅ All production dependencies verified"
fi
```

### **MODIFIED Step 16: Test Build Process**
**Add this validation before the existing test:**

```bash
# NEW: Pre-build validation
echo "=== Pre-Build Validation ==="

# 1. Check for non-dev seeders that use Faker
if grep -r "extends Seeder" database/seeders | grep -v "Test\|Development"; then
    FILES=$(grep -l "Faker\|factory(" database/seeders/*.php | grep -v "Test\|Development")
    if [ ! -z "$FILES" ]; then
        echo "⚠️  Production seeders using Faker detected:"
        echo "$FILES"
        
        # Check if Faker is in production deps
        if ! grep -q '"fakerphp/faker"' composer.json | grep -v "require-dev"; then
            echo "❌ Faker not in production dependencies!"
            echo "Run: composer require fakerphp/faker"
            exit 1
        fi
    fi
fi

# 2. Continue with existing build test...
```

---

## **SECTION C MODIFICATIONS: Build and Deploy**

### **MODIFIED Phase 1.2: Build Environment Setup**
**Add Composer version detection and switching:**

```bash
# Enhanced version alignment
echo "=== Build Environment Version Alignment ==="

# 1. Read requirements from project
source Admin-Local/1-CurrentProject/version-requirements.txt

# 2. Switch Composer version if needed
CURRENT_COMPOSER=$(composer --version | grep -oP '\d+\.\d+\.\d+' | cut -d. -f1)
REQUIRED_COMPOSER=${COMPOSER_REQUIRED:-2}

if [ "$CURRENT_COMPOSER" != "$REQUIRED_COMPOSER" ]; then
    echo "Switching from Composer $CURRENT_COMPOSER to $REQUIRED_COMPOSER"
    
    # Use specific version binary if available
    if [ -f "/usr/local/bin/composer$REQUIRED_COMPOSER" ]; then
        alias composer="/usr/local/bin/composer$REQUIRED_COMPOSER"
    else
        composer self-update --$REQUIRED_COMPOSER
    fi
fi

# 3. Verify versions match
composer --version
php --version
node --version
```

### **MODIFIED Phase 2.2: Dependency Installation**
**Smart dependency installation based on analysis:**

```bash
# Enhanced dependency installation
echo "=== Smart Dependency Installation ==="

# 1. Check if production needs dev dependencies
NEEDS_DEV_IN_PROD=false
if [ -f "Admin-Local/1-CurrentProject/dependency-analysis/dev-deps-in-production.md" ]; then
    if grep -q "⚠️" Admin-Local/1-CurrentProject/dependency-analysis/dev-deps-in-production.md; then
        NEEDS_DEV_IN_PROD=true
    fi
fi

# 2. Install based on analysis
if [ "$NEEDS_DEV_IN_PROD" = true ]; then
    echo "⚠️  Installing with dev dependencies (some are needed in production)"
    composer install \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-interaction \
        --prefer-dist
else
    echo "✅ Installing production-only dependencies"
    composer install \
        --no-dev \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-interaction \
        --prefer-dist
fi

# 3. Verify installation
php artisan about --only=Environment
```

### **NEW Phase 2.6: Runtime Dependency Validation**
**Add after Build Validation:**

```bash
#!/bin/bash
# Runtime validation script

echo "=== Runtime Dependency Validation ==="

# 1. Test database operations
php artisan migrate:status --no-interaction || {
    echo "❌ Migration check failed"
    exit 1
}

# 2. Test seeder classes (dry run)
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
    php -r "
        require 'vendor/autoload.php';
        \$app = require_once 'bootstrap/app.php';
        \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
        \$kernel->bootstrap();
        
        // Test instantiation of seeders
        \$seeder = new \Database\Seeders\DatabaseSeeder();
        echo '✅ Seeder instantiation successful\n';
    " || {
        echo "❌ Seeder validation failed"
        exit 1
    }
fi

# 3. Test critical services
php artisan tinker --execute="
    echo '✅ Application boots: ' . app()->version() . PHP_EOL;
    echo '✅ Database connected: ' . DB::connection()->getPdo() ? 'Yes' : 'No';
" || {
    echo "❌ Application boot test failed"
    exit 1
}

echo "✅ All runtime validations passed"
```

---

## **Universal Pre-Deployment Checklist Tool**

Create this as a single script to run before any deployment:

```bash
#!/bin/bash
# save as: pre-deployment-check.sh

echo "╔══════════════════════════════════════════════════════════╗"
echo "║        Universal Laravel Pre-Deployment Checker          ║"
echo "╚══════════════════════════════════════════════════════════╝"

ISSUES=0

# 1. Check Composer version
echo -n "Checking Composer version... "
if composer --version | grep -q "version 1"; then
    echo "❌ Composer 1 detected (should be 2)"
    ((ISSUES++))
else
    echo "✅"
fi

# 2. Check for Faker in seeders
echo -n "Checking Faker usage... "
if grep -r "Faker\|factory(" database/seeders 2>/dev/null | grep -v "//" > /dev/null; then
    if ! grep '"fakerphp/faker"' composer.json | grep -v "require-dev" > /dev/null; then
        echo "❌ Faker used but not in production deps"
        ((ISSUES++))
    else
        echo "✅"
    fi
else
    echo "✅"
fi

# 3. Check lock files exist
echo -n "Checking lock files... "
if [ ! -f "composer.lock" ]; then
    echo "❌ composer.lock missing"
    ((ISSUES++))
else
    echo "✅"
fi

# 4. Check .env files
echo -n "Checking environment files... "
for env in .env.production .env.staging; do
    if [ ! -f "$env" ]; then
        echo "❌ $env missing"
        ((ISSUES++))
    fi
done
[ $ISSUES -eq 0 ] && echo "✅"

# 5. Test production build
echo -n "Testing production build... "
composer install --no-dev --dry-run 2>&1 | grep -q "Nothing to install" && echo "✅" || {
    echo "❌ Production install would fail"
    ((ISSUES++))
}

# 6. Check optimizations
echo -n "Checking optimization settings... "
if ! grep -q '"optimize-autoloader": true' composer.json; then
    echo "❌ Autoloader optimization not configured"
    ((ISSUES++))
else
    echo "✅"
fi

# Summary
echo ""
echo "══════════════════════════════════════════════════════════"
if [ $ISSUES -eq 0 ]; then
    echo "✅ ALL CHECKS PASSED - Ready for deployment!"
else
    echo "❌ FOUND $ISSUES ISSUES - Fix before deploying!"
fi
echo "══════════════════════════════════════════════════════════"

exit $ISSUES
```

---

## **Complete Flow Integration**

### Where to add each component:

**Section A (Project Setup):**
- Add Step 03.2 (Version Alignment) after Step 03.1
- Move Step 07 before Step 06.1
- Add Step 07.1 (Dependency Analysis) after Step 07

**Section B (Pre-Deployment):**
- Add Step 14.1 (Composer Strategy) as new first step
- Add Step 15.2 (Production Verification) after Step 15.1
- Modify Step 16 with pre-build validation

**Section C (Build & Deploy):**
- Enhance Phase 1.2 with version switching
- Enhance Phase 2.2 with smart installation
- Add Phase 2.6 for runtime validation

### Run Order:
1. **During Section A:** Run dependency analysis immediately after installation
2. **During Section B:** Run all verification scripts before committing
3. **Before Section C:** Run pre-deployment-check.sh as final validation
4. **During Section C:** Use enhanced phases with automatic detection

This universal system will work for ANY Laravel application and automatically detect and fix dependency issues before they cause deployment failures.