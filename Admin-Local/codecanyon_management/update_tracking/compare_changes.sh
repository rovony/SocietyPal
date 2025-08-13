#!/bin/bash
# compare_changes.sh - Comprehensive post-update analysis

if [ -z "$1" ]; then
    echo "Usage: bash compare_changes.sh <snapshot_directory>"
    echo "Example: bash compare_changes.sh snapshots/before_update_20241201_143022"
    exit 1
fi

SNAPSHOT_DIR="$1"
COMPARISON_DATE=$(date +%Y%m%d_%H%M%S)
COMPARISON_DIR="comparisons/comparison_$COMPARISON_DATE"

if [ ! -d "$SNAPSHOT_DIR" ]; then
    echo "❌ Snapshot not found: $SNAPSHOT_DIR"
    exit 1
fi

echo "🔍 Analyzing changes after vendor update..."
mkdir -p "$COMPARISON_DIR"/{files,database,config,custom_verification}

# 1. File Changes Analysis
echo "📊 Analyzing file changes..."
find . -type f \( -name "*.php" -o -name "*.blade.php" -o -name "*.js" -o -name "*.css" \) \
    | grep -v vendor/ | grep -v node_modules/ | grep -v 'app/Custom/' | sort > "$COMPARISON_DIR/files/file_inventory_after.txt"

# Compare file inventories
diff "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" > "$COMPARISON_DIR/files/file_changes.diff"

# Analyze specific changes
echo "📁 New files added by vendor update:"
comm -13 "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" | head -20 | tee "$COMPARISON_DIR/files/new_files.txt"

echo ""
echo "🗑️ Files removed by vendor update:"
comm -23 "$SNAPSHOT_DIR/file_inventory.txt" "$COMPARISON_DIR/files/file_inventory_after.txt" | head -20 | tee "$COMPARISON_DIR/files/removed_files.txt"

# 2. Custom Layer Verification
echo ""
echo "🛡️ Verifying custom layer integrity..."
CUSTOM_INTACT=true

if [ -d "app/Custom" ]; then
    CURRENT_CUSTOM_COUNT=$(find app/Custom -type f | wc -l)
    SNAPSHOT_CUSTOM_COUNT=$(cat "$SNAPSHOT_DIR/custom_files_count.txt" 2>/dev/null || echo "0")

    if [ "$CURRENT_CUSTOM_COUNT" -eq "$SNAPSHOT_CUSTOM_COUNT" ]; then
        echo "✅ Custom layer intact: $CURRENT_CUSTOM_COUNT files preserved"
    else
        echo "⚠️ Custom layer changed: $SNAPSHOT_CUSTOM_COUNT → $CURRENT_CUSTOM_COUNT files"
        CUSTOM_INTACT=false
    fi
else
    echo "❌ Custom layer missing! This is a critical issue."
    CUSTOM_INTACT=false
fi

# 3. CodeCanyon Pattern Verification
echo ""
echo "🔍 Verifying CodeCanyon patterns..."
PATTERNS_INTACT=true

# Check critical CodeCanyon files
CRITICAL_FILES=(
    "config/froiden_envato.php"
    "public/version.txt"
    "public/install-version.txt"
    "public/installer"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -e "$file" ]; then
        echo "✅ $file - Present"
    else
        echo "❌ $file - Missing"
        PATTERNS_INTACT=false
    fi
done

# 4. Version Consistency Check
echo ""
echo "📋 Checking version consistency..."
CURRENT_VERSION_FILE=$(cat public/version.txt 2>/dev/null || echo "Unknown")
CURRENT_GIT_TAG=$(git describe --tags --exact-match HEAD 2>/dev/null || git tag -l | tail -1 || echo "No tags")

echo "Version file: $CURRENT_VERSION_FILE"
echo "Git tag: $CURRENT_GIT_TAG"

# 5. Generate Summary Report
echo ""
echo "📋 Generating update summary..."
cat > "$COMPARISON_DIR/UPDATE_SUMMARY.md" << SUMMARY_EOF
# Vendor Update Analysis Report

**Update Date:** $(date)
**Snapshot:** $SNAPSHOT_DIR
**Analysis:** $COMPARISON_DIR

## Custom Layer Status
- **Status:** $([ "$CUSTOM_INTACT" = true ] && echo "✅ INTACT" || echo "❌ COMPROMISED")
- **Files:** $CURRENT_CUSTOM_COUNT custom files preserved

## CodeCanyon Pattern Status
- **Status:** $([ "$PATTERNS_INTACT" = true ] && echo "✅ INTACT" || echo "❌ COMPROMISED")
- **Critical Files:** All essential CodeCanyon files verified

## Version Information
- **Version File:** $CURRENT_VERSION_FILE
- **Git Tag:** $CURRENT_GIT_TAG
- **Consistency:** $([ "$CURRENT_VERSION_FILE" = "$CURRENT_GIT_TAG" ] && echo "✅ CONSISTENT" || echo "⚠️ MISMATCH")

## File Changes
- **New Files:** $(wc -l < "$COMPARISON_DIR/files/new_files.txt") files added
- **Removed Files:** $(wc -l < "$COMPARISON_DIR/files/removed_files.txt") files removed

## Next Steps
1. **Test all custom functionality**
2. **Verify application works correctly**
3. **Check for new configuration requirements**
4. **Test license verification**
5. **Verify addon compatibility (if applicable)**
6. **Update documentation if needed**

## Files for Review
- New files: $COMPARISON_DIR/files/new_files.txt
- Removed files: $COMPARISON_DIR/files/removed_files.txt
- Full diff: $COMPARISON_DIR/files/file_changes.diff

## Recommended Actions
$([ "$CUSTOM_INTACT" = false ] && echo "🚨 **CRITICAL:** Restore custom layer from backup")
$([ "$PATTERNS_INTACT" = false ] && echo "🚨 **CRITICAL:** Restore missing CodeCanyon files")
$([ "$CURRENT_VERSION_FILE" != "$CURRENT_GIT_TAG" ] && echo "⚠️ **WARNING:** Version mismatch needs attention")
SUMMARY_EOF

echo "✅ Analysis complete: $COMPARISON_DIR/UPDATE_SUMMARY.md"
echo ""
echo "📊 Summary:"
echo "- Custom Layer: $([ "$CUSTOM_INTACT" = true ] && echo "✅ OK" || echo "❌ ISSUE")"
echo "- CodeCanyon Patterns: $([ "$PATTERNS_INTACT" = true ] && echo "✅ OK" || echo "❌ ISSUE")"
echo "- Files Added: $(wc -l < "$COMPARISON_DIR/files/new_files.txt")"
echo "- Files Removed: $(wc -l < "$COMPARISON_DIR/files/removed_files.txt")"