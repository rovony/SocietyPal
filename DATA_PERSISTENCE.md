# Data Persistence Strategy

## Goal: Zero Data Loss During Deployments

This system ensures that user-generated content and application data is NEVER lost during deployments, updates, or rollbacks.

## Shared Directory Structure

```
/var/www/societypal.com/
├─ releases/
│ ├─ 20250815-143022/ # Code release (read-only)
│ ├─ 20250815-150045/ # Code release (read-only)
│ └─ 20250815-152018/ # Code release (read-only)
├─ shared/ # PERSISTENT DATA (survives all deployments)
│ ├─ .env # Production environment secrets
│ ├─ storage/ # Laravel storage directory
│ │ ├─ app/public/ # Private uploaded files
│ │ ├─ framework/ # Cache, sessions, views
│ │ └─ logs/ # Application logs
│ └─ public/ # Public user-generated content
│ ├─ uploads/ # User file uploads
│ ├─ invoices/ # Generated invoices
│ ├─ qrcodes/ # Generated QR codes
│ └─ exports/ # Data exports
└─ current -> releases/20250815-152018 # Active release (symlink)
```

## What Gets Protected

### Always Protected (in /shared):
- ✅ User file uploads (`public/uploads/`)
- ✅ Generated invoices (`public/invoices/`)
- ✅ QR codes (`public/qrcodes/`)
- ✅ Data exports (`public/exports/`)
- ✅ Application logs (`storage/logs/`)
- ✅ User sessions (`storage/framework/sessions/`)
- ✅ Environment configuration (`.env`)

### Never Protected (in releases):
- ❌ Application code (PHP, Blade templates)
- ❌ Frontend assets (CSS, JS, images)
- ❌ Vendor dependencies
- ❌ Cached configurations (rebuilt each deploy)

## Deployment Safety Checklist

Before any deployment:
- [ ] Shared directory structure exists
- [ ] User uploads directory linked
- [ ] Storage directory linked
- [ ] Environment file linked
- [ ] Build artifact directories recreated

## The "One-Line Rule"

**Share ALL public/ contents EXCEPT build artifacts**

This simple rule ensures:
- User content is always preserved
- Fresh assets are delivered with each deployment
- No manual configuration required
- Works with any Laravel application

## Emergency Recovery

If data appears lost after deployment:

```bash
# Check symlink status
ls -la storage/ public/ .env

# Verify shared directory contents
ls -la ../shared/

# Re-run persistence script
bash Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh "$(pwd)" "$(pwd)/../shared"
```

## Framework Detection

The script automatically detects:

- Laravel applications (via `artisan` file)
- Next.js projects (via `package.json`)
- React/Vue projects (via dependencies)

Each type gets appropriate exclusion patterns.