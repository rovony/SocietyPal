# Step 22A: Local Build + SSH Deploy Process

## **Analysis Source**

**V1 vs V2 Comparison:** Scenario A: Local Build + SSH  
**Recommendation:** 🔄 **Take V2's organization + V1's build verification scripts** (V1 has better error handling)  
**Source Used:** V2's structured workflow combined with V1's comprehensive build verification and error handling scripts

> **Purpose:** Execute local build process with comprehensive verification and create deployment package

## **Critical Overview**

**🏗️ LOCAL BUILD + SSH DEPLOYMENT WORKFLOW**

1. **Local Build:** Build production assets on your machine
2. **Package Creation:** Create compressed deployment package
3. **Upload:** Transfer package to server via SSH
4. **Server Deployment:** Extract and deploy with zero downtime

## **Phase 1: Local Build Process**

### **1. Navigate to Project Root**

```bash
# Navigate to project root
cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"

echo "🏗️ Starting local build process for production deployment..."
```

### **2. Clean Previous Builds**

```bash
# Clean any previous builds to ensure fresh start
echo "🧹 Cleaning previous builds..."

rm -rf vendor node_modules public/build bootstrap/cache/*.php
rm -rf storage/framework/cache/* storage/framework/views/* 2>/dev/null || true

echo "✅ Build environment cleaned"
```

### **3. Build PHP Dependencies (Production)**

```bash
# Build PHP dependencies (production only)
echo "📦 Installing PHP dependencies for production..."

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

# Verify composer build
if [ $? -eq 0 ] && [ -d "vendor" ]; then
    echo "✅ PHP dependencies installed successfully"
    echo "📊 Vendor directory size: $(du -h vendor/ | tail -1 | cut -f1)"
else
    echo "❌ Composer install failed"
    exit 1
fi
```

**Expected Result:**

- `vendor/` contains only production packages
- Autoloader optimized for performance
- No development dependencies included

**Flag Details:**

- `--no-dev`: Excludes PHPUnit, debugging tools, development helpers
- `--prefer-dist`: Uses stable releases, faster downloads
- `--optimize-autoloader`: Creates class map for faster class loading
- `--no-interaction`: Runs without prompts (automation-friendly)
- `--no-progress`: Cleaner output for scripts

### **4. Build Frontend Assets (Enhanced Verification)**

```bash
# Build frontend assets with comprehensive verification
if [ -f "package.json" ]; then
    echo "📦 Building frontend assets..."

    # Install production dependencies
    npm ci --only=production
    if [ $? -ne 0 ]; then
        echo "❌ NPM install failed"
        exit 1
    fi

    # Build assets
    npm run build
    if [ $? -ne 0 ]; then
        echo "❌ NPM build failed"
        exit 1
    fi

    # Enhanced verification from V1
    if [ -d "public/build" ]; then
        ASSET_COUNT=$(ls -1 public/build/ | wc -l)
        ASSET_SIZE=$(du -h public/build/ | tail -1 | cut -f1)
        echo "✅ Frontend assets built successfully:"
        echo "   📊 Files created: $ASSET_COUNT"
        echo "   📊 Total size: $ASSET_SIZE"
        echo "   📁 Contents:"
        ls -la public/build/ | head -10

        # Verify critical files exist
        if ls public/build/*.js >/dev/null 2>&1; then
            echo "   ✅ JavaScript files found"
        else
            echo "   ⚠️ No JavaScript files found"
        fi

        if ls public/build/*.css >/dev/null 2>&1; then
            echo "   ✅ CSS files found"
        else
            echo "   ⚠️ No CSS files found"
        fi
    else
        echo "❌ Frontend build failed - no public/build/ directory"
        exit 1
    fi

    # Clean up node_modules to save space in deployment package
    rm -rf node_modules
    echo "🗑️ Removed node_modules (not needed after build)"

else
    echo "ℹ️ No package.json found - PHP-only project"
fi
```

**Expected Result:**

- Production-optimized CSS/JS in `public/build/`
- Assets minified and versioned
- `node_modules/` removed to save deployment space
- Comprehensive verification of build artifacts

### **5. Cache Laravel Configurations (Enhanced)**

```bash
# Set up production environment for caching
echo "⚡ Optimizing Laravel for production..."

# Create temporary production environment
if [ -f ".env.production" ]; then
    cp .env .env.backup
    cp .env.production .env
    echo "✅ Using production environment for optimization"
else
    echo "ℹ️ No .env.production found, using current environment"
fi

# Cache configurations with verification
php artisan config:cache
if [ $? -eq 0 ]; then
    echo "✅ Configuration cached"
else
    echo "❌ Config caching failed"
    exit 1
fi

php artisan route:cache
if [ $? -eq 0 ]; then
    echo "✅ Routes cached"
else
    echo "❌ Route caching failed"
    exit 1
fi

php artisan view:cache
if [ $? -eq 0 ]; then
    echo "✅ Views pre-compiled"
else
    echo "❌ View caching failed"
    exit 1
fi

# Restore local environment
if [ -f ".env.backup" ]; then
    mv .env.backup .env
    echo "✅ Local environment restored"
fi

echo "⚡ Laravel optimized for production performance"
```

### **6. Production Build Verification (V1's Enhanced Testing)**

```bash
# Enhanced production build verification from V1
echo "🧪 Testing production build locally..."

# Start local server for testing
php artisan serve --host=127.0.0.1 --port=8080 &
SERVER_PID=$!
sleep 5

# Test that the built version works
echo "🌐 Testing application response..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8080)

if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Production build works locally (HTTP $HTTP_STATUS)"

    # Additional tests
    echo "🔍 Running additional verification tests..."

    # Test that assets are accessible
    if curl -s http://127.0.0.1:8080 | grep -q "build/"; then
        echo "   ✅ Built assets referenced in HTML"
    else
        echo "   ⚠️ Built assets not found in HTML"
    fi

    # Test basic Laravel functionality
    if curl -s http://127.0.0.1:8080 | grep -q "Laravel\|Blade\|csrf"; then
        echo "   ✅ Laravel framework functioning"
    else
        echo "   ⚠️ Laravel framework issues detected"
    fi

else
    echo "❌ Production build failed local test (HTTP $HTTP_STATUS)"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Stop test server
kill $SERVER_PID 2>/dev/null
echo "✅ Local testing complete"
```

## **Phase 2: Create Deployment Package**

### **1. Create Timestamped Package**

```bash
# Create timestamped deployment package
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
PACKAGE_NAME="deploy-societypal-${TIMESTAMP}.tar.gz"

echo "📦 Creating deployment package: $PACKAGE_NAME"

# Create package with built artifacts (excluding unnecessary files)
tar -czf "$PACKAGE_NAME" \
  --exclude='.git' \
  --exclude='.env*' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='tests' \
  --exclude='node_modules' \
  --exclude='.local-shared' \
  --exclude='tmp-zip-extract' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  app/ \
  bootstrap/ \
  config/ \
  database/ \
  public/ \
  resources/ \
  routes/ \
  storage/ \
  vendor/ \
  Admin-Local/ \
  scripts/ \
  artisan \
  composer.json \
  composer.lock \
  package.json \
  package-lock.json \
  .gitignore \
  README.md

# Enhanced package verification
if [ -f "$PACKAGE_NAME" ]; then
    PACKAGE_SIZE=$(du -h "$PACKAGE_NAME" | cut -f1)
    echo "✅ Deployment package created successfully:"
    echo "   📦 Package: $PACKAGE_NAME"
    echo "   📊 Size: $PACKAGE_SIZE"

    # Verify package contents
    echo "📋 Package contents verification:"
    tar -tzf "$PACKAGE_NAME" | head -15
    echo "   ... (showing first 15 files)"

    # Count files in package
    FILE_COUNT=$(tar -tzf "$PACKAGE_NAME" | wc -l)
    echo "   📊 Total files in package: $FILE_COUNT"

    # Check for critical files
    echo "🔍 Verifying critical files in package:"
    for critical_file in "artisan" "composer.lock" "vendor/" "app/" "public/"; do
        if tar -tzf "$PACKAGE_NAME" | grep -q "^$critical_file"; then
            echo "   ✅ $critical_file included"
        else
            echo "   ❌ $critical_file MISSING"
        fi
    done

else
    echo "❌ Failed to create deployment package"
    exit 1
fi
```

### **2. Upload Package to Server**

```bash
# Upload package to server with verification
echo "🚀 Uploading package to server..."

# Verify SSH connection first
echo "🔍 Testing SSH connection..."
ssh -o ConnectTimeout=10 hostinger-factolo "echo 'SSH connection successful'" 2>/dev/null
if [ $? -ne 0 ]; then
    echo "❌ SSH connection failed"
    echo "💡 Check your SSH configuration and server availability"
    exit 1
fi

# Create releases directory on server if it doesn't exist
ssh hostinger-factolo "mkdir -p ~/domains/societypal.com/releases/"

# Upload with progress and verification
echo "📤 Uploading $PACKAGE_NAME..."
scp -v "$PACKAGE_NAME" hostinger-factolo:~/domains/societypal.com/releases/

if [ $? -eq 0 ]; then
    echo "✅ Package uploaded successfully"

    # Verify uploaded file
    REMOTE_SIZE=$(ssh hostinger-factolo "du -h ~/domains/societypal.com/releases/$PACKAGE_NAME" | cut -f1)
    echo "📊 Remote package size: $REMOTE_SIZE"

    # Compare sizes
    LOCAL_SIZE=$(du -h "$PACKAGE_NAME" | cut -f1)
    if [ "$LOCAL_SIZE" = "$REMOTE_SIZE" ]; then
        echo "✅ Upload verification successful (sizes match)"
    else
        echo "⚠️ Size mismatch - Local: $LOCAL_SIZE, Remote: $REMOTE_SIZE"
    fi

else
    echo "❌ Upload failed"
    exit 1
fi
```

### **3. Cleanup Options**

```bash
# Optional cleanup of local build artifacts
echo ""
echo "🧹 Cleanup options:"
echo "1. Keep everything (recommended for troubleshooting)"
echo "2. Clean build artifacts only"
echo "3. Clean everything including package"

read -p "Choose cleanup level (1-3): " CLEANUP_LEVEL

case $CLEANUP_LEVEL in
    2)
        rm -rf vendor public/build bootstrap/cache/*.php
        echo "✅ Build artifacts cleaned"
        ;;
    3)
        rm -rf vendor public/build bootstrap/cache/*.php
        rm "$PACKAGE_NAME"
        echo "✅ All temporary files cleaned"
        ;;
    *)
        echo "ℹ️ Keeping all files for troubleshooting"
        ;;
esac

# Restore development dependencies if needed
if [ ! -d "vendor" ] || [ $CLEANUP_LEVEL -eq 2 ] || [ $CLEANUP_LEVEL -eq 3 ]; then
    echo "🔄 Restoring development environment..."
    composer install
    npm install 2>/dev/null || true
    echo "✅ Development environment restored"
fi
```

## **Expected Result**

- ✅ Production-optimized application built locally
- ✅ Comprehensive build verification completed
- ✅ Deployment package created and uploaded
- ✅ Ready for server-side deployment (Step 23A)

## **Next Step**

**Proceed to:** [Step 23A: Server-Side Deployment](Step-23A-Server-Deployment.md)

## **Troubleshooting**

### **Build Failures**

```bash
# Check for common issues
composer validate
npm audit --production
php artisan config:clear
```

### **Upload Issues**

```bash
# Test SSH connection
ssh -v hostinger-factolo

# Check server disk space
ssh hostinger-factolo "df -h"
```

### **Package Issues**

```bash
# Verify package contents
tar -tzf "$PACKAGE_NAME" | grep -E "(vendor/|app/|public/)"
```
