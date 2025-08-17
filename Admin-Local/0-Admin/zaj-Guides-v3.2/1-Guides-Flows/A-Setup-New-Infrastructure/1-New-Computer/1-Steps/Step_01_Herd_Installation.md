# Step 01: Herd Installation & Configuration

## Analysis Source

**Primary Source**: V2 Phase0 + Manual-Phase0-steps.md - Complete Herd setup workflow
**Secondary Source**: V1 Complete Guide - No equivalent content  
**Recommendation**: Use V2 + Manual combined for complete coverage

---

## 🎯 Purpose

Establish Laravel Herd as the foundational development environment that supports all future CodeCanyon projects with PHP 8.3, Nginx, MySQL, Redis, and essential development tools.

## ⚡ Quick Reference

**Time Required**: ~20-30 minutes  
**Prerequisites**: macOS computer with admin access  
**Frequency**: One-time per computer (benefits all projects)

---

## 🔄 **PHASE 1: Download and Install Herd**

### **1.1 Download & Install Herd**

**INSTRUCT-USER: Manual Download Required**

```bash
# Manual installation process - HUMAN ACTION REQUIRED
echo "📥 Herd Installation Process"
echo "========================="
echo "⚠️  HUMAN ACTION REQUIRED - External Download"
echo ""
echo "🌐 Steps to complete in browser:"
echo "1. Visit: https://herd.laravel.com"
echo "2. Click 'Download for macOS'"
echo "3. Wait for .dmg file to download"
echo "4. Open the downloaded .dmg file"
echo "5. Drag Laravel Herd to Applications folder"
echo "6. Launch Herd from Applications"
echo "7. Grant permissions when prompted"
echo "8. Enter macOS password when requested"
echo "9. Wait for automatic configuration to complete"
echo ""
echo "💡 Return here after installation completes"
```

**END-INSTRUCT-USER**

1. **Visit Laravel Herd website:**

   - Open browser and go to `https://herd.laravel.com`
   - Click "Download for macOS"
   - Wait for `.dmg` file to download

2. **Install the application:**

   - Open the downloaded `.dmg` file
   - Drag Laravel Herd to Applications folder
   - Launch Herd from Applications

3. **Initial setup and permissions:**
   - Grant permissions when prompted (required for DNS/networking)
   - Enter your macOS password when requested
   - Wait for automatic configuration to complete

**What Herd automatically configures:**

- ✅ PHP 8.3 (latest stable version)
- ✅ Nginx web server with Laravel-friendly configuration
- ✅ DNS masq for automatic `.test` domains
- ✅ Composer globally available
- ✅ Node.js via NVM integration
- ✅ Path configuration for global access

**Expected Results:**

- Herd application running in system tray
- PHP, Composer, and Node.js available globally
- Automatic .test domain resolution configured

---

## 🔄 **PHASE 2: Verify Installation**

### **2.1 Command Line Verification**

Open Terminal and run these verification commands:

```bash
# Verify core development tools
echo "🧪 Verifying Herd Installation"
echo "=========================="

# Check PHP version (should show 8.3.x)
php --version

# Check Composer (should show 2.x)
composer --version

# Check Node.js (should show latest LTS)
node --version

# Check NPM
NPM_VERSION=$(npm --version 2>/dev/null)
echo "NPM: $NPM_VERSION"

# Verify expected versions
echo ""
echo "✅ Expected Results:"
echo "   PHP: 8.3.x or higher"
echo "   Composer: 2.x or higher"
echo "   Node.js: 18.x or higher"
echo "   NPM: 9.x or higher"
```

**Expected Command Results:**

```bash
# Example successful output:
# PHP 8.3.x (cli) (built: ...)
# Composer version 2.x.x
# v18.x.x or higher
# 9.x.x or higher
```

### **2.2 Web Server Test**

```bash
# Test Herd web server functionality
echo ""
echo "🌐 Testing Herd Web Server"
echo "======================="

# Create temporary test site
mkdir -p ~/Herd/test-site
cd ~/Herd/test-site

# Create simple PHP test file
cat > index.php << 'EOF'
<?php
phpinfo();
?>
EOF

echo "📝 Test site created: ~/Herd/test-site"
echo "🌐 Visit: http://test-site.test"
echo "💡 You should see PHP info page"
echo ""
echo "🧹 Cleanup after testing:"
echo "   rm -rf ~/Herd/test-site"
```

### **2.3 DNS Resolution Test**

```bash
# Test automatic .test domain resolution
echo ""
echo "🔍 Testing DNS Resolution"
echo "======================"

# Test if .test domains resolve
TEST_RESOLVE=$(nslookup test-site.test 2>/dev/null | grep "127.0.0.1" || echo "NOT_FOUND")

if [ "$TEST_RESOLVE" != "NOT_FOUND" ]; then
    echo "✅ DNS Resolution: Working"
    echo "   .test domains automatically resolve to localhost"
else
    echo "⚠️ DNS Resolution: Needs attention"
    echo "   You may need to restart Herd or check DNS settings"
fi
```

**Expected Results:**

- PHP 8.3.x responding correctly
- Composer 2.x available globally
- Node.js 18.x+ and NPM 9.x+ functional
- .test domains resolving to localhost
- Test PHP site accessible at http://test-site.test

---

## 🔄 **PHASE 3: Herd Pro Upgrade (Highly Recommended)**

### **3.1 Why Upgrade to Herd Pro**

```bash
echo "🏆 Herd Pro Benefits for CodeCanyon Projects"
echo "========================================"
echo ""
echo "💰 Cost: $99/year (excellent value for features)"
echo ""
echo "🎯 Professional Features:"
echo "   ✅ Built-in MySQL database server"
echo "   ✅ Redis caching and session storage"
echo "   ✅ Mailpit for email testing"
echo "   ✅ Database dumps viewer and management"
echo "   ✅ Services management interface"
echo "   ✅ Multiple PHP versions"
echo "   ✅ Advanced debugging tools"
echo ""
echo "🚀 CodeCanyon Advantages:"
echo "   ✅ No separate database installation needed"
echo "   ✅ Email testing for CodeCanyon apps"
echo "   ✅ Easy database management for multiple projects"
echo "   ✅ Professional development environment"
echo "   ✅ Simplified team setup and sharing"
```

### **3.2 Herd Pro Services Configuration**

> � **Service Naming Strategy**: Use descriptive names that can be shared across multiple CodeCanyon projects:
>
> - `Local_MySQL` (shared database server)
> - `Local_Redis` (shared caching service)
> - Alternative per-project naming: `ProjectName_MySQL`, `ProjectName_Redis`

#### **MySQL Database Setup:**

1. **Navigate to Services:**

   - Open `Herd` → `Services` tab
   - Click `Create Service` → Database → `MySQL 8.0` or higher (example: `8.4.2`)

2. **Configure MySQL service:**

   ```
   Name: Local_MySQL
   Port: 3306 (default)
   Root password: zaj123
   Autostart: ✅ (checked)
   ```

3. **Click Create Service**

4. **Note connection details for environment files:**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=root
   DB_PASSWORD=zaj123
   ```

#### **Redis Cache Setup:**

1. **Create Redis service:**

   - Services → Create Service → Cache → Redis 7.0

2. **Configure Redis service:**

   ```
   Name: Local_Redis
   Port: 6379 (default)
   ```

3. **Click Create Service**

4. **Note connection details:**
   ```bash
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

### **3.3 Optional Development Directory Structure**

Create an organized project structure that works seamlessly with Herd's automatic `.test` domain serving:

```bash
# Create organized project structure for CodeCanyon apps
mkdir -p ~/Herd/codecanyon-projects
cd ~/Herd/codecanyon-projects

# Example: Create a project directory
mkdir yourapp-v2.4
cd yourapp-v2.4

# Herd will automatically serve this as: yourapp-v2.4.test
```

**Directory Structure Benefits:**

- ✅ Herd automatically serves any project in `~/Herd/` as `projectname.test`
- ✅ Shared PHP version across all projects
- ✅ Shared development services (MySQL, Redis)
- ✅ Organized separation of different CodeCanyon applications
- ✅ Version-specific project folders (e.g., `societypro-v1.0.4`)

**Expected Results:**

- Projects automatically accessible via browser
- No manual server configuration required
- Consistent development environment across projects

### **3.4 Verify Services Status**

```bash
# Verify all services are running correctly
echo "🔍 Verifying Herd Pro Services"
echo "============================="

# Check MySQL connection (will prompt for password: zaj123)
mysql -h 127.0.0.1 -P 3306 -u root -p -e "SELECT VERSION();"

# Check Redis connection (should return PONG)
redis-cli ping

echo ""
echo "✅ Expected Service Results:"
echo "   MySQL: Version information displayed"
echo "   Redis: PONG response"
echo "   Herd Services Panel: All services show 'Running' status"
```

**Expected Pro Results:**

- ✅ MySQL server running and accessible
- ✅ Redis available for caching and sessions
- ✅ Services management interface functional
- ✅ Connection details ready for CodeCanyon projects
- ✅ Both services show "Running" status in Herd Services panel

---

## 🔄 **PHASE 4: Development Environment Optimization**

### **4.1 PHP Configuration Optimization**

```bash
# Optimize PHP for CodeCanyon development
echo "🔧 Optimizing PHP for CodeCanyon Projects"
echo "======================================="

# Check current PHP configuration
echo "📊 Current PHP Configuration:"
php -i | grep -E "(memory_limit|max_execution_time|upload_max_filesize|post_max_size)"

echo ""
echo "🎯 Recommended PHP Settings for CodeCanyon:"
echo "   memory_limit = 512M (for large applications)"
echo "   max_execution_time = 300 (for migrations)"
echo "   upload_max_filesize = 64M (for file uploads)"
echo "   post_max_size = 64M (for form data)"
echo ""
echo "💡 Herd Pro allows PHP configuration customization"
echo "   Access via Herd → Settings → PHP"
```

### **4.2 Global Composer Packages**

```bash
# Install useful global Composer packages
echo "📦 Installing Global Composer Packages"
echo "====================================="

# Install Laravel installer
composer global require laravel/installer

# Install Laravel Valet (for advanced users)
# composer global require laravel/valet

echo ""
echo "✅ Global packages installed:"
echo "   - Laravel installer (for new projects)"
echo "   - Global Composer bin directory in PATH"
```

### **4.3 Node.js and NPM Configuration**

```bash
# Configure Node.js for frontend builds
echo "🎨 Configuring Node.js for Frontend Development"
echo "============================================="

# Verify Node.js version
NODE_VERSION=$(node --version)
echo "Current Node.js: $NODE_VERSION"

# Install global packages for Laravel development
npm install -g npm@latest
npm install -g @vue/cli
npm install -g vite

echo ""
echo "✅ Frontend tools installed:"
echo "   - Latest NPM version"
echo "   - Vue CLI (for Vue.js projects)"
echo "   - Vite (for modern build tooling)"
```

**Expected Optimization Results:**

- PHP optimized for CodeCanyon requirements
- Global Laravel tools available
- Frontend build tools configured
- Development environment ready for production

---

## ✅ **Installation Success Verification**

### **Complete Environment Test**

```bash
echo "🏆 Final Herd Installation Verification"
echo "======================================"
echo ""

# Test complete development stack
echo "🧪 Testing Complete Development Stack:"
echo ""

# 1. PHP Test
if command -v php &> /dev/null; then
    PHP_VER=$(php --version | head -1)
    echo "✅ PHP: $PHP_VER"
else
    echo "❌ PHP: Not available"
fi

# 2. Composer Test
if command -v composer &> /dev/null; then
    COMPOSER_VER=$(composer --version 2>/dev/null)
    echo "✅ Composer: $COMPOSER_VER"
else
    echo "❌ Composer: Not available"
fi

# 3. Node.js Test
if command -v node &> /dev/null; then
    NODE_VER=$(node --version)
    echo "✅ Node.js: $NODE_VER"
else
    echo "❌ Node.js: Not available"
fi

# 4. MySQL Test (if Pro)
if command -v mysql &> /dev/null; then
    echo "✅ MySQL: Available via Herd Pro"
else
    echo "ℹ️ MySQL: Available via Herd Pro services"
fi

echo ""
echo "🎯 Environment Ready For:"
echo "   ✅ CodeCanyon Laravel project development"
echo "   ✅ PHP 8.3 modern features"
echo "   ✅ Frontend asset compilation"
echo "   ✅ Database-driven applications"
echo "   ✅ Email testing and debugging"
echo "   ✅ Professional deployment workflows"
```

---

## 🔧 **Troubleshooting Common Issues**

### **Path Issues**

```bash
# Fix PATH issues if commands not found
echo "🔧 Fixing PATH Issues"
echo "=================="

# Add Herd binaries to PATH
echo 'export PATH="/Users/$(whoami)/Library/Application Support/Herd/bin:$PATH"' >> ~/.zshrc
echo 'export PATH="~/.composer/vendor/bin:$PATH"' >> ~/.zshrc

# Reload shell configuration
source ~/.zshrc

echo "✅ PATH updated - restart terminal and test again"
```

### **Permission Issues**

```bash
# Fix common permission issues
echo "🔧 Fixing Permission Issues"
echo "========================"

# Fix Composer permissions
sudo chown -R $(whoami):staff ~/.composer

# Fix NPM permissions
mkdir ~/.npm-global
npm config set prefix '~/.npm-global'
echo 'export PATH=~/.npm-global/bin:$PATH' >> ~/.zshrc

echo "✅ Permissions fixed - restart terminal"
```

### **DNS Resolution Issues**

```bash
# Fix .test domain resolution
echo "🔧 Fixing DNS Resolution"
echo "====================="

# Restart Herd DNS service
echo "1. Quit Herd completely"
echo "2. Open Terminal and run:"
echo "   sudo dscacheutil -flushcache"
echo "   sudo killall -HUP mDNSResponder"
echo "3. Restart Herd"
echo "4. Test: ping test-site.test"
```

---

## 📋 **Next Steps**

✅ **Step 01 Complete** - Laravel Herd development environment established  
🔄 **Continue to**: Step 02 (SSH Configuration)  
🏗️ **Foundation**: Ready for CodeCanyon project development  
🎯 **Achievement**: Professional Laravel development environment operational

---

## 🎯 **Key Success Indicators**

- **Development Stack**: ⚡ PHP 8.3, Composer, Node.js all functional
- **Web Server**: 🌐 Nginx serving .test domains automatically
- **Database**: 🗄️ MySQL ready for CodeCanyon projects (Pro)
- **Email Testing**: 📧 Mailpit configured for development (Pro)
- **Build Tools**: 🎨 Frontend compilation tools ready
- **Global Tools**: 📦 Laravel installer and CLI tools available

**Herd installation complete - ready for professional CodeCanyon development!** 🚀
