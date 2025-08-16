# Step 01: Pre-Update Backup & Assessment

**Goal:** Create comprehensive backups and assess current system state before applying CodeCanyon vendor updates.

**Time Required:** 30 minutes
**Prerequisites:** Deployed application from 1-Setup-New-Project

---

## **🔍 Tracking Integration**

This step integrates with the **Linear Universal Tracking System (5-Tracking-System)** for organized progress management.

### **Initialize Tracking Session:**

```bash
# Set up tracking environment
export PROJECT_ROOT="$(pwd)"
export TRACKING_ROOT="$PROJECT_ROOT/Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/5-Tracking-System"
export SESSION_DIR="$PROJECT_ROOT/Admin-Local/1-CurrentProject/Tracking"

# Create update session directory
UPDATE_SESSION="2-Update-or-Customization"
mkdir -p "$SESSION_DIR/$UPDATE_SESSION"/{0-Backups/{1-Critical-Files,2-Build-Assets,3-Custom-Files,4-Config-Files},1-Planning,2-Baselines,3-Execution,4-Verification,5-Documentation}

# Initialize session tracking
cat > "$SESSION_DIR/$UPDATE_SESSION/1-Planning/step-01-backup-plan.md" << BACKUP_PLAN
# Step 01: Pre-Update Backup Plan

**Date:** $(date)
**Step:** 01 - Pre-Update Backup
**Session:** $UPDATE_SESSION

## Backup Checklist

- [ ] Database backup completed
- [ ] Application files backup completed
- [ ] Git repository backup completed
- [ ] Configuration files backup completed
- [ ] Custom files backup completed
- [ ] Verification completed

## Tracking Progress
BACKUP_PLAN

echo "🔍 Tracking session initialized for Step 01: Pre-Update Backup"
echo "📁 Session directory: $SESSION_DIR/$UPDATE_SESSION"
```

### **Set Project Variables (Project-Agnostic):**

```bash
# Detect project root automatically
export PROJECT_ROOT="$(pwd)"
export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
export PROJECT_NAME="$(basename "$PROJECT_ROOT" | sed 's/-Root$//' | sed 's/App-Master$//' | sed 's/.*\///g')"
cd "$PROJECT_ROOT"

echo "🏠 Project Root: $PROJECT_ROOT"
echo "📁 Admin Local: $ADMIN_LOCAL"
echo "🏷️ Project Name: $PROJECT_NAME"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and **V2 Missing Content Amendments**:

- V2 Amendment: CodeCanyon-specific update handling
- V1 Step 5.3: Vendor commit strategy
- V2 Amendment: License tracking system

---

## **1.1: Determine Update Scenario**

### **Choose Your Update Type:**

**🔍 Assessment Questions:**

1. **Do you have customizations?** (Check if `app/Custom/` directory exists with content)
2. **What's your deployment method?** (A: Manual, B: GitHub Actions, C: DeployHQ, D: GitHub+Manual)
3. **What type of update?** (Vendor update vs custom feature deployment)

**📋 Scenarios:**

| Scenario                        | Description                            | Complexity | Risk Level |
| ------------------------------- | -------------------------------------- | ---------- | ---------- |
| **1A: Simple Vendor Update**    | No customizations, vendor update only  | Low        | Low        |
| **1B: Protected Vendor Update** | Has customizations, vendor update only | Medium     | Medium     |
| **2A: Custom Feature Deploy**   | Deploying your new custom features     | Low        | Low        |
| **2B: Mixed Update**            | Vendor update + custom features        | High       | High       |

---

## **1.2: System State Assessment**

### **Check Current Application State:**

1. **Navigate to local project:**

```bash
cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"

echo "🔍 Assessing current system state..."
````

2. **Check for customizations:**

   ```bash
   # Check if customizations exist
   if [ -d "app/Custom" ] && [ "$(ls -A app/Custom)" ]; then
       echo "🛡️ CUSTOMIZATIONS DETECTED:"
       echo "   - Protected customization layer exists"
       echo "   - Custom files found in app/Custom/"
       CUSTOMIZATION_MODE="protected"
       find app/Custom -type f | head -10
   else
       echo "✅ NO CUSTOMIZATIONS:"
       echo "   - Standard vendor application"
       echo "   - No custom protection needed"
       CUSTOMIZATION_MODE="simple"
   fi
   ```

3. **Check current version:**

   ```bash
   # Check version tracking
   if [ -f "Admin-Local/codecanyon_management/LICENSE_TRACKING.md" ]; then
       echo "📋 Current Version Info:"
       grep "Version.*|" Admin-Local/codecanyon_management/LICENSE_TRACKING.md | tail -1
   else
       echo "⚠️ No version tracking found - first update?"
   fi
   ```

4. **Check deployment method:**
   ```bash
   # Detect deployment method used
   if [ -f ".github/workflows/deploy.yml" ]; then
       echo "🔄 DEPLOYMENT METHOD: GitHub Actions"
       DEPLOY_METHOD="github_actions"
   elif [ -f "Admin-Local/deployhq_config.json" ]; then
       echo "🔄 DEPLOYMENT METHOD: DeployHQ"
       DEPLOY_METHOD="deployhq"
   elif [ -f "Admin-Local/server_deployment/scripts/deploy.sh" ]; then
       echo "🔄 DEPLOYMENT METHOD: Manual SSH"
       DEPLOY_METHOD="manual_ssh"
   else
       echo "❓ DEPLOYMENT METHOD: Unknown - check setup"
       DEPLOY_METHOD="unknown"
   fi
   ```

---

## **1.3: Create Pre-Update Backups**

### **Create Comprehensive Backup System:**

1. **Create backup directory:**

   ```bash
   BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
   BACKUP_DIR="Admin-Local/backups/pre_update_$BACKUP_DATE"
   mkdir -p "$BACKUP_DIR"/{application,database,server}

   echo "📦 Creating pre-update backup: $BACKUP_DATE"
   ```

2. **Backup application state:**

   ```bash
   # Backup current application
   echo "📱 Backing up application state..."

   # Backup customizations (if they exist)
   if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       tar -czf "$BACKUP_DIR/application/customizations_backup.tar.gz" \
         app/Custom/ \
         config/custom.php \
         database/migrations/custom/ \
         resources/views/custom/ \
         Admin-Local/myCustomizations/ 2>/dev/null
       echo "✅ Customizations backed up"
   fi

   # Backup configuration files
   tar -czf "$BACKUP_DIR/application/config_backup.tar.gz" \
     .env \
     .env.example \
     config/ \
     Admin-Local/ 2>/dev/null
   echo "✅ Configuration backed up"

   # Backup composer/package files
   tar -czf "$BACKUP_DIR/application/packages_backup.tar.gz" \
     composer.json \
     composer.lock \
     package.json \
     package-lock.json 2>/dev/null
   echo "✅ Package files backed up"
   ```

3. **Backup server database:**

   ```bash
   # Database backup (if SSH access available)
   echo "🗄️ Backing up server database..."

   if command -v ssh >/dev/null 2>&1 && ssh-add -l >/dev/null 2>&1; then
       # Create database backup on server
       ssh hostinger-factolo "
         BACKUP_FILE=~/backups/database/pre_update_${BACKUP_DATE}.sql.gz
         mkdir -p ~/backups/database
         mysqldump -u u227177893_p_zaj_socpal_u -p't5TmP9\\\$[iG7hu2eYRWUIWH@IRF2' u227177893_p_zaj_socpal_d | gzip > \$BACKUP_FILE
         echo '✅ Database backup created: '\$BACKUP_FILE
       "
       echo "✅ Server database backed up"
   else
       echo "⚠️ SSH not configured - manual database backup required"
       echo "   Run: bash ~/backup_script.sh on server"
   fi
   ```

4. **Create backup manifest:**

   ```bash
   # Create backup information file
   cat > "$BACKUP_DIR/BACKUP_MANIFEST.md" << MANIFEST_EOF
   # Pre-Update Backup Manifest

   **Backup Created:** $(date)
   **Backup Purpose:** Before CodeCanyon vendor update
   **Customization Mode:** $CUSTOMIZATION_MODE
   **Deployment Method:** $DEPLOY_METHOD

   ## Current State
   - **Current Version:** [Check LICENSE_TRACKING.md]
   - **Target Version:** [To be updated]
   - **Customizations:** $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "YES - Protected" || echo "NO - Standard")

   ## Backup Contents
   - ✅ Application customizations (if any)
   - ✅ Configuration files (.env, config/)
   - ✅ Package files (composer.json, package.json)
   - ✅ Server database backup
   - ✅ Admin-Local/ directory

   ## Restore Instructions
   \`\`\`bash
   # Restore customizations
   tar -xzf application/customizations_backup.tar.gz

   # Restore configuration
   tar -xzf application/config_backup.tar.gz

   # Restore database (on server)
   gunzip -c database/pre_update_${BACKUP_DATE}.sql.gz | mysql -u [user] -p [database]
   \`\`\`

   ## Next Steps
   1. Download new CodeCanyon version
   2. Follow Step 02: Download New CodeCanyon Version
   3. Use customization mode: $CUSTOMIZATION_MODE
   4. Use deployment method: $DEPLOY_METHOD
   MANIFEST_EOF

   echo "✅ Backup manifest created"
   ```

---

## **1.4: Version Documentation**

### **Update Version Tracking:**

1. **Update license tracking:**

   ```bash
   # Update version history if tracking exists
   if [ -f "Admin-Local/codecanyon_management/LICENSE_TRACKING.md" ]; then
       echo ""
       echo "📝 Add new version to LICENSE_TRACKING.md:"
       echo "   | v[NEW] | [TODAY] | [TODAY] | [SAME-CODE] | Pre-update backup: $BACKUP_DATE |"
       echo ""
       echo "⏳ Manual action required: Update LICENSE_TRACKING.md with new version info"
   fi
   ```

2. **Create update log:**

   ```bash
   # Create update log
   mkdir -p Admin-Local/update_logs
   cat > "Admin-Local/update_logs/update_${BACKUP_DATE}.md" << UPDATE_EOF
   # Update Log - $BACKUP_DATE

   ## Pre-Update Status
   - **Started:** $(date)
   - **Current Version:** [Check and update]
   - **Target Version:** [To be updated in Step 02]
   - **Backup Location:** $BACKUP_DIR
   - **Customization Mode:** $CUSTOMIZATION_MODE
   - **Deployment Method:** $DEPLOY_METHOD

   ## Update Progress
   - [x] Step 01: Pre-Update Backup & Assessment
   - [ ] Step 02: Download New CodeCanyon Version
   - [ ] Step 03: Compare Changes
   - [ ] Step 04: Update Vendor Files
   - [ ] Step 05: Test Custom Functions
   - [ ] Step 06: Update Dependencies
   - [ ] Step 07: Test Build Process
   - [ ] Step 08: Deploy Updates
   - [ ] Step 09: Verify Deployment

   ## Notes
   - Backup created: $BACKUP_DATE
   - Customizations: $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "Protected - will preserve app/Custom/" || echo "None - standard update")
   - Deployment: $DEPLOY_METHOD method will be used
   UPDATE_EOF

   echo "✅ Update log created: Admin-Local/update_logs/update_${BACKUP_DATE}.md"
   ```

---

## **✅ Step 01 Completion Checklist**

- [ ] Update scenario determined (1A, 1B, 2A, or 2B)
- [ ] Customization mode identified (simple or protected)
- [ ] Deployment method confirmed (manual_ssh, github_actions, deployhq, or unknown)
- [ ] Pre-update backup created with timestamp
- [ ] Application state backed up (customizations, config, packages)
- [ ] Server database backed up
- [ ] Backup manifest documented
- [ ] Update log initialized
- [ ] Next steps determined based on scenario

---

## **Next Steps**

**Based on your assessment:**

- **If Scenario 1A (Simple):** Continue to [Step 02: Download New CodeCanyon Version](Step_02_Download_New_CodeCanyon_Version.md)
- **If Scenario 1B (Protected):** Continue to [Step 02: Download New CodeCanyon Version](Step_02_Download_New_CodeCanyon_Version.md) with customization protection
- **If Scenario 2A/2B (Custom Deploy):** Continue to [Step 02: Download New CodeCanyon Version](Step_02_Download_New_CodeCanyon_Version.md) with feature deployment

---

## **🛠️ ERROR HANDLING & TROUBLESHOOTING**

### **Common Issues:**

**❌ SSH backup fails:**
```bash
# Test SSH connection first
ssh $SSH_ALIAS "echo 'SSH working'"

# If SSH fails, check your SSH config
cat ~/.ssh/config | grep -A 5 $SSH_ALIAS

# Manual database backup if automated fails
ssh $SSH_ALIAS
cd ~/domains/$PRODUCTION_DOMAIN
bash ~/backup_script.sh
```

**❌ Cannot determine deployment method:**
```bash
# Check for deployment artifacts
find . -name "*.yml" -path "*/.github/*"
find . -name "*deploy*" -path "*/Admin-Local/*"
find . -name "deployhq*" -type f

# If none found, you likely have Manual deployment (Scenario A)
```

**❌ Customizations unclear:**
```bash
# Check for custom files in different locations
find app/Custom -type f 2>/dev/null | wc -l
find . -name "*custom*" -type f -not -path "./vendor/*"
find . -name "*.blade.php" -path "*/custom/*"

# Check git commits for custom work
git log --oneline --grep="custom\|Custom\|CUSTOM"
```

**❌ Backup directory already exists:**
```bash
# Solution: Add timestamp to make unique
export BACKUP_DATE=$(date '+%Y%m%d_%H%M%S')
export BACKUP_NAME="pre_update_backup_$BACKUP_DATE"
mkdir "$ADMIN_LOCAL/backups/$BACKUP_NAME"
```

**❌ Insufficient disk space for backup:**
```bash
# Check available space
df -h .
df -h ~/

# Clean up old backups if needed
ls -la "$ADMIN_LOCAL/backups/"
# Remove old backups manually if safe to do so
```

### **Verification Commands:**
```bash
# Verify backup completeness
ls -la "$ADMIN_LOCAL/backups/pre_update_backup/"
du -sh "$ADMIN_LOCAL/backups/pre_update_backup/"

# Verify database backup
ls -la "$ADMIN_LOCAL/backups/pre_update_backup/"*.sql

# Verify server backup
ssh $SSH_ALIAS "ls -la ~/backups/pre_update_$(date '+%Y%m%d')/"
```

### **🤖 AI Assistant Help:**
```bash
"I'm having trouble with Step 01 - Pre-Update Backup.
Error: [describe your specific error]
My deployment scenario: [1A/1B/2A/2B]
Current project: $PROJECT_NAME
Error details: [exact error message]

Please help me:
1. Diagnose the backup issue
2. Provide alternative backup methods
3. Verify my backup is complete and valid"
```

---

## **📈 STEP IMPROVEMENT NOTES**

**Version:** 3.0  
**Last Updated:** [Current Date]

**Recent Improvements:**
- ✅ Added project-specific path variables
- ✅ Enhanced customization detection
- ✅ Improved backup verification

**Potential Future Improvements:**
- [ ] Add automated backup verification
- [ ] Include backup compression options
- [ ] Add cloud backup integration (AWS S3, Google Drive)
- [ ] Include backup restoration testing
- [ ] Add backup encryption for sensitive data

**Issue Tracking:**
```bash
# If you encounter issues with this step, log them:
echo "Step 01 Issue: [description] - $(date)" >> ~/deployment-improvements.log
echo "Project: $PROJECT_NAME" >> ~/deployment-improvements.log
echo "Scenario: [your scenario]" >> ~/deployment-improvements.log
echo "Suggested Fix: [your solution]" >> ~/deployment-improvements.log
echo "---" >> ~/deployment-improvements.log
```

**Performance Metrics:**
```bash
# Track backup performance
echo "Backup Start: $(date)" > /tmp/backup-performance.log
# [backup commands here]
echo "Backup End: $(date)" >> /tmp/backup-performance.log
echo "Backup Size: $(du -sh "$ADMIN_LOCAL/backups/pre_update_backup/")" >> /tmp/backup-performance.log
```

---

## **Troubleshooting**

### **Issue: SSH backup fails**

```bash
# Test SSH connection
ssh hostinger-factolo "echo 'SSH working'"

# Manual database backup
ssh hostinger-factolo
bash ~/backup_script.sh
```

### **Issue: Cannot determine deployment method**

```bash
# Check for deployment artifacts
find . -name "*.yml" -path "*/.github/*"
find . -name "*deploy*" -path "*/Admin-Local/*"
```

### **Issue: Customizations unclear**

```bash
# Check for custom files
find app/Custom -type f 2>/dev/null | wc -l
find . -name "*custom*" -type f
```
