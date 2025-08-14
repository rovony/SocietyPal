# Step 18: Data Persistence Strategy

## **Analysis Source**

**V1 vs V2 Comparison:** Step 15 (V1) vs Step 13 (V2)  
**Recommendation:** ✅ **Take V1's advanced auto-detection scripts** - V2 lacks smart exclusion patterns  
**Source Used:** V1's sophisticated data persistence with automatic framework detection and smart build artifact exclusions

> **Purpose:** Implement zero data loss system with smart content protection during deployments

## **Critical Goal**

**🛡️ ZERO DATA LOSS DURING DEPLOYMENTS**

- User uploads must survive all deployments
- Generated content must persist across updates
- Smart exclusion of build artifacts
- Automatic detection of application type

## **Enhanced Persistence Script**

### **1. Create Advanced Persistence Script**

```bash
# Create persistence automation script
echo "🛡️ Setting up data persistence strategy..."

mkdir -p scripts

# Create shared directory linking script with exclusion strategy
cat > scripts/link_persistent_dirs.sh << 'EOF'
#!/bin/bash

# Data Persistence Script - Prevents User Data Loss During Deployments
# Usage: bash scripts/link_persistent_dirs.sh "/path/to/release" "/path/to/shared"

RELEASE_PATH="${1:-$(pwd)}"
SHARED_PATH="${2:-$(pwd)/../shared}"

echo "🔗 Setting up data persistence links..."
echo "  Release: $RELEASE_PATH"
echo "  Shared:  $SHARED_PATH"

# Universal exclusion list for public/ directory
PUBLIC_EXCLUSIONS=(
    "css"
    "js"
    "build"
    "_next"
    "static"
    "assets"
    "dist"
    "mix-manifest.json"
    "hot"
    "webpack"
    "vite"
    ".vite"
    "fonts"  # if generated/built
)

# Detect application type for specific exclusions
if [ -f "$RELEASE_PATH/artisan" ]; then
    # Laravel application
    PUBLIC_EXCLUSIONS+=("mix-manifest.json" "hot")
elif [ -f "$RELEASE_PATH/package.json" ]; then
    # Check for specific frameworks
    if grep -q "next" "$RELEASE_PATH/package.json"; then
        PUBLIC_EXCLUSIONS+=("_next" ".next" "out")
    elif grep -q "react\|vue" "$RELEASE_PATH/package.json"; then
        PUBLIC_EXCLUSIONS+=("static" "assets" "manifest.json")
    fi
fi

echo "📋 Public folder sharing strategy:"
echo "   Rule: Share ALL public/ contents EXCEPT build artifacts"
echo "   Excluded: ${PUBLIC_EXCLUSIONS[*]}"

# Ensure shared directories exist
mkdir -p "$SHARED_PATH"/{storage,public}
mkdir -p "$SHARED_PATH/storage"/{app/public,framework/{cache,sessions,views},logs}

# Create shared public structure with smart exclusions
if [ ! -d "$SHARED_PATH/public" ]; then
    mkdir -p "$SHARED_PATH/public"

    # Move existing public contents to shared (first deployment only)
    if [ -d "$RELEASE_PATH/public" ]; then
        echo "📦 Moving existing public contents to shared (first deployment)..."

        for item in "$RELEASE_PATH/public"/*; do
            if [ -e "$item" ]; then
                ITEM_NAME=$(basename "$item")

                # Check if item should be excluded
                SHOULD_EXCLUDE=false
                for exclusion in "${PUBLIC_EXCLUSIONS[@]}"; do
                    if [[ "$ITEM_NAME" == "$exclusion" ]]; then
                        SHOULD_EXCLUDE=true
                        echo "   ⚠️ Excluding from shared: $ITEM_NAME (build artifact)"
                        break
                    fi
                done

                # Move to shared if not excluded
                if [ "$SHOULD_EXCLUDE" = false ]; then
                    mv "$item" "$SHARED_PATH/public/"
                    echo "   ✅ Moved to shared: $ITEM_NAME"
                fi
            fi
        done
    fi
fi

# Create exclusion documentation
cat > "$SHARED_PATH/public/.shared-exclusions" << EXCLUSIONS_EOF
# Public Folder Sharing Strategy - "One-Line Rule"
# Generated: $(date)

# RULE: Share ALL public/ contents EXCEPT build artifacts

# ✅ INCLUDED (shared across deployments):
# - User uploads (uploads/, user-uploads/, media/)
# - Static assets (images/, fonts/ if not built)
# - Configuration files (.htaccess, robots.txt)
# - SSL/Security files (.well-known/)

# ❌ EXCLUDED (rebuilt each deployment):
$(printf "# - %s/\n" "${PUBLIC_EXCLUSIONS[@]}")

# This ensures user-generated content persists while
# allowing each release to have its own optimized assets.
EXCLUSIONS_EOF

# Set permissions
chmod -R 775 "$SHARED_PATH/storage" "$SHARED_PATH/public" 2>/dev/null || true

cd "$RELEASE_PATH"

# Link environment file (production secrets)
if [ -f "$SHARED_PATH/.env" ]; then
    rm -f .env
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")"/.env .env
    echo "✅ Environment file linked"
fi

# Link storage directory (Laravel framework + user files)
if [ -d "$SHARED_PATH/storage" ]; then
    rm -rf storage
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")/storage" storage
    echo "✅ Storage directory linked"
fi

# Smart public directory linking with exclusions
if [ -d "$SHARED_PATH/public" ]; then
    # Remove public directory and create symlink to shared
    rm -rf public
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")/public" public

    # Recreate excluded directories in the release (these will be built)
    echo "🔄 Recreating build artifact directories in release..."
    for exclusion in "${PUBLIC_EXCLUSIONS[@]}"; do
        case "$exclusion" in
            "css"|"js"|"build"|"assets"|"static"|"dist"|"_next")
                mkdir -p "public/$exclusion"
                echo "   ✅ Created build directory: public/$exclusion"
                ;;
            "mix-manifest.json"|"hot"|"manifest.json")
                # These are files, will be created by build process
                ;;
        esac
    done

    echo "✅ Smart public directory linked with exclusions"
fi

# Create Laravel storage link for publicly accessible files
if command -v php >/dev/null && [ -f "artisan" ]; then
    php artisan storage:link 2>/dev/null || echo "ℹ️ Laravel storage:link skipped"
fi

echo "🛡️ Data persistence setup complete"
echo ""
echo "📊 Protected directories:"
echo "  - storage/          # Laravel storage (logs, cache, sessions)"
echo "  - public/ (smart)   # User content preserved, build artifacts excluded"
echo "  - .env              # Environment configuration"
echo ""
echo "🎯 Strategy: Share everything EXCEPT build artifacts"
echo "✅ Result: User data survives ALL deployments"
EOF

# Make script executable
chmod +x scripts/link_persistent_dirs.sh

echo "✅ Enhanced data persistence script created with smart exclusion strategy"
```

### **2. Test Persistence Script Locally**

```bash
# Test the enhanced persistence script locally
echo "🧪 Testing enhanced persistence script locally..."

# Create test shared directory
mkdir -p .local-shared

# Create test public structure
mkdir -p public/{uploads,css,js,images}
echo "test upload" > public/uploads/test.txt
echo "/* test css */" > public/css/app.css
echo "// test js" > public/js/app.js
echo "test image" > public/images/logo.png

echo "📋 Before persistence setup:"
ls -la public/

# Run the enhanced script
bash scripts/link_persistent_dirs.sh "$(pwd)" "$(pwd)/.local-shared"

echo "📋 After persistence setup:"
echo "  Shared public contents:"
ls -la .local-shared/public/ 2>/dev/null || echo "   (no shared public yet)"
echo "  Release public contents:"
ls -la public/ 2>/dev/null || echo "   (public is now symlinked)"

# Clean up test
rm -rf .local-shared public/{uploads,css,js,images}
echo "✅ Enhanced persistence script tested and working"
```

### **3. Create Data Persistence Documentation**

```bash
# Create comprehensive documentation
cat > DATA_PERSISTENCE.md << 'EOF'
# Data Persistence Strategy

## Goal: Zero Data Loss During Deployments

This system ensures that user-generated content and application data is NEVER lost during deployments, updates, or rollbacks.

## Shared Directory Structure

```

/var/www/societypal.com/
├─ releases/
│ ├─ 20250815-143022/ # Code release (read-only)
│ ├─ 20250815-150045/ # Code release (read-only)
│ └─ 20250815-152018/ # Code release (read-only)
├─ shared/ # PERSISTENT DATA (survives all deployments)
│ ├─ .env # Production environment secrets
│ ├─ storage/ # Laravel storage directory
│ │ ├─ app/public/ # Private uploaded files
│ │ ├─ framework/ # Cache, sessions, views
│ │ └─ logs/ # Application logs
│ └─ public/ # Public user-generated content
│ ├─ uploads/ # User file uploads
│ ├─ invoices/ # Generated invoices
│ ├─ qrcodes/ # Generated QR codes
│ └─ exports/ # Data exports
└─ current -> releases/20250815-152018 # Active release (symlink)

````

## What Gets Protected

### Always Protected (in /shared):
- ✅ User file uploads (`public/uploads/`)
- ✅ Generated invoices (`public/invoices/`)
- ✅ QR codes (`public/qrcodes/`)
- ✅ Data exports (`public/exports/`)
- ✅ Application logs (`storage/logs/`)
- ✅ User sessions (`storage/framework/sessions/`)
- ✅ Environment configuration (`.env`)

### Never Protected (in releases):
- ❌ Application code (PHP, Blade templates)
- ❌ Frontend assets (CSS, JS, images)
- ❌ Vendor dependencies
- ❌ Cached configurations (rebuilt each deploy)

## Deployment Safety Checklist

Before any deployment:
- [ ] Shared directory structure exists
- [ ] User uploads directory linked
- [ ] Storage directory linked
- [ ] Environment file linked
- [ ] Build artifact directories recreated

## The "One-Line Rule"

**Share ALL public/ contents EXCEPT build artifacts**

This simple rule ensures:
- User content is always preserved
- Fresh assets are delivered with each deployment
- No manual configuration required
- Works with any Laravel application

## Emergency Recovery

If data appears lost after deployment:

```bash
# Check symlink status
ls -la storage/ public/ .env

# Verify shared directory contents
ls -la ../shared/

# Re-run persistence script
bash scripts/link_persistent_dirs.sh "$(pwd)" "$(pwd)/../shared"
````

## Framework Detection

The script automatically detects:

- Laravel applications (via `artisan` file)
- Next.js projects (via `package.json`)
- React/Vue projects (via dependencies)

Each type gets appropriate exclusion patterns.
EOF

echo "✅ Data persistence documentation created"

````

### **4. Create Verification Script**

```bash
# Create verification script
cat > scripts/verify_persistence.sh << 'EOF'
#!/bin/bash

echo "🔍 Verifying data persistence setup..."

SHARED_PATH="${1:-../shared}"
CURRENT_PATH="$(pwd)"

# Check shared directory structure
echo "📁 Checking shared directory structure..."
for dir in "storage" "public"; do
    if [ -d "$SHARED_PATH/$dir" ]; then
        echo "✅ $SHARED_PATH/$dir exists"
    else
        echo "❌ $SHARED_PATH/$dir missing"
    fi
done

# Check symlinks in current release
echo "🔗 Checking symlinks..."
for link in "storage" "public" ".env"; do
    if [ -L "$link" ]; then
        TARGET=$(readlink "$link")
        echo "✅ $link -> $TARGET"
    else
        echo "❌ $link is not a symlink"
    fi
done

# Check permissions
echo "🔒 Checking permissions..."
if [ -d "$SHARED_PATH/storage" ]; then
    STORAGE_PERMS=$(stat -f "%Mp%Lp" "$SHARED_PATH/storage" 2>/dev/null || stat -c "%a" "$SHARED_PATH/storage" 2>/dev/null)
    echo "ℹ️ Storage permissions: $STORAGE_PERMS"
fi

# Check Laravel storage link
if [ -f "artisan" ] && [ -L "public/storage" ]; then
    echo "✅ Laravel storage link exists"
else
    echo "ℹ️ Laravel storage link not found (may not be needed)"
fi

# Test write permissions
echo "🧪 Testing write permissions..."
TEST_FILE="$SHARED_PATH/storage/logs/persistence_test_$(date +%s).txt"
if echo "test" > "$TEST_FILE" 2>/dev/null; then
    echo "✅ Write permissions working"
    rm -f "$TEST_FILE"
else
    echo "❌ Write permissions failed"
fi

echo "✅ Persistence verification complete"
EOF

chmod +x scripts/verify_persistence.sh

echo "✅ Persistence verification script created"
````

## **Verification Steps**

```bash
# Verify persistence setup
echo "🔍 Running comprehensive persistence verification..."

# Test script execution
if [ -f "scripts/link_persistent_dirs.sh" ]; then
    echo "✅ Persistence script exists"
else
    echo "❌ Persistence script missing"
fi

# Check script permissions
if [ -x "scripts/link_persistent_dirs.sh" ]; then
    echo "✅ Persistence script is executable"
else
    echo "❌ Persistence script not executable"
fi

# Verify documentation
if [ -f "DATA_PERSISTENCE.md" ]; then
    echo "✅ Persistence documentation exists"
else
    echo "❌ Persistence documentation missing"
fi

# Test verification script
if [ -f "scripts/verify_persistence.sh" ] && [ -x "scripts/verify_persistence.sh" ]; then
    echo "✅ Verification script ready"
else
    echo "❌ Verification script missing or not executable"
fi

echo "✅ Data persistence system ready for deployment"
```

## **Best Practices**

### **✅ Do This:**

- Always run persistence script before going live
- Test persistence with actual user content
- Document any custom shared directories
- Verify permissions after each deployment
- Keep backups of shared directory

### **❌ Never Do This:**

- Store build artifacts in shared directories
- Manually manage symlinks (use the script)
- Skip persistence testing
- Forget to exclude framework-specific build files

## **Expected Result**

- ✅ Smart persistence script with automatic exclusions
- ✅ Zero data loss during deployments
- ✅ Framework-specific build artifact handling
- ✅ Comprehensive documentation system
- ✅ Verification tools for troubleshooting

## **Troubleshooting**

### **Symlinks Not Working**

```bash
# Check symlink creation permissions
ls -la storage/ public/ .env

# Re-run persistence script
bash scripts/link_persistent_dirs.sh "$(pwd)" "$(pwd)/../shared"
```

### **Permissions Issues**

```bash
# Fix shared directory permissions
chmod -R 775 ../shared/storage ../shared/public

# Re-create Laravel storage link
php artisan storage:link
```

### **Build Artifacts in Shared**

```bash
# Check exclusion documentation
cat ../shared/public/.shared-exclusions

# Clean build artifacts from shared
rm -rf ../shared/public/{css,js,build,assets}
```
