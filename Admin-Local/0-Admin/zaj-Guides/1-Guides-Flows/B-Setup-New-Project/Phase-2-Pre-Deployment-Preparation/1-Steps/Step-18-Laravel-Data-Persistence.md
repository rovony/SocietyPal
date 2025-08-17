# Step 18: Universal Data Persistence System

## 🛡️ Zero Data Loss for ALL CodeCanyon Projects

### **Quick Overview**

-   🎯 **Purpose**: Universal data persistence using exclusion-based strategy
-   ⚡ **Result**: Zero data loss during deployments, vendor updates, and maintenance
-   🌐 **Compatibility**: ALL CodeCanyon apps + Laravel + Shared hosting
-   🔍 **Strategy**: Universal shared patterns + everything else = app code

### **Key Benefits**

-   ✅ **Universal**: Works with ANY CodeCanyon project structure
-   ✅ **Exclusion-Based**: Only specific patterns shared, everything else = app code
-   ✅ **Environment-Aware**: Different behavior for LOCAL vs SERVER
-   ✅ **Multiple Modes**: first, subsequent, verify, custom modes

---

## 🚨 **Universal Data Classification**

### **� EXCLUSION-BASED STRATEGY (Works for ALL Projects)**

```bash
# ✅ ALWAYS SHARED (Universal Patterns)
.env                    # Environment config
storage/                # Laravel storage (logs, cache, sessions, files)
bootstrap/cache/        # Laravel cache
public/.well-known/     # SSL certificates

# ✅ USER DATA PATTERNS (any naming variation)
public/*upload*/        # public/uploads/, public/user-uploads/, public/file-uploads/
public/*avatar*/        # public/avatars/, public/user-avatars/, public/profile-pics/
public/*media*/         # public/media/, public/user-media/, public/file-media/

# ✅ RUNTIME GENERATED PATTERNS
public/qr*/            # public/qrcodes/, public/qr/, public/qr-codes/
public/*invoice*/      # public/invoices/, public/user-invoices/
public/*export*/       # public/exports/, public/data-exports/
public/*report*/       # public/reports/, public/user-reports/
public/*temp*/         # public/temp/, public/temporary/
public/*generated*/    # public/generated/, public/auto-generated/

# ✅ CUSTOM DATA (from customization layer)
public/custom-*/       # public/custom-uploads/, public/custom-media/
storage/app/custom/    # Custom file storage
```

### **🔵 EVERYTHING ELSE = APPLICATION CODE (Deploy with Git)**

```bash
# Examples (but NOT limited to):
public/flags/          # Country flags, icons ← SocietyPal example
public/img/           # Static images, logos ← Universal
public/css/           # Stylesheets ← Universal
public/js/            # JavaScript ← Universal
public/build/         # Compiled assets ← Universal
public/themes/        # App themes ← CodeCanyon common
public/fonts/         # App fonts ← CodeCanyon common
public/vendor/        # Vendor assets ← CodeCanyon common
vendor/               # PHP dependencies ← Laravel standard
# ... LITERALLY EVERYTHING ELSE NOT IN SHARED PATTERNS
```

---

## ⚡ **Implementation**

### **🖥️ LOCAL Development (Directory Setup Only)**

**For localhost/development environment**:

```bash
# Simple local setup - directories only, no symlinks
bash 2-Files/Step-18-Files/simple-persistence.sh "$(pwd)" "local"
```

**What happens locally**:

-   ✅ **CREATES** required directories (storage/, public/user-uploads/, etc.)
-   ✅ **COPIES** .env from .env.example if needed
-   ❌ **NO symlinks created** (not needed for development)
-   ❌ **NO Laravel storage link** (use manually if needed)

### **🌐 SERVER Production (Full Symlinks)**

**For server/production environment**:

```bash
# Create full persistence system with symlinks
bash 2-Files/Step-18-Files/simple-persistence.sh "$(pwd)" "server"
```

**What happens on server**:

-   ✅ Creates shared directory structure
-   ✅ Creates all symlinks for persistence
-   ✅ Creates Laravel storage link
-   ✅ Preserves user data across deployments
-   ✅ Enables zero-downtime updates

### **🔍 Auto-Detection Logic**

```bash
# Script auto-detects environment based on:
# - Path contains "/Users/" (macOS development)
# - Path structure indicates local development
# - Presence of .env.local file
# - Manual override via 4th parameter

# Examples:
bash ultimate-persistence.sh                           # Auto-detect everything
bash ultimate-persistence.sh "$(pwd)" "../shared"     # Auto-detect environment
bash ultimate-persistence.sh "$(pwd)" "../shared" "auto" "local"   # Force local
bash ultimate-persistence.sh "$(pwd)" "../shared" "auto" "server"  # Force server
```

### **Phase 2: Verify Data Persistence**

```bash
# Comprehensive health check
bash ../shared/health-check.sh

# Expected output:
# 🏥 Laravel Data Persistence Health Check
# ==============================================
# ✅ Valid symlink: storage → ../shared/storage
# ✅ Valid symlink: .env → ../shared/.env
# ✅ Valid symlink: bootstrap/cache → ../../shared/bootstrap/cache
# ✅ Valid symlink: public/storage → ../../storage/app/public
# ✅ Shared storage accessible
# ✅ Shared .env accessible
# 📊 Results: ✅ 6 passed, ⚠️ 0 warnings, ❌ 0 failed
# 🎉 System is perfectly healthy!
```

### **Phase 3: Laravel-Specific Verification**

```bash
# Test Laravel-specific features
php artisan storage:link  # Should work without errors
php artisan config:cache  # Should complete successfully
ls -la storage/           # Should show symlink to ../shared/storage
ls -la public/storage/    # Should show symlink to ../../storage/app/public
```

### **Phase 4: Document Configuration**

```bash
# View persistence configuration
cat ../shared/.deployment-history

# View deployment summary
cat ../shared/DEPLOYMENT-SUMMARY.md

# View custom shared patterns detected
cat ../shared/.custom-shared-patterns
```

---

## 🔍 **What the Script Does Automatically**

### **1. Laravel Application Detection**

-   ✅ Validates presence of `artisan` file (Laravel requirement)
-   ✅ Sets Laravel-specific symlink patterns
-   ✅ Configures appropriate directory structures

### **2. Exclusion-Based Classification**

-   ✅ **Standard Laravel Shared**: `.env`, `storage/`, `bootstrap/cache/`
-   ✅ **Universal Shared Patterns**: `public/*upload*/`, `public/*avatar*/`, `public/*media*/`, `public/qr*`
-   ✅ **Custom Detection**: Scans for directories with user content (PDFs, images, docs)
-   ✅ **App Code Default**: Everything else stays as application code

### **3. First vs Subsequent Deployment Logic**

-   ✅ **First Deployment**: Copies demo content to shared, then symlinks
-   ✅ **Subsequent Deployment**: Protects existing shared content
-   ✅ **Auto-Detection**: Checks for `.deployment-history` file

### **4. Shared Hosting Compatibility**

-   ✅ Creates standard symlink structures compatible with most shared hosts
-   ✅ Provides emergency recovery for symlink issues
-   ✅ Generates manual setup instructions when needed

### **5. Emergency Systems**

-   ✅ **Health Check**: `shared/health-check.sh` - comprehensive symlink validation
-   ✅ **Emergency Recovery**: `shared/emergency-recovery.sh` - one-command repair
-   ✅ **Deployment Summary**: `shared/DEPLOYMENT-SUMMARY.md` - configuration record

---

## 🌐 **Directory Structure Created**

### **On Server After Setup**

```
/home/username/domain.com/
├── current -> releases/2024-08-16_14-30-00/    # Atomic deployment
├── releases/                                    # Timestamped releases
│   ├── 2024-08-16_14-30-00/                   # Latest release
│   │   ├── app/, vendor/, public/flags/        # Application code
│   │   ├── storage -> ../shared/storage        # Laravel storage
│   │   ├── .env -> ../shared/.env              # Environment config
│   │   ├── bootstrap/cache -> ../../shared/bootstrap/cache # Laravel cache
│   │   └── public/user-uploads -> ../shared/public/user-uploads # User data
│   └── 2024-08-16_12-00-00/                   # Previous release
├── shared/                                     # Persistent shared data
│   ├── storage/                               # Laravel storage
│   ├── bootstrap/cache/                       # Laravel cache
│   ├── .env                                   # Environment file
│   ├── public/user-uploads/                   # User uploads
│   ├── public/qrcodes/                        # Generated content
│   ├── health-check.sh                        # Monitoring script
│   ├── emergency-recovery.sh                  # Recovery script
│   ├── .deployment-history                    # Deployment log
│   └── DEPLOYMENT-SUMMARY.md                  # Configuration summary
└── public_html -> current/public/              # Web root (shared hosting)
```

### **Symlink Structure in Release**

```
📂 releases/current/
├── storage -> ../shared/storage                      # Laravel storage
├── .env -> ../shared/.env                           # Environment config
├── bootstrap/cache -> ../../shared/bootstrap/cache  # Laravel cache
└── public/
    ├── storage -> ../../storage/app/public          # Laravel public storage
    ├── user-uploads -> ../../shared/public/user-uploads # User content
    ├── qrcodes -> ../../shared/public/qrcodes       # Generated content
    ├── flags/                                       # App assets (NOT symlinked)
    ├── img/                                         # App assets (NOT symlinked)
    └── css/, js/, build/                            # App assets (NOT symlinked)
```

---

## 🚨 **Emergency Procedures**

### **If Symlinks Break**

```bash
# One-command recovery
bash ../shared/emergency-recovery.sh "$(pwd)"

# Manual symlink recreation
ln -sf ../shared/storage storage
ln -sf ../shared/.env .env
ln -sf ../../shared/bootstrap/cache bootstrap/cache
php artisan storage:link
```

### **If Shared Hosting Restricts Symlinks**

The script creates fallback documentation, but typical manual steps:

```bash
# Copy static files to public_html
cp -r releases/current/public/* public_html/

# Manually link only user data
rm -rf public_html/user-uploads
ln -s ../shared/public/user-uploads public_html/user-uploads

# Copy .env manually
cp shared/.env releases/current/.env
```

### **Health Check Failures**

```bash
# Run comprehensive diagnosis
bash ../shared/health-check.sh

# Check specific issues
ls -la | grep -E "(storage|uploads|env)"
find ../shared/ -type d -exec chmod 755 {} \;
find ../shared/ -type f -exec chmod 644 {} \;
```

---

## 📋 **Integration with Customization System**

This data persistence system works seamlessly with **Step 17: Customization Protection**:

### **Custom Layer Protection**

```bash
# Custom directories are automatically preserved
app/Custom/                    # Protected by Step 17
resources/Custom/              # Protected by Step 17
public/Custom/                 # Protected by both Step 17 & 18

# User uploads in custom areas are shared
public/Custom/user-uploads/    # Detected and shared by Step 18
```

### **Vendor Update Safety**

```bash
# During vendor updates (C-Deploy-Vendor-Updates):
# 1. Step 17 protects custom code (app/Custom/)
# 2. Step 18 protects user data (public/user-uploads/)
# 3. Vendor assets get updated (public/flags/, vendor/)
```

---

## ✅ **Success Verification**

After running this step, verify success with:

```bash
# 1. Health check passes
bash ../shared/health-check.sh

# 2. Laravel commands work
php artisan storage:link
php artisan config:cache

# 3. Web application loads correctly
# 4. File uploads work and persist
# 5. User data survives deployment tests
```

**Expected Result**: Zero data loss guarantee for all Laravel deployments with proper segregation of app code vs user data.

---

## 🔄 **Integration with Other Workflows**

This step integrates with:

-   **Step 17**: Customization Protection (preserves `app/Custom/`)
-   **Step 19**: Documentation & Investment Protection
-   **Step 20**: Commit Pre-Deploy
-   **C-Deploy-Vendor-Updates**: Protects data during vendor updates
-   **D-Maintenance-Operations**: Provides backup/recovery capabilities
-   **E-Customize-App**: Ensures custom assets are preserved
