# Step 06: Create Universal .gitignore

**Create comprehensive .gitignore file for CodeCanyon project deployments**

> 📋 **Analysis Source:** V1 Step 5.2 (V2 was basic) - Using V1's advanced universal .gitignore
>
> 🎯 **Purpose:** Comprehensive .gitignore covering Laravel, CodeIgniter, and generic PHP for any deployment scenario

---

## **Universal .gitignore Creation**

### **Create Comprehensive .gitignore File**

```bash
# Navigate to project root (ensure you're in the right directory)
pwd
# Should show: .../SocietyPalApp-Root

# Create comprehensive .gitignore (works for Laravel, CodeIgniter, Generic PHP)
cat > .gitignore << 'EOF'
###############################################################################
# UNIVERSAL .gitignore for PHP Apps (Laravel, CodeIgniter 4/3, Generic PHP)
# Goals: 1-click clone → install → run; safe deploys; no user-data loss.
# Strategy: Track ALL source; ignore only dependencies, build outputs,
# runtime/writable paths, and secrets. Deployment uses /shared + symlinks.
###############################################################################

# -------------------------
# 1) DEPENDENCIES (always rebuilt)
# -------------------------
/vendor/
/node_modules/
npm-debug.log
yarn-error.log
composer.lock*
package-lock.json*

# -------------------------
# 2) FRONTEND BUILD OUTPUTS (rebuilt during deployment)
# -------------------------
/public/build/
/public/hot
/public/mix-manifest.json
/public/js/app.js
/public/css/app.css
/public/assets/build/
vite.config.js.timestamp-*

# -------------------------
# 3) RUNTIME FILES (recreated by application)
# -------------------------
/public/storage
/storage/*.key
/storage/framework/cache/data/*
/storage/framework/sessions/*
/storage/framework/views/*
/storage/logs/*
/storage/app/public/*
/storage/pail
/writable/cache/*
/writable/logs/*
/writable/session/*
/writable/uploads/*

# -------------------------
# 4) ENVIRONMENT FILES (contain secrets)
# -------------------------
.env
.env.*
.env.backup
.env.production
.env.staging
.env.local
.env.testing
!/.env.example
!/.env.template

# -------------------------
# 5) TEST & CACHE FILES (rebuilt)
# -------------------------
/.phpunit.cache
/.phpunit.result.cache
.phpunit.result.cache
/coverage
auth.json
.phpactor.json
.phpmd.xml
.php_cs.cache
.php-cs-fixer.cache

# -------------------------
# 6) OS & IDE FILES
# -------------------------
/.fleet/
/.idea/
/.vscode/
*.sublime-project
*.sublime-workspace
*.komodoproject
.DS_Store
._*
.Spotlight-V100
.Trashes
Thumbs.db
ehthumbs.db
*.swp
*.swo
*.tmp
*~
desktop.ini

# -------------------------
# 7) LARAVEL SPECIFIC
# -------------------------
/bootstrap/cache/*.php
Homestead.yaml
Homestead.json
/.vagrant
/public_html/storage
/public_html/hot
/nova/
.rr.yaml
rr

# -------------------------
# 8) CODEIGNITER 4 (if applicable)
# -------------------------
/writable/*
!/writable/.gitkeep
!/writable/index.html

# -------------------------
# 9) CODEIGNITER 3 (if applicable)
# -------------------------
/application/cache/*
/application/logs/*
!/application/cache/.gitkeep
!/application/logs/.gitkeep

# -------------------------
# 10) CODECANYON SPECIFIC
# -------------------------
# Keep original vendor files trackable but ignore customizations that should be in separate branches
/Admin-Local/
/admin-local/
install.php
setup.php
/install/
/installer/
*.zip
*.tar.gz
*.rar

# License and docs (keep for reference)
!LICENSE
!README.md
!CHANGELOG.md
!INSTALLATION.md

# -------------------------
# 11) BACKUP & TEMP FILES
# -------------------------
*.backup
*.bak
*.sql
*.dump
/backups/
/dumps/
/tmp/
/temp/

# -------------------------
# 12) SECURITY FILES (never commit)
# -------------------------
.htpasswd
.htaccess.backup
/.well-known/
/ssl/
*.pem
*.key
*.crt
*.csr

# -------------------------
# 13) DEPLOYMENT SPECIFIC
# -------------------------
.deployment
deployment-config.php
deploy.php
/.deployer/
.dep/

###############################################################################
# END UNIVERSAL .gitignore
#
# DEPLOYMENT READY: This .gitignore works with:
# • Scenario A: Local build + SSH deploy
# • Scenario B: GitHub Actions build + deploy
# • Scenario C: DeployHQ professional deploy
# • Scenario D: Server pull + local build upload
#
# FRAMEWORK READY: Supports Laravel, CodeIgniter 4/3, Generic PHP
# CODECANYON READY: Handles vendor files, licenses, installation files
###############################################################################
EOF

echo "✅ Universal .gitignore created"
```

### **Verify .gitignore Creation**

```bash
# Check the .gitignore file was created
ls -la .gitignore

# Verify contents (first few lines)
echo "📄 .gitignore file contents (preview):"
head -20 .gitignore

# Check total lines
echo ""
echo "📊 Total lines in .gitignore: $(wc -l < .gitignore)"
echo "✅ Comprehensive .gitignore ready for all deployment scenarios"
```

### **Test .gitignore Effectiveness**

```bash
# Test what files Git will track vs ignore
echo ""
echo "🧪 Testing .gitignore effectiveness:"
echo "=================================="

# Show what would be added (without actually adding)
git add --dry-run .

echo ""
echo "✅ Files above will be tracked by Git"
echo "❌ Dependencies, build files, secrets, and temp files will be ignored"
echo "🎯 Perfect for any deployment scenario (A, B, C, or D)"
```

**Expected Results:**

- ✅ Comprehensive .gitignore file created in project root
- ✅ Ready for any deployment scenario (Local, GitHub Actions, DeployHQ, Server pull)
- ✅ Supports Laravel, CodeIgniter 4/3, and generic PHP applications
- ✅ CodeCanyon-specific exclusions included
- ✅ Security files and secrets properly excluded
- ✅ Dependencies and build artifacts ignored (will be rebuilt during deployment)

---

## **Why This .gitignore is Universal**

### **Deployment Scenario Coverage:**

| Scenario                | Build Location   | .gitignore Compatibility                       |
| ----------------------- | ---------------- | ---------------------------------------------- |
| A) Local Build + SSH    | Local machine    | ✅ Ignores node_modules, builds them locally   |
| B) GitHub Actions       | CI runners       | ✅ CI rebuilds dependencies from scratch       |
| C) DeployHQ             | DeployHQ servers | ✅ Professional build pipeline compatible      |
| D) Server Pull + Upload | Mixed            | ✅ Supports both local builds and server pulls |

### **Framework Coverage:**

- ✅ **Laravel**: All Laravel-specific cache and build directories
- ✅ **CodeIgniter 4**: Writable directory patterns
- ✅ **CodeIgniter 3**: Cache and logs directories
- ✅ **Generic PHP**: Common PHP application patterns
- ✅ **CodeCanyon**: Installation files, licenses, vendor archives

### **Security Benefits:**

- 🔒 Never commits .env files with database passwords
- 🔒 Excludes SSL certificates and keys
- 🔒 Ignores deployment configurations with server details
- 🔒 Protects API keys and sensitive credentials

---

**Next Step:** [Step 07: Download CodeCanyon](Step_07_Download_CodeCanyon.md)
