# Step 14: Run Local & CodeCanyon Installation

**Start local development and complete CodeCanyon application installation**

> 📋 **Analysis Source:** V2 Amendment 9.2 - V1 had nothing, taking V2 Amendment entirely
>
> 🎯 **Purpose:** Launch local development environment and complete CodeCanyon installation process

---

## Legend for Tags

-   **`🏷️ Tag Instruct-User 👤`** = Manual task for human operator  
    ➡️ AI agent must follow ### **📋 Mandatory Confirmation Process** to ask the user to complete it

-   **No tag** = AI can execute or verify directly

---

## ℹ️ Pre-Installation Checks & Troubleshooting

**0. Verify Environment & Services**

0.1 Check PHP binary and extensions:

```bash
which php
php -v
php -m | grep -E "(pdo_mysql|curl|openssl|mbstring)"
```

0.2 **🏷️ Tag Instruct-User 👤** Verify Herd services via GUI:

1. Open Herd App → **Services** tab
2. Ensure **MySQL** is ON (green) and port is **3306**
3. Verify other required services are ON

0.3 Test database connection:

```bash
mysql -u root -h 127.0.0.1 -P 3306 -e "SELECT 'Database OK' as status;"
```

0.4 Test site accessibility:

```bash
curl -I https://societypal.test
```

0.5 **🏷️ Tag Instruct-User 👤** Trust SSL certificate:

1. Visit https://societypal.test → **Not secure → Certificate → View Certificate**
2. Drag to Keychain Access → Double-click → **Trust → Always Trust**

####

-   SAY as a note to Human: for first time setup of codecanyon apps, you may have errors on home page and maybe u should rather visit /install page - whcih is normal for codecanyon apps first time setup. Also the logs may say (The Laravel logs show the expected errors confirming the application is running but not yet fully installed:

"Error in Translatable fallback: Attempt to read property "locale" on null"
"Attempt to read property "landing_site_type" on null"))

### Additional Troubleshooting Tools

0.6 Check Herd-managed services (unofficial - identifiers may change):

```bash
launchctl list | grep -i herd
echo "=== Herd LaunchAgents Above (Unofficial) ==="
```

0.7 **🏷️ Tag Instruct-User 👤** Access Herd logs via GUI:

1. **Herd App → Logs** (Primary log viewer)
2. **Select your site** from dropdown
3. **Filter by error level** if needed

0.8 CLI Log Access:

```bash
# Laravel application logs (more comprehensive view)
tail -n 100 storage/logs/laravel.log

# Check for recent errors with context
tail -n 50 storage/logs/laravel.log | grep -A5 -B5 -i error

# System-level Herd logs (macOS)
tail -f ~/Library/Application\ Support/Herd/Log/herd.log
```

---

## 1. Set Up Environment & Application Key

1.1 Copy local environment to active:

```bash
cp .env.local .env
```

1.2 Generate application key:

```bash
php artisan key:generate
```

---

## 2. Clear & Rebuild Laravel Caches

2.1 Clear all caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 3. Set Laravel Permissions (Required for CodeCanyon Apps)

**🔍 Why These Permissions Are Needed:**

CodeCanyon applications require write access to specific Laravel directories to:

-   Store uploaded files, cache, sessions, logs
-   Write configuration during installation
-   Create temporary files and assets
-   Manage user uploads and application data

**📋 Permission Requirements:**

**When to Apply:**

-   ✅ **Before Installation** - Apply these permissions BEFORE running the CodeCanyon installer
-   ✅ **Permanent** - Keep these permissions for ongoing application operation
-   ✅ **After Updates** - Reapply if CodeCanyon app updates reset permissions

    3.1 Apply comprehensive Laravel permissions:

```bash
# Core Laravel directories (always required)
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# CodeCanyon-specific directories (if they exist)
chmod -R 775 public/user-uploads/ 2>/dev/null || echo "Note: public/user-uploads/ doesn't exist yet"
chmod -R 775 public/uploads/ 2>/dev/null || echo "Note: public/uploads/ doesn't exist yet"

# Verify permissions were applied
echo "✅ Checking applied permissions:"
ls -la storage/ | head -5
ls -la bootstrap/cache/ | head -3
```

3.2 Set proper ownership (macOS/Herd specific):

```bash
# Ensure current user owns the files
chown -R $(whoami):staff storage/ bootstrap/cache/
[ -d "public/user-uploads" ] && chown -R $(whoami):staff public/user-uploads/
[ -d "public/uploads" ] && chown -R $(whoami):staff public/uploads/
```

3.3 **🏷️ Tag Instruct-User 👤** Fix any permission errors during installation:

If the CodeCanyon installer shows permission errors (like the `storage/app/` error):

1. **Note the specific directory** mentioned in the error
2. **Apply 775 permissions** to that directory:
    ```bash
    chmod -R 775 [directory-from-error]
    ```
3. **Click "Check Permission Again"** in the installer
4. **Continue** with the installation process

**📚 Permission Reference:**

-   `775` = Owner: read/write/execute, Group: read/write/execute, Others: read/execute
-   Required for web server to write files while maintaining security
-   Herd runs under your user account, so this permission model is safe for local development

**🔒 Production Security Note:** These `775` permissions are appropriate for **local development only**. For production servers, use `755` permissions for enhanced security. See [Laravel File Permissions Security Guide](../../99-Understand/Laravel_File_Permissions_Security_Guide.md) for complete environment-specific permission guidelines.

---

## 4. Start Required Services

4.1 **🏷️ Tag Instruct-User 👤** Turn services ON in Herd GUI:

1. Herd App → **Services** tab
2. Toggle **MySQL** ON (green status)
3. Enable any other needed services

4.2 Confirm via CLI (unofficial - names may vary):

```bash
launchctl list | grep -i herd
```

4.3 Verify DB connection:

```bash
php artisan migrate:status
```

---

## 5. Access Application & Installer

5.1 **🏷️ Tag Instruct-User 👤** Open site via Herd GUI:

1. **Herd App → Sites** tab → Find `societypal.test` → Click **Open**
2. This handles SSL trust automatically

5.2 Or open installer URL directly:

```bash
open "https://societypal.test/install"
```

5.3 Verify site accessibility:

```bash
curl -I https://societypal.test
echo "🌐 Visit: https://societypal.test"
echo "🔍 Expected: CodeCanyon installer or application homepage"
```

---

## 6. (Optional) CLI Installation
**🔒 Important Note:** This step is optional and can be used if user had issues doing the install manually using site frontend (step 7 below) & ONLY after confirming with user.


6.1 Check if CLI install command exists:

```bash
php artisan list | grep install
```

6.2 Run if available (adjust to actual package):

```bash
php artisan install:app \
  --db-host=127.0.0.1 \
  --db-port=3306 \
  --db-name=societypal_local \
  --db-user=root \
  --db-password="" \
  --app-name="SocietyPal Local" \
  --app-url=https://societypal.test \
  --admin-email=admin@societypal.test \
  --admin-password=SecurePassword123
```

6.3 Otherwise run migrations/seeds:

```bash
php artisan migrate --seed
php artisan db:seed --class=AdminSeeder
```

**⚠️ Note:** Not all CodeCanyon packages provide CLI installation. Check package documentation first.

---

## 7. Manual Web-Based Installation

**🚨 CRITICAL: Herd Database Configuration**

**⚠️ Common Error Fix:** If you get `SQLSTATE[HY000] [2002] No such file or directory`, use the correct database configuration below: Use `127.0.0.1` instead of `localhost`

**🎯 Why This Happens:**

-   **Problem:** `localhost` tries to use Unix socket: `/tmp/mysql.sock`
-   **Reality:** Herd MySQL runs on TCP/IP, NOT Unix sockets
-   **Solution:** Use `127.0.0.1` instead of `localhost`

**📝 Note About CodeCanyon Apps:** CodeCanyon applications are thoroughly tested and validated before being published to the marketplace. If you encounter installation issues, they're typically environment-specific (like this Herd socket issue) rather than application bugs.

7.1 **🏷️ Tag Instruct-User 👤** Complete installer form **with these EXACT database settings:**

-   **Database Host:** `127.0.0.1` ⚠️ **NOT** `localhost`
-   **Database Port:** `3306`
-   **Database Name:** `societypal_local`
-   **Database Username:** `root`
-   **Database Password:** _(leave completely blank - Herd default)_
-   **App Name:** SocietyPal Local
-   **App URL:** https://societypal.test
-   **Admin Email:** admin@societypal.test
-   **Admin Password:** secure password of choice

    7.2 **🏷️ Tag Instruct-User 👤** Finalize in browser:

1. Navigate to https://societypal.test/install in your browser
2. Fill out required details using credentials above
3. Click through each installation step until completion
4. Confirm application loads without installer prompt

---

## 8. Post-Installation Verification

8.1 Check database tables exist:

```bash
php artisan migrate:status
echo "□ Database connection successful"
```

8.2 Verify admin user exists (if applicable):

```bash
php artisan tinker --execute="echo App\\Models\\User::where('email', 'admin@societypal.test')->exists() ? 'Admin user exists' : 'Admin user missing';"
echo "□ Admin user verification"
```

8.3 Test application access:

```bash
curl -s https://societypal.test | grep -qE "login|dashboard" && echo "□ Can access application"
```

8.4 Check for common installation completion indicators:

```bash
[ -f storage/installed ] && echo "□ Installation file found" || echo "ℹ️ No installation file (not all packages create this)"
[ -f .env ] && grep -q "APP_INSTALLED=true" .env && echo "□ APP_INSTALLED flag found" || echo "ℹ️ No APP_INSTALLED flag"
echo "□ Installation process verification complete"
```

---

## 9. Diagnostic Info Collection

9.1 Generate comprehensive debug report:

```bash
echo "=== HERD DEBUG REPORT ==="
echo "Date: $(date)"
echo "PHP Binary: $(which php)"
echo "PHP Version: $(php -v | head -1)"
echo "Herd LaunchAgents: $(launchctl list | grep -i herd)"
echo "Laravel Version: $(php artisan --version)"
echo "Environment: $(cat .env | grep -E '(APP_ENV|DB_)')"
echo "Recent Laravel Logs:"
tail -50 storage/logs/laravel.log
echo "=== END REPORT ==="
```

---

## 🛠️ Common Issues & Solutions

### ERROR: DB Error: SQLSTATE[HY000] [2002] No such file or directory

🔍 Root Cause Analysis
The error SQLSTATE[HY000] [2002] No such file or directory is a classic Herd MySQL socket issue on macOS. Here's exactly what's happening:

The Problem:
CodeCanyon installer is trying to use localhost for database connection
localhost attempts to use Unix socket: /tmp/mysql.sock
Herd MySQL runs on TCP/IP, NOT Unix sockets
This creates a "file not found" error because the socket file doesn't exist
The Solution:
Use 127.0.0.1 instead of localhost in the database configuration

### Database Connection Issues

8.2 Check if MySQL is running (unofficial - service names may change):

```bash
launchctl list | grep -i herd | grep -i mysql
```

8.3 Test direct connection with correct credentials:

```bash
mysql -u root -h 127.0.0.1 -P 3306 -e "SHOW DATABASES;"
```

8.4 **🏷️ Tag Instruct-User 👤** Verify MySQL via Herd GUI:

1. **Open Herd App → Services tab**
2. **Check MySQL toggle is ON (green)**
3. **Click gear ⚙️ icon to verify port 3306 and credentials**
4. **Note:** Herd MySQL default is root user with NO password

### CodeCanyon Installation Database Configuration Issues

**🚨 PRIMARY ISSUE: Herd Socket Error During Installation:** `SQLSTATE[HY000] [2002] No such file or directory`

**🎯 This is the #1 Most Common Herd Setup Issue**

**Problem:** The error `SQLSTATE[HY000] [2002] No such file or directory` is a classic Herd/macOS MySQL socket issue. The installer is trying to use a Unix socket instead of TCP/IP connection.

**✅ SOLUTION - Use TCP/IP Configuration:**
When filling out the database configuration step in the CodeCanyon installer, use:

-   **Database Host:** `127.0.0.1` ⚠️ **CRITICAL - NOT `localhost`**
-   **Database Port:** `3306`
-   **Database User:** `root`
-   **Database Password:** (leave completely empty)
-   **Database Name:** `societypal_local` (or your chosen database name)

**🔍 Why This Happens:**

-   `localhost` tries to use Unix socket: `/tmp/mysql.sock`
-   `127.0.0.1` forces TCP/IP connection to Herd's MySQL
-   Herd MySQL runs on TCP/IP, not Unix sockets
-   The app works fine with other environments because they typically use TCP/IP by default

**✅ Verification:**

```bash
# Test the exact connection the installer will use
mysql -u root -h 127.0.0.1 -P 3306 -e "SELECT 'Connection OK' as status;"
```

**📝 Important Notes:**

-   ✅ **CodeCanyon apps are thoroughly market-tested** - installation issues are typically environment-specific
-   ✅ **This is NOT an application bug** - it's a macOS/Herd MySQL configuration issue
-   ✅ **Pre-existing tables are fine** - CodeCanyon installers handle existing data appropriately

### Permission Issues

8.5 Fix Laravel storage permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):staff storage bootstrap/cache
```

### Site Access Issues

8.6 **🏷️ Tag Instruct-User 👤** Re-link site if needed:

1. **Check Herd App → Sites tab** for societypal.test
2. **If missing, navigate to project directory and run:** `herd link`

8.7 Check HTTPS certificate and trust status:

```bash
curl -k -I https://societypal.test
```

### Service Restart

8.8 **🏷️ Tag Instruct-User 👤** Restart via Herd GUI (Recommended):

1. **Open Herd App → Services tab**
2. **Toggle MySQL OFF then ON**
3. **Wait for green status**

8.9 CLI Alternative (Unofficial - service names may change):

```bash
# Find actual service name
launchctl list | grep -i herd | grep -i mysql

# Stop and start MySQL via launchctl (example name)
launchctl stop com.beyondco.herd.mysql
sleep 2
launchctl start com.beyondco.herd.mysql

# Verify service is running
launchctl list | grep -i herd | grep -i mysql
```

---

## 10. Capture Installation Credentials & Configuration

**🔐 CRITICAL: Secure Credential Storage**

After successful installation, the CodeCanyon installer typically displays superadmin login credentials. **You MUST capture these immediately** as they may not be shown again.

10.1 **🏷️ Tag Instruct-User 👤** Copy installation completion details:

1. **Screenshot the completion page** showing superadmin credentials
2. **Record the following information:**

    - Superadmin Email
    - Superadmin Password
    - Any license keys or tokens
    - Installation completion timestamp

    10.2 **Execute Universal Credential Capture Script:**

```bash
# Run the universal credential capture script
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/Post-Installation-Analysis/2-capture-credentials.sh

echo "✅ Credential capture completed using universal script"
```

**🎯 Script Features:**
- **Auto-Detection**: Automatically detects project name, URL, database name from `.env`
- **Interactive Collection**: Prompts for superadmin credentials securely
- **Secure Storage**: Creates encrypted JSON with 600 permissions
- **Environment Backup**: Backs up current `.env` file
- **Git Security**: Automatically adds sensitive files to `.gitignore`
- **Universal Design**: Works with any Laravel project, not just SocietyPal

**📁 Files Created:**
- `Admin-Local/1-CurrentProject/installation-credentials.json` (secure credentials)
- `Admin-Local/1-CurrentProject/.env.local.backup` (environment backup)
- Updated `.gitignore` with security exclusions

**📖 Script Documentation:**
```bash
cat Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/Post-Installation-Analysis/README.md
```

---

## 11. Post-Installation Security Lockdown

**🔒 CRITICAL SECURITY STEP**

The installation process required temporary elevated permissions (775). Now we must implement appropriate security lockdown using the production-ready permission scripts available in this project.

**📋 Available Permission Scripts:**

This project includes pre-built, production-ready permission management scripts located at:

```
Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/
├── 1-permissions-pre-install.sh      # Before CodeCanyon install/update
├── 2-permissions-post-install.sh     # After CodeCanyon install/update
├── permissions-emergency-security.sh # Emergency security recovery
└── README.md                         # Complete script documentation
```

**🎯 Script Features:**

-   **Environment Auto-Detection**: Automatically detects local/staging/production from `.env`
-   **Smart Permissions**: Different permission levels for different environments
-   **Security Audits**: Built-in checks for dangerous 777 permissions
-   **Logging**: Detailed logs in `install-permissions.log` and `emergency-security.log`
-   **Interactive/Non-Interactive Modes**: `--interactive` or `--auto` flags

    11.1 **Execute Post-Installation Security Lockdown:**

```bash
# Run the production-ready post-installation security script
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/2-permissions-post-install.sh

echo "✅ Post-installation security lockdown completed using production script"
```

**📚 Manual Commands Reference:**

For reference, the script performs environment-appropriate security lockdown:

-   **Local Development:** Maintains 775 permissions for convenience
-   **Staging/Production:** Applies 755 permissions for security
-   **All Environments:** Secures `.env` files (600), config files (755/644)
-   **Security Audit:** Checks and fixes dangerous 777 permissions

**📖 Complete Documentation:**

For detailed script options, modes, and troubleshooting, see:

```bash
cat Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/README.md
```

**🚨 Emergency Recovery:**

If you need immediate security recovery, use:

```bash
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/permissions-emergency-security.sh --auto
```

**🔗 Reference Guide:**

For complete permission theory and manual implementation details, see: [Laravel File Permissions Security Guide](../../99-Understand/Laravel_File_Permissions_Security_Guide.md)

---

## 12. Post-Installation Analysis & Documentation

**📊 CRITICAL: Document Installation Changes**

Understanding what the CodeCanyon installation process modified helps with future updates, troubleshooting, and deployment.

12.1 **Execute Universal Post-Installation Analysis Script:**

```bash
# Run the universal post-installation analysis script
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/Post-Installation-Analysis/1-analyze-file-changes.sh

echo "✅ Post-installation analysis completed using universal script"
```

**🎯 Analysis Script Features:**
- **Auto-Detection**: Dynamically reads project configuration from `.env` file
- **Database Analysis**: Tables, records, schema structure with universal MySQL connections
- **File System Analysis**: New directories, modified configurations, permission audits
- **Application Snapshot**: Laravel version, environment, routes, packages
- **Universal Design**: Works with any Laravel project by auto-detecting configuration
- **Comprehensive Logging**: Detailed analysis with timestamps and project context

**📁 Files Generated:**
- `Admin-Local/1-CurrentProject/post-install-analysis.md` (detailed technical analysis)
- `Admin-Local/1-CurrentProject/installation-changelog.md` (executive summary for stakeholders)

**📖 Script Documentation:**
```bash
cat Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/Post-Installation-Analysis/README.md
```

**⚙️ Manual Override (if needed):**
Both generated files are fully editable if you need to add project-specific details or corrections after the automated analysis.

---

## ✅ **Phase 1 Complete**

**Expected Outcome:** CodeCanyon application installed and running locally at https://societypal.test with verified database, admin user, and UI accessibility.

**✅ Accomplished:**

-   GitHub project repository created with proper branching strategy
-   Local project structure organized with Admin-Local directories
-   CodeCanyon application extracted and integrated
-   Universal .gitignore created for all deployment scenarios
-   Local development site configured at https://societypal.test
-   Environment files created for all deployment stages (local, staging, production)
-   Local database created and CodeCanyon application installed

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
