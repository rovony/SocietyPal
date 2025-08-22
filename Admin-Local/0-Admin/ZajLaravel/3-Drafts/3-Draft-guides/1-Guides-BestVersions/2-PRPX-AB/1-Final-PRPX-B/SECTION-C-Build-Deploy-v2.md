# Master Checklist for **SECTION C: Build and Deploy**

**Version:** 2.0  
**Last Updated:** August 20, 2025  
**Purpose:** Execute universal zero-downtime Laravel deployment with comprehensive build strategies, deployment automation, and complete post-deployment validation

This section implements the actual deployment process with atomic operations, comprehensive validation, and complete rollback capabilities for universal Laravel applications.

---

## **Visual Identification System**
- 🟢 **Local Machine**: Developer workstation operations
- 🟡 **Builder VM**: Build server/CI environment operations
- 🔴 **Server**: Production server operations  
- 🟣 **User-Configurable**: SSH hooks and custom commands
  - 1️⃣ **Pre-release hooks** - Before atomic deployment switch
  - 2️⃣ **Mid-release hooks** - During deployment process
  - 3️⃣ **Post-release hooks** - After deployment completion
- 🏗️ **Builder Commands**: Build-specific operations

---

## **Path Variables System**
```bash
# Load deployment variables at start of each phase
source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh

# Standard path variables used throughout:
%path-localMachine%    # Developer local machine working directory
%path-server%          # Production server deployment directory  
%path-Builder-VM%      # Build server/VM working directory
%path-shared%          # Shared resources directory on server
```

---

## **Phase 1: 🖥️ Prepare Build Environment**

### Phase 1.1: [pre-build-env] - Pre-Build Environment Preparation
**Location:** 🟢 Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Initialize deployment workspace and validate repository connectivity  
**Action:**
1. Execute pre-build environment setup:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-1-1-pre-build-env.sh
   ```

2. Verify workspace initialization and repository connectivity

**Expected Result:**
```
✅ Deployment workspace initialized
🔗 Repository connectivity verified
📁 Build environment variables loaded
```

### Phase 1.2: [build-env-setup] - Build Environment Setup  
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Initialize clean build environment based on configured strategy  
**Action:**
1. Execute build environment setup:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-1-2-build-env-setup.sh
   ```

2. Verify build strategy configuration and environment versions

**Expected Result:**
```
✅ Build environment initialized for strategy: ${BUILD_STRATEGY}
🔧 Version alignment confirmed with production
📦 Build workspace prepared and configured
```

### Phase 1.3: [repo-preparation] - Repository Preparation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Clone repository and validate commit integrity  
**Action:**
1. Execute repository preparation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-1-3-repo-preparation.sh
   ```

2. Verify repository structure and commit integrity

**Expected Result:**
```
✅ Repository cloned and validated
📋 Target commit confirmed: ${TARGET_COMMIT}
🔧 Repository structure verified for Laravel deployment
```

---

## **Phase 2: 🏗️ Build Application**

### Phase 2.1: [cache-restoration] - Intelligent Cache Restoration  
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Restore cached dependencies with integrity validation  
**Action:**
1. Execute intelligent cache restoration:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-1-cache-restoration.sh
   ```

**Expected Result:**
```
✅ Dependency caches restored with hash validation
📦 Cache hit rate optimized for build speed
🔍 Cache integrity verified
```

### Phase 2.2: [universal-deps] - Universal Dependency Installation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Install dependencies with universal analysis and production optimization  
**Action:**
1. Execute universal dependency installation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-2-universal-deps.sh
   ```

**Expected Result:**
```
✅ Dependencies installed with universal analysis
🔧 Production optimization applied
📋 Security audit completed
🔍 Runtime compatibility verified
```

### Phase 2.3: [asset-compilation] - Advanced Asset Compilation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Compile frontend assets with auto-detection and optimization  
**Action:**
1. Execute advanced asset compilation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-3-asset-compilation.sh
   ```

**Expected Result:**
```
✅ Frontend assets compiled successfully
🎨 Asset bundler auto-detected: ${BUNDLER_TYPE}
📦 Production optimization applied
🧹 Development dependencies cleaned post-build
```

### Phase 2.4: [laravel-optimization] - Laravel Production Optimization
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Apply comprehensive Laravel optimizations  
**Action:**
1. Execute Laravel optimization:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-4-laravel-optimization.sh
   ```

**Expected Result:**
```
✅ Laravel caches optimized for production
⚡ Configuration, routes, and views cached
🔧 Autoloader optimized with classmap-authoritative
📋 Additional Laravel optimizations applied
```

### Phase 2.5: [build-validation] - Comprehensive Build Validation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Validate build integrity and Laravel functionality  
**Action:**
1. Execute comprehensive build validation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-5-build-validation.sh
   ```

**Expected Result:**
```
✅ Build validation passed - all critical files present
🧪 Laravel bootstrap functionality confirmed
📦 Build artifacts integrity verified
🎯 Build ready for deployment transfer
```

### Phase 2.6: [runtime-validation] - Runtime Dependency Validation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Final runtime compatibility and dependency validation  
**Action:**
1. Execute runtime validation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-2-6-runtime-validation.sh
   ```

**Expected Result:**
```
✅ Runtime dependency validation complete
🔧 Environment variables validated
💾 Cache systems confirmed functional
📊 Database connectivity potential verified
```

---

## **Phase 3: 📦 Package & Transfer**

### Phase 3.1: [artifact-preparation] - Smart Build Artifact Preparation
**Location:** 🟡 Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Create deployment manifest and optimized application artifacts  
**Action:**
1. Execute artifact preparation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-3-1-artifact-preparation.sh
   ```

**Expected Result:**
```
📦 Deployment package created with manifest
🔐 Checksums generated for integrity validation
📋 Build metadata documented
🗂️ Artifacts optimized for transfer
```

### Phase 3.2: [server-preparation] - Comprehensive Server Preparation
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Prepare zero-downtime deployment structure and backup current release  
**Action:**
1. Execute server preparation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-3-2-server-preparation.sh
   ```

**Expected Result:**
```
🔴 Server deployment structure prepared
💾 Current release backed up efficiently
🔗 Shared resources validated and ready
📁 Deployment permissions configured
```

### Phase 3.3: [release-directory] - Intelligent Release Directory Creation
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Create timestamped release directory with validation  
**Action:**
1. Execute release directory creation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-3-3-release-directory.sh
   ```

**Expected Result:**
```
📁 Release directory created: ${RELEASE_ID}
🔍 Pre-flight deployment checks passed
📊 Sufficient disk space confirmed
🔐 Release permissions properly configured
```

### Phase 3.4: [file-transfer] - Optimized File Transfer & Validation
**Location:** 🔴 Server  
**Path:** `%path-server%/releases/${RELEASE_ID}`  
**Purpose:** Transfer and validate build artifacts with integrity checks  
**Action:**
1. Execute optimized file transfer:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-3-4-file-transfer.sh
   ```

**Expected Result:**
```
📂 Build artifacts transferred successfully
✅ File integrity validated with checksums
🔐 Comprehensive permissions applied
📊 Transfer metrics: ${FILE_COUNT} files, ${RELEASE_SIZE}
```

---

## **Phase 4: 🔗 Configure Release**

### Phase 4.1: [shared-resources] - Advanced Shared Resources Configuration
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Configure comprehensive shared resources with smart detection  
**Action:**
1. Execute shared resources configuration:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-4-1-shared-resources.sh
   ```

**Expected Result:**
```
🔗 Shared directories linked with comprehensive coverage
📁 Smart user content detection applied
✅ All symlinks validated and functional
🔒 Proper permissions applied to shared resources
```

### Phase 4.2: [secure-configuration] - Secure Configuration Management
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Deploy and validate secure environment-specific configurations  
**Action:**
1. Execute secure configuration management:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-4-2-secure-configuration.sh
   ```

**Expected Result:**
```
🔐 Environment configuration validated and secured
🔑 APP_KEY confirmed or generated
🛡️ Production security settings applied
📋 Configuration completeness verified
```

---

## **Phase 5: 🚀 Pre-Release Hooks** 🟣 1️⃣

### Phase 5.1: [maintenance-mode] - Maintenance Mode (Optional)
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Enable maintenance mode with user-friendly page if configured  
**Action:**
1. Execute maintenance mode activation (if enabled):
   ```bash
   ./Admin-Local/Deployment/Scripts/user-hooks/pre-release-hooks.sh
   ```

**Expected Result:**
```
🚧 Maintenance mode enabled (if configured)
👥 User-friendly maintenance page displayed
🔑 Secret bypass configured for testing
```

### Phase 5.2: [pre-release-custom] - Pre-Release Custom Commands 🟣 1️⃣
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Execute user-defined pre-release scripts and preparations  
**Action:**
1. Execute custom pre-release commands:
   ```bash
   # User-configurable commands executed here
   ./Admin-Local/Deployment/Scripts/user-hooks/pre-release-hooks.sh
   ```

**User-Configurable Examples:**
- Database backups
- Cache warmup preparations  
- External service notifications
- Custom validation procedures

---

## **Phase 6: 🔄 Mid-Release Hooks** 🟣 2️⃣

### Phase 6.1: [zero-downtime-migrations] - Database Migrations (Zero-Downtime)
**Location:** 🔴 Server  
**Path:** `%path-server%/releases/${RELEASE_ID}`  
**Purpose:** Execute database migrations with zero-downtime patterns  
**Action:**
1. Execute zero-downtime migrations:
   ```bash
   ./Admin-Local/Deployment/Scripts/user-hooks/mid-release-hooks.sh
   ```

**Expected Result:**
```
🗃️ Database migrations executed safely
🔄 Backward compatibility maintained
📊 Migration status verified
🛡️ Zero-downtime patterns applied
```

### Phase 6.2: [cache-preparation] - Application Cache Preparation
**Location:** 🔴 Server  
**Path:** `%path-server%/releases/${RELEASE_ID}`  
**Purpose:** Prepare and pre-warm application caches  
**Action:**
1. Execute cache preparation:
   ```bash
   # Executed within mid-release-hooks.sh
   cd "$RELEASE_PATH"
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

**Expected Result:**
```
💾 Application caches prepared
⚡ Configuration optimized for first requests
🔧 Cache systems validated
```

### Phase 6.3: [health-checks] - Pre-Switch Health Checks
**Location:** 🔴 Server  
**Path:** `%path-server%/releases/${RELEASE_ID}`  
**Purpose:** Verify application readiness before atomic switch  
**Action:**
1. Execute comprehensive health checks:
   ```bash
   # Health checks within mid-release-hooks.sh
   php artisan --version
   php artisan migrate:status
   # Custom health validation commands
   ```

**Expected Result:**
```
🏥 Application health verified
✅ Database connectivity confirmed
🔧 Critical services validated
🎯 Ready for atomic release switch
```

---

## **Phase 7: ⚡ Atomic Release Switch**

### Phase 7.1: [atomic-switch] - Atomic Symlink Update
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Execute instant atomic deployment switch with rollback preparation  
**Action:**
1. Execute atomic release switch:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-7-1-atomic-switch.sh
   ```

**Expected Result:**
```
⚡ Atomic deployment switch completed instantly
📋 Old release preserved for rollback: ${OLD_RELEASE}
🔗 New release active: releases/${RELEASE_ID}
🌐 Public access updated via symlinks
```

---

## **Phase 8: 🎯 Post-Release Hooks** 🟣 3️⃣

### Phase 8.1: [opcache-management] - Advanced OPcache Management
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Clear OPcache with 3-tier fallback strategy  
**Action:**
1. Execute advanced OPcache clearing:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-8-1-opcache-management.sh
   ```

**Expected Result:**
```
🔄 OPcache cleared with 3-tier strategy
✅ Cache invalidation verified
🌐 New code active across all processes
🔧 Fallback methods applied for shared hosting
```

### Phase 8.2: [background-services] - Background Services Management
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Restart queue workers and background services  
**Action:**
1. Execute background services restart:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-8-2-background-services.sh
   ```

**Expected Result:**
```
🔄 Queue workers gracefully restarted
🎯 Horizon processes updated (if applicable)
⚙️ Supervisor configurations refreshed
✅ Background services using new codebase
```

### Phase 8.3: [post-deployment-validation] - Post-Deployment Validation
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Comprehensive validation of successful deployment  
**Action:**
1. Execute post-deployment validation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-8-3-post-deployment-validation.sh
   ```

**Expected Result:**
```
🏥 Application health check: PASSED
🌐 HTTP status validation: 200 OK
🗃️ Database connectivity: CONFIRMED
⚡ Critical functionality: VALIDATED
```

### Phase 8.4: [exit-maintenance] - Exit Maintenance Mode
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Restore full application access  
**Action:**
1. Execute maintenance mode exit:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-8-4-exit-maintenance.sh
   ```

**Expected Result:**
```
🌐 Maintenance mode disabled
👥 Full user access restored
✅ Application fully operational
```

### Phase 8.5: [user-post-release] - User Post-Release Commands 🟣 3️⃣
**Location:** 🔴 Server  
**Path:** `%path-server%/current`  
**Purpose:** Execute user-defined post-release commands  
**Action:**
1. Execute user post-release hooks:
   ```bash
   ./Admin-Local/Deployment/Scripts/user-hooks/post-release-hooks.sh
   ```

**User-Configurable Examples:**
- Performance monitoring activation
- External service notifications
- Custom validation procedures
- Analytics and reporting setup

---

## **Phase 9: 🧹 Cleanup**

### Phase 9.1: [releases-cleanup] - Old Releases Cleanup
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Clean up old releases while maintaining rollback capability  
**Action:**
1. Execute releases cleanup:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-9-1-releases-cleanup.sh
   ```

**Expected Result:**
```
🧹 Old releases cleaned (keeping last ${KEEP_RELEASES})
💾 Rollback capability maintained
📊 Disk space optimized: ${SPACE_FREED} freed
```

### Phase 9.2: [cache-cleanup] - Cache and Temporary Files Cleanup
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Clean temporary files and optimize cache storage  
**Action:**
1. Execute cache cleanup:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-9-2-cache-cleanup.sh
   ```

**Expected Result:**
```
🧹 Temporary files cleaned
💾 Cache storage optimized
📁 Build artifacts cleaned from builder/local
🗂️ Backup retention policy applied
```

### Phase 9.3: [build-cleanup] - Build Environment Cleanup
**Location:** 🟡 Builder VM / 🟢 Local Machine  
**Path:** `%path-Builder-VM%` or `%path-localMachine%`  
**Purpose:** Clean build artifacts and temporary files  
**Action:**
1. Execute build environment cleanup:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-9-3-build-cleanup.sh
   ```

**Expected Result:**
```
🧹 Build environment cleaned
📦 Temporary artifacts removed
💾 Cache preserved for next build
📊 Build resources optimized
```

---

## **Phase 10: 📋 Finalization**

### Phase 10.1: [deployment-logging] - Comprehensive Deployment Logging
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Document deployment with comprehensive audit trail  
**Action:**
1. Execute deployment logging:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-10-1-deployment-logging.sh
   ```

**Expected Result:**
```
📝 Deployment audit trail completed
📊 Performance metrics recorded
🕐 Deployment duration: ${DEPLOYMENT_TIME}
📋 Comprehensive deployment report generated
```

### Phase 10.2: [notifications] - Deployment Notifications
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Send deployment success notifications and reports  
**Action:**
1. Execute notification system:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-10-2-notifications.sh
   ```

**Expected Result:**
```
📨 Deployment notifications sent
👥 Team notified of successful deployment
📊 Deployment metrics shared
🎉 Success confirmation distributed
```

### Phase 10.3: [monitoring-activation] - Monitoring and Alerting Activation
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Activate monitoring systems and health checks  
**Action:**
1. Execute monitoring activation:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-10-3-monitoring-activation.sh
   ```

**Expected Result:**
```
📊 Application monitoring activated
🚨 Alert systems configured for new release
📈 Performance baseline established
🔍 Health monitoring systems active
```

### Phase 10.4: [deployment-completion] - Final Deployment Completion
**Location:** 🔴 Server  
**Path:** `%path-server%`  
**Purpose:** Finalize deployment with success confirmation and next steps  
**Action:**
1. Execute deployment completion:
   ```bash
   ./Admin-Local/Deployment/Scripts/phase-10-4-deployment-completion.sh
   ```

**Expected Result:**
```
🎉 DEPLOYMENT COMPLETED SUCCESSFULLY
✅ Zero-downtime deployment achieved
⚡ Total deployment time: ${TOTAL_TIME}
🚀 Application fully operational with new release
📋 Post-deployment verification complete
```

---

## **Emergency Rollback Procedures**

### Automatic Rollback Triggers
- Health check failure after deployment
- HTTP status code errors (5xx responses)
- Database connection failures
- Critical application errors

### Manual Rollback Execution
```bash
# Quick rollback to previous release
./Admin-Local/Deployment/Scripts/emergency-rollback.sh

# Or manual rollback commands:
cd %path-server%
PREVIOUS=$(ls -1t releases/ | head -2 | tail -1)
ln -nfs "releases/$PREVIOUS" current
php artisan up
./Admin-Local/Deployment/Scripts/phase-8-1-opcache-management.sh
systemctl reload nginx
```

---

## **Verification Commands**

### Post-Deployment Validation
```bash
# Verify deployment success
curl -I https://your-domain.com
php artisan --version
php artisan queue:work --timeout=1 --tries=1
tail -f storage/logs/laravel.log

# Check deployment status
ls -la %path-server%/current
ls -la %path-server%/releases/
df -h %path-server%
```

### Health Check Endpoint Setup
Create a health check endpoint in your Laravel application:
```php
// routes/web.php or routes/api.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => config('app.version'),
        'environment' => app()->environment(),
    ]);
});
```

---

**🎯 Deployment Success Criteria:**
- ✅ Zero downtime achieved (< 100ms interruption)
- ✅ All functionality preserved
- ✅ User data completely protected  
- ✅ Performance maintained or improved
- ✅ Rollback capability confirmed
- ✅ Monitoring systems active

**Total Estimated Time:** 5-15 minutes depending on application size and build complexity

**Next Steps:** Monitor application performance and user feedback for 24-48 hours post-deployment