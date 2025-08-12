# Step 03: Compare Changes & Plan Update

**Goal:** Systematically compare the current and new versions to identify changes and plan the update strategy while protecting customizations.

**Time Required:** 30 minutes  
**Prerequisites:** Step 02 completed with new version analyzed

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and **V2 Missing Content Amendments**:

- V1 Step 5.1: Change comparison tools
- V2 Amendment: Update capture system
- V1: Advanced change detection scripts

---

## **3.1: Initialize Comparison Environment**

### **Setup Comparison Workspace:**

1. **Locate working directories:**

   ````bash
   2. **Navigate to project root:**

   ```bash
   # Set path variables for consistency
   export PROJECT_ROOT="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
   export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
   cd "$PROJECT_ROOT"

   # Find the latest staging directory from Step 02
   LATEST_STAGING=$(find Admin-Local/vendor_updates -name "202*" -type d | sort | tail -1)
   if [ -z "$LATEST_STAGING" ]; then
       echo "❌ No staging directory found. Please complete Step 02 first."
       exit 1
   fi

   # Read the strategy from Step 02
   STRATEGY_FILE="$LATEST_STAGING/UPDATE_STRATEGY.md"
   NEW_APP_DIR=$(grep "Application Path:" "$STRATEGY_FILE" | cut -d' ' -f3)

   echo "🔍 Starting detailed comparison..."
   echo "   Current: $(pwd)"
   echo "   New:     $NEW_APP_DIR"
   echo "   Staging: $LATEST_STAGING"
   ````

2. **Create comparison workspace:**

   ```bash
   # Create comparison directory
   COMPARE_DIR="$LATEST_STAGING/comparison"
   mkdir -p "$COMPARE_DIR"/{reports,file_changes,custom_preservation}

   # Get customization mode from backup
   CUSTOMIZATION_MODE=$(grep "CUSTOMIZATION_MODE=" Admin-Local/update_logs/update_*.md | tail -1 | cut -d'"' -f2)

   echo "📊 Comparison workspace: $COMPARE_DIR"
   echo "🛡️ Customization mode: $CUSTOMIZATION_MODE"
   ```

---

## **3.2: File-Level Change Analysis**

### **Identify Changed Files:**

1. **Create file comparison report:**

   ```bash
   echo "📋 Analyzing file changes..."

   # Compare directory structures
   echo "## Directory Structure Changes" > "$COMPARE_DIR/reports/file_changes.md"
   echo "" >> "$COMPARE_DIR/reports/file_changes.md"

   # Get file lists
   find . -type f -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.vue" -o -name "*.json" | \
     grep -v vendor/ | grep -v node_modules/ | grep -v ".git/" | sort > "$COMPARE_DIR/current_files.txt"

   find "$NEW_APP_DIR" -type f -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.vue" -o -name "*.json" | \
     sed "s|$NEW_APP_DIR/||" | sort > "$COMPARE_DIR/new_files.txt"

   # Find added, removed, and common files
   comm -23 "$COMPARE_DIR/new_files.txt" "$COMPARE_DIR/current_files.txt" > "$COMPARE_DIR/added_files.txt"
   comm -13 "$COMPARE_DIR/new_files.txt" "$COMPARE_DIR/current_files.txt" > "$COMPARE_DIR/removed_files.txt"
   comm -12 "$COMPARE_DIR/new_files.txt" "$COMPARE_DIR/current_files.txt" > "$COMPARE_DIR/common_files.txt"

   echo "✅ File analysis complete:"
   echo "   Added:   $(wc -l < "$COMPARE_DIR/added_files.txt") files"
   echo "   Removed: $(wc -l < "$COMPARE_DIR/removed_files.txt") files"
   echo "   Common:  $(wc -l < "$COMPARE_DIR/common_files.txt") files"
   ```

2. **Analyze critical file changes:**

   ```bash
   # Check for changes in critical files
   echo "🔍 Checking critical system files..."

   CRITICAL_FILES=(
       "composer.json"
       "package.json"
       "config/app.php"
       "config/database.php"
       "routes/web.php"
       "routes/api.php"
       ".env.example"
   )

   echo "### Critical File Changes" >> "$COMPARE_DIR/reports/file_changes.md"
   echo "" >> "$COMPARE_DIR/reports/file_changes.md"

   for file in "${CRITICAL_FILES[@]}"; do
       if [ -f "$file" ] && [ -f "$NEW_APP_DIR/$file" ]; then
           if ! diff -q "$file" "$NEW_APP_DIR/$file" > /dev/null 2>&1; then
               echo "⚠️  CHANGED: $file"
               echo "- ⚠️ **CHANGED:** \`$file\`" >> "$COMPARE_DIR/reports/file_changes.md"

               # Create detailed diff for critical files
               diff -u "$file" "$NEW_APP_DIR/$file" > "$COMPARE_DIR/file_changes/${file//\//_}.diff" 2>/dev/null
           else
               echo "✅ UNCHANGED: $file"
               echo "- ✅ **UNCHANGED:** \`$file\`" >> "$COMPARE_DIR/reports/file_changes.md"
           fi
       elif [ ! -f "$file" ] && [ -f "$NEW_APP_DIR/$file" ]; then
           echo "🆕 NEW: $file"
           echo "- 🆕 **NEW:** \`$file\`" >> "$COMPARE_DIR/reports/file_changes.md"
       elif [ -f "$file" ] && [ ! -f "$NEW_APP_DIR/$file" ]; then
           echo "🗑️ REMOVED: $file"
           echo "- 🗑️ **REMOVED:** \`$file\`" >> "$COMPARE_DIR/reports/file_changes.md"
       fi
   done
   ```

---

## **3.3: Customization Impact Analysis**

### **Analyze Impact on Customizations:**

1. **Check customization conflicts (if in protected mode):**

   ```bash
   if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       echo "🛡️ Analyzing customization protection conflicts..."

       # Check if vendor changed files that might conflict with customizations
       echo "### Customization Conflict Analysis" >> "$COMPARE_DIR/reports/file_changes.md"
       echo "" >> "$COMPARE_DIR/reports/file_changes.md"

       # Areas where customizations typically interact with vendor code
       INTERACTION_AREAS=(
           "app/Http/Controllers"
           "app/Models"
           "resources/views"
           "routes"
           "config"
           "database/migrations"
       )

       for area in "${INTERACTION_AREAS[@]}"; do
           if [ -d "$area" ] && [ -d "$NEW_APP_DIR/$area" ]; then
               echo "🔍 Checking: $area"

               # Find changed files in this area
               CHANGED_IN_AREA=0
               while IFS= read -r file; do
                   if [ -f "$area/$file" ] && [ -f "$NEW_APP_DIR/$area/$file" ]; then
                       if ! diff -q "$area/$file" "$NEW_APP_DIR/$area/$file" > /dev/null 2>&1; then
                           ((CHANGED_IN_AREA++))
                       fi
                   fi
               done < "$COMPARE_DIR/common_files.txt"

               if [ $CHANGED_IN_AREA -gt 0 ]; then
                   echo "   ⚠️  $CHANGED_IN_AREA changed files in $area"
                   echo "- **$area:** $CHANGED_IN_AREA changed files (potential conflicts)" >> "$COMPARE_DIR/reports/file_changes.md"
               else
                   echo "   ✅ No changes in $area"
                   echo "- **$area:** No changes" >> "$COMPARE_DIR/reports/file_changes.md"
               fi
           fi
       done

       # Check if app/Custom directory would be safe
       if [ -d "app/Custom" ]; then
           echo "✅ app/Custom/ directory will be preserved"
           echo "- **app/Custom/:** ✅ Protected - will be preserved" >> "$COMPARE_DIR/reports/file_changes.md"
       fi
   else
       echo "📝 Simple update mode - no customization conflicts to check"
       echo "### Update Mode" >> "$COMPARE_DIR/reports/file_changes.md"
       echo "- **Mode:** Simple update (no customizations to protect)" >> "$COMPARE_DIR/reports/file_changes.md"
   fi
   ```

2. **Create customization preservation plan:**

   ```bash
   # Create plan for preserving customizations
   cat > "$COMPARE_DIR/custom_preservation/PRESERVATION_PLAN.md" << PRESERVATION_EOF
   # Customization Preservation Plan

   ## Customization Mode: $CUSTOMIZATION_MODE

   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
   cat << 'PROTECTED_EOF'
   ### Protected Areas (Will NOT be updated)
   - \`app/Custom/\` - Custom controllers, models, services
   - \`config/custom.php\` - Custom configuration
   - \`database/migrations/custom/\` - Custom migrations
   - \`resources/views/custom/\` - Custom views
   - \`Admin-Local/myCustomizations/\` - Documentation and backups

   ### Update Strategy
   1. **Backup protected areas** before update
   2. **Replace vendor files** (everything except protected areas)
   3. **Restore protected areas** after vendor update
   4. **Test integration** between vendor updates and custom code
   5. **Update custom code** if vendor APIs changed

   ### Risk Areas to Monitor
   - Changes to base controllers that custom controllers extend
   - Changes to models that custom models reference
   - Changes to configuration that custom config depends on
   - Changes to database schema that custom migrations reference

   ### Verification Required
   - [ ] Custom controllers still function with new vendor base classes
   - [ ] Custom models work with updated vendor models
   - [ ] Custom views render correctly with updated vendor layouts
   - [ ] Custom migrations are compatible with vendor schema changes
   - [ ] Custom configuration values are still valid
   PROTECTED_EOF
   else
   cat << 'SIMPLE_EOF'
   ### Simple Update Strategy
   - **No customizations detected** - standard vendor replacement
   - **Replace all vendor files** without preservation needs
   - **Standard testing** of core functionality
   - **No custom code integration testing** required

   ### Verification Required
   - [ ] Application loads correctly
   - [ ] Core functionality works
   - [ ] Database migrations complete successfully
   - [ ] No errors in application logs
   SIMPLE_EOF
   fi)
   PRESERVATION_EOF

   echo "✅ Preservation plan created: $COMPARE_DIR/custom_preservation/PRESERVATION_PLAN.md"
   ```

---

## **3.4: Database and Dependencies Analysis**

### **Analyze Database and Package Changes:**

1. **Compare database changes:**

   ```bash
   echo "🗄️ Analyzing database changes..."

   # Check for new migrations
   if [ -d "database/migrations" ] && [ -d "$NEW_APP_DIR/database/migrations" ]; then
       echo "### Database Migration Analysis" >> "$COMPARE_DIR/reports/file_changes.md"
       echo "" >> "$COMPARE_DIR/reports/file_changes.md"

       # Find new migration files
       find "$NEW_APP_DIR/database/migrations" -name "*.php" -exec basename {} \; | sort > "$COMPARE_DIR/new_migrations.txt"
       find "database/migrations" -name "*.php" -exec basename {} \; | sort > "$COMPARE_DIR/current_migrations.txt"

       NEW_MIGRATION_COUNT=$(comm -23 "$COMPARE_DIR/new_migrations.txt" "$COMPARE_DIR/current_migrations.txt" | wc -l)

       if [ "$NEW_MIGRATION_COUNT" -gt 0 ]; then
           echo "⚠️  $NEW_MIGRATION_COUNT new migration files detected"
           echo "- **New Migrations:** $NEW_MIGRATION_COUNT files require database update" >> "$COMPARE_DIR/reports/file_changes.md"

           # List the new migrations
           echo "   New migration files:"
           comm -23 "$COMPARE_DIR/new_migrations.txt" "$COMPARE_DIR/current_migrations.txt" | head -5 | sed 's/^/     /'
       else
           echo "✅ No new migrations"
           echo "- **New Migrations:** None" >> "$COMPARE_DIR/reports/file_changes.md"
       fi
   fi
   ```

2. **Compare dependencies:**

   ```bash
   echo "📦 Analyzing dependency changes..."

   # PHP dependencies (composer.json)
   if [ -f "composer.json" ] && [ -f "$NEW_APP_DIR/composer.json" ]; then
       echo "### PHP Dependencies" >> "$COMPARE_DIR/reports/file_changes.md"
       echo "" >> "$COMPARE_DIR/reports/file_changes.md"

       # Extract require sections and compare
       jq '.require' composer.json 2>/dev/null > "$COMPARE_DIR/current_php_deps.json"
       jq '.require' "$NEW_APP_DIR/composer.json" 2>/dev/null > "$COMPARE_DIR/new_php_deps.json"

       if ! diff -q "$COMPARE_DIR/current_php_deps.json" "$COMPARE_DIR/new_php_deps.json" > /dev/null 2>&1; then
           echo "⚠️  PHP dependencies changed"
           echo "- **PHP Dependencies:** ⚠️ Changed - \`composer install\` required" >> "$COMPARE_DIR/reports/file_changes.md"

           # Create detailed diff
           diff -u "$COMPARE_DIR/current_php_deps.json" "$COMPARE_DIR/new_php_deps.json" > "$COMPARE_DIR/file_changes/composer_deps.diff" 2>/dev/null
       else
           echo "✅ PHP dependencies unchanged"
           echo "- **PHP Dependencies:** ✅ Unchanged" >> "$COMPARE_DIR/reports/file_changes.md"
       fi
   fi

   # Frontend dependencies (package.json)
   if [ -f "package.json" ] && [ -f "$NEW_APP_DIR/package.json" ]; then
       echo "### Frontend Dependencies" >> "$COMPARE_DIR/reports/file_changes.md"
       echo "" >> "$COMPARE_DIR/reports/file_changes.md"

       # Compare dependencies sections
       jq '.dependencies' package.json 2>/dev/null > "$COMPARE_DIR/current_js_deps.json"
       jq '.dependencies' "$NEW_APP_DIR/package.json" 2>/dev/null > "$COMPARE_DIR/new_js_deps.json"

       if ! diff -q "$COMPARE_DIR/current_js_deps.json" "$COMPARE_DIR/new_js_deps.json" > /dev/null 2>&1; then
           echo "⚠️  Frontend dependencies changed"
           echo "- **Frontend Dependencies:** ⚠️ Changed - \`npm install\` and rebuild required" >> "$COMPARE_DIR/reports/file_changes.md"

           # Create detailed diff
           diff -u "$COMPARE_DIR/current_js_deps.json" "$COMPARE_DIR/new_js_deps.json" > "$COMPARE_DIR/file_changes/package_deps.diff" 2>/dev/null
       else
           echo "✅ Frontend dependencies unchanged"
           echo "- **Frontend Dependencies:** ✅ Unchanged" >> "$COMPARE_DIR/reports/file_changes.md"
       fi
   fi
   ```

---

## **3.5: Create Update Execution Plan**

### **Generate Detailed Update Plan:**

1. **Create comprehensive update plan:**

   ```bash
   echo "📋 Creating update execution plan..."

   # Read complexity from Step 02
   COMPLEXITY=$(grep "Complexity Level:" "$LATEST_STAGING/UPDATE_STRATEGY.md" | cut -d' ' -f3)
   NEW_VERSION=$(grep "New Version:" "$LATEST_STAGING/UPDATE_STRATEGY.md" | cut -d' ' -f3)

   cat > "$COMPARE_DIR/UPDATE_EXECUTION_PLAN.md" << PLAN_EOF
   # Update Execution Plan - Version $NEW_VERSION

   ## Summary
   - **Complexity:** $COMPLEXITY
   - **Customization Mode:** $CUSTOMIZATION_MODE
   - **Files Changed:** $(wc -l < "$COMPARE_DIR/common_files.txt") total files to check
   - **New Files:** $(wc -l < "$COMPARE_DIR/added_files.txt") files
   - **Removed Files:** $(wc -l < "$COMPARE_DIR/removed_files.txt") files

   ## Critical Changes Detected
   $(grep "⚠️" "$COMPARE_DIR/reports/file_changes.md" | sed 's/^/- /')

   ## Execution Steps Required

   ### Step 04: Update Vendor Files
   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       echo "- **Mode:** Protected update (preserve customizations)"
       echo "- **Strategy:** Selective file replacement excluding app/Custom/"
       echo "- **Risk:** Medium - integration testing required"
   else
       echo "- **Mode:** Full replacement update"
       echo "- **Strategy:** Complete vendor file replacement"
       echo "- **Risk:** Low - standard testing sufficient"
   fi)

   ### Step 05: Test Custom Functions
   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       echo "- **Required:** Full custom functionality testing"
       echo "- **Focus:** Integration between custom and updated vendor code"
       echo "- **Time:** Extended testing period required"
   else
       echo "- **Required:** Basic functionality testing"
       echo "- **Focus:** Core application features"
       echo "- **Time:** Standard testing sufficient"
   fi)

   ### Step 06: Update Dependencies
   $(if grep -q "Changed" "$COMPARE_DIR/reports/file_changes.md"; then
       echo "- **PHP:** \`composer install --no-dev\` required"
       echo "- **Frontend:** \`npm install && npm run build\` required"
       echo "- **Time:** 10-15 minutes for dependency installation"
   else
       echo "- **Dependencies:** No changes detected"
       echo "- **Action:** Verification only"
   fi)

   ### Step 07: Test Build Process
   - **Complexity:** $COMPLEXITY testing level
   $(if [ "$COMPLEXITY" = "High" ]; then
       echo "- **Environment:** Staging environment mandatory"
       echo "- **Duration:** Extended testing period"
   elif [ "$COMPLEXITY" = "Medium" ]; then
       echo "- **Environment:** Staging environment recommended"
       echo "- **Duration:** Thorough testing required"
   else
       echo "- **Environment:** Local testing acceptable"
       echo "- **Duration:** Standard testing"
   fi)

   ### Step 08: Deploy Updates
   - **Method:** $(grep "DEPLOY_METHOD=" Admin-Local/update_logs/update_*.md | tail -1 | cut -d'"' -f2)
   $(if [ "$COMPLEXITY" = "High" ]; then
       echo "- **Approach:** Staged deployment with rollback plan"
   else
       echo "- **Approach:** Standard deployment process"
   fi)

   ### Step 09: Verify Deployment
   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       echo "- **Focus:** Custom functionality verification"
       echo "- **Checklist:** Both vendor and custom features"
   else
       echo "- **Focus:** Core functionality verification"
       echo "- **Checklist:** Standard application features"
   fi)

   ## Risk Assessment
   - **Overall Risk:** $(if [ "$COMPLEXITY" = "High" ]; then echo "HIGH"; elif [ "$COMPLEXITY" = "Medium" ]; then echo "MEDIUM"; else echo "LOW"; fi)
   - **Rollback Plan:** Available (backup created in Step 01)
   - **Testing Requirements:** $(if [ "$COMPLEXITY" = "High" ]; then echo "Extensive"; elif [ "$COMPLEXITY" = "Medium" ]; then echo "Thorough"; else echo "Standard"; fi)

   ## Success Criteria
   - [ ] All vendor files updated successfully
   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
   echo "- [ ] All customizations preserved and functional"
   echo "- [ ] Custom-vendor integration working correctly"
   fi)
   - [ ] Database migrations completed (if any)
   - [ ] Dependencies updated and built successfully
   - [ ] Application loads without errors
   - [ ] Core functionality verified
   - [ ] Production deployment successful

   ## Emergency Procedures
   - **Rollback:** Use backup created in Step 01
   - **Support:** Check documentation in Admin-Local/myCustomizations/
   - **Logs:** Monitor storage/logs/laravel.log during update
   PLAN_EOF

   echo "✅ Update execution plan created: $COMPARE_DIR/UPDATE_EXECUTION_PLAN.md"
   ```

2. **Update the update log:**

   ```bash
   # Update the current update log
   LATEST_LOG=$(find Admin-Local/update_logs -name "update_*.md" | sort | tail -1)

   if [ -n "$LATEST_LOG" ]; then
       # Mark Step 03 as complete
       sed -i.bak 's/- \[ \] Step 03: Compare Changes/- [x] Step 03: Compare Changes/' "$LATEST_LOG"

       # Add Step 03 completion details
       cat >> "$LATEST_LOG" << LOG_UPDATE

   ## Step 03 Completed
   - **Files Analyzed:** $(wc -l < "$COMPARE_DIR/common_files.txt") common files
   - **Changes Found:** $(grep -c "⚠️" "$COMPARE_DIR/reports/file_changes.md" 2>/dev/null || echo "0") critical changes
   - **Execution Plan:** $COMPARE_DIR/UPDATE_EXECUTION_PLAN.md
   - **Customization Impact:** $CUSTOMIZATION_MODE mode
   LOG_UPDATE

       echo "✅ Update log updated: $LATEST_LOG"
   fi
   ```

---

## **✅ Step 03 Completion Checklist**

- [ ] File comparison analysis completed
- [ ] Critical file changes identified
- [ ] Customization conflict analysis done (if protected mode)
- [ ] Database migration changes analyzed
- [ ] Dependency changes checked (PHP and frontend)
- [ ] Customization preservation plan created
- [ ] Detailed update execution plan generated
- [ ] Risk assessment completed
- [ ] Update log updated with comparison results

---

## **Next Steps**

**Based on your analysis:**

- **All Updates:** Continue to [Step 04: Update Vendor Files](Step_04_Update_Vendor_Files.md)
- **Review your plan:** Check `$COMPARE_DIR/UPDATE_EXECUTION_PLAN.md` for your specific strategy

**Key files to review:**

- **Execution Plan:** `$COMPARE_DIR/UPDATE_EXECUTION_PLAN.md`
- **Preservation Plan:** `$COMPARE_DIR/custom_preservation/PRESERVATION_PLAN.md`
- **Change Report:** `$COMPARE_DIR/reports/file_changes.md`

---

## **Troubleshooting**

### **Issue: File comparison fails**

```bash
# Check if directories exist
ls -la "$LATEST_STAGING"
ls -la "$NEW_APP_DIR"
```

### **Issue: JSON parsing fails for dependencies**

```bash
# Install jq if missing
brew install jq
# Or manually compare composer.json files
```

### **Issue: Cannot determine customization mode**

```bash
# Check for customizations manually
ls -la app/Custom/ 2>/dev/null
grep -r "CUSTOMIZATION_MODE" Admin-Local/update_logs/
```
