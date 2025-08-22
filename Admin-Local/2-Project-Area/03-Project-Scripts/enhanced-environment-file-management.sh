#!/bin/bash

# Enhanced Environment File Management
# Purpose: Create and manage environment-specific .env files

set -e

# Configuration
PROJECT_ROOT=$(pwd)
ENV_DIR="Admin-Local/2-Project-Area/02-Environment-Files"
LOG_FILE="Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/environment-management.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔧 ENHANCED ENVIRONMENT FILE MANAGEMENT${NC}"
echo "======================================="

# Function: Log with timestamp
log_message() {
    mkdir -p "$(dirname "$LOG_FILE")"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function: Create environment directories
create_env_directories() {
    mkdir -p "$ENV_DIR"
    mkdir -p "$ENV_DIR/backups"
    mkdir -p "$ENV_DIR/templates"
    log_message "${GREEN}✅ Creating environment file directories${NC}"
}

# Function: Create .env.local
create_env_local() {
    cat > .env.local << 'EOF'
APP_NAME="SocietyPal Local"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=societypal_local
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Local Development Settings
DEBUGBAR_ENABLED=true
TELESCOPE_ENABLED=true
CLOCKWORK_ENABLE=true

# Security Settings (Relaxed for local)
SECURE_COOKIES=false
FORCE_HTTPS=false
HSTS_ENABLED=false
EOF
    
    log_message "${GREEN}✅ Creating .env.local configuration${NC}"
}

# Function: Create .env.staging
create_env_staging() {
    cat > .env.staging << 'EOF'
APP_NAME="SocietyPal Staging"
APP_ENV=staging
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://staging.societypal.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single,errorlog
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=${DB_HOST_STAGING}
DB_PORT=3306
DB_DATABASE=${DB_DATABASE_STAGING}
DB_USERNAME=${DB_USERNAME_STAGING}
DB_PASSWORD=${DB_PASSWORD_STAGING}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.staging.societypal.com
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=redis
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=societypal_staging

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=${REDIS_HOST_STAGING}
REDIS_PASSWORD=${REDIS_PASSWORD_STAGING}
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=${MAIL_HOST_STAGING}
MAIL_PORT=587
MAIL_USERNAME=${MAIL_USERNAME_STAGING}
MAIL_PASSWORD=${MAIL_PASSWORD_STAGING}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@staging.societypal.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID_STAGING}
AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY_STAGING}
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=${AWS_BUCKET_STAGING}
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Staging Development Settings
DEBUGBAR_ENABLED=true
TELESCOPE_ENABLED=true
CLOCKWORK_ENABLE=false

# Security Settings (Enhanced for staging)
SECURE_COOKIES=true
FORCE_HTTPS=true
HSTS_ENABLED=true
HSTS_MAX_AGE=31536000
EOF
    
    log_message "${GREEN}✅ Creating .env.staging configuration${NC}"
}

# Function: Create .env.production
create_env_production() {
    cat > .env.production << 'EOF'
APP_NAME="SocietyPal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://societypal.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single,errorlog,syslog
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST_PRODUCTION}
DB_PORT=3306
DB_DATABASE=${DB_DATABASE_PRODUCTION}
DB_USERNAME=${DB_USERNAME_PRODUCTION}
DB_PASSWORD=${DB_PASSWORD_PRODUCTION}

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.societypal.com
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=redis
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=societypal_prod

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=${REDIS_HOST_PRODUCTION}
REDIS_PASSWORD=${REDIS_PASSWORD_PRODUCTION}
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=${MAIL_HOST_PRODUCTION}
MAIL_PORT=587
MAIL_USERNAME=${MAIL_USERNAME_PRODUCTION}
MAIL_PASSWORD=${MAIL_PASSWORD_PRODUCTION}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@societypal.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID_PRODUCTION}
AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY_PRODUCTION}
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=${AWS_BUCKET_PRODUCTION}
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Production Settings (Disabled for security)
DEBUGBAR_ENABLED=false
TELESCOPE_ENABLED=false
CLOCKWORK_ENABLE=false

# Security Settings (Maximum security for production)
SECURE_COOKIES=true
FORCE_HTTPS=true
HSTS_ENABLED=true
HSTS_MAX_AGE=31536000
HSTS_INCLUDE_SUBDOMAINS=true
HSTS_PRELOAD=true

# Additional Production Security
CONTENT_SECURITY_POLICY=true
X_FRAME_OPTIONS=DENY
X_CONTENT_TYPE_OPTIONS=nosniff
REFERRER_POLICY=strict-origin-when-cross-origin
EOF
    
    log_message "${GREEN}✅ Creating .env.production configuration${NC}"
}

# Function: Validate environment files
validate_environment_files() {
    local files=(".env.local" ".env.staging" ".env.production")
    
    for file in "${files[@]}"; do
        if [[ -f "$file" ]]; then
            # Check for required variables
            local required_vars=("APP_NAME" "APP_ENV" "APP_URL" "DB_CONNECTION")
            local missing_vars=()
            
            for var in "${required_vars[@]}"; do
                if ! grep -q "^${var}=" "$file"; then
                    missing_vars+=("$var")
                fi
            done
            
            if [[ ${#missing_vars[@]} -eq 0 ]]; then
                log_message "${GREEN}✅ Validating $file - All required variables present${NC}"
            else
                log_message "${YELLOW}⚠️  $file missing variables: ${missing_vars[*]}${NC}"
            fi
        else
            log_message "${RED}❌ $file not found${NC}"
        fi
    done
}

# Function: Create environment switcher
create_env_switcher() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/switch-environment.sh << 'EOF'
#!/bin/bash

# Environment Switcher
# Purpose: Switch between different environment configurations

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Function: Display usage
show_usage() {
    echo -e "${BLUE}🔄 ENVIRONMENT SWITCHER${NC}"
    echo "======================="
    echo ""
    echo "Usage: $0 [environment]"
    echo ""
    echo "Available environments:"
    echo "  local      - Local development environment"
    echo "  staging    - Staging environment"
    echo "  production - Production environment"
    echo ""
    echo "Examples:"
    echo "  $0 local"
    echo "  $0 staging"
    echo "  $0 production"
}

# Function: Switch environment
switch_environment() {
    local env="$1"
    local env_file=".env.$env"
    
    if [[ ! -f "$env_file" ]]; then
        echo -e "${RED}❌ Error: $env_file not found${NC}"
        echo "Available environment files:"
        ls -la .env.* 2>/dev/null || echo "No environment files found"
        exit 1
    fi
    
    # Backup current .env if it exists
    if [[ -f ".env" ]]; then
        cp .env "Admin-Local/2-Project-Area/02-Environment-Files/backups/.env.backup.$(date +%Y%m%d_%H%M%S)"
        echo -e "${YELLOW}📁 Backed up current .env${NC}"
    fi
    
    # Switch to new environment
    cp "$env_file" .env
    echo -e "${GREEN}✅ Switched to $env environment${NC}"
    
    # Display current environment info
    echo ""
    echo -e "${BLUE}📊 CURRENT ENVIRONMENT:${NC}"
    echo "   → Name: $(grep '^APP_NAME=' .env | cut -d'=' -f2 | tr -d '"')"
    echo "   → Environment: $(grep '^APP_ENV=' .env | cut -d'=' -f2)"
    echo "   → URL: $(grep '^APP_URL=' .env | cut -d'=' -f2)"
    echo "   → Debug: $(grep '^APP_DEBUG=' .env | cut -d'=' -f2)"
}

# Main execution
main() {
    case "${1:-help}" in
        "local"|"staging"|"production")
            switch_environment "$1"
            ;;
        "help"|"--help"|"-h"|"")
            show_usage
            ;;
        *)
            echo -e "${RED}❌ Error: Unknown environment '$1'${NC}"
            echo ""
            show_usage
            exit 1
            ;;
    esac
}

main "$@"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/switch-environment.sh
    log_message "${GREEN}✅ Creating environment switching utilities${NC}"
}

# Function: Create environment backup system
create_env_backup_system() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/backup-environments.sh << 'EOF'
#!/bin/bash

# Environment Backup System
# Purpose: Backup all environment files

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}💾 ENVIRONMENT BACKUP SYSTEM${NC}"
echo "============================="

# Create backup directory
BACKUP_DIR="Admin-Local/2-Project-Area/02-Environment-Files/backups"
mkdir -p "$BACKUP_DIR"

# Create timestamped backup
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_ARCHIVE="$BACKUP_DIR/env-backup-$TIMESTAMP.tar.gz"

# Backup all environment files
tar -czf "$BACKUP_ARCHIVE" .env* 2>/dev/null || true

if [[ -f "$BACKUP_ARCHIVE" ]]; then
    echo -e "${GREEN}✅ Environment files backed up to: $BACKUP_ARCHIVE${NC}"
    
    # Clean old backups (keep last 10)
    cd "$BACKUP_DIR"
    ls -t env-backup-*.tar.gz 2>/dev/null | tail -n +11 | xargs rm -f 2>/dev/null || true
    cd - >/dev/null
    
    echo -e "${GREEN}✅ Old backups cleaned (keeping last 10)${NC}"
else
    echo -e "${RED}❌ Backup failed${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}🎯 ENVIRONMENT BACKUP: ✅ COMPLETED${NC}"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/backup-environments.sh
    log_message "${GREEN}✅ Creating environment backup system${NC}"
}

# Function: Create environment validator
create_env_validator() {
    cat > Admin-Local/2-Project-Area/03-Project-Scripts/validate-environment.sh << 'EOF'
#!/bin/bash

# Environment Validator
# Purpose: Validate environment file configuration

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔍 ENVIRONMENT VALIDATOR${NC}"
echo "========================"

# Function: Validate environment file
validate_env_file() {
    local file="$1"
    local errors=0
    
    echo -e "${BLUE}Validating $file...${NC}"
    
    if [[ ! -f "$file" ]]; then
        echo -e "${RED}❌ File not found: $file${NC}"
        return 1
    fi
    
    # Required variables
    local required_vars=(
        "APP_NAME"
        "APP_ENV" 
        "APP_KEY"
        "APP_URL"
        "DB_CONNECTION"
        "DB_HOST"
        "DB_DATABASE"
        "DB_USERNAME"
    )
    
    # Check required variables
    for var in "${required_vars[@]}"; do
        if ! grep -q "^${var}=" "$file"; then
            echo -e "${RED}❌ Missing required variable: $var${NC}"
            ((errors++))
        elif grep -q "^${var}=$" "$file"; then
            echo -e "${YELLOW}⚠️  Empty value for: $var${NC}"
        else
            echo -e "${GREEN}✅ $var${NC}"
        fi
    done
    
    # Environment-specific validations
    local env=$(grep '^APP_ENV=' "$file" | cut -d'=' -f2)
    
    case "$env" in
        "production")
            # Production-specific checks
            if grep -q "^APP_DEBUG=true" "$file"; then
                echo -e "${RED}❌ APP_DEBUG should be false in production${NC}"
                ((errors++))
            fi
            if ! grep -q "^SECURE_COOKIES=true" "$file"; then
                echo -e "${YELLOW}⚠️  SECURE_COOKIES should be true in production${NC}"
            fi
            ;;
        "staging")
            # Staging-specific checks
            if grep -q "^APP_DEBUG=true" "$file"; then
                echo -e "${YELLOW}⚠️  Consider setting APP_DEBUG=false in staging${NC}"
            fi
            ;;
        "local")
            # Local-specific checks
            if ! grep -q "^APP_DEBUG=true" "$file"; then
                echo -e "${YELLOW}⚠️  APP_DEBUG should typically be true in local${NC}"
            fi
            ;;
    esac
    
    echo ""
    if [[ $errors -eq 0 ]]; then
        echo -e "${GREEN}✅ $file validation passed${NC}"
        return 0
    else
        echo -e "${RED}❌ $file validation failed with $errors errors${NC}"
        return 1
    fi
}

# Main execution
main() {
    local target_file="${1:-.env}"
    
    if [[ "$1" == "all" ]]; then
        # Validate all environment files
        local files=(".env.local" ".env.staging" ".env.production")
        local total_errors=0
        
        for file in "${files[@]}"; do
            if [[ -f "$file" ]]; then
                validate_env_file "$file"
                if [[ $? -ne 0 ]]; then
                    ((total_errors++))
                fi
                echo ""
            fi
        done
        
        if [[ $total_errors -eq 0 ]]; then
            echo -e "${GREEN}🎯 ALL ENVIRONMENT FILES: ✅ VALIDATION PASSED${NC}"
        else
            echo -e "${RED}🎯 VALIDATION FAILED: $total_errors files have errors${NC}"
            exit 1
        fi
    else
        # Validate single file
        validate_env_file "$target_file"
    fi
}

main "$@"
EOF
    
    chmod +x Admin-Local/2-Project-Area/03-Project-Scripts/validate-environment.sh
    log_message "${GREEN}✅ Creating environment validation utilities${NC}"
}

# Main execution
main() {
    create_env_directories
    create_env_local
    create_env_staging
    create_env_production
    validate_environment_files
    create_env_switcher
    create_env_backup_system
    create_env_validator
    
    # Set current environment to local if no .env exists
    if [[ ! -f ".env" ]]; then
        cp .env.local .env
        log_message "📁 Set current environment to local (.env)"
    fi
    
    echo ""
    log_message "${GREEN}📊 ENVIRONMENT MANAGEMENT SUMMARY:${NC}"
    log_message "   → 3 environment files created (.env.local, .env.staging, .env.production)"
    log_message "   → Environment switcher utility installed"
    log_message "   → Backup system configured"
    log_message "   → Validation system created"
    log_message "   → Current environment: $(grep '^APP_ENV=' .env | cut -d'=' -f2 2>/dev/null || echo 'local')"
    echo ""
    log_message "${GREEN}🎯 ENVIRONMENT MANAGEMENT: ✅ CONFIGURED SUCCESSFULLY${NC}"
}

main "$@"
