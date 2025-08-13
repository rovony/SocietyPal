# Step 14: Run Local & CodeCanyon Installation

**Start local development and complete CodeCanyon application installation**

> 📋 **Analysis Source:** V2 Amendment 9.2 - V1 had nothing, taking V2 Amendment entirely
> 
> 🎯 **Purpose:** Launch local development environment and complete CodeCanyon installation process

---

## Legend for Tags

- **`🏷️ Tag Instruct-User 👤`** = Manual task for human operator  
  ➡️ AI agent must follow ### **📋 Mandatory Confirmation Process** to ask the user to complete it

- **No tag** = AI can execute or verify directly

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
```

---

## 3. Start Required Services

3.1 **🏷️ Tag Instruct-User 👤** Turn services ON in Herd GUI:
1. Herd App → **Services** tab
2. Toggle **MySQL** ON (green status)
3. Enable any other needed services

3.2 Confirm via CLI (unofficial - names may vary):
```bash
launchctl list | grep -i herd
```

3.3 Verify DB connection:
```bash
php artisan migrate:status
```

---

## 4. Access Application & Installer

4.1 **🏷️ Tag Instruct-User 👤** Open site via Herd GUI:
1. **Herd App → Sites** tab → Find `societypal.test` → Click **Open**
2. This handles SSL trust automatically

4.2 Or open installer URL directly:
```bash
open "https://societypal.test/install"
```

4.3 Verify site accessibility:
```bash
curl -I https://societypal.test
echo "🌐 Visit: https://societypal.test"
echo "🔍 Expected: CodeCanyon installer or application homepage"
```

---

## 5. (Optional) CLI Installation

5.1 Check if CLI install command exists:
```bash
php artisan list | grep install
```

5.2 Run if available (adjust to actual package):
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

5.3 Otherwise run migrations/seeds:
```bash
php artisan migrate --seed
php artisan db:seed --class=AdminSeeder
```

**⚠️ Note:** Not all CodeCanyon packages provide CLI installation. Check package documentation first.

---

## 6. Manual Web-Based Installation

6.1 **🏷️ Tag Instruct-User 👤** Complete installer form:
- **Database Host:** 127.0.0.1
- **Port:** 3306
- **Name:** societypal_local
- **Username:** root
- **Password:** _(leave blank - Herd default)_
- **App Name:** SocietyPal Local
- **App URL:** https://societypal.test
- **Admin Email:** admin@societypal.test
- **Admin Password:** secure password of choice

6.2 **🏷️ Tag Instruct-User 👤** Finalize in browser:
1. Navigate to https://societypal.test/install in your browser
2. Fill out required details using credentials above
3. Click through each installation step until completion
4. Confirm application loads without installer prompt

---

## 7. Post-Installation Verification

7.1 Check database tables exist:
```bash
php artisan migrate:status
echo "□ Database connection successful"
```

7.2 Verify admin user exists (if applicable):
```bash
php artisan tinker --execute="echo App\\Models\\User::where('email', 'admin@societypal.test')->exists() ? 'Admin user exists' : 'Admin user missing';"
echo "□ Admin user verification"
```

7.3 Test application access:
```bash
curl -s https://societypal.test | grep -qE "login|dashboard" && echo "□ Can access application"
```

7.4 Check for common installation completion indicators:
```bash
[ -f storage/installed ] && echo "□ Installation file found" || echo "ℹ️ No installation file (not all packages create this)"
[ -f .env ] && grep -q "APP_INSTALLED=true" .env && echo "□ APP_INSTALLED flag found" || echo "ℹ️ No APP_INSTALLED flag"
echo "□ Installation process verification complete"
```

---

## 8. Diagnostic Info Collection

8.1 Generate comprehensive debug report:
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

## ✅ **Phase 1 Complete**

**Expected Outcome:** CodeCanyon application installed and running locally at https://societypal.test with verified database, admin user, and UI accessibility.

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
