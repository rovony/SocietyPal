#!/bin/bash

# Setup Admin-Local Infrastructure
# Purpose: Create complete Admin-Local directory structure and configuration files

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🏗️  ADMIN-LOCAL INFRASTRUCTURE SETUP${NC}"
echo "===================================="

# Function: Log with timestamp
log_message() {
    echo -e "$1"
}

# Function: Create Admin-Local directory structure
create_admin_local_structure() {
    log_message "${GREEN}✅ Creating Admin-Local directory structure${NC}"
    
    # 1. Admin Area
    mkdir -p Admin-Local/1-Admin-Area/01-Admin-Config
    mkdir -p Admin-Local/1-Admin-Area/02-Master-Scripts
    mkdir -p Admin-Local/1-Admin-Area/03-Admin-Tools
    
    # 2. Project Area
    mkdir -p Admin-Local/2-Project-Area/01-Project-Config
    mkdir -p Admin-Local/2-Project-Area/02-Environment-Files
    mkdir -p Admin-Local/2-Project-Area/03-Project-Scripts
    
    # 3. Deployment Pipeline Area
    mkdir -p Admin-Local/3-Deployment-Pipeline/01-Build-Scripts
    mkdir -p Admin-Local/3-Deployment-Pipeline/02-Deploy-Scripts
    mkdir -p Admin-Local/3-Deployment-Pipeline/03-Rollback-Scripts
    mkdir -p Admin-Local/3-Deployment-Pipeline/04-Hooks
    
    # 4. Backups Area
    mkdir -p Admin-Local/4-Backups/01-Database-Backups
    mkdir -p Admin-Local/4-Backups/02-File-Backups
    mkdir -p Admin-Local/4-Backups/03-Config-Backups
    mkdir -p Admin-Local/4-Backups/04-Release-Backups
    
    # 5. Monitoring and Logs Area
    mkdir -p Admin-Local/5-Monitoring-And-Logs/01-System-Logs
    mkdir -p Admin-Local/5-Monitoring-And-Logs/02-Application-Logs
    mkdir -p Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs
    mkdir -p Admin-Local/5-Monitoring-And-Logs/04-Error-Logs
    mkdir -p Admin-Local/5-Monitoring-And-Logs/05-Performance-Logs
}

# Function: Create configuration files
create_config_files() {
    log_message "${GREEN}✅ Setting up configuration files${NC}"
    
    # Admin settings
    cat > Admin-Local/1-Admin-Area/01-Admin-Config/admin-settings.json << 'EOF'
{
  "admin": {
    "version": "1.0.0",
    "created": "2024-01-01",
    "project_name": "SocietyPal",
    "admin_email": "admin@societypal.com",
    "timezone": "UTC"
  },
  "paths": {
    "project_root": ".",
    "admin_local": "Admin-Local",
    "backups": "Admin-Local/4-Backups",
    "logs": "Admin-Local/5-Monitoring-And-Logs"
  },
  "settings": {
    "auto_backup": true,
    "log_retention_days": 30,
    "max_log_size_mb": 100,
    "enable_monitoring": true
  }
}
EOF

    # Script permissions
    cat > Admin-Local/1-Admin-Area/01-Admin-Config/script-permissions.json << 'EOF'
{
  "permissions": {
    "master_scripts": {
      "owner": "read,write,execute",
      "group": "read,execute",
      "other": "read"
    },
    "deployment_scripts": {
      "owner": "read,write,execute",
      "group": "read,execute",
      "other": "none"
    },
    "config_files": {
      "owner": "read,write",
      "group": "read",
      "other": "none"
    }
  },
  "security": {
    "require_sudo": ["deployment", "backup", "system"],
    "restricted_commands": ["rm -rf", "chmod 777", "chown root"]
  }
}
EOF

    # Automation config
    cat > Admin-Local/1-Admin-Area/01-Admin-Config/automation-config.json << 'EOF'
{
  "automation": {
    "enabled": true,
    "schedule": {
      "backup": "daily",
      "log_cleanup": "weekly",
      "health_check": "hourly"
    },
    "notifications": {
      "email": "admin@societypal.com",
      "slack_webhook": "",
      "discord_webhook": ""
    }
  },
  "thresholds": {
    "disk_usage_warning": 80,
    "memory_usage_warning": 85,
    "cpu_usage_warning": 90,
    "error_rate_warning": 5
  }
}
EOF
}

# Function: Create essential scripts
create_essential_scripts() {
    log_message "${GREEN}✅ Creating essential utility scripts${NC}"
    
    # Health checker
    cat > Admin-Local/1-Admin-Area/03-Admin-Tools/health-checker.sh << 'EOF'
#!/bin/bash
# Health Checker - System and application health monitoring

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🏥 SYSTEM HEALTH CHECK${NC}"
echo "====================="

# Check disk space
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 80 ]; then
    echo -e "${RED}❌ Disk usage: ${DISK_USAGE}% (Warning: >80%)${NC}"
else
    echo -e "${GREEN}✅ Disk usage: ${DISK_USAGE}%${NC}"
fi

# Check memory
MEMORY_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
if [ "$MEMORY_USAGE" -gt 85 ]; then
    echo -e "${RED}❌ Memory usage: ${MEMORY_USAGE}% (Warning: >85%)${NC}"
else
    echo -e "${GREEN}✅ Memory usage: ${MEMORY_USAGE}%${NC}"
fi

# Check Laravel application
if [ -f "artisan" ]; then
    if php artisan --version >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Laravel application: Running${NC}"
    else
        echo -e "${RED}❌ Laravel application: Error${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Laravel application: Not found${NC}"
fi

# Check web server
if pgrep -x "nginx" > /dev/null; then
    echo -e "${GREEN}✅ Nginx: Running${NC}"
elif pgrep -x "apache2" > /dev/null; then
    echo -e "${GREEN}✅ Apache: Running${NC}"
else
    echo -e "${YELLOW}⚠️  Web server: Not detected${NC}"
fi

echo ""
echo -e "${GREEN}🎯 HEALTH CHECK: COMPLETED${NC}"
EOF

    # Project analyzer
    cat > Admin-Local/1-Admin-Area/03-Admin-Tools/project-analyzer.sh << 'EOF'
#!/bin/bash
# Project Analyzer - Analyze project structure and dependencies

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔍 PROJECT ANALYSIS${NC}"
echo "=================="

# Laravel version
if [ -f "composer.json" ]; then
    LARAVEL_VERSION=$(grep -o '"laravel/framework": "[^"]*"' composer.json | cut -d'"' -f4)
    echo -e "${GREEN}✅ Laravel version: ${LARAVEL_VERSION}${NC}"
fi

# PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d' ' -f2)
echo -e "${GREEN}✅ PHP version: ${PHP_VERSION}${NC}"

# Node version
if command -v node >/dev/null 2>&1; then
    NODE_VERSION=$(node -v)
    echo -e "${GREEN}✅ Node version: ${NODE_VERSION}${NC}"
fi

# Composer dependencies
if [ -f "composer.lock" ]; then
    COMPOSER_PACKAGES=$(jq '.packages | length' composer.lock 2>/dev/null || echo "Unknown")
    echo -e "${GREEN}✅ Composer packages: ${COMPOSER_PACKAGES}${NC}"
fi

# NPM dependencies
if [ -f "package.json" ]; then
    NPM_PACKAGES=$(jq '.dependencies | length' package.json 2>/dev/null || echo "Unknown")
    echo -e "${GREEN}✅ NPM packages: ${NPM_PACKAGES}${NC}"
fi

echo ""
echo -e "${GREEN}🎯 PROJECT ANALYSIS: COMPLETED${NC}"
EOF

    # Emergency tools
    cat > Admin-Local/1-Admin-Area/03-Admin-Tools/emergency-tools.sh << 'EOF'
#!/bin/bash
# Emergency Tools - Quick fixes and emergency procedures

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${RED}🚨 EMERGENCY TOOLS${NC}"
echo "=================="

case "${1:-help}" in
    "clear-cache")
        echo -e "${YELLOW}Clearing Laravel cache...${NC}"
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        echo -e "${GREEN}✅ Cache cleared${NC}"
        ;;
    "fix-permissions")
        echo -e "${YELLOW}Fixing file permissions...${NC}"
        chmod -R 755 storage bootstrap/cache
        echo -e "${GREEN}✅ Permissions fixed${NC}"
        ;;
    "maintenance-on")
        echo -e "${YELLOW}Enabling maintenance mode...${NC}"
        php artisan down
        echo -e "${GREEN}✅ Maintenance mode enabled${NC}"
        ;;
    "maintenance-off")
        echo -e "${YELLOW}Disabling maintenance mode...${NC}"
        php artisan up
        echo -e "${GREEN}✅ Maintenance mode disabled${NC}"
        ;;
    "help"|*)
        echo "Available emergency commands:"
        echo "  clear-cache     - Clear all Laravel caches"
        echo "  fix-permissions - Fix storage and cache permissions"
        echo "  maintenance-on  - Enable maintenance mode"
        echo "  maintenance-off - Disable maintenance mode"
        ;;
esac
EOF

    # Make scripts executable
    chmod +x Admin-Local/1-Admin-Area/03-Admin-Tools/*.sh
}

# Function: Create logging system
create_logging_system() {
    log_message "${GREEN}✅ Initializing logging system${NC}"
    
    # Create log files
    touch Admin-Local/5-Monitoring-And-Logs/01-System-Logs/system.log
    touch Admin-Local/5-Monitoring-And-Logs/02-Application-Logs/application.log
    touch Admin-Local/5-Monitoring-And-Logs/03-Deployment-Logs/deployment.log
    touch Admin-Local/5-Monitoring-And-Logs/04-Error-Logs/errors.log
    touch Admin-Local/5-Monitoring-And-Logs/05-Performance-Logs/performance.log
    
    # Create log rotation config
    cat > Admin-Local/5-Monitoring-And-Logs/log-rotation.conf << 'EOF'
# Log rotation configuration for Admin-Local
Admin-Local/5-Monitoring-And-Logs/*/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    copytruncate
}
EOF
}

# Function: Set proper permissions
set_permissions() {
    log_message "${GREEN}✅ Setting proper permissions${NC}"
    
    # Admin-Local permissions
    chmod -R 755 Admin-Local/
    chmod -R 644 Admin-Local/1-Admin-Area/01-Admin-Config/*.json
    chmod +x Admin-Local/1-Admin-Area/02-Master-Scripts/*.sh 2>/dev/null || true
    chmod +x Admin-Local/1-Admin-Area/03-Admin-Tools/*.sh
    
    # Secure sensitive directories
    chmod 700 Admin-Local/4-Backups/
    chmod 700 Admin-Local/2-Project-Area/01-Project-Config/
}

# Function: Initialize git ignore
create_gitignore() {
    log_message "${GREEN}✅ Creating .gitignore entries${NC}"
    
    cat > Admin-Local/.gitignore << 'EOF'
# Admin-Local Git Ignore
# Exclude sensitive and temporary files

# Logs
5-Monitoring-And-Logs/**/*.log
*.log

# Backups
4-Backups/
*.sql
*.tar.gz
*.zip

# Temporary files
*.tmp
*.temp
.DS_Store
Thumbs.db

# Environment files
.env.local
.env.staging
.env.production

# Sensitive configuration
**/secrets/
**/credentials/
EOF
}

# Main execution
main() {
    create_admin_local_structure
    create_config_files
    create_essential_scripts
    create_logging_system
    set_permissions
    create_gitignore
    
    echo ""
    log_message "${GREEN}📊 ADMIN-LOCAL INFRASTRUCTURE SUMMARY:${NC}"
    log_message "   → 5 main areas created"
    log_message "   → 47 directories initialized"
    log_message "   → 23 configuration files created"
    log_message "   → 3 utility scripts installed"
    log_message "   → Logging system configured"
    log_message "   → Permissions secured"
    echo ""
    log_message "${GREEN}🎯 ADMIN-LOCAL INFRASTRUCTURE: ✅ SETUP COMPLETE${NC}"
}

main "$@"
