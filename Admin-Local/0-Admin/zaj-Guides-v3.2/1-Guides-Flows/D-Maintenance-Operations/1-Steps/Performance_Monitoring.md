# Performance Monitoring

**Purpose:** Advanced performance monitoring, log management, and optimization for deployed Laravel applications.

**Use Case:** Continuous performance optimization and proactive bottleneck identification

---

## **Analysis Source**

Based on **Laravel - Final Guides/V2/phase4_deployment_guide.md** Step 22:

- V2 Step 22.1: Performance monitoring scripts
- V2 Step 22.2: Database performance tracking
- V2 Step 22.3: Log management and cleanup

---

## **1. Database Performance Monitoring**

### **1.1: Create Database Performance Script**

**Purpose:** Monitor database size, query performance, and optimize database operations.

```bash
cat > ~/db_performance.sh << 'EOF'
#!/bin/bash

echo "📊 Database Performance Monitor - $(date)"
echo "========================================"

# Database configuration
DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"

# Database size analysis
echo "💾 Database Size Analysis:"
mysql -u $DB_USER -p"$DB_PASS" -e "
SELECT
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema='$DB_NAME'
GROUP BY table_schema;
"

echo ""
echo "📋 Table Size Breakdown:"
mysql -u $DB_USER -p"$DB_PASS" -e "
SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.TABLES
WHERE table_schema = '$DB_NAME'
ORDER BY (data_length + index_length) DESC
LIMIT 10;
"

# Connection analysis
echo ""
echo "🔗 Connection Status:"
mysql -u $DB_USER -p"$DB_PASS" -e "SHOW STATUS LIKE 'Threads_connected';"
mysql -u $DB_USER -p"$DB_PASS" -e "SHOW STATUS LIKE 'Max_used_connections';"

# Slow query analysis
echo ""
echo "🐌 Query Performance:"
mysql -u $DB_USER -p"$DB_PASS" -e "SHOW STATUS LIKE 'Slow_queries';"

# Index usage
echo ""
echo "📇 Index Efficiency:"
mysql -u $DB_USER -p"$DB_PASS" -e "
SELECT
    table_name,
    cardinality,
    index_name
FROM information_schema.statistics
WHERE table_schema = '$DB_NAME'
AND cardinality > 0
ORDER BY cardinality DESC
LIMIT 10;
"

echo ""
echo "✅ Database performance check completed"
EOF

chmod +x ~/db_performance.sh

echo "✅ Database performance monitoring script created"
```

### **1.2: Create Database Optimization Script**

```bash
cat > ~/db_optimize.sh << 'EOF'
#!/bin/bash

echo "⚡ Database Optimization - $(date)"
echo "================================="

cd "$HOME/domains/your-domain.com/current"

# Laravel-specific optimizations
echo "🚀 Laravel Optimizations:"

# Clear and rebuild all caches
echo "🗑️ Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "🔧 Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
echo "📦 Optimizing Composer autoloader..."
composer dump-autoload --optimize --no-dev

# Database optimizations
echo ""
echo "🗃️ Database Maintenance:"

DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"

# Optimize all tables
mysql -u $DB_USER -p"$DB_PASS" -e "
SELECT CONCAT('OPTIMIZE TABLE ', table_name, ';')
FROM information_schema.tables
WHERE table_schema='$DB_NAME';
" | grep "OPTIMIZE" | mysql -u $DB_USER -p"$DB_PASS" $DB_NAME

echo "✅ Database tables optimized"

# Update table statistics
mysql -u $DB_USER -p"$DB_PASS" -e "ANALYZE TABLE $(mysql -u $DB_USER -p"$DB_PASS" -e "SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema='$DB_NAME';" | tail -1);" $DB_NAME

echo "✅ Table statistics updated"

echo ""
echo "⚡ Optimization completed successfully"
EOF

chmod +x ~/db_optimize.sh

echo "✅ Database optimization script created"
```

---

## **2. Storage Usage Monitoring**

### **2.1: Create Storage Monitoring Script**

```bash
cat > ~/storage_monitor.sh << 'EOF'
#!/bin/bash

echo "📁 Storage Usage Monitor - $(date)"
echo "=================================="

DOMAIN_PATH="$HOME/domains/your-domain.com"

# Overall disk usage
echo "💾 Overall Disk Usage:"
df -h $HOME | tail -1

# Domain directory breakdown
echo ""
echo "🏠 Domain Directory Sizes:"
du -sh "$DOMAIN_PATH"/* 2>/dev/null | sort -hr

# Laravel storage breakdown
echo ""
echo "📦 Laravel Storage Analysis:"
if [ -d "$DOMAIN_PATH/current/storage" ]; then
    du -sh "$DOMAIN_PATH/current/storage"/* 2>/dev/null | sort -hr
else
    echo "❌ Laravel storage directory not found"
fi

# Shared storage analysis
echo ""
echo "🔗 Shared Storage Analysis:"
if [ -d "$DOMAIN_PATH/shared" ]; then
    du -sh "$DOMAIN_PATH/shared"/* 2>/dev/null | sort -hr
else
    echo "❌ Shared storage directory not found"
fi

# Log file sizes
echo ""
echo "📋 Log File Sizes:"
find "$DOMAIN_PATH" -name "*.log" -type f -exec ls -lh {} \; 2>/dev/null | sort -k5 -hr | head -10

# Backup directory sizes
echo ""
echo "💾 Backup Storage:"
if [ -d "$HOME/backups" ]; then
    du -sh "$HOME/backups"/* 2>/dev/null | sort -hr
else
    echo "❌ Backup directory not found"
fi

# Large files detection
echo ""
echo "🔍 Large Files (>100MB):"
find "$DOMAIN_PATH" -type f -size +100M -exec ls -lh {} \; 2>/dev/null | head -10

# Temporary files
echo ""
echo "🗑️ Temporary Files:"
find "$DOMAIN_PATH" -name "*.tmp" -o -name "*.temp" -o -name "*~" | wc -l | awk '{print "Temporary files found: " $1}'

echo ""
echo "✅ Storage monitoring completed"
EOF

chmod +x ~/storage_monitor.sh

echo "✅ Storage monitoring script created"
```

### **2.2: Create Storage Cleanup Script**

```bash
cat > ~/storage_cleanup.sh << 'EOF'
#!/bin/bash

echo "🧹 Storage Cleanup - $(date)"
echo "============================"

DOMAIN_PATH="$HOME/domains/your-domain.com"

# Laravel cache cleanup
echo "🗑️ Laravel Cache Cleanup:"
cd "$DOMAIN_PATH/current"

# Clear Laravel logs older than 30 days
echo "📋 Cleaning old log files..."
find storage/logs/ -name "*.log" -mtime +30 -delete 2>/dev/null
echo "✅ Old log files cleaned"

# Clear Laravel cache files
echo "🧹 Clearing cache files..."
find storage/framework/cache/ -name "*.php" -mtime +7 -delete 2>/dev/null
find storage/framework/sessions/ -name "*" -mtime +7 -delete 2>/dev/null
find storage/framework/views/ -name "*.php" -mtime +7 -delete 2>/dev/null
echo "✅ Cache files cleaned"

# Clean temporary files
echo ""
echo "🗑️ Temporary File Cleanup:"
find "$DOMAIN_PATH" -name "*.tmp" -mtime +1 -delete 2>/dev/null
find "$DOMAIN_PATH" -name "*.temp" -mtime +1 -delete 2>/dev/null
find "$DOMAIN_PATH" -name "*~" -mtime +1 -delete 2>/dev/null
echo "✅ Temporary files cleaned"

# Clean old releases (keep last 3)
echo ""
echo "📦 Release Cleanup:"
cd "$DOMAIN_PATH/releases"
ls -t | tail -n +4 | xargs -r rm -rf
echo "✅ Old releases cleaned (kept last 3)"

# Clean old backups (keep last 14 days)
echo ""
echo "💾 Backup Cleanup:"
find "$HOME/backups" -name "*.sql.gz" -mtime +14 -delete 2>/dev/null
find "$HOME/backups" -name "*.tar.gz" -mtime +14 -delete 2>/dev/null
echo "✅ Old backups cleaned"

# Report space freed
echo ""
echo "💾 Final Disk Usage:"
df -h $HOME | tail -1

echo ""
echo "✅ Storage cleanup completed"
EOF

chmod +x ~/storage_cleanup.sh

echo "✅ Storage cleanup script created"
```

---

## **3. Website Performance Monitoring**

### **3.1: Create Website Performance Script**

```bash
cat > ~/website_performance.sh << 'EOF'
#!/bin/bash

echo "🌐 Website Performance Monitor - $(date)"
echo "======================================="

DOMAIN="your-domain.com"
URL="https://$DOMAIN"

# Response time test
echo "⚡ Response Time Analysis:"
for i in {1..5}; do
    RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "$URL")
    echo "Test $i: ${RESPONSE_TIME}s"
    sleep 1
done

# HTTP status and headers
echo ""
echo "📊 HTTP Analysis:"
curl -I "$URL" 2>/dev/null | head -10

# Connection test
echo ""
echo "🔗 Connection Analysis:"
curl -s -o /dev/null -w "
DNS Lookup:      %{time_namelookup}s
TCP Connect:     %{time_connect}s
SSL Handshake:   %{time_appconnect}s
Server Process:  %{time_starttransfer}s
Total Time:      %{time_total}s
Content Size:    %{size_download} bytes
" "$URL"

# Load test (simple)
echo ""
echo "🏋️ Basic Load Test (10 concurrent requests):"
LOAD_START=$(date +%s)
for i in {1..10}; do
    curl -s -o /dev/null -w "%{time_total}\n" "$URL" &
done
wait
LOAD_END=$(date +%s)
LOAD_DURATION=$((LOAD_END - LOAD_START))
echo "Load test completed in ${LOAD_DURATION}s"

# SSL certificate check
echo ""
echo "🔐 SSL Certificate Status:"
echo | openssl s_client -servername "$DOMAIN" -connect "$DOMAIN:443" 2>/dev/null | openssl x509 -noout -subject -dates

# DNS resolution test
echo ""
echo "🌍 DNS Resolution:"
dig +short "$DOMAIN" A
dig +short "$DOMAIN" AAAA

echo ""
echo "✅ Website performance check completed"
EOF

chmod +x ~/website_performance.sh

echo "✅ Website performance monitoring script created"
```

### **3.2: Create Performance Optimization Script**

```bash
cat > ~/performance_optimize.sh << 'EOF'
#!/bin/bash

echo "⚡ Performance Optimization - $(date)"
echo "==================================="

cd "$HOME/domains/your-domain.com/current"

# Laravel optimizations
echo "🚀 Laravel Performance Optimizations:"

# Optimize configuration
echo "🔧 Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
echo "📦 Optimizing autoloader..."
composer dump-autoload --optimize --no-dev

# Queue optimization
echo "🔄 Queue optimization..."
php artisan queue:restart

# Storage optimizations
echo ""
echo "💾 Storage Optimizations:"

# Optimize images (if imagemagick available)
if command -v mogrify &> /dev/null; then
    echo "🖼️ Optimizing images..."
    find public/images -name "*.jpg" -exec mogrify -strip -quality 85 {} \;
    find public/images -name "*.png" -exec mogrify -strip {} \;
    echo "✅ Images optimized"
else
    echo "ℹ️ ImageMagick not available for image optimization"
fi

# Gzip test
echo ""
echo "📦 Compression Check:"
curl -s -H "Accept-Encoding: gzip" -I "https://your-domain.com" | grep -i "content-encoding"

# PHP-FPM optimization check
echo ""
echo "🐘 PHP-FPM Status:"
if [ -f "/etc/php/*/fpm/pool.d/www.conf" ]; then
    echo "PHP-FPM pool configuration found"
    # You can add specific checks here
else
    echo "PHP-FPM configuration not accessible"
fi

echo ""
echo "⚡ Performance optimization completed"
EOF

chmod +x ~/performance_optimize.sh

echo "✅ Performance optimization script created"
```

---

## **4. Log Management System**

### **4.1: Create Log Analysis Script**

```bash
cat > ~/log_analysis.sh << 'EOF'
#!/bin/bash

echo "📋 Log Analysis - $(date)"
echo "========================"

DOMAIN_PATH="$HOME/domains/your-domain.com/current"
LOG_PATH="$DOMAIN_PATH/storage/logs"

# Laravel log analysis
echo "📊 Laravel Log Analysis:"
if [ -f "$LOG_PATH/laravel.log" ]; then
    echo "Log file size: $(du -sh $LOG_PATH/laravel.log | cut -f1)"

    echo ""
    echo "🔴 Error Summary (last 24 hours):"
    grep "$(date --date='1 day ago' '+%Y-%m-%d')\|$(date '+%Y-%m-%d')" "$LOG_PATH/laravel.log" | grep -i "error\|critical\|emergency" | wc -l | awk '{print "Total errors: " $1}'

    echo ""
    echo "⚠️ Most Common Errors:"
    grep -i "error" "$LOG_PATH/laravel.log" | tail -100 | cut -d']' -f3- | sort | uniq -c | sort -nr | head -5

    echo ""
    echo "🚨 Recent Critical Issues:"
    grep -i "critical\|emergency" "$LOG_PATH/laravel.log" | tail -5

else
    echo "❌ Laravel log file not found"
fi

# Web server logs (if accessible)
echo ""
echo "🌐 Web Server Log Analysis:"
ACCESS_LOG="/var/log/nginx/access.log"
ERROR_LOG="/var/log/nginx/error.log"

if [ -f "$ACCESS_LOG" ]; then
    echo "📈 Traffic Analysis (last 1000 requests):"
    tail -1000 "$ACCESS_LOG" | awk '{print $9}' | sort | uniq -c | sort -nr | head -10
else
    echo "ℹ️ Web server access log not accessible"
fi

if [ -f "$ERROR_LOG" ]; then
    echo ""
    echo "🔴 Web Server Errors (last 10):"
    tail -10 "$ERROR_LOG"
else
    echo "ℹ️ Web server error log not accessible"
fi

# System resource correlation
echo ""
echo "💾 Resource Usage During Errors:"
echo "Current load average: $(uptime | awk '{print $(NF-2) " " $(NF-1) " " $NF}')"
echo "Current memory usage: $(free -m | awk 'NR==2{printf "%.2f%%", $3*100/$2 }')"
echo "Current disk usage: $(df -h $HOME | tail -1 | awk '{print $5}')"

echo ""
echo "✅ Log analysis completed"
EOF

chmod +x ~/log_analysis.sh

echo "✅ Log analysis script created"
```

### **4.2: Create Log Rotation Script**

```bash
cat > ~/log_rotation.sh << 'EOF'
#!/bin/bash

echo "🔄 Log Rotation - $(date)"
echo "========================"

DOMAIN_PATH="$HOME/domains/your-domain.com/current"
LOG_PATH="$DOMAIN_PATH/storage/logs"

# Laravel log rotation
echo "📋 Laravel Log Rotation:"
if [ -f "$LOG_PATH/laravel.log" ]; then
    LOG_SIZE=$(stat -c%s "$LOG_PATH/laravel.log")
    LOG_SIZE_MB=$((LOG_SIZE / 1024 / 1024))

    echo "Current log size: ${LOG_SIZE_MB}MB"

    # Rotate if larger than 50MB
    if [ $LOG_SIZE_MB -gt 50 ]; then
        echo "🔄 Rotating large log file..."

        # Create archive
        ARCHIVE_NAME="laravel-$(date +%Y%m%d_%H%M%S).log"
        cp "$LOG_PATH/laravel.log" "$LOG_PATH/archived/$ARCHIVE_NAME"
        gzip "$LOG_PATH/archived/$ARCHIVE_NAME"

        # Clear current log
        echo "" > "$LOG_PATH/laravel.log"

        echo "✅ Log rotated to: archived/${ARCHIVE_NAME}.gz"
    else
        echo "ℹ️ Log size OK, no rotation needed"
    fi
else
    echo "❌ Laravel log file not found"
fi

# Create archived directory if it doesn't exist
mkdir -p "$LOG_PATH/archived"

# Clean old archived logs (keep last 30 days)
echo ""
echo "🗑️ Cleaning Old Archives:"
find "$LOG_PATH/archived" -name "*.gz" -mtime +30 -delete 2>/dev/null
REMAINING=$(ls "$LOG_PATH/archived"/*.gz 2>/dev/null | wc -l)
echo "Archived logs remaining: $REMAINING"

# Clean debug files
echo ""
echo "🧹 Debug File Cleanup:"
find "$LOG_PATH" -name "*.debug" -mtime +7 -delete 2>/dev/null
echo "✅ Debug files cleaned"

echo ""
echo "✅ Log rotation completed"
EOF

chmod +x ~/log_rotation.sh

echo "✅ Log rotation script created"
```

---

## **5. Automated Performance Scheduling**

### **5.1: Schedule Performance Monitoring**

```bash
# Add performance monitoring to crontab
echo "⏰ Setting up automated performance monitoring..."

# Database performance check (daily at 3 AM)
(crontab -l 2>/dev/null; echo "0 3 * * * $HOME/db_performance.sh >> $HOME/performance.log 2>&1") | crontab -

# Storage monitoring (daily at 4 AM)
(crontab -l 2>/dev/null; echo "0 4 * * * $HOME/storage_monitor.sh >> $HOME/storage.log 2>&1") | crontab -

# Website performance check (every 4 hours)
(crontab -l 2>/dev/null; echo "0 */4 * * * $HOME/website_performance.sh >> $HOME/website.log 2>&1") | crontab -

# Log analysis (daily at 5 AM)
(crontab -l 2>/dev/null; echo "0 5 * * * $HOME/log_analysis.sh >> $HOME/log_analysis.log 2>&1") | crontab -

# Storage cleanup (weekly on Sunday at 2 AM)
(crontab -l 2>/dev/null; echo "0 2 * * 0 $HOME/storage_cleanup.sh >> $HOME/cleanup.log 2>&1") | crontab -

# Log rotation (weekly on Sunday at 3 AM)
(crontab -l 2>/dev/null; echo "0 3 * * 0 $HOME/log_rotation.sh >> $HOME/rotation.log 2>&1") | crontab -

# Database optimization (monthly on 1st at 1 AM)
(crontab -l 2>/dev/null; echo "0 1 1 * * $HOME/db_optimize.sh >> $HOME/optimize.log 2>&1") | crontab -

echo "✅ Performance monitoring scheduled"

# Verify all cron jobs
echo ""
echo "📋 Scheduled Performance Jobs:"
crontab -l | grep -E "(performance|storage|website|log|cleanup|rotation|optimize)"
```

---

## **6. Performance Dashboard**

### **6.1: Create Performance Summary Script**

```bash
cat > ~/performance_summary.sh << 'EOF'
#!/bin/bash

echo "📊 Performance Dashboard - $(date)"
echo "=================================="

# Quick system overview
echo "🖥️ System Overview:"
echo "Uptime: $(uptime -p)"
echo "Load: $(uptime | awk '{print $(NF-2) " " $(NF-1) " " $NF}')"
echo "Memory: $(free -m | awk 'NR==2{printf "%s/%sMB (%.2f%%)", $3,$2,$3*100/$2 }')"
echo "Disk: $(df -h $HOME | tail -1 | awk '{print $3 "/" $2 " (" $5 ")"}')"

# Website status
echo ""
echo "🌐 Website Status:"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://your-domain.com")
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "https://your-domain.com")
echo "HTTP Status: $HTTP_STATUS"
echo "Response Time: ${RESPONSE_TIME}s"

# Database status
echo ""
echo "📊 Database Status:"
cd "$HOME/domains/your-domain.com/current"
DB_STATUS=$(php artisan tinker --execute="echo 'DB: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed');" 2>/dev/null || echo "DB: Check Failed")
echo "$DB_STATUS"

# Recent performance logs
echo ""
echo "📋 Recent Performance Summary:"
echo "Last performance check: $(ls -lt $HOME/performance.log 2>/dev/null | head -1 | awk '{print $6" "$7" "$8}' || echo 'Never')"
echo "Last storage check: $(ls -lt $HOME/storage.log 2>/dev/null | head -1 | awk '{print $6" "$7" "$8}' || echo 'Never')"
echo "Last cleanup: $(ls -lt $HOME/cleanup.log 2>/dev/null | head -1 | awk '{print $6" "$7" "$8}' || echo 'Never')"

# Error summary
echo ""
echo "🚨 Error Summary:"
ERROR_COUNT=$(grep -c "ERROR\|CRITICAL" "$HOME/domains/your-domain.com/current/storage/logs/laravel.log" 2>/dev/null || echo "0")
echo "Recent errors: $ERROR_COUNT"

echo ""
echo "✅ Performance dashboard complete"
EOF

chmod +x ~/performance_summary.sh

echo "✅ Performance dashboard created"
```

---

## **7. Usage Instructions**

### **Daily Operations:**

```bash
# View performance dashboard
bash ~/performance_summary.sh

# Check database performance
bash ~/db_performance.sh

# Monitor storage usage
bash ~/storage_monitor.sh

# Analyze website performance
bash ~/website_performance.sh

# Review logs
bash ~/log_analysis.sh
```

### **Weekly Maintenance:**

```bash
# Run storage cleanup
bash ~/storage_cleanup.sh

# Rotate logs
bash ~/log_rotation.sh

# Optimize performance
bash ~/performance_optimize.sh
```

### **Monthly Maintenance:**

```bash
# Optimize database
bash ~/db_optimize.sh

# Review all performance logs
tail -50 ~/performance.log
tail -50 ~/storage.log
tail -50 ~/website.log
```

---

## **8. Configuration Checklist**

- [ ] Database performance monitoring active
- [ ] Storage monitoring configured
- [ ] Website performance checks scheduled
- [ ] Log management system operational
- [ ] Automated cleanup scheduled
- [ ] Performance optimization scripts ready
- [ ] Database credentials updated
- [ ] Domain names updated in scripts
- [ ] Cron jobs verified and active
- [ ] Log rotation configured

---

## **Related Files**

- **Server Monitoring:** [Server_Monitoring.md](Server_Monitoring.md)
- **Emergency Procedures:** [Emergency_Procedures.md](Emergency_Procedures.md)
- **Backup Management:** [Backup_Management.md](Backup_Management.md)

---

## **Troubleshooting**

### **Issue: Performance scripts fail**

```bash
# Check script permissions
ls -la ~/performance*.sh

# Test database connectivity
cd ~/domains/your-domain.com/current
php artisan tinker --execute="DB::connection()->getPdo()"
```

### **Issue: High resource usage**

```bash
# Check running processes
ps aux | head -10

# Check disk I/O
iostat -x 1 5

# Check network usage
netstat -i
```

### **Issue: Log files growing too large**

```bash
# Manual log rotation
bash ~/log_rotation.sh

# Check log directory size
du -sh ~/domains/your-domain.com/current/storage/logs/
```
