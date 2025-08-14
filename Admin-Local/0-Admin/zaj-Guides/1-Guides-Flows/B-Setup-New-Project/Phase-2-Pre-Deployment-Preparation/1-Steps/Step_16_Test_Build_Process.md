# Step 16: Test Build Process

**Test production build process to ensure deployment readiness**

> 📋 **Analysis Source:** V1's detailed scripts + V2's organization + Enhanced error handling and rollback mechanisms
>
> 🎯 **Purpose:** Verify production build process works before deployment with complete safety mechanisms
>
> ⚠️ **Safety:** Full environment backup and restoration with automatic rollback on failures

---

## **Prerequisites Check**

**Before starting, verify the current environment:**

```bash
echo "🔍 Step 16: Production Build Testing - Prerequisites Check"
echo "=================================================="

# Backup current environment state
if [ ! -f ".env.backup-step16" ]; then
    cp .env .env.backup-step16
    echo "✅ Environment backed up to .env.backup-step16"
else
    echo "⚠️ Previous backup exists - using existing backup"
fi

# Verify required files exist
if [ ! -f "composer.json" ]; then
    echo "❌ ERROR: composer.json not found"
    exit 1
fi

if [ ! -f ".env.local" ]; then
    echo "❌ ERROR: .env.local not found - run Step 11 first"
    exit 1
fi

echo "✅ Prerequisites verified"
```

---

## **Phase 1: Clean and Test Production Build**

### **1.1 Clean Previous Builds (Safe)**

```bash
echo "🗑️ Phase 1.1: Cleaning previous builds..."

# Create restoration point for build artifacts
if [ -d "public/build" ]; then
    cp -r public/build public/build.backup-step16 2>/dev/null || true
    echo "✅ Backed up existing build artifacts"
fi

# Clean previous builds to test from scratch
rm -rf vendor node_modules public/build
echo "✅ Cleaned: vendor, node_modules, public/build"
```

### **1.2 Test Production PHP Build**

```bash
echo "🔄 Phase 1.2: Testing production PHP build..."

# Install production-only PHP dependencies with error handling
if composer install --no-dev --prefer-dist --optimize-autoloader; then
    echo "✅ Production PHP dependencies installed"
    
    # Verify vendor directory and autoloader
    if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
        echo "✅ Vendor directory verified with autoloader"
    else
        echo "❌ ERROR: Production PHP build failed - missing vendor/autoload.php"
        exit 1
    fi
    
    # Count production packages for verification
    PROD_PACKAGES=$(composer show 2>/dev/null | wc -l)
    echo "✅ Production packages installed: ${PROD_PACKAGES}"
    
else
    echo "❌ ERROR: Composer production install failed"
    exit 1
fi
```

**Expected Result:**
- `vendor/` recreated with only production packages
- Optimized autoloader generated for performance
- No development tools included (PHPUnit, debuggers, etc.)

### **1.3 Test Frontend Build (Enhanced Verification)**

```bash
echo "📦 Phase 1.3: Testing frontend build process..."

if [ -f "package.json" ]; then
    echo "📦 Building frontend assets..."
    
    # Clean install from lock file (production simulation)
    if npm ci; then
        echo "✅ Clean npm install completed"
    else
        echo "❌ ERROR: npm ci failed"
        exit 1
    fi
    
    # Build production assets with verification
    if npm run build; then
        echo "✅ Production assets built successfully"
        
        # Enhanced verification of build output
        if [ -d "public/build" ]; then
            echo "✅ Build directory created: public/build/"
            
            # Check for essential build files
            if [ -f "public/build/manifest.json" ]; then
                echo "✅ Asset manifest found"
            else
                echo "⚠️ WARNING: No asset manifest found"
            fi
            
            # Display build contents
            echo "📁 Build contents:"
            ls -la public/build/ | head -10
            
            # Calculate build size
            BUILD_SIZE=$(du -sh public/build/ | cut -f1)
            echo "📊 Total build size: ${BUILD_SIZE}"
            
        else
            echo "❌ ERROR: Build failed - no public/build/ directory"
            exit 1
        fi
        
    else
        echo "❌ ERROR: npm run build failed"
        exit 1
    fi
    
else
    echo "ℹ️ No JavaScript build needed (PHP-only project)"
fi
```

**Expected Result:**
- `public/build/` directory with compiled CSS/JS
- Manifest files for asset versioning
- Minified, production-ready assets

---

## **Phase 2: Laravel Production Optimizations**

### **2.1 Apply Laravel Caching (Safe)**

```bash
echo "⚡ Phase 2.1: Applying Laravel production optimizations..."

# Set production environment for testing (safely)
if cp .env.local .env; then
    echo "✅ Switched to .env.local for production testing"
else
    echo "❌ ERROR: Failed to copy .env.local to .env"
    exit 1
fi

# Apply Laravel production caches with verification
echo "🔄 Generating Laravel caches..."

# Config cache
if php artisan config:cache; then
    if [ -f "bootstrap/cache/config.php" ]; then
        echo "✅ Config cache generated"
    else
        echo "❌ ERROR: Config cache failed"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan config:cache failed"
    exit 1
fi

# Route cache
if php artisan route:cache; then
    if [ -f "bootstrap/cache/routes-v7.php" ] || [ -f "bootstrap/cache/routes.php" ]; then
        echo "✅ Route cache generated"
    else
        echo "❌ ERROR: Route cache failed"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan route:cache failed"
    exit 1
fi

# View cache
if php artisan view:cache; then
    if [ -d "storage/framework/views" ]; then
        VIEW_COUNT=$(find storage/framework/views -name "*.php" | wc -l)
        echo "✅ View cache generated (${VIEW_COUNT} cached views)"
    else
        echo "❌ ERROR: View cache failed"
        exit 1
    fi
else
    echo "❌ ERROR: php artisan view:cache failed"
    exit 1
fi

echo "✅ All Laravel caches generated successfully"
```

---

## **Phase 3: Production Build Verification**

### **3.1 Test Built Version Locally**

```bash
echo "🧪 Phase 3.1: Testing production build locally..."

# Start test server with timeout and PID tracking
echo "🚀 Starting test server on port 8080..."
php artisan serve --host=127.0.0.1 --port=8080 &
SERVER_PID=$!

# Wait for server to start with timeout
TIMEOUT=10
COUNTER=0
while [ $COUNTER -lt $TIMEOUT ]; do
    if curl -s http://127.0.0.1:8080 > /dev/null 2>&1; then
        echo "✅ Test server is responding"
        break
    fi
    sleep 1
    COUNTER=$((COUNTER + 1))
    echo "⏳ Waiting for server... (${COUNTER}/${TIMEOUT})"
done

if [ $COUNTER -eq $TIMEOUT ]; then
    echo "❌ ERROR: Test server failed to respond within ${TIMEOUT} seconds"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test the production build with enhanced verification
echo "🔍 Testing production build responses..."

# Test main page
if curl -s -w "%{http_code}" http://127.0.0.1:8080 | grep -q "200"; then
    echo "✅ Main page responds with HTTP 200"
else
    echo "❌ ERROR: Main page failed to respond correctly"
    kill $SERVER_PID
    exit 1
fi

# Test assets loading (if build directory exists)
if [ -d "public/build" ]; then
    # Find a CSS or JS file to test
    ASSET_FILE=$(find public/build -name "*.css" -o -name "*.js" | head -1)
    if [ ! -z "$ASSET_FILE" ]; then
        ASSET_PATH=$(echo $ASSET_FILE | sed 's|public||')
        if curl -s -w "%{http_code}" "http://127.0.0.1:8080${ASSET_PATH}" | grep -q "200"; then
            echo "✅ Asset loading verified: ${ASSET_PATH}"
        else
            echo "⚠️ WARNING: Asset failed to load: ${ASSET_PATH}"
        fi
    fi
fi

# Stop test server
echo "🛑 Stopping test server..."
if kill $SERVER_PID; then
    echo "✅ Test server stopped"
else
    echo "⚠️ WARNING: Failed to stop test server (PID: $SERVER_PID)"
fi

echo "✅ Production build verification completed successfully"
```

---

## **Phase 4: Development Environment Restoration**

### **4.1 Complete Development Restoration (Enhanced)**

```bash
echo "🔄 Phase 4.1: Restoring development environment..."

# Clear all production caches first
echo "🧹 Clearing production caches..."
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear 2>/dev/null || true

# Restore original environment file
if [ -f ".env.backup-step16" ]; then
    if cp .env.backup-step16 .env; then
        echo "✅ Original .env restored"
    else
        echo "❌ ERROR: Failed to restore .env"
        exit 1
    fi
else
    echo "⚠️ WARNING: No .env backup found - keeping current .env"
fi

# Restore development dependencies
echo "🔄 Reinstalling development dependencies..."

# Restore PHP development dependencies
if composer install; then
    DEV_PACKAGES=$(composer show 2>/dev/null | wc -l)
    echo "✅ Development PHP dependencies restored (${DEV_PACKAGES} packages)"
else
    echo "❌ ERROR: Failed to restore PHP development dependencies"
    exit 1
fi

# Restore Node.js dependencies (only if package.json exists)
if [ -f "package.json" ]; then
    if npm install 2>/dev/null; then
        NPM_PACKAGES=$(npm list --depth=0 2>/dev/null | grep -c "├──\|└──" || echo "unknown")
        echo "✅ Development Node.js dependencies restored (${NPM_PACKAGES} packages)"
    else
        echo "⚠️ WARNING: npm install had issues, but continuing..."
    fi
fi

# Restore build artifacts if they existed before
if [ -d "public/build.backup-step16" ]; then
    cp -r public/build.backup-step16 public/build
    rm -rf public/build.backup-step16
    echo "✅ Previous build artifacts restored"
fi

# Verify development environment
echo "🔍 Verifying development environment restoration..."

# Check for development tools
if [ -d "vendor/bin" ]; then
    if [ -f "vendor/bin/phpunit" ] || [ -f "vendor/bin/pest" ]; then
        echo "✅ Development testing tools available"
    else
        echo "⚠️ No testing tools found (this might be normal)"
    fi
fi

# Clean up backup files
if [ -f ".env.backup-step16" ]; then
    rm .env.backup-step16
    echo "✅ Cleanup: Removed .env backup"
fi

echo "✅ Development environment fully restored"
```

### **4.2 Final Verification**

```bash
echo "🎯 Phase 4.2: Final verification..."

# Verify Laravel can boot in development mode
if php artisan --version > /dev/null 2>&1; then
    echo "✅ Laravel is working in development mode"
else
    echo "❌ ERROR: Laravel failed to boot - check logs"
    exit 1
fi

# Final status report
echo ""
echo "🎉 STEP 16 COMPLETED SUCCESSFULLY"
echo "=================================="
echo "✅ Production build process tested and verified"
echo "✅ Laravel caching tested and working"
echo "✅ Local production server tested successfully"
echo "✅ Development environment fully restored"
echo ""
echo "📋 Summary:"
echo "   - PHP production dependencies: Tested ✓"
echo "   - Frontend assets build: Tested ✓"
echo "   - Laravel optimizations: Tested ✓"
echo "   - Local server test: Passed ✓"
echo "   - Environment restoration: Complete ✓"
echo ""
echo "🚀 Ready for deployment preparation (Steps 17-20)"
```

---

## **Error Recovery Guide**

**If Step 16 fails at any point:**

1. **Restore Environment:**
   ```bash
   # Restore original .env if backup exists
   [ -f ".env.backup-step16" ] && cp .env.backup-step16 .env
   
   # Clear any partial caches
   php artisan config:clear 2>/dev/null || true
   php artisan route:clear 2>/dev/null || true
   php artisan view:clear 2>/dev/null || true
   
   # Restore development dependencies
   composer install
   npm install 2>/dev/null || true
   ```

2. **Clean Up:**
   ```bash
   # Remove backup files
   rm -f .env.backup-step16
   rm -rf public/build.backup-step16
   
   # Kill any hanging servers
   pkill -f "artisan serve" 2>/dev/null || true
   ```

3. **Verify Recovery:**
   ```bash
   php artisan --version
   echo "Environment recovered - safe to retry Step 16"
   ```

---

**Expected Final Result:** Production build process tested and verified working, development environment completely restored, ready for deployment.

**Next Step:** [Step 17: Customization Protection](Step_17_Customization_Protection.md)
