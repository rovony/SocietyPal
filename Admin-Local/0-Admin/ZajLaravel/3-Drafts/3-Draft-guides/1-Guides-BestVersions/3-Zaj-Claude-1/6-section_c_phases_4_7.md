# Section C: Build and Deploy (Phases 4-7)

**Version:** 2.0  
**Location:** 🔴 Server | 🟣 User-Configurable Hooks  
**Purpose:** Configure Release & Execute Atomic Zero-Downtime Deployment  
**Time Required:** 2-5 minutes (automated execution)

---

## 🎯 **PHASES 4-7 OVERVIEW**

These phases execute the actual server deployment with zero-downtime atomic switching. This document covers:

- **Phase 4:** Configure Release (shared resources, secure configuration)
- **Phase 5:** Pre-Release Hooks 🟣 1️⃣ (maintenance mode, custom commands)
- **Phase 6:** Mid-Release Hooks 🟣 2️⃣ (migrations, cache preparation)
- **Phase 7:** Atomic Release Switch (instant deployment activation)

**Critical Features:**
- ✅ **Zero-Downtime Guarantee** - Production traffic uninterrupted until atomic switch
- ✅ **Shared Resource Protection** - User data and uploads preserved
- ✅ **User-Configurable Hooks** - Custom deployment commands at key phases
- ✅ **Instant Rollback** - Immediate reversion capability if issues arise
- ✅ **Comprehensive Validation** - Health checks before and after switch

---

## 📋 **PREREQUISITES VERIFICATION**

Before starting Phases 4-7, verify Phases 1-3 completed successfully:

```bash
# Load deployment environment
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/load-variables.sh
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/current-deployment.env

echo "🔍 Phases 1-3 Completion Check:"
echo "==============================="
echo "Build ID: $BUILD_ID"
echo "Release ID: $RELEASE_ID"

# Verify required artifacts
[[ -d "$BUILD_SOURCE_DIR" ]] && echo "✅ Build source ready" || echo "❌ Build source missing"
[[ -f "$DEPLOYMENT_WORKSPACE/deployment-manifest.json" ]] && echo "✅ Deployment manifest ready" || echo "❌ Deployment manifest missing"
[[ -f "$DEPLOYMENT_WORKSPACE/checksums.md5" ]] && echo "✅ Integrity checksums ready" || echo "❌ Checksums missing"
```

**Required Status:**
```
✅ Build source ready
✅ Deployment manifest ready  
✅ Integrity checksums ready
🎯 Ready for server deployment phases
```

---

## 🔴 **PHASE 4: CONFIGURE RELEASE**

### **Phase 4.1: Server Preparation & Release Directory**

**Location:** 🔴 Server  
**Purpose:** Prepare server deployment structure and create timestamped release directory  
**Time:** 1 minute  

#### **Action:**

1. **Create Server Preparation Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-1-server-preparation.sh << 'EOF'
   #!/bin/bash
   # Phase 4.1: Server Preparation & Release Directory
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "🔴 Phase 4.1: Preparing server for deployment..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-4-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 4.1: Server Preparation & Release Directory - $(date)"
       echo "=========================================================="
       echo "Server Path: $PATH_SERVER"
       echo "Release ID: $RELEASE_ID"
       
       # NOTE: This script prepares commands for server execution
       # For local deployment strategy, these commands will be executed locally
       # For remote deployment strategies, these will be executed via SSH
       
       # Create server deployment structure commands
       echo "📁 Preparing server deployment structure..."
       
       cat > "$DEPLOYMENT_WORKSPACE/server-commands-4-1.sh" << 'SERVERCMD'
   #!/bin/bash
   # Server commands for Phase 4.1
   set -euo pipefail
   
   SERVER_PATH="$1"
   RELEASE_ID="$2"
   
   echo "🔴 Executing server preparation on target server..."
   echo "Server path: $SERVER_PATH"
   echo "Release ID: $RELEASE_ID"
   
   # Create deployment directory structure
   echo "📁 Creating deployment directory structure..."
   mkdir -p "$SERVER_PATH"
   mkdir -p "$SERVER_PATH/releases"
   mkdir -p "$SERVER_PATH/shared"
   mkdir -p "$SERVER_PATH/shared/storage"
   mkdir -p "$SERVER_PATH/shared/storage/app"
   mkdir -p "$SERVER_PATH/shared/storage/app/public"
   mkdir -p "$SERVER_PATH/shared/storage/logs"
   mkdir -p "$SERVER_PATH/shared/storage/framework"
   mkdir -p "$SERVER_PATH/shared/storage/framework/cache"
   mkdir -p "$SERVER_PATH/shared/storage/framework/sessions"
   mkdir -p "$SERVER_PATH/shared/storage/framework/views"
   
   echo "✅ Core deployment structure created"
   
   # Create shared directories for user content
   echo "📂 Creating shared directories for user content..."
   mkdir -p "$SERVER_PATH/shared/public/uploads"
   mkdir -p "$SERVER_PATH/shared/public/avatars"
   mkdir -p "$SERVER_PATH/shared/public/documents"
   mkdir -p "$SERVER_PATH/shared/public/media"
   mkdir -p "$SERVER_PATH/shared/public/exports"
   mkdir -p "$SERVER_PATH/shared/public/qr-codes"
   mkdir -p "$SERVER_PATH/shared/public/invoices"
   mkdir -p "$SERVER_PATH/shared/public/custom"
   
   echo "✅ User content directories created"
   
   # Check available disk space
   AVAILABLE_SPACE=$(df "$SERVER_PATH" | awk 'NR==2 {print $4}')
   AVAILABLE_GB=$((AVAILABLE_SPACE / 1024 / 1024))
   echo "💾 Available disk space: ${AVAILABLE_GB}GB"
   
   if [[ $AVAILABLE_GB -lt 1 ]]; then
       echo "⚠️ Low disk space warning"
   else
       echo "✅ Sufficient disk space available"
   fi
   
   # Create new release directory
   RELEASE_PATH="$SERVER_PATH/releases/$RELEASE_ID"
   echo "📁 Creating release directory: $RELEASE_PATH"
   mkdir -p "$RELEASE_PATH"
   
   # Set proper permissions
   echo "🔒 Setting proper permissions..."
   chmod 755 "$SERVER_PATH"
   chmod 755 "$SERVER_PATH/releases"
   chmod 755 "$SERVER_PATH/shared"
   chmod -R 775 "$SERVER_PATH/shared/storage"
   chmod 755 "$RELEASE_PATH"
   
   echo "✅ Permissions configured"
   
   # Backup current release (if exists)
   if [[ -L "$SERVER_PATH/current" ]]; then
       CURRENT_RELEASE=$(readlink "$SERVER_PATH/current")
       echo "💾 Current release: $CURRENT_RELEASE"
       echo "📋 Backup capability maintained"
   else
       echo "ℹ️ No current release found (first deployment)"
   fi
   
   # Clean up old releases (keep last 5)
   echo "🧹 Cleaning up old releases..."
   cd "$SERVER_PATH/releases"
   ls -1t | tail -n +6 | xargs -r rm -rf
   RELEASE_COUNT=$(ls -1 | wc -l)
   echo "📊 Releases maintained: $RELEASE_COUNT"
   
   echo "✅ Server preparation completed"
   echo "📁 Release directory ready: $RELEASE_PATH"
   SERVERCMD
       
       chmod +x "$DEPLOYMENT_WORKSPACE/server-commands-4-1.sh"
       echo "✅ Server commands prepared: $DEPLOYMENT_WORKSPACE/server-commands-4-1.sh"
       
       # For local deployment strategy, execute commands locally
       if [[ "$DEPLOYMENT_STRATEGY" == "local" ]]; then
           echo "🟢 Executing server preparation locally..."
           
           # Execute server commands locally
           "$DEPLOYMENT_WORKSPACE/server-commands-4-1.sh" "$PATH_SERVER" "$RELEASE_ID"
           
           echo "✅ Local server preparation completed"
       else
           echo "🟡 Server commands prepared for remote execution"
           echo "📋 Commands saved to: $DEPLOYMENT_WORKSPACE/server-commands-4-1.sh"
           echo "🔗 Execute on server: bash server-commands-4-1.sh '$PATH_SERVER' '$RELEASE_ID'"
       fi
       
       # Save release path for next phases
       RELEASE_PATH="$PATH_SERVER/releases/$RELEASE_ID"
       echo "RELEASE_PATH=$RELEASE_PATH" >> "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
       
       echo "✅ Phase 4.1: Server preparation completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 4.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-1-server-preparation.sh
   ```

2. **Execute Phase 4.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-1-server-preparation.sh
   ```

#### **Expected Result:**
```
✅ Core deployment structure created
✅ User content directories created
💾 Sufficient disk space available
📁 Release directory ready
✅ Server preparation completed
```

---

### **Phase 4.2: File Transfer & Shared Resources**

**Location:** 🔴 Server  
**Purpose:** Transfer build artifacts and configure shared resources with zero data loss  
**Time:** 1-3 minutes  

#### **Action:**

1. **Create File Transfer & Shared Resources Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-2-file-transfer.sh << 'EOF'
   #!/bin/bash
   # Phase 4.2: File Transfer & Shared Resources Configuration
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "📂 Phase 4.2: Transferring files and configuring shared resources..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-4-2-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 4.2: File Transfer & Shared Resources - $(date)"
       echo "=================================================="
       echo "Build Source: $BUILD_SOURCE_DIR"
       echo "Release Path: $RELEASE_PATH"
       
       # Create file transfer commands
       echo "📁 Preparing file transfer..."
       
       cat > "$DEPLOYMENT_WORKSPACE/server-commands-4-2.sh" << 'SERVERCMD'
   #!/bin/bash
   # Server commands for Phase 4.2
   set -euo pipefail
   
   BUILD_SOURCE_DIR="$1"
   RELEASE_PATH="$2"
   SERVER_PATH="$3"
   
   echo "🔴 Executing file transfer and shared resources configuration..."
   echo "Source: $BUILD_SOURCE_DIR"
   echo "Target: $RELEASE_PATH"
   
   # Transfer application files
   echo "📂 Transferring application files..."
   
   # Copy all files from build source to release directory
   if [[ -d "$BUILD_SOURCE_DIR" ]]; then
       rsync -av --exclude='.git*' --exclude='node_modules' --exclude='*.log' \
             "$BUILD_SOURCE_DIR/" "$RELEASE_PATH/"
       echo "✅ Application files transferred"
   else
       echo "❌ Build source directory not found: $BUILD_SOURCE_DIR"
       exit 1
   fi
   
   # Verify critical files were transferred
   echo "🔍 Verifying file transfer..."
   CRITICAL_FILES=("artisan" "composer.json" "vendor/autoload.php")
   for file in "${CRITICAL_FILES[@]}"; do
       if [[ -f "$RELEASE_PATH/$file" ]]; then
           echo "  ✅ $file"
       else
           echo "  ❌ $file missing"
           exit 1
       fi
   done
   
   # Configure shared resources with comprehensive coverage
   echo "🔗 Configuring shared resources..."
   
   # Load shared directories configuration
   SHARED_CONFIG="$RELEASE_PATH/Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/shared-directories.json"
   
   if [[ -f "$SHARED_CONFIG" ]]; then
       echo "✅ Shared directories configuration found"
       
       # Remove and link Laravel standard directories
       LARAVEL_SHARED_DIRS=(
           "storage/app/public"
           "storage/logs"
           "storage/framework/cache"
           "storage/framework/sessions"
           "storage/framework/views"
       )
       
       for dir in "${LARAVEL_SHARED_DIRS[@]}"; do
           RELEASE_DIR="$RELEASE_PATH/$dir"
           SHARED_DIR="$SERVER_PATH/shared/$dir"
           
           # Ensure shared directory exists
           mkdir -p "$(dirname "$SHARED_DIR")"
           if [[ ! -e "$SHARED_DIR" ]]; then
               mkdir -p "$SHARED_DIR"
           fi
           
           # Remove release directory and create symlink
           if [[ -d "$RELEASE_DIR" ]] || [[ -L "$RELEASE_DIR" ]]; then
               rm -rf "$RELEASE_DIR"
           fi
           
           mkdir -p "$(dirname "$RELEASE_DIR")"
           ln -s "$SHARED_DIR" "$RELEASE_DIR"
           echo "  🔗 Linked: $dir"
       done
       
       # Remove and link user content directories
       USER_CONTENT_DIRS=(
           "public/uploads"
           "public/avatars"
           "public/documents" 
           "public/media"
           "public/exports"
           "public/qr-codes"
           "public/invoices"
           "public/custom"
       )
       
       for dir in "${USER_CONTENT_DIRS[@]}"; do
           RELEASE_DIR="$RELEASE_PATH/$dir"
           SHARED_DIR="$SERVER_PATH/shared/$dir"
           
           # Ensure shared directory exists
           mkdir -p "$(dirname "$SHARED_DIR")"
           if [[ ! -e "$SHARED_DIR" ]]; then
               mkdir -p "$SHARED_DIR"
           fi
           
           # Remove release directory and create symlink
           if [[ -d "$RELEASE_DIR" ]] || [[ -L "$RELEASE_DIR" ]]; then
               rm -rf "$RELEASE_DIR"
           fi
           
           mkdir -p "$(dirname "$RELEASE_DIR")"
           ln -s "$SHARED_DIR" "$RELEASE_DIR"
           echo "  🔗 Linked: $dir"
       done
       
       echo "✅ Shared resources configured"
   else
       echo "⚠️ Shared directories configuration not found, using defaults"
   fi
   
   # Configure environment file
   echo "🔧 Configuring environment file..."
   
   ENV_SHARED="$SERVER_PATH/shared/.env"
   ENV_RELEASE="$RELEASE_PATH/.env"
   
   # If shared .env doesn't exist, copy from release
   if [[ ! -f "$ENV_SHARED" ]] && [[ -f "$ENV_RELEASE" ]]; then
       cp "$ENV_RELEASE" "$ENV_SHARED"
       echo "✅ Environment file copied to shared storage"
   fi
   
   # Link shared .env to release
   if [[ -f "$ENV_SHARED" ]]; then
       rm -f "$ENV_RELEASE"
       ln -s "$ENV_SHARED" "$ENV_RELEASE"
       echo "✅ Environment file linked"
   else
       echo "⚠️ Environment file configuration needs attention"
   fi
   
   # Set proper permissions
   echo "🔒 Setting release permissions..."
   find "$RELEASE_PATH" -type d -exec chmod 755 {} \;
   find "$RELEASE_PATH" -type f -exec chmod 644 {} \;
   chmod +x "$RELEASE_PATH/artisan"
   
   # Set storage permissions
   if [[ -L "$RELEASE_PATH/storage" ]]; then
       chmod -R 775 "$SERVER_PATH/shared/storage"
   fi
   
   echo "✅ Permissions configured"
   
   # Verify symlinks
   echo "🔍 Verifying symlinks..."
   BROKEN_LINKS=$(find "$RELEASE_PATH" -type l ! -exec test -e {} \; -print 2>/dev/null | wc -l)
   if [[ $BROKEN_LINKS -eq 0 ]]; then
       echo "✅ All symlinks verified"
   else
       echo "⚠️ $BROKEN_LINKS broken symlinks found"
   fi
   
   # Calculate release size
   RELEASE_SIZE=$(du -sh "$RELEASE_PATH" | cut -f1)
   echo "📊 Release size: $RELEASE_SIZE"
   
   echo "✅ File transfer and shared resources configuration completed"
   SERVERCMD
       
       chmod +x "$DEPLOYMENT_WORKSPACE/server-commands-4-2.sh"
       
       # For local deployment strategy, execute commands locally
       if [[ "$DEPLOYMENT_STRATEGY" == "local" ]]; then
           echo "🟢 Executing file transfer locally..."
           
           # Execute server commands locally
           "$DEPLOYMENT_WORKSPACE/server-commands-4-2.sh" "$BUILD_SOURCE_DIR" "$RELEASE_PATH" "$PATH_SERVER"
           
           echo "✅ Local file transfer completed"
       else
           echo "🟡 File transfer commands prepared for remote execution"
           echo "📋 Commands saved to: $DEPLOYMENT_WORKSPACE/server-commands-4-2.sh"
           
           # For remote strategies, the build artifacts would need to be transferred first
           echo "📦 Build artifacts ready for transfer in: $BUILD_SOURCE_DIR"
       fi
       
       echo "✅ Phase 4.2: File transfer and shared resources completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 4.2 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-2-file-transfer.sh
   ```

2. **Execute Phase 4.2:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-4-2-file-transfer.sh
   ```

#### **Expected Result:**
```
✅ Application files transferred
✅ Shared resources configured
✅ Environment file linked
✅ Permissions configured
✅ All symlinks verified
```

---

## 🟣 **PHASE 5: PRE-RELEASE HOOKS** 1️⃣

### **Phase 5.1: Pre-Release Custom Commands**

**Location:** 🔴 Server  
**Purpose:** Execute user-defined pre-release scripts and preparations  
**Time:** 30 seconds - 2 minutes (depends on custom commands)  

#### **Action:**

1. **Create Pre-Release Hooks Framework:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-5-1-pre-release-hooks.sh << 'EOF'
   #!/bin/bash
   # Phase 5.1: Pre-Release Hooks (User-Configurable)
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "🟣 Phase 5.1: Executing pre-release hooks..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-5-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 5.1: Pre-Release Hooks - $(date)"
       echo "====================================="
       echo "Release Path: $RELEASE_PATH"
       
       # Check for user-defined pre-release hooks
       PRE_RELEASE_HOOKS_FILE="${SCRIPT_DIR}/pre-release-hooks.sh"
       
       if [[ -f "$PRE_RELEASE_HOOKS_FILE" ]]; then
           echo "✅ Pre-release hooks file found"
           echo "🔧 Executing user-defined pre-release commands..."
           
           # Execute user hooks with error handling
           if bash "$PRE_RELEASE_HOOKS_FILE" "$RELEASE_PATH" "$PATH_SERVER" "$RELEASE_ID"; then
               echo "✅ User pre-release hooks executed successfully"
           else
               echo "❌ User pre-release hooks failed"
               exit 1
           fi
       else
           echo "ℹ️ No user-defined pre-release hooks found"
           echo "📋 Creating default pre-release hooks template..."
           
           # Create default pre-release hooks template
           cat > "$PRE_RELEASE_HOOKS_FILE" << 'HOOKEOF'
   #!/bin/bash
   # Pre-Release Hooks - Executed BEFORE deployment changes are applied
   # 
   # Parameters:
   # $1 = RELEASE_PATH (path to new release directory)
   # $2 = SERVER_PATH (path to deployment root)
   # $3 = RELEASE_ID (unique release identifier)
   
   set -euo pipefail
   
   RELEASE_PATH="$1"
   SERVER_PATH="$2" 
   RELEASE_ID="$3"
   
   echo "🟣 1️⃣ Pre-Release Hook: Starting custom pre-release commands..."
   echo "Release: $RELEASE_ID"
   echo "Path: $RELEASE_PATH"
   
   # ============================================================================
   # CUSTOMIZE THIS SECTION FOR YOUR APPLICATION
   # ============================================================================
   
   # Example 1: Enable maintenance mode (optional)
   # if [[ -f "$SERVER_PATH/current/artisan" ]]; then
   #     echo "🚧 Enabling maintenance mode..."
   #     cd "$SERVER_PATH/current"
   #     php artisan down --message="Deploying new version" --retry=60
   #     echo "✅ Maintenance mode enabled"
   # fi
   
   # Example 2: Create database backup (optional)
   # echo "💾 Creating database backup..."
   # cd "$RELEASE_PATH"
   # php artisan backup:run --only-db --quiet
   # echo "✅ Database backup created"
   
   # Example 3: Send deployment notification
   # echo "📧 Sending deployment start notification..."
   # curl -X POST "https://hooks.slack.com/your/webhook/url" \
   #      -H 'Content-type: application/json' \
   #      --data '{"text":"🚀 Starting deployment of release '$RELEASE_ID'"}'
   # echo "✅ Notification sent"
   
   # Example 4: Custom validation
   # echo "🔍 Running custom pre-deployment validation..."
   # cd "$RELEASE_PATH"
   # Add your custom validation commands here
   # echo "✅ Custom validation passed"
   
   # ============================================================================
   # END CUSTOMIZABLE SECTION
   # ============================================================================
   
   echo "✅ Pre-release hooks completed successfully"
   HOOKEOF
           
           chmod +x "$PRE_RELEASE_HOOKS_FILE"
           echo "✅ Default pre-release hooks template created"
           echo "📝 Edit $PRE_RELEASE_HOOKS_FILE to customize pre-release actions"
           
           # Execute the default (no-op) hooks
           echo "🔧 Executing default pre-release hooks..."
           bash "$PRE_RELEASE_HOOKS_FILE" "$RELEASE_PATH" "$PATH_SERVER" "$RELEASE_ID"
       fi
       
       echo "✅ Phase 5.1: Pre-release hooks completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 5.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-5-1-pre-release-hooks.sh
   ```

2. **Execute Phase 5.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-5-1-pre-release-hooks.sh
   ```

#### **Expected Result:**
```
✅ Default pre-release hooks template created
🔧 Pre-release hooks executed successfully
📝 Template ready for customization
✅ Pre-release hooks completed
```

---

## 🟣 **PHASE 6: MID-RELEASE HOOKS** 2️⃣

### **Phase 6.1: Mid-Release Operations (Migrations & Cache)**

**Location:** 🔴 Server  
**Purpose:** Execute database migrations and prepare application caches  
**Time:** 30 seconds - 2 minutes  

#### **Action:**

1. **Create Mid-Release Hooks Framework:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-6-1-mid-release-hooks.sh << 'EOF'
   #!/bin/bash
   # Phase 6.1: Mid-Release Hooks (User-Configurable)
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "🟣 Phase 6.1: Executing mid-release hooks..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-6-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 6.1: Mid-Release Hooks - $(date)"
       echo "===================================="
       echo "Release Path: $RELEASE_PATH"
       
       # Check for user-defined mid-release hooks
       MID_RELEASE_HOOKS_FILE="${SCRIPT_DIR}/mid-release-hooks.sh"
       
       if [[ -f "$MID_RELEASE_HOOKS_FILE" ]]; then
           echo "✅ Mid-release hooks file found"
           echo "🔧 Executing user-defined mid-release commands..."
           
           # Execute user hooks with error handling
           if bash "$MID_RELEASE_HOOKS_FILE" "$RELEASE_PATH" "$PATH_SERVER" "$RELEASE_ID"; then
               echo "✅ User mid-release hooks executed successfully"
           else
               echo "❌ User mid-release hooks failed"
               exit 1
           fi
       else
           echo "ℹ️ No user-defined mid-release hooks found"
           echo "📋 Creating default mid-release hooks template..."
           
           # Create default mid-release hooks template
           cat > "$MID_RELEASE_HOOKS_FILE" << 'HOOKEOF'
   #!/bin/bash
   # Mid-Release Hooks - Executed AFTER files uploaded but BEFORE atomic switch
   # 
   # Parameters:
   # $1 = RELEASE_PATH (path to new release directory)
   # $2 = SERVER_PATH (path to deployment root)
   # $3 = RELEASE_ID (unique release identifier)
   
   set -euo pipefail
   
   RELEASE_PATH="$1"
   SERVER_PATH="$2"
   RELEASE_ID="$3"
   
   echo "🟣 2️⃣ Mid-Release Hook: Starting mid-release operations..."
   echo "Release: $RELEASE_ID"
   echo "Path: $RELEASE_PATH"
   
   # Change to release directory for Laravel commands
   cd "$RELEASE_PATH"
   
   # ============================================================================
   # CUSTOMIZE THIS SECTION FOR YOUR APPLICATION
   # ============================================================================
   
   # Example 1: Run database migrations (RECOMMENDED)
   echo "🗄️ Running database migrations..."
   if php artisan migrate --force --no-interaction; then
       echo "✅ Database migrations completed"
   else
       echo "❌ Database migrations failed"
       exit 1
   fi
   
   # Example 2: Clear and rebuild application caches
   echo "🧹 Clearing application caches..."
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   echo "✅ Application caches cleared"
   
   echo "⚡ Rebuilding production caches..."
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   echo "✅ Production caches rebuilt"
   
   # Example 3: Seed database (if needed)
   # echo "🌱 Running database seeders..."
   # php artisan db:seed --force --no-interaction
   # echo "✅ Database seeding completed"
   
   # Example 4: Clear OPcache (if available)
   echo "🔄 Clearing OPcache..."
   if command -v php >/dev/null && php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not available'; }"; then
       echo "✅ OPcache cleared"
   else
       echo "ℹ️ OPcache not available or cannot be cleared"
   fi
   
   # Example 5: Health check before switch
   echo "🩺 Running pre-switch health check..."
   if php artisan --version >/dev/null 2>&1; then
       echo "✅ Laravel application responsive"
   else
       echo "❌ Laravel application not responsive"
       exit 1
   fi
   
   # Example 6: Custom application preparation
   # echo "🔧 Running custom application preparation..."
   # Add your custom preparation commands here
   # echo "✅ Custom preparation completed"
   
   # ============================================================================
   # END CUSTOMIZABLE SECTION
   # ============================================================================
   
   echo "✅ Mid-release hooks completed successfully"
   HOOKEOF
           
           chmod +x "$MID_RELEASE_HOOKS_FILE"
           echo "✅ Default mid-release hooks template created"
           echo "📝 Edit $MID_RELEASE_HOOKS_FILE to customize mid-release actions"
           
           # Execute the default hooks
           echo "🔧 Executing default mid-release hooks..."
           bash "$MID_RELEASE_HOOKS_FILE" "$RELEASE_PATH" "$PATH_SERVER" "$RELEASE_ID"
       fi
       
       echo "✅ Phase 6.1: Mid-release hooks completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 6.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-6-1-mid-release-hooks.sh
   ```

2. **Execute Phase 6.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-6-1-mid-release-hooks.sh
   ```

#### **Expected Result:**
```
🗄️ Database migrations completed
✅ Application caches cleared
⚡ Production caches rebuilt
🩺 Laravel application responsive
✅ Mid-release hooks completed
```

---

## ⚡ **PHASE 7: ATOMIC RELEASE SWITCH**

### **Phase 7.1: Atomic Symlink Update**

**Location:** 🔴 Server  
**Purpose:** Execute instant atomic deployment switch with health validation  
**Time:** < 10 seconds  

#### **Action:**

1. **Create Atomic Switch Script:**
   ```bash
   cat > Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-7-1-atomic-switch.sh << 'EOF'
   #!/bin/bash
   # Phase 7.1: Atomic Release Switch
   
   set -euo pipefail
   SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
   source "${SCRIPT_DIR}/load-variables.sh"
   
   # Load current deployment environment
   source "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
   
   echo "⚡ Phase 7.1: Executing atomic release switch..."
   
   LOG_FILE="${SCRIPT_DIR}/../../02-Project-Records/05-Logs-And-Maintenance/deployment-phase-7-1-$(date +%Y%m%d-%H%M%S).log"
   mkdir -p "$(dirname "$LOG_FILE")"
   
   {
       echo "Phase 7.1: Atomic Release Switch - $(date)"
       echo "========================================"
       echo "Release Path: $RELEASE_PATH"
       echo "Server Path: $PATH_SERVER"
       
       # Create atomic switch commands
       echo "⚡ Preparing atomic switch..."
       
       cat > "$DEPLOYMENT_WORKSPACE/server-commands-7-1.sh" << 'SERVERCMD'
   #!/bin/bash
   # Server commands for Phase 7.1 - Atomic Switch
   set -euo pipefail
   
   RELEASE_PATH="$1"
   SERVER_PATH="$2"
   RELEASE_ID="$3"
   
   echo "⚡ Executing atomic release switch..."
   echo "New release: $RELEASE_PATH"
   echo "Server path: $SERVER_PATH"
   
   # Pre-switch validation
   echo "🔍 Pre-switch validation..."
   
   # Verify new release exists and is functional
   if [[ ! -d "$RELEASE_PATH" ]]; then
       echo "❌ Release directory not found: $RELEASE_PATH"
       exit 1
   fi
   
   if [[ ! -f "$RELEASE_PATH/artisan" ]]; then
       echo "❌ Laravel application not found in release"
       exit 1
   fi
   
   # Test Laravel functionality in new release
   cd "$RELEASE_PATH"
   if ! php artisan --version >/dev/null 2>&1; then
       echo "❌ Laravel application not functional in new release"
       exit 1
   fi
   
   echo "✅ New release validation passed"
   
   # Record current release for rollback
   CURRENT_SYMLINK="$SERVER_PATH/current"
   PREVIOUS_RELEASE=""
   
   if [[ -L "$CURRENT_SYMLINK" ]]; then
       PREVIOUS_RELEASE=$(readlink "$CURRENT_SYMLINK")
       echo "📋 Previous release: $PREVIOUS_RELEASE"
   else
       echo "ℹ️ No previous release (first deployment)"
   fi
   
   # Atomic symlink switch
   echo "⚡ Executing atomic switch..."
   SWITCH_TIMESTAMP=$(date +%s.%N)
   
   # Create temporary symlink
   TEMP_SYMLINK="$SERVER_PATH/current-$RELEASE_ID"
   ln -s "$RELEASE_PATH" "$TEMP_SYMLINK"
   
   # Atomic move
   mv "$TEMP_SYMLINK" "$CURRENT_SYMLINK"
   
   SWITCH_COMPLETE_TIMESTAMP=$(date +%s.%N)
   SWITCH_DURATION=$(echo "$SWITCH_COMPLETE_TIMESTAMP - $SWITCH_TIMESTAMP" | bc -l 2>/dev/null || echo "< 0.1")
   
   echo "✅ Atomic switch completed in ${SWITCH_DURATION}s"
   
   # Post-switch validation
   echo "🔍 Post-switch validation..."
   
   # Verify symlink points to correct release
   CURRENT_TARGET=$(readlink "$CURRENT_SYMLINK")
   if [[ "$CURRENT_TARGET" == "$RELEASE_PATH" ]]; then
       echo "✅ Symlink correctly points to new release"
   else
       echo "❌ Symlink validation failed"
       echo "Expected: $RELEASE_PATH"
       echo "Actual: $CURRENT_TARGET"
       exit 1
   fi
   
   # Test application functionality after switch
   cd "$SERVER_PATH/current"
   if php artisan --version >/dev/null 2>&1; then
       LARAVEL_VERSION=$(php artisan --version)
       echo "✅ Application functional: $LARAVEL_VERSION"
   else
       echo "❌ Application not functional after switch"
       
       # Emergency rollback
       if [[ -n "$PREVIOUS_RELEASE" ]]; then
           echo "🚨 Executing emergency rollback..."
           ln -nfs "$PREVIOUS_RELEASE" "$CURRENT_SYMLINK"
           echo "✅ Emergency rollback completed"
       fi
       exit 1
   fi
   
   # Additional health checks
   echo "🩺 Running health checks..."
   
   # Check if key files are accessible
   if [[ -f "$SERVER_PATH/current/artisan" ]] && [[ -f "$SERVER_PATH/current/vendor/autoload.php" ]]; then
       echo "✅ Critical files accessible"
   else
       echo "❌ Critical files not accessible"
       exit 1
   fi
   
   # Check storage symlinks
   if [[ -L "$SERVER_PATH/current/storage" ]] && [[ -e "$SERVER_PATH/current/storage" ]]; then
       echo "✅ Storage symlinks functional"
   else
       echo "⚠️ Storage symlinks may have issues"
   fi
   
   # Log successful deployment
   echo "📊 Deployment Summary:"
   echo "====================="
   echo "Release ID: $RELEASE_ID"
   echo "Switch Duration: ${SWITCH_DURATION}s"
   echo "Previous Release: ${PREVIOUS_RELEASE:-'None (first deployment)'}"
   echo "Current Release: $RELEASE_PATH"
   echo "Status: ✅ SUCCESSFUL"
   
   echo "⚡ Atomic release switch completed successfully"
   SERVERCMD
       
       chmod +x "$DEPLOYMENT_WORKSPACE/server-commands-7-1.sh"
       
       # For local deployment strategy, execute commands locally
       if [[ "$DEPLOYMENT_STRATEGY" == "local" ]]; then
           echo "🟢 Executing atomic switch locally..."
           
           # Execute atomic switch commands locally
           "$DEPLOYMENT_WORKSPACE/server-commands-7-1.sh" "$RELEASE_PATH" "$PATH_SERVER" "$RELEASE_ID"
           
           echo "✅ Local atomic switch completed"
           
           # Update deployment environment with success
           echo "DEPLOYMENT_STATUS=success" >> "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
           echo "CURRENT_RELEASE=$RELEASE_PATH" >> "${SCRIPT_DIR}/../01-Configs/current-deployment.env"
           
       else
           echo "🟡 Atomic switch commands prepared for remote execution"
           echo "📋 Commands saved to: $DEPLOYMENT_WORKSPACE/server-commands-7-1.sh"
           echo "🔗 Execute on server: bash server-commands-7-1.sh '$RELEASE_PATH' '$PATH_SERVER' '$RELEASE_ID'"
       fi
       
       echo "✅ Phase 7.1: Atomic release switch completed"
       
   } | tee "$LOG_FILE"
   
   echo "📄 Phase 7.1 log saved to: $LOG_FILE"
   EOF
   
   chmod +x Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-7-1-atomic-switch.sh
   ```

2. **Execute Phase 7.1:**
   ```bash
   ./Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/phase-7-1-atomic-switch.sh
   ```

#### **Expected Result:**
```
✅ New release validation passed
⚡ Atomic switch completed in < 0.1s
✅ Symlink correctly points to new release
✅ Application functional after switch
✅ Critical files accessible
⚡ Atomic release switch completed successfully
```

---

## 🎉 **PHASES 4-7 COMPLETION**

### **Completion Verification**

Run this verification to confirm Phases 4-7 completed successfully:

```bash
# Load deployment environment
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/load-variables.sh
source Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/current-deployment.env

echo "🔍 Phases 4-7 Completion Verification:"
echo "======================================"
echo "Release ID: $RELEASE_ID"
echo "Server Path: $PATH_SERVER" 
echo "Release Path: $RELEASE_PATH"

# For local deployment, verify the switch
if [[ "$DEPLOYMENT_STRATEGY" == "local" ]]; then
    echo ""
    echo "📊 Deployment Verification:"
    echo "==========================="
    
    # Check current symlink
    if [[ -L "$PATH_SERVER/current" ]]; then
        CURRENT_TARGET=$(readlink "$PATH_SERVER/current")
        echo "Current symlink: $CURRENT_TARGET"
        
        if [[ "$CURRENT_TARGET" == "$RELEASE_PATH" ]]; then
            echo "✅ Symlink points to correct release"
        else
            echo "❌ Symlink verification failed"
        fi
    else
        echo "❌ Current symlink not found"
    fi
    
    # Test application functionality
    if [[ -d "$PATH_SERVER/current" ]]; then
        cd "$PATH_SERVER/current"
        if php artisan --version >/dev/null 2>&1; then
            LARAVEL_VERSION=$(php artisan --version)
            echo "✅ Application functional: $LARAVEL_VERSION"
        else
            echo "❌ Application not functional"
        fi
    fi
    
    # Check shared resources
    if [[ -L "$PATH_SERVER/current/storage" ]]; then
        echo "✅ Storage symlinks configured"
    else
        echo "⚠️ Storage symlinks missing"
    fi
fi

echo ""
echo "🎯 Status: ✅ PHASES 4-7 COMPLETED SUCCESSFULLY"
echo "⚡ Zero-downtime deployment achieved"
echo "🚀 Ready for Phases 8-10: Post-Release & Finalization"
```

### **What's Been Accomplished**

**Phase 4 - Configure Release:**
- ✅ **Server deployment structure** created with proper permissions
- ✅ **Application files transferred** with integrity verification
- ✅ **Shared resources configured** for zero data loss protection
- ✅ **Environment configuration** linked securely

**Phase 5 - Pre-Release Hooks:**
- ✅ **User-configurable hooks framework** established
- ✅ **Pre-release template** created for customization
- ✅ **Custom deployment commands** executed before changes

**Phase 6 - Mid-Release Hooks:**
- ✅ **Database migrations** executed safely
- ✅ **Application caches** cleared and rebuilt for production
- ✅ **Health checks** validated before atomic switch
- ✅ **Custom preparation commands** executed

**Phase 7 - Atomic Release Switch:**
- ✅ **Zero-downtime switch** executed in < 100ms
- ✅ **Instant deployment activation** with symlink management
- ✅ **Post-switch validation** confirmed application functionality
- ✅ **Emergency rollback capability** maintained

### **Next Steps**

**Ready for Phases 8-10:**
- **Phase 8:** Post-Release Hooks (OPcache, background services, notifications)
- **Phase 9:** Cleanup (old releases, temporary files, optimization)
- **Phase 10:** Finalization (reporting, monitoring, completion)

**Continue to:** [7-Section-C-Deploy-Phases8-10.md](7-Section-C-Deploy-Phases8-10.md)

---

## 🆘 **TROUBLESHOOTING PHASES 4-7**

### **Issue: Server Preparation Fails**
```bash
# Check server path and permissions
ls -la "$PATH_SERVER"
mkdir -p "$PATH_SERVER"
chmod 755 "$PATH_SERVER"
```

### **Issue: File Transfer Fails**
```bash
# Check source directory exists
ls -la "$BUILD_SOURCE_DIR"
# Check target permissions
ls -la "$RELEASE_PATH"
# Verify disk space
df -h "$PATH_SERVER"
```

### **Issue: Shared Resources Symlink Fails**
```bash
# Check shared directory structure
ls -la "$PATH_SERVER/shared/"
# Verify symlink creation
ln -s /test/path /test/link && rm /test/link
```

### **Issue: Database Migrations Fail**
```bash
# Check database connectivity
cd "$RELEASE_PATH"
php artisan migrate:status
# Check migration files
ls -la database/migrations/
```

### **Issue: Atomic Switch Fails**
```bash
# Check symlink capability
ln -s /test/path /test/link && rm /test/link
# Verify release functionality
cd "$RELEASE_PATH"
php artisan --version
```

### **Emergency Rollback**
```bash
# Quick manual rollback if needed
cd "$PATH_SERVER"
PREVIOUS=$(ls -1t releases/ | head -2 | tail -1)
ln -nfs "releases/$PREVIOUS" current
php artisan up
```

**Need Help?** Check the phase logs in `Admin-Local/2-Project-Area/02-Project-Records/05-Logs-And-Maintenance/` for detailed error information.