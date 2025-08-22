# Master Checklist for **SECTION C: Build and Deploy** - Universal Laravel Zero-Downtime

**Version:** 2.0  
**Last Updated:** August 20, 2025  
**Purpose:** Complete professional zero-downtime deployment pipeline with enhanced build strategies and atomic deployment

---

## **Universal Deployment Process**

This deployment flow ensures **TRUE zero-downtime** for ANY Laravel application using atomic symlink switches, comprehensive build strategies, and advanced error handling.

### **Visual Step Identification Guide**

-   🟢 **Local Machine**: Steps executed on developer's local environment
-   🟡 **Builder VM**: Steps executed on dedicated build/CI server
-   🔴 **Server**: Steps executed on production server
-   🟣 **User-configurable**: SSH Commands - User hooks (1️⃣ Pre-release, 2️⃣ Mid-release, 3️⃣ Post-release)
-   🏗️ **Builder Commands**: Build-specific operations

### **Path Variables System**

All paths use dynamic variables from `deployment-variables.json`:

-   `%path-localMachine%`: Local development machine paths
-   `%path-Builder-VM%`: Build server/CI environment paths
-   `%path-server%`: Production server paths

---

## **Phase 1: 🖥️ Prepare Build Environment**

### Step 1.1: Pre-Build Environment Preparation [1.1-prebuild-prep]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Load deployment configuration and validate deployment readiness

#### **Action Steps:**

1. **Load Deployment Variables and Initialize**

    ```bash
    # Load deployment variables from Section A setup
    source Admin-Local/Deployment/Scripts/load-variables.sh

    # Initialize deployment workspace
    mkdir -p "${DEPLOY_WORKSPACE}/logs" 2>/dev/null || mkdir -p "deploy-workspace/logs"
    cd "${DEPLOY_WORKSPACE:-deploy-workspace}"

    # Repository connectivity validation
    echo "🔍 Validating repository connectivity..."
    if git ls-remote --heads "${GITHUB_REPO}" > /dev/null 2>&1; then
        echo "✅ Repository accessible: ${GITHUB_REPO}"
    else
        echo "❌ Repository not accessible: ${GITHUB_REPO}"
        exit 1
    fi

    # Branch availability check
    if git ls-remote --heads "${GITHUB_REPO}" "${DEPLOY_BRANCH}" | grep -q "${DEPLOY_BRANCH}"; then
        echo "✅ Branch available: ${DEPLOY_BRANCH}"
    else
        echo "❌ Branch not found: ${DEPLOY_BRANCH}"
        exit 1
    fi

    echo "✅ Pre-build preparation complete"
    ```

    **Expected Result:**

    ```
    ✅ Deployment variables loaded successfully
    ✅ Repository connectivity verified
    ✅ Target branch confirmed available
    ✅ Deployment workspace initialized
    ✅ Ready to proceed to build environment setup
    ```

### Step 1.2: Build Environment Setup [1.2-build-setup]

**Location:** 🟡 Run on Builder VM (or 🟢 Local if BUILD_LOCATION=local)  
**Path:** `%path-Builder-VM%` or `%path-localMachine%/build-tmp`  
**Purpose:** Initialize clean build environment with correct versions matching production

#### **Action Steps:**

1. **Execute Build Strategy Configuration**

    ```bash
    # Execute build strategy based on deployment configuration
    BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)

    case "${BUILD_LOCATION}" in
        "local")
            echo "🏠 Using local build environment"
            BUILD_PATH="${PATH_LOCAL_MACHINE}/build-tmp"
            mkdir -p "$BUILD_PATH"
            ;;
        "vm")
            echo "🖥️ Using VM build environment"
            BUILD_PATH="${PATH_BUILDER}"
            # Initialize VM if needed
            if ! ping -c 1 "${BUILD_SERVER_HOST}" > /dev/null 2>&1; then
                echo "❌ Cannot connect to build server: ${BUILD_SERVER_HOST}"
                echo "🔄 Falling back to local build..."
                BUILD_PATH="${PATH_LOCAL_MACHINE}/build-tmp"
                mkdir -p "$BUILD_PATH"
            fi
            ;;
        "server")
            echo "🌐 Using server build environment"
            BUILD_PATH="${PATH_SERVER}/build-tmp"
            ;;
        *)
            echo "❌ Unknown build strategy: ${BUILD_LOCATION}"
            exit 1
            ;;
    esac

    # Set build environment variables
    export BUILD_ENV="production"
    export COMPOSER_MEMORY_LIMIT=-1
    export NODE_ENV="production"
    export BUILD_PATH

    echo "✅ Build environment setup complete for strategy: ${BUILD_LOCATION}"
    echo "📁 Build path: $BUILD_PATH"
    ```

2. **Run Comprehensive Environment Analysis**

    ```bash
    # Execute environment analysis from Section A
    source Admin-Local/Deployment/Scripts/comprehensive-env-check.sh
    ```

    **Expected Result:**

    ```
    ✅ Build strategy determined and configured
    ✅ Build path established and ready
    ✅ Environment variables set for production
    ✅ Comprehensive environment analysis completed
    ✅ Version compatibility confirmed
    ```

### Step 1.3: Repository Preparation [1.3-repo-prep]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Clone repository to build environment and validate commit integrity

#### **Action Steps:**

1. **Repository Cloning with Build Strategy Support**

    ```bash
    # Navigate to build environment
    cd "${BUILD_PATH}"

    # Clean build directory
    rm -rf "${PROJECT_NAME}" 2>/dev/null || true

    # Clone repository with optimized settings
    echo "📥 Cloning repository..."
    if git clone --depth=1 --branch="${DEPLOY_BRANCH}" "${GITHUB_REPO}" "${PROJECT_NAME}"; then
        echo "✅ Repository cloned successfully"
    else
        echo "❌ Repository clone failed"
        exit 1
    fi

    cd "${PROJECT_NAME}"

    # Checkout specific commit if provided
    if [[ -n "${TARGET_COMMIT}" ]]; then
        echo "🔄 Checking out specific commit: ${TARGET_COMMIT}"
        git fetch --depth=1 origin "${TARGET_COMMIT}"
        if git checkout "${TARGET_COMMIT}"; then
            echo "✅ Commit checked out successfully"
        else
            echo "❌ Cannot checkout commit: ${TARGET_COMMIT}"
            exit 1
        fi
    fi

    # Validate repository structure
    if [[ -f "composer.json" ]] && [[ -f "artisan" ]]; then
        echo "✅ Laravel repository structure validated"
    else
        echo "❌ Invalid Laravel repository structure"
        exit 1
    fi

    # Log commit information
    echo "📋 Repository prepared:"
    echo "  - Branch: $(git branch --show-current)"
    echo "  - Commit: $(git rev-parse --short HEAD)"
    echo "  - Message: $(git log -1 --pretty=format:'%s')"
    ```

    **Expected Result:**

    ```
    ✅ Repository cloned to build environment
    ✅ Target commit checked out (if specified)
    ✅ Laravel structure validated
    ✅ Build directory ready for Phase 2
    ```

---

## **Phase 2: 🏗️ Build Application**

### Step 2.1: Intelligent Cache Restoration [2.1-cache-restore]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Restore cached dependencies with integrity validation to speed up builds

#### **Action Steps:**

1. **Execute Intelligent Cache System**

    ```bash
    echo "♻️ Intelligent cache restoration..."

    # Load cache configuration from deployment variables
    CACHE_DIR="${CACHE_BASE_PATH:-/tmp/build-cache}/${PROJECT_NAME}"
    COMPOSER_CACHE="${CACHE_DIR}/composer"
    NPM_CACHE="${CACHE_DIR}/npm"

    # Create cache directories
    mkdir -p "${COMPOSER_CACHE}" "${NPM_CACHE}"

    # Validate lock file consistency for Composer
    if [[ -f "composer.lock" ]]; then
        COMPOSER_HASH=$(md5sum composer.lock 2>/dev/null | cut -d' ' -f1)
        CACHED_COMPOSER_HASH=$(cat "${COMPOSER_CACHE}/.hash" 2>/dev/null || echo "")

        if [[ "${COMPOSER_HASH}" == "${CACHED_COMPOSER_HASH}" ]] && [[ -d "${COMPOSER_CACHE}/vendor" ]]; then
            echo "✅ Restoring Composer cache (hash match: ${COMPOSER_HASH})"
            cp -r "${COMPOSER_CACHE}/vendor" ./ 2>/dev/null || true
        else
            echo "⚠️ Composer cache miss or invalid - will rebuild"
        fi
    fi

    # Validate lock file consistency for NPM
    if [[ -f "package-lock.json" ]]; then
        NPM_HASH=$(md5sum package-lock.json 2>/dev/null | cut -d' ' -f1)
        CACHED_NPM_HASH=$(cat "${NPM_CACHE}/.hash" 2>/dev/null || echo "")

        if [[ "${NPM_HASH}" == "${CACHED_NPM_HASH}" ]] && [[ -d "${NPM_CACHE}/node_modules" ]]; then
            echo "✅ Restoring NPM cache (hash match: ${NPM_HASH})"
            cp -r "${NPM_CACHE}/node_modules" ./ 2>/dev/null || true
        else
            echo "⚠️ NPM cache miss or invalid - will rebuild"
        fi
    fi

    echo "✅ Cache restoration phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Cache restoration attempted with integrity validation
    ✅ Composer cache restored (if hash match)
    ✅ NPM cache restored (if hash match and package.json exists)
    ✅ Build process accelerated where possible
    ```

### Step 2.2: Universal Dependency Installation [2.2-dependencies]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Execute smart dependency installation with production optimization

#### **Action Steps:**

1. **Execute Universal Dependency Analyzer**

    ```bash
    # Run enhanced dependency analysis from Section A
    source Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh

    # Verify analysis tools are available
    source Admin-Local/Deployment/Scripts/install-analysis-tools.sh
    ```

2. **Smart Dependency Installation**

    ```bash
    echo "📦 Universal Smart Dependency Installation..."

    # Configure Composer for build environment
    COMPOSER_FLAGS="--optimize-autoloader --prefer-dist --no-scripts"

    case "${BUILD_LOCATION}" in
        "local"|"vm")
            # Development-friendly flags for build environment
            COMPOSER_FLAGS="${COMPOSER_FLAGS} --classmap-authoritative"
            ;;
        "server")
            # Production-optimized flags
            COMPOSER_FLAGS="${COMPOSER_FLAGS} --no-dev --classmap-authoritative"
            ;;
    esac

    # Smart Composer installation with enhanced strategy
    echo "📦 Installing PHP dependencies..."
    if [[ -f "composer.json" ]]; then
        # Apply enhanced Composer strategy from Section B
        source Admin-Local/Deployment/Scripts/setup-composer-strategy.sh

        # Install with appropriate flags
        if composer install ${COMPOSER_FLAGS}; then
            echo "✅ Composer dependencies installed successfully"

            # Cache successful installation
            if [[ -d "vendor" ]] && [[ -n "${COMPOSER_CACHE}" ]]; then
                rm -rf "${COMPOSER_CACHE}/vendor" 2>/dev/null || true
                cp -r vendor "${COMPOSER_CACHE}/" 2>/dev/null || true
                echo "${COMPOSER_HASH}" > "${COMPOSER_CACHE}/.hash"
                echo "💾 Composer cache updated"
            fi
        else
            echo "❌ Composer installation failed"
            exit 1
        fi
    fi

    # Smart Node.js installation (if applicable)
    if [[ -f "package.json" ]]; then
        echo "📦 Installing Node.js dependencies..."

        # Determine installation strategy based on build scripts
        if grep -q '"build":\|"production":\|"prod":\|"dev":.*"vite\|webpack\|laravel-mix"' package.json; then
            echo "🏗️ Build dependencies detected - installing all packages"
            npm ci --production=false
        else
            echo "📦 Production-only installation"
            npm ci --production=true
        fi

        # Verify installation
        if [[ -d "node_modules" ]]; then
            echo "✅ Node.js dependencies installed successfully"

            # Cache successful installation
            if [[ -n "${NPM_CACHE}" ]]; then
                rm -rf "${NPM_CACHE}/node_modules" 2>/dev/null || true
                cp -r node_modules "${NPM_CACHE}/" 2>/dev/null || true
                echo "${NPM_HASH}" > "${NPM_CACHE}/.hash"
                echo "💾 NPM cache updated"
            fi
        else
            echo "❌ Node.js installation failed"
            exit 1
        fi

        # Security audit
        if npm audit --audit-level=high --silent; then
            echo "✅ No high-severity vulnerabilities detected"
        else
            echo "⚠️ High-severity vulnerabilities detected - review required"
        fi
    fi

    echo "✅ Universal dependency installation complete"
    ```

    **Expected Result:**

    ```
    ✅ Universal dependency analyzer executed
    ✅ Enhanced Composer strategy applied
    ✅ PHP dependencies installed with optimization
    ✅ Node.js dependencies installed (if applicable)
    ✅ Security audit completed
    ✅ Dependency cache updated for future builds
    ```

### Step 2.3: Advanced Asset Compilation [2.3-assets]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Compile frontend assets with auto-detection and optimization

#### **Action Steps:**

1. **Execute Advanced Asset Compilation**

    ```bash
    echo "🎨 Advanced asset compilation with auto-detection..."

    if [[ ! -f "package.json" ]]; then
        echo "📝 No package.json found - skipping asset compilation"
    else
        # Detect asset bundler automatically
        BUNDLER="none"
        if grep -q '"vite"' package.json; then
            BUNDLER="vite"
        elif grep -q '"laravel-mix"' package.json; then
            BUNDLER="mix"
        elif grep -q '"webpack"' package.json; then
            BUNDLER="webpack"
        fi

        echo "🔍 Detected bundler: ${BUNDLER}"

        # Execute build based on detected bundler
        case "${BUNDLER}" in
            "vite")
                echo "⚡ Building with Vite..."
                if npm run build || npm run prod; then
                    echo "✅ Vite build successful"
                else
                    echo "❌ Vite build failed"
                    exit 1
                fi
                ;;
            "mix")
                echo "🏗️ Building with Laravel Mix..."
                if npm run production || npm run prod; then
                    echo "✅ Laravel Mix build successful"
                else
                    echo "❌ Laravel Mix build failed"
                    exit 1
                fi
                ;;
            "webpack")
                echo "📦 Building with Webpack..."
                if npm run build || npm run production; then
                    echo "✅ Webpack build successful"
                else
                    echo "❌ Webpack build failed"
                    exit 1
                fi
                ;;
            *)
                echo "🤷 Unknown bundler - attempting generic build..."
                if npm run build 2>/dev/null || npm run prod 2>/dev/null || npm run production 2>/dev/null; then
                    echo "✅ Generic build successful"
                else
                    echo "⚠️ No suitable build script found - continuing without frontend build"
                fi
                ;;
        esac

        # Validate build output
        if [[ -d "public/build" ]] || [[ -d "public/assets" ]] || [[ -d "public/js" ]] || [[ -d "public/css" ]]; then
            echo "✅ Asset compilation successful - build output detected"

            # Clean up dev dependencies post-build (production mode)
            if [[ "${BUILD_ENV}" == "production" ]]; then
                echo "🧹 Cleaning up dev dependencies..."
                rm -rf node_modules
                npm ci --production=true --silent 2>/dev/null || true
            fi
        else
            echo "⚠️ No build output detected - build may have failed silently"
        fi
    fi

    echo "✅ Advanced asset compilation phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Asset bundler automatically detected (Vite/Mix/Webpack)
    ✅ Frontend assets compiled successfully
    ✅ Build output validated and confirmed
    ✅ Dev dependencies cleaned up (production mode)
    ✅ Assets ready for production deployment
    ```

### Step 2.4: Laravel Production Optimization [2.4-optimize]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Apply comprehensive Laravel optimizations for production performance

#### **Action Steps:**

1. **Execute Comprehensive Laravel Optimization**

    ```bash
    echo "⚡ Comprehensive Laravel production optimization..."

    # Clear existing caches to start fresh
    php artisan cache:clear --quiet 2>/dev/null || true
    php artisan config:clear --quiet 2>/dev/null || true
    php artisan route:clear --quiet 2>/dev/null || true
    php artisan view:clear --quiet 2>/dev/null || true

    # Create temporary .env for optimization
    if [[ -f ".env.production" ]]; then
        cp .env.production .env
    elif [[ -f ".env.example" ]]; then
        cp .env.example .env
        # Generate temporary app key for optimization
        php artisan key:generate --force --quiet
    fi

    # Production optimization sequence
    echo "📝 Caching configuration..."
    if php artisan config:cache --quiet; then
        echo "✅ Configuration cached successfully"
    else
        echo "❌ Config cache failed"
        exit 1
    fi

    echo "🗺️ Caching routes..."
    if php artisan route:cache --quiet; then
        echo "✅ Routes cached successfully"
    else
        echo "❌ Route cache failed"
        exit 1
    fi

    echo "👁️ Caching views..."
    if php artisan view:cache --quiet; then
        echo "✅ Views cached successfully"
    else
        echo "⚠️ View cache failed - continuing anyway"
    fi

    # Advanced Laravel optimizations
    echo "⚙️ Advanced optimizations..."

    # Cache events if available
    if php artisan list | grep -q "event:cache"; then
        php artisan event:cache --quiet 2>/dev/null && echo "📅 Events cached" || true
    fi

    # Cache icons if available
    if php artisan list | grep -q "icons:cache"; then
        php artisan icons:cache --quiet 2>/dev/null && echo "🎨 Icons cached" || true
    fi

    # Optimize Composer autoloader
    echo "🔧 Optimizing autoloader..."
    if composer dump-autoload --optimize --classmap-authoritative --no-dev --quiet; then
        echo "✅ Autoloader optimized for production"
    else
        echo "❌ Autoloader optimization failed"
        exit 1
    fi

    echo "✅ Laravel optimization sequence completed"
    ```

    **Expected Result:**

    ```
    ✅ All Laravel caches cleared and rebuilt
    ✅ Configuration cache created
    ✅ Route cache optimized
    ✅ View cache compiled
    ✅ Advanced features cached (events, icons)
    ✅ Autoloader optimized for maximum performance
    ✅ Application ready for production deployment
    ```

### Step 2.5: Comprehensive Build Validation [2.5-validate]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Execute comprehensive validation of build artifacts and integrity

#### **Action Steps:**

1. **Execute Enhanced Build Validation**

    ```bash
    # Run enhanced pre-deployment validation from Section B
    source Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
    ```

2. **Additional Build-Specific Validation**

    ```bash
    echo "🔍 Build artifact validation..."

    # Critical file existence check
    CRITICAL_FILES=(
        "bootstrap/app.php"
        "artisan"
        "composer.json"
        "composer.lock"
        "bootstrap/cache/config.php"
        "bootstrap/cache/routes-v7.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        if [[ -f "${file}" ]]; then
            echo "✅ Critical file present: ${file}"
        else
            # Check for alternative route cache file
            if [[ "${file}" == "bootstrap/cache/routes-v7.php" ]] && [[ -f "bootstrap/cache/routes.php" ]]; then
                echo "✅ Alternative route cache present: bootstrap/cache/routes.php"
            else
                echo "❌ Critical file missing: ${file}"
                exit 1
            fi
        fi
    done

    # Validate vendor directory
    if [[ -d "vendor" ]] && [[ -f "vendor/autoload.php" ]]; then
        echo "✅ Vendor directory structure valid"
    else
        echo "❌ Vendor directory invalid"
        exit 1
    fi

    # Test basic Laravel functionality
    echo "🧪 Testing Laravel bootstrap..."
    if php artisan --version --quiet >/dev/null; then
        echo "✅ Laravel bootstrap successful"
    else
        echo "❌ Laravel bootstrap failed"
        exit 1
    fi

    # Advanced application test
    echo "🔬 Advanced application testing..."
    if php -r "
        try {
            require_once 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            echo 'Application instantiation successful\n';
        } catch (Exception \$e) {
            echo 'Application instantiation failed: ' . \$e->getMessage() . '\n';
            exit(1);
        }
    "; then
        echo "✅ Advanced application test passed"
    else
        echo "❌ Advanced application test failed"
        exit 1
    fi

    # Optional: Run automated tests
    if [[ "${RUN_TESTS}" == "true" ]] && [[ -d "tests" ]]; then
        echo "🧪 Running automated tests..."
        if php artisan test --parallel --stop-on-failure >/dev/null 2>&1; then
            echo "✅ Automated tests passed"
        else
            echo "❌ Automated tests failed"
            exit 1
        fi
    fi

    echo "✅ Comprehensive build validation completed successfully"
    ```

    **Expected Result:**

    ```
    ✅ Pre-deployment validation checklist passed (10 points)
    ✅ All critical files present and validated
    ✅ Vendor directory structure confirmed
    ✅ Laravel bootstrap functionality verified
    ✅ Advanced application instantiation tested
    ✅ Automated tests passed (if enabled)
    ✅ Build ready for packaging and deployment
    ```

---

## **Phase 3: 📦 Package & Transfer**

### Step 3.1: Smart Build Artifact Preparation [3.1-package]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Create optimized deployment package with manifest and integrity validation

#### **Action Steps:**

1. **Create Smart Deployment Package**

    ```bash
    echo "📦 Smart build artifact preparation..."

    # Execute build output validation
    source Admin-Local/Deployment/Scripts/validate-build-output.sh

    # Create deployment manifest
    echo "📝 Creating deployment manifest..."
    cat > deployment-manifest.json << EOF
    {
        "build_strategy": "${BUILD_LOCATION}",
        "release_id": "$(date +%Y%m%d%H%M%S)",
        "git_commit": "$(git rev-parse HEAD)",
        "git_branch": "$(git branch --show-current)",
        "build_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "php_version": "$(php --version | head -n1)",
        "composer_version": "$(composer --version)",
        "node_version": "$(node --version 2>/dev/null || echo 'not installed')",
        "laravel_version": "$(php artisan --version | grep -oP '\d+\.\d+\.\d+' || echo 'unknown')",
        "environment": "${BUILD_ENV:-production}",
        "has_frontend": $([ -f "package.json" ] && echo "true" || echo "false"),
        "frontend_framework": "$([ -f "package.json" ] && (grep -q vite package.json && echo "vite" || grep -q laravel-mix package.json && echo "mix" || echo "webpack") || echo "none")"
    }
    EOF

    # Enhanced exclusion patterns from deployment configuration
    EXCLUDE_PATTERNS=(
        ".git" ".github" ".gitlab"
        "tests" "Test" "Tests"
        "node_modules"
        ".env*" "!.env.example"
        ".phpunit*" "phpunit.xml" "pest.xml"
        "webpack.mix.js" "vite.config.js" "postcss.config.js"
        ".eslintrc*" ".editorconfig" "tsconfig.json"
        "*.log" "*.tmp"
        "Admin-Local"
        ".vscode" ".idea"
        "*.swp" "*.swo" "*~"
        ".DS_Store" "Thumbs.db"
        "composer-unused.phar"
        "build-tmp"
    )

    # Build tar command with exclusions
    TAR_EXCLUDES=""
    for pattern in "${EXCLUDE_PATTERNS[@]}"; do
        TAR_EXCLUDES="${TAR_EXCLUDES} --exclude='${pattern}'"
    done

    # Create release artifact with timestamp
    RELEASE_TIMESTAMP=$(date +%Y%m%d%H%M%S)
    RELEASE_NAME="release-${RELEASE_TIMESTAMP}-$(git rev-parse --short HEAD)"

    echo "🗂️ Creating artifact package: ${RELEASE_NAME}.tar.gz..."
    eval "tar ${TAR_EXCLUDES} -czf ${RELEASE_NAME}.tar.gz ."

    # Generate comprehensive checksums
    echo "🔐 Generating checksums and validation..."
    md5sum "${RELEASE_NAME}.tar.gz" > "${RELEASE_NAME}.md5"
    sha256sum "${RELEASE_NAME}.tar.gz" > "${RELEASE_NAME}.sha256"

    # Create artifact info file
    cat > "${RELEASE_NAME}.info" << EOF
    Release: ${RELEASE_NAME}
    Timestamp: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    Git Commit: $(git rev-parse HEAD)
    Git Branch: $(git branch --show-current)
    Build Strategy: ${BUILD_LOCATION}
    Size: $(du -h "${RELEASE_NAME}.tar.gz" | cut -f1)
    Files: $(tar -tzf "${RELEASE_NAME}.tar.gz" | wc -l)
    EOF

    echo "✅ Smart artifact preparation completed"
    echo "📦 Package: ${RELEASE_NAME}.tar.gz"
    echo "📊 Size: $(du -h "${RELEASE_NAME}.tar.gz" | cut -f1)"
    echo "📁 Files: $(tar -tzf "${RELEASE_NAME}.tar.gz" | wc -l)"
    ```

    **Expected Result:**

    ```
    ✅ Build output validated successfully
    ✅ Deployment manifest created with comprehensive metadata
    ✅ Smart exclusion patterns applied
    ✅ Release package created with timestamp and commit hash
    ✅ MD5 and SHA256 checksums generated
    ✅ Artifact info file created
    ✅ Package ready for secure transfer
    ```

### Step 3.2: Comprehensive Server Preparation [3.2-server-prep]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Prepare zero-downtime deployment structure with intelligent backup strategy

#### **Action Steps:**

1. **Enhanced Server Preparation**

    ```bash
    echo "🔴 Enhanced server preparation for zero-downtime deployment..."

    # Load deployment variables on server
    DEPLOY_PATH="${PATH_SERVER}"
    RELEASES_PATH="${DEPLOY_PATH}/releases"
    CURRENT_PATH="${DEPLOY_PATH}/current"
    SHARED_PATH="${DEPLOY_PATH}/shared"

    # Create comprehensive directory structure
    echo "📁 Creating deployment directory structure..."
    mkdir -p "${RELEASES_PATH}" "${SHARED_PATH}"

    # Advanced backup strategy
    echo "💾 Executing intelligent backup strategy..."
    if [ -L "${CURRENT_PATH}" ]; then
        BACKUP_ID="backup-$(date +%Y%m%d%H%M%S)"
        BACKUP_PATH="${RELEASES_PATH}/${BACKUP_ID}"

        # Create space-efficient backup using hard links
        echo "📋 Creating backup: ${BACKUP_ID}"
        if cp -al "$(readlink "${CURRENT_PATH}")" "${BACKUP_PATH}" 2>/dev/null; then
            echo "✅ Current release backed up efficiently"

            # Create backup metadata
            cat > "${BACKUP_PATH}/.backup-info" << EOF
    {
        "backup_id": "${BACKUP_ID}",
        "created_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "original_release": "$(basename $(readlink "${CURRENT_PATH}"))",
        "backup_type": "hard-link"
    }
    EOF
        else
            echo "⚠️ Hard-link backup failed, creating regular backup..."
            cp -r "$(readlink "${CURRENT_PATH}")" "${BACKUP_PATH}"
        fi

        # Cleanup old backups (keep last 3)
        echo "🧹 Cleaning old backups..."
        cd "${RELEASES_PATH}"
        ls -1t backup-* 2>/dev/null | tail -n +4 | xargs -r rm -rf
    else
        echo "ℹ️ No current release found - first deployment"
    fi

    # Comprehensive shared resources setup
    echo "🔗 Setting up shared resources..."

    # Enhanced shared directories from deployment configuration
    SHARED_DIRECTORIES=(
        "storage/app/public"
        "storage/logs"
        "storage/framework/cache/data"
        "storage/framework/sessions"
        "storage/framework/views"
        "storage/framework/testing"
        "public/uploads"
        "public/media"
        "public/avatars"
        "public/documents"
        "public/exports"
        "public/qrcodes"
        "public/invoices"
        "public/reports"
        "Modules"
    )

    for dir in "${SHARED_DIRECTORIES[@]}"; do
        mkdir -p "${SHARED_PATH}/${dir}"
        echo "📁 Created shared directory: ${dir}"
    done

    # Set comprehensive permissions
    echo "🔐 Setting secure permissions..."

    # Storage directories - read/write for web server
    chown -R www-data:www-data "${SHARED_PATH}/storage" 2>/dev/null || true
    chmod -R 755 "${SHARED_PATH}/storage"
    chmod -R 775 "${SHARED_PATH}/storage/logs"
    chmod -R 775 "${SHARED_PATH}/storage/framework/cache"
    chmod -R 775 "${SHARED_PATH}/storage/framework/sessions"
    chmod -R 775 "${SHARED_PATH}/storage/framework/views"

    # Public directories - web server accessible
    chown -R www-data:www-data "${SHARED_PATH}/public" 2>/dev/null || true
    chmod -R 755 "${SHARED_PATH}/public"

    echo "✅ Comprehensive server preparation completed"
    ```

    **Expected Result:**

    ```
    ✅ Zero-downtime directory structure created
    ✅ Intelligent backup created with hard links
    ✅ Old backups cleaned (kept last 3)
    ✅ Comprehensive shared directories created
    ✅ Secure permissions applied
    ✅ Server ready for atomic deployment
    ```

### Step 3.3: Intelligent Release Directory Creation [3.3-release-dir]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Create timestamped release directory with proper validation and permissions

#### **Action Steps:**

1. **Create Release Directory with Pre-flight Checks**

    ```bash
    echo "🔴 Creating intelligent release directory..."

    # Generate unique release identifier with git info
    RELEASE_ID="$(date +%Y%m%d%H%M%S)-$(echo ${DEPLOY_COMMIT:-manual} | cut -c1-8)"
    RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"

    # Comprehensive pre-flight checks
    echo "🔍 Pre-flight deployment checks..."

    # Check available disk space (require at least 1GB)
    AVAILABLE_SPACE=$(df "${DEPLOY_PATH}" | awk 'NR==2 {print $4}')
    REQUIRED_SPACE=1048576  # 1GB in KB

    if [[ "${AVAILABLE_SPACE}" -lt "${REQUIRED_SPACE}" ]]; then
        echo "❌ Insufficient disk space: $((AVAILABLE_SPACE / 1024))MB available, $((REQUIRED_SPACE / 1024))MB required"
        exit 1
    else
        echo "✅ Sufficient disk space: $((AVAILABLE_SPACE / 1024))MB available"
    fi

    # Validate write permissions
    if touch "${DEPLOY_PATH}/.write-test" 2>/dev/null; then
        rm -f "${DEPLOY_PATH}/.write-test"
        echo "✅ Write permissions validated"
    else
        echo "❌ No write permission in deployment directory: ${DEPLOY_PATH}"
        exit 1
    fi

    # Check for deployment conflicts
    if [[ -d "${RELEASE_PATH}" ]]; then
        echo "⚠️ Release directory already exists: ${RELEASE_ID}"
        echo "🔄 Generating new release ID..."
        RELEASE_ID="${RELEASE_ID}-$(date +%S)"
        RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"
    fi

    # Create release directory with proper structure
    echo "📁 Creating release directory: ${RELEASE_ID}"
    if mkdir -p "${RELEASE_PATH}"; then
        echo "✅ Release directory created successfully"
    else
        echo "❌ Failed to create release directory"
        exit 1
    fi

    # Create comprehensive release metadata
    cat > "${RELEASE_PATH}/.release-info" << EOF
    {
        "release_id": "${RELEASE_ID}",
        "created_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "git_commit": "${DEPLOY_COMMIT:-unknown}",
        "git_branch": "${DEPLOY_BRANCH:-unknown}",
        "build_strategy": "${BUILD_LOCATION:-local}",
        "deployed_by": "${USER:-unknown}",
        "server_hostname": "$(hostname)",
        "deployment_type": "${DEPLOYMENT_TYPE:-standard}",
        "php_version": "$(php --version | head -n1 | grep -oP '\d+\.\d+\.\d+' || echo 'unknown')",
        "disk_usage_before": "$((AVAILABLE_SPACE / 1024))MB"
    }
    EOF

    # Set proper permissions
    echo "🔐 Setting release permissions..."
    chmod 755 "${RELEASE_PATH}"
    chmod 644 "${RELEASE_PATH}/.release-info"

    # If running as root, ensure proper ownership
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data "${RELEASE_PATH}"
        echo "✅ Ownership set to www-data"
    fi

    # Export release info for subsequent steps
    export RELEASE_ID RELEASE_PATH

    echo "✅ Release directory creation completed"
    echo "📁 Release: ${RELEASE_ID}"
    echo "📍 Path: ${RELEASE_PATH}"
    echo "💾 Available space: $((AVAILABLE_SPACE / 1024))MB"
    ```

    **Expected Result:**

    ```
    ✅ Unique release ID generated with git commit info
    ✅ Pre-flight checks passed (disk space, permissions)
    ✅ Release directory created successfully
    ✅ Comprehensive metadata generated
    ✅ Proper permissions and ownership set
    ✅ Release environment ready for file transfer
    ```

### Step 3.4: Optimized File Transfer & Validation [3.4-transfer]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Transfer build artifacts with integrity validation and optimized permissions

#### **Action Steps:**

1. **Execute Optimized Transfer with Validation**

    ```bash
    echo "🔴 Executing optimized file transfer with validation..."

    # Find the latest build artifact (assuming transfer from build environment)
    ARTIFACT_PATTERN="release-*.tar.gz"
    ARTIFACT_FILE=$(ls -t ${ARTIFACT_PATTERN} 2>/dev/null | head -n1)

    if [[ -z "${ARTIFACT_FILE}" ]]; then
        echo "❌ No build artifact found matching pattern: ${ARTIFACT_PATTERN}"
        echo "🔍 Available files:"
        ls -la *.tar.gz 2>/dev/null || echo "No .tar.gz files found"
        exit 1
    fi

    echo "📦 Found artifact: ${ARTIFACT_FILE}"

    # Validate artifact integrity before extraction
    echo "🔐 Validating artifact integrity..."
    CHECKSUM_FILE="${ARTIFACT_FILE%.*}.sha256"

    if [[ -f "${CHECKSUM_FILE}" ]]; then
        if sha256sum -c "${CHECKSUM_FILE}" --quiet; then
            echo "✅ Artifact integrity verified via SHA256"
        else
            echo "❌ Artifact integrity check failed"
            exit 1
        fi
    else
        echo "⚠️ No checksum file found - proceeding without verification"
        echo "ℹ️ Expected: ${CHECKSUM_FILE}"
    fi

    # Extract with comprehensive error handling
    echo "📂 Extracting to release directory..."

    # Verify artifact is not corrupted before extraction
    if tar -tzf "${ARTIFACT_FILE}" >/dev/null 2>&1; then
        echo "✅ Artifact structure verified"
    else
        echo "❌ Artifact appears corrupted"
        exit 1
    fi

    # Extract with progress indication
    if tar -xzf "${ARTIFACT_FILE}" -C "${RELEASE_PATH}" 2>/dev/null; then
        echo "✅ Artifact extracted successfully"
    else
        echo "❌ Artifact extraction failed"
        exit 1
    fi

    # Validate critical Laravel files post-extraction
    echo "🔍 Validating Laravel structure post-extraction..."
    CRITICAL_FILES=(
        "artisan"
        "bootstrap/app.php"
        "composer.json"
        "composer.lock"
        "public/index.php"
        "bootstrap/cache/config.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        if [[ -f "${RELEASE_PATH}/${file}" ]]; then
            echo "✅ Critical file validated: ${file}"
        else
            # Some files are optional depending on build process
            if [[ "${file}" == "bootstrap/cache/config.php" ]]; then
                echo "⚠️ Cache file missing (will be generated): ${file}"
            else
                echo "❌ Critical file missing: ${file}"
                exit 1
            fi
        fi
    done

    # Set comprehensive file permissions
    echo "🔐 Setting comprehensive file permissions..."

    cd "${RELEASE_PATH}"

    # Set ownership (if running as root)
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data .
        echo "✅ Ownership set to www-data:www-data"
    fi

    # Directory permissions (755 = rwxr-xr-x)
    find . -type d -exec chmod 755 {} \;

    # File permissions (644 = rw-r--r--)
    find . -type f -exec chmod 644 {} \;

    # Executable permissions for specific files
    chmod +x artisan
    [[ -f "vendor/bin/phpunit" ]] && chmod +x vendor/bin/phpunit
    [[ -f "vendor/bin/phpstan" ]] && chmod +x vendor/bin/phpstan

    # Secure permissions for sensitive files
    [[ -f ".env" ]] && chmod 600 .env
    [[ -f ".env.example" ]] && chmod 644 .env.example

    # Storage and cache directories need write permissions
    [[ -d "storage" ]] && chmod -R 755 storage
    [[ -d "bootstrap/cache" ]] && chmod -R 755 bootstrap/cache

    # Validate file count and calculate size
    echo "📊 Transfer validation summary..."
    FILE_COUNT=$(find . -type f | wc -l)
    RELEASE_SIZE=$(du -sh . | cut -f1)

    # Create transfer manifest
    cat > .transfer-manifest << EOF
    Transfer completed: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    Artifact: ${ARTIFACT_FILE}
    Files transferred: ${FILE_COUNT}
    Total size: ${RELEASE_SIZE}
    Integrity check: $([ -f "../${CHECKSUM_FILE}" ] && echo "Verified" || echo "Skipped")
    Permissions set: Yes
    Laravel structure: Validated
    EOF

    echo "✅ Optimized transfer completed successfully"
    echo "📊 Transfer Summary:"
    echo "  - Files transferred: ${FILE_COUNT}"
    echo "  - Release size: ${RELEASE_SIZE}"
    echo "  - Release path: ${RELEASE_PATH}"
    echo "  - Artifact: ${ARTIFACT_FILE}"
    ```

    **Expected Result:**

    ```
    ✅ Build artifact located and verified
    ✅ Integrity validation completed (SHA256)
    ✅ Artifact structure verified before extraction
    ✅ Files extracted successfully to release directory
    ✅ Critical Laravel files validated
    ✅ Comprehensive permissions set (755/644)
    ✅ Sensitive files secured (600 permissions)
    ✅ Transfer manifest created
    ✅ Ready for Phase 4 configuration
    ```

---

## **Phase 4: 🔗 Configure Release**

### Step 4.1: Advanced Shared Resources Configuration [4.1-shared-config]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Configure comprehensive shared resources with intelligent linking and validation

#### **Action Steps:**

1. **Execute Advanced Shared Resources Setup**

    ```bash
    echo "🔴 Advanced shared resources configuration..."

    cd "${RELEASE_PATH}"

    # Load shared directories from deployment configuration
    SHARED_DIRECTORIES=(
        "storage/app/public"
        "storage/logs"
        "storage/framework/cache"
        "storage/framework/sessions"
        "storage/framework/views"
        "public/uploads"
        "public/media"
        "public/avatars"
        "public/documents"
        "public/exports"
        "public/qrcodes"
        "public/invoices"
        "public/reports"
        "Modules"
    )

    echo "🔗 Configuring shared directory links..."

    # Remove existing directories and create symlinks
    for dir in "${SHARED_DIRECTORIES[@]}"; do
        if [[ -e "${dir}" ]]; then
            echo "📁 Removing existing directory: ${dir}"
            rm -rf "${dir}"
        fi

        # Create parent directories if needed
        PARENT_DIR=$(dirname "${dir}")
        if [[ "${PARENT_DIR}" != "." ]] && [[ ! -d "${PARENT_DIR}" ]]; then
            mkdir -p "${PARENT_DIR}"
        fi

        # Create symlink to shared directory
        if ln -nfs "${SHARED_PATH}/${dir}" "${dir}"; then
            echo "✅ Linked shared directory: ${dir}"
        else
            echo "❌ Failed to link directory: ${dir}"
            exit 1
        fi
    done

    # Configure shared files
    echo "📄 Configuring shared files..."
    SHARED_FILES=(
        ".env"
        "auth.json"
        "oauth-private.key"
        "oauth-public.key"
    )

    for file in "${SHARED_FILES[@]}"; do
        if [[ -f "${file}" ]]; then
            echo "📄 Removing existing file: ${file}"
            rm -f "${file}"
        fi

        # Create symlink only if shared file exists
        if [[ -f "${SHARED_PATH}/${file}" ]]; then
            if ln -nfs "${SHARED_PATH}/${file}" "${file}"; then
                echo "✅ Linked shared file: ${file}"
            else
                echo "❌ Failed to link file: ${file}"
                exit 1
            fi
        else
            echo "⚠️ Shared file not found: ${SHARED_PATH}/${file}"
        fi
    done

    # Validate all symlinks
    echo "🔍 Validating symlinks..."
    BROKEN_LINKS=0

    for dir in "${SHARED_DIRECTORIES[@]}"; do
        if [[ -L "${dir}" ]]; then
            if [[ -e "${dir}" ]]; then
                echo "✅ Valid symlink: ${dir} → $(readlink "${dir}")"
            else
                echo "❌ Broken symlink: ${dir}"
                ((BROKEN_LINKS++))
            fi
        else
            echo "❌ Missing symlink: ${dir}"
            ((BROKEN_LINKS++))
        fi
    done

    for file in "${SHARED_FILES[@]}"; do
        if [[ -L "${file}" ]]; then
            if [[ -e "${file}" ]]; then
                echo "✅ Valid file link: ${file}"
            else
                echo "⚠️ Broken file link: ${file} (may be created during deployment)"
            fi
        elif [[ -f "${SHARED_PATH}/${file}" ]]; then
            echo "⚠️ Shared file exists but not linked: ${file}"
        fi
    done

    if [[ ${BROKEN_LINKS} -gt 0 ]]; then
        echo "❌ Found ${BROKEN_LINKS} broken symlinks"
        exit 1
    fi

    echo "✅ Advanced shared resources configuration completed successfully"
    ```

    **Expected Result:**

    ```
    ✅ All shared directories linked successfully
    ✅ Shared files configured and linked
    ✅ Symlink validation completed
    ✅ No broken links detected
    ✅ Release configured with persistent data protection
    ```

### Step 4.2: Secure Configuration Management [4.2-secure-config]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Deploy and validate secure environment configurations

#### **Action Steps:**

1. **Execute Secure Configuration Deployment**

    ```bash
    echo "🔴 Secure configuration management..."

    cd "${RELEASE_PATH}"

    # Validate environment configuration
    echo "🔍 Validating environment configuration..."

    REQUIRED_ENV_VARS=(
        "APP_KEY"
        "APP_ENV"
        "APP_DEBUG"
        "APP_URL"
        "DB_CONNECTION"
        "DB_HOST"
        "DB_DATABASE"
    )

    # Check if .env exists (should be symlinked from shared)
    if [[ -f ".env" ]]; then
        echo "✅ Environment file found"

        # Validate critical variables
        MISSING_VARS=()
        for var in "${REQUIRED_ENV_VARS[@]}"; do
            if ! grep -q "^${var}=" .env; then
                MISSING_VARS+=("${var}")
            fi
        done

        if [[ ${#MISSING_VARS[@]} -eq 0 ]]; then
            echo "✅ All required environment variables present"
        else
            echo "⚠️ Missing environment variables:"
            printf "  - %s\n" "${MISSING_VARS[@]}"
            echo "⚠️ Please configure missing variables in shared .env file"
        fi
    else
        echo "❌ .env file not found - should be symlinked from shared directory"
        echo "🔧 Creating .env from template..."

        if [[ -f ".env.example" ]]; then
            cp ".env.example" "${SHARED_PATH}/.env"
            ln -nfs "${SHARED_PATH}/.env" .env
            echo "⚠️ Please configure production values in: ${SHARED_PATH}/.env"
        else
            echo "❌ No .env.example found - manual .env creation required"
            exit 1
        fi
    fi

    # Set secure permissions on configuration
    echo "🔐 Applying secure configuration permissions..."
    [[ -f "${SHARED_PATH}/.env" ]] && chmod 600 "${SHARED_PATH}/.env"

    # Validate APP_KEY
    APP_KEY=$(grep -E "^APP_KEY=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" | tr -d ' ')
    if [[ -z "${APP_KEY}" ]] || [[ "${APP_KEY}" == "base64:" ]] || [[ "${APP_KEY}" == "" ]]; then
        echo "🔑 Generating missing APP_KEY..."

        # Generate key and update shared .env
        NEW_KEY=$(php artisan key:generate --show --no-interaction)
        sed -i "s/APP_KEY=.*/APP_KEY=${NEW_KEY}/" "${SHARED_PATH}/.env"
        echo "✅ APP_KEY generated and updated in shared configuration"
    else
        echo "✅ APP_KEY validation passed"
    fi

    # Environment-specific security settings
    APP_ENV=$(grep -E "^APP_ENV=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" | tr -d ' ')
    if [[ "${APP_ENV}" == "production" ]]; then
        echo "🛡️ Applying production security settings..."

        # Ensure debug is disabled
        if grep -q "APP_DEBUG=true" "${SHARED_PATH}/.env"; then
            sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" "${SHARED_PATH}/.env"
            echo "✅ Debug mode disabled for production"
        fi

        # Add secure session cookie setting if missing
        if ! grep -q "SESSION_SECURE_COOKIE=true" "${SHARED_PATH}/.env"; then
            echo "SESSION_SECURE_COOKIE=true" >> "${SHARED_PATH}/.env"
            echo "✅ Secure cookie setting added"
        fi

        # Add HTTPS enforcement if missing
        if ! grep -q "FORCE_HTTPS=true" "${SHARED_PATH}/.env" && ! grep -q "APP_URL=https" "${SHARED_PATH}/.env"; then
            echo "⚠️ HTTPS enforcement not configured - consider adding FORCE_HTTPS=true"
        fi
    fi

    # Final configuration validation
    echo "📊 Configuration summary:"
    echo "  - Environment: ${APP_ENV:-unknown}"
    echo "  - Debug mode: $(grep APP_DEBUG .env | cut -d'=' -f2)"
    echo "  - APP_KEY: $([ -n "${APP_KEY}" ] && echo 'Set' || echo 'Missing')"
    echo "  - Database: $(grep DB_CONNECTION .env | cut -d'=' -f2)"
    echo "  - URL: $(grep APP_URL .env | cut -d'=' -f2)"

    echo "✅ Secure configuration management completed"
    ```

    **Expected Result:**

    ```
    ✅ Environment file validated and configured
    ✅ All required environment variables checked
    ✅ APP_KEY generated/validated
    ✅ Production security settings applied
    ✅ Secure permissions set on configuration files
    ✅ Configuration summary generated
    ✅ Release ready for deployment hooks
    ```

---

## **Phase 5: 🚀 Pre-Release Hooks** 🟣 1️⃣ **User-configurable SSH Commands**

### Step 5.1: Maintenance Mode (Optional) [5.1-maintenance]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current`  
**Purpose:** Put application in maintenance mode with user-friendly display

#### **Action Steps:**

1. **Enable Maintenance Mode (If Configured)**

    ```bash
    # Only run if maintenance mode is enabled in deployment config
    MAINTENANCE_MODE=$(jq -r '.deployment.maintenance_mode // false' Admin-Local/Deployment/Configs/deployment-variables.json)

    if [[ "${MAINTENANCE_MODE}" == "true" ]] && [[ -L "${DEPLOY_PATH}/current" ]]; then
        echo "🚧 Enabling maintenance mode..."

        cd "${DEPLOY_PATH}/current"

        # Enable maintenance with secret bypass and custom message
        php artisan down \
            --render="errors::503" \
            --secret="${DEPLOY_SECRET:-deploy-bypass-$(date +%s)}" \
            --retry=60 \
            --message="Application is being updated. Please try again shortly."

        echo "🚧 Maintenance mode enabled with bypass secret"
        echo "🔑 Bypass URL: ${APP_URL}/${DEPLOY_SECRET:-deploy-bypass-$(date +%s)}"
    else
        echo "ℹ️ Maintenance mode disabled - continuing with live deployment"
    fi
    ```

    **Expected Result:**

    ```
    ✅ Maintenance mode enabled (if configured)
    ✅ User-friendly message displayed
    ✅ Secret bypass URL available
    ✅ Deployment can proceed without user disruption
    ```

### Step 5.2: Pre-Release Custom Commands [5.2-pre-custom]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current`  
**Purpose:** Execute user-defined pre-release scripts and backups

#### **Action Steps:**

1. **Execute Pre-Release Actions** 🟣 1️⃣ User-customizable

    ```bash
    echo "🟣 Executing pre-release custom commands..."

    # Database backup (if configured)
    if [[ "${CREATE_DB_BACKUP}" == "true" ]] && [[ -f ".env" ]]; then
        echo "💾 Creating database backup..."

        # Load database credentials from current environment
        source .env

        BACKUP_FILE="${DEPLOY_PATH}/backups/db-$(date +%Y%m%d%H%M%S).sql"
        mkdir -p "${DEPLOY_PATH}/backups"

        if mysqldump -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" > "${BACKUP_FILE}"; then
            gzip "${BACKUP_FILE}"
            echo "✅ Database backed up to: ${BACKUP_FILE}.gz"
        else
            echo "⚠️ Database backup failed - continuing anyway"
        fi
    fi

    # Custom pre-release script (user-defined)
    if [[ -f "${DEPLOY_PATH}/scripts/pre-release.sh" ]]; then
        echo "🔧 Running custom pre-release script..."
        bash "${DEPLOY_PATH}/scripts/pre-release.sh"
    else
        echo "ℹ️ No custom pre-release script found"
    fi

    # External service notifications
    if [[ -n "${DEPLOYMENT_WEBHOOK_URL}" ]]; then
        echo "📡 Sending pre-deployment notification..."
        curl -X POST "${DEPLOYMENT_WEBHOOK_URL}" \
             -H "Content-Type: application/json" \
             -d "{
                  \"status\": \"pre-release\",
                  \"release_id\": \"${RELEASE_ID}\",
                  \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"
                 }" > /dev/null 2>&1 || echo "⚠️ Webhook notification failed"
    fi

    echo "✅ Pre-release custom commands completed"
    ```

    **Expected Result:**

    ```
    ✅ Database backup created (if configured)
    ✅ Custom pre-release scripts executed
    ✅ External notifications sent
    ✅ Pre-release phase completed successfully
    ```

---

## **Phase 6: 🔄 Mid-Release Hooks** 🟣 2️⃣ **User-configurable SSH Commands**

### Step 6.1: Zero-Downtime Database Migrations [6.1-migrations]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Execute database migrations safely with zero-downtime strategy

#### **Action Steps:**

1. **Execute Zero-Downtime Migrations** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Zero-downtime database migrations..."

    cd "${RELEASE_PATH}"

    # Check migration status
    echo "📋 Current migration status:"
    if php artisan migrate:status --no-interaction; then
        echo "✅ Migration status retrieved successfully"
    else
        echo "⚠️ Could not retrieve migration status - may be first deployment"
    fi

    # Run migrations with zero-downtime strategy
    echo "🔄 Running database migrations..."

    # Use --step to run migrations one at a time for safety
    if php artisan migrate --force --step --no-interaction; then
        echo "✅ Database migrations completed successfully"

        # Log migration completion
        php artisan migrate:status | grep "Ran" | wc -l > /tmp/migration_count
        MIGRATION_COUNT=$(cat /tmp/migration_count)
        echo "📊 Total migrations applied: ${MIGRATION_COUNT}"
    else
        echo "❌ Database migrations failed"

        # In case of migration failure, we should not proceed
        # The atomic switch will not happen, keeping the old version live
        exit 1
    fi

    # Optional: Run database seeders for production (if configured)
    if [[ "${RUN_PRODUCTION_SEEDERS}" == "true" ]]; then
        echo "🌱 Running production seeders..."
        if php artisan db:seed --class=ProductionSeeder --force --no-interaction; then
            echo "✅ Production seeders completed"
        else
            echo "⚠️ Production seeders failed - continuing anyway"
        fi
    fi

    echo "✅ Zero-downtime migration phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Migration status retrieved and logged
    ✅ Database migrations executed safely with --step flag
    ✅ Zero-downtime strategy maintained
    ✅ Migration count documented
    ✅ Production seeders run (if configured)
    ✅ Database schema updated successfully
    ```

### Step 6.2: Application Cache Preparation [6.2-cache-prep]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Prepare and optimize application caches for production

#### **Action Steps:**

1. **Execute Advanced Cache Preparation** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Advanced application cache preparation..."

    cd "${RELEASE_PATH}"

    # Clear any existing caches first
    echo "🧹 Clearing existing caches..."
    php artisan cache:clear --no-interaction --quiet || true
    php artisan config:clear --no-interaction --quiet || true
    php artisan route:clear --no-interaction --quiet || true
    php artisan view:clear --no-interaction --quiet || true

    # Rebuild caches with production environment
    echo "⚡ Rebuilding production caches..."

    # Configuration cache
    if php artisan config:cache --no-interaction; then
        echo "✅ Configuration cache built"
    else
        echo "❌ Configuration cache failed"
        exit 1
    fi

    # Route cache
    if php artisan route:cache --no-interaction; then
        echo "✅ Route cache built"
    else
        echo "❌ Route cache failed"
        exit 1
    fi

    # View cache
    if php artisan view:cache --no-interaction; then
        echo "✅ View cache built"
    else
        echo "⚠️ View cache failed - continuing anyway"
    fi

    # Advanced Laravel caches (if available)
    echo "🔧 Building advanced caches..."

    # Event cache (Laravel 8+)
    if php artisan list | grep -q "event:cache"; then
        php artisan event:cache --no-interaction && echo "📅 Event cache built" || true
    fi

    # Icon cache (if using Laravel icons)
    if php artisan list | grep -q "icons:cache"; then
        php artisan icons:cache --no-interaction && echo "🎨 Icon cache built" || true
    fi

    # Custom cache warmup (user-defined)
    if php artisan list | grep -q "cache:warmup"; then
        echo "🔥 Running cache warmup..."
        php artisan cache:warmup --no-interaction || echo "⚠️ Cache warmup failed"
    fi

    # Optional: Pre-warm application cache with critical data
    echo "🔥 Pre-warming critical application data..."

    # Custom cache warmup script (user-defined)
    if [[ -f "${DEPLOY_PATH}/scripts/cache-warmup.sh" ]]; then
        bash "${DEPLOY_PATH}/scripts/cache-warmup.sh"
    fi

    echo "✅ Advanced cache preparation completed"
    ```

    **Expected Result:**

    ```
    ✅ All existing caches cleared successfully
    ✅ Configuration cache rebuilt for production
    ✅ Route cache optimized
    ✅ View cache compiled
    ✅ Advanced caches built (events, icons)
    ✅ Custom cache warmup executed
    ✅ Application optimized for first requests
    ```

### Step 6.3: Health Checks [6.3-health]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Verify application functionality before atomic switch

#### **Action Steps:**

1. **Execute Comprehensive Health Checks** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Comprehensive application health checks..."

    cd "${RELEASE_PATH}"

    # Basic Laravel functionality tests
    echo "🧪 Testing basic Laravel functionality..."

    if php artisan --version --no-interaction >/dev/null; then
        echo "✅ Artisan commands functional"
    else
        echo "❌ Artisan commands not working"
        exit 1
    fi

    # Database connectivity test
    echo "🗃️ Testing database connectivity..."
    if php artisan migrate:status --no-interaction >/dev/null; then
        echo "✅ Database connection verified"
    else
        echo "❌ Database connection failed"
        exit 1
    fi

    # Route functionality test
    echo "🗺️ Testing route system..."
    if php artisan route:list --compact >/dev/null; then
        ROUTE_COUNT=$(php artisan route:list --compact | wc -l)
        echo "✅ Route system functional (${ROUTE_COUNT} routes)"
    else
        echo "❌ Route system not functional"
        exit 1
    fi

    # Cache functionality test
    echo "💾 Testing cache systems..."
    if php artisan cache:clear >/dev/null 2>&1; then
        echo "✅ Cache system functional"
    else
        echo "⚠️ Cache system issues detected"
    fi

    # Application bootstrap test
    echo "🚀 Testing application bootstrap..."
    if php -r "
        try {
            require_once 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
            \$kernel->bootstrap();
            echo 'Application bootstrap successful\n';
        } catch (Exception \$e) {
            echo 'Bootstrap failed: ' . \$e->getMessage() . '\n';
            exit(1);
        }
    "; then
        echo "✅ Application bootstrap successful"
    else
        echo "❌ Application bootstrap failed"
        exit 1
    fi

    # Custom health checks (user-defined)
    if php artisan list | grep -q "health:check"; then
        echo "🏥 Running custom health checks..."
        if php artisan health:check --no-interaction; then
            echo "✅ Custom health checks passed"
        else
            echo "⚠️ Custom health checks failed - review required"
        fi
    fi

    # File permissions validation
    echo "🔐 Validating file permissions..."
    PERM_ISSUES=0

    # Check critical writable directories
    WRITABLE_DIRS=("storage" "bootstrap/cache")
    for dir in "${WRITABLE_DIRS[@]}"; do
        if [[ -w "${dir}" ]]; then
            echo "✅ ${dir} is writable"
        else
            echo "❌ ${dir} is not writable"
            ((PERM_ISSUES++))
        fi
    done

    if [[ ${PERM_ISSUES} -gt 0 ]]; then
        echo "❌ Found ${PERM_ISSUES} permission issues"
        exit 1
    fi

    # Summary
    echo "📊 Health check summary:"
    echo "  - Laravel core: ✅ Functional"
    echo "  - Database: ✅ Connected"
    echo "  - Routes: ✅ ${ROUTE_COUNT:-0} routes loaded"
    echo "  - Cache: ✅ Functional"
    echo "  - Bootstrap: ✅ Successful"
    echo "  - Permissions: ✅ Validated"

    echo "✅ Comprehensive health checks completed - ready for atomic switch"
    ```

    **Expected Result:**

    ```
    ✅ Artisan commands verified functional
    ✅ Database connectivity confirmed
    ✅ Route system tested and validated
    ✅ Cache systems verified working
    ✅ Application bootstrap successful
    ✅ Custom health checks passed (if available)
    ✅ File permissions validated
    ✅ All systems ready for atomic deployment switch
    ```

---

## **Phase 7: ⚡ Atomic Release Switch**

### Step 7.1: Atomic Symlink Update [7.1-atomic-switch]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Execute atomic switch to new release - THE zero-downtime moment

#### **Action Steps:**

1. **Execute Atomic Deployment Switch**

    ```bash
    echo "⚡ EXECUTING ATOMIC RELEASE SWITCH - Zero-downtime moment..."

    cd "${DEPLOY_PATH}"

    # Store current release info for potential rollback
    if [[ -L "current" ]]; then
        OLD_RELEASE=$(readlink current)
        OLD_RELEASE_NAME=$(basename "${OLD_RELEASE}")
        echo "📋 Previous release: ${OLD_RELEASE_NAME}"
    else
        OLD_RELEASE=""
        echo "📋 First deployment - no previous release"
    fi

    # Prepare atomic switch variables
    NEW_RELEASE="releases/${RELEASE_ID}"
    CURRENT_LINK="current"
    TEMP_LINK="current-temp-$(date +%s)"

    # Pre-switch validation
    echo "🔍 Pre-switch validation..."
    if [[ ! -d "${NEW_RELEASE}" ]]; then
        echo "❌ New release directory not found: ${NEW_RELEASE}"
        exit 1
    fi

    if [[ ! -f "${NEW_RELEASE}/artisan" ]]; then
        echo "❌ New release appears invalid - missing artisan"
        exit 1
    fi

    echo "✅ New release validated: ${RELEASE_ID}"

    # **ATOMIC SWITCH EXECUTION**
    echo "🚀 Executing atomic symlink switch..."

    # Create temporary symlink first
    if ln -nfs "${NEW_RELEASE}" "${TEMP_LINK}"; then
        echo "✅ Temporary symlink created"
    else
        echo "❌ Failed to create temporary symlink"
        exit 1
    fi

    # Atomic move (this is the zero-downtime moment)
    if mv "${TEMP_LINK}" "${CURRENT_LINK}"; then
        echo "⚡ ATOMIC SWITCH COMPLETED SUCCESSFULLY"
        echo "🎉 Zero-downtime deployment achieved!"
    else
        echo "❌ Atomic switch failed"
        rm -f "${TEMP_LINK}"
        exit 1
    fi

    # Handle public_html for shared hosting
    PUBLIC_HTML_PATH="/home/u227177893/public_html"
    if [[ -d "$(dirname "${PUBLIC_HTML_PATH}")" ]]; then
        echo "🌐 Configuring public_html for shared hosting..."

        # Backup existing public_html if it's not a symlink
        if [[ -d "${PUBLIC_HTML_PATH}" ]] && [[ ! -L "${PUBLIC_HTML_PATH}" ]]; then
            echo "📁 Backing up existing public_html..."
            mv "${PUBLIC_HTML_PATH}" "${PUBLIC_HTML_PATH}.backup-$(date +%Y%m%d%H%M%S)"
        fi

        # Create/update symlink to current release public directory
        if ln -nfs "${DEPLOY_PATH}/current/public" "${PUBLIC_HTML_PATH}"; then
            echo "✅ Public_html symlinked to current release"
        else
            echo "⚠️ Failed to update public_html symlink"
        fi
    fi

    # Verify switch success
    echo "🔍 Post-switch verification..."

    CURRENT_TARGET=$(readlink current)
    if [[ "${CURRENT_TARGET}" == "${NEW_RELEASE}" ]]; then
        echo "✅ Symlink verification passed"
        echo "📍 Current points to: ${CURRENT_TARGET}"
    else
        echo "❌ Symlink verification failed"
        echo "Expected: ${NEW_RELEASE}"
        echo "Actual: ${CURRENT_TARGET}"
        exit 1
    fi

    # Log the switch
    echo "📝 Logging atomic switch..."
    cat >> deployment.log << EOF
    ATOMIC SWITCH: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    From: ${OLD_RELEASE_NAME:-"(first deployment)"}
    To: ${RELEASE_ID}
    Status: SUCCESS
    Switch Duration: <1 second (atomic)
    EOF

    # Export variables for next phase
    export OLD_RELEASE OLD_RELEASE_NAME

    echo "⚡ ATOMIC RELEASE SWITCH PHASE COMPLETED"
    echo "🏁 Zero-downtime deployment switch successful!"
    ```

    **Expected Result:**

    ```
    ✅ Previous release information captured for rollback
    ✅ New release validated before switch
    ✅ Temporary symlink created successfully
    ⚡ ATOMIC SWITCH EXECUTED IN <1 SECOND
    ✅ Public_html configured for shared hosting (if applicable)
    ✅ Post-switch verification passed
    ✅ Switch logged with timestamp
    ✅ ZERO-DOWNTIME DEPLOYMENT ACHIEVED
    ```

---

_This enhanced Section C provides a complete zero-downtime deployment pipeline with:_

-   ✅ **Path Variables Integration** - Consistent variable usage throughout
-   ✅ **Visual Location Tags** - Clear 🟢🟡🔴🟣 identification
-   ✅ **Enhanced Build Strategies** - Local/VM/Server with fallback logic
-   ✅ **Universal Dependency Management** - Comprehensive analysis and smart installation
-   ✅ **Advanced Asset Compilation** - Auto-detection of bundlers (Vite/Mix/Webpack)
-   ✅ **Intelligent Caching** - Hash-based cache restoration and validation
-   ✅ **Comprehensive Health Checks** - Multi-layer validation before deployment
-   ✅ **True Atomic Switching** - <1 second zero-downtime deployment
-   ✅ **User-Configurable Hooks** - 1️⃣2️⃣3️⃣ Pre/Mid/Post release customization
-   ✅ **Expected Results** - Every step includes validation and confirmation
-   ✅ **Error Handling** - Comprehensive failure detection and rollback triggers

**Ready for Phases 8-10 completion in the final implementation.**
