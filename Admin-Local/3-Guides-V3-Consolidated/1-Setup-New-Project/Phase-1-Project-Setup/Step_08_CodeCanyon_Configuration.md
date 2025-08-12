# Step 08: CodeCanyon Configuration

**Setup CodeCanyon-specific application handling and tracking systems**

> 📋 **Analysis Source:** V2 Amendment 6.1 + V1's comparison scripts (our analysis: "Take V2 Amendment + V1's comparison scripts")
>
> 🎯 **Purpose:** Establish CodeCanyon license tracking and update management system

---

## **CodeCanyon Detection and Setup**

1. **Navigate to project root:**

   ```bash
   # Set path variables for consistency
   export PROJECT_ROOT="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
   cd "$PROJECT_ROOT"

   echo "🎯 Setting up CodeCanyon-specific configuration..."
   ```

2. **Detect if this is a CodeCanyon application:**

   ```bash
   # Detect if this is a CodeCanyon application
   CODECANYON_APP=false
   if [ -f "install/index.php" ] || [ -f "installer/index.php" ] || grep -q "codecanyon\|envato" composer.json 2>/dev/null; then
       CODECANYON_APP=true
       echo "✅ CodeCanyon application detected"
   else
       echo "ℹ️ Standard Laravel application"
   fi
   ```

3. **Setup CodeCanyon-specific handling:**

   ```bash
   if [ "$CODECANYON_APP" = true ]; then
       echo "📋 Setting up CodeCanyon-specific handling..."

       # Create license management structure
       mkdir -p Admin-Local/codecanyon_management/{licenses,installer_backup,update_tracking}

       # Backup installer for future reference
       if [ -d "install" ]; then
           cp -r install Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Installer backed up to Admin-Local/codecanyon_management/"
       fi

       if [ -d "installer" ]; then
           cp -r installer Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Installer backed up to Admin-Local/codecanyon_management/"
       fi
   fi
   ```

## **License Tracking System**

4. **Create license tracking system:**

   ```bash
   # Create license tracking system
   cat > Admin-Local/codecanyon_management/LICENSE_TRACKING.md << 'LICENSE_EOF'
   # CodeCanyon License Tracking

   ## Application Details
   - **Script Name:** SocietyPro - Society Management Software
   - **Purchase Code:** [Enter your purchase code]
   - **Version Purchased:** v1.0.4 (update with actual version)
   - **Purchase Date:** [Enter purchase date]
   - **License Type:** Regular License (or Extended License)

   ## Version History
   | Version | Release Date | Purchase Date | Purchase Code | Notes |
   |---------|--------------|---------------|---------------|-------|
   | v1.0.4 | [Date] | [Date] | [Code] | Initial purchase |

   ## License Files Location
   - Production: `shared/licenses/` (on server)
   - Backup: `Admin-Local/codecanyon_management/licenses/`
   - Original: Store securely offline

   ## Update Strategy
   1. **Before Updates:** Backup current version and customizations
   2. **During Updates:** Never overwrite `app/Custom/` directory
   3. **After Updates:** Test all custom functionality
   4. **License Migration:** Copy license files to new version

   ## Support Information
   - **Author:** [CodeCanyon author name]
   - **Support Until:** [Date + 6 months from purchase]
   - **Documentation:** [Link to documentation]
   LICENSE_EOF
   ```

## **Update Tracking and Comparison System**

5. **Create update capture system (V1's advanced comparison scripts):**

   ```bash
   # Create update capture system for GUI changes
   cat > Admin-Local/codecanyon_management/update_tracking/capture_changes.sh << 'CAPTURE_EOF'
   #!/bin/bash
   # Capture changes made through CodeCanyon GUI updates
   # Usage: Run this before applying vendor updates

   echo "📊 Capturing current state before vendor update..."

   # Create snapshot of current state
   SNAPSHOT_DATE=$(date +%Y%m%d_%H%M%S)
   SNAPSHOT_DIR="snapshots/before_update_$SNAPSHOT_DATE"

   mkdir -p "$SNAPSHOT_DIR"/{files,database,config,custom_verification}

   # Backup critical vendor files that might change
   echo "📦 Creating vendor file snapshot..."
   tar -czf "$SNAPSHOT_DIR/vendor_files_backup.tar.gz" \
     --exclude='vendor/' \
     --exclude='node_modules/' \
     --exclude='storage/logs/*' \
     --exclude='storage/framework/cache/*' \
     app/ config/ database/ resources/ routes/ public/

   # Backup database schema
   echo "🗄️ Capturing database schema..."
   php artisan schema:dump --path="$SNAPSHOT_DIR/schema_before_update.sql" 2>/dev/null || echo "Schema dump skipped (not available)"

   # Create file comparison baseline
   echo "📋 Creating file inventory..."
   find . -type f -name "*.php" -o -name "*.blade.php" -o -name "*.js" -o -name "*.css" | \
     grep -v vendor/ | grep -v node_modules/ | sort > "$SNAPSHOT_DIR/file_inventory.txt"

   # Count custom files for integrity verification
   if [ -d "app/Custom" ]; then
       find app/Custom -type f | wc -l > "$SNAPSHOT_DIR/custom_files_count.txt"
   fi

   echo "✅ Snapshot created: $SNAPSHOT_DIR"
   echo ""
   echo "📝 Next steps:"
   echo "1. Apply vendor update (replace vendor files only)"
   echo "2. Run: bash update_tracking/compare_changes.sh $SNAPSHOT_DIR"
   echo "3. Review changes and test custom functionality"
   CAPTURE_EOF
   ```

6. **Create post-update comparison system (V1's advanced analysis):**

   ```bash
   # Create comprehensive post-update analysis script
   cat > Admin-Local/codecanyon_management/update_tracking/compare_changes.sh << 'COMPARE_EOF'
   #!/bin/bash
   # compare_changes.sh - Comprehensive post-update analysis

   if [ -z "$1" ]; then
       echo "Usage: bash compare_changes.sh <snapshot_directory>"
       echo "Example: bash compare_changes.sh snapshots/before_update_20241201_143022"
       exit 1
   fi

   SNAPSHOT_DIR="$1"
   COMPARISON_DATE=$(date +%Y%m%d_%H%M%S)
   COMPARISON_DIR="comparisons/comparison_$COMPARISON_DATE"

   if [ ! -d "$SNAPSHOT_DIR" ]; then
       echo "❌ Snapshot not found: $SNAPSHOT_DIR"
       exit 1
   fi

   echo "🔍 Analyzing changes after vendor update..."
   mkdir -p "$COMPARISON_DIR"/{files,database,config,custom_verification}

   # 1. File Changes Analysis
   echo "📊 Analyzing file changes..."
   find . -type f \( -name "*.php" -o -name "*.blade.php" -o -name "*.js" -o -name "*.css" \) \
       | grep -v vendor/ | grep -v node_modules/ | grep -v 'app/Custom/' | sort > "$COMPARISON_DIR/files/file_inventory_after.txt"

   # Compare file inventories
   diff "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" > "$COMPARISON_DIR/files/file_changes.diff"

   # Analyze specific changes
   echo "📁 New files added by vendor update:"
   comm -13 "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" | head -20 | tee "$COMPARISON_DIR/files/new_files.txt"

   echo ""
   echo "🗑️ Files removed by vendor update:"
   comm -23 "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" | head -20 | tee "$COMPARISON_DIR/files/removed_files.txt"

   # 2. Custom Layer Verification
   echo ""
   echo "🛡️ Verifying custom layer integrity..."
   CUSTOM_INTACT=true

   if [ -d "app/Custom" ]; then
       CURRENT_CUSTOM_COUNT=$(find app/Custom -type f | wc -l)
       SNAPSHOT_CUSTOM_COUNT=$(cat "$SNAPSHOT_DIR/custom_files_count.txt" 2>/dev/null || echo "0")

       if [ "$CURRENT_CUSTOM_COUNT" -eq "$SNAPSHOT_CUSTOM_COUNT" ]; then
           echo "✅ Custom layer intact: $CURRENT_CUSTOM_COUNT files preserved"
       else
           echo "⚠️ Custom layer changed: $SNAPSHOT_CUSTOM_COUNT → $CURRENT_CUSTOM_COUNT files"
           CUSTOM_INTACT=false
       fi
   else
       echo "❌ Custom layer missing! This is a critical issue."
       CUSTOM_INTACT=false
   fi

   # 3. Generate Summary Report
   echo ""
   echo "📋 Generating update summary..."
   cat > "$COMPARISON_DIR/UPDATE_SUMMARY.md" << SUMMARY_EOF
   # Vendor Update Analysis Report

   **Update Date:** $(date)
   **Snapshot:** $SNAPSHOT_DIR
   **Analysis:** $COMPARISON_DIR

   ## Custom Layer Status
   - **Status:** $([ "$CUSTOM_INTACT" = true ] && echo "✅ INTACT" || echo "❌ COMPROMISED")
   - **Files:** $CURRENT_CUSTOM_COUNT custom files preserved

   ## File Changes
   - **New Files:** $(wc -l < "$COMPARISON_DIR/files/new_files.txt") files added
   - **Removed Files:** $(wc -l < "$COMPARISON_DIR/files/removed_files.txt") files removed

   ## Next Steps
   1. **Test all custom functionality**
   2. **Verify application works correctly**
   3. **Check for new configuration requirements**
   4. **Update documentation if needed**

   ## Files for Review
   - New files: $COMPARISON_DIR/files/new_files.txt
   - Removed files: $COMPARISON_DIR/files/removed_files.txt
   - Full diff: $COMPARISON_DIR/files/file_changes.diff
   SUMMARY_EOF

   echo "✅ Analysis complete: $COMPARISON_DIR/UPDATE_SUMMARY.md"
   COMPARE_EOF

   # Make scripts executable
   chmod +x Admin-Local/codecanyon_management/update_tracking/capture_changes.sh
   chmod +x Admin-Local/codecanyon_management/update_tracking/compare_changes.sh

   echo "✅ CodeCanyon management system created with advanced tracking"
   ```

**Expected Result:** CodeCanyon application detected, license tracking system created, update tools installed with V1's advanced comparison capabilities.

---

**Next Step:** [Step 09: Commit Original Vendor Files](Step_09_Commit_Original_Vendor.md)
