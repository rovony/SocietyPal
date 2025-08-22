-   **SECTION B: Prepare for Build and Deployment & Commit Codebase to Github**
    IMPORTANT Note: below is a draft and may have errors or be incorrect or missing steps- you can modify or change to enhance and ensure proper and correct flow.
    Important Note 2: The steps below should cover apps we develop or apps we purchase like from codecanyon. same flow for all.
    # Master Checklist for **SECTION B: Prepare for Build and Deployment**
    This checklist consolidates all the necessary steps for Phase 2 of the project setup. Follow each step carefully to ensure a smooth and successful deployment.
    1. **Step 14.1: Composer Version Strategy Setup**
        1. **Purpose:** Configure Composer for production compatibility and optimize settings.
        2. **When:** Before Step 15 to ensure proper dependency installation.
        3. **Action:**
            1. Create Composer strategy setup script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/setup-composer-strategy.sh << 'EOF'
                #!/bin/bash
                
                echo "=== Setting Up Composer Production Strategy ==="
                
                # 1. Check if composer.json needs modification for v2
                if ! grep -q '"config"' composer.json; then
                    echo "Adding config section to composer.json..."
                    jq '. + {"config": {}}' composer.json > composer.tmp && mv composer.tmp composer.json
                fi
                
                # 2. Add production optimizations
                jq '.config += {
                    "optimize-autoloader": true,
                    "preferred-install": "dist",
                    "sort-packages": true,
                    "classmap-authoritative": true,
                    "apcu-autoloader": true,
                    "platform-check": false
                }' composer.json > composer.tmp && mv composer.tmp composer.json
                
                # 3. Handle plugin compatibility for Composer 2
                if composer --version | grep -q "version 2"; then
                    # Get all plugins and add to allow-plugins
                    PLUGINS=$(composer show -s | grep "composer-plugin" -B2 | grep "name" | cut -d: -f2 | tr -d ' ')
                    
                    for plugin in $PLUGINS; do
                        jq --arg plugin "$plugin" '.config."allow-plugins"[$plugin] = true' composer.json > composer.tmp
                        mv composer.tmp composer.json
                    done
                fi
                
                # 4. Add platform requirements
                PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
                jq --arg ver "$PHP_VERSION" '.config.platform.php = $ver' composer.json > composer.tmp
                mv composer.tmp composer.json
                
                echo "✅ Composer configured for production"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
                
            2. Run the script.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
                
                ```
                
            3. Verify composer.json configuration has been updated with production optimizations.
    2. **Step 15: Install Dependencies & Generate Lock Files**
        1. **Purpose:** Install and verify all project dependencies for reproducible builds.
        2. **Action:**
            1. Navigate to the project root.
            2. Install PHP dependencies.

                ```bash
                composer install

                ```

            3. Install JavaScript dependencies (if applicable).

                ```bash
                npm install

                ```

            4. Verify that `composer.lock` and `package-lock.json` files are created.
    2. **Step 15.1: Run Database Migrations `(New Added)`**
        1. **Purpose:** Ensure the database schema is up-to-date with the application's requirements.
        2. **Action:**
            1. Run the following command to apply any pending migrations:

                ```bash
                php artisan migrate

                ```
    3. **Step 15.2: Production Dependency Verification**
        1. **Purpose:** Verify that all production dependencies are correctly classified and no dev dependencies are used in production code.
        2. **When:** After dependencies are installed and before build testing begins.
        3. **Action:**
            1. Create production dependency verification script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/verify-production-dependencies.sh << 'EOF'
                #!/bin/bash
                
                echo "=== Production Dependency Verification ==="
                
                # 1. Check for dev dependencies in production code
                DEV_DEPS=$(php -r "
                \$composer = json_decode(file_get_contents('composer.json'), true);
                \$devDeps = array_keys(\$composer['require-dev'] ?? []);
                foreach (\$devDeps as \$dep) {
                    \$packageName = explode('/', \$dep)[1] ?? \$dep;
                    \$uses = shell_exec('grep -r \"use.*' . \$packageName . '\" app/ resources/ || true');
                    if (!empty(\$uses)) {
                        echo \"❌ Dev dependency '\$dep' used in production code:\\n\$uses\\n\";
                    }
                }
                ")
                
                if [[ -n "$DEV_DEPS" ]]; then
                    echo "$DEV_DEPS"
                    echo ""
                    echo "🔧 Suggested fixes:"
                    echo "1. Move dev dependencies to 'require' section if needed in production"
                    echo "2. Create production-safe alternatives for development-only features"
                    echo "3. Use environment checks to conditionally load dev dependencies"
                fi
                
                # 2. Verify composer install --no-dev works
                echo "🧪 Testing production dependency installation..."
                composer install --no-dev --dry-run > /tmp/composer-production-test.log 2>&1
                
                if [ $? -eq 0 ]; then
                    echo "✅ Production dependency installation: PASSED"
                else
                    echo "❌ Production dependency installation: FAILED"
                    echo "Check /tmp/composer-production-test.log for details"
                fi
                
                # 3. Check for missing platform requirements
                echo "🔍 Checking platform requirements..."
                composer check-platform-reqs --no-dev 2>/dev/null || echo "⚠️  Platform requirement issues detected"
                
                # 4. Validate lock file consistency
                echo "🔒 Validating lock file consistency..."
                composer validate --strict --no-check-all || echo "⚠️  Composer validation issues detected"
                
                echo "=== Verification Complete ==="
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
                
            2. Run the script.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
                
                ```
                
            3. Fix any dependency classification issues identified by the verification script.
            4. Re-run verification until all checks pass.
    4. **Step 16: Test Build Process**
        1. **Purpose:** Verify the production build process works before deployment with comprehensive pre-build validation.
        2. **Action:**
            1. **Pre-Build Validation** - Run comprehensive checks before attempting build.
                
                ```bash
                #!/bin/bash
                # Create pre-build validation script
                # save as: Admin-Local/1-CurrentProject/pre-build-validation.sh
                
                echo "=== Pre-Build Validation ==="
                
                # 1. Environment validation
                echo "🔍 Validating environment..."
                php -v || { echo "❌ PHP not available"; exit 1; }
                composer --version || { echo "❌ Composer not available"; exit 1; }
                node --version || { echo "❌ Node.js not available"; exit 1; }
                npm --version || { echo "❌ NPM not available"; exit 1; }
                
                # 2. Required files validation
                echo "🔍 Validating required files..."
                [[ -f "composer.json" ]] || { echo "❌ composer.json missing"; exit 1; }
                [[ -f "composer.lock" ]] || { echo "❌ composer.lock missing"; exit 1; }
                [[ -f "package.json" ]] || { echo "❌ package.json missing"; exit 1; }
                [[ -f "package-lock.json" ]] || { echo "❌ package-lock.json missing"; exit 1; }
                [[ -f ".env" ]] || { echo "❌ .env file missing"; exit 1; }
                
                # 3. Dependency validation
                echo "🔍 Validating dependencies..."
                composer validate --strict --no-check-all || { echo "❌ Composer validation failed"; exit 1; }
                npm audit --audit-level=high || echo "⚠️  High-severity vulnerabilities found"
                
                # 4. Laravel-specific validation
                echo "🔍 Validating Laravel setup..."
                php artisan --version || { echo "❌ Laravel Artisan not working"; exit 1; }
                [[ -f "config/app.php" ]] || { echo "❌ Laravel config missing"; exit 1; }
                
                # 5. Database connection test
                echo "🔍 Testing database connection..."
                php artisan migrate:status > /dev/null 2>&1 || echo "⚠️  Database connection issues detected"
                
                echo "✅ Pre-build validation complete"
                
                ```
                
            2. Run the pre-build validation script.
                
                ```bash
                chmod +x Admin-Local/1-CurrentProject/pre-build-validation.sh
                ./Admin-Local/1-CurrentProject/pre-build-validation.sh
                
                ```
                
            3. Clean previous builds.

                ```bash
                rm -rf vendor node_modules public/build

                ```

            4. Test the production PHP build.

                ```bash
                composer install --no-dev --prefer-dist --optimize-autoloader

                ```

            5. Build frontend assets.

                ```bash
                npm ci
                npm run build

                ```

            6. Apply Laravel caching.

                ```bash
                php artisan config:cache
                php artisan route:cache
                php artisan view:cache

                ```

            7. Test the built version locally.
            8. Restore the development environment.

                ```bash
                php artisan config:clear
                php artisan route:clear
                php artisan view:clear
                composer install
                npm install

                ```
    5. **Step 16.1: Pre-Deployment Validation Checklist**
        1. **Purpose:** Comprehensive 10-point validation checklist to ensure deployment readiness before any build or deployment activities.
        2. **When:** After build testing and before security scans - critical gateway step.
        3. **Action:**
            1. Create comprehensive pre-deployment validation script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/pre-deployment-validation.sh << 'EOF'
                #!/bin/bash
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║           Pre-Deployment Validation Checklist           ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Load deployment variables
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                # Initialize validation results
                VALIDATION_REPORT="Admin-Local/Deployment/Logs/pre-deployment-validation-$(date +%Y%m%d-%H%M%S).md"
                FAILED_CHECKS=()
                PASSED_CHECKS=()
                TOTAL_CHECKS=10
                
                echo "# Pre-Deployment Validation Report" > $VALIDATION_REPORT
                echo "Generated: $(date)" >> $VALIDATION_REPORT
                echo "Project: $PROJECT_NAME" >> $VALIDATION_REPORT
                echo "" >> $VALIDATION_REPORT
                
                # Helper function for check results
                check_result() {
                    local check_name="$1"
                    local check_status="$2"
                    local check_details="$3"
                    
                    if [ "$check_status" = "PASS" ]; then
                        echo "✅ $check_name: PASSED"
                        echo "## ✅ $check_name" >> $VALIDATION_REPORT
                        echo "**Status:** PASSED" >> $VALIDATION_REPORT
                        [ -n "$check_details" ] && echo "**Details:** $check_details" >> $VALIDATION_REPORT
                        PASSED_CHECKS+=("$check_name")
                    else
                        echo "❌ $check_name: FAILED"
                        echo "## ❌ $check_name" >> $VALIDATION_REPORT
                        echo "**Status:** FAILED" >> $VALIDATION_REPORT
                        echo "**Details:** $check_details" >> $VALIDATION_REPORT
                        FAILED_CHECKS+=("$check_name")
                    fi
                    echo "" >> $VALIDATION_REPORT
                }
                
                echo "🔍 Running 10-point validation checklist..."
                echo ""
                
                # 1. Environment Configuration Validation
                echo "1/10 - Validating Environment Configuration..."
                ENV_STATUS="PASS"
                ENV_DETAILS=""
                
                if [ ! -f ".env" ]; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="Missing .env file"
                elif ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d'=' -f2)" ]; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="APP_KEY not set in .env"
                elif ! grep -q "DB_" .env; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="Database configuration missing in .env"
                else
                    ENV_DETAILS="Environment file properly configured"
                fi
                check_result "Environment Configuration" "$ENV_STATUS" "$ENV_DETAILS"
                
                # 2. Dependencies Installation Check
                echo "2/10 - Validating Dependencies..."
                DEPS_STATUS="PASS"
                DEPS_DETAILS=""
                
                if [ ! -d "vendor" ] || [ ! -f "composer.lock" ]; then
                    DEPS_STATUS="FAIL"
                    DEPS_DETAILS="PHP dependencies not installed (run: composer install)"
                elif [ -f "package.json" ] && ([ ! -d "node_modules" ] || [ ! -f "package-lock.json" ]); then
                    DEPS_STATUS="FAIL"
                    DEPS_DETAILS="Node.js dependencies not installed (run: npm install)"
                else
                    # Test production dependencies
                    if composer install --no-dev --dry-run > /dev/null 2>&1; then
                        DEPS_DETAILS="All dependencies properly installed and production-ready"
                    else
                        DEPS_STATUS="FAIL"
                        DEPS_DETAILS="Production dependency installation issues detected"
                    fi
                fi
                check_result "Dependencies Installation" "$DEPS_STATUS" "$DEPS_DETAILS"
                
                # 3. Database Connectivity
                echo "3/10 - Validating Database Connection..."
                DB_STATUS="PASS"
                DB_DETAILS=""
                
                if ! php artisan migrate:status > /dev/null 2>&1; then
                    DB_STATUS="FAIL"
                    DB_DETAILS="Database connection failed or migrations not run"
                else
                    MIGRATION_COUNT=$(php artisan migrate:status 2>/dev/null | grep -c "Ran")
                    DB_DETAILS="Database connected, $MIGRATION_COUNT migrations applied"
                fi
                check_result "Database Connectivity" "$DB_STATUS" "$DB_DETAILS"
                
                # 4. Build Process Validation
                echo "4/10 - Validating Build Process..."
                BUILD_STATUS="PASS"
                BUILD_DETAILS=""
                
                # Test Laravel optimization commands
                if ! php artisan config:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel config:cache failed"
                elif ! php artisan route:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel route:cache failed"
                elif ! php artisan view:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel view:cache failed"
                else
                    # Clear caches after testing
                    php artisan config:clear > /dev/null 2>&1
                    php artisan route:clear > /dev/null 2>&1
                    php artisan view:clear > /dev/null 2>&1
                    
                    # Test frontend build if applicable
                    if [ -f "package.json" ] && grep -q '"build"' package.json; then
                        if npm run build > /dev/null 2>&1; then
                            BUILD_DETAILS="Laravel optimization and frontend build successful"
                        else
                            BUILD_STATUS="FAIL"
                            BUILD_DETAILS="Frontend build process failed"
                        fi
                    else
                        BUILD_DETAILS="Laravel optimization successful"
                    fi
                fi
                check_result "Build Process" "$BUILD_STATUS" "$BUILD_DETAILS"
                
                # 5. Security Configuration
                echo "5/10 - Validating Security Configuration..."
                SEC_STATUS="PASS"
                SEC_DETAILS=""
                
                SECURITY_ISSUES=()
                
                # Check APP_DEBUG setting
                if grep -q "APP_DEBUG=true" .env; then
                    SECURITY_ISSUES+=("APP_DEBUG=true (should be false in production)")
                fi
                
                # Check APP_ENV setting
                if ! grep -q "APP_ENV=production" .env && ! grep -q "APP_ENV=staging" .env; then
                    SECURITY_ISSUES+=("APP_ENV should be 'production' or 'staging'")
                fi
                
                # Check for HTTPS enforcement
                if ! grep -q "FORCE_HTTPS=true" .env && ! grep -q "APP_URL=https" .env; then
                    SECURITY_ISSUES+=("HTTPS enforcement not configured")
                fi
                
                if [ ${#SECURITY_ISSUES[@]} -gt 0 ]; then
                    SEC_STATUS="FAIL"
                    SEC_DETAILS="Issues: $(IFS='; '; echo "${SECURITY_ISSUES[*]}")"
                else
                    SEC_DETAILS="Security configuration validated"
                fi
                check_result "Security Configuration" "$SEC_STATUS" "$SEC_DETAILS"
                
                # 6. File Permissions
                echo "6/10 - Validating File Permissions..."
                PERM_STATUS="PASS"
                PERM_DETAILS=""
                
                if [ ! -w "storage" ] || [ ! -w "bootstrap/cache" ]; then
                    PERM_STATUS="FAIL"
                    PERM_DETAILS="storage/ or bootstrap/cache/ not writable"
                else
                    PERM_DETAILS="Critical directories have proper write permissions"
                fi
                check_result "File Permissions" "$PERM_STATUS" "$PERM_DETAILS"
                
                # 7. Git Repository Status
                echo "7/10 - Validating Git Repository..."
                GIT_STATUS="PASS"
                GIT_DETAILS=""
                
                if [ ! -d ".git" ]; then
                    GIT_STATUS="FAIL"
                    GIT_DETAILS="Not a git repository"
                elif [ -n "$(git status --porcelain 2>/dev/null)" ]; then
                    UNCOMMITTED_COUNT=$(git status --porcelain 2>/dev/null | wc -l)
                    GIT_STATUS="FAIL"
                    GIT_DETAILS="$UNCOMMITTED_COUNT uncommitted changes detected"
                else
                    CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
                    GIT_DETAILS="Repository clean, current branch: $CURRENT_BRANCH"
                fi
                check_result "Git Repository Status" "$GIT_STATUS" "$GIT_DETAILS"
                
                # 8. Configuration Files Validation
                echo "8/10 - Validating Configuration Files..."
                CONFIG_STATUS="PASS"
                CONFIG_DETAILS=""
                
                MISSING_CONFIGS=()
                [ ! -f "config/app.php" ] && MISSING_CONFIGS+=("config/app.php")
                [ ! -f "config/database.php" ] && MISSING_CONFIGS+=("config/database.php")
                [ ! -f "composer.json" ] && MISSING_CONFIGS+=("composer.json")
                
                if [ ${#MISSING_CONFIGS[@]} -gt 0 ]; then
                    CONFIG_STATUS="FAIL"
                    CONFIG_DETAILS="Missing: $(IFS=', '; echo "${MISSING_CONFIGS[*]}")"
                else
                    CONFIG_DETAILS="All critical configuration files present"
                fi
                check_result "Configuration Files" "$CONFIG_STATUS" "$CONFIG_DETAILS"
                
                # 9. Deployment Variables Validation
                echo "9/10 - Validating Deployment Configuration..."
                DEPLOY_STATUS="PASS"
                DEPLOY_DETAILS=""
                
                if [ ! -f "Admin-Local/Deployment/Configs/deployment-variables.json" ]; then
                    DEPLOY_STATUS="FAIL"
                    DEPLOY_DETAILS="Deployment variables configuration missing"
                else
                    # Validate JSON structure
                    if jq empty Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null; then
                        PROJECT_NAME_CONFIG=$(jq -r '.project.name' Admin-Local/Deployment/Configs/deployment-variables.json)
                        DEPLOY_DETAILS="Deployment configuration valid for project: $PROJECT_NAME_CONFIG"
                    else
                        DEPLOY_STATUS="FAIL"
                        DEPLOY_DETAILS="Invalid JSON in deployment-variables.json"
                    fi
                fi
                check_result "Deployment Variables" "$DEPLOY_STATUS" "$DEPLOY_DETAILS"
                
                # 10. Health Check Endpoint
                echo "10/10 - Validating Application Health..."
                HEALTH_STATUS="PASS"
                HEALTH_DETAILS=""
                
                # Test basic Laravel functionality
                if ! php artisan --version > /dev/null 2>&1; then
                    HEALTH_STATUS="FAIL"
                    HEALTH_DETAILS="Laravel artisan commands not functional"
                elif ! php artisan route:list > /dev/null 2>&1; then
                    HEALTH_STATUS="FAIL"
                    HEALTH_DETAILS="Laravel routing system not functional"
                else
                    HEALTH_DETAILS="Laravel application core functionality verified"
                fi
                check_result "Application Health" "$HEALTH_STATUS" "$HEALTH_DETAILS"
                
                # Generate Summary
                echo "" >> $VALIDATION_REPORT
                echo "## 📊 Validation Summary" >> $VALIDATION_REPORT
                echo "- **Total Checks:** $TOTAL_CHECKS" >> $VALIDATION_REPORT
                echo "- **Passed:** ${#PASSED_CHECKS[@]}" >> $VALIDATION_REPORT
                echo "- **Failed:** ${#FAILED_CHECKS[@]}" >> $VALIDATION_REPORT
                echo "" >> $VALIDATION_REPORT
                
                if [ ${#FAILED_CHECKS[@]} -eq 0 ]; then
                    echo "## ✅ DEPLOYMENT READY" >> $VALIDATION_REPORT
                    echo "All validation checks passed. Project is ready for deployment." >> $VALIDATION_REPORT
                    
                    echo ""
                    echo "🎉 ALL VALIDATION CHECKS PASSED!"
                    echo "📋 Project is ready for deployment"
                    echo "📁 Full report: $VALIDATION_REPORT"
                    exit 0
                else
                    echo "## ❌ DEPLOYMENT BLOCKED" >> $VALIDATION_REPORT
                    echo "The following issues must be resolved before deployment:" >> $VALIDATION_REPORT
                    for failed_check in "${FAILED_CHECKS[@]}"; do
                        echo "- $failed_check" >> $VALIDATION_REPORT
                    done
                    
                    echo ""
                    echo "🚫 VALIDATION FAILED!"
                    echo "❌ ${#FAILED_CHECKS[@]} check(s) failed"
                    echo "📋 Review issues in: $VALIDATION_REPORT"
                    exit 1
                fi
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
                
                ```
                
            2. Run the pre-deployment validation checklist.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
                
                ```
                
            3. **Critical:** Address all failed validation points before proceeding to the next step.

    6. **Step 16.2: Build Strategy Configuration**
        1. **Purpose:** Configure and validate the build strategy (local, VM, or server-based) using path variables system for flexible deployment workflows.
        2. **When:** After pre-deployment validation passes, before security scans.
        3. **Action:**
            1. Create build strategy configuration script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/configure-build-strategy.sh << 'EOF'
                #!/bin/bash
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║             Build Strategy Configuration                 ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Load deployment variables
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                # Get build configuration
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                DEPLOYMENT_STRATEGY=$(jq -r '.deployment.strategy' Admin-Local/Deployment/Configs/deployment-variables.json)
                
                echo "📋 Current Configuration:"
                echo "   Build Location: $BUILD_LOCATION"
                echo "   Deployment Strategy: $DEPLOYMENT_STRATEGY"
                echo "   Project: $PROJECT_NAME"
                echo ""
                
                # Create build strategy script based on configuration
                BUILD_SCRIPT="Admin-Local/Deployment/Scripts/execute-build-strategy.sh"
                
                cat > $BUILD_SCRIPT << 'BUILD_EOF'
                #!/bin/bash
                
                # Execute Build Strategy
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                BUILD_REPORT="Admin-Local/Deployment/Logs/build-execution-$(date +%Y%m%d-%H%M%S).md"
                
                echo "# Build Execution Report" > $BUILD_REPORT
                echo "Generated: $(date)" >> $BUILD_REPORT
                echo "Strategy: $BUILD_LOCATION" >> $BUILD_REPORT
                echo "" >> $BUILD_REPORT
                
                case $BUILD_LOCATION in
                    "local")
                        echo "🏠 Executing LOCAL build strategy..."
                        echo "## 🏠 Local Build Execution" >> $BUILD_REPORT
                        
                        # Create local build directory
                        mkdir -p "$PATH_LOCAL_MACHINE/build-tmp"
                        cd "$PATH_LOCAL_MACHINE"
                        
                        echo "📁 Build Path: $PATH_LOCAL_MACHINE/build-tmp" >> $BUILD_REPORT
                        
                        # Clean previous build
                        echo "🧹 Cleaning previous build artifacts..."
                        rm -rf build-tmp/*
                        
                        # Copy source code
                        echo "📋 Copying source code..."
                        rsync -av --exclude='build-tmp' --exclude='node_modules' --exclude='vendor' . build-tmp/
                        
                        cd build-tmp
                        
                        # Install dependencies
                        echo "📦 Installing production dependencies..."
                        echo "### Dependencies Installation" >> $BUILD_REPORT
                        
                        if composer install --no-dev --prefer-dist --optimize-autoloader >> $BUILD_REPORT 2>&1; then
                            echo "✅ Composer dependencies installed" >> $BUILD_REPORT
                        else
                            echo "❌ Composer installation failed" >> $BUILD_REPORT
                            exit 1
                        fi
                        
                        if [ -f "package.json" ]; then
                            if npm ci --only=production >> $BUILD_REPORT 2>&1; then
                                echo "✅ NPM dependencies installed" >> $BUILD_REPORT
                            else
                                echo "❌ NPM installation failed" >> $BUILD_REPORT
                                exit 1
                            fi
                            
                            if npm run build >> $BUILD_REPORT 2>&1; then
                                echo "✅ Frontend assets built" >> $BUILD_REPORT
                            else
                                echo "❌ Frontend build failed" >> $BUILD_REPORT
                                exit 1
                            fi
                        fi
                        
                        # Laravel optimizations
                        echo "⚡ Applying Laravel optimizations..."
                        echo "### Laravel Optimizations" >> $BUILD_REPORT
                        
                        cp .env.production .env 2>/dev/null || cp .env.example .env
                        php artisan key:generate --force >> $BUILD_REPORT 2>&1
                        php artisan config:cache >> $BUILD_REPORT 2>&1
                        php artisan route:cache >> $BUILD_REPORT 2>&1
                        php artisan view:cache >> $BUILD_REPORT 2>&1
                        
                        echo "✅ Local build completed successfully" >> $BUILD_REPORT
                        ;;
                        
                    "vm")
                        echo "☁️ Executing VM-based build strategy..."
                        echo "## ☁️ VM Build Execution" >> $BUILD_REPORT
                        
                        # Check if BUILD_SERVER_PATH is configured
                        if [ -z "$BUILD_SERVER_PATH" ]; then
                            echo "❌ BUILD_SERVER_PATH not configured for VM build" >> $BUILD_REPORT
                            exit 1
                        fi
                        
                        echo "📁 Build Server Path: $BUILD_SERVER_PATH" >> $BUILD_REPORT
                        
                        # Create build package
                        echo "📦 Creating build package..."
                        BUILD_PACKAGE="$PATH_LOCAL_MACHINE/${PROJECT_NAME}-build-$(date +%Y%m%d-%H%M%S).tar.gz"
                        
                        tar czf "$BUILD_PACKAGE" \
                            --exclude='build-tmp' \
                            --exclude='node_modules' \
                            --exclude='vendor' \
                            --exclude='.git' \
                            -C "$PATH_LOCAL_MACHINE" .
                        
                        echo "📁 Package created: $BUILD_PACKAGE" >> $BUILD_REPORT
                        
                        # Note: VM execution would require SSH/deployment tool integration
                        echo "⚠️ VM build package ready. Upload to build server and execute build commands." >> $BUILD_REPORT
                        echo "Build commands should mirror local strategy within VM environment." >> $BUILD_REPORT
                        ;;
                        
                    "server")
                        echo "🖥️ Executing SERVER-based build strategy..."
                        echo "## 🖥️ Server Build Execution" >> $BUILD_REPORT
                        
                        echo "📁 Server Build Path: $PATH_SERVER/build-tmp" >> $BUILD_REPORT
                        
                        # Note: Server build would require SSH access or deployment automation
                        echo "⚠️ Server build configuration prepared." >> $BUILD_REPORT
                        echo "Build should be executed on server with:" >> $BUILD_REPORT
                        echo "- composer install --no-dev --prefer-dist --optimize-autoloader" >> $BUILD_REPORT
                        echo "- npm ci --only=production && npm run build" >> $BUILD_REPORT
                        echo "- Laravel optimization commands" >> $BUILD_REPORT
                        ;;
                        
                    *)
                        echo "❌ Unknown build location: $BUILD_LOCATION" >> $BUILD_REPORT
                        exit 1
                        ;;
                esac
                
                echo ""
                echo "📋 Build execution report: $BUILD_REPORT"
                BUILD_EOF
                
                chmod +x $BUILD_SCRIPT
                
                # Create build validation script
                cat > Admin-Local/Deployment/Scripts/validate-build-output.sh << 'VALIDATE_EOF'
                #!/bin/bash
                
                echo "🔍 Validating build output..."
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                
                case $BUILD_LOCATION in
                    "local")
                        BUILD_DIR="$PATH_LOCAL_MACHINE/build-tmp"
                        ;;
                    "vm"|"server")
                        echo "⚠️ Remote build validation requires manual verification"
                        exit 0
                        ;;
                esac
                
                if [ ! -d "$BUILD_DIR" ]; then
                    echo "❌ Build directory not found: $BUILD_DIR"
                    exit 1
                fi
                
                cd "$BUILD_DIR"
                
                # Validate critical files exist
                VALIDATION_ERRORS=()
                
                [ ! -f "composer.json" ] && VALIDATION_ERRORS+=("composer.json missing")
                [ ! -d "vendor" ] && VALIDATION_ERRORS+=("vendor directory missing")
                [ ! -f "artisan" ] && VALIDATION_ERRORS+=("artisan file missing")
                [ ! -f ".env" ] && VALIDATION_ERRORS+=(".env file missing")
                
                if [ -f "package.json" ]; then
                    [ ! -d "public/build" ] && [ ! -d "public/assets" ] && VALIDATION_ERRORS+=("frontend build assets missing")
                fi
                
                # Validate Laravel caches
                [ ! -f "bootstrap/cache/config.php" ] && VALIDATION_ERRORS+=("config cache missing")
                [ ! -f "bootstrap/cache/routes-v7.php" ] && [ ! -f "bootstrap/cache/routes.php" ] && VALIDATION_ERRORS+=("route cache missing")
                
                if [ ${#VALIDATION_ERRORS[@]} -gt 0 ]; then
                    echo "❌ Build validation failed:"
                    for error in "${VALIDATION_ERRORS[@]}"; do
                        echo "   - $error"
                    done
                    exit 1
                else
                    echo "✅ Build validation successful"
                    echo "📁 Build ready at: $BUILD_DIR"
                fi
                VALIDATE_EOF
                
                chmod +x Admin-Local/Deployment/Scripts/validate-build-output.sh
                
                echo "✅ Build strategy configured successfully!"
                echo ""
                echo "📋 Available commands:"
                echo "   🔨 Execute build: ./Admin-Local/Deployment/Scripts/execute-build-strategy.sh"
                echo "   ✅ Validate build: ./Admin-Local/Deployment/Scripts/validate-build-output.sh"
                echo ""
                echo "⚡ Build Location: $BUILD_LOCATION"
                echo "🎯 Deployment Strategy: $DEPLOYMENT_STRATEGY"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/configure-build-strategy.sh
                
                ```
                
            2. Configure the build strategy.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/configure-build-strategy.sh
                
                ```
                
            3. Test the build execution (for local builds).
                
                ```bash
                ./Admin-Local/Deployment/Scripts/execute-build-strategy.sh
                
                ```
                
            4. Validate the build output.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/validate-build-output.sh
                
                ```

    7. **Step 17: Run Security Scans `(New Added)`**
       1. **Purpose:** Identify and fix potential security vulnerabilities before deployment.
       2. **Action:**
           1. Use a tool like Snyk or Larastan to scan the codebase for vulnerabilities.
   6. **Step 18: Customization Protection**
        1. **Purpose:** Implement a Laravel (with or without js)-compatible customization layer to protect changes during team or upstream vendor updates, and establish comprehensive investment protection documentation for the customization project.
        2. **Action: Create and use scripts from Universal Customization System and Investment Protection System templates**
            1. **Customization Layer Setup** - Use `setup-customization.sh` (ensure this customization project adapts per tech stack of marketplace app):
                - Create a protected directory structure for customizations in `app/Custom`, `config/custom`, `database/migrations/custom`, `resources/views/custom`, and `public/custom`.
                - Create custom configuration files (e.g., `config/custom-app.php`, `config/custom-database.php`).
                - Create a `CustomizationServiceProvider` to load custom routes, views, migrations, and blade directives.
                - Register the `CustomizationServiceProvider` in `config/app.php`.
                - Add custom environment variables to the `.env` file for feature toggles, branding, and other settings.
                - Create an update-safe asset strategy using a separate webpack configuration (e.g., `webpack.custom.js`).
            2. **Investment Protection Documentation** - Use `setup-customization-protection.sh`:
                - Set up comprehensive investment tracking and documentation system for the customization project
                - Create investment summary and ROI analysis tools for customization project
                - Implement customization catalog and business logic documentation
                - Generate API extensions, frontend changes, and database modifications documentation
                - Set up security enhancements and performance optimization tracking for customization project
                - Create integration points documentation and recovery procedures
                - Generate automated customization project documentation and team handoff materials
            3. **Stack Variations:**
                - **Blade-only:** Focus on `CustomizationServiceProvider` for backend logic and `resources/views/custom` for frontend overrides.
                - **Inertia/Vue/React:** Use the `CustomizationServiceProvider` for backend logic and create a separate, namespaced frontend component library within `resources/js/Custom` to avoid conflicts with vendor components.
    7. **Step 19: Data Persistence Strategy**
        1. **Purpose:** Implement a zero data loss system with smart content protection during deployments.
        - **✅ Goal Achieved:** "Zero data loss during deployments" with focus on user uploads, invoices, QR codes, exports
        - **✅ Correct Strategy:** Uses exclusion-based approach (everything is vendor code EXCEPT specific user data)
        - **✅ Smart Detection:** Auto-detects Laravel, Next.js, React/Vue with appropriate exclusions
        - **✅ Advanced Features:** Framework detection, smart build artifact exclusions, comprehensive documentation
        1. **Action:**
            1. Create an advanced persistence script (`link_persistent_dirs.sh`) with automatic framework detection and smart build artifact exclusions.
            2. Test the persistence script locally.
            3. Create data persistence documentation.
            4. Create a verification script (`verify_persistence.sh`).
    8. **Step 20: Commit Pre-Deployment Setup**
        1. **Purpose:** Commit all preparation work to the repository with comprehensive documentation.
        2. **Action:**
            1. Verify the current status of the repository and ensure sensitive files are excluded.
            2. Run a comprehensive verification script to ensure all Phase 2 steps are complete.
            3. Add all appropriate files to the staging area.
            4. Create a comprehensive commit message documenting all preparation work.
            5. Push the changes to the repository.
            6. Create a deployment readiness report.
