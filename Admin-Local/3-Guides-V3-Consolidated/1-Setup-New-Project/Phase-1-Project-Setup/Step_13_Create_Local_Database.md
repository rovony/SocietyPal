# Step 13: Create Local Database

**Setup local MySQL database in Herd for development**

> 📋 **Analysis Source:** V2 Amendment 9.1 - V1 had nothing, taking V2 Amendment entirely
> 
> 🎯 **Purpose:** Create local database for CodeCanyon application development

---

## **Local Database Setup in Herd MySQL**

1. **Verify Herd MySQL is running:**
   ```bash
   # Check if Local_MySQL service is running in Herd
   echo "🔍 Verifying Herd MySQL service..."
   echo "✅ Ensure Herd app is running and Local_MySQL service is active"
   ```

2. **Connect to MySQL via CLI:**
   ```bash
   # Connect to MySQL CLI 
   # Herd symlinks the mysql CLI to your PATH
   mysql -u root -h 127.0.0.1 -P 3306 -p
   # When asked for password: enter 'zaj123' (or password you set in Herd)
   
   # Success will show:
   # Welcome to the MySQL monitor.  Commands end with ; or \g.
   # mysql> 
   ```

3. **Create project database:**
   ```bash
   # In MySQL CLI, create database for SocietyPal
   CREATE DATABASE societypal_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   # Confirm database created 
   SHOW DATABASES;
   # Look for 'societypal_local' in the list
   
   # Exit MySQL CLI
   exit;
   ```

4. **Update local environment file:**
   ```bash
   # Update .env.local with correct database name
   sed -i '' 's/DB_DATABASE=laravel/DB_DATABASE=societypal_local/' .env.local
   
   # Verify the change
   grep "DB_DATABASE" .env.local
   # Should show: DB_DATABASE=societypal_local
   ```

**Expected Result:** Local database created and environment file updated with correct database name.

---

**Next Step:** [Step 14: Run Local & CodeCanyon Installation](Step_14_Run_Local_Installation.md)
