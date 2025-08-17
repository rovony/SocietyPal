# Step 02: Download New CodeCanyon Version

**Goal:** Safely download and prepare the new CodeCanyon version while preserving existing customizations.

**Time Required:** 20 minutes
**Prerequisites:** Step 01 completed with backup created

---

## **🔍 Tracking Integration**

This step integrates with the **Linear Universal Tracking System (5-Tracking-System)** for organized progress management.

### **Initialize Step 02 Tracking:**

```bash
# Continue from Step 01 tracking session
export PROJECT_ROOT="$(pwd)"
export SESSION_DIR="$PROJECT_ROOT/Admin-Local/1-CurrentProject/Tracking"
export UPDATE_SESSION="2-Update-or-Customization"

# Update step tracking
cat > "$SESSION_DIR/$UPDATE_SESSION/1-Planning/step-02-download-plan.md" << DOWNLOAD_PLAN
# Step 02: Download New CodeCanyon Version Plan

**Date:** $(date)
**Step:** 02 - Download New CodeCanyon Version
**Session:** $UPDATE_SESSION

## Download Checklist

- [ ] Create staging directory
- [ ] Download new version from CodeCanyon
- [ ] Extract and verify new version
- [ ] Analyze version information
- [ ] Compare with current version
- [ ] Check for breaking changes
- [ ] Identify new migrations
- [ ] Assess frontend changes
- [ ] Determine update complexity
- [ ] Create update strategy document
- [ ] Update tracking logs

## Current Status
DOWNLOAD_PLAN

# Create baseline record
echo "📁 New version download - $(date)" > "$SESSION_DIR/$UPDATE_SESSION/2-Baselines/step-02-version-baseline.txt"

echo "🔍 Step 02 tracking initialized"
echo "📁 Planning: $SESSION_DIR/$UPDATE_SESSION/1-Planning/step-02-download-plan.md"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and **V2 Missing Content Amendments**:

- V2 Amendment: CodeCanyon-specific configuration (Step 6.1)
- V1 Step 5.1: CodeCanyon handling
- V2 Amendment: Update capture system

---

## **2.1: Download New CodeCanyon Version**

### **Set Project Variables (Project-Agnostic):**

```bash
# Detect project root and set variables
export PROJECT_ROOT="$(pwd)"
export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
export PROJECT_NAME="$(basename "$PROJECT_ROOT" | sed 's/-Root$//' | sed 's/App-Master$//' | sed 's/.*\///g')"
cd "$PROJECT_ROOT"

echo "🏠 Project Root: $PROJECT_ROOT"
echo "📁 Admin Local: $ADMIN_LOCAL"
echo "🏷️ Project Name: $PROJECT_NAME"
```

### **Obtain New Version from CodeCanyon:**

1. **Create staging directory:**

   ```bash
   echo "📥 Preparing to download new CodeCanyon version..."

   # Create unique staging area with timestamp
   STAGING_TIMESTAMP=$(date +%Y%m%d_%H%M%S)
   STAGING_DIR="$ADMIN_LOCAL/vendor_updates/$STAGING_TIMESTAMP"
   mkdir -p "$STAGING_DIR"

   echo "📁 Staging Directory: $STAGING_DIR"

   # Update tracking with staging info
   echo "Staging Directory: $STAGING_DIR" >> "$SESSION_DIR/$UPDATE_SESSION/2-Baselines/step-02-version-baseline.txt"
   echo "Timestamp: $STAGING_TIMESTAMP" >> "$SESSION_DIR/$UPDATE_SESSION/2-Baselines/step-02-version-baseline.txt"
   ```

2. **Download from CodeCanyon:**

   ```bash
   echo "🌐 Manual Download Required:"
   echo "1. Go to: https://codecanyon.net/downloads"
   echo "2. Find: SocietyPro - Society Management Software"
   echo "3. Download: Latest version (not just the update files)"
   echo "4. Save to: $STAGING_DIR/"
   echo ""
   echo "⏳ Waiting for download completion..."
   echo "   Expected file: SocietyPro-v[VERSION].zip"
   echo ""
   read -p "Press ENTER when download is complete and saved to staging directory..."
   ```

3. **Verify download:**

   ```bash
   # Check for downloaded file
   DOWNLOAD_FILE=$(find "$STAGING_DIR" -name "*.zip" | head -1)

   if [ -n "$DOWNLOAD_FILE" ]; then
       echo "✅ Download found: $(basename "$DOWNLOAD_FILE")"
       echo "📊 File size: $(ls -lh "$DOWNLOAD_FILE" | awk '{print $5}')"
   else
       echo "❌ No ZIP file found in $STAGING_DIR"
       echo "   Please download and place the CodeCanyon ZIP file in the staging directory"
       exit 1
   fi
   ```

---

## **2.2: Extract and Analyze New Version**

### **Safely Extract New Version:**

1. **Create extraction directory:**

   ```bash
   # Create extraction area
   EXTRACT_DIR="$STAGING_DIR/extracted"
   mkdir -p "$EXTRACT_DIR"

   echo "📦 Extracting new version..."
   unzip -q "$DOWNLOAD_FILE" -d "$EXTRACT_DIR"

   # Find the actual application directory
   APP_DIR=$(find "$EXTRACT_DIR" -name "composer.json" -type f | head -1 | xargs dirname)

   if [ -n "$APP_DIR" ]; then
       echo "✅ Application found in: $APP_DIR"
   else
       echo "❌ Could not locate Laravel application in extracted files"
       echo "   Please check the extraction directory: $EXTRACT_DIR"
       exit 1
   fi
   ```

2. **Analyze version information:**

   ```bash
   # Extract version information
   echo "🔍 Analyzing new version..."

   # Check for version info in common locations
   NEW_VERSION="Unknown"

   # Check config/app.php for version
   if [ -f "$APP_DIR/config/app.php" ]; then
       VERSION_LINE=$(grep -i "version\|'app_version'" "$APP_DIR/config/app.php" | head -1)
       if [ -n "$VERSION_LINE" ]; then
           NEW_VERSION=$(echo "$VERSION_LINE" | grep -o "[0-9]\+\.[0-9]\+\.[0-9]\+" | head -1)
       fi
   fi

   # Check composer.json for version
   if [ -f "$APP_DIR/composer.json" ] && [ "$NEW_VERSION" = "Unknown" ]; then
       NEW_VERSION=$(grep -o '"version"[[:space:]]*:[[:space:]]*"[^"]*"' "$APP_DIR/composer.json" | cut -d'"' -f4)
   fi

   echo "📋 New Version: $NEW_VERSION"
   echo "📁 Application Path: $APP_DIR"
   ```

3. **Create version comparison:**

   ```bash
   # Compare with current version
   echo "📊 Version Comparison:"

   CURRENT_VERSION="Unknown"
   if [ -f "config/app.php" ]; then
       CURRENT_LINE=$(grep -i "version\|'app_version'" "config/app.php" | head -1)
       if [ -n "$CURRENT_LINE" ]; then
           CURRENT_VERSION=$(echo "$CURRENT_LINE" | grep -o "[0-9]\+\.[0-9]\+\.[0-9]\+" | head -1)
       fi
   fi

   echo "   Current: $CURRENT_VERSION"
   echo "   New:     $NEW_VERSION"

   if [ "$CURRENT_VERSION" = "$NEW_VERSION" ]; then
       echo "⚠️  WARNING: Versions appear to be the same"
       echo "   Continue only if you're sure this is a newer release"
   fi
   ```

---

## **2.3: Identify Changes and Requirements**

### **Analyze Update Changes:**

1. **Check for breaking changes:**

   ```bash
   echo "🔍 Checking for potential breaking changes..."

   # Compare composer.json for dependency changes
   if [ -f "composer.json" ] && [ -f "$APP_DIR/composer.json" ]; then
       echo "📦 Dependency Changes:"

       # Extract PHP version requirements
       CURRENT_PHP=$(grep -o '"php"[[:space:]]*:[[:space:]]*"[^"]*"' composer.json | cut -d'"' -f4)
       NEW_PHP=$(grep -o '"php"[[:space:]]*:[[:space:]]*"[^"]*"' "$APP_DIR/composer.json" | cut -d'"' -f4)

       echo "   PHP Requirement:"
       echo "     Current: $CURRENT_PHP"
       echo "     New:     $NEW_PHP"

       # Check Laravel version
       CURRENT_LARAVEL=$(grep -o '"laravel/framework"[[:space:]]*:[[:space:]]*"[^"]*"' composer.json | cut -d'"' -f4)
       NEW_LARAVEL=$(grep -o '"laravel/framework"[[:space:]]*:[[:space:]]*"[^"]*"' "$APP_DIR/composer.json" | cut -d'"' -f4)

       echo "   Laravel Version:"
       echo "     Current: $CURRENT_LARAVEL"
       echo "     New:     $NEW_LARAVEL"
   fi
   ```

2. **Check for new migration files:**

   ```bash
   # Check for new database migrations
   echo "🗄️ Database Changes:"

   if [ -d "$APP_DIR/database/migrations" ]; then
       NEW_MIGRATIONS=$(find "$APP_DIR/database/migrations" -name "*.php" | wc -l)
       CURRENT_MIGRATIONS=$(find "database/migrations" -name "*.php" 2>/dev/null | wc -l)

       echo "   Migration files:"
       echo "     Current: $CURRENT_MIGRATIONS"
       echo "     New:     $NEW_MIGRATIONS"

       if [ "$NEW_MIGRATIONS" -gt "$CURRENT_MIGRATIONS" ]; then
           echo "   ⚠️  New migrations detected - database update required"
           NEW_MIGRATION_FILES=$(find "$APP_DIR/database/migrations" -name "*.php" -newer "database/migrations" 2>/dev/null | head -5)
           if [ -n "$NEW_MIGRATION_FILES" ]; then
               echo "   📄 Recent migration files:"
               echo "$NEW_MIGRATION_FILES" | sed 's/^/     /'
           fi
       fi
   fi
   ```

3. **Check for asset changes:**

   ```bash
   # Check for frontend changes
   echo "🎨 Frontend Changes:"

   # Compare package.json
   if [ -f "package.json" ] && [ -f "$APP_DIR/package.json" ]; then
       # Check for dependency changes
       CURRENT_DEPS=$(grep -c '".*":' package.json 2>/dev/null || echo "0")
       NEW_DEPS=$(grep -c '".*":' "$APP_DIR/package.json" 2>/dev/null || echo "0")

       echo "   Package dependencies:"
       echo "     Current: $CURRENT_DEPS"
       echo "     New:     $NEW_DEPS"

       if [ "$NEW_DEPS" -ne "$CURRENT_DEPS" ]; then
           echo "   ⚠️  Frontend dependencies changed - rebuild required"
       fi
   fi

   # Check for new asset files
   if [ -d "$APP_DIR/resources" ]; then
       NEW_ASSETS=$(find "$APP_DIR/resources" -name "*.js" -o -name "*.css" -o -name "*.vue" | wc -l)
       CURRENT_ASSETS=$(find "resources" -name "*.js" -o -name "*.css" -o -name "*.vue" 2>/dev/null | wc -l)

       echo "   Asset files:"
       echo "     Current: $CURRENT_ASSETS"
       echo "     New:     $NEW_ASSETS"
   fi
   ```

---

## **2.4: Prepare Update Strategy**

### **Create Update Plan Based on Analysis:**

1. **Determine update complexity:**

   ```bash
   # Assess update complexity
   echo "📋 Update Complexity Assessment:"

   COMPLEXITY="Simple"
   REASONS=()

   # Check various factors
   if [ "$NEW_MIGRATIONS" -gt "$CURRENT_MIGRATIONS" ]; then
       COMPLEXITY="Medium"
       REASONS+=("Database migrations required")
   fi

   if [ "$NEW_DEPS" -ne "$CURRENT_DEPS" ]; then
       COMPLEXITY="Medium"
       REASONS+=("Frontend dependencies changed")
   fi

   if [ "$CURRENT_LARAVEL" != "$NEW_LARAVEL" ]; then
       COMPLEXITY="High"
       REASONS+=("Laravel framework version change")
   fi

   if [ "$CURRENT_PHP" != "$NEW_PHP" ]; then
       COMPLEXITY="High"
       REASONS+=("PHP version requirement change")
   fi

   echo "   Complexity Level: $COMPLEXITY"
   for reason in "${REASONS[@]}"; do
       echo "   - $reason"
   done
   ```

2. **Create update strategy document:**

   ```bash
   # Document the update strategy
   cat > "$STAGING_DIR/UPDATE_STRATEGY.md" << STRATEGY_EOF
   # Update Strategy - $NEW_VERSION

   ## Version Information
   - **Current Version:** $CURRENT_VERSION
   - **New Version:** $NEW_VERSION
   - **Update Complexity:** $COMPLEXITY
   - **Download Location:** $STAGING_DIR
   - **Application Path:** $APP_DIR

   ## Changes Detected
   - **PHP Requirement:** $CURRENT_PHP → $NEW_PHP
   - **Laravel Version:** $CURRENT_LARAVEL → $NEW_LARAVEL
   - **Database Migrations:** $CURRENT_MIGRATIONS → $NEW_MIGRATIONS files
   - **Frontend Dependencies:** $CURRENT_DEPS → $NEW_DEPS packages
   - **Asset Files:** $CURRENT_ASSETS → $NEW_ASSETS files

   ## Update Requirements
   $(for reason in "${REASONS[@]}"; do echo "- $reason"; done)

   ## Recommended Approach
   $(if [ "$COMPLEXITY" = "Simple" ]; then
       echo "✅ **Standard Update Process**"
       echo "- Follow normal vendor file replacement"
       echo "- Minimal testing required"
   elif [ "$COMPLEXITY" = "Medium" ]; then
       echo "⚠️ **Enhanced Update Process**"
       echo "- Extended testing required"
       echo "- Database/frontend changes need attention"
       echo "- Staging environment recommended"
   else
       echo "🚨 **Complex Update Process**"
       echo "- Framework/PHP changes require careful testing"
       echo "- Staging environment mandatory"
       echo "- Consider professional review"
   fi)

   ## Next Steps
   1. Continue to Step 03: Compare Changes
   2. Use complexity level: $COMPLEXITY
   3. Pay attention to: $(echo "${REASONS[@]}" | tr ' ' ',')

   ## Customization Handling
   - **Mode:** $(grep "CUSTOMIZATION_MODE=" Admin-Local/update_logs/update_*.md | tail -1 | cut -d'"' -f2)
   - **Strategy:** $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "Preserve app/Custom/ directory" || echo "Standard vendor replacement")
   STRATEGY_EOF

   echo "✅ Update strategy documented: $STAGING_DIR/UPDATE_STRATEGY.md"
   ```

3. **Update the update log:**

   ```bash
   # Update the current update log
   LATEST_LOG=$(find Admin-Local/update_logs -name "update_*.md" | sort | tail -1)

   if [ -n "$LATEST_LOG" ]; then
       # Add Step 02 completion
       sed -i.bak 's/- \[ \] Step 02: Download New CodeCanyon Version/- [x] Step 02: Download New CodeCanyon Version/' "$LATEST_LOG"

       # Add version information
       cat >> "$LATEST_LOG" << LOG_UPDATE

   ## Step 02 Completed
   - **New Version:** $NEW_VERSION
   - **Complexity:** $COMPLEXITY
   - **Location:** $STAGING_DIR
   - **Strategy:** See $STAGING_DIR/UPDATE_STRATEGY.md
   LOG_UPDATE

       echo "✅ Update log updated: $LATEST_LOG"
   fi
   ```

---

## **✅ Step 02 Completion Checklist**

- [ ] New CodeCanyon version downloaded
- [ ] Version extracted and verified
- [ ] Version comparison completed (current vs new)
- [ ] Breaking changes analyzed (PHP, Laravel, dependencies)
- [ ] Database migrations checked
- [ ] Frontend changes assessed
- [ ] Update complexity determined (Simple/Medium/High)
- [ ] Update strategy documented
- [ ] Update log updated with new version info

---

## **Next Steps**

**Based on your update complexity:**

- **If Simple:** Continue to [Step 03: Compare Changes](Step_03_Compare_Changes.md) - standard process
- **If Medium:** Continue to [Step 03: Compare Changes](Step_03_Compare_Changes.md) - enhanced testing
- **If High:** Continue to [Step 03: Compare Changes](Step_03_Compare_Changes.md) - staging environment mandatory

**Your update strategy:** Check `$STAGING_DIR/UPDATE_STRATEGY.md` for detailed plan.

---

## **Troubleshooting**

### **Issue: Cannot find application in extracted files**

```bash
# Manual search for Laravel application
find "$EXTRACT_DIR" -name "artisan" -type f
find "$EXTRACT_DIR" -name "composer.json" -type f
```

### **Issue: Version detection fails**

```bash
# Manual version check
grep -r "version" "$APP_DIR/config/" | head -5
cat "$APP_DIR/composer.json" | grep version
```

### **Issue: Download file not found**

```bash
# Check staging directory
ls -la "$STAGING_DIR"
# Ensure ZIP file is placed in the staging directory
```
