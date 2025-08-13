#!/bin/bash
# Capture changes made through CodeCanyon GUI updates
# Usage: Run this before applying vendor updates

echo "📊 Capturing current state before vendor update..."

# Create snapshot of current state
SNAPSHOT_DATE=$(date +%Y%m%d_%H%M%S)
SNAPSHOT_DIR="snapshots/before_update_$SNAPSHOT_DATE"

mkdir -p "$SNAPSHOT_DIR"/{files,database,config,custom_verification}

# Backup critical vendor files that might change
echo "📦 Creating vendor file snapshot..."
tar -czf "$SNAPSHOT_DIR/vendor_files_backup.tar.gz" \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  app/ config/ database/ resources/ routes/ public/

# Backup database schema
echo "🗄️ Capturing database schema..."
php artisan schema:dump --path="$SNAPSHOT_DIR/schema_before_update.sql" 2>/dev/null || echo "Schema dump skipped (not available)"

# Create file comparison baseline
echo "📋 Creating file inventory..."
find . -type f -name "*.php" -o -name "*.blade.php" -o -name "*.js" -o -name "*.css" | \
  grep -v vendor/ | grep -v node_modules/ | sort > "$SNAPSHOT_DIR/file_inventory.txt"

# Count custom files for integrity verification
if [ -d "app/Custom" ]; then
    find app/Custom -type f | wc -l > "$SNAPSHOT_DIR/custom_files_count.txt"
fi

# Capture current CodeCanyon patterns for SocietyPro
echo "🔍 Documenting CodeCanyon patterns..."
cat > "$SNAPSHOT_DIR/codecanyon_patterns.txt" << 'PATTERNS_EOF'
# SocietyPro CodeCanyon Patterns Snapshot
Date: $(date)

## License & Configuration Files:
- config/froiden_envato.php
- public/version.txt
- public/install-version.txt

## Installer Components:
- public/installer/ (directory)
- public/error_install.php
- config/installer.php
- resources/views/vendor/froiden-envato/
- resources/views/custom-modules/install.blade.php
- lang/eng/installer_messages.php

## Version Information:
PATTERNS_EOF

# Capture version info
echo "Version file: $(cat public/version.txt 2>/dev/null || echo 'Not found')" >> "$SNAPSHOT_DIR/codecanyon_patterns.txt"
echo "Install status: $(cat public/install-version.txt 2>/dev/null || echo 'Not found')" >> "$SNAPSHOT_DIR/codecanyon_patterns.txt"
echo "Git tags: $(git tag -l | tail -5)" >> "$SNAPSHOT_DIR/codecanyon_patterns.txt"

# Document addon structure if exists
if [ -d "app/Modules" ] || [ -d "Modules" ]; then
    echo "📦 Documenting addon/module structure..."
    find . -name "*addon*" -o -name "*module*" -o -name "*plugin*" -type d | head -10 > "$SNAPSHOT_DIR/addon_directories.txt"
fi

echo "✅ Snapshot created: $SNAPSHOT_DIR"
echo ""
echo "📝 Next steps:"
echo "1. Apply vendor update (replace vendor files only)"
echo "2. Run: bash update_tracking/compare_changes.sh $SNAPSHOT_DIR"
echo "3. Review changes and test custom functionality"