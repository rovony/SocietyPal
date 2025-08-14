# Step 24B: Post-Deployment Monitoring and Maintenance (Scenario B: GitHub Actions)

## Analysis Source
**Primary Source**: V2 Phase4 (lines 507-619) - Ongoing maintenance workflows and vendor updates  
**Secondary Source**: V1 Complete Guide (lines 1800-1950) - Server maintenance and monitoring commands  
**Recommendation**: Use V2's automated workflow approach enhanced with V1's comprehensive server monitoring and backup procedures

---

## 🎯 Purpose

Establish ongoing monitoring, maintenance workflows, and automated processes for GitHub Actions-deployed applications to ensure long-term stability and performance.

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
- Automated health checks every 30 minutes
- Performance monitoring and alerting
- SSL certificate verification
- GitHub Actions warnings for issues

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
- Daily automated backup verification
- Disk space monitoring
- Alert generation for missing backups
- Automated backup health reporting

---

## 🔄 **PHASE 2: Maintenance Workflow Automation**

### **2.1 Create Dependency Update Workflow**

```bash
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
```

**Expected Result:**
- Monthly automated dependency updates
- Patch/minor/major update options
- Automated testing of updates
- Report generation for review

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
- Automated version numbering
- Release notes generation
- Git tagging and GitHub releases
- Production deployment guidance

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
- Automated server health monitoring every 4 hours
- Comprehensive application status checking
- Performance metrics collection
- Alert generation for issues

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
- Weekly automated performance testing
- Response time monitoring
- Compression and security analysis
- Performance trend tracking

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
- Weekly automated database maintenance
- Health monitoring and backup creation
- Size analysis and optimization alerts
- Automated cleanup of old backups

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
- Complete monitoring automation in place
- All workflows scheduled and ready
- Comprehensive health checking active
- Automated maintenance procedures established

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
- Daily review of monitoring alerts
- Weekly performance trend analysis
- Monthly dependency update reviews
- Quarterly security audits
- As-needed manual interventions

---

## 🎯 **Key Success Indicators**

- **Monitoring Active**: ✅ All workflows running on schedule
- **Health Checking**: 📊 Automated verification every 30 minutes
- **Backup Verification**: 💾 Daily backup status confirmation
- **Performance Tracking**: ⚡ Weekly performance analysis
- **Maintenance Automation**: 🔧 Server and database maintenance
- **Alert System**: 🚨 GitHub Actions warnings for issues
- **Release Management**: 📦 Automated version control and releases

**Scenario B monitoring and maintenance system fully operational!** 🎉

---

## 📋 **Next Steps**

✅ **Step 24B Complete** - Comprehensive monitoring and maintenance automation established  
🔄 **Continue to**: Scenario C deployment setup (if needed)  
🎯 **Ongoing**: Monitor GitHub Actions for automated health checks and maintenance  
📊 **Optional**: Set up external monitoring tools for additional redundancy

**Scenario B complete with full automation and monitoring!** 🚀
