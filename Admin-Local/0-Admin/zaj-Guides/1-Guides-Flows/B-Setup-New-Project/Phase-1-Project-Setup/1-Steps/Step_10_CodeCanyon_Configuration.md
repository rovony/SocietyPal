# Step 10: CodeCanyon Configuration & License Management - Use the structure to set up management systems

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

   **EXPLANATION:**
   - **Purpose:** Navigate to the correct project directory and set environment variables
     - **Step 1:** `export PROJECT_ROOT=...` - Creates a reusable path variable for consistency
     - **Step 2:** `cd "$PROJECT_ROOT"` - Changes to the project directory
     - **Step 3:** `echo "🎯 Setting up..."` - Displays progress message
   - **Example:** If your project is at `/home/user/myapp/`, this ensures all commands run from the correct location

2. **Detect if this is a CodeCanyon application (Enhanced Detection):**

   ```bash
   # Enhanced CodeCanyon application detection
   CODECANYON_APP=false
   APP_NAME="Unknown"
   
   # Multiple detection patterns for better accuracy
   if [ -f "config/froiden_envato.php" ] || \
      [ -f "public/installer/index.php" ] || \
      [ -d "public/installer" ] || \
      [ -f "public/error_install.php" ] || \
      [ -f "public/version.txt" ] || \
      [ -f "public/install-version.txt" ] || \
      grep -q "codecanyon\|envato\|froiden" config/*.php 2>/dev/null || \
      [ -d "vendor/froiden" ] 2>/dev/null; then
       
       CODECANYON_APP=true
       
       # Detect specific application
       if grep -q "SocietyPro\|society.*pro" database/seeders/*.php 2>/dev/null; then
           APP_NAME="SocietyPro - Society Management Software"
       fi
       
       echo "✅ CodeCanyon application detected: $APP_NAME"
       echo "📋 Detected patterns:"
       [ -f "config/froiden_envato.php" ] && echo "  - Froiden Envato integration"
       [ -d "public/installer" ] && echo "  - Web installer present"
       [ -f "public/version.txt" ] && echo "  - Version file: $(cat public/version.txt 2>/dev/null)"
       [ -f "public/install-version.txt" ] && echo "  - Install status: $(cat public/install-version.txt 2>/dev/null)"
   else
       echo "ℹ️ Standard Laravel application"
   fi
   ```

   **EXPLANATION:**
   - **Purpose:** Intelligently detect if this is a CodeCanyon application using multiple verification patterns
     - **Step 1:** `CODECANYON_APP=false` - Sets default assumption (not a CodeCanyon app)
     - **Step 2:** Multiple file checks using `[ -f "file" ]` and `[ -d "directory" ]`:
       1. `config/froiden_envato.php` - Envato API integration file
       2. `public/installer/` - Web-based installer directory
       3. `public/version.txt` - Application version file
       4. `public/install-version.txt` - Installation status tracker
       5. `grep -q "codecanyon|envato|froiden"` - Search config files for CodeCanyon keywords
     - **Step 3:** `if grep -q "SocietyPro"` - Detect specific application type from database seeders
     - **Step 4:** Display detected patterns and version information
   - **Example Output:**
     ```
     ✅ CodeCanyon application detected: SocietyPro - Society Management Software
     📋 Detected patterns:
       - Froiden Envato integration
       - Web installer present
       - Version file: 1.0.42
       - Install status: complete
     ```

3. **Setup CodeCanyon-specific handling with addon support:**

   ```bash
   if [ "$CODECANYON_APP" = true ]; then
       echo "📋 Setting up CodeCanyon-specific handling..."

       # Create comprehensive license management structure (including addon support)
       mkdir -p Admin-Local/codecanyon_management/{licenses,installer_backup,update_tracking,addons}
       
       # Setup addon management subdirectories
       mkdir -p Admin-Local/codecanyon_management/addons/{purchased,licenses,installed,updates}

       # Backup installer components for future reference
       if [ -d "public/installer" ]; then
           cp -r public/installer Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Public installer backed up to Admin-Local/codecanyon_management/"
       fi
       
       if [ -f "public/error_install.php" ]; then
           cp public/error_install.php Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Installation error handler backed up"
       fi

       # Backup installer configuration and related files
       if [ -f "config/installer.php" ]; then
           cp config/installer.php Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Installer config backed up"
       fi
       
       if [ -f "config/froiden_envato.php" ]; then
           cp config/froiden_envato.php Admin-Local/codecanyon_management/installer_backup/
           echo "✅ Froiden Envato config backed up"
       fi

       # Document discovered patterns
       echo "📋 Documenting CodeCanyon patterns found..."
       find . -name "*install*" -o -name "*envato*" -o -name "*froiden*" 2>/dev/null | head -15 > Admin-Local/codecanyon_management/discovered_patterns.txt
       
       echo "✅ CodeCanyon management structure created with addon support"
   fi
   ```

   **EXPLANATION:**
   - **Purpose:** Create a comprehensive CodeCanyon management system with backup and addon support
     - **Directory Creation:**
       1. `mkdir -p Admin-Local/codecanyon_management/{licenses,installer_backup,update_tracking,addons}` - Creates main structure
       2. `mkdir -p Admin-Local/codecanyon_management/addons/{purchased,licenses,installed,updates}` - Creates addon subdirectories
     - **Backup Process:** Safely preserves original installer files before any modifications
       1. **Installer Directory:** `cp -r public/installer` - Backs up the web installer
       2. **Error Handler:** `cp public/error_install.php` - Backs up installation error pages
       3. **Config Files:** Backs up `config/installer.php` and `config/froiden_envato.php`
     - **Pattern Documentation:** `find . -name "*install*"` - Discovers and logs all CodeCanyon-related files
   - **Directory Structure Created:**
     ```
     Admin-Local/codecanyon_management/
     ├── licenses/           # License files and tracking
     ├── installer_backup/   # Original installer files (safe copy)
     ├── update_tracking/    # Scripts for tracking updates
     └── addons/
         ├── purchased/      # Downloaded addon files
         ├── licenses/       # Addon license files
         ├── installed/      # Currently active addons
         └── updates/        # Addon update files
     ```
   - **Example Output:** `✅ CodeCanyon management structure created with addon support`

## **License Tracking System**

4. **Request license information from user:**

   ```bash
   # Request license information from user
   echo ""
   echo "🔑 LICENSE INFORMATION REQUIRED"
   echo "════════════════════════════════════════════════════════════════"
   echo "Please provide your CodeCanyon license information:"
   echo ""
   
   # Main Application License
   echo "📱 MAIN APPLICATION: $APP_NAME"
   read -p "Enter your CodeCanyon Purchase Code: " MAIN_PURCHASE_CODE
   read -p "Enter your Purchase Date (YYYY-MM-DD): " MAIN_PURCHASE_DATE
   read -p "License Type (Regular/Extended): " MAIN_LICENSE_TYPE
   
   echo ""
   echo "🔌 ADDONS/PLUGINS"
   read -p "Do you have any addons/plugins? (y/n): " HAS_ADDONS
   
   ADDON_INFO=""
   if [[ "$HAS_ADDONS" == "y" || "$HAS_ADDONS" == "Y" ]]; then
       echo "Please enter addon information (press Enter with empty addon name to finish):"
       ADDON_COUNT=1
       while true; do
           read -p "Addon $ADDON_COUNT Name (or press Enter to finish): " ADDON_NAME
           if [ -z "$ADDON_NAME" ]; then
               break
           fi
           read -p "Addon $ADDON_COUNT Purchase Code: " ADDON_CODE
           read -p "Addon $ADDON_COUNT Purchase Date: " ADDON_DATE
           ADDON_INFO="$ADDON_INFO\n| $ADDON_NAME | [Date] | $ADDON_DATE | $ADDON_CODE | Addon |"
           ADDON_COUNT=$((ADDON_COUNT + 1))
       done
   fi
   
   echo ""
   echo "✅ License information collected. Creating tracking system..."
   ```

   **EXPLANATION:**
   - **Purpose:** Interactive license information collection from user for main app and addons
     - **Main Application Data:** Collects essential license details
       1. `read -p "Enter your CodeCanyon Purchase Code:"` - Gets the unique purchase verification code
       2. `read -p "Enter your Purchase Date:"` - Records purchase date for support tracking
       3. `read -p "License Type:"` - Identifies Regular vs Extended license permissions
     - **Addon Management:** Handles multiple addon licenses dynamically
       1. `read -p "Do you have any addons/plugins? (y/n):"` - Checks if addons exist
       2. **Loop Structure:** `while true; do` - Continues until user enters empty addon name
       3. **Dynamic Counter:** `ADDON_COUNT=$((ADDON_COUNT + 1))` - Tracks addon numbers
       4. **Data Storage:** `ADDON_INFO="$ADDON_INFO\n|..."` - Builds markdown table format
   - **Interactive Flow:**
     ```
     🔑 LICENSE INFORMATION REQUIRED
     📱 MAIN APPLICATION: SocietyPro - Society Management Software
     Enter your CodeCanyon Purchase Code: [user enters code]
     Enter your Purchase Date (YYYY-MM-DD): [user enters date]
     License Type (Regular/Extended): [user selects type]
     
     🔌 ADDONS/PLUGINS
     Do you have any addons/plugins? (y/n): [y]
     Addon 1 Name (or press Enter to finish): [Payment Gateway Addon]
     Addon 1 Purchase Code: [user enters code]
     Addon 1 Purchase Date: [user enters date]
     ```
   - **Example Output:** `✅ License information collected. Creating tracking system...`

5. **Create comprehensive license tracking system:**

   ```bash
   # Get current version information
   CURRENT_VERSION=$(cat public/version.txt 2>/dev/null || echo "Unknown")
   CURRENT_GIT_TAG=$(git describe --tags --exact-match HEAD 2>/dev/null || git tag -l | tail -1 || echo "No tags")
   INSTALL_STATUS=$(cat public/install-version.txt 2>/dev/null || echo "Unknown")
   
   # Create comprehensive license tracking system
   cat > Admin-Local/codecanyon_management/LICENSE_TRACKING.md << LICENSE_EOF
# CodeCanyon License Tracking - $APP_NAME

## Application Details
- **Script Name:** $APP_NAME
- **Purchase Code:** $MAIN_PURCHASE_CODE
- **Version Purchased:** $CURRENT_GIT_TAG (confirmed from git tags - source of truth)
- **Version Files:** $CURRENT_VERSION (public/version.txt - vendor file, keep as-is)
- **Purchase Date:** $MAIN_PURCHASE_DATE
- **License Type:** $MAIN_LICENSE_TYPE
- **Vendor:** Froiden Technologies (confirmed via config/froiden_envato.php)

## 📋 Discovered CodeCanyon Patterns in $APP_NAME

### 🔍 Detection Patterns Found:
| Pattern | Location | Purpose | Notes |
|---------|----------|---------|-------|
| \`froiden_envato.php\` | \`/config/\` | Envato API integration | Primary license verification |
| \`installer/\` | \`/public/\` | Web-based installer | Multi-step setup process |
| \`error_install.php\` | \`/public/\` | Installation error handling | Custom error pages |
| \`version.txt\` | \`/public/\` | App version display | Shows $CURRENT_VERSION (vendor file) |
| \`install-version.txt\` | \`/public/\` | Installation status | Shows '$INSTALL_STATUS' |
| \`installer.php\` | \`/config/\` | Installer configuration | Installation settings |
| Installer views | \`/resources/views/vendor/froiden-envato/\` | Installation UI templates | Envato-specific views |
| Custom installer | \`/resources/views/custom-modules/install.blade.php\` | Custom installation modules | Extended functionality |
| Language support | \`/lang/eng/installer_messages.php\` | Installer translations | Multi-language support |
| Vendor package | \`/vendor/froiden/laravel-installer/\` | Core installer logic | Composer package |

### 🏗️ Architecture Patterns:
- **Froiden Integration:** Uses Froiden Technologies' Laravel installer package
- **Multi-Layer Installation:** Web installer + config files + custom modules
- **Version Management:** Separate version files for different purposes
- **Error Handling:** Custom error pages for installation issues
- **Multi-Language:** Built-in language support for installers

### 🔌 Addon Support Structure:
- **Custom Modules:** \`/resources/views/custom-modules/\` - Indicates addon support
- **Modular Architecture:** Suggests plugin/addon capability
- **Separate Licensing:** Each addon likely requires separate purchase codes

## Version History
| Version | Release Date | Purchase Date | Purchase Code | Git Tag | Notes |
|---------|--------------|---------------|---------------|---------|-------|
| $CURRENT_GIT_TAG | [Date] | $MAIN_PURCHASE_DATE | $MAIN_PURCHASE_CODE | ✅ $CURRENT_GIT_TAG | Current (git source of truth) |
| $CURRENT_VERSION | [Date] | $MAIN_PURCHASE_DATE | $MAIN_PURCHASE_CODE | - | Version file display |$(echo -e "$ADDON_INFO")

## License Files Location
- **Production:** \`shared/licenses/\` (on server)
- **Backup:** \`Admin-Local/codecanyon_management/licenses/\`
- **Original:** Store securely offline
- **Config:** \`config/froiden_envato.php\` (license verification)

## Addon Management
### Addon Directory Structure:
\`\`\`
Admin-Local/codecanyon_management/addons/
├── purchased/          # Downloaded addon files
├── licenses/          # Addon license files
├── installed/         # Currently active addons
└── updates/           # Addon update files
\`\`\`

### Addon Installation Patterns:
- **Web Install:** Via admin panel (one-click)
- **Manual Install:** Via codebase (like main app)
- **License Requirements:** Each addon needs separate purchase code
- **Update Strategy:** Similar to main app but independent

## Update Strategy
### 🔄 Main App Updates:
1. **Before Updates:**
   - Run: \`bash Admin-Local/codecanyon_management/update_tracking/capture_changes.sh\`
   - Backup current version and customizations
   - Document all custom modifications in \`app/Custom/\`

2. **During Updates:**
   - **NEVER overwrite** \`app/Custom/\` directory (customization layer)
   - **PRESERVE** license files and configuration
   - **MAINTAIN** addon compatibility

3. **After Updates:**
   - Run: \`bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh <snapshot_dir>\`
   - Test all custom functionality
   - Verify addon compatibility
   - Test license verification

4. **License Migration:**
   - Copy license files to new version
   - Update \`config/froiden_envato.php\` if needed
   - Verify Envato API connectivity

### 🔌 Addon Updates:
- **Independent Updates:** Each addon updates separately
- **Compatibility Check:** Verify main app version requirements
- **License Validation:** Ensure addon licenses remain valid
- **Testing:** Test addon functionality after updates

## Support Information
- **Author:** Froiden Technologies (confirmed)
- **Support Until:** [Date + 6 months from purchase]
- **Documentation:** [Link to documentation]
- **API Integration:** Envato API for license verification
- **Installer Package:** \`froiden/laravel-installer\` (Composer)

## 🚨 Critical Notes
- **Version Source Truth:** Git tags ($CURRENT_GIT_TAG) override version files ($CURRENT_VERSION)
- **Customization Policy:** Use \`app/Custom/\` layer, minimize vendor file changes
- **License Verification:** Handled via \`config/froiden_envato.php\` + Envato API
- **Installation Status:** Tracked in \`public/install-version.txt\` (shows '$INSTALL_STATUS')
- **Multi-Component:** Main app + potential addons require separate management

## 📊 Installation Flow Analysis
\`\`\`
1. Download from CodeCanyon
2. Extract to server
3. Access /public/installer/
4. Follow web-based installation
5. License verification via Envato API
6. Database setup and seeding
7. Configuration completion
8. Installation status: '$INSTALL_STATUS'
\`\`\`

## 🔧 Maintenance Commands
\`\`\`bash
# Check installation status
cat public/install-version.txt

# View current version
cat public/version.txt

# Backup before updates
bash Admin-Local/codecanyon_management/update_tracking/capture_changes.sh

# Compare after updates
bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh <snapshot_dir>
\`\`\`
LICENSE_EOF
   
   echo "✅ Comprehensive license tracking system created"
   ```

   **EXPLANATION:**
   - **Purpose:** Create a comprehensive license tracking document with user-provided information and detected CodeCanyon patterns
     - **Version Detection:** Collects version information from multiple sources
       1. `CURRENT_VERSION=$(cat public/version.txt)` - Gets display version from vendor file
       2. `CURRENT_GIT_TAG=$(git describe --tags --exact-match HEAD)` - Gets actual version from Git tags (source of truth)
       3. `INSTALL_STATUS=$(cat public/install-version.txt)` - Gets installation completion status
     - **Document Creation:** `cat > Admin-Local/codecanyon_management/LICENSE_TRACKING.md << LICENSE_EOF`
       1. **Heredoc Pattern:** Creates multi-line file with variable substitution
       2. **Comprehensive Documentation:** Includes application details, discovered patterns, version history, addon management
       3. **Dynamic Content:** Integrates user-provided license data with system-detected information
     - **Key Sections Created:**
       1. **Application Details:** License codes, versions, purchase information
       2. **Discovered Patterns:** All CodeCanyon-specific files and their purposes
       3. **Version History:** Tracking table with Git tags as source of truth
       4. **Update Strategy:** Detailed procedures for safe updates
       5. **Addon Management:** Structure and installation patterns
   - **Example Output Structure:**
     ```markdown
     # CodeCanyon License Tracking - SocietyPro - Society Management Software
     
     ## Application Details
     - Purchase Code: [user-entered-code]
     - Version: v1.0.42 (git source of truth)
     - License Type: Regular/Extended
     
     ## Discovered CodeCanyon Patterns
     - froiden_envato.php: Envato API integration
     - installer/: Web-based installer
     ```
   - **Result:** `✅ Comprehensive license tracking system created`

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

   **EXPLANATION:**
   - **Purpose:** Create a comprehensive snapshot system to track all changes before vendor updates
     - **Snapshot Creation:** `SNAPSHOT_DIR="snapshots/before_update_$SNAPSHOT_DATE"`
       1. **Timestamp-based:** Uses `date +%Y%m%d_%H%M%S` for unique snapshot identification
       2. **Directory Structure:** Creates organized folders for `files`, `database`, `config`, `custom_verification`
     - **File Backup Process:**
       1. **Comprehensive Archive:** `tar -czf "$SNAPSHOT_DIR/vendor_files_backup.tar.gz"`
       2. **Smart Exclusions:** Skips `vendor/`, `node_modules/`, logs, and cache files
       3. **Critical Inclusion:** Captures `app/`, `config/`, `database/`, `resources/`, `routes/`, `public/`
     - **Database Snapshot:** `php artisan schema:dump` - Creates schema backup for structure comparison
     - **File Inventory System:**
       1. **File Discovery:** `find . -type f -name "*.php"` - Creates comprehensive file list
       2. **Custom Layer Protection:** Counts files in `app/Custom` for integrity verification
       3. **Baseline Creation:** Sorted file inventory for accurate before/after comparison
   - **Usage Workflow:**
     ```bash
     # Before vendor update
     bash Admin-Local/codecanyon_management/update_tracking/capture_changes.sh
     
     # Apply vendor update (replace files)
     
     # After vendor update
     bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh snapshots/before_update_20241201_143022
     ```
   - **Example Output:**
     ```
     📊 Capturing current state before vendor update...
     📦 Creating vendor file snapshot...
     🗄️ Capturing database schema...
     📋 Creating file inventory...
     ✅ Snapshot created: snapshots/before_update_20241201_143022
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

   **EXPLANATION:**
   - **Purpose:** Create a comprehensive post-update analysis system to verify vendor update integrity and detect changes
     - **Input Validation:** `if [ -z "$1" ]` - Ensures snapshot directory parameter is provided
       1. **Usage Check:** Validates required snapshot directory argument
       2. **Example Display:** Shows proper command format with real example
       3. **Error Exit:** Prevents script execution without proper parameters
     - **Analysis Process:**
       1. **Directory Setup:** `COMPARISON_DIR="comparisons/comparison_$COMPARISON_DATE"` - Creates timestamped analysis folder
       2. **File Comparison:** `diff "$SNAPSHOT_DIR/file_inventory.txt"` - Compares before/after file lists
       3. **Change Detection:** Uses `comm -13` and `comm -23` to find new and removed files
       4. **Custom Layer Verification:** Ensures `app/Custom/` directory integrity with file count comparison
     - **Report Generation:**
       1. **Summary Document:** `cat > "$COMPARISON_DIR/UPDATE_SUMMARY.md"` - Creates detailed markdown report
       2. **Status Indicators:** Uses ✅ and ❌ symbols for clear visual status
       3. **File Lists:** Separates new files, removed files, and full diff for detailed review
       4. **Next Steps:** Provides actionable post-update tasks
   - **Usage Workflow:**
     ```bash
     # After vendor update, run analysis
     bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh snapshots/before_update_20241201_143022
     
     # Review generated report
     cat comparisons/comparison_20241201_150022/UPDATE_SUMMARY.md
     ```
   - **Example Analysis Output:**
     ```
     🔍 Analyzing changes after vendor update...
     📊 Analyzing file changes...
     📁 New files added by vendor update: 5 files
     🗑️ Files removed by vendor update: 2 files
     🛡️ Verifying custom layer integrity...
     ✅ Custom layer intact: 15 files preserved
     📋 Generating update summary...
     ✅ Analysis complete: comparisons/comparison_20241201_150022/UPDATE_SUMMARY.md
     ```
   - **Critical Features:**
     1. **Custom Layer Protection:** Verifies `app/Custom/` files remain untouched
     2. **Change Tracking:** Documents all file additions and removals
     3. **Integrity Verification:** Ensures vendor updates don't break customizations
     4. **Detailed Reporting:** Creates comprehensive analysis for review

**Expected Result:** CodeCanyon application detected, license tracking system created, update tools installed with V1's advanced comparison capabilities.

---

**Next Step:** [Step 11: Setup Local Development Site](Step_11_Setup_Local_Dev_Site.md)
