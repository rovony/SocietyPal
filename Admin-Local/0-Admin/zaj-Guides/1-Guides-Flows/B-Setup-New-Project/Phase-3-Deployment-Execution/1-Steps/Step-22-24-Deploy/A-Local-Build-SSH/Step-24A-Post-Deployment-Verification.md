# Step 24A: Post-Deployment Verification (Scenario A: Local Build + SSH)

## Analysis Source
**Primary Source**: V2 Phase3 (lines 500-600) - HTTP status checking and application health verification  
**Secondary Source**: V1 Complete Guide (lines 1700-1950) - Comprehensive monitoring commands and backup verification  
**Recommendation**: Use V2's systematic HTTP/application verification approach enhanced with V1's comprehensive monitoring commands

---

## 🎯 Purpose

After successful deployment via Scenario A (Local Build + SSH), thoroughly verify that the application is functioning correctly and all systems are operational.

## ⚡ Quick Reference

**Time Required**: ~15 minutes  
**Prerequisites**: Step 23A completed successfully  
**Critical Path**: Application accessibility → Database connectivity → Feature verification

---

## 🔄 **PHASE 1: Immediate Health Verification**

### **1.1 Application Accessibility Check**

```bash
# Verify domain resolution and SSL
echo "🌐 Testing domain accessibility..."
curl -I https://societypal.com
```

**Expected Output:**
```
HTTP/2 200 
server: nginx
content-type: text/html; charset=UTF-8
```

**🚨 Critical Indicators:**
- ✅ HTTP 200 status
- ✅ SSL certificate valid
- ❌ 5xx errors = deployment issue

### **1.2 Application Response Verification**

```bash
# Test application response time and content
echo "⚡ Testing application response..."
time curl -s https://societypal.com | head -20

# Verify Laravel application is responding
curl -s https://societypal.com | grep -i "csrf-token\|laravel"
```

**Expected Result:**
- Response time under 3 seconds
- Laravel CSRF token present in HTML
- No error pages or stack traces

### **1.3 Database Connectivity Check**

```bash
# SSH to server for internal checks
ssh hostinger-factolo

# Navigate to current application
cd ~/domains/societypal.com/current

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"
```

**Expected Output:**
```
Database connected successfully
```

**🚨 If Database Fails:**
```bash
# Check database credentials
cat .env | grep DB_
php artisan config:cache
php artisan migrate:status
```

---

## 🔄 **PHASE 2: Feature Verification**

### **2.1 Authentication System Check**

```bash
# Test login page accessibility
curl -s https://societypal.com/login | grep -i "login\|email"

# Test registration if enabled
curl -s https://societypal.com/register | grep -i "register\|password"
```

**Expected Result:**
- Login form elements present
- No PHP errors or exceptions
- Proper CSRF token generation

### **2.2 Key Application Routes**

```bash
# Test main application routes
echo "🧪 Testing key routes..."

# Dashboard (authenticated route test)
curl -I https://societypal.com/dashboard

# API endpoints (if applicable)
curl -I https://societypal.com/api/health

# Static assets
curl -I https://societypal.com/css/app.css
curl -I https://societypal.com/js/app.js
```

**Expected Results:**
- Dashboard returns 200 or 302 (redirect to login)
- Static assets return 200
- API endpoints respond appropriately

### **2.3 Session and Cache Verification**

```bash
# Verify session storage
php artisan session:table 2>/dev/null || echo "File-based sessions"

# Test cache functionality
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

**Expected Result:**
- No cache errors
- Configuration properly cached
- Routes accessible after caching

---

## 🔄 **PHASE 3: Performance and Security Validation**

### **3.1 Performance Metrics**

```bash
# Check application performance
echo "📊 Performance metrics:"
curl -w "Time: %{time_total}s | Size: %{size_download} bytes\n" -s -o /dev/null https://societypal.com

# Memory usage check
php artisan about | grep -E "Environment|PHP|Laravel"
```

**Expected Metrics:**
- Load time: Under 3 seconds
- Memory usage: Reasonable for server capacity
- No memory leaks or excessive resource usage

### **3.2 Security Verification**

```bash
# Check SSL certificate
echo "🔒 SSL certificate verification:"
curl -vI https://societypal.com 2>&1 | grep -E "SSL|TLS|certificate"

# Verify security headers
curl -I https://societypal.com | grep -E "X-Frame-Options|X-Content-Type-Options|Strict-Transport-Security"
```

**Expected Security Headers:**
- X-Frame-Options: DENY or SAMEORIGIN
- X-Content-Type-Options: nosniff
- SSL/TLS properly configured

### **3.3 File Permissions Check**

```bash
# Verify correct file permissions
ls -la bootstrap/cache/
ls -la storage/
ls -la storage/logs/

# Check writable directories
echo "📁 Checking writable permissions:"
test -w storage/logs && echo "✅ logs writable" || echo "❌ logs not writable"
test -w bootstrap/cache && echo "✅ cache writable" || echo "❌ cache not writable"
```

**Expected Permissions:**
- storage/ and subdirectories: 755 or 775
- bootstrap/cache/: 755 or 775
- Web server can write to required directories

---

## 🔄 **PHASE 4: Data Integrity Verification**

### **4.1 Database Migration Status**

```bash
# Verify all migrations ran successfully
echo "📋 Migration status:"
php artisan migrate:status

# Check for pending migrations
PENDING=$(php artisan migrate:status | grep "No" | wc -l)
if [ $PENDING -gt 0 ]; then
    echo "⚠️ Warning: $PENDING pending migrations"
else
    echo "✅ All migrations completed"
fi
```

**Expected Result:**
- All migrations show "Yes" status
- No pending or failed migrations

### **4.2 Seeded Data Verification** *(If applicable)*

```bash
# Check if seeded data exists (adjust for your application)
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count();
echo 'Settings: ' . (class_exists('App\Models\Setting') ? App\Models\Setting::count() : 'N/A');
"
```

**Expected Result:**
- Expected number of records in each table
- Required seed data present

### **4.3 Storage and Upload Verification**

```bash
# Check shared storage mounting
ls -la ~/domains/societypal.com/shared/storage/
ls -la storage/app/public/

# Verify symlink for public storage
ls -la public/storage
```

**Expected Result:**
- Shared storage properly mounted
- Symlink to storage/app/public exists
- Upload directories accessible

---

## 🔄 **PHASE 5: Monitoring and Backup Verification**

### **5.1 Log File Check**

```bash
# Check for recent errors
echo "📋 Recent application logs:"
tail -20 storage/logs/laravel.log

# Check for critical errors
grep -i "error\|exception\|fatal" storage/logs/laravel.log | tail -10
```

**🚨 Review For:**
- No fatal errors since deployment
- Expected application activity
- No database connection errors

### **5.2 Backup Verification**

```bash
# Verify backup directory exists
ls -la ~/backups/

# Check recent backups
echo "💾 Recent backups:"
ls -lt ~/backups/database/ | head -3
ls -lt ~/backups/files/ | head -3
```

**Expected Result:**
- Backup directories exist
- Recent backups available for recovery
- Backup system operational

---

## ✅ **SUCCESS CONFIRMATION**

### **Deployment Verification Checklist**

Run this final verification script:

```bash
#!/bin/bash
echo "🎯 FINAL DEPLOYMENT VERIFICATION"
echo "================================"

# Website accessibility
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://societypal.com)
if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Website accessible (HTTP $HTTP_STATUS)"
else
    echo "❌ Website issue (HTTP $HTTP_STATUS)"
fi

# Database connectivity
DB_STATUS=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAIL'; }")
if [[ "$DB_STATUS" == *"OK"* ]]; then
    echo "✅ Database connected"
else
    echo "❌ Database connection failed"
fi

# Migration status
PENDING_MIGRATIONS=$(php artisan migrate:status | grep "No" | wc -l)
if [ "$PENDING_MIGRATIONS" -eq 0 ]; then
    echo "✅ All migrations completed"
else
    echo "❌ $PENDING_MIGRATIONS pending migrations"
fi

# File permissions
if [ -w storage/logs ] && [ -w bootstrap/cache ]; then
    echo "✅ File permissions correct"
else
    echo "❌ File permission issues"
fi

# SSL certificate
SSL_STATUS=$(curl -s -I https://societypal.com | head -1)
if [[ "$SSL_STATUS" == *"200"* ]]; then
    echo "✅ SSL certificate working"
else
    echo "❌ SSL certificate issues"
fi

echo "================================"
echo "🚀 Scenario A deployment verification complete!"
echo "📱 Test the application manually at: https://societypal.com"
```

### **Manual Testing Checklist**

- [ ] Homepage loads correctly
- [ ] User registration/login works
- [ ] Main application features functional
- [ ] Database operations successful
- [ ] File uploads work (if applicable)
- [ ] Email sending functional (if applicable)
- [ ] Mobile responsiveness intact

---

## 🔧 **Troubleshooting Common Issues**

### **HTTP 500 Errors**

```bash
# Check application logs
tail -50 storage/logs/laravel.log

# Clear and rebuild cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### **Database Connection Issues**

```bash
# Verify database credentials
cat .env | grep DB_
php artisan config:cache

# Test manual connection
mysql -u $DB_USERNAME -p$DB_PASSWORD -h $DB_HOST $DB_DATABASE
```

### **Permission Problems**

```bash
# Fix storage permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **SSL Certificate Problems**

```bash
# Check certificate validity
openssl s_client -connect societypal.com:443 -servername societypal.com

# Verify with hosting provider if issues persist
```

---

## 📋 **Next Steps**

✅ **Scenario A Complete** - Your application is successfully deployed via Local Build + SSH  
🔄 **Optional**: Set up automated monitoring for ongoing health checks  
📊 **Recommended**: Schedule regular backups and maintenance  

**Continue to**: Environment-specific configurations or explore Scenarios B and C for alternative deployment methods.

---

## 🎯 **Key Success Indicators**

- **Website Response**: HTTP 200 with proper Laravel responses
- **Database Health**: All connections working, migrations complete
- **Security Status**: SSL active, proper headers present
- **Performance**: Acceptable load times and resource usage
- **Data Integrity**: All expected data accessible and consistent
- **Backup Status**: Recovery systems operational and verified

**Scenario A deployment verification completed successfully!** 🎉
