#!/bin/bash

# SocietyPal Intelligent Migration Safety Check Script
# This script intelligently distinguishes between true destructive operations
# and normal Laravel migration patterns to eliminate false positives

set -e

echo "🧠 Laravel Migration Intelligent Safety Analysis..."

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Initialize counters
TRULY_DESTRUCTIVE_COUNT=0
RISKY_OPERATION_COUNT=0
SAFE_COUNT=0
TOTAL_FILES=0

# Get list of migration files
if [ ! -d "database/migrations" ]; then
    echo -e "${GREEN}✅ No migrations directory found${NC}"
    exit 0
fi

MIGRATION_FILES=$(find database/migrations -name "*.php" -type f | sort)

if [ -z "$MIGRATION_FILES" ]; then
    echo -e "${GREEN}✅ No migration files found${NC}"
    exit 0
fi

TOTAL_FILES=$(echo "$MIGRATION_FILES" | wc -l | tr -d ' ')
echo "📁 Found $TOTAL_FILES migration files"
echo ""

# Intelligent analysis function
analyze_migration_intelligently() {
    local file="$1"
    local filename=$(basename "$file")
    local has_true_destructive=0
    local has_risky_operation=0
    local risk_details=()
    
    echo "🔍 Analyzing: $filename"
    
    # Extract up() method content for analysis (macOS compatible)
    local up_content=$(sed -n '/function up()/,/function down()/p' "$file" | head -n -1)
    local down_content=$(sed -n '/function down()/,/^[[:space:]]*}[[:space:]]*$/p' "$file")
    
    # ====================
    # TRUE DESTRUCTIVE OPERATIONS (Should block deployment)
    # ====================
    
    # 1. Schema::drop() (NOT dropIfExists) in up() method
    if echo "$up_content" | grep -q "Schema::drop(" && ! echo "$up_content" | grep -q "Schema::dropIfExists("; then
        echo -e "    ${RED}🚨 TRUE DESTRUCTIVE: Schema::drop() without dropIfExists${NC}"
        has_true_destructive=1
        risk_details+=("Schema::drop() permanently deletes tables")
    fi
    
    # 2. dropTable() in up() method
    if echo "$up_content" | grep -q "->dropTable("; then
        echo -e "    ${RED}🚨 TRUE DESTRUCTIVE: dropTable() operation${NC}"
        has_true_destructive=1
        risk_details+=("dropTable() permanently removes tables")
    fi
    
    # 3. dropColumn() in up() method  
    if echo "$up_content" | grep -q "->dropColumn("; then
        echo -e "    ${RED}🚨 TRUE DESTRUCTIVE: dropColumn() operation${NC}"
        has_true_destructive=1
        risk_details+=("dropColumn() permanently removes data")
    fi
    
    # 4. Raw SQL DROP statements
    if echo "$up_content" | grep -i -q "DB::statement.*DROP\|DB::raw.*DROP"; then
        echo -e "    ${RED}🚨 TRUE DESTRUCTIVE: Raw DROP SQL statement${NC}"
        has_true_destructive=1
        risk_details+=("Raw DROP statements can destroy data")
    fi
    
    # 5. TRUNCATE operations
    if echo "$up_content" | grep -i -q "DB::statement.*TRUNCATE\|truncate("; then
        echo -e "    ${RED}🚨 TRUE DESTRUCTIVE: TRUNCATE operation${NC}"
        has_true_destructive=1
        risk_details+=("TRUNCATE empties tables permanently")
    fi
    
    # ====================
    # RISKY BUT NORMAL OPERATIONS (Warnings, not blockers)
    # ====================
    
    # 1. Column modifications with ->change() (NORMAL LARAVEL PATTERN)
    if echo "$up_content" | grep -q "->change()"; then
        # Check if it's a reasonable change (enum to string, int to bigint, etc.)
        if echo "$up_content" | grep -q "string(.*)->.*change()\|enum(.*)->.*change()\|integer(.*)->.*change()"; then
            echo -e "    ${BLUE}ℹ️  NORMAL: Column type modification with ->change()${NC}"
            echo -e "    ${BLUE}    (This is standard Laravel pattern for column changes)${NC}"
        else
            echo -e "    ${YELLOW}⚠️  RISKY: Complex column modification${NC}"
            has_risky_operation=1
            risk_details+=("Column modifications may affect existing data")
        fi
    fi
    
    # 2. Making columns NOT NULL
    if echo "$up_content" | grep -q "nullable(false)"; then
        echo -e "    ${YELLOW}⚠️  RISKY: Making column NOT NULL${NC}"
        has_risky_operation=1
        risk_details+=("NOT NULL constraints may fail if existing NULL data exists")
    fi
    
    # 3. Rename operations
    if echo "$up_content" | grep -q "renameColumn\|renameTable"; then
        echo -e "    ${YELLOW}⚠️  RISKY: Rename operation${NC}"
        has_risky_operation=1
        risk_details+=("Rename operations may break application code dependencies")
    fi
    
    # 4. Adding unique constraints
    if echo "$up_content" | grep -q "->unique()\|->index(.*unique"; then
        echo -e "    ${YELLOW}⚠️  RISKY: Adding unique constraints${NC}"
        has_risky_operation=1
        risk_details+=("Unique constraints may fail if duplicate data exists")
    fi
    
    # ====================
    # POSITIVE PATTERNS (Good practices)
    # ====================
    
    # Check for proper rollback in down() method
    if echo "$down_content" | grep -q "Schema::dropIfExists\|->dropColumn\|->change()"; then
        echo -e "    ${GREEN}✅ GOOD: Proper rollback operations in down() method${NC}"
    fi
    
    # Check for safe column additions
    if echo "$up_content" | grep -q "addColumn\|string(\|integer(\|boolean(" && ! echo "$up_content" | grep -q "->change()"; then
        echo -e "    ${GREEN}✅ SAFE: Adding new columns${NC}"
    fi
    
    # Check for table creation
    if echo "$up_content" | grep -q "Schema::create\|create("; then
        echo -e "    ${GREEN}✅ SAFE: Creating new tables/structures${NC}"
    fi
    
    # Update counters and provide summary
    if [ $has_true_destructive -eq 1 ]; then
        TRULY_DESTRUCTIVE_COUNT=$((TRULY_DESTRUCTIVE_COUNT + 1))
        echo -e "    ${RED}❌ VERDICT: TRULY DESTRUCTIVE - BLOCKS DEPLOYMENT${NC}"
    elif [ $has_risky_operation -eq 1 ]; then
        RISKY_OPERATION_COUNT=$((RISKY_OPERATION_COUNT + 1))
        echo -e "    ${YELLOW}⚠️  VERDICT: RISKY BUT NORMAL - PROCEED WITH BACKUP${NC}"
    else
        SAFE_COUNT=$((SAFE_COUNT + 1))
        echo -e "    ${GREEN}✅ VERDICT: SAFE FOR DEPLOYMENT${NC}"
    fi
    
    # Show risk details if any
    if [ ${#risk_details[@]} -gt 0 ]; then
        echo -e "    ${PURPLE}📋 Risk Details:${NC}"
        for detail in "${risk_details[@]}"; do
            echo -e "      • $detail"
        done
    fi
    
    echo ""
}

# Analyze all migration files with intelligent detection
echo -e "${PURPLE}🧠 RUNNING INTELLIGENT MIGRATION ANALYSIS${NC}"
echo "======================================================================="

for migration_file in $MIGRATION_FILES; do
    analyze_migration_intelligently "$migration_file"
done

# Intelligent Summary Report
echo "======================================================================="
echo -e "${PURPLE}🧠 INTELLIGENT MIGRATION SAFETY REPORT${NC}"
echo "======================================================================="
echo -e "${GREEN}✅ Safe for deployment: $SAFE_COUNT migrations${NC}"
echo -e "${YELLOW}⚠️  Risky but normal (Laravel patterns): $RISKY_OPERATION_COUNT migrations${NC}"
echo -e "${RED}🚨 Truly destructive (BLOCKS deployment): $TRULY_DESTRUCTIVE_COUNT migrations${NC}"
echo ""

# Provide intelligent recommendations
if [ $TRULY_DESTRUCTIVE_COUNT -gt 0 ]; then
    echo -e "${RED}🛑 DEPLOYMENT BLOCKED - TRUE DESTRUCTIVE OPERATIONS DETECTED${NC}"
    echo ""
    echo -e "${RED}⚠️  CRITICAL SAFETY MEASURES REQUIRED:${NC}"
    echo "   1. 🔍 Manual review of destructive operations by senior developer"
    echo "   2. 💾 MANDATORY full database backup before any deployment"  
    echo "   3. 🧪 Test on complete production data copy first"
    echo "   4. 🔄 Verify rollback procedures work correctly"
    echo "   5. ⏰ Deploy only during scheduled maintenance window"
    echo "   6. 👥 Have database administrator on standby"
    echo ""
    echo -e "${RED}🚨 These operations can permanently destroy data - extreme caution required${NC}"
    
elif [ $RISKY_OPERATION_COUNT -gt 0 ]; then
    echo -e "${YELLOW}✅ DEPLOYMENT ALLOWED - NORMAL LARAVEL PATTERNS DETECTED${NC}"
    echo ""
    echo -e "${YELLOW}📋 RECOMMENDED PRECAUTIONS (Standard Practice):${NC}"
    echo "   1. 💾 Create database backup (standard precaution)"
    echo "   2. 🧪 Test on staging environment first"
    echo "   3. 📊 Monitor application after deployment"
    echo "   4. 🔄 Verify rollback capability"
    echo ""
    echo -e "${GREEN}💡 Note: ->change() operations are normal Laravel column modifications${NC}"
    echo -e "${GREEN}🚀 These operations are safe when properly tested${NC}"
    
else
    echo -e "${GREEN}🎉 OPTIMAL DEPLOYMENT CONDITIONS${NC}"
    echo ""
    echo -e "${GREEN}✅ All operations are safe for deployment${NC}"
    echo -e "${GREEN}🚀 No special precautions required${NC}"
    echo -e "${GREEN}📈 Standard deployment procedures apply${NC}"
fi

echo ""
echo "======================================================================="
echo -e "${BLUE}🛠️  LARAVEL MIGRATION TESTING COMMANDS${NC}"
echo "======================================================================="
echo "Preview all pending migrations:"
echo "  php artisan migrate --pretend"
echo ""
echo "Check current migration status:"  
echo "  php artisan migrate:status"
echo ""
echo "Test rollback capability:"
echo "  php artisan migrate:rollback --pretend"
echo ""
echo "Run migrations with step-by-step confirmation:"
echo "  php artisan migrate --step"
echo ""
echo "Check for pending migrations:"
echo "  php artisan migrate:fresh --seed --pretend"

# Intelligent exit codes for CI/CD integration
if [ $TRULY_DESTRUCTIVE_COUNT -gt 0 ]; then
    echo -e "\n${RED}❌ BLOCKING DEPLOYMENT - TRUE DESTRUCTIVE OPERATIONS FOUND${NC}"
    echo -e "${RED}🛑 Manual review and explicit approval required${NC}"
    exit 2  # Critical - blocks automated deployment
elif [ $RISKY_OPERATION_COUNT -gt 0 ]; then
    echo -e "\n${YELLOW}✅ ALLOWING DEPLOYMENT - NORMAL LARAVEL PATTERNS${NC}"
    echo -e "${YELLOW}💡 These are standard Laravel column modifications${NC}"
    exit 0  # Allow deployment - these are normal patterns
else
    echo -e "\n${GREEN}✅ MIGRATION ANALYSIS PASSED - OPTIMAL CONDITIONS${NC}"
    exit 0  # Perfect - all clear for deployment
fi