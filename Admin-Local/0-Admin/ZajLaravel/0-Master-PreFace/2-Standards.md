# Master Checklist Standards & Consistency Guidelines

**Version:** 2.0
**Generated:** August 21, 2025
**Purpose:** Establish unified formatting, structure, and content standards for all Master Checklist files to ensure a consistent, beginner-friendly, and error-proof deployment process.

---
> **Note:** All project-specific names (e.g., paths, domains, etc.) in this document are examples. Please replace them with the actual values for your project.

## **Document Structure Standards**

### **File Naming Convention**
To avoid overly long documents (aim for < 1000 lines), sections can be split into multiple parts (P1, P2, ... PN).
```
SECTION A - Project Setup-P1.md
SECTION A - Project Setup-P2.md
...
SECTION A - Project Setup-PN.md
SECTION B - Pre-Deployment Preparation.md
SECTION C - Build and Deploy-P1.md
...
SECTION C - Build and Deploy-PN.md
```

### **Header Format**
```markdown
# Master Checklist for **SECTION [X]: [Title]**

**Version:** 2.0
**Last Updated:** [Date]
**Purpose:** [Brief description of section objective]

This checklist consolidates all necessary steps for [Section Description]. Follow each step carefully to ensure a smooth and successful deployment.
```

---

## **Step Format Standards**

### **Primary Step Format**
A consistent, detailed format for each step is crucial for clarity.
```markdown
### Step [X]: [short-name] - Descriptive Title
**Location:** [🟢🟡🔴🟣] [Environment Description]  
**Path:** `%path-variable%`  
**Purpose:** [Clear objective statement of what this step achieves.]  
**When:** [Timing and prerequisites (e.g., "Run this after cloning the repository but before installing dependencies.")]  
**Action:**
1.  **[Sub-step Title]**:
    ```bash
    # Command to be executed, with comments explaining flags and arguments
    php artisan key:generate --force
    ```
2.  **Verification**:
    ```bash
    # Command to verify the action was successful
    grep "APP_KEY=base64:" .env
    ```
3.  **Expected Result**:
    ```
    ✅ A new application key should be generated and visible in the .env file.
    ```
4.  **Error Handling**:
    - **If you see "Error: ...", it means [explanation]. To fix it, [solution].**
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
- 🟢 **Local Machine** - Operations on the developer's workstation.
- 🟡 **Builder VM** - Operations on a build server or CI/CD environment.
- 🔴 **Server** - Operations on the live production server.
- 🟣 **User-Configurable** - Steps involving custom user scripts or SSH hooks.

### **Hook Identification (For User-Configurable Steps)**
- 1️⃣ **Pre-release hooks** - Custom scripts run before the build process.
- 2️⃣ **Mid-release hooks** - Custom scripts run after the build but before the atomic switch.
- 3️⃣ **Post-release hooks** - Custom scripts run after the deployment is live.

### **Special Operation Tags**
- 🏗️ **Builder Commands** - Build-specific operations
- 🔧 **Configuration** - Setup and configuration tasks
- 🔍 **Validation** - Testing and verification steps
- 🧹 **Cleanup** - Maintenance and cleanup operations

---

## **Path & Variable Standards**

### **Centralized Configuration**
All paths and dynamic variables will be managed in a single `deployment-variables.json` file to ensure reusability across projects.

### **Standard Path Variables**
```bash
# Primary paths defined in the JSON config
%path-localMachine%    # Developer's local project root
%path-server%          # Root deployment directory on the server (e.g., /home/user/deploy)
%path-Builder-VM%      # Working directory on the build server
%path-shared%          # Directory for shared resources on the server

# Derived paths (calculated from primary paths)
%path-releases%        # Server releases directory
%path-current%         # Current release symlink
%path-backup%          # Backup directory
```

### **Path Usage Examples**
```bash
# Correct usage
cd %path-localMachine%
source %path-localMachine%/Admin-Local/2-Project-Area/01-Deployment-Toolbox/03-Scripts/load-variables.sh

# Incorrect usage  
cd /absolute/path/to/project  # Should use variable
```

---

## **Admin-Local Structure Standards**

### **The Final, Recommended `Admin-Local` Structure (v6)**
This version refines the structure to better organize master templates vs. project-specific files, providing a clean and scalable foundation.

```
Admin-Local/
├── 1-Admin-Area/  (The Universal "Library" - Reusable for ALL projects)
│   ├── 01-Guides-And-Standards/
│   │   └── (Master checklists like SECTION A, B, C...)
│   ├── 02-Master-Scripts/
│   │   ├── universal-dependency-analyzer.sh
│   │   ├── setup-customization-protection.sh
│   │   └── (All other universal automation scripts...)
│   └── 03-File-Templates/
│       ├── 01-Project-Setup/
│       │   ├── .gitignore.template
│       │   └── project-card.template.md
│       ├── 02-Deployment/
│       │   └── deployment-variables.template.json
│       └── 03-Customization/
│           └── CustomizationServiceProvider.template.php
│
└── 2-Project-Area/  (Specific to THIS project)
    ├── 01-Deployment-Toolbox/ (Version-Controlled Tools for this project)
    │   ├── 01-Configs/
    │   │   └── deployment-variables.json
    │   ├── 02-EnvFiles/
    │   │   └── (.env.local, .env.staging, etc.)
    │   └── 03-Scripts/
    │       └── (Copies of master scripts used for this project)
    │
    └── 02-Project-Records/ (Local Only - Excluded from Git)
        ├── 01-Project-Info/
        │   └── project-card.md
        ├── 02-Installation-History/
        │   └── (Installation notes, vendor snapshots)
        ├── 03-Deployment-History/
        │   └── (Logs from server deployments)
        ├── 04-Customization-And-Investment-Tracker/
        │   └── (Audit trails, custom change docs, investment summary)
        └── 05-Logs-And-Maintenance/
            ├── Local-Script-Logs/
            └── Maintenance-Notes.md
```

---

## **Scripting Standards**

### **Script Header Template**
All scripts must start with a standardized header.
```bash
#!/bin/bash

# Script: [script-name.sh]
# Purpose: [Clear description of what the script does.]
# Version: 2.0
# Section: [A/B/C] - [Section Name]
# Location: [🟢🟡🔴] [Environment where it runs]

set -euo pipefail # Fail on error, undefined variable, or pipe failure

# Load centralized variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "${SCRIPT_DIR}/../01-Configs/load-variables.sh"
```

### **Standard Functions**
Every script should include standard functions for logging and error handling.
```bash
# Log function with timestamp
log() { echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"; }

# Error handling function
error_exit() { log "ERROR: $1"; exit 1; }

# Success confirmation function
success() { log "✅ $1"; }

# Warning function
warning() { log "⚠️ $1"; }
```

### **Robust Error Handling**
Critical commands must be checked for success.
```bash
# File existence checks
[[ -f "required_file" ]] || error_exit "Required file missing: required_file"

# Command execution checks
if ! command_that_might_fail; then
    error_exit "Command failed: [description of what failed]."
fi
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
- **Beginner-friendly:** Avoid jargon and assume no prior knowledge.
- **Action-oriented:** Use clear, imperative verbs (e.g., "Create," "Configure," "Verify").
- **Specific and Unambiguous:** Provide exact commands, paths, and expected outcomes.

### **Code Block Standards**
- All commands must be in formatted code blocks.
- Comments should explain *why* a command is being run.
```bash
# Always include comments explaining what commands do
# Use consistent indentation (2 spaces)

cd %path-localMachine%  # Navigate to project root
composer install       # Install PHP dependencies
```

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