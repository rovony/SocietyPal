# Zaj Laravel Customization Workflow - TLDR Cheat Sheet

## 🚀 Quick Setup (5 mins)

```bash
# 1. Create structure
mkdir -p app/Custom/{config,Controllers,Models,Services}
mkdir -p resources/Custom/{css,js,views}

# 2. Create provider
cp CustomizationServiceProvider.php.template app/Providers/CustomizationServiceProvider.php

# 3. Register provider in bootstrap/providers.php
# Add: 'App\Providers\CustomizationServiceProvider',

# 4. Create config
mkdir -p app/Custom/config
cp custom-app.php.template app/Custom/config/custom-app.php

# 5. Test
php artisan config:clear && php artisan config:cache
```

---

## 📋 BEFORE Customization Checklist

### 🔐 Investment Protection

-   [ ] Create baseline fingerprint: `find . -name "*.php" | xargs md5sum > .investment-tracking/baselines/original.fingerprint`
-   [ ] Backup original files: `cp -r app/ archivedFiles/original-app/`
-   [ ] Document current state: `git commit -m "Pre-customization checkpoint"`

### 🛠️ Environment Prep

-   [ ] Clear all caches: `php artisan optimize:clear`
-   [ ] Test current functionality: `php artisan test`
-   [ ] Update dependencies: `composer update --no-dev`
-   [ ] Build fresh assets: `npm run build`

### 📊 Planning

-   [ ] Define feature requirements
-   [ ] Identify modification points
-   [ ] Plan database changes
-   [ ] Map UI/UX changes

---

## ⚡ DURING Customization Workflow

### 🎯 Development Flow

```bash
# 1. Start custom development mode
npm run custom:dev

# 2. Create feature branch
git checkout -b feature/custom-dashboard

# 3. Work in custom directories only
# ✅ DO: Modify files in app/Custom/
# ✅ DO: Create views in resources/Custom/
# ❌ DON'T: Edit vendor files directly

# 4. Test continuously
php artisan serve & npm run custom:dev
```

### 📁 File Modification Rules

| Type             | Location                        | Action                   |
| ---------------- | ------------------------------- | ------------------------ |
| **Controllers**  | `app/Custom/Controllers/`       | ✅ Create new            |
| **Models**       | `app/Custom/Models/`            | ✅ Extend vendor models  |
| **Views**        | `resources/Custom/views/`       | ✅ Override vendor views |
| **CSS/JS**       | `resources/Custom/css/js/`      | ✅ Custom styling        |
| **Routes**       | `app/Custom/routes/`            | ✅ Custom routes         |
| **Vendor Files** | `app/Http/`, `resources/views/` | ❌ Never modify directly |

### 🔄 Continuous Integration

```bash
# Every hour - Track changes
git add app/Custom/ resources/Custom/
git commit -m "WIP: Feature development"

# Every feature - Document
echo "Feature: Custom Dashboard" >> docs/Custom/features/implemented.md
```

---

## ✅ AFTER Customization Checklist

### 🧪 Testing Phase

-   [ ] **Unit Tests**: `php artisan test`
-   [ ] **Feature Tests**: Test all custom functionality
-   [ ] **Integration Tests**: Verify vendor + custom integration
-   [ ] **Performance Tests**: Check impact on load times
-   [ ] **Browser Tests**: Cross-browser compatibility

### 📦 Production Prep

```bash
# 1. Build production assets
npm run custom:build
npm run build

# 2. Optimize for production
php artisan optimize
composer install --no-dev --optimize-autoloader

# 3. Generate documentation
php artisan custom:docs:generate

# 4. Create deployment package
tar -czf custom-$(date +%Y%m%d).tar.gz app/Custom/ resources/Custom/ docs/Custom/
```

### 🛡️ Investment Protection

```bash
# 1. Generate final fingerprint
find . -name "*.php" | xargs md5sum > .investment-tracking/changes/final.fingerprint

# 2. Create change summary
diff .investment-tracking/baselines/original.fingerprint .investment-tracking/changes/final.fingerprint > docs/Investment-Protection/changes-summary.txt

# 3. Update documentation
echo "Customization completed: $(date)" >> docs/Investment-Protection/timeline.md
```

### 🚀 Deployment

-   [ ] **Staging Deploy**: Test in staging environment
-   [ ] **Database Backup**: Full backup before production
-   [ ] **Rollback Plan**: Prepare rollback scripts
-   [ ] **Production Deploy**: Deploy to production
-   [ ] **Post-Deploy Tests**: Verify all functionality

---

## 🆘 Emergency Commands

### 🔧 Quick Fixes

```bash
# Reset custom assets
rm -rf public/custom/ && npm run custom:build

# Clear all caches
php artisan optimize:clear

# Rebuild autoloader
composer dump-autoload

# Reset to clean state
git checkout . && composer install && npm install
```

### 🔄 Rollback Commands

```bash
# Rollback custom files
git checkout HEAD~1 -- app/Custom/ resources/Custom/

# Restore from backup
cp -r archivedFiles/original-app/* app/

# Nuclear option - full reset
git reset --hard HEAD~1
```

---

## 📊 Quick Status Check

```bash
# Check custom system status
php artisan custom:status

# Verify file integrity
md5sum -c .investment-tracking/baselines/original.fingerprint

# Check custom asset status
ls -la public/custom/

# Verify custom provider
php artisan config:show | grep CustomizationServiceProvider
```

---

## 🎯 Pro Tips

### ⚡ Speed Hacks

-   Use `php artisan make:custom:controller` for auto-namespaced controllers
-   Leverage `@extends('custom.layouts.app')` for consistent UI
-   Use `custom_asset('css/app.css')` helper for asset paths

### 🐛 Debug Mode

```bash
# Enable custom debugging
export CUSTOM_DEBUG=true
php artisan serve --host=0.0.0.0

# Watch custom logs
tail -f storage/logs/custom.log
```

### 🔍 Monitor Changes

```bash
# Real-time file watching
fswatch app/Custom/ resources/Custom/ | while read file; do echo "Changed: $file"; done
```

---

## 🎯 Complete Implementation Examples

### 🆕 Priority Task Analytics Dashboard

**Location:** `Files/2-Complete-Feature-Example/`

-   📊 **Full-stack feature**: Database + Backend + Frontend + Build System
-   🏗️ **Production-ready**: Chart.js, real-time updates, responsive design
-   ⚙️ **All components**: Shows every part of the customization system working together

```bash
# Study the complete implementation
open Files/2-Complete-Feature-Example/README-Priority-Task-Analytics-Dashboard.md

# Copy analytics database config
cp Files/2-Complete-Feature-Example/1-Database/custom-database.php.template app/Custom/config/custom-database.php

# Set up analytics build system
cp Files/2-Complete-Feature-Example/3-Build-System/webpack.custom.cjs.template webpack.custom.cjs
```

### 📊 Traditional Widget Example

**Location:** `Files/Example-Customization/Enhanced-Dashboard-Widget.md`

-   🎨 **SocietyPal-specific**: Traditional Livewire widget for society management
-   🔧 **Simpler scope**: Basic customization patterns and CSS styling
