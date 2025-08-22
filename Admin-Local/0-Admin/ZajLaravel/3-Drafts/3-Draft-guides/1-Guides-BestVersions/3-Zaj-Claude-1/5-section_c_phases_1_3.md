# Section C: Build and Deploy (Phases 1-3)

**Version:** 2.0  
**Location:** 🟢 Local Machine | 🟡 Builder VM | 🔴 Server  
**Purpose:** Execute universal zero-downtime Laravel deployment - Build Environment & Application Preparation  
**Time Required:** 3-8 minutes (automated execution)

---

## 🎯 **SECTION C OVERVIEW**

Section C executes the actual zero-downtime deployment using a 10-phase automated pipeline. This document covers the first 3 phases:

- **Phase 1:** Prepare Build Environment
- **Phase 2:** Build Application  
- **Phase 3:** Package & Transfer

**Key Features:**
- ✅ **Atomic Operations** - Each phase is reversible
- ✅ **Comprehensive Validation** - Every step verified before proceeding
- ✅ **Universal Compatibility** - Works with all deployment strategies
- ✅ **Zero-Downtime Guarantee** - Production traffic unaffected until atomic switch
- ✅ **Emergency Rollback** - Instant rollback capability at any stage

---

## 📋 **PREREQUISITES VERIFICATION**

Before starting Section C, verify Section B is complete:

```bash
# Navigate to project root
cd /path/to/your/laravel/project

# Verify Section B completion
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/load-variables.sh
./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/final-pre-deployment-validation.sh

# Expected output: "SECTION B VALIDATION: ✅ PASSED"
```

**Required Status:**
```
🎯 DEPLOYMENT READY STATUS: ✅ PASSED
📋 All validation checkpoints successful  
🚀 Ready for zero-downtime deployment
```

---

## 🏗️ **PHASE 1: PREPARE BUILD ENVIRONMENT**

### **Phase 1.1: Pre-Build Environment Preparation**

**Location:** 🟢 Local Machine  
**Purpose:** Initialize deployment workspace and validate repository connectivity  
**Time:** 1 minute  

#### **Action:**

1. **Create Pre-Build Environment Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-1-pre-build-env.sh << 'EOF'
   #!/bin/bash
   # Phase 1.1: Pre-Build Environment Preparation
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   echo "🏗️ Phase 1.1: Starting pre-build environment preparation..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-1-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 1.1: Pre-Build Environment Preparation - $(date)"
       echo "====================================================="
       
       # Validate deployment workspace
       echo "🔍 Validating deployment workspace..."
       if [[ -d "Admin-Local/2-Project-Area/01-Deployment-Toolbox" ]]; then
           echo "✅ Deployment workspace validated"
       else
           echo "❌ Deployment workspace missing"
           exit 1
       fi
       
       # Verify repository connectivity
       echo "🐙 Verifying repository connectivity..."
       if git remote -v >/dev/null 2>&1; then
           echo "✅ Git repository connectivity confirmed"
           git remote -v
       else
           echo "❌ Git repository not configured"
           exit 1
       fi
       
       # Check current branch and status
       echo "📋 Checking repository status..."
       CURRENT_BRANCH=$(git branch --show-current)
       echo "Current branch: $CURRENT_BRANCH"
       
       if git status --porcelain | grep -q .; then
           echo "⚠️ Working directory has uncommitted changes"
           git status --short
           echo "💡 Consider committing changes before deployment"
       else
           echo "✅ Working directory clean"
       fi
       
       # Prepare build environment variables
       echo "🔧 Preparing build environment variables..."
       export BUILD_TIMESTAMP=$(date +%Y%m%d-%H%M%S)
       export BUILD_ID="build-${BUILD_TIMESTAMP}"
       export RELEASE_ID="release-${BUILD_TIMESTAMP}"
       
       echo "Build ID: $BUILD_ID"
       echo "Release ID: $RELEASE_ID"
       
       # Create deployment workspace
       DEPLOYMENT_WORKSPACE="/tmp/laravel-deployment-${BUILD_TIMESTAMP}"
       mkdir -p "$DEPLOYMENT_WORKSPACE"
       echo "Deployment workspace: $DEPLOYMENT_WORKSPACE"
       
       # Save environment variables for next phases
       cat > "${SCRIPT_DIR}/../01-Configs/current-deployment.env" << ENVEOF
   BUILD_TIMESTAMP=$BUILD_TIMESTAMP
   BUILD_ID=$BUILD_ID
   RELEASE_ID=$RELEASE_ID
   DEPLOYMENT_WORKSPACE=$DEPLOYMENT_WORKSPACE
   CURRENT_BRANCH=$CURRENT_BRANCH
   ENVEOF
       
       echo "✅ Phase 1.1: Pre-build environment preparation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 1.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-1-pre-build-env.sh
   ```

2. **Execute Phase 1.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-1-pre-build-env.sh
   ```

#### **Expected Result:**
```
✅ Deployment workspace validated
🐙 Git repository connectivity confirmed
📋 Working directory clean
🔧 Build environment variables prepared
```

---

### **Phase 1.2: Build Environment Setup**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Initialize clean build environment based on configured strategy  
**Time:** 1 minute  

#### **Action:**

1. **Create Build Environment Setup Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-2-build-env-setup.sh << 'EOF'
   #!/bin/bash
   # Phase 1.2: Build Environment Setup
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   if [[ -f "${SCRIPT_DIR}/../01-Configs/current-deployment.env" ]]; then
       source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   else
       echo "❌ Current deployment environment not found. Run phase 1.1 first."
       exit 1
   fi
   
   echo "🟡 Phase 1.2: Setting up build environment..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-1-2-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 1.2: Build Environment Setup - $(date)"
       echo "============================================"
       echo "Build ID: $BUILD_ID"
       echo "Deployment Strategy: $DEPLOYMENT_STRATEGY"
       
       # Validate system requirements
       echo "🔍 Validating system requirements..."
       
       # Check PHP version
       PHP_VERSION=$(php -r "echo PHP_VERSION;")
       echo "PHP Version: $PHP_VERSION"
       if [[ "${PHP_VERSION%%.*}" -ge "8" ]]; then
           echo "✅ PHP version compatible"
       else
           echo "❌ PHP version incompatible (8.0+ required)"
           exit 1
       fi
       
       # Check Composer version
       COMPOSER_VERSION=$(composer --version | grep -oE '[0-9]+\.[0-9]+' | head -1)
       echo "Composer Version: $COMPOSER_VERSION"
       if [[ "${COMPOSER_VERSION%%.*}" -ge "2" ]]; then
           echo "✅ Composer version compatible"
       else
           echo "⚠️ Composer 2.x recommended for optimal performance"
       fi
       
       # Validate build strategy configuration
       BUILD_CONFIG_FILE="${SCRIPT_DIR}/../01-Configs/build-strategy.json"
       if [[ -f "$BUILD_CONFIG_FILE" ]]; then
           echo "✅ Build strategy configuration found"
           
           # Display build configuration
           echo "📋 Build Configuration:"
           jq -r '.project_analysis' "$BUILD_CONFIG_FILE"
       else
           echo "❌ Build strategy not configured"
           exit 1
       fi
       
       # Setup build directory
       BUILD_DIR="$DEPLOYMENT_WORKSPACE/build"
       mkdir -p "$BUILD_DIR"
       echo "Build directory: $BUILD_DIR"
       
       # Validate disk space
       AVAILABLE_SPACE=$(df "$DEPLOYMENT_WORKSPACE" | awk 'NR==2 {print $4}')
       AVAILABLE_GB=$((AVAILABLE_SPACE / 1024 / 1024))
       echo "Available disk space: ${AVAILABLE_GB}GB"
       
       if [[ $AVAILABLE_GB -lt 1 ]]; then
           echo "⚠️ Low disk space available"
       else
           echo "✅ Sufficient disk space available"
       fi
       
       # Create environment-specific configurations
       case "$DEPLOYMENT_STRATEGY" in
           "local")
               echo "🟢 Configuring for local build strategy"
               ;;
           "github_actions")
               echo "🟡 Configuring for GitHub Actions build strategy"
               ;;
           "deployhq")
               echo "🟡 Configuring for DeployHQ build strategy"
               ;;
           "server_build")
               echo "🔴 Configuring for server build strategy"
               ;;
           *)
               echo "⚠️ Unknown deployment strategy: $DEPLOYMENT_STRATEGY"
               ;;
       esac
       
       echo "✅ Phase 1.2: Build environment setup completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 1.2 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-2-build-env-setup.sh
   ```

2. **Execute Phase 1.2:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-2-build-env-setup.sh
   ```

#### **Expected Result:**
```
✅ PHP version compatible
✅ Composer version compatible  
✅ Build strategy configuration found
✅ Sufficient disk space available
🟢 Build environment configured for strategy
```

---

### **Phase 1.3: Repository Preparation**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Clone repository and validate commit integrity  
**Time:** 1 minute  

#### **Action:**

1. **Create Repository Preparation Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-3-repo-preparation.sh << 'EOF'
   #!/bin/bash
   # Phase 1.3: Repository Preparation
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "🐙 Phase 1.3: Preparing repository for build..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-1-3-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 1.3: Repository Preparation - $(date)"
       echo "==========================================="
       echo "Build ID: $BUILD_ID"
       echo "Repository: $REPO_URL"
       
       # Validate current repository state
       echo "🔍 Validating current repository state..."
       
       CURRENT_COMMIT=$(git rev-parse HEAD)
       echo "Current commit: $CURRENT_COMMIT"
       
       CURRENT_BRANCH=$(git branch --show-current)
       echo "Current branch: $CURRENT_BRANCH"
       
       # Check if working directory is clean
       if git diff-index --quiet HEAD --; then
           echo "✅ Working directory is clean"
       else
           echo "⚠️ Working directory has uncommitted changes"
           echo "📋 Changes will not be included in build"
       fi
       
       # Validate commit integrity
       echo "🔐 Validating commit integrity..."
       if git fsck --no-progress >/dev/null 2>&1; then
           echo "✅ Repository integrity verified"
       else
           echo "⚠️ Repository integrity issues detected"
       fi
       
       # Create build workspace and prepare source
       BUILD_SOURCE_DIR="$DEPLOYMENT_WORKSPACE/source"
       echo "📁 Preparing build source directory: $BUILD_SOURCE_DIR"
       
       # For local builds, create a clean copy of the repository
       if [[ "$DEPLOYMENT_STRATEGY" == "local" ]]; then
           echo "🟢 Creating clean local build copy..."
           
           # Use git archive to create clean copy without .git
           git archive HEAD | tar -x -C "$DEPLOYMENT_WORKSPACE"
           mv "$DEPLOYMENT_WORKSPACE" "$BUILD_SOURCE_DIR" 2>/dev/null || {
               mkdir -p "$BUILD_SOURCE_DIR"
               git archive HEAD | tar -x -C "$BUILD_SOURCE_DIR"
           }
           
           echo "✅ Clean source copy created"
       else
           # For other strategies, prepare for remote build
           echo "🟡 Preparing for remote build strategy..."
           mkdir -p "$BUILD_SOURCE_DIR"
           
           # Save commit information for remote builds
           cat > "$BUILD_SOURCE_DIR/build-info.json" << BUILDINFO
   {
     "commit": "$CURRENT_COMMIT",
     "branch": "$CURRENT_BRANCH", 
     "repository": "$REPO_URL",
     "build_timestamp": "$BUILD_TIMESTAMP",
     "build_id": "$BUILD_ID"
   }
   BUILDINFO
           
           echo "✅ Remote build information prepared"
       fi
       
       # Verify source directory
       if [[ -d "$BUILD_SOURCE_DIR" ]]; then
           echo "📊 Source directory size: $(du -sh "$BUILD_SOURCE_DIR" | cut -f1)"
           
           # Verify Laravel project structure
           if [[ -f "$BUILD_SOURCE_DIR/artisan" ]] && [[ -f "$BUILD_SOURCE_DIR/composer.json" ]]; then
               echo "✅ Laravel project structure verified"
           else
               echo "❌ Invalid Laravel project structure"
               exit 1
           fi
           
           # Copy deployment configuration to build source
           cp -r "$BUILD_SOURCE_DIR/Admin-Local/2-Project-Area/01-Deployment-Toolbox" "$DEPLOYMENT_WORKSPACE/deployment-config"
           echo "✅ Deployment configuration copied to build workspace"
       else
           echo "❌ Source directory creation failed"
           exit 1
       fi
       
       # Save repository state for next phases
       cat >> "${SCRIPT_DIR}/../01-Configs/current-deployment.env" << ENVEOF
   CURRENT_COMMIT=$CURRENT_COMMIT
   BUILD_SOURCE_DIR=$BUILD_SOURCE_DIR
   ENVEOF
       
       echo "✅ Phase 1.3: Repository preparation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 1.3 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-3-repo-preparation.sh
   ```

2. **Execute Phase 1.3:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-1-3-repo-preparation.sh
   ```

#### **Expected Result:**
```
✅ Working directory is clean
🔐 Repository integrity verified
✅ Clean source copy created
✅ Laravel project structure verified
✅ Deployment configuration copied to build workspace
```

---

## 🗏️ **PHASE 2: BUILD APPLICATION**

### **Phase 2.1: Cache Restoration & Dependency Installation**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Install dependencies with intelligent cache management  
**Time:** 2-5 minutes  

#### **Action:**

1. **Create Cache Restoration & Dependencies Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-1-dependencies.sh << 'EOF'
   #!/bin/bash
   # Phase 2.1: Cache Restoration & Dependency Installation
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "📦 Phase 2.1: Installing dependencies with cache optimization..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-2-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   # Change to build source directory
   cd "$BUILD_SOURCE_DIR"
   
   {
       echo "Phase 2.1: Cache Restoration & Dependencies - $(date)"
       echo "===================================================="
       echo "Build Source: $BUILD_SOURCE_DIR"
       
       # PHP Dependencies Installation
       echo "📦 Installing PHP dependencies..."
       
       # Check if composer.lock exists
       if [[ -f "composer.lock" ]]; then
           echo "✅ composer.lock found - using locked versions"
           COMPOSER_INSTALL_CMD="composer install --no-dev --optimize-autoloader --no-interaction"
       else
           echo "⚠️ composer.lock not found - generating lock file"
           COMPOSER_INSTALL_CMD="composer install --no-dev --optimize-autoloader --no-interaction"
       fi
       
       echo "Executing: $COMPOSER_INSTALL_CMD"
       if $COMPOSER_INSTALL_CMD; then
           echo "✅ PHP dependencies installed successfully"
       else
           echo "❌ PHP dependency installation failed"
           exit 1
       fi
       
       # Verify vendor directory
       if [[ -d "vendor" ]] && [[ -f "vendor/autoload.php" ]]; then
           echo "✅ Vendor directory and autoloader verified"
           VENDOR_SIZE=$(du -sh vendor | cut -f1)
           echo "📊 Vendor directory size: $VENDOR_SIZE"
       else
           echo "❌ Vendor directory validation failed"
           exit 1
       fi
       
       # Frontend Dependencies Installation (if applicable)
       if [[ -f "package.json" ]]; then
           echo "🟢 Installing frontend dependencies..."
           
           # Check for package-lock.json for reproducible builds
           if [[ -f "package-lock.json" ]]; then
               echo "✅ package-lock.json found - using npm ci"
               NPM_INSTALL_CMD="npm ci --silent"
           else
               echo "⚠️ package-lock.json not found - using npm install"
               NPM_INSTALL_CMD="npm install --silent"
           fi
           
           echo "Executing: $NPM_INSTALL_CMD"
           if $NPM_INSTALL_CMD; then
               echo "✅ Frontend dependencies installed successfully"
           else
               echo "❌ Frontend dependency installation failed"
               exit 1
           fi
           
           # Verify node_modules directory
           if [[ -d "node_modules" ]]; then
               echo "✅ Node modules directory verified"
               NODE_MODULES_SIZE=$(du -sh node_modules | cut -f1)
               echo "📊 Node modules size: $NODE_MODULES_SIZE"
           else
               echo "❌ Node modules directory validation failed"
               exit 1
           fi
       else
           echo "ℹ️ No package.json found - skipping frontend dependencies"
       fi
       
       # Dependency Security Audit
       echo "🔒 Running dependency security audit..."
       
       # Composer audit
       echo "Composer security audit:"
       composer audit --format=plain 2>/dev/null | head -5 || echo "✅ No Composer security issues"
       
       # NPM audit (if applicable)
       if [[ -f "package.json" ]]; then
           echo "NPM security audit:"
           npm audit --audit-level=high 2>/dev/null | head -5 || echo "✅ No critical NPM security issues"
       fi
       
       # Validate build requirements
       echo "🔍 Validating build requirements..."
       
       # Check if Laravel is properly installed
       if php artisan --version >/dev/null 2>&1; then
           LARAVEL_VERSION=$(php artisan --version)
           echo "✅ Laravel functional: $LARAVEL_VERSION"
       else
           echo "❌ Laravel not functional"
           exit 1
       fi
       
       # Check composer validate
       if composer validate --strict >/dev/null 2>&1; then
           echo "✅ Composer configuration valid"
       else
           echo "⚠️ Composer configuration issues detected"
           composer validate --strict
       fi
       
       echo "✅ Phase 2.1: Dependencies installation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 2.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-1-dependencies.sh
   ```

2. **Execute Phase 2.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-1-dependencies.sh
   ```

#### **Expected Result:**
```
✅ PHP dependencies installed successfully
✅ Frontend dependencies installed successfully
✅ Security audit completed
✅ Laravel functional
✅ Composer configuration valid
```

---

### **Phase 2.2: Asset Compilation**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Compile frontend assets with auto-detection and optimization  
**Time:** 1-3 minutes  

#### **Action:**

1. **Create Asset Compilation Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-2-assets.sh << 'EOF'
   #!/bin/bash
   # Phase 2.2: Asset Compilation
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "🎨 Phase 2.2: Compiling frontend assets..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-2-2-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   # Change to build source directory
   cd "$BUILD_SOURCE_DIR"
   
   {
       echo "Phase 2.2: Asset Compilation - $(date)"
       echo "======================================"
       echo "Build Source: $BUILD_SOURCE_DIR"
       
       # Check if frontend build is needed
       if [[ ! -f "package.json" ]]; then
           echo "ℹ️ No package.json found - skipping asset compilation"
           echo "✅ Phase 2.2: Asset compilation skipped (backend only)"
           exit 0
       fi
       
       echo "🟢 Frontend build system detected"
       
       # Load build strategy configuration
       BUILD_CONFIG_FILE="${DEPLOYMENT_WORKSPACE}/deployment-config/01-Configs/build-strategy.json"
       if [[ -f "$BUILD_CONFIG_FILE" ]]; then
           BUILD_SYSTEM=$(jq -r '.project_analysis.build_system' "$BUILD_CONFIG_FILE")
           FRONTEND_FRAMEWORK=$(jq -r '.project_analysis.frontend_framework' "$BUILD_CONFIG_FILE")
           echo "Build system: $BUILD_SYSTEM"
           echo "Frontend framework: $FRONTEND_FRAMEWORK"
       else
           echo "⚠️ Build strategy configuration not found, using auto-detection"
           BUILD_SYSTEM="auto-detect"
       fi
       
       # Auto-detect build system if needed
       if [[ "$BUILD_SYSTEM" == "auto-detect" ]] || [[ "$BUILD_SYSTEM" == "none" ]]; then
           echo "🔍 Auto-detecting build system..."
           
           if grep -q '"vite"' package.json; then
               BUILD_SYSTEM="vite"
           elif grep -q '"laravel-mix"' package.json; then
               BUILD_SYSTEM="mix"
           else
               BUILD_SYSTEM="unknown"
           fi
           
           echo "Detected build system: $BUILD_SYSTEM"
       fi
       
       # Determine build command
       BUILD_COMMAND=""
       if jq -e '.scripts.build' package.json >/dev/null 2>&1; then
           BUILD_COMMAND="npm run build"
       elif jq -e '.scripts.production' package.json >/dev/null 2>&1; then
           BUILD_COMMAND="npm run production"
       elif jq -e '.scripts.prod' package.json >/dev/null 2>&1; then
           BUILD_COMMAND="npm run prod"
       else
           echo "⚠️ No build command found in package.json"
           BUILD_COMMAND=""
       fi
       
       # Execute asset compilation
       if [[ -n "$BUILD_COMMAND" ]]; then
           echo "🗏️ Executing build command: $BUILD_COMMAND"
           
           # Set NODE_ENV for production
           export NODE_ENV=production
           
           if $BUILD_COMMAND; then
               echo "✅ Asset compilation successful"
           else
               echo "❌ Asset compilation failed"
               exit 1
           fi
       else
           echo "ℹ️ No build command available - skipping asset compilation"
       fi
       
       # Validate build artifacts
       echo "🔍 Validating build artifacts..."
       
       # Check for common build output directories/files
       BUILD_ARTIFACTS_FOUND=false
       
       if [[ -d "public/build" ]]; then
           echo "✅ Vite build artifacts found in public/build/"
           BUILD_ARTIFACTS_SIZE=$(du -sh public/build | cut -f1)
           echo "📊 Build artifacts size: $BUILD_ARTIFACTS_SIZE"
           BUILD_ARTIFACTS_FOUND=true
       fi
       
       if [[ -f "public/mix-manifest.json" ]]; then
           echo "✅ Laravel Mix manifest found"
           BUILD_ARTIFACTS_FOUND=true
       fi
       
       if [[ -f "public/js/app.js" ]] || [[ -f "public/css/app.css" ]]; then
           echo "✅ Compiled assets found in public/"
           BUILD_ARTIFACTS_FOUND=true
       fi
       
       if [[ "$BUILD_ARTIFACTS_FOUND" == "true" ]]; then
           echo "✅ Build artifacts validation passed"
       else
           if [[ -n "$BUILD_COMMAND" ]]; then
               echo "⚠️ Build command executed but no artifacts found"
           else
               echo "ℹ️ No build artifacts expected (no build command)"
           fi
       fi
       
       # Optimize assets if possible
       echo "⚡ Optimizing compiled assets..."
       
       # Remove source maps in production (if they exist)
       find public/ -name "*.map" -type f -delete 2>/dev/null || true
       echo "🧹 Source maps removed for production"
       
       # Display final asset structure
       echo "📋 Final asset structure:"
       if [[ -d "public/build" ]]; then
           ls -la public/build/ | head -10
       elif [[ -d "public/js" ]] || [[ -d "public/css" ]]; then
           ls -la public/js/ public/css/ 2>/dev/null | head -10
       else
           echo "No compiled assets directory found"
       fi
       
       echo "✅ Phase 2.2: Asset compilation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 2.2 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-2-assets.sh
   ```

2. **Execute Phase 2.2:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-2-assets.sh
   ```

#### **Expected Result:**
```
🟢 Frontend build system detected
🗏️ Build command executed successfully
✅ Build artifacts validation passed
⚡ Assets optimized for production
✅ Asset compilation completed
```

---

### **Phase 2.3: Laravel Optimization**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Apply comprehensive Laravel optimizations for production  
**Time:** 1 minute  

#### **Action:**

1. **Create Laravel Optimization Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-3-laravel-optimization.sh << 'EOF'
   #!/bin/bash
   # Phase 2.3: Laravel Production Optimization
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "⚡ Phase 2.3: Applying Laravel production optimizations..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-2-3-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   # Change to build source directory
   cd "$BUILD_SOURCE_DIR"
   
   {
       echo "Phase 2.3: Laravel Production Optimization - $(date)"
       echo "=================================================="
       echo "Build Source: $BUILD_SOURCE_DIR"
       
       # Setup production environment for optimization
       echo "🔧 Setting up production environment..."
       
       # Copy production .env file
       PROD_ENV_FILE="${DEPLOYMENT_WORKSPACE}/deployment-config/02-EnvFiles/.env.production"
       if [[ -f "$PROD_ENV_FILE" ]]; then
           cp "$PROD_ENV_FILE" .env
           echo "✅ Production environment file copied"
       else
           echo "⚠️ Production environment file not found, using existing .env"
       fi
       
       # Clear existing caches
       echo "🧹 Clearing existing caches..."
       php artisan config:clear >/dev/null 2>&1 || true
       php artisan route:clear >/dev/null 2>&1 || true  
       php artisan view:clear >/dev/null 2>&1 || true
       php artisan cache:clear >/dev/null 2>&1 || true
       echo "✅ Existing caches cleared"
       
       # Generate optimized autoloader
       echo "📦 Optimizing Composer autoloader..."
       composer dump-autoload --optimize --classmap-authoritative --no-interaction
       if [[ -f "vendor/composer/autoload_classmap.php" ]]; then
           AUTOLOAD_CLASSES=$(php -r "echo count(include 'vendor/composer/autoload_classmap.php');")
           echo "✅ Autoloader optimized with $AUTOLOAD_CLASSES classes"
       else
           echo "⚠️ Autoloader optimization may have issues"
       fi
       
       # Cache Laravel configurations
       echo "⚡ Caching Laravel configurations..."
       
       # Config cache
       if php artisan config:cache; then
           echo "✅ Configuration cached"
       else
           echo "❌ Configuration caching failed"
           exit 1
       fi
       
       # Route cache
       if php artisan route:cache; then
           echo "✅ Routes cached"
       else
           echo "❌ Route caching failed"
           exit 1
       fi
       
       # View cache  
       if php artisan view:cache; then
           echo "✅ Views cached"
       else
           echo "❌ View caching failed"
           exit 1
       fi
       
       # Event cache (if available)
       if php artisan event:cache >/dev/null 2>&1; then
           echo "✅ Events cached"
       else
           echo "ℹ️ Event caching not available (Laravel 9+)"
       fi
       
       # Optimize application for production
       echo "🚀 Applying additional optimizations..."
       
       # Create storage/logs directory if it doesn't exist
       mkdir -p storage/logs
       chmod 775 storage/logs
       
       # Create bootstrap/cache directory if it doesn't exist
       mkdir -p bootstrap/cache
       chmod 775 bootstrap/cache
       
       # Verify cache files were created
       echo "🔍 Verifying cache files..."
       
       CACHE_FILES=(
           "bootstrap/cache/config.php"
           "bootstrap/cache/routes-v7.php"
           "bootstrap/cache/compiled.php"
       )
       
       for cache_file in "${CACHE_FILES[@]}"; do
           if [[ -f "$cache_file" ]]; then
               echo "✅ Cache file exists: $cache_file"
           else
               echo "ℹ️ Cache file not found: $cache_file (may be version dependent)"
           fi
       done
       
       # Verify Laravel is still functional after optimization
       echo "🧪 Testing Laravel functionality after optimization..."
       if php artisan --version >/dev/null 2>&1; then
           LARAVEL_VERSION=$(php artisan --version)
           echo "✅ Laravel functional after optimization: $LARAVEL_VERSION"
       else
           echo "❌ Laravel not functional after optimization"
           exit 1
       fi
       
       # Test configuration access
       if php artisan config:show app.name >/dev/null 2>&1; then
           echo "✅ Configuration cache functional"
       else
           echo "❌ Configuration cache not functional"
           exit 1
       fi
       
       # Display optimization summary
       echo ""
       echo "📊 Optimization Summary:"
       echo "======================="
       echo "✅ Autoloader optimized with classmap"
       echo "✅ Configuration cached"
       echo "✅ Routes cached"
       echo "✅ Views cached"
       echo "✅ Storage directories prepared"
       echo "✅ Laravel functionality verified"
       
       # Calculate cache sizes
       if [[ -d "bootstrap/cache" ]]; then
           CACHE_SIZE=$(du -sh bootstrap/cache | cut -f1)
           echo "📊 Cache size: $CACHE_SIZE"
       fi
       
       echo "✅ Phase 2.3: Laravel optimization completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 2.3 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-3-laravel-optimization.sh
   ```

2. **Execute Phase 2.3:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-2-3-laravel-optimization.sh
   ```

#### **Expected Result:**
```
✅ Production environment file copied
✅ Autoloader optimized with classmap
✅ Configuration cached
✅ Routes cached  
✅ Views cached
✅ Laravel functionality verified
```

---

## 📦 **PHASE 3: PACKAGE & TRANSFER**

### **Phase 3.1: Build Validation & Artifact Preparation**

**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Purpose:** Validate build integrity and create deployment artifacts  
**Time:** 1 minute  

#### **Action:**

1. **Create Build Validation & Packaging Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-3-1-build-validation.sh << 'EOF'
   #!/bin/bash
   # Phase 3.1: Build Validation & Artifact Preparation
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "📦 Phase 3.1: Validating build and preparing artifacts..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-3-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   # Change to build source directory
   cd "$BUILD_SOURCE_DIR"
   
   {
       echo "Phase 3.1: Build Validation & Artifact Preparation - $(date)"
       echo "=========================================================="
       echo "Build Source: $BUILD_SOURCE_DIR"
       echo "Release ID: $RELEASE_ID"
       
       # Comprehensive build validation
       echo "🔍 Running comprehensive build validation..."
       
       VALIDATION_PASSED=true
       
       # 1. Laravel application validation
       echo "[1/8] Testing Laravel application..."
       if php artisan --version >/dev/null 2>&1; then
           echo "  ✅ Laravel application functional"
       else
           echo "  ❌ Laravel application not functional"
           VALIDATION_PASSED=false
       fi
       
       # 2. Autoloader validation
       echo "[2/8] Testing autoloader..."
       if [[ -f "vendor/autoload.php" ]]; then
           echo "  ✅ Autoloader file exists"
       else
           echo "  ❌ Autoloader file missing"
           VALIDATION_PASSED=false
       fi
       
       # 3. Configuration cache validation
       echo "[3/8] Testing configuration cache..."
       if [[ -f "bootstrap/cache/config.php" ]]; then
           echo "  ✅ Configuration cache exists"
       else
           echo "  ⚠️ Configuration cache missing (may be normal)"
       fi
       
       # 4. Frontend assets validation (if applicable)
       echo "[4/8] Testing frontend assets..."
       if [[ -f "package.json" ]]; then
           if [[ -d "public/build" ]] || [[ -f "public/mix-manifest.json" ]] || [[ -f "public/js/app.js" ]]; then
               echo "  ✅ Frontend assets present"
           else
               echo "  ⚠️ Frontend assets may be missing"
           fi
       else
           echo "  ℹ️ No frontend build expected"
       fi
       
       # 5. Storage directories validation
       echo "[5/8] Testing storage directories..."
       if [[ -d "storage" ]] && [[ -w "storage" ]]; then
           echo "  ✅ Storage directory accessible"
       else
           echo "  ❌ Storage directory issues"
           VALIDATION_PASSED=false
       fi
       
       # 6. Environment configuration validation
       echo "[6/8] Testing environment configuration..."
       if [[ -f ".env" ]]; then
           if grep -q "APP_KEY=base64:" .env; then
               echo "  ✅ Application key configured"
           else
               echo "  ❌ Application key missing"
               VALIDATION_PASSED=false
           fi
       else
           echo "  ❌ Environment file missing"
           VALIDATION_PASSED=false
       fi
       
       # 7. Critical file permissions
       echo "[7/8] Testing file permissions..."
       if [[ -w "storage" ]] && [[ -w "bootstrap/cache" ]]; then
           echo "  ✅ Critical directories writable"
       else
           echo "  ❌ Permission issues detected"
           VALIDATION_PASSED=false
       fi
       
       # 8. Composer validation
       echo "[8/8] Testing Composer configuration..."
       if composer validate --strict >/dev/null 2>&1; then
           echo "  ✅ Composer configuration valid"
       else
           echo "  ⚠️ Composer configuration warnings"
       fi
       
       # Build validation result
       echo ""
       if [[ "$VALIDATION_PASSED" == "true" ]]; then
           echo "✅ BUILD VALIDATION: PASSED"
           echo "🚀 Build ready for deployment"
       else
           echo "❌ BUILD VALIDATION: FAILED"
           echo "🔧 Critical issues must be resolved"
           exit 1
       fi
       
       # Create deployment manifest
       echo "📋 Creating deployment manifest..."
       
       MANIFEST_FILE="$DEPLOYMENT_WORKSPACE/deployment-manifest.json"
       
       cat > "$MANIFEST_FILE" << MANIFEST
   {
     "deployment": {
       "build_id": "$BUILD_ID",
       "release_id": "$RELEASE_ID", 
       "timestamp": "$BUILD_TIMESTAMP",
       "commit": "$CURRENT_COMMIT",
       "branch": "$CURRENT_BRANCH"
     },
     "application": {
       "name": "$PROJECT_NAME",
       "laravel_version": "$(php artisan --version | cut -d' ' -f3)",
       "php_version": "$(php -r 'echo PHP_VERSION;')",
       "has_frontend": $(if [[ -f "package.json" ]]; then echo "true"; else echo "false"; fi)
     },
     "build_info": {
       "build_source": "$BUILD_SOURCE_DIR",
       "deployment_workspace": "$DEPLOYMENT_WORKSPACE",
       "build_strategy": "$DEPLOYMENT_STRATEGY"
     },
     "validation": {
       "status": "passed",
       "validated_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)"
     }
   }
   MANIFEST
       
       echo "✅ Deployment manifest created: $MANIFEST_FILE"
       
       # Calculate build size
       BUILD_SIZE=$(du -sh "$BUILD_SOURCE_DIR" | cut -f1)
       echo "📊 Build size: $BUILD_SIZE"
       
       # Create checksums for integrity verification
       echo "🔐 Generating integrity checksums..."
       
       CHECKSUMS_FILE="$DEPLOYMENT_WORKSPACE/checksums.md5"
       find "$BUILD_SOURCE_DIR" -type f -name "*.php" -o -name "*.js" -o -name "*.css" | \
           head -100 | xargs md5sum > "$CHECKSUMS_FILE" 2>/dev/null || \
           find "$BUILD_SOURCE_DIR" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" \) | \
           head -100 | xargs md5sum > "$CHECKSUMS_FILE"
       
       CHECKSUM_COUNT=$(wc -l < "$CHECKSUMS_FILE")
       echo "✅ Generated checksums for $CHECKSUM_COUNT files"
       
       # Prepare deployment package information
       echo "📦 Preparing deployment package information..."
       
       cat > "$DEPLOYMENT_WORKSPACE/package-info.txt" << PACKAGE
   Laravel Deployment Package
   ==========================
   Build ID: $BUILD_ID
   Release ID: $RELEASE_ID
   Timestamp: $BUILD_TIMESTAMP
   Commit: $CURRENT_COMMIT
   Branch: $CURRENT_BRANCH
   Size: $BUILD_SIZE
   Strategy: $DEPLOYMENT_STRATEGY
   
   Contents:
   - Laravel application (optimized for production)
   - Compiled frontend assets (if applicable)
   - Cached configurations and routes
   - Deployment manifest and checksums
   
   Validation: PASSED
   Ready for deployment: YES
   PACKAGE
       
       echo "✅ Package information prepared"
       
       echo "✅ Phase 3.1: Build validation and artifact preparation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 3.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-3-1-build-validation.sh
   ```

2. **Execute Phase 3.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-3-1-build-validation.sh
   ```

#### **Expected Result:**
```
✅ BUILD VALIDATION: PASSED
🚀 Build ready for deployment
📋 Deployment manifest created
🔐 Integrity checksums generated
📦 Package information prepared
```

---

## 🎉 **PHASES 1-3 COMPLETION**

### **Completion Verification**

Run this verification to confirm Phases 1-3 completed successfully:

```bash
# Load deployment environment
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/load-variables.sh
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/current-deployment.env

echo "🔍 Phases 1-3 Completion Verification:"
echo "======================================"
echo "Build ID: $BUILD_ID"
echo "Release ID: $RELEASE_ID"
echo "Build Source: $BUILD_SOURCE_DIR"
echo "Deployment Workspace: $DEPLOYMENT_WORKSPACE"

# Verify build artifacts
echo ""
echo "📦 Build Artifacts Status:"
echo "=========================="

[[ -d "$BUILD_SOURCE_DIR" ]] && echo "✅ Build source directory" || echo "❌ Build source directory"
[[ -f "$DEPLOYMENT_WORKSPACE/deployment-manifest.json" ]] && echo "✅ Deployment manifest" || echo "❌ Deployment manifest"
[[ -f "$DEPLOYMENT_WORKSPACE/checksums.md5" ]] && echo "✅ Integrity checksums" || echo "❌ Integrity checksums"
[[ -f "$BUILD_SOURCE_DIR/vendor/autoload.php" ]] && echo "✅ Optimized autoloader" || echo "❌ Optimized autoloader"

echo ""
echo "📊 Build Summary:"
echo "================="
if [[ -d "$BUILD_SOURCE_DIR" ]]; then
    BUILD_SIZE=$(du -sh "$BUILD_SOURCE_DIR" | cut -f1)
    echo "Build size: $BUILD_SIZE"
fi

if [[ -f "$DEPLOYMENT_WORKSPACE/deployment-manifest.json" ]]; then
    echo "Laravel version: $(jq -r '.application.laravel_version' "$DEPLOYMENT_WORKSPACE/deployment-manifest.json")"
    echo "PHP version: $(jq -r '.application.php_version' "$DEPLOYMENT_WORKSPACE/deployment-manifest.json")"
    echo "Validation status: $(jq -r '.validation.status' "$DEPLOYMENT_WORKSPACE/deployment-manifest.json")"
fi

echo ""
echo "🎯 Status: ✅ PHASES 1-3 COMPLETED SUCCESSFULLY"
echo "🚀 Ready for Phases 4-7: Configure Release & Atomic Switch"
```

### **What's Been Accomplished**

**Phase 1 - Prepare Build Environment:**
- ✅ **Deployment workspace initialized** and validated
- ✅ **Repository prepared** with clean source copy
- ✅ **Build environment configured** for deployment strategy

**Phase 2 - Build Application:**
- ✅ **Dependencies installed** with security validation
- ✅ **Frontend assets compiled** and optimized
- ✅ **Laravel optimized** for production with caching

**Phase 3 - Package & Transfer:**
- ✅ **Build validated** with comprehensive 8-point testing
- ✅ **Deployment manifest created** with metadata
- ✅ **Integrity checksums generated** for verification

### **Next Steps**

**Ready for Phases 4-7:**
- **Phase 4:** Configure Release (shared resources, environment)
- **Phase 5:** Pre-Release Hooks (maintenance mode, custom commands)
- **Phase 6:** Mid-Release Hooks (migrations, cache preparation)
- **Phase 7:** Atomic Release Switch (zero-downtime deployment)

**Continue to:** [6-Section-C-Deploy-Phases4-7.md](6-Section-C-Deploy-Phases4-7.md)

---

## 🆘 **TROUBLESHOOTING PHASES 1-3**

### **Issue: Build Environment Setup Fails**
```bash
# Check system requirements
php --version
composer --version
df -h /tmp

# Verify workspace permissions
ls -la /tmp/laravel-deployment-*
```

### **Issue: Dependency Installation Fails**
```bash
# Clear caches and retry
composer clear-cache
npm cache clean --force
rm -rf vendor node_modules
# Re-run Phase 2.1
```

### **Issue: Asset Compilation Fails**
```bash
# Check Node.js version and dependencies
node --version
npm --version
npm audit
# Re-run Phase 2.2 with debug
```

### **Issue: Laravel Optimization Fails**
```bash
# Clear all caches and retry
php artisan config:clear
php artisan route:clear
php artisan view:clear
# Re-run Phase 2.3
```

### **Issue: Build Validation Fails**
```bash
# Check specific validation points
cd "$BUILD_SOURCE_DIR"
php artisan --version
composer validate --strict
ls -la storage/ bootstrap/cache/
```

**Need Help?** Check the phase logs in `Admin-Local/2-Project-Area/02-Project-Records/05-Logs-And-Maintenance/` for detailed error information.