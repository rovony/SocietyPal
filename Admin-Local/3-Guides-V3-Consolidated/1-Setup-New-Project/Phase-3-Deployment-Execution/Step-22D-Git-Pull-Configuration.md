# Step 22D: Git Pull + Manual Build Upload Configuration (Scenario D: Hostinger/cPanel Workflow)

## **Analysis Source**

**Primary Source:** The-deployment-4-scenarios-i-want.md - Scenario D: Git Pull on Server + Manual Upload of Built Folders  
**Secondary Source:** V2 Phase3 organization approach  
**Recommendation:** Use 4-scenarios document Scenario D workflow with V2's structured organization

> **Purpose:** Configure Git-based deployment with manual build artifact upload for Hostinger/cPanel hosting environments

## **Critical Overview**

**🔄 GIT PULL + MANUAL BUILD WORKFLOW**

1. **Code Deployment:** Server pulls code from GitHub via Git
2. **Build Locally:** Create production assets on local machine
3. **Manual Upload:** Upload vendor/ and public/build/ via SFTP
4. **Server Finalization:** Link shared resources and finalize deployment

## **Scenario D Benefits**

### **✅ Advantages**

- **Familiar Workflow:** Standard Git operations on server
- **Hostinger Compatible:** Works with built-in Git features
- **Partial Automation:** Code via Git, builds via manual control
- **Cost Effective:** No external CI/CD service fees
- **Simple Debugging:** Easy to troubleshoot each step

### **⚠️ Considerations**

- Manual coordination between code and build artifacts
- Risk of version drift if not properly synchronized
- Requires SFTP access for build uploads
- Some manual steps in deployment process

## **Phase 1: Server Git Configuration**

### **1. Setup Git Repository on Server**

```bash
echo "🔧 Configuring Git Repository on Server"
echo "======================================="
echo ""
echo "Method 1: SSH Git Setup (Manual Control)"
echo "----------------------------------------"
echo ""
echo "Connect to server and setup repository:"
echo "  ssh hostinger-factolo"
echo "  cd ~/domains/societypal.com"
echo "  git clone https://github.com/[username]/societypal.git current"
echo "  cd current"
echo "  git checkout main"
echo ""
echo "Method 2: Hostinger Git Integration (Automatic)"
echo "-----------------------------------------------"
echo ""
echo "1. Login to Hostinger control panel"
echo "2. Navigate to Git section"
echo "3. Add repository:"
echo "   Repository URL: https://github.com/[username]/societypal.git"
echo "   Branch: main (for staging) or production"
echo "   Deploy Path: ~/domains/societypal.com/current"
echo "4. Configure automatic deployment triggers"
echo ""
echo "Choose your preferred method..."
read -p "Press Enter to continue with configuration..."
```

### **2. Create Server Directory Structure**

```bash
# Create comprehensive directory structure for releases
cat > scripts/setup_server_structure.sh << 'EOF'
#!/bin/bash

echo "🏗️ Setting up server directory structure for Scenario D..."

# Base paths
DOMAIN_PATH="~/domains/societypal.com"
STAGING_PATH="~/domains/staging.societypal.com"

echo "📁 Creating directory structure..."

# Production structure
mkdir -p $DOMAIN_PATH/{releases,shared,backup}
mkdir -p $DOMAIN_PATH/shared/{storage/{app,framework/{cache,sessions,views},logs},uploads}

# Staging structure (if different server paths)
mkdir -p $STAGING_PATH/{releases,shared,backup}
mkdir -p $STAGING_PATH/shared/{storage/{app,framework/{cache,sessions,views},logs},uploads}

echo "🔗 Setting up symbolic links..."

# Create initial shared directory links
cd $DOMAIN_PATH
if [ ! -f shared/.env ]; then
    echo "Creating default .env file..."
    cp current/.env.example shared/.env
    echo "⚠️ IMPORTANT: Configure shared/.env with production settings"
fi

# Set proper permissions
chmod -R 775 shared/storage
chmod 640 shared/.env

echo "✅ Server directory structure ready"
echo ""
echo "📋 Manual steps required:"
echo "  1. Configure shared/.env with production database settings"
echo "  2. Upload any existing storage files to shared/storage/"
echo "  3. Set up file permissions as needed"
echo "  4. Configure web server document root"
EOF

chmod +x scripts/setup_server_structure.sh

echo "✅ Server setup script created"
echo "📤 Upload and run this script on your server"
```

### **3. Configure Branch Strategy**

````bash
# Document branch strategy for Scenario D
cat > Admin-Local/server_deployment/configs/git_pull_branch_strategy.md << 'EOF'
# Git Pull Branch Strategy for Scenario D

## Branch Mapping

### Environment Configuration
- **Staging Environment**
  - Branch: `main`
  - Domain: staging.societypal.com
  - Auto-deploy: Yes (via Git webhook or manual pull)

- **Production Environment**
  - Branch: `production`
  - Domain: societypal.com
  - Auto-deploy: Manual pull only

## Git Operations on Server

### Staging Deployment
```bash
# On staging server
cd ~/domains/staging.societypal.com/current
git fetch origin
git checkout main
git pull origin main
echo "✅ Staging code updated"
````

### Production Deployment

```bash
# On production server
cd ~/domains/societypal.com/current
git fetch origin
git checkout production
git pull origin production
echo "✅ Production code updated"
```

## Hostinger Git Integration

### Auto-Deploy Configuration

1. **Repository Settings:**

   - URL: https://github.com/[username]/societypal.git
   - Branch: main (staging) or production
   - Deploy path: ~/domains/[domain]/current

2. **Webhook Triggers:**

   - Push to main → auto-deploy staging
   - Push to production → manual deploy (approval required)

3. **Deployment Actions:**
   - Pull latest code
   - Preserve shared directories
   - Run post-deployment scripts

## Manual Git Operations

### Check Current Status

```bash
cd ~/domains/societypal.com/current
git status
git log --oneline -5
git branch -a
```

### Update to Latest Code

```bash
git fetch --all
git checkout [target-branch]
git pull origin [target-branch]
```

### Rollback to Previous Version

```bash
git log --oneline -10
git checkout [previous-commit-hash]
# Or create rollback branch
git checkout -b rollback-$(date +%Y%m%d)
```

EOF

echo "✅ Git branch strategy documented"

````

## **Phase 2: Local Build Process**

### **1. Create Local Build Script**

```bash
# Create enhanced local build script for Scenario D
cat > scripts/build_for_server_upload.sh << 'EOF'
#!/bin/bash

echo "🏗️ Building Local Assets for Server Upload (Scenario D)"
echo "======================================================="

# Configuration
PROJECT_ROOT=$(pwd)
BUILD_DIR="build-output"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)

echo "📁 Project: $PROJECT_ROOT"
echo "⏰ Build timestamp: $TIMESTAMP"

# Clean previous builds
echo "🧹 Cleaning previous builds..."
rm -rf $BUILD_DIR
mkdir -p $BUILD_DIR

# Build PHP dependencies
echo "📦 Building PHP dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if [ $? -eq 0 ]; then
    echo "✅ Composer build successful"
    cp -r vendor $BUILD_DIR/
    echo "📊 Vendor size: $(du -sh $BUILD_DIR/vendor | cut -f1)"
else
    echo "❌ Composer build failed"
    exit 1
fi

# Build frontend assets
if [ -f "package.json" ]; then
    echo "🎨 Building frontend assets..."

    npm ci --only=production
    npm run build

    if [ -d "public/build" ]; then
        cp -r public/build $BUILD_DIR/
        echo "✅ Frontend build successful"
        echo "📊 Build size: $(du -sh $BUILD_DIR/build | cut -f1)"
    else
        echo "❌ Frontend build failed - no build directory"
        exit 1
    fi

    # Cleanup
    rm -rf node_modules

else
    echo "ℹ️ No frontend build required"
    mkdir -p $BUILD_DIR/build
fi

# Create deployment manifest
cat > $BUILD_DIR/deployment-manifest.txt << MANIFEST
Build Information for Scenario D
================================
Timestamp: $TIMESTAMP
Git Commit: $(git rev-parse --short HEAD)
Git Branch: $(git branch --show-current)
PHP Version: $(php --version | head -1)
Node Version: $(node --version 2>/dev/null || echo "N/A")

Build Contents:
- vendor/ (PHP dependencies)
- build/ (Frontend assets from public/build/)

Deployment Instructions:
1. Upload vendor/ to server, overwriting existing
2. Upload build/ to server's public/build/, overwriting existing
3. Run server finalization commands
4. Verify deployment successful

MANIFEST

# Create upload instructions
cat > $BUILD_DIR/UPLOAD_INSTRUCTIONS.md << 'INSTRUCTIONS'
# Upload Instructions for Scenario D

## 1. Upload Build Artifacts

### Via SFTP/FTP Client
````

Local Path: build-output/vendor/
Server Path: ~/domains/societypal.com/current/vendor/
Action: Upload and overwrite

Local Path: build-output/build/
Server Path: ~/domains/societypal.com/current/public/build/
Action: Upload and overwrite

````

### Via Command Line (if SSH available)
```bash
# Upload vendor directory
rsync -avz build-output/vendor/ hostinger-factolo:~/domains/societypal.com/current/vendor/

# Upload build directory
rsync -avz build-output/build/ hostinger-factolo:~/domains/societypal.com/current/public/build/
````

## 2. Server Finalization Commands

After uploading, run on server:

```bash
cd ~/domains/societypal.com/current

# Link shared resources
bash Admin-Local/server_deployment/scripts/link_persistent_dirs.sh

# Set permissions
chmod -R 775 storage bootstrap/cache

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify deployment
php artisan about
```

## 3. Verification Checklist

- [ ] vendor/ uploaded and contains all packages
- [ ] public/build/ uploaded and contains assets
- [ ] Shared resources linked correctly
- [ ] Database migrations completed
- [ ] Application cache rebuilt
- [ ] Site loads without errors
- [ ] Performance is acceptable

INSTRUCTIONS

echo ""
echo "✅ Local build completed successfully!"
echo ""
echo "📦 Build artifacts ready in: $BUILD_DIR/"
echo "📋 Upload instructions: $BUILD_DIR/UPLOAD_INSTRUCTIONS.md"
echo "📄 Build manifest: $BUILD_DIR/deployment-manifest.txt"
echo ""
echo "📊 Build Summary:"
echo " Vendor: $(du -sh $BUILD_DIR/vendor 2>/dev/null | cut -f1 || echo 'N/A')"
echo " Build: $(du -sh $BUILD_DIR/build 2>/dev/null | cut -f1 || echo 'N/A')"
echo " Total: $(du -sh $BUILD_DIR | cut -f1)"
echo ""
echo "🚀 Ready for upload to server!"

EOF

chmod +x scripts/build_for_server_upload.sh

echo "✅ Local build script created"

````

### **2. Test Local Build Process**

```bash
echo "🧪 Testing Local Build Process"
echo "============================="
echo ""
echo "Running local build script..."

# Execute build script
./scripts/build_for_server_upload.sh

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Local build test successful!"
    echo ""
    echo "📁 Build output directory: build-output/"
    echo "📋 Contents:"
    ls -la build-output/
    echo ""
    echo "📄 Review upload instructions:"
    echo "   cat build-output/UPLOAD_INSTRUCTIONS.md"
else
    echo "❌ Local build test failed"
    echo "💡 Check error messages and fix issues before proceeding"
fi
````

## **Phase 3: Server Integration Scripts**

### **1. Create Server Finalization Script**

```bash
# Create server-side finalization script
cat > Admin-Local/server_deployment/scripts/finalize_scenario_d_deployment.sh << 'EOF'
#!/bin/bash

echo "🔧 Finalizing Scenario D Deployment"
echo "==================================="

# Ensure we're in the correct directory
DOMAIN_PATH="$1"
if [ -z "$DOMAIN_PATH" ]; then
    DOMAIN_PATH="~/domains/societypal.com/current"
fi

cd "$DOMAIN_PATH" || {
    echo "❌ Cannot access deployment directory: $DOMAIN_PATH"
    exit 1
}

echo "📁 Working directory: $(pwd)"

# Verify build artifacts exist
echo "🔍 Verifying build artifacts..."

if [ ! -d "vendor" ]; then
    echo "❌ vendor/ directory missing - upload required"
    exit 1
fi

if [ ! -d "public/build" ]; then
    echo "⚠️ public/build/ directory missing - may be PHP-only project"
fi

echo "✅ Build artifacts verified"

# Link shared resources using existing script
echo "🔗 Linking shared resources..."
if [ -f "Admin-Local/server_deployment/scripts/link_persistent_dirs.sh" ]; then
    bash Admin-Local/server_deployment/scripts/link_persistent_dirs.sh "$(pwd)" "../shared"
    echo "✅ Shared resources linked"
else
    # Fallback manual linking
    echo "📄 Manual shared resource linking..."
    ln -nfs ../shared/.env .env
    rm -rf storage 2>/dev/null || true
    ln -nfs ../shared/storage storage
    echo "✅ Manual linking completed"
fi

# Set proper permissions
echo "🔒 Setting file permissions..."
find . -type f -exec chmod 644 {} \; 2>/dev/null || true
find . -type d -exec chmod 755 {} \; 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo "✅ Permissions set"

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction
if [ $? -eq 0 ]; then
    echo "✅ Migrations completed"
else
    echo "⚠️ Migration issues detected - check manually"
fi

# Rebuild application cache
echo "♻️ Rebuilding application cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo "✅ Cache rebuilt"

# Create storage link if needed
echo "🖼️ Setting up storage link..."
php artisan storage:link 2>/dev/null || echo "Storage link already exists"

# Deployment verification
echo "✅ Running deployment verification..."
php artisan about | head -10

echo ""
echo "🎉 Scenario D deployment finalization complete!"
echo ""
echo "📊 Deployment Summary:"
echo "   Domain: $(pwd)"
echo "   PHP Version: $(php --version | head -1)"
echo "   Laravel Version: $(php artisan --version)"
echo "   Environment: $(php artisan env)"
echo ""
echo "🔍 Verification steps:"
echo "   1. Check site loads: https://$(basename $(dirname $(pwd)))"
echo "   2. Verify database connectivity"
echo "   3. Test critical functionality"
echo "   4. Monitor error logs"

EOF

chmod +x Admin-Local/server_deployment/scripts/finalize_scenario_d_deployment.sh

echo "✅ Server finalization script created"
```

### **2. Create Upload Helper Scripts**

```bash
# Create SFTP upload helper
cat > scripts/upload_build_artifacts.sh << 'EOF'
#!/bin/bash

echo "📤 Upload Build Artifacts Helper (Scenario D)"
echo "============================================="

# Configuration
BUILD_DIR="build-output"
SERVER_HOST="${1:-hostinger-factolo}"
DOMAIN="${2:-societypal.com}"

if [ ! -d "$BUILD_DIR" ]; then
    echo "❌ Build directory not found: $BUILD_DIR"
    echo "💡 Run './scripts/build_for_server_upload.sh' first"
    exit 1
fi

echo "📊 Upload configuration:"
echo "   Build directory: $BUILD_DIR"
echo "   Server: $SERVER_HOST"
echo "   Domain: $DOMAIN"
echo ""

# Upload vendor directory
if [ -d "$BUILD_DIR/vendor" ]; then
    echo "📦 Uploading vendor directory..."
    rsync -avz --progress "$BUILD_DIR/vendor/" "$SERVER_HOST:~/domains/$DOMAIN/current/vendor/"

    if [ $? -eq 0 ]; then
        echo "✅ Vendor upload successful"
    else
        echo "❌ Vendor upload failed"
        exit 1
    fi
else
    echo "⚠️ No vendor directory to upload"
fi

# Upload build directory
if [ -d "$BUILD_DIR/build" ] && [ "$(ls -A $BUILD_DIR/build)" ]; then
    echo "🎨 Uploading build assets..."
    rsync -avz --progress "$BUILD_DIR/build/" "$SERVER_HOST:~/domains/$DOMAIN/current/public/build/"

    if [ $? -eq 0 ]; then
        echo "✅ Build assets upload successful"
    else
        echo "❌ Build assets upload failed"
        exit 1
    fi
else
    echo "ℹ️ No build assets to upload (PHP-only project)"
fi

# Run server finalization
echo "🔧 Running server finalization..."
ssh "$SERVER_HOST" "cd ~/domains/$DOMAIN/current && bash Admin-Local/server_deployment/scripts/finalize_scenario_d_deployment.sh"

if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 Scenario D deployment completed successfully!"
    echo ""
    echo "🌐 Site should be available at: https://$DOMAIN"
    echo "🔍 Verify deployment and test functionality"
else
    echo "❌ Server finalization failed"
    echo "💡 Check server logs and fix issues manually"
fi

EOF

chmod +x scripts/upload_build_artifacts.sh

echo "✅ Upload helper script created"
```

## **Phase 4: Complete Workflow Documentation**

### **1. Create Complete Workflow Guide**

````bash
# Document complete Scenario D workflow
cat > Admin-Local/server_deployment/configs/scenario_d_complete_workflow.md << 'EOF'
# Scenario D: Complete Workflow Guide

## Overview: Git Pull + Manual Build Upload

Scenario D provides a balance between automation and control, perfect for Hostinger/cPanel environments with Git integration.

## 🔄 Complete Deployment Workflow

### 1. Code Development and Push
```bash
# Work on feature
git checkout -b feature/new-feature
# ... development work ...
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Merge to main for staging
git checkout main
git merge feature/new-feature
git push origin main

# For production deployment
git checkout production
git merge main
git push origin production
````

### 2. Server Code Update

**Option A: Automatic (Hostinger Git)**

- Code automatically pulled on push
- Monitor Hostinger Git section for status

**Option B: Manual SSH**

```bash
ssh hostinger-factolo
cd ~/domains/societypal.com/current
git pull origin production
```

### 3. Local Build Process

```bash
# Run local build
./scripts/build_for_server_upload.sh

# Verify build output
ls -la build-output/
cat build-output/deployment-manifest.txt
```

### 4. Upload Build Artifacts

```bash
# Automated upload (recommended)
./scripts/upload_build_artifacts.sh hostinger-factolo societypal.com

# Manual upload via SFTP if needed
# Upload build-output/vendor/ to server vendor/
# Upload build-output/build/ to server public/build/
```

### 5. Server Finalization

```bash
# Automatic via upload script or manual via SSH
ssh hostinger-factolo
cd ~/domains/societypal.com/current
bash Admin-Local/server_deployment/scripts/finalize_scenario_d_deployment.sh
```

### 6. Verification

```bash
# Check site functionality
curl -I https://societypal.com
# Test critical features
# Monitor logs for errors
```

## 🎯 Best Practices

### Code and Build Synchronization

- Always build from the same commit that's deployed on server
- Use git tags for production releases
- Maintain build manifest for tracking

### Version Control

```bash
# Tag production releases
git tag -a v1.0.0 -m "Production release 1.0.0"
git push origin v1.0.0

# Deploy tagged release
git checkout v1.0.0
./scripts/build_for_server_upload.sh
./scripts/upload_build_artifacts.sh
```

### Rollback Strategy

```bash
# Server code rollback
ssh hostinger-factolo
cd ~/domains/societypal.com/current
git checkout [previous-tag-or-commit]

# Rebuild and reupload if needed
git checkout [previous-tag-or-commit]
./scripts/build_for_server_upload.sh
./scripts/upload_build_artifacts.sh
```

## 🔍 Troubleshooting

### Common Issues

- **Build/code version mismatch**: Always rebuild from deployed commit
- **Upload failures**: Check SSH keys and server permissions
- **Shared resource issues**: Verify link_persistent_dirs.sh script

### Verification Commands

```bash
# Check deployed code version
ssh hostinger-factolo "cd ~/domains/societypal.com/current && git log --oneline -1"

# Check build artifacts
ssh hostinger-factolo "ls -la ~/domains/societypal.com/current/vendor ~/domains/societypal.com/current/public/build"

# Check application status
ssh hostinger-factolo "cd ~/domains/societypal.com/current && php artisan about"
```

## 📊 Monitoring and Maintenance

### Regular Tasks

- Monitor Git deployment success
- Verify build artifact synchronization
- Check application performance
- Update dependencies as needed

### Automated Monitoring

- Set up uptime monitoring
- Monitor deployment logs
- Track build and upload times
- Alert on deployment failures

EOF

echo "✅ Complete workflow documentation created"

````

### **2. Create Deployment Checklist**

```bash
# Create comprehensive deployment checklist
cat > Admin-Local/server_deployment/configs/scenario_d_deployment_checklist.md << 'EOF'
# Scenario D Deployment Checklist

## Pre-Deployment Checklist

### Code Preparation
- [ ] All changes committed to Git
- [ ] Code pushed to appropriate branch (main/production)
- [ ] Git tags created for production releases
- [ ] Local environment tested and working

### Build Preparation
- [ ] Dependencies up to date (composer.lock, package-lock.json)
- [ ] Local build script tested successfully
- [ ] Build artifacts directory clean
- [ ] Upload scripts ready and tested

### Server Preparation
- [ ] Server Git repository updated
- [ ] Shared directories properly configured
- [ ] Database backup completed (production)
- [ ] Maintenance mode ready if needed

## Deployment Execution Checklist

### 1. Code Deployment
- [ ] Server pulls latest code (automatic or manual)
- [ ] Code version verified on server
- [ ] No Git conflicts or issues

### 2. Local Build Process
- [ ] Run `./scripts/build_for_server_upload.sh`
- [ ] Build completed without errors
- [ ] Build artifacts verified in build-output/
- [ ] Deployment manifest reviewed

### 3. Artifact Upload
- [ ] Run `./scripts/upload_build_artifacts.sh`
- [ ] Vendor directory uploaded successfully
- [ ] Build assets uploaded successfully
- [ ] Upload verification completed

### 4. Server Finalization
- [ ] Shared resources linked correctly
- [ ] File permissions set properly
- [ ] Database migrations executed
- [ ] Application cache rebuilt
- [ ] Storage links created

## Post-Deployment Verification

### Application Testing
- [ ] Site loads without errors (HTTP 200)
- [ ] Database connectivity verified
- [ ] Authentication system working
- [ ] Critical features functional
- [ ] Performance acceptable

### Technical Verification
- [ ] Error logs clear of critical issues
- [ ] Application version correct
- [ ] Environment configuration proper
- [ ] Cache and optimization active

### Monitoring Setup
- [ ] Uptime monitoring active
- [ ] Error tracking configured
- [ ] Performance monitoring enabled
- [ ] Backup verification completed

## Emergency Procedures

### Rollback Process
1. [ ] Identify rollback target (commit/tag)
2. [ ] Server code rollback via Git
3. [ ] Rebuild and reupload if needed
4. [ ] Run server finalization
5. [ ] Verify rollback successful

### Incident Response
- [ ] Team notification procedures
- [ ] Emergency contact information
- [ ] Escalation procedures
- [ ] Post-incident review process

EOF

echo "✅ Deployment checklist created"
````

## **Expected Result**

- ✅ Complete Scenario D configuration ready
- ✅ Git-based deployment with manual build control
- ✅ Local build scripts optimized for server upload
- ✅ Server integration scripts configured
- ✅ Upload automation and verification ready
- ✅ Comprehensive workflow documentation

## **Next Steps**

**Proceed to:** [Step 23A: Server-Side Deployment](Step-23A-Server-Deployment.md) (if using Scenario A)  
**Or continue with:** Scenario D deployment testing and optimization

## **Business Impact**

### **For Hosting Compatibility**

- Perfect integration with Hostinger Git features
- Compatible with cPanel and shared hosting
- No external service dependencies
- Cost-effective deployment solution

### **For Development Workflow**

- Familiar Git-based workflow
- Manual control over build artifacts
- Easy debugging and troubleshooting
- Flexible deployment timing

## **Troubleshooting**

### **Git Pull Issues**

```bash
# Check Git status on server
ssh hostinger-factolo "cd ~/domains/societypal.com/current && git status"

# Reset local changes if conflicts
ssh hostinger-factolo "cd ~/domains/societypal.com/current && git reset --hard origin/production"
```

### **Upload Problems**

```bash
# Test SSH connectivity
ssh -v hostinger-factolo

# Check server disk space
ssh hostinger-factolo "df -h"

# Verify paths exist
ssh hostinger-factolo "ls -la ~/domains/societypal.com/"
```

### **Build Synchronization**

```bash
# Verify local and server Git commits match
git log --oneline -1
ssh hostinger-factolo "cd ~/domains/societypal.com/current && git log --oneline -1"
```

---

**Scenario D configuration completed successfully!** 🔄
