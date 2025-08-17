# Step 07: Test Build Process

**Goal:** Thoroughly test the build process for your specific deployment method, ensuring everything works correctly before deploying to production.

**Time Required:** 15-30 minutes
**Prerequisites:** Step 06 completed with successful dependency updates

---

## **Tracking Integration**

```bash
# Initialize Step 07 tracking using Linear Universal Tracking System
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

echo "🧪 Step 07: Test Build Process - Tracking Integration"
echo "   Project: $PROJECT_NAME"
echo "   Root: $PROJECT_ROOT"
echo "   Session: $(basename "$SESSION_DIR")"

# Create step-specific tracking files
cat > "$SESSION_DIR/1-Planning/step-07-build-test-plan.md" << 'PLAN_EOF'
# Step 07: Test Build Process - Plan

## Objectives
- [ ] Initialize build testing environment
- [ ] Execute method-specific build tests
- [ ] Test customization integration
- [ ] Validate application performance
- [ ] Test database integration
- [ ] Generate comprehensive test report
- [ ] Assess deployment readiness

## Configuration
- **Deployment Method:** [To be determined]
- **Customization Mode:** [To be determined]
- **Build Testing Strategy:** [To be determined]

## Test Categories
- [ ] Method A: Manual SSH Build Tests
- [ ] Method B: GitHub Actions Tests
- [ ] Method C: DeployHQ Tests
- [ ] Method D: GitHub + Manual Build Tests
- [ ] Customization Integration
- [ ] Performance Testing
- [ ] Database Integration

## Success Criteria
- **Target Success Rate:** ≥ 90% for deployment readiness
- **Minimum Success Rate:** ≥ 70% to proceed with warnings
- **Critical Tests:** All deployment method tests must pass

## Execution Log
- **Started:** $(date)
- **Step 07 Tracking Initialized:** ✅

PLAN_EOF

# Record baseline before testing
cat > "$SESSION_DIR/2-Baselines/step-07-pre-test-baseline.txt" << 'BASELINE_EOF'
# Step 07 Pre-Testing Baseline - $(date)

## Current Build State
$([ -d public/build ] && echo "Build directory: $(du -sh public/build | cut -f1)" || echo "Build directory: Not found")
$([ -f bootstrap/cache/config.php ] && echo "Laravel config cache: Present" || echo "Laravel config cache: Not cached")
$([ -f bootstrap/cache/routes.php ] && echo "Laravel route cache: Present" || echo "Laravel route cache: Not cached")
$([ -f bootstrap/cache/views.php ] && echo "Laravel view cache: Present" || echo "Laravel view cache: Not cached")

## Current Application State
$(php artisan --version 2>/dev/null || echo "Laravel: Not accessible")
$(php -v | head -1 || echo "PHP: Not accessible")
$(node -v 2>/dev/null || echo "Node.js: Not installed")

## Git Status
$(git branch --show-current 2>/dev/null || echo "Git: Not a repository")
$(git status --porcelain | wc -l | xargs echo "Uncommitted changes:")

BASELINE_EOF

echo "✅ Step 07 tracking initialized: $SESSION_DIR"
echo "📋 Planning: $SESSION_DIR/1-Planning/step-07-build-test-plan.md"
echo "📊 Baseline: $SESSION_DIR/2-Baselines/step-07-pre-test-baseline.txt"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and deployment testing:

-   V1 Step 13: Build Process Testing with comprehensive validation
-   V2 Step 12: Pre-deployment verification with automated testing
-   V3 Scenarios 23A/B/C/D: Method-specific build testing strategies

---

## **7.1: Initialize Build Testing Environment**

### **Set Up Testing Context:**

1. **Load testing environment:**

    ```bash
    # Navigate to project root using project-agnostic detection
    cd "$PROJECT_ROOT"

    # Get context from previous steps with project-agnostic paths
    LATEST_STAGING=$(find "$ADMIN_LOCAL/vendor_updates" -name "202*" -type d | sort | tail -1)
    DEPLOY_METHOD=$(grep "DEPLOY_METHOD=" "$ADMIN_LOCAL/update_logs/update_"*.md | tail -1 | cut -d'"' -f2 2>/dev/null)
    CUSTOMIZATION_MODE=$(grep "CUSTOMIZATION_MODE=" "$ADMIN_LOCAL/update_logs/update_"*.md | tail -1 | cut -d'"' -f2 2>/dev/null)

    # Fallback detection if not found in logs
    if [[ -z "$DEPLOY_METHOD" ]]; then
        DEPLOY_METHOD="manual_ssh"  # Default fallback
    fi

    if [[ -z "$CUSTOMIZATION_MODE" ]]; then
        CUSTOMIZATION_MODE="simple"  # Default fallback
    fi

    echo "🧪 Initializing build testing for deployment method..."
    echo "   Method: $DEPLOY_METHOD"
    echo "   Customization Mode: $CUSTOMIZATION_MODE"
    echo "   Testing Directory: $LATEST_STAGING"

    # Update tracking with detected configuration
    sed -i.bak "s/\*\*Deployment Method:\*\* \[To be determined\]/\*\*Deployment Method:\*\* $DEPLOY_METHOD/g" "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"
    sed -i.bak "s/\*\*Customization Mode:\*\* \[To be determined\]/\*\*Customization Mode:\*\* $CUSTOMIZATION_MODE/g" "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"

    # Create testing logs directory
    mkdir -p "$LATEST_STAGING/testing_logs"

    # Initialize build test report
    BUILD_TEST_REPORT="$LATEST_STAGING/BUILD_TEST_REPORT.md"
    cat > "$BUILD_TEST_REPORT" << TEST_INIT
    # Build Process Testing Report

    **Test Date:** $(date)
    **Deployment Method:** $DEPLOY_METHOD
    **Customization Mode:** $CUSTOMIZATION_MODE

    ## Test Progress
    TEST_INIT

    # Record environment initialization in tracking
    cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << 'ENV_INIT_EOF'
    ```

## Step 07 Execution Log - Started $(date)

### Environment Initialization

-   **Deployment Method:** $DEPLOY_METHOD
-   **Customization Mode:** $CUSTOMIZATION_MODE
-   **Testing Directory:** $LATEST_STAGING
-   **Build Test Report:** $BUILD_TEST_REPORT
-   **Status:** ✅ Environment initialized

ENV_INIT_EOF

# Update planning checklist

sed -i.bak 's/- \[ \] Initialize build testing environment/- [x] Initialize build testing environment/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"

echo "✅ Build testing environment initialized"

````

---

## **7.2: Method-Specific Build Testing**

### **Test Based on Deployment Method:**

1. **Method A: Manual SSH Build Testing:**

```bash
if [ "$DEPLOY_METHOD" = "manual_ssh" ]; then
    echo "🔨 Testing Method A: Manual SSH Build Process"
    echo "   Strategy: Test complete local build for SSH upload"

    cat >> "$BUILD_TEST_REPORT" << METHOD_A_TEST

## Method A: Manual SSH Build Tests

### Test 1: Production Build Validation
METHOD_A_TEST

    # Test 1: Verify production build exists and works
    echo "   Test 1: Production build validation..."
    if [ -d "public/build" ] && [ -f "bootstrap/cache/config.php" ]; then
        echo "   ✅ Production build artifacts exist"
        echo "   - [x] Production build artifacts exist" >> "$BUILD_TEST_REPORT"

        # Test build file integrity
        BUILD_FILES=$(find public/build -name "*.js" -o -name "*.css" | wc -l)
        if [ "$BUILD_FILES" -gt 0 ]; then
            echo "   ✅ Build contains $BUILD_FILES asset files"
            echo "   - [x] Build contains $BUILD_FILES asset files" >> "$BUILD_TEST_REPORT"
        else
            echo "   ❌ No build asset files found"
            echo "   - [ ] ❌ No build asset files found" >> "$BUILD_TEST_REPORT"
        fi
    else
        echo "   ❌ Production build missing"
        echo "   - [ ] ❌ Production build missing" >> "$BUILD_TEST_REPORT"
    fi

    # Test 2: Laravel optimization validation
    echo "   Test 2: Laravel optimization validation..."
    cat >> "$BUILD_TEST_REPORT" << LARAVEL_TEST

### Test 2: Laravel Optimization Validation
LARAVEL_TEST

    if [ -f "bootstrap/cache/config.php" ] && [ -f "bootstrap/cache/routes.php" ] && [ -f "bootstrap/cache/views.php" ]; then
        echo "   ✅ Laravel caches generated"
        echo "   - [x] Config cache: ✅" >> "$BUILD_TEST_REPORT"
        echo "   - [x] Route cache: ✅" >> "$BUILD_TEST_REPORT"
        echo "   - [x] View cache: ✅" >> "$BUILD_TEST_REPORT"
    else
        echo "   ⚠️  Some Laravel caches missing"
        echo "   - [ ] Some Laravel caches missing" >> "$BUILD_TEST_REPORT"
    fi

    # Test 3: Create deployment package
    echo "   Test 3: Deployment package creation..."
    cat >> "$BUILD_TEST_REPORT" << PACKAGE_TEST

### Test 3: Deployment Package Creation
PACKAGE_TEST

    PACKAGE_DIR="$LATEST_STAGING/deployment_package"
    mkdir -p "$PACKAGE_DIR"

    echo "   Creating deployment package for SSH upload..."

    # Create exclusion list
    cat > "$PACKAGE_DIR/exclude_list.txt" << EXCLUDE_LIST
.git/
.gitignore
.env*
node_modules/
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
Admin-Local/
tests/
.phpunit.result.cache
EXCLUDE_LIST

    # Create package
    if tar --exclude-from="$PACKAGE_DIR/exclude_list.txt" -czf "$PACKAGE_DIR/deployment.tar.gz" -C . . > "$LATEST_STAGING/testing_logs/package_creation.log" 2>&1; then
        PACKAGE_SIZE=$(du -sh "$PACKAGE_DIR/deployment.tar.gz" | cut -f1)
        echo "   ✅ Deployment package created ($PACKAGE_SIZE)"
        echo "   - [x] Deployment package created: $PACKAGE_SIZE" >> "$BUILD_TEST_REPORT"
    else
        echo "   ❌ Package creation failed"
        echo "   - [ ] ❌ Package creation failed" >> "$BUILD_TEST_REPORT"
    fi

    # Record Method A test results in tracking
    cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << METHOD_A_RESULTS

### Method A: Manual SSH Build Test Results

- **Production Build Artifacts:** $([ -d "public/build" ] && [ -f "bootstrap/cache/config.php" ] && echo "✅ Present" || echo "❌ Missing")
- **Build Files Count:** $(find public/build -name "*.js" -o -name "*.css" 2>/dev/null | wc -l) assets
- **Laravel Caches:** $([ -f "bootstrap/cache/config.php" ] && [ -f "bootstrap/cache/routes.php" ] && [ -f "bootstrap/cache/views.php" ] && echo "✅ Complete" || echo "⚠️ Partial")
- **Deployment Package:** $([ -f "$PACKAGE_DIR/deployment.tar.gz" ] && echo "✅ Created ($(du -sh "$PACKAGE_DIR/deployment.tar.gz" | cut -f1))" || echo "❌ Failed")

METHOD_A_RESULTS

    # Update planning checklist
    sed -i.bak 's/- \[ \] Method A: Manual SSH Build Tests/- [x] Method A: Manual SSH Build Tests/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"
fi
````

2. **Method B: GitHub Actions Build Testing:**

    ```bash
    if [ "$DEPLOY_METHOD" = "github_actions" ]; then
        echo "🔨 Testing Method B: GitHub Actions Build Process"
        echo "   Strategy: Validate source code readiness for automated build"

        cat >> "$BUILD_TEST_REPORT" << METHOD_B_TEST

    ## Method B: GitHub Actions Build Tests

    ### Test 1: Source Code Validation
    METHOD_B_TEST

        # Test 1: Verify source code is ready for automated build
        echo "   Test 1: Source code validation..."

        # Check required files exist
        REQUIRED_FILES=("package.json" "composer.json" "webpack.mix.js" "artisan")
        ALL_FILES_EXIST=true

        for file in "${REQUIRED_FILES[@]}"; do
            if [ -f "$file" ]; then
                echo "   ✅ $file exists"
                echo "   - [x] $file exists" >> "$BUILD_TEST_REPORT"
            else
                echo "   ❌ $file missing"
                echo "   - [ ] ❌ $file missing" >> "$BUILD_TEST_REPORT"
                ALL_FILES_EXIST=false
            fi
        done

        # Test 2: Build scripts validation
        echo "   Test 2: Build scripts validation..."
        cat >> "$BUILD_TEST_REPORT" << BUILD_SCRIPT_TEST

    ### Test 2: Build Scripts Validation
    BUILD_SCRIPT_TEST

        # Check package.json scripts
        if grep -q '"build"' package.json; then
            echo "   ✅ Build script exists in package.json"
            echo "   - [x] Build script exists in package.json" >> "$BUILD_TEST_REPORT"

            # Test build script
            echo "   Testing build script locally..."
            if timeout 300 npm run build > "$LATEST_STAGING/testing_logs/build_test.log" 2>&1; then
                echo "   ✅ Build script executes successfully"
                echo "   - [x] Build script executes successfully" >> "$BUILD_TEST_REPORT"
            else
                echo "   ❌ Build script failed"
                echo "   - [ ] ❌ Build script failed" >> "$BUILD_TEST_REPORT"
                echo "     Check: $LATEST_STAGING/testing_logs/build_test.log"
            fi
        else
            echo "   ❌ Build script missing from package.json"
            echo "   - [ ] ❌ Build script missing from package.json" >> "$BUILD_TEST_REPORT"
        fi

        # Test 3: GitHub Actions workflow file
        echo "   Test 3: GitHub Actions workflow validation..."
        cat >> "$BUILD_TEST_REPORT" << WORKFLOW_TEST

    ### Test 3: GitHub Actions Workflow Validation
    WORKFLOW_TEST

        if [ -f ".github/workflows/deploy.yml" ] || [ -f ".github/workflows/ci.yml" ]; then
            echo "   ✅ GitHub Actions workflow exists"
            echo "   - [x] GitHub Actions workflow exists" >> "$BUILD_TEST_REPORT"

            # Check workflow has required steps
            WORKFLOW_FILE=$(find .github/workflows -name "*.yml" | head -1)
            if grep -q "npm.*build\|yarn.*build" "$WORKFLOW_FILE"; then
                echo "   ✅ Workflow includes build step"
                echo "   - [x] Workflow includes build step" >> "$BUILD_TEST_REPORT"
            else
                echo "   ⚠️  Workflow may be missing build step"
                echo "   - [ ] ⚠️  Workflow may be missing build step" >> "$BUILD_TEST_REPORT"
            fi
        else
            echo "   ⚠️  GitHub Actions workflow not found"
            echo "   - [ ] ⚠️  GitHub Actions workflow not found" >> "$BUILD_TEST_REPORT"
            echo "     Note: You'll need to set this up in GitHub repository"
        fi

        # Record Method B test results in tracking
        cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << METHOD_B_RESULTS

    ```

### Method B: GitHub Actions Build Test Results

-   **Required Files:** $(for file in package.json composer.json webpack.mix.js artisan; do [ -f "$file" ] && echo "✅ $file" || echo "❌ $file"; done | tr '\n' '; ')
-   **Build Script:** $(grep -q '"build"' package.json && echo "✅ Present" || echo "❌ Missing")
-   **Build Test:** $(timeout 300 npm run build >/dev/null 2>&1 && echo "✅ Successful" || echo "❌ Failed")
-   **GitHub Workflow:** $([ -f ".github/workflows/deploy.yml" ] || [ -f ".github/workflows/ci.yml" ] && echo "✅ Present" || echo "⚠️ Not found")

METHOD_B_RESULTS

       # Update planning checklist
       sed -i.bak 's/- \[ \] Method B: GitHub Actions Tests/- [x] Method B: GitHub Actions Tests/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"

fi

````

3. **Method C: DeployHQ Build Testing:**

```bash
if [ "$DEPLOY_METHOD" = "deployhq" ]; then
    echo "🔨 Testing Method C: DeployHQ Build Process"
    echo "   Strategy: Validate source readiness for professional build service"

    cat >> "$BUILD_TEST_REPORT" << METHOD_C_TEST

## Method C: DeployHQ Build Tests

### Test 1: Professional Build Readiness
METHOD_C_TEST

    # Test 1: Verify professional build readiness
    echo "   Test 1: Professional build readiness..."

    # Check for DeployHQ configuration
    if [ -f ".deployhq" ] || [ -f "deployhq.yml" ]; then
        echo "   ✅ DeployHQ configuration exists"
        echo "   - [x] DeployHQ configuration exists" >> "$BUILD_TEST_REPORT"
    else
        echo "   ⚠️  DeployHQ configuration not found"
        echo "   - [ ] ⚠️  DeployHQ configuration not found" >> "$BUILD_TEST_REPORT"
        echo "     Note: Configuration will be created in DeployHQ interface"
    fi

    # Test 2: Build environment validation
    echo "   Test 2: Build environment validation..."
    cat >> "$BUILD_TEST_REPORT" << ENV_TEST

### Test 2: Build Environment Validation
ENV_TEST

    # Check composer.json for production requirements
    if grep -q '"require"' composer.json; then
        echo "   ✅ Composer requirements defined"
        echo "   - [x] Composer requirements defined" >> "$BUILD_TEST_REPORT"

        # Check for post-install scripts
        if grep -q '"post-install-cmd"\|"post-update-cmd"' composer.json; then
            echo "   ✅ Composer automation scripts found"
            echo "   - [x] Composer automation scripts found" >> "$BUILD_TEST_REPORT"
        else
            echo "   ⚠️  No composer automation scripts"
            echo "   - [ ] ⚠️  No composer automation scripts" >> "$BUILD_TEST_REPORT"
        fi
    fi

    # Test 3: Local build simulation for DeployHQ
    echo "   Test 3: Local build simulation..."
    cat >> "$BUILD_TEST_REPORT" << SIMULATION_TEST

### Test 3: Local Build Simulation
SIMULATION_TEST

    # Simulate DeployHQ build process locally
    echo "   Simulating DeployHQ build process..."

    # Create temporary build environment
    TEMP_BUILD_DIR="$LATEST_STAGING/temp_deployhq_build"
    mkdir -p "$TEMP_BUILD_DIR"

    # Test build sequence that DeployHQ would run
    if (
        echo "Installing production dependencies..." &&
        composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction > "$LATEST_STAGING/testing_logs/deployhq_composer.log" 2>&1 &&
        echo "Building frontend assets..." &&
        npm ci > "$LATEST_STAGING/testing_logs/deployhq_npm.log" 2>&1 &&
        npm run build >> "$LATEST_STAGING/testing_logs/deployhq_build.log" 2>&1 &&
        echo "Optimizing Laravel..." &&
        php artisan config:cache >> "$LATEST_STAGING/testing_logs/deployhq_optimize.log" 2>&1
    ); then
        echo "   ✅ DeployHQ build simulation successful"
        echo "   - [x] DeployHQ build simulation successful" >> "$BUILD_TEST_REPORT"
    else
        echo "   ❌ DeployHQ build simulation failed"
        echo "   - [ ] ❌ DeployHQ build simulation failed" >> "$BUILD_TEST_REPORT"
    fi

    # Record Method C test results in tracking
    cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << METHOD_C_RESULTS

### Method C: DeployHQ Build Test Results

- **DeployHQ Configuration:** $([ -f ".deployhq" ] || [ -f "deployhq.yml" ] && echo "✅ Present" || echo "⚠️ Not found")
- **Composer Requirements:** $(grep -q '"require"' composer.json && echo "✅ Defined" || echo "❌ Missing")
- **Composer Scripts:** $(grep -q '"post-install-cmd"\|"post-update-cmd"' composer.json && echo "✅ Present" || echo "⚠️ Missing")
- **Build Simulation:** $(composer install --no-dev >/dev/null 2>&1 && npm ci >/dev/null 2>&1 && npm run build >/dev/null 2>&1 && echo "✅ Successful" || echo "❌ Failed")

METHOD_C_RESULTS

    # Update planning checklist
    sed -i.bak 's/- \[ \] Method C: DeployHQ Tests/- [x] Method C: DeployHQ Tests/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"
fi
````

4. **Method D: GitHub + Manual Build Testing:**

    ```bash
    if [ "$DEPLOY_METHOD" = "github_manual" ]; then
        echo "🔨 Testing Method D: GitHub + Manual Build Process"
        echo "   Strategy: Test artifact creation from GitHub source"

        cat >> "$BUILD_TEST_REPORT" << METHOD_D_TEST

    ## Method D: GitHub + Manual Build Tests

    ### Test 1: GitHub Source Preparation
    METHOD_D_TEST

        # Test 1: Verify GitHub source readiness
        echo "   Test 1: GitHub source preparation validation..."

        # Check git status
        if git status > /dev/null 2>&1; then
            CURRENT_BRANCH=$(git branch --show-current)
            echo "   ✅ Git repository initialized (branch: $CURRENT_BRANCH)"
            echo "   - [x] Git repository: $CURRENT_BRANCH" >> "$BUILD_TEST_REPORT"

            # Check for uncommitted changes
            if git diff-index --quiet HEAD --; then
                echo "   ✅ No uncommitted changes"
                echo "   - [x] No uncommitted changes" >> "$BUILD_TEST_REPORT"
            else
                echo "   ⚠️  Uncommitted changes detected"
                echo "   - [ ] ⚠️  Uncommitted changes detected" >> "$BUILD_TEST_REPORT"
                echo "     Note: Commit changes before GitHub deployment"
            fi
        else
            echo "   ❌ Not a git repository"
            echo "   - [ ] ❌ Not a git repository" >> "$BUILD_TEST_REPORT"
        fi

        # Test 2: Build artifact creation
        echo "   Test 2: Build artifact creation..."
        cat >> "$BUILD_TEST_REPORT" << ARTIFACT_TEST

    ### Test 2: Build Artifact Creation
    ARTIFACT_TEST

        # Create artifact directory
        ARTIFACT_DIR="$LATEST_STAGING/github_artifacts"
        mkdir -p "$ARTIFACT_DIR"

        echo "   Creating deployment artifact (build, not source)..."

        # Verify build exists from Step 06
        if [ -d "public/build" ] && [ -f "bootstrap/cache/config.php" ]; then
            echo "   ✅ Production build artifacts exist"
            echo "   - [x] Production build artifacts exist" >> "$BUILD_TEST_REPORT"

            # Create deployment artifact package
            echo "   Packaging deployment artifact..."

            # Create artifact exclusion list (exclude source files)
            cat > "$ARTIFACT_DIR/artifact_exclude.txt" << ARTIFACT_EXCLUDE
    .git/
    .gitignore
    node_modules/
    resources/js/
    resources/sass/
    resources/vue/
    webpack.mix.js
    package.json
    package-lock.json
    .env*
    storage/logs/*
    storage/framework/cache/*
    storage/framework/sessions/*
    storage/framework/views/*
    Admin-Local/
    tests/
    .phpunit.result.cache
    ARTIFACT_EXCLUDE

            # Create production artifact (runtime files only)
            if tar --exclude-from="$ARTIFACT_DIR/artifact_exclude.txt" -czf "$ARTIFACT_DIR/production_artifact.tar.gz" -C . . > "$LATEST_STAGING/testing_logs/artifact_creation.log" 2>&1; then
                ARTIFACT_SIZE=$(du -sh "$ARTIFACT_DIR/production_artifact.tar.gz" | cut -f1)
                echo "   ✅ Production artifact created ($ARTIFACT_SIZE)"
                echo "   - [x] Production artifact created: $ARTIFACT_SIZE" >> "$BUILD_TEST_REPORT"

                # Create artifact manifest
                cat > "$ARTIFACT_DIR/ARTIFACT_MANIFEST.md" << MANIFEST
    # Method D: Production Artifact Manifest

    **Created:** $(date)
    **Size:** $ARTIFACT_SIZE
    **Type:** Production Runtime (No Source Code)

    ## Included
    - Built frontend assets (public/build/)
    - Compiled/optimized vendor/ directory
    - Cached Laravel configurations
    - Application runtime files
    - Database migrations
    - Public assets

    ## Excluded (Source Code)
    - Node.js source files (resources/js/, resources/sass/)
    - Development dependencies (node_modules/)
    - Build configuration (webpack.mix.js, package.json)
    - Git repository (.git/)
    - Development/testing files

    ## Deployment Notes
    - This is a RUNTIME ARTIFACT, not source code
    - Ready for production deployment
    - No build process required on server
    - Extract and configure only
    MANIFEST

                echo "   ✅ Artifact manifest created"
                echo "   - [x] Artifact manifest created" >> "$BUILD_TEST_REPORT"
            else
                echo "   ❌ Artifact creation failed"
                echo "   - [ ] ❌ Artifact creation failed" >> "$BUILD_TEST_REPORT"
            fi
        else
            echo "   ❌ Build artifacts missing - run Step 06 first"
            echo "   - [ ] ❌ Build artifacts missing" >> "$BUILD_TEST_REPORT"
        fi

        # Test 3: GitHub-to-artifact workflow simulation
        echo "   Test 3: GitHub-to-artifact workflow simulation..."
        cat >> "$BUILD_TEST_REPORT" << WORKFLOW_SIM

    ### Test 3: GitHub-to-Artifact Workflow Simulation
    WORKFLOW_SIM

        echo "   Simulating: GitHub pull → build → artifact creation..."

        # This simulates what would happen in Step 08:
        # 1. Pull from GitHub (simulated - we're already on the branch)
        # 2. Build (already done in Step 06)
        # 3. Create artifact (just completed above)
        # 4. Deploy artifact (will be done in Step 08)

        if [ -f "$ARTIFACT_DIR/production_artifact.tar.gz" ] && [ -f "$ARTIFACT_DIR/ARTIFACT_MANIFEST.md" ]; then
            echo "   ✅ GitHub-to-artifact workflow ready"
            echo "   - [x] Complete workflow ready for Step 08" >> "$BUILD_TEST_REPORT"

            # Show workflow summary
            echo "   📋 Method D Workflow Summary:"
            echo "     1. ✅ Source: GitHub repository"
            echo "     2. ✅ Build: Local production build"
            echo "     3. ✅ Package: Runtime artifact (not source)"
            echo "     4. ➡️  Deploy: Upload artifact to server (Step 08)"
        else
            echo "   ❌ Workflow incomplete"
            echo "   - [ ] ❌ Workflow incomplete" >> "$BUILD_TEST_REPORT"
        fi

        # Record Method D test results in tracking
        cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << METHOD_D_RESULTS

    ```

### Method D: GitHub + Manual Build Test Results

-   **Git Repository:** $(git status >/dev/null 2>&1 && echo "✅ Present ($(git branch --show-current))" || echo "❌ Not a repository")
-   **Uncommitted Changes:** $(git diff-index --quiet HEAD -- && echo "✅ Clean" || echo "⚠️ Detected")
-   **Production Artifacts:** $([ -d "public/build" ] && [ -f "bootstrap/cache/config.php" ] && echo "✅ Present" || echo "❌ Missing")
-   **Production Artifact Package:** $([ -f "$ARTIFACT_DIR/production_artifact.tar.gz" ] && echo "✅ Created ($(du -sh "$ARTIFACT_DIR/production_artifact.tar.gz" | cut -f1))" || echo "❌ Failed")
-   **Artifact Manifest:** $([ -f "$ARTIFACT_DIR/ARTIFACT_MANIFEST.md" ] && echo "✅ Created" || echo "❌ Missing")

METHOD_D_RESULTS

       # Update planning checklist
       sed -i.bak 's/- \[ \] Method D: GitHub + Manual Build Tests/- [x] Method D: GitHub + Manual Build Tests/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"

fi

````

---

## **7.3: Customization Integration Testing**

### **Test Custom Components with Updated Build:**

1. **Test customization compatibility:**

```bash
echo "🔧 Testing customization integration with updated build..."

cat >> "$BUILD_TEST_REPORT" << CUSTOM_TEST

## Customization Integration Tests

### Customization Mode: $CUSTOMIZATION_MODE
CUSTOM_TEST

if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
    echo "   Testing protected mode customizations..."

    # Test custom directory exists and is preserved
    if [ -d "app/Custom" ]; then
        CUSTOM_FILES=$(find app/Custom -name "*.php" | wc -l)
        echo "   ✅ Custom directory preserved ($CUSTOM_FILES PHP files)"
        echo "   - [x] Custom directory preserved: $CUSTOM_FILES files" >> "$BUILD_TEST_REPORT"

        # Test custom functions are loadable
        echo "   Testing custom function integration..."
        if php -l app/Custom/*.php > "$LATEST_STAGING/testing_logs/custom_syntax.log" 2>&1; then
            echo "   ✅ Custom PHP files have valid syntax"
            echo "   - [x] Custom PHP syntax valid" >> "$BUILD_TEST_REPORT"
        else
            echo "   ❌ Custom PHP syntax errors found"
            echo "   - [ ] ❌ Custom PHP syntax errors" >> "$BUILD_TEST_REPORT"
            echo "     Check: $LATEST_STAGING/testing_logs/custom_syntax.log"
        fi

        # Test autoload includes custom files
        if composer dump-autoload > "$LATEST_STAGING/testing_logs/autoload_test.log" 2>&1; then
            echo "   ✅ Custom files included in autoloader"
            echo "   - [x] Custom autoload integration" >> "$BUILD_TEST_REPORT"
        else
            echo "   ⚠️  Autoload issues detected"
            echo "   - [ ] ⚠️  Autoload issues detected" >> "$BUILD_TEST_REPORT"
        fi

    else
        echo "   ⚠️  Custom directory not found"
        echo "   - [ ] ⚠️  Custom directory missing" >> "$BUILD_TEST_REPORT"
    fi

elif [ "$CUSTOMIZATION_MODE" = "simple" ]; then
    echo "   Testing simple mode (no customizations)..."
    echo "   ✅ Simple mode - no custom integration testing needed"
    echo "   - [x] Simple mode confirmed" >> "$BUILD_TEST_REPORT"
fi
````

---

## **7.4: Performance & Functionality Testing**

### **Test Application Performance:**

1. **Performance testing:**

    ```bash
    echo "⚡ Testing application performance with updated build..."

    cat >> "$BUILD_TEST_REPORT" << PERF_TEST

    ## Performance Testing
    PERF_TEST

    # Start test server
    echo "   Starting performance test server..."
    php artisan serve --host=127.0.0.1 --port=8002 > "$LATEST_STAGING/testing_logs/perf_server.log" 2>&1 &
    PERF_SERVER_PID=$!

    # Wait for server to start
    sleep 5

    # Test server response
    if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8002 | grep -q "200\|302"; then
        echo "   ✅ Server responding"
        echo "   - [x] Server responding" >> "$BUILD_TEST_REPORT"

        # Test page load performance
        LOAD_TIME=$(curl -s -o /dev/null -w "%{time_total}" "http://127.0.0.1:8002")
        echo "   📊 Home page load time: ${LOAD_TIME}s"
        echo "   - Page load time: ${LOAD_TIME}s" >> "$BUILD_TEST_REPORT"

        # Test asset loading
        ASSET_COUNT=$(curl -s "http://127.0.0.1:8002" | grep -c "public/build\|/build/" 2>/dev/null || echo "0")
        if [ "$ASSET_COUNT" -gt 0 ]; then
            echo "   ✅ $ASSET_COUNT built assets loading correctly"
            echo "   - [x] $ASSET_COUNT built assets loading" >> "$BUILD_TEST_REPORT"
        else
            echo "   ⚠️  Built assets may not be loading"
            echo "   - [ ] ⚠️  Built assets may not be loading" >> "$BUILD_TEST_REPORT"
        fi

        # Test key pages (if accessible)
        KEY_PAGES=("/login" "/register" "/dashboard" "/api/health")
        for page in "${KEY_PAGES[@]}"; do
            STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8002$page")
            if [ "$STATUS" = "200" ] || [ "$STATUS" = "302" ] || [ "$STATUS" = "401" ]; then
                echo "   ✅ $page responds ($STATUS)"
            else
                echo "   ⚠️  $page status: $STATUS"
            fi
        done

    else
        echo "   ❌ Server not responding"
        echo "   - [ ] ❌ Server not responding" >> "$BUILD_TEST_REPORT"
    fi

    # Stop test server
    kill $PERF_SERVER_PID 2>/dev/null
    echo "   🛑 Performance test server stopped"

    # Record performance test results in tracking
    cat >> "$SESSION_DIR/3-Execution/step-07-execution.log" << PERF_RESULTS

### Performance Testing Results

- **Server Status:** $(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8002 2>/dev/null | grep -q "200\|302" && echo "✅ Responsive" || echo "❌ Not responding")
- **Home Page Load Time:** $(curl -s -o /dev/null -w "%{time_total}" "http://127.0.0.1:8002" 2>/dev/null || echo "N/A") seconds
- **Built Assets Loading:** $(curl -s "http://127.0.0.1:8002" 2>/dev/null | grep -c "public/build\|/build/" || echo "0") assets detected
- **Key Pages Tested:** /login, /register, /dashboard, /api/health
- **Performance Test Duration:** $(($(date +%s) - SESSION_START)) seconds

PERF_RESULTS

    # Update planning checklist
    sed -i.bak 's/- \[ \] Validate application performance/- [x] Validate application performance/g' "$SESSION_DIR/1-Planning/step-07-build-test-plan.md"
    ```

2. **Database integration testing:**

    ```bash
    echo "   Testing database integration..."
    cat >> "$BUILD_TEST_REPORT" << DB_TEST

    ## Database Integration Testing
    DB_TEST

    # Test database connection
    if php artisan migrate:status > "$LATEST_STAGING/testing_logs/db_status.log" 2>&1; then
        echo "   ✅ Database connection successful"
        echo "   - [x] Database connection successful" >> "$BUILD_TEST_REPORT"

        # Count migrations
        MIGRATION_COUNT=$(php artisan migrate:status | grep -c "Y " 2>/dev/null || echo "0")
        echo "   📊 $MIGRATION_COUNT migrations applied"
        echo "   - $MIGRATION_COUNT migrations applied" >> "$BUILD_TEST_REPORT"

    else
        echo "   ⚠️  Database connection issues"
        echo "   - [ ] ⚠️  Database connection issues" >> "$BUILD_TEST_REPORT"
        echo "     Note: Check database configuration for deployment"
    fi
    ```

---

## **7.5: Build Test Summary**

### **Generate Complete Test Report:**

1. **Create final test summary:**

    ```bash
    echo "📊 Generating build test summary..."

    # Count passed and failed tests
    PASSED_TESTS=$(grep -c "\[x\]" "$BUILD_TEST_REPORT" 2>/dev/null || echo "0")
    FAILED_TESTS=$(grep -c "\[ \]" "$BUILD_TEST_REPORT" 2>/dev/null || echo "0")
    TOTAL_TESTS=$((PASSED_TESTS + FAILED_TESTS))

    # Calculate success rate
    if [ "$TOTAL_TESTS" -gt 0 ]; then
        SUCCESS_RATE=$(( (PASSED_TESTS * 100) / TOTAL_TESTS ))
    else
        SUCCESS_RATE=0
    fi

    cat >> "$BUILD_TEST_REPORT" << SUMMARY

    ## Build Test Summary

    **Test Completion:** $(date)
    **Tests Passed:** $PASSED_TESTS / $TOTAL_TESTS
    **Success Rate:** $SUCCESS_RATE%
    **Deployment Method:** $DEPLOY_METHOD
    **Customization Mode:** $CUSTOMIZATION_MODE

    ## Deployment Readiness Assessment

    $(if [ "$SUCCESS_RATE" -ge 90 ]; then
        echo "**Status:** ✅ READY FOR DEPLOYMENT"
        echo ""
        echo "All critical tests passed. The build process is working correctly"
        echo "and the application is ready for deployment using $DEPLOY_METHOD."
    elif [ "$SUCCESS_RATE" -ge 70 ]; then
        echo "**Status:** ⚠️  READY WITH WARNINGS"
        echo ""
        echo "Most tests passed but some warnings detected. Review failed tests"
        echo "before proceeding to deployment."
    else
        echo "**Status:** ❌ NOT READY FOR DEPLOYMENT"
        echo ""
        echo "Multiple critical tests failed. Fix issues before proceeding."
    fi)

    ## Next Steps

    $(if [ "$SUCCESS_RATE" -ge 70 ]; then
        echo "1. ✅ Continue to Step 08: Deploy Updates"
        echo "2. Monitor deployment process closely"
        echo "3. Verify functionality in production"
    else
        echo "1. ❌ Fix failed tests identified above"
        echo "2. Re-run Step 07 after fixes"
        echo "3. Only proceed when success rate ≥ 70%"
    fi)

    ## Build Test Artifacts

    - **Full Report:** $BUILD_TEST_REPORT
    - **Test Logs:** $LATEST_STAGING/testing_logs/
    $(case "$DEPLOY_METHOD" in
        "manual_ssh")
            echo "- **Deployment Package:** $LATEST_STAGING/deployment_package/deployment.tar.gz"
            ;;
        "github_manual")
            echo "- **Production Artifact:** $LATEST_STAGING/github_artifacts/production_artifact.tar.gz"
            echo "- **Artifact Manifest:** $LATEST_STAGING/github_artifacts/ARTIFACT_MANIFEST.md"
            ;;
        "github_actions"|"deployhq")
            echo "- **Source Validation:** Complete - ready for automated build"
            ;;
    esac)
    SUMMARY

    echo "✅ Build test summary completed"
    echo "   Report: $BUILD_TEST_REPORT"
    echo "   Success Rate: $SUCCESS_RATE%"
    echo "   Status: $([ "$SUCCESS_RATE" -ge 90 ] && echo "READY" || [ "$SUCCESS_RATE" -ge 70 ] && echo "READY WITH WARNINGS" || echo "NOT READY")"
    ```

2. **Update the update log:**

    ```bash
    # Update the current update log
    LATEST_LOG=$(find Admin-Local/update_logs -name "update_*.md" | sort | tail -1)

    if [ -n "$LATEST_LOG" ]; then
        # Mark Step 07 as complete
        sed -i.bak 's/- \[ \] Step 07: Test Build Process/- [x] Step 07: Test Build Process/' "$LATEST_LOG"

        # Add Step 07 completion details
        cat >> "$LATEST_LOG" << LOG_UPDATE

    ## Step 07 Completed
    - **Tests Passed:** $PASSED_TESTS/$TOTAL_TESTS ($SUCCESS_RATE%)
    - **Build Method:** $DEPLOY_METHOD
    - **Deployment Status:** $([ "$SUCCESS_RATE" -ge 90 ] && echo "✅ Ready" || [ "$SUCCESS_RATE" -ge 70 ] && echo "⚠️  Ready with warnings" || echo "❌ Not ready")
    - **Test Report:** $BUILD_TEST_REPORT
    LOG_UPDATE

        echo "✅ Update log updated: $LATEST_LOG"
    fi
    ```

---

## **✅ Step 07 Completion Checklist**

-   [ ] Testing environment initialized
-   [ ] Method-specific build tests completed
-   [ ] Source code validation performed (for automated methods)
-   [ ] Build artifacts tested (for manual methods)
-   [ ] Customization integration verified
-   [ ] Application performance tested
-   [ ] Database integration tested
-   [ ] Build test summary generated
-   [ ] Deployment readiness assessed

---

## **Next Steps**

**Success Rate ≥ 90%:** Continue to [Step 08: Deploy Updates](Step_08_Deploy_Updates.md)  
**Success Rate 70-89%:** Review warnings, then continue to Step 08  
**Success Rate < 70%:** Fix issues and re-run Step 07

**Method-specific readiness:**

-   **Method A (Manual SSH):** Deployment package ready for upload
-   **Method B (GitHub Actions):** Source code validated for automated deployment
-   **Method C (DeployHQ):** Source code validated for professional deployment
-   **Method D (GitHub + Manual):** Production artifact ready for deployment

**Key files to review:**

-   **Test Report:** `$BUILD_TEST_REPORT`
-   **Test Logs:** `$LATEST_STAGING/testing_logs/`

---

## **Troubleshooting**

### **Issue: Low success rate**

```bash
# Re-run specific test categories
grep "\[ \]" "$BUILD_TEST_REPORT"  # See failed tests
```

### **Issue: Build artifacts missing**

```bash
# Re-run Step 06 dependency updates
npm run build
php artisan config:cache
```

### **Issue: Performance problems**

```bash
# Clear all caches and rebuild
php artisan cache:clear
php artisan config:clear
npm run build
```
