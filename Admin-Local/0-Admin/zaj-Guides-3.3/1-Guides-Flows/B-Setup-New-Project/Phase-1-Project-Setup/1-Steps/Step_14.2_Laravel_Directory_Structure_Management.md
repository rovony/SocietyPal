# Step 14.2: Laravel Directory Structure Management

## 🎯 **Purpose**
This step ensures that all Laravel applications have the correct directory structure for both local development and production deployment, using a hybrid approach that combines repository tracking with build-time verification.

## 🔍 **The Problem**
Laravel requires specific directories to exist for proper operation:
- `bootstrap/cache/` - Framework cache files
- `storage/framework/cache/data/` - Application cache
- `storage/framework/sessions/` - User sessions
- `storage/framework/views/` - Compiled Blade templates
- `storage/logs/` - Application logs
- `storage/app/public/` - Public file storage
- `public/storage/` - Public storage symlink

**Git Limitation**: Git cannot track empty directories, so these critical folders are missing from fresh clones.

## 🚀 **The Solution: Hybrid Approach**

### **Phase 1: Repository Setup (.gitkeep files)**
Create `.gitkeep` files in all required directories to ensure they exist in the repository:

```bash
# Create directory structure
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/clockwork
mkdir -p storage/debugbar
mkdir -p bootstrap/cache
mkdir -p public/storage

# Add .gitkeep files to preserve directories
touch storage/app/public/.gitkeep
touch storage/framework/cache/data/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/testing/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/logs/.gitkeep
touch storage/clockwork/.gitkeep
touch storage/debugbar/.gitkeep
touch bootstrap/cache/.gitkeep
touch public/storage/.gitkeep
```

### **Phase 2: Build-Time Verification (DeployHQ Commands)**
Use build commands that verify and fix directory structure during deployment:

```bash
#!/bin/bash
set -e

echo "=== Creating Laravel Directory Structure ==="

# Create ALL required Laravel directories (covers all Laravel versions 8-12)
echo "Creating Laravel directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/clockwork
mkdir -p storage/debugbar
mkdir -p public/storage

# Set proper permissions for all directories
echo "Setting directory permissions..."
chmod -R 755 bootstrap/cache
chmod -R 755 storage
find storage -type d -exec chmod 775 {} \;

# Create .gitkeep files for empty directories
echo "Creating .gitkeep files..."
find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true
touch bootstrap/cache/.gitkeep 2>/dev/null || true

# Verify directory structure
echo "Verifying directory structure..."
echo "✅ bootstrap/cache: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")"
echo "✅ storage/app: $([ -d "storage/app" ] && echo "exists" || echo "missing")"
echo "✅ storage/framework: $([ -d "storage/framework" ] && echo "exists" || echo "missing")"
echo "✅ storage/logs: $([ -d "storage/logs" ] && echo "exists" || echo "missing")"

echo "✅ Laravel directory structure created"
```

## 🌍 **Universal Application: Works for ALL Laravel Apps**

### **Laravel Version Compatibility**
This approach works universally across all Laravel versions:

- **Laravel 8-9**: Standard directory structure
- **Laravel 10**: Enhanced caching and storage
- **Laravel 11**: Modern framework with additional directories
- **Laravel 12**: Latest version with optimized structure

### **Application Types Supported**
- **Web Applications**: Full-stack Laravel apps
- **API Applications**: REST/GraphQL APIs
- **Microservices**: Individual Laravel services
- **Headless CMS**: Content management systems
- **E-commerce**: Shopping cart applications
- **Admin Panels**: Backend management systems

### **Deployment Methods Compatible**
- **DeployHQ**: Professional deployment service
- **GitHub Actions**: CI/CD automation
- **GitLab CI**: GitLab pipelines
- **Jenkins**: Enterprise CI/CD
- **Manual Deployment**: SSH/SCP deployments
- **Docker**: Containerized deployments

## 🔧 **Implementation Steps for Any Laravel App**

### **Step 1: Initial Setup (One-time)**
```bash
# Clone or create new Laravel project
git clone <repository> my-laravel-app
cd my-laravel-app

# Run the directory creation script
./scripts/setup-laravel-directories.sh
```

### **Step 2: DeployHQ Configuration**
1. **Build Commands**: Add the directory creation script as Build Command 1
2. **Runtime**: PHP 8.2+ (or your app's PHP version)
3. **Order**: Must run before `composer install`

### **Step 3: Repository Management**
```bash
# Commit the directory structure
git add storage bootstrap/cache public/storage
git commit -m "feat: add Laravel runtime directory structure"
git push origin main
```

## 🎯 **Benefits of This Approach**

### **For Developers**
- ✅ **Immediate Setup**: Fresh clones work out-of-the-box
- ✅ **Consistent Environment**: Same structure across all machines
- ✅ **No Manual Creation**: Eliminates "mkdir" commands in documentation

### **For Deployment**
- ✅ **Build Reliability**: No more "bootstrap/cache must be writable" errors
- ✅ **Permission Consistency**: Proper 755/775 permissions every time
- ✅ **Zero Downtime**: Atomic deployments with correct structure

### **For Maintenance**
- ✅ **Self-Healing**: Build commands fix any directory issues
- ✅ **Version Agnostic**: Works with Laravel updates
- ✅ **Universal**: Same approach for all projects

## 🚨 **Common Issues & Solutions**

### **Issue 1: "bootstrap/cache must be writable"**
**Cause**: Directory doesn't exist or wrong permissions
**Solution**: Build Command 1 creates and sets permissions

### **Issue 2: "storage/logs directory not found"**
**Cause**: Missing storage subdirectories
**Solution**: .gitkeep files ensure directories exist in repository

### **Issue 3: "public/storage symlink failed"**
**Cause**: Missing public/storage directory
**Solution**: Directory created during build, symlinked during SSH commands

## 🔄 **Maintenance & Updates**

### **Adding New Directories**
When Laravel adds new required directories in future versions:

1. **Update Build Command**: Add new `mkdir -p` commands
2. **Add .gitkeep**: Include new directories in repository
3. **Test**: Verify on staging environment first

### **Permission Changes**
If Laravel requires different permissions:

1. **Update Build Command**: Modify `chmod` commands
2. **Test Permissions**: Verify on target server
3. **Document**: Update this guide

## 📝 **Best Practices**

1. **Always Use .gitkeep**: Ensures repository completeness
2. **Keep Build Commands**: Provides deployment safety net
3. **Test Locally First**: Verify directory creation works
4. **Monitor Deployments**: Check build logs for directory creation
5. **Document Changes**: Update this guide for team members

## 🎯 **Success Criteria**

✅ **Local Development**: `git clone` → `composer install` → working app
✅ **Deployment**: Build commands complete without directory errors
✅ **Permissions**: All directories have correct 755/775 permissions
✅ **Persistence**: User data survives deployments via shared symlinks
✅ **Scalability**: Approach works for any Laravel project size

## 🔗 **Related Steps**

- **Step 14**: Run Local Installation
- **Step 14.1**: Create .deployignore for DeployHQ
- **Step 06**: Universal GitIgnore
- **Step 11**: Create Environment Files
- **DeployHQ Guide**: Build Commands & SSH Commands

---

## 📚 **References**

- [Laravel Directory Structure Documentation](https://laravel.com/docs/structure)
- [DeployHQ Build Commands](https://www.deployhq.com/support/build-commands)
- [Git .gitkeep Best Practices](https://git-scm.com/docs/gitignore)
- [Laravel Deployment Best Practices](https://laravel.com/docs/deployment)

---

## 🔍 **Key Insight: mkdir -p is Idempotent**

**Important**: The `mkdir -p` command in your build scripts is **idempotent** - if directories already exist, it does nothing. This means:

- **First deployment**: Creates all directories from scratch
- **Subsequent deployments**: Skips existing directories, only sets permissions
- **No duplication**: Never creates duplicate directories
- **Always safe**: Can run multiple times without issues

This is why the hybrid approach works perfectly - the build commands are both safe and necessary.

---

*This step ensures your Laravel application has a robust, maintainable directory structure that works consistently across all environments and deployment methods.*

