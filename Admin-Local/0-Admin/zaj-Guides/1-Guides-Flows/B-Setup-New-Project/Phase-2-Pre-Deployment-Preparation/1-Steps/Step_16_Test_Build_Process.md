# Step 16: Test Build Process & Template-Based Tracking

**Test production build process using template-based tracking for complete deployment readiness verification**

> 📋 **Analysis Source:** V1's detailed scripts + V2's organization + Template-Based Tracking System V4.0
>
> 🎯 **Purpose:** Verify production build process works before deployment with project-agnostic template system and complete safety mechanisms
>
> ⚠️ **Safety:** Template-based environment backup and restoration with automatic rollback capabilities

---

## **🎯 Phase 1: Template-Based Tracking Integration**

### **1. Initialize Template-Based Build Testing**

**Deploy and configure the template-based tracking system for build testing:**

```bash
# Navigate to project root (project-agnostic detection)
PROJECT_ROOT=$(pwd)
while [[ "$PROJECT_ROOT" != "/" && ! -f "$PROJECT_ROOT/composer.json" && ! -f "$PROJECT_ROOT/package.json" ]]; do
    PROJECT_ROOT=$(dirname "$PROJECT_ROOT")
done

if [[ "$PROJECT_ROOT" == "/" ]]; then
    echo "❌ ERROR: Could not detect project root"
    exit 1
fi

cd "$PROJECT_ROOT"
echo "🎯 Project root detected: $PROJECT_ROOT"

# Verify tracking system is available
TEMPLATE_PATH="Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/5-Tracking-System"

if [ ! -d "$TEMPLATE_PATH" ]; then
    echo "❌ ERROR: Tracking system template not found at: $TEMPLATE_PATH"
    echo "Please ensure Step 15 was completed successfully"
    exit 1
fi

# Verify project tracking directory exists
if [ ! -d "Admin-Local/1-CurrentProject/Tracking" ]; then
    echo "🚀 Deploying Template-Based Tracking System..."
    if [ -f "$TEMPLATE_PATH/setup-tracking.sh" ]; then
        bash "$TEMPLATE_PATH/setup-tracking.sh"
        
        if [ $? -ne 0 ]; then
            echo "❌ ERROR: Template deployment failed"
            exit 1
        fi
    else
        echo "❌ ERROR: setup-tracking.sh not found in templates"
        exit 1
    fi
fi

echo "✅ Template-based tracking system ready"
```

### **2. Create Build Testing Session**

**Initialize comprehensive tracking session for build testing:**

```bash
# Initialize tracking session for Step 16
TRACKING_SESSION="phase2-step16-$(date +'%Y%m%d-%H%M%S')"
SESSION_DIR="Admin-Local/1-CurrentProject/Tracking/2-Operation-Template"

# Create session-specific directory structure
mkdir -p "$SESSION_DIR/1-Planning"
mkdir -p "$SESSION_DIR/2-Baselines"
mkdir -p "$SESSION_DIR/3-Execution"
mkdir -p "$SESSION_DIR/4-Verification"
mkdir -p "$SESSION_DIR/5-Documentation"

echo "🎯 Template-Based Build Testing Session: $TRACKING_SESSION"
echo "Session: $TRACKING_SESSION" | tee "$SESSION_DIR/1-Planning/session-info.md"
echo "Purpose: Phase 2 Production Build Testing" | tee -a "$SESSION_DIR/1-Planning/session-info.md"
echo "Date: $(date)" | tee -a "$SESSION_DIR/1-Planning/session-info.md"
echo "Template Version: V4.0" | tee -a "$SESSION_DIR/1-Planning/session-info.md"

# Create build testing plan
cat > "$SESSION_DIR/1-Planning/step16-build-plan.md" << 'EOF'
# Step 16: Production Build Testing Plan

## Tracking Objectives
1. **Pre-Build Baseline**: Capture complete environment state
2. **Production Build Testing**: Test all build processes with tracking
3. **Laravel Optimizations**: Test caching and optimizations
4. **Local Testing**: Verify production build works locally
5. **Environment Restoration**: Complete restoration with verification
6. **Change Documentation**: Generate comprehensive reports

## Template Integration Benefits
- ✅ Project-agnostic path handling
- ✅ Session-based change tracking  
- ✅ Comprehensive baseline recording
- ✅ Automatic rollback capabilities
- ✅ Structured documentation generation

## Expected Outcomes
- Production build process verified and working
- All optimizations tested and functional
- Complete audit trail of all changes
- Development environment fully restored
- Ready for deployment preparation (Steps 17-20)
EOF

echo "✅ Build testing session initialized with template system"
```

### **3. Comprehensive Pre-Build Baseline**

**Record complete environment state using template-based approach:**

```bash
echo "🔍 Creating comprehensive pre-build baseline..."

BASELINE_DIR="$SESSION_DIR/2-Baselines"

# System Environment Baseline
echo "💻 Recording system environment baseline..."
echo "Node Version: $(node --version 2>/dev/null || echo 'Not installed')" > "$BASELINE_DIR/baseline-system-env.txt"
echo "NPM Version: $(npm --version 2>/dev/null || echo 'Not installed')" >> "$BASELINE_DIR/baseline-system-env.txt"  
echo "PHP Version: $(php --version 2>/dev/null | head -1 || echo 'Not installed')" >> "$BASELINE_DIR/baseline-system-env.txt"
echo "Composer Version: $(composer --version 2>/dev/null || echo 'Not installed')" >> "$BASELINE_DIR/baseline-system-env.txt"

# Laravel Environment Baseline
echo "🔧 Recording Laravel environment baseline..."
if [ -f ".env" ]; then
    cp .env "$BASELINE_DIR/baseline-env-current.txt"
    echo "✅ Current .env backed up"
else
    echo "⚠️ No .env file found" > "$BASELINE_DIR/baseline-env-current.txt"
fi

# Check for .env.local (production environment file)
if [ -f ".env.local" ]; then
    cp .env.local "$BASELINE_DIR/baseline-env-local.txt"
    echo "✅ .env.local found and backed up"
else
    echo "❌ ERROR: .env.local not found - run Step 11 first"
    exit 1
fi

# Dependencies Baseline
echo "📦 Recording dependencies baseline..."
if [ -f "composer.json" ]; then
    cp composer.json "$BASELINE_DIR/baseline-composer.json"
    [ -f "composer.lock" ] && cp composer.lock "$BASELINE_DIR/baseline-composer.lock"
    
    # Record current package state
    composer show > "$BASELINE_DIR/baseline-composer-packages.txt" 2>/dev/null || echo "Composer packages not available" > "$BASELINE_DIR/baseline-composer-packages.txt"
    
    DEV_PACKAGES=$(composer show --dev 2>/dev/null | wc -l || echo "0")
    echo "Development packages count: $DEV_PACKAGES" >> "$BASELINE_DIR/baseline-composer-packages.txt"
fi

if [ -f "package.json" ]; then
    cp package.json "$BASELINE_DIR/baseline-package.json"
    [ -f "package-lock.json" ] && cp package-lock.json "$BASELINE_DIR/baseline-package-lock.json"
    
    # Record current NPM packages
    npm list --depth=0 > "$BASELINE_DIR/baseline-npm-packages.txt" 2>/dev/null || echo "NPM packages not available" > "$BASELINE_DIR/baseline-npm-packages.txt"
fi

# Build Artifacts Baseline
echo "🏗️ Recording build artifacts baseline..."
if [ -d "public/build" ]; then
    cp -r public/build "$BASELINE_DIR/baseline-build-artifacts" 2>/dev/null
    BUILD_SIZE=$(du -sh public/build/ | cut -f1)
    echo "Existing build size: $BUILD_SIZE" > "$BASELINE_DIR/baseline-build-info.txt"
    ls -la public/build/ >> "$BASELINE_DIR/baseline-build-info.txt"
else
    echo "No existing build artifacts" > "$BASELINE_DIR/baseline-build-info.txt"
fi

# Laravel Cache State Baseline
echo "⚡ Recording Laravel cache state..."
CACHE_INFO="$BASELINE_DIR/baseline-laravel-cache.txt"
echo "Laravel Cache State - Pre-Build" > "$CACHE_INFO"
echo "=================================" >> "$CACHE_INFO"

[ -f "bootstrap/cache/config.php" ] && echo "✅ Config cache exists" >> "$CACHE_INFO" || echo "❌ Config cache missing" >> "$CACHE_INFO"
[ -f "bootstrap/cache/routes-v7.php" ] || [ -f "bootstrap/cache/routes.php" ] && echo "✅ Route cache exists" >> "$CACHE_INFO" || echo "❌ Route cache missing" >> "$CACHE_INFO"
[ -d "storage/framework/views" ] && echo "✅ View cache directory exists ($(find storage/framework/views -name "*.php" | wc -l) cached views)" >> "$CACHE_INFO" || echo "❌ View cache directory missing" >> "$CACHE_INFO"

# File System State
echo "📁 Recording file system baseline..."
find . -type f \
    -not -path "./vendor/*" \
    -not -path "./node_modules/*" \
    -not -path "./.git/*" \
    -not -path "./Admin-Local/1-CurrentProject/Tracking/*" \
    -not -path "./storage/logs/*" \
    -not -path "./storage/framework/cache/*" \
    > "$BASELINE_DIR/baseline-all-files.txt"

echo "✅ Comprehensive pre-build baseline recorded in: $BASELINE_DIR"
```

---

## **📦 Phase 2: Production Build Testing with Tracking**

### **4. Clean and Test Production PHP Build**

```bash
echo "🔄 Phase 2: Production Build Testing with Template-Based Tracking"

EXECUTION_DIR="$SESSION_DIR/3-Execution"

# Record pre-build execution state
echo "🔍 Recording pre-production build state..."
echo "Production PHP Build Test - Step 16" > "$EXECUTION_DIR/build-test-log.txt"
echo "=======================================" >> "$EXECUTION_DIR/build-test-log.txt"
echo "Started: $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
echo "" >> "$EXECUTION_DIR/build-test-log.txt"

# Create safe restoration point for build artifacts
if [ -d "public/build" ]; then
    cp -r public/build "$EXECUTION_DIR/pre-build-artifacts" 2>/dev/null || true
    echo "✅ Pre-build artifacts backed up"
    echo "Pre-build artifacts backed up: $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
fi

# Clean previous builds to test from scratch
echo "🗑️ Cleaning previous builds for fresh testing..."
rm -rf vendor node_modules public/build
echo "Cleaned: vendor, node_modules, public/build: $(date)" >> "$EXECUTION_DIR/build-test-log.txt"

# Test Production PHP Build with enhanced tracking
echo "🔄 Testing production PHP build..."
if composer install --no-dev --prefer-dist --optimize-autoloader; then
    echo "✅ Production PHP dependencies installed"
    echo "PHP production build: SUCCESS $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
    
    # Verify vendor directory and autoloader
    if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
        echo "✅ Vendor directory verified with autoloader"
        
        # Count production packages for verification
        PROD_PACKAGES=$(composer show 2>/dev/null | wc -l)
        echo "✅ Production packages installed: ${PROD_PACKAGES}"
        echo "Production packages count: $PROD_PACKAGES" >> "$EXECUTION_DIR/build-test-log.txt"
        
        # Save production package list
        composer show > "$EXECUTION_DIR/production-packages-list.txt" 2>/dev/null || true
        
    else
        echo "❌ ERROR: Production PHP build failed - missing vendor/autoload.php"
        echo "PHP build FAILED: missing autoloader $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
        exit 1
    fi
    
else
    echo "❌ ERROR: Composer production install failed"
    echo "Composer production install: FAILED $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
    exit 1
fi
```

### **5. Test Frontend Build with Enhanced Verification**

```bash
echo "📦 Testing frontend build process with tracking..."

if [ -f "package.json" ]; then
    echo "📦 Building frontend assets with template-based tracking..."
    
    # Clean install from lock file (production simulation)
    if npm ci; then
        echo "✅ Clean npm install completed"
        echo "NPM clean install: SUCCESS $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
        
        # Record NPM packages after clean install
        npm list --depth=0 > "$EXECUTION_DIR/clean-npm-packages.txt" 2>/dev/null || true
        
    else
        echo "❌ ERROR: npm ci failed"
        echo "NPM clean install: FAILED $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
        exit 1
    fi
    
    # Build production assets with comprehensive verification
    echo "🏗️ Building production assets..."
    if npm run build; then
        echo "✅ Production assets built successfully"
        echo "Frontend build: SUCCESS $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
        
        # Enhanced verification of build output
        if [ -d "public/build" ]; then
            echo "✅ Build directory created: public/build/"
            
            # Comprehensive build verification
            BUILD_VERIFICATION="$EXECUTION_DIR/build-verification.txt"
            echo "Frontend Build Verification - Step 16" > "$BUILD_VERIFICATION"
            echo "====================================" >> "$BUILD_VERIFICATION"
            echo "Build completed: $(date)" >> "$BUILD_VERIFICATION"
            echo "" >> "$BUILD_VERIFICATION"
            
            # Check for essential build files
            if [ -f "public/build/manifest.json" ]; then
                echo "✅ Asset manifest found"
                echo "✅ Asset manifest: FOUND" >> "$BUILD_VERIFICATION"
                cp public/build/manifest.json "$EXECUTION_DIR/build-manifest.json"
            else
                echo "⚠️ WARNING: No asset manifest found"
                echo "⚠️ Asset manifest: MISSING" >> "$BUILD_VERIFICATION"
            fi
            
            # Record build contents
            echo "📁 Build contents:" | tee -a "$BUILD_VERIFICATION"
            ls -la public/build/ | head -20 | tee -a "$BUILD_VERIFICATION"
            
            # Calculate and record build size
            BUILD_SIZE=$(du -sh public/build/ | cut -f1)
            echo "📊 Total build size: ${BUILD_SIZE}"
            echo "Total build size: $BUILD_SIZE" >> "$BUILD_VERIFICATION"
            
            # Count different asset types
            CSS_COUNT=$(find public/build -name "*.css" | wc -l)
            JS_COUNT=$(find public/build -name "*.js" | wc -l)
            OTHER_COUNT=$(find public/build -type f ! -name "*.css" ! -name "*.js" ! -name "manifest.json" | wc -l)
            
            echo "Asset breakdown:" >> "$BUILD_VERIFICATION"
            echo "- CSS files: $CSS_COUNT" >> "$BUILD_VERIFICATION"
            echo "- JS files: $JS_COUNT" >> "$BUILD_VERIFICATION"
            echo "- Other files: $OTHER_COUNT" >> "$BUILD_VERIFICATION"
            
        else
            echo "❌ ERROR: Build failed - no public/build/ directory"
            echo "Frontend build: FAILED - no build directory $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
            exit 1
        fi
        
    else
        echo "❌ ERROR: npm run build failed"
        echo "Frontend build: FAILED $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
        exit 1
    fi
    
else
    echo "ℹ️ No JavaScript build needed (PHP-only project)"
    echo "Project type: PHP-only (no frontend build)" >> "$EXECUTION_DIR/build-test-log.txt"
fi
```

---

## **📊 Phase 3: Laravel Production Optimizations with Tracking**

### **6. Apply Laravel Caching with Template-Based Safety**

```bash
echo "⚡ Phase 3: Laravel Production Optimizations with Template-Based Tracking"

# Switch to production environment using template-safe approach
echo "🔧 Switching to production environment for testing..."
if cp .env.local .env; then
    echo "✅ Switched to .env.local for production testing"
    echo "Environment switched to production: $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
else
    echo "❌ ERROR: Failed to copy .env.local to .env"
    echo "Environment switch: FAILED $(date)" >> "$EXECUTION_DIR/build-test-log.txt"
    exit 1
fi

# Apply Laravel production caches with comprehensive verification
echo "🔄 Generating Laravel caches with tracking..."

CACHE_LOG="$EXECUTION_DIR/laravel-cache-operations.txt"
echo "Laravel Cache Operations - Step 16" > "$CACHE_LOG"
echo "==================================" >> "$CACHE_LOG"
echo "Started: $(date)" >> "$CACHE_LOG"
echo "" >> "$CACHE_LOG"

# Config cache
echo "⚙️ Generating config cache..."
if php artisan config:cache; then
    if [ -f "bootstrap/cache/config.php" ]; then
        echo "✅ Config cache generated"
        echo "✅ Config cache: SUCCESS" >> "$CACHE_LOG"
        CONFIG_SIZE=$(wc -c < bootstrap/cache/config.php)
        echo "   Size: $CONFIG_SIZE bytes" >> "$CACHE_LOG"
    else
        echo "❌ ERROR: Config cache failed"
        echo "❌ Config cache: FAILED - file not found" >> "$CACHE_LOG"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan config:cache failed"
    echo "❌ Config cache: FAILED - command error" >> "$CACHE_LOG"
    exit 1
fi

# Route cache
echo "🛣️ Generating route cache..."
if php artisan route:cache; then
    if [ -f "bootstrap/cache/routes-v7.php" ] || [ -f "bootstrap/cache/routes.php" ]; then
        echo "✅ Route cache generated"
        echo "✅ Route cache: SUCCESS" >> "$CACHE_LOG"
        
        # Record route cache file size
        if [ -f "bootstrap/cache/routes-v7.php" ]; then
            ROUTE_SIZE=$(wc -c < bootstrap/cache/routes-v7.php)
            echo "   Routes file: routes-v7.php ($ROUTE_SIZE bytes)" >> "$CACHE_LOG"
        elif [ -f "bootstrap/cache/routes.php" ]; then
            ROUTE_SIZE=$(wc -c < bootstrap/cache/routes.php)
            echo "   Routes file: routes.php ($ROUTE_SIZE bytes)" >> "$CACHE_LOG"
        fi
    else
        echo "❌ ERROR: Route cache failed"
        echo "❌ Route cache: FAILED - file not found" >> "$CACHE_LOG"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan route:cache failed"
    echo "❌ Route cache: FAILED - command error" >> "$CACHE_LOG"
    exit 1
fi

# View cache
echo "👁️ Generating view cache..."
if php artisan view:cache; then
    if [ -d "storage/framework/views" ]; then
        VIEW_COUNT=$(find storage/framework/views -name "*.php" | wc -l)
        echo "✅ View cache generated (${VIEW_COUNT} cached views)"
        echo "✅ View cache: SUCCESS" >> "$CACHE_LOG"
        echo "   Cached views count: $VIEW_COUNT" >> "$CACHE_LOG"
        
        # Record view cache directory size
        VIEW_SIZE=$(du -sh storage/framework/views | cut -f1)
        echo "   View cache size: $VIEW_SIZE" >> "$CACHE_LOG"
    else
        echo "❌ ERROR: View cache failed"
        echo "❌ View cache: FAILED - directory not found" >> "$CACHE_LOG"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan view:cache failed"
    echo "❌ View cache: FAILED - command error" >> "$CACHE_LOG"
    exit 1
fi

echo "" >> "$CACHE_LOG"
echo "All Laravel caches completed: $(date)" >> "$CACHE_LOG"
echo "✅ All Laravel production caches generated successfully"
```

---

## **🧪 Phase 4: Production Build Verification with Tracking**

### **7. Local Production Server Testing**

```bash
echo "🧪 Phase 4: Production Build Verification with Template-Based Tracking"

VERIFICATION_DIR="$SESSION_DIR/4-Verification"
mkdir -p "$VERIFICATION_DIR"

echo "🚀 Testing production build locally with comprehensive tracking..."

# Create server testing log
SERVER_LOG="$VERIFICATION_DIR/server-test-results.txt"
echo "Local Production Server Test - Step 16" > "$SERVER_LOG"
echo "======================================" >> "$SERVER_LOG"
echo "Started: $(date)" >> "$SERVER_LOG"
echo "" >> "$SERVER_LOG"

# Start test server with enhanced monitoring
echo "🚀 Starting test server on port 8080..."
php artisan serve --host=127.0.0.1 --port=8080 &
SERVER_PID=$!
echo "Server PID: $SERVER_PID" >> "$SERVER_LOG"

# Wait for server to start with timeout and detailed logging
TIMEOUT=15
COUNTER=0
echo "⏳ Waiting for server to respond..." | tee -a "$SERVER_LOG"

while [ $COUNTER -lt $TIMEOUT ]; do
    if curl -s http://127.0.0.1:8080 > /dev/null 2>&1; then
        echo "✅ Test server is responding"
        echo "✅ Server response: SUCCESS (after ${COUNTER} seconds)" >> "$SERVER_LOG"
        break
    fi
    sleep 1
    COUNTER=$((COUNTER + 1))
    echo "⏳ Waiting for server... (${COUNTER}/${TIMEOUT})"
done

if [ $COUNTER -eq $TIMEOUT ]; then
    echo "❌ ERROR: Test server failed to respond within ${TIMEOUT} seconds"
    echo "❌ Server response: TIMEOUT after ${TIMEOUT} seconds" >> "$SERVER_LOG"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Comprehensive production build testing
echo "🔍 Testing production build with comprehensive verification..." | tee -a "$SERVER_LOG"
echo "" >> "$SERVER_LOG"

# Test main page
echo "Testing main page..." >> "$SERVER_LOG"
MAIN_RESPONSE=$(curl -s -w "%{http_code}" http://127.0.0.1:8080)
MAIN_CODE=$(echo "$MAIN_RESPONSE" | tail -c 4)

if [ "$MAIN_CODE" = "200" ]; then
    echo "✅ Main page responds with HTTP 200"
    echo "✅ Main page: HTTP $MAIN_CODE" >> "$SERVER_LOG"
    
    # Save response for analysis
    curl -s http://127.0.0.1:8080 > "$VERIFICATION_DIR/main-page-response.html"
    
else
    echo "❌ ERROR: Main page failed (HTTP: $MAIN_CODE)"
    echo "❌ Main page: HTTP $MAIN_CODE" >> "$SERVER_LOG"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test asset loading (if build directory exists)
if [ -d "public/build" ]; then
    echo "🎨 Testing asset loading..." | tee -a "$SERVER_LOG"
    
    # Test CSS assets
    CSS_ASSETS=$(find public/build -name "*.css" | head -3)
    for ASSET_FILE in $CSS_ASSETS; do
        if [ ! -z "$ASSET_FILE" ]; then
            ASSET_PATH=$(echo $ASSET_FILE | sed 's|public||')
            ASSET_RESPONSE=$(curl -s -w "%{http_code}" "http://127.0.0.1:8080${ASSET_PATH}")
            ASSET_CODE=$(echo "$ASSET_RESPONSE" | tail -c 4)
            
            if [ "$ASSET_CODE" = "200" ]; then
                echo "✅ CSS asset loading verified: ${ASSET_PATH}"
                echo "✅ CSS asset: ${ASSET_PATH} - HTTP $ASSET_CODE" >> "$SERVER_LOG"
            else
                echo "⚠️ WARNING: CSS asset failed: ${ASSET_PATH} (HTTP: $ASSET_CODE)"
                echo "⚠️ CSS asset: ${ASSET_PATH} - HTTP $ASSET_CODE" >> "$SERVER_LOG"
            fi
        fi
    done
    
    # Test JS assets
    JS_ASSETS=$(find public/build -name "*.js" | head -3)
    for ASSET_FILE in $JS_ASSETS; do
        if [ ! -z "$ASSET_FILE" ]; then
            ASSET_PATH=$(echo $ASSET_FILE | sed 's|public||')
            ASSET_RESPONSE=$(curl -s -w "%{http_code}" "http://127.0.0.1:8080${ASSET_PATH}")
            ASSET_CODE=$(echo "$ASSET_RESPONSE" | tail -c 4)
            
            if [ "$ASSET_CODE" = "200" ]; then
                echo "✅ JS asset loading verified: ${ASSET_PATH}"
                echo "✅ JS asset: ${ASSET_PATH} - HTTP $ASSET_CODE" >> "$SERVER_LOG"
            else
                echo "⚠️ WARNING: JS asset failed: ${ASSET_PATH} (HTTP: $ASSET_CODE)"
                echo "⚠️ JS asset: ${ASSET_PATH} - HTTP $ASSET_CODE" >> "$SERVER_LOG"
            fi
        fi
    done
fi

# Stop test server safely
echo "🛑 Stopping test server..." | tee -a "$SERVER_LOG"
if kill $SERVER_PID 2>/dev/null; then
    echo "✅ Test server stopped successfully"
    echo "✅ Server stopped: SUCCESS" >> "$SERVER_LOG"
else
    echo "⚠️ WARNING: Failed to stop test server (PID: $SERVER_PID)"
    echo "⚠️ Server stop: WARNING (PID: $SERVER_PID)" >> "$SERVER_LOG"
fi

echo "" >> "$SERVER_LOG"
echo "Server testing completed: $(date)" >> "$SERVER_LOG"
echo "✅ Production build verification completed successfully"
```

---

## **🔄 Phase 5: Development Environment Restoration with Template System**

### **8. Complete Development Environment Restoration**

```bash
echo "🔄 Phase 5: Development Environment Restoration with Template-Based Safety"

# Create restoration log
RESTORATION_LOG="$VERIFICATION_DIR/environment-restoration.txt"
echo "Development Environment Restoration - Step 16" > "$RESTORATION_LOG"
echo "=============================================" >> "$RESTORATION_LOG"
echo "Started: $(date)" >> "$RESTORATION_LOG"
echo "" >> "$RESTORATION_LOG"

# Clear all production caches first
echo "🧹 Clearing production caches..." | tee -a "$RESTORATION_LOG"
php artisan config:clear >> "$RESTORATION_LOG" 2>&1
php artisan route:clear >> "$RESTORATION_LOG" 2>&1
php artisan view:clear >> "$RESTORATION_LOG" 2>&1
php artisan cache:clear >> "$RESTORATION_LOG" 2>&1 || true

echo "✅ Production caches cleared"
echo "✅ Production caches: CLEARED" >> "$RESTORATION_LOG"

# Restore original environment file using template-safe approach
echo "🔧 Restoring original environment..." | tee -a "$RESTORATION_LOG"
if [ -f "$BASELINE_DIR/baseline-env-current.txt" ]; then
    if cp "$BASELINE_DIR/baseline-env-current.txt" .env; then
        echo "✅ Original .env restored from template backup"
        echo "✅ Environment restoration: SUCCESS" >> "$RESTORATION_LOG"
    else
        echo "❌ ERROR: Failed to restore .env from template backup"
        echo "❌ Environment restoration: FAILED" >> "$RESTORATION_LOG"
        exit 1
    fi
else
    echo "⚠️ WARNING: No template backup found - keeping current .env"
    echo "⚠️ Environment restoration: NO BACKUP FOUND" >> "$RESTORATION_LOG"
fi

# Restore development dependencies with enhanced tracking
echo "🔄 Restoring development dependencies..." | tee -a "$RESTORATION_LOG"

# Restore PHP development dependencies
echo "📦 Restoring PHP development dependencies..."
if composer install; then
    DEV_PACKAGES=$(composer show 2>/dev/null | wc -l)
    echo "✅ Development PHP dependencies restored (${DEV_PACKAGES} packages)"
    echo "✅ PHP development dependencies: $DEV_PACKAGES packages" >> "$RESTORATION_LOG"
    
    # Save restored package list for comparison
    composer show > "$VERIFICATION_DIR/restored-php-packages.txt" 2>/dev/null || true
    
else
    echo "❌ ERROR: Failed to restore PHP development dependencies"
    echo "❌ PHP development dependencies: FAILED" >> "$RESTORATION_LOG"
    exit 1
fi

# Restore Node.js dependencies (only if package.json exists)
if [ -f "package.json" ]; then
    echo "🎨 Restoring Node.js development dependencies..."
    if npm install; then
        NPM_PACKAGES=$(npm list --depth=0 2>/dev/null | grep -c "├──\|└──" || echo "unknown")
        echo "✅ Development Node.js dependencies restored (${NPM_PACKAGES} packages)"
        echo "✅ NPM development dependencies: $NPM_PACKAGES packages" >> "$RESTORATION_LOG"
        
        # Save restored NPM package list
        npm list --depth=0 > "$VERIFICATION_DIR/restored-npm-packages.txt" 2>/dev/null || true
        
    else
        echo "⚠️ WARNING: npm install had issues, but continuing..."
        echo "⚠️ NPM development dependencies: WARNING" >> "$RESTORATION_LOG"
    fi
fi

# Restore build artifacts if they existed before using template backup
if [ -d "$EXECUTION_DIR/pre-build-artifacts" ]; then
    cp -r "$EXECUTION_DIR/pre-build-artifacts" public/build 2>/dev/null
    echo "✅ Previous build artifacts restored from template backup"
    echo "✅ Build artifacts: RESTORED" >> "$RESTORATION_LOG"
elif [ -d "$BASELINE_DIR/baseline-build-artifacts" ]; then
    cp -r "$BASELINE_DIR/baseline-build-artifacts" public/build 2>/dev/null  
    echo "✅ Baseline build artifacts restored from template backup"
    echo "✅ Build artifacts: RESTORED from baseline" >> "$RESTORATION_LOG"
else
    echo "ℹ️ No previous build artifacts to restore"
    echo "ℹ️ Build artifacts: NONE TO RESTORE" >> "$RESTORATION_LOG"
fi

# Verify development environment restoration
echo "🔍 Verifying development environment restoration..." | tee -a "$RESTORATION_LOG"

# Check for development tools
if [ -d "vendor/bin" ]; then
    DEV_TOOLS=""
    [ -f "vendor/bin/phpunit" ] && DEV_TOOLS="$DEV_TOOLS phpunit"
    [ -f "vendor/bin/pest" ] && DEV_TOOLS="$DEV_TOOLS pest"
    [ -f "vendor/bin/php-cs-fixer" ] && DEV_TOOLS="$DEV_TOOLS php-cs-fixer"
    
    if [ ! -z "$DEV_TOOLS" ]; then
        echo "✅ Development tools available:$DEV_TOOLS"
        echo "✅ Development tools:$DEV_TOOLS" >> "$RESTORATION_LOG"
    else
        echo "ℹ️ No common development tools found (this might be normal)"
        echo "ℹ️ Development tools: NONE FOUND" >> "$RESTORATION_LOG"
    fi
fi

echo "" >> "$RESTORATION_LOG"
echo "Environment restoration completed: $(date)" >> "$RESTORATION_LOG"
echo "✅ Development environment fully restored using template system"
```

### **9. Final Verification and Template-Based Documentation**

```bash
echo "🎯 Phase 5.2: Final verification and comprehensive documentation..."

# Verify Laravel can boot in development mode
echo "🔍 Final Laravel verification..."
if php artisan --version > /dev/null 2>&1; then
    LARAVEL_VERSION=$(php artisan --version)
    echo "✅ Laravel is working in development mode: $LARAVEL_VERSION"
    echo "✅ Laravel verification: SUCCESS - $LARAVEL_VERSION" >> "$RESTORATION_LOG"
else
    echo "❌ ERROR: Laravel failed to boot - check logs"
    echo "❌ Laravel verification: FAILED" >> "$RESTORATION_LOG"
    exit 1
fi
```

---

## **📊 Phase 6: Template-Based Documentation Generation**

### **10. Generate Comprehensive Build Testing Documentation**

```bash
echo "📝 Generating comprehensive template-based documentation..."

DOCUMENTATION_DIR="$SESSION_DIR/5-Documentation"
mkdir -p "$DOCUMENTATION_DIR"

# Create comprehensive build testing report
cat > "$DOCUMENTATION_DIR/step16-build-test-report.md" << 'EOF'
# Step 16: Production Build Testing - Complete Report

## Executive Summary
Template-based production build testing completed successfully with comprehensive verification and safe restoration.

## Template System Integration
- ✅ Project-agnostic path detection implemented
- ✅ Session-based tracking for all build operations
- ✅ Comprehensive baseline and restoration system
- ✅ Template-based safety mechanisms active
- ✅ Structured documentation generation

## Build Testing Results
EOF

# Add execution results
echo "" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
echo "## Build Process Verification" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
if [ -f "$EXECUTION_DIR/build-test-log.txt" ]; then
    cat "$EXECUTION_DIR/build-test-log.txt" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
else
    echo "Build test log not available" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
fi
echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"

# Add server testing results
if [ -f "$VERIFICATION_DIR/server-test-results.txt" ]; then
    echo "" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo "## Local Production Server Testing" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    cat "$VERIFICATION_DIR/server-test-results.txt" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
fi

# Add environment restoration results
if [ -f "$VERIFICATION_DIR/environment-restoration.txt" ]; then
    echo "" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo "## Development Environment Restoration" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    cat "$VERIFICATION_DIR/environment-restoration.txt" >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
    echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
fi

# Add template system benefits
cat >> "$DOCUMENTATION_DIR/step16-build-test-report.md" << 'EOF'

## Template System Advantages

### Safety and Reliability
- ✅ Comprehensive baseline recording before any changes
- ✅ Automatic restoration mechanisms with template backups
- ✅ Project-agnostic path handling prevents hardcoded issues
- ✅ Session-based tracking for complete audit trail

### Build Process Verification  
- ✅ Production PHP dependencies tested and verified
- ✅ Frontend assets build process validated
- ✅ Laravel optimization caching tested
- ✅ Local production server functionality confirmed
- ✅ Complete environment restoration verified

### Documentation and Tracking
- ✅ Comprehensive change documentation generated
- ✅ Build artifacts properly backed up and restored
- ✅ Package state changes tracked and documented
- ✅ Server testing results recorded and archived

## Next Steps
- All build processes verified and working
- Environment safely restored to development state  
- Ready to proceed to Step 17: Customization Protection
- Template-based tracking system ready for deployment preparation

## Session Files
EOF

echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
find "$SESSION_DIR" -type f -exec basename {} \; | sort | head -20 >> "$DOCUMENTATION_DIR/step16-build-test-report.md"
echo '```' >> "$DOCUMENTATION_DIR/step16-build-test-report.md"

# Create quick summary
cat > "$DOCUMENTATION_DIR/quick-summary.md" << 'EOF'
# Step 16: Quick Summary

## Status: ✅ COMPLETED SUCCESSFULLY

### Key Achievements
- 🏗️ Production build process tested and verified
- ⚡ Laravel production optimizations working
- 🧪 Local production server testing passed
- 🔄 Development environment safely restored
- 📊 Template-based tracking system fully functional

### What Was Tested
- PHP production dependencies installation
- Frontend asset compilation and verification
- Laravel config, route, and view caching
- Local production server functionality
- Complete environment restoration

### Template System Benefits
- Project-agnostic implementation
- Comprehensive baseline and restoration
- Session-based change tracking
- Structured documentation generation

### Next Action
Proceed to Step 17: Customization Protection System with confidence - all build processes verified and working.
EOF

# Final status display with template system integration
echo ""
echo "🎯 STEP 16 COMPLETE - PRODUCTION BUILD TESTING WITH TEMPLATE SYSTEM"
echo "====================================================================="
echo "🏗️ Build Process: Production build tested and verified successfully"
echo "⚡ Laravel Optimizations: All caching mechanisms tested and working"
echo "🧪 Local Testing: Production server functionality confirmed"
echo "🔄 Environment: Development environment safely restored"
echo "📊 Template System: Complete session-based tracking and documentation"
echo "🔍 Safety: All changes tracked with comprehensive restoration capabilities"
echo ""
echo "📁 Session Data: $SESSION_DIR"
echo "📄 Build Test Report: $DOCUMENTATION_DIR/step16-build-test-report.md"
echo "📄 Quick Summary: $DOCUMENTATION_DIR/quick-summary.md"
echo ""
echo "✅ Ready for Step 17: Customization Protection System"
echo ""
```

---

## **🚨 Template-Based Error Recovery Guide**

**If Step 16 fails at any point, use the template-based recovery system:**

### **1. Automatic Template-Based Restoration:**
```bash
# Restore environment using template backups
SESSION_DIR="Admin-Local/1-CurrentProject/Tracking/2-Operation-Template"

# Restore original .env from template backup
if [ -f "$SESSION_DIR/2-Baselines/baseline-env-current.txt" ]; then
    cp "$SESSION_DIR/2-Baselines/baseline-env-current.txt" .env
    echo "✅ Environment restored from template backup"
fi

# Clear any partial caches
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Restore development dependencies
composer install
npm install 2>/dev/null || true

# Restore build artifacts if they existed
if [ -d "$SESSION_DIR/2-Baselines/baseline-build-artifacts" ]; then
    cp -r "$SESSION_DIR/2-Baselines/baseline-build-artifacts" public/build
fi
```

### **2. Template System Health Check:**
```bash
# Verify template system is working
echo "🔍 Template System Health Check:"
[ -d "Admin-Local/1-CurrentProject/Tracking" ] && echo "✅ Project tracking active" || echo "❌ Project tracking missing"
[ -f "$SESSION_DIR/1-Planning/session-info.md" ] && echo "✅ Session properly initialized" || echo "❌ Session initialization missing"

# Verify Laravel functionality
php artisan --version && echo "✅ Laravel working" || echo "❌ Laravel issues detected"
```

### **3. Complete Recovery Verification:**
```bash
# Final recovery verification
echo "🎯 Recovery Verification:"
echo "Environment: $([ -f ".env" ] && echo "✅ Restored" || echo "❌ Missing")"
echo "Dependencies: $([ -d "vendor" ] && echo "✅ Restored" || echo "❌ Missing")"
echo "Laravel: $(php artisan --version > /dev/null 2>&1 && echo "✅ Working" || echo "❌ Failed")"
echo ""
echo "Template-based recovery completed - safe to retry Step 16"
```

---

## **📈 Success Metrics & Implementation Status**

**Template-Based Build Testing Implementation:**
- [x] Project-agnostic path detection and session management
- [x] Comprehensive baseline recording before build testing
- [x] Production PHP dependencies installation and verification
- [x] Frontend assets build testing with detailed verification
- [x] Laravel production optimizations testing (config, route, view caching)
- [x] Local production server functionality testing
- [x] Complete development environment restoration with verification
- [x] Template-based safety mechanisms and automatic recovery
- [x] Structured documentation generation with session tracking

**Expected State After Step 16:**
- **Build Process**: Thoroughly tested and verified working
- **Laravel Optimizations**: All caching mechanisms tested and functional
- **Environment**: Development environment safely restored with template system
- **Documentation**: Complete audit trail using template-based tracking
- **Integration**: Seamless integration with template ecosystem
- **Safety
