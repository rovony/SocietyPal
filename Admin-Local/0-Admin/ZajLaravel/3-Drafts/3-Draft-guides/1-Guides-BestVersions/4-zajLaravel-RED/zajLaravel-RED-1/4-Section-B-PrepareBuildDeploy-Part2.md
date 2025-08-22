# Universal Laravel Build & Deploy Guide - Part 4
## Section B: Prepare Build & Deploy - Part 2 (Security & Data Protection)

**Version:** 1.0  
**Generated:** August 21, 2025, 6:16 PM EST  
**Purpose:** Complete step-by-step guide for Laravel security scanning, data persistence, and final validation  
**Coverage:** Steps 17-20 - Security through final pre-deployment preparation  
**Authority:** Based on 4-way consolidated FINAL documents  
**Prerequisites:** Part 3 completed successfully with build validation and dependencies configured

---

## Quick Navigation

| **Part** | **Coverage** | **Focus Area** | **Link** |
|----------|--------------|----------------|----------|
| Part 1 | Steps 00-07 | Foundation & Configuration | ← [Part 1 Guide](./1-Section-A-ProjectSetup-Part1.md) |
| Part 2 | Steps 08-11 | Dependencies & Final Setup | ← [Part 2 Guide](./2-Section-A-ProjectSetup-Part2.md) |
| Part 3 | Steps 14.0-16.2 | Build Validation & Dependencies | ← [Part 3 Guide](./3-Section-B-PrepareBuildDeploy-Part1.md) |
| **Part 4** | Steps 17-20 | Security & Data Protection | **(Current Guide)** |
| Part 5 | Steps 1.1-5.2 | Build Process | → [Part 5 Guide](./5-Section-C-BuildDeploy-Part1.md) |
| Part 6 | Steps 6.1-10.3 | Deploy & Finalization | → [Part 6 Guide](./6-Section-C-BuildDeploy-Part2.md) |

**Master Checklist:** → [0-Master-Checklist.md](../1-FINAL-MASTER-CHECKLIST/0-Master-Checklist.md)

---

## Overview

This guide completes Section B by implementing critical security measures and data protection systems. You'll establish:

- 🔒 Comprehensive security vulnerability scanning
- 💾 Zero-data-loss data persistence systems
- 🚨 Emergency rollback and recovery procedures  
- ✅ Final pre-deployment validation and readiness confirmation

By completing Part 4, your project will be security-hardened and protected against data loss during deployment.

---

## Prerequisites Validation

Before starting Part 4, ensure Part 3 is completely finished:

### Required from Part 3 ✅
- [ ] Pre-build validation passed with 100% success rate (10-point checklist)
- [ ] Composer strategy configured and tested for hosting environment
- [ ] Production dependencies verified and lock file generated
- [ ] Build process tested successfully (12-point checklist)
- [ ] Build strategy configured and integrated with deployment pipeline

### Validation Commands
```bash
# Verify Part 3 completion
ls -la Admin-Local/Deployment/Configs/
ls -la Admin-Local/Deployment/Logs/
php artisan --version
composer show --no-dev | wc -l
```

---

## Step 17: Comprehensive Security Scan
**🟢 Location:** Local Machine | **⏱️ Time:** 25-35 minutes | **🔒 Type:** Security Analysis

### Purpose
Execute comprehensive security vulnerability scanning across application dependencies, configuration, and code to identify and address security issues before deployment to production environment.

### When to Execute
**After build process validation** - This ensures all dependencies and configurations are ready before security hardening.

### Action Steps

1. **Execute Comprehensive Security Scanning Script**
   a. Run the comprehensive security scan:
   ```bash
   chmod +x Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh
   bash Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh
   ```
   b. Review the detailed security report thoroughly
   c. Address all critical and high-severity vulnerabilities
   d. Document any accepted risks or deferred fixes

2. **Dependency Vulnerability Scanning**
   a. **Composer Security Analysis**
      - Run Enlightn Security Checker: `vendor/bin/security-checker security:check`
      - Review all reported vulnerabilities in PHP packages
      - Update vulnerable packages to secure versions
      - Document any packages that cannot be updated

   b. **Node.js Security Analysis (if applicable)**
      - Run npm audit: `npm audit --audit-level=moderate`
      - Fix automatically: `npm audit fix`
      - Review remaining vulnerabilities manually
      - Consider alternative packages for unfixable vulnerabilities

   c. **Third-Party Package Assessment**
      - Review CodeCanyon and custom packages for known issues
      - Check package update availability and changelogs  
      - Verify package authenticity and integrity
      - Document third-party package security posture

3. **Laravel-Specific Security Configuration**
   a. **Application Security Settings**
      - Verify APP_DEBUG=false for production
      - Ensure APP_ENV=production
      - Confirm strong APP_KEY generation
      - Check session and cookie security settings

   b. **Database Security Configuration**
      - Review database connection security (SSL, ports)
      - Verify database user permissions are minimal
      - Check for default/weak database credentials
      - Ensure database backups are secured

   c. **File System Security**
      - Verify storage directory permissions (755/644)
      - Check for publicly accessible sensitive files
      - Review .htaccess and nginx security configurations
      - Ensure log files are not publicly accessible

4. **Code Security Analysis**
   a. **Static Code Analysis**
      - Run PHPStan security rules if available
      - Check for SQL injection vulnerabilities
      - Review input validation and sanitization
      - Identify potential XSS vulnerabilities

   b. **Configuration Security Review**
      - Review all .env file configurations
      - Check for hard-coded credentials or secrets
      - Verify CORS configuration appropriateness
      - Review API rate limiting and authentication

### Expected Results ✅
- [ ] Zero critical security vulnerabilities remaining in dependencies
- [ ] Laravel security configuration hardened for production
- [ ] File system permissions and access controls verified
- [ ] Security scan report generated with all issues addressed

### Verification Steps
- [ ] Security scan shows no critical or high-severity issues
- [ ] All dependency vulnerabilities resolved or documented
- [ ] Laravel application passes security configuration checklist
- [ ] File permissions and access controls validated

### Troubleshooting Tips
- **Issue:** Vulnerable dependencies cannot be updated
  - **Solution:** Check for security patches, alternative packages, or vendor updates
- **Issue:** False positive security warnings
  - **Solution:** Document justification and implement additional protective measures
- **Issue:** Configuration conflicts during hardening
  - **Solution:** Test configuration changes in staging environment first

### Comprehensive Security Scanning Script Template

**comprehensive-security-scan.sh:**
```bash
#!/bin/bash

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Comprehensive Laravel Security Analysis              ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

REPORT="Admin-Local/Deployment/Logs/security-scan-$(date +%Y%m%d-%H%M%S).md"
CRITICAL_COUNT=0
HIGH_COUNT=0
MEDIUM_COUNT=0

echo "# Comprehensive Security Scan Report" > $REPORT
echo "Generated: $(date)" >> $REPORT
echo "Project: $PROJECT_NAME" >> $REPORT
echo "" >> $REPORT

# 1. Composer Security Check
echo "## 1. PHP Dependency Security Analysis" >> $REPORT
if command -v vendor/bin/security-checker >/dev/null 2>&1; then
    echo "### Composer Security Check Results" >> $REPORT
    if vendor/bin/security-checker security:check --format=json > security-temp.json 2>/dev/null; then
        if [ -s security-temp.json ]; then
            VULN_COUNT=$(cat security-temp.json | grep -o '"vulnerability"' | wc -l)
            if [ $VULN_COUNT -gt 0 ]; then
                echo "❌ Found $VULN_COUNT vulnerabilities in PHP dependencies" >> $REPORT
                CRITICAL_COUNT=$((CRITICAL_COUNT + VULN_COUNT))
                cat security-temp.json >> $REPORT
            else
                echo "✅ No vulnerabilities found in PHP dependencies" >> $REPORT
            fi
        else
            echo "✅ No vulnerabilities found in PHP dependencies" >> $REPORT
        fi
        rm -f security-temp.json
    else
        echo "⚠️ Security checker failed to run" >> $REPORT
    fi
else
    echo "⚠️ Security checker not installed" >> $REPORT
fi

# 2. Node.js Security Check (if applicable)
if [ -f "package.json" ]; then
    echo "" >> $REPORT
    echo "## 2. Node.js Dependency Security Analysis" >> $REPORT
    if command -v npm >/dev/null 2>&1; then
        echo "### NPM Audit Results" >> $REPORT
        if npm audit --audit-level=low --json > npm-audit.json 2>/dev/null; then
            CRITICAL_NPM=$(cat npm-audit.json | grep -o '"critical"' | wc -l)
            HIGH_NPM=$(cat npm-audit.json | grep -o '"high"' | wc -l)
            MODERATE_NPM=$(cat npm-audit.json | grep -o '"moderate"' | wc -l)
            
            if [ $CRITICAL_NPM -gt 0 ]; then
                echo "❌ Critical: $CRITICAL_NPM vulnerabilities" >> $REPORT
                CRITICAL_COUNT=$((CRITICAL_COUNT + CRITICAL_NPM))
            fi
            if [ $HIGH_NPM -gt 0 ]; then
                echo "⚠️ High: $HIGH_NPM vulnerabilities" >> $REPORT
                HIGH_COUNT=$((HIGH_COUNT + HIGH_NPM))
            fi
            if [ $MODERATE_NPM -gt 0 ]; then
                echo "⚠️ Moderate: $MODERATE_NPM vulnerabilities" >> $REPORT
                MEDIUM_COUNT=$((MEDIUM_COUNT + MODERATE_NPM))
            fi
            
            if [ $CRITICAL_NPM -eq 0 ] && [ $HIGH_NPM -eq 0 ]; then
                echo "✅ No critical or high severity Node.js vulnerabilities" >> $REPORT
            fi
            rm -f npm-audit.json
        else
            echo "⚠️ NPM audit failed" >> $REPORT
        fi
    fi
else
    echo "" >> $REPORT
    echo "## 2. Node.js Dependencies: Not Applicable" >> $REPORT
fi

# 3. Laravel Configuration Security
echo "" >> $REPORT
echo "## 3. Laravel Security Configuration" >> $REPORT

# Check debug mode
if grep -q "APP_DEBUG=true" .env 2>/dev/null; then
    echo "❌ APP_DEBUG is enabled (security risk)" >> $REPORT
    HIGH_COUNT=$((HIGH_COUNT + 1))
else
    echo "✅ APP_DEBUG properly configured" >> $REPORT
fi

# Check environment
if grep -q "APP_ENV=production" .env 2>/dev/null; then
    echo "✅ APP_ENV set to production" >> $REPORT
elif grep -q "APP_ENV=local" .env 2>/dev/null; then
    echo "⚠️ APP_ENV still set to local" >> $REPORT
    MEDIUM_COUNT=$((MEDIUM_COUNT + 1))
else
    echo "❌ APP_ENV not properly configured" >> $REPORT
    HIGH_COUNT=$((HIGH_COUNT + 1))
fi

# Check application key
if grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "✅ Application key properly configured" >> $REPORT
else
    echo "❌ Application key missing or invalid" >> $REPORT
    CRITICAL_COUNT=$((CRITICAL_COUNT + 1))
fi

# 4. File Permissions Security
echo "" >> $REPORT
echo "## 4. File System Security" >> $REPORT

# Check storage permissions
if [ -d "storage" ]; then
    STORAGE_PERMS=$(stat -c "%a" storage 2>/dev/null || stat -f "%Mp%Lp" storage 2>/dev/null)
    if [ "$STORAGE_PERMS" = "755" ] || [ "$STORAGE_PERMS" = "775" ]; then
        echo "✅ Storage directory permissions: $STORAGE_PERMS" >> $REPORT
    else
        echo "⚠️ Storage directory permissions: $STORAGE_PERMS (review required)" >> $REPORT
        MEDIUM_COUNT=$((MEDIUM_COUNT + 1))
    fi
fi

# Check for sensitive files in public
SENSITIVE_FILES=(
    "public/.env"
    "public/composer.json"
    "public/artisan"
    "public/README.md"
)

for file in "${SENSITIVE_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "❌ Sensitive file in public directory: $file" >> $REPORT
        HIGH_COUNT=$((HIGH_COUNT + 1))
    fi
done

# 5. Configuration Files Security
echo "" >> $REPORT
echo "## 5. Configuration Security Review" >> $REPORT

# Check for hardcoded credentials
echo "### Hardcoded Credentials Check" >> $REPORT
CREDENTIAL_PATTERNS=(
    "password.*=.*['\"][^'\"]+['\"]"
    "secret.*=.*['\"][^'\"]+['\"]" 
    "key.*=.*['\"][^'\"]+['\"]"
    "token.*=.*['\"][^'\"]+['\"]"
)

HARDCODED_FOUND=false
for pattern in "${CREDENTIAL_PATTERNS[@]}"; do
    if grep -r "$pattern" app/ config/ 2>/dev/null | grep -v "// *test\|#\|/\*"; then
        HARDCODED_FOUND=true
        HIGH_COUNT=$((HIGH_COUNT + 1))
    fi
done

if [ "$HARDCODED_FOUND" = true ]; then
    echo "❌ Potential hardcoded credentials found" >> $REPORT
else
    echo "✅ No obvious hardcoded credentials detected" >> $REPORT
fi

# 6. Database Security
echo "" >> $REPORT
echo "## 6. Database Security Configuration" >> $REPORT

# Check database connection security
if grep -q "DB_CONNECTION=mysql" .env 2>/dev/null; then
    if grep -q "DB_HOST=127.0.0.1\|DB_HOST=localhost" .env 2>/dev/null; then
        echo "✅ Database connection uses localhost" >> $REPORT
    else
        echo "⚠️ Database connection to external host (review security)" >> $REPORT
        MEDIUM_COUNT=$((MEDIUM_COUNT + 1))
    fi
fi

# Check for default database credentials
if grep -q "DB_PASSWORD=\|DB_PASSWORD=password\|DB_PASSWORD=root" .env 2>/dev/null; then
    echo "❌ Weak or empty database password detected" >> $REPORT
    CRITICAL_COUNT=$((CRITICAL_COUNT + 1))
else
    echo "✅ Database password appears to be configured" >> $REPORT
fi

# 7. Generate Security Summary
echo "" >> $REPORT
echo "## Security Scan Summary" >> $REPORT
echo "- **Critical Issues:** $CRITICAL_COUNT" >> $REPORT
echo "- **High Severity:** $HIGH_COUNT" >> $REPORT
echo "- **Medium Severity:** $MEDIUM_COUNT" >> $REPORT

TOTAL_ISSUES=$((CRITICAL_COUNT + HIGH_COUNT + MEDIUM_COUNT))
echo "- **Total Issues:** $TOTAL_ISSUES" >> $REPORT

echo "" >> $REPORT
if [ $CRITICAL_COUNT -eq 0 ] && [ $HIGH_COUNT -eq 0 ]; then
    echo "🎉 **SECURITY SCAN PASSED - No critical or high severity issues**" >> $REPORT
elif [ $CRITICAL_COUNT -eq 0 ] && [ $HIGH_COUNT -le 2 ]; then
    echo "⚠️ **REVIEW REQUIRED - Address high severity issues before deployment**" >> $REPORT
else
    echo "❌ **SECURITY ISSUES FOUND - Critical fixes required before deployment**" >> $REPORT
fi

# 8. Generate Action Items
echo "" >> $REPORT
echo "## Recommended Actions" >> $REPORT

if [ $CRITICAL_COUNT -gt 0 ]; then
    echo "### Critical (Must Fix)" >> $REPORT
    echo "- Address all critical security vulnerabilities before deployment" >> $REPORT
    echo "- Update vulnerable dependencies to secure versions" >> $REPORT
    echo "- Fix configuration security issues" >> $REPORT
fi

if [ $HIGH_COUNT -gt 0 ]; then
    echo "### High Priority" >> $REPORT
    echo "- Review and fix high severity vulnerabilities" >> $REPORT
    echo "- Implement additional security measures where needed" >> $REPORT
fi

if [ $MEDIUM_COUNT -gt 0 ]; then
    echo "### Medium Priority" >> $REPORT
    echo "- Review medium severity issues for production impact" >> $REPORT
    echo "- Plan fixes for next deployment cycle if not critical" >> $REPORT
fi

echo ""
echo "📋 Security scan report: $REPORT"
cat $REPORT
```

---

## Step 18: Setup Data Persistence
**🟢 Location:** Local Machine | **⏱️ Time:** 30-40 minutes | **💾 Type:** Data Protection

### Purpose
Implement comprehensive zero-data-loss data persistence system ensuring critical application data, user uploads, and customizations are preserved during deployment processes and protected against data loss.

### When to Execute
**After security scanning** - This ensures data protection systems are implemented on a security-hardened foundation.

### Action Steps

1. **Execute Data Persistence Setup Script**
   a. Run the data persistence configuration script:
   ```bash
   chmod +x Admin-Local/Deployment/Scripts/setup-data-persistence.sh
   bash Admin-Local/Deployment/Scripts/setup-data-persistence.sh
   ```
   b. Review the generated persistence configuration
   c. Test data persistence mechanisms in safe environment
   d. Verify all critical data sources are protected

2. **Configure Shared Directories and Files**
   a. **Laravel Storage Directories**
      - `storage/app/public` - User uploaded files and public assets
      - `storage/logs` - Application logs and error tracking
      - `storage/framework/cache` - Application cache storage
      - `storage/framework/sessions` - Session data storage
      - `storage/framework/views` - Compiled view cache

   b. **Public Directory Assets**
      - `public/uploads` - Direct public file uploads
      - `public/user-content` - User-generated content
      - `public/avatars` - User profile images
      - `public/documents` - Document storage
      - `public/media` - Media file storage

   c. **Custom Module Data**
      - `Modules/` - Custom module files and configurations
      - Custom application directories as defined in deployment variables

3. **Configure Shared Configuration Files**
   a. **Environment Configuration**
      ```bash
      # Shared files that persist across deployments
      .env
      .env.production
      .env.staging
      ```

   b. **Authentication and Security Files**
      ```bash
      auth.json                    # Composer authentication
      oauth-private.key           # OAuth private key
      oauth-public.key            # OAuth public key
      jwt-private.key             # JWT private key (if used)
      jwt-public.key              # JWT public key (if used)
      ```

   c. **SSL and Certificate Files**
      ```bash
      ssl/                        # SSL certificates
      certificates/               # Application certificates
      keys/                       # Private keys and secrets
      ```

4. **Implement Smart Content Protection**
   a. **Create Data Backup Hooks**
      - Pre-deployment data backup
      - Post-deployment verification
      - Automated rollback triggers
      - Data integrity validation

   b. **Version Control Integration**
      - Exclude data directories from Git tracking
      - Maintain data directory structure in repository
      - Document data restoration procedures
      - Create data migration scripts when needed

### Expected Results ✅
- [ ] All critical data directories configured for persistence across deployments
- [ ] Shared configuration files identified and protected
- [ ] Smart content protection system operational
- [ ] Data persistence tested and validated in safe environment

### Verification Steps
- [ ] Data persistence configuration matches deployment variables
- [ ] Shared directories maintain data across simulated deployments
- [ ] Configuration files persist correctly during updates
- [ ] Backup and restoration procedures tested successfully

### Troubleshooting Tips
- **Issue:** Symlink creation fails during deployment
  - **Solution:** Check server permissions and symlink support
- **Issue:** Data directories not persisting
  - **Solution:** Verify shared directory configuration and paths
- **Issue:** File permission conflicts
  - **Solution:** Adjust ownership and permissions in shared directories

### Data Persistence Setup Script Template

**setup-data-persistence.sh:**
```bash
#!/bin/bash

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Zero-Data-Loss Data Persistence Setup               ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

PERSISTENCE_CONFIG="Admin-Local/Deployment/Configs/data-persistence.json"

echo "💾 Setting up data persistence for: $PROJECT_NAME"
echo "📍 Server path: $PATH_SERVER"

# Load shared directories from deployment variables
SHARED_DIRS=$(jq -r '.shared_directories[]' Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null || echo "storage/app/public storage/logs public/uploads")
SHARED_FILES=$(jq -r '.shared_files[]' Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null || echo ".env auth.json")

echo "📂 Configuring shared directories..."
echo "$SHARED_DIRS"

echo "📄 Configuring shared files..."
echo "$SHARED_FILES"

# Create comprehensive persistence configuration
cat > $PERSISTENCE_CONFIG << EOF
{
  "project": "$PROJECT_NAME",
  "strategy": "symlink-shared",
  "server_path": "$PATH_SERVER",
  "shared_path": "$PATH_SERVER/shared",
  "persistence": {
    "directories": [
EOF

# Add shared directories to config
FIRST_DIR=true
for dir in $SHARED_DIRS; do
    if [ "$FIRST_DIR" = true ]; then
        FIRST_DIR=false
    else
        echo "," >> $PERSISTENCE_CONFIG
    fi
    cat >> $PERSISTENCE_CONFIG << EOF
      {
        "source": "$dir",
        "target": "shared/$dir",
        "type": "directory",
        "critical": true,
        "backup": true
      }EOF
done

cat >> $PERSISTENCE_CONFIG << EOF

    ],
    "files": [
EOF

# Add shared files to config
FIRST_FILE=true
for file in $SHARED_FILES; do
    if [ "$FIRST_FILE" = true ]; then
        FIRST_FILE=false
    else
        echo "," >> $PERSISTENCE_CONFIG
    fi
    cat >> $PERSISTENCE_CONFIG << EOF
      {
        "source": "$file",
        "target": "shared/$file",
        "type": "file",
        "critical": true,
        "backup": true
      }EOF
done

cat >> $PERSISTENCE_CONFIG << EOF

    ]
  },
  "protection": {
    "enable_backups": true,
    "backup_retention": 7,
    "integrity_checks": true,
    "rollback_support": true
  },
  "hooks": {
    "pre_deployment": "backup_shared_data",
    "post_deployment": "verify_data_integrity",
    "rollback": "restore_shared_data"
  }
}
EOF

echo "✅ Data persistence configuration saved to: $PERSISTENCE_CONFIG"

# Create data persistence validation script
VALIDATION_SCRIPT="Admin-Local/Deployment/Scripts/validate-data-persistence.sh"
cat > $VALIDATION_SCRIPT << 'EOF'
#!/bin/bash

echo "🔍 Validating data persistence configuration..."

# Load persistence config
PERSISTENCE_CONFIG="Admin-Local/Deployment/Configs/data-persistence.json"

if [ ! -f "$PERSISTENCE_CONFIG" ]; then
    echo "❌ Persistence configuration not found"
    exit 1
fi

# Check local directories exist
echo "📂 Checking local shared directories..."
jq -r '.persistence.directories[].source' $PERSISTENCE_CONFIG | while read dir; do
    if [ -d "$dir" ]; then
        echo "✅ $dir exists"
    else
        echo "⚠️ $dir does not exist (will be created if needed)"
    fi
done

# Check local files exist
echo "📄 Checking local shared files..."
jq -r '.persistence.files[].source' $PERSISTENCE_CONFIG | while read file; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "⚠️ $file does not exist (normal for new projects)"
    fi
done

echo "✅ Data persistence validation completed"
EOF

chmod +x $VALIDATION_SCRIPT

# Create backup script for shared data
BACKUP_SCRIPT="Admin-Local/Deployment/Scripts/backup-shared-data.sh"
cat > $BACKUP_SCRIPT << 'EOF'
#!/bin/bash

echo "💾 Creating backup of shared data..."

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

BACKUP_DIR="Admin-Local/Deployment/Backups/shared-data-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

PERSISTENCE_CONFIG="Admin-Local/Deployment/Configs/data-persistence.json"

if [ ! -f "$PERSISTENCE_CONFIG" ]; then
    echo "❌ Persistence configuration not found"
    exit 1
fi

# Backup shared directories
echo "📂 Backing up directories..."
jq -r '.persistence.directories[].source' $PERSISTENCE_CONFIG | while read dir; do
    if [ -d "$dir" ]; then
        echo "Backing up: $dir"
        mkdir -p "$BACKUP_DIR/$(dirname $dir)"
        cp -r "$dir" "$BACKUP_DIR/$dir"
    fi
done

# Backup shared files  
echo "📄 Backing up files..."
jq -r '.persistence.files[].source' $PERSISTENCE_CONFIG | while read file; do
    if [ -f "$file" ]; then
        echo "Backing up: $file"
        mkdir -p "$BACKUP_DIR/$(dirname $file)"
        cp "$file" "$BACKUP_DIR/$file"
    fi
done

echo "✅ Backup completed: $BACKUP_DIR"
echo "$BACKUP_DIR" > Admin-Local/Deployment/Logs/last-backup-path.txt
EOF

chmod +x $BACKUP_SCRIPT

# Run validation
echo ""
echo "🔍 Running data persistence validation..."
bash $VALIDATION_SCRIPT

echo ""
echo "📋 Data persistence setup completed!"
echo "Configuration: $PERSISTENCE_CONFIG"
echo "Validation script: $VALIDATION_SCRIPT"  
echo "Backup script: $BACKUP_SCRIPT"

# Show configuration summary
echo ""
echo "📊 Persistence Summary:"
cat $PERSISTENCE_CONFIG | head -20
```

---

## Step 19: Emergency Rollback Setup
**🟢 Location:** Local Machine | **⏱️ Time:** 20-30 minutes | **🚨 Type:** Recovery System

### Purpose
Establish comprehensive emergency rollback and recovery procedures ensuring rapid restoration of service in case of deployment failures, critical errors, or system compromises.

### When to Execute
**After data persistence setup** - This ensures rollback procedures can restore both application and data to previous working states.

### Action Steps

1. **Execute Emergency Rollback Setup Script**
   a. Run the emergency rollback configuration:
   ```bash
   chmod +x Admin-Local/Deployment/Scripts/emergency-rollback.sh
   bash Admin-Local/Deployment/Scripts/emergency-rollback.sh
   ```
   b. Review rollback procedures and test in safe environment
   c. Verify automatic rollback triggers are properly configured
   d. Document manual rollback procedures for critical scenarios

2. **Configure Automatic Rollback Triggers**
   a. **Health Check Failures**
      - HTTP response code monitoring (500, 503, timeout)
      - Application boot failure detection
      - Database connection failure detection
      - Critical functionality validation failures

   b. **Performance Threshold Violations**
      - Response time degradation beyond acceptable limits
      - Memory usage exceeding critical thresholds
      - CPU utilization sustained above emergency levels
      - Disk space exhaustion detection

   c. **Security Incident Detection**
      - Unusual error rate spikes
      - Authentication failure patterns
      - Suspicious file system changes
      - Malware or intrusion detection

3. **Implement Multi-Level Rollback Strategy**
   a. **Level 1: Configuration Rollback**
      - Revert environment configuration changes
      - Restore previous .env settings
      - Reset cache and configuration files
      - Reload application configuration

   b. **Level 2: Application Version Rollback**
      - Switch symlink to previous release
      - Restore previous application code
      - Revert database migrations if needed
      - Clear and rebuild caches

   c. **Level 3: Complete System Rollback**
      - Full restoration from backup
      - Data restoration to last known good state
      - Complete environment reconstruction
      - Manual verification and validation

4. **Test Rollback Procedures**
   a. **Automated Testing**
      - Simulate deployment failures
      - Test automatic rollback triggers
      - Verify rollback completion time
      - Validate application functionality post-rollback

   b. **Manual Rollback Testing**
      - Practice manual rollback procedures
      - Time manual rollback processes
      - Train team on emergency procedures
      - Document lessons learned and improvements

### Expected Results ✅
- [ ] Emergency rollback procedures fully configured and tested
- [ ] Automatic rollback triggers operational and responsive
- [ ] Multi-level rollback strategy implemented and validated
- [ ] Rollback procedures documented and team trained

### Verification Steps
- [ ] Rollback triggers respond correctly to simulated failures
- [ ] Application can be restored to previous working state
- [ ] Data integrity maintained during rollback process
- [ ] Rollback completion time meets acceptable thresholds

### Troubleshooting Tips
- **Issue:** Rollback fails to restore previous state
  - **Solution:** Check symlink integrity and backup completeness
- **Issue:** Data loss during rollback process
  - **Solution:** Verify data persistence and backup procedures
- **Issue:** Rollback takes too long to complete
  - **Solution:** Optimize rollback scripts and pre-prepare rollback assets

### Emergency Rollback Script Template

**emergency-rollback.sh:**
```bash
#!/bin/bash

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Emergency Rollback and Recovery Setup                ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

ROLLBACK_CONFIG="Admin-Local/Deployment/Configs/rollback-config.json"

echo "🚨 Setting up emergency rollback system for: $PROJECT_NAME"

# Create rollback configuration
cat > $ROLLBACK_CONFIG << EOF
{
  "project": "$PROJECT_NAME",
  "server_path": "$PATH_SERVER",
  "rollback_strategy": "atomic-symlink",
  "health_checks": {
    "enabled": true,
    "url": "$(jq -r '.deployment.health_check_url // "https://example.com/health"' Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null)",
    "timeout": 30,
    "retries": 3,
    "expected_status": 200
  },
  "triggers": {
    "http_errors": {
      "enabled": true,
      "threshold": 5,
      "window": 300,
      "codes": [500, 502, 503, 504]
    },
    "response_time": {
      "enabled": true,
      "max_response_ms": 10000,
      "consecutive_failures": 3
    },
    "boot_failure": {
      "enabled": true,
      "artisan_check": true,
      "config_check": true
    }
  },
  "rollback_levels": [
    {
      "level": 1,
      "name": "configuration",
      "description": "Revert configuration changes only",
      "actions": [
        "clear_cache",
        "revert_env",
        "reload_config"
      ]
    },
    {
      "level": 2,
      "name": "application",
      "description": "Revert to previous application version",
      "actions": [
        "switch_symlink",
        "clear_cache",
        "restart_services"
      ]
    },
    {
      "level": 3,
      "name": "complete",
      "description": "Full system rollback with data restoration",
      "actions": [
        "restore_backup",
        "revert_database",
        "full_restart"
      ]
    }
  ],
  "notifications": {
    "email": "",
    "webhook": "",
    "log_file": "$PATH_SERVER/shared/logs/rollback.log"
  }
}
EOF

echo "✅ Rollback configuration created: $ROLLBACK_CONFIG"

# Create atomic rollback script
ATOMIC_ROLLBACK="Admin-Local/Deployment/Scripts/atomic-rollback.sh"
cat > $ATOMIC_ROLLBACK << 'EOF'
#!/bin/bash

echo "🚨 EMERGENCY ROLLBACK INITIATED"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

ROLLBACK_CONFIG="Admin-Local/Deployment/Configs/rollback-config.json"
ROLLBACK_LEVEL="${1:-2}"  # Default to level 2 (application rollback)

if [ ! -f "$ROLLBACK_CONFIG" ]; then
    echo "❌ Rollback configuration not found!"
    exit 1
fi

echo "📊 Rollback Level: $ROLLBACK_LEVEL"

# Log rollback attempt
ROLLBACK_LOG="Admin-Local/Deployment/Logs/rollback-$(date +%Y%m%d-%H%M%S).log"
echo "Rollback initiated at $(date)" > $ROLLBACK_LOG
echo "Rollback level: $ROLLBACK_LEVEL" >> $ROLLBACK_LOG
echo "Triggered by: ${ROLLBACK_TRIGGER:-manual}" >> $ROLLBACK_LOG

case $ROLLBACK_LEVEL in
    1)
        echo "🔧 Level 1: Configuration Rollback"
        # Clear caches
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        
        # Revert environment if backup exists
        if [ -f ".env.backup" ]; then
            cp .env.backup .env
            echo "✅ Environment configuration reverted"
        fi
        ;;
    2)
        echo "🔄 Level 2: Application Rollback"
        # Check if previous release exists
        if [ -L "current" ] && [ -d "releases" ]; then
            CURRENT_RELEASE=$(readlink current)
            RELEASES=($(ls -1t releases/))
            
            if [ ${#RELEASES[@]} -gt 1 ]; then
                PREVIOUS_RELEASE=${RELEASES[1]}
                echo "Rolling back from $CURRENT_RELEASE to $PREVIOUS_RELEASE"
                
                # Create backup of current symlink
                ln -nfs $CURRENT_RELEASE current.backup
                
                # Switch to previous release
                ln -nfs releases/$PREVIOUS_RELEASE current
                
                # Clear caches
                php artisan config:clear
                php artisan route:clear
                php artisan view:clear
                
                echo "✅ Application rolled back to previous version"
            else
                echo "❌ No previous release available for rollback"
                exit 1
            fi
        else
            echo "❌ Release structure not found - cannot perform atomic rollback"
            exit 1
        fi
        ;;
    3)
        echo "🚨 Level 3: Complete System Rollback"
        echo "⚠️ This is a destructive operation - proceeding with caution"
        
        # Restore from backup (implementation depends on backup strategy)
        if [ -f "Admin-Local/Deployment/Logs/last-backup-path.txt" ]; then
            BACKUP_PATH=$(cat Admin-Local/Deployment/Logs/last-backup-path.txt)
            if [ -d "$BACKUP_PATH" ]; then
                echo "Restoring from backup: $BACKUP_PATH"
                # Implement full restore logic here
                echo "⚠️ Complete rollback requires manual verification"
            fi
        else
            echo "❌ No backup available for complete rollback"
            exit 1
        fi
        ;;
    *)
        echo "❌ Invalid rollback level: $ROLLBACK_LEVEL"
        exit 1
        ;;
esac

# Post-rollback verification
echo "🔍 Verifying rollback success..."

# Test application availability
if php artisan --version >/dev/null 2>&1; then
    echo "✅ Application is responsive"
    echo "Rollback completed successfully at $(date)" >> $ROLLBACK_LOG
else
    echo "❌ Application is not responsive after rollback"
    echo "Rollback failed at $(date)" >> $ROLLBACK_LOG
    exit 1
fi

echo "✅ Emergency rollback completed successfully"
echo "📋 Rollback log: $ROLLBACK_LOG"
EOF

chmod +x $ATOMIC_ROLLBACK

# Create health check script for rollback triggers
HEALTH_CHECK="Admin-Local/Deployment/Scripts/health-check.sh"
cat > $HEALTH_CHECK << 'EOF'
#!/bin/bash

# Health check script for rollback triggers
source Admin-Local/Deployment/Scripts/load-variables.sh

HEALTH_URL=$(jq -r '.health_checks.url' Admin-Local/Deployment/Configs/rollback-config.json 2>/dev/null)
TIMEOUT=$(jq -r '.health_checks.timeout // 30' Admin-Local/Deployment/Configs/rollback-config.json 2>/dev/null)

if [ "$HEALTH_URL" != "null" ] && [ -n "$HEALTH_URL" ]; then
    echo "🏥 Checking application health: $HEALTH_URL"
    
    if curl -f -s --max-time $TIMEOUT "$HEALTH_URL" >/dev/null 2>&1; then
        echo "✅ Health check passed"
        exit 0
    else
        echo "❌ Health check failed"
        exit 1
    fi
else
    # Fallback to local application check
    echo "🏥 Checking local application health"
    
    if php artisan --version >/dev/null 2>&1; then
        echo "✅ Local health check passed"
        exit 0
    else
        echo "❌ Local health check failed"
        exit 1
    fi
fi
EOF

chmod +x $HEALTH_CHECK

# Test rollback system
echo ""
echo "🧪 Testing rollback system..."

# Test health check
echo "Testing health check..."
if bash $HEALTH_CHECK; then
    echo "✅ Health check system operational"
else
    echo "⚠️ Health check needs configuration"
fi

echo ""
echo "📋 Emergency rollback setup completed!"
echo "Configuration: $ROLLBACK_CONFIG"
echo "Atomic rollback: $ATOMIC_ROLLBACK"
echo "Health check: $HEALTH_CHECK"

echo ""
echo "🚨 Emergency Rollback Usage:"
echo "Level 1 (Config): bash $ATOMIC_ROLLBACK 1"
echo "Level 2 (App):    bash $ATOMIC_ROLLBACK 2"
echo "Level 3 (Full):   bash $ATOMIC_ROLLBACK 3"
```

---

## Step 20: Final Pre-Build Validation
**🟢 Location:** Local Machine | **⏱️ Time:** 15-25 minutes | **✅ Type:** Final Validation

### Purpose
Execute final comprehensive validation before beginning the actual build and deployment process, ensuring all preparation steps are completed successfully and the project is fully ready for production deployment.

### When to Execute
**After emergency rollback setup** - This serves as the final gate before proceeding to the build and deployment phase.

### Action Steps

1. **Execute Final Pre-Build Validation**
   a. Run the final validation script:
   ```bash
   chmod +x Admin-Local/Deployment/Scripts/final-pre-build-validation.sh
   bash Admin-Local/Deployment/Scripts/final-pre-build-validation.sh
   ```
   b. Review the comprehensive validation report
   c. Address any remaining issues or gaps
   d. Confirm readiness to proceed to build phase

2. **Complete Section B Readiness Checklist**
   a. **Validation and Testing (Steps 14-16)**
      - [ ] Pre-build validation passed (10-point checklist)
      - [ ] Composer strategy configured and tested
      - [ ] Production dependencies verified
      - [ ] Build process tested (12-point checklist)
      - [ ] Build strategy configured

   b. **Security and Protection (Steps 17-19)**
      - [ ] Security scan completed with no critical issues
      - [ ] Data persistence configured and tested
      - [ ] Emergency rollback system operational

   c. **Infrastructure Readiness**
      - [ ] All deployment scripts executable and functional
      - [ ] Configuration files validated and version controlled
      - [ ] Backup and recovery procedures tested
      - [ ] Server connectivity and permissions verified

3. **Generate Deployment Readiness Report**
   a. Create comprehensive readiness documentation
   b. Include validation results and metrics
   c. Document any known issues or limitations
   d. Provide recommendations for build process

4. **Tag Section B Completion**
   a. Create Git tag for Section B completion:
   ```bash
   git add .
   git commit -m "Section B complete: Build preparation and validation ready"
   git tag v1.0-section-b-complete
   git push origin main --tags
   ```
   b. Update project documentation
   c. Notify team of readiness status

### Expected Results ✅
- [ ] Final validation confirms 100% readiness for build phase
- [ ] All Section B components operational and tested
- [ ] Deployment readiness report generated
- [ ] Project tagged and documented for Section B completion

### Verification Steps
- [ ] All validation checks pass without critical issues
- [ ] Security posture meets production requirements
- [ ] Data protection and rollback systems functional
- [ ] Build process validated and optimized

### Final Pre-Build Validation Script Template

**final-pre-build-validation.sh:**
```bash
#!/bin/bash

echo "╔══════════════════════════════════════════════════════════╗"
echo "║     Final Pre-Build Validation & Readiness Check        ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Load variables
source Admin-Local/Deployment/Scripts/load-variables.sh

FINAL_REPORT