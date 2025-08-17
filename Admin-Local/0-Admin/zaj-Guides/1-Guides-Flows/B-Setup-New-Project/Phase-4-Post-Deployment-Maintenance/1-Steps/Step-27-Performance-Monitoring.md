# Step 27: Performance Monitoring & Optimization

**Goal:** Implement comprehensive performance monitoring and optimization procedures for your deployed Laravel application.

**Time Required:** 45 minutes  
**Prerequisites:** Step 26 completed successfully

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** - Phase 4 Analysis:

- V2 Step 22: Performance Monitoring & Optimization
- V1 Status: ❌ Missing | V2 Status: ✅ Complete | Recommendation: Use V2

---

## **27.1: Create Performance Monitoring Scripts**

### **Create Performance Monitor:**

1. **Create performance monitoring script:**

   ```bash
   ssh hostinger-factolo

   cat > ~/performance_monitor.sh << 'EOF'
   #!/bin/bash

   echo "📈 SocietyPal Performance Monitor - $(date)"
   echo "========================================="

   DOMAIN_PATH="$HOME/domains/societypal.com"

   # Database size monitoring
   echo "🗄️ Database Information:"
   DB_NAME="u227177893_p_zaj_socpal_d"
   DB_USER="u227177893_p_zaj_socpal_u"
   DB_PASS="t5TmP9\$[iG7hu2eYRWUIWH@IRF2"

   mysql -u $DB_USER -p"$DB_PASS" -e "
   SELECT
       table_schema as 'Database',
       ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
   FROM information_schema.tables
   WHERE table_schema = '$DB_NAME'
   GROUP BY table_schema;"

   # Storage usage
   echo ""
   echo "💾 Storage Usage:"
   du -sh $DOMAIN_PATH/shared/storage/ $DOMAIN_PATH/shared/public/ 2>/dev/null

   # Release management
   echo ""
   echo "📦 Release Management:"
   RELEASE_COUNT=$(ls -1 $DOMAIN_PATH/releases/ | wc -l)
   echo "Total releases: $RELEASE_COUNT"
   echo "Recent releases:"
   ls -t $DOMAIN_PATH/releases/ | head -3

   # Log file sizes
   echo ""
   echo "📋 Log File Sizes:"
   find $DOMAIN_PATH/shared/storage/logs/ -name "*.log" -exec du -h {} \; 2>/dev/null | head -5

   # Website performance test
   echo ""
   echo "🌐 Website Performance:"
   curl -o /dev/null -s -w "Connect: %{time_connect}s\nTTFB: %{time_starttransfer}s\nTotal: %{time_total}s\nSize: %{size_download} bytes\n" https://societypal.com

   echo ""
   echo "✅ Performance monitoring complete"
   EOF

   chmod +x ~/performance_monitor.sh

   echo "✅ Performance monitoring script created"
   ```

2. **Test performance monitoring:**

   ```bash
   bash ~/performance_monitor.sh
   ```

   **Expected Result:** Detailed performance metrics and system usage information.

---

## **27.2: Create Log Management System**

### **Create Log Management Script:**

1. **Create comprehensive log management:**

   ```bash
   cat > ~/log_management.sh << 'EOF'
   #!/bin/bash

   DOMAIN_PATH="$HOME/domains/societypal.com"
   LOG_PATH="$DOMAIN_PATH/shared/storage/logs"

   echo "📋 Log Management - $(date)"
   echo "=========================="

   # Show recent Laravel logs
   echo "📄 Recent Laravel Activity (last 50 lines):"
   tail -50 "$LOG_PATH/laravel.log" 2>/dev/null || echo "No Laravel log found"

   echo ""
   echo "❌ Recent Errors (last 20):"
   grep -i "ERROR\|CRITICAL\|EMERGENCY" "$LOG_PATH/laravel.log" 2>/dev/null | tail -20 || echo "No recent errors"

   echo ""
   echo "📊 Log File Summary:"
   find "$LOG_PATH" -name "*.log" -exec du -h {} \; 2>/dev/null

   # Offer log cleanup
   echo ""
   read -p "Clean old logs (older than 30 days)? (y/N): " CLEAN_LOGS
   if [[ "$CLEAN_LOGS" =~ ^[Yy]$ ]]; then
       CLEANED=$(find "$LOG_PATH" -name "*.log" -mtime +30 -delete -print | wc -l)
       echo "🗑️ Cleaned $CLEANED old log files"
   fi

   echo "✅ Log management complete"
   EOF

   chmod +x ~/log_management.sh

   echo "✅ Log management script created"
   ```

2. **Test log management:**

   ```bash
   bash ~/log_management.sh
   ```

   **Expected Result:** Log monitoring and cleanup capability.

---

## **27.3: Create Database Performance Monitoring**

### **Create Database Health Check:**

1. **Create database performance script:**

   ```bash
   cat > ~/db_performance.sh << 'EOF'
   #!/bin/bash

   echo "🗄️ Database Performance Monitor - $(date)"
   echo "========================================="

   DB_NAME="u227177893_p_zaj_socpal_d"
   DB_USER="u227177893_p_zaj_socpal_u"
   DB_PASS="t5TmP9\$[iG7hu2eYRWUIWH@IRF2"

   # Table sizes
   echo "📊 Table Sizes (Top 10):"
   mysql -u $DB_USER -p"$DB_PASS" $DB_NAME -e "
   SELECT
       table_name AS 'Table',
       table_rows AS 'Rows',
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
   FROM information_schema.TABLES
   WHERE table_schema = '$DB_NAME'
   ORDER BY (data_length + index_length) DESC
   LIMIT 10;"

   # Connection status
   echo ""
   echo "🔗 Database Connections:"
   mysql -u $DB_USER -p"$DB_PASS" -e "SHOW STATUS LIKE 'Threads_connected';"

   # Slow queries (if enabled)
   echo ""
   echo "🐌 Slow Query Status:"
   mysql -u $DB_USER -p"$DB_PASS" -e "SHOW STATUS LIKE 'Slow_queries';"

   echo ""
   echo "✅ Database performance monitoring complete"
   EOF

   chmod +x ~/db_performance.sh

   echo "✅ Database performance script created"
   ```

2. **Test database monitoring:**

   ```bash
   bash ~/db_performance.sh
   ```

   **Expected Result:** Database performance metrics and table information.

---

## **27.4: Create Application Performance Optimization**

### **Create Optimization Script:**

1. **Create Laravel optimization script:**

   ```bash
   cat > ~/optimize_app.sh << 'EOF'
   #!/bin/bash

   DOMAIN_PATH="$HOME/domains/societypal.com"

   echo "⚡ Application Optimization - $(date)"
   echo "===================================="

   cd "$DOMAIN_PATH/current"

   # Clear all caches
   echo "🧹 Clearing caches..."
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear

   # Optimize for production
   echo "⚡ Optimizing for production..."
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

   # Optimize Composer autoloader
   echo "📦 Optimizing Composer autoloader..."
   composer install --optimize-autoloader --no-dev

   # Clear OPcache if available
   echo "🚀 Clearing OPcache..."
   php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not available'; }"

   echo ""
   echo "✅ Application optimization complete"
   EOF

   chmod +x ~/optimize_app.sh

   echo "✅ Application optimization script created"
   ```

2. **Test optimization:**

   ```bash
   bash ~/optimize_app.sh
   ```

   **Expected Result:** Application fully optimized for production performance.

---

## **27.5: Create Performance Monitoring Cron**

### **Setup Automated Monitoring:**

1. **Create crontab entry for daily monitoring:**

   ```bash
   # Add to crontab
   crontab -e

   # Add this line (runs daily at 6 AM):
   0 6 * * * /home/u227177893/performance_monitor.sh >> /home/u227177893/performance_logs.txt 2>&1
   ```

2. **Create weekly optimization:**

   ```bash
   # Add weekly optimization (runs Sunday at 3 AM):
   0 3 * * 0 /home/u227177893/optimize_app.sh >> /home/u227177893/optimization_logs.txt 2>&1
   ```

3. **Verify crontab:**

   ```bash
   crontab -l
   ```

   **Expected Result:** Automated daily monitoring and weekly optimization scheduled.

---

## **✅ Step 27 Completion Checklist**

- [ ] Performance monitoring script created and tested
- [ ] Log management system implemented
- [ ] Database performance monitoring setup
- [ ] Application optimization script created
- [ ] Automated monitoring scheduled via cron
- [ ] All scripts executable and functioning

---

## **Next Steps**

Continue to [Step 28: Emergency Procedures](Step-28-Emergency-Procedures.md) to implement disaster recovery and emergency procedures.

---

## **Troubleshooting**

### **Issue: Performance script fails**

```bash
# Check database credentials
mysql -u u227177893_p_zaj_socpal_u -p
```

### **Issue: Log cleanup fails**

```bash
# Check log directory permissions
ls -la ~/domains/societypal.com/shared/storage/logs/
```

### **Issue: Cron jobs not running**

```bash
# Check cron service
service cron status

# Check cron logs
tail -f /var/log/cron
```
