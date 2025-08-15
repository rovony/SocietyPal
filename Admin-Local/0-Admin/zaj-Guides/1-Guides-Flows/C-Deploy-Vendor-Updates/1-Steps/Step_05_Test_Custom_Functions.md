# Step 05: Test Custom Functions & Integration

**Goal:** Thoroughly test the integration between updated vendor code and existing customizations to ensure compatibility and functionality.

**Time Required:** 45-90 minutes (varies by customization complexity)  
**Prerequisites:** Step 04 completed with vendor files updated

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and **V2 Missing Content Amendments**:

- V2 Amendment: Custom functionality testing strategy
- V1: Integration testing approaches
- V2: CodeCanyon-specific customization preservation

---

## **5.1: Initialize Testing Environment**

### **Setup Testing Context:**

1. **Determine testing approach based on customization mode:**

   ````bash
   2. **Navigate to project root:**

   ```bash
   # Set path variables for consistency
   export PROJECT_ROOT="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
   export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
   cd "$PROJECT_ROOT"

   # Get context from previous steps
   LATEST_STAGING=$(find Admin-Local/vendor_updates -name "202*" -type d | sort | tail -1)
   CUSTOMIZATION_MODE=$(grep "CUSTOMIZATION_MODE=" Admin-Local/update_logs/update_*.md | tail -1 | cut -d'"' -f2)
   DEPLOY_METHOD=$(grep "DEPLOY_METHOD=" Admin-Local/update_logs/update_*.md | tail -1 | cut -d'"' -f2)

   echo "🧪 Starting integration testing..."
   echo "   Customization Mode: $CUSTOMIZATION_MODE"
   echo "   Deployment Method: $DEPLOY_METHOD"
   echo "   Testing Level: $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "FULL INTEGRATION" || echo "STANDARD FUNCTIONAL")"
   ````

2. **Create testing workspace:**

   ```bash
   # Create testing directory
   TEST_DIR="$LATEST_STAGING/testing"
   mkdir -p "$TEST_DIR"/{reports,logs,results}

   # Initialize testing environment
   cp .env.local .env 2>/dev/null || cp .env.example .env

   echo "📋 Testing workspace: $TEST_DIR"
   ```

---

## **5.2: Pre-Testing System Validation**

### **Validate Basic System Functionality:**

1. **Check application bootstrap:**

   ```bash
   echo "🔍 Validating application bootstrap..."

   # Clear any cached configuration from old version
   echo "🧹 Clearing application caches..."
   php artisan cache:clear 2>/dev/null || echo "Cache clear skipped"
   php artisan config:clear 2>/dev/null || echo "Config clear skipped"
   php artisan route:clear 2>/dev/null || echo "Route clear skipped"
   php artisan view:clear 2>/dev/null || echo "View clear skipped"

   # Test basic artisan functionality
   echo "⚡ Testing Artisan commands..."
   if php artisan --version > "$TEST_DIR/logs/artisan_test.log" 2>&1; then
       echo "✅ Artisan working: $(php artisan --version)"
   else
       echo "❌ Artisan failed - check $TEST_DIR/logs/artisan_test.log"
       cat "$TEST_DIR/logs/artisan_test.log"
       exit 1
   fi
   ```

2. **Test database connectivity:**

   ```bash
   echo "🗄️ Testing database connectivity..."

   # Test database connection
   if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch(Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); }" > "$TEST_DIR/logs/db_test.log" 2>&1; then
       DB_STATUS=$(cat "$TEST_DIR/logs/db_test.log")
       if [[ "$DB_STATUS" == *"connected successfully"* ]]; then
           echo "✅ Database connectivity confirmed"
       else
           echo "❌ Database connection issue: $DB_STATUS"
       fi
   else
       echo "⚠️ Could not test database - will test during web server testing"
   fi
   ```

3. **Test application loading:**

   ```bash
   echo "🌐 Testing application loading..."

   # Start local development server for testing
   echo "🚀 Starting local development server..."
   php artisan serve --host=127.0.0.1 --port=8000 > "$TEST_DIR/logs/server.log" 2>&1 &
   SERVER_PID=$!

   # Wait a moment for server to start
   sleep 5

   # Test if server responds
   if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000 | grep -q "200\|302"; then
       echo "✅ Application server started successfully"
       SERVER_RUNNING=true
   else
       echo "❌ Application server failed to start"
       echo "Server logs:"
       tail -10 "$TEST_DIR/logs/server.log"
       SERVER_RUNNING=false
   fi
   ```

---

## **5.3: Customization-Specific Testing**

### **Test Based on Customization Mode:**

1. **Protected Mode Testing (if customizations exist):**

   ```bash
   if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       echo "🛡️ Testing protected customizations integration..."

       # Initialize testing report
       cat > "$TEST_DIR/reports/custom_integration_test.md" << TEST_HEADER
   # Custom Integration Test Report

   **Test Date:** $(date)
   **Mode:** Protected Customizations
   **Vendor Update:** $(grep "New Version:" "$LATEST_STAGING/UPDATE_STRATEGY.md" | cut -d' ' -f3)

   ## Custom Components Testing

   TEST_HEADER

       # Test custom controllers
       if [ -d "app/Custom/Controllers" ]; then
           echo "🎮 Testing custom controllers..."

           CUSTOM_CONTROLLERS=$(find app/Custom/Controllers -name "*.php" | wc -l)
           echo "   Found $CUSTOM_CONTROLLERS custom controllers"
           echo "- **Custom Controllers:** $CUSTOM_CONTROLLERS found" >> "$TEST_DIR/reports/custom_integration_test.md"

           # Test if custom controllers can be loaded
           php artisan route:list | grep -i custom > "$TEST_DIR/logs/custom_routes.log" 2>/dev/null
           CUSTOM_ROUTES=$(wc -l < "$TEST_DIR/logs/custom_routes.log" 2>/dev/null || echo "0")
           echo "   Custom routes registered: $CUSTOM_ROUTES"
           echo "  - Routes registered: $CUSTOM_ROUTES" >> "$TEST_DIR/reports/custom_integration_test.md"
       fi

       # Test custom models
       if [ -d "app/Custom/Models" ]; then
           echo "📊 Testing custom models..."

           CUSTOM_MODELS=$(find app/Custom/Models -name "*.php" | wc -l)
           echo "   Found $CUSTOM_MODELS custom models"
           echo "- **Custom Models:** $CUSTOM_MODELS found" >> "$TEST_DIR/reports/custom_integration_test.md"

           # Test model loading with tinker
           if [ $CUSTOM_MODELS -gt 0 ]; then
               FIRST_MODEL=$(find app/Custom/Models -name "*.php" | head -1 | xargs basename | sed 's/.php$//')
               if [ -n "$FIRST_MODEL" ]; then
                   php artisan tinker --execute="try { new App\\Custom\\Models\\$FIRST_MODEL(); echo 'Custom model loadable'; } catch(Exception \$e) { echo 'Model error: ' . \$e->getMessage(); }" > "$TEST_DIR/logs/model_test.log" 2>&1
                   MODEL_TEST_RESULT=$(cat "$TEST_DIR/logs/model_test.log")
                   echo "   Model test: $MODEL_TEST_RESULT"
                   echo "  - Model loading: $MODEL_TEST_RESULT" >> "$TEST_DIR/reports/custom_integration_test.md"
               fi
           fi
       fi

       # Test custom views
       if [ -d "resources/views/custom" ]; then
           echo "👁️ Testing custom views..."

           CUSTOM_VIEWS=$(find resources/views/custom -name "*.blade.php" | wc -l)
           echo "   Found $CUSTOM_VIEWS custom views"
           echo "- **Custom Views:** $CUSTOM_VIEWS found" >> "$TEST_DIR/reports/custom_integration_test.md"

           # Test view compilation
           if [ $CUSTOM_VIEWS -gt 0 ]; then
               php artisan view:cache > "$TEST_DIR/logs/view_cache.log" 2>&1
               if [ $? -eq 0 ]; then
                   echo "   ✅ Custom views compile successfully"
                   echo "  - View compilation: ✅ Success" >> "$TEST_DIR/reports/custom_integration_test.md"
               else
                   echo "   ❌ Custom view compilation errors"
                   echo "  - View compilation: ❌ Errors found" >> "$TEST_DIR/reports/custom_integration_test.md"
                   tail -5 "$TEST_DIR/logs/view_cache.log"
               fi
           fi
       fi

       # Test custom migrations
       if [ -d "database/migrations/custom" ]; then
           echo "🗄️ Testing custom migrations..."

           CUSTOM_MIGRATIONS=$(find database/migrations/custom -name "*.php" | wc -l)
           echo "   Found $CUSTOM_MIGRATIONS custom migrations"
           echo "- **Custom Migrations:** $CUSTOM_MIGRATIONS found" >> "$TEST_DIR/reports/custom_integration_test.md"

           # Check migration status
           php artisan migrate:status | grep custom > "$TEST_DIR/logs/custom_migrations.log" 2>/dev/null || echo "No custom migrations in status"
           CUSTOM_MIGRATION_STATUS=$(wc -l < "$TEST_DIR/logs/custom_migrations.log" 2>/dev/null || echo "0")
           echo "   Custom migrations in status: $CUSTOM_MIGRATION_STATUS"
           echo "  - Migration status: $CUSTOM_MIGRATION_STATUS tracked" >> "$TEST_DIR/reports/custom_integration_test.md"
       fi

       # Test custom configuration
       if [ -f "config/custom.php" ]; then
           echo "⚙️ Testing custom configuration..."

           php artisan tinker --execute="try { config('custom'); echo 'Custom config loadable'; } catch(Exception \$e) { echo 'Config error: ' . \$e->getMessage(); }" > "$TEST_DIR/logs/config_test.log" 2>&1
           CONFIG_TEST_RESULT=$(cat "$TEST_DIR/logs/config_test.log")
           echo "   Custom config test: $CONFIG_TEST_RESULT"
           echo "- **Custom Config:** $CONFIG_TEST_RESULT" >> "$TEST_DIR/reports/custom_integration_test.md"
       fi

   else
       echo "📝 Simple mode - no custom integration testing required"

       cat > "$TEST_DIR/reports/standard_functionality_test.md" << STANDARD_HEADER
   # Standard Functionality Test Report

   **Test Date:** $(date)
   **Mode:** Simple Update (No Customizations)
   **Vendor Update:** $(grep "New Version:" "$LATEST_STAGING/UPDATE_STRATEGY.md" | cut -d' ' -f3)

   ## Core Functionality Testing

   STANDARD_HEADER
   fi
   ```

---

## **5.4: Web Application Testing**

### **Test Application Functionality:**

1. **Web interface testing:**

   ```bash
   if [ "$SERVER_RUNNING" = true ]; then
       echo "🌐 Testing web application functionality..."

       # Test main pages
       PAGES_TO_TEST=(
           "/"
           "/login"
           "/register"
           "/dashboard"
           "/admin"
       )

       echo "### Web Interface Testing" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       echo "" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"

       for page in "${PAGES_TO_TEST[@]}"; do
           echo "   Testing page: $page"

           HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8000$page")
           RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "http://127.0.0.1:8000$page")

           if [[ "$HTTP_CODE" =~ ^(200|302|401)$ ]]; then
               echo "   ✅ $page - HTTP $HTTP_CODE (${RESPONSE_TIME}s)"
               echo "- **$page:** ✅ HTTP $HTTP_CODE (${RESPONSE_TIME}s)" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
           else
               echo "   ❌ $page - HTTP $HTTP_CODE"
               echo "- **$page:** ❌ HTTP $HTTP_CODE" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
           fi
       done

       # Test if login page contains expected elements
       echo "   Testing login page content..."
       LOGIN_CONTENT=$(curl -s "http://127.0.0.1:8000/login")
       if [[ "$LOGIN_CONTENT" == *"password"* ]] && [[ "$LOGIN_CONTENT" == *"email"* ]]; then
           echo "   ✅ Login form elements present"
           echo "- **Login Form:** ✅ Required elements present" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       else
           echo "   ⚠️ Login form may have issues"
           echo "- **Login Form:** ⚠️ Elements may be missing" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       fi
   else
       echo "⚠️ Server not running - skipping web interface tests"
   fi
   ```

2. **API endpoint testing (if applicable):**

   ```bash
   echo "🔗 Testing API endpoints..."

   # Check if API routes exist
   API_ROUTES=$(php artisan route:list | grep -c "api/" 2>/dev/null || echo "0")

   if [ "$API_ROUTES" -gt 0 ]; then
       echo "   Found $API_ROUTES API routes"

       # Test basic API endpoints
       if [ "$SERVER_RUNNING" = true ]; then
           # Test API health/status endpoint
           for endpoint in "/api/health" "/api/status" "/api/user" "/api/v1/health"; do
               HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8000$endpoint" 2>/dev/null)
               if [[ "$HTTP_CODE" =~ ^(200|401|404)$ ]]; then
                   echo "   API $endpoint: HTTP $HTTP_CODE"
                   if [ "$HTTP_CODE" = "200" ]; then
                       echo "- **API $endpoint:** ✅ Responding" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
                   fi
                   break
               fi
           done
       fi
   else
       echo "   No API routes detected"
       echo "- **API Endpoints:** No API routes found" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
   fi
   ```

---

## **5.5: Database Testing**

### **Test Database Functionality:**

1. **Migration testing:**

   ```bash
   echo "🗄️ Testing database migrations..."

   # Check current migration status
   echo "   Checking migration status..."
   php artisan migrate:status > "$TEST_DIR/logs/migration_status.log" 2>&1

   if [ $? -eq 0 ]; then
       TOTAL_MIGRATIONS=$(grep -c "Y\|N" "$TEST_DIR/logs/migration_status.log" 2>/dev/null || echo "0")
       PENDING_MIGRATIONS=$(grep -c " N " "$TEST_DIR/logs/migration_status.log" 2>/dev/null || echo "0")

       echo "   Total migrations: $TOTAL_MIGRATIONS"
       echo "   Pending migrations: $PENDING_MIGRATIONS"

       echo "### Database Testing" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       echo "- **Migration Status:** $TOTAL_MIGRATIONS total, $PENDING_MIGRATIONS pending" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"

       # Run pending migrations if any
       if [ "$PENDING_MIGRATIONS" -gt 0 ]; then
           echo "   Running pending migrations..."
           if php artisan migrate --force > "$TEST_DIR/logs/migrate_run.log" 2>&1; then
               echo "   ✅ Migrations completed successfully"
               echo "- **Migration Execution:** ✅ $PENDING_MIGRATIONS migrations completed" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
           else
               echo "   ❌ Migration errors occurred"
               echo "- **Migration Execution:** ❌ Errors occurred" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
               tail -10 "$TEST_DIR/logs/migrate_run.log"
           fi
       else
           echo "   ✅ No pending migrations"
           echo "- **Migration Execution:** ✅ Database up to date" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       fi
   else
       echo "   ❌ Could not check migration status"
       echo "- **Migration Status:** ❌ Could not determine status" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
   fi
   ```

2. **Basic data operations:**

   ```bash
   echo "📊 Testing basic database operations..."

   # Test if we can query the users table (most common)
   php artisan tinker --execute="try { DB::table('users')->count(); echo 'Database query successful'; } catch(Exception \$e) { echo 'Database query failed: ' . \$e->getMessage(); }" > "$TEST_DIR/logs/db_query_test.log" 2>&1

   DB_QUERY_RESULT=$(cat "$TEST_DIR/logs/db_query_test.log")
   echo "   Database query test: $DB_QUERY_RESULT"
   echo "- **Database Queries:** $DB_QUERY_RESULT" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
   ```

---

## **5.6: Performance & Error Testing**

### **Check for Performance and Errors:**

1. **Application logs:**

   ```bash
   echo "📋 Checking application logs for errors..."

   # Clear logs first
   > storage/logs/laravel.log 2>/dev/null

   # Generate some activity by hitting the home page
   if [ "$SERVER_RUNNING" = true ]; then
       curl -s "http://127.0.0.1:8000/" > /dev/null
       sleep 2
   fi

   # Check for errors in logs
   if [ -f "storage/logs/laravel.log" ]; then
       ERROR_COUNT=$(grep -c "ERROR\|CRITICAL\|EMERGENCY" storage/logs/laravel.log 2>/dev/null || echo "0")
       WARNING_COUNT=$(grep -c "WARNING" storage/logs/laravel.log 2>/dev/null || echo "0")

       echo "   Error count: $ERROR_COUNT"
       echo "   Warning count: $WARNING_COUNT"

       echo "### Error Analysis" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       echo "- **Errors:** $ERROR_COUNT found" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
       echo "- **Warnings:** $WARNING_COUNT found" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"

       if [ "$ERROR_COUNT" -gt 0 ]; then
           echo "   ⚠️ Recent errors found:"
           tail -10 storage/logs/laravel.log | grep "ERROR\|CRITICAL\|EMERGENCY" | tail -3
       fi
   else
       echo "   No log file found"
       echo "- **Logs:** No log file generated" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
   fi
   ```

2. **Memory and performance:**

   ```bash
   echo "⚡ Testing application performance..."

   if [ "$SERVER_RUNNING" = true ]; then
       # Test page load time
       LOAD_TIME=$(curl -s -o /dev/null -w "%{time_total}" "http://127.0.0.1:8000/")
       echo "   Home page load time: ${LOAD_TIME}s"

       # Determine performance rating
       if (( $(echo "$LOAD_TIME < 2.0" | bc -l) )); then
           PERFORMANCE_RATING="✅ Good"
       elif (( $(echo "$LOAD_TIME < 5.0" | bc -l) )); then
           PERFORMANCE_RATING="⚠️ Acceptable"
       else
           PERFORMANCE_RATING="❌ Slow"
       fi

       echo "   Performance rating: $PERFORMANCE_RATING"
       echo "- **Performance:** $PERFORMANCE_RATING (${LOAD_TIME}s load time)" >> "$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"
   fi
   ```

---

## **5.7: Generate Test Summary**

### **Create Comprehensive Test Summary:**

1. **Cleanup and stop server:**

   ```bash
   # Stop the development server
   if [ -n "$SERVER_PID" ]; then
       kill $SERVER_PID 2>/dev/null
       echo "🛑 Development server stopped"
   fi
   ```

2. **Generate test summary:**

   ```bash
   echo "📊 Generating test summary..."

   # Count test results
   REPORT_FILE="$TEST_DIR/reports/$([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "custom_integration_test.md" || echo "standard_functionality_test.md")"

   if [ -f "$REPORT_FILE" ]; then
       SUCCESS_COUNT=$(grep -c "✅" "$REPORT_FILE" 2>/dev/null || echo "0")
       WARNING_COUNT=$(grep -c "⚠️" "$REPORT_FILE" 2>/dev/null || echo "0")
       ERROR_COUNT=$(grep -c "❌" "$REPORT_FILE" 2>/dev/null || echo "0")

       # Add summary to report
       cat >> "$REPORT_FILE" << SUMMARY_EOF

   ## Test Summary

   **Overall Results:**
   - ✅ Successful tests: $SUCCESS_COUNT
   - ⚠️ Warnings: $WARNING_COUNT
   - ❌ Failures: $ERROR_COUNT

   **Recommendation:**
   $(if [ "$ERROR_COUNT" -gt 0 ]; then
       echo "❌ **DO NOT PROCEED** - Critical issues found that must be resolved"
   elif [ "$WARNING_COUNT" -gt 3 ]; then
       echo "⚠️ **PROCEED WITH CAUTION** - Multiple warnings require investigation"
   else
       echo "✅ **SAFE TO PROCEED** - Testing completed successfully"
   fi)

   **Next Steps:**
   $(if [ "$ERROR_COUNT" -gt 0 ]; then
       echo "1. Review error details above"
       echo "2. Check application logs: storage/logs/laravel.log"
       echo "3. Fix issues before proceeding to Step 06"
   elif [ "$WARNING_COUNT" -gt 0 ]; then
       echo "1. Review warnings above"
       echo "2. Document known issues"
       echo "3. Proceed to Step 06 with monitoring"
   else
       echo "1. Continue to Step 06: Update Dependencies"
       echo "2. Proceed with confidence"
   fi)
   SUMMARY_EOF

       echo "✅ Test summary completed:"
       echo "   Successes: $SUCCESS_COUNT"
       echo "   Warnings: $WARNING_COUNT"
       echo "   Errors: $ERROR_COUNT"

       # Determine overall result
       if [ "$ERROR_COUNT" -gt 0 ]; then
           TEST_RESULT="FAILED"
           echo "🚨 TESTING RESULT: FAILED - Critical issues found"
       elif [ "$WARNING_COUNT" -gt 3 ]; then
           TEST_RESULT="WARNING"
           echo "⚠️ TESTING RESULT: WARNING - Multiple issues need attention"
       else
           TEST_RESULT="PASSED"
           echo "✅ TESTING RESULT: PASSED - Ready to proceed"
       fi
   else
       TEST_RESULT="INCOMPLETE"
       echo "❌ TESTING RESULT: INCOMPLETE - Report not generated"
   fi
   ```

3. **Update the update log:**

   ```bash
   # Update the current update log
   LATEST_LOG=$(find Admin-Local/update_logs -name "update_*.md" | sort | tail -1)

   if [ -n "$LATEST_LOG" ]; then
       # Mark Step 05 as complete
       sed -i.bak 's/- \[ \] Step 05: Test Custom Functions/- [x] Step 05: Test Custom Functions/' "$LATEST_LOG"

       # Add Step 05 completion details
       cat >> "$LATEST_LOG" << LOG_UPDATE

   ## Step 05 Completed
   - **Test Result:** $TEST_RESULT
   - **Successes:** $SUCCESS_COUNT
   - **Warnings:** $WARNING_COUNT
   - **Errors:** $ERROR_COUNT
   - **Report:** $REPORT_FILE
   - **Recommendation:** $([ "$ERROR_COUNT" -gt 0 ] && echo "Do not proceed - fix errors" || [ "$WARNING_COUNT" -gt 3 ] && echo "Proceed with caution" || echo "Safe to proceed")
   LOG_UPDATE

       echo "✅ Update log updated: $LATEST_LOG"
   fi
   ```

---

## **✅ Step 05 Completion Checklist**

- [ ] Testing environment initialized
- [ ] Application bootstrap validated
- [ ] Database connectivity confirmed
- [ ] Custom functionality tested (if protected mode)
- [ ] Web interface functionality verified
- [ ] API endpoints tested (if applicable)
- [ ] Database migrations executed (if needed)
- [ ] Application logs checked for errors
- [ ] Performance baseline established
- [ ] Test summary generated with recommendation

---

## **Next Steps**

**Based on your test results:**

- **If PASSED:** Continue to [Step 06: Update Dependencies](Step_06_Update_Dependencies.md) ✅
- **If WARNING:** Review warnings, then proceed to [Step 06: Update Dependencies](Step_06_Update_Dependencies.md) ⚠️
- **If FAILED:** Fix critical issues before proceeding ❌

**Key files to review:**

- **Test Report:** `$REPORT_FILE`
- **Application Logs:** `storage/logs/laravel.log`
- **Test Logs:** `$TEST_DIR/logs/`

---

## **Troubleshooting**

### **Issue: Server won't start**

```bash
# Check for port conflicts
lsof -i :8000

# Check PHP configuration
php -v
php -m | grep required_extension

# Check .env configuration
cat .env | grep -E "APP_KEY|DB_"
```

### **Issue: Database connection fails**

```bash
# Test database manually
mysql -u [username] -p [database]

# Check .env database settings
grep DB_ .env

# Test connection with artisan
php artisan tinker --execute="DB::connection()->getPdo()"
```

### **Issue: Custom functions not working**

```bash
# Check custom directory structure
find app/Custom -type f

# Verify custom service provider
grep -r "Custom" config/app.php

# Check custom routes
php artisan route:list | grep -i custom
```
