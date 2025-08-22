#!/bin/bash
set -e

# Phase B-First: Symlink Fallback Verification (After Upload, Before Release)
# Purpose: Verify and create missing core Laravel symlinks that DeployHQ might not create
# Timing: After file upload, before current symlink switch - RUNS BEFORE Phase B
# Version 2.0 - PRODUCTION READY

echo "=== Phase B-First: Symlink Fallback Verification ==="

cd %release_path%

# A-Post-01: Verify Core Laravel Symlinks
echo "=== Verifying Core Laravel Symlinks ==="

# Check if storage symlink exists and is correct
if [ ! -L "storage" ] || [ ! -e "storage" ]; then
    echo "⚠️ Storage symlink missing or broken - creating fallback symlink"
    rm -rf storage 2>/dev/null || true
    ln -sf %shared_path%/storage storage
    echo "✅ Created storage → shared/storage symlink"
else
    STORAGE_TARGET=$(readlink storage)
    echo "✅ Storage symlink exists: storage → $STORAGE_TARGET"
fi

# Check if bootstrap/cache symlink exists and is correct
if [ ! -L "bootstrap/cache" ] || [ ! -e "bootstrap/cache" ]; then
    echo "⚠️ Bootstrap cache symlink missing or broken - creating fallback symlink"
    rm -rf bootstrap/cache 2>/dev/null || true
    ln -sf %shared_path%/bootstrap/cache bootstrap/cache
    echo "✅ Created bootstrap/cache → shared/bootstrap/cache symlink"
else
    BOOTSTRAP_TARGET=$(readlink bootstrap/cache)
    echo "✅ Bootstrap cache symlink exists: bootstrap/cache → $BOOTSTRAP_TARGET"
fi

# A-Post-02: Verify .env Symlink (Critical)
echo "=== Verifying Environment Symlink ==="
if [ ! -L ".env" ] || [ ! -e ".env" ]; then
    echo "⚠️ .env symlink missing or broken - creating fallback symlink"
    rm -rf .env 2>/dev/null || true
    ln -sf %shared_path%/.env .env
    echo "✅ Created .env → shared/.env symlink"
else
    ENV_TARGET=$(readlink .env)
    echo "✅ .env symlink exists: .env → $ENV_TARGET"
fi

# A-Post-03: Verify Modules Symlink (CodeCanyon)
echo "=== Verifying Modules Symlink ==="
if [ -d "%shared_path%/Modules" ]; then
    if [ ! -L "Modules" ] || [ ! -e "Modules" ]; then
        echo "⚠️ Modules symlink missing or broken - creating fallback symlink"
        rm -rf Modules 2>/dev/null || true
        ln -sf %shared_path%/Modules Modules
        echo "✅ Created Modules → shared/Modules symlink"
    else
        MODULES_TARGET=$(readlink Modules)
        echo "✅ Modules symlink exists: Modules → $MODULES_TARGET"
    fi
else
    echo "ℹ️ No shared Modules directory found - skipping"
fi

# A-Post-04: Verify Backups Symlink
echo "=== Verifying Backups Symlink ==="
if [ ! -L "backups" ] || [ ! -e "backups" ]; then
    echo "⚠️ Backups symlink missing or broken - creating fallback symlink"
    rm -rf backups 2>/dev/null || true
    ln -sf %shared_path%/backups backups
    echo "✅ Created backups → shared/backups symlink"
else
    BACKUPS_TARGET=$(readlink backups)
    echo "✅ Backups symlink exists: backups → $BACKUPS_TARGET"
fi

# A-Post-05: Initialize Storage Structure (if needed)
echo "=== Initializing Storage Structure ==="
if [ -L "storage" ] && [ -e "storage" ]; then
    # Only create structure if storage is properly symlinked
    mkdir -p %shared_path%/storage/{app/public,framework/{cache/data,sessions,views},logs}
    chmod -R 775 %shared_path%/storage
    echo "✅ Storage structure initialized"
else
    echo "❌ Storage symlink not working - structure initialization skipped"
fi

# A-Post-06: Final Verification Report
echo "=== Symlink Verification Summary ==="
SYMLINK_ISSUES=0

# Check all critical symlinks
for LINK in storage bootstrap/cache .env backups; do
    if [ -L "$LINK" ] && [ -e "$LINK" ]; then
        TARGET=$(readlink "$LINK")
        echo "✅ $LINK → $TARGET"
    else
        echo "❌ $LINK: BROKEN OR MISSING"
        SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1))
    fi
done

# Check Modules if it should exist
if [ -d "%shared_path%/Modules" ]; then
    if [ -L "Modules" ] && [ -e "Modules" ]; then
        MODULES_TARGET=$(readlink Modules)
        echo "✅ Modules → $MODULES_TARGET"
    else
        echo "❌ Modules: BROKEN OR MISSING"
        SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1))
    fi
fi

if [ $SYMLINK_ISSUES -eq 0 ]; then
    echo "🎉 All critical symlinks verified successfully!"
else
    echo "⚠️ $SYMLINK_ISSUES symlink issues detected - may need manual intervention"
fi

echo "✅ Phase B-First symlink verification completed successfully"
