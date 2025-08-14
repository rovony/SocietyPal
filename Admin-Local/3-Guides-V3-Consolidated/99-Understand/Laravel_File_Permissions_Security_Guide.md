# Laravel File Permissions Security Guide

**Complete guide to Laravel file permissions for local development, production, and CodeCanyon applications**

> 🎯 **Purpose:** Understand when to use different permission levels for security vs functionality
>
> 🔒 **Security Priority:** Production security vs development convenience vs installation requirements

---

## 📋 **Complete Quick Reference Table**

| Directory/File Type           | Local Dev | Production | During Install | After Install | Purpose                        |
| ----------------------------- | --------- | ---------- | -------------- | ------------- | ------------------------------ |
| **Core Writable Directories** |
| `storage/`                    | `775`     | `755`      | `777`          | `755`         | Logs, cache, sessions, uploads |
| `bootstrap/cache/`            | `775`     | `755`      | `777`          | `755`         | Framework cache files          |
| `public/user-uploads/`        | `775`     | `755`      | `777`          | `755`         | User uploaded files            |
| `public/uploads/`             | `775`     | `755`      | `777`          | `755`         | Application uploads            |
| **Security-Critical Files**   |
| `.env` `.env.*`               | `600`     | `600`      | `600`          | `600`         | Environment configuration      |
| `config/database.php`         | `600`     | `600`      | `600`          | `600`         | Database credentials           |
| **Application Directories**   |
| `app/`                        | `755`     | `755`      | `755`          | `755`         | Application code               |
| `config/`                     | `755`     | `755`      | `777`\*        | `755`         | Configuration files            |
| `routes/`                     | `755`     | `755`      | `755`          | `755`         | Route definitions              |
| `resources/`                  | `755`     | `755`      | `755`          | `755`         | Views, assets, lang            |
| `database/`                   | `755`     | `755`      | `755`          | `755`         | Migrations, seeds              |
| `tests/`                      | `755`     | `755`      | `755`          | `755`         | Test files                     |
| **Application Files**         |
| `*.php` (app code)            | `644`     | `644`      | `644`          | `644`         | PHP source files               |
| `composer.json/lock`          | `644`     | `644`      | `644`          | `644`         | Dependency definitions         |
| `package.json/lock`           | `644`     | `644`      | `644`          | `644`         | Frontend dependencies          |
| **Executable Files**          |
| `artisan`                     | `755`     | `755`      | `755`          | `755`         | Laravel CLI tool               |
| **Public Web Files**          |
| `public/*.php`                | `644`     | `644`      | `644`          | `644`         | Web entry points               |
| `public/js/css/`              | `644`     | `644`      | `644`          | `644`         | Static assets                  |
| **Vendor Dependencies**       |
| `vendor/`                     | `755`     | `755`      | `755`          | `755`         | Composer packages              |
| `node_modules/`               | `755`     | `755`      | `755`          | `755`         | NPM packages                   |

**Notes:**

-   `*` = Some CodeCanyon installers modify config files during installation
-   `777` permissions should NEVER be permanent - only during installation/updates

---

## 🎯 **Core Principles**

### **Security vs Functionality Balance**

-   **Development:** Convenience and debugging capability
-   **Production:** Maximum security with functional requirements
-   **Installation:** Temporary elevated permissions for setup processes

### **File Permission Basics**

-   **`644`** = Owner: read/write, Group: read, Others: read (standard files)
-   **`755`** = Owner: read/write/execute, Group: read/execute, Others: read/execute (directories & executables)
-   **`775`** = Owner: read/write/execute, Group: read/write/execute, Others: read/execute (shared write access)
-   **`777`** = Everyone: read/write/execute (⚠️ **DANGEROUS** - use only temporarily)
-   **`600`** = Owner: read/write only (private files like .env)

---

## 🏠 **Local Development Environment**

### **Complete Local Development Permissions**

```bash
# ===============================================
# COMPLETE LARAVEL APP PERMISSIONS - LOCAL DEV
# ===============================================

# 1. Core Laravel writable directories
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/user-uploads/
chmod -R 775 public/uploads/

# 2. Application directories (standard)
chmod -R 755 app/
chmod -R 755 config/
chmod -R 755 routes/
chmod -R 755 resources/
chmod -R 755 database/
chmod -R 755 tests/
chmod -R 755 public/
chmod -R 755 vendor/
chmod -R 755 node_modules/

# 3. All application files (PHP, JSON, etc.)
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.json" -exec chmod 644 {} \;
find . -type f -name "*.js" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type f -name "*.md" -exec chmod 644 {} \;
find . -type f -name "*.txt" -exec chmod 644 {} \;
find . -type f -name "*.yml" -exec chmod 644 {} \;
find . -type f -name "*.yaml" -exec chmod 644 {} \;

# 4. Secure sensitive files
chmod 600 .env*
chmod 600 config/database.php

# 5. Make executable files executable
chmod +x artisan
find . -name "*.sh" -exec chmod +x {} \;

# 6. Set proper ownership (macOS/Herd)
chown -R $(whoami):staff storage/ bootstrap/cache/
[ -d "public/user-uploads" ] && chown -R $(whoami):staff public/user-uploads/
[ -d "public/uploads" ] && chown -R $(whoami):staff public/uploads/
```

### **Why These Permissions for Local Dev:**

-   **`775` for storage/cache:** Allows web server and CLI to write freely
-   **Herd runs under your user:** No permission conflicts
-   **Development priority:** Functionality over strict security
-   **Debugging access:** You can easily read/modify all files

---

## 🌐 **Production Environment**

### **Complete Production Security Permissions**

```bash
# ===============================================
# COMPLETE LARAVEL APP PERMISSIONS - PRODUCTION
# ===============================================

# 1. Core Laravel writable directories (more restrictive)
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/user-uploads/
chmod -R 755 public/uploads/

# 2. Application directories (read-only for security)
chmod -R 755 app/
chmod -R 755 config/
chmod -R 755 routes/
chmod -R 755 resources/
chmod -R 755 database/
chmod -R 755 tests/
chmod -R 755 public/
chmod -R 755 vendor/
chmod -R 755 node_modules/

# 3. All application files (read-only)
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.json" -exec chmod 644 {} \;
find . -type f -name "*.js" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type f -name "*.md" -exec chmod 644 {} \;
find . -type f -name "*.txt" -exec chmod 644 {} \;
find . -type f -name "*.yml" -exec chmod 644 {} \;
find . -type f -name "*.yaml" -exec chmod 644 {} \;

# 4. Highly secure sensitive files
chmod 600 .env
chmod 600 config/database.php

# 5. Executable files only where needed
chmod +x artisan
find . -name "*.sh" -exec chmod +x {} \;

# 6. Remove execute permissions from public PHP files (security)
find public/ -name "*.php" -exec chmod 644 {} \;
```

### **Production Ownership (Critical)**

```bash
# Set proper ownership (adjust user/group to your server setup)
chown -R www-data:www-data storage/ bootstrap/cache/
chown -R www-data:www-data public/user-uploads/
chown -R www-data:www-data public/uploads/

# Application files owned by deploy user, readable by web server
chown -R deploy-user:www-data app/ config/ routes/ resources/
```

### **Why More Restrictive in Production:**

-   **`755` instead of `775`:** Removes group write access (security)
-   **Proper ownership:** Web server can read/write only what it needs
-   **Attack surface reduction:** Minimal permissions reduce security risks
-   **Compliance:** Meets security audit requirements

---

## �️ **Frontend GUI Installation & Updates (All Environments)**

**This section covers CodeCanyon frontend installations and updates performed through the web interface on local, staging, and production environments.**

### **Universal Pre-Installation Permissions (Any Environment)**

**⚠️ IMPORTANT: Run these commands BEFORE starting any frontend installation or update process:**

```bash
# ===============================================
# PRE-INSTALLATION TEMPORARY PERMISSIONS
# ===============================================

echo "�🛠️ Setting temporary permissions for frontend installation..."
echo "⚠️ These will be REVERTED immediately after installation"

# 1. Core writable directories - TEMPORARY 777
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 public/user-uploads/
chmod -R 777 public/uploads/

# 2. Configuration directories (some installers modify these)
chmod -R 777 config/

# 3. Public directory (for asset generation)
chmod -R 777 public/

# 4. Keep sensitive files secure even during installation
chmod 600 .env*
chmod 600 config/database.php

# 5. Verify permissions applied
echo "✅ Temporary installation permissions applied:"
ls -la storage/ | head -3
ls -la bootstrap/cache/ | head -3
ls -la config/ | head -3

echo ""
echo "🎯 Ready for frontend installation/update"
echo "⚠️ REMEMBER: Run post-installation script immediately after completion!"
```

### **Frontend Installation Process (Web Interface)**

**🏷️ Tag Instruct-User 👤** Web Interface Steps:

1. **Open your application** in browser (e.g., `https://yourdomain.com/install`)
2. **Follow installation wizard** - fill out database configuration, admin details, etc.
3. **Complete all installation steps** until you see "Installation Complete" or similar message
4. **IMMEDIATELY run post-installation script** (see next section)

### **Immediate Post-Installation Security Lockdown**

**🚨 CRITICAL: Run this IMMEDIATELY after frontend installation completes:**

```bash
# ===============================================
# POST-INSTALLATION SECURITY LOCKDOWN
# ===============================================

echo "🔒 IMMEDIATE SECURITY LOCKDOWN - Post Installation"
echo "📅 $(date)"

# 1. Determine environment and set appropriate permissions
if [[ -f ".env" && $(grep "APP_ENV=local" .env) ]]; then
    ENV_TYPE="local"
    PERM_STORAGE="775"
    PERM_CACHE="775"
    PERM_UPLOADS="775"
    echo "🏠 Detected: LOCAL environment"
elif [[ -f ".env" && $(grep "APP_ENV=staging" .env) ]]; then
    ENV_TYPE="staging"
    PERM_STORAGE="755"
    PERM_CACHE="755"
    PERM_UPLOADS="755"
    echo "🌐 Detected: STAGING environment"
elif [[ -f ".env" && $(grep "APP_ENV=production" .env) ]]; then
    ENV_TYPE="production"
    PERM_STORAGE="755"
    PERM_CACHE="755"
    PERM_UPLOADS="755"
    echo "🚀 Detected: PRODUCTION environment"
else
    # Default to production security
    ENV_TYPE="production"
    PERM_STORAGE="755"
    PERM_CACHE="755"
    PERM_UPLOADS="755"
    echo "⚠️ Unknown environment - defaulting to PRODUCTION security"
fi

# 2. Revert core directories to appropriate permissions
echo "🔧 Setting $ENV_TYPE permissions..."
chmod -R $PERM_STORAGE storage/
chmod -R $PERM_CACHE bootstrap/cache/
chmod -R $PERM_UPLOADS public/user-uploads/
chmod -R $PERM_UPLOADS public/uploads/

# 3. Secure configuration directories
chmod -R 755 config/
find config/ -type f -exec chmod 644 {} \;

# 4. Secure public directory
chmod -R 755 public/
find public/ -type f -name "*.php" -exec chmod 644 {} \;
find public/ -type f -name "*.js" -exec chmod 644 {} \;
find public/ -type f -name "*.css" -exec chmod 644 {} \;

# 5. Ensure sensitive files remain secure
chmod 600 .env*
chmod 600 config/database.php

# 6. Set proper ownership (adjust for your server)
if [[ "$ENV_TYPE" == "local" ]]; then
    # Local development (Herd/macOS)
    chown -R $(whoami):staff storage/ bootstrap/cache/ public/user-uploads/ public/uploads/
else
    # Production/Staging (adjust user:group as needed)
    echo "⚠️ MANUAL ACTION REQUIRED: Set proper ownership for $ENV_TYPE"
    echo "Example: chown -R www-data:www-data storage/ bootstrap/cache/ public/user-uploads/ public/uploads/"
fi

# 7. Verification
echo ""
echo "✅ POST-INSTALLATION SECURITY VERIFICATION:"
echo "Environment: $ENV_TYPE"
echo "Storage permissions: $(ls -ld storage/ | awk '{print $1}')"
echo "Cache permissions: $(ls -ld bootstrap/cache/ | awk '{print $1}')"
echo "Config permissions: $(ls -ld config/ | awk '{print $1}')"
echo "Env file permissions: $(ls -l .env | awk '{print $1}')"

# 8. Security audit
echo ""
echo "🔍 SECURITY AUDIT:"
DANGEROUS_FILES=$(find . -type f -perm 777 2>/dev/null | wc -l)
if [[ $DANGEROUS_FILES -gt 0 ]]; then
    echo "❌ WARNING: Found $DANGEROUS_FILES files with 777 permissions"
    find . -type f -perm 777 -ls
else
    echo "✅ No files with dangerous 777 permissions found"
fi

echo ""
echo "🎯 INSTALLATION SECURITY LOCKDOWN COMPLETE"
echo "🔒 Your application is now secured with $ENV_TYPE-appropriate permissions"
```

### **Quick Environment-Specific Scripts**

#### **Local Development (Post-Install)**

```bash
#!/bin/bash
# scripts/post-install-local.sh
chmod -R 775 storage/ bootstrap/cache/ public/user-uploads/ public/uploads/
chmod -R 755 config/ && find config/ -type f -exec chmod 644 {} \;
chmod 600 .env*
echo "✅ Local development permissions restored"
```

#### **Production/Staging (Post-Install)**

```bash
#!/bin/bash
# scripts/post-install-production.sh
chmod -R 755 storage/ bootstrap/cache/ public/user-uploads/ public/uploads/
chmod -R 755 config/ && find config/ -type f -exec chmod 644 {} \;
chmod 600 .env*
chown -R www-data:www-data storage/ bootstrap/cache/ public/user-uploads/ public/uploads/
echo "✅ Production security permissions restored"
```

### **Update Process (Frontend GUI)**

**The same permissions process applies for CodeCanyon app updates:**

1. **Before Update:**

    ```bash
    # Run pre-installation permissions (777 temporary)
    ./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/1-permissions-pre-install.sh
    ```

2. **Perform Update via Web Interface:**

    - Navigate to update section in your app's admin panel
    - Follow update wizard steps
    - Wait for "Update Complete" confirmation

3. **After Update:**
    ```bash
    # IMMEDIATELY run post-installation security lockdown
    ./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/2-permissions-post-install.sh
    ```

### **Emergency Security Recovery**

**If you forget to run post-installation security:**

```bash
# EMERGENCY: Immediate security recovery
echo "🚨 EMERGENCY SECURITY RECOVERY"

# Force production-level security regardless of environment
chmod -R 755 storage/ bootstrap/cache/ public/user-uploads/ public/uploads/
chmod -R 755 config/ && find config/ -type f -exec chmod 644 {} \;
chmod -R 755 public/ && find public/ -type f -exec chmod 644 {} \;
chmod 600 .env*

# Audit for dangerous permissions
find . -type f -perm 777 -exec chmod 644 {} \;
find . -type d -perm 777 -exec chmod 755 {} \;

echo "✅ Emergency security lockdown complete"
echo "⚠️ Review and adjust permissions for your specific environment if needed"
```

---

## 🛠️ **CLI Installation/Updates (Alternative Method)**

## 🛠️ **CLI Installation/Updates (Alternative Method)**

### **CLI Installation Phase Permissions**

**⚠️ TEMPORARY ONLY - Revert after installation**

```bash
# Temporarily elevate permissions for CodeCanyon installer
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 public/user-uploads/
chmod -R 777 public/uploads/
chmod -R 777 config/  # Some installers modify config files

# Keep .env secure even during installation
chmod 600 .env
```

### **Why `777` During Installation:**

-   **CodeCanyon installers:** May run under different user contexts
-   **File creation:** Installer needs to create directories and files
-   **Configuration writes:** Some installers modify config files
-   **Database migrations:** May need to write migration tracking files

### **After CLI Installation - IMMEDIATE REVERT**

```bash
# IMMEDIATELY after installation completes
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/user-uploads/
chmod -R 755 public/uploads/
chmod -R 644 config/  # Revert config to read-only
find config/ -type d -exec chmod 755 {} \;  # But directories need execute

# Verify .env is still secure
chmod 600 .env
```

---

## 🔄 **Update Process Permissions**

### **For CodeCanyon App Updates**

```bash
# Before applying CodeCanyon updates
echo "🔄 Preparing for CodeCanyon update..."
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 public/user-uploads/
chmod -R 777 public/uploads/

# If update includes config changes
chmod -R 777 config/

# Apply the update...
# [Update process runs here]

# IMMEDIATELY after update
echo "🔒 Restoring secure permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/user-uploads/
chmod -R 755 public/uploads/
chmod -R 644 config/
find config/ -type d -exec chmod 755 {} \;
chmod 600 .env

echo "✅ Update complete with secure permissions restored"
```

---

## 🚨 **Security Warnings & Best Practices**

### **NEVER Do This in Production:**

```bash
# ❌ DANGEROUS - Never do this
chmod -R 777 /  # Never give 777 to entire system
chmod 777 .env  # Never make .env world-writable
chmod -R 777 . && leave_it  # Never leave 777 permanently
```

### **Security Checklist for Production:**

#### **File Permissions Audit:**

```bash
# Check for dangerous permissions
find . -type f -perm 777 -ls  # Should return nothing
find . -type f -perm 666 -ls  # Should be minimal
find . -name ".env*" -ls      # Should show 600 permissions

# Check ownership
ls -la storage/ bootstrap/cache/ public/uploads/
```

#### **Web Server Configuration:**

-   **Disable PHP execution** in `storage/` and `bootstrap/cache/`
-   **Block direct access** to `.env` files
-   **Configure proper upload restrictions** for `public/uploads/`

#### **Regular Security Maintenance:**

```bash
# Weekly permission audit script
#!/bin/bash
echo "🔍 Security Audit - $(date)"
echo "Checking for world-writable files..."
find . -type f -perm 777 -ls

echo "Checking .env security..."
ls -la .env*

echo "Checking upload directories..."
ls -la public/uploads/ public/user-uploads/
```

---

## 📝 **CodeCanyon Specific Considerations**

### **Why CodeCanyon Apps Need Special Handling:**

1. **Frontend Installation:** Users install via web interface, not command line
2. **Multi-tenancy:** May create tenant-specific directories
3. **User Uploads:** Extensive file upload functionality
4. **Theme/Plugin Systems:** Dynamic file creation for customizations
5. **Update Mechanisms:** Self-updating via web interface

### **CodeCanyon Permission Strategy:**

```bash
# Development Phase
# ✅ Use 775 for convenience and debugging
chmod -R 775 storage/ bootstrap/cache/ public/user-uploads/

# Installation Phase
# ⚠️ Temporarily use 777, then immediately revert
chmod -R 777 [required-directories]
# [Run installation]
chmod -R 755 [required-directories]  # Immediate revert

# Production Phase
# 🔒 Use 755 for security
chmod -R 755 storage/ bootstrap/cache/ public/user-uploads/
```

---

## 🎯 **Environment-Specific Scripts**

### **Local Development Setup**

````bash
---

## 📁 **Ready-to-Use Permission Scripts**

**Create these scripts in your project for easy permission management:**

### **1. Create Scripts Directory**

```bash
mkdir -p scripts/permissions
````

### **2. Pre-Installation Script**

```bash
cat > scripts/permissions/pre-install.sh << 'EOF'
#!/bin/bash
# ===============================================
# PRE-INSTALLATION TEMPORARY PERMISSIONS
# ===============================================

echo "🛠️ Setting temporary permissions for frontend installation..."
echo "⚠️ These will be REVERTED immediately after installation"

# Core writable directories - TEMPORARY 777
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R 777 public/user-uploads/
[ -d "public/uploads" ] && chmod -R 777 public/uploads/

# Configuration directories (some installers modify these)
chmod -R 777 config/

# Public directory (for asset generation)
chmod -R 777 public/

# Keep sensitive files secure even during installation
chmod 600 .env*
[ -f "config/database.php" ] && chmod 600 config/database.php

echo "✅ Temporary installation permissions applied"
echo "🎯 Ready for frontend installation/update"
echo "⚠️ REMEMBER: Run ./scripts/permissions/post-install.sh immediately after completion!"
EOF

chmod +x scripts/permissions/pre-install.sh
```

### **3. Post-Installation Security Script**

```bash
cat > scripts/permissions/post-install.sh << 'EOF'
#!/bin/bash
# ===============================================
# POST-INSTALLATION SECURITY LOCKDOWN
# ===============================================

echo "🔒 IMMEDIATE SECURITY LOCKDOWN - Post Installation"
echo "📅 $(date)"

# Determine environment and set appropriate permissions
if [[ -f ".env" && $(grep "APP_ENV=local" .env) ]]; then
    ENV_TYPE="local"
    PERM_STORAGE="775"
    echo "🏠 Detected: LOCAL environment"
elif [[ -f ".env" && $(grep "APP_ENV=staging" .env) ]]; then
    ENV_TYPE="staging"
    PERM_STORAGE="755"
    echo "🌐 Detected: STAGING environment"
elif [[ -f ".env" && $(grep "APP_ENV=production" .env) ]]; then
    ENV_TYPE="production"
    PERM_STORAGE="755"
    echo "🚀 Detected: PRODUCTION environment"
else
    ENV_TYPE="production"
    PERM_STORAGE="755"
    echo "⚠️ Unknown environment - defaulting to PRODUCTION security"
fi

# Revert core directories to appropriate permissions
echo "🔧 Setting $ENV_TYPE permissions..."
chmod -R $PERM_STORAGE storage/
chmod -R $PERM_STORAGE bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R $PERM_STORAGE public/user-uploads/
[ -d "public/uploads" ] && chmod -R $PERM_STORAGE public/uploads/

# Secure configuration and public directories
chmod -R 755 config/ && find config/ -type f -exec chmod 644 {} \;
chmod -R 755 public/ && find public/ -type f -name "*.php" -exec chmod 644 {} \;

# Ensure sensitive files remain secure
chmod 600 .env*
[ -f "config/database.php" ] && chmod 600 config/database.php

# Set proper ownership (adjust for your server)
if [[ "$ENV_TYPE" == "local" ]]; then
    chown -R $(whoami):staff storage/ bootstrap/cache/
    [ -d "public/user-uploads" ] && chown -R $(whoami):staff public/user-uploads/
    [ -d "public/uploads" ] && chown -R $(whoami):staff public/uploads/
else
    echo "⚠️ MANUAL ACTION REQUIRED: Set proper ownership for $ENV_TYPE"
    echo "Example: chown -R www-data:www-data storage/ bootstrap/cache/ public/user-uploads/ public/uploads/"
fi

# Security audit
DANGEROUS_FILES=$(find . -type f -perm 777 2>/dev/null | wc -l)
if [[ $DANGEROUS_FILES -gt 0 ]]; then
    echo "❌ WARNING: Found $DANGEROUS_FILES files with 777 permissions"
    find . -type f -perm 777 -ls
else
    echo "✅ No files with dangerous 777 permissions found"
fi

echo ""
echo "🎯 INSTALLATION SECURITY LOCKDOWN COMPLETE"
echo "🔒 Your application is now secured with $ENV_TYPE-appropriate permissions"
EOF

chmod +x scripts/permissions/post-install.sh
```

### **4. Emergency Security Recovery Script**

```bash
cat > scripts/permissions/emergency-security.sh << 'EOF'
#!/bin/bash
# ===============================================
# EMERGENCY SECURITY RECOVERY
# ===============================================

echo "🚨 EMERGENCY SECURITY RECOVERY"
echo "📅 $(date)"

# Force production-level security regardless of environment
chmod -R 755 storage/ bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R 755 public/user-uploads/
[ -d "public/uploads" ] && chmod -R 755 public/uploads/
chmod -R 755 config/ && find config/ -type f -exec chmod 644 {} \;
chmod -R 755 public/ && find public/ -type f -exec chmod 644 {} \;
chmod 600 .env*

# Audit and fix dangerous permissions
find . -type f -perm 777 -exec chmod 644 {} \;
find . -type d -perm 777 -exec chmod 755 {} \;

echo "✅ Emergency security lockdown complete"
echo "⚠️ Review and adjust permissions for your specific environment if needed"
EOF

chmod +x scripts/permissions/emergency-security.sh
```

### **5. Complete App Permissions Reset Script**

```bash
cat > scripts/permissions/complete-reset.sh << 'EOF'
#!/bin/bash
# ===============================================
# COMPLETE APP PERMISSIONS RESET
# ===============================================

echo "🔄 COMPLETE LARAVEL APP PERMISSIONS RESET"
echo "📅 $(date)"

# Determine environment
if [[ -f ".env" && $(grep "APP_ENV=local" .env) ]]; then
    ENV_TYPE="local"
    STORAGE_PERM="775"
    echo "🏠 LOCAL environment detected"
else
    ENV_TYPE="production"
    STORAGE_PERM="755"
    echo "🚀 PRODUCTION/STAGING environment (or unknown - defaulting to secure)"
fi

echo "🔧 Applying complete $ENV_TYPE permissions..."

# 1. Core Laravel writable directories
chmod -R $STORAGE_PERM storage/
chmod -R $STORAGE_PERM bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R $STORAGE_PERM public/user-uploads/
[ -d "public/uploads" ] && chmod -R $STORAGE_PERM public/uploads/

# 2. Application directories (standard)
chmod -R 755 app/ config/ routes/ resources/ database/ tests/ public/ vendor/
[ -d "node_modules" ] && chmod -R 755 node_modules/

# 3. All application files (PHP, JSON, etc.)
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.json" -exec chmod 644 {} \;
find . -type f -name "*.js" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type f -name "*.md" -exec chmod 644 {} \;
find . -type f -name "*.txt" -exec chmod 644 {} \;
find . -type f -name "*.yml" -exec chmod 644 {} \;
find . -type f -name "*.yaml" -exec chmod 644 {} \;

# 4. Secure sensitive files
chmod 600 .env*
[ -f "config/database.php" ] && chmod 600 config/database.php

# 5. Make executable files executable
chmod +x artisan
find . -name "*.sh" -exec chmod +x {} \;

# 6. Set proper ownership
if [[ "$ENV_TYPE" == "local" ]]; then
    chown -R $(whoami):staff storage/ bootstrap/cache/
    [ -d "public/user-uploads" ] && chown -R $(whoami):staff public/user-uploads/
    [ -d "public/uploads" ] && chown -R $(whoami):staff public/uploads/
    echo "✅ Local ownership applied"
else
    echo "⚠️ MANUAL ACTION REQUIRED: Set proper ownership for production"
    echo "Example: chown -R www-data:www-data storage/ bootstrap/cache/ public/user-uploads/ public/uploads/"
fi

echo ""
echo "✅ COMPLETE PERMISSIONS RESET FINISHED"
echo "Environment: $ENV_TYPE"
echo "Storage permissions: $STORAGE_PERM"
echo "🔒 Your application is now properly secured"
EOF

chmod +x scripts/permissions/complete-reset.sh
```

### **6. Usage Examples**

```bash
# Before frontend installation/update (any environment)
./scripts/permissions/pre-install.sh

# Immediately after frontend installation/update completes
./scripts/permissions/post-install.sh

# If you forgot to run post-install and need emergency security
./scripts/permissions/emergency-security.sh

# Complete permissions reset (useful for new deployments)
./scripts/permissions/complete-reset.sh
```

### **7. Add to .gitignore (Optional)**

```bash
echo "" >> .gitignore
echo "# Permission management scripts (optional - you may want to commit these)" >> .gitignore
echo "# scripts/permissions/" >> .gitignore
```

**Note:** You probably want to commit these scripts to your repository so they're available on all environments.

````

### **Production Deployment**

```bash
#!/bin/bash
# scripts/permissions-production.sh
echo "🌐 Setting up PRODUCTION permissions..."
chmod -R 755 storage/ bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R 755 public/user-uploads/
[ -d "public/uploads" ] && chmod -R 755 public/uploads/
chmod 600 .env
chmod +x artisan
chown -R www-data:www-data storage/ bootstrap/cache/ public/uploads/ public/user-uploads/
echo "✅ Production permissions applied"
````

### **Installation Helper**

```bash
#!/bin/bash
# scripts/permissions-install.sh
echo "🛠️ Setting up INSTALLATION permissions..."
chmod -R 777 storage/ bootstrap/cache/
[ -d "public/user-uploads" ] && chmod -R 777 public/user-uploads/
[ -d "public/uploads" ] && chmod -R 777 public/uploads/
echo "⚠️  TEMPORARY 777 permissions applied"
echo "⚠️  Remember to run permissions-production.sh after installation!"
```

---

## ✅ **Summary & Decision Tree**

### **When to Use Each Permission Level:**

**🏠 Local Development (Herd/XAMPP/WAMP):**

-   **Use `775`** for storage, cache, uploads
-   **Convenience over security**
-   **Easy debugging and file access**

**🌐 Production Server:**

-   **Use `755`** for storage, cache, uploads
-   **Security over convenience**
-   **Proper ownership with web server user**

**🛠️ During Installation/Updates:**

-   **Temporarily use `777`** only during the process
-   **IMMEDIATELY revert** to production permissions
-   **Never leave `777` permanent**

**🔒 Always Secure:**

-   **`.env` files: `600`** (never world-readable)
-   **Application code: `644`** (read-only for most files)
-   **Directories: `755`** (minimum needed for directory access)

### **Your CodeCanyon App Decision:**

**✅ YES, you should change permissions based on environment:**

1. **Local Development:** Keep `775` as set in Step 14 ✅
2. **Production:** Change to `755` for security ✅
3. **During Updates:** Temporarily use `777`, then revert ✅
4. **Never:** Leave `777` permanent in production ❌

**The permissions in Step 14 are correct for local development but should be adjusted for production deployment.**
