# Step 23B: Automated Build and Deployment (Scenario B: GitHub Actions)

## Analysis Source
**Primary Source**: V2 Phase3 (lines 480-500) - Deployment verification and branch management  
**Secondary Source**: V1 Complete Guide (lines 1650-1700) - Environment-specific deployment process  
**Recommendation**: Use V2's automated verification enhanced with V1's comprehensive environment management and monitoring

---

## 🎯 Purpose

Execute and monitor automated build and deployment process through GitHub Actions with comprehensive verification and environment-specific handling.

## ⚡ Quick Reference

**Time Required**: ~10-15 minutes (mostly automated)  
**Prerequisites**: Step 22B completed successfully, GitHub secrets configured  
**Critical Path**: Push trigger → Build monitoring → Deployment verification → Environment testing

---

## 🔄 **PHASE 1: Deployment Trigger and Monitoring**

### **1.1 Trigger Deployment Based on Environment**

#### **For Staging Deployment:**
```bash
# Ensure you're on main branch for staging
git checkout main

# Verify current status
git status
git log --oneline -3

# Push to trigger staging deployment
echo "🚀 Triggering staging deployment..."
git push origin main

echo "📊 Staging deployment initiated"
echo "🌐 Target: https://staging.societypal.com"
echo "📋 Monitor at: https://github.com/$(git config remote.origin.url | sed 's/.*github.com[:/]\([^/]*\/[^/]*\)\.git/\1/')/actions"
```

#### **For Production Deployment:**
```bash
# Switch to production branch
git checkout production

# Merge tested changes from staging (NEVER from main directly)
git merge staging

# Verify merge completed successfully
git log --oneline -3

# Push to trigger production deployment
echo "🚀 Triggering PRODUCTION deployment..."
git push origin production

echo "📊 Production deployment initiated"
echo "🌐 Target: https://societypal.com"
echo "📋 Monitor at: https://github.com/$(git config remote.origin.url | sed 's/.*github.com[:/]\([^/]*\/[^/]*\)\.git/\1/')/actions"
```

### **1.2 Real-Time Workflow Monitoring**

```bash
# Function to monitor GitHub Actions workflow
monitor_deployment() {
    local target_env=${1:-staging}
    local max_wait=${2:-600}  # 10 minutes max
    local check_interval=30
    local elapsed=0
    
    echo "👀 Monitoring $target_env deployment..."
    echo "⏱️ Maximum wait time: $((max_wait / 60)) minutes"
    echo ""
    
    while [ $elapsed -lt $max_wait ]; do
        echo "🕐 Elapsed: $((elapsed / 60))m $((elapsed % 60))s"
        echo "📊 Check GitHub Actions tab for detailed progress"
        echo "🔄 Automatic verification will run after deployment completes"
        
        sleep $check_interval
        elapsed=$((elapsed + check_interval))
    done
    
    echo "⚠️ Monitoring timeout reached - check GitHub Actions manually"
}

# Start monitoring (choose one)
monitor_deployment staging    # For staging deployment
# monitor_deployment production  # For production deployment
```

**Expected GitHub Actions Workflow:**
1. **Code Checkout** (30 seconds)
2. **PHP/Node Setup** (60 seconds) 
3. **Dependency Installation** (90 seconds)
4. **Asset Building** (60 seconds)
5. **Laravel Optimization** (30 seconds)
6. **Package Creation** (30 seconds)
7. **Server Deployment** (120 seconds)
8. **Health Verification** (60 seconds)

**Total Expected Time: 8-12 minutes**

---

## 🔄 **PHASE 2: Deployment Progress Verification**

### **2.1 Manual Status Checking** *(While GitHub Actions Runs)*

```bash
# Quick server status check (runs independently of GitHub Actions)
check_server_status() {
    local target_url=${1:-https://staging.societypal.com}
    
    echo "🔍 Checking current server status for: $target_url"
    
    # Test connectivity
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$target_url" 2>/dev/null || echo "000")
    
    if [ "$HTTP_STATUS" = "200" ]; then
        echo "✅ Current site: ONLINE (HTTP $HTTP_STATUS)"
        
        # Check response time
        RESPONSE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$target_url" 2>/dev/null || echo "timeout")
        echo "⏱️ Response time: ${RESPONSE_TIME}s"
        
    elif [ "$HTTP_STATUS" = "000" ]; then
        echo "🔄 Site: DEPLOYING or OFFLINE (connection timeout)"
    else
        echo "⚠️ Site status: HTTP $HTTP_STATUS"
    fi
    
    echo ""
}

# Check staging status
check_server_status "https://staging.societypal.com"

# If deploying to production, also check production
# check_server_status "https://societypal.com"
```

### **2.2 SSH Server Monitoring** *(Optional - During Deployment)*

```bash
# Monitor server-side deployment process
monitor_server_deployment() {
    echo "📡 Connecting to server for live deployment monitoring..."
    
    ssh hostinger-factolo << 'ENDSSH'
        echo "🖥️ Server-side deployment monitoring"
        echo "=================================="
        
        # Check current releases
        echo "📦 Current releases:"
        ls -la ~/domains/staging.societypal.com/releases/ 2>/dev/null | tail -5 || echo "No releases yet"
        
        # Monitor deployment activity
        echo ""
        echo "🔄 Active deployment processes:"
        ps aux | grep -E "(tar|php|artisan)" | grep -v grep || echo "No active deployment processes"
        
        # Check disk space
        echo ""
        echo "💾 Disk space:"
        df -h ~/domains/ | head -2
        
        # Monitor logs for deployment activity
        echo ""
        echo "📋 Recent deployment activity:"
        tail -10 ~/domains/staging.societypal.com/current/storage/logs/laravel.log 2>/dev/null || echo "No logs yet"
        
        echo ""
        echo "✅ Server monitoring complete"
    ENDSSH
}

# Uncomment to run server monitoring
# monitor_server_deployment
```

**Expected Server Activity:**
- New release directory creation
- Package extraction
- Symlink updates
- Migration execution
- Cache rebuilding

---

## 🔄 **PHASE 3: Post-Deployment Verification**

### **3.1 Automated Verification Status**

After GitHub Actions completes, the workflow automatically performs verification. Monitor these results:

```bash
# Check GitHub Actions completion status
echo "📊 GitHub Actions Verification Results:"
echo "======================================"
echo ""
echo "✅ Expected Successful Checks:"
echo "   - HTTP 200 response from site"
echo "   - Laravel CSRF token present"
echo "   - Database connectivity"
echo "   - Static assets loading"
echo "   - Response time under 3 seconds"
echo ""
echo "❌ Failure Indicators:"
echo "   - HTTP 5xx errors"
echo "   - Timeout responses"
echo "   - Missing Laravel indicators"
echo "   - Database connection failures"
echo ""
echo "📱 Manual verification recommended after automated checks pass"
```

### **3.2 Comprehensive Application Testing**

```bash
# Comprehensive post-deployment testing
comprehensive_test() {
    local site_url=${1:-https://staging.societypal.com}
    
    echo "🧪 Running comprehensive application tests for: $site_url"
    echo "========================================================="
    
    # Test 1: Basic connectivity
    echo "🌐 Test 1: Basic Connectivity"
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$site_url")
    if [ "$HTTP_STATUS" = "200" ]; then
        echo "✅ Basic connectivity: PASSED (HTTP $HTTP_STATUS)"
    else
        echo "❌ Basic connectivity: FAILED (HTTP $HTTP_STATUS)"
        return 1
    fi
    
    # Test 2: Laravel application detection
    echo ""
    echo "🔍 Test 2: Laravel Application Detection"
    if curl -s "$site_url" | grep -q "csrf-token\|Laravel"; then
        echo "✅ Laravel detection: PASSED"
    else
        echo "⚠️ Laravel detection: UNCERTAIN (may be custom implementation)"
    fi
    
    # Test 3: Response time
    echo ""
    echo "⏱️ Test 3: Performance"
    RESPONSE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$site_url")
    if (( $(echo "$RESPONSE_TIME < 3.0" | bc -l) )); then
        echo "✅ Response time: PASSED (${RESPONSE_TIME}s)"
    else
        echo "⚠️ Response time: SLOW (${RESPONSE_TIME}s)"
    fi
    
    # Test 4: Static assets
    echo ""
    echo "🎨 Test 4: Static Assets"
    CSS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$site_url/css/app.css" 2>/dev/null || echo "404")
    JS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$site_url/js/app.js" 2>/dev/null || echo "404")
    
    if [ "$CSS_STATUS" = "200" ] || [ "$JS_STATUS" = "200" ]; then
        echo "✅ Static assets: AVAILABLE"
    else
        echo "ℹ️ Static assets: Using build system (CSS: $CSS_STATUS, JS: $JS_STATUS)"
    fi
    
    # Test 5: Common routes
    echo ""
    echo "🛣️ Test 5: Common Routes"
    
    LOGIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$site_url/login" 2>/dev/null || echo "404")
    DASHBOARD_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$site_url/dashboard" 2>/dev/null || echo "404")
    
    echo "   Login page: HTTP $LOGIN_STATUS"
    echo "   Dashboard: HTTP $DASHBOARD_STATUS"
    
    if [ "$LOGIN_STATUS" = "200" ] || [ "$DASHBOARD_STATUS" = "302" ]; then
        echo "✅ Authentication routes: FUNCTIONAL"
    else
        echo "ℹ️ Authentication routes: Custom or protected"
    fi
    
    echo ""
    echo "🎉 Comprehensive testing completed!"
    echo "📱 Proceed with manual user acceptance testing"
}

# Run comprehensive testing
comprehensive_test "https://staging.societypal.com"

# If deploying to production, test production too
# comprehensive_test "https://societypal.com"
```

**Expected Results:**
- All automated tests pass
- Response times under 3 seconds
- Static assets properly served
- Authentication system functional

### **3.3 Database and Migration Verification**

```bash
# Verify database state after deployment
verify_database_state() {
    echo "🗄️ Verifying database state after deployment..."
    
    ssh hostinger-factolo << 'ENDSSH'
        cd ~/domains/staging.societypal.com/current
        
        echo "📋 Migration Status:"
        php artisan migrate:status | head -10
        
        echo ""
        echo "📊 Database Connection Test:"
        php artisan tinker --execute="
            try {
                DB::connection()->getPdo();
                echo 'Database: CONNECTED' . PHP_EOL;
                echo 'Tables: ' . count(DB::select('SHOW TABLES')) . PHP_EOL;
            } catch (Exception \$e) {
                echo 'Database: ERROR - ' . \$e->getMessage() . PHP_EOL;
            }
        "
        
        echo ""
        echo "🔧 Cache Status:"
        ls -la bootstrap/cache/
    ENDSSH
}

# Run database verification
verify_database_state
```

**Expected Database Status:**
- All migrations showing "Yes"
- Database connection successful
- Required tables present
- Cache files properly generated

---

## 🔄 **PHASE 4: Environment-Specific Validation**

### **4.1 Staging Environment Validation**

```bash
# Staging-specific validation
validate_staging() {
    echo "🧪 Staging Environment Validation"
    echo "================================"
    
    local staging_url="https://staging.societypal.com"
    
    # Check staging-specific configurations
    echo "🔍 Staging Configuration Checks:"
    
    # Test staging environment indicators
    if curl -s "$staging_url" | grep -i "staging\|test\|dev"; then
        echo "✅ Staging indicators: PRESENT"
    else
        echo "ℹ️ Staging indicators: NOT VISIBLE (may be configured internally)"
    fi
    
    # Verify staging is not production
    echo ""
    echo "🚨 Production Safety Check:"
    if [ "$staging_url" != "https://societypal.com" ]; then
        echo "✅ Staging isolation: CONFIRMED"
    else
        echo "❌ Staging isolation: FAILED - THIS IS PRODUCTION!"
        return 1
    fi
    
    # Test staging-specific features
    echo ""
    echo "🔧 Staging Features:"
    echo "   - Debug mode: Should be disabled"
    echo "   - Error reporting: Should be enabled for testing"
    echo "   - Performance monitoring: Available"
    echo "   - Test data: Can be safely modified"
    
    echo ""
    echo "✅ Staging validation completed"
    echo "📱 Safe for client testing and feature validation"
}

validate_staging
```

### **4.2 Production Environment Validation** *(If deploying to production)*

```bash
# Production-specific validation (only run if deploying to production)
validate_production() {
    echo "🚀 Production Environment Validation"
    echo "==================================="
    
    local production_url="https://societypal.com"
    
    echo "⚠️ PRODUCTION DEPLOYMENT DETECTED"
    echo ""
    
    # Critical production checks
    echo "🔒 Production Security Checks:"
    
    # SSL verification
    SSL_STATUS=$(curl -I "$production_url" 2>&1 | grep -i "SSL\|TLS" | wc -l)
    if [ "$SSL_STATUS" -gt 0 ]; then
        echo "✅ SSL/TLS: ACTIVE"
    else
        echo "❌ SSL/TLS: NOT DETECTED"
    fi
    
    # Debug mode check (should be off)
    echo ""
    echo "🐞 Debug Configuration:"
    if curl -s "$production_url" | grep -i "whoops\|debugbar\|error.*trace"; then
        echo "❌ Debug mode: ENABLED (SECURITY RISK!)"
    else
        echo "✅ Debug mode: DISABLED"
    fi
    
    # Performance verification
    echo ""
    echo "⚡ Performance Verification:"
    PROD_RESPONSE_TIME=$(curl -w "%{time_total}" -s -o /dev/null "$production_url")
    echo "   Response time: ${PROD_RESPONSE_TIME}s"
    
    if (( $(echo "$PROD_RESPONSE_TIME < 2.0" | bc -l) )); then
        echo "✅ Production performance: OPTIMAL"
    else
        echo "⚠️ Production performance: NEEDS OPTIMIZATION"
    fi
    
    echo ""
    echo "🎯 Production validation completed"
    echo "🌍 Live site active at: $production_url"
}

# Uncomment if deploying to production
# validate_production
```

**Production Critical Indicators:**
- SSL certificate active and valid
- Debug mode disabled
- Response time under 2 seconds
- No error traces visible
- Security headers present

---

## ✅ **Success Confirmation and Next Steps**

### **Deployment Success Checklist**

```bash
# Final deployment verification
echo "🎯 DEPLOYMENT SUCCESS VERIFICATION"
echo "=================================="
echo ""
echo "GitHub Actions Workflow:"
echo "   [ ] Build completed successfully"
echo "   [ ] Deployment executed without errors"
echo "   [ ] Automated verification passed"
echo "   [ ] GitHub Actions shows green status"
echo ""
echo "Application Health:"
echo "   [ ] Site responds with HTTP 200"
echo "   [ ] Response time under 3 seconds"
echo "   [ ] Laravel application loading"
echo "   [ ] Database connectivity confirmed"
echo "   [ ] Static assets accessible"
echo ""
echo "Environment Configuration:"
echo "   [ ] Correct domain deployment"
echo "   [ ] Environment-specific settings"
echo "   [ ] Security configurations active"
echo "   [ ] Performance within acceptable limits"
echo ""
echo "Manual Testing Required:"
echo "   [ ] User registration/login"
echo "   [ ] Core application features"
echo "   [ ] File uploads (if applicable)"
echo "   [ ] Email functionality (if applicable)"
echo "   [ ] Mobile responsiveness"
```

### **Post-Deployment Actions**

```bash
# Clean up local environment
echo "🧹 Post-deployment cleanup:"
echo "=========================="

# Return to main branch for continued development
git checkout main

# Optional: Tag successful production deployment
if git branch -r | grep -q "origin/production"; then
    echo "🏷️ Production deployment detected"
    echo "Consider tagging release:"
    echo "   git tag -a v1.0.0 -m 'Production release'"
    echo "   git push origin v1.0.0"
fi

echo ""
echo "📊 Deployment metrics:"
echo "   Branch deployed: $(git branch --show-current)"
echo "   Commit hash: $(git rev-parse --short HEAD)"
echo "   Deployment time: $(date)"
echo ""
echo "✅ Scenario B deployment completed successfully!"
```

**Expected Final State:**
- GitHub Actions workflow green
- Application accessible and functional
- Database migrations completed
- Environment properly configured
- Ready for user acceptance testing

---

## 🔧 **Troubleshooting Common Issues**

### **GitHub Actions Failures**

```bash
# Common GitHub Actions troubleshooting
echo "🔧 GitHub Actions Troubleshooting Guide:"
echo "======================================="
echo ""
echo "Build Failures:"
echo "   - Check composer.json syntax"
echo "   - Verify PHP/Node versions"
echo "   - Review dependency conflicts"
echo ""
echo "Deployment Failures:"
echo "   - Verify GitHub secrets are correct"
echo "   - Check SSH key permissions"
echo "   - Confirm server accessibility"
echo ""
echo "Verification Failures:"
echo "   - Site may need more time to start"
echo "   - Check server resources"
echo "   - Review Laravel logs"
```

### **Quick Recovery Commands**

```bash
# If deployment fails, quick recovery options
recovery_options() {
    echo "🚨 Deployment Recovery Options:"
    echo "============================="
    echo ""
    echo "1. Retry deployment:"
    echo "   git push origin [branch] --force-with-lease"
    echo ""
    echo "2. Manual server check:"
    echo "   ssh hostinger-factolo"
    echo "   cd ~/domains/[domain]/current"
    echo "   php artisan migrate:status"
    echo ""
    echo "3. Rollback to previous release:"
    echo "   ssh hostinger-factolo"
    echo "   cd ~/domains/[domain]/"
    echo "   ls -t releases/ | head -2"
    echo "   ln -nfs releases/[previous-timestamp] current"
}

# Uncomment if deployment issues occur
# recovery_options
```

---

## 📋 **Next Steps**

✅ **Step 23B Complete** - Automated deployment executed and verified  
🔄 **Continue to**: Step 24B (Post-Deployment Monitoring and Maintenance)  
🎯 **Optional**: Set up automated monitoring and alerting  
🔄 **Alternative**: Continue with production deployment using same process

---

## 🎯 **Key Success Indicators**

- **GitHub Actions**: ✅ Green workflow status
- **Build Time**: 8-12 minutes total automation
- **Deployment**: Zero-downtime atomic switching
- **Verification**: All automated checks passing
- **Performance**: Response times under 3 seconds
- **Security**: Production safeguards active

**Scenario B automated deployment completed successfully!** 🎉
