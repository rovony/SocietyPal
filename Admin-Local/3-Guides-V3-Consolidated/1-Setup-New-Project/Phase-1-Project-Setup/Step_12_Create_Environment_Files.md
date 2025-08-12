# Step 12: Create Environment Files

**Setup environment configuration files for all deployment stages**

> 📋 **Analysis Source:** V2 Step 9 - V1 had nothing, taking V2 entirely
> 
> 🎯 **Purpose:** Create environment configurations for local, staging, and production deployments

---

## **Environment Configuration Setup**

1. **Check existing environment files:**
   ```bash
   ls -la .env*
   # Should show .env.example and possibly .env
   ```

2. **Create local environment file:**
   ```bash
   cp .env.example .env.local
   ```

3. **Configure local environment:**
   ```bash
   # Edit .env.local with Herd MySQL details
   cat > .env.local << 'EOF'
   APP_NAME="SocietyPal Local"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=https://societypal.test
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=societypal_local
   DB_USERNAME=root
   DB_PASSWORD=zaj123
   
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   
   MAIL_MAILER=smtp
   MAIL_HOST=localhost
   MAIL_PORT=2525
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="dev@societypal.test"
   MAIL_FROM_NAME="${APP_NAME}"
   EOF
   ```

4. **Create staging environment file:**
   ```bash
   cat > .env.staging << 'EOF'
   APP_NAME="SocietyPal Staging"
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://staging.societypal.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u227177893_s_zaj_socpal_d
   DB_USERNAME=u227177893_s_zaj_socpal_u
   DB_PASSWORD="V0Z^G=I2:=r^f2"
   
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=database
   
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@staging.societypal.com
   MAIL_PASSWORD=your-mail-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@staging.societypal.com"
   MAIL_FROM_NAME="${APP_NAME}"
   
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=strict
   EOF
   ```

5. **Create production environment file:**
   ```bash
   cat > .env.production << 'EOF'
   APP_NAME="SocietyPal"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://societypal.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u227177893_p_zaj_socpal_d
   DB_USERNAME=u227177893_p_zaj_socpal_u
   DB_PASSWORD="t5TmP9$[iG7hu2eYRWUIWH@IRF2"
   
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=database
   
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@societypal.com
   MAIL_PASSWORD=your-mail-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@societypal.com"
   MAIL_FROM_NAME="${APP_NAME}"
   
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=strict
   EOF
   ```

6. **Generate unique application keys:**
   ```bash
   # For local environment
   cp .env.local .env
   php artisan key:generate
   # Copy the generated key to .env.local
   
   # Generate keys for other environments manually
   php artisan key:generate --show
   # Copy this key to .env.staging APP_KEY=
   
   php artisan key:generate --show
   # Copy this key to .env.production APP_KEY=
   ```

**Expected Result:** Environment files created for local, staging, and production with unique application keys.

---

**Next Step:** [Step 13: Create Local Database](Step_13_Create_Local_Database.md)
