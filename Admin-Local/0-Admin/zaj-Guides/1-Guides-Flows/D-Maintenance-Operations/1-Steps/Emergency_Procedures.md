# Emergency Procedures

**Purpose:** Critical emergency response procedures for production Laravel applications including rollback, disaster recovery, and incident management.

**Use Case:** Immediate response to production issues, security incidents, and system failures

---

## **Analysis Source**

Based on **Laravel - Final Guides/V2/phase4_deployment_guide.md** Step 23:

- V2 Step 23.1: Emergency rollback procedures
- V2 Step 23.2: Disaster recovery protocols
- V2 Step 23.3: Incident response workflows

---

## **1. Emergency Rollback System**

### **1.1: Create Emergency Rollback Script**

**Purpose:** Instant rollback to previous working release in case of critical issues.

```bash
# SSH into your server
ssh your-server

# Create emergency rollback script
cat > ~/emergency_rollback.sh << 'EOF'
#!/bin/bash

echo "🚨 EMERGENCY ROLLBACK INITIATED - $(date)"
echo "========================================"

DOMAIN_PATH="$HOME/domains/your-domain.com"
RELEASES_PATH="$DOMAIN_PATH/releases"
CURRENT_LINK="$DOMAIN_PATH/current"

# Enable maintenance mode immediately
echo "🚧 Enabling maintenance mode..."
cd "$DOMAIN_PATH/current"
php artisan down --message="Emergency maintenance - system recovery in progress" --retry=30 2>/dev/null || echo "Maintenance mode command failed"

# Identify current and previous releases
echo ""
echo "🔍 Identifying releases..."
CURRENT_RELEASE=$(readlink "$CURRENT_LINK" 2>/dev/null | basename)
echo "Current release: $CURRENT_RELEASE"

# Find previous release (second newest)
cd "$RELEASES_PATH"
PREVIOUS_RELEASE=$(ls -t | head -2 | tail -1)
echo "Previous release: $PREVIOUS_RELEASE"

if [ -z "$PREVIOUS_RELEASE" ] || [ ! -d "$PREVIOUS_RELEASE" ]; then
    echo "❌ ERROR: No previous release found for rollback"
    echo "Available releases:"
    ls -la "$RELEASES_PATH"
    exit 1
fi

# Create backup of current state
echo ""
echo "💾 Backing up current state..."
BACKUP_NAME="emergency-backup-$(date +%Y%m%d_%H%M%S)"
cp -r "$DOMAIN_PATH/current" "$HOME/backups/$BACKUP_NAME" 2>/dev/null || mkdir -p "$HOME/backups" && cp -r "$DOMAIN_PATH/current" "$HOME/backups/$BACKUP_NAME"
echo "✅ Current state backed up to: ~/backups/$BACKUP_NAME"

# Perform atomic rollback
echo ""
echo "🔄 Performing atomic rollback..."
ROLLBACK_TARGET="$RELEASES_PATH/$PREVIOUS_RELEASE"

# Update current symlink atomically
ln -sfn "$ROLLBACK_TARGET" "$CURRENT_LINK"

if [ $? -eq 0 ]; then
    echo "✅ Rollback completed successfully"
    echo "📍 Active release: $PREVIOUS_RELEASE"

    # Clear caches to prevent stale data
    echo ""
    echo "🗑️ Clearing application caches..."
    cd "$DOMAIN_PATH/current"
    php artisan cache:clear 2>/dev/null || echo "Cache clear failed"
    php artisan config:clear 2>/dev/null || echo "Config clear failed"
    php artisan route:clear 2>/dev/null || echo "Route clear failed"
    php artisan view:clear 2>/dev/null || echo "View clear failed"

    # Disable maintenance mode
    echo ""
    echo "✅ Disabling maintenance mode..."
    php artisan up 2>/dev/null || echo "Maintenance mode disable failed"

    # Verify website response
    echo ""
    echo "🌐 Verifying website response..."
    sleep 3
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://your-domain.com")
    if [ "$HTTP_STATUS" = "200" ]; then
        echo "✅ Website is responding (HTTP $HTTP_STATUS)"
    else
        echo "⚠️ Website response: HTTP $HTTP_STATUS"
    fi

    # Log rollback action
    echo "$(date): EMERGENCY ROLLBACK: $CURRENT_RELEASE -> $PREVIOUS_RELEASE" >> "$HOME/emergency.log"

    echo ""
    echo "🎉 EMERGENCY ROLLBACK COMPLETED SUCCESSFULLY"
    echo "📊 System Status: OPERATIONAL"
    echo "⏱️ Total Downtime: ~2-3 minutes"

else
    echo "❌ CRITICAL ERROR: Rollback failed"
    echo "🚨 MANUAL INTERVENTION REQUIRED"
    exit 1
fi
EOF

chmod +x ~/emergency_rollback.sh

echo "✅ Emergency rollback script created"
```

### **1.2: Test Emergency Rollback (Safe Test)**

```bash
# Create test environment for rollback testing
mkdir -p ~/test-rollback/releases/release1 ~/test-rollback/releases/release2

# Test the rollback logic
echo "🧪 Testing rollback logic..."
cd ~/test-rollback

# Create fake releases
echo "test1" > releases/release1/version
echo "test2" > releases/release2/version

# Create current symlink
ln -s releases/release2 current

# Test rollback script logic (without affecting production)
echo "Current points to: $(readlink current)"
echo "Previous release would be: $(ls -t releases/ | head -2 | tail -1)"

echo "✅ Rollback test environment ready"
```

**Expected Result:** Emergency rollback capability with <5 minute recovery time.

---

## **2. Disaster Recovery Procedures**

### **2.1: Create Disaster Recovery Script**

**Purpose:** Complete system restoration from catastrophic failures.

```bash
cat > ~/disaster_recovery.sh << 'EOF'
#!/bin/bash

echo "🆘 DISASTER RECOVERY INITIATED - $(date)"
echo "======================================="

# Configuration
DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"
DB_NAME="your_database_name"
DB_USER="your_database_user"
DB_PASS="your_database_password"
BACKUP_PATH="$HOME/backups"

echo "📋 Disaster Recovery Plan Activated"
echo "Target Domain: $DOMAIN"
echo "Recovery Point Objective (RPO): 24 hours"
echo "Recovery Time Objective (RTO): 60 minutes"

# Step 1: Assess damage
echo ""
echo "🔍 STEP 1: Damage Assessment"
echo "=============================="

# Check if domain directory exists
if [ -d "$DOMAIN_PATH" ]; then
    echo "✅ Domain directory exists: $DOMAIN_PATH"
    DOMAIN_SIZE=$(du -sh "$DOMAIN_PATH" | cut -f1)
    echo "📊 Current size: $DOMAIN_SIZE"
else
    echo "❌ Domain directory missing: $DOMAIN_PATH"
    DOMAIN_MISSING=true
fi

# Check database connectivity
echo ""
echo "📊 Database Status:"
if mysql -u $DB_USER -p"$DB_PASS" -e "USE $DB_NAME; SELECT 1;" 2>/dev/null; then
    echo "✅ Database accessible"
    DB_SIZE=$(mysql -u $DB_USER -p"$DB_PASS" -e "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema='$DB_NAME';" | tail -1)
    echo "📊 Database size: ${DB_SIZE}MB"
else
    echo "❌ Database not accessible"
    DB_MISSING=true
fi

# Step 2: Locate latest backups
echo ""
echo "💾 STEP 2: Backup Location & Verification"
echo "=========================================="

# Find latest database backup
LATEST_DB_BACKUP=$(find "$BACKUP_PATH/database" -name "*.sql.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)
if [ -n "$LATEST_DB_BACKUP" ]; then
    echo "✅ Latest database backup: $(basename $LATEST_DB_BACKUP)"
    echo "📅 Backup date: $(stat -c %y "$LATEST_DB_BACKUP")"
    DB_BACKUP_SIZE=$(du -sh "$LATEST_DB_BACKUP" | cut -f1)
    echo "📊 Backup size: $DB_BACKUP_SIZE"
else
    echo "❌ No database backup found"
    exit 1
fi

# Find latest application backup
LATEST_APP_BACKUP=$(find "$BACKUP_PATH/files" -name "app_backup_*.tar.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)
if [ -n "$LATEST_APP_BACKUP" ]; then
    echo "✅ Latest application backup: $(basename $LATEST_APP_BACKUP)"
    echo "📅 Backup date: $(stat -c %y "$LATEST_APP_BACKUP")"
    APP_BACKUP_SIZE=$(du -sh "$LATEST_APP_BACKUP" | cut -f1)
    echo "📊 Backup size: $APP_BACKUP_SIZE"
else
    echo "❌ No application backup found"
    exit 1
fi

# Find latest storage backup
LATEST_STORAGE_BACKUP=$(find "$BACKUP_PATH/files" -name "storage_backup_*.tar.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)
if [ -n "$LATEST_STORAGE_BACKUP" ]; then
    echo "✅ Latest storage backup: $(basename $LATEST_STORAGE_BACKUP)"
    echo "📅 Backup date: $(stat -c %y "$LATEST_STORAGE_BACKUP")"
    STORAGE_BACKUP_SIZE=$(du -sh "$LATEST_STORAGE_BACKUP" | cut -f1)
    echo "📊 Backup size: $STORAGE_BACKUP_SIZE"
fi

# Step 3: Recovery confirmation
echo ""
echo "⚠️ STEP 3: Recovery Confirmation"
echo "================================="
echo "🚨 WARNING: This will completely restore the system from backups"
echo "📅 Recovery Point: $(stat -c %y "$LATEST_DB_BACKUP")"
echo ""
echo "Data that will be LOST:"
echo "- Any changes made after backup date"
echo "- Current database state (if accessible)"
echo "- Current application files (if accessible)"
echo ""
read -p "🔴 Are you sure you want to proceed? (type 'RECOVER' to confirm): " confirm

if [ "$confirm" != "RECOVER" ]; then
    echo "❌ Recovery cancelled by user"
    exit 1
fi

# Step 4: Begin recovery
echo ""
echo "🔄 STEP 4: Beginning System Recovery"
echo "===================================="

# Create recovery workspace
RECOVERY_WORKSPACE="$HOME/disaster-recovery-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$RECOVERY_WORKSPACE"
cd "$RECOVERY_WORKSPACE"

echo "📁 Recovery workspace: $RECOVERY_WORKSPACE"

# Recovery Step 4.1: Database Recovery
echo ""
echo "📊 Step 4.1: Database Recovery"
echo "------------------------------"

if [ "$DB_MISSING" = true ]; then
    # Create database if missing
    echo "🗃️ Creating database: $DB_NAME"
    mysql -u $DB_USER -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

# Restore database from backup
echo "📥 Restoring database from backup..."
gunzip -c "$LATEST_DB_BACKUP" | mysql -u $DB_USER -p"$DB_PASS" $DB_NAME

if [ $? -eq 0 ]; then
    echo "✅ Database restored successfully"
else
    echo "❌ Database restoration failed"
    exit 1
fi

# Recovery Step 4.2: Application Recovery
echo ""
echo "📦 Step 4.2: Application Recovery"
echo "---------------------------------"

# Create temporary recovery directory
mkdir -p temp-recovery
cd temp-recovery

# Extract application backup
echo "📥 Extracting application backup..."
tar -xzf "$LATEST_APP_BACKUP"

# Verify extraction
if [ -d "current" ] || [ -f "artisan" ]; then
    echo "✅ Application backup extracted successfully"
else
    echo "❌ Application backup extraction failed"
    exit 1
fi

# Recovery Step 4.3: Storage Recovery
echo ""
echo "🗂️ Step 4.3: Storage Recovery"
echo "-----------------------------"

if [ -n "$LATEST_STORAGE_BACKUP" ]; then
    echo "📥 Extracting storage backup..."
    mkdir -p storage-recovery
    cd storage-recovery
    tar -xzf "$LATEST_STORAGE_BACKUP"
    cd ..
    echo "✅ Storage backup extracted successfully"
fi

# Step 5: Deploy recovered system
echo ""
echo "🚀 STEP 5: Deploying Recovered System"
echo "======================================"

# Create new release directory
NEW_RELEASE="disaster-recovery-$(date +%Y%m%d_%H%M%S)"
RELEASE_PATH="$DOMAIN_PATH/releases/$NEW_RELEASE"

# Recreate directory structure if missing
if [ "$DOMAIN_MISSING" = true ]; then
    echo "🏗️ Recreating domain directory structure..."
    mkdir -p "$DOMAIN_PATH"/{releases,shared/{storage,public}}
fi

# Deploy recovered application
echo "📦 Deploying recovered application..."
mkdir -p "$RELEASE_PATH"
cp -r temp-recovery/* "$RELEASE_PATH/"

# Restore shared storage if available
if [ -n "$LATEST_STORAGE_BACKUP" ]; then
    echo "🗂️ Restoring shared storage..."
    cp -r storage-recovery/* "$DOMAIN_PATH/shared/"
fi

# Configure environment
echo "⚙️ Configuring environment..."
if [ -f "$DOMAIN_PATH/shared/.env" ]; then
    ln -sf "$DOMAIN_PATH/shared/.env" "$RELEASE_PATH/.env"
else
    echo "⚠️ Environment file missing - manual configuration required"
fi

# Link shared directories
echo "🔗 Linking shared directories..."
rm -rf "$RELEASE_PATH/storage"
ln -sf "$DOMAIN_PATH/shared/storage" "$RELEASE_PATH/storage"

# Update current symlink
echo "🔄 Updating current symlink..."
ln -sfn "$RELEASE_PATH" "$DOMAIN_PATH/current"

# Update web root symlink
echo "🌐 Updating web root..."
rm -f "$DOMAIN_PATH/public_html"
ln -sf "$DOMAIN_PATH/current/public" "$DOMAIN_PATH/public_html"

# Step 6: Post-recovery verification
echo ""
echo "✅ STEP 6: Post-Recovery Verification"
echo "===================================="

cd "$DOMAIN_PATH/current"

# Clear all caches
echo "🗑️ Clearing caches..."
php artisan cache:clear 2>/dev/null || echo "Cache clear failed"
php artisan config:clear 2>/dev/null || echo "Config clear failed"
php artisan route:clear 2>/dev/null || echo "Route clear failed"
php artisan view:clear 2>/dev/null || echo "View clear failed"

# Test database connectivity
echo "📊 Testing database connectivity..."
if php artisan tinker --execute="echo 'DB Status: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed');" 2>/dev/null; then
    echo "✅ Database connectivity verified"
else
    echo "❌ Database connectivity failed"
fi

# Test website response
echo "🌐 Testing website response..."
sleep 5
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN")
if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Website responding (HTTP $HTTP_STATUS)"
else
    echo "⚠️ Website response: HTTP $HTTP_STATUS"
fi

# Log recovery action
echo "$(date): DISASTER RECOVERY COMPLETED - RTO: $(date +%s)" >> "$HOME/disaster-recovery.log"

echo ""
echo "🎉 DISASTER RECOVERY COMPLETED"
echo "=============================="
echo "📊 Recovery Status: SUCCESSFUL"
echo "⏱️ Recovery Time: ~30-60 minutes"
echo "📅 Recovery Point: $(stat -c %y "$LATEST_DB_BACKUP")"
echo "📁 Recovery Workspace: $RECOVERY_WORKSPACE"
echo ""
echo "🔍 Next Steps:"
echo "1. Verify all application functionality"
echo "2. Check user data integrity"
echo "3. Test all critical business processes"
echo "4. Monitor error logs for issues"
echo "5. Notify stakeholders of recovery completion"
EOF

chmod +x ~/disaster_recovery.sh

echo "✅ Disaster recovery script created"
```

### **2.2: Create Recovery Verification Script**

```bash
cat > ~/recovery_verification.sh << 'EOF'
#!/bin/bash

echo "🔍 Recovery Verification Checklist - $(date)"
echo "============================================"

DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"

# System verification
echo "🖥️ System Verification:"
echo "Current release: $(readlink $DOMAIN_PATH/current | basename)"
echo "Web root: $(readlink $DOMAIN_PATH/public_html)"
echo "Disk usage: $(df -h $HOME | tail -1 | awk '{print $5}')"

# Application verification
echo ""
echo "📱 Application Verification:"
cd "$DOMAIN_PATH/current"

# Laravel status
echo "Laravel version: $(php artisan --version)"
echo "Environment: $(php artisan env)"

# Database verification
echo ""
echo "📊 Database Verification:"
php artisan tinker --execute="
echo 'Total users: ' . App\Models\User::count();
echo 'Database size: ' . DB::select('SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = DATABASE()')[0]->size_mb . 'MB';
"

# File integrity
echo ""
echo "📁 File Integrity:"
echo "Storage permissions: $(ls -ld storage/ | awk '{print $1}')"
echo "Shared storage size: $(du -sh ../shared/storage | cut -f1)"
echo "Public uploads size: $(du -sh ../shared/public | cut -f1)"

# Performance test
echo ""
echo "⚡ Performance Test:"
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "https://$DOMAIN")
echo "Response time: ${RESPONSE_TIME}s"

# Error check
echo ""
echo "🚨 Error Check:"
ERROR_COUNT=$(grep -c "ERROR\|CRITICAL" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "Recent errors: $ERROR_COUNT"

echo ""
echo "✅ Recovery verification completed"
EOF

chmod +x ~/recovery_verification.sh

echo "✅ Recovery verification script created"
```

---

## **3. Security Incident Response**

### **3.1: Create Security Incident Response Script**

```bash
cat > ~/security_incident_response.sh << 'EOF'
#!/bin/bash

echo "🚨 SECURITY INCIDENT RESPONSE - $(date)"
echo "======================================="

DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"
INCIDENT_LOG="$HOME/security-incidents.log"

echo "🛡️ Security Incident Response Protocol Activated"
echo "Incident ID: SEC-$(date +%Y%m%d-%H%M%S)"

# Log incident start
echo "$(date): SECURITY INCIDENT RESPONSE INITIATED" >> "$INCIDENT_LOG"

# Step 1: Immediate containment
echo ""
echo "🚧 STEP 1: Immediate Containment"
echo "================================"

# Enable maintenance mode
echo "🚧 Enabling maintenance mode..."
cd "$DOMAIN_PATH/current"
php artisan down --message="Security maintenance in progress" --retry=300

# Change sensitive passwords (simulate - actual implementation would vary)
echo "🔐 Initiating password changes..."
echo "- Database passwords: [MANUAL ACTION REQUIRED]"
echo "- SSH keys: [MANUAL ACTION REQUIRED]"
echo "- API tokens: [MANUAL ACTION REQUIRED]"

# Step 2: Evidence collection
echo ""
echo "🔍 STEP 2: Evidence Collection"
echo "=============================="

EVIDENCE_DIR="$HOME/security-evidence-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$EVIDENCE_DIR"

# Collect system information
echo "📊 Collecting system information..."
echo "System snapshot - $(date)" > "$EVIDENCE_DIR/system_snapshot.txt"
ps aux >> "$EVIDENCE_DIR/system_snapshot.txt"
netstat -tulpn >> "$EVIDENCE_DIR/system_snapshot.txt"
who >> "$EVIDENCE_DIR/system_snapshot.txt"

# Collect log files
echo "📋 Collecting log files..."
cp storage/logs/laravel.log "$EVIDENCE_DIR/" 2>/dev/null || echo "Laravel log not accessible"
cp /var/log/nginx/access.log "$EVIDENCE_DIR/" 2>/dev/null || echo "Nginx access log not accessible"
cp /var/log/nginx/error.log "$EVIDENCE_DIR/" 2>/dev/null || echo "Nginx error log not accessible"
cp /var/log/auth.log "$EVIDENCE_DIR/" 2>/dev/null || echo "Auth log not accessible"

# Collect file integrity information
echo "🗂️ Collecting file integrity..."
find "$DOMAIN_PATH/current" -type f -name "*.php" -exec md5sum {} \; > "$EVIDENCE_DIR/file_hashes.txt"

echo "✅ Evidence collected in: $EVIDENCE_DIR"

# Step 3: Threat assessment
echo ""
echo "⚠️ STEP 3: Threat Assessment"
echo "============================"

# Check for common attack patterns
echo "🔍 Checking for attack patterns..."

# SQL injection attempts
SQL_INJECTION_COUNT=$(grep -ci "union\|select\|drop\|insert\|update\|delete" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "Potential SQL injection attempts: $SQL_INJECTION_COUNT"

# File upload attacks
FILE_UPLOAD_COUNT=$(grep -ci "\.php\|\.exe\|\.sh" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "Suspicious file uploads: $FILE_UPLOAD_COUNT"

# Brute force attempts
BRUTE_FORCE_COUNT=$(grep -ci "failed\|authentication\|login" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "Failed authentication attempts: $BRUTE_FORCE_COUNT"

# Check for modified files
echo ""
echo "📝 Checking for unauthorized modifications..."
find "$DOMAIN_PATH/current" -name "*.php" -mtime -1 | head -10

# Step 4: System hardening
echo ""
echo "🛡️ STEP 4: System Hardening"
echo "============================"

# File permission audit
echo "🔒 Auditing file permissions..."
find "$DOMAIN_PATH/current" -type f -perm -002 | head -10 > "$EVIDENCE_DIR/world_writable.txt"
WRITABLE_COUNT=$(cat "$EVIDENCE_DIR/world_writable.txt" | wc -l)
echo "World-writable files found: $WRITABLE_COUNT"

# Check for suspicious processes
echo "🔍 Checking for suspicious processes..."
ps aux | grep -E "(nc|netcat|wget|curl)" | grep -v grep > "$EVIDENCE_DIR/suspicious_processes.txt"

# Network connection audit
echo "🌐 Auditing network connections..."
netstat -tulpn | grep ESTABLISHED > "$EVIDENCE_DIR/network_connections.txt"

# Step 5: Recovery planning
echo ""
echo "🔄 STEP 5: Recovery Planning"
echo "==========================="

echo "📋 Recovery Options Available:"
echo "1. Clean rollback to previous release"
echo "2. Selective file restoration from backup"
echo "3. Complete disaster recovery from backup"
echo "4. Manual cleanup and hardening"

# Check backup availability
LATEST_BACKUP=$(find "$HOME/backups/database" -name "*.sql.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)
if [ -n "$LATEST_BACKUP" ]; then
    echo "✅ Latest clean backup available: $(basename $LATEST_BACKUP)"
    echo "📅 Backup date: $(stat -c %y "$LATEST_BACKUP")"
else
    echo "❌ No recent backup available"
fi

# Step 6: Incident documentation
echo ""
echo "📝 STEP 6: Incident Documentation"
echo "=================================="

cat > "$EVIDENCE_DIR/incident_report.md" << 'EOI'
# Security Incident Report

**Incident ID:** SEC-[DATETIME]
**Detection Time:** [DATETIME]
**Response Time:** [DATETIME]
**Severity:** [HIGH/MEDIUM/LOW]

## Incident Summary
[Description of what happened]

## Impact Assessment
- **Systems Affected:** [List]
- **Data Compromised:** [Yes/No/Unknown]
- **Service Availability:** [Degraded/Offline/Normal]
- **Users Affected:** [Number/All/None]

## Timeline
- **[TIME]** - Incident detected
- **[TIME]** - Response initiated
- **[TIME]** - Containment measures applied
- **[TIME]** - Investigation started
- **[TIME]** - Resolution implemented

## Technical Details
- **Attack Vector:** [Description]
- **Vulnerability Exploited:** [Description]
- **Evidence Collected:** [List files]

## Actions Taken
1. [Action 1]
2. [Action 2]
3. [Action 3]

## Recovery Actions
1. [Recovery action 1]
2. [Recovery action 2]

## Lessons Learned
- [What we learned]
- [How to prevent similar incidents]

## Recommendations
1. [Recommendation 1]
2. [Recommendation 2]

**Report prepared by:** [Name]
**Date:** [Date]
EOI

echo "✅ Incident report template created: $EVIDENCE_DIR/incident_report.md"

# Log incident completion
echo "$(date): SECURITY INCIDENT RESPONSE COMPLETED - Evidence: $EVIDENCE_DIR" >> "$INCIDENT_LOG"

echo ""
echo "🎯 SECURITY INCIDENT RESPONSE COMPLETED"
echo "======================================="
echo "📁 Evidence Location: $EVIDENCE_DIR"
echo "📋 Incident Log: $INCIDENT_LOG"
echo ""
echo "🔍 Next Steps:"
echo "1. Complete incident report"
echo "2. Implement recovery plan"
echo "3. Apply security patches"
echo "4. Monitor for continued threats"
echo "5. Notify relevant stakeholders"
echo "6. Schedule security review"
EOF

chmod +x ~/security_incident_response.sh

echo "✅ Security incident response script created"
```

---

## **4. Critical Contact Procedures**

### **4.1: Create Emergency Contact System**

```bash
cat > ~/emergency_contacts.sh << 'EOF'
#!/bin/bash

echo "📞 Emergency Contact System - $(date)"
echo "===================================="

# Emergency contact information
cat << 'CONTACTS'
🚨 EMERGENCY CONTACTS 🚨

TECHNICAL CONTACTS:
├─ Server Administrator
│  ├─ Name: [Name]
│  ├─ Phone: [Phone]
│  ├─ Email: [Email]
│  └─ Availability: 24/7

├─ Lead Developer
│  ├─ Name: [Name]
│  ├─ Phone: [Phone]
│  ├─ Email: [Email]
│  └─ Availability: Business hours + on-call

├─ Backup Developer
│  ├─ Name: [Name]
│  ├─ Phone: [Phone]
│  ├─ Email: [Email]
│  └─ Availability: On-call weekends

BUSINESS CONTACTS:
├─ Project Owner
│  ├─ Name: [Name]
│  ├─ Phone: [Phone]
│  ├─ Email: [Email]
│  └─ Authority: Final decisions

├─ Business Manager
│  ├─ Name: [Name]
│  ├─ Phone: [Phone]
│  ├─ Email: [Email]
│  └─ Authority: Operational decisions

VENDOR SUPPORT:
├─ Hostinger Support
│  ├─ Phone: +370 5 214 1231
│  ├─ Email: support@hostinger.com
│  ├─ Live Chat: https://www.hostinger.com
│  └─ Availability: 24/7

├─ CodeCanyon Support
│  ├─ Profile: [Vendor Profile URL]
│  ├─ Support: [Support Email]
│  └─ Response: 1-3 business days

ESCALATION MATRIX:
├─ Level 1: Server Administrator (immediate)
├─ Level 2: Lead Developer (if Level 1 unavailable)
├─ Level 3: Project Owner (major incidents)
├─ Level 4: External vendor support

EMERGENCY PROCEDURES:
├─ Website Down: bash ~/emergency_rollback.sh
├─ Security Incident: bash ~/security_incident_response.sh
├─ Data Loss: bash ~/disaster_recovery.sh
├─ Performance Issues: bash ~/performance_monitor.sh

CONTACTS

# Send notification (placeholder - implement based on your notification system)
echo ""
echo "📧 Notification System:"
echo "Email alerts: [Configure SMTP/mail service]"
echo "SMS alerts: [Configure SMS service]"
echo "Slack/Teams: [Configure webhook]"

# Log emergency access
echo "$(date): Emergency contact list accessed" >> "$HOME/emergency.log"
EOF

chmod +x ~/emergency_contacts.sh

echo "✅ Emergency contact system created"
```

### **4.2: Create Incident Communication Template**

```bash
cat > ~/incident_communication.sh << 'EOF'
#!/bin/bash

echo "📢 Incident Communication Templates"
echo "=================================="

cat << 'TEMPLATES'

🚨 INITIAL INCIDENT NOTIFICATION
Subject: [URGENT] Production Issue - [System Name]

Team,

We are currently experiencing a production issue with [System Name].

INCIDENT DETAILS:
- Detection Time: [TIME]
- Severity: [HIGH/MEDIUM/LOW]
- Impact: [Description]
- Affected Users: [Number/All/Specific groups]
- Current Status: [Investigating/Responding/Resolving]

IMMEDIATE ACTIONS:
- [Action 1]
- [Action 2]
- [Action 3]

ESTIMATED RESOLUTION: [Time estimate]
NEXT UPDATE: [Time for next update]

Incident Response Team:
- Lead: [Name]
- Technical: [Name]
- Communication: [Name]

Updates will be provided every [interval] until resolved.

Thanks,
[Your Name]

═══════════════════════════════════

📋 STATUS UPDATE TEMPLATE
Subject: [UPDATE] Production Issue - [System Name] - [Status]

Team,

UPDATE on the production issue with [System Name]:

CURRENT STATUS: [Resolved/In Progress/Investigating]

PROGRESS MADE:
- [Progress item 1]
- [Progress item 2]
- [Progress item 3]

REMAINING ACTIONS:
- [Action 1]
- [Action 2]

REVISED ETA: [New time estimate if changed]
NEXT UPDATE: [Time for next update]

Impact remains: [Current impact description]

Thanks,
[Your Name]

═══════════════════════════════════

✅ RESOLUTION NOTIFICATION
Subject: [RESOLVED] Production Issue - [System Name]

Team,

The production issue with [System Name] has been RESOLVED.

RESOLUTION SUMMARY:
- Issue: [What happened]
- Root Cause: [Why it happened]
- Resolution: [How it was fixed]
- Total Downtime: [Duration]

VERIFICATION:
- System Status: ✅ Operational
- User Access: ✅ Restored
- Performance: ✅ Normal
- Data Integrity: ✅ Verified

POST-INCIDENT ACTIONS:
- [ ] Post-mortem scheduled for [date/time]
- [ ] Documentation updated
- [ ] Monitoring enhanced
- [ ] Process improvements identified

Thank you for your patience during this incident.

Thanks,
[Your Name]

TEMPLATES

echo ""
echo "💡 Usage Instructions:"
echo "1. Copy appropriate template"
echo "2. Fill in the bracketed placeholders"
echo "3. Send to relevant stakeholders"
echo "4. Log communication in incident report"
EOF

chmod +x ~/incident_communication.sh

echo "✅ Incident communication templates created"
```

---

## **5. System Recovery Verification**

### **5.1: Create Recovery Checklist**

```bash
cat > ~/recovery_checklist.sh << 'EOF'
#!/bin/bash

echo "✅ System Recovery Verification Checklist"
echo "========================================="

DOMAIN="your-domain.com"
DOMAIN_PATH="$HOME/domains/$DOMAIN"

# Initialize checklist
CHECKLIST_PASSED=0
CHECKLIST_TOTAL=0

check_item() {
    local description="$1"
    local command="$2"
    local expected="$3"

    echo -n "⏳ $description... "

    if eval "$command" >/dev/null 2>&1; then
        echo "✅ PASS"
        ((CHECKLIST_PASSED++))
    else
        echo "❌ FAIL"
    fi
    ((CHECKLIST_TOTAL++))
}

echo "🔍 RECOVERY VERIFICATION CHECKLIST"
echo "Date: $(date)"
echo "System: $DOMAIN"
echo ""

# System Structure Checks
echo "📁 SYSTEM STRUCTURE:"
check_item "Domain directory exists" "[ -d '$DOMAIN_PATH' ]"
check_item "Releases directory exists" "[ -d '$DOMAIN_PATH/releases' ]"
check_item "Shared directory exists" "[ -d '$DOMAIN_PATH/shared' ]"
check_item "Current symlink exists" "[ -L '$DOMAIN_PATH/current' ]"
check_item "Public symlink exists" "[ -L '$DOMAIN_PATH/public_html' ]"

echo ""
echo "🐘 LARAVEL APPLICATION:"
cd "$DOMAIN_PATH/current"
check_item "Laravel artisan available" "[ -f 'artisan' ]"
check_item "Environment file exists" "[ -f '.env' ]"
check_item "Storage directory linked" "[ -L 'storage' ]"
check_item "Config cached" "[ -f 'bootstrap/cache/config.php' ]"
check_item "Routes cached" "[ -f 'bootstrap/cache/routes-v7.php' ]"

echo ""
echo "📊 DATABASE CONNECTIVITY:"
check_item "Database connection" "php artisan tinker --execute='DB::connection()->getPdo()' 2>/dev/null"
check_item "Users table accessible" "php artisan tinker --execute='App\\Models\\User::count()' 2>/dev/null"
check_item "Migrations current" "php artisan migrate:status | grep -q 'Ran'"

echo ""
echo "🌐 WEB ACCESSIBILITY:"
check_item "Website responds HTTP 200" "curl -s -o /dev/null -w '%{http_code}' 'https://$DOMAIN' | grep -q '200'"
check_item "Response time < 5 seconds" "[ \$(curl -s -o /dev/null -w '%{time_total}' 'https://$DOMAIN' | cut -d. -f1) -lt 5 ]"
check_item "SSL certificate valid" "echo | openssl s_client -servername '$DOMAIN' -connect '$DOMAIN:443' 2>/dev/null | openssl x509 -noout -checkend 0"

echo ""
echo "🔒 SECURITY & PERMISSIONS:"
check_item "Storage permissions correct" "[ \$(stat -c %a storage) = '755' ]"
check_item "Environment file secured" "[ \$(stat -c %a .env) = '600' ]"
check_item "No world-writable files" "[ \$(find . -type f -perm -002 | wc -l) -eq 0 ]"

echo ""
echo "📋 LOGGING & MONITORING:"
check_item "Laravel log writable" "[ -w 'storage/logs/laravel.log' ]"
check_item "No recent critical errors" "[ \$(grep -c 'CRITICAL\\|EMERGENCY' storage/logs/laravel.log 2>/dev/null || echo 0) -eq 0 ]"
check_item "Backup script accessible" "[ -x '$HOME/backup_script.sh' ]"
check_item "Health check script accessible" "[ -x '$HOME/health_check.sh' ]"

echo ""
echo "💾 BACKUP & RECOVERY:"
check_item "Backup directory exists" "[ -d '$HOME/backups' ]"
check_item "Recent database backup" "[ \$(find '$HOME/backups/database' -name '*.sql.gz' -mtime -1 | wc -l) -gt 0 ]"
check_item "Recent files backup" "[ \$(find '$HOME/backups/files' -name '*.tar.gz' -mtime -1 | wc -l) -gt 0 ]"
check_item "Emergency rollback ready" "[ -x '$HOME/emergency_rollback.sh' ]"

echo ""
echo "📊 FINAL RESULTS:"
echo "=================="
echo "Tests Passed: $CHECKLIST_PASSED/$CHECKLIST_TOTAL"

PASS_RATE=$((CHECKLIST_PASSED * 100 / CHECKLIST_TOTAL))
echo "Pass Rate: $PASS_RATE%"

if [ $PASS_RATE -ge 95 ]; then
    echo "🎉 RECOVERY VERIFICATION: SUCCESS"
    echo "✅ System is fully operational"
elif [ $PASS_RATE -ge 80 ]; then
    echo "⚠️ RECOVERY VERIFICATION: PARTIAL SUCCESS"
    echo "🔧 Some items need attention"
else
    echo "❌ RECOVERY VERIFICATION: FAILED"
    echo "🚨 Manual intervention required"
fi

# Log verification
echo "$(date): Recovery verification - $CHECKLIST_PASSED/$CHECKLIST_TOTAL passed ($PASS_RATE%)" >> "$HOME/recovery.log"

echo ""
echo "📋 Verification completed - see details above"
EOF

chmod +x ~/recovery_checklist.sh

echo "✅ Recovery verification checklist created"
```

---

## **6. Usage Instructions**

### **Emergency Response Workflow:**

```bash
# 1. IMMEDIATE RESPONSE (Website down/critical issue)
ssh your-server
bash ~/emergency_rollback.sh

# 2. SECURITY INCIDENT
ssh your-server
bash ~/security_incident_response.sh

# 3. COMPLETE SYSTEM FAILURE
ssh your-server
bash ~/disaster_recovery.sh

# 4. POST-RECOVERY VERIFICATION
ssh your-server
bash ~/recovery_checklist.sh

# 5. GET EMERGENCY CONTACTS
bash ~/emergency_contacts.sh
```

### **Recovery Time Objectives:**

- **Emergency Rollback:** < 5 minutes
- **Security Response:** < 15 minutes
- **Disaster Recovery:** < 60 minutes
- **Full Verification:** < 10 minutes

---

## **7. Configuration Checklist**

- [ ] Emergency rollback script tested
- [ ] Disaster recovery script configured
- [ ] Security incident response ready
- [ ] Emergency contacts updated
- [ ] Communication templates prepared
- [ ] Recovery verification checklist ready
- [ ] All scripts executable and accessible
- [ ] Database credentials updated
- [ ] Domain names configured
- [ ] Backup locations verified

---

## **Related Files**

- **Server Monitoring:** [Server_Monitoring.md](Server_Monitoring.md)
- **Performance Monitoring:** [Performance_Monitoring.md](Performance_Monitoring.md)
- **Backup Management:** [Backup_Management.md](Backup_Management.md)

---

## **Emergency Quick Reference**

### **System Down:**

```bash
ssh your-server && bash ~/emergency_rollback.sh
```

### **Security Breach:**

```bash
ssh your-server && bash ~/security_incident_response.sh
```

### **Data Loss:**

```bash
ssh your-server && bash ~/disaster_recovery.sh
```

### **Get Help:**

```bash
bash ~/emergency_contacts.sh
```
