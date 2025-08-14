# Troubleshooting Guide

**Purpose:** Common issues, diagnostic procedures, and resolution strategies for Laravel CodeCanyon deployment

**Use Case:** Quick issue resolution and system debugging

---

## **Analysis Source**

Based on **V3 Step files troubleshooting sections** and **V1/V2 deployment guides** with comprehensive issue resolution procedures.

---

## **1. Deployment Issues**

### **1.1: HTTP 500 Internal Server Error**

**Symptoms:**

- Website shows "Internal Server Error"
- Blank white page
- No specific error message

**Diagnosis:**

```bash
# Check application logs
tail -50 storage/logs/laravel.log

# Check web server error logs
sudo tail -50 /var/log/nginx/error.log
# OR
sudo tail -50 /var/log/apache2/error.log

# Check PHP error logs
sudo tail -50 /var/log/php8.2-fpm.log
```

**Common Causes & Solutions:**

```bash
# Issue 1: Permission problems
echo "🔧 Fixing permission issues..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Issue 2: Missing .env file
if [ ! -f ".env" ]; then
    echo "📝 Creating .env from template..."
    cp .env.example .env
    php artisan key:generate
fi

# Issue 3: Cache conflicts
echo "🗑️ Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Issue 4: Composer autoload issues
echo "🔄 Rebuilding autoloader..."
composer dump-autoload --optimize

# Issue 5: Missing APP_KEY
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi
```

### **1.2: Database Connection Issues**

**Symptoms:**

- Database connection errors
- Migration failures
- Application crashes on database operations

**Diagnosis:**

```bash
# Test database connection
echo "🗃️ Testing database connection..."

# Method 1: Using Laravel Tinker
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"

# Method 2: Direct MySQL connection
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" "$DB_DATABASE" -e "SELECT 1;"

# Check database configuration
echo "📋 Database configuration:"
cat .env | grep DB_
```

**Common Solutions:**

```bash
# Issue 1: Wrong database credentials
echo "🔧 Verifying database credentials..."
cat > verify_db.php << 'EOF'
<?php
require 'vendor/autoload.php';

$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
EOF

php verify_db.php
rm verify_db.php

# Issue 2: Database doesn't exist
echo "🗃️ Creating database if it doesn't exist..."
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;"

# Issue 3: Configuration cache issues
echo "🗑️ Clearing configuration cache..."
php artisan config:clear
php artisan config:cache

# Issue 4: Migration issues
echo "🔄 Running migrations..."
php artisan migrate --force

# Check migration status
php artisan migrate:status
```

### **1.3: Package Extraction Issues**

**Symptoms:**

- Deployment package won't extract
- Missing files after extraction
- Corrupted deployment package

**Diagnosis:**

```bash
# Check package integrity
echo "📦 Checking package integrity..."
tar -tzf ../deploy-*.tar.gz | head -10

# Check available disk space
echo "💾 Checking disk space..."
df -h .

# Check file permissions on target directory
echo "📁 Checking directory permissions..."
ls -la ..

# Verify package size
echo "📏 Package size analysis..."
ls -lh ../deploy-*.tar.gz
```

**Solutions:**

```bash
# Issue 1: Corrupted package
echo "🔧 Re-creating deployment package..."
cd ../
rm -f deploy-*.tar.gz

# Recreate package with verification
tar -czf "deploy-$(date +%Y%m%d_%H%M%S).tar.gz" current/
tar -tzf "deploy-$(date +%Y%m%d_%H%M%S).tar.gz" > package_contents.txt
echo "✅ Package recreated and verified"

# Issue 2: Insufficient disk space
echo "🗑️ Cleaning up disk space..."
# Remove old releases (keep last 3)
find releases/ -maxdepth 1 -type d -name "20*" | sort | head -n -3 | xargs rm -rf

# Clean temporary files
rm -rf /tmp/deploy_*
find . -name "*.log" -mtime +7 -delete

# Issue 3: Permission problems
echo "🔧 Fixing extraction permissions..."
chmod 755 .
chmod +x ../deploy-*.tar.gz
```

### **1.4: Symlink Issues**

**Symptoms:**

- Current symlink broken
- Website points to wrong release
- Permission denied on symlink

**Diagnosis:**

```bash
# Check current symlink
echo "🔗 Checking symlink status..."
ls -la current
readlink current

# Verify target exists
if [ -L "current" ]; then
    target=$(readlink current)
    echo "Symlink target: $target"
    if [ -d "$target" ]; then
        echo "✅ Target directory exists"
    else
        echo "❌ Target directory missing: $target"
    fi
fi

# Check available releases
echo "📁 Available releases:"
ls -la releases/
```

**Solutions:**

```bash
# Issue 1: Broken symlink
echo "🔧 Fixing broken symlink..."
rm -f current

# Find latest successful release
latest_release=$(ls -1 releases/ | grep "^20" | sort | tail -1)
if [ -n "$latest_release" ]; then
    ln -nfs "releases/$latest_release" current
    echo "✅ Symlink fixed to: $latest_release"
else
    echo "❌ No valid releases found"
fi

# Issue 2: Wrong permissions
echo "🔧 Fixing symlink permissions..."
chown -h www-data:www-data current

# Issue 3: Multiple symlinks
echo "🗑️ Cleaning duplicate symlinks..."
find . -maxdepth 1 -type l -name "current*" | sort
# Keep only 'current', remove others
find . -maxdepth 1 -type l -name "current*" ! -name "current" -delete
```

---

## **2. Performance Issues**

### **2.1: Slow Page Load Times**

**Symptoms:**

- Pages take > 5 seconds to load
- Timeout errors
- High server resource usage

**Diagnosis:**

```bash
# Check application performance
echo "⚡ Application performance analysis..."

# Test page load time
curl -o /dev/null -s -w "Time: %{time_total}s | Size: %{size_download} bytes\n" "https://your-domain.com"

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check web server status
sudo systemctl status nginx
# OR
sudo systemctl status apache2

# Monitor real-time performance
echo "📊 Real-time monitoring (press Ctrl+C to stop):"
top -p $(pgrep -d, "php-fpm|nginx|apache")
```

**Solutions:**

```bash
# Issue 1: Missing optimizations
echo "🚀 Applying Laravel optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Issue 2: Database performance
echo "🗃️ Database optimization..."
# Check slow query log
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW VARIABLES LIKE 'slow_query_log';"

# Optimize database tables
php artisan optimize:clear
php artisan optimize

# Issue 3: OPcache not enabled
echo "🔧 Checking OPcache status..."
php -m | grep -i opcache
php --ri opcache | grep "opcache.enable"

# Enable OPcache if not enabled
if ! php -m | grep -q OPcache; then
    echo "📝 OPcache configuration needed in PHP configuration"
fi

# Issue 4: Too many files in storage
echo "🗑️ Cleaning storage directories..."
find storage/logs/ -name "*.log" -mtime +30 -delete
find storage/app/temp/ -mtime +7 -delete 2>/dev/null || true
```

### **2.2: High Memory Usage**

**Symptoms:**

- Out of memory errors
- PHP fatal errors (memory limit)
- Server running out of RAM

**Diagnosis:**

```bash
# Check memory usage
echo "💾 Memory usage analysis..."
free -h

# Check PHP memory limits
echo "🐘 PHP memory configuration:"
php -ini | grep memory_limit

# Check process memory usage
echo "📊 Top memory consumers:"
ps aux --sort=-%mem | head -10

# Check for memory leaks
echo "🔍 Memory leak detection:"
tail -100 storage/logs/laravel.log | grep -i "memory"
```

**Solutions:**

```bash
# Issue 1: PHP memory limit too low
echo "🔧 Checking PHP memory limit..."
current_limit=$(php -r "echo ini_get('memory_limit');")
echo "Current PHP memory limit: $current_limit"

# Increase if needed (requires server access)
if [ "$current_limit" = "128M" ]; then
    echo "📝 Consider increasing PHP memory_limit to 256M or 512M"
fi

# Issue 2: Large file processing
echo "🔧 Optimizing large file handling..."
# Use chunked processing for large imports
# Implement queue system for heavy operations

# Issue 3: Memory leaks in code
echo "🔍 Memory optimization..."
# Clear large variables
php artisan queue:work --timeout=60 --memory=512

# Issue 4: Too many processes
echo "📊 Process optimization..."
# Check PHP-FPM pool configuration
sudo cat /etc/php/8.2/fpm/pool.d/www.conf | grep -E "(pm.max_children|pm.start_servers)"
```

### **2.3: Database Performance Issues**

**Symptoms:**

- Slow database queries
- Connection timeouts
- High database CPU usage

**Diagnosis:**

```bash
# Database performance analysis
echo "🗃️ Database performance check..."

# Check MySQL process list
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW PROCESSLIST;"

# Check slow queries
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW VARIABLES LIKE 'slow_query_log%';"

# Database size analysis
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "
SELECT
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = '$DB_DATABASE'
GROUP BY table_schema;"

# Check table status
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLE STATUS;"
```

**Solutions:**

```bash
# Issue 1: Missing indexes
echo "🔧 Database optimization..."

# Check for missing indexes (requires analysis)
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
SELECT DISTINCT
    CONCAT('ALTER TABLE ', table_name, ' ADD INDEX (', column_name, ');') AS 'Suggested Index'
FROM information_schema.columns
WHERE table_schema = '$DB_DATABASE'
AND column_name LIKE '%_id'
AND column_name != 'id';"

# Issue 2: Large tables without optimization
echo "🗃️ Table optimization..."
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "OPTIMIZE TABLE users, sessions, jobs;"

# Issue 3: Connection pool exhaustion
echo "🔧 Connection optimization..."
# Check current connections
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW STATUS LIKE 'Threads_connected';"

# Issue 4: Query cache issues
echo "📊 Query cache analysis..."
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW VARIABLES LIKE 'query_cache%';"
```

---

## **3. Security Issues**

### **3.1: SSL Certificate Problems**

**Symptoms:**

- SSL warnings in browser
- Certificate expired
- Mixed content warnings

**Diagnosis:**

```bash
# Check SSL certificate
echo "🔒 SSL certificate analysis..."
openssl s_client -servername your-domain.com -connect your-domain.com:443 < /dev/null 2>/dev/null | openssl x509 -noout -dates

# Check certificate chain
echo "🔗 Certificate chain verification..."
openssl s_client -servername your-domain.com -connect your-domain.com:443 -showcerts < /dev/null

# Check SSL configuration
echo "🔧 SSL configuration check..."
curl -I https://your-domain.com | grep -i "strict-transport-security"
```

**Solutions:**

```bash
# Issue 1: Expired certificate
echo "🔄 Certificate renewal..."
# For Let's Encrypt
sudo certbot renew --dry-run
sudo certbot renew

# Issue 2: Mixed content
echo "🔧 Fixing mixed content..."
# Force HTTPS in Laravel
grep -r "APP_URL=https" .env || echo "⚠️ Set APP_URL=https://your-domain.com in .env"

# Issue 3: Certificate chain incomplete
echo "🔗 Certificate chain fix..."
# Check if intermediate certificates are installed
# May require certificate authority support

# Issue 4: SSL redirect not working
echo "🔄 SSL redirect configuration..."
# Check web server configuration
sudo nginx -t
# OR
sudo apache2ctl configtest
```

### **3.2: Authentication Issues**

**Symptoms:**

- Users can't log in
- Session issues
- Authentication loops

**Diagnosis:**

```bash
# Check authentication system
echo "🔐 Authentication system check..."

# Check session configuration
php artisan tinker --execute="dd(config('session'));"

# Check authentication guards
php artisan tinker --execute="dd(config('auth.guards'));"

# Check user table
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "DESCRIBE users; SELECT COUNT(*) as user_count FROM users;"

# Check session storage
ls -la storage/framework/sessions/
```

**Solutions:**

```bash
# Issue 1: Session driver problems
echo "🔧 Session configuration fix..."
# Check session driver in .env
grep "SESSION_DRIVER" .env

# Clear sessions
php artisan session:table --force 2>/dev/null || echo "File sessions in use"
rm -rf storage/framework/sessions/*

# Issue 2: Cache problems
echo "🗑️ Authentication cache clear..."
php artisan auth:clear-resets
php artisan cache:clear

# Issue 3: Database authentication issues
echo "🗃️ User table verification..."
# Check if users table exists and is properly structured
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES LIKE 'users';"

# Issue 4: Middleware conflicts
echo "🔧 Middleware troubleshooting..."
# Check for conflicting middleware
grep -r "auth" app/Http/Middleware/
```

### **3.3: File Permission Issues**

**Symptoms:**

- Permission denied errors
- Can't write to storage
- Upload failures

**Diagnosis:**

```bash
# Check file permissions
echo "📁 File permission analysis..."
ls -la storage/
ls -la bootstrap/cache/

# Check web server user
echo "👤 Web server user identification..."
ps aux | grep -E "(nginx|apache|www-data)" | head -3

# Check ownership
echo "👑 File ownership check..."
stat -c "%U:%G" storage/
stat -c "%U:%G" bootstrap/cache/

# Check writable directories
echo "✍️ Writable directory check..."
find storage/ -type d ! -writable
find bootstrap/cache/ -type d ! -writable
```

**Solutions:**

```bash
# Issue 1: Wrong permissions
echo "🔧 Fixing file permissions..."
# Set correct permissions for Laravel
find storage/ -type f -exec chmod 644 {} \;
find storage/ -type d -exec chmod 755 {} \;
find bootstrap/cache/ -type f -exec chmod 644 {} \;
find bootstrap/cache/ -type d -exec chmod 755 {} \;

# Issue 2: Wrong ownership
echo "👑 Fixing file ownership..."
# Find web server user
WEB_USER=$(ps aux | grep -E "(nginx|apache)" | grep -v grep | head -1 | awk '{print $1}')
if [ -n "$WEB_USER" ]; then
    sudo chown -R "$WEB_USER:$WEB_USER" storage/ bootstrap/cache/
    echo "✅ Ownership changed to $WEB_USER"
fi

# Issue 3: SELinux issues (if applicable)
if command -v getenforce >/dev/null 2>&1; then
    echo "🛡️ SELinux status: $(getenforce)"
    if [ "$(getenforce)" = "Enforcing" ]; then
        echo "⚠️ SELinux may be blocking file access"
        # setsebool -P httpd_can_network_connect 1
        # setsebool -P httpd_unified 1
    fi
fi

# Issue 4: Disk space issues
echo "💾 Disk space check..."
df -h storage/
if [ $(df -P storage/ | tail -1 | awk '{print $5}' | sed 's/%//') -gt 90 ]; then
    echo "⚠️ Disk space critically low"
    find storage/logs/ -name "*.log" -mtime +7 -delete
fi
```

---

## **4. CodeCanyon-Specific Issues**

### **4.1: License Verification Failures**

**Symptoms:**

- "Invalid license" errors
- Application won't start
- License verification timeouts

**Diagnosis:**

```bash
# Check license file
echo "🔑 License file verification..."
if [ -f "shared/licenses/production.license" ]; then
    echo "✅ License file exists"
    ls -la shared/licenses/production.license

    # Check file content (safely)
    echo "📝 License file size: $(wc -c < shared/licenses/production.license) bytes"

    # Check if file is readable
    if [ -r "shared/licenses/production.license" ]; then
        echo "✅ License file is readable"
    else
        echo "❌ License file not readable"
    fi
else
    echo "❌ License file missing"
fi

# Check license in environment
echo "🔍 Environment license check..."
grep -i license .env || echo "No license configuration in .env"

# Check application license system
echo "🔧 Application license system..."
php artisan tinker --execute="
if (class_exists('App\Services\LicenseService')) {
    echo 'License service exists';
} else {
    echo 'No license service found';
}"
```

**Solutions:**

```bash
# Issue 1: Missing license file
echo "🔧 Installing license file..."
if [ ! -f "shared/licenses/production.license" ]; then
    mkdir -p shared/licenses
    echo "📝 Please place production.license in shared/licenses/"
    echo "📥 Download from CodeCanyon account > Downloads > License"
fi

# Issue 2: Wrong file permissions
echo "🔒 Fixing license file permissions..."
if [ -f "shared/licenses/production.license" ]; then
    chmod 600 shared/licenses/production.license
    chown www-data:www-data shared/licenses/production.license
fi

# Issue 3: Domain mismatch
echo "🌐 Domain verification..."
current_domain=$(grep APP_URL .env | cut -d'=' -f2)
echo "Current domain: $current_domain"
echo "⚠️ Ensure license is bound to: $current_domain"

# Issue 4: License verification service down
echo "🔧 License verification troubleshooting..."
# Test external connectivity if license uses online verification
curl -I https://api.envato.com/ || echo "⚠️ External license verification may be blocked"
```

### **4.2: Customization Layer Conflicts**

**Symptoms:**

- Custom features stop working
- Class conflicts
- Missing custom files after update

**Diagnosis:**

```bash
# Check customization layer
echo "🔍 Customization layer verification..."
if [ -d "app/Custom" ]; then
    echo "✅ Custom directory exists"
    echo "📁 Custom files found:"
    find app/Custom -name "*.php" | head -10

    # Check for conflicts
    echo "🔍 Checking for potential conflicts..."
    for custom_file in $(find app/Custom -name "*.php"); do
        basename_file=$(basename "$custom_file")
        vendor_equivalent=$(find app/ -name "$basename_file" ! -path "*/Custom/*")
        if [ -n "$vendor_equivalent" ]; then
            echo "⚠️ Potential conflict: $custom_file vs $vendor_equivalent"
        fi
    done
else
    echo "❌ Custom directory missing"
fi

# Check autoloader
echo "🔧 Autoloader check..."
composer dump-autoload -o
```

**Solutions:**

```bash
# Issue 1: Missing custom files
echo "🔄 Restoring custom files..."
if [ -d "Admin-Local/vendor_snapshots" ]; then
    latest_snapshot=$(ls -1 Admin-Local/vendor_snapshots/ | sort | tail -1)
    if [ -n "$latest_snapshot" ] && [ -d "Admin-Local/vendor_snapshots/$latest_snapshot/app/Custom" ]; then
        echo "📋 Restoring from snapshot: $latest_snapshot"
        cp -r "Admin-Local/vendor_snapshots/$latest_snapshot/app/Custom" app/
        echo "✅ Custom files restored"
    fi
fi

# Issue 2: Autoloader conflicts
echo "🔧 Resolving autoloader conflicts..."
composer dump-autoload --optimize
php artisan clear-compiled
php artisan optimize

# Issue 3: Namespace conflicts
echo "🔍 Namespace conflict resolution..."
# Check for namespace issues
grep -r "namespace.*Custom" app/Custom/ | head -5

# Issue 4: Service provider registration
echo "🔧 Service provider check..."
grep -r "CustomServiceProvider" config/ app/
```

### **4.3: Vendor Update Conflicts**

**Symptoms:**

- Update process fails
- Vendor files overwrite customizations
- Application breaks after update

**Diagnosis:**

```bash
# Check update workspace
echo "🔍 Update process diagnosis..."
if [ -d "Admin-Local/update_workspace_*" ]; then
    latest_workspace=$(ls -1 Admin-Local/ | grep update_workspace | sort | tail -1)
    echo "📁 Latest update workspace: $latest_workspace"

    if [ -f "Admin-Local/$latest_workspace/UPDATE_LOG.txt" ]; then
        echo "📋 Update log:"
        tail -20 "Admin-Local/$latest_workspace/UPDATE_LOG.txt"
    fi
fi

# Check vendor snapshots
echo "📸 Vendor snapshot analysis..."
if [ -d "Admin-Local/vendor_snapshots" ]; then
    echo "📁 Available snapshots:"
    ls -la Admin-Local/vendor_snapshots/
fi

# Check for incomplete updates
echo "🔍 Incomplete update detection..."
find . -name "*.backup" -o -name "*.orig" -o -name "*.tmp"
```

**Solutions:**

```bash
# Issue 1: Restore from snapshot
echo "🔄 Emergency restore from snapshot..."
if [ -d "Admin-Local/vendor_snapshots" ]; then
    echo "📋 Available snapshots:"
    ls -1 Admin-Local/vendor_snapshots/ | sort

    read -p "Enter snapshot name to restore from: " snapshot_name
    if [ -d "Admin-Local/vendor_snapshots/$snapshot_name" ]; then
        echo "⚠️ This will overwrite current files. Continue? (y/N)"
        read -r confirm
        if [ "$confirm" = "y" ]; then
            cp -r "Admin-Local/vendor_snapshots/$snapshot_name"/* ./
            composer install
            php artisan migrate
            echo "✅ Restored from snapshot: $snapshot_name"
        fi
    fi
fi

# Issue 2: Re-run safe update process
echo "🔄 Re-running safe update process..."
if [ -f "safe_vendor_update.sh" ]; then
    ./safe_vendor_update.sh
else
    echo "❌ Safe update script not found"
fi

# Issue 3: Manual conflict resolution
echo "🔧 Manual conflict resolution..."
echo "📝 Review VENDOR_UPDATE_REPORT_*.md for detailed conflict information"
find . -name "VENDOR_UPDATE_REPORT_*.md" | sort | tail -1
```

---

## **5. Environment-Specific Issues**

### **5.1: Local Development Issues**

**Symptoms:**

- Herd not working
- Local database connection issues
- Asset compilation problems

**Diagnosis:**

```bash
# Check Herd status
echo "🐘 Herd service check..."
herd status 2>/dev/null || echo "Herd not available"

# Check local database
echo "🗃️ Local database check..."
mysql -u root -e "SELECT 1;" 2>/dev/null || echo "MySQL not accessible"

# Check Node.js and npm
echo "📦 Node.js environment..."
node --version 2>/dev/null || echo "Node.js not installed"
npm --version 2>/dev/null || echo "NPM not installed"
```

**Solutions:**

```bash
# Issue 1: Herd service problems
echo "🔧 Herd troubleshooting..."
# Restart Herd services
herd restart 2>/dev/null || echo "Use Herd GUI to restart services"

# Issue 2: Database connection
echo "🗃️ Local database fix..."
# Check if database exists
mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravel_local;"

# Issue 3: Asset compilation
echo "📦 Asset compilation fix..."
if [ -f "package.json" ]; then
    npm install
    npm run build
else
    echo "No package.json found - no assets to compile"
fi

# Issue 4: Permission issues on macOS
echo "🔧 macOS permission fix..."
# Fix common macOS permission issues
chmod +x artisan
sudo chown -R $(whoami) storage/ bootstrap/cache/
```

### **5.2: Staging Environment Issues**

**Symptoms:**

- Staging site not accessible
- Different behavior than production
- Deployment failures

**Diagnosis:**

```bash
# Check staging environment
echo "🎭 Staging environment check..."
echo "Environment: $(grep APP_ENV .env | cut -d'=' -f2)"
echo "Debug mode: $(grep APP_DEBUG .env | cut -d'=' -f2)"
echo "URL: $(grep APP_URL .env | cut -d'=' -f2)"

# Check staging-specific configurations
echo "🔧 Staging configuration..."
php artisan config:show app.env
php artisan config:show app.debug
```

**Solutions:**

```bash
# Issue 1: Wrong environment settings
echo "🔧 Staging environment fix..."
# Ensure correct staging settings
grep -q "APP_ENV=staging" .env || echo "APP_ENV=staging" >> .env
grep -q "APP_DEBUG=false" .env || sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Issue 2: Cache conflicts with production
echo "🗑️ Staging cache clear..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Issue 3: Database sync issues
echo "🗃️ Staging database sync..."
# Run migrations to ensure database is current
php artisan migrate --force

# Issue 4: Asset issues
echo "📦 Staging asset compilation..."
npm run production 2>/dev/null || echo "No frontend build process"
```

### **5.3: Production Environment Issues**

**Symptoms:**

- Production site down
- Performance degradation
- Security alerts

**Diagnosis:**

```bash
# Production health check
echo "🏥 Production health check..."
curl -I https://your-domain.com | head -1

# Check system resources
echo "📊 System resources..."
free -h
df -h
uptime

# Check critical services
echo "🔧 Service status..."
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

**Emergency Solutions:**

```bash
# Issue 1: Site completely down
echo "🚨 Emergency site recovery..."
# Quick rollback to previous release
if [ -L "current" ] && [ -d "releases" ]; then
    current_release=$(readlink current)
    previous_release=$(ls -1 releases/ | grep "^20" | sort | tail -2 | head -1)

    if [ -n "$previous_release" ] && [ "$previous_release" != "$(basename $current_release)" ]; then
        echo "🔄 Rolling back to: $previous_release"
        ln -nfs "releases/$previous_release" current
        echo "✅ Emergency rollback complete"
    fi
fi

# Issue 2: Database issues
echo "🗃️ Database emergency check..."
# Check if database is accessible
php artisan migrate:status || echo "❌ Database migration issues"

# Issue 3: High resource usage
echo "📊 Resource usage mitigation..."
# Enable maintenance mode to reduce load
php artisan down --message="Maintenance in progress" --retry=60

# Issue 4: Security incident
echo "🚨 Security incident response..."
# Basic security lockdown
chmod 600 .env
find storage/ -name "*.log" -exec chmod 600 {} \;
echo "🔒 Emergency security lockdown applied"
```

---

## **6. Monitoring and Diagnostics**

### **6.1: System Health Diagnostics**

```bash
# Comprehensive system health check
create_health_check_script() {
    cat > health_check.sh << 'EOF'
#!/bin/bash

echo "🏥 System Health Check - $(date)"
echo "================================"

# Application health
echo ""
echo "📱 Application Status:"
if curl -s -f https://your-domain.com >/dev/null; then
    echo "✅ Website accessible"
else
    echo "❌ Website not accessible"
fi

# Database health
echo ""
echo "🗃️ Database Status:"
if php artisan migrate:status >/dev/null 2>&1; then
    echo "✅ Database connected"
else
    echo "❌ Database connection failed"
fi

# Storage health
echo ""
echo "💾 Storage Status:"
storage_usage=$(df -h storage/ | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$storage_usage" -lt 80 ]; then
    echo "✅ Storage usage normal ($storage_usage%)"
else
    echo "⚠️ Storage usage high ($storage_usage%)"
fi

# Service health
echo ""
echo "🔧 Service Status:"
services=("nginx" "php8.2-fpm" "mysql")
for service in "${services[@]}"; do
    if sudo systemctl is-active "$service" >/dev/null 2>&1; then
        echo "✅ $service running"
    else
        echo "❌ $service not running"
    fi
done

# Performance metrics
echo ""
echo "📊 Performance Metrics:"
load_avg=$(uptime | awk -F'load average:' '{print $2}' | cut -d',' -f1)
echo "Load average: $load_avg"

memory_usage=$(free | awk 'NR==2{printf "%.2f%%", $3*100/$2}')
echo "Memory usage: $memory_usage"

# Log check
echo ""
echo "📋 Recent Errors:"
error_count=$(grep -c "ERROR" storage/logs/laravel.log 2>/dev/null || echo 0)
echo "Application errors (last 24h): $error_count"

if [ "$error_count" -gt 0 ]; then
    echo "Recent errors:"
    tail -20 storage/logs/laravel.log | grep ERROR | tail -3
fi

echo ""
echo "🏁 Health check complete"
EOF

    chmod +x health_check.sh
    echo "✅ Health check script created: health_check.sh"
}
```

### **6.2: Performance Diagnostics**

```bash
# Performance analysis tools
create_performance_check() {
    cat > performance_check.sh << 'EOF'
#!/bin/bash

echo "⚡ Performance Analysis - $(date)"
echo "================================"

# Response time test
echo ""
echo "🌐 Response Time Test:"
for i in {1..5}; do
    response_time=$(curl -o /dev/null -s -w "%{time_total}" https://your-domain.com)
    echo "Test $i: ${response_time}s"
done

# Database performance
echo ""
echo "🗃️ Database Performance:"
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
SELECT
    COUNT(*) as query_count,
    AVG(query_time) as avg_time,
    MAX(query_time) as max_time
FROM information_schema.processlist
WHERE db = '$DB_DATABASE';"

# PHP performance
echo ""
echo "🐘 PHP Performance:"
php -m | grep -i opcache >/dev/null && echo "✅ OPcache enabled" || echo "❌ OPcache disabled"

opcache_status=$(php -r "echo json_encode(opcache_get_status());")
if [ "$opcache_status" != "false" ]; then
    echo "OPcache hit rate: $(echo $opcache_status | jq -r '.opcache_statistics.opcache_hit_rate')%"
fi

# Cache performance
echo ""
echo "📦 Cache Performance:"
if php artisan cache:status >/dev/null 2>&1; then
    echo "✅ Application cache working"
else
    echo "❌ Application cache issues"
fi

echo ""
echo "📊 Performance check complete"
EOF

    chmod +x performance_check.sh
    echo "✅ Performance check script created: performance_check.sh"
}
```

---

## **7. Emergency Procedures**

### **7.1: Emergency Rollback**

```bash
# Emergency rollback procedure
emergency_rollback() {
    echo "🚨 EMERGENCY ROLLBACK PROCEDURE"
    echo "==============================="

    # Stop further deployments
    touch .maintenance_mode

    # Quick rollback to previous release
    if [ -L "current" ] && [ -d "releases" ]; then
        current_release=$(readlink current)
        echo "Current release: $(basename $current_release)"

        # Find previous release
        previous_release=$(ls -1 releases/ | grep "^20" | sort | tail -2 | head -1)

        if [ -n "$previous_release" ]; then
            echo "Rolling back to: $previous_release"

            # Atomic rollback
            ln -nfs "releases/$previous_release" current

            # Clear caches
            php artisan cache:clear
            php artisan config:clear

            echo "✅ Emergency rollback complete"
            echo "🔍 Verify site functionality"
        else
            echo "❌ No previous release found"
        fi
    else
        echo "❌ Release structure not found"
    fi

    # Log the emergency rollback
    echo "$(date): Emergency rollback executed" >> emergency.log
}
```

### **7.2: System Recovery**

```bash
# System recovery procedure
system_recovery() {
    echo "🔧 SYSTEM RECOVERY PROCEDURE"
    echo "============================"

    # Check and fix basic issues
    echo "1. Checking file permissions..."
    chmod -R 755 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache

    echo "2. Clearing all caches..."
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    echo "3. Rebuilding autoloader..."
    composer dump-autoload --optimize

    echo "4. Running migrations..."
    php artisan migrate --force

    echo "5. Rebuilding caches..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "6. Testing application..."
    php artisan about

    echo "✅ System recovery complete"
}
```

---

## **8. Quick Reference Commands**

### **8.1: Diagnostic Commands**

```bash
# Application diagnostics
php artisan about                    # Application overview
php artisan route:list              # Available routes
php artisan config:show             # Current configuration
php artisan migrate:status          # Migration status
php artisan queue:failed            # Failed jobs

# System diagnostics
tail -50 storage/logs/laravel.log    # Application logs
sudo tail -50 /var/log/nginx/error.log  # Web server logs
df -h                               # Disk usage
free -h                             # Memory usage
top                                 # Process monitor

# Database diagnostics
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES;"
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW PROCESSLIST;"
```

### **8.2: Recovery Commands**

```bash
# Quick fixes
php artisan optimize:clear          # Clear all caches
php artisan optimize               # Rebuild optimizations
composer install --no-dev --optimize-autoloader
npm run production                 # Rebuild assets

# Permission fixes
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod 600 .env

# Service restarts
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
```

### **8.3: Emergency Commands**

```bash
# Emergency maintenance mode
php artisan down --message="Emergency maintenance" --retry=300

# Emergency rollback (if symlinked deployment)
ln -nfs releases/$(ls -1 releases/ | sort | tail -2 | head -1) current

# Emergency database backup
mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > emergency_backup_$(date +%Y%m%d_%H%M%S).sql

# Emergency site disable (Nginx)
sudo mv /etc/nginx/sites-enabled/your-domain.com /etc/nginx/sites-available/
sudo systemctl reload nginx
```

---

## **9. Prevention and Best Practices**

### **9.1: Monitoring Setup**

```bash
# Setup continuous monitoring
setup_monitoring() {
    # Health check cron job
    (crontab -l 2>/dev/null; echo "*/5 * * * * $PWD/health_check.sh >> health_monitor.log 2>&1") | crontab -

    # Performance check cron job
    (crontab -l 2>/dev/null; echo "0 */6 * * * $PWD/performance_check.sh >> performance_monitor.log 2>&1") | crontab -

    # Log rotation
    (crontab -l 2>/dev/null; echo "0 2 * * * find storage/logs/ -name '*.log' -mtime +7 -delete") | crontab -

    echo "✅ Monitoring setup complete"
}
```

### **9.2: Backup Verification**

```bash
# Regular backup verification
verify_backups() {
    echo "🔍 Backup Verification"

    # Check database backups
    if [ -d "backups/database" ]; then
        latest_db_backup=$(ls -1 backups/database/ | sort | tail -1)
        if [ -n "$latest_db_backup" ]; then
            echo "✅ Latest database backup: $latest_db_backup"
            # Verify backup can be restored
            mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS backup_test;"
            mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" backup_test < "backups/database/$latest_db_backup"
            mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "DROP DATABASE backup_test;"
            echo "✅ Database backup verified"
        fi
    fi

    # Check file backups
    if [ -d "backups/files" ]; then
        latest_file_backup=$(ls -1 backups/files/ | sort | tail -1)
        echo "✅ Latest file backup: $latest_file_backup"
    fi
}
```

---

## **Related Documentation**

### **Core References:**

- **Emergency Procedures:** [Emergency_Procedures.md](../3-Maintenance/Emergency_Procedures.md)
- **Performance Monitoring:** [Performance_Monitoring.md](../3-Maintenance/Performance_Monitoring.md)
- **Server Monitoring:** [Server_Monitoring.md](../3-Maintenance/Server_Monitoring.md)

### **Understanding Guides:**

- **Deployment Concepts:** [Deployment_Concepts.md](Deployment_Concepts.md)
- **CodeCanyon Specifics:** [CodeCanyon_Specifics.md](CodeCanyon_Specifics.md)
- **Best Practices:** [Best_Practices.md](Best_Practices.md)

### **Quick Reference:**

- **FAQ:** [FAQ_Common_Issues.md](FAQ_Common_Issues.md)
- **Terminology:** [Terminology_Definitions.md](Terminology_Definitions.md)

---

## **Troubleshooting Checklist**

When encountering issues:

### **Initial Response:**

- [ ] Check if site is accessible from browser
- [ ] Review recent deployments or changes
- [ ] Check application logs (storage/logs/laravel.log)
- [ ] Verify basic system resources (disk, memory)
- [ ] Test database connectivity

### **Systematic Diagnosis:**

- [ ] Run health check script
- [ ] Check web server logs
- [ ] Verify file permissions
- [ ] Test configuration integrity
- [ ] Review recent commits/changes

### **Resolution Process:**

- [ ] Apply appropriate fix from this guide
- [ ] Test fix thoroughly
- [ ] Document the issue and solution
- [ ] Update monitoring if needed
- [ ] Prevent recurrence with process improvements

### **Recovery Verification:**

- [ ] Confirm site fully functional
- [ ] Test critical user flows
- [ ] Verify data integrity
- [ ] Check performance metrics
- [ ] Update stakeholders on resolution
