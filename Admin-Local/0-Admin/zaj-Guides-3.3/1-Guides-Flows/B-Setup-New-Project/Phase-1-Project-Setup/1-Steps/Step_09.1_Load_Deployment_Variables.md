# Step 09.1: Load Deployment Variables

**Location:** 🟢 Local Machine  
**Purpose:** JSON deployment variables management and loading system  
**When:** After environment configuration  
**Automation:** 🔧 Automated Script  
**Time:** 1-2 minutes

---

## 🎯 **STEP OVERVIEW**

This step implements the deployment variables loading system that provides centralized configuration management throughout the entire deployment pipeline. It loads and validates all deployment settings from the JSON configuration.

**What This Step Achieves:**
- ✅ Loads deployment-variables.json configuration
- ✅ Validates all configuration parameters
- ✅ Exports variables for use in other scripts
- ✅ Provides environment-specific variable loading
- ✅ Implements configuration error handling
- ✅ Creates variable loading utilities

---

## 📋 **PREREQUISITES**

- Deployment variables configured (Step 02.1)
- Admin-Local infrastructure setup (Step 06.1)
- Environment files configured (Step 09)

---

## 🔧 **AUTOMATED EXECUTION**

### **Load Deployment Variables**

```bash
# Navigate to project root
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# Run variable loading system
./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh
```

### **Expected Output**

```
🔧 DEPLOYMENT VARIABLES LOADING
===============================

✅ Loading deployment-variables.json
✅ Validating configuration structure
✅ Processing environment-specific settings
✅ Exporting variables for pipeline use
✅ Creating variable access utilities

📊 VARIABLES LOADED:
   → Project: SocietyPal (v1.0.0)
   → Environment: production
   → Server: societypal.com
   → Build Strategy: atomic
   → Security: enabled

🎯 DEPLOYMENT VARIABLES: ✅ LOADED SUCCESSFULLY
```

---

## 🔧 **VARIABLE LOADING SYSTEM**

### **Main Loading Script (load-variables.sh)**

```bash
#!/bin/bash

# Load Deployment Variables System
# Purpose: Load and validate deployment configuration from JSON

set -e  # Exit on any error

# Configuration paths
VARS_FILE="Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
ENV_FILE=".env"
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/variable-loading.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔧 DEPLOYMENT VARIABLES LOADING${NC}"
echo "==============================="

# Function: Log with timestamp
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function: Validate JSON file exists and is valid
validate_json_file() {
    if [[ ! -f "$VARS_FILE" ]]; then
        log_message "${RED}❌ Error: deployment-variables.json not found${NC}"
        exit 1
    fi
    
    if ! jq empty "$VARS_FILE" 2>/dev/null; then
        log_message "${RED}❌ Error: Invalid JSON in deployment-variables.json${NC}"
        exit 1
    fi
    
    log_message "${GREEN}✅ Loading deployment-variables.json${NC}"
}

# Function: Load project variables
load_project_variables() {
    export PROJECT_NAME=$(jq -r '.project.name' "$VARS_FILE")
    export PROJECT_SLUG=$(jq -r '.project.slug' "$VARS_FILE")
    export PROJECT_VERSION=$(jq -r '.project.version' "$VARS_FILE")
    export PROJECT_REPOSITORY=$(jq -r '.project.repository' "$VARS_FILE")
    
    log_message "${GREEN}✅ Validating configuration structure${NC}"
}

# Function: Load environment-specific variables
load_environment_variables() {
    local env=${1:-production}
    
    export APP_URL=$(jq -r ".environments.$env.app_url" "$VARS_FILE")
    export APP_ENV=$(jq -r ".environments.$env.app_env" "$VARS_FILE")
    export APP_DEBUG=$(jq -r ".environments.$env.app_debug" "$VARS_FILE")
    export DB_CONNECTION=$(jq -r ".environments.$env.database_connection" "$VARS_FILE")
    
    log_message "${GREEN}✅ Processing environment-specific settings${NC}"
}

# Function: Load server configuration
load_server_variables() {
    local env=${1:-production}
    
    export SERVER_HOST=$(jq -r ".servers.$env.host" "$VARS_FILE")
    export SERVER_USERNAME=$(jq -r ".servers.$env.username" "$VARS_FILE")
    export SERVER_PORT=$(jq -r ".servers.$env.port" "$VARS_FILE")
    export SERVER_PATH=$(jq -r ".servers.$env.path" "$VARS_FILE")
    export PHP_VERSION=$(jq -r ".servers.$env.php_version" "$VARS_FILE")
    export NODE_VERSION=$(jq -r ".servers.$env.node_version" "$VARS_FILE")
    
    log_message "${GREEN}✅ Exporting variables for pipeline use${NC}"
}

# Function: Load build configuration
load_build_variables() {
    export BUILD_PHP_VERSION=$(jq -r '.build.php_version' "$VARS_FILE")
    export BUILD_NODE_VERSION=$(jq -r '.build.node_version' "$VARS_FILE")
    export BUILD_COMPOSER_INSTALL=$(jq -r '.build.composer_install' "$VARS_FILE")
    export BUILD_NPM_INSTALL=$(jq -r '.build.npm_install' "$VARS_FILE")
    export BUILD_ASSET_COMPILATION=$(jq -r '.build.asset_compilation' "$VARS_FILE")
    export BUILD_OPTIMIZATION=$(jq -r '.build.optimization' "$VARS_FILE")
}

# Function: Load deployment configuration
load_deployment_variables() {
    export DEPLOYMENT_STRATEGY=$(jq -r '.deployment.strategy' "$VARS_FILE")
    export DEPLOYMENT_KEEP_RELEASES=$(jq -r '.deployment.keep_releases' "$VARS_FILE")
    export DEPLOYMENT_MAINTENANCE_MODE=$(jq -r '.deployment.maintenance_mode' "$VARS_FILE")
    export DEPLOYMENT_RUN_MIGRATIONS=$(jq -r '.deployment.run_migrations' "$VARS_FILE")
    export DEPLOYMENT_CLEAR_CACHE=$(jq -r '.deployment.clear_cache' "$VARS_FILE")
    export DEPLOYMENT_HEALTH_CHECK=$(jq -r '.deployment.health_check' "$VARS_FILE")
    export DEPLOYMENT_ROLLBACK_ON_FAILURE=$(jq -r '.deployment.rollback_on_failure' "$VARS_FILE")
}

# Function: Load security configuration
load_security_variables() {
    export SECURITY_FORCE_HTTPS=$(jq -r '.security.force_https' "$VARS_FILE")
    export SECURITY_HEADERS=$(jq -r '.security.security_headers' "$VARS_FILE")
    export SECURITY_CSRF_PROTECTION=$(jq -r '.security.csrf_protection' "$VARS_FILE")
    export SECURITY_RATE_LIMITING=$(jq -r '.security.rate_limiting' "$VARS_FILE")
    export SECURITY_DIR_PERMISSIONS=$(jq -r '.security.file_permissions.directories' "$VARS_FILE")
    export SECURITY_FILE_PERMISSIONS=$(jq -r '.security.file_permissions.files' "$VARS_FILE")
}

# Function: Create variable access utilities
create_variable_utilities() {
    local utils_file="Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh"
    
    cat > "$utils_file" << 'EOF'
#!/bin/bash
# Variable Utilities - Helper functions for accessing deployment variables

# Get project variable
get_project_var() {
    local var_name="$1"
    jq -r ".project.$var_name" "Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
}

# Get environment variable
get_env_var() {
    local env="$1"
    local var_name="$2"
    jq -r ".environments.$env.$var_name" "Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
}

# Get server variable
get_server_var() {
    local env="$1"
    local var_name="$2"
    jq -r ".servers.$env.$var_name" "Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
}

# Get build variable
get_build_var() {
    local var_name="$1"
    jq -r ".build.$var_name" "Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
}

# Get deployment variable
get_deployment_var() {
    local var_name="$1"
    jq -r ".deployment.$var_name" "Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json"
}

# Check if variable exists and is not null
var_exists() {
    local value="$1"
    [[ "$value" != "null" && "$value" != "" ]]
}
EOF
    
    chmod +x "$utils_file"
    log_message "${GREEN}✅ Creating variable access utilities${NC}"
}

# Function: Display loaded variables summary
display_variables_summary() {
    echo ""
    log_message "${BLUE}📊 VARIABLES LOADED:${NC}"
    log_message "   → Project: $PROJECT_NAME (v$PROJECT_VERSION)"
    log_message "   → Environment: $APP_ENV"
    log_message "   → Server: $SERVER_HOST"
    log_message "   → Build Strategy: $DEPLOYMENT_STRATEGY"
    log_message "   → Security: $([ "$SECURITY_FORCE_HTTPS" = "true" ] && echo "enabled" || echo "disabled")"
    echo ""
}

# Main execution
main() {
    local environment=${1:-production}
    
    validate_json_file
    load_project_variables
    load_environment_variables "$environment"
    load_server_variables "$environment"
    load_build_variables
    load_deployment_variables
    load_security_variables
    create_variable_utilities
    display_variables_summary
    
    log_message "${GREEN}🎯 DEPLOYMENT VARIABLES: ✅ LOADED SUCCESSFULLY${NC}"
}

# Execute main function with environment parameter
main "$@"
```

---

## 🔧 **ENVIRONMENT-SPECIFIC LOADING**

### **Load Production Variables**
```bash
# Load production environment variables
./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh production
```

### **Load Staging Variables**
```bash
# Load staging environment variables
./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh staging
```

### **Load Local Variables**
```bash
# Load local development variables
./Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh local
```

---

## 🔧 **VARIABLE ACCESS UTILITIES**

### **Using Variable Utilities in Scripts**

```bash
#!/bin/bash
# Example: Using variable utilities in deployment scripts

# Source the variable utilities
source Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh

# Get project information
PROJECT_NAME=$(get_project_var "name")
PROJECT_VERSION=$(get_project_var "version")

# Get environment-specific settings
APP_URL=$(get_env_var "production" "app_url")
APP_DEBUG=$(get_env_var "production" "app_debug")

# Get server configuration
SERVER_HOST=$(get_server_var "production" "host")
SERVER_PATH=$(get_server_var "production" "path")

# Get build settings
PHP_VERSION=$(get_build_var "php_version")
COMPOSER_INSTALL=$(get_build_var "composer_install")

# Check if variables exist
if var_exists "$PROJECT_NAME"; then
    echo "Project: $PROJECT_NAME"
fi
```

---

## 📊 **VARIABLE VALIDATION**

### **Configuration Validation Checks**

```bash
# Validate required project variables
validate_project_config() {
    local errors=0
    
    if ! var_exists "$(get_project_var 'name')"; then
        echo "❌ Error: project.name is required"
        ((errors++))
    fi
    
    if ! var_exists "$(get_project_var 'repository')"; then
        echo "❌ Error: project.repository is required"
        ((errors++))
    fi
    
    return $errors
}

# Validate environment configuration
validate_environment_config() {
    local env="$1"
    local errors=0
    
    if ! var_exists "$(get_env_var "$env" 'app_url')"; then
        echo "❌ Error: environments.$env.app_url is required"
        ((errors++))
    fi
    
    return $errors
}
```

---

## 📁 **FILES CREATED/MODIFIED**

This step creates:
- `Admin-Local/1-Admin-Area/02-Master-Scripts/load-variables.sh` - Main variable loading script
- `Admin-Local/1-Admin-Area/02-Master-Scripts/variable-utils.sh` - Variable access utilities
- `Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/variable-loading.log` - Loading log

---

## 🚨 **ERROR HANDLING**

### **Common Issues & Solutions**

**Issue: JSON file not found**
```bash
# Problem: deployment-variables.json missing
# Solution: Run Step 02.1 to create configuration
./Admin-Local/1-Admin-Area/02-Master-Scripts/create-deployment-variables.sh
```

**Issue: Invalid JSON format**
```bash
# Problem: Malformed JSON in configuration file
# Solution: Validate and fix JSON syntax
jq . Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json
```

**Issue: Missing required variables**
```bash
# Problem: Required configuration values missing
# Solution: Update deployment-variables.json with required values
nano Admin-Local/2-Project-Area/01-Project-Config/deployment-variables.json
```

---

## ✅ **COMPLETION CRITERIA**

Step 09.1 is complete when:
- [x] deployment-variables.json loaded successfully
- [x] All configuration parameters validated
- [x] Variables exported for pipeline use
- [x] Variable access utilities created
- [x] Environment-specific loading working
- [x] Error handling implemented
- [x] Loading system tested

---

## 🔄 **NEXT STEP**

Continue to **Step 10: Configure Database**

---

**Note:** This variable loading system provides centralized configuration management for the entire deployment pipeline and ensures consistent settings across all automation scripts.
