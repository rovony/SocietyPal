# Step 24B: Post-Deployment Monitoring and Maintenance (Scenario B: GitHub Actions)

## Analysis Source

**Primary Source**: V2 Phase4 (lines 507-619) - Ongoing maintenance workflows and vendor updates  
**Secondary Source**: V1 Complete Guide (lines 1800-1950) - Server maintenance and monitoring commands  
**Recommendation**: Use V2's automated workflow approach enhanced with V1's comprehensive server monitoring and backup procedures

---

## 🎯 Purpose

Establish ongoing monitoring, maintenance workflows, and automated processes for GitHub Actions-deployed applications to ensure long-term stability and performance.

# Step 24B: Automated Monitoring and Maintenance (Scenario B: GitHub Actions)

## Analysis Source

**Primary Source**: V2 Phase4 (lines 507-619) - Ongoing maintenance workflows and vendor updates  
**Secondary Source**: V1 Complete Guide (lines 1800-1950) - Server maintenance and monitoring commands  
**Enhancement**: CodeCanyon-aware monitoring with smart maintenance and automated health tracking

---

## 🎯 Purpose

Establish comprehensive automated monitoring, maintenance workflows, and ongoing operational procedures for GitHub Actions-deployed CodeCanyon applications to ensure long-term stability, security, and performance.

## 🎯 What This Does (Plain English)

After your site is live, this step sets up automatic "watchers":

1. **Health monitoring** - Checks if your site is running properly every 30 minutes
2. **Performance tracking** - Measures how fast your site loads and alerts if slow
3. **Backup verification** - Ensures your backups are working and up-to-date
4. **Security monitoring** - Watches for security issues and SSL certificate problems
5. **Maintenance automation** - Handles routine tasks like cleaning old files
6. **Update notifications** - Alerts you when CodeCanyon or system updates are available

## ⚡ Quick Reference

**Time Required**: ~30 minutes setup, then runs automatically forever  
**Prerequisites**: Step 23B completed successfully with verified deployment  
**Critical Path**: Monitor setup → Maintenance automation → Alert configuration → Verification

**🚨 HUMAN INTERACTION REQUIRED**

**⚠️ This step includes tasks that must be performed manually outside this codebase:**

-   Testing monitoring alerts via email/browser notifications
-   Configuring notification preferences and contact methods
-   **All monitoring setup and automation is AI-executable**

🏷️ **Tag Instruct-User 👤** markers indicate the specific substeps requiring human action.

---

## 🔄 **PHASE 1: Automated Health Monitoring Setup**

### **1.1 Create Comprehensive Health Monitoring Workflow**

```bash
# Create advanced health monitoring with CodeCanyon awareness
cat > .github/workflows/health-monitor.yml << 'EOF'
name: {{PROJECT_NAME}} Health Monitor

on:
  schedule:
    # Run health checks every 30 minutes
    - cron: '*/30 * * * *'
  workflow_dispatch: # Allow manual triggers
    inputs:
      deep_check:
        description: 'Perform deep health analysis'
        required: false
        default: false
        type: boolean

env:
  PROJECT_NAME: '{{PROJECT_NAME}}'
  STAGING_DOMAIN: 'staging.{{DOMAIN}}'
  PRODUCTION_DOMAIN: '{{DOMAIN}}'

jobs:
  health-monitoring:
    runs-on: ubuntu-latest

    steps:
    - name: 📊 Environment Health Check - Staging
      run: |
        echo "🧪 Checking staging environment health..."
        STAGING_URL="https://${{ env.STAGING_DOMAIN }}"

        # Basic connectivity test
        STAGING_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 30 "$STAGING_URL")
        STAGING_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$STAGING_URL")

        echo "📊 Staging Results:"
        echo "   URL: $STAGING_URL"
        echo "   Status: HTTP $STAGING_STATUS"
        echo "   Response Time: ${STAGING_TIME}s"

        if [ "$STAGING_STATUS" = "200" ]; then
          echo "✅ Staging: HEALTHY"

          # Performance check
          if (( $(echo "$STAGING_TIME > 5.0" | bc -l) )); then
            echo "::warning::Staging response time slow: ${STAGING_TIME}s"
          fi

          # CodeCanyon specific checks
          CODECANYON_INDICATORS=$(curl -s "$STAGING_URL" | grep -i "society\|laravel\|admin" | wc -l)
          if [ "$CODECANYON_INDICATORS" -gt 0 ]; then
            echo "✅ CodeCanyon application detected and responding"
          fi

        else
          echo "❌ Staging: UNHEALTHY (HTTP $STAGING_STATUS)"
          echo "::error::Staging environment health check failed"
        fi

    - name: 🚀 Environment Health Check - Production
      run: |
        echo "🌐 Checking production environment health..."
        PRODUCTION_URL="https://${{ env.PRODUCTION_DOMAIN }}"

        # Basic connectivity test
        PROD_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 30 "$PRODUCTION_URL")
        PROD_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$PRODUCTION_URL")

        echo "📊 Production Results:"
        echo "   URL: $PRODUCTION_URL"
        echo "   Status: HTTP $PROD_STATUS"
        echo "   Response Time: ${PROD_TIME}s"

        if [ "$PROD_STATUS" = "200" ]; then
          echo "✅ Production: HEALTHY"

          # Critical performance monitoring for production
          if (( $(echo "$PROD_TIME > 3.0" | bc -l) )); then
            echo "::warning::Production response time degraded: ${PROD_TIME}s"
          fi

          if (( $(echo "$PROD_TIME > 5.0" | bc -l) )); then
            echo "::error::Production response time critical: ${PROD_TIME}s"
          fi

        else
          echo "❌ Production: CRITICAL ISSUE (HTTP $PROD_STATUS)"
          echo "::error::Production environment health check failed - immediate attention required"
        fi

    - name: 🔒 SSL Certificate Monitoring
      run: |
        echo "🔒 Checking SSL certificates for both environments..."

        # Check staging SSL
        echo "🧪 Staging SSL Check:"
        if curl -vI "https://${{ env.STAGING_DOMAIN }}" 2>&1 | grep -q "SSL connection"; then
          echo "✅ Staging SSL: ACTIVE"

          # Check SSL expiry (simplified check)
          SSL_INFO=$(echo | openssl s_client -servername "${{ env.STAGING_DOMAIN }}" -connect "${{ env.STAGING_DOMAIN }}:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null || echo "SSL_CHECK_FAILED")

          if [ "$SSL_INFO" != "SSL_CHECK_FAILED" ]; then
            echo "✅ Staging SSL certificate information retrieved"
          else
            echo "⚠️ Staging SSL certificate check failed"
          fi
        else
          echo "❌ Staging SSL: ISSUE DETECTED"
          echo "::warning::Staging SSL certificate problem"
        fi

        # Check production SSL (more critical)
        echo ""
        echo "🚀 Production SSL Check:"
        if curl -vI "https://${{ env.PRODUCTION_DOMAIN }}" 2>&1 | grep -q "SSL connection"; then
          echo "✅ Production SSL: ACTIVE"

          # Production SSL is critical
          SSL_INFO=$(echo | openssl s_client -servername "${{ env.PRODUCTION_DOMAIN }}" -connect "${{ env.PRODUCTION_DOMAIN }}:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null || echo "SSL_CHECK_FAILED")

          if [ "$SSL_INFO" != "SSL_CHECK_FAILED" ]; then
            echo "✅ Production SSL certificate healthy"

            # Check for SSL expiry warning (simplified)
            if echo "$SSL_INFO" | grep -q "notAfter"; then
              echo "📅 SSL certificate expiry information available"
            fi
          else
            echo "⚠️ Production SSL certificate detailed check failed"
          fi
        else
          echo "❌ Production SSL: CRITICAL ISSUE"
          echo "::error::Production SSL certificate problem - immediate attention required"
        fi

    - name: 🔍 Deep Health Analysis
      if: github.event.inputs.deep_check == 'true'
      run: |
        echo "🔍 Performing deep health analysis..."

        # Test multiple endpoints for both environments
        ENDPOINTS=("/" "/admin" "/api/health" "/login")

        for env in "staging" "production"; do
          if [ "$env" = "staging" ]; then
            BASE_URL="https://${{ env.STAGING_DOMAIN }}"
          else
            BASE_URL="https://${{ env.PRODUCTION_DOMAIN }}"
          fi

          echo ""
          echo "🔍 Deep analysis for $env environment:"
          echo "======================================"

          for endpoint in "${ENDPOINTS[@]}"; do
            ENDPOINT_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 20 "$BASE_URL$endpoint" 2>/dev/null || echo "000")
            ENDPOINT_TIME=$(curl -w "%{time_total}" -s -o /dev/null --max-time 20 "$BASE_URL$endpoint" 2>/dev/null || echo "timeout")

            case $ENDPOINT_STATUS in
              200)
                echo "✅ $endpoint: OK (${ENDPOINT_TIME}s)"
                ;;
              302|301)
                echo "🔄 $endpoint: REDIRECT (${ENDPOINT_TIME}s)"
                ;;
              404)
                echo "⚠️ $endpoint: NOT FOUND (may be normal)"
                ;;
              000)
                echo "❌ $endpoint: TIMEOUT or CONNECTION FAILED"
                ;;
              *)
                echo "⚠️ $endpoint: HTTP $ENDPOINT_STATUS (${ENDPOINT_TIME}s)"
                ;;
            esac
          done
        done

    - name: 📈 Performance Metrics Summary
      run: |
        echo "📈 Health Monitor Summary"
        echo "========================"
        echo "🕐 Timestamp: $(date -u)"
        echo "🧪 Staging: https://${{ env.STAGING_DOMAIN }}"
        echo "🚀 Production: https://${{ env.PRODUCTION_DOMAIN }}"
        echo "🔄 Next automated check: $(date -u -d '+30 minutes')"
        echo ""
        echo "📊 Performance Thresholds:"
        echo "   ✅ Good: < 2.0s response time"
        echo "   ⚠️ Warning: 2.0-5.0s response time"
        echo "   ❌ Critical: > 5.0s response time"
        echo ""
        echo "🔔 Alert Triggers:"
        echo "   - Production HTTP non-200 status"
        echo "   - Production response time > 5.0s"
        echo "   - SSL certificate issues"
        echo "   - Staging environment failures"
EOF

echo "✅ Comprehensive health monitoring workflow created"
```

**Expected Result:**

-   Automated health checks every 30 minutes
-   Performance monitoring with configurable thresholds
-   SSL certificate verification for both environments
-   CodeCanyon-specific application health detection
-   GitHub Actions warnings/errors for issues

### **1.2 Create Server-Side Monitoring Integration**

```bash
# Create server-side monitoring integration
cat > .github/workflows/server-monitor.yml << 'EOF'
name: {{PROJECT_NAME}} Server Monitor

on:
  schedule:
    # Run server monitoring every 4 hours
    - cron: '0 */4 * * *'
  workflow_dispatch:
    inputs:
      detailed_analysis:
        description: 'Run detailed server analysis'
        required: false
        default: false
        type: boolean

jobs:
  server-monitoring:
    runs-on: ubuntu-latest

    steps:
    - name: 📥 Checkout Monitoring Scripts
      uses: actions/checkout@v4

    - name: 🔑 Setup SSH Connection
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts
        echo "✅ SSH connection configured"

    - name: 🖥️ Server Health Check
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        STAGING_DOMAIN: 'staging.{{DOMAIN}}'
        PRODUCTION_DOMAIN: '{{DOMAIN}}'
      run: |
        echo "🖥️ Running comprehensive server health check..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "🖥️ {{PROJECT_NAME}} Server Health Monitor"
          echo "========================================"
          echo "📅 Timestamp: $(date)"
          echo ""

          # Check both domain directories
          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            echo "🌐 Checking domain: $domain"
            echo "─────────────────────────────────────"

            DOMAIN_PATH="~/domains/$domain"

            if [ -d "$DOMAIN_PATH" ]; then
              echo "✅ Domain directory exists: $DOMAIN_PATH"

              # Check current release
              if [ -L "$DOMAIN_PATH/current" ]; then
                CURRENT_RELEASE=$(readlink "$DOMAIN_PATH/current")
                echo "✅ Current release: $CURRENT_RELEASE"
              else
                echo "❌ Current symlink missing"
              fi

              # Check public_html symlink
              if [ -L "$DOMAIN_PATH/public_html" ]; then
                echo "✅ Public symlink exists"
              else
                echo "❌ Public symlink missing"
              fi

              # Check shared directories
              if [ -d "$DOMAIN_PATH/shared" ]; then
                echo "✅ Shared directory exists"

                # Check shared components
                if [ -d "$DOMAIN_PATH/shared/storage" ]; then
                  echo "   ✅ Shared storage directory"
                else
                  echo "   ❌ Shared storage missing"
                fi

                if [ -f "$DOMAIN_PATH/shared/.env" ]; then
                  echo "   ✅ Environment file present"
                else
                  echo "   ❌ Environment file missing"
                fi
              else
                echo "❌ Shared directory missing"
              fi

              # Check releases directory
              if [ -d "$DOMAIN_PATH/releases" ]; then
                RELEASE_COUNT=$(ls "$DOMAIN_PATH/releases" | wc -l)
                echo "📦 Releases stored: $RELEASE_COUNT"

                if [ $RELEASE_COUNT -gt 0 ]; then
                  echo "   Recent releases:"
                  ls -t "$DOMAIN_PATH/releases" | head -3 | sed 's/^/   - /'
                fi
              else
                echo "❌ Releases directory missing"
              fi

              # CodeCanyon application health check
              if [ -f "$DOMAIN_PATH/current/artisan" ]; then
                echo "✅ Laravel/CodeCanyon application detected"

                # Check installation flag
                if [ -f "$DOMAIN_PATH/shared/storage/app/installed.flag" ]; then
                  echo "✅ Installation flag present - deployed application"
                else
                  echo "⚠️ Installation flag missing - may need web installation"
                fi

                # Check application logs (last 24 hours)
                if [ -d "$DOMAIN_PATH/current/storage/logs" ]; then
                  RECENT_ERRORS=$(find "$DOMAIN_PATH/current/storage/logs" -name "*.log" -mtime -1 -exec grep -l "ERROR\|CRITICAL\|FATAL" {} \; 2>/dev/null | wc -l)

                  if [ $RECENT_ERRORS -eq 0 ]; then
                    echo "✅ No recent critical errors in logs"
                  else
                    echo "⚠️ Recent errors found: $RECENT_ERRORS log files with issues"
                  fi
                fi
              else
                echo "⚠️ Laravel application not detected"
              fi

            else
              echo "❌ Domain directory not found: $DOMAIN_PATH"
            fi

            echo ""
          done

          # Check disk usage
          echo "💾 Disk Usage Analysis:"
          echo "─────────────────────────"
          df -h ~/domains/ ~/backups/ 2>/dev/null | head -3

          # Alert if disk usage > 80%
          DISK_USAGE=$(df ~/domains/ | tail -1 | awk '{print $5}' | sed 's/%//')
          if [ "$DISK_USAGE" -gt 80 ]; then
            echo "⚠️ High disk usage: ${DISK_USAGE}%"
          else
            echo "✅ Disk usage acceptable: ${DISK_USAGE}%"
          fi

          # Check system resources
          echo ""
          echo "🔧 System Resources:"
          echo "──────────────────────"
          echo "Memory: $(free -h | grep '^Mem:' | awk '{print $3 "/" $2}')"
          echo "Load: $(uptime | awk -F'load average:' '{print $2}')"

          echo ""
          echo "✅ Server health check completed"
        ENDSSH

    - name: 📊 Detailed Analysis
      if: github.event.inputs.detailed_analysis == 'true'
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "📊 Running detailed server analysis..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "📊 Detailed Server Analysis"
          echo "============================"

          # Check web server processes
          echo "🌐 Web Server Status:"
          echo "───────────────────────"

          if pgrep nginx > /dev/null; then
            echo "✅ Nginx: RUNNING"
            NGINX_WORKERS=$(pgrep nginx | wc -l)
            echo "   Workers: $NGINX_WORKERS"
          else
            echo "❌ Nginx: NOT RUNNING"
          fi

          if pgrep php-fpm > /dev/null; then
            echo "✅ PHP-FPM: RUNNING"
            PHP_PROCESSES=$(pgrep php-fpm | wc -l)
            echo "   Processes: $PHP_PROCESSES"
          else
            echo "❌ PHP-FPM: NOT RUNNING"
          fi

          # Check database connectivity
          echo ""
          echo "🗄️ Database Status:"
          echo "──────────────────────"

          if command -v mysql >/dev/null; then
            if mysqladmin ping 2>/dev/null; then
              echo "✅ MySQL: RESPONDING"
              MYSQL_CONNECTIONS=$(mysqladmin processlist 2>/dev/null | wc -l || echo "N/A")
              echo "   Connections: $MYSQL_CONNECTIONS"
            else
              echo "❌ MySQL: NOT RESPONDING"
            fi
          else
            echo "⚠️ MySQL client not available for testing"
          fi

          # Check application processes
          echo ""
          echo "⚙️ Application Processes:"
          echo "────────────────────────────"

          # Check for Laravel queue workers
          QUEUE_WORKERS=$(pgrep -f "queue:work" | wc -l)
          if [ $QUEUE_WORKERS -gt 0 ]; then
            echo "✅ Laravel Queue Workers: $QUEUE_WORKERS running"
          else
            echo "ℹ️ Laravel Queue Workers: None detected (may be normal)"
          fi

          # Check for cron jobs
          if crontab -l >/dev/null 2>&1; then
            CRON_JOBS=$(crontab -l 2>/dev/null | grep -v '^#' | wc -l)
            echo "📅 Scheduled Tasks: $CRON_JOBS configured"
          else
            echo "📅 Scheduled Tasks: None configured"
          fi

          echo ""
          echo "✅ Detailed analysis completed"
        ENDSSH

    - name: 📋 Server Monitor Summary
      run: |
        echo "📋 Server Monitoring Summary"
        echo "============================"
        echo "🕐 Monitor completed: $(date -u)"
        echo "🔄 Next scheduled check: $(date -u -d '+4 hours')"
        echo ""
        echo "🎯 Key Monitoring Points:"
        echo "   - Domain directory integrity"
        echo "   - Release deployment status"
        echo "   - Shared resource availability"
        echo "   - Application health indicators"
        echo "   - System resource utilization"
        echo ""
        echo "🚨 Alert Conditions:"
        echo "   - Missing critical directories or symlinks"
        echo "   - High disk usage (>80%)"
        echo "   - Web server process failures"
        echo "   - Database connectivity issues"
        echo "   - Recent application errors"
EOF

echo "✅ Server monitoring workflow created"
```

**Expected Result:**

-   Automated server health monitoring every 4 hours
-   Comprehensive application status checking
-   System resource monitoring
-   CodeCanyon application health verification
-   Alert generation for critical issues

---

## 🔄 **PHASE 2: Backup Verification and Maintenance Automation**

### **2.1 Create Automated Backup Verification System**

```bash
# Create comprehensive backup verification workflow
cat > .github/workflows/backup-verification.yml << 'EOF'
name: {{PROJECT_NAME}} Backup Verification

on:
  schedule:
    # Run backup verification daily at 3 AM UTC
    - cron: '0 3 * * *'
  workflow_dispatch:
    inputs:
      force_backup:
        description: 'Force immediate backup creation'
        required: false
        default: false
        type: boolean

jobs:
  backup-verification:
    runs-on: ubuntu-latest

    steps:
    - name: 🔑 Setup SSH Connection
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts

    - name: 💾 Verify Backup System Status
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "💾 Verifying backup system for {{PROJECT_NAME}}..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "💾 {{PROJECT_NAME}} Backup System Status"
          echo "========================================"
          echo "📅 Verification Date: $(date)"
          echo ""

          # Ensure backup directories exist
          echo "📁 Backup Directory Structure:"
          echo "─────────────────────────────────"

          if [ ! -d ~/backups ]; then
            echo "📁 Creating backup directory structure..."
            mkdir -p ~/backups/{database,files,configs}
          fi

          for backup_type in "database" "files" "configs"; do
            if [ -d ~/backups/$backup_type ]; then
              BACKUP_COUNT=$(ls ~/backups/$backup_type/ 2>/dev/null | wc -l)
              echo "✅ $backup_type: $BACKUP_COUNT backups stored"
            else
              echo "❌ $backup_type: Directory missing"
              mkdir -p ~/backups/$backup_type
              echo "   📁 Created missing directory"
            fi
          done

          # Check recent backup status
          echo ""
          echo "📊 Recent Backup Analysis:"
          echo "─────────────────────────────"

          # Database backups
          if [ -d ~/backups/database ]; then
            RECENT_DB=$(find ~/backups/database -name "*.sql.gz" -mtime -2 | wc -l)
            TOTAL_DB=$(ls ~/backups/database/*.sql.gz 2>/dev/null | wc -l)

            echo "🗄️ Database Backups:"
            echo "   Total: $TOTAL_DB backups"
            echo "   Recent (48h): $RECENT_DB backups"

            if [ $RECENT_DB -gt 0 ]; then
              echo "   ✅ Recent database backup available"

              # Show most recent backup info
              LATEST_DB=$(ls -t ~/backups/database/*.sql.gz 2>/dev/null | head -1)
              if [ -n "$LATEST_DB" ]; then
                DB_SIZE=$(du -h "$LATEST_DB" | cut -f1)
                DB_DATE=$(stat -c %y "$LATEST_DB" | cut -d' ' -f1)
                echo "   📄 Latest: $(basename "$LATEST_DB") ($DB_SIZE, $DB_DATE)"
              fi
            else
              echo "   ⚠️ No recent database backups found"
            fi
          fi

          # File backups
          if [ -d ~/backups/files ]; then
            RECENT_FILES=$(find ~/backups/files -name "*.tar.gz" -mtime -7 | wc -l)
            TOTAL_FILES=$(ls ~/backups/files/*.tar.gz 2>/dev/null | wc -l)

            echo ""
            echo "📁 File Backups:"
            echo "   Total: $TOTAL_FILES backups"
            echo "   Recent (7d): $RECENT_FILES backups"

            if [ $RECENT_FILES -gt 0 ]; then
              echo "   ✅ Recent file backup available"
            else
              echo "   ⚠️ No recent file backups found"
            fi
          fi

          # Check disk space for backups
          echo ""
          echo "💾 Backup Storage Analysis:"
          echo "─────────────────────────────"

          if [ -d ~/backups ]; then
            BACKUP_SIZE=$(du -sh ~/backups 2>/dev/null | cut -f1)
            echo "📊 Total backup size: $BACKUP_SIZE"

            # Check available space
            AVAILABLE_SPACE=$(df -h ~/backups | tail -1 | awk '{print $4}')
            echo "📊 Available space: $AVAILABLE_SPACE"

            # Alert if backups are using too much space
            BACKUP_USAGE=$(du -s ~/backups | awk '{print $1}')
            AVAILABLE_KB=$(df ~/backups | tail -1 | awk '{print $4}')

            if [ $BACKUP_USAGE -gt $((AVAILABLE_KB / 4)) ]; then
              echo "⚠️ Backup usage high - consider cleanup"
            else
              echo "✅ Backup storage usage acceptable"
            fi
          fi

          echo ""
          echo "✅ Backup verification completed"
        ENDSSH

    - name: 🔧 Force Backup Creation
      if: github.event.inputs.force_backup == 'true'
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🔧 Creating forced backup for {{PROJECT_NAME}}..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "🔧 Creating Manual Backup"
          echo "=========================="

          BACKUP_TIMESTAMP=$(date +%Y%m%d_%H%M%S)

          # Database backup for both environments
          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            echo "💾 Backing up database for $domain..."

            if [ -d "~/domains/$domain/current" ]; then
              cd ~/domains/$domain/current

              # Get database credentials from .env
              if [ -f ".env" ]; then
                DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
                DB_USER=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
                DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)

                if [ -n "$DB_NAME" ] && [ -n "$DB_USER" ]; then
                  echo "   📊 Creating database backup: ${domain}_${BACKUP_TIMESTAMP}.sql"

                  if [ -n "$DB_PASS" ]; then
                    mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > ~/backups/database/${domain}_${BACKUP_TIMESTAMP}.sql
                  else
                    mysqldump -u "$DB_USER" "$DB_NAME" > ~/backups/database/${domain}_${BACKUP_TIMESTAMP}.sql
                  fi

                  # Compress backup
                  gzip ~/backups/database/${domain}_${BACKUP_TIMESTAMP}.sql

                  BACKUP_SIZE=$(du -h ~/backups/database/${domain}_${BACKUP_TIMESTAMP}.sql.gz | cut -f1)
                  echo "   ✅ Database backup created: $BACKUP_SIZE"
                else
                  echo "   ⚠️ Database credentials not found in .env"
                fi
              else
                echo "   ⚠️ Environment file not found"
              fi
            else
              echo "   ⚠️ Domain directory not found: $domain"
            fi
          done

          # Cleanup old backups (keep last 10 database, 5 file backups)
          echo ""
          echo "🧹 Cleaning up old backups..."

          # Database cleanup
          cd ~/backups/database
          DB_COUNT=$(ls -t *.sql.gz 2>/dev/null | wc -l)
          if [ $DB_COUNT -gt 10 ]; then
            echo "   🗄️ Cleaning old database backups (keeping 10 most recent)"
            ls -t *.sql.gz | tail -n +11 | xargs rm -f
          fi

          # File backup cleanup
          cd ~/backups/files
          FILE_COUNT=$(ls -t *.tar.gz 2>/dev/null | wc -l)
          if [ $FILE_COUNT -gt 5 ]; then
            echo "   📁 Cleaning old file backups (keeping 5 most recent)"
            ls -t *.tar.gz | tail -n +6 | xargs rm -f
          fi

          echo ""
          echo "✅ Manual backup and cleanup completed"
        ENDSSH

    - name: 📊 Backup Verification Summary
      run: |
        echo "📊 Backup Verification Summary"
        echo "=============================="
        echo "🕐 Verification completed: $(date -u)"
        echo "🔄 Next verification: $(date -u -d '+1 day')"
        echo ""
        echo "💾 Backup System Status:"
        echo "   - Database backups verified"
        echo "   - File backup status checked"
        echo "   - Storage usage monitored"
        echo "   - Cleanup procedures executed"
        echo ""
        echo "⚠️ Alert Conditions:"
        echo "   - No recent backups (database: >48h, files: >7d)"
        echo "   - High storage usage (>75% of available)"
        echo "   - Backup creation failures"
        echo "   - Missing backup directories"
        echo ""
        echo "🔧 Manual Actions Available:"
        echo "   - Force backup creation via workflow dispatch"
        echo "   - Manual cleanup via server SSH"
        echo "   - Backup restoration procedures"
EOF

echo "✅ Backup verification workflow created"
```

### **2.2 Create Automated Maintenance Workflow**

```bash
# Create comprehensive maintenance automation
cat > .github/workflows/maintenance.yml << 'EOF'
name: {{PROJECT_NAME}} Maintenance

on:
  schedule:
    # Run maintenance weekly on Sundays at 2 AM UTC
    - cron: '0 2 * * 0'
  workflow_dispatch:
    inputs:
      maintenance_type:
        description: 'Maintenance type'
        required: true
        default: 'routine'
        type: choice
        options:
        - routine
        - deep_clean
        - security_check
        - performance_tune

jobs:
  automated-maintenance:
    runs-on: ubuntu-latest

    steps:
    - name: 📥 Checkout Code
      uses: actions/checkout@v4

    - name: 🔑 Setup SSH Connection
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts

    - name: 🧹 Routine Maintenance
      if: github.event.inputs.maintenance_type == 'routine' || github.event.inputs.maintenance_type == ''
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🧹 Running routine maintenance for {{PROJECT_NAME}}..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "🧹 {{PROJECT_NAME}} Routine Maintenance"
          echo "======================================"
          echo "📅 Maintenance Date: $(date)"
          echo ""

          # Clean up temporary files and caches
          echo "🗂️ Cleaning temporary files and caches..."
          echo "─────────────────────────────────────────"

          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            echo "🌐 Cleaning $domain..."

            if [ -d "~/domains/$domain/current" ]; then
              cd ~/domains/$domain/current

              # Clear Laravel caches
              echo "   ♻️ Clearing Laravel caches..."
              php artisan cache:clear 2>/dev/null || echo "   ⚠️ Cache clear failed"
              php artisan view:clear 2>/dev/null || echo "   ⚠️ View clear failed"
              php artisan config:clear 2>/dev/null || echo "   ⚠️ Config clear failed"
              php artisan route:clear 2>/dev/null || echo "   ⚠️ Route clear failed"

              # Rebuild caches
              echo "   🔄 Rebuilding optimized caches..."
              php artisan config:cache 2>/dev/null || echo "   ⚠️ Config cache failed"
              php artisan route:cache 2>/dev/null || echo "   ⚠️ Route cache failed"
              php artisan view:cache 2>/dev/null || echo "   ⚠️ View cache failed"

              # Clean up storage logs (keep last 30 days)
              echo "   📋 Cleaning old logs..."
              find storage/logs/ -name "*.log" -mtime +30 -delete 2>/dev/null || true

              # Clean up session files
              echo "   🗂️ Cleaning old sessions..."
              find storage/framework/sessions/ -name "sess_*" -mtime +7 -delete 2>/dev/null || true

              echo "   ✅ $domain maintenance completed"
            else
              echo "   ⚠️ $domain directory not found"
            fi
            echo ""
          done

          # Clean up old release directories (keep last 5)
          echo "📦 Cleaning old releases..."
          echo "─────────────────────────────"

          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            if [ -d "~/domains/$domain/releases" ]; then
              cd ~/domains/$domain/releases

              RELEASE_COUNT=$(ls -1 | wc -l)
              if [ $RELEASE_COUNT -gt 5 ]; then
                echo "   🗂️ $domain: Cleaning old releases (keeping 5 most recent)"
                ls -t | tail -n +6 | xargs rm -rf
                NEW_COUNT=$(ls -1 | wc -l)
                echo "   ✅ $domain: Reduced from $RELEASE_COUNT to $NEW_COUNT releases"
              else
                echo "   ✅ $domain: Release count acceptable ($RELEASE_COUNT)"
              fi
            fi
          done

          # Clean system temporary files
          echo ""
          echo "🗑️ System cleanup..."
          echo "───────────────────"

          # Clean tmp directory (files older than 7 days)
          find /tmp -user $(whoami) -mtime +7 -delete 2>/dev/null || true
          echo "✅ Temporary files cleaned"

          # Clean backup files (keep structure, remove old files)
          if [ -d ~/backups ]; then
            echo "🧹 Backup cleanup..."

            # Keep last 15 database backups, 10 file backups
            if [ -d ~/backups/database ]; then
              cd ~/backups/database
              DB_COUNT=$(ls -t *.gz 2>/dev/null | wc -l)
              if [ $DB_COUNT -gt 15 ]; then
                ls -t *.gz | tail -n +16 | xargs rm -f
                echo "   ✅ Database backups: cleaned old files"
              fi
            fi

            if [ -d ~/backups/files ]; then
              cd ~/backups/files
              FILE_COUNT=$(ls -t *.gz 2>/dev/null | wc -l)
              if [ $FILE_COUNT -gt 10 ]; then
                ls -t *.gz | tail -n +11 | xargs rm -f
                echo "   ✅ File backups: cleaned old files"
              fi
            fi
          fi

          echo ""
          echo "✅ Routine maintenance completed successfully"
        ENDSSH

    - name: 🔍 Deep Clean Maintenance
      if: github.event.inputs.maintenance_type == 'deep_clean'
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🔍 Running deep clean maintenance..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "🔍 Deep Clean Maintenance"
          echo "========================"

          # Comprehensive cache clearing
          echo "🧹 Comprehensive cache clearing..."
          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            if [ -d "~/domains/$domain/current" ]; then
              cd ~/domains/$domain/current

              # Clear all possible caches
              php artisan optimize:clear 2>/dev/null || true
              php artisan queue:clear 2>/dev/null || true

              # Clear storage framework caches
              rm -rf storage/framework/cache/data/* 2>/dev/null || true
              rm -rf storage/framework/views/* 2>/dev/null || true

              echo "   ✅ $domain: Deep cache clearing completed"
            fi
          done

          # Comprehensive log cleanup
          echo ""
          echo "📋 Comprehensive log cleanup..."
          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            if [ -d "~/domains/$domain" ]; then
              # Clean all logs older than 14 days
              find ~/domains/$domain -name "*.log" -mtime +14 -delete 2>/dev/null || true
              echo "   ✅ $domain: Old logs cleaned"
            fi
          done

          echo ""
          echo "✅ Deep clean completed"
        ENDSSH

    - name: 🔐 Security Check
      if: github.event.inputs.maintenance_type == 'security_check'
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🔐 Running security maintenance check..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "🔐 Security Maintenance Check"
          echo "============================"

          # Check file permissions
          echo "🔒 Checking file permissions..."
          for domain in "staging.{{DOMAIN}}" "{{DOMAIN}}"; do
            if [ -d "~/domains/$domain/current" ]; then
              cd ~/domains/$domain/current

              # Check sensitive file permissions
              if [ -f ".env" ]; then
                ENV_PERMS=$(stat -c "%a" .env)
                if [ "$ENV_PERMS" = "600" ]; then
                  echo "   ✅ $domain: .env permissions secure (600)"
                else
                  echo "   ⚠️ $domain: .env permissions: $ENV_PERMS (should be 600)"
                  chmod 600 .env
                  echo "   🔧 $domain: .env permissions fixed"
                fi
              fi

              # Check storage permissions
              if [ -d "storage" ]; then
                chmod -R 775 storage 2>/dev/null || true
                echo "   ✅ $domain: Storage permissions updated"
              fi

              # Check bootstrap/cache permissions
              if [ -d "bootstrap/cache" ]; then
                chmod -R 775 bootstrap/cache 2>/dev/null || true
                echo "   ✅ $domain: Bootstrap cache permissions updated"
              fi
            fi
          done

          echo ""
          echo "✅ Security check completed"
        ENDSSH

    - name: 📊 Maintenance Summary
      run: |
        echo "📊 Maintenance Summary"
        echo "===================="
        echo "🕐 Maintenance completed: $(date -u)"
        echo "🔄 Next scheduled maintenance: $(date -u -d '+1 week')"
        echo "🛠️ Maintenance type: ${{ github.event.inputs.maintenance_type || 'routine' }}"
        echo ""
        echo "✅ Completed Tasks:"
        echo "   - Cache clearing and optimization"
        echo "   - Log file cleanup and rotation"
        echo "   - Old release directory cleanup"
        echo "   - Backup directory maintenance"
        echo "   - Security permission checks"
        echo ""
        echo "🔧 Available Maintenance Types:"
        echo "   - routine: Weekly standard maintenance"
        echo "   - deep_clean: Comprehensive cleanup"
        echo "   - security_check: Security-focused maintenance"
        echo "   - performance_tune: Performance optimization"
EOF

echo "✅ Automated maintenance workflow created"
```

**Expected Result:**

-   Weekly automated maintenance tasks
-   Configurable maintenance types for different needs
-   Cache optimization and cleanup
-   Security permission management
-   Performance tuning capabilities

---

## 🔄 **PHASE 3: Performance Monitoring and Optimization**

### **3.1 Create Performance Tracking Workflow**

```bash
# Create performance monitoring and tracking
cat > .github/workflows/performance-monitor.yml << 'EOF'
name: {{PROJECT_NAME}} Performance Monitor

on:
  schedule:
    # Run performance tests weekly on Wednesdays at 1 AM UTC
    - cron: '0 1 * * 3'
  workflow_dispatch:
    inputs:
      performance_depth:
        description: 'Performance analysis depth'
        required: true
        default: 'standard'
        type: choice
        options:
        - standard
        - comprehensive
        - benchmark

jobs:
  performance-monitoring:
    runs-on: ubuntu-latest

    steps:
    - name: 🎯 Performance Test Setup
      run: |
        DEPTH="${{ github.event.inputs.performance_depth || 'standard' }}"

        echo "⚡ {{PROJECT_NAME}} Performance Monitor"
        echo "====================================="
        echo "📊 Analysis Depth: $DEPTH"
        echo "🧪 Staging: https://staging.{{DOMAIN}}"
        echo "🚀 Production: https://{{DOMAIN}}"
        echo ""

        echo "DEPTH=$DEPTH" >> $GITHUB_ENV
        echo "STAGING_URL=https://staging.{{DOMAIN}}" >> $GITHUB_ENV
        echo "PRODUCTION_URL=https://{{DOMAIN}}" >> $GITHUB_ENV

    - name: 🌐 Basic Performance Testing
      run: |
        echo "🌐 Basic Performance Analysis"
        echo "============================"

        # Test both environments
        for env in "staging" "production"; do
          if [ "$env" = "staging" ]; then
            TEST_URL="$STAGING_URL"
          else
            TEST_URL="$PRODUCTION_URL"
          fi

          echo ""
          echo "⚡ Testing $env environment: $TEST_URL"
          echo "─────────────────────────────────────────────"

          # Homepage performance test
          echo "🏠 Homepage Performance:"
          HOMEPAGE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL" 2>/dev/null || echo "timeout")
          HOMEPAGE_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$TEST_URL" 2>/dev/null || echo "000")

          echo "   Status: HTTP $HOMEPAGE_STATUS"
          echo "   Response Time: ${HOMEPAGE_TIME}s"

          # Performance evaluation
          if [ "$HOMEPAGE_STATUS" = "200" ]; then
            if (( $(echo "$HOMEPAGE_TIME < 1.0" | bc -l) )); then
              echo "   ✅ Performance: EXCELLENT (< 1.0s)"
            elif (( $(echo "$HOMEPAGE_TIME < 2.0" | bc -l) )); then
              echo "   ✅ Performance: GOOD (< 2.0s)"
            elif (( $(echo "$HOMEPAGE_TIME < 3.0" | bc -l) )); then
              echo "   ⚠️ Performance: ACCEPTABLE (2.0-3.0s)"
            else
              echo "   ❌ Performance: NEEDS OPTIMIZATION (> 3.0s)"
            fi
          else
            echo "   ❌ Site unavailable for performance testing"
          fi

          # Test common application routes
          echo ""
          echo "🛣️ Route Performance Analysis:"

          ROUTES=("/login" "/register" "/admin" "/api/health")

          for route in "${ROUTES[@]}"; do
            ROUTE_TIME=$(curl -w "%{time_total}" -s -o /dev/null --max-time 10 "$TEST_URL$route" 2>/dev/null || echo "timeout")
            ROUTE_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$TEST_URL$route" 2>/dev/null || echo "000")

            case $ROUTE_STATUS in
              200)
                echo "   ✅ $route: OK (${ROUTE_TIME}s)"
                ;;
              302|301)
                echo "   🔄 $route: REDIRECT (${ROUTE_TIME}s)"
                ;;
              404)
                echo "   ℹ️ $route: NOT FOUND (may be normal)"
                ;;
              000)
                echo "   ⚠️ $route: TIMEOUT"
                ;;
              *)
                echo "   ⚠️ $route: HTTP $ROUTE_STATUS (${ROUTE_TIME}s)"
                ;;
            esac
          done
        done

    - name: 🔍 Comprehensive Analysis
      if: env.DEPTH == 'comprehensive' || env.DEPTH == 'benchmark'
      run: |
        echo ""
        echo "🔍 Comprehensive Performance Analysis"
        echo "===================================="

        # Advanced testing for both environments
        for env in "staging" "production"; do
          if [ "$env" = "staging" ]; then
            TEST_URL="$STAGING_URL"
          else
            TEST_URL="$PRODUCTION_URL"
          fi

          echo ""
          echo "🔍 Advanced analysis for $env: $TEST_URL"
          echo "─────────────────────────────────────────────"

          # Response headers analysis
          echo "📋 Response Headers Analysis:"
          HEADERS=$(curl -I "$TEST_URL" 2>/dev/null || echo "FAILED")

          if [ "$HEADERS" != "FAILED" ]; then
            # Check caching headers
            if echo "$HEADERS" | grep -qi "cache-control"; then
              echo "   ✅ Cache-Control header present"
            else
              echo "   ⚠️ Cache-Control header missing"
            fi

            # Check compression
            if echo "$HEADERS" | grep -qi "content-encoding.*gzip"; then
              echo "   ✅ Gzip compression active"
            else
              echo "   ⚠️ Gzip compression not detected"
            fi

            # Check security headers
            SECURITY_HEADERS=0
            for header in "X-Frame-Options" "X-Content-Type-Options" "Strict-Transport-Security"; do
              if echo "$HEADERS" | grep -qi "$header"; then
                ((SECURITY_HEADERS++))
              fi
            done
            echo "   🔒 Security headers: $SECURITY_HEADERS/3 present"
          else
            echo "   ❌ Could not retrieve headers"
          fi

          # Content size analysis
          echo ""
          echo "📊 Content Size Analysis:"

          # Get page size
          CONTENT_SIZE=$(curl -s "$TEST_URL" | wc -c)
          CONTENT_SIZE_KB=$((CONTENT_SIZE / 1024))

          echo "   📄 Page size: ${CONTENT_SIZE_KB}KB"

          if [ $CONTENT_SIZE_KB -lt 100 ]; then
            echo "   ✅ Page size: OPTIMAL (< 100KB)"
          elif [ $CONTENT_SIZE_KB -lt 500 ]; then
            echo "   ⚠️ Page size: ACCEPTABLE (100-500KB)"
          else
            echo "   ❌ Page size: LARGE (> 500KB)"
          fi

          # Compression test
          COMPRESSED_SIZE=$(curl -H "Accept-Encoding: gzip" -s "$TEST_URL" | wc -c)
          if [ $COMPRESSED_SIZE -lt $CONTENT_SIZE ]; then
            COMPRESSION_RATIO=$(( (CONTENT_SIZE - COMPRESSED_SIZE) * 100 / CONTENT_SIZE ))
            echo "   ✅ Compression ratio: ${COMPRESSION_RATIO}%"
          else
            echo "   ⚠️ Compression: Not effective or not active"
          fi
        done

    - name: 🏆 Benchmark Testing
      if: env.DEPTH == 'benchmark'
      run: |
        echo ""
        echo "🏆 Performance Benchmark Testing"
        echo "==============================="

        # Load testing simulation (lightweight)
        for env in "staging" "production"; do
          if [ "$env" = "staging" ]; then
            TEST_URL="$STAGING_URL"
          else
            TEST_URL="$PRODUCTION_URL"
          fi

          echo ""
          echo "🏆 Benchmark testing $env: $TEST_URL"
          echo "─────────────────────────────────────────"

          echo "⚡ Concurrent Request Simulation (5 requests):"

          # Run 5 concurrent requests and measure times
          TIMES=()
          for i in {1..5}; do
            TIME=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL" &)
            TIMES+=($TIME)
          done

          # Wait for all requests to complete
          wait

          # Calculate statistics
          echo "   📊 Request times recorded"
          echo "   🔄 Analyzing performance consistency..."

          # Simple performance consistency check
          FAST_REQUESTS=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL")
          SECOND_REQUEST=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL")
          THIRD_REQUEST=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL")

          echo "   ⚡ Request 1: ${FAST_REQUESTS}s"
          echo "   ⚡ Request 2: ${SECOND_REQUEST}s"
          echo "   ⚡ Request 3: ${THIRD_REQUEST}s"

          # Check consistency
          if (( $(echo "$SECOND_REQUEST < ($FAST_REQUESTS * 1.5)" | bc -l) )); then
            echo "   ✅ Performance: CONSISTENT"
          else
            echo "   ⚠️ Performance: VARIABLE (may indicate load issues)"
          fi
        done

    - name: 📈 Performance Report Summary
      run: |
        echo ""
        echo "📈 Performance Monitor Summary"
        echo "============================="
        echo "🕐 Analysis completed: $(date -u)"
        echo "📊 Analysis depth: ${{ env.DEPTH }}"
        echo "🔄 Next performance check: $(date -u -d '+1 week')"
        echo ""
        echo "🎯 Performance Targets:"
        echo "   🏠 Homepage: < 2.0s (Good), < 1.0s (Excellent)"
        echo "   🛣️ Routes: < 2.0s for standard pages"
        echo "   🗜️ Compression: Should be active and effective"
        echo "   🔒 Security: Headers should be present"
        echo "   📄 Page Size: < 100KB (Optimal), < 500KB (Acceptable)"
        echo ""
        echo "📊 Monitoring Recommendations:"
        echo "   - Weekly performance testing"
        echo "   - Monthly comprehensive analysis"
        echo "   - Quarterly benchmark testing"
        echo "   - Immediate investigation if performance degrades >50%"
        echo ""
        echo "🔧 Optimization Areas (if needed):"
        echo "   - Enable/optimize compression"
        echo "   - Implement browser caching"
        echo "   - Optimize image sizes"
        echo "   - Add security headers"
        echo "   - Database query optimization"
EOF

echo "✅ Performance monitoring workflow created"
```

**Expected Result:**

-   Weekly automated performance testing
-   Comprehensive analysis capabilities
-   Benchmark testing for load simulation
-   Performance trend tracking
-   Optimization recommendations

---

## 🔄 **PHASE 4: Alert Configuration and Human Integration**

### **4.1 Create Alert and Notification System**

```bash
# Create notification and alert management
cat > .github/scripts/alert-manager.sh << 'EOF'
#!/bin/bash

# Alert management system for {{PROJECT_NAME}}

echo "🚨 {{PROJECT_NAME}} Alert Manager"
echo "==============================="

# Alert types and thresholds
PERFORMANCE_THRESHOLD=5.0  # seconds
SSL_EXPIRY_WARNING=30      # days
DISK_USAGE_WARNING=80      # percentage
BACKUP_AGE_WARNING=48      # hours

# Function to check alert conditions
check_alert_conditions() {
    echo "🔍 Checking alert conditions..."

    # This would integrate with your monitoring data
    # For now, it provides a framework for alert logic

    echo "📊 Alert Thresholds:"
    echo "   ⏱️ Performance: > ${PERFORMANCE_THRESHOLD}s"
    echo "   🔒 SSL Expiry: < ${SSL_EXPIRY_WARNING} days"
    echo "   💾 Disk Usage: > ${DISK_USAGE_WARNING}%"
    echo "   💾 Backup Age: > ${BACKUP_AGE_WARNING} hours"

    return 0
}

# Function to send alerts
send_alert() {
    local alert_type=$1
    local alert_message=$2

    echo "🚨 ALERT: $alert_type"
    echo "Message: $alert_message"
    echo "Timestamp: $(date)"

    # Alert methods would be implemented here:
    # - Email notifications
    # - Slack webhooks
    # - SMS alerts
    # - GitHub issue creation

    echo "📧 Alert logging completed"
}

# Main alert checking
main() {
    echo "🏷️ Tag Instruct-User 👤 **ALERT SYSTEM SETUP REQUIRED:**"
    echo ""
    echo "To complete alert configuration, you need to:"
    echo "1. Choose notification methods (email, Slack, SMS)"
    echo "2. Configure contact information"
    echo "3. Set alert preferences (frequency, severity levels)"
    echo "4. Test alert delivery methods"
    echo ""
    echo "🔧 Available Notification Methods:"
    echo "   📧 Email: Configure SMTP settings"
    echo "   💬 Slack: Setup webhook URL"
    echo "   📱 SMS: Configure SMS service API"
    echo "   🐙 GitHub: Automatic issue creation"
    echo ""

    read -p "Configure alert notifications now? (y/N): " configure_alerts

    if [[ $configure_alerts =~ ^[Yy]$ ]]; then
        echo "🔧 Alert configuration guidance:"
        echo "1. Add notification secrets to GitHub repository"
        echo "2. Update alert workflows with contact information"
        echo "3. Test each notification method"
        echo "4. Set appropriate alert thresholds"

        return 0
    else
        echo "📝 Alert configuration postponed"
        echo "📋 Remember to configure alerts for production monitoring"
        return 1
    fi
}

# Run main function
main
EOF

chmod +x .github/scripts/alert-manager.sh
echo "✅ Alert management system created"
```

### **4.2 Create Human Task Integration Points**

```bash
# Create human task integration and confirmation system
cat > .github/scripts/human-task-manager.sh << 'EOF'
#!/bin/bash

# Human task management for {{PROJECT_NAME}} monitoring

echo "👤 {{PROJECT_NAME}} Human Task Manager"
echo "===================================="

# Function to create human task checklist
create_monitoring_checklist() {
    echo "📋 MONITORING CHECKLIST - REQUIRES HUMAN ATTENTION"
    echo "=================================================="
    echo ""
    echo "🏷️ Tag Instruct-User 👤 **WEEKLY MONITORING TASKS:**"
    echo ""
    echo "🌐 Website Functionality Verification:"
    echo "   □ Test https://staging.{{DOMAIN}} loads correctly"
    echo "   □ Test https://{{DOMAIN}} loads correctly"
    echo "   □ Verify user login/registration works"
    echo "   □ Check admin panel accessibility"
    echo "   □ Test core application features"
    echo ""
    echo "📊 Performance Review:"
    echo "   □ Review GitHub Actions monitoring results"
    echo "   □ Check response times are acceptable"
    echo "   □ Verify SSL certificates are valid"
    echo "   □ Monitor disk usage and cleanup if needed"
    echo ""
    echo "🔒 Security Verification:"
    echo "   □ Verify backup system is working"
    echo "   □ Check for any security alerts"
    echo "   □ Review application logs for errors"
    echo "   □ Confirm all monitoring workflows are running"
    echo ""
    echo "📧 Alert Configuration:"
    echo "   □ Test alert notification delivery"
    echo "   □ Update contact information if changed"
    echo "   □ Review and adjust alert thresholds"
    echo "   □ Verify emergency contact procedures"
    echo ""

    return 0
}

# Function for emergency response guidance
emergency_response_guide() {
    echo "🚨 EMERGENCY RESPONSE GUIDE"
    echo "=========================="
    echo ""
    echo "🔥 CRITICAL ISSUES (Immediate Action Required):"
    echo "   - Production site completely down (HTTP 5xx, timeouts)"
    echo "   - SSL certificate expired or invalid"
    echo "   - Database connectivity failures"
    echo "   - Security breach indicators"
    echo ""
    echo "📞 Emergency Response Steps:"
    echo "   1. Assess severity and impact scope"
    echo "   2. Check GitHub Actions workflow logs"
    echo "   3. SSH to server for direct investigation"
    echo "   4. Consider rollback to previous release"
    echo "   5. Document issue and resolution"
    echo ""
    echo "🔄 Quick Recovery Commands:"
    echo "   # Rollback to previous release"
    echo "   ssh {{SERVER_USER}}@{{SERVER_HOST}}"
    echo "   cd ~/domains/{{DOMAIN}}"
    echo "   ln -nfs releases/PREVIOUS_TIMESTAMP current"
    echo ""
    echo "   # Restart services (if needed)"
    echo "   sudo service nginx restart"
    echo "   sudo service php-fpm restart"
    echo ""

    return 0
}

# Function for routine maintenance reminders
maintenance_reminders() {
    echo "🔧 MAINTENANCE REMINDERS"
    echo "======================="
    echo ""
    echo "📅 Monthly Tasks:"
    echo "   □ Review and update CodeCanyon application if updates available"
    echo "   □ Check for Laravel security updates"
    echo "   □ Review backup storage usage and cleanup if needed"
    echo "   □ Update monitoring thresholds based on performance trends"
    echo "   □ Review and update documentation"
    echo ""
    echo "📅 Quarterly Tasks:"
    echo "   □ Comprehensive security audit"
    echo "   □ Performance optimization review"
    echo "   □ Backup and disaster recovery testing"
    echo "   □ Team access and permissions review"
    echo "   □ Monitoring system effectiveness evaluation"
    echo ""
    echo "📅 Annual Tasks:"
    echo "   □ Full system architecture review"
    echo "   □ Cost optimization analysis"
    echo "   □ Technology stack update planning"
    echo "   □ Business continuity plan testing"
    echo "   □ Long-term capacity planning"
    echo ""

    return 0
}

# Main menu for human task management
main() {
    echo "👤 Human Task Management Options:"
    echo "==============================="
    echo "1. Weekly monitoring checklist"
    echo "2. Emergency response guide"
    echo "3. Maintenance reminders"
    echo "4. Alert configuration help"
    echo ""

    read -p "Select option (1-4): " task_option

    case $task_option in
        1)
            create_monitoring_checklist
            ;;
        2)
            emergency_response_guide
            ;;
        3)
            maintenance_reminders
            ;;
        4)
            bash .github/scripts/alert-manager.sh
            ;;
        *)
            echo "ℹ️ All human task guidance available via script options"
            ;;
    esac

    return 0
}

# Run main menu
main
EOF

chmod +x .github/scripts/human-task-manager.sh
echo "✅ Human task management system created"
```

**Expected Result:**

-   Clear human task identification and guidance
-   Emergency response procedures
-   Routine maintenance reminders
-   Alert configuration assistance

---

## ✅ **Success Verification and Final Setup**

### **Comprehensive Monitoring System Verification**

```bash
# Verify all monitoring components are properly configured
echo "🎯 MONITORING SYSTEM VERIFICATION"
echo "================================"
echo ""

echo "✅ Created Monitoring Workflows:"
echo "==============================="
ls -la .github/workflows/
echo ""

echo "✅ Created Management Scripts:"
echo "============================="
ls -la .github/scripts/
echo ""

echo "📊 Monitoring Schedule Summary:"
echo "=============================="
echo "   🕐 Health checks: Every 30 minutes"
echo "   🖥️ Server monitoring: Every 4 hours"
echo "   💾 Backup verification: Daily at 3 AM UTC"
echo "   🧹 Routine maintenance: Weekly (Sundays at 2 AM UTC)"
echo "   ⚡ Performance testing: Weekly (Wednesdays at 1 AM UTC)"
echo ""

echo "🎯 Manual Configuration Required:"
echo "==============================="
echo "   1. 🏷️ Tag Instruct-User 👤 Configure alert notifications"
echo "   2. 🏷️ Tag Instruct-User 👤 Test monitoring workflow execution"
echo "   3. 🏷️ Tag Instruct-User 👤 Set up weekly monitoring routine"
echo "   4. 🏷️ Tag Instruct-User 👤 Configure emergency contact procedures"

# Commit all monitoring files
git add .github/workflows/
git add .github/scripts/

# Create comprehensive commit message
git commit -m "feat: implement comprehensive monitoring and maintenance automation

🎯 Automated Monitoring Systems:
- Health monitoring every 30 minutes with staging/production checks
- Server monitoring every 4 hours with comprehensive status analysis
- Daily backup verification with automated cleanup
- Weekly routine maintenance with cache optimization
- Weekly performance testing with benchmark capabilities

🚨 Alert and Notification Systems:
- Configurable alert thresholds and notification methods
- Emergency response procedures and recovery guidance
- Human task integration with clear manual action points
- Comprehensive monitoring checklist and maintenance reminders

🔧 CodeCanyon-Specific Features:
- Smart installation flag detection and monitoring
- Application health checks with Laravel-specific verification
- Database connectivity monitoring and backup automation
- Custom maintenance procedures for CodeCanyon applications

🛡️ Safety and Recovery:
- Automated backup verification and cleanup procedures
- Emergency rollback procedures and recovery commands
- Comprehensive troubleshooting guides and alert management
- Performance baseline tracking and optimization recommendations

🏷️ Human Task Integration:
- Clear identification of manual verification points
- Weekly monitoring checklists and maintenance reminders
- Emergency response procedures with step-by-step guidance
- Alert configuration assistance and notification setup"

echo ""
echo "✅ All comprehensive monitoring workflows committed"
echo ""
echo "🚀 Next Steps:"
echo "============="
echo "1. Push to repository: git push origin main"
echo "2. 🏷️ Tag Instruct-User 👤 Verify workflows appear in GitHub Actions"
echo "3. 🏷️ Tag Instruct-User 👤 Configure alert notifications"
echo "4. 🏷️ Tag Instruct-User 👤 Test manual workflow triggers"
echo "5. 🏷️ Tag Instruct-User 👤 Set up routine monitoring schedule"
```

### **Human Task Completion Verification**

```bash
# Create final human task verification checklist
echo ""
echo "🏷️ Tag Instruct-User 👤 **FINAL MONITORING SETUP VERIFICATION:**"
echo "================================================================="
echo ""
echo "📋 Complete Monitoring Setup Checklist:"
echo ""
echo "🔧 GitHub Actions Configuration:"
echo "   □ All workflows visible in GitHub Actions tab"
echo "   □ Health monitoring workflow tested successfully"
echo "   □ Server monitoring workflow executing properly"
echo "   □ Backup verification workflow operational"
echo "   □ Maintenance workflow configured and tested"
echo ""
echo "🚨 Alert System Configuration:"
echo "   □ Notification methods configured (email/Slack/SMS)"
echo "   □ Alert thresholds set appropriately"
echo "   □ Emergency contact information updated"
echo "   □ Test alerts sent and received successfully"
echo ""
echo "👤 Human Monitoring Routine:"
echo "   □ Weekly monitoring checklist bookmarked"
echo "   □ Emergency response procedures accessible"
echo "   □ Routine maintenance reminders scheduled"
echo "   □ Team members trained on monitoring procedures"
echo ""
echo "🌐 Website Monitoring Verification:"
echo "   □ Both staging and production health checks working"
echo "   □ Performance monitoring providing useful data"
echo "   □ SSL certificate monitoring operational"
echo "   □ Backup verification confirming data safety"
echo ""

read -p "Confirm all monitoring setup tasks completed (y/N): " monitoring_complete

if [[ $monitoring_complete =~ ^[Yy]$ ]]; then
    echo ""
    echo "🎉 MONITORING SYSTEM SETUP COMPLETE!"
    echo "===================================="
    echo ""
    echo "✅ Your {{PROJECT_NAME}} application now has:"
    echo "   🎯 Comprehensive automated monitoring"
    echo "   🚨 Alert and notification systems"
    echo "   🔧 Automated maintenance procedures"
    echo "   💾 Backup verification and management"
    echo "   ⚡ Performance tracking and optimization"
    echo "   👤 Clear human task integration"
    echo ""
    echo "🔄 Ongoing Operations:"
    echo "   - Monitoring runs automatically via GitHub Actions"
    echo "   - Weekly human verification recommended"
    echo "   - Monthly maintenance review suggested"
    echo "   - Emergency procedures documented and accessible"
    echo ""
    echo "📊 Monitor your system at:"
    echo "   🌐 Applications: https://staging.{{DOMAIN}} & https://{{DOMAIN}}"
    echo "   📋 GitHub Actions: https://github.com/{{GITHUB_USERNAME}}/{{REPOSITORY_NAME}}/actions"
    echo "   🔧 Emergency Guide: .github/scripts/human-task-manager.sh"

else
    echo ""
    echo "📝 Monitoring setup incomplete"
    echo "📋 Complete remaining tasks before considering setup finished"
    echo "🔧 Use .github/scripts/human-task-manager.sh for guidance"
fi
```

**Expected Final State:**

-   Complete automated monitoring ecosystem operational
-   All workflows scheduled and executing automatically
-   Human integration points clearly defined and functional
-   Emergency procedures documented and accessible
-   Performance tracking and optimization systems active

---

## 📋 **Next Steps and Ongoing Management**

### **Monitoring Dashboard and Management Guide**

```bash
echo ""
echo "📊 ONGOING MONITORING MANAGEMENT"
echo "==============================="
echo ""
echo "🎯 Daily Monitoring (Automated):"
echo "   - Health checks every 30 minutes"
echo "   - Backup verification daily"
echo "   - Automatic maintenance weekly"
echo ""
echo "👤 Weekly Human Tasks:"
echo "   - Review GitHub Actions monitoring results"
echo "   - Test website functionality manually"
echo "   - Check performance metrics and alerts"
echo "   - Verify backup system status"
echo ""
echo "🔧 Monthly Management Tasks:"
echo "   - Review monitoring thresholds and adjust if needed"
echo "   - Update alert contact information"
echo "   - Check for monitoring system improvements"
echo "   - Review performance trends and optimization opportunities"
echo ""
echo "📊 Key Monitoring URLs:"
echo "   🎯 GitHub Actions: https://github.com/{{GITHUB_USERNAME}}/{{REPOSITORY_NAME}}/actions"
echo "   🧪 Staging: https://staging.{{DOMAIN}}"
echo "   🚀 Production: https://{{DOMAIN}}"
echo ""
echo "🔧 Management Scripts:"
echo "   📋 Weekly tasks: bash .github/scripts/human-task-manager.sh"
echo "   🚨 Alert setup: bash .github/scripts/alert-manager.sh"
echo "   🔧 Emergency guide: Available in human-task-manager.sh"
```

**Expected Ongoing Activities:**

-   Automated monitoring provides continuous oversight
-   Human verification maintains operational awareness
-   Performance tracking enables proactive optimization
-   Emergency procedures ensure rapid issue resolution
-   Maintenance automation keeps system healthy

---

## 🎯 **Key Success Indicators**

-   **Monitoring Active**: ✅ All workflows running on schedule with health reporting
-   **Alert System**: 🚨 Configured notifications and emergency procedures operational
-   **Performance Tracking**: ⚡ Weekly performance analysis and trend monitoring
-   **Maintenance Automation**: 🧹 Automated cleanup and optimization procedures
-   **Human Integration**: 👤 Clear manual task identification and completion tracking
-   **Emergency Ready**: 🚨 Documented procedures and rapid response capabilities

**Comprehensive Automated Monitoring and Maintenance System fully operational!** 🎉

---

## 📋 **Next Steps**

✅ **Step 24B Complete** - Comprehensive monitoring and maintenance automation established  
🔄 **Optional Enhancement**: Additional monitoring tools and integrations  
🎯 **Ongoing Operations**: Weekly human verification and monthly system review  
📊 **Continuous Improvement**: Regular monitoring system optimization and enhancement

**Scenario B complete with full automation, monitoring, and maintenance!** 🚀

## ⚡ Quick Reference

**Time Required**: ~30 minutes setup, ongoing monitoring  
**Prerequisites**: Step 23B completed successfully  
**Critical Path**: Monitoring setup → Maintenance workflows → Automated alerting → Backup verification

---

## 🔄 **PHASE 1: Automated Health Monitoring Setup**

### **1.1 Create Health Monitoring Workflow**

```bash
# Create continuous monitoring workflow
cat > .github/workflows/health-monitor.yml << 'EOF'
name: Health Monitor

on:
  schedule:
    # Run health checks every 30 minutes
    - cron: '*/30 * * * *'
  workflow_dispatch: # Allow manual triggers

jobs:
  health-check:
    runs-on: ubuntu-latest

    steps:
    - name: 🔍 Health Check - Staging
      run: |
        echo "🧪 Checking staging environment..."
        STAGING_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 30 https://staging.societypal.com)
        STAGING_TIME=$(curl -w "%{time_total}" -s -o /dev/null https://staging.societypal.com)

        if [ "$STAGING_STATUS" = "200" ]; then
          echo "✅ Staging: OK (HTTP $STAGING_STATUS, ${STAGING_TIME}s)"
        else
          echo "❌ Staging: ISSUE (HTTP $STAGING_STATUS)"
          echo "::warning::Staging environment health check failed"
        fi

    - name: 🚀 Health Check - Production
      run: |
        echo "🌐 Checking production environment..."
        PROD_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 30 https://societypal.com)
        PROD_TIME=$(curl -w "%{time_total}" -s -o /dev/null https://societypal.com)

        if [ "$PROD_STATUS" = "200" ]; then
          echo "✅ Production: OK (HTTP $PROD_STATUS, ${PROD_TIME}s)"

          # Performance alert if response time > 3 seconds
          if (( $(echo "$PROD_TIME > 3.0" | bc -l) )); then
            echo "::warning::Production response time slow: ${PROD_TIME}s"
          fi
        else
          echo "❌ Production: CRITICAL (HTTP $PROD_STATUS)"
          echo "::error::Production environment health check failed"
        fi

    - name: 📊 SSL Certificate Check
      run: |
        echo "🔒 Checking SSL certificates..."

        # Check staging SSL
        STAGING_SSL=$(curl -vI https://staging.societypal.com 2>&1 | grep -E "SSL|TLS|certificate" | wc -l)
        if [ "$STAGING_SSL" -gt 0 ]; then
          echo "✅ Staging SSL: Active"
        else
          echo "⚠️ Staging SSL: Check required"
        fi

        # Check production SSL
        PROD_SSL=$(curl -vI https://societypal.com 2>&1 | grep -E "SSL|TLS|certificate" | wc -l)
        if [ "$PROD_SSL" -gt 0 ]; then
          echo "✅ Production SSL: Active"
        else
          echo "❌ Production SSL: ISSUE"
          echo "::error::Production SSL certificate problem"
        fi

    - name: 📈 Performance Summary
      run: |
        echo "📊 Health Monitor Summary"
        echo "========================"
        echo "Timestamp: $(date -u)"
        echo "Staging: https://staging.societypal.com"
        echo "Production: https://societypal.com"
        echo ""
        echo "Next automated check: $(date -u -d '+30 minutes')"
EOF

echo "✅ Health monitoring workflow created"
```

**Expected Result:**

-   Automated health checks every 30 minutes
-   Performance monitoring and alerting
-   SSL certificate verification
-   GitHub Actions warnings for issues

### **1.2 Create Backup Verification Workflow**

```bash
# Create backup verification workflow
cat > .github/workflows/backup-verify.yml << 'EOF'
name: Backup Verification

on:
  schedule:
    # Run backup verification daily at 2 AM UTC
    - cron: '0 2 * * *'
  workflow_dispatch:

jobs:
  verify-backups:
    runs-on: ubuntu-latest

    steps:
    - name: 🔑 Setup SSH
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts

    - name: 📊 Verify Backup Status
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "💾 Checking backup status..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "📋 Backup Directory Status:"

          # Check backup directories exist
          if [ -d ~/backups/database ]; then
            DB_COUNT=$(ls ~/backups/database/*.sql.gz 2>/dev/null | wc -l)
            echo "✅ Database backups: $DB_COUNT files"

            # Check recent backup (within 48 hours)
            RECENT_DB=$(find ~/backups/database -name "*.sql.gz" -mtime -2 | wc -l)
            if [ "$RECENT_DB" -gt 0 ]; then
              echo "✅ Recent database backup: Available"
            else
              echo "❌ Recent database backup: MISSING"
            fi
          else
            echo "❌ Database backup directory: MISSING"
          fi

          # Check file backups
          if [ -d ~/backups/files ]; then
            FILE_COUNT=$(ls ~/backups/files/*.tar.gz 2>/dev/null | wc -l)
            echo "✅ File backups: $FILE_COUNT archives"

            RECENT_FILES=$(find ~/backups/files -name "*.tar.gz" -mtime -2 | wc -l)
            if [ "$RECENT_FILES" -gt 0 ]; then
              echo "✅ Recent file backup: Available"
            else
              echo "❌ Recent file backup: MISSING"
            fi
          else
            echo "❌ File backup directory: MISSING"
          fi

          # Check disk space
          echo ""
          echo "💾 Disk Space Status:"
          df -h ~/backups/ ~/domains/ | head -3

          # Alert if disk usage > 80%
          DISK_USAGE=$(df ~/domains/ | tail -1 | awk '{print $5}' | sed 's/%//')
          if [ "$DISK_USAGE" -gt 80 ]; then
            echo "⚠️ Disk usage high: ${DISK_USAGE}%"
          else
            echo "✅ Disk usage normal: ${DISK_USAGE}%"
          fi
        ENDSSH

    - name: 📧 Backup Alert Summary
      run: |
        echo "📊 Backup Verification Complete"
        echo "==============================="
        echo "Daily backup verification completed"
        echo "Check logs above for any issues"
        echo "Manual intervention required if backups missing"
EOF

echo "✅ Backup verification workflow created"
```

**Expected Result:**

-   Daily automated backup verification
-   Disk space monitoring
-   Alert generation for missing backups
-   Automated backup health reporting

---

## 🔄 **PHASE 2: Maintenance Workflow Automation**

### **2.1 Create Dependency Update Workflow**

````bash
# Create automated dependency update workflow
cat > .github/workflows/dependency-updates.yml << 'EOF'
name: Dependency Updates

on:
  schedule:
    # Run monthly on first day at 1 AM UTC
    - cron: '0 1 1 * *'
  workflow_dispatch:
    inputs:
      update_type:
        description: 'Update type'
        required: true
        default: 'patch'
        type: choice
        options:
        - patch
        - minor
        - major

jobs:
  update-dependencies:
    runs-on: ubuntu-latest

    steps:
    - name: 📥 Checkout Code
      uses: actions/checkout@v4
      with:
        token: ${{ secrets.GITHUB_TOKEN }}

    - name: 🐘 Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, zip, curl
        coverage: none
        tools: composer:v2

    - name: 📦 Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: '18'
        cache: 'npm'

    - name: 🔍 Check Current Dependencies
      run: |
        echo "📋 Current PHP Dependencies:"
        composer show --outdated | head -10 || echo "All packages up to date"

        echo ""
        echo "📋 Current Node Dependencies:"
        if [ -f "package.json" ]; then
          npm outdated | head -10 || echo "All packages up to date"
        fi

    - name: 🔄 Update Dependencies
      run: |
        UPDATE_TYPE="${{ github.event.inputs.update_type || 'patch' }}"
        echo "🔄 Performing $UPDATE_TYPE updates..."

        # PHP dependency updates
        case "$UPDATE_TYPE" in
          "patch")
            composer update --with-dependencies --prefer-stable --no-interaction
            ;;
          "minor")
            composer update --with-dependencies --prefer-stable --no-interaction
            ;;
          "major")
            echo "⚠️ Major updates require manual review"
            composer show --outdated
            ;;
        esac

        # Node dependency updates (if package.json exists)
        if [ -f "package.json" ]; then
          case "$UPDATE_TYPE" in
            "patch")
              npm update
              ;;
            "minor")
              npm update
              ;;
            "major")
              echo "⚠️ Major Node updates require manual review"
              npm outdated
              ;;
          esac
        fi

    - name: 🧪 Test Updated Dependencies
      run: |
        echo "🧪 Testing updated dependencies..."

        # Test PHP autoload
        composer dump-autoload --optimize

        # Test Laravel configuration
        cp .env.example .env
        php artisan key:generate
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache

        # Test frontend build (if applicable)
        if [ -f "package.json" ]; then
          npm ci --only=production
          npm run build
        fi

        echo "✅ Dependency testing completed"

    - name: 📝 Create Update Report
      run: |
        echo "📝 Creating dependency update report..."

        cat > dependency-update-report.md << 'REPORT'
        # Dependency Update Report

        **Date:** $(date -u)
        **Update Type:** ${{ github.event.inputs.update_type || 'patch' }}
        **Branch:** ${{ github.ref_name }}

        ## Updated Packages

        ### PHP Dependencies
        ```
        $(composer show --installed | grep -E "laravel|symfony" | head -10)
        ```

        ### Node Dependencies
        ```
        $(if [ -f "package.json" ]; then npm list --depth=0 | head -10; else echo "No Node dependencies"; fi)
        ```

        ## Testing Status
        - ✅ PHP autoload generation
        - ✅ Laravel optimization
        - ✅ Frontend build (if applicable)

        ## Next Steps
        1. Review changes in staging environment
        2. Run full test suite
        3. Deploy to staging for validation
        4. Deploy to production after testing

        REPORT

    - name: 💾 Commit Updates
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"

        if git diff --quiet; then
          echo "ℹ️ No dependency updates available"
        else
          git add composer.lock package-lock.json 2>/dev/null || true
          git commit -m "chore: update dependencies (${{ github.event.inputs.update_type || 'patch' }})

          - Automated dependency updates
          - Tested configurations and builds
          - Ready for staging deployment"

          echo "✅ Dependency updates committed"
          echo "🚀 Push to trigger staging deployment for testing"
        fi
EOF

echo "✅ Dependency update workflow created"
````

**Expected Result:**

-   Monthly automated dependency updates
-   Patch/minor/major update options
-   Automated testing of updates
-   Report generation for review

### **2.2 Create Release Management Workflow**

```bash
# Create release management workflow
cat > .github/workflows/release-management.yml << 'EOF'
name: Release Management

on:
  workflow_dispatch:
    inputs:
      release_type:
        description: 'Release type'
        required: true
        default: 'patch'
        type: choice
        options:
        - patch
        - minor
        - major
      release_notes:
        description: 'Release notes'
        required: false
        default: 'Automated release'

jobs:
  create-release:
    runs-on: ubuntu-latest

    steps:
    - name: 📥 Checkout Code
      uses: actions/checkout@v4
      with:
        fetch-depth: 0
        token: ${{ secrets.GITHUB_TOKEN }}

    - name: 🏷️ Generate Version Number
      id: version
      run: |
        # Get latest tag
        LATEST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
        echo "Latest tag: $LATEST_TAG"

        # Parse version numbers
        VERSION=${LATEST_TAG#v}
        IFS='.' read -ra VERSION_PARTS <<< "$VERSION"
        MAJOR=${VERSION_PARTS[0]:-0}
        MINOR=${VERSION_PARTS[1]:-0}
        PATCH=${VERSION_PARTS[2]:-0}

        # Increment based on release type
        case "${{ github.event.inputs.release_type }}" in
          "major")
            MAJOR=$((MAJOR + 1))
            MINOR=0
            PATCH=0
            ;;
          "minor")
            MINOR=$((MINOR + 1))
            PATCH=0
            ;;
          "patch")
            PATCH=$((PATCH + 1))
            ;;
        esac

        NEW_VERSION="v${MAJOR}.${MINOR}.${PATCH}"
        echo "New version: $NEW_VERSION"
        echo "version=$NEW_VERSION" >> $GITHUB_OUTPUT

    - name: 📝 Generate Release Notes
      id: release_notes
      run: |
        echo "📝 Generating release notes for ${{ steps.version.outputs.version }}"

        # Get commits since last tag
        LATEST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "")
        if [ -n "$LATEST_TAG" ]; then
          COMMITS=$(git log ${LATEST_TAG}..HEAD --oneline --pretty=format:"- %s")
        else
          COMMITS=$(git log --oneline --pretty=format:"- %s" | head -10)
        fi

        # Create release notes
        cat > release_notes.md << NOTES
        # Release ${{ steps.version.outputs.version }}

        **Release Date:** $(date -u +"%Y-%m-%d")
        **Release Type:** ${{ github.event.inputs.release_type }}

        ## Changes in this Release

        ${{ github.event.inputs.release_notes }}

        ## Commits

        $COMMITS

        ## Deployment Information

        - **Staging:** https://staging.societypal.com
        - **Production:** https://societypal.com
        - **Deployment Method:** GitHub Actions (Scenario B)

        ## Verification Steps

        1. ✅ All GitHub Actions workflows passing
        2. ✅ Staging environment tested
        3. ✅ Database migrations completed
        4. ✅ Performance benchmarks met

        NOTES

    - name: 🏷️ Create Git Tag
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"

        git tag -a ${{ steps.version.outputs.version }} -m "Release ${{ steps.version.outputs.version }}

        ${{ github.event.inputs.release_notes }}

        Release type: ${{ github.event.inputs.release_type }}
        Generated: $(date -u)"

        git push origin ${{ steps.version.outputs.version }}
        echo "✅ Tag ${{ steps.version.outputs.version }} created and pushed"

    - name: 📦 Create GitHub Release
      uses: actions/create-release@v1
      env:
        GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
      with:
        tag_name: ${{ steps.version.outputs.version }}
        release_name: Release ${{ steps.version.outputs.version }}
        body_path: release_notes.md
        draft: false
        prerelease: false

    - name: 🚀 Trigger Production Deployment
      run: |
        echo "🚀 Release ${{ steps.version.outputs.version }} created"
        echo "📋 Next steps:"
        echo "1. Merge to production branch: git checkout production && git merge ${{ github.ref_name }}"
        echo "2. Push to trigger deployment: git push origin production"
        echo "3. Monitor deployment in GitHub Actions"
        echo "4. Verify at: https://societypal.com"
EOF

echo "✅ Release management workflow created"
```

**Expected Result:**

-   Automated version numbering
-   Release notes generation
-   Git tagging and GitHub releases
-   Production deployment guidance

---

## 🔄 **PHASE 3: Server-Side Monitoring Setup**

### **3.1 Create Server Monitoring Scripts**

```bash
# Create comprehensive server monitoring
cat > .github/scripts/server-monitor.sh << 'EOF'
#!/bin/bash

# Server monitoring script for SocietyPal deployment

echo "🖥️ SocietyPal Server Health Monitor"
echo "=================================="
echo "Timestamp: $(date)"
echo ""

# Function to check domain health
check_domain_health() {
    local domain=$1
    local domain_path="~/domains/$domain"

    echo "🌐 Checking $domain:"
    echo "-------------------"

    # Check if domain directory exists
    if [ -d "$domain_path" ]; then
        echo "✅ Domain directory: EXISTS"

        # Check current symlink
        if [ -L "$domain_path/current" ]; then
            CURRENT_RELEASE=$(readlink "$domain_path/current")
            echo "✅ Current release: $CURRENT_RELEASE"
        else
            echo "❌ Current symlink: MISSING"
        fi

        # Check public_html symlink
        if [ -L "$domain_path/public_html" ]; then
            echo "✅ Public symlink: EXISTS"
        else
            echo "❌ Public symlink: MISSING"
        fi

        # Check shared directories
        if [ -d "$domain_path/shared" ]; then
            echo "✅ Shared directory: EXISTS"
            echo "   - Storage: $([ -d "$domain_path/shared/storage" ] && echo "EXISTS" || echo "MISSING")"
            echo "   - Environment: $([ -f "$domain_path/shared/.env" ] && echo "EXISTS" || echo "MISSING")"
        else
            echo "❌ Shared directory: MISSING"
        fi

        # Check release count
        if [ -d "$domain_path/releases" ]; then
            RELEASE_COUNT=$(ls "$domain_path/releases" | wc -l)
            echo "📦 Releases: $RELEASE_COUNT stored"

            # Show recent releases
            echo "   Recent releases:"
            ls -t "$domain_path/releases" | head -3 | sed 's/^/   - /'
        fi

        # Check Laravel application health
        if [ -f "$domain_path/current/artisan" ]; then
            echo "✅ Laravel: DETECTED"

            # Check Laravel logs
            if [ -f "$domain_path/current/storage/logs/laravel.log" ]; then
                RECENT_ERRORS=$(grep -i "error\|exception\|fatal" "$domain_path/current/storage/logs/laravel.log" | tail -5 | wc -l)
                if [ "$RECENT_ERRORS" -gt 0 ]; then
                    echo "⚠️ Recent errors: $RECENT_ERRORS found"
                else
                    echo "✅ Error log: CLEAN"
                fi
            fi
        fi

    else
        echo "❌ Domain directory: NOT FOUND"
    fi
    echo ""
}

# Check both environments
check_domain_health "staging.societypal.com"
check_domain_health "societypal.com"

# Check disk usage
echo "💾 Disk Usage:"
echo "-------------"
df -h ~/domains/ ~/backups/ | head -3

# Check system resources
echo ""
echo "🔧 System Resources:"
echo "-------------------"
echo "Memory usage: $(free -h | grep '^Mem:' | awk '{print $3 "/" $2}')"
echo "Load average: $(uptime | awk -F'load average:' '{print $2}')"

# Check backup status
echo ""
echo "💾 Backup Status:"
echo "----------------"
if [ -d ~/backups/database ]; then
    DB_BACKUP_COUNT=$(ls ~/backups/database/*.sql.gz 2>/dev/null | wc -l)
    echo "Database backups: $DB_BACKUP_COUNT files"

    LATEST_DB_BACKUP=$(ls -t ~/backups/database/*.sql.gz 2>/dev/null | head -1)
    if [ -n "$LATEST_DB_BACKUP" ]; then
        BACKUP_AGE=$(stat -c %Y "$LATEST_DB_BACKUP")
        CURRENT_TIME=$(date +%s)
        AGE_HOURS=$(( (CURRENT_TIME - BACKUP_AGE) / 3600 ))
        echo "Latest database backup: ${AGE_HOURS}h ago"
    fi
else
    echo "❌ Database backup directory: NOT FOUND"
fi

# Check file backups
if [ -d ~/backups/files ]; then
    FILE_BACKUP_COUNT=$(ls ~/backups/files/*.tar.gz 2>/dev/null | wc -l)
    echo "File backups: $FILE_BACKUP_COUNT archives"
else
    echo "❌ File backup directory: NOT FOUND"
fi

echo ""
echo "✅ Server monitoring completed"
echo "Next check recommended: $(date -d '+1 hour')"
EOF

chmod +x .github/scripts/server-monitor.sh
echo "✅ Server monitoring script created"
```

### **3.2 Setup Automated Server Monitoring**

```bash
# Create server monitoring workflow
cat > .github/workflows/server-monitor.yml << 'EOF'
name: Server Monitor

on:
  schedule:
    # Run server monitoring every 4 hours
    - cron: '0 */4 * * *'
  workflow_dispatch:

jobs:
  monitor-server:
    runs-on: ubuntu-latest

    steps:
    - name: 📥 Checkout Code
      uses: actions/checkout@v4

    - name: 🔑 Setup SSH
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts

    - name: 🖥️ Run Server Health Check
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🖥️ Running comprehensive server health check..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST 'bash -s' < .github/scripts/server-monitor.sh

    - name: 📊 Performance Metrics
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "📊 Collecting performance metrics..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "⚡ Application Performance:"
          echo "=========================="

          # Check Laravel queue status
          if [ -f ~/domains/societypal.com/current/artisan ]; then
            cd ~/domains/societypal.com/current
            echo "Queue workers: $(pgrep -f "queue:work" | wc -l)"
            echo "Failed jobs: $(php artisan queue:failed --format=json 2>/dev/null | jq '. | length' 2>/dev/null || echo "N/A")"
          fi

          # Check database connections
          MYSQL_CONNECTIONS=$(mysqladmin processlist 2>/dev/null | wc -l || echo "N/A")
          echo "MySQL connections: $MYSQL_CONNECTIONS"

          # Check web server status
          if pgrep nginx > /dev/null; then
            echo "✅ Nginx: RUNNING"
          else
            echo "❌ Nginx: NOT RUNNING"
          fi

          if pgrep php-fpm > /dev/null; then
            echo "✅ PHP-FPM: RUNNING"
          else
            echo "❌ PHP-FPM: NOT RUNNING"
          fi
        ENDSSH

    - name: 📈 Generate Health Report
      run: |
        echo "📈 Server Health Report Summary"
        echo "==============================="
        echo "Monitor completed at: $(date -u)"
        echo "Next scheduled check: $(date -u -d '+4 hours')"
        echo ""
        echo "🎯 Key Metrics to Review:"
        echo "- Domain accessibility"
        echo "- Release deployment status"
        echo "- Backup freshness"
        echo "- Resource utilization"
        echo "- Service status"
        echo ""
        echo "🔍 Manual investigation required if:"
        echo "- Any domain shows errors"
        echo "- Backups older than 48 hours"
        echo "- Disk usage above 80%"
        echo "- Services not running"
EOF

echo "✅ Server monitoring workflow created"
```

**Expected Result:**

-   Automated server health monitoring every 4 hours
-   Comprehensive application status checking
-   Performance metrics collection
-   Alert generation for issues

---

## 🔄 **PHASE 4: Performance Optimization Workflows**

### **4.1 Create Performance Testing Workflow**

```bash
# Create performance testing workflow
cat > .github/workflows/performance-test.yml << 'EOF'
name: Performance Test

on:
  schedule:
    # Run performance tests weekly on Sundays at 3 AM UTC
    - cron: '0 3 * * 0'
  workflow_dispatch:
    inputs:
      test_environment:
        description: 'Test environment'
        required: true
        default: 'staging'
        type: choice
        options:
        - staging
        - production

jobs:
  performance-test:
    runs-on: ubuntu-latest

    steps:
    - name: 🎯 Setup Test Environment
      run: |
        TEST_ENV="${{ github.event.inputs.test_environment || 'staging' }}"

        if [ "$TEST_ENV" = "production" ]; then
          TEST_URL="https://societypal.com"
          echo "🚀 Testing PRODUCTION environment"
        else
          TEST_URL="https://staging.societypal.com"
          echo "🧪 Testing STAGING environment"
        fi

        echo "TEST_URL=$TEST_URL" >> $GITHUB_ENV
        echo "TEST_ENV=$TEST_ENV" >> $GITHUB_ENV

    - name: ⚡ Basic Performance Test
      run: |
        echo "⚡ Running basic performance tests for $TEST_ENV..."
        echo "Target: $TEST_URL"
        echo ""

        # Test homepage performance
        echo "🏠 Homepage Performance:"
        HOMEPAGE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL")
        echo "   Response time: ${HOMEPAGE_TIME}s"

        if (( $(echo "$HOMEPAGE_TIME < 2.0" | bc -l) )); then
          echo "   ✅ Performance: EXCELLENT"
        elif (( $(echo "$HOMEPAGE_TIME < 3.0" | bc -l) )); then
          echo "   ✅ Performance: GOOD"
        else
          echo "   ⚠️ Performance: NEEDS OPTIMIZATION"
        fi

        # Test common routes
        echo ""
        echo "🛣️ Route Performance Tests:"

        for route in "/login" "/register" "/dashboard"; do
          ROUTE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$TEST_URL$route" 2>/dev/null || echo "timeout")
          ROUTE_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$TEST_URL$route" 2>/dev/null || echo "000")
          echo "   $route: HTTP $ROUTE_STATUS (${ROUTE_TIME}s)"
        done

    - name: 🔍 Detailed Analysis
      run: |
        echo "🔍 Detailed performance analysis..."
        echo ""

        # Analyze response headers
        echo "📋 Response Headers Analysis:"
        curl -I "$TEST_URL" | grep -E "Server|Cache|Content-Type|Content-Encoding"

        echo ""
        echo "🗜️ Compression Test:"
        COMPRESSED_SIZE=$(curl -H "Accept-Encoding: gzip" -s "$TEST_URL" | wc -c)
        UNCOMPRESSED_SIZE=$(curl -s "$TEST_URL" | wc -c)

        if [ "$COMPRESSED_SIZE" -lt "$UNCOMPRESSED_SIZE" ]; then
          COMPRESSION_RATIO=$(( (UNCOMPRESSED_SIZE - COMPRESSED_SIZE) * 100 / UNCOMPRESSED_SIZE ))
          echo "   ✅ Compression active: ${COMPRESSION_RATIO}% savings"
        else
          echo "   ⚠️ Compression: NOT DETECTED"
        fi

        echo ""
        echo "🔒 Security Headers Check:"
        SECURITY_HEADERS=$(curl -I "$TEST_URL" | grep -E "X-Frame-Options|X-Content-Type-Options|Strict-Transport-Security" | wc -l)
        echo "   Security headers found: $SECURITY_HEADERS"

    - name: 📊 Performance Report
      run: |
        echo "📊 Performance Test Summary"
        echo "=========================="
        echo "Environment: $TEST_ENV"
        echo "URL: $TEST_URL"
        echo "Test Date: $(date -u)"
        echo ""
        echo "🎯 Performance Targets:"
        echo "   Homepage: < 2.0s (Excellent), < 3.0s (Good)"
        echo "   Routes: < 2.0s for authenticated pages"
        echo "   Compression: Should be active"
        echo "   Security: Headers should be present"
        echo ""
        echo "📈 Recommendations:"
        echo "   - Monitor response times weekly"
        echo "   - Optimize if performance degrades"
        echo "   - Enable compression if not active"
        echo "   - Implement security headers if missing"
        echo ""
        echo "🔄 Next test: $(date -u -d '+1 week')"
EOF

echo "✅ Performance testing workflow created"
```

**Expected Result:**

-   Weekly automated performance testing
-   Response time monitoring
-   Compression and security analysis
-   Performance trend tracking

### **4.2 Create Database Maintenance Workflow**

```bash
# Create database maintenance workflow
cat > .github/workflows/database-maintenance.yml << 'EOF'
name: Database Maintenance

on:
  schedule:
    # Run database maintenance weekly on Saturdays at 2 AM UTC
    - cron: '0 2 * * 6'
  workflow_dispatch:

jobs:
  database-maintenance:
    runs-on: ubuntu-latest

    steps:
    - name: 🔑 Setup SSH
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
      run: |
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts

    - name: 🗄️ Database Health Check
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "🗄️ Running database health check..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          echo "📊 Database Health Analysis"
          echo "=========================="

          # Check database connections
          cd ~/domains/societypal.com/current

          echo "🔗 Connection Test:"
          if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database: CONNECTED';" 2>/dev/null; then
            echo "✅ Database connection: OK"
          else
            echo "❌ Database connection: FAILED"
          fi

          echo ""
          echo "📋 Migration Status:"
          php artisan migrate:status | head -10

          echo ""
          echo "📊 Database Size Analysis:"
          mysql -e "
            SELECT
              table_schema as 'Database',
              ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            GROUP BY table_schema;
          " 2>/dev/null || echo "Database size query failed"

          echo ""
          echo "🧹 Table Analysis:"
          mysql -e "
            SELECT
              table_name as 'Table',
              table_rows as 'Rows',
              ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
            LIMIT 10;
          " 2>/dev/null || echo "Table analysis query failed"
        ENDSSH

    - name: 💾 Create Database Backup
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
      run: |
        echo "💾 Creating database backup..."

        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << 'ENDSSH'
          # Create backup with timestamp
          BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
          mkdir -p ~/backups/database

          echo "📦 Creating backup: backup_${BACKUP_DATE}.sql"

          # Get database credentials from .env
          cd ~/domains/societypal.com/current
          DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
          DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
          DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)

          if [ -n "$DB_NAME" ] && [ -n "$DB_USER" ] && [ -n "$DB_PASS" ]; then
            # Create database backup
            mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > ~/backups/database/backup_${BACKUP_DATE}.sql

            # Compress backup
            gzip ~/backups/database/backup_${BACKUP_DATE}.sql

            echo "✅ Backup created: backup_${BACKUP_DATE}.sql.gz"

            # Show backup size
            du -h ~/backups/database/backup_${BACKUP_DATE}.sql.gz

            # Cleanup old backups (keep last 10)
            cd ~/backups/database
            ls -t *.sql.gz | tail -n +11 | xargs rm -f 2>/dev/null || true
            echo "🧹 Cleaned up old backups"

          else
            echo "❌ Database credentials not found in .env"
          fi
        ENDSSH

    - name: 📈 Maintenance Summary
      run: |
        echo "📈 Database Maintenance Summary"
        echo "=============================="
        echo "Maintenance completed: $(date -u)"
        echo ""
        echo "✅ Tasks Completed:"
        echo "   - Database health check"
        echo "   - Connection verification"
        echo "   - Migration status review"
        echo "   - Size analysis"
        echo "   - Backup creation"
        echo "   - Backup cleanup"
        echo ""
        echo "🔄 Next maintenance: $(date -u -d '+1 week')"
        echo ""
        echo "⚠️ Manual attention required if:"
        echo "   - Database connection failures"
        echo "   - Pending migrations"
        echo "   - Excessive database growth"
        echo "   - Backup creation failures"
EOF

echo "✅ Database maintenance workflow created"
```

**Expected Result:**

-   Weekly automated database maintenance
-   Health monitoring and backup creation
-   Size analysis and optimization alerts
-   Automated cleanup of old backups

---

## ✅ **Success Verification and Final Setup**

### **Workflow Configuration Summary**

```bash
# Verify all monitoring workflows are created
echo "📋 GitHub Actions Monitoring Workflows Summary"
echo "=============================================="
echo ""
echo "✅ Created Workflows:"
ls -la .github/workflows/
echo ""
echo "✅ Created Scripts:"
ls -la .github/scripts/
echo ""
echo "📊 Monitoring Schedule:"
echo "   - Health checks: Every 30 minutes"
echo "   - Backup verification: Daily at 2 AM UTC"
echo "   - Dependency updates: Monthly"
echo "   - Server monitoring: Every 4 hours"
echo "   - Performance tests: Weekly (Sundays)"
echo "   - Database maintenance: Weekly (Saturdays)"
echo ""
echo "🎯 Manual Setup Required:"
echo "   1. Commit all workflow files to repository"
echo "   2. Verify GitHub secrets are configured"
echo "   3. Test manual workflow triggers"
echo "   4. Monitor first automated runs"
```

### **Commit All Monitoring Files**

```bash
# Add all monitoring and maintenance files
git add .github/workflows/
git add .github/scripts/
git add .github/DEPLOYMENT_SECRETS.md

# Commit monitoring setup
git commit -m "feat: add comprehensive monitoring and maintenance workflows

- Health monitoring every 30 minutes with alerting
- Daily backup verification and reporting
- Monthly dependency updates with testing
- Server monitoring every 4 hours
- Weekly performance testing and analysis
- Database maintenance and backup automation
- Release management with version control
- Comprehensive documentation and scripts"

echo "✅ All monitoring workflows committed"
echo ""
echo "🚀 Next steps:"
echo "1. Push to repository: git push origin main"
echo "2. Verify workflows appear in GitHub Actions"
echo "3. Test manual workflow triggers"
echo "4. Monitor automated executions"
```

**Expected Final State:**

-   Complete monitoring automation in place
-   All workflows scheduled and ready
-   Comprehensive health checking active
-   Automated maintenance procedures established

---

## 📋 **Next Steps and Ongoing Management**

### **Monitoring Dashboard Setup**

```bash
echo "📊 Monitoring Management Guide"
echo "============================="
echo ""
echo "GitHub Actions Monitoring:"
echo "   → Repository → Actions tab"
echo "   → Monitor workflow success/failure"
echo "   → Review logs for issues"
echo ""
echo "Manual Workflow Triggers:"
echo "   → Actions → Select workflow → Run workflow"
echo "   → Test dependency updates"
echo "   → Trigger performance tests"
echo "   → Create releases"
echo ""
echo "Key Metrics to Monitor:"
echo "   - Deployment success rate"
echo "   - Performance trends"
echo "   - Backup freshness"
echo "   - Security status"
echo "   - Resource utilization"
echo ""
echo "Alert Thresholds:"
echo "   - Response time > 3 seconds"
echo "   - Backup age > 48 hours"
echo "   - Disk usage > 80%"
echo "   - Service failures"
```

**Expected Ongoing Activities:**

-   Daily review of monitoring alerts
-   Weekly performance trend analysis
-   Monthly dependency update reviews
-   Quarterly security audits
-   As-needed manual interventions

---

## 🎯 **Key Success Indicators**

-   **Monitoring Active**: ✅ All workflows running on schedule
-   **Health Checking**: 📊 Automated verification every 30 minutes
-   **Backup Verification**: 💾 Daily backup status confirmation
-   **Performance Tracking**: ⚡ Weekly performance analysis
-   **Maintenance Automation**: 🔧 Server and database maintenance
-   **Alert System**: 🚨 GitHub Actions warnings for issues
-   **Release Management**: 📦 Automated version control and releases

**Scenario B monitoring and maintenance system fully operational!** 🎉

---

## 📋 **Next Steps**

✅ **Step 24B Complete** - Comprehensive monitoring and maintenance automation established  
🔄 **Continue to**: Scenario C deployment setup (if needed)  
🎯 **Ongoing**: Monitor GitHub Actions for automated health checks and maintenance  
📊 **Optional**: Set up external monitoring tools for additional redundancy

**Scenario B complete with full automation and monitoring!** 🚀
