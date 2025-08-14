# Step 28: Emergency Procedures & Disaster Recovery

**Goal:** Implement comprehensive emergency procedures and disaster recovery protocols for your deployed Laravel application.

**Time Required:** 60 minutes  
**Prerequisites:** Step 27 completed successfully

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** - Phase 4 Analysis:

- V2 Step 23: Emergency Procedures
- V1 Status: ❌ Missing | V2 Status: ✅ Complete | Recommendation: Use V2

---

## **28.1: Create Emergency Rollback Procedures**

### **Create Instant Rollback Script:**

1. **Create emergency rollback capability:**

   ```bash
   ssh hostinger-factolo

   cat > ~/emergency_rollback.sh << 'EOF'
   #!/bin/bash

   DOMAIN_PATH="$HOME/domains/societypal.com"

   echo "🚨 EMERGENCY ROLLBACK PROCEDURE"
   echo "==============================="

   if [ ! -d "$DOMAIN_PATH/releases" ]; then
       echo "❌ Releases directory not found"
       exit 1
   fi

   cd "$DOMAIN_PATH"

   # Show current release
   if [ -L "current" ]; then
       CURRENT_RELEASE=$(readlink current)
       echo "📍 Current release: $CURRENT_RELEASE"
   else
       echo "❌ No current release found"
       exit 1
   fi

   # List available releases
   echo ""
   echo "📋 Available releases for rollback:"
   ls -t releases/ | head -5 | nl

   echo ""
   read -p "Enter number of release to rollback to (1-5): " CHOICE

   if [[ "$CHOICE" =~ ^[1-5]$ ]]; then
       SELECTED_RELEASE=$(ls -t releases/ | sed -n "${CHOICE}p")

       if [ -n "$SELECTED_RELEASE" ]; then
           echo ""
           echo "🔄 Rolling back to: $SELECTED_RELEASE"
           echo "⚠️  Current release will be: $CURRENT_RELEASE"
           echo ""
           read -p "Confirm rollback? (y/N): " CONFIRM

           if [[ "$CONFIRM" =~ ^[Yy]$ ]]; then
               # Perform atomic rollback
               ln -nfs "releases/$SELECTED_RELEASE" current

               if [ $? -eq 0 ]; then
                   echo "✅ Emergency rollback completed to: $SELECTED_RELEASE"
                   echo "🔍 Verifying site response..."
                   sleep 3
                   curl -I https://societypal.com | head -1
               else
                   echo "❌ Rollback failed"
                   exit 1
               fi
           else
               echo "⏹️ Rollback cancelled"
           fi
       else
           echo "❌ Invalid selection"
       fi
   else
       echo "❌ Invalid choice"
   fi
   EOF

   chmod +x ~/emergency_rollback.sh

   echo "✅ Emergency rollback script created"
   ```

2. **Test rollback script (dry run):**

   ```bash
   # Test the script interface (don't confirm actual rollback)
   bash ~/emergency_rollback.sh
   ```

   **Expected Result:** Script shows available releases and rollback options.

---

## **28.2: Create Disaster Recovery Documentation**

### **Create Complete DR Procedures:**

1. **Create disaster recovery documentation:**

   ````bash
   cat > ~/DISASTER_RECOVERY.md << 'EOF'
   # Disaster Recovery Procedures - SocietyPal

   ## Emergency Contacts
   - **Primary Admin:** [Your Name] - [Your Phone] - [Your Email]
   - **Hosting Support:** Hostinger Support - support@hostinger.com
   - **Developer:** [Developer Name] - [Developer Contact]

   ## Critical Information
   - **Server IP:** 31.97.195.108
   - **SSH Port:** 65002
   - **SSH User:** u227177893
   - **Domain:** societypal.com
   - **Staging:** staging.societypal.com
   - **Database:** u227177893_p_zaj_socpal_d
   - **DB User:** u227177893_p_zaj_socpal_u

   ## Emergency Procedures

   ### 1. Website Down (HTTP 500/404)
   ```bash
   # Quick rollback to previous release
   ssh hostinger-factolo
   cd ~/domains/societypal.com
   bash ~/emergency_rollback.sh
   ````

   ### 2. Database Issues

   ```bash
   # Check database connectivity
   cd ~/domains/societypal.com/current
   php artisan tinker --execute="DB::connection()->getPdo()"

   # Restore from backup (if needed)
   cd ~/backups/database
   gunzip backup_YYYYMMDD_HHMMSS.sql.gz
   mysql -u u227177893_p_zaj_socpal_u -p u227177893_p_zaj_socpal_d < backup_YYYYMMDD_HHMMSS.sql
   ```

   ### 3. File Corruption/Loss

   ```bash
   # Restore from backup
   cd ~/backups/files
   tar -xzf storage_backup_YYYYMMDD_HHMMSS.tar.gz -C ~/domains/societypal.com/
   ```

   ### 4. Complete Server Failure

   1. **Contact Hostinger support immediately**
   2. **Restore from off-site backups** (if configured)
   3. **Rebuild server using GitHub repository**
   4. **Restore database from latest backup**
   5. **Update DNS if server IP changed**

   ## Recovery Time Objectives (RTO)

   - **Website Rollback:** < 5 minutes
   - **Database Recovery:** < 30 minutes
   - **Full Site Recovery:** < 2 hours
   - **Complete Rebuild:** < 24 hours

   ## Recovery Point Objectives (RPO)

   - **Database:** Last 24 hours (daily backups)
   - **Files:** Last 24 hours (daily backups)
   - **Code:** Current (Git repository)

   ## Backup Locations

   - **Server:** ~/backups/ (7 days retention)
   - **Off-site:** [Configure S3/Google Drive/etc.]
   - **Git:** GitHub repository (code only)

   ## Verification Steps After Recovery

   1. Check website loads: `curl -I https://societypal.com`
   2. Test database: `php artisan migrate:status`
   3. Verify user uploads: `ls -la shared/public/uploads/`
   4. Check application logs: `tail -f storage/logs/laravel.log`
   5. Test core functionality manually

   ## Prevention Measures

   - Daily automated backups
   - Health monitoring scripts
   - Performance monitoring
   - Regular testing of recovery procedures
   - Keep staging environment synchronized
     EOF

   echo "✅ Disaster recovery documentation created"

   ```

   ```

---

## **28.3: Create Database Emergency Procedures**

### **Create Database Recovery Script:**

1. **Create database emergency script:**

   ```bash
   cat > ~/db_emergency.sh << 'EOF'
   #!/bin/bash

   echo "🗄️ DATABASE EMERGENCY PROCEDURES"
   echo "================================"

   DB_NAME="u227177893_p_zaj_socpal_d"
   DB_USER="u227177893_p_zaj_socpal_u"
   DB_PASS="t5TmP9\$[iG7hu2eYRWUIWH@IRF2"
   BACKUP_DIR="$HOME/backups/database"

   echo "1. Test Database Connection"
   echo "2. Create Emergency Backup"
   echo "3. Restore from Backup"
   echo "4. Check Database Health"
   echo ""
   read -p "Choose option (1-4): " OPTION

   case $OPTION in
       1)
           echo "🔍 Testing database connection..."
           mysql -u $DB_USER -p"$DB_PASS" $DB_NAME -e "SELECT 'Connection successful' as Status;"
           if [ $? -eq 0 ]; then
               echo "✅ Database connection successful"
           else
               echo "❌ Database connection failed"
           fi
           ;;
       2)
           echo "💾 Creating emergency backup..."
           mkdir -p "$BACKUP_DIR"
           BACKUP_FILE="emergency_backup_$(date +%Y%m%d_%H%M%S).sql.gz"
           mysqldump -u $DB_USER -p"$DB_PASS" $DB_NAME | gzip > "$BACKUP_DIR/$BACKUP_FILE"
           if [ $? -eq 0 ]; then
               echo "✅ Emergency backup created: $BACKUP_FILE"
           else
               echo "❌ Emergency backup failed"
           fi
           ;;
       3)
           echo "📋 Available backups:"
           ls -la "$BACKUP_DIR"/*.sql.gz 2>/dev/null | nl
           echo ""
           read -p "Enter backup filename: " BACKUP_NAME
           if [ -f "$BACKUP_DIR/$BACKUP_NAME" ]; then
               echo "⚠️  WARNING: This will overwrite current database!"
               read -p "Confirm restore? (y/N): " CONFIRM
               if [[ "$CONFIRM" =~ ^[Yy]$ ]]; then
                   gunzip -c "$BACKUP_DIR/$BACKUP_NAME" | mysql -u $DB_USER -p"$DB_PASS" $DB_NAME
                   if [ $? -eq 0 ]; then
                       echo "✅ Database restored from backup"
                   else
                       echo "❌ Database restore failed"
                   fi
               fi
           else
               echo "❌ Backup file not found"
           fi
           ;;
       4)
           echo "🏥 Checking database health..."
           mysql -u $DB_USER -p"$DB_PASS" $DB_NAME -e "CHECK TABLE users, migrations;"
           echo ""
           echo "📊 Database size:"
           mysql -u $DB_USER -p"$DB_PASS" -e "
           SELECT
               table_schema as 'Database',
               ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
           FROM information_schema.tables
           WHERE table_schema = '$DB_NAME'
           GROUP BY table_schema;"
           ;;
       *)
           echo "❌ Invalid option"
           ;;
   esac
   EOF

   chmod +x ~/db_emergency.sh

   echo "✅ Database emergency script created"
   ```

2. **Test database emergency script:**

   ```bash
   bash ~/db_emergency.sh
   # Choose option 1 to test connection
   ```

   **Expected Result:** Database emergency procedures available and working.

---

## **28.4: Create Health Check & Monitoring**

### **Create Automated Health Check:**

1. **Create comprehensive health check:**

   ```bash
   cat > ~/health_check.sh << 'EOF'
   #!/bin/bash

   echo "🏥 SYSTEM HEALTH CHECK - $(date)"
   echo "==============================="

   DOMAIN_PATH="$HOME/domains/societypal.com"
   ERROR_COUNT=0

   # Check website response
   echo "🌐 Website Status:"
   HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://societypal.com)
   if [ "$HTTP_CODE" = "200" ]; then
       echo "✅ Website responding (HTTP $HTTP_CODE)"
   else
       echo "❌ Website issue (HTTP $HTTP_CODE)"
       ((ERROR_COUNT++))
   fi

   # Check database connectivity
   echo ""
   echo "🗄️ Database Status:"
   cd "$DOMAIN_PATH/current"
   DB_TEST=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'connected'; } catch(Exception \$e) { echo 'failed'; }" 2>/dev/null | grep -o "connected\|failed")
   if [ "$DB_TEST" = "connected" ]; then
       echo "✅ Database connected"
   else
       echo "❌ Database connection failed"
       ((ERROR_COUNT++))
   fi

   # Check disk space
   echo ""
   echo "💾 Disk Space:"
   DISK_USAGE=$(df -h ~ | awk 'NR==2 {print $5}' | sed 's/%//')
   if [ "$DISK_USAGE" -lt 90 ]; then
       echo "✅ Disk space OK ($DISK_USAGE% used)"
   else
       echo "⚠️  Disk space warning ($DISK_USAGE% used)"
       ((ERROR_COUNT++))
   fi

   # Check recent errors in logs
   echo ""
   echo "📋 Recent Log Errors:"
   RECENT_ERRORS=$(grep -i "ERROR\|CRITICAL\|EMERGENCY" "$DOMAIN_PATH/shared/storage/logs/laravel.log" 2>/dev/null | tail -5 | wc -l)
   if [ "$RECENT_ERRORS" -eq 0 ]; then
       echo "✅ No recent critical errors"
   else
       echo "⚠️  $RECENT_ERRORS recent errors found"
       ((ERROR_COUNT++))
   fi

   # Check SSL certificate
   echo ""
   echo "🔒 SSL Certificate:"
   SSL_DAYS=$(echo | openssl s_client -servername societypal.com -connect societypal.com:443 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2)
   if [ -n "$SSL_DAYS" ]; then
       echo "✅ SSL certificate valid until: $SSL_DAYS"
   else
       echo "⚠️  Unable to check SSL certificate"
   fi

   # Summary
   echo ""
   echo "📊 HEALTH CHECK SUMMARY:"
   if [ "$ERROR_COUNT" -eq 0 ]; then
       echo "✅ All systems healthy"
   else
       echo "⚠️  $ERROR_COUNT issues detected - investigation required"
   fi

   echo ""
   echo "🔍 For detailed investigation:"
   echo "   - Check logs: tail -f $DOMAIN_PATH/shared/storage/logs/laravel.log"
   echo "   - Check performance: bash ~/performance_monitor.sh"
   echo "   - Emergency rollback: bash ~/emergency_rollback.sh"
   EOF

   chmod +x ~/health_check.sh

   echo "✅ Health check script created"
   ```

2. **Test health check:**

   ```bash
   bash ~/health_check.sh
   ```

   **Expected Result:** Comprehensive system health report.

---

## **28.5: Create Emergency Contact & Escalation**

### **Create Emergency Response Protocol:**

1. **Create emergency response documentation:**

   ````bash
   cat > ~/EMERGENCY_RESPONSE.md << 'EOF'
   # Emergency Response Protocol - SocietyPal

   ## Immediate Response Checklist

   ### 🚨 CRITICAL ISSUE (Site Down)
   **Time Limit: 5 minutes**

   1. **Immediate Action:**
      ```bash
      ssh hostinger-factolo
      bash ~/health_check.sh
   ````

   2. **If Health Check Shows Issues:**

      ```bash
      # Quick rollback
      bash ~/emergency_rollback.sh
      ```

   3. **If Rollback Fails:**

      ```bash
      # Emergency database backup
      bash ~/db_emergency.sh
      # Choose option 2 (Create Emergency Backup)
      ```

   4. **Contact Escalation:**
      - **Developer:** [Phone] / [Email]
      - **Hostinger Support:** support@hostinger.com
      - **Backup Admin:** [Contact Info]

   ### ⚠️ WARNING ISSUE (Performance/Errors)

   **Time Limit: 30 minutes**

   1. **Investigate:**

      ```bash
      bash ~/performance_monitor.sh
      bash ~/log_management.sh
      ```

   2. **Optimize if needed:**

      ```bash
      bash ~/optimize_app.sh
      ```

   3. **Monitor for 15 minutes:**
      ```bash
      # Check multiple times
      curl -I https://societypal.com
      ```

   ### 📊 INFO ISSUE (Monitoring Alerts)

   **Time Limit: 2 hours**

   1. **Gather Information:**

      ```bash
      bash ~/health_check.sh
      bash ~/db_emergency.sh  # Option 4 - Health Check
      ```

   2. **Document findings**
   3. **Schedule maintenance if needed**

   ## Communication Templates

   ### Critical Issue Email:

   ```
   Subject: CRITICAL - SocietyPal Website Down

   ISSUE: SocietyPal website is currently experiencing [SPECIFIC ISSUE]
   TIME: [TIMESTAMP]
   IMPACT: Website unavailable to users
   ACTION: [SPECIFIC STEPS TAKEN]
   ETA: [ESTIMATED RESOLUTION TIME]

   Status updates will be provided every 15 minutes.
   ```

   ### Resolution Email:

   ```
   Subject: RESOLVED - SocietyPal Website Issue

   RESOLUTION: SocietyPal website has been restored
   TIME: [TIMESTAMP]
   DURATION: [TOTAL DOWNTIME]
   CAUSE: [ROOT CAUSE]
   PREVENTION: [STEPS TO PREVENT RECURRENCE]
   ```

   ## Emergency Contacts

   - **Primary Admin:** [Your Details]
   - **Secondary Admin:** [Backup Contact]
   - **Developer:** [Developer Contact]
   - **Hosting Provider:** Hostinger Support

   ## Quick Reference Commands

   ```bash
   # Health Check
   bash ~/health_check.sh

   # Emergency Rollback
   bash ~/emergency_rollback.sh

   # Database Emergency
   bash ~/db_emergency.sh

   # Performance Check
   bash ~/performance_monitor.sh

   # Application Optimization
   bash ~/optimize_app.sh
   ```

   EOF

   echo "✅ Emergency response protocol created"

   ```

   ```

---

## **✅ Step 28 Completion Checklist**

- [ ] Emergency rollback script created and tested
- [ ] Disaster recovery documentation complete
- [ ] Database emergency procedures implemented
- [ ] Health check script created and working
- [ ] Emergency response protocol documented
- [ ] All emergency scripts executable and tested

---

## **Next Steps**

Phase 4 Post-Deployment Maintenance is now complete! Your first deployment workflow is fully finished.

**Available options:**

- **2-Subsequent-Deployment:** For CodeCanyon vendor updates
- **3-Maintenance:** For ongoing operational maintenance
- **99-Understand:** For documentation and troubleshooting

---

## **Troubleshooting**

### **Issue: Rollback script fails**

```bash
# Check releases directory
ls -la ~/domains/societypal.com/releases/

# Check current symlink
readlink ~/domains/societypal.com/current
```

### **Issue: Health check shows database issues**

```bash
# Test database manually
mysql -u u227177893_p_zaj_socpal_u -p u227177893_p_zaj_socpal_d

# Check Laravel database config
cd ~/domains/societypal.com/current
php artisan config:show database
```

### **Issue: Emergency scripts not working**

```bash
# Check script permissions
ls -la ~/*.sh

# Make all scripts executable
chmod +x ~/*.sh
```
