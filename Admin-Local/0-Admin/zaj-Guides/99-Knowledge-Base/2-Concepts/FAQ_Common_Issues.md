# FAQ Common Issues

**Purpose:** Frequently asked questions and quick solutions for Laravel CodeCanyon deployment

**Use Case:** Quick reference for common problems, immediate solutions, and rapid troubleshooting

---

## **Analysis Source**

Based on **V1/V2 troubleshooting sections** and **real-world deployment issues** with quick-reference solutions.

---

## **1. Setup and Installation FAQs**

### **Q1: "Cannot install Laravel dependencies - Composer memory limit exceeded"**

**Quick Solution:**

```bash
# Increase PHP memory limit
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/cli/php.ini

# Or run with unlimited memory
php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader
```

**Prevention:** Always check PHP memory limits before starting large projects.

---

### **Q2: "Permission denied when accessing storage/logs directory"**

**Quick Solution:**

```bash
# Fix Laravel permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
sudo chmod -R 644 storage/logs/*

# For development (not production):
sudo chmod -R 777 storage bootstrap/cache
```

**Root Cause:** Incorrect file ownership and permissions after extraction.

---

### **Q3: "PHP extensions missing (mbstring, openssl, pdo, etc.)"**

**Quick Solution:**

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install php8.2-mbstring php8.2-openssl php8.2-pdo php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# CentOS/RHEL
sudo yum install php-mbstring php-openssl php-pdo php-mysql php-xml php-curl php-zip php-gd

# Restart web server
sudo systemctl restart nginx php8.2-fpm
```

**Verification:**

```bash
php -m | grep -E "mbstring|openssl|pdo|mysql"
```

---

### **Q4: "MySQL connection refused during setup"**

**Quick Solution:**

```bash
# Check MySQL service
sudo systemctl status mysql
sudo systemctl start mysql

# Test connection
mysql -u root -p -e "SELECT 1;"

# Check MySQL is listening
netstat -tuln | grep :3306

# Common fix for socket issues
sudo ln -sf /var/run/mysqld/mysqld.sock /tmp/mysql.sock
```

**Environment Check:**

```bash
# Verify .env settings
grep -E "DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD" .env
```

---

## **2. Configuration FAQs**

### **Q5: "APP_KEY not set - Please generate application key"**

**Quick Solution:**

```bash
# Generate new application key
php artisan key:generate

# Verify key was set
grep APP_KEY .env

# If still issues, manually set
php artisan key:generate --show
# Copy the key to .env file
```

**Prevention:** Always run `php artisan key:generate` after copying `.env.example`.

---

### **Q6: "Environment file not found or not readable"**

**Quick Solution:**

```bash
# Check if .env exists
ls -la .env

# If missing, copy from example
cp .env.example .env

# Fix permissions
chmod 644 .env
sudo chown www-data:www-data .env

# Verify content
head .env
```

**Common Locations:**

```bash
# .env should be in project root, same level as artisan
find . -name ".env" -type f
```

---

### **Q7: "HTTPS mixed content warnings after SSL setup"**

**Quick Solution:**

```bash
# Update .env file
sed -i 's/APP_URL=http:/APP_URL=https:/' .env

# Force HTTPS in Laravel
echo "FORCE_HTTPS=true" >> .env
```

**Laravel Configuration (config/app.php):**

```php
// Add to config/app.php if not using .env
'force_https' => env('FORCE_HTTPS', false),
```

**Service Provider Fix:**

```php
// In AppServiceProvider boot() method
if (config('app.force_https')) {
    URL::forceScheme('https');
}
```

---

## **3. Database FAQs**

### **Q8: "Migration failed - Table already exists"**

**Quick Solution:**

```bash
# Check migration status
php artisan migrate:status

# Reset migrations (CAUTION: loses data)
php artisan migrate:fresh

# Or rollback specific migration
php artisan migrate:rollback --step=1

# For production (safer approach)
php artisan migrate --force
```

**Manual Fix:**

```sql
-- Check existing tables
SHOW TABLES;

-- Drop problematic table if safe
DROP TABLE IF EXISTS problematic_table;

-- Re-run migrations
php artisan migrate --force
```

---

### **Q9: "SQLSTATE[HY000] [2002] Connection refused"**

**Quick Solution:**

```bash
# Check MySQL/MariaDB status
sudo systemctl status mysql
sudo systemctl start mysql

# Check if MySQL is running
ps aux | grep mysql

# Test connection with credentials
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -e "SELECT 1;"

# Check MySQL configuration
sudo cat /etc/mysql/mysql.conf.d/mysqld.cnf | grep bind-address
```

**Socket Issue Fix:**

```bash
# Find MySQL socket
sudo find /var -name "mysql.sock" 2>/dev/null

# Update PHP configuration if needed
sudo grep -r "mysql.default_socket" /etc/php/
```

---

### **Q10: "Database seeding fails with foreign key constraint errors"**

**Quick Solution:**

```bash
# Disable foreign key checks during seeding
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SET FOREIGN_KEY_CHECKS=0;"

# Run seeding
php artisan db:seed

# Re-enable foreign key checks
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SET FOREIGN_KEY_CHECKS=1;"
```

**Alternative Approach:**

```php
// In seeder file
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// Seeding code here
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

---

## **4. Web Server FAQs**

### **Q11: "404 Error for all routes except homepage"**

**Quick Solution:**

```bash
# Enable Apache mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Check .htaccess exists
ls -la public/.htaccess
```

**Nginx Fix:**

```nginx
# Add to nginx site configuration
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**Apache VirtualHost Fix:**

```apache
<Directory "/path/to/project/public">
    AllowOverride All
    Require all granted
</Directory>
```

---

### **Q12: "500 Internal Server Error after deployment"**

**Quick Solution:**

```bash
# Check error logs immediately
sudo tail -f /var/log/nginx/error.log
sudo tail -f storage/logs/laravel.log

# Common fixes
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

**Debugging Steps:**

```bash
# Enable debug mode temporarily
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env

# Check PHP error log
sudo tail -f /var/log/php8.2-fpm.log
```

---

### **Q13: "SSL certificate issues - Site not secure"**

**Quick Solution:**

```bash
# Check certificate status
openssl s_client -servername your-domain.com -connect your-domain.com:443 < /dev/null 2>/dev/null | grep -A 2 "Verify return code"

# Renew Let's Encrypt certificate
sudo certbot renew --dry-run
sudo certbot renew

# Restart web server
sudo systemctl restart nginx
```

**Certificate Installation Check:**

```bash
# Verify certificate files exist
sudo ls -la /etc/letsencrypt/live/your-domain.com/

# Check nginx configuration
sudo nginx -t
```

---

## **5. Performance FAQs**

### **Q14: "Site is very slow to load"**

**Quick Solution:**

```bash
# Enable Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev

# Check OPcache
php -m | grep OPcache

# Clear application cache if issues
php artisan cache:clear
```

**Performance Diagnostics:**

```bash
# Check slow query log
sudo grep "Query_time" /var/log/mysql/slow.log | tail -10

# Monitor system resources
top
htop
iotop
```

---

### **Q15: "Memory limit exceeded errors"**

**Quick Solution:**

```bash
# Increase PHP memory limit
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/cli/php.ini

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# For immediate CLI tasks
php -d memory_limit=512M artisan your:command
```

**Monitoring Memory Usage:**

```bash
# Check current memory usage
php -r "echo 'Memory limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Current usage: ' . memory_get_usage(true) / 1024 / 1024 . ' MB' . PHP_EOL;"
```

---

### **Q16: "Database queries taking too long"**

**Quick Solution:**

```bash
# Enable slow query log
mysql -u root -p -e "
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL log_queries_not_using_indexes = 'ON';
"

# Check for missing indexes
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
SELECT table_name, cardinality, index_name
FROM information_schema.statistics
WHERE table_schema = '$DB_DATABASE'
ORDER BY cardinality DESC;
"
```

**Query Optimization:**

```bash
# Use Laravel query debugging
php artisan tinker
# In tinker:
DB::enableQueryLog();
// Run your code
dd(DB::getQueryLog());
```

---

## **6. CodeCanyon Specific FAQs**

### **Q17: "License verification fails after deployment"**

**Quick Solution:**

```bash
# Check license configuration
grep -i "license\|purchase" .env

# Verify license files exist
find . -name "*license*" -type f
find . -name "*purchase*" -type f

# Check API connectivity
curl -I https://api.envato.com/

# Test license endpoint
php artisan tinker
# Test license verification code
```

**Common Issues:**

```
✅ VERIFY:
├── Purchase code is correct in .env
├── Domain is registered with license
├── API keys are properly configured
├── Internet connectivity from server
└── Firewall allows API connections
```

---

### **Q18: "Vendor updates break customizations"**

**Quick Solution:**

```bash
# Backup customizations first
cp -r app/Custom/ ../custom_backup_$(date +%Y%m%d)
cp -r resources/custom/ ../custom_resources_backup_$(date +%Y%m%d)

# Check what vendor changed
diff -r vendor/ ../previous_vendor/ > vendor_changes.txt

# Restore custom files after update
cp -r ../custom_backup_*/Custom/* app/Custom/
cp -r ../custom_resources_backup_*/custom/* resources/custom/
```

**Prevention Strategy:**

```
✅ CUSTOMIZATION STRATEGY:
├── Keep all customizations in separate directories
├── Use service providers for custom functionality
├── Override views in custom themes
├── Document all changes in CUSTOMIZATIONS.md
└── Test updates on staging environment first
```

---

### **Q19: "Cannot access admin panel after deployment"**

**Quick Solution:**

```bash
# Check admin route configuration
php artisan route:list | grep admin

# Verify admin user exists
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
SELECT id, email, role FROM users WHERE role = 'admin' OR is_admin = 1;
"

# Create admin user if missing
php artisan tinker
# In tinker:
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@your-domain.com';
$user->password = Hash::make('secure_password');
$user->role = 'admin';
$user->save();
```

**Middleware Check:**

```bash
# Check middleware configuration
grep -r "admin" app/Http/Middleware/
grep -r "role" app/Http/Middleware/
```

---

## **7. Security FAQs**

### **Q20: "Getting CSRF token mismatch errors"**

**Quick Solution:**

```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear

# Check session configuration
grep SESSION_ .env

# Verify CSRF middleware
grep -r "VerifyCsrfToken" app/Http/
```

**Session Domain Fix:**

```bash
# Update session configuration
sed -i "s/'domain' => null/'domain' => '.your-domain.com'/" config/session.php

# Or in .env
echo "SESSION_DOMAIN=.your-domain.com" >> .env
```

---

### **Q21: "File upload security warnings"**

**Quick Solution:**

```bash
# Check upload directory permissions
ls -la storage/app/uploads/
ls -la public/uploads/

# Secure upload directory
find storage/app/uploads/ -name "*.php" -delete
find public/uploads/ -name "*.php" -delete

# Add .htaccess to uploads (Apache)
cat > public/uploads/.htaccess << 'EOF'
Options -ExecCGI
AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi
EOF
```

**Nginx Upload Security:**

```nginx
# Add to nginx site config
location ~* /uploads/.*\.php$ {
    deny all;
}
```

---

### **Q22: "XSS or SQL injection warnings in security scan"**

**Quick Solution:**

```bash
# Update Laravel to latest version
composer update

# Check for unescaped output in Blade
grep -r "{!!" resources/views/

# Review user input handling
grep -r "Request::" app/
grep -r "\$_POST\|\$_GET" app/
```

**Validation Check:**

```bash
# Ensure all forms use validation
find app/Http/Requests/ -name "*.php" | wc -l
find app/Http/Controllers/ -name "*.php" -exec grep -l "validate\|FormRequest" {} \;
```

---

## **8. Backup and Recovery FAQs**

### **Q23: "Backup restoration fails with permission errors"**

**Quick Solution:**

```bash
# Extract with correct ownership
sudo tar -xzf backup.tar.gz --same-owner

# Fix ownership after extraction
sudo chown -R www-data:www-data .
sudo chmod -R 755 .

# Fix specific directories
sudo chmod -R 755 storage bootstrap/cache
sudo chmod 600 .env
```

**Database Restoration:**

```bash
# Restore database with proper encoding
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" --default-character-set=utf8mb4 "$DB_DATABASE" < backup.sql

# Check restoration
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES;"
```

---

### **Q24: "Backup file too large - upload fails"**

**Quick Solution:**

```bash
# Split large backup file
split -b 100M backup.tar.gz backup_part_

# Or create compressed backup
tar -czf backup.tar.gz --exclude='storage/logs/*' --exclude='bootstrap/cache/*' .

# Database backup compression
mysqldump --single-transaction -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" | gzip > backup.sql.gz
```

**Server Upload Limits:**

```bash
# Increase PHP upload limits
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1G/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 1G/' /etc/php/8.2/fpm/php.ini
sudo systemctl restart php8.2-fpm
```

---

## **9. Monitoring FAQs**

### **Q25: "How to monitor application performance"**

**Quick Solution:**

```bash
# Install Laravel Telescope (non-production)
composer require laravel/telescope
php artisan telescope:install
php artisan migrate

# Basic monitoring script
cat > monitor.sh << 'EOF'
#!/bin/bash
echo "=== System Status $(date) ==="
echo "CPU Usage: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)%"
echo "Memory Usage: $(free | awk 'NR==2{printf "%.2f%%", $3*100/$2}')"
echo "Disk Usage: $(df -h / | tail -1 | awk '{print $5}')"
echo "Active Connections: $(netstat -an | grep :80 | grep ESTABLISHED | wc -l)"
EOF
chmod +x monitor.sh
```

**Application Health Check:**

```bash
# Create health endpoint
curl -s http://your-domain.com/health | jq .

# Monitor response times
curl -w "@curl-format.txt" -o /dev/null -s http://your-domain.com/
```

---

### **Q26: "Error logs filling up disk space"**

**Quick Solution:**

```bash
# Rotate Laravel logs
cd storage/logs
sudo logrotate -f /etc/logrotate.d/laravel

# Manual cleanup (keep last 7 days)
find storage/logs/ -name "*.log" -mtime +7 -delete

# Compress old logs
find storage/logs/ -name "*.log" -mtime +1 -exec gzip {} \;
```

**Logrotate Configuration:**

```bash
# Create logrotate config
sudo cat > /etc/logrotate.d/laravel << 'EOF'
/path/to/project/storage/logs/*.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    su www-data www-data
}
EOF
```

---

## **10. Emergency Procedures FAQ**

### **Q27: "Site is completely down - emergency recovery"**

**Emergency Checklist:**

```bash
# 1. Check web server
sudo systemctl status nginx
sudo systemctl restart nginx

# 2. Check PHP-FPM
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm

# 3. Check database
sudo systemctl status mysql
sudo systemctl restart mysql

# 4. Check disk space
df -h

# 5. Check error logs
sudo tail -50 /var/log/nginx/error.log
sudo tail -50 storage/logs/laravel.log

# 6. Quick rollback if needed
ln -sf releases/previous_version current
```

**Recovery Order:**

```
🚨 EMERGENCY RECOVERY ORDER:
1️⃣ Check system resources (disk, memory)
2️⃣ Restart critical services
3️⃣ Check error logs
4️⃣ Test database connectivity
5️⃣ Verify file permissions
6️⃣ Clear all caches
7️⃣ Rollback if necessary
8️⃣ Notify stakeholders
```

---

### **Q28: "Data corruption detected - immediate action needed"**

**Immediate Response:**

```bash
# 1. Stop application immediately
sudo systemctl stop nginx

# 2. Backup current state
cp -r database/ database_backup_$(date +%Y%m%d_%H%M%S)/

# 3. Restore from latest known good backup
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < latest_backup.sql

# 4. Verify data integrity
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "CHECK TABLE users, orders, products;"

# 5. Restart services if verification passes
sudo systemctl start nginx
```

**Data Verification Script:**

```bash
# Quick data integrity check
data_integrity_check() {
    echo "Checking critical tables..."

    # Check user count
    user_count=$(mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -se "SELECT COUNT(*) FROM users;")
    echo "Users: $user_count"

    # Check for NULL critical fields
    null_emails=$(mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -se "SELECT COUNT(*) FROM users WHERE email IS NULL;")
    echo "Users with NULL email: $null_emails"

    if [ "$null_emails" -gt 0 ]; then
        echo "❌ Data corruption detected!"
        return 1
    else
        echo "✅ Basic data integrity check passed"
        return 0
    fi
}
```

---

## **Quick Reference Commands**

### **Essential Laravel Commands:**

```bash
# Cache management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed

# Maintenance
php artisan down
php artisan up
```

### **System Commands:**

```bash
# Service management
sudo systemctl status nginx php8.2-fpm mysql
sudo systemctl restart nginx php8.2-fpm mysql

# Log monitoring
sudo tail -f /var/log/nginx/error.log
sudo tail -f storage/logs/laravel.log

# Permission fixes
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

### **Database Commands:**

```bash
# Quick database tasks
mysql -u username -p database_name
SHOW TABLES;
DESCRIBE table_name;
SELECT COUNT(*) FROM users;

# Backup and restore
mysqldump -u username -p database_name > backup.sql
mysql -u username -p database_name < backup.sql
```

---

## **Emergency Contacts and Resources**

### **When to Escalate:**

- **Critical:** Site completely down for >5 minutes
- **High:** Data corruption or security breach
- **Medium:** Performance degradation >50%
- **Low:** Non-critical feature issues

### **Key Log Locations:**

```
System Logs:
├── /var/log/nginx/error.log
├── /var/log/nginx/access.log
├── /var/log/php8.2-fpm.log
├── /var/log/mysql/error.log
└── /var/log/syslog

Application Logs:
├── storage/logs/laravel.log
├── storage/logs/laravel-$(date +%Y-%m-%d).log
└── bootstrap/cache/ (if issues)
```

### **Useful Resources:**

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Troubleshooting:** https://laravel.com/docs/errors
- **PHP Documentation:** https://php.net/manual
- **MySQL Documentation:** https://dev.mysql.com/doc

---

**Remember:** When in doubt, check the logs first, backup before making changes, and document everything you do for future reference!
