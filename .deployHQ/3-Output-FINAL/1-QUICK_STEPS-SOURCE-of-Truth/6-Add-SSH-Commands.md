# 6. Add SSH Commands

## 6.1 Phase A: Pre-Deployment Commands (Before)
1. Go to Deployment → SSH Commands Before
2. Copy this command block:

```bash
#!/bin/bash
set -e

# A01: System Check & Environment Setup
echo "=== Pre-Deployment System Check ==="
php --version
composer --version

# Ensure shared directories exist
mkdir -p ../shared/storage/{app/public,framework/{cache,sessions,views},logs}
mkdir -p ../shared/backups

# A02: Backup Current Release  
echo "=== Backing Up Current Release ==="
if [ -L "../current" ] && [ -d "../current" ]; then
  BACKUP_DIR="../shared/backups/release-backup-$(date +%Y%m%d_%H%M%S)"
  mkdir -p "$BACKUP_DIR"
  cp -r ../current/* "$BACKUP_DIR/" 2>/dev/null || true
  echo "✅ Current release backed up to: $BACKUP_DIR"
else
  echo "ℹ️ No current release to backup (first deployment)"
fi

# A03: Database Backup
echo "=== Database Backup ==="
if [ -f "../shared/.env" ]; then
  set +e  # Allow errors for optional backup
  source "../shared/.env" 2>/dev/null
  if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    BACKUP_FILE="../shared/backups/db-backup-$(date +%Y%m%d_%H%M%S).sql"
    echo "Creating database backup..."
    if mysqldump -h"${DB_HOST:-localhost}" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null; then
      echo "✅ Database backed up to: $BACKUP_FILE"
    else
      echo "⚠️ Database backup failed (continuing deployment)"
    fi
  else
    echo "ℹ️ Database credentials not found, skipping backup"
  fi
  set -e
else
  echo "ℹ️ No .env file found, skipping database backup"
fi

# A04: Enter Maintenance Mode
echo "=== Entering Maintenance Mode ==="
if [ -f "../current/artisan" ]; then
  cd ../current
  if php artisan down --secret="deploying" --render="errors::503" 2>/dev/null; then
    echo "✅ Application in maintenance mode"
  else
    echo "⚠️ Failed to enter maintenance mode (continuing)"
  fi
  cd - > /dev/null
else
  echo "ℹ️ No current application (first deployment)"
fi

echo "✅ Pre-deployment checks completed"
```

## 6.2 Phase B: Pre-Release Commands (After Upload, Before Release)
1. Go to Deployment → SSH Commands After Upload, Before Release
2. Copy this command block:

```bash
#!/bin/bash
set -e

# B01: Link Shared Resources
echo "=== Linking Shared Resources ==="
ln -nfs ../shared/.env .env
if [ -d storage ]; then
  rm -rf storage
fi
ln -nfs ../shared/storage storage
echo "✅ Shared resources linked"

# B02: File Permissions
echo "=== Setting File Permissions ==="
find . -type f -exec chmod 644 {} \; 2>/dev/null || true
find . -type d -exec chmod 755 {} \; 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo "✅ File permissions set"

# B03: Laravel Optimization
echo "=== Laravel Database & Cache ==="
set +e  # Allow errors for some commands

# Run migrations
if php artisan migrate --force --no-interaction 2>/dev/null; then
  echo "✅ Database migrations completed"
else
  echo "⚠️ Database migrations failed or not needed"
fi

# Cache optimization
php artisan config:cache 2>/dev/null && echo "✅ Config cached" || echo "⚠️ Config cache failed"
php artisan route:cache 2>/dev/null && echo "✅ Routes cached" || echo "⚠️ Route cache failed"  
php artisan view:cache 2>/dev/null && echo "✅ Views cached" || echo "⚠️ View cache failed"

# Storage link
if php artisan storage:link 2>/dev/null; then
  echo "✅ Storage link created"
else
  echo "ℹ️ Storage link already exists or failed"
fi

set -e
```

## 6.3 Phase C: Post-Deployment Commands (After)
1. Go to Deployment → SSH Commands After
2. Copy this command block:

```bash
#!/bin/bash
set -e

# B04: Activate New Release
echo "=== Activating New Release ==="
cd ..
CURRENT_RELEASE=$(basename "$(pwd)/releases/%RELEASE%")
echo "Activating release: $CURRENT_RELEASE"

# Atomic symlink switch
ln -nfs "releases/%RELEASE%" current
echo "✅ Current symlink updated"

# Setup public_html for shared hosting (if needed)
if [ ! -L public_html ] && [ ! -d public_html ]; then
  ln -s current/public public_html
  echo "✅ public_html symlink created"
elif [ -L public_html ]; then
  echo "ℹ️ public_html symlink already exists"
else
  echo "ℹ️ public_html directory exists (not symlinking)"
fi

# B05: Exit Maintenance Mode
echo "=== Exiting Maintenance Mode ==="
cd current
if php artisan up 2>/dev/null; then
  echo "✅ Application is now live"
else
  echo "⚠️ Failed to exit maintenance mode (check manually)"
fi

# B06: Cleanup Old Releases
echo "=== Cleaning Up Old Releases ==="
cd ../releases
RELEASES_TO_KEEP=3
TOTAL_RELEASES=$(ls -1 | wc -l)

if [ "$TOTAL_RELEASES" -gt "$RELEASES_TO_KEEP" ]; then
  RELEASES_TO_DELETE=$((TOTAL_RELEASES - RELEASES_TO_KEEP))
  echo "Keeping $RELEASES_TO_KEEP releases, removing $RELEASES_TO_DELETE old releases"
  ls -t | tail -n +$((RELEASES_TO_KEEP + 1)) | xargs rm -rf 2>/dev/null || true
  echo "✅ Old releases cleaned up"
else
  echo "ℹ️ Only $TOTAL_RELEASES releases found, no cleanup needed"
fi

echo "🎉 Deployment completed successfully!"
echo "📊 Active release: %RELEASE%"
echo "🌐 Site should be live at your domain"
```

## 6.3 Command Settings
1. Set command timeout: 10 minutes
2. Enable error handling
3. Save all SSH commands

## 6.4 SSH Commands Ready
✅ Pre-deployment commands added
✅ Post-deployment commands added
✅ Deployment automation configured
✅ Ready for first deployment

**Next: 7-Deploy.md**
