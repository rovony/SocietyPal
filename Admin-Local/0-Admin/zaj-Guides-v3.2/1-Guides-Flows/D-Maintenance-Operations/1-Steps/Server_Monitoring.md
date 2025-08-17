# Server Monitoring

**Purpose:** Comprehensive server monitoring, backup automation, and health checking for deployed Laravel applications.

**Use Case:** Ongoing server management and proactive issue detection

---

## **Analysis Source**

Based on **Laravel - Final Guides/V2/phase4_deployment_guide.md** Step 21:

- V2 Step 21.1: Backup automation scripts
- V2 Step 21.2: Health monitoring system
- V2 Step 21.3: Maintenance mode management

---

## **1. Automated Backup System**

### **1.1: Create Comprehensive Backup Script**

**Purpose:** Automated daily backups of database, application files, and user uploads.

```bash
# SSH into your server
ssh your-server

# Create backup directories
mkdir -p ~/backups/{database,files,releases}

# Create automated backup script
cat > ~/backup_script.sh << 'EOF'
#!/bin/bash

# Automated backup script for Laravel Application
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
DOMAIN_PATH="$HOME/domains/your-domain.com"
BACKUP_PATH="$HOME/backups"

echo "💾 Starting automated backup: $BACKUP_DATE"

# Create backup directories
mkdir -p $BACKUP_PATH/{database,files}

# Database backup
echo "📊 Creating database backup..."
DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"

mysqldump -u $DB_USER -p"$DB_PASS" $DB_NAME | gzip > $BACKUP_PATH/database/backup_${BACKUP_DATE}.sql.gz

if [ $? -eq 0 ]; then
    echo "✅ Database backup created: backup_${BACKUP_DATE}.sql.gz"
else
    echo "❌ Database backup failed"
fi

# Application files backup
echo "📦 Creating application backup..."
tar -czf $BACKUP_PATH/files/app_backup_${BACKUP_DATE}.tar.gz \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  -C $DOMAIN_PATH current/

echo "✅ Application backup created: app_backup_${BACKUP_DATE}.tar.gz"

# Shared storage backup (user uploads, etc.)
echo "🗂️ Creating shared storage backup..."
tar -czf $BACKUP_PATH/files/storage_backup_${BACKUP_DATE}.tar.gz \
  -C $DOMAIN_PATH shared/

echo "✅ Shared storage backup created: storage_backup_${BACKUP_DATE}.tar.gz"

# Cleanup old backups (keep last 7 days)
echo "🗑️ Cleaning old backups..."
find $BACKUP_PATH -name "*.sql.gz" -mtime +7 -delete
find $BACKUP_PATH -name "*_backup_*.tar.gz" -mtime +7 -delete

echo "✅ Backup completed: $BACKUP_DATE"
echo "📍 Backup location: $BACKUP_PATH"
EOF

# Make script executable
chmod +x ~/backup_script.sh

echo "✅ Automated backup script created"
```

### **1.2: Schedule Daily Backups**

```bash
# Add to crontab (daily at 2 AM)
(crontab -l 2>/dev/null; echo "0 2 * * * $HOME/backup_script.sh >> $HOME/backup.log 2>&1") | crontab -

echo "✅ Automated daily backups configured (2 AM daily)"

# Verify crontab
crontab -l
```

**Expected Result:** Automated daily backups with 7-day retention.

---

## **2. Health Monitoring System**

### **2.1: Create Comprehensive Health Check Script**

```bash
cat > ~/health_check.sh << 'EOF'
#!/bin/bash

echo "🏥 Laravel Application Health Check - $(date)"
echo "============================================="

DOMAIN_PATH="$HOME/domains/your-domain.com"

# Check if current release exists
if [ -L "$DOMAIN_PATH/current" ]; then
    echo "✅ Current release symlink exists"
    CURRENT_RELEASE=$(readlink "$DOMAIN_PATH/current")
    echo "📍 Current release: $CURRENT_RELEASE"
else
    echo "❌ Current release symlink missing"
    exit 1
fi

cd "$DOMAIN_PATH/current"

# Application health
echo ""
echo "🔍 Application Health:"
php artisan about | head -10

# Database connectivity
echo ""
echo "📊 Database Status:"
php artisan tinker --execute="echo 'DB Status: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed');" 2>/dev/null || echo "Database check failed"

# Storage permissions
echo ""
echo "📁 Storage Permissions:"
ls -la storage/ | head -5

# Shared directory verification
echo ""
echo "🔗 Shared Directory Status:"
ls -la ../shared/ | head -5

# Website response test
echo ""
echo "🌐 Website Response:"
curl -s -o /dev/null -w "HTTP Status: %{http_code}\nResponse Time: %{time_total}s\n" https://your-domain.com

# SSL certificate check
echo ""
echo "🔐 SSL Certificate:"
echo | openssl s_client -servername your-domain.com -connect your-domain.com:443 2>/dev/null | openssl x509 -noout -dates

# Disk space check
echo ""
echo "💾 Disk Usage:"
df -h $HOME | tail -1

# Recent error logs
echo ""
echo "📋 Recent Errors (last 10):"
grep -i "error\|critical\|emergency" storage/logs/laravel.log | tail -10 2>/dev/null || echo "No recent errors found"

echo ""
echo "✅ Health check completed"
EOF

chmod +x ~/health_check.sh

echo "✅ Health monitoring script created"
```

### **2.2: Test Health Check**

```bash
# Run health check
bash ~/health_check.sh
```

**Expected Result:** Comprehensive health report showing all system components.

---

## **3. Maintenance Mode Management**

### **3.1: Create Maintenance Mode Scripts**

**Purpose:** Easy maintenance mode enable/disable for updates and maintenance.

```bash
# Maintenance mode ON script
cat > ~/maintenance_on.sh << 'EOF'
#!/bin/bash

echo "🚧 Enabling maintenance mode..."

cd "$HOME/domains/your-domain.com/current"

# Enable Laravel maintenance mode
php artisan down --message="Scheduled maintenance in progress" --retry=60

if [ $? -eq 0 ]; then
    echo "✅ Maintenance mode enabled"
    echo "🔍 Site status: Showing maintenance page"
else
    echo "❌ Failed to enable maintenance mode"
fi
EOF

# Maintenance mode OFF script
cat > ~/maintenance_off.sh << 'EOF'
#!/bin/bash

echo "✅ Disabling maintenance mode..."

cd "$HOME/domains/your-domain.com/current"

# Disable Laravel maintenance mode
php artisan up

if [ $? -eq 0 ]; then
    echo "✅ Maintenance mode disabled - site is live"
    echo "🌐 Testing site response..."
    sleep 2
    curl -I https://your-domain.com | head -1
else
    echo "❌ Failed to disable maintenance mode"
fi
EOF

chmod +x ~/maintenance_on.sh ~/maintenance_off.sh

echo "✅ Maintenance mode scripts created"
```

**Expected Result:** Easy maintenance mode control for updates.

---

## **4. Monitoring Automation**

### **4.1: Schedule Health Checks**

```bash
# Add health checks to crontab (every 6 hours)
(crontab -l 2>/dev/null; echo "0 */6 * * * $HOME/health_check.sh >> $HOME/health.log 2>&1") | crontab -

echo "✅ Automated health checks configured (every 6 hours)"
```

### **4.2: Create Alert System**

```bash
cat > ~/alert_check.sh << 'EOF'
#!/bin/bash

# Simple alert system for critical issues
DOMAIN="your-domain.com"
LOG_FILE="$HOME/alert.log"

# Check website response
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN")

if [ "$HTTP_STATUS" != "200" ]; then
    echo "$(date): ALERT - Website down! HTTP Status: $HTTP_STATUS" | tee -a "$LOG_FILE"
    # Add email notification here if configured
fi

# Check disk space (alert if > 90%)
DISK_USAGE=$(df -h $HOME | tail -1 | awk '{print $5}' | sed 's/%//')

if [ "$DISK_USAGE" -gt 90 ]; then
    echo "$(date): ALERT - Disk space critical: ${DISK_USAGE}%" | tee -a "$LOG_FILE"
fi

# Check for recent errors
ERROR_COUNT=$(grep -c "ERROR\|CRITICAL" "$HOME/domains/$DOMAIN/current/storage/logs/laravel.log" 2>/dev/null || echo "0")

if [ "$ERROR_COUNT" -gt 10 ]; then
    echo "$(date): ALERT - High error count: $ERROR_COUNT errors" | tee -a "$LOG_FILE"
fi
EOF

chmod +x ~/alert_check.sh

# Schedule alert checks (every 15 minutes)
(crontab -l 2>/dev/null; echo "*/15 * * * * $HOME/alert_check.sh") | crontab -

echo "✅ Alert system configured"
```

---

## **5. Server Resource Monitoring**

### **5.1: Create Resource Monitor**

```bash
cat > ~/resource_monitor.sh << 'EOF'
#!/bin/bash

echo "📊 Server Resource Monitor - $(date)"
echo "===================================="

# CPU usage
echo "🖥️ CPU Usage:"
top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print "CPU Usage: " 100 - $1 "%"}'

# Memory usage
echo ""
echo "🧠 Memory Usage:"
free -m | awk 'NR==2{printf "Memory Usage: %s/%sMB (%.2f%%)\n", $3,$2,$3*100/$2 }'

# Disk usage
echo ""
echo "💾 Disk Usage:"
df -h $HOME | tail -1 | awk '{print "Disk Usage: " $3 "/" $2 " (" $5 ")"}'

# Load average
echo ""
echo "⚖️ Load Average:"
uptime | awk '{print "Load Average: " $(NF-2) " " $(NF-1) " " $NF}'

# Network connections
echo ""
echo "🌐 Active Connections:"
netstat -an | grep :80 | wc -l | awk '{print "HTTP Connections: " $1}'
netstat -an | grep :443 | wc -l | awk '{print "HTTPS Connections: " $1}'

# Process count
echo ""
echo "🔄 Process Information:"
ps aux | wc -l | awk '{print "Total Processes: " $1}'
ps aux | grep php | grep -v grep | wc -l | awk '{print "PHP Processes: " $1}'
EOF

chmod +x ~/resource_monitor.sh

echo "✅ Resource monitoring script created"
```

---

## **6. Usage Instructions**

### **Daily Operations:**

```bash
# Check server health
bash ~/health_check.sh

# Monitor resources
bash ~/resource_monitor.sh

# View backup status
ls -la ~/backups/database/ | tail -5
ls -la ~/backups/files/ | tail -5

# Enable maintenance mode (before updates)
bash ~/maintenance_on.sh

# Disable maintenance mode (after updates)
bash ~/maintenance_off.sh
```

### **Emergency Procedures:**

```bash
# Check recent alerts
tail -20 ~/alert.log

# Manual backup (before emergency maintenance)
bash ~/backup_script.sh

# Check current logs for errors
tail -50 ~/domains/your-domain.com/current/storage/logs/laravel.log
```

---

## **7. Configuration Checklist**

- [ ] Backup script created and scheduled
- [ ] Health monitoring script active
- [ ] Maintenance mode scripts ready
- [ ] Alert system configured
- [ ] Resource monitoring available
- [ ] Database credentials updated in scripts
- [ ] Domain names updated in scripts
- [ ] Cron jobs verified and active
- [ ] Log rotation configured
- [ ] Disk space monitoring active

---

## **Related Files**

- **Performance Monitoring:** [Performance_Monitoring.md](Performance_Monitoring.md)
- **Emergency Procedures:** [Emergency_Procedures.md](Emergency_Procedures.md)
- **Backup Management:** [Backup_Management.md](Backup_Management.md)

---

## **Troubleshooting**

### **Issue: Backup script fails**

```bash
# Check database credentials
mysql -u your_user -p your_database -e "SELECT 1;"

# Check disk space
df -h

# Check script permissions
ls -la ~/backup_script.sh
```

### **Issue: Health check fails**

```bash
# Check Laravel installation
cd ~/domains/your-domain.com/current
php artisan --version

# Check database connectivity
php artisan tinker --execute="DB::connection()->getPdo()"
```

### **Issue: Cron jobs not running**

```bash
# Check cron service
service cron status

# Check cron logs
tail -f /var/log/cron

# Verify crontab
crontab -l
```
