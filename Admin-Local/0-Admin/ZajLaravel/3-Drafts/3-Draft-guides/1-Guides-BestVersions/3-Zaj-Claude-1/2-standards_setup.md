# Standards, Path Variables & Setup Requirements

**Version:** 2.0  
**Purpose:** Establish unified standards, path management, and setup requirements for consistent Laravel deployment

---

## 🎯 **DOCUMENT PURPOSE**

This document establishes the foundation standards that make the Universal Laravel Deployment system work reliably across all environments. Understanding these standards is crucial for successful deployment.

**Key Concepts Covered:**
- Visual identification system for different environments
- Path variables that make commands work anywhere
- Admin-Local directory structure
- Prerequisites and setup requirements
- JSON configuration management

---

## 🎨 **VISUAL IDENTIFICATION SYSTEM**

Throughout this guide, you'll see color-coded indicators showing where each operation takes place:

### **Environment Locations**
- 🟢 **Local Machine** - Your development computer where you write code
- 🟡 **Builder VM** - Build server or CI/CD environment (GitHub Actions, DeployHQ)
- 🔴 **Server** - Your production web server where users access your application
- 🟣 **User-Configurable** - Scripts and hooks you can customize

### **Operation Types**
- 🔧 **Automated Script** - Runs automatically with provided scripts
- 📋 **Manual Action** - Requires your input or decision
- 🗏️ **Builder Commands** - Build-specific operations (npm, composer)
- 📊 **Validation Step** - Testing and verification
- 🛡️ **Security Operation** - Security-related actions

### **Hook Types (User-Configurable)**
- 1️⃣ **Pre-release Hooks** - Run BEFORE deployment changes
- 2️⃣ **Mid-release Hooks** - Run DURING deployment process  
- 3️⃣ **Post-release Hooks** - Run AFTER deployment completion

---

## 📂 **PATH VARIABLES SYSTEM**

### **Why Path Variables Matter**

Instead of using hardcoded paths like `/home/user/myproject`, we use variables like `%path-localMachine%`. This allows:
- ✅ Commands work on any computer
- ✅ Easy to adapt for different projects
- ✅ No need to modify scripts for your specific setup
- ✅ Consistent documentation across all environments

### **Primary Path Variables**

```bash
# These are defined in deployment-variables.json
%path-localMachine%    # Your local development project directory
%path-server%          # Root deployment directory on server
%path-Builder-VM%      # Working directory in build environment
%path-shared%          # Shared resources directory on server
```

### **Example Path Usage**

**Before (Hardcoded - DON'T DO THIS):**
```bash
cd /Users/john/projects/mylaravelapp
./scripts/deploy.sh
```

**After (Variable-based - CORRECT):**
```bash
cd %path-localMachine%
./Admin-Local/Deployment/Scripts/deploy.sh
```

### **How Path Variables Get Set**

**1. Initial Configuration** (You do this once in Section A)
```json
# deployment-variables.json
{
  "paths": {
    "local_machine": "/Users/yourname/projects/your-laravel-app",
    "server_deploy": "/var/www/your-app",
    "server_domain": "your-app.com"
  }
}
```

**2. Automatic Loading** (Scripts do this automatically)
```bash
# This happens automatically in all scripts
source Admin-Local/Deployment/Scripts/load-variables.sh
echo "Working in: $PATH_LOCAL_MACHINE"
```

---

## 🏗️ **ADMIN-LOCAL DIRECTORY STRUCTURE**

### **Complete Structure Overview**

```
Admin-Local/
├── 1-Admin-Area/                    # Universal templates (copy once, use everywhere)
│   ├── 01-Guides-And-Standards/     # Master checklists and documentation
│   │   ├── SECTION-A-Project-Setup.md
│   │   ├── SECTION-B-Build-Preparation.md
│   │   └── SECTION-C-Deploy-Execution.md
│   ├── 02-Master-Scripts/           # Universal automation scripts
│   │   ├── load-variables.sh
│   │   ├── comprehensive-env-check.sh
│   │   ├── universal-dependency-analyzer.sh
│   │   ├── build-pipeline.sh
│   │   ├── atomic-switch.sh
│   │   └── emergency-rollback.sh
│   └── 03-File-Templates/           # Templates for new projects
│       ├── 01-Project-Setup/
│       │   ├── .gitignore.template
│       │   └── project-card.template.md
│       ├── 02-Deployment/
│       │   └── deployment-variables.template.json
│       └── 03-Customization/
│           └── CustomizationServiceProvider.template.php
│
└── 2-Project-Area/                  # Specific to THIS project
    ├── 01-Deployment-Toolbox/       # Version-controlled deployment tools
    │   ├── 01-Configs/
    │   │   ├── deployment-variables.json
    │   │   └── build-strategy.json
    │   ├── 02-EnvFiles/
    │   │   ├── .env.local
    │   │   ├── .env.staging
    │   │   └── .env.production
    │   └── 03-Scripts/               # Project-specific scripts
    │       ├── pre-release-hooks.sh
    │       ├── mid-release-hooks.sh
    │       └── post-release-hooks.sh
    │
    └── 02-Project-Records/           # Local only (excluded from Git)
        ├── 01-Project-Info/
        │   └── project-card.md
        ├── 02-Installation-History/
        │   ├── vendor-snapshots/
        │   └── installation-notes.md
        ├── 03-Deployment-History/
        │   ├── deployment-logs/
        │   └── rollback-records/
        ├── 04-Customization-And-Investment-Tracker/
        │   ├── custom-changes.md
        │   ├── investment-summary.md
        │   └── audit-trail/
        └── 05-Logs-And-Maintenance/
            ├── Local-Script-Logs/
            └── Maintenance-Notes.md
```

### **Directory Purpose Explained**

#### **1-Admin-Area (Universal)**
- **Purpose:** Templates and scripts that work for ANY Laravel project
- **Usage:** Copy once to your development machine, use for all projects
- **Version Control:** Not project-specific, maintain separately

#### **2-Project-Area/01-Deployment-Toolbox (Version Controlled)**
- **Purpose:** Project-specific deployment configurations and scripts
- **Usage:** Committed to Git, shared with team members
- **Contains:** Configuration files, environment files, custom hooks

#### **2-Project-Area/02-Project-Records (Local Only)**
- **Purpose:** Local documentation and logs
- **Usage:** Never committed to Git (in .gitignore)
- **Contains:** Personal notes, deployment history, maintenance logs

---

## ⚙️ **JSON CONFIGURATION MANAGEMENT**

### **Central Configuration File**

**File Location:** `Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/deployment-variables.json`

### **Complete Configuration Template**

```json
{
  "project": {
    "name": "MyLaravelApp",
    "type": "laravel",
    "version": "10.x",
    "has_frontend": true,
    "frontend_framework": "vue|react|blade|auto-detect",
    "build_system": "vite|mix|none|auto-detect",
    "uses_queues": false,
    "uses_websockets": false
  },
  "paths": {
    "local_machine": "/absolute/path/to/your/project",
    "server_deploy": "/var/www/your-app",
    "server_domain": "your-app.com",
    "server_public": "/var/www/your-app/current/public",
    "builder_vm": "/tmp/build"
  },
  "repository": {
    "url": "https://github.com/yourusername/your-app.git",
    "ssh_url": "git@github.com:yourusername/your-app.git",
    "branch": "main",
    "commit_start": "HEAD~5",
    "commit_end": "HEAD"
  },
  "versions": {
    "php": "8.2",
    "composer": "2.6",
    "node": "18.17",
    "npm": "9.6"
  },
  "deployment": {
    "strategy": "local|github_actions|deployhq|server_build",
    "build_location": "local|builder_vm|server",
    "keep_releases": 5,
    "health_check_url": "/health",
    "maintenance_mode": false,
    "backup_before_deploy": true
  },
  "hosting": {
    "type": "shared|vps|dedicated",
    "provider": "optional",
    "has_root_access": false,
    "exec_enabled": true,
    "symlink_enabled": true,
    "max_execution_time": 300
  },
  "database": {
    "connection": "mysql|pgsql|sqlite",
    "backup_before_migration": true,
    "zero_downtime_migrations": true
  },
  "cache": {
    "driver": "file|redis|memcached",
    "clear_opcache": true,
    "clear_application_cache": true
  },
  "queues": {
    "driver": "database|redis|sqs",
    "restart_workers": true,
    "uses_horizon": false
  },
  "notifications": {
    "email": "your-email@example.com",
    "slack_webhook": "",
    "discord_webhook": ""
  }
}
```

### **Configuration Sections Explained**

#### **Project Section**
- Defines what type of Laravel application you're deploying
- Auto-detection features identify frontend frameworks and build systems
- Helps scripts make appropriate decisions

#### **Paths Section**
- Defines where everything is located
- Used to generate all deployment commands
- Makes scripts work on any environment

#### **Deployment Section**
- Controls how deployment happens
- Strategy determines which scripts run where
- Safety features like backup and health checks

#### **Hosting Section**
- Describes your server capabilities
- Helps scripts adapt to hosting limitations
- Prevents deployment failures due to server restrictions

---

## 🔧 **SCRIPT STANDARDS**

### **Script Header Template**

Every script in the system follows this standard format:

```bash
#!/bin/bash

# Script: script-name.sh
# Purpose: Clear description of what this script does
# Version: 2.0
# Section: A|B|C - Section Name
# Location: 🟢🟡🔴 Environment where it runs

set -euo pipefail # Fail fast on errors

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "${SCRIPT_DIR}/../01-Configs/load-variables.sh"

# Standard functions
log() { echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"; }
error_exit() { log "ERROR: $1"; exit 1; }
success() { log "✅ $1"; }
warning() { log "⚠️ $1"; }
```

### **Error Handling Standards**

All scripts include robust error handling:

```bash
# File existence checks
[[ -f "required_file" ]] || error_exit "Required file missing: required_file"

# Command execution checks
if ! command_that_might_fail; then
    error_exit "Command failed: description of what failed"
fi

# Directory checks
[[ -d "$REQUIRED_DIR" ]] || error_exit "Directory missing: $REQUIRED_DIR"
```

---

## 📋 **PREREQUISITES CHECKLIST**

### **Required on Local Development Machine**

#### **Essential Software**
- [ ] **PHP 8.1+** - Laravel requirement
- [ ] **Composer 2.x** - Dependency management
- [ ] **Git** - Version control
- [ ] **Node.js 16+** (if using frontend assets)
- [ ] **SSH client** - Server access

#### **Recommended Software**
- [ ] **Laravel Herd Pro** - Local development environment
- [ ] **VS Code** - Code editor with good Laravel support
- [ ] **jq** - JSON processing (installed automatically by scripts)

#### **Verification Commands**
```bash
# Run these to verify your setup
php --version        # Should show 8.1+
composer --version   # Should show 2.x
git --version       # Any recent version
node --version      # Should show 16+ (if using frontend)
ssh -V              # Any recent version
```

### **Required Knowledge**

#### **Essential Skills**
- [ ] Basic Laravel development (routes, controllers, views)
- [ ] Basic terminal/command line usage
- [ ] Basic Git operations (add, commit, push, pull)
- [ ] Basic understanding of web servers

#### **Helpful but Not Required**
- [ ] Linux file permissions
- [ ] SSH and server administration
- [ ] JSON file format
- [ ] Docker/containerization concepts

### **Server Requirements**

#### **Minimum Server Specs**
- [ ] **PHP 8.1+** with required Laravel extensions
- [ ] **Composer** (will be installed if missing)
- [ ] **SSH access** (or similar server access)
- [ ] **Git** (for repository operations)
- [ ] **Sufficient disk space** (at least 2GB free)

#### **PHP Extensions Required**
```bash
# These extensions must be available:
- openssl
- pdo
- mbstring
- tokenizer
- xml
- ctype
- json
- fileinfo
- zip (for deployment packages)
```

#### **Server Functions Required**
```bash
# These functions must be enabled:
- exec (for running commands)
- symlink (for atomic deployments)
- file_get_contents (for health checks)
```

---

## 🎯 **EXPECTED RESULTS FORMAT**

### **Standard Success Messages**

Throughout the guides, you'll see consistent result indicators:

```bash
✅ Success condition description
📁 File/directory creation confirmation  
🔧 Configuration change confirmation
📊 Status/metrics display
⚠️ Warning or attention needed
❌ Error condition
```

### **Verification Commands Pattern**

Each step includes verification commands to confirm success:

```bash
# Example verification pattern
ls -la expected_file                    # File exists check
grep "expected_content" config_file      # Content verification
curl -I http://your-app.com             # Service check
```

---

## 🔄 **INTEGRATION POINTS**

### **Section A → Section B Integration**
- Admin-Local structure must be complete
- Path variables must be configured
- Environment analysis must pass
- Dependencies must be analyzed

### **Section B → Section C Integration**
- Build process must be validated
- Security scans must pass
- Deployment strategy must be configured
- All validation checkpoints must pass

### **Cross-Section Consistency**
- Path variables remain consistent
- Admin-Local structure stays identical
- Script standards apply throughout
- Error handling is uniform

---

## 🚀 **GETTING STARTED CHECKLIST**

### **Before Starting Section A**
- [ ] Read and understand this standards document
- [ ] Verify all prerequisites are met
- [ ] Choose your deployment strategy
- [ ] Have server access credentials ready
- [ ] Backup any existing deployment setup

### **Setup Verification**
```bash
# Run this quick verification
php --version && composer --version && git --version
echo "✅ Basic tools verified"

# Test SSH access to your server (if using server deployment)
ssh user@your-server.com "echo 'SSH connection successful'"
```

### **Path Planning**
Before starting, decide on your path structure:

```bash
# Example local machine path
%path-localMachine% = /Users/yourname/projects/your-laravel-app

# Example server path  
%path-server% = /var/www/your-app

# Example domain
your-app.com
```

---

## 📖 **READING THE GUIDES**

### **Command Explanation Format**
```bash
command --flag value  # Comment explaining what this does
```

### **Step Structure**
Each step follows this pattern:
1. **Purpose** - What this step achieves
2. **Action** - What you need to do
3. **Commands** - Exact commands to run
4. **Verification** - How to confirm success
5. **Expected Result** - What you should see

### **Troubleshooting**
- Common issues are documented with each step
- Error messages include explanations and solutions
- Alternative approaches provided when needed

---

## 🎯 **SUCCESS CRITERIA**

### **Standards Compliance**
- All path variables properly configured
- Admin-Local structure correctly established
- JSON configuration valid and complete
- All prerequisites verified

### **Ready for Section A**
- Development environment functional
- Server access confirmed
- Basic Laravel application running locally
- Git repository accessible

**Next Document:** [3-Section-A-Project-Setup.md](3-Section-A-Project-Setup.md)

---

## 💡 **KEY INSIGHT**

The standards in this document aren't just guidelines - they're the foundation that makes universal deployment possible. By following these standards consistently, you ensure that:

- Commands work reliably across different environments
- Scripts can find and use configuration automatically  
- Team members can understand and modify the deployment setup
- The system scales from simple projects to complex applications

**Take time to understand these standards - they'll save you hours of troubleshooting later.**