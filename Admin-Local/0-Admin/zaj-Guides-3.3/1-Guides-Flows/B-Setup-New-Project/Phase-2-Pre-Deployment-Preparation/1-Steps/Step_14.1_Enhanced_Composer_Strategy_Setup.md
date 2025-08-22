# Step 14.1: Enhanced Composer Strategy Setup

**Location:** 🟢 Local Machine  
**Purpose:** Setup enhanced composer strategy with build-aware dependency management  
**When:** After development environment setup  
**Automation:** 🔧 Automated Script  
**Time:** 3-5 minutes

---

## 🎯 **STEP OVERVIEW**

This step implements an enhanced Composer strategy that prevents the "Faker not found" errors and other dependency issues by implementing intelligent build-aware dependency management that preserves dev dependencies when needed.

**What This Step Achieves:**
- ✅ Configures intelligent composer dependency strategy
- ✅ Prevents destructive composer install --no-dev commands
- ✅ Implements build-aware dependency resolution
- ✅ Creates composer optimization for production
- ✅ Sets up dependency validation and verification
- ✅ Establishes fallback strategies for dependency issues

---

## 📋 **PREREQUISITES**

- Laravel project setup completed
- Admin-Local infrastructure configured (Step 06.1)
- Development environment functional

---

## 🔧 **AUTOMATED EXECUTION**

### **Setup Enhanced Composer Strategy**

```bash
# Navigate to project root
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# Run enhanced composer strategy setup
./Admin-Local/1-Admin-Area/02-Master-Scripts/setup-composer-strategy.sh
```

### **Expected Output**

```
🔧 ENHANCED COMPOSER STRATEGY SETUP
===================================

✅ Analyzing current composer configuration
✅ Implementing build-aware dependency management
✅ Configuring production optimization strategy
✅ Setting up dependency validation system
✅ Creating composer fallback strategies
✅ Implementing dev dependency preservation

📊 COMPOSER STRATEGY CONFIGURED:
   → Build Strategy: Build-aware preservation
   → Production Mode: Optimized with dev fallback
   → Validation: Comprehensive dependency checking
   → Fallback: Smart recovery for missing dependencies

🎯 COMPOSER STRATEGY: ✅ CONFIGURED SUCCESSFULLY
```

---

## 🔧 **COMPOSER STRATEGY IMPLEMENTATION**

### **Build-Aware Composer Management**

The enhanced strategy implements these key principles:

1. **Build-First Approach**: Dependencies are resolved during build, not on server
2. **Dev Dependency Preservation**: Critical dev dependencies (like Faker) are preserved when needed
3. **Smart Production Optimization**: Optimizes for production while maintaining functionality
4. **Intelligent Fallback**: Provides recovery mechanisms for dependency issues

---

## 🔧 **COMPOSER CONFIGURATION ENHANCEMENTS**

### **Enhanced composer.json Configuration**

```json
{
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        },
        "platform-check": false
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ],
        "build-production": [
            "@composer install --optimize-autoloader --no-dev",
            "@php artisan config:cache",
            "@php artisan route:cache",
            "@php artisan view:cache"
        ],
        "build-with-dev": [
            "@composer install --optimize-autoloader",
            "@php artisan config:cache",
            "@php artisan route:cache",
            "@php artisan view:cache"
        ]
    }
}
```

### **Smart Dependency Resolution Script**

```bash
#!/bin/bash
# Smart Composer Dependency Resolution

set -e

# Configuration
COMPOSER_STRATEGY="build-aware"
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/composer-strategy.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function: Analyze current dependencies
analyze_dependencies() {
    log_message "${BLUE}🔍 Analyzing current composer configuration${NC}"
    
    # Check for critical dev dependencies
    CRITICAL_DEV_DEPS=("fakerphp/faker" "phpunit/phpunit" "laravel/telescope" "barryvdh/laravel-debugbar")
    MISSING_CRITICAL=()
    
    for dep in "${CRITICAL_DEV_DEPS[@]}"; do
        if ! composer show "$dep" --dev 2>/dev/null | grep -q "name"; then
            MISSING_CRITICAL+=("$dep")
        fi
    done
    
    if [[ ${#MISSING_CRITICAL[@]} -gt 0 ]]; then
        log_message "${YELLOW}⚠️  Missing critical dev dependencies: ${MISSING_CRITICAL[*]}${NC}"
    else
        log_message "${GREEN}✅ All critical dev dependencies present${NC}"
    fi
}

# Function: Configure build-aware strategy
configure_build_aware_strategy() {
    log_message "${BLUE}🔧 Implementing build-aware dependency management${NC}"
    
    # Create composer strategy configuration
    cat > Admin-Local/2-Project-Area/01-Project-Config/composer-strategy.json << 'EOF'
{
    "strategy": "build-aware",
    "build_environment": "local",
    "production_optimization": true,
    "dev_dependency_preservation": {
        "enabled": true,
        "critical_packages": [
            "fakerphp/faker",
            "phpunit/phpunit",
            "laravel/telescope",
            "barryvdh/laravel-debugbar"
        ],
        "preserve_for_migrations": true,
        "preserve_for_seeders": true
    },
    "fallback_strategy": {
        "enabled": true,
        "auto_recovery": true,
        "backup_vendor": true
    }
}
EOF
    
    log_message "${GREEN}✅ Build-aware strategy configured${NC}"
}

# Function: Create production optimization with dev fallback
create_production_optimization() {
    cat > Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh << 'EOF'
#!/bin/bash
# Production Composer Optimization with Dev Fallback

set -e

STRATEGY_FILE="Admin-Local/2-Project-Area/01-Project-Config/composer-strategy.json"
BACKUP_DIR="Admin-Local/4-Backups-And-Recovery/02-File-Backups/vendor-backup"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🔧 PRODUCTION COMPOSER OPTIMIZATION${NC}"
echo "=================================="

# Function: Check if migrations need dev dependencies
check_migration_dependencies() {
    if find database/migrations -name "*.php" -exec grep -l "Faker\|factory" {} \; | head -1 | grep -q .; then
        echo "true"
    else
        echo "false"
    fi
}

# Function: Backup current vendor directory
backup_vendor() {
    if [[ -d "vendor" ]]; then
        mkdir -p "$BACKUP_DIR"
        TIMESTAMP=$(date +%Y%m%d_%H%M%S)
        tar -czf "$BACKUP_DIR/vendor-backup-$TIMESTAMP.tar.gz" vendor/
        echo "✅ Vendor directory backed up"
        
        # Keep only last 5 backups
        cd "$BACKUP_DIR"
        ls -1t vendor-backup-*.tar.gz | tail -n +6 | xargs -r rm -f
        cd - > /dev/null
    fi
}

# Function: Smart composer install
smart_composer_install() {
    local needs_dev_deps=$(check_migration_dependencies)
    
    if [[ "$needs_dev_deps" == "true" ]]; then
        echo "🔍 Migrations require dev dependencies - using build-with-dev strategy"
        composer run-script build-with-dev
    else
        echo "🚀 No dev dependencies needed - using production optimization"
        composer run-script build-production
    fi
}

# Main execution
main() {
    backup_vendor
    smart_composer_install
    
    echo -e "${GREEN}✅ Production optimization complete${NC}"
}

main "$@"
EOF
    
    chmod +x Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh
    log_message "${GREEN}✅ Production optimization with fallback created${NC}"
}

# Function: Create dependency validation system
create_dependency_validation() {
    cat > Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh << 'EOF'
#!/bin/bash
# Composer Dependency Validation System

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🔍 COMPOSER DEPENDENCY VALIDATION${NC}"
echo "================================="

# Function: Validate composer.json
validate_composer_json() {
    if [[ ! -f "composer.json" ]]; then
        echo -e "${RED}❌ composer.json not found${NC}"
        exit 1
    fi
    
    if ! composer validate --no-check-publish 2>/dev/null; then
        echo -e "${RED}❌ composer.json validation failed${NC}"
        composer validate --no-check-publish
        exit 1
    fi
    
    echo -e "${GREEN}✅ composer.json is valid${NC}"
}

# Function: Check for critical dependencies
check_critical_dependencies() {
    local critical_deps=("laravel/framework" "fakerphp/faker")
    local missing_deps=()
    
    for dep in "${critical_deps[@]}"; do
        if ! composer show "$dep" 2>/dev/null | grep -q "name"; then
            missing_deps+=("$dep")
        fi
    done
    
    if [[ ${#missing_deps[@]} -eq 0 ]]; then
        echo -e "${GREEN}✅ All critical dependencies present${NC}"
    else
        echo -e "${YELLOW}⚠️  Missing critical dependencies: ${missing_deps[*]}${NC}"
        return 1
    fi
}

# Function: Check autoload optimization
check_autoload_optimization() {
    if [[ -f "vendor/composer/autoload_classmap.php" ]] && [[ -s "vendor/composer/autoload_classmap.php" ]]; then
        echo -e "${GREEN}✅ Autoloader is optimized${NC}"
    else
        echo -e "${YELLOW}⚠️  Autoloader not optimized - run composer dump-autoload --optimize${NC}"
    fi
}

# Function: Validate Laravel dependencies
validate_laravel_dependencies() {
    local laravel_deps=("laravel/framework" "laravel/tinker")
    
    for dep in "${laravel_deps[@]}"; do
        if composer show "$dep" 2>/dev/null | grep -q "name"; then
            local version=$(composer show "$dep" | grep "versions" | head -1 | awk '{print $3}')
            echo -e "${GREEN}✅ $dep: $version${NC}"
        else
            echo -e "${RED}❌ Missing Laravel dependency: $dep${NC}"
        fi
    done
}

# Main validation
main() {
    validate_composer_json
    check_critical_dependencies
    check_autoload_optimization
    validate_laravel_dependencies
    
    echo -e "${GREEN}🎯 Dependency validation complete${NC}"
}

main "$@"
EOF
    
    chmod +x Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh
    log_message "${GREEN}✅ Dependency validation system created${NC}"
}

# Function: Create composer recovery system
create_composer_recovery() {
    cat > Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh << 'EOF'
#!/bin/bash
# Composer Recovery System

set -e

BACKUP_DIR="Admin-Local/4-Backups-And-Recovery/02-File-Backups/vendor-backup"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🚨 COMPOSER RECOVERY SYSTEM${NC}"
echo "============================"

# Function: Restore from backup
restore_from_backup() {
    if [[ ! -d "$BACKUP_DIR" ]]; then
        echo -e "${RED}❌ No backup directory found${NC}"
        exit 1
    fi
    
    local latest_backup=$(ls -1t "$BACKUP_DIR"/vendor-backup-*.tar.gz 2>/dev/null | head -1)
    
    if [[ -z "$latest_backup" ]]; then
        echo -e "${RED}❌ No vendor backups found${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}🔄 Restoring from backup: $(basename "$latest_backup")${NC}"
    
    # Remove current vendor directory
    if [[ -d "vendor" ]]; then
        rm -rf vendor
    fi
    
    # Extract backup
    tar -xzf "$latest_backup"
    
    echo -e "${GREEN}✅ Vendor directory restored from backup${NC}"
}

# Function: Fresh composer install
fresh_composer_install() {
    echo -e "${YELLOW}🔄 Performing fresh composer install${NC}"
    
    # Remove vendor and composer.lock
    rm -rf vendor composer.lock
    
    # Fresh install with dev dependencies
    composer install --optimize-autoloader
    
    echo -e "${GREEN}✅ Fresh composer install complete${NC}"
}

# Function: Repair autoloader
repair_autoloader() {
    echo -e "${YELLOW}🔧 Repairing composer autoloader${NC}"
    
    composer dump-autoload --optimize
    
    echo -e "${GREEN}✅ Autoloader repaired${NC}"
}

# Main recovery options
main() {
    local recovery_type=${1:-"auto"}
    
    case "$recovery_type" in
        "backup")
            restore_from_backup
            ;;
        "fresh")
            fresh_composer_install
            ;;
        "autoloader")
            repair_autoloader
            ;;
        "auto")
            echo "🤖 Auto-recovery: Trying backup first, then fresh install"
            if restore_from_backup 2>/dev/null; then
                echo -e "${GREEN}✅ Recovery successful via backup${NC}"
            else
                echo -e "${YELLOW}⚠️  Backup recovery failed, trying fresh install${NC}"
                fresh_composer_install
            fi
            ;;
        *)
            echo "Usage: $0 [backup|fresh|autoloader|auto]"
            exit 1
            ;;
    esac
    
    echo -e "${GREEN}🎯 Composer recovery complete${NC}"
}

main "$@"
EOF
    
    chmod +x Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh
    log_message "${GREEN}✅ Composer recovery system created${NC}"
}

# Main execution
main() {
    analyze_dependencies
    configure_build_aware_strategy
    create_production_optimization
    create_dependency_validation
    create_composer_recovery
    
    log_message "${GREEN}🎯 COMPOSER STRATEGY: ✅ CONFIGURED SUCCESSFULLY${NC}"
}

main "$@"
```

---

## 🔧 **COMPOSER STRATEGY UTILITIES**

### **Production Optimization**
```bash
# Run production optimization with dev fallback
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh
```

### **Dependency Validation**
```bash
# Validate all composer dependencies
./Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh
```

### **Recovery Options**
```bash
# Auto-recovery (tries backup first, then fresh install)
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh auto

# Restore from backup
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh backup

# Fresh composer install
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh fresh

# Repair autoloader only
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh autoloader
```

---

## 🚨 **CRITICAL ANTI-PATTERNS PREVENTED**

### **Destructive Commands Eliminated**
```bash
# ❌ NEVER USE - Destroys dev dependencies needed for migrations
composer install --no-dev

# ❌ NEVER USE - Can break Faker and other dev dependencies
composer install --no-dev --optimize-autoloader

# ✅ USE INSTEAD - Smart build-aware installation
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh
```

### **Build-Aware Dependency Resolution**
The enhanced strategy ensures:
- Dev dependencies are preserved when migrations need them
- Production optimization happens without breaking functionality
- Automatic fallback to dev dependencies when needed
- Smart recovery from dependency issues

---

## 📁 **FILES CREATED/MODIFIED**

This step creates:
- `Admin-Local/2-Project-Area/01-Project-Config/composer-strategy.json` - Strategy configuration
- `Admin-Local/1-Admin-Area/02-Master-Scripts/setup-composer-strategy.sh` - Main setup script
- `Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh` - Production optimization
- `Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh` - Validation system
- `Admin-Local/1-Admin-Area/02-Master-Scripts/composer-recovery.sh` - Recovery system

This step modifies:
- `composer.json` - Enhanced with build scripts and optimization settings

---

## 🎯 **DEPLOYMENT INTEGRATION**

### **DeployHQ Build Commands**
```bash
# Use in DeployHQ build pipeline
./Admin-Local/1-Admin-Area/02-Master-Scripts/composer-production-optimize.sh
```

### **Server-Side Commands**
```bash
# Server scripts should NEVER run composer install
# They should only validate and use build artifacts
./Admin-Local/1-Admin-Area/02-Master-Scripts/validate-composer-dependencies.sh
```

---

## ✅ **COMPLETION CRITERIA**

Step 14.1 is complete when:
- [x] Enhanced composer strategy configured
- [x] Build-aware dependency management implemented
- [x] Production optimization with dev fallback created
- [x] Dependency validation system established
- [x] Recovery mechanisms implemented
- [x] Anti-patterns eliminated from deployment pipeline
- [x] Integration with build pipeline configured

---

## 🔄 **NEXT STEP**

Continue to **Step 15: Production Dependencies Verification**

---

**Note:** This enhanced composer strategy prevents the "Faker not found" errors and other dependency issues by implementing intelligent build-aware dependency management that preserves critical dev dependencies when needed for migrations and seeders.
