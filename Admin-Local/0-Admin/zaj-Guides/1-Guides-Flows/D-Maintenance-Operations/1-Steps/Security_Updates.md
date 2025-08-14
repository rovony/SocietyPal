# Security Updates

**Purpose:** Comprehensive security update procedures, vulnerability management, and security hardening for Laravel applications.

**Use Case:** Ongoing security maintenance, threat mitigation, and compliance management

---

## **Analysis Source**

Based on **Laravel - Final Guides/V2/phase4_deployment_guide.md** security considerations and ongoing maintenance requirements.

---

## **1. Security Update Strategy**

### **1.1: Multi-Layer Security Approach**

**Security Update Layers:**

```bash
cat > ~/security_strategy.md << 'EOF'
# Comprehensive Security Update Strategy

## Security Layers

### Layer 1: System Level Security
- **Operating System Updates** (security patches)
- **Web Server Updates** (Nginx/Apache security fixes)
- **PHP Runtime Updates** (security releases)
- **Database Security** (MySQL/PostgreSQL patches)

### Layer 2: Application Security
- **Laravel Framework** (security releases)
- **Composer Dependencies** (vulnerability fixes)
- **NPM Packages** (JavaScript security updates)
- **CodeCanyon Vendor Updates** (security patches)

### Layer 3: Configuration Security
- **SSL Certificate Management** (renewal, updates)
- **Firewall Rules** (access control updates)
- **SSH Security** (key rotation, hardening)
- **Environment Variables** (credential rotation)

## Update Priorities
1. **Critical**: Remote code execution, SQL injection
2. **High**: Authentication bypass, privilege escalation
3. **Medium**: Information disclosure, denial of service
4. **Low**: Minor security improvements

## Update Schedule
- **Critical**: Immediate (within 24 hours)
- **High**: Weekly security window
- **Medium**: Monthly maintenance window
- **Low**: Quarterly updates
EOF

echo "✅ Security strategy documented"
```

---

## **2. System Security Updates**

### **2.1: Create System Security Update Script**

```bash
cat > ~/system_security_updates.sh << 'EOF'
#!/bin/bash

echo "🔒 System Security Updates - $(date)"
echo "=================================="

SECURITY_LOG="$HOME/security_updates.log"
UPDATE_LOG="/tmp/security_update_$(date +%Y%m%d_%H%M%S).log"

# Function: Log security action
log_security() {
    local message="$1"
    echo "$(date): $message" | tee -a "$SECURITY_LOG"
}

# Function: Check for available updates
check_system_updates() {
    echo ""
    echo "🔍 Checking for System Updates:"
    echo "==============================="

    # Update package lists
    echo "📦 Updating package lists..."
    if sudo apt update > "$UPDATE_LOG" 2>&1; then
        echo "✅ Package lists updated"
    else
        echo "❌ Failed to update package lists"
        return 1
    fi

    # Check for security updates
    echo ""
    echo "🔒 Available Security Updates:"
    security_updates=$(apt list --upgradable 2>/dev/null | grep -i security | wc -l)
    total_updates=$(apt list --upgradable 2>/dev/null | grep -v "WARNING" | wc -l)

    echo "Security updates: $security_updates"
    echo "Total updates: $total_updates"

    if [ "$security_updates" -gt 0 ]; then
        echo ""
        echo "📋 Security Update Details:"
        apt list --upgradable 2>/dev/null | grep -i security | head -10
    fi

    return 0
}

# Function: Apply security updates
apply_security_updates() {
    echo ""
    echo "🚀 Applying Security Updates:"
    echo "============================"

    # Check if any security updates available
    security_count=$(apt list --upgradable 2>/dev/null | grep -i security | wc -l)

    if [ "$security_count" -eq 0 ]; then
        echo "✅ No security updates available"
        log_security "No security updates available"
        return 0
    fi

    echo "📦 Found $security_count security updates"

    # Confirmation for security updates
    echo ""
    echo "⚠️ Security updates will be applied automatically"
    echo "This may require system restart for kernel updates"

    read -p "🔴 Continue with security updates? (y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        echo "❌ Security updates cancelled"
        return 1
    fi

    # Apply security-only updates
    echo ""
    echo "🔧 Installing security updates..."

    # Use unattended-upgrades if available, otherwise apt upgrade
    if command -v unattended-upgrade &> /dev/null; then
        echo "Using unattended-upgrades for security updates..."
        sudo unattended-upgrade -d 2>&1 | tee -a "$UPDATE_LOG"
    else
        echo "Using apt for security updates..."
        sudo apt upgrade -y 2>&1 | tee -a "$UPDATE_LOG"
    fi

    if [ $? -eq 0 ]; then
        echo "✅ Security updates applied successfully"
        log_security "Security updates applied - $security_count updates"

        # Check if reboot required
        if [ -f /var/run/reboot-required ]; then
            echo ""
            echo "🔄 REBOOT REQUIRED"
            echo "Kernel or system updates require restart"
            echo "Reboot during next maintenance window"
            log_security "REBOOT REQUIRED after security updates"
        fi

        return 0
    else
        echo "❌ Security updates failed"
        log_security "ERROR: Security updates failed"
        return 1
    fi
}

# Function: Update web server
update_web_server() {
    echo ""
    echo "🌐 Web Server Security:"
    echo "======================"

    # Check Nginx version
    if command -v nginx &> /dev/null; then
        NGINX_VERSION=$(nginx -v 2>&1 | grep -o '[0-9.]*')
        echo "🔧 Current Nginx version: $NGINX_VERSION"

        # Check if Nginx update available
        nginx_update=$(apt list --upgradable 2>/dev/null | grep nginx)
        if [ -n "$nginx_update" ]; then
            echo "📦 Nginx update available: $nginx_update"
        else
            echo "✅ Nginx is up to date"
        fi
    fi

    # Check Apache version (if installed)
    if command -v apache2 &> /dev/null; then
        APACHE_VERSION=$(apache2 -v | head -1 | grep -o '[0-9.]*')
        echo "🔧 Current Apache version: $APACHE_VERSION"

        apache_update=$(apt list --upgradable 2>/dev/null | grep apache2)
        if [ -n "$apache_update" ]; then
            echo "📦 Apache update available: $apache_update"
        else
            echo "✅ Apache is up to date"
        fi
    fi
}

# Function: Update PHP
update_php() {
    echo ""
    echo "🐘 PHP Security:"
    echo "==============="

    # Check PHP version
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -v | head -1 | grep -o 'PHP [0-9.]*' | cut -d' ' -f2)
        echo "🔧 Current PHP version: $PHP_VERSION"

        # Check for PHP updates
        php_updates=$(apt list --upgradable 2>/dev/null | grep php | wc -l)
        if [ "$php_updates" -gt 0 ]; then
            echo "📦 PHP updates available: $php_updates packages"
            apt list --upgradable 2>/dev/null | grep php | head -5
        else
            echo "✅ PHP is up to date"
        fi

        # Check PHP security configuration
        echo ""
        echo "🔒 PHP Security Configuration:"

        # Check expose_php
        expose_php=$(php -r "echo ini_get('expose_php') ? 'On' : 'Off';")
        echo "expose_php: $expose_php $([ "$expose_php" = "Off" ] && echo "✅" || echo "⚠️")"

        # Check display_errors
        display_errors=$(php -r "echo ini_get('display_errors') ? 'On' : 'Off';")
        echo "display_errors: $display_errors $([ "$display_errors" = "Off" ] && echo "✅" || echo "⚠️")"

        # Check allow_url_fopen
        allow_url_fopen=$(php -r "echo ini_get('allow_url_fopen') ? 'On' : 'Off';")
        echo "allow_url_fopen: $allow_url_fopen $([ "$allow_url_fopen" = "Off" ] && echo "✅" || echo "⚠️")"
    fi
}

# Function: Update database
update_database() {
    echo ""
    echo "🗃️ Database Security:"
    echo "===================="

    # Check MySQL version
    if command -v mysql &> /dev/null; then
        MYSQL_VERSION=$(mysql -V | grep -o '[0-9.]*-[0-9]*' | head -1)
        echo "🔧 Current MySQL version: $MYSQL_VERSION"

        mysql_updates=$(apt list --upgradable 2>/dev/null | grep mysql | wc -l)
        if [ "$mysql_updates" -gt 0 ]; then
            echo "📦 MySQL updates available: $mysql_updates packages"
        else
            echo "✅ MySQL is up to date"
        fi
    fi

    # Check PostgreSQL version (if installed)
    if command -v psql &> /dev/null; then
        POSTGRES_VERSION=$(psql --version | grep -o '[0-9.]*')
        echo "🔧 Current PostgreSQL version: $POSTGRES_VERSION"

        postgres_updates=$(apt list --upgradable 2>/dev/null | grep postgresql | wc -l)
        if [ "$postgres_updates" -gt 0 ]; then
            echo "📦 PostgreSQL updates available: $postgres_updates packages"
        else
            echo "✅ PostgreSQL is up to date"
        fi
    fi
}

# Function: Security hardening check
security_hardening_check() {
    echo ""
    echo "🛡️ Security Hardening Check:"
    echo "==========================="

    # Check SSH configuration
    echo "🔑 SSH Security:"

    # Check SSH root login
    root_login=$(sudo grep -i "^PermitRootLogin" /etc/ssh/sshd_config 2>/dev/null | awk '{print $2}')
    echo "SSH root login: ${root_login:-'not configured'} $([ "$root_login" = "no" ] && echo "✅" || echo "⚠️")"

    # Check SSH password authentication
    password_auth=$(sudo grep -i "^PasswordAuthentication" /etc/ssh/sshd_config 2>/dev/null | awk '{print $2}')
    echo "SSH password auth: ${password_auth:-'not configured'} $([ "$password_auth" = "no" ] && echo "✅" || echo "⚠️")"

    # Check firewall status
    echo ""
    echo "🔥 Firewall Status:"
    if command -v ufw &> /dev/null; then
        ufw_status=$(sudo ufw status | head -1 | awk '{print $2}')
        echo "UFW status: ${ufw_status:-'inactive'} $([ "$ufw_status" = "active" ] && echo "✅" || echo "⚠️")"
    elif command -v iptables &> /dev/null; then
        iptables_rules=$(sudo iptables -L | grep -c "Chain")
        echo "iptables rules: $iptables_rules $([ "$iptables_rules" -gt 3 ] && echo "✅" || echo "⚠️")"
    else
        echo "Firewall: not configured ⚠️"
    fi

    # Check fail2ban
    echo ""
    echo "🚫 Intrusion Prevention:"
    if command -v fail2ban-client &> /dev/null; then
        fail2ban_status=$(sudo systemctl is-active fail2ban 2>/dev/null)
        echo "fail2ban: ${fail2ban_status:-'inactive'} $([ "$fail2ban_status" = "active" ] && echo "✅" || echo "⚠️")"
    else
        echo "fail2ban: not installed ⚠️"
    fi
}

# Main execution
echo "🚀 Starting system security updates..."

# Log start
log_security "System security update check started"

# Check for updates
if check_system_updates; then
    echo "✅ Update check completed"
else
    echo "❌ Update check failed"
    exit 1
fi

# Apply security updates
if apply_security_updates; then
    echo "✅ Security updates completed"
else
    echo "❌ Security updates failed"
fi

# Check web server updates
update_web_server

# Check PHP updates
update_php

# Check database updates
update_database

# Security hardening check
security_hardening_check

# Final summary
echo ""
echo "📊 Security Update Summary:"
echo "=========================="
echo "Update log: $UPDATE_LOG"
echo "Security log: $SECURITY_LOG"

# Check overall security status
security_issues=0

# Count security warnings from checks above
if grep -q "⚠️" "$UPDATE_LOG" 2>/dev/null; then
    ((security_issues++))
fi

if [ "$security_issues" -eq 0 ]; then
    echo "🎉 System security status: EXCELLENT"
    log_security "System security update completed - no issues"
else
    echo "⚠️ System security status: NEEDS ATTENTION ($security_issues issues)"
    log_security "System security update completed - $security_issues issues found"
fi

echo ""
echo "✅ System security update completed"
EOF

chmod +x ~/system_security_updates.sh

echo "✅ System security update script created"
```

### **2.2: Create Automated Security Monitoring**

```bash
cat > ~/security_monitor.sh << 'EOF'
#!/bin/bash

echo "👁️ Security Monitoring - $(date)"
echo "==============================="

SECURITY_LOG="$HOME/security_monitor.log"
ALERT_LOG="$HOME/security_alerts.log"

# Function: Log security event
log_security_event() {
    local level="$1"
    local message="$2"
    echo "$(date): [$level] $message" | tee -a "$SECURITY_LOG"

    # Also log alerts separately
    if [ "$level" = "ALERT" ] || [ "$level" = "CRITICAL" ]; then
        echo "$(date): [$level] $message" >> "$ALERT_LOG"
    fi
}

# Function: Check authentication logs
check_auth_logs() {
    echo ""
    echo "🔐 Authentication Security:"
    echo "=========================="

    # Check for failed SSH login attempts
    failed_ssh=$(grep "Failed password" /var/log/auth.log 2>/dev/null | grep "$(date '+%b %d')" | wc -l)
    echo "Failed SSH attempts today: $failed_ssh"

    if [ "$failed_ssh" -gt 10 ]; then
        log_security_event "ALERT" "High number of failed SSH attempts: $failed_ssh"
        echo "🚨 ALERT: High number of failed SSH attempts"
    elif [ "$failed_ssh" -gt 5 ]; then
        log_security_event "WARNING" "Moderate failed SSH attempts: $failed_ssh"
        echo "⚠️ WARNING: Moderate failed SSH attempts"
    else
        log_security_event "INFO" "Normal SSH activity: $failed_ssh failed attempts"
        echo "✅ Normal SSH activity"
    fi

    # Check for successful root logins
    root_logins=$(grep "Accepted.*root" /var/log/auth.log 2>/dev/null | grep "$(date '+%b %d')" | wc -l)
    if [ "$root_logins" -gt 0 ]; then
        log_security_event "ALERT" "Root login detected: $root_logins times"
        echo "🚨 ALERT: Root login detected ($root_logins times)"
    fi

    # Check for new user accounts
    new_users=$(grep "new user" /var/log/auth.log 2>/dev/null | grep "$(date '+%b %d')" | wc -l)
    if [ "$new_users" -gt 0 ]; then
        log_security_event "WARNING" "New user accounts created: $new_users"
        echo "⚠️ WARNING: New user accounts created ($new_users)"
    fi
}

# Function: Check web server logs
check_web_logs() {
    echo ""
    echo "🌐 Web Server Security:"
    echo "======================"

    ACCESS_LOG="/var/log/nginx/access.log"
    ERROR_LOG="/var/log/nginx/error.log"

    if [ -f "$ACCESS_LOG" ]; then
        # Check for suspicious requests
        suspicious_requests=$(grep "$(date '+%d/%b/%Y')" "$ACCESS_LOG" 2>/dev/null | grep -iE "(union|select|drop|insert|update|delete|script|alert|eval)" | wc -l)
        echo "Suspicious web requests today: $suspicious_requests"

        if [ "$suspicious_requests" -gt 5 ]; then
            log_security_event "ALERT" "High number of suspicious web requests: $suspicious_requests"
            echo "🚨 ALERT: High number of suspicious web requests"
        elif [ "$suspicious_requests" -gt 0 ]; then
            log_security_event "WARNING" "Suspicious web requests detected: $suspicious_requests"
            echo "⚠️ WARNING: Suspicious web requests detected"
        fi

        # Check for 404 errors (possible scanning)
        error_404=$(grep "$(date '+%d/%b/%Y')" "$ACCESS_LOG" 2>/dev/null | grep " 404 " | wc -l)
        echo "404 errors today: $error_404"

        if [ "$error_404" -gt 100 ]; then
            log_security_event "WARNING" "High number of 404 errors (possible scanning): $error_404"
            echo "⚠️ WARNING: High number of 404 errors"
        fi

        # Check for large file uploads
        large_uploads=$(grep "$(date '+%d/%b/%Y')" "$ACCESS_LOG" 2>/dev/null | awk '$10 > 10000000' | wc -l)
        if [ "$large_uploads" -gt 0 ]; then
            log_security_event "INFO" "Large file uploads detected: $large_uploads"
            echo "ℹ️ INFO: Large file uploads detected ($large_uploads)"
        fi
    else
        echo "⚠️ Web server access log not accessible"
    fi

    if [ -f "$ERROR_LOG" ]; then
        # Check for recent web server errors
        recent_errors=$(grep "$(date '+%Y/%m/%d')" "$ERROR_LOG" 2>/dev/null | wc -l)
        echo "Web server errors today: $recent_errors"

        if [ "$recent_errors" -gt 20 ]; then
            log_security_event "WARNING" "High number of web server errors: $recent_errors"
            echo "⚠️ WARNING: High number of web server errors"
        fi
    fi
}

# Function: Check application security
check_application_security() {
    echo ""
    echo "📱 Application Security:"
    echo "======================="

    DOMAIN_PATH="$HOME/domains/your-domain.com"

    if [ -d "$DOMAIN_PATH/current" ]; then
        cd "$DOMAIN_PATH/current"

        # Check Laravel logs for security events
        if [ -f "storage/logs/laravel.log" ]; then
            app_errors=$(grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log 2>/dev/null | grep -iE "(error|critical|emergency|exception)" | wc -l)
            echo "Application errors today: $app_errors"

            if [ "$app_errors" -gt 10 ]; then
                log_security_event "WARNING" "High number of application errors: $app_errors"
                echo "⚠️ WARNING: High number of application errors"
            fi

            # Check for authentication failures
            auth_failures=$(grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log 2>/dev/null | grep -i "authentication\|login.*fail" | wc -l)
            if [ "$auth_failures" -gt 5 ]; then
                log_security_event "WARNING" "Multiple authentication failures: $auth_failures"
                echo "⚠️ WARNING: Multiple authentication failures"
            fi

            # Check for SQL-related errors (possible injection attempts)
            sql_errors=$(grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log 2>/dev/null | grep -i "sql\|database\|query" | grep -i error | wc -l)
            if [ "$sql_errors" -gt 3 ]; then
                log_security_event "ALERT" "SQL-related errors detected: $sql_errors"
                echo "🚨 ALERT: SQL-related errors detected"
            fi
        fi

        # Check file permissions
        echo ""
        echo "📁 File Permission Check:"

        # Check for world-writable files
        writable_files=$(find . -type f -perm -002 2>/dev/null | wc -l)
        if [ "$writable_files" -gt 0 ]; then
            log_security_event "WARNING" "World-writable files found: $writable_files"
            echo "⚠️ WARNING: $writable_files world-writable files found"
        else
            echo "✅ No world-writable files found"
        fi

        # Check .env file permissions
        if [ -f ".env" ]; then
            env_perms=$(stat -c %a .env)
            if [ "$env_perms" != "600" ]; then
                log_security_event "ALERT" ".env file permissions incorrect: $env_perms"
                echo "🚨 ALERT: .env file permissions incorrect ($env_perms)"
            else
                echo "✅ .env file permissions correct"
            fi
        fi

    else
        echo "⚠️ Application directory not found"
    fi
}

# Function: Check system integrity
check_system_integrity() {
    echo ""
    echo "🔍 System Integrity:"
    echo "==================="

    # Check for unusual processes
    echo "🔄 Process Check:"
    suspicious_procs=$(ps aux | grep -iE "(nc|netcat|wget.*http|curl.*-X)" | grep -v grep | wc -l)
    if [ "$suspicious_procs" -gt 0 ]; then
        log_security_event "WARNING" "Suspicious processes detected: $suspicious_procs"
        echo "⚠️ WARNING: Suspicious processes detected"
        ps aux | grep -iE "(nc|netcat|wget.*http|curl.*-X)" | grep -v grep | head -3
    else
        echo "✅ No suspicious processes found"
    fi

    # Check for unusual network connections
    echo ""
    echo "🌐 Network Connections:"
    unusual_connections=$(netstat -tn | grep ESTABLISHED | grep -v ":80\|:443\|:22\|:3306" | wc -l)
    if [ "$unusual_connections" -gt 5 ]; then
        log_security_event "INFO" "Unusual network connections: $unusual_connections"
        echo "ℹ️ INFO: $unusual_connections unusual network connections"
    else
        echo "✅ Normal network activity"
    fi

    # Check disk space (security logs can fill disk)
    echo ""
    echo "💾 Disk Space Check:"
    disk_usage=$(df -h "$HOME" | tail -1 | awk '{print $5}' | sed 's/%//')
    if [ "$disk_usage" -gt 90 ]; then
        log_security_event "CRITICAL" "Disk space critical: ${disk_usage}%"
        echo "🚨 CRITICAL: Disk space critical (${disk_usage}%)"
    elif [ "$disk_usage" -gt 80 ]; then
        log_security_event "WARNING" "Disk space high: ${disk_usage}%"
        echo "⚠️ WARNING: Disk space high (${disk_usage}%)"
    else
        echo "✅ Disk space normal (${disk_usage}%)"
    fi
}

# Function: Check SSL certificate
check_ssl_certificate() {
    echo ""
    echo "🔐 SSL Certificate Check:"
    echo "========================"

    DOMAIN="your-domain.com"

    # Check certificate expiration
    if command -v openssl &> /dev/null; then
        cert_info=$(echo | openssl s_client -servername "$DOMAIN" -connect "$DOMAIN:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null)

        if [ $? -eq 0 ]; then
            expiry_date=$(echo "$cert_info" | grep "notAfter" | cut -d= -f2)
            expiry_timestamp=$(date -d "$expiry_date" +%s 2>/dev/null)
            current_timestamp=$(date +%s)
            days_until_expiry=$(( (expiry_timestamp - current_timestamp) / 86400 ))

            echo "SSL certificate expires in: $days_until_expiry days"

            if [ "$days_until_expiry" -lt 7 ]; then
                log_security_event "CRITICAL" "SSL certificate expires in $days_until_expiry days"
                echo "🚨 CRITICAL: SSL certificate expires soon"
            elif [ "$days_until_expiry" -lt 30 ]; then
                log_security_event "WARNING" "SSL certificate expires in $days_until_expiry days"
                echo "⚠️ WARNING: SSL certificate expires in 30 days"
            else
                echo "✅ SSL certificate valid"
            fi
        else
            log_security_event "ERROR" "Unable to check SSL certificate"
            echo "❌ Unable to check SSL certificate"
        fi
    fi
}

# Function: Generate security report
generate_security_report() {
    echo ""
    echo "📊 Security Monitoring Summary:"
    echo "=============================="

    # Count different types of events
    alerts=$(grep -c "\[ALERT\]" "$SECURITY_LOG" 2>/dev/null || echo 0)
    warnings=$(grep -c "\[WARNING\]" "$SECURITY_LOG" 2>/dev/null || echo 0)
    info=$(grep -c "\[INFO\]" "$SECURITY_LOG" 2>/dev/null || echo 0)

    echo "Security events today:"
    echo "  Critical/Alerts: $alerts"
    echo "  Warnings: $warnings"
    echo "  Information: $info"

    # Overall security status
    if [ "$alerts" -gt 0 ]; then
        echo "🚨 Overall Security Status: CRITICAL ISSUES"
        log_security_event "CRITICAL" "Security monitoring found critical issues"
    elif [ "$warnings" -gt 3 ]; then
        echo "⚠️ Overall Security Status: NEEDS ATTENTION"
        log_security_event "WARNING" "Security monitoring found multiple warnings"
    else
        echo "✅ Overall Security Status: GOOD"
        log_security_event "INFO" "Security monitoring completed - status good"
    fi

    # Recent alerts
    if [ -f "$ALERT_LOG" ]; then
        echo ""
        echo "📋 Recent Security Alerts:"
        tail -5 "$ALERT_LOG" 2>/dev/null | sed 's/^/  /'
    fi
}

# Main execution
echo "🚀 Starting security monitoring..."

# Log monitoring start
log_security_event "INFO" "Security monitoring started"

# Run all security checks
check_auth_logs
check_web_logs
check_application_security
check_system_integrity
check_ssl_certificate

# Generate final report
generate_security_report

echo ""
echo "✅ Security monitoring completed"
echo "📋 Security log: $SECURITY_LOG"
echo "🚨 Alert log: $ALERT_LOG"
EOF

chmod +x ~/security_monitor.sh

echo "✅ Security monitoring script created"
```

---

## **3. Application Security Updates**

### **3.1: Create Laravel Security Update Script**

```bash
cat > ~/laravel_security_updates.sh << 'EOF'
#!/bin/bash

echo "🛡️ Laravel Security Updates - $(date)"
echo "===================================="

DOMAIN_PATH="$HOME/domains/your-domain.com"
SECURITY_LOG="$HOME/laravel_security.log"

# Function: Log Laravel security action
log_laravel_security() {
    local message="$1"
    echo "$(date): $message" | tee -a "$SECURITY_LOG"
}

# Function: Check Laravel framework updates
check_laravel_updates() {
    echo ""
    echo "🚀 Laravel Framework Security:"
    echo "============================="

    cd "$DOMAIN_PATH/current"

    # Check current Laravel version
    current_version=$(php artisan --version 2>/dev/null | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1)
    echo "Current Laravel version: $current_version"

    # Check composer.lock for Laravel version
    if [ -f "composer.lock" ]; then
        locked_version=$(grep -A 10 '"name": "laravel/framework"' composer.lock | grep '"version"' | cut -d'"' -f4)
        echo "Locked framework version: $locked_version"
    fi

    log_laravel_security "Laravel version check: $current_version"

    # Check for outdated packages
    echo ""
    echo "📦 Checking for outdated packages..."
    if command -v composer &> /dev/null; then
        outdated_output=$(composer outdated --direct 2>/dev/null)

        if [ -n "$outdated_output" ]; then
            echo "Outdated packages found:"
            echo "$outdated_output"

            # Check for security-related packages
            security_packages=$(echo "$outdated_output" | grep -iE "(laravel|symfony|guzzle|monolog)" | wc -l)
            if [ "$security_packages" -gt 0 ]; then
                log_laravel_security "WARNING: Security-related packages outdated: $security_packages"
                echo "⚠️ WARNING: Security-related packages need updating"
            fi
        else
            echo "✅ All packages are up to date"
            log_laravel_security "All packages up to date"
        fi
    fi
}

# Function: Security configuration audit
security_config_audit() {
    echo ""
    echo "🔒 Laravel Security Configuration:"
    echo "================================="

    cd "$DOMAIN_PATH/current"

    # Check APP_DEBUG setting
    app_debug=$(grep "^APP_DEBUG=" .env 2>/dev/null | cut -d'=' -f2)
    echo "APP_DEBUG: ${app_debug:-'not set'} $([ "$app_debug" = "false" ] && echo "✅" || echo "⚠️")"

    if [ "$app_debug" != "false" ]; then
        log_laravel_security "WARNING: APP_DEBUG not set to false in production"
    fi

    # Check APP_ENV setting
    app_env=$(grep "^APP_ENV=" .env 2>/dev/null | cut -d'=' -f2)
    echo "APP_ENV: ${app_env:-'not set'} $([ "$app_env" = "production" ] && echo "✅" || echo "⚠️")"

    if [ "$app_env" != "production" ]; then
        log_laravel_security "WARNING: APP_ENV not set to production"
    fi

    # Check APP_KEY setting
    app_key=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d'=' -f2)
    if [ -n "$app_key" ] && [ ${#app_key} -gt 20 ]; then
        echo "APP_KEY: set ✅"
    else
        echo "APP_KEY: not properly set ⚠️"
        log_laravel_security "WARNING: APP_KEY not properly configured"
    fi

    # Check session configuration
    session_driver=$(grep "^SESSION_DRIVER=" .env 2>/dev/null | cut -d'=' -f2)
    echo "SESSION_DRIVER: ${session_driver:-'file'}"

    session_lifetime=$(grep "^SESSION_LIFETIME=" .env 2>/dev/null | cut -d'=' -f2)
    if [ -n "$session_lifetime" ] && [ "$session_lifetime" -le 120 ]; then
        echo "SESSION_LIFETIME: ${session_lifetime} minutes ✅"
    else
        echo "SESSION_LIFETIME: ${session_lifetime:-'default'} minutes ⚠️"
        log_laravel_security "INFO: Consider shorter session lifetime for security"
    fi

    # Check HTTPS enforcement
    app_url=$(grep "^APP_URL=" .env 2>/dev/null | cut -d'=' -f2)
    if [[ "$app_url" == https://* ]]; then
        echo "HTTPS enforcement: enabled ✅"
    else
        echo "HTTPS enforcement: not configured ⚠️"
        log_laravel_security "WARNING: APP_URL not using HTTPS"
    fi
}

# Function: Check vulnerable dependencies
check_vulnerable_dependencies() {
    echo ""
    echo "🔍 Vulnerability Scan:"
    echo "====================="

    cd "$DOMAIN_PATH/current"

    # Use composer audit if available (Composer 2.4+)
    if composer audit --help >/dev/null 2>&1; then
        echo "🔍 Running Composer security audit..."
        audit_output=$(composer audit --format=json 2>/dev/null)

        if [ $? -eq 0 ]; then
            vulnerabilities=$(echo "$audit_output" | grep -o '"vulnerabilities":[0-9]*' | cut -d':' -f2)
            if [ "$vulnerabilities" -gt 0 ]; then
                log_laravel_security "ALERT: $vulnerabilities vulnerabilities found in dependencies"
                echo "🚨 ALERT: $vulnerabilities vulnerabilities found"
                composer audit --format=table
            else
                echo "✅ No known vulnerabilities in dependencies"
                log_laravel_security "No vulnerabilities found in dependencies"
            fi
        else
            echo "⚠️ Unable to run security audit"
        fi
    else
        echo "ℹ️ Composer audit not available (requires Composer 2.4+)"

        # Alternative: Check for known vulnerable packages manually
        echo "🔍 Checking for known vulnerable packages..."

        # Check for old Laravel versions with known issues
        if [ -n "$current_version" ]; then
            version_major=$(echo "$current_version" | cut -d. -f1)
            version_minor=$(echo "$current_version" | cut -d. -f2)

            # Example: Laravel versions before 8.83.0, 9.52.0 had security issues
            if [ "$version_major" -lt 8 ]; then
                log_laravel_security "WARNING: Laravel version $current_version is outdated and may have security issues"
                echo "⚠️ WARNING: Laravel version is outdated"
            fi
        fi
    fi
}

# Function: File permission security check
file_permission_check() {
    echo ""
    echo "📁 File Permission Security:"
    echo "=========================="

    cd "$DOMAIN_PATH/current"

    # Check storage directory permissions
    if [ -d "storage" ]; then
        storage_perms=$(stat -c %a storage 2>/dev/null)
        echo "Storage directory: $storage_perms $([ "$storage_perms" = "755" ] && echo "✅" || echo "⚠️")"

        # Check for world-writable files in storage
        writable_storage=$(find storage -type f -perm -002 2>/dev/null | wc -l)
        if [ "$writable_storage" -gt 0 ]; then
            log_laravel_security "WARNING: $writable_storage world-writable files in storage"
            echo "⚠️ WARNING: $writable_storage world-writable files in storage"
        else
            echo "✅ Storage file permissions secure"
        fi
    fi

    # Check bootstrap/cache permissions
    if [ -d "bootstrap/cache" ]; then
        cache_perms=$(stat -c %a bootstrap/cache 2>/dev/null)
        echo "Bootstrap cache: $cache_perms $([ "$cache_perms" = "755" ] && echo "✅" || echo "⚠️")"
    fi

    # Check .env file permissions
    if [ -f ".env" ]; then
        env_perms=$(stat -c %a .env 2>/dev/null)
        echo ".env file: $env_perms $([ "$env_perms" = "600" ] && echo "✅" || echo "🚨")"

        if [ "$env_perms" != "600" ]; then
            log_laravel_security "CRITICAL: .env file permissions insecure: $env_perms"
        fi
    fi

    # Check for .git directory in web root (security risk)
    if [ -d "public/.git" ]; then
        log_laravel_security "CRITICAL: .git directory found in public web root"
        echo "🚨 CRITICAL: .git directory in web root"
    fi
}

# Function: Apply security updates
apply_security_updates() {
    echo ""
    echo "🔧 Applying Security Updates:"
    echo "============================"

    cd "$DOMAIN_PATH/current"

    # Confirmation before updates
    echo "⚠️ This will update Laravel dependencies"
    echo "Ensure you have tested updates in staging first"

    read -p "🔴 Continue with security updates? (y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        echo "❌ Security updates cancelled"
        return 1
    fi

    # Create backup before updates
    echo "💾 Creating backup before updates..."
    backup_name="pre_security_update_$(date +%Y%m%d_%H%M%S)"
    cp -r . "/tmp/$backup_name"
    log_laravel_security "Backup created before security updates: /tmp/$backup_name"

    # Update Composer dependencies
    echo "📦 Updating Composer dependencies..."
    if composer update --no-dev --optimize-autoloader; then
        echo "✅ Dependencies updated successfully"
        log_laravel_security "Composer dependencies updated successfully"

        # Clear application caches
        echo "🗑️ Clearing application caches..."
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear

        # Rebuild optimized caches
        echo "🔧 Rebuilding optimized caches..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache

        echo "✅ Security updates applied successfully"
        log_laravel_security "Security updates applied and caches rebuilt"

        return 0
    else
        echo "❌ Dependency update failed"
        log_laravel_security "ERROR: Composer dependency update failed"

        # Restore from backup
        echo "🔄 Restoring from backup..."
        rm -rf ./*
        cp -r "/tmp/$backup_name"/* .
        echo "✅ Backup restored"

        return 1
    fi
}

# Function: Security hardening
security_hardening() {
    echo ""
    echo "🛡️ Security Hardening:"
    echo "====================="

    cd "$DOMAIN_PATH/current"

    # Fix .env permissions
    if [ -f ".env" ]; then
        current_perms=$(stat -c %a .env)
        if [ "$current_perms" != "600" ]; then
            echo "🔒 Fixing .env file permissions..."
            chmod 600 .env
            log_laravel_security "Fixed .env file permissions from $current_perms to 600"
            echo "✅ .env permissions fixed"
        fi
    fi

    # Remove world-writable permissions
    echo "🔒 Fixing file permissions..."
    find . -type f -perm -002 -exec chmod o-w {} \; 2>/dev/null
    find . -type d -perm -002 -exec chmod o-w {} \; 2>/dev/null
    echo "✅ World-writable permissions removed"

    # Ensure storage and cache directories are writable by web server
    echo "📁 Setting correct directory permissions..."
    chmod -R 755 storage bootstrap/cache 2>/dev/null
    echo "✅ Directory permissions set"

    # Remove development files if found
    if [ -f ".env.example" ]; then
        echo "🗑️ Removing development files..."
        rm -f .env.example README.md CHANGELOG.md
        log_laravel_security "Removed development files from production"
        echo "✅ Development files removed"
    fi
}

# Main execution
echo "🚀 Starting Laravel security updates..."

# Log start
log_laravel_security "Laravel security update process started"

# Check if Laravel application exists
if [ ! -d "$DOMAIN_PATH/current" ]; then
    echo "❌ Laravel application not found at $DOMAIN_PATH/current"
    exit 1
fi

# Run security checks
check_laravel_updates
security_config_audit
check_vulnerable_dependencies
file_permission_check

# Ask about applying updates
echo ""
echo "🔧 Security Update Options:"
echo "1. Apply security updates (composer update)"
echo "2. Apply security hardening only"
echo "3. Skip updates (monitoring only)"

read -p "Choose option (1-3): " update_choice

case $update_choice in
    1)
        apply_security_updates
        security_hardening
        ;;
    2)
        security_hardening
        ;;
    3)
        echo "ℹ️ Updates skipped - monitoring only"
        ;;
    *)
        echo "❌ Invalid choice - no updates applied"
        ;;
esac

# Final summary
echo ""
echo "📊 Laravel Security Summary:"
echo "=========================="
echo "Security log: $SECURITY_LOG"

# Count issues found
alerts=$(grep -c "ALERT\|CRITICAL" "$SECURITY_LOG" 2>/dev/null || echo 0)
warnings=$(grep -c "WARNING" "$SECURITY_LOG" 2>/dev/null || echo 0)

if [ "$alerts" -gt 0 ]; then
    echo "🚨 Laravel Security Status: CRITICAL ISSUES ($alerts alerts)"
elif [ "$warnings" -gt 0 ]; then
    echo "⚠️ Laravel Security Status: NEEDS ATTENTION ($warnings warnings)"
else
    echo "✅ Laravel Security Status: GOOD"
fi

log_laravel_security "Laravel security update process completed"

echo ""
echo "✅ Laravel security update completed"
EOF

chmod +x ~/laravel_security_updates.sh

echo "✅ Laravel security update script created"
```

---

## **4. Security Update Scheduling**

### **4.1: Create Security Update Schedule**

```bash
# Remove any existing security update jobs
crontab -l | grep -v "security" | crontab -

# Add comprehensive security update schedule
echo "⏰ Setting up security update schedule..."

# System security monitoring (every 6 hours)
(crontab -l 2>/dev/null; echo "0 */6 * * * $HOME/security_monitor.sh >> $HOME/security_monitor.log 2>&1") | crontab -

# System security updates check (daily at 3 AM)
(crontab -l 2>/dev/null; echo "0 3 * * * $HOME/system_security_updates.sh >> $HOME/system_security.log 2>&1") | crontab -

# Laravel security check (daily at 4 AM)
(crontab -l 2>/dev/null; echo "0 4 * * * $HOME/laravel_security_updates.sh >> $HOME/laravel_security.log 2>&1") | crontab -

# SSL certificate monitoring (weekly on Monday at 6 AM)
(crontab -l 2>/dev/null; echo "0 6 * * 1 echo | openssl s_client -servername your-domain.com -connect your-domain.com:443 2>/dev/null | openssl x509 -noout -dates >> $HOME/ssl_check.log 2>&1") | crontab -

echo "✅ Security update schedule configured"

# Verify security cron jobs
echo ""
echo "📋 Scheduled Security Jobs:"
crontab -l | grep -E "(security|ssl)"
```

### **4.2: Create Security Dashboard**

```bash
cat > ~/security_dashboard.sh << 'EOF'
#!/bin/bash

echo "🛡️ Security Dashboard - $(date)"
echo "==============================="

# Function: Get status icon
get_status_icon() {
    local count="$1"
    local threshold="$2"

    if [ "$count" -eq 0 ]; then
        echo "✅"
    elif [ "$count" -le "$threshold" ]; then
        echo "⚠️"
    else
        echo "🚨"
    fi
}

# System Security Status
echo ""
echo "🖥️ SYSTEM SECURITY STATUS"
echo "========================="

# Check for pending security updates
if command -v apt &> /dev/null; then
    security_updates=$(apt list --upgradable 2>/dev/null | grep -i security | wc -l)
    echo "Pending security updates: $security_updates $(get_status_icon $security_updates 0)"
fi

# Check authentication failures (last 24 hours)
auth_failures=$(grep "Failed password" /var/log/auth.log 2>/dev/null | grep "$(date '+%b %d')" | wc -l)
echo "Failed login attempts: $auth_failures $(get_status_icon $auth_failures 5)"

# Check disk space
disk_usage=$(df -h $HOME | tail -1 | awk '{print $5}' | sed 's/%//')
echo "Disk usage: ${disk_usage}% $([ "$disk_usage" -lt 80 ] && echo "✅" || echo "⚠️")"

# Application Security Status
echo ""
echo "📱 APPLICATION SECURITY STATUS"
echo "=============================="

DOMAIN_PATH="$HOME/domains/your-domain.com"

if [ -d "$DOMAIN_PATH/current" ]; then
    cd "$DOMAIN_PATH/current"

    # Laravel version
    laravel_version=$(php artisan --version 2>/dev/null | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1)
    echo "Laravel version: ${laravel_version:-'Unknown'}"

    # Environment check
    app_debug=$(grep "^APP_DEBUG=" .env 2>/dev/null | cut -d'=' -f2)
    app_env=$(grep "^APP_ENV=" .env 2>/dev/null | cut -d'=' -f2)
    echo "Environment: ${app_env:-'Unknown'} $([ "$app_env" = "production" ] && echo "✅" || echo "⚠️")"
    echo "Debug mode: ${app_debug:-'Unknown'} $([ "$app_debug" = "false" ] && echo "✅" || echo "⚠️")"

    # Application errors (last 24 hours)
    if [ -f "storage/logs/laravel.log" ]; then
        app_errors=$(grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log 2>/dev/null | grep -iE "(error|critical|emergency)" | wc -l)
        echo "Application errors: $app_errors $(get_status_icon $app_errors 5)"
    fi

    # File permissions
    env_perms=$(stat -c %a .env 2>/dev/null)
    echo ".env permissions: ${env_perms:-'Unknown'} $([ "$env_perms" = "600" ] && echo "✅" || echo "🚨")"

else
    echo "❌ Application not found"
fi

# Web Security Status
echo ""
echo "🌐 WEB SECURITY STATUS"
echo "====================="

# SSL certificate check
DOMAIN="your-domain.com"
if command -v openssl &> /dev/null; then
    cert_info=$(echo | openssl s_client -servername "$DOMAIN" -connect "$DOMAIN:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null)

    if [ $? -eq 0 ]; then
        expiry_date=$(echo "$cert_info" | grep "notAfter" | cut -d= -f2)
        expiry_timestamp=$(date -d "$expiry_date" +%s 2>/dev/null)
        current_timestamp=$(date +%s)
        days_until_expiry=$(( (expiry_timestamp - current_timestamp) / 86400 ))

        echo "SSL expiry: $days_until_expiry days $([ "$days_until_expiry" -gt 30 ] && echo "✅" || echo "⚠️")"
    else
        echo "SSL certificate: Unable to check ❌"
    fi
fi

# Web server response
if command -v curl &> /dev/null; then
    http_status=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN")
    response_time=$(curl -s -o /dev/null -w "%{time_total}" "https://$DOMAIN")
    echo "Website status: HTTP $http_status $([ "$http_status" = "200" ] && echo "✅" || echo "❌")"
    echo "Response time: ${response_time}s $([ "${response_time%.*}" -lt 3 ] && echo "✅" || echo "⚠️")"
fi

# Security Events Summary
echo ""
echo "🚨 RECENT SECURITY EVENTS"
echo "========================="

# Recent alerts from security logs
if [ -f "$HOME/security_alerts.log" ]; then
    recent_alerts=$(grep "$(date '+%Y-%m-%d')" "$HOME/security_alerts.log" 2>/dev/null | wc -l)
    echo "Security alerts today: $recent_alerts $(get_status_icon $recent_alerts 0)"

    if [ "$recent_alerts" -gt 0 ]; then
        echo ""
        echo "📋 Latest Security Alerts:"
        tail -3 "$HOME/security_alerts.log" 2>/dev/null | sed 's/^/  /'
    fi
else
    echo "No security alert log found"
fi

# Overall Security Score
echo ""
echo "📊 OVERALL SECURITY SCORE"
echo "========================="

# Calculate security score (simplified)
score=100

# Deduct points for issues
[ "$security_updates" -gt 0 ] && score=$((score - 10))
[ "$auth_failures" -gt 5 ] && score=$((score - 15))
[ "$disk_usage" -gt 80 ] && score=$((score - 10))
[ "$app_debug" != "false" ] && score=$((score - 20))
[ "$app_env" != "production" ] && score=$((score - 20))
[ "$env_perms" != "600" ] && score=$((score - 25))
[ "$days_until_expiry" -lt 30 ] && score=$((score - 10))
[ "$recent_alerts" -gt 0 ] && score=$((score - 20))

# Ensure score doesn't go below 0
[ "$score" -lt 0 ] && score=0

echo "Security Score: $score/100"

if [ "$score" -ge 90 ]; then
    echo "Status: EXCELLENT 🎉"
elif [ "$score" -ge 75 ]; then
    echo "Status: GOOD ✅"
elif [ "$score" -ge 60 ]; then
    echo "Status: NEEDS ATTENTION ⚠️"
else
    echo "Status: CRITICAL ISSUES 🚨"
fi

# Recommendations
echo ""
echo "💡 SECURITY RECOMMENDATIONS"
echo "=========================="

[ "$security_updates" -gt 0 ] && echo "• Apply $security_updates pending security updates"
[ "$auth_failures" -gt 5 ] && echo "• Investigate $auth_failures failed login attempts"
[ "$disk_usage" -gt 80 ] && echo "• Free disk space (currently ${disk_usage}%)"
[ "$app_debug" != "false" ] && echo "• Disable APP_DEBUG in production"
[ "$app_env" != "production" ] && echo "• Set APP_ENV to production"
[ "$env_perms" != "600" ] && echo "• Fix .env file permissions (chmod 600 .env)"
[ "$days_until_expiry" -lt 30 ] && echo "• Renew SSL certificate (expires in $days_until_expiry days)"
[ "$recent_alerts" -gt 0 ] && echo "• Review $recent_alerts security alerts"

echo ""
echo "📅 Last Updated: $(date)"
echo "📍 Dashboard Location: $0"
EOF

chmod +x ~/security_dashboard.sh

echo "✅ Security dashboard created"
```

---

## **5. Usage Instructions**

### **Daily Operations:**

```bash
# View security dashboard
bash ~/security_dashboard.sh

# Run security monitoring
bash ~/security_monitor.sh

# Check for system security updates
bash ~/system_security_updates.sh

# Check Laravel security
bash ~/laravel_security_updates.sh
```

### **Weekly Operations:**

```bash
# Review security logs
tail -50 ~/security_monitor.log
tail -50 ~/security_alerts.log
tail -50 ~/laravel_security.log

# Check SSL certificate status
echo | openssl s_client -servername your-domain.com -connect your-domain.com:443 2>/dev/null | openssl x509 -noout -dates
```

### **Monthly Operations:**

```bash
# Full security audit
bash ~/system_security_updates.sh
bash ~/laravel_security_updates.sh
bash ~/security_monitor.sh

# Review all security logs
find ~ -name "*security*.log" -exec tail -20 {} \;

# Update security documentation
bash ~/security_dashboard.sh > ~/monthly_security_report.txt
```

---

## **6. Configuration Checklist**

- [ ] System security update script configured
- [ ] Security monitoring system operational
- [ ] Laravel security checks automated
- [ ] Security update scheduling active
- [ ] Security dashboard available
- [ ] SSL certificate monitoring enabled
- [ ] Authentication failure monitoring active
- [ ] File permission monitoring configured
- [ ] Vulnerability scanning operational
- [ ] Security event logging functional

---

## **Related Files**

- **Server Monitoring:** [Server_Monitoring.md](Server_Monitoring.md)
- **Performance Monitoring:** [Performance_Monitoring.md](Performance_Monitoring.md)
- **Emergency Procedures:** [Emergency_Procedures.md](Emergency_Procedures.md)
- **Backup Management:** [Backup_Management.md](Backup_Management.md)

---

## **Security Quick Reference**

### **Emergency Security Response:**

```bash
bash ~/security_monitor.sh
bash ~/security_dashboard.sh
```

### **Apply Security Updates:**

```bash
bash ~/system_security_updates.sh
bash ~/laravel_security_updates.sh
```

### **Check Security Status:**

```bash
bash ~/security_dashboard.sh
```

### **Review Security Logs:**

```bash
tail -50 ~/security_alerts.log
tail -50 ~/security_monitor.log
```
