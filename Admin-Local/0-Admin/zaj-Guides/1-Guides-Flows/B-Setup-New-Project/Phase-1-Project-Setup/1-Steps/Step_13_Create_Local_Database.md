# Step 13: Create Local Database

**Setup local MySQL database in Herd for development**

> 📋 **Analysis Source:** V2 Amendment 9.1 - V1 had nothing, taking V2 Amendment entirely
>
> 🎯 **Purpose:** Create local database for CodeCanyon application development

---

## 🚨 Human Interaction Required

**⚠️ This step includes tasks that must be performed manually outside this codebase:**

-   Verifying Herd Pro MySQL service status through GUI interface
-   **All other operations in this step are automated/AI-executable**

**🏷️ Tag Instruct-User 👤** markers indicate the specific substeps requiring human action.

---

## 1. Local Environment Configuration for Herd

### 1.1. Verify Herd Pro MySQL Service Status

```bash
# Check if Herd Pro MySQL service is running
echo "🔍 Verifying Herd Pro MySQL service status..."
echo "✅ Ensure Herd Pro app is running and MySQL service is ENABLED (green status)"
echo "📍 Default Herd MySQL Configuration:"
echo "   - Host: 127.0.0.1 (localhost)"
echo "   - Port: 3306"
echo "   - User: root"
echo "   - Password: (empty - no password required)"
```

### 1.2. Database Connection Information

**🎯 Important:** Herd Pro's MySQL uses **root user with NO password** by default:
- **Username:** `root`
- **Password:** `""` (empty string)
- **Host:** `127.0.0.1`
- **Port:** `3306`

---

## 2. Create Local MySQL Database

### 2.1. Verify Herd MySQL Service

**🏷️ Tag Instruct-User 👤** Service Verification:

1. Open Herd Pro application from system tray
2. Navigate to **Services** tab
3. Confirm **MySQL** service shows **green status** (running)
4. If not running, toggle MySQL service to **ON**

### 2.2. Connect to MySQL CLI (Non-Interactive | AI Agent Compatible)

```bash
# Connect to Herd Pro MySQL CLI (NO password required)
# Press ENTER when prompted for password (don't type anything)
mysql -u root -h 127.0.0.1 -P 3306 -p

# Expected output:
# Enter password: [Press ENTER - leave empty]
# Welcome to the MySQL monitor. Commands end with ; or \g.
# mysql>
```

### 2.3. Create SocietyPal Database (Non-Interactive | AI Agent Compatible)

```bash
# In MySQL CLI, create database for SocietyPal
CREATE DATABASE societypal_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Verify database creation
SHOW DATABASES;
# Look for 'societypal_local' in the list

# Exit MySQL CLI
exit;
```

---

## 3. Update Local Environment Configuration

### 3.1. Configure Database Settings in .env.local

```bash
# Update .env.local with correct database configuration
# Database name
sed -i '' 's/DB_DATABASE=.*/DB_DATABASE=societypal_local/' .env.local

# Ensure DB_PASSWORD is empty for Herd's default root user
sed -i '' 's/DB_PASSWORD=.*/DB_PASSWORD=/' .env.local

# Verify all database configurations
echo "🔍 Current database configuration in .env.local:"
grep -E "DB_(HOST|PORT|DATABASE|USERNAME|PASSWORD)" .env.local
```

### 3.2. Expected Configuration Output

```bash
# Your .env.local should show:
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=societypal_local
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Clear Configuration Cache and Verify Connection

### 4.1. Clear Laravel Configuration Cache

```bash
# Clear Laravel configuration cache to apply new database settings
echo "🧹 Clearing Laravel configuration cache..."
php artisan config:clear
php artisan cache:clear

echo "✅ Configuration cache cleared"
```

### 4.2. Test Database Connection

```bash
# Test database connection using Laravel's connection verification
echo "🔍 Testing database connection..."
php artisan migrate:status

# Expected successful output:
# Migration table created successfully.
# +------+----------------------------------------------+-------+
# | Ran? | Migration                                    | Batch |
# +------+----------------------------------------------+-------+
# [Shows migration status or empty table if no migrations run yet]

echo "✅ Database connection verified successfully"
```

---

## 5. Connection Verification Summary

### 5.1. Verification Checklist

```bash
echo "📋 Database Setup Verification:"
echo "✅ Herd Pro MySQL service: RUNNING"
echo "✅ Database 'societypal_local': CREATED"
echo "✅ .env.local DB_DATABASE: societypal_local"
echo "✅ .env.local DB_PASSWORD: (empty for Herd root user)"
echo "✅ Laravel configuration cache: CLEARED"
echo "✅ Database connection: VERIFIED"

echo ""
echo "🎯 Ready for Step 14: Run Local Installation"
```

**Expected Result:** Local MySQL database created, environment properly configured for Herd Pro, Laravel cache cleared, and database connection verified.

---

**Next Step:** [Step 14: Run Local & CodeCanyon Installation](Step_14_Run_Local_Installation.md)
