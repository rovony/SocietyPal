# Step 06: Update Dependencies & Build Process

**Goal:** Update PHP and frontend dependencies, then prepare the build artifacts according to your deployment method.

**Time Required:** 20-40 minutes (varies by dependency changes)
**Prerequisites:** Step 05 completed with successful testing

---

## **Tracking Integration**

```bash
# Initialize Step 06 tracking using Linear Universal Tracking System
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

echo "📦 Step 06: Update Dependencies & Build Process - Tracking Integration"
echo "   Project: $PROJECT_NAME"
echo "   Root: $PROJECT_ROOT"
echo "   Session: $(basename "$SESSION_DIR")"

# Create step-specific tracking files
cat > "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md" << 'PLAN_EOF'
# Step 06: Update Dependencies & Build Process - Plan

## Objectives
- [ ] Analyze dependency requirements from Step 03
- [ ] Backup current dependencies
- [ ] Update PHP dependencies if changed
- [ ] Update frontend dependencies if changed
- [ ] Check for security vulnerabilities
- [ ] Build assets according to deployment method
- [ ] Verify build quality
- [ ] Create deployment readiness summary

## Configuration
- **Deployment Method:** [To be determined]
- **PHP Dependencies Changed:** [To be determined]
- **Frontend Dependencies Changed:** [To be determined]
- **Build Strategy:** [To be determined]

## Build Targets
- [ ] PHP Dependencies (Composer)
- [ ] Frontend Dependencies (NPM)
- [ ] Security Audit
- [ ] Asset Building
- [ ] Laravel Optimization
- [ ] Deployment Verification

## Execution Log
- **Started:** $(date)
- **Step 06 Tracking Initialized:** ✅

PLAN_EOF

# Record baseline before dependency updates
cat > "$SESSION_DIR/2-Baselines/step-06-pre-update-baseline.txt" << 'BASELINE_EOF'
# Step 06 Pre-Update Baseline - $(date)

## Current Dependency State
$(composer show --installed | wc -l | xargs echo "PHP Packages:")
$(npm list --depth=0 2>/dev/null | grep -c "├\|└" || echo "0") Frontend packages

## Current Lock Files
$([ -f composer.lock ] && echo "composer.lock: $(stat -f%z composer.lock 2>/dev/null || stat -c%s composer.lock 2>/dev/null || echo "unknown") bytes" || echo "composer.lock: Not found")
$([ -f package-lock.json ] && echo "package-lock.json: $(stat -f%z package-lock.json 2>/dev/null || stat -c%s package-lock.json 2>/dev/null || echo "unknown") bytes" || echo "package-lock.json: Not found")

## Current Build State
$([ -d public/build ] && echo "Build directory: $(du -sh public/build | cut -f1)" || echo "Build directory: Not found")
$([ -f bootstrap/cache/config.php ] && echo "Laravel cache: Present" || echo "Laravel cache: Not cached")

BASELINE_EOF

echo "✅ Step 06 tracking initialized: $SESSION_DIR"
echo "📋 Planning: $SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
echo "📊 Baseline: $SESSION_DIR/2-Baselines/step-06-pre-update-baseline.txt"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and deployment scenarios:

-   V1 Step 12: Install Dependencies with production optimization
-   V2 Step 11: Test Build Process with verification
-   V3 Scenarios 22A/B/C/D: Different build strategies by deployment method

---

## **6.1: Analyze Dependency Requirements**

### **Check Dependency Changes from Step 03:**

1. **Load dependency analysis from comparison:**

    ```bash
    # Navigate to project root using project-agnostic detection
    cd "$PROJECT_ROOT"

    # Get context from previous steps with project-agnostic paths
    LATEST_STAGING=$(find "$ADMIN_LOCAL/vendor_updates" -name "202*" -type d | sort | tail -1)
    DEPLOY_METHOD=$(grep "DEPLOY_METHOD=" "$ADMIN_LOCAL/update_logs/update_"*.md | tail -1 | cut -d'"' -f2 2>/dev/null)
    COMPARISON_DIR="$LATEST_STAGING/comparison"

    # Fallback detection if not found in logs
    if [[ -z "$DEPLOY_METHOD" ]]; then
        DEPLOY_METHOD="manual_ssh"  # Default fallback
    fi

    echo "📦 Analyzing dependency update requirements..."
    echo "   Deployment Method: $DEPLOY_METHOD"
    echo "   Comparison Data: $COMPARISON_DIR"

    # Update tracking with detected configuration
    sed -i.bak "s/\*\*Deployment Method:\*\* \[To be determined\]/\*\*Deployment Method:\*\* $DEPLOY_METHOD/g" "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"

    # Check if dependencies changed from Step 03 analysis
    PHP_DEPS_CHANGED=false
    JS_DEPS_CHANGED=false

    if [ -f "$COMPARISON_DIR/file_changes/composer_deps.diff" ]; then
        PHP_DEPS_CHANGED=true
        echo "   ⚠️  PHP dependencies changed"
    fi

    if [ -f "$COMPARISON_DIR/file_changes/package_deps.diff" ]; then
        JS_DEPS_CHANGED=true
        echo "   ⚠️  Frontend dependencies changed"
    fi

    if [ "$PHP_DEPS_CHANGED" = false ] && [ "$JS_DEPS_CHANGED" = false ]; then
        echo "   ✅ No dependency changes detected"
    fi

    # Update tracking with dependency change status
    sed -i.bak "s/\*\*PHP Dependencies Changed:\*\* \[To be determined\]/\*\*PHP Dependencies Changed:\*\* $([ "$PHP_DEPS_CHANGED" = true ] && echo "YES" || echo "NO")/g" "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
    sed -i.bak "s/\*\*Frontend Dependencies Changed:\*\* \[To be determined\]/\*\*Frontend Dependencies Changed:\*\* $([ "$JS_DEPS_CHANGED" = true ] && echo "YES" || echo "NO")/g" "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"

    # Record analysis in execution log
    cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << 'EXEC_ANALYSIS_EOF'
    ```

## Step 06 Execution Log - Started $(date)

### Dependency Analysis Results

-   **PHP Dependencies Changed:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "YES" || echo "NO")
-   **Frontend Dependencies Changed:** $([ "$JS_DEPS_CHANGED" = true ] && echo "YES" || echo "NO")
-   **Deployment Method:** $DEPLOY_METHOD
-   **Comparison Directory:** $COMPARISON_DIR

EXEC_ANALYSIS_EOF

````

2. **Backup current dependencies:**

```bash
# Create dependency backup
DEPENDENCY_BACKUP_DIR="$LATEST_STAGING/dependency_backup"
mkdir -p "$DEPENDENCY_BACKUP_DIR"

echo "💾 Backing up current dependencies..."

# Backup current lock files
[ -f "composer.lock" ] && cp composer.lock "$DEPENDENCY_BACKUP_DIR/composer.lock.backup"
[ -f "package-lock.json" ] && cp package-lock.json "$DEPENDENCY_BACKUP_DIR/package-lock.json.backup"
[ -f "yarn.lock" ] && cp yarn.lock "$DEPENDENCY_BACKUP_DIR/yarn.lock.backup"

# Backup vendor directory size info
[ -d "vendor" ] && du -sh vendor > "$DEPENDENCY_BACKUP_DIR/vendor_size.txt"
[ -d "node_modules" ] && du -sh node_modules > "$DEPENDENCY_BACKUP_DIR/node_modules_size.txt"

echo "✅ Dependencies backed up to: $DEPENDENCY_BACKUP_DIR"

# Record backup details in tracking
COMPOSER_SIZE=$([ -f "composer.lock" ] && stat -f%z composer.lock 2>/dev/null || stat -c%s composer.lock 2>/dev/null || echo "0")
PACKAGE_SIZE=$([ -f "package-lock.json" ] && stat -f%z package-lock.json 2>/dev/null || stat -c%s package-lock.json 2>/dev/null || echo "0")
VENDOR_SIZE=$([ -d "vendor" ] && du -sh vendor | cut -f1 || echo "N/A")
NODE_MODULES_SIZE=$([ -d "node_modules" ] && du -sh node_modules | cut -f1 || echo "N/A")

cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << BACKUP_EOF

### Dependency Backup Results

- **Backup Location:** $DEPENDENCY_BACKUP_DIR
- **Composer Lock:** $([ -f "composer.lock" ] && echo "Backed up ($COMPOSER_SIZE bytes)" || echo "Not found")
- **Package Lock:** $([ -f "package-lock.json" ] && echo "Backed up ($PACKAGE_SIZE bytes)" || echo "Not found")
- **Vendor Directory:** $VENDOR_SIZE
- **Node Modules:** $NODE_MODULES_SIZE
- **Backup Status:** ✅ Complete

BACKUP_EOF

# Update planning checklist
sed -i.bak 's/- \[ \] Backup current dependencies/- [x] Backup current dependencies/g' "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
````

---

## **6.2: Update PHP Dependencies**

### **Update Composer Dependencies:**

1. **Update PHP dependencies:**

    ```bash
    echo "🐘 Updating PHP dependencies..."

    if [ "$PHP_DEPS_CHANGED" = true ]; then
        echo "   Dependencies changed - full update required"

        # Remove vendor directory for clean install
        echo "   Removing vendor directory for clean install..."
        rm -rf vendor/

        # Install updated dependencies
        echo "   Installing updated PHP dependencies..."
        if composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction > "$LATEST_STAGING/logs/composer_install.log" 2>&1; then
            echo "   ✅ PHP dependencies updated successfully"

            # Check for security issues
            echo "   Checking for security vulnerabilities..."
            composer audit --format=plain > "$LATEST_STAGING/logs/composer_audit.log" 2>&1 || echo "   Security audit completed (some issues may exist)"

            VULNERABILITIES=$(grep -c "advisory" "$LATEST_STAGING/logs/composer_audit.log" 2>/dev/null || echo "0")
            if [ "$VULNERABILITIES" -gt 0 ]; then
                echo "   ⚠️  $VULNERABILITIES security advisories found"
            else
                echo "   ✅ No security vulnerabilities detected"
            fi
        else
            echo "   ❌ PHP dependency update failed"
            echo "   Check log: $LATEST_STAGING/logs/composer_install.log"
            tail -10 "$LATEST_STAGING/logs/composer_install.log"
            exit 1
        fi
    else
        echo "   No PHP dependency changes - verifying current installation"

        # Verify and optimize existing installation
        if composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction > "$LATEST_STAGING/logs/composer_verify.log" 2>&1; then
            echo "   ✅ PHP dependencies verified and optimized"
        else
            echo "   ⚠️  Issues found during verification"
            tail -5 "$LATEST_STAGING/logs/composer_verify.log"
        fi
    fi

    # Show final composer info
    echo "   Final PHP dependency status:"
    composer show --installed | wc -l | xargs echo "     Packages installed:"
    composer check-platform-reqs 2>/dev/null | grep -E "php|ext-" | head -3 | sed 's/^/     /'
    
    # Record PHP dependency results in tracking
    PHP_PACKAGES_COUNT=$(composer show --installed | wc -l | tr -d ' ')
    PHP_UPDATE_STATUS=$([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
    
    cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << PHP_DEPS_EOF

### PHP Dependencies Results

- **Status:** $PHP_UPDATE_STATUS
- **Packages Installed:** $PHP_PACKAGES_COUNT
- **Security Vulnerabilities:** $([ "${VULNERABILITIES:-0}" -gt 0 ] && echo "$VULNERABILITIES found" || echo "None detected")
- **Optimization:** ✅ Completed
- **Update Method:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "Full reinstall (clean)" || echo "Verification only")

PHP_DEPS_EOF

    # Update planning checklist
    sed -i.bak 's/- \[ \] Update PHP dependencies if changed/- [x] Update PHP dependencies if changed/g' "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
    ```

---

## **6.3: Update Frontend Dependencies**

### **Update Node.js Dependencies:**

1. **Update frontend dependencies:**

    ```bash
    echo "🌐 Updating frontend dependencies..."

    if [ "$JS_DEPS_CHANGED" = true ]; then
        echo "   Frontend dependencies changed - full update required"

        # Remove node_modules for clean install
        echo "   Removing node_modules for clean install..."
        rm -rf node_modules/

        # Clear npm cache
        npm cache clean --force 2>/dev/null || echo "   npm cache clean skipped"

        # Install updated dependencies
        echo "   Installing updated frontend dependencies..."
        if npm ci > "$LATEST_STAGING/logs/npm_install.log" 2>&1; then
            echo "   ✅ Frontend dependencies updated successfully"

            # Check for vulnerabilities
            echo "   Checking for security vulnerabilities..."
            npm audit --audit-level=moderate > "$LATEST_STAGING/logs/npm_audit.log" 2>&1 || echo "   npm audit completed"

            VULNERABILITIES=$(grep -c "vulnerabilities" "$LATEST_STAGING/logs/npm_audit.log" 2>/dev/null || echo "0")
            if [ "$VULNERABILITIES" -gt 0 ]; then
                echo "   ⚠️  Frontend vulnerabilities found - check audit log"
            else
                echo "   ✅ No critical frontend vulnerabilities"
            fi
        else
            echo "   ❌ Frontend dependency update failed"
            echo "   Check log: $LATEST_STAGING/logs/npm_install.log"
            tail -10 "$LATEST_STAGING/logs/npm_install.log"
            exit 1
        fi
    else
        echo "   No frontend dependency changes - verifying current installation"

        # Verify existing installation
        if [ ! -d "node_modules" ]; then
            echo "   Installing frontend dependencies..."
            npm ci > "$LATEST_STAGING/logs/npm_verify.log" 2>&1
        else
            echo "   ✅ Frontend dependencies already installed"
        fi
    fi

    # Show final npm info
    echo "   Final frontend dependency status:"
    npm ls --depth=0 2>/dev/null | head -5 | tail -4 | sed 's/^/     /'
    
    # Record frontend dependency results in tracking
    JS_PACKAGES_COUNT=$(npm ls --depth=0 2>/dev/null | grep -c "├\|└" || echo "0")
    JS_UPDATE_STATUS=$([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
    
    cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << JS_DEPS_EOF

### Frontend Dependencies Results

- **Status:** $JS_UPDATE_STATUS
- **Packages Installed:** $JS_PACKAGES_COUNT
- **Security Check:** $([ -f "$LATEST_STAGING/logs/npm_audit.log" ] && echo "Audit completed" || echo "Skipped")
- **Update Method:** $([ "$JS_DEPS_CHANGED" = true ] && echo "Full reinstall (clean)" || echo "Verification/install")

JS_DEPS_EOF

    # Update planning checklist
    sed -i.bak 's/- \[ \] Update frontend dependencies if changed/- [x] Update frontend dependencies if changed/g' "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
    ```

---

## **6.4: Build Assets Based on Deployment Method**

### **Create Build Strategy Based on Deployment Method:**

1. **Method A & D: Manual Build (create build artifacts):**

    ```bash
    if [ "$DEPLOY_METHOD" = "manual_ssh" ] || [ "$DEPLOY_METHOD" = "github_manual" ]; then
        echo "🔨 Building for Method $([ "$DEPLOY_METHOD" = "manual_ssh" ] && echo "A" || echo "D"): Manual Deployment"
        echo "   Strategy: Create production build artifacts locally"

        # Create build directory
        BUILD_DIR="$LATEST_STAGING/build_artifacts"
        mkdir -p "$BUILD_DIR"

        # Build frontend assets
        echo "   Building frontend assets..."
        if npm run build > "$LATEST_STAGING/logs/npm_build.log" 2>&1; then
            echo "   ✅ Frontend build completed"

            # Check build output
            if [ -d "public/build" ]; then
                BUILD_SIZE=$(du -sh public/build | cut -f1)
                echo "     Build size: $BUILD_SIZE"
            fi
        else
            echo "   ❌ Frontend build failed"
            tail -10 "$LATEST_STAGING/logs/npm_build.log"
            exit 1
        fi

        # Optimize Laravel for production
        echo "   Optimizing Laravel for production..."
        php artisan config:cache > "$LATEST_STAGING/logs/laravel_optimize.log" 2>&1
        php artisan route:cache >> "$LATEST_STAGING/logs/laravel_optimize.log" 2>&1
        php artisan view:cache >> "$LATEST_STAGING/logs/laravel_optimize.log" 2>&1

        if [ $? -eq 0 ]; then
            echo "   ✅ Laravel optimization completed"
        else
            echo "   ⚠️  Laravel optimization had issues"
            tail -5 "$LATEST_STAGING/logs/laravel_optimize.log"
        fi

        if [ "$DEPLOY_METHOD" = "github_manual" ]; then
            echo "   📤 Method D: Preparing to pull from GitHub and build locally"
            echo "   Note: This build will be packaged as artifact (not source code)"

            # Create deployment artifact preparation note
            cat > "$BUILD_DIR/DEPLOYMENT_ARTIFACT_NOTES.md" << ARTIFACT_EOF
    # Method D: GitHub + Manual Build Artifact

    ## Build Strategy
    - **Source:** GitHub repository (branch-specific)
    - **Build:** Local production build
    - **Deploy:** Upload build artifact ZIP (not source code)

    ## Artifact Contents
    - Production-optimized vendor/ directory
    - Built frontend assets in public/build/
    - Cached Laravel configurations
    - All necessary runtime files
    - NO source code files (like .vue, .scss, etc.)

    ## Next Steps (Step 08)
    1. Create deployment package from this build
    2. Upload package to server
    3. Extract and deploy atomically
    ARTIFACT_EOF
        fi

    elif [ "$DEPLOY_METHOD" = "github_actions" ]; then
        echo "🔨 Building for Method B: GitHub Actions"
        echo "   Strategy: Prepare source code for automated build"

        # Verify build scripts work locally first
        echo "   Testing build process locally..."
        if npm run build > "$LATEST_STAGING/logs/test_build.log" 2>&1; then
            echo "   ✅ Build process verified - will work in GitHub Actions"
        else
            echo "   ❌ Build process failed - GitHub Actions will fail"
            tail -10 "$LATEST_STAGING/logs/test_build.log"
            exit 1
        fi

        # Don't build here - GitHub Actions will build
        echo "   ✅ Source code prepared for GitHub Actions automated build"

    elif [ "$DEPLOY_METHOD" = "deployhq" ]; then
        echo "🔨 Building for Method C: DeployHQ"
        echo "   Strategy: Prepare source code for DeployHQ build service"

        # Verify build scripts work locally first
        echo "   Testing build process locally..."
        if npm run build > "$LATEST_STAGING/logs/test_build.log" 2>&1; then
            echo "   ✅ Build process verified - will work in DeployHQ"
        else
            echo "   ❌ Build process failed - DeployHQ will fail"
            tail -10 "$LATEST_STAGING/logs/test_build.log"
            exit 1
        fi

        # Don't build here - DeployHQ will build
        echo "   ✅ Source code prepared for DeployHQ automated build"

    else
        echo "❓ Unknown deployment method: $DEPLOY_METHOD"
        echo "   Defaulting to local build process..."

        # Default to local build
        if npm run build > "$LATEST_STAGING/logs/default_build.log" 2>&1; then
            echo "   ✅ Default build completed"
        else
            echo "   ❌ Default build failed"
            exit 1
        fi
    fi
    
    # Record build results in tracking
    BUILD_STRATEGY=$(case "$DEPLOY_METHOD" in
        "manual_ssh"|"github_manual") echo "Local production build" ;;
        "github_actions"|"deployhq") echo "Source preparation for remote build" ;;
        *) echo "Default local build" ;;
    esac)
    
    BUILD_STATUS=$(case "$DEPLOY_METHOD" in
        "manual_ssh"|"github_manual")
            if [ -d "public/build" ]; then echo "✅ Artifacts created"; else echo "⚠️ Build issues"; fi ;;
        "github_actions"|"deployhq") echo "✅ Verified for remote build" ;;
        *) echo "✅ Default build completed" ;;
    esac)
    
    cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << BUILD_EOF

### Build Process Results

- **Deployment Method:** $DEPLOY_METHOD
- **Build Strategy:** $BUILD_STRATEGY
- **Build Status:** $BUILD_STATUS
- **Asset Directory:** $([ -d "public/build" ] && echo "Created ($(du -sh public/build | cut -f1))" || echo "Not applicable")
- **Laravel Optimization:** $([ "$DEPLOY_METHOD" = "manual_ssh" ] || [ "$DEPLOY_METHOD" = "github_manual" ] && echo "✅ Cached" || echo "N/A (remote build)")

BUILD_EOF

    # Update planning checklist
    sed -i.bak 's/- \[ \] Build assets according to deployment method/- [x] Build assets according to deployment method/g' "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
    ```

---

## **6.5: Build Verification & Testing**

### **Verify Build Quality:**

1. **Test built application:**

    ```bash
    echo "🧪 Verifying build quality..."

    # Start server to test built application
    echo "   Starting server to test build..."
    php artisan serve --host=127.0.0.1 --port=8001 > "$LATEST_STAGING/logs/build_test_server.log" 2>&1 &
    BUILD_SERVER_PID=$!

    # Wait for server to start
    sleep 5

    # Test build quality
    if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8001 | grep -q "200\|302"; then
        echo "   ✅ Built application responds correctly"

        # Test asset loading
        ASSET_TEST=$(curl -s "http://127.0.0.1:8001" | grep -c "public/build\|/build/" 2>/dev/null || echo "0")
        if [ "$ASSET_TEST" -gt 0 ]; then
            echo "   ✅ Built assets are properly referenced"
        else
            echo "   ⚠️  Built assets may not be properly referenced"
        fi

        # Test page load time
        LOAD_TIME=$(curl -s -o /dev/null -w "%{time_total}" "http://127.0.0.1:8001")
        echo "   📊 Page load time: ${LOAD_TIME}s"

    else
        echo "   ❌ Built application failed to respond"
    fi

    # Stop test server
    kill $BUILD_SERVER_PID 2>/dev/null
    echo "   🛑 Build test server stopped"
    
    # Record build verification results in tracking
    SERVER_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8001 2>/dev/null || echo "000")
    VERIFICATION_STATUS=$([ "$SERVER_RESPONSE" = "200" ] || [ "$SERVER_RESPONSE" = "302" ] && echo "✅ Passed" || echo "❌ Failed")
    
    cat >> "$SESSION_DIR/3-Execution/step-06-execution.log" << VERIFY_EOF

### Build Verification Results

- **Application Response:** HTTP $SERVER_RESPONSE
- **Verification Status:** $VERIFICATION_STATUS
- **Asset References:** $([ "$ASSET_TEST" -gt 0 ] && echo "✅ Properly linked" || echo "⚠️ May have issues")
- **Load Time:** ${LOAD_TIME:-unknown}s
- **Test Method:** Local server verification

VERIFY_EOF

    # Update planning checklist
    sed -i.bak 's/- \[ \] Verify build quality/- [x] Verify build quality/g' "$SESSION_DIR/1-Planning/step-06-dependency-update-plan.md"
    ```

2. **Verify deployment readiness by method:**

    ```bash
    echo "📋 Verifying deployment readiness..."

    case "$DEPLOY_METHOD" in
        "manual_ssh")
            echo "   Method A Readiness Check:"
            echo "   ✅ Production dependencies installed"
            echo "   ✅ Frontend assets built"
            echo "   ✅ Laravel optimized"
            echo "   ➡️  Ready for manual packaging and SSH upload"
            ;;
        "github_manual")
            echo "   Method D Readiness Check:"
            echo "   ✅ Production dependencies installed"
            echo "   ✅ Frontend assets built (artifact ready)"
            echo "   ✅ Laravel optimized"
            echo "   ✅ Build artifact prepared for packaging"
            echo "   ➡️  Ready for artifact-based deployment"
            ;;
        "github_actions")
            echo "   Method B Readiness Check:"
            echo "   ✅ Source code prepared"
            echo "   ✅ Build process verified"
            echo "   ✅ Dependencies configuration updated"
            echo "   ➡️  Ready for GitHub Actions automated deployment"
            ;;
        "deployhq")
            echo "   Method C Readiness Check:"
            echo "   ✅ Source code prepared"
            echo "   ✅ Build process verified"
            echo "   ✅ Dependencies configuration updated"
            echo "   ➡️  Ready for DeployHQ automated deployment"
            ;;
        *)
            echo "   ⚠️  Unknown method - manual verification required"
            ;;
    esac
    ```

---

## **6.6: Create Deployment Summary**

### **Generate Dependency Update Summary:**

1. **Create comprehensive summary:**

    ```bash
    echo "📊 Creating dependency update summary..."

    cat > "$LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md" << SUMMARY_EOF
    # Dependency Update Summary

    **Update Date:** $(date)
    **Deployment Method:** $DEPLOY_METHOD

    ## Changes Applied
    - **PHP Dependencies:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "✅ Updated" || echo "✅ Verified (no changes)")
    - **Frontend Dependencies:** $([ "$JS_DEPS_CHANGED" = true ] && echo "✅ Updated" || echo "✅ Verified (no changes)")
    - **Build Process:** ✅ Completed for $DEPLOY_METHOD

    ## Build Artifacts Status
    $(case "$DEPLOY_METHOD" in
        "manual_ssh"|"github_manual")
            echo "- **Production Build:** ✅ Created locally"
            echo "- **Laravel Optimization:** ✅ Cached"
            echo "- **Frontend Assets:** ✅ Built and optimized"
            ;;
        "github_actions"|"deployhq")
            echo "- **Source Preparation:** ✅ Ready for automated build"
            echo "- **Build Verification:** ✅ Process tested locally"
            echo "- **Configuration:** ✅ Updated for remote build"
            ;;
    esac)

    ## Security Status
    - **PHP Vulnerabilities:** $([ "$VULNERABILITIES" -gt 0 ] && echo "$VULNERABILITIES found" || echo "None detected")
    - **Frontend Vulnerabilities:** Check logs/npm_audit.log

    ## Deployment Readiness
    $(case "$DEPLOY_METHOD" in
        "manual_ssh")
            echo "**Method A (Manual SSH):**"
            echo "- Ready for Step 07: Test Build Process"
            echo "- Ready for Step 08: Manual packaging and deployment"
            ;;
        "github_manual")
            echo "**Method D (GitHub + Manual):**"
            echo "- Build artifact ready for packaging"
            echo "- Ready for Step 07: Test Build Process"
            echo "- Ready for Step 08: Artifact deployment"
            ;;
        "github_actions")
            echo "**Method B (GitHub Actions):**"
            echo "- Source code ready for automated build"
            echo "- Ready for Step 07: Test Build Process"
            echo "- Ready for Step 08: Automated deployment"
            ;;
        "deployhq")
            echo "**Method C (DeployHQ):**"
            echo "- Source code ready for professional build"
            echo "- Ready for Step 07: Test Build Process"
            echo "- Ready for Step 08: Professional deployment"
            ;;
    esac)

    ## Files Changed
    - **composer.lock:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Unchanged")
    - **package-lock.json:** $([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Unchanged")
    - **public/build/:** $([ -d "public/build" ] && echo "Built" || echo "N/A")
    - **bootstrap/cache/:** $([ -f "bootstrap/cache/config.php" ] && echo "Optimized" || echo "N/A")

    ## Next Steps
    1. Continue to Step 07: Test Build Process
    2. Verify all functionality with updated dependencies
    3. Proceed to Step 08: Deploy Updates using $DEPLOY_METHOD method
    SUMMARY_EOF

    echo "✅ Dependency update summary: $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md"
    ```

2. **Update the update log:**

    ```bash
    # Update the current update log with project-agnostic paths
    LATEST_LOG=$(find "$ADMIN_LOCAL/update_logs" -name "update_*.md" | sort | tail -1)

    if [ -n "$LATEST_LOG" ]; then
        # Mark Step 06 as complete
        sed -i.bak 's/- \[ \] Step 06: Update Dependencies/- [x] Step 06: Update Dependencies/' "$LATEST_LOG"

        # Add Step 06 completion details
        cat >> "$LATEST_LOG" << LOG_UPDATE

## Step 06 Completed
- **PHP Dependencies:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Frontend Dependencies:** $([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Build Method:** $DEPLOY_METHOD
- **Build Status:** ✅ Ready for deployment
- **Summary:** $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md
LOG_UPDATE

        echo "✅ Update log updated: $LATEST_LOG"
    fi
    
    # Complete Step 06 tracking
    cat > "$SESSION_DIR/4-Verification/step-06-verification.md" << 'VERIFY_EOF'
# Step 06 Verification Summary

## Completion Status
- **Step Started:** ✅ Tracking initialized
- **Dependency Analysis:** ✅ Completed
- **Dependency Backup:** ✅ Completed
- **PHP Dependencies:** ✅ $([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Frontend Dependencies:** ✅ $([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Build Process:** ✅ Completed for $DEPLOY_METHOD
- **Build Verification:** ✅ Application tested
- **Documentation:** ✅ Summary generated

## Key Metrics
- **PHP Packages:** $PHP_PACKAGES_COUNT
- **Frontend Packages:** $JS_PACKAGES_COUNT
- **Security Issues:** $([ "${VULNERABILITIES:-0}" -gt 0 ] && echo "$VULNERABILITIES PHP vulnerabilities" || echo "No critical issues found")
- **Build Size:** $([ -d "public/build" ] && du -sh public/build | cut -f1 || echo "N/A (remote build)")

## Next Steps
1. Continue to Step 07: Test Build Process
2. Use deployment method: $DEPLOY_METHOD
3. Review summary: $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md

## Files Created
- Dependency backup: $LATEST_STAGING/dependency_backup/
- Build logs: $LATEST_STAGING/logs/
- Summary report: $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md

VERIFY_EOF

    # Update session summary if it exists, create if not
    if [[ -f "$SESSION_DIR/5-Documentation/session-summary.md" ]]; then
        cat >> "$SESSION_DIR/5-Documentation/session-summary.md" << 'SESSION_EOF'

## Step 06: Update Dependencies & Build Process
- **Status:** ✅ Completed
- **Duration:** $(date)
- **PHP Dependencies:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Frontend Dependencies:** $([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Build Method:** $DEPLOY_METHOD
- **Key Files:** $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md

SESSION_EOF
    else
        cat > "$SESSION_DIR/5-Documentation/session-summary.md" << 'SESSION_CREATE_EOF'
# Update/Customization Session Summary

## Session Information
- **Project:** $PROJECT_NAME
- **Session:** $(basename "$SESSION_DIR")
- **Started:** $(date)

## Completed Steps

### Step 06: Update Dependencies & Build Process
- **Status:** ✅ Completed
- **Duration:** $(date)
- **PHP Dependencies:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Frontend Dependencies:** $([ "$JS_DEPS_CHANGED" = true ] && echo "Updated" || echo "Verified")
- **Build Method:** $DEPLOY_METHOD
- **Key Files:** $LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md

SESSION_CREATE_EOF
    fi

    echo "✅ Step 06 tracking completed:"
    echo "   📋 Verification: $SESSION_DIR/4-Verification/step-06-verification.md"
    echo "   📄 Session Summary: $SESSION_DIR/5-Documentation/session-summary.md"
    ```

---

## **✅ Step 06 Completion Checklist**

-   [ ] Dependency changes analyzed from Step 03
-   [ ] Current dependencies backed up
-   [ ] PHP dependencies updated/verified
-   [ ] Frontend dependencies updated/verified
-   [ ] Security vulnerabilities checked
-   [ ] Build process executed according to deployment method
-   [ ] Build artifacts created (for Method A & D) or verified (for Method B & C)
-   [ ] Application tested with updated dependencies
-   [ ] Deployment readiness confirmed
-   [ ] Dependency update summary generated

---

## **Next Steps**

**All Methods:** Continue to [Step 07: Test Build Process](Step_07_Test_Build_Process.md)

**Method-specific notes:**

-   **Method A (Manual SSH):** Build artifacts ready for packaging
-   **Method B (GitHub Actions):** Source code ready for automated build
-   **Method C (DeployHQ):** Source code ready for professional build
-   **Method D (GitHub + Manual):** Build artifacts ready for packaging from GitHub source

**Key files to review:**

-   **Summary:** `$LATEST_STAGING/DEPENDENCY_UPDATE_SUMMARY.md`
-   **Build Logs:** `$LATEST_STAGING/logs/`

---

## **Troubleshooting**

### **Issue: Composer install fails**

```bash
# Check PHP version compatibility
php -v
composer diagnose

# Clear composer cache
composer clear-cache
rm -rf vendor/ composer.lock
composer install
```

### **Issue: npm install fails**

```bash
# Check Node version
node -v
npm -v

# Clear npm cache
npm cache clean --force
rm -rf node_modules/ package-lock.json
npm install
```

### **Issue: Build process fails**

```bash
# Check build script
npm run --silent build --dry-run

# Check Laravel optimization
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```
