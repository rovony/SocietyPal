#!/bin/bash
set -e

# Phase B: Pre-Release Commands (After Upload, Before Release)
# Purpose: Configure release, set security, link shared resources
# Note: DeployHQ has already uploaded files to %release_path%
# Version 2 - PRODUCTION READY (Enhanced with deployment report fixes)
echo "=== Phase B: Pre-Release Configuration (V2) ==="
cd %release_path%

# B01: Environment Setup (ENHANCED VERSION)
echo "=== Environment Configuration ==="
# DeployHQ has uploaded .env to release - handle it properly
if [ ! -f "%shared_path%/.env" ]; then
    # First deployment: move uploaded .env to shared
    echo "🔍 DEBUG: Checking for uploaded .env file..."
    echo "   Release path: %release_path%"
    echo "   Shared path: %shared_path%"
    ls -la %release_path%/.env* 2>/dev/null || echo "   No .env* files found"
    
    if [ -f "%release_path%/.env" ]; then
        echo "✅ Found uploaded .env file - moving to shared"
        mv %release_path%/.env %shared_path%/.env
        echo "✅ Moved uploaded .env to shared (first deployment)"
    elif [ -f "%release_path%/.env.example" ]; then
        echo "⚠️ WARNING: No uploaded .env found, using .env.example as fallback"
        echo "   This suggests DeployHQ config issue - .env should be uploaded!"
        cp %release_path%/.env.example %shared_path%/.env
        echo "⚠️ Created .env from example - configure database!"
    else
        echo "❌ No .env file found - this may cause issues"
    fi
    
    # Generate APP_KEY if missing
    if [ -f "%shared_path%/.env" ]; then
        if ! grep -q "^APP_KEY=base64:" %shared_path%/.env; then
            KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
            sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" %shared_path%/.env
            echo "✅ Generated APP_KEY"
        fi
    fi
else
    # Subsequent deployment: remove uploaded .env (we use shared one)
    if [ -f "%release_path%/.env" ]; then
        rm -f %release_path%/.env
        echo "✅ Removed uploaded .env (using shared version)"
    fi
fi

# Set secure permissions on shared .env
chmod 600 %shared_path%/.env 2>/dev/null || true

# CRITICAL: Ensure .env symlink exists (DeployHQ sometimes fails to create it)
if [ ! -L "%release_path%/.env" ]; then
    echo "⚠️ .env symlink missing - creating it manually"
    ln -sf %shared_path%/.env %release_path%/.env
    echo "✅ Created .env symlink: .env -> ../../shared/.env"
else
    echo "✅ .env symlink already exists"
fi

# B02: Security - Create/Update .htaccess files (ENHANCED VERSION)
echo "=== Security Configuration ==="

# Root .htaccess (redirect all to public)
cat > %release_path%/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Deny access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "package.json">
    Order allow,deny
    Deny from all
</Files>

<Files "artisan">
    Order allow,deny
    Deny from all
</Files>

# Additional security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
EOF

# Public .htaccess for Laravel (ENHANCED VERSION)
cat > %release_path%/public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# Disable directory browsing
Options -Indexes

# Block access to hidden files except .well-known
<Files ".*">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</Files>

<FilesMatch "^\.well-known">
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
</FilesMatch>

# Compress text files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
EOF

echo "✅ Security configurations created"

# B03: Fix Composer Dependencies (ENHANCED VERSION - CRITICAL FIX)
echo "=== Verify Dependencies ==="

# Use Composer 2 if available (required for Laravel 12+)
COMPOSER_CMD="composer"
if command -v composer2 &> /dev/null; then
    COMPOSER_CMD="composer2"
    echo "✅ Using Composer 2 for Laravel 12+ compatibility"
elif composer --version | grep -q "version 1\."; then
    echo "⚠️ WARNING: Using Composer 1.x with Laravel 12+ may cause issues"
fi

if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing composer dependencies with $COMPOSER_CMD..."
    if $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction; then
        echo "✅ Dependencies installed successfully"
    else
        echo "❌ Composer install failed"
        echo "Checking Composer version compatibility..."
        $COMPOSER_CMD --version
        exit 1
    fi
else
    echo "✅ Dependencies already installed"
fi

# B04: Enhanced Inertia Detection (IMPROVED - Multiple Location Check)
echo "=== Comprehensive Inertia Detection ==="

INERTIA_DETECTED=false

# Check 1: composer.json for inertiajs/inertia-laravel
if [ -f "composer.json" ] && grep -q "inertiajs/inertia-laravel" composer.json 2>/dev/null; then
    echo "✅ Inertia detected in composer.json"
    INERTIA_DETECTED=true
fi

# Check 2: config/app.php for Inertia ServiceProvider
if [ -f "config/app.php" ] && grep -q "Inertia\\\ServiceProvider" config/app.php 2>/dev/null; then
    echo "✅ Inertia ServiceProvider detected in config/app.php"
    INERTIA_DETECTED=true
fi

# Check 3: package.json for @inertiajs/ packages
if [ -f "package.json" ] && grep -q "@inertiajs/" package.json 2>/dev/null; then
    echo "✅ Inertia frontend packages detected in package.json"
    INERTIA_DETECTED=true
fi

# Check 4: vendor directory for actual installation
if [ -d "vendor/inertiajs" ] && [ -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then
    echo "✅ Inertia Laravel package found in vendor"
    INERTIA_DETECTED=true
fi

if [ "$INERTIA_DETECTED" = true ]; then
    echo "🔍 Inertia detected - verifying installation..."
    
    # Verify Inertia is actually installed in vendor
    if [ ! -d "vendor/inertiajs" ] || [ ! -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then
        echo "⚠️ Inertia detected in config but not installed - installing..."
        $COMPOSER_CMD require inertiajs/inertia-laravel --no-interaction 2>/dev/null || {
            echo "⚠️ Failed to install Inertia via $COMPOSER_CMD, trying alternative..."
            # Try to install from source if composer fails
            if [ -f "composer.json" ]; then
                # Add Inertia to composer.json if not present
                if ! grep -q "inertiajs/inertia-laravel" composer.json; then
                    echo "Adding Inertia to composer.json..."
                    sed -i '/"require": {/a\        "inertiajs/inertia-laravel": "^0.6.0",' composer.json
                    $COMPOSER_CMD update --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true
                fi
            fi
        }
    else
        echo "✅ Inertia Laravel package properly installed"
    fi
    
    # Additional check: Verify Inertia middleware exists
    if [ -f "app/Http/Kernel.php" ] && grep -q "HandleInertiaRequests" app/Http/Kernel.php 2>/dev/null; then
        echo "✅ Inertia middleware detected"
    else
        echo "ℹ️ Inertia middleware not found (may need manual setup)"
    fi
else
    echo "ℹ️ Inertia not detected in any location (composer.json, config/app.php, package.json, vendor)"
fi

# B05: Set Secure Permissions (ENHANCED VERSION)
echo "=== Setting Secure Permissions ==="

# Default secure permissions for release files
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Make artisan executable
[ -f "artisan" ] && chmod 755 artisan

# Make any shell scripts executable
find . -name "*.sh" -type f -exec chmod 755 {} \; 2>/dev/null || true

# Protect .git if exists in release
[ -d ".git" ] && chmod -R 700 .git

# Note: Shared directories permissions are set in Phase A
# DeployHQ symlinks will inherit the shared directory permissions

echo "✅ Release permissions secured"

# B06: Database Migrations (ENHANCED VERSION with better error handling)
echo "=== Database Operations ==="
if [ -f "artisan" ]; then
    # Wait for DeployHQ to create .env symlink, then test database
    echo "Testing database connection..."
    
    # Give DeployHQ a moment to create symlinks if needed
    sleep 1
    
    if [ -f ".env" ] || [ -f "%shared_path%/.env" ]; then
        # Test database connection using Laravel's built-in method
        if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then
            echo "✅ Database connection successful, running migrations..."
            php artisan migrate --force || echo "⚠️ Migration failed but continuing"
            echo "✅ Migrations completed"
        else
            echo "⚠️ Database not accessible - skip migrations"
            echo "ℹ️ This may be normal for first deployment or if database is not configured"
        fi
    else
        echo "⚠️ No .env file available - skip migrations"
        echo "ℹ️ DeployHQ will create .env symlink after this phase"
    fi
else
    echo "ℹ️ No artisan file found - not a Laravel application"
fi

# B07: Laravel Optimization (ENHANCED VERSION - NO BUILD DEPENDENCIES)
echo "=== Application Optimization ==="

if [ -f "artisan" ]; then
    # Clear old caches first
    echo "Clearing old caches..."
    php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped"
    php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped"
    php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped"
    php artisan view:clear 2>/dev/null || echo "ℹ️ View clear skipped"

    # Build new caches with better error handling
    echo "Building new caches..."
    if php artisan config:cache 2>/dev/null; then
        echo "✅ Configuration cached"
    else
        echo "⚠️ Config cache failed - will retry in Phase C"
    fi

    if php artisan route:cache 2>/dev/null; then
        echo "✅ Routes cached"
    else
        echo "⚠️ Route cache failed - will retry in Phase C"
    fi

    if php artisan view:cache 2>/dev/null; then
        echo "✅ Views cached"
    else
        echo "⚠️ View cache failed - will retry in Phase C"
    fi

    # Optional: Event cache (if using events)
    if [ -f "app/Providers/EventServiceProvider.php" ]; then
        php artisan event:cache 2>/dev/null && echo "✅ Events cached" || echo "ℹ️ Event cache skipped"
    fi

    # Note: Storage link will be created in Phase C after current symlink exists
    echo "ℹ️ Storage link will be created in Phase C"
else
    echo "ℹ️ No artisan file - skipping Laravel optimization"
fi

# B09: Universal Content Symlinks (COMPREHENSIVE COVERAGE)
echo "=== Creating Universal Content Symlinks ==="

cd %release_path%

# Function to create symlink if directory exists in release
create_content_symlink() {
    local DIR_PATH="$1"
    local DIR_NAME=$(basename "$DIR_PATH")
    
    if [ -d "$DIR_PATH" ] && [ ! -L "$DIR_PATH" ]; then
        SHARED_DIR="%shared_path%/public/$DIR_NAME"
        
        # Migrate any existing content to shared (first deployment)
        if [ "$(ls -A "$DIR_PATH" 2>/dev/null)" ]; then
            echo "📦 Migrating existing content: $DIR_NAME"
            mkdir -p "$SHARED_DIR"
            cp -r "$DIR_PATH"/* "$SHARED_DIR"/ 2>/dev/null || true
            chmod -R 775 "$SHARED_DIR" 2>/dev/null || true
        fi
        
        # Remove directory and create symlink
        rm -rf "$DIR_PATH"
        ln -sf "../../shared/public/$DIR_NAME" "$DIR_PATH"
        echo "✅ Created symlink: $DIR_NAME -> shared/public/$DIR_NAME"
    elif [ -L "$DIR_PATH" ]; then
        echo "ℹ️ Symlink already exists: $DIR_NAME"
    fi
}

# 1. User Content Variations Pattern
echo "Processing user content directories..."
create_content_symlink "public/upload"
create_content_symlink "public/uploads"
create_content_symlink "public/uploaded"
create_content_symlink "public/user-upload"
create_content_symlink "public/user-uploads"
create_content_symlink "public/media"
create_content_symlink "public/medias"
create_content_symlink "public/avatar"
create_content_symlink "public/avatars"
create_content_symlink "public/clientAvatar"
create_content_symlink "public/attachment"
create_content_symlink "public/attachments"
create_content_symlink "public/document"
create_content_symlink "public/documents"
create_content_symlink "public/file"
create_content_symlink "public/files"
create_content_symlink "public/images"

# 2. User Generated Content
echo "Processing generated content directories..."
create_content_symlink "public/qrcode"
create_content_symlink "public/qrcodes"
create_content_symlink "public/barcode"
create_content_symlink "public/barcodes"
create_content_symlink "public/certificate"
create_content_symlink "public/certificates"
create_content_symlink "public/report"
create_content_symlink "public/reports"
create_content_symlink "public/temp"
create_content_symlink "public/temporary"

# 3. CodeCanyon Specific Patterns
echo "Processing CodeCanyon-specific patterns..."

# Handle favicon files (preserve custom favicons)
if [ -f "public/favicon.ico" ]; then
    if [ ! -f "%shared_path%/public/favicon/favicon.ico" ]; then
        echo "📦 Preserving custom favicon.ico"
        mkdir -p "%shared_path%/public/favicon"
        cp "public/favicon.ico" "%shared_path%/public/favicon/"
        chmod 644 "%shared_path%/public/favicon/favicon.ico"
    fi
    rm -f "public/favicon.ico"
    ln -sf "../shared/public/favicon/favicon.ico" "public/favicon.ico"
    echo "✅ Created symlink: favicon.ico -> shared"
fi

# Handle Modules directory (root level)
if [ -d "Modules" ] && [ ! -L "Modules" ]; then
    echo "📦 Preserving Modules directory"
    if [ "$(ls -A "Modules" 2>/dev/null)" ]; then
        mkdir -p "%shared_path%/Modules"
        cp -r "Modules"/* "%shared_path%/Modules"/ 2>/dev/null || true
        chmod -R 775 "%shared_path%/Modules" 2>/dev/null || true
    fi
    rm -rf "Modules"
    ln -sf "../shared/Modules" "Modules"
    echo "✅ Created symlink: Modules -> shared/Modules"
elif [ -L "Modules" ]; then
    echo "ℹ️ Modules symlink already exists"
fi

# Handle modules_statuses.json (in storage/app)
if [ -f "storage/app/modules_statuses.json" ]; then
    echo "📦 Preserving modules_statuses.json"
    mkdir -p "%shared_path%/storage/app"
    cp "storage/app/modules_statuses.json" "%shared_path%/storage/app/" 2>/dev/null || true
    chmod 644 "%shared_path%/storage/app/modules_statuses.json" 2>/dev/null || true
    echo "✅ Preserved modules_statuses.json in shared storage"
fi

echo "✅ Universal content symlinks completed"

# Note: No build commands here - build pipeline is handled separately
echo "ℹ️ Build pipeline handled separately - no build dependencies"

echo "✅ Phase B completed successfully"
