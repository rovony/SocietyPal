# Step 14: Run Local & CodeCanyon Installation

**Start local development and complete CodeCanyon application installation**

> 📋 **Analysis Source:** V2 Amendment 9.2 - V1 had nothing, taking V2 Amendment entirely
> 
> 🎯 **Purpose:** Launch local development environment and complete CodeCanyon installation process

---

## 🚨 Human Interaction Required

**⚠️ This step includes tasks that must be performed manually outside this codebase:**

-   Completing CodeCanyon web installation through browser interface (filling forms, clicking buttons)
-   **Non-Interactive alternatives are provided for AI agents where possible**

**🏷️ Tag Instruct-User 👤** markers indicate the specific substeps requiring human action.

---

## **🔍 FYI: Herd Debugging & Logs for Installation Issues**

**📌 Important Note:** CodeCanyon applications are vetted by authors and typically work reliably. Installation issues in Step 14 are usually related to local Herd environment setup, not the application itself.

### **Quick Herd Diagnostics (AI Agent Compatible):**

```bash
# Check all Herd services status
herd services
echo "=== Services Status Above ==="

# Test database connection
mysql -u root -h 127.0.0.1 -P 3306 -e "SELECT 'Database OK' as status;"

# Check PHP version and configuration
herd php -v
herd php -m | grep -E "(pdo_mysql|curl|openssl|mbstring)"

# Verify site linking
herd sites

# Test site accessibility
curl -I https://societypal.test
echo "=== Site Response Above ==="
```

### **Herd Log Locations for Troubleshooting:**

**🏷️ Tag Instruct-User 👤** Access Herd logs via GUI:
1. **Herd App → Logs** (Primary log viewer)
2. **Select your site** from dropdown
3. **Filter by error level** if needed

**CLI Log Access (AI Agent Compatible):**
```bash
# Laravel application logs
tail -f storage/logs/laravel.log

# Check for recent errors
grep -i error storage/logs/laravel.log | tail -20

# System-level Herd logs (macOS)
tail -f ~/Library/Application\ Support/Herd/Log/herd.log

# PHP error logs
sudo tail -f /opt/homebrew/var/log/php-error.log
```

### **Common Herd-Related Installation Issues:**

**Database Connection Issues:**
```bash
# Verify MySQL is running
herd services | grep mysql

# Test direct connection
mysql -u root -h 127.0.0.1 -P 3306 -e "SHOW DATABASES;"

# Check Laravel database config
herd php artisan config:show database.connections.mysql
```

**Permission Issues:**
```bash
# Fix Laravel storage permissions
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):staff storage bootstrap/cache

# Clear all caches
herd php artisan config:clear
herd php artisan cache:clear
herd php artisan view:clear
```

**Site Access Issues:**
```bash
# Verify Herd site is linked
herd sites | grep societypal

# Re-link if needed
herd unlink societypal.test
herd link

# Check HTTPS certificate
curl -k -I https://societypal.test
```

### **If Installation Fails - Report These Details:**

**📋 Copy this diagnostic info when seeking help:**
```bash
# Generate comprehensive debug report
echo "=== HERD DEBUG REPORT ==="
echo "Date: $(date)"
echo "Herd Version: $(herd --version)"
echo "PHP Version: $(herd php -v | head -1)"
echo "MySQL Status: $(herd services | grep mysql)"
echo "Site Status: $(herd sites | grep societypal)"
echo "Laravel Version: $(herd php artisan --version)"
echo "Environment: $(cat .env | grep -E '(APP_ENV|DB_)')"
echo "Recent Errors:"
tail -20 storage/logs/laravel.log
echo "=== END REPORT ==="
```

**🎯 Most Common Fix:** Restart Herd services:
```bash
# Stop and start MySQL
herd services stop mysql
herd services start mysql

# Or restart all services via Herd GUI
```

---

## **Start Local Development Server**

1. **Set environment for local development:**
   ```bash
   # Copy local environment file
   cp .env.local .env
   
   # Generate application key (multiple options)
   php artisan key:generate
   # OR using Herd CLI:
   herd php artisan key:generate
   ```

2. **Start required services (Non-Interactive | AI Agent Compatible):**
   ```bash
   # Check Herd services status
   herd services
   
   # Start MySQL if not running (command line)
   herd services start mysql
   
   # Verify database connection
   herd php artisan migrate:status
   ```

3. **Access application via Herd URL:**
   ```bash
   # Open in browser automatically (Non-Interactive | AI Agent can execute)
   herd open
   
   # OR manual URL check
   echo "🌐 Visit: https://societypal.test"
   echo "🔍 Expected: CodeCanyon installer or application homepage"
   
   # Verify site is accessible (Non-Interactive | AI Agent test)
   curl -I https://societypal.test
   ```

## **Complete CodeCanyon Installation (if applicable)**

3. **Pre-installation checks (Non-Interactive | AI Agent Compatible):**
   ```bash
   # Verify database exists and is accessible
   herd php artisan migrate:status
   
   # Check if installer is accessible
   curl -I https://societypal.test/install || curl -I https://societypal.test/installer
   
   # Verify environment configuration
   herd php artisan config:show database
   ```

4. **Access CodeCanyon installer:**
   ```bash
   # If CodeCanyon app, visit installer URL
   echo "🛠️ Visit: https://societypal.test/install"
   echo "   OR"
   echo "🛠️ Visit: https://societypal.test/installer"
   
   # Non-Interactive: Open installer automatically (AI Agent)
   herd open /install
   ```

5. **Installation via CLI (Non-Interactive Alternative | AI Agents):**
   ```bash
   # If application supports CLI installation
   herd php artisan install:app \
     --db-host=127.0.0.1 \
     --db-port=3306 \
     --db-name=societypal_local \
     --db-user=root \
     --db-password=zaj123 \
     --app-name="SocietyPal Local" \
     --app-url=https://societypal.test \
     --admin-email=admin@societypal.test \
     --admin-password=SecurePassword123
   
   # OR run specific artisan commands if available
   herd php artisan migrate --seed
   herd php artisan db:seed --class=AdminSeeder
   ```

6. **Fill installation details (Manual/Web Interface):**

**🏷️ Tag Instruct-User 👤** CodeCanyon Web Installation:

   ```bash
   # Database Configuration (for installer form):
   # Database Host: 127.0.0.1
   # Database Port: 3306  
   # Database Name: societypal_local
   # Database Username: root
   # Database Password: zaj123
   
   # Application Configuration:
   # App Name: SocietyPal Local
   # App URL: https://societypal.test
   # Admin Email: admin@societypal.test
   # Admin Password: [Choose secure password]
   ```

**🏷️ Tag Instruct-User 👤** Required Manual Steps:

1. Navigate to https://societypal.test/install in your browser
2. Complete the installation wizard by filling out the forms
3. Click through each installation step until completion
4. Verify successful installation by accessing the dashboard

7. **Post-installation verification (Non-Interactive | AI Agent Compatible):**
   ```bash
   echo "📋 Installation verification checklist:"
   
   # Check database tables exist
   herd php artisan migrate:status
   echo "□ Database connection successful"
   
   # Verify admin user exists
   herd php artisan tinker --execute="echo App\Models\User::where('email', 'admin@societypal.test')->exists() ? 'Admin user exists' : 'Admin user missing';"
   echo "□ Admin user created"
   
   # Test application access
   curl -s https://societypal.test | grep -q "login\|dashboard" && echo "□ Can access application"
   
   # Verify installation completed
   [ -f storage/installed ] && echo "□ Installation completed" || echo "⚠️ Installation file not found"
   
   echo "□ Installation process complete"
   ```

**Expected Result:** CodeCanyon application installed and running locally at https://societypal.test.

---

## ✅ **Phase 1 Complete**

You have successfully completed the project setup and configuration:

**✅ Accomplished:**
- GitHub project repository created with proper branching strategy
- Local project structure organized with Admin-Local directories
- CodeCanyon application extracted and integrated
- Universal .gitignore created for all deployment scenarios
- Local development site configured at https://societypal.test
- Environment files created for all deployment stages (local, staging, production)
- Local database created and CodeCanyon application installed

**📁 Project Structure:**
```
SocietyPalApp-Root/
├─ Admin-Local/              # Project organization (your custom work)
├─ app/                      # Laravel application code
├─ config/                   # Configuration files
├─ public/                   # Public web assets
├─ .env.local               # Local development environment
├─ .env.staging             # Staging environment
├─ .env.production          # Production environment
├─ .gitignore               # Universal ignore rules
└─ README.md                # Repository documentation
```

---

**Next Phase:** [Phase 2: Pre-Deployment Preparation](../2-Subsequent-Deployment/README.md)
