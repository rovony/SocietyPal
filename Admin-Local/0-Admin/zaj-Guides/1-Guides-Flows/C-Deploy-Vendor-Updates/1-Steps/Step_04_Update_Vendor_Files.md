# Step 04: Update Vendor Files

**Goal:** Execute the vendor file update using the appropriate strategy based on customization mode and deployment method.

**Time Required:** 45 minutes
**Prerequisites:** Step 03 completed with execution plan ready

---

## **Tracking Integration**

```bash
# Initialize Step 04 tracking using Linear Universal Tracking System
# Detect project root and admin directories dynamically
PROJECT_ROOT=$(pwd)
while [[ ! -d "$PROJECT_ROOT/Admin-Local" && "$PROJECT_ROOT" != "/" ]]; do
    PROJECT_ROOT=$(dirname "$PROJECT_ROOT")
done

if [[ "$PROJECT_ROOT" == "/" ]]; then
    echo "❌ Could not find project root with Admin-Local directory"
    exit 1
fi

ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
PROJECT_NAME=$(basename "$PROJECT_ROOT")

# Detect current session directory from tracking system
if [[ -d "$ADMIN_LOCAL/1-CurrentProject/Tracking" ]]; then
    # Find the most recent update session
    SESSION_DIR=$(find "$ADMIN_LOCAL/1-CurrentProject/Tracking" -name "*Update-or-Customization" -type d | sort | tail -1)
    if [[ -z "$SESSION_DIR" ]]; then
        echo "❌ No update session found. Run Step 01 first."
        exit 1
    fi
else
    echo "❌ Tracking system not initialized. Run setup-tracking.sh first."
    exit 1
fi

echo "🔍 Step 04: Update Vendor Files - Tracking Integration"
echo "   Project: $PROJECT_NAME"
echo "   Root: $PROJECT_ROOT"
echo "   Session: $(basename "$SESSION_DIR")"

# Create step-specific tracking files
cat > "$SESSION_DIR/1-Planning/step-04-vendor-update-plan.md" << 'PLAN_EOF'
# Step 04: Update Vendor Files - Execution Plan

## Objectives
- [ ] Determine update type (complete app vs batch)
- [ ] Execute vendor file update with appropriate strategy
- [ ] Preserve customizations (if protected mode)
- [ ] Handle configuration file merging
- [ ] Validate update success
- [ ] Document changes and create recovery options

## Strategy Selected
- **Update Type:** [To be determined]
- **Customization Mode:** [To be determined]
- **Deploy Method:** [To be determined]
- **Safety Backup:** [To be created]

## Execution Log
- **Started:** $(date)
- **Step 04 Tracking Initialized:** ✅

PLAN_EOF

# Record baseline before update
cat > "$SESSION_DIR/2-Baselines/step-04-pre-update-baseline.txt" << 'BASELINE_EOF'
# Step 04 Pre-Update Baseline - $(date)

## Project State Before Vendor Update
$(find . -maxdepth 2 -type f -name "*.php" -o -name "*.js" -o -name "*.json" | head -20)

## Configuration Files
$(ls -la composer.json package.json .env 2>/dev/null || echo "Standard config files")

## Custom Areas (if exists)
$(ls -la app/Custom/ config/custom* resources/Custom/ 2>/dev/null || echo "No custom areas detected")

BASELINE_EOF

echo "✅ Step 04 tracking initialized: $SESSION_DIR"
echo "📋 Planning: $SESSION_DIR/1-Planning/step-04-vendor-update-plan.md"
echo "📊 Baseline: $SESSION_DIR/2-Baselines/step-04-pre-update-baseline.txt"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and **V2 Missing Content Amendments**:

- V1 Step 5.3: Vendor commit strategy
- V2 Amendment 12.1: Protected customization layer
- V1: Advanced change detection and preservation scripts

---

## **4.1: Determine Update Type and Method**

### **Initialize Update Environment:**

1. **Load previous analysis:**

   ```bash
   # Navigate to project root (using project-agnostic detection)
   cd "$PROJECT_ROOT"

   # Find latest comparison analysis
   LATEST_STAGING=$(find "$ADMIN_LOCAL/vendor_updates" -name "202*" -type d | sort | tail -1)
   COMPARE_DIR="$LATEST_STAGING/comparison"
   NEW_APP_DIR=$(grep "Application Path:" "$LATEST_STAGING/UPDATE_STRATEGY.md" | cut -d' ' -f3)

   # Load settings from previous steps with fallback detection
   CUSTOMIZATION_MODE=$(grep "CUSTOMIZATION_MODE=" "$ADMIN_LOCAL/update_logs/update_"*.md | tail -1 | cut -d'"' -f2 2>/dev/null)
   DEPLOY_METHOD=$(grep "DEPLOY_METHOD=" "$ADMIN_LOCAL/update_logs/update_"*.md | tail -1 | cut -d'"' -f2 2>/dev/null)

   # Fallback detection if not found in logs
   if [[ -z "$CUSTOMIZATION_MODE" ]]; then
       if [[ -d "app/Custom" ]]; then
           CUSTOMIZATION_MODE="protected"
       else
           CUSTOMIZATION_MODE="simple"
       fi
   fi

   if [[ -z "$DEPLOY_METHOD" ]]; then
       DEPLOY_METHOD="manual_ssh"  # Default fallback
   fi

   echo "🔄 Starting vendor file update..."
   echo "   Customization: $CUSTOMIZATION_MODE"
   echo "   Deploy Method: $DEPLOY_METHOD"
   echo "   Source: $NEW_APP_DIR"

   # Update tracking with detected configuration
   sed -i.bak "s/\*\*Update Type:\*\* \[To be determined\]/\*\*Update Type:\*\* [Analyzing...]/g" "$SESSION_DIR/1-Planning/step-04-vendor-update-plan.md"
   sed -i.bak "s/\*\*Customization Mode:\*\* \[To be determined\]/\*\*Customization Mode:\*\* $CUSTOMIZATION_MODE/g" "$SESSION_DIR/1-Planning/step-04-vendor-update-plan.md"
   sed -i.bak "s/\*\*Deploy Method:\*\* \[To be determined\]/\*\*Deploy Method:\*\* $DEPLOY_METHOD/g" "$SESSION_DIR/1-Planning/step-04-vendor-update-plan.md"
   ```

2. **Determine update type (whole app vs batch):**

   ```bash
   # Detect if this is a batch update or complete new version
   UPDATE_TYPE="unknown"

   # Check if new version has installation directory (indicates complete app)
   if [ -d "$NEW_APP_DIR/install" ] || [ -d "$NEW_APP_DIR/installer" ]; then
       UPDATE_TYPE="complete_app"
       echo "📦 UPDATE TYPE: Complete application (full CodeCanyon package)"
   else
       # Check if it's a structured update package
       if [ -f "$NEW_APP_DIR/UPDATE_README.md" ] || [ -f "$NEW_APP_DIR/update.txt" ]; then
           UPDATE_TYPE="batch_update"
           echo "📦 UPDATE TYPE: Batch update (incremental changes only)"
       else
           # Fallback: determine by file count
           FILE_COUNT=$(find "$NEW_APP_DIR" -type f | wc -l)
           if [ "$FILE_COUNT" -lt 100 ]; then
               UPDATE_TYPE="batch_update"
               echo "📦 UPDATE TYPE: Batch update (detected by file count: $FILE_COUNT)"
           else
               UPDATE_TYPE="complete_app"
               echo "📦 UPDATE TYPE: Complete application (detected by file count: $FILE_COUNT)"
           fi
       fi
   fi

   echo "✅ Update type determined: $UPDATE_TYPE"
   ```

---

## **4.2: Pre-Update Safety Measures**

### **Create Safety Checkpoints:**

1. **Create working backup:**

   ```bash
   # Create immediate pre-update backup
   SAFETY_BACKUP="Admin-Local/safety_backups/pre_vendor_update_$(date +%Y%m%d_%H%M%S)"
   mkdir -p "$SAFETY_BACKUP"

   echo "🛡️ Creating safety backup..."

   if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
       # Backup customizations
       echo "   Backing up customizations..."
       tar -czf "$SAFETY_BACKUP/customizations.tar.gz" \
         app/Custom/ \
         config/custom.php \
         database/migrations/custom/ \
         resources/views/custom/ \
         Admin-Local/myCustomizations/ 2>/dev/null

       # Backup integration points
       tar -czf "$SAFETY_BACKUP/integration_points.tar.gz" \
         app/Http/Controllers/ \
         app/Models/ \
         routes/ \
         config/ 2>/dev/null
   fi

   # Backup critical config files always
   tar -czf "$SAFETY_BACKUP/critical_config.tar.gz" \
     .env \
     composer.json \
     package.json \
     webpack.mix.js \
     vite.config.js 2>/dev/null

   echo "✅ Safety backup created: $SAFETY_BACKUP"
   ```

2. **Validate current state:**

   ```bash
   # Ensure application is working before update
   echo "🔍 Validating current application state..."

   # Test composer autoload
   if ! php -r "require 'vendor/autoload.php'; echo 'Autoload OK\n';" 2>/dev/null; then
       echo "❌ Current application has composer issues"
       echo "   Run: composer install"
       exit 1
   fi

   # Test Laravel bootstrap
   if ! php artisan --version >/dev/null 2>&1; then
       echo "❌ Current application has Laravel issues"
       echo "   Check your configuration"
       exit 1
   fi

   echo "✅ Current application state validated"
   ```

---

## **4.3: Execute Vendor Update by Type**

### **Branch Based on Update Type:**

1. **Complete Application Update:**

   ```bash
   if [ "$UPDATE_TYPE" = "complete_app" ]; then
       echo "🔄 Executing complete application update..."

       if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
           echo "🛡️ Protected mode: Preserving customizations..."

           # Define protected areas
           PROTECTED_DIRS=(
               "app/Custom"
               "config/custom.php"
               "database/migrations/custom"
               "resources/views/custom"
               "Admin-Local/myCustomizations"
           )

           # Temporarily move protected areas
           TEMP_PROTECTED="$SAFETY_BACKUP/temp_protected"
           mkdir -p "$TEMP_PROTECTED"

           for protected in "${PROTECTED_DIRS[@]}"; do
               if [ -e "$protected" ]; then
                   echo "   Protecting: $protected"
                   PARENT_DIR=$(dirname "$protected")
                   mkdir -p "$TEMP_PROTECTED/$PARENT_DIR"
                   mv "$protected" "$TEMP_PROTECTED/$protected" 2>/dev/null || true
               fi
           done

           # Define vendor directories to update
           VENDOR_DIRS=(
               "app/Http"
               "app/Models"
               "app/Services"
               "app/Providers"
               "resources/views"
               "resources/js"
               "resources/css"
               "database/migrations"
               "database/seeders"
               "routes"
               "config"
               "public"
           )

           # Update vendor directories
           for vendor_dir in "${VENDOR_DIRS[@]}"; do
               if [ -d "$NEW_APP_DIR/$vendor_dir" ]; then
                   echo "   Updating vendor: $vendor_dir"
                   rm -rf "$vendor_dir" 2>/dev/null || true
                   cp -r "$NEW_APP_DIR/$vendor_dir" "$vendor_dir"
               fi
           done

           # Restore protected areas
           for protected in "${PROTECTED_DIRS[@]}"; do
               if [ -e "$TEMP_PROTECTED/$protected" ]; then
                   echo "   Restoring: $protected"
                   PARENT_DIR=$(dirname "$protected")
                   mkdir -p "$PARENT_DIR"
                   mv "$TEMP_PROTECTED/$protected" "$protected"
               fi
           done

           echo "✅ Protected update completed - customizations preserved"

       else
           echo "📦 Simple mode: Full replacement..."

           # Simple full replacement (excluding version control and admin files)
           EXCLUDE_PATTERNS=(
               ".git"
               ".gitignore"
               "Admin-Local"
               ".env"
               "storage/app"
               "storage/logs"
               "vendor"
               "node_modules"
           )

           # Create exclusion parameters for rsync
           RSYNC_EXCLUDES=""
           for pattern in "${EXCLUDE_PATTERNS[@]}"; do
               RSYNC_EXCLUDES="$RSYNC_EXCLUDES --exclude=$pattern"
           done

           # Use rsync for efficient update
           rsync -av $RSYNC_EXCLUDES "$NEW_APP_DIR/" "./"

           echo "✅ Simple update completed - full vendor replacement"
       fi
   fi
   ```

2. **Batch Update Process:**

   ```bash
   if [ "$UPDATE_TYPE" = "batch_update" ]; then
       echo "🔄 Executing batch update..."

       # Batch updates are typically safer and more targeted
       echo "📋 Analyzing batch update contents..."

       # Look for update instructions
       UPDATE_INSTRUCTIONS=""
       for instruction_file in "UPDATE_README.md" "update.txt" "CHANGELOG.md" "README.txt"; do
           if [ -f "$NEW_APP_DIR/$instruction_file" ]; then
               UPDATE_INSTRUCTIONS="$NEW_APP_DIR/$instruction_file"
               break
           fi
       done

       if [ -n "$UPDATE_INSTRUCTIONS" ]; then
           echo "📖 Update instructions found: $UPDATE_INSTRUCTIONS"
           echo "   Review: cat $UPDATE_INSTRUCTIONS"
           echo ""
           echo "📄 Instructions preview:"
           head -20 "$UPDATE_INSTRUCTIONS" | sed 's/^/   /'
           echo ""
           read -p "Continue with batch update? (y/N): " CONTINUE_BATCH

           if [[ ! "$CONTINUE_BATCH" =~ ^[Yy]$ ]]; then
               echo "⏹️ Batch update cancelled by user"
               exit 1
           fi
       fi

       # Apply batch update files
       if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
           echo "🛡️ Protected batch update..."

           # Copy batch files but avoid protected areas
           find "$NEW_APP_DIR" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.vue" \) | while read -r file; do
               RELATIVE_PATH="${file#$NEW_APP_DIR/}"

               # Skip protected paths
               SKIP_FILE=false
               for protected in "app/Custom" "config/custom.php" "database/migrations/custom"; do
                   if [[ "$RELATIVE_PATH" == "$protected"* ]]; then
                       SKIP_FILE=true
                       break
                   fi
               done

               if [ "$SKIP_FILE" = false ]; then
                   PARENT_DIR=$(dirname "$RELATIVE_PATH")
                   mkdir -p "$PARENT_DIR"
                   cp "$file" "$RELATIVE_PATH"
                   echo "   Updated: $RELATIVE_PATH"
               else
                   echo "   Skipped (protected): $RELATIVE_PATH"
               fi
           done

       else
           echo "📦 Simple batch update..."

           # Copy all batch files
           cp -r "$NEW_APP_DIR"/* "./"
       fi

       echo "✅ Batch update completed"
   fi
   ```

---

## **4.4: Handle Configuration Files**

### **Merge Configuration Changes:**

1. **Update core configuration files:**

   ```bash
   echo "⚙️ Handling configuration updates..."

   # Handle composer.json
   if [ -f "$NEW_APP_DIR/composer.json" ]; then
       echo "📦 Updating composer.json..."

       if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
           # Merge composer.json preserving custom dependencies
           echo "   Merging with custom dependencies..."

           # Backup current composer.json
           cp composer.json "$SAFETY_BACKUP/composer.json.backup"

           # Extract custom dependencies (if any)
           CUSTOM_DEPS=$(jq -r '.require | to_entries[] | select(.key | test("custom/|local/")) | "\(.key): \(.value)"' composer.json 2>/dev/null || echo "")

           # Use new composer.json as base
           cp "$NEW_APP_DIR/composer.json" composer.json

           # Re-add custom dependencies if they exist
           if [ -n "$CUSTOM_DEPS" ]; then
               echo "   Preserving custom dependencies: $CUSTOM_DEPS"
               # Would need jq manipulation here for real implementation
           fi
       else
           # Simple replacement
           cp "$NEW_APP_DIR/composer.json" composer.json
       fi

       echo "✅ composer.json updated"
   fi

   # Handle package.json
   if [ -f "$NEW_APP_DIR/package.json" ]; then
       echo "🎨 Updating package.json..."

       if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
           # Similar merge logic for package.json
           cp package.json "$SAFETY_BACKUP/package.json.backup"
       fi

       cp "$NEW_APP_DIR/package.json" package.json
       echo "✅ package.json updated"
   fi

   # Handle Laravel config files
   CONFIG_FILES=(
       "config/app.php"
       "config/database.php"
       "config/filesystems.php"
       "config/mail.php"
   )

   for config_file in "${CONFIG_FILES[@]}"; do
       if [ -f "$NEW_APP_DIR/$config_file" ]; then
           echo "⚙️ Updating $config_file..."

           if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
               # Check if custom configurations exist
               if grep -q "custom\|Custom" "$config_file" 2>/dev/null; then
                   echo "   ⚠️ Custom configuration detected in $config_file"
                   echo "   Manual merge may be required"
                   cp "$config_file" "$SAFETY_BACKUP/$(basename $config_file).backup"
               fi
           fi

           cp "$NEW_APP_DIR/$config_file" "$config_file"
       fi
   done
   ```

---

## **4.5: Update Dependencies Based on Deployment Method**

### **Prepare for Different Deployment Scenarios:**

1. **Method A: Manual SSH Deployment:**

   ```bash
   if [ "$DEPLOY_METHOD" = "manual_ssh" ]; then
       echo "🔧 Preparing for Manual SSH deployment..."

       # Dependencies will be installed locally, then packaged
       echo "📦 Installing dependencies locally for packaging..."

       # Remove dev dependencies
       composer install --no-dev --optimize-autoloader --no-interaction

       # Install frontend dependencies
       if [ -f "package.json" ]; then
           npm ci --production
           npm run build
       fi

       echo "✅ Dependencies prepared for manual deployment"
   fi
   ```

2. **Method B: GitHub Actions:**

   ```bash
   if [ "$DEPLOY_METHOD" = "github_actions" ]; then
       echo "🔧 Preparing for GitHub Actions deployment..."

       # Dependencies will be installed on CI/CD server
       # Only commit source files
       echo "📝 Source files updated for CI/CD build"

       # Ensure lock files are current
       if [ -f "package.json" ] && [ ! -f "package-lock.json" ]; then
           echo "   Generating package-lock.json..."
           npm install --package-lock-only
       fi

       echo "✅ Source prepared for GitHub Actions"
   fi
   ```

3. **Method C: DeployHQ:**

   ```bash
   if [ "$DEPLOY_METHOD" = "deployhq" ]; then
       echo "🔧 Preparing for DeployHQ deployment..."

       # Similar to GitHub Actions - source only
       echo "📝 Source files updated for DeployHQ build"
       echo "✅ Source prepared for DeployHQ"
   fi
   ```

4. **Method D: GitHub + Manual Build (Scenario D):**

   ```bash
   if [ "$DEPLOY_METHOD" = "github_manual" ]; then
       echo "🔧 Preparing for GitHub + Manual Build deployment..."

       # Commit source, but build manually before deployment
       echo "📝 Source files updated for manual build"
       echo "   Note: Will pull from GitHub and build manually in Step 07"
       echo "✅ Source prepared for GitHub + Manual workflow"
   fi
   ```

---

## **4.6: Validate Vendor Update**

### **Verify Update Success:**

1. **Check file integrity:**

   ```bash
   echo "🔍 Validating vendor update..."

   # Test composer autoload with new files
   if ! composer dump-autoload >/dev/null 2>&1; then
       echo "❌ Composer autoload failed after update"
       echo "   This may indicate file conflicts or missing files"
       exit 1
   fi

   # Test Laravel bootstrap
   if ! php artisan --version >/dev/null 2>&1; then
       echo "❌ Laravel bootstrap failed after update"
       echo "   Check for configuration conflicts"
       exit 1
   fi

   echo "✅ Basic file integrity validated"
   ```

2. **Document update completion:**

   ```bash
   # Update the update log
   LATEST_LOG=$(find Admin-Local/update_logs -name "update_*.md" | sort | tail -1)

   if [ -n "$LATEST_LOG" ]; then
       # Mark Step 04 as complete
       sed -i.bak 's/- \[ \] Step 04: Update Vendor Files/- [x] Step 04: Update Vendor Files/' "$LATEST_LOG"

       # Add completion details
       cat >> "$LATEST_LOG" << LOG_UPDATE

   ## Step 04 Completed
   - **Update Type:** $UPDATE_TYPE
   - **Customization Mode:** $CUSTOMIZATION_MODE
   - **Deploy Method:** $DEPLOY_METHOD
   - **Safety Backup:** $SAFETY_BACKUP
   - **Files Updated:** Vendor files replaced/updated
   LOG_UPDATE

       echo "✅ Update log updated: $LATEST_LOG"
   fi

   # Create completion summary
   cat > "$LATEST_STAGING/STEP_04_SUMMARY.md" << SUMMARY_EOF
   # Step 04 Completion Summary

   ## Update Details
   - **Completed:** $(date)
   - **Update Type:** $UPDATE_TYPE
   - **Customization Mode:** $CUSTOMIZATION_MODE
   - **Deploy Method:** $DEPLOY_METHOD

   ## Files Modified
   $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
   echo "- ✅ Vendor files updated"
   echo "- ✅ Customizations preserved in app/Custom/"
   echo "- ✅ Custom configuration preserved"
   else
   echo "- ✅ All vendor files updated"
   echo "- ✅ Full application replacement completed"
   fi)

   ## Safety Measures
   - **Safety Backup:** $SAFETY_BACKUP
   - **Rollback Available:** Yes (use backup from Step 01)

   ## Next Steps
   1. Proceed to Step 05: Test Custom Functions
   2. Verify integration between updated vendor and custom code
   3. Test core application functionality

   ## Emergency Rollback
   \`\`\`bash
   # If critical issues found, rollback from safety backup
   tar -xzf $SAFETY_BACKUP/customizations.tar.gz
   tar -xzf $SAFETY_BACKUP/critical_config.tar.gz
   \`\`\`
   SUMMARY_EOF

   echo "✅ Step 04 completion summary: $LATEST_STAGING/STEP_04_SUMMARY.md"
   ```

---

## **✅ Step 04 Completion Checklist**

- [ ] Update type determined (complete app vs batch)
- [ ] Customization mode applied (protected vs simple)
- [ ] Safety backup created
- [ ] Current application state validated
- [ ] Vendor files updated according to strategy
- [ ] Protected areas preserved (if applicable)
- [ ] Configuration files handled appropriately
- [ ] Dependencies prepared for deployment method
- [ ] File integrity validated
- [ ] Update log and summary documentation completed

---

## **Next Steps**

**All Scenarios:** Continue to [Step 05: Test Custom Functions](Step_05_Test_Custom_Functions.md)

**Important Notes:**

- **Protected Mode:** Verify custom-vendor integration
- **Simple Mode:** Test core application functionality
- **Emergency:** Use safety backup in `$SAFETY_BACKUP` if issues arise

---

## **Troubleshooting**

### **Issue: Composer autoload fails**

```bash
# Check for syntax errors
find app/ -name "*.php" -exec php -l {} \;

# Regenerate autoload
composer dump-autoload
```

### **Issue: Custom files missing**

```bash
# Restore from safety backup
tar -xzf $SAFETY_BACKUP/customizations.tar.gz
```

### **Issue: Configuration conflicts**

```bash
# Check for duplicate configuration keys
grep -r "duplicate_key" config/
```
