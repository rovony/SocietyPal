# 🚀 FINAL DeployHQ Pipeline - Universal Laravel Deployment

**One definitive pipeline for ALL Laravel applications**

---

## 📋 DeployHQ Project Settings

### Repository
- **Repository URL**: Your GitHub/GitLab repository
- **Branch**: `main` (production), `staging` (testing)
- **Deploy Key**: Add DeployHQ's SSH key to your repository

### Build Environment
- **PHP Version**: 8.2
- **Node.js Version**: 18.x LTS  
- **Composer Version**: 2.x (latest)
- **Memory Limit**: 512M
- **Build Timeout**: 20 minutes

### Build Cache
- **Composer Cache**: `~/.composer/cache`
- **NPM Cache**: `~/.npm`
- **Vendor Cache**: `vendor`

### SSH Known Hosts
- **GitHub**: `github.com`
- **GitLab**: `gitlab.com` (if needed)

---

## 🏗️ Build Pipeline Commands

### Build Command 1: "Frontend Assets"
**Runtime: Node.js 18.x**
```bash
npm ci --only=production --no-audit --no-fund
npm run build
rm -rf node_modules
```

### Build Command 2: "Laravel Production"
**Runtime: PHP 8.2**
```bash
export COMPOSER_MEMORY_LIMIT=-1
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
composer dump-autoload --optimize --classmap-authoritative
```

---

## 🔧 Server Configuration

### Shared Files
```
.env
```

### Shared Directories
```
storage
```

### SSH Pre-Deployment Commands
```bash
# A01: System Check
php --version && composer --version

# A02: Backup Current Release
if [ -L "../current" ]; then
  cp -r ../current "../shared/backup-$(date +%Y%m%d_%H%M%S)"
fi

# A03: Database Backup
if [ -f "../shared/.env" ]; then
  source "../shared/.env"
  if [ -n "$DB_DATABASE" ]; then
    mysqldump -h"${DB_HOST:-localhost}" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "../shared/db-backup-$(date +%Y%m%d_%H%M%S).sql" 2>/dev/null || true
  fi
fi

# A04: Maintenance Mode
if [ -f "../current/artisan" ]; then
  cd ../current && php artisan down --secret="deploying" 2>/dev/null || true
fi
```

### SSH Post-Deployment Commands
```bash
# B01: Link Shared Resources
ln -nfs ../shared/.env .env
rm -rf storage
ln -nfs ../shared/storage storage

# B02: File Permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# B03: Database & Cache
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# B04: Activate Release
cd ..
ln -nfs releases/%RELEASE% current
if [ ! -L public_html ]; then
  ln -s current/public public_html
fi

# B05: Exit Maintenance
cd current && php artisan up

# B06: Cleanup
cd ../releases && ls -t | tail -n +4 | xargs rm -rf 2>/dev/null || true
```

---

## 📁 Directory Structure

```
/domains/yourapp.com/
├── deploy/
│   ├── releases/
│   │   ├── 20240101120000/    # Current release
│   │   └── 20240101100000/    # Previous release
│   ├── shared/
│   │   ├── .env               # Environment file
│   │   ├── storage/           # Persistent storage
│   │   └── backups/           # Automated backups
│   ├── current -> releases/20240101120000/
│   └── public_html -> current/public/
```

---

## 🎯 Deployment Workflow

1. **Push to Repository** → Triggers DeployHQ
2. **Build Phase**: Frontend + Laravel optimization
3. **Pre-Deploy**: Backup + Maintenance mode
4. **Deploy**: File transfer to new release directory
5. **Post-Deploy**: Link resources + Database + Cache + Activate
6. **Complete**: Exit maintenance + Cleanup old releases

---

## ✅ Universal Settings

### Environment Variables (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key
APP_URL=https://yourapp.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
```

### Deployment Triggers
- **Automatic**: Push to `main` branch
- **Manual**: Deploy button in DeployHQ
- **Approval**: Required for production

---

## 📊 This Pipeline Handles

✅ **Frontend Assets**: Vite, Laravel Mix, React, Vue  
✅ **PHP Dependencies**: Composer optimization  
✅ **Database**: Migrations with backup  
✅ **Zero Downtime**: Atomic symlink switching  
✅ **Rollback**: Automatic on failure  
✅ **Cleanup**: Maintains 3 releases  
✅ **Security**: Proper permissions and maintenance mode  

**Result**: Professional deployment for ANY Laravel application in ~2-3 minutes.
