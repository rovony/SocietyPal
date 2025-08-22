# Master Checklist Standards & Consistency Guidelines

**Version:** 2.0  
**Generated:** August 20, 2025  
**Purpose:** Establish unified formatting, structure, and content standards for all Master Checklist files

---

## **Document Structure Standards**

### **File Naming Convention**
```
SECTION A: Project Setup.md
SECTION B: Prepare for Build and Deployment.md  
SECTION C: Build and Deploy.md
```

### **Header Format**
```markdown
# Master Checklist for **SECTION [X]: [Title]**

**Version:** 2.0  
**Last Updated:** [Date]  
**Purpose:** [Brief description of section objective]

This checklist consolidates all necessary steps for [Section Description]. Follow each step carefully to ensure smooth and successful deployment.
```

---

## **Step Format Standards**

### **Primary Step Format**
```markdown
### Step [X]: [short-name] - Descriptive Title
**Location:** [🟢🟡🔴🟣] [Environment Description]  
**Path:** `%path-variable%`  
**Purpose:** [Clear objective statement]  
**When:** [Timing and prerequisites]  
**Action:**
1. [Numbered sub-steps with clear commands]
2. [Expected results validation]
3. [Error handling instructions]
```

### **Sub-Step Format**  
```markdown
### Step [X.Y]: [sub-short-name] - Sub-Task Title
**Purpose:** [Sub-task objective]
**Action:**
1. [Sub-task steps]
   ```bash
   # Commands with comments
   command --flag value
   ```
2. [Validation step]
3. [Expected result confirmation]
```

---

## **Visual Identification Standards**

### **Location Tags (Required)**
- 🟢 **Local Machine** - Operations on developer workstation
- 🟡 **Builder VM** - Operations on build server/CI environment
- 🔴 **Server** - Operations on production server  
- 🟣 **User-Configurable** - SSH hooks and customizable commands

### **Hook Identification (For User-Configurable)**
- 1️⃣ **Pre-release hooks** - Before atomic deployment switch
- 2️⃣ **Mid-release hooks** - During deployment process
- 3️⃣ **Post-release hooks** - After deployment completion

### **Special Operation Tags**
- 🏗️ **Builder Commands** - Build-specific operations
- 🔧 **Configuration** - Setup and configuration tasks
- 🔍 **Validation** - Testing and verification steps
- 🧹 **Cleanup** - Maintenance and cleanup operations

---

## **Path Variables Standards**

### **Standard Path Variables**
```bash
# Primary paths (must be consistent across all files)
%path-localMachine%    # Developer local machine working directory
%path-server%          # Production server deployment directory  
%path-Builder-VM%      # Build server/VM working directory
%path-shared%          # Shared resources directory on server

# Derived paths (calculated from primary paths)
%path-releases%        # Server releases directory
%path-current%         # Current release symlink
%path-backup%          # Backup directory
```

### **Path Usage Examples**
```bash
# Correct usage
cd %path-localMachine%
source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh

# Incorrect usage  
cd /absolute/path/to/project  # Should use variable
```

---

## **Admin-Local Structure Standards**

### **Unified Directory Structure**
```
Admin-Local/
├── 0-Admin/
│   └── zaj-Guides/                    # Universal guides system
├── 1-CurrentProject/
│   ├── Current-Session/               # Active session data
│   ├── Deployment-History/            # Historical deployments
│   ├── Installation-Records/          # Install documentation
│   ├── Maintenance-Logs/              # Maintenance history
│   └── Project-Trackers/              # Project tracking
│       ├── Audit-Trail/               # Change tracking
│       ├── Conflict-Resolution/       # Conflict documentation  
│       ├── Custom-Changes/            # Customization tracking
│       └── Vendor-Snapshots/          # Original code snapshots
└── Deployment/
    ├── EnvFiles/                      # Environment configurations
    ├── Scripts/                       # Deployment scripts
    ├── Configs/                       # Deployment configurations
    ├── Backups/                       # Database/file backups
    └── Logs/                          # Deployment logs
```

### **Required Files in Admin-Local/Deployment/**
```
Configs/
└── deployment-variables.json         # Universal configuration

Scripts/  
├── load-variables.sh                 # Variable loader
├── comprehensive-env-check.sh        # Environment analysis
├── universal-dependency-analyzer.sh  # Dependency analysis
├── pre-deployment-validation.sh      # Pre-deployment checks
├── setup-composer-strategy.sh        # Composer configuration
└── configure-build-strategy.sh       # Build strategy setup

EnvFiles/
├── .env.local                        # Local environment
├── .env.staging                      # Staging environment  
└── .env.production                   # Production environment
```

---

## **Script Standards**

### **Script Header Template**
```bash
#!/bin/bash

# Script: [script-name.sh]
# Purpose: [Clear description of script function]
# Version: 2.0
# Section: [A/B/C] - [Section Name]
# Location: [🟢🟡🔴] [Environment]

set -euo pipefail  # Exit on error, undefined variables, pipe failures

# Load deployment variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "${SCRIPT_DIR}/load-variables.sh"
```

### **Script Function Standards**
```bash
# Standard functions that should be in all scripts

# Log function with timestamp
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Error handling function
error_exit() {
    log "ERROR: $1"
    exit 1
}

# Success confirmation function  
success() {
    log "✅ $1"
}

# Warning function
warning() {
    log "⚠️ $1"  
}
```

### **Error Handling Standards**
```bash
# Every command that can fail should have error handling
command_that_might_fail || error_exit "Command failed: description"

# File existence checks
[[ -f "required_file" ]] || error_exit "Required file missing: required_file"

# Directory checks  
[[ -d "required_dir" ]] || error_exit "Required directory missing: required_dir"
```

---

## **Expected Results Standards**

### **Results Format Template**
```markdown
**Expected Result:**
```
✅ [Success condition description]
📁 [File/directory creation confirmation]  
🔧 [Configuration change confirmation]
📊 [Status/metrics display]
```

**Verification Commands:**
```bash
# Commands to verify the step worked correctly
ls -la expected_file
grep "expected_content" config_file
```
```

### **Standard Success Messages**
- ✅ **Installation complete** - For dependency/software installation
- ✅ **Configuration applied** - For configuration changes
- ✅ **Validation passed** - For verification steps
- ✅ **Build successful** - For build operations  
- ✅ **Deployment ready** - For pre-deployment completion
- ✅ **Service started** - For service operations

---

## **Content Standards**

### **Language Guidelines**
- **Beginner-friendly:** No assumptions about user knowledge
- **Action-oriented:** Use imperative verbs (Create, Configure, Install)
- **Specific:** Exact commands and file paths
- **Clear:** Simple sentence structure
- **Consistent:** Same terminology throughout

### **Code Block Standards**
```bash
# Always include comments explaining what commands do
# Use consistent indentation (2 spaces)
# Include error handling
# Show expected output when helpful

cd %path-localMachine%  # Navigate to project root
composer install       # Install PHP dependencies
```

### **Command Standards**
- Always use full command paths when ambiguous
- Include all required flags and options
- Add error handling for critical commands
- Provide alternative commands for different environments

---

## **Cross-Section Integration Standards**

### **Section A → Section B Integration Points**
- Admin-Local structure must be identical
- Path variables must be consistent  
- Analysis tools must be compatible
- Environment configuration must align

### **Section B → Section C Integration Points**
- Build strategies must match deployment expectations
- Validation results must be usable in deployment
- Pre-deployment checks must align with deployment requirements
- Configuration must be deployment-ready

### **Universal Elements (All Sections)**
- Path variable system
- Admin-Local directory structure
- Visual identification tags
- Error handling patterns
- Expected results format

---

## **Quality Assurance Standards**

### **Internal Consistency Checklist**
- [ ] All path variables use standard format
- [ ] All location tags applied correctly
- [ ] All scripts have proper error handling
- [ ] All steps have expected results
- [ ] All commands have explanatory comments

### **Cross-Section Consistency Checklist**  
- [ ] Admin-Local structure identical across sections
- [ ] Path variables consistent between sections
- [ ] Integration points clearly defined
- [ ] No duplicate or conflicting steps

### **User Experience Checklist**
- [ ] Beginner can follow without assistance
- [ ] No unexplained technical terms
- [ ] Clear next steps after each action
- [ ] Error recovery procedures provided

---

## **Maintenance Standards**

### **Version Control**
- All changes must be documented
- Breaking changes require version increment
- Backward compatibility must be maintained
- Testing required before publication

### **Update Procedures**
1. Review impact across all three sections
2. Update standards document if needed
3. Validate integration points still work
4. Test with fresh Laravel installation
5. Update version numbers consistently

### **Documentation Updates**
- Keep examples current with latest Laravel
- Update path examples for current structure
- Refresh script examples with latest syntax
- Validate all external links and references

---

## **Validation Checklist**

### **Before Publication**
- [ ] All sections follow these standards
- [ ] Integration between sections tested
- [ ] Fresh Laravel project successfully deployed
- [ ] All scripts execute without errors
- [ ] Documentation clear for beginners

### **Regular Maintenance**  
- [ ] Standards document updated quarterly
- [ ] Example paths updated as needed
- [ ] Script compatibility verified with new Laravel releases
- [ ] User feedback incorporated into improvements

This standards document ensures consistent, professional, and maintainable Master Checklist files that work reliably across all Laravel deployment scenarios.