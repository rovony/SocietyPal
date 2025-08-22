# Data Persistence Maintenance

## 🛡️ Universal Data Persistence System Monitoring

### **Quick Overview**

-   🎯 **Purpose**: Monitor universal data persistence system health
-   ⚡ **Operations**: Directory verification, pattern validation, structure monitoring
-   🌐 **Environment**: LOCAL monitoring, SERVER health checks
-   🔍 **Focus**: Directory existence and data accessibility

---

## **Environment-Specific Maintenance**

### **🖥️ LOCAL Development Monitoring**

```bash
# Daily local development health check
bash ../../../B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/ultimate-persistence.sh "$(pwd)" "$(pwd)/../shared" "verify" "local"
```

**What this monitors locally**:

-   ✅ **Directory Structure**: All data directories exist
-   ✅ **Universal Patterns**: Shared pattern directories present
-   ✅ **Development Health**: Local environment structure intact
-   ❌ **NO symlink checks** (not applicable for local)

### **🌐 SERVER Production Monitoring**

```bash
# Daily server persistence health check
bash ultimate-persistence.sh "$(pwd)" "$(pwd)/../shared" "verify" "server"
```

**What this monitors on server**:

-   ✅ **Symlink Integrity**: All persistence symlinks functional
-   ✅ **Shared Data Access**: All shared directories accessible
-   ✅ **Pattern Validation**: Universal patterns working correctly
-   ✅ **Emergency Detection**: Identify and report issues

---

## **Routine Maintenance Tasks**

### **Daily Directory Verification**

```bash
# Verify all data directories exist (LOCAL)
echo "🔍 Daily directory structure check..."

# Check universal shared patterns exist
test -d storage && echo "✅ Storage directory exists" || echo "❌ Storage missing"
test -d public/user-uploads && echo "✅ User uploads directory exists" || echo "❌ User uploads missing"
test -f .env && echo "✅ Environment file exists" || echo "❌ .env missing"

# Check for any custom data directories
find public/ -name "*upload*" -o -name "*avatar*" -o -name "*media*" | while read dir; do
    echo "✅ Found data directory: $dir"
done
```

### **Weekly Pattern Validation** 

```bash
# Weekly universal pattern check
echo "� Weekly universal pattern validation..."

# Verify all patterns from exclusion-based strategy
patterns=("public/*upload*/" "public/*avatar*/" "public/*media*/" "public/qr*" "public/*invoice*/" "public/*export*/")

for pattern in "${patterns[@]}"; do
    if find . -path "$pattern" 2>/dev/null | grep -q .; then
        echo "✅ Pattern found: $pattern"
    else
        echo "ℹ️  Pattern not present: $pattern (may not be used by this app)"
    fi
done
```

# Verify backup integrity
echo "🔍 Verifying backup integrity..."
tar -tzf "shared-backup-$(date +%Y%m%d).tar.gz" | head -10

# Cleanup old backups (keep last 3 months)
find . -name "shared-backup-*.tar.gz" -mtime +90 -delete
```

---

## **Emergency Procedures**

### **Data Persistence System Failure**

```bash
# If health check fails or symlinks are broken
echo "🚨 Data persistence emergency recovery..."

# Method 1: Automated recovery
bash shared/emergency-recovery.sh "$(pwd)"

# Method 2: Manual recovery if automated fails
echo "🔧 Manual recovery procedures..."

# Recreate broken symlinks
rm -rf storage .env bootstrap/cache
ln -sf ../shared/storage storage
ln -sf ../shared/.env .env
ln -sf ../../shared/bootstrap/cache bootstrap/cache

# Fix Laravel storage link
rm -rf public/storage
php artisan storage:link

# Fix permissions
chmod -R 755 ../shared/storage
chmod -R 755 ../shared/bootstrap/cache
chmod 644 ../shared/.env

# Re-run health check
bash shared/health-check.sh
```

### **Shared Directory Recovery**

```bash
# If shared directory is corrupted or missing
echo "🚨 Shared directory recovery..."

# Create shared structure
mkdir -p ../shared/{storage,bootstrap/cache,public}

# Restore from backup if available
if [[ -f "shared-backup-$(date +%Y%m%d).tar.gz" ]]; then
    echo "📦 Restoring from today's backup..."
    cd ..
    tar -xzf "shared-backup-$(date +%Y%m%d).tar.gz"
else
    echo "📦 Restoring from latest backup..."
    LATEST_BACKUP=$(ls -t shared-backup-*.tar.gz 2>/dev/null | head -1)
    if [[ -n "$LATEST_BACKUP" ]]; then
        tar -xzf "$LATEST_BACKUP"
    else
        echo "⚠️ No backup found - manual recovery required"
    fi
fi

# Recreate symlinks
cd project_directory
bash shared/emergency-recovery.sh "$(pwd)"
```

---

## **Performance Monitoring Integration**

### **Data Persistence Performance Metrics**

```bash
# Monitor shared directory disk usage
echo "💾 Shared directory disk usage:"
du -sh ../shared/

# Monitor individual shared components
echo "📊 Component breakdown:"
du -sh ../shared/storage/
du -sh ../shared/public/
du -sh ../shared/bootstrap/cache/

# Monitor symlink performance
echo "🔗 Symlink response times:"
time ls -la storage/ > /dev/null
time ls -la public/storage/ > /dev/null
```

### **Laravel Storage Performance**

```bash
# Test Laravel storage performance
echo "⚡ Laravel storage performance test..."

# Test storage write performance
time php artisan tinker --execute="Storage::put('test.txt', 'performance test');"

# Test storage read performance
time php artisan tinker --execute="Storage::get('test.txt');"

# Cleanup
php artisan tinker --execute="Storage::delete('test.txt');"
```

---

## **Security Monitoring**

### **Shared Directory Security**

```bash
# Check shared directory permissions
echo "🔒 Shared directory security check..."

# Verify proper ownership and permissions
ls -la ../shared/

# Check for unauthorized files
find ../shared/ -type f -name "*.php" -o -name "*.sh" | grep -v "health-check\|emergency-recovery"

# Verify .env security
ls -la ../shared/.env
# Should be: -rw-r--r-- (644) owned by web user
```

### **Symlink Security Validation**

```bash
# Verify symlinks point to expected locations
echo "🔗 Symlink security validation..."

# Check storage symlink
STORAGE_TARGET=$(readlink storage)
echo "Storage symlink: $STORAGE_TARGET"
[[ "$STORAGE_TARGET" = "../shared/storage" ]] && echo "✅ Storage symlink secure" || echo "❌ Storage symlink issue"

# Check .env symlink
ENV_TARGET=$(readlink .env)
echo ".env symlink: $ENV_TARGET"
[[ "$ENV_TARGET" = "../shared/.env" ]] && echo "✅ .env symlink secure" || echo "❌ .env symlink issue"
```

---

## **Backup Integration**

### **Include Data Persistence in Backups**

```bash
# Enhanced backup that includes shared data
echo "📦 Creating comprehensive backup with data persistence..."

# Backup application code
tar -czf "app-backup-$(date +%Y%m%d).tar.gz" \
    --exclude="storage" \
    --exclude=".env" \
    --exclude="bootstrap/cache" \
    --exclude="public/storage" \
    .

# Backup shared data separately
tar -czf "shared-backup-$(date +%Y%m%d).tar.gz" -C .. shared/

# Create combined backup manifest
cat > "backup-manifest-$(date +%Y%m%d).txt" << EOF
# Backup created: $(date)
# Laravel Data Persistence Backup Manifest

## Application Code
File: app-backup-$(date +%Y%m%d).tar.gz
Contains: Application code without symlinked directories

## Shared Data
File: shared-backup-$(date +%Y%m%d).tar.gz
Contains: User data, environment config, Laravel storage, caches

## Restoration Instructions
1. Extract app-backup to project directory
2. Extract shared-backup to parent directory
3. Run: bash shared/emergency-recovery.sh "\$(pwd)"
4. Run: bash shared/health-check.sh

## Verification
- Health check should pass: ✅ 6 passed, ⚠️ 0 warnings, ❌ 0 failed
EOF

echo "✅ Comprehensive backup created with data persistence"
```

---

## **Monitoring Alerts**

### **Automated Health Check Monitoring**

```bash
# Add to monitoring system (e.g., cron + email alerts)
# 0 */6 * * * cd /path/to/project && bash shared/health-check.sh || echo "Data persistence issue detected" | mail -s "ALERT: Data Persistence Failure" admin@domain.com

# Manual monitoring script
create_monitoring_alert() {
    bash shared/health-check.sh > /tmp/health-status.log 2>&1
    if [[ $? -ne 0 ]]; then
        echo "🚨 ALERT: Data persistence health check failed"
        echo "Time: $(date)"
        echo "Details:"
        cat /tmp/health-status.log
        # Send alert via email, Slack, etc.
    else
        echo "✅ Data persistence system healthy"
    fi
}

# Run monitoring check
create_monitoring_alert
```

---

## **Integration with Other Maintenance Tasks**

### **Before Server Updates**

```bash
# Before any server maintenance
echo "🔍 Pre-maintenance data persistence check..."
bash shared/health-check.sh

# Create emergency backup
tar -czf "pre-maintenance-shared-$(date +%Y%m%d-%H%M).tar.gz" -C .. shared/
echo "✅ Emergency backup created"
```

### **After Server Updates**

```bash
# After server maintenance
echo "🔍 Post-maintenance data persistence verification..."

# Re-run health check
bash shared/health-check.sh

# Test application functionality
php artisan config:cache
php artisan storage:link

# Verify user data accessibility
ls -la ../shared/storage/app/public/
echo "✅ Post-maintenance verification complete"
```

---

This maintenance guide ensures the data persistence system remains healthy and continues protecting user data throughout all maintenance operations.
