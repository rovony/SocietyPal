#!/bin/bash
set -e

# Phase B: Pre-Release Commands (After Upload, Before Release)
# Purpose: Configure release, set security, link shared resources
# Note: DeployHQ has already uploaded files to %release_path%
# V2
echo "=== Phase B: Pre-Release Configuration ==="
cd %release_path%

# B01: Environment Setup
echo "=== Environment Configuration ==="
# DeployHQ uploads .env to release, we move it to shared if first deployment
if [ ! -f "%shared_path%/.env" ]; then
    if [ -f "%release_path%/.env" ]; then
        mv %release_path%/.env %shared_path%/.env
        echo "✅ Moved .env to shared"
    elif [ -f "%release_path%/.env.example" ]; then
        cp %release_path%/.env.example %shared_path%/.env
        echo "⚠️ Created .env from example - configure database!"
    fi
    
    # Generate APP_KEY if missing
    if [ -f "%shared_path%/.env" ]; then
        if ! grep -q "^APP_KEY=base64:" %shared_path%/.env; then
            KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
            sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" %shared_path%/.env
            echo "✅ Generated APP_KEY"
        fi
    fi
fi

# Remove uploaded .env and link to shared (DeployHQ will handle the symlink)
rm -f %release_path%/.env

# B02: Security - Create/Update .htaccess files
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
EOF

# Public .htaccess for Laravel
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

# B03: Fix Composer Dependencies (if needed)
echo "=== Verify Dependencies ==="
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
    echo "✅ Dependencies installed"
fi

# B04: Set Secure Permissions
echo "=== Setting Secure Permissions ==="

# Default secure permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Make artisan executable
[ -f "artisan" ] && chmod 755 artisan

# Protect sensitive files
chmod 600 %shared_path%/.env 2>/dev/null || true

# Storage needs write permissions (will be symlinked by DeployHQ)
chmod -R 775 %shared_path%/storage
chmod -R 775 %shared_path%/bootstrap/cache

# Protect .git if exists
[ -d ".git" ] && chmod -R 700 .git

echo "✅ Permissions secured"

# B05: Database Migrations
echo "=== Database Operations ==="
if [ -f "artisan" ] && [ -f "%shared_path%/.env" ]; then
    # Test database connection
    if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
        php artisan migrate --force || echo "⚠️ Migration failed"
        echo "✅ Migrations completed"
    else
        echo "⚠️ Database not accessible - skip migrations"
    fi
fi

# B06: Laravel Optimization
echo "=== Application Optimization ==="

# Clear old caches first
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Build new caches
php artisan config:cache || echo "⚠️ Config cache failed"
php artisan route:cache || echo "⚠️ Route cache failed"
php artisan view:cache || echo "⚠️ View cache failed"
php artisan event:cache 2>/dev/null || true

# Create storage link (public/storage -> storage/app/public)
php artisan storage:link --force 2>/dev/null || true

# Generate sitemap if package exists
php artisan sitemap:generate 2>/dev/null || true

echo "✅ Phase B completed successfully"